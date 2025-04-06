<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: signin.php");
    exit();
}

require_once "partial/connect.php";

$tables = [
    'tblQueuePositions', 'tblNotificationsSettings', 'tblNotifications', 'tblDrivers',
    'tblBusSchedules', 'tblBusRoutes', 'tblBuses', 'tblBusCapacity', 'tblBookings',
    'Students', 'location_history', 'live', 'Emergency', 'Admins'
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Databases</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="view_databases.css">
</head>
<body>
    <div class="view-db-container">
        <header>
            <h1>Database Viewer</h1>
            <nav>
                <a href="admin.php">← Back to Dashboard</a>
            </nav>
        </header>

        <main>
            <form id="table-form" method="get">
                <label for="table">Select Table:</label>
                <select name="table" id="table" required>
                    <option value="">--Choose Table--</option>
                    <?php foreach ($tables as $table): ?>
                        <option value="<?= $table ?>" <?= (isset($_GET['table']) && $_GET['table'] === $table) ? 'selected' : '' ?>><?= $table ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">View</button>
            </form>

            <?php if (isset($_GET['table']) && in_array($_GET['table'], $tables)): ?>
                <section id="table-result">
                    <h2><?= htmlspecialchars($_GET['table']) ?> Contents</h2>
                    <div class="table-wrapper">
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
                </section>
            <?php endif; ?>
        </main>
    </div>

    <script src="view_databases.js"></script>
</body>
</html>
