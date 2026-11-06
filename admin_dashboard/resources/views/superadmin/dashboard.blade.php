<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <span>🛡️</span> {{ __('Super Admin Command Center — Pemkab Sleman') }}
            </h2>
            <div class="text-sm text-gray-500">
                Layanan Terpadu IT Diskominfo Sleman
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="p-3 bg-indigo-50 text-indigo-600 rounded-lg text-2xl">👥</div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Total Pengguna</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalUsers) }}</h3>
                        <p class="text-xs text-indigo-600 mt-1">Super, Dinas Admin & Staff</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="p-3 bg-green-50 text-green-600 rounded-lg text-2xl">💬</div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Total Percakapan</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalChats) }}</h3>
                        <p class="text-xs text-green-600 mt-1">{{ $monthlyChats }} bulan ini</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="p-3 bg-purple-50 text-purple-600 rounded-lg text-2xl">🤖</div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">AI Agents Aktif</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">3 Unit</h3>
                        <p class="text-xs text-purple-600 mt-1">Dukcapil, Wisata, Kominfo</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-lg text-2xl">⚡</div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Isolasi Tenant</p>
                        <h3 class="text-2xl font-bold text-blue-600 mt-1">100% Strict</h3>
                        <p class="text-xs text-gray-500 mt-1">Air-gapped KB routing</p>
                    </div>
                </div>
            </div>

            <!-- Per Dinas Breakdown & Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Dinas Stats -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span>📊</span> Distribusi Interaksi per Dinas
                    </h3>
                    <div class="space-y-4">
                        @foreach(['dukcapil' => ['Dinas Dukcapil', 'bg-blue-500'], 'pariwisata' => ['Dinas Pariwisata', 'bg-emerald-500'], 'kominfo' => ['Diskominfo Sleman', 'bg-purple-500']] as $key => $meta)
                            @php
                                $cnt = $chatsPerDinas[$key] ?? 0;
                                $percentage = $totalChats > 0 ? round(($cnt / $totalChats) * 100, 1) : 0;
                            @endphp
                            <div>
                                <div class="flex justify-between text-sm font-medium mb-1">
                                    <span class="text-gray-700">{{ $meta[0] }}</span>
                                    <span class="text-gray-900 font-bold">{{ $cnt }} chat ({{ $percentage }}%)</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-3">
                                    <div class="{{ $meta[1] }} h-3 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-100 flex gap-4">
                        <a href="{{ route('superadmin.chat-history') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                            Lihat Rincian Riwayat &rarr;
                        </a>
                        <a href="{{ route('superadmin.analytics') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-800">
                            Buka Laporan Analitik &rarr;
                        </a>
                    </div>
                </div>

                <!-- Quick Actions & Embed Links -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <span>🔗</span> URL Widget Publik Dinas
                    </h3>
                    <p class="text-xs text-gray-500 mb-4">Embed widget mandiri yang siap disematkan di portal web masing-masing dinas:</p>
                    <div class="space-y-3">
                        <div class="p-3 bg-gray-50 rounded-lg text-xs">
                            <span class="font-semibold text-blue-700 block mb-1">Dukcapil Widget:</span>
                            <code class="text-gray-600 break-all select-all">{{ url('/embed/dukcapil') }}</code>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg text-xs">
                            <span class="font-semibold text-emerald-700 block mb-1">Pariwisata Widget:</span>
                            <code class="text-gray-600 break-all select-all">{{ url('/embed/pariwisata') }}</code>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg text-xs">
                            <span class="font-semibold text-purple-700 block mb-1">Kominfo Widget:</span>
                            <code class="text-gray-600 break-all select-all">{{ url('/embed/kominfo') }}</code>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('superadmin.users.create') }}" class="w-full inline-flex justify-center items-center py-2 px-4 rounded-lg bg-indigo-600 text-white font-medium text-xs hover:bg-indigo-700 transition">
                            + Tambah Pengguna Baru
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Chat Log Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <span>💬</span> 10 Percakapan Terakhir Lintas Dinas
                    </h3>
                    <a href="{{ route('superadmin.chat-history') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                        Lihat Semua &rarr;
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold">
                            <tr>
                                <th class="px-6 py-3 text-left">Waktu</th>
                                <th class="px-6 py-3 text-left">Dinas</th>
                                <th class="px-6 py-3 text-left">Pertanyaan Pengguna</th>
                                <th class="px-6 py-3 text-left">Jawaban AI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentChats as $chat)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                        {{ $chat->created_at->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                            {{ $chat->dinas === 'dukcapil' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $chat->dinas === 'pariwisata' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                            {{ $chat->dinas === 'kominfo' ? 'bg-purple-100 text-purple-800' : '' }}">
                                            {{ ucfirst($chat->dinas) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 max-w-xs truncate text-gray-900 font-medium">
                                        {{ $chat->question }}
                                    </td>
                                    <td class="px-6 py-4 max-w-md truncate text-gray-600">
                                        {{ $chat->answer }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-400">
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

