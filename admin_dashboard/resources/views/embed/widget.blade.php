<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $info['title'] }} - Pemkab Sleman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        body {
            background-color: #f8fafc;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .widget-header {
            background: {{ $info['primary_color'] }};
            color: #ffffff;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .header-info h2 {
            font-size: 15px;
            font-weight: 700;
        }
        .header-info p {
            font-size: 11px;
            opacity: 0.9;
        }
        .chat-body {
            flex: 1;
            padding: 16px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .message {
            max-width: 82%;
            padding: 12px 16px;
            border-radius: 16px;
            font-size: 13px;
            line-height: 1.5;
            word-wrap: break-word;
            white-space: pre-line;
        }
        .message.bot {
            align-self: flex-start;
            background: #ffffff;
            color: #1e293b;
            border-bottom-left-radius: 4px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }
        .message.user {
            align-self: flex-end;
            background: {{ $info['primary_color'] }};
            color: #ffffff;
            border-bottom-right-radius: 4px;
        }
        .chat-footer {
            padding: 14px 16px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            display: flex;
            gap: 8px;
        }
        .chat-footer input {
            flex: 1;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 24px;
            font-size: 13px;
            outline: none;
            transition: border-color 0.2s;
        }
        .chat-footer input:focus {
            border-color: {{ $info['primary_color'] }};
        }
        .chat-footer button {
            background: {{ $info['primary_color'] }};
            color: #ffffff;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .chat-footer button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .chat-footer button svg {
            width: 18px;
            height: 18px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
        }
        .badge-sleman {
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            padding: 4px 0 8px 0;
            background: #ffffff;
        }
        .typing {
            display: inline-flex;
            gap: 4px;
            padding: 8px 12px;
            background: #e2e8f0;
            border-radius: 12px;
            align-self: flex-start;
        }
        .typing span {
            width: 6px;
            height: 6px;
            background: #64748b;
            border-radius: 50%;
            animation: blink 1.2s infinite;
        }
        .typing span:nth-child(2) { animation-delay: 0.2s; }
        .typing span:nth-child(3) { animation-delay: 0.4s; }
        @keyframes blink {
            0%, 100% { opacity: 0.2; transform: translateY(0); }
            50% { opacity: 1; transform: translateY(-2px); }
        }
    </style>
</head>
<body>

    <header class="widget-header">
        <div class="avatar">🏛️</div>
        <div class="header-info">
            <h2>{{ $info['title'] }}</h2>
            <p>{{ $info['subtitle'] }}</p>
        </div>
    </header>

    <main class="chat-body" id="chatBody">
        <div class="message bot">
            {{ $info['initial_message'] }}
        </div>
    </main>

    <footer class="chat-footer">
        <input type="text" id="userInput" placeholder="Ketik pertanyaan Anda..." autocomplete="off">
        <button id="sendBtn" title="Kirim">
            <svg viewBox="0 0 24 24"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"></path></svg>
        </button>
    </footer>
    <div class="badge-sleman">Pemerintah Kabupaten Sleman • AI Air-Gapped Service</div>

    <script>
        const chatBody = document.getElementById('chatBody');
        const userInput = document.getElementById('userInput');
        const sendBtn = document.getElementById('sendBtn');
        const dinas = "{{ $dinas }}";
        let conversationId = 'embed_' + Math.random().toString(36).substring(2, 12);

        function appendMessage(text, sender) {
            const msg = document.createElement('div');
            msg.className = 'message ' + sender;
            msg.innerText = text;
            chatBody.appendChild(msg);
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        async function sendMessage() {
            const text = userInput.value.trim();
            if (!text) return;

            appendMessage(text, 'user');
            userInput.value = '';
            userInput.disabled = true;
            sendBtn.disabled = true;

            const typing = document.createElement('div');
            typing.className = 'typing';
            typing.innerHTML = '<span></span><span></span><span></span>';
            chatBody.appendChild(typing);
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
                typing.remove();

                if (data.answer) {
                    appendMessage(data.answer, 'bot');
                    if (data.conversation_id) conversationId = data.conversation_id;
                } else {
                    appendMessage(data.error || 'Maaf, terjadi kendala teknis saat memproses jawaban.', 'bot');
                }
            } catch (err) {
                typing.remove();
                appendMessage('Koneksi terputus. Silakan coba sesaat lagi.', 'bot');
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

