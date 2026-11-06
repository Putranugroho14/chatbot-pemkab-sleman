<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <span>🤖</span> {{ __('Simulator AI Chatbot & Manajemen Knowledge Base') }}
            </h2>
            <div class="text-xs">
                @if(Auth::user()->isSuperAdmin())
                    <span class="px-3 py-1 bg-purple-100 text-purple-800 font-semibold rounded-full border border-purple-200">
                        🛡️ Mode Super Admin (Bebas Memilih Dinas)
                    </span>
                @else
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 font-semibold rounded-full border border-blue-200">
                        🏢 Terisolasi ke Dinas: {{ strtoupper(Auth::user()->dinas ?? 'Umum') }}
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Statistik Card -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-indigo-500">
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Percakapan (Bulan Ini)</h4>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($totalChat) }}</p>
                    <span class="text-green-600 text-xs font-semibold">↑ {{ $totalChatWeek }} percakapan minggu ini</span>
                </div>
                <!-- Card 2 -->
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-emerald-500">
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Dokumen Knowledge Base</h4>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ count($documents) }} Dokumen</p>
                    <span class="text-gray-500 text-xs truncate block mt-1">
                        Dukcapil, Pariwisata, Diskominfo Sleman
                    </span>
                </div>
                <!-- Card 3 -->
                <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Status AI Engine</h4>
                    <div class="flex items-center mt-2">
                        <span class="relative flex h-3.5 w-3.5 mr-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-green-500"></span>
                        </span>
                        <p class="text-xl font-bold text-gray-900">Online & Terisolasi</p>
                    </div>
                    <span class="text-gray-500 text-xs block mt-1">RAGFlow / S-Bot Local Engine</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Kolom Kiri: Manajemen Dokumen -->
                <div class="space-y-6">
                    <!-- Form Upload -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6">
                            <h3 class="text-base font-bold text-gray-900 mb-2 flex items-center gap-2">
                                <span>📄</span> Upload Dokumen SOP / Regulasi Baru
                            </h3>
                            <p class="text-xs text-gray-500 mb-4">
                                Dokumen PDF atau TXT akan dimasukkan ke Knowledge Base dinas terkait untuk memperkaya pemahaman AI.
                            </p>

                            <form id="uploadForm" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700">Nama Dokumen / Aturan</label>
                                    <input type="text" id="uploadName" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs" placeholder="Contoh: Peraturan Bupati No. 20 Tahun 2026">
                                </div>

                                @if(Auth::user()->isSuperAdmin())
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700">Target Dinas Knowledge Base</label>
                                    <select id="uploadDinas" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs">
                                        <option value="dukcapil">Dinas Dukcapil</option>
                                        <option value="pariwisata">Dinas Pariwisata</option>
                                        <option value="kominfo">Diskominfo Sleman</option>
                                    </select>
                                </div>
                                @else
                                <input type="hidden" id="uploadDinas" value="{{ Auth::user()->dinas }}">
                                @endif

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700">File Dokumen (.PDF / .TXT)</label>
                                    <input type="file" id="uploadFile" accept=".pdf,.txt" required class="mt-1 block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                </div>

                                <div id="uploadAlert" class="hidden text-xs p-3 rounded-lg"></div>

                                <div class="flex justify-end">
                                    <button type="button" id="uploadBtn" onclick="uploadDocument()" class="bg-indigo-600 rounded-md py-2 px-4 text-xs font-semibold text-white hover:bg-indigo-700 transition">
                                        Upload & Latih AI
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- List Dokumen Aktif -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6">
                            <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <span>📚</span> Knowledge Base Dokumen (Aktif)
                            </h3>
                            <ul class="divide-y divide-gray-100">
                                @forelse ($documents as $doc)
                                <li class="py-3 flex justify-between items-center text-xs">
                                    <div class="flex items-center gap-3">
                                        <span class="text-red-500 text-lg">📕</span>
                                        <div>
                                            <span class="font-medium text-gray-900 block" title="{{ $doc['name'] }}">{{ $doc['name'] }}</span>
                                            <span class="text-gray-400">{{ $doc['size'] ?? 'Ukuran bervariasi' }}</span>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-700">
                                        {{ $doc['status'] ?? 'Indexed' }}
                                    </span>
                                </li>
                                @empty
                                <li class="py-6 text-xs text-gray-400 text-center bg-gray-50 rounded-lg">Belum ada dokumen yang di-upload.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Simulator Uji Coba -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                                <span>💬</span> Simulator Uji Coba Chatbot
                            </h3>
                            <button onclick="resetConversation()" class="text-xs text-gray-500 hover:text-gray-800 underline">
                                🔄 Reset Sesi
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mb-4">Tes respons kecerdasan chatbot dan pastikan isolasi knowledge base dinas berjalan dengan tepat.</p>
                        
                        <!-- Select Lokasi Dinas -->
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Target Dinas yang Diuji:</label>
                            @if(Auth::user()->isSuperAdmin())
                                <select id="lokasiDinas" onchange="onDinasChange()" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs">
                                    <option value="dukcapil" {{ ($selectedDinas ?? '') == 'dukcapil' ? 'selected' : '' }}>Dinas Dukcapil (Kependudukan & Catatan Sipil)</option>
                                    <option value="pariwisata" {{ ($selectedDinas ?? '') == 'pariwisata' ? 'selected' : '' }}>Dinas Pariwisata (Wisata & Destinasi)</option>
                                    <option value="kominfo" {{ ($selectedDinas ?? '') == 'kominfo' ? 'selected' : '' }}>Diskominfo (Jaringan & Aduan Layanan)</option>
                                </select>
                            @else
                                <div class="p-2.5 bg-gray-100 border border-gray-200 rounded-md text-xs font-semibold text-gray-700 flex items-center justify-between">
                                    <span>🏢 Dinas {{ ucfirst(Auth::user()->dinas) }} (Terkunci Sesuai Akun)</span>
                                    <span class="text-green-600">✓ Isolated</span>
                                </div>
                                <input type="hidden" id="lokasiDinas" value="{{ Auth::user()->dinas }}">
                            @endif
                        </div>
                        
                        <!-- Area Chat -->
                        <div class="border border-gray-200 rounded-xl h-96 flex flex-col bg-gray-50 overflow-hidden">
                            <div id="chatContainer" class="flex-1 p-4 overflow-y-auto space-y-3">
                                <div class="flex justify-start">
                                    <div class="bg-indigo-100 text-indigo-900 rounded-xl p-3 max-w-[85%] text-xs shadow-sm">
                                        Halo! Saya adalah Asisten Virtual Multi-Tenant Pemkab Sleman. Silakan ajukan pertanyaan untuk menguji respon saya.
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Input -->
                            <div class="p-3 bg-white border-t border-gray-200 flex items-center gap-2">
                                <input type="text" id="chatInput" class="flex-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs" placeholder="Ketik pertanyaan untuk mengetes AI..." onkeypress="handleKeyPress(event)">
                                <button type="button" id="sendBtn" onclick="sendMessage()" class="px-4 py-2 border border-transparent text-xs font-semibold rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 transition">
                                    Kirim
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Integrasi AI Simulator -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <style>
        .markdown-body p { margin-bottom: 0.35rem; }
        .markdown-body ul { list-style-type: disc; margin-left: 1.25rem; margin-bottom: 0.35rem; }
        .markdown-body ol { list-style-type: decimal; margin-left: 1.25rem; margin-bottom: 0.35rem; }
        .markdown-body strong { font-weight: 700; color: #111827; }
    </style>
    <script>
        let conversationId = null;

        function resetConversation() {
            conversationId = 'sim_' + Math.random().toString(36).substring(2, 10);
            const chatContainer = document.getElementById('chatContainer');
            chatContainer.innerHTML = `
                <div class="flex justify-start">
                    <div class="bg-indigo-100 text-indigo-900 rounded-xl p-3 max-w-[85%] text-xs shadow-sm">
                        Sesi percakapan direset. Silakan ajukan pertanyaan baru.
                    </div>
                </div>
            `;
        }

        function onDinasChange() {
            resetConversation();
        }

        function appendMessage(text, isUser, sourceBadge = null) {
            const chatContainer = document.getElementById('chatContainer');
            const wrapper = document.createElement('div');
            wrapper.className = isUser ? 'flex justify-end' : 'flex justify-start';
            
            const bubble = document.createElement('div');
            bubble.className = isUser 
                ? 'bg-indigo-600 text-white rounded-xl p-3 max-w-[85%] text-xs shadow-sm whitespace-pre-wrap' 
                : 'bg-white text-gray-800 border border-gray-200 rounded-xl p-3 max-w-[85%] text-xs shadow-sm markdown-body';
            
            if (isUser) {
                bubble.innerText = text;
            } else {
                let html = marked.parse(text);
                if (sourceBadge) {
                    html += `<div class="mt-2 pt-2 border-t border-gray-100 text-[10px] text-gray-400">Sumber: ${sourceBadge}</div>`;
                }
                bubble.innerHTML = html;
            }
            
            wrapper.appendChild(bubble);
            chatContainer.appendChild(wrapper);
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        function handleKeyPress(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        }

        function showTypingIndicator() {
            const chatContainer = document.getElementById('chatContainer');
            const wrapper = document.createElement('div');
            wrapper.id = 'typingIndicator';
            wrapper.className = 'flex justify-start mb-2';
            
            const bubble = document.createElement('div');
            bubble.className = 'bg-white text-gray-500 border border-gray-200 rounded-full px-3 py-1.5 text-xs shadow-sm flex items-center space-x-2';
            bubble.innerHTML = `
                <span class="text-[11px] font-semibold">AI sedang memproses</span>
                <div class="flex space-x-1">
                    <div class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0s"></div>
                    <div class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    <div class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                </div>
            `;
            
            wrapper.appendChild(bubble);
            chatContainer.appendChild(wrapper);
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        function removeTypingIndicator() {
            const indicator = document.getElementById('typingIndicator');
            if (indicator) indicator.remove();
        }

        async function sendMessage() {
            const inputEl = document.getElementById('chatInput');
            const message = inputEl.value.trim();
            const lokasiDinas = document.getElementById('lokasiDinas').value;
            const sendBtn = document.getElementById('sendBtn');

            if (!message) return;

            appendMessage(message, true);
            inputEl.value = '';
            inputEl.disabled = true;
            sendBtn.disabled = true;
            
            showTypingIndicator();

            try {
                const response = await fetch('{{ route("chatbot.chat") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        message: message,
                        dinas: lokasiDinas,
                        lokasi_dinas: lokasiDinas,
                        conversation_id: conversationId
                    })
                });

                const data = await response.json();
                removeTypingIndicator();

                if (response.ok) {
                    appendMessage(data.answer, false, data.source);
                    if (data.conversation_id) {
                        conversationId = data.conversation_id;
                    }
                } else {
                    appendMessage("❌ Error: " + (data.error || "Gagal memproses pesan."), false);
                }
            } catch (error) {
                removeTypingIndicator();
                appendMessage("❌ Terjadi gangguan jaringan saat menghubungi server.", false);
            } finally {
                inputEl.disabled = false;
                sendBtn.disabled = false;
                inputEl.focus();
            }
        }

        async function uploadDocument() {
            const fileInput = document.getElementById('uploadFile');
            const file = fileInput.files[0];
            const nameInput = document.getElementById('uploadName');
            const dinasInput = document.getElementById('uploadDinas');
            const uploadBtn = document.getElementById('uploadBtn');

            if (!file) {
                showAlert('Silakan pilih file PDF atau TXT terlebih dahulu.', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('file_dokumen', file);
            formData.append('nama_dokumen', nameInput.value || file.name);
            formData.append('dinas', dinasInput.value);
            formData.append('_token', '{{ csrf_token() }}');

            uploadBtn.disabled = true;
            uploadBtn.innerHTML = 'Mengupload...';

            try {
                const response = await fetch('{{ route("chatbot.upload") }}', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    showAlert(data.message || 'Dokumen berhasil diunggah.', 'success');
                    fileInput.value = '';
                    nameInput.value = '';
                } else {
                    showAlert(data.error || 'Gagal mengunggah dokumen.', 'error');
                }
            } catch (error) {
                showAlert('Terjadi kesalahan jaringan.', 'error');
            } finally {
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = 'Upload & Latih AI';
            }
        }

        function showAlert(message, type) {
            const alertBox = document.getElementById('uploadAlert');
            alertBox.innerText = message;
            alertBox.className = `text-xs p-3 rounded-lg mb-2 ${type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
            alertBox.classList.remove('hidden');
        }
    </script>
</x-app-layout>
