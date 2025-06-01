<?php
include 'partial/connect.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$sort = $_GET['sort'] ?? 'date';
$selectedDate = $_GET['date'] ?? date('Y-m-d');

$orderClause = "ORDER BY departure_time";
if ($sort === 'hour') {
    $orderClause = "ORDER BY HOUR(departure_time)";
}

$query = "
    SELECT schedule_id, bus_id, departure_time, eta
    FROM tblBusSchedules
    WHERE DATE(departure_time) = ?
    $orderClause
";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $selectedDate);

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$result = $stmt->get_result();
if (!$result) {
    die("Getting result set failed: " . $stmt->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Bus Schedules</title>
    <link rel='stylesheet' href='schedule.css'>
        <link rel="icon" type="image/jpeg" href="images/Stabus.jpeg">
</head>
<body>

<div class="back-button" style="background-color: beige; padding: 10px; border-radius: 5px;">
    <a href="dashboard.php" style="color: orange; text-decoration: none; font-weight: bold;">&larr; Back to Dashboard</a>
</div>

<div class="container">

    <h1>Bus Schedules for <?= htmlspecialchars($selectedDate) ?></h1>

    <form method="GET">
        <label for="date">Choose a date:</label>
        <input type="date" id="date" name="date" value="<?= htmlspecialchars($selectedDate) ?>" required />
        <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>" />
        <button type="submit">Filter</button>
    </form>

    <div class="sort-options">
        Sort by:
        <a href="?sort=date&date=<?= urlencode($selectedDate) ?>">Date</a> |
        <a href="?sort=hour&date=<?= urlencode($selectedDate) ?>">Hour</a>
    </div>

    <div class="view-toggle">
        <button id="table-view">Table View</button>
        <button id="list-view" class="active">List View</button>
    </div>
    <div id="table-view-container" style="display:none;">
        <table>
            <thead>
                <tr>
                    <th>Schedule ID</th>
                    <th>Bus ID</th>
                    <th>Departure</th>
                    <th>ETA</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['schedule_id']) ?></td>
                    <td><?= htmlspecialchars($row['bus_id']) ?></td>
                    <td><?= htmlspecialchars(date("H:i", strtotime($row['departure_time']))) ?></td>
                    <td><?= htmlspecialchars(date("H:i", strtotime($row['eta']))) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <div id="list-view-container">
        <ul>
            <?php 
            $result->data_seek(0);
            while ($row = $result->fetch_assoc()): ?>
            <li>
                <strong>Schedule ID:</strong> <?= htmlspecialchars($row['schedule_id']) ?><br>
                <strong>Bus ID:</strong> <?= htmlspecialchars($row['bus_id']) ?><br>
                <strong>Departure:</strong> <?= htmlspecialchars(date("H:i", strtotime($row['departure_time']))) ?><br>
                <strong>ETA:</strong> <?= htmlspecialchars(date("H:i", strtotime($row['eta']))) ?>
            </li>
            <?php endwhile; ?>
        </ul>
    </div>
</div>

<script>
    const tableBtn = document.getElementById('table-view');
    const listBtn = document.getElementById('list-view');
    const tableContainer = document.getElementById('table-view-container');
    const listContainer = document.getElementById('list-view-container');

    tableBtn.addEventListener('click', () => {
        tableContainer.style.display = 'block';
        listContainer.style.display = 'none';
        tableBtn.classList.add('active');
        listBtn.classList.remove('active');
    });

    listBtn.addEventListener('click', () => {
        tableContainer.style.display = 'none';
        listContainer.style.display = 'block';
        listBtn.classList.add('active');
        tableBtn.classList.remove('active');
    });
</script>
</body>
</html>
