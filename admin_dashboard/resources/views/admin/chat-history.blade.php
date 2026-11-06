<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <span>📋</span> {{ __('Riwayat Percakapan Terisolasi: Dinas ') }} {{ ucfirst($dinas) }}
        </h2>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-xs text-blue-900">
                🔒 <strong>Prinsip Privasi Data:</strong> Halaman ini hanya menampilkan log percakapan masyarakat untuk <strong>Dinas {{ ucfirst($dinas) }}</strong>. Dinas lain tidak dapat melihat percakapan ini.
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-6 py-3 text-left">Waktu</th>
                            <th class="px-6 py-3 text-left">Pengguna / Sesi</th>
                            <th class="px-6 py-3 text-left">Pertanyaan Warga</th>
                            <th class="px-6 py-3 text-left">Jawaban AI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($histories as $h)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                    {{ $h->created_at->format('d M Y H:i:s') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                                    {{ $h->user ? $h->user->name : 'Publik / Web Widget' }}
                                    @if($h->ip_address)
                                        <div class="text-[10px] text-gray-400">{{ $h->ip_address }}</div>
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
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                                    Belum ada catatan percakapan untuk dinas ini.
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

