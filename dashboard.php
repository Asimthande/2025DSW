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
</head>
<body>

    <nav class="sidebar" id="sidebar">
        <h2>Dashboard</h2>
              <ul>
            <li><button onclick="window.location.href='dashboard.php'"><i class="fas fa-home"></i> Home</button></li>
            <li><button onclick="window.location.href='profile.php'" <?= $role == 'guest' ? 'style="opacity: 0.5;display:none; pointer-events: none;"' : '' ?>><i class="fas fa-user"></i> Profile</button></li>
            <li><button onclick="window.location.href='bus-tracking.php'" <?= $role == 'guest' ? 'style="opacity: 0.5; pointer-events: none;display:none;"' : '' ?>><i class="fas fa-bus"></i> Live Tracking</button></li>
            <li><button onclick="window.location.href='schedule.php'"><i class="fas fa-calendar-alt"></i> Schedule</button></li>
            <li><button onclick="window.location.href='chart.php'" <?= $role == 'guest' ? 'style="opacity: 0.5;display:none; pointer-events: none;"' : '' ?>><i class="fas fa-chart-bar"></i> Stats & Analysis</button></li>
            <li><button onclick="window.location.href='reserve-seat.php'" <?= $role == 'guest' ? 'style="opacity: 0.5;display:none; pointer-events: none;"' : '' ?>><i class="fas fa-chair"></i> Reserve Seat</button></li>
            <li><button onclick="window.location.href='reservation.php'" <?= $role == 'guest' ? 'style="opacity: 0.5;display:none; pointer-events: none;"' : '' ?>><i class="fas fa-clipboard-check"></i> My Reservations</button></li>
            <li><button onclick="window.location.href='notifications.php'" <?= $role == 'guest' ? 'style="opacity: 0.5;display:none; pointer-events: none;"' : '' ?>><i class="fas fa-bell"></i> Notifications</button></li>
            <li><button onclick="window.location.href='verify.php'" <?= $role == 'guest' ? 'style="opacity: 0.5; pointer-events: none;display:none;"' : '' ?>><i class="fas fa-shield-alt"></i> Verify</button></li>
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
            <p>Next Bus: Route 5 - 10:30 AM (ETA: 8 mins)</p>
        </section>

        <section id="tracking">
            <h2>Live Bus Tracking</h2>
            <div id="tracking-map"></div>
        </section>

        <section id="schedule">
            <h2>Bus Schedule</h2>
            <div id="schedule-info">Loading Schedule...</div>
        </section>
    </div>

    <aside class="right-panel" id="right-panel">
        <h3>Notifications</h3>
        <ul id="alerts">
            <li>No new alerts</li>
        </ul>
        <h3>Weather</h3>
        <div id="weather-container">
            <p id="weather">Loading weather...</p>
        </div>
    </aside>

    <script src="dashboard.js"></script>
</body>
</html>
