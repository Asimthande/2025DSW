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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Driver Dashboard</title>
    <link rel="stylesheet" href="driver.css">
<script src="language.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <a href="logout.php" class="logout-btn">Logout</a>
        <h2>Driver Live Location</h2>

        <p><strong>Bus ID:</strong> <?= $busID ?></p>

        <!-- Starting Point Selection -->
        <label for="start">Starting Point:</label>
        <select id="start" name="start" required>
            <option value="">-- Select Start --</option>
            <option value="APB">APB</option>
            <option value="APK">APK</option>
            <option value="DFC">DFC</option>
            <option value="SWC">SWC</option>
        </select>

        <!-- Ending Point Selection -->
        <label for="end">Ending Point:</label>
        <select id="end" name="end" required>
            <option value="">-- Select End --</option>
            <option value="APB">APB</option>
            <option value="APK">APK</option>
            <option value="DFC">DFC</option>
            <option value="SWC">SWC</option>
        </select>

        <!-- Departure Time -->
        <label for="departure-time">Departure Time:</label>
        <input type="time" id="departure-time" name="departure-time" required min="06:00" max="22:00">
        <small>(Time must be between 06:00 and 22:00)</small>

        <!-- Buttons to Start and Stop Location Sharing -->
        <div class="btn-container">
            <button class="start-btn" onclick="startSharing()">📡 Start Sharing</button>
            <button class="stop-btn" onclick="stopSharing()" disabled>🛑 Stop Sharing</button>
        </div>

        <button type="button" onclick="updateSchedule()">Set Schedule</button>

        <script>
            let intervalId = null;

            // Function to start sharing location
            function startSharing() {
                if (!intervalId) {
                    updateLocation();
                    intervalId = setInterval(updateLocation, 5000);
                    document.querySelector(".start-btn").disabled = true;
                    document.querySelector(".stop-btn").disabled = false;
                    document.getElementById("status-text").innerText = "✅ Sharing";
                }
            }

            // Function to stop sharing location
            function stopSharing() {
                if (intervalId) {
                    clearInterval(intervalId);
                    intervalId = null;
                    document.querySelector(".start-btn").disabled = false;
                    document.querySelector(".stop-btn").disabled = true;
                    document.getElementById("status-text").innerText = "❌ Not Sharing";
                }
            }

            // Function to update location every 5 seconds
            function updateLocation() {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;
                    const busID = <?= $busID ?>;
                    const departureTime = document.getElementById("departure-time").value;
                    const start = document.getElementById("start").value;
                    const end = document.getElementById("end").value;

                    // Ensure all fields are filled before sending the request
                    if (!busID || !departureTime || !start || !end) {
                        alert("Please fill all required fields.");
                        stopSharing();
                        return;
                    }

                    // Send location to update-location.php
                    $.post("update-location.php", {
                        latitude: latitude,
                        longitude: longitude,
                        BusID: busID
                    }, function(response) {
                        console.log(response);
                    });
                }, function(error) {
                    console.error("Error getting location:", error.message);
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });
            }

            // Function to update the bus schedule
            function updateSchedule() {
                const departureTime = document.getElementById("departure-time").value;
                const start = document.getElementById("start").value;
                const end = document.getElementById("end").value;

                // Check if the start and end locations are the same
                if (start === end) {
                    alert("❌ Start location and End location cannot be the same.");
                    return;
                }

                // Check if all fields are filled
                if (!departureTime || !start || !end) {
                    alert("❌ Please fill all required fields.");
                    return;
                }

                // Send the schedule data to update-schedule.php
                $.post("update-schedule.php", {
                    departure-time: departureTime,
                    start: start,
                    end: end
                }, function(response) {
                    alert(response); // Display the response from the server
                });
            }
        </script>

    </div>
</body>
</html>
