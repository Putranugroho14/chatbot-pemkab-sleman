<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatHistory;
use App\Services\RAGFlowService;
use Illuminate\Support\Str;

class EmbedController extends Controller
{
    protected RAGFlowService $ragflow;

    public function __construct(RAGFlowService $ragflow)
    {
        $this->ragflow = $ragflow;
    }

    /**
     * Tampilkan antarmuka embed widget mandiri untuk dinas terkait.
     */
    public function widget(string $dinas)
    {
        $validDinas = ['kominfo', 'pariwisata', 'dukcapil'];
        $dinas = strtolower($dinas);

        if (!in_array($dinas, $validDinas)) {
            abort(404, 'Dinas tidak ditemukan.');
        }

        $meta = [
            'dukcapil' => [
                'title' => 'Layanan Kependudukan Sleman',
                'subtitle' => 'Dinas Kependudukan dan Pencatatan Sipil',
                'primary_color' => '#2563eb',
                'initial_message' => 'Halo! Ada yang bisa kami bantu seputar KTP-el, KK, KIA, atau Akta?',
            ],
            'pariwisata' => [
                'title' => 'Wisata Sleman Explorer',
                'subtitle' => 'Dinas Pariwisata Kabupaten Sleman',
                'primary_color' => '#059669',
                'initial_message' => 'Sugeng rawuh! Ingin info tentang Candi, Jeep Lava Tour, atau Desa Wisata?',
            ],
            'kominfo' => [
                'title' => 'Pusat Aduan & Informasi Kominfo',
                'subtitle' => 'Dinas Komunikasi dan Informatika',
                'primary_color' => '#4f46e5',
                'initial_message' => 'Halo! Ada kendala wifi padukuhan atau butuh info publik Pemkab Sleman?',
            ],
        ];

        $info = $meta[$dinas];

        return view('embed.widget', compact('dinas', 'info'));
    }

    /**
     * API chat untuk publik / embedded widget.
     */
    public function chat(Request $request, string $dinas)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'conversation_id' => 'nullable|string',
        ]);

        $validDinas = ['kominfo', 'pariwisata', 'dukcapil'];
        $dinas = strtolower($dinas);

        if (!in_array($dinas, $validDinas)) {
            return response()->json(['error' => 'Dinas tidak valid'], 400);
        }

        $convId = $request->input('conversation_id') ?: 'embed_' . Str::random(12);

        $response = $this->ragflow->chat(
            $dinas,
            $request->message,
            $convId,
            null
        );

        ChatHistory::create([
            'user_id' => null,
            'session_id' => session()->getId() ?: 'guest_' . uniqid(),
            'conversation_id' => $response['conversation_id'] ?? $convId,
            'dinas' => $dinas,
            'question' => $request->message,
            'answer' => $response['answer'],
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'answer' => $response['answer'],
            'conversation_id' => $response['conversation_id'] ?? $convId,
            'dinas' => $dinas,
        ]);
    }
}

