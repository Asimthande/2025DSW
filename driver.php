<?php
session_start();
include('partial/connect.php');

if (!isset($_SESSION['bus_id'])) {
    die("❌ You are not logged in.");
}

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
    <link rel='stylesheet' href='driver.css'>
</head>
<body>
    <div class="container">
        <a href="logout.php" class="logout-btn">Logout</a>
        <h2>Driver Dashboard</h2>
        <p><strong>Bus ID:</strong> <?= htmlspecialchars($busID) ?></p>

        <!-- Schedule -->
        <div>
            <h3>Set Your Schedule</h3>
            <label for="start">Starting Point:</label>
            <select id="start" required onchange="updateDuration()">
                <option value="">-- Select Start --</option>
                <option value="APB">APB</option>
                <option value="APK">APK</option>
                <option value="DFC">DFC</option>
                <option value="SWC">SWC</option>
            </select>

            <label for="end">Ending Point:</label>
            <select id="end" required onchange="updateDuration()">
                <option value="">-- Select End --</option>
                <option value="APB">APB</option>
                <option value="APK">APK</option>
                <option value="DFC">DFC</option>
                <option value="SWC">SWC</option>
            </select>

            <div class="duration-info" id="duration-info"></div>

            <label for="departure-time">Departure Time:</label>
            <input type="time" id="departure-time" min="06:00" max="22:00" required>
            <small>(Time must be between 06:00 and 22:00)</small><br>

            <div class="btn-container">
                <button onclick="updateSchedule()">Set Schedule</button>
            </div>
        </div>

        <!-- Location Sharing -->
        <div class="sharing-controls">
            <h3>Live Location Sharing</h3>
            <button class="start-btn" onclick="startSharing()">📡 Start Sharing</button>
            <button class="stop-btn" onclick="stopSharing()" disabled>🛑 Stop Sharing</button>
            <p id="status-text">❌ Not Sharing</p>
        </div>

        <!-- Stats Section -->
        <div class="stats-container">
            <h3>Stats & Analysis</h3>
            <ul>
                <li>Total Trips Today: <strong>5</strong></li>
                <li>Average Delay: <strong>3 mins</strong></li>
                <li>Fuel Consumption: <strong>12.5L</strong></li>
                <li>Passenger Feedback: <strong>👍 92% Positive</strong></li>
            </ul>
        </div>
    </div>

    <script>
        const durations = {
            "APK-SWC": "25–35 min",
            "APK-DFC": "12–18 min",
            "SWC-DFC": "20–30 min",
            "APB-APK": "10–15 min",
            "APB-DFC": "15–20 min",
            "APB-SWC": "30–40 min",
            "SWC-APK": "25–35 min",
            "DFC-APK": "12–18 min",
            "DFC-SWC": "20–30 min",
            "APK-APB": "10–15 min",
            "DFC-APB": "15–20 min",
            "SWC-APB": "30–40 min"
        };

        let intervalId = null;

        function updateDuration() {
            const start = document.getElementById("start").value;
            const end = document.getElementById("end").value;
            const info = document.getElementById("duration-info");

            if (start && end && start !== end) {
                const key = `${start}-${end}`;
                const reverseKey = `${end}-${start}`;
                const eta = durations[key] || durations[reverseKey];
                info.textContent = eta ? `🕒 Estimated Travel Time: ${eta}` : "";
                return eta;
            } else {
                info.textContent = "";
                return "";
            }
        }

        function updateSchedule() {
            const departureTime = document.getElementById("departure-time").value;
            const start = document.getElementById("start").value;
            const end = document.getElementById("end").value;

            if (start === end) {
                alert("Start location and End location cannot be the same.");
                return;
            }

            if (!departureTime || !start || !end) {
                alert("Please fill all required fields.");
                return;
            }

            const eta = updateDuration();

            // Combine today's date with the selected time
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const fullDepartureTime = `${yyyy}-${mm}-${dd} ${departureTime}:00`;

            $.post("update-schedule.php", {
                "departure_time": fullDepartureTime,
                start: start,
                end: end,
                eta: eta
            }, function(response) {
                alert(`✅ Schedule Set!\nStart: ${start}\nEnd: ${end}\nTime: ${departureTime}\nETA: ${eta}\n\nServer: ${response}`);
            });
        }

        function startSharing() {
            if (!intervalId) {
                updateLocation();
                intervalId = setInterval(updateLocation, 5000);
                document.querySelector(".start-btn").disabled = true;
                document.querySelector(".stop-btn").disabled = false;
                document.getElementById("status-text").innerText = "📡 Sharing";
                document.getElementById("status-text").style.color = "green";
            }
        }

       
        function stopSharing() {
            if (intervalId) {
                clearInterval(intervalId);
                intervalId = null;
                document.querySelector(".start-btn").disabled = false;
                document.querySelector(".stop-btn").disabled = true;
                document.getElementById("status-text").innerText = "❌ Not Sharing";
                document.getElementById("status-text").style.color = "red";
            }
        }

        function updateLocation() {
            navigator.geolocation.getCurrentPosition(function(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                const busID = <?= json_encode($busID) ?>;

                $.post("update-location.php", {
                    latitude: latitude,
                    longitude: longitude,
                    BusID: busID
                }, function(response) {
                    console.log("Location updated:", response);
                });
            }, function(error) {
                console.error("Location error:", error.message);
                alert("⚠️ Error accessing location. Please allow location access.");
                stopSharing();
            }, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            });
        }
    </script>
</body>
</html>
