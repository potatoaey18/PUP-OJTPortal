<?php
$chatbotImageSrc = isset($chatbotImageSrc) ? $chatbotImageSrc : 'images/question.png';
?>
<style>
#chatbot-widget {
  position: fixed;
  bottom: 16px;
  right: 16px;
  z-index: 9999;
  font-family: Arial, sans-serif;
  margin-bottom: 20px;
  margin-right: 20px;
}
#chatbot-toggle {
  width: 64px;
  height: 64px;
  background: #700000;
  border: none;
  border-radius: 50%;
  box-shadow: 0 2px 8px rgba(0,0,0,0.18);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  padding: 0;
  transition: box-shadow 0.2s;
}
#chatbot-toggle img {
  width: 28px;
  height: 28px;
  object-fit: contain;
  border-radius: 50%;
  pointer-events: none;
  filter: brightness(0) invert(1);
}
#chatbot-toggle:focus {
  outline: none;
  box-shadow: 0 0 0 3px #b3d7ff;
}
#chatbot-box {
  position: absolute;
  bottom: 80px; /* place above the circle */
  right: 0;
  width: 400px;
  height: 640px;
  background: #fff;
  border: 1px solid #ccc;
  border-radius: 10px 10px 10px 10px;
  box-shadow: 0 4px 16px rgba(0,0,0,0.18);
  flex-direction: column;
  overflow: hidden;
  opacity: 0;
  transform: translateY(40px) scale(0.95);
  pointer-events: none;
  transition: opacity 0.35s cubic-bezier(.4,2,.6,1), transform 0.35s cubic-bezier(.4,2,.6,1), width 0.4s cubic-bezier(.4,2,.6,1), height 0.4s cubic-bezier(.4,2,.6,1);
  z-index: 10000;
}
#chatbot-box.closing {
  opacity: 0;
  transform: translateY(60px) scale(0.85);
  pointer-events: none;
  transition: all 0.4s ease !important;
}

#chatbot-widget.open #chatbot-box:not(.closing) {
  opacity: 1;
  transform: translateY(0) scale(1);
  pointer-events: auto;
}
#chatbot-toggle {
  z-index: 10001;
}
#chatbot-header {
  padding-bottom: 30px !important ;
  height: 60px;
  min-height: 60px;
  max-height: 60px;
  display: flex;
  align-items: center;
  box-sizing: border-box;

  background: #fff;
  color: #700000;
  padding: 28px 10px 18px 10px;
  font-weight: bold;
  text-align: left;
  border-top-left-radius: 10px;
  border-top-right-radius: 10px;
  border-bottom: 1.5px solid #ccc;
  font-size: 15px;
  min-height: 56px;
}
#chatbot-messages {
  flex: 1;
  padding: 10px;
  overflow-y: auto;
  background: #f6f6f6;
  font-size: 15px;
  margin-bottom: 62px;
}
#chatbot-input-area {
  display: flex;
  border-top: 1px solid #eee;
  background: #fafafa;
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 2;
}
#chatbot-input {
  flex: 1;
  border: none;
  padding: 10px;
  font-size: 15px;
  outline: none;
  background: #fafafa;
}
#chatbot-send {
  background: #007bff;
  color: #fff;
  border: none;
  padding: 0 18px;
  font-size: 15px;
  cursor: pointer;
  border-radius: 0 0 10px 0;
}
.chatbot-msg {
  margin: 8px 0;
  display: flex;
}
.chatbot-msg.user {
  justify-content: flex-end;
}
.chatbot-msg.bot {
  margin-left: 6px;
  justify-content: flex-start;
}
.chatbot-msg .bubble {
  white-space: pre-line;
  max-width: 80%;
  padding: 16px 16px;
  border-radius: 18px;
  line-height: 1.35;
  font-size: 15px;
}
.chatbot-msg.user .bubble {
  background: #007bff;
  color: #fff;
  border-bottom-right-radius: 4px;
}
.chatbot-msg.bot .bubble {
  background: #e9ecef;
  color: #222;
  border-bottom-left-radius: 4px;
}
</style>
<div id="chatbot-widget">
  <button id="chatbot-toggle">
    <img src="<?php echo htmlspecialchars($chatbotImageSrc); ?>" alt="Chatbot" />
  </button>
  <div id="chatbot-box">
    
    <div id="chatbot-home-view" style="position:relative; height:100%;">
      <div class="chatbot-main-bg"></div>
      <div style="position:relative; z-index:1;">
      <div id="chatbot-header-home">
        <div class="header-img-text-vertical">
  <img src="<?php echo isset($headerImgSrc) ? htmlspecialchars($headerImgSrc) : 'images/PUPLogo (1).png'; ?>" alt="Header Image" class="header-img" />
  <span class="home-title">Hi there <span style='font-size:20px;'>👋</span><br><span class="home-help">How can we help?</span></span>
