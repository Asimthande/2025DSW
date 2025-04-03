<?php
// Start the session
session_start();

// Destroy all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to the splash page after logging out
header("Location: splash.php");
exit();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <link rel="stylesheet" href="logout.css">
</head>
<body>

<div class="container">
    <h1>You have been logged out</h1>
    <p>We're sorry to see you go. Would you like to return to the homepage or log in again?</p>

    <div class="buttons">
        <a href="" onclick="window.location.href='splash.php'"><button id="home-btn" class="btn">Go to Homepage</button></a>
        <a href="" onclick="window.location.href='signin.php'"><button id="login-btn" class="btn">Log In Again</button></a>
    </div>

    <div id="fun-message" class="fun-message"></div>
</div>

<script src="logout.js"></script>

</body>
</html>
