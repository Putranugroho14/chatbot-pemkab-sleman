<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ChatHistory;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    /**
     * Dashboard ringkasan Super Admin IT Diskominfo.
     */
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalChats = ChatHistory::count();
        $monthlyChats = ChatHistory::whereMonth('created_at', now()->month)->count();
        
        $chatsPerDinas = ChatHistory::select('dinas', DB::raw('count(*) as total'))
            ->groupBy('dinas')
            ->pluck('total', 'dinas')
            ->toArray();

        $recentChats = ChatHistory::with('user')
            ->latest()
            ->take(10)
            ->get();

        $dinasList = ['kominfo', 'pariwisata', 'dukcapil'];

        return view('superadmin.dashboard', compact(
            'totalUsers',
            'totalChats',
            'monthlyChats',
            'chatsPerDinas',
            'recentChats',
            'dinasList'
        ));
    }

    /**
     * Manajemen & status agent multi-tenant.
     */
    public function agents()
    {
        $agents = [
            [
                'id' => config('services.ragflow.agent_dukcapil', 'agent_dukcapil_01'),
                'dinas' => 'dukcapil',
                'name' => 'Agent Dukcapil Sleman (Kependudukan & Catatan Sipil)',
                'persona' => 'Professional & Formal',
                'kb_docs' => 12,
                'status' => 'Active',
                'description' => 'Menangani KTP-el, KIA, KK, Akta Kelahiran, dan Surat Pindah.',
            ],
            [
                'id' => config('services.ragflow.agent_pariwisata', 'agent_pariwisata_02'),
                'dinas' => 'pariwisata',
                'name' => 'Agent Pariwisata Sleman (Destinasi & Kebudayaan)',
                'persona' => 'Friendly & Informative',
                'kb_docs' => 18,
                'status' => 'Active',
                'description' => 'Menangani destinasi wisata Candi, Lava Tour Merapi, desa wisata, dan event pariwisata.',
            ],
            [
                'id' => config('services.ragflow.agent_kominfo', 'agent_kominfo_03'),
                'dinas' => 'kominfo',
                'name' => 'Agent Kominfo Sleman (Aduan Jaringan & Informasi Publik)',
                'persona' => 'Empathetic & Responsive',
                'kb_docs' => 9,
                'status' => 'Active',
                'description' => 'Menangani pelaporan wifi padukuhan, portal satu data, dan layanan aduan masyarakat.',
            ],
        ];

        return view('superadmin.agents', compact('agents'));
    }

    /**
     * Semua riwayat chat lintas dinas dengan fitur filter.
     */
    public function allChatHistory(Request $request)
    {
        $query = ChatHistory::with('user')->latest();

        if ($request->filled('dinas')) {
            $query->where('dinas', $request->query('dinas'));
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                  ->orWhere('answer', 'like', "%{$search}%");
            });
        }

        $histories = $query->paginate(20)->withQueryString();

        return view('superadmin.chat-history', compact('histories'));
    }

    /**
     * Analitik dan statistik interaksi AI.
     */
    public function analytics()
    {
        $dinasStats = ChatHistory::select('dinas', DB::raw('count(*) as count'))
            ->groupBy('dinas')
            ->get();

        $dailyStats = ChatHistory::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(14))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return view('superadmin.analytics', compact('dinasStats', 'dailyStats'));
    }
}

