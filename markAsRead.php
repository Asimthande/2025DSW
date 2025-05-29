<?php
session_start();

if (!isset($_GET['student_number'])) {
    die("Invalid student number");
}

$studentNumber = $_GET['student_number'];

include('partial/connect.php');
$sql = "UPDATE tblNotifications SET status = 1 WHERE student_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $studentNumber);

if ($stmt->execute()) {
    echo "Notification marked as read.";
} else {
    echo "Error marking notification as read.";
}

$stmt->close();
$conn->close();
?>
