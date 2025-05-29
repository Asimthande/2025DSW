<?php
session_start();
include 'partial/connect.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['student_number'])) {
    die("Access denied. Please log in.");
}

$student_number = $_SESSION['student_number'];
$student_stmt = $conn->prepare("SELECT * FROM Students WHERE StudentNumber = ?");
$student_stmt->bind_param("s", $student_number);
$student_stmt->execute();
$student_result = $student_stmt->get_result();
$student = $student_result->fetch_assoc();

$start_date = $_GET['start_date'] ?? null;
$end_date = $_GET['end_date'] ?? null;
function is_valid_date($date) {
    return preg_match('/^\d{4}-\d{2}-\d{2}$/', $date);
}

$valid_dates = false;
if ($start_date && $end_date && is_valid_date($start_date) && is_valid_date($end_date)) {
    $valid_dates = true;
}
$booking_sql = "SELECT * FROM tblBookings WHERE student_number = ?";
if ($valid_dates) {
    $booking_sql .= " AND booking_time BETWEEN ? AND ?";
    $booking_stmt = $conn->prepare($booking_sql);
    $booking_stmt->bind_param("sss", $student_number, $start_date, $end_date);
} else {
    $booking_stmt = $conn->prepare($booking_sql);
    $booking_stmt->bind_param("s", $student_number);
}
$booking_stmt->execute();
$bookings = $booking_stmt->get_result();
$booking_count = $bookings->num_rows;
$emergency_sql = "SELECT * FROM Emergency WHERE StudentNumber = ?";
if ($valid_dates) {
    $emergency_sql .= " AND report_date BETWEEN ? AND ?";
    $emergency_stmt = $conn->prepare($emergency_sql);
    $emergency_stmt->bind_param("sss", $student_number, $start_date, $end_date);
} else {
    $emergency_stmt = $conn->prepare($emergency_sql);
    $emergency_stmt->bind_param("s", $student_number);
}
$emergency_stmt->execute();
$emergencies = $emergency_stmt->get_result();
$emergency_count = $emergencies->num_rows;
$notification_sql = "SELECT * FROM tblNotifications WHERE student_number = ?";
$notification_stmt = $conn->prepare($notification_sql);
$notification_stmt->bind_param("s", $student_number);
$notification_stmt->execute();
$notifications = $notification_stmt->get_result();
$notification_count = $notifications->num_rows;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($student['FirstName'] . ' ' . $student['LastName']) ?>'s Profile</title>
    <link rel="stylesheet" href="profile.css" />
    <link rel="icon" type="image/jpeg" href="images/Stabus.jpeg" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>

<header>
    <h1><?= htmlspecialchars($student['FirstName'] . ' ' . $student['LastName']) ?>'s Profile</h1>
</header>

<div class="container">
    <div class="back-button">
        <a href="dashboard.php">&larr; Back to Dashboard</a>
    </div>

    <div class="navbar">
        <button onclick="toggleSection('profileSection')">Profile Overview</button>
        <button onclick="toggleSection('bookingsSection')">Booking History</button>
        <button onclick="toggleSection('emergencySection')">Emergency Reports</button>
        <button onclick="toggleSection('notificationsSection')">Notifications</button>
        <button onclick="toggleSection('chartSection')">Stats & Charts</button>
    </div>
    <div id="profileSection" class="section">
        <h2>Profile Overview</h2>
        <p><strong>Name:</strong> <?= htmlspecialchars($student['FirstName']) ?> <?= htmlspecialchars($student['LastName']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($student['Email']) ?></p>
        <p><strong>Status:</strong> <?= $student['state'] == 1 ? 'Active' : 'Inactive' ?></p>
    </div>
    <div id="bookingsSection" class="section">
        <h2>Booking History</h2>
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Bus ID</th>
                    <th>Booking Time</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $bookings->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['booking_id']) ?></td>
                        <td><?= htmlspecialchars($row['bus_id']) ?></td>
                        <td><?= htmlspecialchars($row['booking_time']) ?></td>
                    </tr>
                <?php endwhile; ?>
                <?php if ($booking_count === 0): ?>
                    <tr><td colspan="3" style="text-align:center;">No bookings found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div id="emergencySection" class="section">
        <h2>Emergency Reports</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Bus ID</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Situation</th>
                </tr>
            </thead>
            <tbody>
                <?php while($e = $emergencies->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['ID']) ?></td>
                        <td><?= htmlspecialchars($e['Type']) ?></td>
                        <td><?= htmlspecialchars($e['BusID']) ?></td>
                        <td><?= htmlspecialchars($e['Latitude']) ?></td>
                        <td><?= htmlspecialchars($e['Longitude']) ?></td>
                        <td><?= htmlspecialchars($e['Situation']) ?></td>
                    </tr>
                <?php endwhile; ?>
                <?php if ($emergency_count === 0): ?>
                    <tr><td colspan="7" style="text-align:center;">No emergency reports found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div id="notificationsSection" class="section">
        <h2>Notifications</h2>
        <table>
            <thead>
                <tr>
                    <th>Message</th>
                    <th>Timestamp</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($n = $notifications->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($n['message']) ?></td>
                        <td><?= htmlspecialchars($n['timestamp']) ?></td>
                        <td><?= htmlspecialchars($n['status']) ?></td>
                    </tr>
                <?php endwhile; ?>
                <?php if ($notification_count === 0): ?>
                    <tr><td colspan="3" style="text-align:center;">No notifications found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div id="chartSection" class="section">
        <h2>Stats & Charts</h2>
        <label for="chartType">Select Chart Type:</label>
        <select id="chartType" onchange="updateChartType()">
            <option value="bar">Bar Chart</option>
            <option value="line">Line Chart</option>
            <option value="pie">Pie Chart</option>
        </select>
        <canvas id="bookingChart" height="120"></canvas>
    </div>

</div>

<script src="profile.js"></script>
<script>
    const bookingCount = <?= (int)$booking_count ?>;
    const emergencyCount = <?= (int)$emergency_count ?>;
</script>

</body>
</html>
