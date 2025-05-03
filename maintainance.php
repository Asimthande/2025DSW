<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

if (!in_array($_SESSION['role'], ['admin', 'driver', 'student','guest'])) {
    header("Location: dashboard.php");
    exit();
}

$buses = [
    [
        'bus_id' => 101,
        'problem_description' => 'Engine malfunction',
        'estimated_return' => '2025-03-20 08:00:00',
        'bus_image_url' => 'https://via.placeholder.com/300?text=Bus+101'
    ],
    [
        'bus_id' => 102,
        'problem_description' => 'Brake system failure',
        'estimated_return' => '2025-03-22 10:30:00',
        'bus_image_url' => 'https://via.placeholder.com/300?text=Bus+102'
    ],
    [
        'bus_id' => 103,
        'problem_description' => 'Electrical issues',
        'estimated_return' => '2025-03-25 14:15:00',
        'bus_image_url' => 'https://via.placeholder.com/300?text=Bus+103'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buses Under Maintenance</title>
    <link rel="stylesheet" href="maintainance.css">
</head>
<body>

    <div class="container">
        <h1>Buses Under Maintenance</h1>

        <div class="view-toggle">
            <button id="table-view-btn">Table View</button>
            <button id="picture-view-btn">Picture View</button>
        </div>

        <!-- Table View -->
        <div id="table-view" class="view">
            <table id="maintenance-table">
                <thead>
                    <tr>
                        <th>Bus ID</th>
                        <th>Problem</th>
                        <th>Estimated Return</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($buses as $bus): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($bus['bus_id']); ?></td>
                            <td><?php echo htmlspecialchars($bus['problem_description']); ?></td>
                            <td><?php echo date('F d, Y H:i', strtotime($bus['estimated_return'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Picture View -->
        <div id="picture-view" class="view" style="display:none;">
            <div class="bus-cards">
                <?php foreach ($buses as $bus): ?>
                    <div class="bus-card">
                        <img src="<?php echo htmlspecialchars($bus['bus_image_url']); ?>" alt="Bus Image">
                        <h3>Bus ID: <?php echo htmlspecialchars($bus['bus_id']); ?></h3>
                        <p><?php echo htmlspecialchars($bus['problem_description']); ?></p>
                        <p><strong>Estimated Return:</strong> <?php echo date('F d, Y H:i', strtotime($bus['estimated_return'])); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- External JS -->
    <script src="maintenance.js"></script>

</body>
</html>
