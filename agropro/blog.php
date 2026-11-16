<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AgroBot - Smart Farming</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/responsive.css">
  <link rel="stylesheet" href="css/projects.css">
  <link rel="icon" href="images/fevicon.png" type="image/gif" />
  <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
  <style>
    .chat-wrap{background:#f4f7ea;padding:40px 0 60px}
    .chat-box{max-width:860px;margin:auto;background:#fff;border-radius:20px;box-shadow:0 12px 36px rgba(0,0,0,.12);overflow:hidden;display:flex;flex-direction:column;height:640px}
    .chat-top{background:linear-gradient(135deg,#6b7908,#4f5a05);color:#fff;padding:16px 22px;display:flex;align-items:center;gap:14px}
    .chat-avatar{width:46px;height:46px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff}
    .chat-top strong{display:block;font-size:18px;color:#fff}.chat-top small{color:#e9f0c0}
    .chat-top .dot{display:inline-block;width:8px;height:8px;background:#9be36b;border-radius:50%;margin-right:5px}
    .chat-log{flex:1;overflow-y:auto;padding:22px;background:#fafcf3;display:flex;flex-direction:column;gap:14px}
    .msg{max-width:80%;padding:12px 16px;border-radius:16px;line-height:1.6;font-size:15px;word-wrap:break-word;animation:pop .25s ease}
    .msg.bot{background:#fff;color:#1f2a1f;border:1px solid #e5ebd0;border-bottom-left-radius:4px;align-self:flex-start}
    .msg.user{background:#6b7908;color:#fff;border-bottom-right-radius:4px;align-self:flex-end}
    .msg ul{margin:6px 0 6px 18px;padding:0}.msg p{margin:0 0 6px;font-size:15px;line-height:1.6;color:inherit}
    .msg strong{color:inherit}
    .typing span{display:inline-block;width:8px;height:8px;margin:0 2px;background:#9aa55a;border-radius:50%;animation:bounce 1.2s infinite}
    .typing span:nth-child(2){animation-delay:.2s}.typing span:nth-child(3){animation-delay:.4s}
    .chips{display:flex;flex-wrap:wrap;gap:8px;padding:0 22px 12px;background:#fafcf3}
    .chips button{background:#fff;border:1.5px solid #6b7908;color:#6b7908;padding:6px 14px;border-radius:20px;font-size:13px;cursor:pointer;transition:.2s}
    .chips button:hover{background:#6b7908;color:#fff}
    .chat-form{display:flex;gap:10px;padding:16px 20px;border-top:1px solid #eef2dc;background:#fff}
    .chat-form input{flex:1;padding:13px 18px;border:1.5px solid #dfe5cf;border-radius:30px;font-size:15px;color:#1f2a1f}
    .chat-form input:focus{outline:0;border-color:#6b7908;box-shadow:0 0 0 3px rgba(107,121,8,.15)}
    .chat-form button{width:50px;height:50px;border-radius:50%;border:0;background:#6b7908;color:#fff;font-size:18px;cursor:pointer;transition:.2s}
    .chat-form button:hover{background:#4f5a05}.chat-form button:disabled{background:#c9cfb8}
    @keyframes bounce{0%,60%,100%{transform:translateY(0)}30%{transform:translateY(-6px)}}
    @keyframes pop{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}
    @media(max-width:600px){.chat-box{height:calc(100vh - 160px)}.msg{max-width:92%}}
  </style>
</head>
<body class="main-layout inner_page">
  <?php include('navbar.html'); ?>
  <section class="page-hero">
    <span class="badge-pill">Chatbot</span>
    <h1>Ask AgroBot</h1>
    <p>Your smart farming assistant — ask about crops, soil, fertilizer, pests, weather, markets and costs.</p>
  </section>

  <section class="chat-wrap">
    <div class="container">
      <div class="chat-box">
        <div class="chat-top">
          <div class="chat-avatar"><i class="fa fa-leaf"></i></div>
          <div><strong>AgroBot</strong><small><span class="dot"></span>Online — here to help farmers</small></div>
        </div>
        <div class="chat-log" id="log"></div>
        <div class="chips" id="chips">
          <button>Best crop for Aman season?</button>
          <button>How to control pests in rice?</button>
          <button>Which fertilizer for loamy soil?</button>
          <button>Show markets</button>
          <button>ধানের রোগ কীভাবে দমন করব?</button>
        </div>
        <form class="chat-form" id="form">
          <input id="input" type="text" placeholder="Type your farming question..." autocomplete="off" maxlength="500">
          <button id="send" type="submit" title="Send"><i class="fa fa-paper-plane"></i></button>
        </form>
      </div>
    </div>
  </section>

  <?php include 'footer.php'; ?>
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.bundle.min.js"></script>
  <script>
    const log = document.getElementById('log'), input = document.getElementById('input'), send = document.getElementById('send');
    const history = [];
    const esc = s => s.replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
    function md(t) { // minimal markdown: **bold**, - lists, line breaks
      const lines = esc(t).split('\n'); let html = '', inList = false;
      for (let l of lines) {
        l = l.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>').replace(/_(.+?)_/g, '<em>$1</em>');
        const li = l.match(/^\s*(?:[-*•]|\d+\.)\s+(.*)/);
        if (li) { if (!inList) { html += '<ul>'; inList = true; } html += '<li>' + li[1] + '</li>'; }
        else { if (inList) { html += '</ul>'; inList = false; } if (l.trim()) html += '<p>' + l + '</p>'; }
      }
      return html + (inList ? '</ul>' : '');
    }
    function add(cls, html) {
      const d = document.createElement('div'); d.className = 'msg ' + cls; d.innerHTML = html;
      log.appendChild(d); log.scrollTop = log.scrollHeight; return d;
    }
    add('bot', md("Hello! I'm **AgroBot** 🌾\nAsk me anything about farming — crops, soil, fertilizer, pests, weather, markets or costs. You can write in English or বাংলা."));

    async function ask(text) {
      text = text.trim(); if (!text) return;
      document.getElementById('chips').style.display = 'none';
      add('user', esc(text)); history.push({role: 'user', content: text});
      input.value = ''; send.disabled = true;
      const t = add('bot typing', '<span></span><span></span><span></span>');
      try {
        const r = await fetch('chat_api.php', {method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({messages: history})});
        const data = await r.json();
        t.className = 'msg bot'; t.innerHTML = md(data.reply || 'Sorry, something went wrong.');
        history.push({role: 'assistant', content: data.reply || ''});
      } catch (e) { t.className = 'msg bot'; t.innerHTML = md('Sorry, I could not reach the server. Please try again.'); history.pop(); }
      send.disabled = false; input.focus(); log.scrollTop = log.scrollHeight;
    }
    document.getElementById('form').addEventListener('submit', e => { e.preventDefault(); ask(input.value); });
    document.querySelectorAll('#chips button').forEach(b => b.onclick = () => ask(b.textContent));
  </script>
</body>
</html>
