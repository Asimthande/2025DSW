<?php
session_start();

if (!isset($_SESSION['student_number'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['notification_id'])) {
    header("Location: notifications.php");
    exit();
}

$notification_id = intval($_POST['notification_id']);

include('partial/connect.php');

$stmt = $conn->prepare("UPDATE tblNotifications SET status = 'read' WHERE id = ?");
$stmt->bind_param("i", $notification_id);
$stmt->execute();
$stmt->close();
$conn->close();

header("Location: notifications.php");
exit();