</div>
      </div>

      <div class="chatbot-home-helpbox">
        <div class="chatbot-home-search" style="box-shadow:none; margin:0; border-radius:10px 10px 0 0; border-bottom:1.5px solid #eee; padding: 0; background: transparent;">
          <div class="chatbot-home-search-inner" style="display: flex; align-items: center; background: #f1f1f1; border-radius: 6px; padding: 0 8px;">
            <span style="flex: 1; padding: 14px 8px; font-size: 16px; color: #700000; background: #f1f1f1; border-radius: 6px; font-weight: 600; letter-spacing: 1px;">FAQ's</span>
            <img class="chatbot-home-search-icon" src="images/question.png" alt="Search" style="width: 20px; height: 20px; object-fit: contain; margin-left: 6px; margin-right: 8px; vertical-align: middle;" />
          </div>
        </div>
        <button class="chatbot-home-action-btn" type="button">FAQ 1<img src="images/greater-than.png" class="faq-arrow" alt=">" /></button>
        <button class="chatbot-home-action-btn" type="button">FAQ 2<img src="images/greater-than.png" class="faq-arrow" alt=">" /></button>
        <button class="chatbot-home-action-btn" type="button">FAQ 3<img src="images/greater-than.png" class="faq-arrow" alt=">" /></button>
        <button class="chatbot-home-action-btn no-border" type="button">More FAQ's<img src="images/greater-than.png" class="faq-arrow" alt=">" /></button>
        <br>
        <div class="chatbot-home-search-inner" style="margin-top: -25px; display: flex; align-items: center; background: #f1f1f1; border-radius: 6px; padding: 0 8px;">
          </div>
      </div>
      </div>
      <button id="chatbot-ask-btn">
  <span class="ask-btn-text">Ask Chatbot</span>
  <img src="<?php echo isset($askBtnImgSrc) ? htmlspecialchars($askBtnImgSrc) : 'images/submit.png'; ?>" alt="Ask Icon" class="ask-btn-img" />
</button>
      <div class="chatbot-home-footer">
        <div class="home-nav-btn active"><img src="<?php echo isset($homeIconSrc) ? htmlspecialchars($homeIconSrc) : 'images/home1.png'; ?>" alt="Home" style="width:22px;height:22px;object-fit:contain;display:block;margin:0 auto 2px auto;filter:invert(15%) sepia(100%) saturate(7487%) hue-rotate(355deg) brightness(55%) contrast(102%);" />Home</div>
      </div>
    </div>
    <style>
      .chatbot-home-action-btn {
        display: block;
        width: 100%;
        max-width: 435px;
        margin: 0 0 10px 0;
        background: rgb(255, 255, 255);
        color: #000000;
        border: none;
        border-bottom: 1.5px solid rgb(190, 190, 190);
        padding: 12px 0 12px 18px;
        font-size: 14px;
        font-weight: 300;
        cursor: pointer;
        text-align: left;
        position: relative;
      }
      .faq-arrow {
        position: absolute;
        right: 18px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        pointer-events: none;
      }
      .chatbot-home-action-btn.no-border {
        border-bottom: none;
      }
      .chatbot-home-action-btn:hover, .chatbot-home-action-btn:focus {
        color: #1976d2;
        text-decoration: underline;
        background: rgb(255, 255, 255);
      }
    </style>
    
    

    <div id="chatbot-chat-view" style="display:none; height:100%; display:flex; flex-direction:column; position:relative;">
      <div class="chatbot-main-bg"></div>
      <div style="position:relative; z-index:1; height:100%; display:flex; flex-direction:column;">
  <div id="chatbot-header" style="display: flex; align-items: center; justify-content: space-between;">
    <div style="display: flex; align-items: center;">
      <img src="<?php echo isset($chatbotBackIconSrc) ? htmlspecialchars($chatbotBackIconSrc) : 'images/less-than.png'; ?>" alt="Back" class="chatbot-back-icon" />
      <img src="<?php echo isset($chatbotHeaderImgSrc) ? htmlspecialchars($chatbotHeaderImgSrc) : 'images/pupLogo.png'; ?>" alt="Header Icon" class="chatbot-header-img" style="width:38px;height:38px;object-fit:contain;margin:0 8px;vertical-align:middle;" />
      <span class="chatbot-header-title">Chatbot</span>
    </div>
  <img id="chatbot-expand-btn" src="images/expand.png" alt="Header Right" style="height:18px;width:18px;object-fit:contain;display:block; margin-right: 10px; cursor:pointer;" />

