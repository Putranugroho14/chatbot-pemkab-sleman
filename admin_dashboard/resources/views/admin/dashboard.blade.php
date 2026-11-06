<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <span>🏢</span> {{ __('Dashboard Admin: ') }} {{ strtoupper($dinas) }}
            </h2>
            <span class="text-xs font-semibold px-3 py-1 bg-blue-100 text-blue-800 rounded-full">
                Tenant Terisolasi: Dinas {{ ucfirst($dinas) }}
            </span>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('status'))
                <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-lg text-2xl">💬</div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Total Percakapan {{ ucfirst($dinas) }}</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalChats) }}</h3>
                        <p class="text-xs text-blue-600 mt-1">{{ $monthlyChats }} percakapan bulan ini</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="p-3 bg-green-50 text-green-600 rounded-lg text-2xl">👥</div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Jumlah Staff Dinas</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $staffCount }} Orang</h3>
                        <a href="{{ route('admin.staff') }}" class="text-xs text-green-700 underline mt-1 block">Kelola staff dinas</a>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="p-3 bg-purple-50 text-purple-600 rounded-lg text-2xl">🧩</div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Widget Web Resmi</p>
                        <h3 class="text-sm font-bold text-gray-900 mt-1">Siap Sematkan</h3>
                        <a href="{{ url('/embed/' . $dinas) }}" target="_blank" class="text-xs text-indigo-600 underline mt-1 block">Buka pratinjau widget &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Upload Knowledge Base Form & Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Upload Form -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-base font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <span>📤</span> Unggah Dokumen SOP / Aturan Baru
                    </h3>
                    <p class="text-xs text-gray-500 mb-4">
                        Dokumen yang diunggah akan otomatis dimasukkan ke dalam Knowledge Base khusus Dinas {{ ucfirst($dinas) }} dan tidak dapat diakses oleh dinas lain.
                    </p>

                    <form method="POST" action="{{ route('admin.kb.upload') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-gray-700">Judul Dokumen / Nama Layanan</label>
                            <input type="text" name="title" required placeholder="Contoh: Petunjuk Teknis Pelayanan Kependudukan 2026" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-xs focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700">File Dokumen (.PDF atau .TXT)</label>
                            <input type="file" name="document" accept=".pdf,.txt" required class="mt-1 block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>

                        <button type="submit" class="w-full py-2 px-4 bg-indigo-600 text-white rounded-md text-xs font-semibold hover:bg-indigo-700 transition">
                            Upload ke Knowledge Base {{ ucfirst($dinas) }}
                        </button>
                    </form>
                </div>

                <!-- Embed Code Box -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-base font-bold text-gray-900 mb-2 flex items-center gap-2">
                        <span>💻</span> Kode Semat Widget Website
                    </h3>
                    <p class="text-xs text-gray-500 mb-4">
                        Tempelkan cuplikan kode iframe berikut ke halaman website dinas Anda untuk menampilkan chatbot:
                    </p>
                    <div class="bg-gray-900 text-gray-200 p-3 rounded-lg text-xs font-mono select-all overflow-x-auto">
&lt;iframe src="{{ url('/embed/' . $dinas) }}" width="100%" height="600" frameborder="0" style="border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);"&gt;&lt;/iframe&gt;
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-100 flex gap-3">
                        <a href="{{ route('chatbot.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                            Uji Chatbot Simulator &rarr;
                        </a>
                        <a href="{{ route('admin.chat-history') }}" class="text-xs font-semibold text-gray-600 hover:text-gray-800">
                            Lihat Riwayat Chat &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Chat Log Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-base font-bold text-gray-900">
                        Percakapan Terakhir Warga (Dinas {{ ucfirst($dinas) }})
                    </h3>
                    <a href="{{ route('admin.chat-history') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                        Lihat Semua &rarr;
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold">
                            <tr>
                                <th class="px-6 py-3 text-left">Waktu</th>
                                <th class="px-6 py-3 text-left">Pertanyaan Warga</th>
                                <th class="px-6 py-3 text-left">Respon AI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentChats as $chat)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                        {{ $chat->created_at->format('d M Y H:i') }}
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
                                    <td colspan="3" class="px-6 py-8 text-center text-gray-400">
                                        Belum ada riwayat percakapan untuk dinas ini.
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

