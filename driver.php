<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Driver Live Tracking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="driver.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="container">
    <a href="logout.php" class="logout-btn">Logout</a>
    <h2>Driver Live Location</h2>

    <select id="bus-id">
        <option value="" disabled selected>Select Bus ID</option>
        <?php for ($i = 1; $i <= 10; $i++): ?>
            <option value="<?= $i ?>">Bus <?= $i ?></option>
        <?php endfor; ?>
    </select>

    <!-- Added Departure Time input field -->
    <label for="departure-time">Departure Time:</label>
    <input type="time" id="departure-time" name="departure-time" required>

    <div class="btn-container">
        <button class="start-btn" onclick="startSharing()">📡 Start Sharing</button>
        <button class="stop-btn" onclick="stopSharing()" disabled>🛑 Stop Sharing</button>
    </div>

    <p class="status">Status: <span id="status-text">❌ Not Sharing</span></p>
</div>

<script>
    let intervalId = null;
    let busID = null;
    let departureTime = null;

    function updateLocation() {
        navigator.geolocation.getCurrentPosition(function(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;

            busID = document.getElementById("bus-id").value;
            departureTime = document.getElementById("departure-time").value; // Get the departure time

            if (!busID || !departureTime) {
                alert("Please select a Bus ID and a Departure Time.");
                stopSharing();
                return;
            }

            $.post("driver.php", {
                latitude: latitude,
                longitude: longitude,
                BusID: busID,
                departureTime: departureTime  // Pass departure time
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

    function startSharing() {
        busID = document.getElementById("bus-id").value;
        departureTime = document.getElementById("departure-time").value; // Get the departure time

        if (!busID || !departureTime) {
            alert("Please select a Bus ID and a Departure Time.");
            return;
        }

        if (!intervalId) {
            updateLocation();
            intervalId = setInterval(updateLocation, 5000);
            document.querySelector(".start-btn").disabled = true;
            document.querySelector(".stop-btn").disabled = false;
            document.getElementById("status-text").innerText = "✅ Sharing";
        }
    }

    function stopSharing() {
        if (intervalId) {
            clearInterval(intervalId);
            intervalId = null;
            document.querySelector(".start-btn").disabled = false;
            document.querySelector(".stop-btn").disabled = true;
            document.getElementById("status-text").innerText = "❌ Not Sharing";
        }
    }
</script>

</body>
</html>