</div>
<div class="chatbot-content-bg" style="flex:1; display:flex; flex-direction:column; background:#f6f6f6; min-height:0;">
  <div id="chatbot-info-note" style="background:#f6f6f6; padding:10px 0 8px 0; width:100%;">
    <span style="display:block; margin:0 auto; max-width:430px; width:100%; text-align:center; color:#555; font-size:14px;">If the chatbot can't resolve your issue, you may also message the admin or faculty for further assistance.</span>
  </div>
  <div id="chatbot-messages"
       style="flex:1; overflow-y:auto; max-height:100%; padding:10px; background:transparent;">
  </div>
</div>
      </div>
  <form id="chatbot-input-area" autocomplete="off" style="display:flex; align-items:center; padding:10px; background:#fff; border-top:1px solid #eee;">
    <input id="chatbot-input" type="text" placeholder="Type your message..." autocomplete="off" style="flex:1; margin-right:8px;" />
    <img id="chatbot-send-img" src="<?php echo isset($chatbotSendImgSrc) ? htmlspecialchars($chatbotSendImgSrc) : 'images/arrow.png'; ?>" alt="Send" style="width:32px;height:32px;cursor:pointer;object-fit:contain;filter: invert(14%) sepia(100%) saturate(7477%) hue-rotate(354deg) brightness(49%) contrast(107%); transition:filter 0.2s;" />
  </form>
</div>
  </div>    
</div>
<script>
function persistChatState() {
  try {
    var widget = document.getElementById('chatbot-widget');
    var isOpen = widget && widget.classList.contains('open');
    var messages = document.getElementById('chatbot-messages');
    var chatHistory = messages ? messages.innerHTML : '';
    localStorage.setItem('chatbot_isOpen', isOpen ? '1' : '0');
    localStorage.setItem('chatbot_history', chatHistory);
  } catch (e) {}
}
function restoreChatState() {
  try {
    var widget = document.getElementById('chatbot-widget');
    var messages = document.getElementById('chatbot-messages');
    if (localStorage.getItem('chatbot_isOpen') === '1') {
      widget.classList.add('open');
    }
    if (messages && localStorage.getItem('chatbot_history')) {
      messages.innerHTML = localStorage.getItem('chatbot_history');
    }
  } catch (e) {}
}
window.addEventListener('DOMContentLoaded', function() {
  restoreChatState();
  document.body.addEventListener('click', function(e) {
    setTimeout(persistChatState, 200);
  }, true);
  const logoutUrls = ['index.php', '/index.php', 'login.php', '/login.php'];
  const currentPath = window.location.pathname.split('/').pop();
  if (logoutUrls.includes(currentPath)) {
    localStorage.removeItem('chatbot_history');
    localStorage.removeItem('chatbot_isOpen');
  }
});
</script>
<style>
.chatbot-back-icon {
}
#chatbot-send-img:hover {
  filter: invert(18%) sepia(100%) saturate(7477%) hue-rotate(354deg) brightness(70%) contrast(120%);
}
.chatbot-back-icon {
  width: 22px;
  height: 22px;
  object-fit: contain;
  margin-right: 8px;
  vertical-align: middle;
  cursor: pointer;
}
#chatbot-header-home {
  background: transparent;
  color: #fff;
  padding: 32px 22px 18px 22px;
  border-top-left-radius: 10px;
  border-top-right-radius: 10px;
  font-size: 22px;
  font-weight: 600;
  letter-spacing: 0.2px;
}
.header-img-text-vertical {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}
.header-img {
  width: 48px;
  height: 48px;
  object-fit: cover;
  border-radius: 8px;
  margin-bottom: 10px;
}
#chatbot-box {
  background: linear-gradient(to bottom, #700000 0%, #fff 50%, #fff 100%) !important;
}

