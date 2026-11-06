<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-extrabold text-xl text-slate-900 flex items-center gap-2">
                    <span class="p-2 bg-indigo-50 text-indigo-600 rounded-xl">🤖</span>
                    <span>AI Simulator & Manajemen Knowledge Base</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Uji coba interaksi semantik dan latih otak asisten virtual Pemkab Sleman</p>
            </div>
            <div class="flex items-center gap-2">
                @if(Auth::user()->isSuperAdmin())
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-purple-50 text-purple-700 text-xs font-bold rounded-full border border-purple-200 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-purple-500 animate-pulse"></span>
                        Super Admin: Mode Bebas Dinas
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded-full border border-blue-200 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        Dinas Terikat: {{ strtoupper(Auth::user()->dinas ?? 'Umum') }}
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Metric Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Percakapan Bulan Ini</span>
                        <h4 class="text-3xl font-extrabold text-slate-900 mt-1">{{ number_format($totalChat) }}</h4>
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 mt-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            +{{ $totalChatWeek }} minggu ini
                        </span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl font-bold">
                        💬
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Dokumen Knowledge Base</span>
                        <h4 class="text-3xl font-extrabold text-slate-900 mt-1">{{ count($documents) }} <span class="text-xs font-normal text-slate-400">File</span></h4>
                        <span class="text-xs text-slate-500 mt-1 block">Tersinkronisasi & Terisolasi</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl font-bold">
                        📚
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Status AI Engine</span>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                            <h4 class="text-lg font-bold text-slate-900">Online & Siap</h4>
                        </div>
                        <span class="text-xs text-slate-500 mt-1 block">Air-Gapped RAG / S-Bot Local</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl font-bold">
                        ⚡
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Kolom Kiri: Manajemen Dokumen (5 Cols) -->
                <div class="lg:col-span-5 space-y-6">
                    <!-- Form Upload Dokumen -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                                <span class="text-indigo-600 font-extrabold text-lg">📄</span>
                                Unggah Dokumen SOP / Aturan Baru
                            </h3>
                        </div>
                        <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                            File dokumen yang diunggah akan otomatis di-chunking dan dibuat vektor indeksnya ke dalam otak AI dinas yang dipilih.
                        </p>

                        <form id="uploadForm" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Dokumen / Peraturan</label>
                                <input type="text" id="uploadName" required class="w-full rounded-xl border-slate-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3" placeholder="Misal: SOP Layanan KTP-el Sleman 2026">
                            </div>

                            @if(Auth::user()->isSuperAdmin())
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Target Dinas (Isolasi Silo)</label>
                                <select id="uploadDinas" class="w-full rounded-xl border-slate-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3">
                                    <option value="dukcapil">🏢 Dinas Dukcapil Sleman</option>
                                    <option value="pariwisata">🌲 Dinas Pariwisata Sleman</option>
                                    <option value="kominfo">📡 Diskominfo Sleman</option>
                                </select>
                            </div>
                            @else
                            <input type="hidden" id="uploadDinas" value="{{ Auth::user()->dinas }}">
                            @endif

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Pilih File (.PDF atau .TXT)</label>
                                <div class="relative border-2 border-dashed border-slate-300 rounded-xl p-4 text-center hover:border-indigo-500 hover:bg-indigo-50/20 transition cursor-pointer" onclick="document.getElementById('uploadFile').click()">
                                    <input type="file" id="uploadFile" accept=".pdf,.txt" required class="hidden" onchange="previewFileName(this)">
                                    <div class="text-2xl mb-1">📤</div>
                                    <p id="fileNamePreview" class="text-xs text-slate-600 font-medium">Klik untuk memilih file dokumen</p>
                                    <p class="text-[10px] text-slate-400 mt-1">Maksimal 10 MB per dokumen</p>
                                </div>
                            </div>

                            <div id="uploadAlert" class="hidden text-xs p-3 rounded-xl"></div>

                            <button type="button" id="uploadBtn" onclick="uploadDocument()" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center justify-center gap-2">
                                <span>⚡</span> Upload & Latih AI
                            </button>
                        </form>
                    </div>

                    <!-- Dokumen Aktif List -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
                        <h3 class="font-bold text-slate-900 text-sm mb-3 flex items-center gap-2">
                            <span>📚</span> Dokumen Knowledge Base Aktif
                        </h3>
                        <div class="divide-y divide-slate-100">
                            @forelse ($documents as $doc)
                            <div class="py-3 flex items-center justify-between text-xs hover:bg-slate-50/50 p-2 rounded-lg transition">
                                <div class="flex items-center gap-3">
                                    <span class="p-2 bg-red-50 text-red-600 rounded-lg text-sm font-bold">📕</span>
                                    <div>
                                        <p class="font-semibold text-slate-800 leading-snug truncate max-w-[200px]" title="{{ $doc['name'] }}">{{ $doc['name'] }}</p>
                                        <span class="text-[10px] text-slate-400">{{ $doc['size'] ?? 'Vektor Aktif' }}</span>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    ● Terindeks
                                </span>
                            </div>
                            @empty
                            <div class="py-8 text-center text-xs text-slate-400 bg-slate-50 rounded-xl">
                                Belum ada dokumen pengetahuan yang diunggah.
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Simulator Chatbot (7 Cols) -->
                <div class="lg:col-span-7 flex flex-col">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 flex-1 flex flex-col overflow-hidden">
                        
                        <!-- Header Simulator -->
                        <div class="p-4 bg-slate-900 text-white flex items-center justify-between border-b border-slate-800">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-500 to-emerald-400 flex items-center justify-center text-lg shadow">
                                    🤖
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-bold text-sm text-white">S-Bot Assistant Console</h3>
                                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                    </div>
                                    <span id="personaLabel" class="text-[10px] text-slate-300">
                                        👔 Professional • Regulasi Dukcapil Sleman
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button onclick="resetConversation()" title="Reset Percakapan" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium transition flex items-center gap-1">
                                    <span>🔄</span> <span class="hidden sm:inline">Reset</span>
                                </button>
                            </div>
                        </div>

                        <!-- Selector Target Dinas Simulator -->
                        <div class="px-4 py-2.5 bg-slate-100/70 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2 text-xs">
                            <span class="font-semibold text-slate-600 flex items-center gap-1.5">
                                <span>🎯</span> Simulasi Dinas Tujuan:
                            </span>
                            @if(Auth::user()->isSuperAdmin())
                                <select id="lokasiDinas" onchange="onDinasChange()" class="rounded-lg border-slate-300 text-xs font-semibold py-1 px-3 bg-white text-indigo-700 focus:ring-indigo-500">
                                    <option value="dukcapil" {{ ($selectedDinas ?? '') == 'dukcapil' ? 'selected' : '' }}>Dinas Dukcapil (Kependudukan)</option>
                                    <option value="pariwisata" {{ ($selectedDinas ?? '') == 'pariwisata' ? 'selected' : '' }}>Dinas Pariwisata (Wisata Sleman)</option>
                                    <option value="kominfo" {{ ($selectedDinas ?? '') == 'kominfo' ? 'selected' : '' }}>Diskominfo (Aduan & IT)</option>
                                </select>
                            @else
                                <span class="px-2.5 py-1 rounded-md bg-white font-bold text-indigo-700 border border-slate-200">
                                    Dinas {{ ucfirst(Auth::user()->dinas) }} (Terkunci Sesuai Akun)
                                </span>
                                <input type="hidden" id="lokasiDinas" value="{{ Auth::user()->dinas }}">
                            @endif
                        </div>

                        <!-- Area Percakapan Chat -->
                        <div id="chatContainer" class="flex-1 p-5 overflow-y-auto space-y-4 min-h-[380px] max-h-[460px] bg-slate-50/60">
                            <!-- Bot Welcome Message -->
                            <div class="flex items-start gap-3">
                                <div class="w-7 h-7 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs font-bold shrink-0 shadow">
                                    AI
                                </div>
                                <div class="bg-white text-slate-800 border border-slate-200/80 rounded-2xl rounded-tl-sm p-4 text-xs shadow-sm max-w-[85%] leading-relaxed">
                                    <p class="font-bold text-slate-900 mb-1">Halo! Saya Asisten Virtual Pintar Pemkab Sleman. 👋</p>
                                    <p>Saya siap menjawab pertanyaan Anda seputar pelayanan publik, syarat kependudukan, destinasi pariwisata, atau pengaduan warga secara akurat sesuai regulasi.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Reply Chips -->
                        <div class="px-4 py-2 bg-white border-t border-slate-100 flex items-center gap-1.5 overflow-x-auto text-[11px]" id="quickChips">
                            <span class="text-slate-400 font-semibold shrink-0">Pertanyaan Cepat:</span>
                            <button onclick="sendQuickPrompt(this.innerText)" class="chip px-3 py-1 rounded-full bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 text-slate-600 font-medium transition shrink-0">Syarat KTP-el Pemula</button>
                            <button onclick="sendQuickPrompt(this.innerText)" class="chip px-3 py-1 rounded-full bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 text-slate-600 font-medium transition shrink-0">Cara Pembuatan KK Baru</button>
                            <button onclick="sendQuickPrompt(this.innerText)" class="chip px-3 py-1 rounded-full bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 text-slate-600 font-medium transition shrink-0">Tarif Jeep Lava Tour Merapi</button>
                            <button onclick="sendQuickPrompt(this.innerText)" class="chip px-3 py-1 rounded-full bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 text-slate-600 font-medium transition shrink-0">Lapor Gangguan Wifi</button>
                        </div>

                        <!-- Chat Input Box -->
                        <div class="p-4 bg-white border-t border-slate-200/80">
                            <div class="flex items-center gap-2">
                                <input type="text" id="chatInput" placeholder="Ketik pertanyaan untuk menguji AI..." onkeypress="handleKeyPress(event)" class="flex-1 rounded-xl border-slate-300 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4">
                                <button type="button" id="sendBtn" onclick="sendMessage()" class="p-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-md transition flex items-center justify-center font-bold">
                                    <svg class="w-4 h-4 transform rotate-90" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Script Integrasi AI Simulator & Markdown -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <style>
        .markdown-body p { margin-bottom: 0.4rem; }
        .markdown-body ul { list-style-type: disc; margin-left: 1.25rem; margin-bottom: 0.4rem; }
        .markdown-body ol { list-style-type: decimal; margin-left: 1.25rem; margin-bottom: 0.4rem; }
        .markdown-body strong { font-weight: 700; color: #0f172a; }
    </style>

    <script>
        let conversationId = 'sim_' + Math.random().toString(36).substring(2, 10);

        function previewFileName(input) {
            if (input.files && input.files[0]) {
                document.getElementById('fileNamePreview').innerText = "File terpilih: " + input.files[0].name;
            }
        }

        function onDinasChange() {
            const dinas = document.getElementById('lokasiDinas').value;
            const label = document.getElementById('personaLabel');
            if (dinas === 'pariwisata') {
                label.innerText = '😊 Ramah • Pemandu Wisata & Budaya Sleman';
            } else if (dinas === 'kominfo') {
                label.innerText = '📡 Solutif • Layanan Aduan & IT Diskominfo';
            } else {
                label.innerText = '👔 Professional • Regulasi Dukcapil Sleman';
            }
            resetConversation();
        }

        function resetConversation() {
            conversationId = 'sim_' + Math.random().toString(36).substring(2, 10);
            const chatContainer = document.getElementById('chatContainer');
            chatContainer.innerHTML = `
                <div class="flex items-start gap-3">
                    <div class="w-7 h-7 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs font-bold shrink-0 shadow">
                        AI
                    </div>
                    <div class="bg-white text-slate-800 border border-slate-200/80 rounded-2xl rounded-tl-sm p-4 text-xs shadow-sm max-w-[85%] leading-relaxed">
                        <p class="font-bold text-slate-900 mb-1">Sesi baru dimulai. 🔄</p>
                        <p>Silakan ajukan pertanyaan seputar layanan dinas terkait.</p>
                    </div>
                </div>
            `;
        }

        function sendQuickPrompt(promptText) {
            document.getElementById('chatInput').value = promptText;
            sendMessage();
        }

        function copyToClipboard(button, text) {
            navigator.clipboard.writeText(text).then(() => {
                const originalHtml = button.innerHTML;
                button.innerHTML = '✓ Tersalin';
                setTimeout(() => {
                    button.innerHTML = originalHtml;
                }, 2000);
            });
        }

        function appendMessage(text, isUser, sourceBadge = null) {
            const chatContainer = document.getElementById('chatContainer');
            const wrapper = document.createElement('div');
            wrapper.className = isUser ? 'flex justify-end' : 'flex items-start gap-3';
            
            if (isUser) {
                const bubble = document.createElement('div');
                bubble.className = 'bg-gradient-to-tr from-indigo-600 to-blue-600 text-white rounded-2xl rounded-tr-sm p-3.5 max-w-[80%] text-xs shadow-md whitespace-pre-wrap leading-relaxed';
                bubble.innerText = text;
                wrapper.appendChild(bubble);
            } else {
                const avatar = document.createElement('div');
                avatar.className = 'w-7 h-7 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs font-bold shrink-0 shadow';
                avatar.innerText = 'AI';
                
                const bubble = document.createElement('div');
                bubble.className = 'bg-white text-slate-800 border border-slate-200/80 rounded-2xl rounded-tl-sm p-4 max-w-[85%] text-xs shadow-sm markdown-body leading-relaxed relative group';
                
                let renderedHtml = marked.parse(text);
                
                // Footer bubble info & copy button
                renderedHtml += `
                    <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400">
                        <span class="inline-flex items-center gap-1 font-medium text-indigo-600">
                            ✓ ${sourceBadge || 'Knowledge Base Sleman'}
                        </span>
                        <button onclick="copyToClipboard(this, \`${text.replace(/`/g, '\\`')}\`)" class="text-slate-400 hover:text-slate-700 transition font-medium px-2 py-0.5 rounded bg-slate-50 hover:bg-slate-100">
                            📋 Salin Jawaban
                        </button>
                    </div>
                `;
                bubble.innerHTML = renderedHtml;

                wrapper.appendChild(avatar);
                wrapper.appendChild(bubble);
            }
            
            chatContainer.appendChild(wrapper);
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        function handleKeyPress(e) {
            if (e.key === 'Enter') sendMessage();
        }

        function showTypingIndicator() {
            const chatContainer = document.getElementById('chatContainer');
            const wrapper = document.createElement('div');
            wrapper.id = 'typingIndicator';
            wrapper.className = 'flex items-start gap-3 mb-2';
            wrapper.innerHTML = `
                <div class="w-7 h-7 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs font-bold shrink-0">AI</div>
                <div class="bg-white text-slate-500 border border-slate-200 rounded-2xl rounded-tl-sm px-4 py-3 text-xs shadow-sm flex items-center gap-2">
                    <span class="text-[11px] font-semibold">AI sedang menganalisis dokumen</span>
                    <div class="flex space-x-1">
                        <div class="w-1.5 h-1.5 bg-indigo-600 rounded-full animate-bounce" style="animation-delay: 0s"></div>
                        <div class="w-1.5 h-1.5 bg-indigo-600 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        <div class="w-1.5 h-1.5 bg-indigo-600 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                    </div>
                </div>
            `;
            chatContainer.appendChild(wrapper);
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        function removeTypingIndicator() {
            const ind = document.getElementById('typingIndicator');
            if (ind) ind.remove();
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
                    if (data.conversation_id) conversationId = data.conversation_id;
                } else {
                    appendMessage("❌ Error: " + (data.error || "Gagal memproses pesan."), false);
                }
            } catch (error) {
                removeTypingIndicator();
                appendMessage("❌ Gangguan koneksi ke backend AI server.", false);
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
            uploadBtn.innerHTML = '⚡ Sedang Mengupload & Memproses Vektor...';

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
                    document.getElementById('fileNamePreview').innerText = 'Klik untuk memilih file dokumen';
                } else {
                    showAlert(data.error || 'Gagal mengunggah dokumen.', 'error');
                }
            } catch (error) {
                showAlert('Terjadi kesalahan jaringan.', 'error');
            } finally {
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = '<span>⚡</span> Upload & Latih AI';
            }
        }

        function showAlert(message, type) {
            const alertBox = document.getElementById('uploadAlert');
            alertBox.innerText = message;
            alertBox.className = `text-xs p-3 rounded-xl mb-3 ${type === 'success' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-red-50 text-red-800 border border-red-200'}`;
            alertBox.classList.remove('hidden');
        }
    </script>
</x-app-layout>
