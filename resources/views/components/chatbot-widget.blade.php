{{-- Chatbot AI NovaPhone (Gemini) --}}
<div id="nv-chatbot">
    <button id="nv-chat-toggle" aria-label="Mở chat tư vấn">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
        </svg>
    </button>

    <div id="nv-chat-panel" hidden>
        <div class="nv-chat-header">
            <strong>Trợ lý NovaPhone</strong>
            <button id="nv-chat-close" aria-label="Đóng">&times;</button>
        </div>
        <div id="nv-chat-messages">
            <div class="nv-msg assistant">Xin chào! Mình có thể tư vấn điện thoại theo nhu cầu và ngân sách của bạn. Bạn cần gì nào?</div>
        </div>
        <div class="nv-chat-input">
            <input id="nv-chat-text" type="text" placeholder="Hỏi về sản phẩm, giá..." autocomplete="off">
            <button id="nv-chat-send">Gửi</button>
        </div>
    </div>
</div>

<style>
#nv-chatbot { position: fixed; right: 20px; bottom: 20px; z-index: 9999; font-size: 14px; }
#nv-chat-toggle {
    width: 56px; height: 56px; border-radius: 50%; border: none; cursor: pointer;
    background: #2563eb; color: #fff; display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 14px rgba(0,0,0,.25); transition: all .2s ease-in-out;
}
#nv-chat-toggle:hover { transform: scale(1.08); }
#nv-chat-panel {
    position: absolute; right: 0; bottom: 70px; width: 340px; max-width: calc(100vw - 40px);
    height: 460px; background: #fff; border-radius: 14px; overflow: hidden;
    display: flex; flex-direction: column; box-shadow: 0 10px 40px rgba(0,0,0,.25);
}
.nv-chat-header {
    background: #2563eb; color: #fff; padding: 12px 14px;
    display: flex; justify-content: space-between; align-items: center;
}
#nv-chat-close { background: none; border: none; color: #fff; font-size: 20px; cursor: pointer; }
#nv-chat-messages { flex: 1; overflow-y: auto; padding: 12px; display: flex; flex-direction: column; gap: 8px; }
.nv-msg { max-width: 85%; padding: 8px 12px; border-radius: 12px; line-height: 1.45; white-space: pre-wrap; word-break: break-word; }
.nv-msg.user { align-self: flex-end; background: #2563eb; color: #fff; border-bottom-right-radius: 4px; }
.nv-msg.assistant { align-self: flex-start; background: #f1f5f9; color: #111; border-bottom-left-radius: 4px; }
.nv-msg.loading { color: #64748b; font-style: italic; animation: nv-pulse 1.2s ease-in-out infinite; }
.nv-msg-img { display: block; max-width: 140px; border-radius: 8px; margin: 4px 0; }
.nv-msg.assistant a { color: #2563eb; font-weight: 600; text-decoration: underline; }
@keyframes nv-pulse { 50% { opacity: .45; } }
.nv-chat-input { display: flex; gap: 8px; padding: 10px; border-top: 1px solid #e2e8f0; }
.nv-chat-input input { flex: 1; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 10px; outline: none; }
.nv-chat-input input:focus { border-color: #2563eb; }
.nv-chat-input button {
    background: #2563eb; color: #fff; border: none; border-radius: 8px;
    padding: 8px 14px; cursor: pointer; transition: all .2s ease-in-out;
}
.nv-chat-input button:disabled { opacity: .5; cursor: default; }
</style>

<script>
(function () {
    const toggle   = document.getElementById('nv-chat-toggle');
    const panel    = document.getElementById('nv-chat-panel');
    const closeBtn = document.getElementById('nv-chat-close');
    const box      = document.getElementById('nv-chat-messages');
    const input    = document.getElementById('nv-chat-text');
    const sendBtn  = document.getElementById('nv-chat-send');

    // Lịch sử hội thoại gửi kèm mỗi lượt (API stateless)
    let history = [];

    toggle.addEventListener('click', () => { panel.hidden = !panel.hidden; if (!panel.hidden) input.focus(); });
    closeBtn.addEventListener('click', () => { panel.hidden = true; });

    // Escape HTML rồi mới chuyển markdown ảnh/link thành thẻ -> an toàn XSS
    function renderMarkdown(text) {
        const esc = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        return esc
            .replace(/!\[([^\]]*)\]\((https?:\/\/[^\s)]+)\)/g,
                '<img src="$2" alt="$1" class="nv-msg-img" loading="lazy">')
            .replace(/\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)/g,
                '<a href="$2" target="_blank" rel="noopener">$1</a>')
            .replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
    }

    function addMsg(role, text, extraClass = '') {
        const div = document.createElement('div');
        div.className = 'nv-msg ' + role + (extraClass ? ' ' + extraClass : '');
        if (role === 'assistant' && !extraClass) {
            div.innerHTML = renderMarkdown(text);
        } else {
            div.textContent = text;
        }
        box.appendChild(div);
        box.scrollTop = box.scrollHeight;
        return div;
    }

    async function send() {
        const text = input.value.trim();
        if (!text || sendBtn.disabled) return;
        input.value = '';
        addMsg('user', text);
        history.push({ role: 'user', content: text });

        sendBtn.disabled = true;
        const loading = addMsg('assistant', 'Đang trả lời…', 'loading');
        try {
            const res = await fetch('/api/chatbot', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                // Chỉ gửi 10 lượt gần nhất để tiết kiệm token
                body: JSON.stringify({ messages: history.slice(-10) }),
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const data = await res.json();
            loading.remove();
            addMsg('assistant', data.reply);
            history.push({ role: 'assistant', content: data.reply });
        } catch (e) {
            loading.remove();
            addMsg('assistant', 'Xin lỗi, có lỗi xảy ra. Bạn thử lại sau nhé.');
        } finally {
            sendBtn.disabled = false;
            input.focus();
        }
    }

    sendBtn.addEventListener('click', send);
    input.addEventListener('keydown', (e) => { if (e.key === 'Enter') send(); });
})();
</script>
