<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <span>📋</span> {{ __('Riwayat Percakapan AI') }}
            </h2>
            <div class="text-xs">
                @if(Auth::user()->isSuperAdmin())
                    <span class="px-3 py-1 bg-purple-100 text-purple-800 font-semibold rounded-full border border-purple-200">
                        Filter: {{ $activeDinas ?? 'Semua Dinas' }}
                    </span>
                @else
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 font-semibold rounded-full border border-blue-200">
                        Terisolasi: Dinas {{ ucfirst(Auth::user()->dinas) }}
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(Auth::user()->isSuperAdmin())
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <form method="GET" action="{{ route('chatbot.history') }}" class="flex items-center gap-3">
                        <label class="text-xs font-semibold text-gray-700">Filter Dinas:</label>
                        <select name="dinas" onchange="this.form.submit()" class="rounded-md border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Semua Dinas</option>
                            <option value="dukcapil" {{ request('dinas') == 'dukcapil' ? 'selected' : '' }}>Dinas Dukcapil</option>
                            <option value="pariwisata" {{ request('dinas') == 'pariwisata' ? 'selected' : '' }}>Dinas Pariwisata</option>
                            <option value="kominfo" {{ request('dinas') == 'kominfo' ? 'selected' : '' }}>Diskominfo</option>
                        </select>
                    </form>
                    <a href="{{ route('superadmin.chat-history') }}" class="text-xs text-indigo-600 font-semibold hover:underline">
                        Audit Lanjutan Super Admin &rarr;
                    </a>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold">
                            <tr>
                                <th class="py-3 px-6 text-left">Waktu</th>
                                <th class="py-3 px-6 text-left">Dinas Tujuan</th>
                                <th class="py-3 px-6 text-left">Pertanyaan Warga</th>
                                <th class="py-3 px-6 text-left">Jawaban AI</th>
                                <th class="py-3 px-6 text-center">IP / Sesi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @forelse($histories as $history)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-3 px-6 text-left whitespace-nowrap text-xs text-gray-500">
                                    {{ $history->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="py-3 px-6 text-left whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        {{ $history->dinas === 'dukcapil' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $history->dinas === 'pariwisata' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                        {{ $history->dinas === 'kominfo' ? 'bg-purple-100 text-purple-800' : '' }}">
                                        {{ ucfirst($history->dinas) }}
                                    </span>
                                </td>
                                <td class="py-3 px-6 text-left">
                                    <div class="max-w-xs break-words line-clamp-2 text-xs font-medium text-gray-900" title="{{ $history->question }}">
                                        {{ $history->question }}
                                    </div>
                                </td>
                                <td class="py-3 px-6 text-left">
                                    <div class="max-w-md break-words line-clamp-3 text-xs text-gray-600" title="{{ $history->answer }}">
                                        {{ $history->answer }}
                                    </div>
                                </td>
                                <td class="py-3 px-6 text-center text-xs text-gray-400">
                                    {{ $history->ip_address ?? '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-8 px-6 text-center text-gray-400 text-xs">
                                    Belum ada riwayat percakapan yang terekam.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-gray-100">
                    {{ $histories->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
