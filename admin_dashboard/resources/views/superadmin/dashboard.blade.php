<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-extrabold text-xl text-slate-900 flex items-center gap-2">
                    <span class="p-2 bg-indigo-50 text-indigo-600 rounded-xl">🛡️</span>
                    <span>Super Admin Command Center</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Pemantauan & Manajemen Terpusat AI Layanan Pemkab Sleman</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-purple-50 text-purple-700 text-xs font-bold rounded-full border border-purple-200">
                    <span class="w-2 h-2 rounded-full bg-purple-500 animate-pulse"></span>
                    Diskominfo Sleman Core
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Summary KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Pengguna</span>
                        <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($totalUsers) }}</h3>
                        <p class="text-[11px] text-indigo-600 font-medium mt-1">Super, Admin Dinas & Staff</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold">
                        👥
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Percakapan</span>
                        <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($totalChats) }}</h3>
                        <p class="text-[11px] text-emerald-600 font-medium mt-1">+{{ $monthlyChats }} bulan ini</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                        💬
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">AI Agents Aktif</span>
                        <h3 class="text-2xl font-extrabold text-slate-900 mt-1">3 Unit</h3>
                        <p class="text-[11px] text-purple-600 font-medium mt-1">Dukcapil, Wisata, Kominfo</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl font-bold">
                        🤖
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Keamanan Tenant</span>
                        <h3 class="text-2xl font-extrabold text-blue-600 mt-1">100% Strict</h3>
                        <p class="text-[11px] text-slate-500 font-medium mt-1">Siloing Data Air-Gapped</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
                        🔒
                    </div>
                </div>
            </div>

            <!-- Per Dinas Breakdown & Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Dinas Stats (8 Cols) -->
                <div class="lg:col-span-8 bg-white rounded-2xl shadow-sm p-6 border border-slate-200/80">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <span>📊</span> Distribusi Interaksi Percakapan per Dinas
                        </h3>
                        <a href="{{ route('superadmin.analytics') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                            Lihat Analitik Lengkap &rarr;
                        </a>
                    </div>
                    
                    <div class="space-y-4">
                        @foreach([
                            'dukcapil' => ['Dinas Dukcapil Sleman (Kependudukan)', 'bg-blue-600', 'text-blue-700'],
                            'pariwisata' => ['Dinas Pariwisata Sleman (Wisata & Budaya)', 'bg-emerald-600', 'text-emerald-700'],
                            'kominfo' => ['Diskominfo Sleman (Aduan & Jaringan)', 'bg-indigo-600', 'text-indigo-700']
                        ] as $key => $meta)
                            @php
                                $cnt = $chatsPerDinas[$key] ?? 0;
                                $percentage = $totalChats > 0 ? round(($cnt / $totalChats) * 100, 1) : 0;
                            @endphp
                            <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-100">
                                <div class="flex justify-between text-xs font-semibold mb-1.5">
                                    <span class="text-slate-800">{{ $meta[0] }}</span>
                                    <span class="{{ $meta[2] }} font-bold">{{ $cnt }} chat ({{ $percentage }}%)</span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
                                    <div class="{{ $meta[1] }} h-2.5 rounded-full transition-all duration-500" style="width: {{ max(4, $percentage) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Quick Actions & Widget Links (4 Cols) -->
                <div class="lg:col-span-4 bg-white rounded-2xl shadow-sm p-6 border border-slate-200/80 flex flex-col justify-between">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm mb-3 flex items-center gap-2">
                            <span>🔗</span> Pintasan Widget Publik
                        </h3>
                        <p class="text-xs text-slate-500 mb-4">Pratinjau widget yang terpasang di portal resmi dinas:</p>
                        
                        <div class="space-y-2 text-xs">
                            <a href="{{ url('/embed/dukcapil') }}" target="_blank" class="p-2.5 rounded-xl bg-blue-50/60 hover:bg-blue-100/60 text-blue-800 font-semibold flex items-center justify-between transition border border-blue-100">
                                <span>🏛️ Widget Dukcapil</span>
                                <span class="text-blue-500">↗</span>
                            </a>
                            <a href="{{ url('/embed/pariwisata') }}" target="_blank" class="p-2.5 rounded-xl bg-emerald-50/60 hover:bg-emerald-100/60 text-emerald-800 font-semibold flex items-center justify-between transition border border-emerald-100">
                                <span>🌲 Widget Pariwisata</span>
                                <span class="text-emerald-500">↗</span>
                            </a>
                            <a href="{{ url('/embed/kominfo') }}" target="_blank" class="p-2.5 rounded-xl bg-indigo-50/60 hover:bg-indigo-100/60 text-indigo-800 font-semibold flex items-center justify-between transition border border-indigo-100">
                                <span>📡 Widget Diskominfo</span>
                                <span class="text-indigo-500">↗</span>
                            </a>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100">
                        <a href="{{ route('superadmin.users.create') }}" class="w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 shadow-sm">
                            <span>+</span> Tambah Pengguna Baru
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Chat Log Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <span>📋</span> Log Percakapan Lintas Dinas Terkini
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Audit interaksi langsung antara masyarakat dan AI Agent Pemkab Sleman</p>
                    </div>
                    <a href="{{ route('superadmin.chat-history') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                        Lihat Semua Riwayat &rarr;
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-xs">
                        <thead class="bg-slate-50 text-slate-500 uppercase font-bold">
                            <tr>
                                <th class="px-6 py-3.5 text-left">Waktu</th>
                                <th class="px-6 py-3.5 text-left">Dinas Tujuan</th>
                                <th class="px-6 py-3.5 text-left">Pertanyaan Warga</th>
                                <th class="px-6 py-3.5 text-left">Respon AI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($recentChats as $chat)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-500 font-medium">
                                        {{ $chat->created_at->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold
                                            {{ $chat->dinas === 'dukcapil' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $chat->dinas === 'pariwisata' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                            {{ $chat->dinas === 'kominfo' ? 'bg-indigo-100 text-indigo-800' : '' }}">
                                            {{ strtoupper($chat->dinas) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 max-w-xs truncate text-slate-800 font-semibold">
                                        {{ $chat->question }}
                                    </td>
                                    <td class="px-6 py-4 max-w-md truncate text-slate-600">
                                        {{ $chat->answer }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-slate-400">
                                        Belum ada data percakapan yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
