<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>UJ Stabus Chatbot AI</title>
  <link rel="stylesheet" href="AI.css" />
</head>
<body>
  <div class="chat-container">
    <h2>UJ Stabus Chatbot</h2>
    <div id="chatbox">
      <div class="message user-message">
        <div class="avatar" style="background-image: url('images/User.png');"></div>
      </div>
      <div class="message bot-message">
        <div class="avatar" style="background-image: url('images/Chatbot Chat Message.jpg');"></div>
      </div>
    </div>
    <input
      type="text"
      id="userInput"
      placeholder="Ask me anything about UJ Stabus..."
      autocomplete="off"
      onkeydown="if(event.key==='Enter'){sendMessage();}"
    />
    <button onclick="sendMessage()">Send</button>
  </div>

  <script src="AI.js"></script>
</body>
</html>
