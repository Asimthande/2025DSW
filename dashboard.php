<?php
session_start();

$timeout_duration = 10;

if (isset($_SESSION['LAST_ACTIVITY'])) {
    $elapsed_time = time() - $_SESSION['LAST_ACTIVITY'];
    if ($elapsed_time > $timeout_duration * 60) {
        session_unset();
        session_destroy();
        header("Location: signin.php");
        exit();
    }
}

$_SESSION['LAST_ACTIVITY'] = time();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 'guest';
    $_SESSION['role'] = 'guest';
    $_SESSION['first_name'] = 'Guest';
    $_SESSION['last_name'] = 'Guest';
    $_SESSION['email'] = 'guest@example.com';
}

if ($_SESSION['role'] !== 'student' && $_SESSION['role'] !== 'admin') {
    $_SESSION['role'] = 'guest';
    $_SESSION['first_name'] = 'Guest';
    $_SESSION['last_name'] = 'Guest';
    $_SESSION['email'] = 'guest@example.com';
}

$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$email = $_SESSION['email'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script src="language.js"></script>
    <style>
#right-panel{
    display: flex;
    flex-direction: column;
    text-align: center;
}
         .loader {
    border: 8px solid lightgray;
    border-top: 8px solid blue;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    animation: spin 1s linear infinite;
  }
  
  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }
  
    100% {
      transform: rotate(360deg);
    }
  }
    #current-time{
        position:absolute;
        left:30%;
    }
    </style>
</head>
<body >

            <p id="current-time" style="font-size: 3pc; font-weight: bold; color: #333;"></p>
    <nav class="sidebar" id="sidebar">
        <h2>Dashboard</h2>
              <ul>
            <li><button onclick="window.location.href='dashboard.php'"><i class="fas fa-home"></i> Home</button></li>
            <li><button onclick="window.location.href='profile.php'" <?= $role == 'guest' ? 'style="opacity: 0.5;display:none; pointer-events: none;"' : '' ?>><i class="fas fa-user"></i> Profile</button></li>
            <li><button onclick="window.location.href='bus-tracking.php'" <?= $role == 'guest' ? 'style="opacity: 0.5; pointer-events: none;display:none;"' : '' ?>><i class="fas fa-bus"></i> Live Tracking</button></li>
            <li><button onclick="window.location.href='schedule.php'"><i class="fas fa-calendar-alt"></i> Schedule</button></li>
            <li><button onclick="window.location.href='chart.php'" <?= $role == 'guest' ? 'style="opacity: 0.5;display:none; pointer-events: none;"' : '' ?>><i class="fas fa-chart-bar"></i> Stats & Analysis</button></li>
            <li><button onclick="window.location.href='reservation.php'" <?= $role == 'guest' ? 'style="opacity: 0.5;display:none; pointer-events: none;"' : '' ?>><i class="fas fa-clipboard-check"></i> My Reservations</button></li>
            <li><button onclick="window.location.href='notifications.php'" <?= $role == 'guest' ? 'style="opacity: 0.5;display:none; pointer-events: none;"' : '' ?>><i class="fas fa-bell"></i> Notifications</button></li>
            <li><button onclick="window.location.href='settings.php'" <?= $role == 'guest' ? 'style="opacity: 0.5; pointer-events: none;display:none;"' : '' ?>><i class="fas fa-cogs"></i> Settings</button></li>
            <li><button onclick="window.location.href='weather.php'"><i class="fas fa-cloud-sun"></i> Weather</button></li>
            <li><button onclick="window.location.href='emergency-alert.php'"><i class="fas fa-exclamation-circle"></i> Emergency Alert</button></li>
            <li><button onclick="window.location.href='support.php'"><i class="fas fa-headset"></i> Help & Support</button></li>
            <li><button onclick="window.location.href='termsandconditions.php'"><i class="fas fa-file-contract"></i> Terms & Conditions</button></li>
            <li><button onclick="window.location.href='maintainance.php'"><i class="fas fa-tools"></i> Maintenance</button></li>
            <li><button onclick="window.location.href='logout.php'"><i class="fas fa-sign-out-alt"></i> Logout</button></li>
        </ul>
    </nav>
    
    <button class="hamburger" id="menu-btn">
        <i class="fas fa-bars"></i>
    </button>
    <button class="bell-icon" id="bell-btn">
        <i class="fas fa-bell"></i>
    </button>

    <div class="main-content">
        <section id="home">
            <h1>Welcome, <?php echo htmlspecialchars($first_name) . " " . htmlspecialchars($last_name); ?>!</h1>
            <div id="map-display">
                <div id="map"></div>
            </div>
        </section>

        <section id="tracking">
            <h2>Live Bus Tracking</h2>
            <div id="tracking-map"></div>
        </section>
