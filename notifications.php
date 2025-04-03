<?php
// Start the session
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    // If not authenticated, redirect to the login page
    header("Location: signin.php");
    exit();
}

// Check if the user has the appropriate role (only allow students)
if ($_SESSION['role'] !== 'student') {
    // If the user is not a student, redirect to the dashboard or another page
    header("Location: dashboard.php");
    exit();
}

// Fetch user details from the session
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notification Center</title>
  <link rel="stylesheet" href="notifications.css">
</head>
<body>
  <div class="notification-center">
    <h2>Notification Center</h2>
    <div class="filters">
      <div class="dropdown">
        <label for="order-select">Sort Notifications:</label>
        <select id="order-select">
          <option value="newest">Newest First</option>
          <option value="oldest">Oldest First</option>
          <option value="name">By Name</option>
        </select>
      </div>
      <div class="dropdown">
        <label for="group-select">Group Notifications:</label>
        <select id="group-select">
          <option value="none">None</option>
          <option value="category">By Category</option>
        </select>
      </div>
    </div>
    <div class="notifications-list">
      <ul id="notifications-list">
        <li class="group">General</li>
        <li>Notification 1: Sample Notification 1</li>
        <li>Notification 2: Sample Notification 2</li>
      </ul>
    </div>
  </div>
  <script src="Notification.js"></script>
</body>
</html>
