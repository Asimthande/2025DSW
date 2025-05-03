<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fallback to session values if GET parameters are missing
$user_name = isset($_GET['username']) ? $_GET['username'] : ($_SESSION['first_name'] . ' ' . $_SESSION['last_name']);
$user_email = isset($_GET['email']) ? $_GET['email'] : $_SESSION['email'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Settings</title>
    <link rel="stylesheet" href="settings.css">
</head>
<body>

<div class="container">
    <h1>User Settings</h1>
    
    <div class="settings-section">
        <h2>Account Settings</h2>
        <form id="account-form" method="GET">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_name); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_email); ?>" required>

            <label for="password">New Password:</label>
            <input type="password" id="password" name="password" placeholder="Cannot update password with GET">

            <button type="submit" class="save-btn">Preview Settings</button>
        </form>
    </div>

    <div class="settings-section">
        <h2>Notification Preferences</h2>
        <form id="notifications-form" method="GET">
            <label for="email-notifications">
                <input type="checkbox" id="email-notifications" name="email-notifications"
                    <?php echo isset($_GET['email-notifications']) ? 'checked' : ''; ?>>
                Email Notifications
            </label>

            <button type="submit" class="save-btn">Preview Preferences</button>
        </form>
    </div>
</div>

<script src="Settings.js"></script>

</body>
</html>
