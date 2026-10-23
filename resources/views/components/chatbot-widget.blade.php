<!-- MDT Agentic AI Chatbot Widget -->
<div id="mdt-chat-widget-root" class="fixed bottom-6 right-6 z-50 font-sans">
    <!-- Floating Launcher Button -->
    <button id="mdt-chat-launcher" 
            onclick="toggleMdtChat()"
            aria-label="Buka Chatbot AI MDT"
            class="group relative flex items-center justify-center w-14 h-14 rounded-full bg-gradient-to-tr from-blue-700 via-indigo-600 to-cyan-500 text-white shadow-2xl hover:scale-105 active:scale-95 transition-all duration-300 focus:outline-none">
        <!-- Pulse Glow Ring -->
        <span class="absolute -inset-1 rounded-full bg-indigo-500/40 animate-ping pointer-events-none"></span>
        
        <!-- Icons -->
        <span id="mdt-chat-icon-open" class="material-symbols-outlined text-[28px] transition-transform duration-300 group-hover:rotate-12">smart_toy</span>
        <span id="mdt-chat-icon-close" class="material-symbols-outlined text-[28px] hidden transition-transform duration-300">close</span>

        <!-- Unread / Active Badge -->
        <span class="absolute top-0 right-0 w-3.5 h-3.5 bg-emerald-400 border-2 border-white rounded-full"></span>
    </button>

    <!-- Chat Tooltip / Teaser Badge (Shows briefly or on hover) -->
    <div id="mdt-chat-teaser" class="absolute bottom-16 right-0 mb-2 w-max max-w-[220px] bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-100 text-xs px-3 py-2 rounded-xl shadow-xl flex items-center gap-2 pointer-events-none transition-all duration-500 opacity-95">
        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
        <span>Ada yang bisa kami bantu? Tanya harga &amp; portofolio di sini!</span>
    </div>

    <!-- Chat Modal Window -->
    <div id="mdt-chat-window" 
         class="hidden fixed sm:absolute bottom-0 sm:bottom-20 right-0 w-full sm:w-[420px] h-[100dvh] sm:h-[620px] max-h-[100dvh] sm:max-h-[85vh] bg-white dark:bg-slate-950 sm:rounded-2xl border border-slate-200 dark:border-slate-800/80 shadow-2xl flex flex-col overflow-hidden z-50 backdrop-blur-xl transition-all duration-300">
        
        <!-- Header -->
        <div class="px-5 py-4 bg-gradient-to-r from-blue-700 via-indigo-700 to-slate-900 text-white flex items-center justify-between border-b border-white/10 shrink-0">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <div class="w-10 h-10 rounded-full bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/20 shadow-inner">
                        <span class="material-symbols-outlined text-cyan-300 text-[24px]">smart_toy</span>
                    </div>
                    <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-400 border-2 border-indigo-900 rounded-full"></span>
                </div>
                <div>
                    <h3 class="text-sm font-bold tracking-tight text-white flex items-center gap-1.5">
                        MDT AI Assistant
                        <span class="px-1.5 py-0.5 text-[10px] uppercase font-semibold bg-cyan-400/20 text-cyan-200 rounded border border-cyan-300/30">Agentic</span>
                    </h3>
                    <p class="text-[11px] text-slate-200/80 flex items-center gap-1">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Konsultan Cerdas &amp; Booking Jadwal
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-1">
                <!-- Clear Chat Button -->
                <button onclick="clearMdtChatHistory()" 
                        title="Hapus Riwayat Percakapan"
                        class="p-1.5 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                </button>
                <!-- Close Window Button -->
                <button onclick="toggleMdtChat()" 
                        title="Tutup Chat"
                        class="p-1.5 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
        </div>

        <!-- Chat Messages Area -->
        <div id="mdt-chat-messages" 
             class="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-50/50 dark:bg-slate-900/40 text-sm scroll-smooth">
            <!-- Messages will be injected by JavaScript -->
        </div>

        <!-- Quick Suggestion Chips -->
        <div id="mdt-chat-chips" class="px-4 py-2 bg-white/70 dark:bg-slate-950/70 border-t border-slate-100 dark:border-slate-800/60 overflow-x-auto flex gap-2 shrink-0 no-scrollbar">
            <button onclick="sendQuickPrompt('Berapa estimasi harga pembuatan website?')" 
                    class="shrink-0 text-xs px-3 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-blue-600 dark:hover:text-blue-400 border border-slate-200 dark:border-slate-700/60 transition-all flex items-center gap-1">
                💰 <span>Estimasi Biaya Web</span>
            </button>
            <button onclick="sendQuickPrompt('Ada contoh portofolio yang pernah dikerjakan?')" 
                    class="shrink-0 text-xs px-3 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-blue-600 dark:hover:text-blue-400 border border-slate-200 dark:border-slate-700/60 transition-all flex items-center gap-1">
                💼 <span>Lihat Portofolio</span>
            </button>
            <button onclick="sendQuickPrompt('Saya mau jadwalkan meeting konsultasi')" 
                    class="shrink-0 text-xs px-3 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-blue-600 dark:hover:text-blue-400 border border-slate-200 dark:border-slate-700/60 transition-all flex items-center gap-1">
                📅 <span>Booking Meeting</span>
            </button>
            <button onclick="sendQuickPrompt('Jelaskan profil perusahaan MDT Solution')" 
                    class="shrink-0 text-xs px-3 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-blue-600 dark:hover:text-blue-400 border border-slate-200 dark:border-slate-700/60 transition-all flex items-center gap-1">
                🏢 <span>Profil MDT</span>
            </button>
        </div>

        <!-- Input Box Area -->
        <div class="p-3 bg-white dark:bg-slate-950 border-t border-slate-200 dark:border-slate-800/80 shrink-0">
            <form id="mdt-chat-form" onsubmit="handleMdtChatSubmit(event)" class="flex items-center gap-2">
                <input type="text" 
                       id="mdt-chat-input" 
                       placeholder="Ketik pesan Anda..." 
                       autocomplete="off"
                       maxlength="1000"
                       class="flex-1 bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-slate-100 placeholder-slate-400 text-xs sm:text-sm px-4 py-2.5 rounded-xl border border-transparent focus:border-blue-500 focus:bg-white dark:focus:bg-slate-900 focus:outline-none transition-all shadow-inner">
                <button type="submit" 
                        id="mdt-chat-submit-btn" 
                        aria-label="Kirim Pesan"
                        class="w-10 h-10 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-95 text-white flex items-center justify-center transition-all shadow-md focus:outline-none disabled:opacity-50 disabled:pointer-events-none">
                    <span class="material-symbols-outlined text-[20px]">send</span>
                </button>
            </form>
            <div class="mt-1.5 flex items-center justify-between text-[10px] text-slate-400 dark:text-slate-500 px-1">
                <span>Didukung oleh AI Agentic &amp; Groq Cloud</span>
                <span class="text-emerald-500 font-medium flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Online
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Load Marked.js for markdown formatting in chat bubbles -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<style>
/* Custom Chatbot Styles */
#mdt-chat-messages::-webkit-scrollbar {
    width: 5px;
    display: block !important;
}
#mdt-chat-messages::-webkit-scrollbar-track {
    background: transparent;
}
#mdt-chat-messages::-webkit-scrollbar-thumb {
    background: rgba(148, 163, 184, 0.4);
    border-radius: 9999px;
}
.mdt-bubble-markdown table {
    width: 100%;
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
    border-collapse: collapse;
    font-size: 11px;
}
.mdt-bubble-markdown th, 
.mdt-bubble-markdown td {
    padding: 4px 6px;
    border: 1px solid rgba(148, 163, 184, 0.3);
}
.mdt-bubble-markdown th {
    background: rgba(59, 130, 246, 0.1);
    font-weight: 600;
}
.mdt-bubble-markdown ul {
    list-style-type: disc;
    padding-left: 1.25rem;
    margin-top: 0.25rem;
    margin-bottom: 0.25rem;
}
.mdt-bubble-markdown ol {
    list-style-type: decimal;
    padding-left: 1.25rem;
    margin-top: 0.25rem;
    margin-bottom: 0.25rem;
}
.mdt-bubble-markdown a {
    color: #2563eb;
    text-decoration: underline;
    font-weight: 500;
}
.dark .mdt-bubble-markdown a {
    color: #60a5fa;
}
.mdt-bubble-markdown blockquote {
    border-left: 3px solid #3b82f6;
    padding-left: 0.5rem;
    margin: 0.5rem 0;
    opacity: 0.9;
    font-style: italic;
}
</style>

