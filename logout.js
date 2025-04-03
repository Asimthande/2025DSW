// logout.js

// Array of fun messages
const funMessages = [
    "Oops! Looks like you’ve been logged out. But hey, it happens!",
    "Well, you gave it a good try! Come back soon!",
    "Logging out is like closing a book... but don’t worry, you’ll open it again!",
    "Don’t worry, we’ll save your spot! Come back when you're ready!",
    "Adios, amigo! See you soon on the other side!",
    "We hope you had fun while you were here. Come back soon!",
    "You’ve officially logged out... but you can always log back in 😉!",
    "Hasta la vista, baby! But we’ll be waiting for you.",
    "The logout journey is complete. Now, go conquer the world!",
    "Goodbye, for now. We’ll keep the lights on for your return!",
    "You hit the logout button like a pro! Catch you later!",
    "Logged out and ready for new adventures. See you soon!",
    "Take a break! We’ll be here when you’re ready to come back.",
    "Your logout status: Confirmed! But don’t be a stranger.",
    "And with that, you’ve logged out... but don’t stay away too long!",
    "The logout button strikes again! But you’ll always have a way back.",
    "No worries, your account is just a click away from coming back!",
    "Logging out is like hitting refresh. See you when you come back!",
    "Well, that was easy! Let’s make it even easier to log in again!",
    "You’ve logged out like a champ! Now go do something awesome!",
    "Goodbye for now. We know you’ll be back before we know it!",
    "That’s one way to close the door... but we’ll leave the window open!"
];

// Handle "Go to Homepage" button click
document.getElementById('home-btn').addEventListener('click', function() {
    // Redirect to the homepage (you can adjust the URL)
    window.location.href = '/index.php';  // Example redirect to homepage
});

// Handle "Log In Again" button click
document.getElementById('login-btn').addEventListener('click', function() {
    // Redirect to the signin.php page located in the DSW folder
    window.location.href = '/DSW/signin.php';  // Absolute path from the root directory
});

// Display fun messages at 7-second intervals
let messageIndex = 0;

function showFunMessage() {
    const messageElement = document.getElementById('fun-message');
    
    messageElement.textContent = funMessages[messageIndex];
    messageElement.style.display = 'block';  // Make the message visible

    // Hide the message after 5 seconds
    setTimeout(() => {
        messageElement.style.display = 'none';
    }, 5000); // Hide after 5 seconds

    // Increment message index and loop back to the start if necessary
    messageIndex = (messageIndex + 1) % funMessages.length;
}

// Show a fun message every 7 seconds
setInterval(showFunMessage, 7000);

// Initial call to show the first message immediately
showFunMessage();
