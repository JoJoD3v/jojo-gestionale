@extends('layouts.app')

@section('title', 'Assistente AI')

@push('styles')
<style>
    .chat-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 160px);
        min-height: 500px;
    }

    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 1.25rem;
        background: #f8f9fa;
        border-radius: 12px 12px 0 0;
        border: 1px solid #e0e0e0;
        border-bottom: none;
        scroll-behavior: smooth;
    }

    .chat-input-area {
        background: #fff;
        padding: 1rem;
        border: 1px solid #e0e0e0;
        border-radius: 0 0 12px 12px;
    }

    .message-bubble {
        max-width: 78%;
        word-wrap: break-word;
    }

    .message-user {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 0.85rem;
    }

    .message-user .message-bubble {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: #fff;
        border-radius: 18px 18px 4px 18px;
        padding: 0.7rem 1rem;
        font-size: 0.93rem;
    }

    .message-bot {
        display: flex;
        justify-content: flex-start;
        margin-bottom: 0.85rem;
        align-items: flex-start;
        gap: 0.6rem;
    }

    .bot-avatar {
        width: 34px;
        height: 34px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: white;
        font-size: 0.9rem;
    }

    .message-bot .message-bubble {
        background: #fff;
        color: #212529;
        border-radius: 4px 18px 18px 18px;
        padding: 0.7rem 1rem;
        font-size: 0.93rem;
        border: 1px solid #e9ecef;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }

    .message-time {
        font-size: 0.72rem;
        opacity: 0.6;
        margin-top: 0.2rem;
        padding: 0 0.3rem;
    }

    .user-time { text-align: right; }
    .bot-time  { text-align: left; padding-left: 2.8rem; }

    .typing-indicator span {
        display: inline-block;
        width: 7px;
        height: 7px;
        background: #9c82d4;
        border-radius: 50%;
        margin: 0 1px;
        animation: typing 1.2s infinite;
    }
    .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
    .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }

    @keyframes typing {
        0%, 60%, 100% { transform: translateY(0); opacity: 0.5; }
        30%            { transform: translateY(-6px); opacity: 1; }
    }

    .welcome-message {
        text-align: center;
        padding: 2.5rem 1rem;
        color: #6c757d;
    }

    .welcome-message .bi-robot {
        font-size: 3rem;
        background: linear-gradient(135deg, #667eea, #764ba2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .suggestion-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        justify-content: center;
        margin-top: 1rem;
    }

    .chip {
        background: #fff;
        border: 1px solid #d4c5f9;
        color: #667eea;
        border-radius: 20px;
        padding: 0.35rem 0.85rem;
        font-size: 0.83rem;
        cursor: pointer;
        transition: all 0.15s;
    }

    .chip:hover {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: #fff;
        border-color: transparent;
    }

    /* Markdown-like rendering */
    .message-bot .message-bubble strong { font-weight: 600; }
    .message-bot .message-bubble ul {
        margin: 0.4rem 0 0 0;
        padding-left: 1.2rem;
    }
    .message-bot .message-bubble li { margin-bottom: 0.2rem; }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center mb-3">
            <div style="width:40px;height:40px;background:linear-gradient(135deg,#667eea,#764ba2);border-radius:50%;display:flex;align-items:center;justify-content:center;" class="me-3">
                <i class="bi bi-robot text-white fs-5"></i>
            </div>
            <div>
                <h4 class="mb-0">Assistente AI</h4>
                <small class="text-muted">Connesso ai dati del gestionale &bull; Sola lettura</small>
            </div>
            <span class="badge ms-3" id="modelBadge" style="background:linear-gradient(135deg,#667eea,#764ba2);font-size:0.72rem;">{{ config('services.openai.model', 'gpt-4o-mini') }}</span>
        </div>

        <div class="chat-container card shadow-sm p-0">
            <!-- Messages area -->
            <div class="chat-messages" id="chatMessages">
                <div class="welcome-message" id="welcomeMessage">
                    <i class="bi bi-robot"></i>
                    <h5 class="mt-3 mb-1" style="color:#495057;">Ciao! Sono il tuo assistente AI.</h5>
                    <p class="text-muted" style="font-size:0.9rem;">Posso rispondere a domande sui tuoi task, lavori, pagamenti e clienti.<br>Prova una di queste domande:</p>
                    <div class="suggestion-chips">
                        <span class="chip" onclick="sendSuggestion(this)">Che task devo finire oggi?</span>
                        <span class="chip" onclick="sendSuggestion(this)">Quanti pagamenti ho in sospeso questo mese?</span>
                        <span class="chip" onclick="sendSuggestion(this)">Ci sono task in ritardo?</span>
                        <span class="chip" onclick="sendSuggestion(this)">Mostrami i lavori attivi</span>
                        <span class="chip" onclick="sendSuggestion(this)">Quanto devo ancora incassare questo mese?</span>
                    </div>
                </div>
            </div>

            <!-- Input area -->
            <div class="chat-input-area">
                <form id="chatForm" class="d-flex gap-2" autocomplete="off">
                    @csrf
                    <input
                        type="text"
                        id="domandaInput"
                        class="form-control"
                        placeholder="Chiedimi qualcosa sui tuoi dati..."
                        maxlength="1000"
                        autofocus
                    >
                    <button type="submit" class="btn btn-primary px-3" id="sendBtn" style="background:linear-gradient(135deg,#667eea,#764ba2);border:none;white-space:nowrap;">
                        <i class="bi bi-send-fill"></i>
                    </button>
                </form>
                <div class="d-flex justify-content-between mt-1">
                    <small class="text-muted" id="charCount" style="font-size:0.72rem;"></small>
                    <button class="btn btn-link btn-sm text-muted p-0" onclick="clearChat()" style="font-size:0.78rem;">
                        <i class="bi bi-trash3 me-1"></i>Pulisci chat
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const chatMessages   = document.getElementById('chatMessages');
    const chatForm       = document.getElementById('chatForm');
    const domandaInput   = document.getElementById('domandaInput');
    const sendBtn        = document.getElementById('sendBtn');
    const welcomeMessage = document.getElementById('welcomeMessage');
    const charCount      = document.getElementById('charCount');

    // Cronologia locale (max 10 scambi = 20 messaggi)
    let cronologia = [];

    // Aggiorna contatore caratteri
    domandaInput.addEventListener('input', function () {
        const len = this.value.length;
        charCount.textContent = len > 0 ? `${len}/1000` : '';
    });

    chatForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const domanda = domandaInput.value.trim();
        if (!domanda) return;
        inviaMessaggio(domanda);
    });

    function inviaMessaggio(domanda) {
        if (welcomeMessage) welcomeMessage.remove();
        domandaInput.value = '';
        charCount.textContent = '';

        appendMessaggio('user', domanda);

        const typingId = appendTyping();
        sendBtn.disabled = true;
        domandaInput.disabled = true;

        axios.post('{{ route('chatbot.ask') }}', {
            domanda:    domanda,
            cronologia: cronologia.slice(-14) // ultimi 7 scambi
        })
        .then(function (response) {
            removeTyping(typingId);
            const risposta = response.data.risposta || 'Nessuna risposta ricevuta.';
            appendMessaggio('bot', risposta);

            // Aggiorna cronologia locale
            cronologia.push({ role: 'user',      content: domanda  });
            cronologia.push({ role: 'assistant', content: risposta });
            if (cronologia.length > 20) cronologia = cronologia.slice(-20);
        })
        .catch(function (error) {
            removeTyping(typingId);
            const msg = error.response?.data?.message || 'Si è verificato un errore. Riprova.';
            appendMessaggio('bot', '⚠️ ' + msg, true);
        })
        .finally(function () {
            sendBtn.disabled  = false;
            domandaInput.disabled = false;
            domandaInput.focus();
        });
    }

    function appendMessaggio(role, testo, isError = false) {
        const ora = new Date().toLocaleTimeString('it-IT', { hour: '2-digit', minute: '2-digit' });

        if (role === 'user') {
            const div = document.createElement('div');
            div.innerHTML = `
                <div class="message-user">
                    <div class="message-bubble">${escapeHtml(testo)}</div>
                </div>
                <div class="message-time user-time">${ora}</div>`;
            chatMessages.appendChild(div);
        } else {
            const div = document.createElement('div');
            div.innerHTML = `
                <div class="message-bot">
                    <div class="bot-avatar"><i class="bi bi-robot"></i></div>
                    <div class="message-bubble">${renderMarkdown(testo)}</div>
                </div>
                <div class="message-time bot-time">${ora}</div>`;
            chatMessages.appendChild(div);
        }

        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function appendTyping() {
        const id = 'typing-' + Date.now();
        const div = document.createElement('div');
        div.id = id;
        div.innerHTML = `
            <div class="message-bot">
                <div class="bot-avatar"><i class="bi bi-robot"></i></div>
                <div class="message-bubble typing-indicator">
                    <span></span><span></span><span></span>
                </div>
            </div>`;
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        return id;
    }

    function removeTyping(id) {
        const el = document.getElementById(id);
        if (el) el.remove();
    }

    function escapeHtml(text) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    // Rendering minimale Markdown: grassetto, liste puntate, newline
    function renderMarkdown(text) {
        let html = escapeHtml(text);
        // grassetto **testo**
        html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
        // list items che iniziano con "- " o "• "
        const lines = html.split('\n');
        let result = '';
        let inList = false;
        for (const line of lines) {
            if (/^[-•]\s+/.test(line)) {
                if (!inList) { result += '<ul>'; inList = true; }
                result += '<li>' + line.replace(/^[-•]\s+/, '') + '</li>';
            } else {
                if (inList) { result += '</ul>'; inList = false; }
                result += line + '<br>';
            }
        }
        if (inList) result += '</ul>';
        // rimuovi <br> finale
        result = result.replace(/<br>$/, '');
        return result;
    }

    // Esposta globalmente per i chip
    window.sendSuggestion = function (el) { inviaMessaggio(el.textContent.trim()); };

    window.clearChat = function () {
        chatMessages.innerHTML = '';
        cronologia = [];
        // Rimetti il welcome message
        chatMessages.innerHTML = `
            <div class="welcome-message" id="welcomeMessage">
                <i class="bi bi-robot"></i>
                <h5 class="mt-3 mb-1" style="color:#495057;">Ciao! Sono il tuo assistente AI.</h5>
                <p class="text-muted" style="font-size:0.9rem;">Posso rispondere a domande sui tuoi task, lavori, pagamenti e clienti.<br>Prova una di queste domande:</p>
                <div class="suggestion-chips">
                    <span class="chip" onclick="sendSuggestion(this)">Che task devo finire oggi?</span>
                    <span class="chip" onclick="sendSuggestion(this)">Quanti pagamenti ho in sospeso questo mese?</span>
                    <span class="chip" onclick="sendSuggestion(this)">Ci sono task in ritardo?</span>
                    <span class="chip" onclick="sendSuggestion(this)">Mostrami i lavori attivi</span>
                    <span class="chip" onclick="sendSuggestion(this)">Quanto devo ancora incassare questo mese?</span>
                </div>
            </div>`;
    };
})();
</script>
@endpush