<script>
const MDT_CHAT_STORAGE_KEY = 'mdt_chat_history_v1';
const MDT_CHAT_SESSION_KEY = 'mdt_chat_session_id_v1';
let mdtChatHistory = [];
let isMdtChatWaiting = false;

function getMdtSessionId() {
    let sessionId = localStorage.getItem(MDT_CHAT_SESSION_KEY);
    if (!sessionId) {
        sessionId = 'sess_' + Math.random().toString(36).substring(2, 15) + '_' + Date.now();
        localStorage.setItem(MDT_CHAT_SESSION_KEY, sessionId);
    }
    return sessionId;
}

function resetMdtSessionId() {
    const newSessionId = 'sess_' + Math.random().toString(36).substring(2, 15) + '_' + Date.now();
    localStorage.setItem(MDT_CHAT_SESSION_KEY, newSessionId);
    return newSessionId;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    getMdtSessionId();
    loadMdtChatHistory();
    // Hide teaser after 7 seconds
    setTimeout(() => {
        const teaser = document.getElementById('mdt-chat-teaser');
        if (teaser) teaser.classList.add('opacity-0', 'pointer-events-none');
    }, 7000);
});

function toggleMdtChat() {
    const windowEl = document.getElementById('mdt-chat-window');
    const iconOpen = document.getElementById('mdt-chat-icon-open');
    const iconClose = document.getElementById('mdt-chat-icon-close');
    const teaser = document.getElementById('mdt-chat-teaser');

    if (teaser) teaser.classList.add('opacity-0', 'pointer-events-none');

    const isHidden = windowEl.classList.contains('hidden');
    if (isHidden) {
        windowEl.classList.remove('hidden');
        iconOpen.classList.add('hidden');
        iconClose.classList.remove('hidden');
        scrollMdtChatBottom();
        setTimeout(() => document.getElementById('mdt-chat-input').focus(), 150);
    } else {
        windowEl.classList.add('hidden');
        iconOpen.classList.remove('hidden');
        iconClose.classList.add('hidden');
    }
}

