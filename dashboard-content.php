<?php
include "partial/connect.php";
session_start();

$studentNumber = isset($_SESSION['student_number']) ? $_SESSION['student_number'] : null;

if ($studentNumber) {
    $stmt = $conn->prepare("SELECT message, timestamp FROM tblNotifications WHERE student_number = ? OR student_number = '99' ORDER BY timestamp DESC LIMIT 10");
    $stmt->bind_param("s", $studentNumber);
} else {
    $stmt = $conn->prepare("SELECT message, timestamp FROM tblNotifications WHERE student_number = '99' ORDER BY timestamp DESC LIMIT 10");
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<li onclick=\"window.location.href='notifications.php'\"><strong>{$row['timestamp']}</strong> : {$row['message']}</li>";
        echo "<hr>";
    }
} else {
    echo "<li>No alerts available.</li>";
}

if (!$studentNumber) {
    echo "<li><a href='signin.php'>Log In</a></li>"; 
}

$stmt->close();
?>
