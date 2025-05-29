<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

if ($_SESSION['role'] !== 'student') {
    header("Location: dashboard.php");
    exit();
}

$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$email = $_SESSION['email'];
$student_number = $_SESSION['student_number'];
include('partial/connect.php');
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$sql = "SELECT student_number, message, timestamp, status 
        FROM tblNotifications 
        WHERE student_number = ? 
        ORDER BY timestamp DESC";

if ($sortBy === 'oldest') {
    $sql = "SELECT student_number, message, timestamp, status 
            FROM tblNotifications 
            WHERE student_number = ? 
            ORDER BY timestamp ASC";
} elseif ($sortBy === 'name') {
    $sql = "SELECT student_number, message, timestamp, status 
            FROM tblNotifications 
            WHERE student_number = ? 
            ORDER BY message ASC";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $student_number); 
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Center</title>
    <link rel="stylesheet" href="notifications.css">    
    <link rel="stylesheet" href="back.css">
        <link rel="icon" type="image/jpeg" href="images/Stabus.jpeg">
</head>
<body>
    <div class="notification-center">
        <h2>Notification Center</h2>
<div class="back-button">
    <a href="dashboard.php">&larr; Back to Dashboard</a>
</div>
        <div class="filters">
            <div class="dropdown">
                <label for="order-select">Sort Notifications:</label>
                <select id="order-select" onchange="location.href = '?sort=' + this.value;">
                    <option value="newest" <?= $sortBy === 'newest' ? 'selected' : '' ?>>Newest First</option>
                    <option value="oldest" <?= $sortBy === 'oldest' ? 'selected' : '' ?>>Oldest First</option>
                    <option value="name" <?= $sortBy === 'name' ? 'selected' : '' ?>>By Message</option>
                </select>
            </div>
        </div>
        <div class="notifications-list">
            <ul id="notifications-list">
                <?php if (empty($notifications)): ?>
                    <li>No new notifications</li>
                <?php else: ?>
                    <?php foreach ($notifications as $notification): ?>
                        <li class="notification <?= $notification['status'] == 0 ? 'unread' : 'read'; ?>" id="notification-<?= $notification['student_number']; ?>">
                            <strong>Message:</strong> <?= htmlspecialchars($notification['message']); ?><br>
                            <strong>Time:</strong> <?= date('Y-m-d H:i:s', strtotime($notification['timestamp'])); ?><br>
                            <button class="mark-as-read" onclick="markAsRead(<?= $notification['student_number']; ?>)">
                                <?= $notification['status'] == 0 ? 'Mark as Read' : 'Already Read'; ?>
                            </button>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <script src="notifications.js"></script>
</body>
</html>
