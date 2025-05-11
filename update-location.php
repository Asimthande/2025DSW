<?php
session_start();

// Include the database connection file
include('partial/connect.php');

// Ensure the user is logged in and has a valid bus ID
if (!isset($_SESSION['bus_id'])) {
    die("❌ You are not logged in.");
}

// Get the bus ID from the session
$busID = $_SESSION['bus_id'];

// Get the data sent from the front-end (location)
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];

// Current timestamp for the update
$updateTime = date('Y-m-d H:i:s');

// Insert or update the live location for this bus
$sql = "INSERT INTO live (Latitude, Longitude, UpdateTime, BusID) VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE Latitude = ?, Longitude = ?, UpdateTime = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ddssddss", $latitude, $longitude, $updateTime, $busID, $latitude, $longitude, $updateTime);
$stmt->execute();

echo "✅ Location updated successfully!";
?>
