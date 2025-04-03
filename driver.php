<?php
// Start the session
session_start();

// Check if the user is logged in by checking if user_id is set in the session
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: signin.php");
    exit();
}

// Check if the user has the correct role (either 'driver' or 'admin')
if ($_SESSION['role'] !== 'driver' && $_SESSION['role'] !== 'admin') {
    // If not a driver or admin, redirect to another page (e.g., dashboard or home page)
    header("Location: dashboard.php");  // You can change this to an error or different page
    exit();
}

// Fetch user details from the session
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard</title>
    <link rel="stylesheet" href="driver.css">
</head>
<body>
    <div class="driver-container">
        <header>
            <h1>Driver Dashboard</h1>
        </header>

        <main>
            <section id="bus-reservation">
                <h2>Bus Reservation</h2>
                <form>
                    <label for="departure-time">Departure Time</label>
                    <input type="time" id="departure-time" placeholder="Select Departure Time">
                    
                    <label for="departure-location">Departure Location</label>
                    <select name="select-departure" id="select-departure" onchange="Destinations()">
                        <option value="Select">Select Departure Location</option>
                        <option value="APB">APB</option>
                        <option value="APK">APK</option>
                        <option value="DFC">DFC</option>
                        <option value="SWC">SWC</option>
                    </select>
                    
                    <label for="destination">Destination</label>
                    <select name="select-destination" id="select-destination">
                        <option value="Select">Select Destination Location</option>
                    </select>
                    
                    <label for="bus-number">Bus Number</label>
                    <input type="text" id="bus-number" placeholder="Enter Bus Number" readonly>
                    
                    <label for="eta">Estimated Time of Arrival (ETA)</label>
                    <input type="text" id="eta" placeholder="ETA will be calculated" disabled>
                    
                    <button type="button">Execute Ride</button>
                </form>
            </section>

        </main>
    </div>

    <script src="driver.js"></script>
</body>
</html>
