<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'partial/connect.php';  // This must create $conn (mysqli connection)

if (!isset($_SESSION['bus_id'])) {
    header('Location: signin.php');
    exit();
}

$bus_id = intval($_SESSION['bus_id']);

// Get Driver Name
$driverName = '';
$stmt = $conn->prepare("SELECT CONCAT(FirstName, ' ', LastName) AS DriverName FROM tblDrivers WHERE BusID = ?");
$stmt->bind_param("i", $bus_id);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    $driverName = $row['DriverName'];
} else {
    $driverName = 'Unknown Driver';
}

// 1. Booking count per month
$bookingPerMonthQuery = "
    SELECT DATE_FORMAT(booking_time, '%Y-%m') AS month, COUNT(*) AS total_bookings
    FROM tblBookings
    WHERE bus_id = ?
    GROUP BY month
    ORDER BY month
";
$stmt = $conn->prepare($bookingPerMonthQuery);
$stmt->bind_param("i", $bus_id);
$stmt->execute();
$result = $stmt->get_result();
$bookingLabels = [];
$bookingCounts = [];
while ($row = $result->fetch_assoc()) {
    $bookingLabels[] = $row['month'];
    $bookingCounts[] = (int)$row['total_bookings'];
}

// 2. Upcoming schedules count by day (next 10 days)
$scheduleQuery = "
    SELECT DATE(departure_time) as day, COUNT(*) AS count
    FROM tblBusSchedules
    WHERE bus_id = ? AND departure_time >= CURDATE()
    GROUP BY day
    ORDER BY day
    LIMIT 10
";
$stmt = $conn->prepare($scheduleQuery);
$stmt->bind_param("i", $bus_id);
$stmt->execute();
$result = $stmt->get_result();
$scheduleLabels = [];
$scheduleCounts = [];
while ($row = $result->fetch_assoc()) {
    $scheduleLabels[] = $row['day'];
    $scheduleCounts[] = (int)$row['count'];
}

// 3. Bus capacity usage
$capacityQuery = "SELECT available_seats, booked_seats FROM tblBusCapacity WHERE bus_id = ?";
$stmt = $conn->prepare($capacityQuery);
$stmt->bind_param("i", $bus_id);
$stmt->execute();
$result = $stmt->get_result();
$availableSeats = 0;
$bookedSeats = 0;
if ($row = $result->fetch_assoc()) {
    $availableSeats = (int)$row['available_seats'];
    $bookedSeats = (int)$row['booked_seats'];
}
$freeSeats = max($availableSeats - $bookedSeats, 0);

// 4. Maintenance status counts
$maintQuery = "
    SELECT 
      SUM(CASE WHEN EstimatedReturn > NOW() THEN 1 ELSE 0 END) AS active_count,
      SUM(CASE WHEN EstimatedReturn <= NOW() THEN 1 ELSE 0 END) AS completed_count
    FROM Maintain
    WHERE BusID = ?
";
$stmt = $conn->prepare($maintQuery);
$stmt->bind_param("i", $bus_id);
$stmt->execute();
$result = $stmt->get_result();
$activeMaint = 0;
$completedMaint = 0;
if ($row = $result->fetch_assoc()) {
    $activeMaint = (int)$row['active_count'];
    $completedMaint = (int)$row['completed_count'];
}

// 5. Live location: last known lat/lng
$liveQuery = "
    SELECT Latitude, Longitude, UpdateTime 
    FROM live 
    WHERE BusID = ? 
    ORDER BY UpdateTime DESC LIMIT 1
";
$stmt = $conn->prepare($liveQuery);
$stmt->bind_param("i", $bus_id);
$stmt->execute();
$result = $stmt->get_result();
$lastLat = null;
$lastLng = null;
$lastUpdateTime = null;
if ($row = $result->fetch_assoc()) {
    $lastLat = $row['Latitude'];
    $lastLng = $row['Longitude'];
    $lastUpdateTime = $row['UpdateTime'];
}

// Close connection if you want (optional)
// $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Driver Dashboard Charts</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #fafafa;
        margin: 0; padding: 30px;
        color: #333;
    }
    .container {
        max-width: 900px;
        margin: auto;
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0 20px #ccc;
    }
    h1 {
        text-align: center;
        color: #d2691e;
    }
    .chart-box {
        margin-top: 40px;
    }
    canvas {
        max-width: 100%;
    }
    #location {
        margin-top: 30px;
        padding: 15px;
        background: #ffe9b3;
        border-radius: 8px;
        font-weight: bold;
    }
</style>
</head>
<body>

<div class="container">
    <h1>Driver Dashboard for <?= htmlspecialchars($driverName) ?></h1>

    <div class="chart-box">
        <h3>Monthly Bookings</h3>
        <canvas id="bookingChart"></canvas>
    </div>

    <div class="chart-box">
        <h3>Upcoming Bus Schedules (Next 10 Days)</h3>
        <canvas id="scheduleChart"></canvas>
    </div>

    <div class="chart-box">
        <h3>Bus Seat Usage</h3>
        <canvas id="capacityChart"></canvas>
    </div>

    <div class="chart-box">
        <h3>Maintenance Status</h3>
        <canvas id="maintenanceChart"></canvas>
    </div>

    <div id="location">
        <h3>Last Known Location</h3>
        <?php if ($lastLat !== null && $lastLng !== null): ?>
            Latitude: <?= htmlspecialchars($lastLat) ?>, Longitude: <?= htmlspecialchars($lastLng) ?><br>
            Last Updated: <?= htmlspecialchars($lastUpdateTime) ?>
        <?php else: ?>
            Location data unavailable.
        <?php endif; ?>
    </div>
</div>

<script>
const bookingLabels = <?= json_encode($bookingLabels) ?>;
const bookingCounts = <?= json_encode($bookingCounts) ?>;

const scheduleLabels = <?= json_encode($scheduleLabels) ?>;
const scheduleCounts = <?= json_encode($scheduleCounts) ?>;

const busCapacityData = {
    labels: ['Booked Seats', 'Free Seats'],
    datasets: [{
        data: [<?= $bookedSeats ?>, <?= $freeSeats ?>],
        backgroundColor: ['#ff9900', '#ffcc66'],
        hoverOffset: 30
    }]
};

const maintenanceData = {
    labels: ['Active Maintenance', 'Completed Maintenance'],
    datasets: [{
        label: 'Maintenance Count',
        data: [<?= $activeMaint ?>, <?= $completedMaint ?>],
        backgroundColor: ['#ff6600', '#999999']
    }]
};

function createBarChart(ctx, labels, data, label, color) {
    return new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: label,
                data: data,
                backgroundColor: color
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true, stepSize: 1 }
            },
            plugins: { legend: { display: false } }
        }
    });
}

function createPieChart(ctx, data) {
    return new Chart(ctx, {
        type: 'doughnut',
        data: data,
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
}

window.onload = () => {
    const bookingCtx = document.getElementById('bookingChart').getContext('2d');
    createBarChart(bookingCtx, bookingLabels, bookingCounts, 'Bookings', '#ff9900');

    const scheduleCtx = document.getElementById('scheduleChart').getContext('2d');
    createBarChart(scheduleCtx, scheduleLabels, scheduleCounts, 'Schedules', '#cc6600');

    const capacityCtx = document.getElementById('capacityChart').getContext('2d');
    createPieChart(capacityCtx, busCapacityData);

    const maintenanceCtx = document.getElementById('maintenanceChart').getContext('2d');
    createPieChart(maintenanceCtx, maintenanceData);
};
</script>

</body>
</html>