function loadMdtChatHistory() {
    const stored = localStorage.getItem(MDT_CHAT_STORAGE_KEY);
    if (stored) {
        try {
            mdtChatHistory = JSON.parse(stored);
        } catch (e) {
            mdtChatHistory = [];
        }
    }

    const messagesContainer = document.getElementById('mdt-chat-messages');
    messagesContainer.innerHTML = '';

    // Render Initial Welcome Message if empty
    if (mdtChatHistory.length === 0) {
        appendBotMessage("Halo! 👋 Selamat datang di **MDT Solution Indonesia**.\n\nSaya adalah **MDT Assistant**, asisten AI yang siap membantu Anda:\n- 💡 Mengetahui profil & layanan kami\n- 💰 Estimasi harga dan durasi project\n- 📂 Melihat portofolio yang pernah dikerjakan\n- 📅 **Membuat jadwal konsultasi meeting gratis** langsung dengan tim kami\n\nAda yang bisa saya bantu untuk kebutuhan digital Anda hari ini?");
    } else {
        mdtChatHistory.forEach(item => {
            if (item.role === 'user') {
                renderUserBubble(item.content);
            } else {
                renderBotBubble(item.content);
            }
        });
    }
    scrollMdtChatBottom();
}

function saveMdtChatHistory() {
    localStorage.setItem(MDT_CHAT_STORAGE_KEY, JSON.stringify(mdtChatHistory));
}

function clearMdtChatHistory() {
    if (confirm('Hapus seluruh riwayat percakapan chatbot?')) {
        localStorage.removeItem(MDT_CHAT_STORAGE_KEY);
        resetMdtSessionId();
        mdtChatHistory = [];
        loadMdtChatHistory();
    }
}

function sendQuickPrompt(promptText) {
    const inputEl = document.getElementById('mdt-chat-input');
    inputEl.value = promptText;
    document.getElementById('mdt-chat-form').dispatchEvent(new Event('submit', { cancelable: true }));
}

