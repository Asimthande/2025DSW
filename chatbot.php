<?php
$message = strtolower(trim($_POST['message'] ?? ''));

$response = "Sorry, I don't understand that yet.";

if (strpos($message, 'hello') !== false) {
  $response = "Hello! How can I help you?";
} elseif (strpos($message, 'help') !== false) {
  $response = "Sure! Ask me anything.";
} elseif (strpos($message, 'bye') !== false) {
  $response = "Goodbye! Have a nice day!";
} elseif (strpos($message, 'name') !== false) {
  $response = "I'm your friendly free chatbot.";
}

echo $response;
?>