<h1>Schedule</h1><?php
include 'partial/connect.php'; // database credentials

$today = date("Y-m-d");
$sql = "SELECT 
            tblBusSchedules.schedule_id,
            tblBusSchedules.bus_id,
            tblBusSchedules.departure_time,
            tblBusSchedules.eta,
            tblBusRoutes.route_id,
            tblBusRoutes.start_location,
            tblBusRoutes.end_location
        FROM tblBusSchedules 
        JOIN tblBusRoutes 
        ON tblBusRoutes.route_id = tblBusSchedules.route_id
        WHERE DATE(departure_time) = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();

echo '<div class="schedule-table-container">';
if ($result->num_rows > 0) {
    echo '
    <table class="schedule-table">
        <thead>
            <tr>
                <th>Schedule ID</th>
                <th>Bus ID</th>
                <th>Route ID</th>
                <th>From</th>
                <th>To</th>
                <th>Departure Time</th>
                <th>ETA</th>
            </tr>
        </thead>
        <tbody>';
    while ($row = $result->fetch_assoc()) {
        echo '
            <tr>
                <td>' . htmlspecialchars($row['schedule_id']) . '</td>
                <td>' . htmlspecialchars($row['bus_id']) . '</td>
                <td>' . htmlspecialchars($row['route_id']) . '</td>
                <td>' . htmlspecialchars($row['start_location']) . '</td>
                <td>' . htmlspecialchars($row['end_location']) . '</td>
                <td>' . date("H:i", strtotime($row['departure_time'])) . '</td>
                <td>' . date("H:i", strtotime($row['eta'])) . '</td>
            </tr>';
    }
    echo '
        </tbody>
    </table>';
} else {
    echo '<p class="no-schedule">No schedules found for today.</p>';
}
echo '</div>';

$stmt->close();
$conn->close();
?>
    </div>

<aside class="right-panel" id="right-panel" style="height: 100vh; overflow-y: auto;">
        <h3>Notifications</h3>
        <div style="display: flex;flex-direction: column; text-align: center;">
        <ul id="alerts">
        <div class="loader" id="loader"></div>
        </ul>
        </div>


        <h3>Weather</h3>
        <div id="weather-container">
            <p id="weather">Loading weather...</p>
        </div>
        <br><br><br>
    </aside>
<script>
setInterval(function() {
    fetch('dashboard-content.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('alerts').innerHTML = data;
        });
}, 20000);
    setTimeout(() => {
      const loader = document.getElementById("loader");
      loader.style.display = "none";
    }, 20000);
    setTimeout()
function showCurrentTime() {
  const timeElement = document.getElementById('current-time');
  const currentTime = new Date();
  const hours = String(currentTime.getHours()).padStart(2, '0');
  const minutes = String(currentTime.getMinutes()).padStart(2, '0');
  const seconds = String(currentTime.getSeconds()).padStart(2, '0');
  
  timeElement.innerHTML = ` ${hours}:${minutes}:${seconds}`;
}

setInterval(showCurrentTime, 1000);
</script>
    <script src="dashboard.js"></script>
</body>
</html>
