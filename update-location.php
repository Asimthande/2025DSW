<?php
session_start();
include 'partial/connect.php';

if (!isset($_SESSION['bus_id'])) exit;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $longitude = floatval($_POST['longitude']);
    $latitude = floatval($_POST['latitude']);
    $storeHistory = ($_POST['storeHistory'] === 'true');
    $busID = $_SESSION['bus_id'];
    $updateTime = date('Y-m-d H:i:s');
    $check = $conn->prepare("SELECT ID FROM live WHERE BusID = ?");
    $check->bind_param("s", $busID);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE live SET Longitude = ?, Latitude = ?, UpdateTime = ? WHERE BusID = ?");
        $stmt->bind_param("ddss", $longitude, $latitude, $updateTime, $busID);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $conn->prepare("INSERT INTO live (Longitude, Latitude, UpdateTime, BusID) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ddss", $longitude, $latitude, $updateTime, $busID);
        $stmt->execute();
        $stmt->close();
    }

    $check->close();
    if ($storeHistory) {
        $stmt2 = $conn->prepare("INSERT INTO location_history (Longitude, Latitude, UpdateTime, BusID) VALUES (?, ?, ?, ?)");
        $stmt2->bind_param("ddss", $longitude, $latitude, $updateTime, $busID);
        $stmt2->execute();
        $stmt2->close();
    }

    $conn->close();
}
?>
