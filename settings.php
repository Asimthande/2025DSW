<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user data from session
$user_name = $_SESSION['first_name'] . ' ' . $_SESSION['last_name']; // Concatenate first and last name
$user_email = $_SESSION['email']; // The email from the session
$hashed_password = $_SESSION['user_password']; // The password stored during login

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once('db_connection.php');

    if (isset($_POST['username'], $_POST['email'], $_POST['password'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update_query = "UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("sssi", $username, $email, $hashed_password, $_SESSION['user_id']);
        } else {
            $update_query = "UPDATE users SET name = ?, email = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("ssi", $username, $email, $_SESSION['user_id']);
        }
        $stmt->execute();
    }

    if (isset($_POST['email-notifications'])) {
        $notifications = $_POST['email-notifications'] == 'on' ? 1 : 0;
        $update_notifications_query = "UPDATE users SET notifications = ? WHERE id = ?";
        $stmt = $conn->prepare($update_notifications_query);
        $stmt->bind_param("ii", $notifications, $_SESSION['user_id']);
        $stmt->execute();
    }

    header("Location: settings.php");
    exit();
}
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
        <form id="account-form" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_name); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_email); ?>" required>

            <label for="password">New Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter new password">

            <button type="submit" class="save-btn">Save Changes</button>
        </form>
    </div>

    <div class="settings-section">
        <h2>Notification Preferences</h2>
        <form id="notifications-form" method="POST">
            <label for="email-notifications">
                <input type="checkbox" id="email-notifications" name="email-notifications" <?php echo isset($_SESSION['user_notifications']) && $_SESSION['user_notifications'] ? 'checked' : ''; ?>>
                Email Notifications
            </label>

            <button type="submit" class="save-btn">Save Preferences</button>
        </form>
    </div>

</div>

<script src="Settings.js"></script>

</body>
</html>
