<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $info['title'] }} — Pemerintah Kabupaten Sleman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        body {
            background-color: #f8fafc;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        /* Top Header */
        .widget-header {
            background: linear-gradient(135deg, {{ $info['primary_color'] }}, #0f172a);
            color: #ffffff;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            z-index: 10;
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .avatar-wrap {
            position: relative;
        }
        .avatar {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }
        .online-dot {
            position: absolute;
            bottom: -2px;
            right: -2px;
            width: 12px;
            height: 12px;
            background: #10b981;
            border: 2px solid #ffffff;
            border-radius: 50%;
        }
        .header-info h1 {
            font-size: 15px;
            font-weight: 800;
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .verified-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 14px;
            height: 14px;
            background: #38bdf8;
            color: #ffffff;
            border-radius: 50%;
            font-size: 9px;
            font-weight: bold;
        }
        .header-info p {
            font-size: 11px;
            opacity: 0.85;
            margin-top: 1px;
        }
        .header-right button {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: #ffffff;
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .header-right button:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        /* Chat Body */
        .chat-body {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 16px;
            background: #f8fafc;
        }
        .message-wrap {
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }
        .message-wrap.user {
            justify-content: flex-end;
        }
        .msg-avatar {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: {{ $info['primary_color'] }};
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
            shrink: 0;
        }
        .message {
            max-width: 82%;
            padding: 14px 16px;
            border-radius: 18px;
            font-size: 13px;
            line-height: 1.6;
            word-wrap: break-word;
            position: relative;
        }
        .message.bot {
            background: #ffffff;
            color: #1e293b;
            border-top-left-radius: 4px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.03);
        }
        .message.user {
            background: {{ $info['primary_color'] }};
            color: #ffffff;
            border-top-right-radius: 4px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .message.bot p { margin-bottom: 6px; }
        .message.bot p:last-child { margin-bottom: 0; }
        .message.bot ul, .message.bot ol { margin-left: 20px; margin-bottom: 6px; }
        .message.bot strong { font-weight: 700; color: #0f172a; }

        /* Quick Suggestions Chips */
        .chips-container {
            padding: 8px 16px;
            background: #ffffff;
            border-top: 1px solid #f1f5f9;
            display: flex;
            gap: 6px;
            overflow-x: auto;
            white-space: nowrap;
        }
        .chips-container::-webkit-scrollbar { display: none; }
        .chip-btn {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #475569;
            padding: 6px 12px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .chip-btn:hover {
            background: #eef2ff;
            color: {{ $info['primary_color'] }};
            border-color: {{ $info['primary_color'] }};
        }

        /* Footer & Input */
        .chat-footer {
            padding: 12px 16px 6px 16px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .chat-footer input {
            flex: 1;
            padding: 12px 18px;
            border: 1.5px solid #cbd5e1;
            border-radius: 9999px;
            font-size: 13px;
            outline: none;
            transition: all 0.2s;
            background: #f8fafc;
        }
        .chat-footer input:focus {
            background: #ffffff;
            border-color: {{ $info['primary_color'] }};
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }
        .chat-footer button {
            background: {{ $info['primary_color'] }};
            color: #ffffff;
            border: none;
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 3px 8px rgba(0,0,0,0.12);
        }
        .chat-footer button:hover {
            transform: scale(1.05);
            opacity: 0.95;
        }
        .chat-footer button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        .chat-footer button svg {
            width: 18px;
            height: 18px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2.5;
        }
        .badge-sleman {
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            padding: 6px 0 10px 0;
            background: #ffffff;
            font-weight: 500;
        }

        /* Typing Dots */
        .typing {
            display: inline-flex;
            gap: 5px;
            padding: 10px 14px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            align-self: flex-start;
        }
        .typing span {
            width: 6px;
            height: 6px;
            background: {{ $info['primary_color'] }};
            border-radius: 50%;
            animation: bounce 1.2s infinite ease-in-out;
        }
        .typing span:nth-child(2) { animation-delay: 0.2s; }
        .typing span:nth-child(3) { animation-delay: 0.4s; }
        @keyframes bounce {
            0%, 80%, 100% { transform: scale(0); opacity: 0.4; }
            40% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>

    <header class="widget-header">
        <div class="header-left">
            <div class="avatar-wrap">
                <div class="avatar">🏛️</div>
                <div class="online-dot"></div>
            </div>
            <div class="header-info">
                <h1>
                    <span>{{ $info['title'] }}</span>
                    <span class="verified-badge" title="Layanan Resmi Terverifikasi">✓</span>
                </h1>
                <p>{{ $info['subtitle'] }}</p>
            </div>
        </div>
        <div class="header-right">
            <button onclick="resetConversation()">🔄 Reset</button>
        </div>
    </header>

    <main class="chat-body" id="chatBody">
        <div class="message-wrap">
            <div class="msg-avatar">AI</div>
            <div class="message bot">
                {{ $info['initial_message'] }}
            </div>
        </div>
    </main>

    <!-- Suggestion Chips -->
    <div class="chips-container" id="chipsBox">
        @if($dinas === 'dukcapil')
            <button class="chip-btn" onclick="sendPrompt('Syarat KTP-el Pemula')">Syarat KTP-el Pemula</button>
            <button class="chip-btn" onclick="sendPrompt('Cara Pembuatan KK Baru')">Pembuatan KK Baru</button>
            <button class="chip-btn" onclick="sendPrompt('Syarat Akta Kelahiran')">Akta Kelahiran</button>
        @elseif($dinas === 'pariwisata')
            <button class="chip-btn" onclick="sendPrompt('Tarif Jeep Lava Tour Merapi')">Tarif Jeep Lava Tour</button>
            <button class="chip-btn" onclick="sendPrompt('Wisata Candi di Sleman')">Wisata Candi Terpopuler</button>
            <button class="chip-btn" onclick="sendPrompt('Rekomendasi Desa Wisata')">Desa Wisata Sleman</button>
        @else
            <button class="chip-btn" onclick="sendPrompt('Lapor Wifi Padukuhan Gangguan')">Lapor Wifi Padukuhan</button>
            <button class="chip-btn" onclick="sendPrompt('Portal Satu Data Sleman')">Satu Data Sleman</button>
        @endif
    </div>

    <footer class="chat-footer">
        <input type="text" id="userInput" placeholder="Ketik pertanyaan Anda di sini..." autocomplete="off">
        <button id="sendBtn" title="Kirim Pesan">
            <svg viewBox="0 0 24 24"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"></path></svg>
        </button>
    </footer>
    <div class="badge-sleman">Pemerintah Kabupaten Sleman • Layanan AI Terverifikasi</div>

    <script>
        const chatBody = document.getElementById('chatBody');
        const userInput = document.getElementById('userInput');
        const sendBtn = document.getElementById('sendBtn');
        const dinas = "{{ $dinas }}";
        let conversationId = 'embed_' + Math.random().toString(36).substring(2, 12);

        function resetConversation() {
            conversationId = 'embed_' + Math.random().toString(36).substring(2, 12);
            chatBody.innerHTML = `
                <div class="message-wrap">
                    <div class="msg-avatar">AI</div>
                    <div class="message bot">
                        Sesi percakapan baru telah dimulai. Silakan ajukan pertanyaan Anda!
                    </div>
                </div>
            `;
        }

        function sendPrompt(text) {
            userInput.value = text;
            sendMessage();
        }

        function appendMessage(text, sender) {
            const wrap = document.createElement('div');
            wrap.className = 'message-wrap ' + sender;

            if (sender === 'bot') {
                const av = document.createElement('div');
                av.className = 'msg-avatar';
                av.innerText = 'AI';
                wrap.appendChild(av);
            }

            const msg = document.createElement('div');
            msg.className = 'message ' + sender;
            
            if (sender === 'bot') {
                msg.innerHTML = marked.parse(text);
            } else {
                msg.innerText = text;
            }

            wrap.appendChild(msg);
            chatBody.appendChild(wrap);
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        async function sendMessage() {
            const text = userInput.value.trim();
            if (!text) return;

            appendMessage(text, 'user');
            userInput.value = '';
            userInput.disabled = true;
            sendBtn.disabled = true;

            const typingWrap = document.createElement('div');
            typingWrap.className = 'message-wrap';
            typingWrap.id = 'typingIndicator';
            typingWrap.innerHTML = `
                <div class="msg-avatar">AI</div>
                <div class="typing"><span></span><span></span><span></span></div>
            `;
            chatBody.appendChild(typingWrap);
            chatBody.scrollTop = chatBody.scrollHeight;

            try {
                const res = await fetch(`/embed/${dinas}/chat`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        message: text,
                        conversation_id: conversationId
                    })
                });

                const data = await res.json();
                typingWrap.remove();

                if (data.answer) {
                    appendMessage(data.answer, 'bot');
                    if (data.conversation_id) conversationId = data.conversation_id;
                } else {
                    appendMessage(data.error || 'Maaf, terjadi kendala saat merespon.', 'bot');
                }
            } catch (err) {
                typingWrap.remove();
                appendMessage('Gangguan jaringan. Silakan coba kembali sesaat lagi.', 'bot');
            } finally {
                userInput.disabled = false;
                sendBtn.disabled = false;
                userInput.focus();
            }
        }

        sendBtn.addEventListener('click', sendMessage);
        userInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') sendMessage();
        });
    </script>
</body>
</html>
