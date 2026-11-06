<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\ChatHistory;
use App\Services\RAGFlowService;

class ChatbotController extends Controller
{
    protected RAGFlowService $ragflow;

    public function __construct(RAGFlowService $ragflow)
    {
        $this->ragflow = $ragflow;
    }

    /**
     * Menampilkan halaman chatbot simulator + statistik terfilter per dinas/role.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $selectedDinas = $request->query('dinas');

        // Jika bukan super admin, paksa dinas sesuai akun user
        if (!$user->isSuperAdmin()) {
            $selectedDinas = $user->dinas ?? 'dukcapil';
        } else {
            $selectedDinas = $selectedDinas ?: 'dukcapil';
        }

        // Query riwayat chat berdasarkan hak akses
        $chatQuery = ChatHistory::query();
        if (!$user->isSuperAdmin()) {
            $chatQuery->where('dinas', $selectedDinas);
        } elseif ($request->filled('dinas')) {
            $chatQuery->where('dinas', $selectedDinas);
        }

        $totalChat = (clone $chatQuery)->whereMonth('created_at', now()->month)->count();
        $totalChatWeek = (clone $chatQuery)->where('created_at', '>=', now()->startOfWeek())->count();
        $totalChatAll = (clone $chatQuery)->count();

        // Daftar dokumen lokal simulasi
        $documents = [
            ['name' => 'SOP Layanan Kependudukan Sleman.pdf', 'status' => 'Indexed', 'size' => '2.4 MB'],
            ['name' => 'Buku Panduan Wisata Kaliurang & Merapi.pdf', 'status' => 'Indexed', 'size' => '5.1 MB'],
            ['name' => 'Tata Kelola Jaringan Wifi Padukuhan Kominfo.pdf', 'status' => 'Indexed', 'size' => '1.8 MB'],
        ];

        return view('chatbot', compact('documents', 'totalChat', 'totalChatWeek', 'totalChatAll', 'selectedDinas', 'user'));
    }

    /**
     * Menampilkan halaman tabel riwayat percakapan dengan isolasi tenant.
     */
    public function history(Request $request)
    {
        $user = $request->user();
        $query = ChatHistory::with('user')->orderBy('created_at', 'desc');

        // Isolasi riwayat: Dinas admin & staff hanya melihat chat dinasnya
        if (!$user->isSuperAdmin()) {
            $query->where('dinas', $user->dinas);
        } elseif ($request->filled('dinas')) {
            $query->where('dinas', $request->query('dinas'));
        }

        $histories = $query->paginate(15);
        $activeDinas = $user->isSuperAdmin() ? $request->query('dinas', 'Semua Dinas') : $user->dinas;

        return view('chat-history', compact('histories', 'activeDinas', 'user'));
    }

    /**
     * Menerima request dari frontend, memverifikasi izin dinas,
     * mengirim ke RAGFlow/Agent AI Engine, dan mencatat ke database.
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'lokasi_dinas' => 'nullable|string',
            'dinas' => 'nullable|string|in:kominfo,pariwisata,dukcapil',
            'conversation_id' => 'nullable|string'
        ]);

        $user = $request->user();
        
        // Tentukan target dinas
        $targetDinas = $request->input('dinas');
        if (!$targetDinas && $request->filled('lokasi_dinas')) {
            $raw = strtolower($request->lokasi_dinas);
            if (str_contains($raw, 'wisata') || str_contains($raw, 'pariwisata')) {
                $targetDinas = 'pariwisata';
            } elseif (str_contains($raw, 'kominfo')) {
                $targetDinas = 'kominfo';
            } else {
                $targetDinas = 'dukcapil';
            }
        }
        $targetDinas = $targetDinas ?: ($user->dinas ?: 'dukcapil');

        // Verifikasi hak akses multi-tenant
        if (!$user->canAccessDinas($targetDinas)) {
            return response()->json([
                'error' => 'Akses ditolak: Anda tidak memiliki hak akses untuk dinas ' . $targetDinas
            ], 403);
        }

        try {
            $response = $this->ragflow->chat(
                $targetDinas,
                $request->message,
                $request->conversation_id,
                $user->id
            );

            // Simpan ke riwayat database
            ChatHistory::create([
                'user_id' => $user->id,
                'session_id' => session()->getId() ?: 'sess_' . uniqid(),
                'conversation_id' => $response['conversation_id'] ?? null,
                'dinas' => $targetDinas,
                'question' => $request->message,
                'answer' => $response['answer'],
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'answer' => $response['answer'],
                'conversation_id' => $response['conversation_id'],
                'dinas' => $targetDinas,
                'source' => $response['source'] ?? 'ai_engine',
            ]);

        } catch (\Exception $e) {
            Log::error('Chatbot Controller Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Gagal memproses pesan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload dokumen pengetahuan baru ke dinas terkait.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'nama_dokumen' => 'required|string|max:255',
            'file_dokumen' => 'required|file|mimes:pdf,txt|max:10240',
            'dinas' => 'nullable|string|in:kominfo,pariwisata,dukcapil',
        ]);

        $user = $request->user();
        $targetDinas = $request->input('dinas') ?: ($user->dinas ?: 'dukcapil');

        if (!$user->canManageAgent($targetDinas)) {
            return response()->json([
                'error' => 'Akses ditolak: Anda tidak memiliki hak kelola dokumen untuk dinas ini.'
            ], 403);
        }

        $file = $request->file('file_dokumen');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('knowledge_base/' . $targetDinas, $fileName, 'local');

        return response()->json([
            'success' => true,
            'message' => "Dokumen '{$request->nama_dokumen}' berhasil diunggah ke Knowledge Base {$targetDinas}.",
            'file' => $fileName
        ]);
    }
}
