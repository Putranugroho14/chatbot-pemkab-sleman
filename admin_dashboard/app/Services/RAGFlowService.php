<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RAGFlowService
{
    private string $apiUrl;
    private ?string $apiToken;
    private array $agents;
    private string $sbotUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.ragflow.api_url', 'http://localhost:9380/api/v1');
        $this->apiToken = config('services.ragflow.api_token');
        $this->sbotUrl = config('services.sbot_api_url', 'http://localhost:3000');
        $this->agents = [
            'kominfo' => config('services.ragflow.agent_kominfo', 'agent_kominfo_id_01'),
            'pariwisata' => config('services.ragflow.agent_pariwisata', 'agent_pariwisata_id_02'),
            'dukcapil' => config('services.ragflow.agent_dukcapil', 'agent_dukcapil_id_03'),
        ];
    }

    /**
     * Dapatkan Agent ID berdasarkan dinas.
     */
    public function getAgentIdForDinas(string $dinas): string
    {
        $dinas = strtolower(trim($dinas));
        if (str_contains($dinas, 'dukcapil')) {
            $key = 'dukcapil';
        } elseif (str_contains($dinas, 'pariwisata') || str_contains($dinas, 'wisata')) {
            $key = 'pariwisata';
        } elseif (str_contains($dinas, 'kominfo') || str_contains($dinas, 'diskominfo')) {
            $key = 'kominfo';
        } else {
            $key = $dinas;
        }

        return $this->agents[$key] ?? 'agent_' . $key . '_default';
    }

    /**
     * Eksekusi Chat ke RAGFlow atau S-Bot Local KB Engine.
     */
    public function chat(string $dinas, string $message, ?string $conversationId = null, ?int $userId = null): array
    {
        $convId = $conversationId ?: 'conv_' . Str::random(16);
        $agentId = $this->getAgentIdForDinas($dinas);

        // 1. Coba koneksi ke RAGFlow jika token diset
        if (!empty($this->apiToken)) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiToken,
                    'Content-Type' => 'application/json',
                ])->timeout(10)->post("{$this->apiUrl}/chats", [
                    'agent_id' => $agentId,
                    'message' => $message,
                    'conversation_id' => $convId,
                    'user_id' => $userId ?: auth()->id() ?: 'guest',
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'answer' => $data['data']['answer'] ?? $data['answer'] ?? 'Tidak ada jawaban dari agent.',
                        'conversation_id' => $data['conversation_id'] ?? $convId,
                        'source' => 'ragflow',
                    ];
                }
            } catch (\Exception $e) {
                Log::warning('RAGFlow offline/unreachable: ' . $e->getMessage());
            }
        }

        // 2. Coba koneksi ke S-Bot Local Express Service (nyai_support)
        try {
            $sbotAgentMap = [
                'dukcapil' => 'dukcapil-layanan',
                'pariwisata' => 'wisata-sleman',
                'kominfo' => 'pengaduan-layanan',
            ];
            $cleanDinas = strtolower(trim($dinas));
            $sbotAgent = $sbotAgentMap[$cleanDinas] ?? 'dukcapil-layanan';

            $response = Http::timeout(4)->post("{$this->sbotUrl}/api/chat", [
                'agent_id' => $sbotAgent,
                'agentSlug' => $sbotAgent,
                'sessionId' => $convId,
                'message' => $message,
                'conversation_id' => $convId,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'answer' => $data['reply'] ?? $data['answer'] ?? 'Respon diterima dari S-Bot Agent.',
                    'conversation_id' => $convId,
                    'source' => 'sbot_local',
                ];
            }
        } catch (\Exception $e) {
            Log::info('S-Bot node offline, menggunakan built-in local knowledge fallback.');
        }

        // 3. Fallback cerdas berbasis Knowledge Base Lokal Sleman
        return [
            'answer' => $this->generateFallbackResponse($dinas, $message),
            'conversation_id' => $convId,
            'source' => 'knowledge_base_local',
        ];
    }

    /**
     * Fallback Knowledge Base cerdas berdasarkan dokumen SOP Sleman.
     */
    private function generateFallbackResponse(string $dinas, string $message): string
    {
        $msg = strtolower($message);
        $cleanDinas = strtolower($dinas);

        if (str_contains($cleanDinas, 'dukcapil') || str_contains($msg, 'ktp') || str_contains($msg, 'kk') || str_contains($msg, 'akta')) {
            if (str_contains($msg, 'ktp')) {
                return "Untuk pengurusan KTP Elektronik di Dinas Dukcapil Kabupaten Sleman:\n" .
                    "1. Fotokopi Kartu Keluarga (KK).\n" .
                    "2. Berusia minimal 17 tahun atau sudah/pernah menikah.\n" .
                    "3. Melakukan perekaman biometrik di Kapanewon atau Kantor Dinas Dukcapil Sleman.\n" .
                    "Layanan juga dapat diakses secara daring melalui aplikasi Dukcapil Prima Sleman.";
            }
            if (str_contains($msg, 'kk') || str_contains($msg, 'kartu keluarga')) {
                return "Untuk perubahan atau pembuatan Kartu Keluarga (KK) baru di Dukcapil Sleman:\n" .
                    "1. Mengisi Formulir F-1.02.\n" .
                    "2. KK lama asli (jika pembaharuan).\n" .
                    "3. Buku Nikah/Akta Perkawinan (jika penambahan anggota karena pernikahan).\n" .
                    "4. Surat Keterangan Kematian (jika pengurangan karena meninggal dunia).";
            }
            return "Halo, ini layanan informasi resmi Dinas Kependudukan dan Pencatatan Sipil (Dukcapil) Kabupaten Sleman. Kami melayani pengurusan KTP-el, KK, KIA, Akta Kelahiran, dan Surat Pindah. Silakan tanyakan informasi spesifik yang Anda butuhkan.";
        }

        if (str_contains($cleanDinas, 'pariwisata') || str_contains($msg, 'wisata') || str_contains($msg, 'candi') || str_contains($msg, 'merapi')) {
            if (str_contains($msg, 'merapi') || str_contains($msg, 'jeep')) {
                return "Untuk wisata Jeep Lava Tour Merapi Sleman:\n" .
                    "- Rute Pendek: Titik kumpul Kaliurang/Kaliadem, Bunker Kaliadem, Museum Sisa Hartaku, Batu Alien (durasi ±1,5 - 2 jam).\n" .
                    "- Rute Menengah/Panjang: Termasuk Petilasan Mbah Maridjan dan manuver air Sungai Kalikuning.\n" .
                    "Tarif berkisar antara Rp 400.000 - Rp 600.000 per armada (maksimal 4 penumpang).";
            }
            if (str_contains($msg, 'candi') || str_contains($msg, 'prambanan')) {
                return "Destinasi wisata candi populer di Sleman meliputi Candi Prambanan, Candi Ratu Boko, Candi Sambisari, Candi Sari, dan Candi Barong. Buka setiap hari mulai pukul 06.00 hingga 17.00 WIB.";
            }
            return "Selamat datang di Layanan Informasi Dinas Pariwisata Kabupaten Sleman. Temukan pesona wisata budaya (Candi Prambanan), keindahan alam lereng Gunung Merapi, dan beragam desa wisata berprestasi di Sleman!";
        }

        // Kominfo
        if (str_contains($msg, 'wifi') || str_contains($msg, 'internet') || str_contains($msg, 'aduan')) {
            return "Untuk pengaduan gangguan wifi padukuhan atau infrastruktur jaringan Diskominfo Sleman:\n" .
                "Silakan sertakan lokasi padukuhan, kalurahan, kapanewon, serta detail kendala (lampu indikator modem / sinyal). Tim teknis Diskominfo akan menindaklanjuti dalam waktu 1x24 jam kerja.";
        }

        return "Halo! Ini adalah Layanan Informasi Chatbot Terpadu Pemerintah Kabupaten Sleman. Layanan kami terisolasi untuk masing-masing dinas (Diskominfo, Dukcapil, dan Pariwisata). Silakan ajukan pertanyaan seputar layanan kami.";
    }
}
