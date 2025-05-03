<?php
session_start();
include 'partial/connect.php';

$student_number = $_SESSION['student_number'] ?? '';
$bus_number_plate = $_SESSION['bus_number_plate'] ?? '';
$driver_id = $_SESSION['driver_id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['emergency-type'] ?? '';
    $studentNumber = $_POST['student-number'] ?? '';
    $busID = $_POST['bus-number-plate'] ?? '';
    $driverID = $_POST['driver-id'] ?? '';
    $situation = $_POST['emergency-comment'] ?? '';
    $latitude = isset($_POST['latitude']) ? floatval($_POST['latitude']) : null;
    $longitude = isset($_POST['longitude']) ? floatval($_POST['longitude']) : null;

    if ($type && $studentNumber && $busID && $latitude !== null && $longitude !== null && $situation) {
        $stmt = $conn->prepare("INSERT INTO Emergency (Type, StudentNumber, BusID, Latitude, Longitude, Situation) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdds", $type, $studentNumber, $busID, $latitude, $longitude, $situation);

        if ($stmt->execute()) {
            echo "<script>alert('Emergency report submitted successfully.'); window.location.href='dashboard.php';</script>";
        } else {
            echo "<script>alert('Failed to submit emergency report.');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Please fill in all required fields including location.');</script>";
    }

    $conn->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Reporting System</title>
    <link rel="stylesheet" href="emergency-alert.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>
<body>
<div class="container">
    <h1>Emergency Reporting System</h1>
    <form id="emergency-form" method="POST">
        <label for="emergency-type">Select Emergency Type:</label>
        <select id="emergency-type" name="emergency-type" required>
            <option value="">--Please Choose an Option--</option>
            <option value="medical">Medical Emergency</option>
            <option value="accident">Accident</option>
            <option value="fire">Fire</option>
            <option value="misconduct">Misconduct</option>
        </select>

        <label for="student-number">Student Number:</label>
        <input type="text" id="student-number" name="student-number" required value="<?= htmlspecialchars($student_number); ?>">

        <label for="bus-number-plate">Bus Number Plate:</label>
        <input type="text" id="bus-number-plate" name="bus-number-plate" required value="<?= htmlspecialchars($bus_number_plate); ?>">

        <label for="driver-id">Driver ID:</label>
        <input type="text" id="driver-id" name="driver-id" value="<?= htmlspecialchars($driver_id); ?>">

        <label for="emergency-comment">Describe the Emergency:</label>
        <textarea id="emergency-comment" name="emergency-comment" rows="4" required></textarea>

        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">

        <button type="button" id="share-location-btn">Share Location (Double Click)</button>
        <button type="submit" id="submit-btn">Submit Report</button>
    </form>

    <div id="response-message"></div>
    <div id="map-container">
        <div id="map" style="height: 300px;"></div>
        <p id="coordinates"></p>
    </div>

    <button id="logout-btn" onclick="logout()">Logout</button>
</div>

<script>
function logout() {
    window.location.href = 'logout.php';
}

let map;
let marker;

// Initialize Leaflet map
window.onload = function () {
    map = L.map('map').setView([0, 0], 2);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
};

document.getElementById("share-location-btn").addEventListener("dblclick", () => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position) => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            document.getElementById("latitude").value = lat;
            document.getElementById("longitude").value = lng;

            if (marker) {
                map.removeLayer(marker);
            }

            map.setView([lat, lng], 15);
            marker = L.marker([lat, lng]).addTo(map).bindPopup("Your location").openPopup();

            document.getElementById("coordinates").innerText = `Latitude: ${lat.toFixed(6)}, Longitude: ${lng.toFixed(6)}`;
        }, () => {
            alert("Unable to retrieve your location.");
        });
    } else {
        alert("Geolocation is not supported by your browser.");
    }
});
</script>
</body>
</html>