.home-title { font-size: 22px; font-weight: 300; }
.home-help { font-size: 23px; font-weight: 400; letter-spacing: 0.2px; }
.ask-chatbot-row {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  box-sizing: border-box;
}
#chatbot-ask-btn {
  font-family: inherit;
  font-weight: 400;
  font-size: 14px !important;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  text-align: left;
  padding-left: 20px !important;
  margin: 0;
  width: 100%;
  box-sizing: border-box;
}
.ask-btn-text {
  flex: 1;
}
.ask-btn-img {
  width: 22px;
  height: 22px;
  object-fit: contain;
  margin-left: 12px;
  margin-right: 15px;
  filter: brightness(0) invert(1);
}
.chatbot-header-title{
    margin-top:5px;
}
.chatbot-home-helpbox {
  margin: 18px 18px 0 18px;
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.4);
  border: 1.5px solid #eee;
  padding: 0;
}
.chatbot-home-search {
  display: flex;
  align-items: center;
  background: #fff;
  border-radius: 10px 10px 0 0;
  border: none;
  box-shadow: none;
  padding: 0 10px;
  margin: 0;
}
.chatbot-home-faqs {
  margin: 0;
  background: #fff;
  border-radius: 0 0 10px 10px;
  box-shadow: none;
  padding: 0;
}
.faq-item {
  padding: 7px 12px;
  border-bottom: 1px solid #ececec;
  font-size: 15px;
  cursor: pointer;
  margin: 0;
}
.faq-item:last-child { border-bottom: none; }

.chatbot-home-search-inner {
  display: flex;
  align-items: center;
  background: #f1f1f1;
  border-radius: 6px;
  width: 100%;
  padding: 0 8px;
}
#chatbot-home-search-input {
  flex: 1;
  border: none;
  padding: 14px 0;
  font-size: 15px;
  background: #f1f1f1;
  outline: none;
  border-radius: 6px;
}
.chatbot-home-search-icon {
  width: 18px;
  height: 18px;
  object-fit: contain;
  margin-left: 6px;
  vertical-align: middle;
}
.chatbot-home-faqs {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin: 18px 0 0 0;
  background: #f8f8f8;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.03);
  padding: 3px 0 8px 0;
}
.faq-box {
  max-width: 320px;
  width: 100%;
  margin: -10px 0;
  padding: 0;
  box-sizing: border-box;
}
.faq-separator {
  border: none;
  border-top: 1px solid rgb(226, 226, 226);
  margin: 0 16px;
  width: 90%;
  margin-left: 15px;
}
.faq-item {
  padding: 7px 16px;
  font-size: 14px;
  cursor: pointer;
  color:rgb(0, 0, 0);
}

