<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=400, initial-scale=1.0">
  <title>Chatbot Widget</title>
  <style>
    html, body {
      width: 100%;
      height: 100%;
      margin: 0;
      padding: 0;
      background: transparent;
      overflow: hidden;
    }
    #chatbot-widget {
      width: 100vw;
      height: 100vh;
      min-width: 320px;
      min-height: 400px;
      background: transparent;
    }
  </style>
</head>
<body>
<?php // include 'chatbot.php'; ?>
<script>
function persistChatState() {
  try {
    var box = document.getElementById('chatbot-box');
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
});
</script>
</body>
</html>
