<?php
session_start();
if (!isset($_SESSION['bus_id'])) {
    header('Location: signin.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Driver Dashboard</title>
    <link rel="stylesheet" href="driver.css">
    <style>
    body{
        display:flex;
        flex-direction:column;
        text-align:center;
    }
    </style>
</head>
<body>
    <h1>Driver Dashboard</h1>
    <p><strong>Bus ID:</strong> <?= htmlspecialchars($_SESSION['bus_id']) ?></p>

    <button id="startBtn">Start Sharing</button>
    <button id="stopBtn">Stop Sharing</button>
    <button id="logoutBtn">Logout</button>
    <p id="status">Click "Start Sharing" to send your location.</p>
    <div id="schedule-container"></div>
<div id="statistics">
            <button onclick="window.location.href='driver-chart.php'"><i class="fas fa-home"></i> Statistics</button>
            <button onclick="window.location.href='driver-setting-schedule.php'"><i class="fas fa-home"></i> Schedule</button>
</div><script>
const startBtn = document.getElementById("startBtn");
const stopBtn = document.getElementById("stopBtn");
const logoutBtn = document.getElementById("logoutBtn");
const statusEl = document.getElementById("status");

let intervalId = null;

startBtn.onclick = function () {
    if (!navigator.geolocation) {
        statusEl.textContent = "Geolocation is not supported by your browser.";
        console.error("Geolocation not supported.");
        return;
    }

    intervalId = setInterval(() => {
        navigator.geolocation.getCurrentPosition(
            position => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                // Format: YYYY-MM-DD HH:MM:SS
                const now = new Date();
                const datetime = now.toISOString().slice(0, 19).replace("T", " ");
                const body = `latitude=${lat}&longitude=${lng}&datetime=${encodeURIComponent(datetime)}`;

                console.log(`Sending: ${body}`);

                fetch('driver-location.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: body
                })
                .then(response => response.text().then(text => {
                    console.log("Response Status:", response.status);
                    console.log("Response Text:", text);

                    if (response.ok) {
                        statusEl.textContent = `Location shared at ${datetime}`;
                    } else {
                        statusEl.textContent = `Failed to share location: ${response.status}`;
                    }
                }))
                .catch(err => {
                    statusEl.textContent = "Error sending location: " + err.message;
                    console.error("Fetch error:", err);
                });
            },
            error => {
                const errors = {
                    1: "Permission denied",
                    2: "Position unavailable",
                    3: "Timeout"
                };
                const errorMessage = errors[error.code] || error.message;
                statusEl.textContent = "Geolocation error: " + errorMessage;
                console.error("Geolocation error:", error);
            }
        );
    }, 4000); // Every 4 seconds

    statusEl.textContent = "Started sharing location...";
};

stopBtn.onclick = function () {
    if (intervalId !== null) {
        clearInterval(intervalId);
        intervalId = null;
        statusEl.textContent = "Stopped sharing location.";
        console.log("Location sharing stopped.");
    }
};

logoutBtn.onclick = function () {
    window.location.href = 'logout.php';
};
</script>

</body>
</html>
