<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-extrabold text-xl text-slate-900 flex items-center gap-2">
                    <span class="p-2 bg-indigo-50 text-indigo-600 rounded-xl">🏢</span>
                    <span>Dashboard Pengelola: Dinas {{ ucfirst($dinas) }}</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Ruang kendali AI terisolasi khusus Pemerintah Kabupaten Sleman</p>
            </div>
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-full border border-emerald-200 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Tenant Silo: {{ strtoupper($dinas) }} (Terisolasi Penuh)
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('status'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-semibold shadow-sm flex items-center gap-2">
                    <span>✓</span> {{ session('status') }}
                </div>
            @endif

            <!-- Summary KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Chat {{ ucfirst($dinas) }}</span>
                        <h3 class="text-3xl font-extrabold text-slate-900 mt-1">{{ number_format($totalChats) }}</h3>
                        <p class="text-[11px] text-blue-600 font-medium mt-1">+{{ $monthlyChats }} percakapan bulan ini</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
                        💬
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Staff Dinas Aktif</span>
                        <h3 class="text-3xl font-extrabold text-slate-900 mt-1">{{ $staffCount }} <span class="text-xs font-normal text-slate-400">Orang</span></h3>
                        <a href="{{ route('admin.staff') }}" class="text-[11px] text-indigo-600 font-semibold hover:underline mt-1 block">Kelola Staff Dinas &rarr;</a>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                        👥
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Widget Web Resmi</span>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <h3 class="text-base font-bold text-slate-900">Siap Disematkan</h3>
                        </div>
                        <a href="{{ url('/embed/' . $dinas) }}" target="_blank" class="text-[11px] text-indigo-600 font-semibold hover:underline mt-1 block">Buka Demo Widget ↗</a>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl font-bold">
                        🧩
                    </div>
                </div>
            </div>

            <!-- Upload Knowledge Base Form & Embed Box -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Upload Form (6 Cols) -->
                <div class="lg:col-span-6 bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
                    <h3 class="font-bold text-slate-900 text-sm mb-2 flex items-center gap-2">
                        <span>📤</span> Unggah Dokumen SOP / Aturan Dinas {{ ucfirst($dinas) }}
                    </h3>
                    <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                        Dokumen yang diunggah hanya akan digunakan oleh agen AI Dinas {{ ucfirst($dinas) }} dan aman dari akses dinas lainnya.
                    </p>

                    <form method="POST" action="{{ route('admin.kb.upload') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Judul Layanan / Nama SOP</label>
                            <input type="text" name="title" required placeholder="Contoh: Petunjuk Teknis Pelayanan {{ ucfirst($dinas) }} 2026" class="w-full rounded-xl border-slate-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">File Dokumen (.PDF atau .TXT)</label>
                            <input type="file" name="document" accept=".pdf,.txt" required class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>

                        <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                            Upload ke Knowledge Base {{ ucfirst($dinas) }}
                        </button>
                    </form>
                </div>

                <!-- Embed Code Box (6 Cols) -->
                <div class="lg:col-span-6 bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                                <span>💻</span> Kode Semat Widget Website Resmi
                            </h3>
                            <button onclick="copyEmbedCode()" id="copyEmbedBtn" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-lg transition">
                                📋 Salin Kode
                            </button>
                        </div>
                        <p class="text-xs text-slate-500 mb-4">
                            Salin kode iframe berikut ke website portal dinas Anda untuk menampilkan chatbot secara instan:
                        </p>
                        <div class="bg-slate-900 text-indigo-300 p-4 rounded-xl text-xs font-mono select-all overflow-x-auto leading-relaxed border border-slate-800" id="embedCodeSnippet">&lt;iframe src="{{ url('/embed/' . $dinas) }}" width="100%" height="600" frameborder="0" style="border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.12);"&gt;&lt;/iframe&gt;</div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                        <a href="{{ route('chatbot.index') }}" class="font-bold text-indigo-600 hover:underline">
                            💬 Buka Chatbot Simulator &rarr;
                        </a>
                        <a href="{{ route('admin.chat-history') }}" class="font-bold text-slate-600 hover:underline">
                            📋 Lihat Riwayat Chat &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Chat Log Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">
                            Percakapan Terakhir Warga (Dinas {{ ucfirst($dinas) }})
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Pertanyaan masyarakat yang dijawab oleh AI dinas Anda</p>
                    </div>
                    <a href="{{ route('admin.chat-history') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                        Lihat Semua &rarr;
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-xs">
                        <thead class="bg-slate-50 text-slate-500 uppercase font-bold">
                            <tr>
                                <th class="px-6 py-3.5 text-left">Waktu</th>
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
                                    <td class="px-6 py-4 max-w-xs truncate text-slate-800 font-semibold">
                                        {{ $chat->question }}
                                    </td>
                                    <td class="px-6 py-4 max-w-md truncate text-slate-600">
                                        {{ $chat->answer }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-10 text-center text-slate-400">
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

    <script>
        function copyEmbedCode() {
            const snippet = document.getElementById('embedCodeSnippet').innerText;
            navigator.clipboard.writeText(snippet).then(() => {
                const btn = document.getElementById('copyEmbedBtn');
                btn.innerText = '✓ Berhasil Disalin!';
                btn.className = 'text-xs font-bold text-emerald-700 bg-emerald-100 px-3 py-1 rounded-lg transition';
                setTimeout(() => {
                    btn.innerText = '📋 Salin Kode';
                    btn.className = 'text-xs font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-lg transition';
                }, 2500);
            });
        }
    </script>
</x-app-layout>
