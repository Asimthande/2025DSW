<?php
require_once "partial/connect.php";

$data = [
    'students_verified' => 0,
    'students_unverified' => 0,
    'total_students' => 0,  // New field for total students
    'drivers' => 0,
    'location_dates' => [],
    'location_counts' => [],
    'notifications' => 0
];

// Get count of verified students
$students_verified = mysqli_query($conn, "SELECT COUNT(*) AS total FROM Students WHERE state = 1");
$data['students_verified'] = mysqli_fetch_assoc($students_verified)['total'];

// Get count of unverified students
$students_unverified = mysqli_query($conn, "SELECT COUNT(*) AS total FROM Students WHERE state = 0");
$data['students_unverified'] = mysqli_fetch_assoc($students_unverified)['total'];

// Get total students count
$total_students = mysqli_query($conn, "SELECT COUNT(*) AS total FROM Students");
$data['total_students'] = mysqli_fetch_assoc($total_students)['total'];  // Adding total count of students

// Get count of drivers
$drivers = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tblDrivers");
$data['drivers'] = mysqli_fetch_assoc($drivers)['total'];

// Get location history data
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

// Get count of notifications
$notifications = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tblNotifications");
$data['notifications'] = mysqli_fetch_assoc($notifications)['total'];

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($data);
?>
