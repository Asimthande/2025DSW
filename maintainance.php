<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

require_once "partial/connect.php";
$sql = "SELECT MaintainID, BusID, Date, `Estimated Return`, `Maintainance Type`, AdminID FROM Maintain";
$result = mysqli_query($conn, $sql);

$buses = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $buses[] = $row;
    }
} else {
    die("Failed to fetch data: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Buses Under Maintenance</title>
    <link rel="stylesheet" href="maintainance.css">
        <link rel="icon" type="image/jpeg" href="images/Stabus.jpeg">
</head>
<body>

<div class="container">
    <h1>Buses Under Maintenance</h1>

    <div class="view-toggle">
        <button id="list-view-btn">List View</button>
        <button id="grid-view-btn">Grid View</button>
    </div>
    <div id="list-view" class="view">
        <table>
            <thead>
                <tr>
                    <th>Maintain ID</th>
                    <th>Bus ID</th>
                    <th>Date</th>
                    <th>Estimated Return</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($buses as $bus): ?>
                    <tr>
                        <td><?= htmlspecialchars($bus['MaintainID']) ?></td>
                        <td><?= htmlspecialchars($bus['BusID']) ?></td>
                        <td><?= htmlspecialchars($bus['Date']) ?></td>
                        <td><?= htmlspecialchars($bus['Estimated Return']) ?></td>
                        <td><?= htmlspecialchars($bus['Maintainance Type']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div id="grid-view" class="view" style="display: none;">
        <div class="grid-container">
            <?php foreach ($buses as $bus): ?>
                <div class="card">
                    <h3>Bus ID: <?= htmlspecialchars($bus['BusID']) ?></h3>
                    <p><strong>Maintenance ID:</strong> <?= htmlspecialchars($bus['MaintainID']) ?></p>
                    <p><strong>Date:</strong> <?= htmlspecialchars($bus['Date']) ?></p>
                    <p><strong>Return:</strong> <?= htmlspecialchars($bus['Estimated Return']) ?></p>
                    <p><strong>Type:</strong> <?= htmlspecialchars($bus['Maintainance Type']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script src="maintainance.js"></script>
</body>
</html>
