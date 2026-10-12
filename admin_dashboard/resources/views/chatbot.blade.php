<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pusat Komando AI (Dukcapil Sleman)') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Statistik Card -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-indigo-500">
                    <h4 class="text-sm font-medium text-gray-500">Total Percakapan (Bulan Ini)</h4>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($totalChat) }}</p>
                    <span class="text-green-500 text-sm font-medium">↑ {{ $totalChatWeek }} percakapan minggu ini</span>
                </div>
                <!-- Card 2 -->
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                    <h4 class="text-sm font-medium text-gray-500">Dokumen Aktif (Knowledge Base)</h4>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ count($documents) }}</p>
                    <span class="text-gray-500 text-sm font-medium truncate block">
                        @if(count($documents) > 0)
                            {{ collect($documents)->take(2)->pluck('name')->join(', ') }}...
                        @else
                            Belum ada dokumen
                        @endif
                    </span>
                </div>
                <!-- Card 3 -->
                <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                    <h4 class="text-sm font-medium text-gray-500">Status API Dify</h4>
                    <div class="flex items-center mt-2">
                        <span class="relative flex h-4 w-4 mr-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-4 w-4 bg-blue-500"></span>
                        </span>
                        <p class="text-xl font-bold text-gray-900">Online</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Kolom Kiri: Manajemen Dokumen -->
                <div class="space-y-6">
                    <!-- Form Upload -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Upload Aturan Baru
                            </h3>
                            <p class="text-sm text-gray-600 mb-6">File PDF akan otomatis dikirim ke otak AI (Dify) agar bot semakin cerdas menjawab pertanyaan warga.</p>

                            <form id="uploadForm" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nama Aturan</label>
                                    <input type="text" id="uploadName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: Peraturan Bupati No 15">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">File Dokumen (PDF/TXT)</label>
                                    <input type="file" id="uploadFile" accept=".pdf,.txt" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                </div>
                                <!-- Tempat Notifikasi Upload -->
                                <div id="uploadAlert" class="hidden text-sm p-3 rounded-lg"></div>
                                <div class="flex justify-end">
                                    <button type="button" id="uploadBtn" onclick="uploadDocument()" class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Upload & Latih AI
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- List Dokumen Aktif -->
                    <div class="bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Database Dokumen (Aktif)
                            </h3>
                            <ul class="divide-y divide-gray-200">
                                @forelse ($documents as $doc)
                                <li class="py-3 flex justify-between items-center">
                                    <div class="flex items-center">
                                        <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path></svg>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-gray-900 truncate" style="max-width: 250px;" title="{{ $doc['name'] }}">{{ $doc['name'] }}</span>
                                            <span class="text-xs font-semibold {{ isset($doc['indexing_status']) && $doc['indexing_status'] === 'completed' ? 'text-green-600' : 'text-yellow-600' }}">
                                                {{ isset($doc['indexing_status']) && $doc['indexing_status'] === 'completed' ? 'Tersedia' : 'Memproses...' }}
                                            </span>
                                        </div>
                                    </div>
                                    <!-- <button class="text-red-600 hover:text-red-900 text-sm font-medium px-2 py-1 bg-red-50 rounded-md">Hapus</button> -->
                                </li>
                                @empty
                                <li class="py-6 text-sm text-gray-500 text-center bg-gray-50 rounded-lg">Belum ada dokumen yang di-upload.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Simulator Uji Coba -->
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            Simulator Uji Coba Chatbot
                        </h3>
                        <p class="text-sm text-gray-600 mb-4">Tes kecerdasan chatbot di sini setelah Anda meng-upload PDF baru.</p>
                        
                        <!-- Select Lokasi Dinas (Bunglon Feature) -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Simulasikan Lokasi Website (Routing)</label>
                            <select id="lokasiDinas" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="dukcapil">Dinas Dukcapil</option>
                                <option value="pariwisata">Dinas Pariwisata</option>
                            </select>
                        </div>
                        
                        <!-- Area Chat -->
                        <div class="border border-gray-200 rounded-lg h-96 flex flex-col bg-gray-50">
                            <div id="chatContainer" class="flex-1 p-4 overflow-y-auto space-y-4">
                                <div class="flex justify-start">
                                    <div class="bg-indigo-100 text-indigo-900 rounded-lg p-3 max-w-[80%] text-sm shadow-sm">
                                        Halo! Saya asisten AI Pemkab Sleman. Silakan pilih lokasi dinas di atas, lalu tes kemampuan saya!
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Input -->
                            <div class="p-3 bg-white border-t border-gray-200 flex items-center">
                                <input type="text" id="chatInput" class="flex-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Ketik pesan untuk mengetes AI..." onkeypress="handleKeyPress(event)">
                                <button type="button" id="sendBtn" onclick="sendMessage()" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
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
        /* CSS tambahan agar Markdown seperti list dan bold terlihat rapi */
        .markdown-body p { margin-bottom: 0.5rem; }
        .markdown-body ul { list-style-type: disc; margin-left: 1.5rem; margin-bottom: 0.5rem; }
        .markdown-body ol { list-style-type: decimal; margin-left: 1.5rem; margin-bottom: 0.5rem; }
        .markdown-body strong { font-weight: 700; color: #111827; }
    </style>
    <script>
        let conversationId = null;

        function appendMessage(text, isUser) {
            const chatContainer = document.getElementById('chatContainer');
            const wrapper = document.createElement('div');
            wrapper.className = isUser ? 'flex justify-end' : 'flex justify-start';
            
            const bubble = document.createElement('div');
            bubble.className = isUser 
                ? 'bg-indigo-600 text-white rounded-lg p-3 max-w-[80%] text-sm shadow-sm whitespace-pre-wrap' 
                : 'bg-white text-gray-800 border border-gray-200 rounded-lg p-3 max-w-[80%] text-sm shadow-sm markdown-body';
            
            if (isUser) {
                bubble.innerText = text;
            } else {
                bubble.innerHTML = marked.parse(text);
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
            bubble.className = 'bg-white text-gray-500 border border-gray-200 rounded-full px-4 py-2 text-sm shadow-sm flex items-center space-x-2';
            bubble.innerHTML = `
                <span class="text-xs font-semibold">AI sedang mengetik</span>
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
            if (indicator) {
                indicator.remove();
            }
        }

        async function sendMessage() {
            const inputEl = document.getElementById('chatInput');
            const message = inputEl.value.trim();
            const lokasiDinas = document.getElementById('lokasiDinas').value;
            const sendBtn = document.getElementById('sendBtn');

            if (!message) return;

            // Tampilkan pesan user
            appendMessage(message, true);
            inputEl.value = '';
            
            // Disable input sementara loading
            inputEl.disabled = true;
            sendBtn.disabled = true;
            
            // Munculkan animasi typing
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
                        lokasi_dinas: lokasiDinas,
                        conversation_id: conversationId
                    })
                });

                const data = await response.json();
                
                removeTypingIndicator();

                if (response.ok) {
                    appendMessage(data.answer, false);
                    if (data.conversation_id) {
                        conversationId = data.conversation_id; // Simpan memori chat
                    }
                } else {
                    appendMessage("❌ Error: " + (data.error || "Gagal menghubungi API Dify."), false);
                }
            } catch (error) {
                removeTypingIndicator();
                appendMessage("❌ Sistem sibuk atau terputus. Coba lagi.", false);
            } finally {
                inputEl.disabled = false;
                sendBtn.disabled = false;
                inputEl.focus();
            }
        }

        async function uploadDocument() {
            const fileInput = document.getElementById('uploadFile');
            const file = fileInput.files[0];
            const uploadBtn = document.getElementById('uploadBtn');
            const alertBox = document.getElementById('uploadAlert');

            if (!file) {
                showAlert('Silakan pilih file PDF terlebih dahulu.', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', '{{ csrf_token() }}');

            // Set loading state
            uploadBtn.disabled = true;
            uploadBtn.innerHTML = 'Mengupload...';
            alertBox.className = 'hidden';

            try {
                const response = await fetch('{{ route("chatbot.upload") }}', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    showAlert(data.success, 'success');
                    fileInput.value = ''; // Reset form
                } else {
                    showAlert(data.error || 'Gagal mengupload file.', 'error');
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
            alertBox.className = `text-sm p-3 rounded-lg mb-4 ${type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
            alertBox.style.display = 'block';
        }
    </script>
</x-app-layout>
