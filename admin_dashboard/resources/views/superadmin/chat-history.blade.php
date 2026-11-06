<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <span>📋</span> {{ __('Semua Riwayat Percakapan (Audit Lintas Dinas)') }}
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Filter -->
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-wrap gap-4 items-center justify-between">
                <form method="GET" action="{{ route('superadmin.chat-history') }}" class="flex flex-wrap gap-3 items-center">
                    <div>
                        <select name="dinas" class="rounded-lg border-gray-300 text-xs focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Semua Dinas --</option>
                            <option value="kominfo" {{ request('dinas') == 'kominfo' ? 'selected' : '' }}>Kominfo</option>
                            <option value="pariwisata" {{ request('dinas') == 'pariwisata' ? 'selected' : '' }}>Pariwisata</option>
                            <option value="dukcapil" {{ request('dinas') == 'dukcapil' ? 'selected' : '' }}>Dukcapil</option>
                        </select>
                    </div>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pertanyaan / respon..." class="rounded-lg border-gray-300 text-xs w-72 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <button type="submit" class="px-3 py-2 bg-gray-800 text-white rounded-lg text-xs font-semibold hover:bg-gray-700">
                        Filter
                    </button>
                    @if(request()->hasAny(['dinas', 'search']))
                        <a href="{{ route('superadmin.chat-history') }}" class="text-xs text-gray-500 hover:text-gray-700 underline">Reset</a>
                    @endif
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-6 py-3 text-left">Waktu</th>
                            <th class="px-6 py-3 text-left">Dinas</th>
                            <th class="px-6 py-3 text-left">Pengguna / Sesi</th>
                            <th class="px-6 py-3 text-left">Pertanyaan</th>
                            <th class="px-6 py-3 text-left">Respon AI Agent</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($histories as $h)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                    {{ $h->created_at->format('d M Y H:i:s') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        {{ $h->dinas === 'dukcapil' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $h->dinas === 'pariwisata' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                        {{ $h->dinas === 'kominfo' ? 'bg-purple-100 text-purple-800' : '' }}">
                                        {{ ucfirst($h->dinas) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                                    {{ $h->user ? $h->user->name : 'Publik/Widget' }}
                                    @if($h->ip_address)
                                        <div class="text-gray-400 text-[10px]">{{ $h->ip_address }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 max-w-xs text-gray-900 font-medium break-words">
                                    {{ $h->question }}
                                </td>
                                <td class="px-6 py-4 max-w-md text-gray-600 break-words whitespace-pre-line text-xs">
                                    {{ $h->answer }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                                    Belum ada data riwayat percakapan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $histories->links() }}
            </div>

        </div>
    </div>
</x-app-layout>

