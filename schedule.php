<?php
include 'partial/connect.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle sorting
$sort = $_GET['sort'] ?? 'date';
$selectedDate = $_GET['date'] ?? date('Y-m-d');

$orderClause = "ORDER BY departure_time";
if ($sort === 'hour') {
    $orderClause = "ORDER BY HOUR(departure_time)";
}

$query = "
    SELECT 
        s.bus_id,
        s.departure_time,
        s.eta,
        r.start_location,
        r.end_location
    FROM tblBusSchedules s
    JOIN tblBusRoutes r ON s.route_id = r.route_id
    WHERE DATE(s.departure_time) = ?
    $orderClause
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $selectedDate);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bus Schedules</title>
    <link rel="stylesheet" href="schedule.css">
</head>
<body>
    <div class="container">
        <h1>Bus Schedules</h1>

        <form method="GET" style="margin-bottom: 20px;">
            <label for="date">Choose a date:</label>
            <input type="date" name="date" id="date" value="<?= htmlspecialchars($selectedDate) ?>" required>
            <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
            <button type="submit">Filter</button>
        </form>

        <div class="sort-options">
            <a href="schedule.php?sort=date&date=<?= $selectedDate ?>">Sort by Date</a> |
            <a href="schedule.php?sort=hour&date=<?= $selectedDate ?>">Sort by Hour</a>
        </div>

        <div class="view-toggle">
            <button id="table-view">Table View</button>
            <button id="list-view" class="active">List View</button>
        </div>

        <!-- Table View -->
        <div id="table-view-container" class="view-container" style="display: none;">
            <table>
                <thead>
                    <tr>
                        <th>Bus ID</th>
                        <th>Pickup</th>
                        <th>Drop Off</th>
                        <th>Departure</th>
                        <th>Arrival</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['bus_id']) ?></td>
                        <td><?= htmlspecialchars($row['start_location']) ?></td>
                        <td><?= htmlspecialchars($row['end_location']) ?></td>
                        <td><?= htmlspecialchars(date("H:i", strtotime($row['departure_time']))) ?></td>
                        <td><?= htmlspecialchars(date("H:i", strtotime($row['eta']))) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- List View -->
        <div id="list-view-container" class="view-container" style="display: block;">
            <ul>
                <?php
                // Reset result pointer
                $result->data_seek(0);
                while ($row = $result->fetch_assoc()):
                ?>
                <li>
                    <strong>Bus ID:</strong> <?= htmlspecialchars($row['bus_id']) ?><br>
                    <strong>Pickup:</strong> <?= htmlspecialchars($row['start_location']) ?><br>
                    <strong>Drop Off:</strong> <?= htmlspecialchars($row['end_location']) ?><br>
                    <strong>Departure:</strong> <?= htmlspecialchars(date("H:i", strtotime($row['departure_time']))) ?><br>
                    <strong>Arrival:</strong> <?= htmlspecialchars(date("H:i", strtotime($row['eta']))) ?>
                </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>

    <script>
        // Toggle view
        document.getElementById("table-view").addEventListener("click", function() {
            document.getElementById("table-view-container").style.display = "block";
            document.getElementById("list-view-container").style.display = "none";
        });
        document.getElementById("list-view").addEventListener("click", function() {
            document.getElementById("table-view-container").style.display = "none";
            document.getElementById("list-view-container").style.display = "block";
        });
    </script>
</body>
</html>
