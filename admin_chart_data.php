<?php
require_once "partial/connect.php";

$data = [
    'students_verified' => 0,
    'students_unverified' => 0,
    'total_students' => 0,
    'drivers' => 0,
    'location_dates' => [],
    'location_counts' => [],
    'notifications' => 0
];
$students_verified = mysqli_query($conn, "SELECT COUNT(*) AS total FROM Students WHERE state = 1");
$data['students_verified'] = mysqli_fetch_assoc($students_verified)['total'];
$students_unverified = mysqli_query($conn, "SELECT COUNT(*) AS total FROM Students WHERE state = 0");
$data['students_unverified'] = mysqli_fetch_assoc($students_unverified)['total'];
$total_students = mysqli_query($conn, "SELECT COUNT(*) AS total FROM Students");
$data['total_students'] = mysqli_fetch_assoc($total_students)['total'];  // Adding total count of students
$drivers = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tblDrivers");
$data['drivers'] = mysqli_fetch_assoc($drivers)['total'];

$locations = mysqli_query($conn, "
    SELECT DATE(UpdateTime) as date, COUNT(*) as count 
    FROM location_history 
    GROUP BY DATE(UpdateTime)
    ORDER BY DATE(UpdateTime)
");

while ($row = mysqli_fetch_assoc($locations)) {
    $data['location_dates'][] = $row['date'];
    $data['location_counts'][] = $row['count'];
}
$notifications = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tblNotifications");
$data['notifications'] = mysqli_fetch_assoc($notifications)['total'];
header('Content-Type: application/json');
echo json_encode($data);
?>
