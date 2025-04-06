<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: signin.php");
    exit();
}

require_once "partial/connect.php";

$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$email = $_SESSION['email'];
$tables = [
    'tblQueuePositions', 'tblNotificationsSettings', 'tblNotifications', 'tblDrivers',
    'tblBusSchedules', 'tblBusRoutes', 'tblBuses', 'tblBusCapacity', 'tblBookings',
    'Students', 'location_history', 'live', 'Emergency', 'Admins'
];

$students = mysqli_query($conn, "SELECT COUNT(*) AS total FROM Students");
$students_total = mysqli_fetch_assoc($students)['total'];

$drivers = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tblDrivers");
$drivers_total = mysqli_fetch_assoc($drivers)['total'];

$locations = mysqli_query($conn, "
    SELECT DATE(UpdateTime) as date, COUNT(*) as count 
    FROM location_history 
    GROUP BY DATE(UpdateTime)
    ORDER BY DATE(UpdateTime)
");
$location_dates = [];
$location_counts = [];
while ($row = mysqli_fetch_assoc($locations)) {
    $location_dates[] = $row['date'];
    $location_counts[] = $row['count'];
}

$notifications = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tblNotifications");
$notifications_total = mysqli_fetch_assoc($notifications)['total'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="admin-container">
        <header>
            <h1>Admin Dashboard</h1>
            <nav>
                <ul>
                    <li><a href="#overview">Overview</a></li>
                    <li><a href="manage_users.php">Manage Users</a></li>
                    <li><a href="manage_drivers.php">Manage Drivers</a></li>
                    <li><a href="bus_maintenance.php">Bus Maintenance</a></li>
                    <li><a href="view_databases.php">View Databases</a></li>
                </ul>
            </nav>
        </header>

        <section id="greeting">
            <h2>Welcome, <?= htmlspecialchars($first_name) . " " . htmlspecialchars($last_name); ?></h2>
            <p>Email: <?= htmlspecialchars($email); ?></p>
            <a href="logout.php">Log Out</a>
        </section>

        <main>
            <section id="overview">
                <h2>Overview</h2>
                <div class="overview-container">
                    <div class="card">
                        <h3>Total Students</h3>
                        <p><?= $students_total ?></p>
                    </div>
                    <div class="card">
                        <h3>Total Drivers</h3>
                        <p><?= $drivers_total ?></p>
                    </div>
                    <div class="card">
                        <h3>Total Notifications</h3>
                        <p><?= $notifications_total ?></p>
                    </div>
                </div>

                <div class="chart-container">
                    <p id="totalStudents" style="font-weight: bold;"></p>
                    <canvas id="studentsChart"></canvas>
                    <canvas id="driversChart"></canvas>
                    <canvas id="locationHistoryChart"></canvas>
                    <canvas id="notificationsChart"></canvas> <!-- ✅ Added this canvas -->
                </div>
            </section>

            <section id="manage-users">
                <h2>Manage Users</h2>
                <button onclick="window.location.href='manage_users.php'">Edit Users</button>
            </section>

            <section id="bus-maintenance">
                <h2>Bus Maintenance</h2>
                <form action="update_bus_status.php" method="post">
                    <label for="bus-id">Bus ID</label>
                    <input type="text" id="bus-id" name="bus-id" required>

                    <label for="status">Maintenance Status</label>
                    <select id="status" name="status" required>
                        <option value="under-maintenance">Under Maintenance</option>
                        <option value="active">Active</option>
                    </select>

                    <button type="submit">Update Status</button>
                </form>
            </section>

            <section id="view-db">
                <h2>View Databases</h2>
                <form method="get">
                    <label for="table">Select Table</label>
                    <select name="table" id="table" required>
                        <option value="">--Choose Table--</option>
                        <?php foreach ($tables as $table): ?>
                            <option value="<?= $table ?>" <?= (isset($_GET['table']) && $_GET['table'] === $table) ? 'selected' : '' ?>><?= $table ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">View Table</button>
                </form>

                <?php if (isset($_GET['table']) && in_array($_GET['table'], $tables)): ?>
                    <div class="table-wrapper">
                        <h3><?= htmlspecialchars($_GET['table']) ?> Data</h3>
                        <table>
                            <thead>
                                <tr>
                                    <?php
                                    $table = $_GET['table'];
                                    $result = mysqli_query($conn, "SELECT * FROM `$table` LIMIT 1");
                                    while ($fieldinfo = mysqli_fetch_field($result)) {
                                        echo "<th>{$fieldinfo->name}</th>";
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = mysqli_query($conn, "SELECT * FROM `$table`");
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    foreach ($row as $value) {
                                        echo "<td>" . htmlspecialchars($value) . "</td>";
                                    }
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <script src="admin.js"></script>
</body>
</html>