#chatbot-ask-btn {
  width: calc(100% - 36px);
  margin: 18px 18px 0 18px;
  padding: 13px 0;
  background: #700000;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 400;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  transition: background 0.2s;
}
#chatbot-ask-btn:hover { background: #a00000; }
.chatbot-home-footer {
  display: flex;
  justify-content: center;
  align-items: end;
  padding: 18px 0 0 0;
  background: #fff;
  border-bottom-left-radius: 10px;
  border-bottom-right-radius: 10px;
}
.home-nav-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  font-size: 13px;
  color: #700000;
  font-weight: 500;
  padding: 20px 22px 18px 22px;
  background: #f5f5f5;
  box-shadow: 0 -4px 8px -2px rgba(0,0,0,0.2);
  width: 550px;
  margin-top: 7px;
  letter-spacing: 0.7px;
}
.home-nav-btn.active {
  background: #fff;
  color: #700000;
  border: 1.5px solid #eee;
}
</style>
<script>
(function() {
  var toggle = document.getElementById('chatbot-toggle');
  var box = document.getElementById('chatbot-box');
  var widget = document.getElementById('chatbot-widget');
  var homeView = document.getElementById('chatbot-home-view');
  var chatView = document.getElementById('chatbot-chat-view');
  var backBtn = document.querySelector('.chatbot-back-icon');
  var expandBtn = document.getElementById('chatbot-expand-btn');
  var isExpanded = false;
  if (backBtn && homeView && chatView) {
    backBtn.addEventListener('click', function() {
      var doSwitch = function() {
        chatView.style.transition = 'opacity 0.45s cubic-bezier(.4,2,.6,1), transform 0.45s cubic-bezier(.4,2,.6,1)';
        chatView.style.opacity = 0;
        chatView.style.transform = 'translateY(40px)';
        setTimeout(function() {
          chatView.style.display = 'none';
          homeView.style.display = '';
          homeView.style.opacity = 0;
          homeView.style.transform = 'translateY(40px)';
          setTimeout(function() {
            homeView.style.transition = 'opacity 0.45s cubic-bezier(.4,2,.6,1), transform 0.45s cubic-bezier(.4,2,.6,1)';
            homeView.style.opacity = 1;
            homeView.style.transform = 'translateY(0)';
            setTimeout(function() {
              homeView.style.transition = '';
            }, 450);
          }, 10);
          chatView.style.opacity = 1;
          chatView.style.transform = 'translateY(0)';
        }, 450);
      };
      if (isExpanded) {
        box.style.transition = 'width 0.4s cubic-bezier(.4,2,.6,1), height 0.4s cubic-bezier(.4,2,.6,1)';
        box.style.width = '400px';
        box.style.height = '640px';
        isExpanded = false;
        setTimeout(doSwitch, 400);
      } else {
        doSwitch();
      }
    });
  }
  if (expandBtn) {
    expandBtn.addEventListener('click', function() {
      if (!isExpanded) {
        box.style.transition = 'width 0.4s cubic-bezier(.4,2,.6,1), height 0.4s cubic-bezier(.4,2,.6,1)';
        box.style.width = '540px';
        box.style.height = '780px';
        isExpanded = true;
      } else {
        box.style.transition = 'width 0.4s cubic-bezier(.4,2,.6,1), height 0.4s cubic-bezier(.4,2,.6,1)';
        box.style.width = '400px';
        box.style.height = '640px';
        isExpanded = false;
      }
    });
  }
  var askBtn = document.getElementById('chatbot-ask-btn');
  var messages = document.getElementById('chatbot-messages');
  var input = document.getElementById('chatbot-input');
  var form = document.getElementById('chatbot-input-area');
  var sendImg = document.getElementById('chatbot-send-img');
  if (sendImg) {
    sendImg.addEventListener('click', function(e) {
      e.preventDefault();
      form.requestSubmit();
    });
  }

  toggle.onclick = function() {
  var isOpen = widget.classList.contains('open');
  if (!isOpen) {
    widget.classList.add('open');
    box.style.width  = '';  
    box.style.height = '';  
    isExpanded = false;     
    setTimeout(function() { input && input.focus(); }, 350);
    homeView.style.display = '';
    chatView.style.display = 'none';
  } else {
    box.classList.add("closing");
    box.style.width  = '';
    box.style.height = '';
    isExpanded = false;

    setTimeout(() => {
      widget.classList.remove("open");
      box.classList.remove("closing");
    }, 350);  
  }
};


  var chatbotGreetingShown = false;
  askBtn.onclick = function() {
    chatView.style.opacity = 0;
    chatView.style.transform = 'translateY(40px)';
    chatView.style.display = '';
    setTimeout(function() {
      homeView.style.display = 'none';
      chatView.style.transition = 'opacity 0.5s cubic-bezier(.4,2,.6,1), transform 0.5s cubic-bezier(.4,2,.6,1)';
      chatView.style.opacity = 1;
      chatView.style.transform = 'translateY(0)';
      setTimeout(function() { input && input.focus(); }, 350);
      if (messages && messages.childElementCount === 0) {
        chatbotGreetingShown = false;
        sendToBackend('__init__');
      }
    }, 50);
  };

  function sendToBackend(msg) {
    var isGreeting = (msg === '__init__' && !chatbotGreetingShown);
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'chatbot_backend.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4 && xhr.status === 200) {
        var response = xhr.responseText;
        var reply = '';
        try {
          var data = JSON.parse(response);
          if (data.reply) reply = data.reply;
          else reply = response;
        } catch (e) {
          reply = response;
        }
        if (isGreeting) {
          addMessage(reply, 'bot');
          chatbotGreetingShown = true;
        } else {
          var typingDiv = addTypingIndicatorJS();
          setTimeout(function() {
            removeTypingIndicatorJS(typingDiv);
            addMessage(reply, 'bot');
          }, 400 + Math.min(reply.length * 8, 300));
        }
      }
    };
    xhr.send('message=' + encodeURIComponent(msg));
  }

  document.addEventListener('click', function(e) {
    if (!widget.contains(e.target) && widget.classList.contains('open')) {
      widget.classList.remove('open');
    }
  });

  function addTypingIndicatorJS() {
    var typingDiv = document.createElement('div');
    typingDiv.className = 'chatbot-msg bot typing-indicator';
    var bubble = document.createElement('div');
    bubble.className = 'bubble';
    for (let i = 0; i < 3; i++) {
      let dot = document.createElement('span');
      dot.style.display = 'inline-block';
      dot.style.width = '10px';
      dot.style.height = '10px';
      dot.style.marginRight = '4px';
      dot.style.background = '#bbb';
      dot.style.borderRadius = '50%';
      dot.style.opacity = '0.7';
      dot.style.animation = `typingBlinkJS 1.2s ${(i*0.2)}s infinite both`;
      dot.className = 'typing-dot-js';
      bubble.appendChild(dot);
    }
    typingDiv.appendChild(bubble);
    messages.appendChild(typingDiv);
    messages.scrollTop = messages.scrollHeight;
    return typingDiv;
  }

  (function addTypingKeyframes() {
    if (!document.getElementById('typingBlinkJSStyle')) {
      var style = document.createElement('style');
      style.id = 'typingBlinkJSStyle';
      style.innerHTML = `@keyframes typingBlinkJS {
        0%, 80%, 100% { opacity: 0.7; }
        40% { opacity: 1; }
      }`;
      document.head.appendChild(style);
    }
  })();

  function removeTypingIndicatorJS(typingDiv) {
    if (typingDiv && typingDiv.parentNode) {
      typingDiv.parentNode.removeChild(typingDiv);
    }
  }

  function addMessage(text, sender) {
    var msgDiv = document.createElement('div');
    msgDiv.className = 'chatbot-msg ' + sender;
    var bubble = document.createElement('div');
    bubble.className = 'bubble';
    if (sender === 'bot') {
      var botLabelRow = document.createElement('div');
      botLabelRow.style.display = 'flex';
      botLabelRow.style.alignItems = 'center';
      botLabelRow.style.marginBottom = '2px';
      var botImg = document.createElement('img');
      botImg.src = typeof chatbotImageSrc !== 'undefined' ? chatbotImageSrc : 'images/PUPLogo (1).png';
      botImg.alt = 'Chatbot';
      botImg.style.width = '18px';
      botImg.style.height = '18px';
      botImg.style.objectFit = 'contain';
      botImg.style.marginRight = '6px';
      var botLabel = document.createElement('b');
      botLabel.textContent = 'PUP Chatbot';
      botLabel.style.fontSize = '13px';
      botLabelRow.appendChild(botImg);
      botLabelRow.appendChild(botLabel);
      bubble.appendChild(botLabelRow);
    }
    var msgText = document.createElement('div');
    msgText.innerHTML = text;
    msgText.style.fontSize = '13px';
    msgText.style.fontWeight = '500';
    bubble.appendChild(msgText);
    bubble.style.opacity = 0;
    bubble.style.transform = 'translateY(20px)';
    bubble.style.transition = 'opacity 0.4s cubic-bezier(.4,2,.6,1), transform 0.4s cubic-bezier(.4,2,.6,1)';
    msgDiv.appendChild(bubble);
    messages.appendChild(msgDiv);
    messages.scrollTop = messages.scrollHeight;
    setTimeout(function() {
      bubble.style.opacity = 1;
      bubble.style.transform = 'translateY(0)';
    }, 10);
  }

  function sendToBackend(userText) {
    var typingDiv = addTypingIndicatorJS();
    var delay = 1000 + Math.floor(Math.random() * 2000); 
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'chatbot_backend.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4) {
        var reply = 'Sorry, there was a problem contacting the assistant.';
        if (xhr.status === 200) {
          try {
            var data = JSON.parse(xhr.responseText);
            if (data.reply) reply = data.reply;
          } catch (e) {}
        }
        setTimeout(function() {
          removeTypingIndicatorJS(typingDiv);
          addMessage(reply, 'bot');
        }, delay);
      }
    };
    xhr.send('message=' + encodeURIComponent(userText));
  }

  form.onsubmit = function(e) {
    e.preventDefault();
    var userText = input.value.trim();
    if (!userText) return;
    addMessage(userText, 'user');
    sendToBackend(userText);
    input.value = '';
  };

})();
</script>
