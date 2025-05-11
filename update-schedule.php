<?php
session_start();
include 'partial/connect.php';

// Ensure the user is logged in and has a valid bus ID
if (!isset($_SESSION['bus_id'])) {
    die("❌ You are not logged in.");
}

// Get the bus ID from the session
$busID = $_SESSION['bus_id'];

// Get the data sent from the front-end (schedule info)
$departureTime = $_POST['departure-time'];
$start = $_POST['start'];
$end = $_POST['end'];

// Fetch the route_id from tblBusRoutes based on the start and end locations
$sql = "SELECT route_id FROM tblBusRoutes WHERE start_location = ? AND end_location = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $start, $end);
$stmt->execute();
$result = $stmt->get_result();

// Check if a route was found
if ($result->num_rows === 0) {
    die("❌ No route found for the selected start and end locations.");
}

$route = $result->fetch_assoc();
$route_id = $route['route_id']; // Get the route_id

// Check if a schedule already exists for this bus and departure time
$sql = "SELECT * FROM tblBusSchedules WHERE bus_id = ? AND departure_time = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $busID, $departureTime);
$stmt->execute();
$result = $stmt->get_result();

// If the schedule already exists, do nothing
if ($result->num_rows > 0) {
    echo "❌ Schedule already set for this bus.";
    exit();
}

// Insert the new schedule into tblBusSchedules
$sql = "INSERT INTO tblBusSchedules (bus_id, departure_time, eta, route_id) VALUES (?, ?, ?, ?)";
$eta = date('H:i', strtotime($departureTime . ' + 2 hours')); // ETA is 2 hours after departure time
$stmt = $conn->prepare($sql);
$stmt->bind_param("issi", $busID, $departureTime, $eta, $route_id);
$stmt->execute();

echo "✅ Schedule set successfully!";
?>