async function handleMdtChatSubmit(e) {
    e.preventDefault();
    if (isMdtChatWaiting) return;

    const inputEl = document.getElementById('mdt-chat-input');
    const userMessage = inputEl.value.trim();
    if (!userMessage) return;

    // Reset input
    inputEl.value = '';

    // Add user message to UI and history
    renderUserBubble(userMessage);
    mdtChatHistory.push({ role: 'user', content: userMessage });
    saveMdtChatHistory();
    scrollMdtChatBottom();

    // Show Typing Indicator
    showMdtTyping();
    setMdtChatState(true);

    try {
        const sessionId = getMdtSessionId();
        const response = await fetch('/api/chatbot/message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                session_id: sessionId,
                message: userMessage,
                history: mdtChatHistory.slice(0, -1) // exclude current to avoid duplicate
            })
        });

        const data = await response.json();
        hideMdtTyping();

        if (data.session_id) {
            localStorage.setItem(MDT_CHAT_SESSION_KEY, data.session_id);
        }

        if (data.success && data.reply) {
            appendBotMessage(data.reply);
        } else {
            appendBotMessage("Mohon maaf, saat ini sedang terjadi kendala teknis pada sistem kami. Tim kami sedang menanganinya. Anda juga dapat menghubungi tim kami langsung melalui WhatsApp untuk respon cepat.");
        }
    } catch (err) {
        hideMdtTyping();
        console.error(err);
        appendBotMessage("Mohon maaf, koneksi ke sistem sedang mengalami kendala. Silakan coba beberapa saat lagi atau hubungi tim kami langsung via WhatsApp.");
    } finally {
        setMdtChatState(false);
        scrollMdtChatBottom();
    }
}

function renderUserBubble(text) {
    const messagesContainer = document.getElementById('mdt-chat-messages');
    const bubble = document.createElement('div');
    bubble.className = 'flex justify-end';
    bubble.innerHTML = `
        <div class="max-w-[85%] bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl rounded-tr-sm px-4 py-2.5 shadow-sm">
            <p class="whitespace-pre-wrap leading-relaxed text-xs sm:text-sm">${escapeHtml(text)}</p>
        </div>
    `;
    messagesContainer.appendChild(bubble);
}

function renderBotBubble(markdownText) {
    const messagesContainer = document.getElementById('mdt-chat-messages');
    const bubble = document.createElement('div');
    bubble.className = 'flex items-start gap-2.5 justify-start';

    // Parse markdown using marked.js if available
    let parsedContent = markdownText;
    if (typeof marked !== 'undefined' && marked.parse) {
        parsedContent = marked.parse(markdownText);
    } else {
        parsedContent = escapeHtml(markdownText).replace(/\n/g, '<br>');
    }

    bubble.innerHTML = `
        <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-700 text-white flex items-center justify-center shrink-0 shadow-sm mt-0.5">
            <span class="material-symbols-outlined text-[15px]">smart_toy</span>
        </div>
        <div class="max-w-[88%] bg-white dark:bg-slate-800/90 text-slate-800 dark:text-slate-100 rounded-2xl rounded-tl-sm px-4 py-3 border border-slate-200/80 dark:border-slate-700/60 shadow-sm mdt-bubble-markdown text-xs sm:text-sm leading-relaxed">
            ${parsedContent}
        </div>
    `;
    messagesContainer.appendChild(bubble);
}

function appendBotMessage(markdownText) {
    renderBotBubble(markdownText);
    mdtChatHistory.push({ role: 'assistant', content: markdownText });
    saveMdtChatHistory();
}

function showMdtTyping() {
    const messagesContainer = document.getElementById('mdt-chat-messages');
    const typing = document.createElement('div');
    typing.id = 'mdt-typing-indicator';
    typing.className = 'flex items-start gap-2.5 justify-start';
    typing.innerHTML = `
        <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-700 text-white flex items-center justify-center shrink-0 shadow-sm mt-0.5">
            <span class="material-symbols-outlined text-[15px]">smart_toy</span>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl rounded-tl-sm px-4 py-3 border border-slate-200/80 dark:border-slate-700/60 shadow-sm flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-blue-500 animate-bounce" style="animation-delay: 0ms"></span>
            <span class="w-2 h-2 rounded-full bg-indigo-500 animate-bounce" style="animation-delay: 150ms"></span>
            <span class="w-2 h-2 rounded-full bg-cyan-500 animate-bounce" style="animation-delay: 300ms"></span>
        </div>
    `;
    messagesContainer.appendChild(typing);
    scrollMdtChatBottom();
}

function hideMdtTyping() {
    const typing = document.getElementById('mdt-typing-indicator');
    if (typing) typing.remove();
}

function setMdtChatState(waiting) {
    isMdtChatWaiting = waiting;
    const btn = document.getElementById('mdt-chat-submit-btn');
    const input = document.getElementById('mdt-chat-input');
    if (btn) btn.disabled = waiting;
    if (input) input.disabled = waiting;
    if (!waiting && input) input.focus();
}

function scrollMdtChatBottom() {
    const messagesContainer = document.getElementById('mdt-chat-messages');
    if (messagesContainer) {
        setTimeout(() => {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }, 50);
    }
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}
</script>
