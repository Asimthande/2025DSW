<?php
session_start();

if (!isset($_SESSION["role"]) || !in_array($_SESSION["role"], ['admin', 'driver', 'student'])) {
    header("Location: signin.php");
    exit();
}

include 'partial/connect.php';

$busID1 = isset($_GET['busID1']) ? intval($_GET['busID1']) : 1;
$busID2 = isset($_GET['busID2']) ? intval($_GET['busID2']) : null;
$range = isset($_GET['range']) ? $_GET['range'] : 'daily';
$chartType = isset($_GET['chart']) ? $_GET['chart'] : 'bar';

$busIDs = [$busID1];
if ($busID2 && $busID2 !== $busID1) {
    $busIDs[] = $busID2;
}

switch ($range) {
    case 'weekly':
        $dateFilter = "UpdateTime >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        break;
    case 'monthly':
        $dateFilter = "UpdateTime >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        break;
    default:
        $dateFilter = "DATE(UpdateTime) = CURDATE()";
}

$data = [];

foreach ($busIDs as $busID) {
    $sql = "SELECT HOUR(UpdateTime) as hour, COUNT(*) as count 
            FROM location_history 
            WHERE BusID = ? AND $dateFilter 
            GROUP BY hour";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $busID);
    $stmt->execute();
    $result = $stmt->get_result();
    $counts = array_fill(0, 24, 0);
    while ($row = $result->fetch_assoc()) {
        $counts[intval($row['hour'])] = intval($row['count']);
    }
    $data["Bus $busID"] = $counts;
    $stmt->close();
}

$totalHourlyCounts = array_fill(0, 24, 0);
foreach ($data as $busData) {
    foreach ($busData as $hour => $count) {
        $totalHourlyCounts[$hour] += $count;
    }
}
$busiestHour = array_keys($totalHourlyCounts, max($totalHourlyCounts))[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bus Tracking Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            padding: 20px;
            margin: 0;
            background: hsl(30, 30%, 95%);
        }

        h1, h2 {
            text-align: center;
            color: hsl(10, 90%, 50%);
        }

        form {
            margin: 20px auto;
            text-align: center;
            background: hsl(275, 80%, 50%);
            padding: 15px;
            border-radius: 10px;
            color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        select, input, button {
            padding: 12px;
            margin: 5px;
            font-size: 16px;
            border-radius: 8px;
            border: none;
            outline: none;
        }

        select {
            background: white;
            color: hsl(275, 80%, 50%);
            font-weight: bold;
        }

        button {
            background: hsl(10, 90%, 50%);
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        #chart-container {
            width: 95%;
            max-width: 1000px;
            margin: auto;
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        canvas {
            width: 100% !important;
            height: auto !important;
        }

        .info {
            text-align: center;
            margin-top: 10px;
            font-size: 18px;
            color: hsl(275, 80%, 50%);
            font-weight: bold;
        }
    </style>
</head>
<body>

<h1>UJ Stabus: Bus Tracking Report</h1>

<form method="GET" action="chart.php">
    <label>Bus ID 1:
        <input type="number" name="busID1" value="<?= $busID1 ?>" required>
    </label>
    <label>Bus ID 2:
        <input type="number" name="busID2" value="<?= $busID2 ?? '' ?>">
    </label>
    <label>Range:
        <select name="range">
            <option value="daily" <?= $range == 'daily' ? 'selected' : '' ?>>Daily</option>
            <option value="weekly" <?= $range == 'weekly' ? 'selected' : '' ?>>Weekly</option>
            <option value="monthly" <?= $range == 'monthly' ? 'selected' : '' ?>>Monthly</option>
        </select>
    </label>
    <label>Chart Type:
        <select name="chart">
            <option value="bar" <?= $chartType == 'bar' ? 'selected' : '' ?>>Bar</option>
            <option value="line" <?= $chartType == 'line' ? 'selected' : '' ?>>Line</option>
            <option value="pie" <?= $chartType == 'pie' ? 'selected' : '' ?>>Pie</option>
            <option value="horizontal-bar" <?= $chartType == 'horizontal-bar' ? 'selected' : '' ?>>Horizontal Bar</option>
        </select>
    </label>
    <button type="submit">Update Chart</button>
</form>

<div id="chart-container">
    <canvas id="busChart"></canvas>
</div>

<div class="info">
    Busiest hour of the day: <strong><?= $busiestHour ?>:00</strong>
</div>

<script>
    const chartType = "<?= $chartType ?>";
    const chartData = <?= json_encode($data) ?>;
    const labels = Array.from({length: 24}, (_, i) => `${i}:00`);

    const datasets = Object.keys(chartData).map((bus, index) => {
        const colors = ['hsl(10, 90%, 50%)', 'hsl(290, 70%, 55%)', 'hsl(25, 80%, 60%)', 'hsl(200, 80%, 50%)'];
        const borderColors = ['hsl(10, 80%, 40%)', 'hsl(290, 60%, 45%)', 'hsl(25, 70%, 50%)', 'hsl(200, 70%, 40%)'];

        const background = colors[index % colors.length];
        const border = borderColors[index % borderColors.length];

        const dataPoints = labels.map(hour => chartData[bus][parseInt(hour)] ?? 0);

        return {
            label: bus,
            data: dataPoints,
            backgroundColor: chartType === 'pie' ? colors : background,
            borderColor: border,
            borderWidth: 2,
            type: chartType === 'horizontal-bar' ? 'bar' : chartType,
            fill: chartType !== 'line',
        };
    });

    const ctx = document.getElementById('busChart').getContext('2d');
    new Chart(ctx, {
        type: chartType === 'horizontal-bar' ? 'bar' : chartType,
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            responsive: true,
            indexAxis: chartType === 'horizontal-bar' ? 'y' : 'x',
            plugins: {
                title: {
                    display: true,
                    text: 'Bus Requests per Hour'
                },
                legend: {
                    display: true,
                    position: 'bottom'
                }
            },
            scales: chartType !== 'pie' ? {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Requests'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Hour of Day'
                    }
                }
            } : {}
        }
    });
</script>

</body>
</html>
