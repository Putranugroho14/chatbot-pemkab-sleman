<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Percakapan AI') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <h3 class="text-lg font-bold mb-4">Tabel Log Interaksi Warga</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">Waktu</th>
                                    <th class="py-3 px-6 text-left">Dinas Tujuan</th>
                                    <th class="py-3 px-6 text-left">Pertanyaan Warga</th>
                                    <th class="py-3 px-6 text-left">Jawaban AI</th>
                                    <th class="py-3 px-6 text-center">IP Address</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @forelse($histories as $history)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-6 text-left whitespace-nowrap">
                                        <span class="font-medium">{{ $history->created_at->format('d M Y, H:i') }}</span>
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        <span class="bg-blue-100 text-blue-700 py-1 px-3 rounded-full text-xs">{{ $history->dinas }}</span>
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        <div class="max-w-xs break-words line-clamp-2" title="{{ $history->question }}">
                                            {{ $history->question }}
                                        </div>
                                    </td>
                                    <td class="py-3 px-6 text-left">
                                        <div class="max-w-md break-words line-clamp-3 text-gray-500" title="{{ $history->answer }}">
                                            {{ $history->answer }}
                                        </div>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        {{ $history->ip_address ?? '-' }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-6 px-6 text-center text-gray-500">
                                        Belum ada riwayat percakapan yang terekam.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $histories->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
