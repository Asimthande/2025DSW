<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <link rel="stylesheet" href="logout.css">
        <link rel="icon" type="image/jpeg" href="images/Stabus.jpeg">
</head>
<body>

<div class="container">
    <h1>You have been logged out</h1>
    <p>We're sorry to see you go. Would you like to return to the homepage or log in again?</p>

    <div class="buttons">
        <a href="index.html"><button class="btn">Go to Homepage</button></a>
        <a href="signin.php"><button class="btn">Log In Again</button></a>
    </div>

    <div id="fun-message" class="fun-message"></div>
</div>

<script src="logout.js"></script>

</body>
</html>
