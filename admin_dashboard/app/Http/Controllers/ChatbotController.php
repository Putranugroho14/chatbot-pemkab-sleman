<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ChatHistory;

class ChatbotController extends Controller
{
    /**
     * Menampilkan halaman chatbot simulator + statistik dari database lokal.
     */
    public function index()
    {
        $documents = [];
        $datasetApiKey = env('DIFY_DATASET_API_KEY');
        $difyApiUrl = env('DIFY_API_URL', 'http://localhost/v1');
        $datasetId = '124cca7a-39a4-4f05-8094-a969370e3a0d'; // ID Dataset Dukcapil

        if ($datasetApiKey) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $datasetApiKey,
                ])->timeout(10)->get("$difyApiUrl/datasets/$datasetId/documents");

                if ($response->successful()) {
                    $documents = $response->json('data') ?? [];
                }
            } catch (\Exception $e) {
                Log::error('Dify Fetch Documents Error: ' . $e->getMessage());
            }
        }

        // Ambil statistik nyata dari database lokal MySQL
        $totalChat      = ChatHistory::whereMonth('created_at', now()->month)->count();
        $totalChatWeek  = ChatHistory::where('created_at', '>=', now()->startOfWeek())->count();
        $totalChatAll   = ChatHistory::count();

        return view('chatbot', compact('documents', 'totalChat', 'totalChatWeek', 'totalChatAll'));
    }

    /**
     * Menampilkan halaman tabel riwayat percakapan.
     */
    public function history()
    {
        // Mengambil riwayat percakapan, diurutkan dari yang terbaru, 15 baris per halaman
        $histories = ChatHistory::orderBy('created_at', 'desc')->paginate(15);
        return view('chat-history', compact('histories'));
    }

    /**
     * Menerima request dari frontend, mengirim ke Dify API,
     * menyimpan percakapan ke MySQL, dan mengembalikan respons.
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message'         => 'required|string|max:2000',
            'lokasi_dinas'    => 'required|string',
            'conversation_id' => 'nullable|string'
        ]);

        $difyApiUrl = env('DIFY_API_URL', 'http://localhost/v1');

        // LOGIKA MULTI-AGENT: Tentukan API Key berdasarkan lokasi dinas
        $dinasTujuan = strtolower($request->lokasi_dinas);
        $difyApiKey = null;
        
        if (str_contains($dinasTujuan, 'pariwisata')) {
            $difyApiKey = env('DIFY_API_KEY_PARIWISATA'); // Harus ditambahkan di .env nanti
        } else {
            // Default ke Dukcapil
            $difyApiKey = env('DIFY_API_KEY'); 
        }

        if (!$difyApiKey) {
            return response()->json([
                'error' => 'DIFY_API_KEY untuk dinas tersebut belum diatur di file .env'
            ], 500);
        }

        // Gunakan session ID sebagai identifier user (Multi-user identifier)
        $userId = session()->getId();

        try {
            $payload = [
                'inputs' => [
                    'lokasi_dinas' => $request->lokasi_dinas,
                ],
                'query'         => $request->message,
                'response_mode' => 'streaming',
                'user'          => $userId
            ];

            if ($request->filled('conversation_id')) {
                $payload['conversation_id'] = $request->conversation_id;
            }

            // Memanggil API Dify
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $difyApiKey,
                'Content-Type'  => 'application/json'
            ])->timeout(60)->post($difyApiUrl . '/chat-messages', $payload);

            if ($response->successful()) {
                // Parse respons SSE streaming dari Agent
                $body        = $response->body();
                $lines       = explode("\n", $body);
                $finalAnswer = '';
                $convoId     = null;

                foreach ($lines as $line) {
                    if (strpos($line, 'data: ') === 0) {
                        $json = json_decode(substr($line, 6), true);
                        if (isset($json['event'])) {
                            if ($json['event'] === 'message' || $json['event'] === 'agent_message') {
                                $finalAnswer .= $json['answer'] ?? '';
                            }
                            if (isset($json['conversation_id'])) {
                                $convoId = $json['conversation_id'];
                            }
                        }
                    }
                }

                // Bersihkan tag <think>...</think> bawaan model reasoning
                $finalAnswer = preg_replace('/<think>.*?<\/think>\s*/s', '', $finalAnswer);
                $finalAnswer = trim($finalAnswer);

                $answerToSave = $finalAnswer ?: 'Tidak ada jawaban dari AI.';

                // ==========================================
                // SIMPAN PERCAKAPAN KE DATABASE LOKAL MYSQL
                // ==========================================
                ChatHistory::create([
                    'session_id'  => $userId,
                    'dinas'       => $request->lokasi_dinas,
                    'question'    => $request->message,
                    'answer'      => $answerToSave,
                    'ip_address'  => $request->ip(),
                ]);

                return response()->json([
                    'answer'          => $answerToSave,
                    'conversation_id' => $convoId
                ]);

            } else {
                Log::error('Dify API Error: ' . $response->body());
                return response()->json([
                    'error' => 'Gagal terhubung ke AI. Silakan coba lagi.'
                ], $response->status());
            }

        } catch (\Exception $e) {
            Log::error('Dify Connection Exception: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menerima file dari user dan menguploadnya ke Knowledge Base Dify.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,txt,doc,docx,csv|max:10240',
        ]);

        $datasetApiKey = env('DIFY_DATASET_API_KEY');
        $difyApiUrl    = env('DIFY_API_URL', 'http://localhost/v1');
        $datasetId     = '124cca7a-39a4-4f05-8094-a969370e3a0d';

        if (!$datasetApiKey) {
            return response()->json([
                'error' => 'DIFY_DATASET_API_KEY belum diatur di file .env'
            ], 500);
        }

        try {
            $file = $request->file('file');

            $data = json_encode([
                "indexing_technique" => "high_quality",
                "process_rule" => [
                    "rules" => [
                        "pre_processing_rules" => [
                            ["id" => "remove_extra_spaces", "enabled" => true],
                            ["id" => "remove_urls_emails",  "enabled" => false]
                        ],
                        "segmentation" => [
                            "separator"  => "\n",
                            "max_tokens" => 500
                        ]
                    ],
                    "mode" => "custom"
                ]
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $datasetApiKey,
            ])->timeout(120)->attach(
                'file', file_get_contents($file->path()), $file->getClientOriginalName()
            )->post("$difyApiUrl/datasets/$datasetId/document/create_by_file", [
                'data' => $data
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => 'Dokumen berhasil di-upload dan sedang dipelajari oleh AI!'
                ]);
            } else {
                Log::error('Dify Upload Error: ' . $response->body());
                return response()->json([
                    'error' => 'Gagal mengupload dokumen ke Dify.'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Dify Upload Exception: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
}
