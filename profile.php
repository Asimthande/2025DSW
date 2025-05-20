<?php
session_start();
include 'partial/connect.php';

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

// Bookings
$booking_sql = "SELECT * FROM tblBookings WHERE student_number = ?";
if ($start_date && $end_date) {
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

// Emergencies
$emergency_sql = "SELECT * FROM Emergency WHERE StudentNumber = ?";
if ($start_date && $end_date) {
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

// Notifications
$notification_sql = "SELECT * FROM tblNotifications WHERE student_number = ?";
$notification_stmt = $conn->prepare($notification_sql);
$notification_stmt->bind_param("s", $student_number);
$notification_stmt->execute();
$notifications = $notification_stmt->get_result();
$notification_count = $notifications->num_rows;

// Queue Positions
$queue_sql = "SELECT * FROM tblQueuePositions WHERE student_number = ?";
$queue_stmt = $conn->prepare($queue_sql);
$queue_stmt->bind_param("s", $student_number);
$queue_stmt->execute();
$queue_positions = $queue_stmt->get_result();
$queue_count = $queue_positions->num_rows;

// Admins and All Students (for admin users only)
$admins_stmt = $conn->prepare("SELECT * FROM Admins");
$admins_stmt->execute();
$admins = $admins_stmt->get_result();

$students_stmt = $conn->prepare("SELECT * FROM Students");
$students_stmt->execute();
$students = $students_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($student['FirstName'] . ' ' . $student['LastName']) ?>'s Profile</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #fdf6ec;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .back-button {
    text-align: left;
    margin-bottom: 20px;
}

.back-button a {
    display: inline-block;
    padding: 10px 20px;
    background-color: #ffa726;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    transition: background-color 0.3s;
}

.back-button a:hover {
    background-color: #fb8c00;
}
        header {
            background: #ffb347;
            padding: 20px;
            text-align: center;
            color: #fff;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 30px 20px;
        }

        .navbar {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .navbar button {
            padding: 10px 20px;
            margin: 0 10px;
            border-radius: 5px;
            background: #fffaf3;
            font-size: 14px;
            cursor: pointer;
            border: 1px solid #dba15a;
        }

        .section {
            display: none;
            margin-top: 30px;
            background: #fff7ea;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #f0d9b5;
            text-align: left;
        }

        th {
            background: #f6d8ae;
        }

        h2 {
            color: #d67200;
            margin-bottom: 10px;
        }

        canvas {
            margin-top: 30px;
        }

        .filter-form {
            margin-top: 15px;
        }

        .filter-form input {
            margin-right: 10px;
        }
    </style>
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
        <button onclick="toggleSection('queuePositionsSection')">Queue Positions</button>
        <?php if ($student['role'] == 'admin') { ?>
            <button onclick="toggleSection('allStudentsSection')">All Students</button>
            <button onclick="toggleSection('adminsSection')">Admins</button>
        <?php } ?>
        <button onclick="toggleSection('chartSection')">Stats & Charts</button>
    </div>

    <!-- Profile -->
    <div id="profileSection" class="section">
        <h2>Profile Overview</h2>
        <p><strong>Name:</strong> <?= htmlspecialchars($student['FirstName']) ?> <?= htmlspecialchars($student['LastName']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($student['Email']) ?></p>
        <p><strong>Status:</strong> <?= $student['state'] == 1 ? 'Active' : 'Inactive' ?></p>
    </div>

    <!-- Bookings -->
    <div id="bookingsSection" class="section">
        <h2>Booking History</h2>
        <form method="GET" class="filter-form">
            <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>">
            <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>">
            <button type="submit">Filter</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Bus ID</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $bookings->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['booking_id'] ?></td>
                        <td><?= $row['bus_id'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Emergencies -->
    <div id="emergencySection" class="section">
        <h2>Emergency Reports</h2>
        <form method="GET" class="filter-form">
            <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>">
            <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>">
            <button type="submit">Filter</button>
        </form>
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
                        <td><?= $e['ID'] ?></td>
                        <td><?= $e['Type'] ?></td>
                        <td><?= $e['BusID'] ?></td>
                        <td><?= $e['Latitude'] ?></td>
                        <td><?= $e['Longitude'] ?></td>
                        <td><?= $e['Situation'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Notifications -->
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
                        <td><?= $n['message'] ?></td>
                        <td><?= $n['timestamp'] ?></td>
                        <td><?= $n['status'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Queue Positions -->
    <div id="queuePositionsSection" class="section">
        <h2>Queue Positions</h2>
        <table>
            <thead>
                <tr>
                    <th>Queue ID</th>
                    <th>Booking ID</th>
                    <th>Queue Position</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while($q = $queue_positions->fetch_assoc()): ?>
                    <tr>
                        <td><?= $q['queue_id'] ?></td>
                        <td><?= $q['booking_id'] ?></td>
                        <td><?= $q['queue_position'] ?></td>
                        <td><?= $q['queue_status'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- All Students -->
    <div id="allStudentsSection" class="section">
        <h2>All Students</h2>
        <table>
            <thead><tr><th>ID</th><th>Student Number</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Status</th></tr></thead>
            <tbody>
                <?php while($s = $students->fetch_assoc()): ?>
                    <tr>
                        <td><?= $s['ID'] ?></td>
                        <td><?= $s['StudentNumber'] ?></td>
                        <td><?= $s['FirstName'] ?></td>
                        <td><?= $s['LastName'] ?></td>
                        <td><?= $s['Email'] ?></td>
                        <td><?= $s['state'] == 1 ? 'Active' : 'Inactive' ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Admins -->
    <div id="adminsSection" class="section">
        <h2>Admins</h2>
        <table>
            <thead><tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Role</th><th>Contract End</th><th>Status</th></tr></thead>
            <tbody>
                <?php while($a = $admins->fetch_assoc()): ?>
                    <tr>
                        <td><?= $a['ID'] ?></td>
                        <td><?= $a['FirstName'] ?></td>
                        <td><?= $a['LastName'] ?></td>
                        <td><?= $a['Email'] ?></td>
                        <td><?= $a['role_id'] ?></td>
                        <td><?= $a['EndOfContract'] ?></td>
                        <td><?= $a['state'] == 1 ? 'Active' : 'Inactive' ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Charts -->
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

<script>
    function toggleSection(sectionId) {
        const sections = document.querySelectorAll('.section');
        sections.forEach(sec => sec.style.display = 'none');
        if (sectionId) {
            document.getElementById(sectionId).style.display = 'block';
        }
    }

    let chartType = 'bar';
    const ctx = document.getElementById('bookingChart').getContext('2d');
    let chart;

    function renderChart(type) {
        if (chart) chart.destroy();

        chart = new Chart(ctx, {
            type: type,
            data: {
                labels: ['Total Bookings', 'Total Emergencies'],
                datasets: [{
                    label: 'User Activity',
                    data: [<?= $booking_count ?>, <?= $emergency_count ?>],
                    backgroundColor: ['#ffa726', '#ef5350'],
                    borderColor: ['#fb8c00', '#e53935'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: type !== 'pie'
                    }
                },
                scales: type === 'pie' ? {} : {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    function updateChartType() {
        chartType = document.getElementById('chartType').value;
        renderChart(chartType);
    }

    window.onload = function () {
        toggleSection('profileSection');
        renderChart('bar');
    };
</script>

</body>
</html>
