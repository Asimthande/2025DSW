<?php
$schedules = [
    ['bus_id' => '101', 'route' => 'Route A', 'departure_time' => '08:00 AM', 'arrival_time' => '09:00 AM'],
    ['bus_id' => '102', 'route' => 'Route B', 'departure_time' => '09:30 AM', 'arrival_time' => '10:30 AM'],
    ['bus_id' => '103', 'route' => 'Route C', 'departure_time' => '11:00 AM', 'arrival_time' => '12:00 PM'],
    ['bus_id' => '104', 'route' => 'Route D', 'departure_time' => '01:00 PM', 'arrival_time' => '02:00 PM'],
    ['bus_id' => '105', 'route' => 'Route E', 'departure_time' => '03:00 PM', 'arrival_time' => '04:00 PM']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Schedules</title>
    <link rel="stylesheet" href="schedule.css">
</head>
<body>

    <div class="container">
        <h1>Bus Schedules</h1>
        
        <div class="view-toggle">
            <button id="table-view" class="active">Table View</button>
            <button id="list-view">List View</button>
        </div>

        <div id="table-view-container" class="view-container">
            <table id="schedule-table">
                <thead>
                    <tr>
                        <th>Bus ID</th>
                        <th>Route</th>
                        <th>Departure Time</th>
                        <th>Arrival Time</th>
                    </tr>
                </thead>
                <tbody id="schedule-table-body">
                    <?php foreach ($schedules as $schedule): ?>
                        <tr>
                            <td><?php echo $schedule['bus_id']; ?></td>
                            <td><?php echo $schedule['route']; ?></td>
                            <td><?php echo $schedule['departure_time']; ?></td>
                            <td><?php echo $schedule['arrival_time']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- List View -->
        <div id="list-view-container" class="view-container" style="display: none;">
            <ul id="schedule-list">
                <?php foreach ($schedules as $schedule): ?>
                    <li>
                        <strong>Bus ID:</strong> <?php echo $schedule['bus_id']; ?><br>
                        <strong>Route:</strong> <?php echo $schedule['route']; ?><br>
                        <strong>Departure Time:</strong> <?php echo $schedule['departure_time']; ?><br>
                        <strong>Arrival Time:</strong> <?php echo $schedule['arrival_time']; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <script src="schedule.js"></script>
</body>
</html>
