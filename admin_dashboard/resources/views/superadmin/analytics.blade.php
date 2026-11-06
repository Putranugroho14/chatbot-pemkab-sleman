<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <span>📈</span> {{ __('Analitik & Tren Penggunaan AI Sleman') }}
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tren Aktivitas 14 Hari Terakhir -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-base font-bold text-gray-900 mb-4">Tren Percakapan Harian (14 Hari Terakhir)</h3>
                    @if($dailyStats->isEmpty())
                        <div class="py-12 text-center text-gray-400 text-sm">
                            Belum ada riwayat aktivitas dalam 14 hari terakhir.
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($dailyStats as $stat)
                                <div>
                                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                                        <span>{{ \Carbon\Carbon::parse($stat->date)->format('d M Y') }}</span>
                                        <span class="font-bold text-gray-900">{{ $stat->count }} percakapan</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2.5">
                                        <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ min(100, $stat->count * 10) }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Proporsi Penggunaan per Dinas -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-base font-bold text-gray-900 mb-4">Total Percakapan Berdasarkan Dinas</h3>
                    <div class="space-y-4">
                        @forelse($dinasStats as $ds)
                            <div class="p-4 bg-gray-50 rounded-lg flex items-center justify-between">
                                <div>
                                    <span class="font-bold text-gray-900 uppercase text-sm">{{ $ds->dinas }}</span>
                                    <p class="text-xs text-gray-500">Agent AI Terisolasi</p>
                                </div>
                                <span class="text-2xl font-bold text-indigo-600">{{ $ds->count }}</span>
                            </div>
                        @empty
                            <div class="py-12 text-center text-gray-400 text-sm">
                                Belum ada statistik per dinas.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Health & Uptime Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-base font-bold text-gray-900 mb-4">Kesehatan Infrastruktur AI (Pemkab Sleman)</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="p-4 rounded-lg border border-green-200 bg-green-50">
                        <span class="font-bold text-green-900 block">S-Bot Agentic Studio</span>
                        <p class="text-xs text-green-700 mt-1">Status: Siap (Port 3000)</p>
                    </div>
                    <div class="p-4 rounded-lg border border-blue-200 bg-blue-50">
                        <span class="font-bold text-blue-900 block">Laravel RBAC Layer</span>
                        <p class="text-xs text-blue-700 mt-1">Status: Berjalan Normal</p>
                    </div>
                    <div class="p-4 rounded-lg border border-purple-200 bg-purple-50">
                        <span class="font-bold text-purple-900 block">Knowledge Base Isolation</span>
                        <p class="text-xs text-purple-700 mt-1">Status: 100% Terenkripsi & Terpisah</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

