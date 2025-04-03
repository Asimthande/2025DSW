<?php
session_start();

$timeout_duration = 1200;

if (isset($_SESSION['LAST_ACTIVITY'])) {
    $elapsed_time = time() - $_SESSION['LAST_ACTIVITY'];
    if ($elapsed_time > $timeout_duration) {
        session_unset();
        session_destroy();
        header("Location: signin.php");
        exit();
    }
}
$_SESSION['LAST_ACTIVITY'] = time();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: signin.php");
    exit();
}

$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-container">
        <header>
            <h1>Admin Dashboard</h1>
            <nav>
                <ul>
                    <li><a href="#overview">Overview</a></li>
                    <li><a href="#manage-users">Manage Users</a></li>
                    <li><a href="#manage-drivers">Manage Drivers</a></li>
                    <li><a href="#bus-maintenance">Bus Maintenance</a></li>
                    <li><a href="#view-db">View Databases</a></li>
                </ul>
            </nav>
        </header>

        <section id="greeting">
            <h2>Welcome, <?php echo htmlspecialchars($first_name) . " " . htmlspecialchars($last_name); ?></h2>
            <p>Email: <?php echo htmlspecialchars($email); ?></p>
            <a href="logout.php">Log Out</a>
        </section>

        <main>
            <section id="overview">
                <h2>Overview</h2>
                <p>Get a snapshot of the current system status, including active drivers, buses under maintenance, and rider statistics.</p>
            </section>

            <section id="manage-users">
                <h2>Manage Users</h2>
                <p>CRUD operations for managing users.</p>
                <button onclick="window.location.href='manage_users.php'">Edit Users</button>
            </section>

            <section id="manage-drivers">
                <h2>Manage Drivers</h2>
                <p>CRUD operations for managing drivers.</p>
                <button onclick="window.location.href='manage_drivers.php'">Edit Drivers</button>
            </section>

            <section id="bus-maintenance">
                <h2>Bus Maintenance</h2>
                <form action="update_bus_status.php" method="post">
                    <label for="bus-id">Select Bus</label>
                    <input type="text" id="bus-id" name="bus-id" placeholder="Enter bus ID" required>
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
                <form action="view_database.php" method="post">
                    <label for="db-select">Select Database</label>
                    <select id="db-select" name="db-select" required>
                        <option value="users">Users</option>
                        <option value="drivers">Drivers</option>
                        <option value="buses">Buses</option>
                    </select>
                    <button type="submit">View Data</button>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
