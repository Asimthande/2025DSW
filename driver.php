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
</head>
<body>
    <h1>Driver Dashboard</h1>
    <p><strong>Bus ID:</strong> <?= htmlspecialchars($_SESSION['bus_id']) ?></p>

    <button id="startBtn">Start Sharing</button>
    <button id="stopBtn">Stop Sharing</button>
    <button id="logoutBtn">Logout</button>
    <p id="status">Click "Start Sharing" to send your location.</p>

    <script>
    const startBtn = document.getElementById("startBtn");
    const stopBtn = document.getElementById("stopBtn");
    const logoutBtn = document.getElementById("logoutBtn");
    const statusEl = document.getElementById("status");

    let intervalId = null;

    startBtn.onclick = function () {
        if (!navigator.geolocation) {
            statusEl.textContent = "Geolocation is not supported by your browser.";
            return;
        }

        intervalId = setInterval(() => {
            navigator.geolocation.getCurrentPosition(
                position => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    // Format: YYYY-MM-DD HH:MM:SS
                    const now = new Date();
                    const year = now.getFullYear();
                    const month = String(now.getMonth() + 1).padStart(2, '0');
                    const day = String(now.getDate()).padStart(2, '0');
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    const seconds = String(now.getSeconds()).padStart(2, '0');
                    const datetime = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;

                    const body = `latitude=${lat}&longitude=${lng}&datetime=${encodeURIComponent(datetime)}`;

                    fetch('driver-location.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: body
                    })
                    .then(response => {
                        if (response.ok) {
                            statusEl.textContent = `Location shared at ${datetime}`;
                        } else {
                            statusEl.textContent = "Failed to share location.";
                        }
                    })
                    .catch(err => {
                        statusEl.textContent = "Error sending location: " + err.message;
                    });
                },
                error => {
                    statusEl.textContent = "Geolocation error: " + error.message;
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
        }
    };

    logoutBtn.onclick = function () {
        window.location.href = 'logout.php';
    };
    </script>
</body>
</html>
