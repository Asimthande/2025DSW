<?php
session_start();

if (!isset($_SESSION["role"]) || !in_array($_SESSION["role"], ['admin', 'driver', 'student'])) {
    header("Location: signin.php");
    exit();
}

include 'partial/connect.php';

$chartType = isset($_GET['chartType']) ? $_GET['chartType'] : 'bus_requests';
$range = isset($_GET['range']) ? $_GET['range'] : 'daily';

// Date filter based on selected range
switch ($range) {
    case 'weekly':
        $dateFilter = "booking_time >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        break;
    case 'monthly':
        $dateFilter = "booking_time >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        break;
    default:
        $dateFilter = "DATE(booking_time) = CURDATE()";
}

$data = [];

if ($chartType === 'bus_requests') {
    $sql = "SELECT bus_id, COUNT(*) as count 
            FROM tblBookings 
            WHERE $dateFilter 
            GROUP BY bus_id 
            ORDER BY count DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $data[$row['bus_id']] = $row['count'];
    }
    $stmt->close();
} elseif ($chartType === 'booking_hours') {
    $sql = "SELECT HOUR(booking_time) as hour, COUNT(*) as count 
            FROM tblBookings 
            WHERE $dateFilter 
            GROUP BY hour 
            ORDER BY hour ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = array_fill(0, 24, 0);
    while ($row = $result->fetch_assoc()) {
        $data[intval($row['hour'])] = intval($row['count']);
    }
    $stmt->close();
}

$busiestHour = null;
if ($chartType === 'booking_hours') {
    $busiestHour = array_keys($data, max($data))[0];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>UJ Stabus Booking Analytics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="language.js"></script>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            padding: 20px;
            margin: 0;
            background: #f4f4f4;
        }

        h1 {
            text-align: center;
            color: #d9534f;
        }

        form {
            margin: 20px auto;
            text-align: center;
            background-color: orange;
            padding: 15px;
            border-radius: 10px;
            color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        select, button {
            padding: 12px;
            margin: 5px;
            font-size: 16px;
            border-radius: 8px;
            border: none;
            outline: none;
        }

        select {
            background: white;
            color: #5bc0de;
            font-weight: bold;
        }

        button {
            background: rgb(184,227,233);
            color: black;
            font-weight: bold;
            cursor: pointer;
            box-shadow:0 0 10px black;
        }

        #chart-container {
            width: 95%;
            max-width: 1000px;
            margin: auto;
            background: white;
            padding: 20px;
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
            color: #5bc0de;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h1>UJ Stabus: Booking Analytics</h1>

<form method="GET" action="chart.php">
    <label>Chart Type:
        <select name="chartType">
            <option value="bus_requests" <?= $chartType == 'bus_requests' ? 'selected' : '' ?>>Most Requested Buses</option>
            <option value="booking_hours" <?= $chartType == 'booking_hours' ? 'selected' : '' ?>>Busiest Booking Hours</option>
        </select>
    </label>

    <label>Range:
        <select name="range">
            <option value="daily" <?= $range == 'daily' ? 'selected' : '' ?>>Daily</option>
            <option value="weekly" <?= $range == 'weekly' ? 'selected' : '' ?>>Weekly</option>
            <option value="monthly" <?= $range == 'monthly' ? 'selected' : '' ?>>Monthly</option>
        </select>
    </label>

    <button type="submit">Update Chart</button>
</form>

<div id="chart-container">
    <canvas id="bookingChart"></canvas>
</div>

<?php if ($chartType === 'booking_hours'): ?>
    <div class="info">
        Busiest hour: <strong><?= $busiestHour ?>:00</strong>
    </div>
<?php endif; ?>

<script>
    const chartType = "<?= $chartType ?>";
    const chartData = <?= json_encode($data) ?>;

    let labels = [];
    let dataPoints = [];

    if (chartType === 'bus_requests') {
        labels = Object.keys(chartData).map(busId => `Bus ${busId}`);
        dataPoints = Object.values(chartData);
    } else if (chartType === 'booking_hours') {
        labels = Array.from({length: 24}, (_, i) => `${i}:00`);
        dataPoints = labels.map(hour => chartData[parseInt(hour)] ?? 0);
    }

    const ctx = document.getElementById('bookingChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: chartType === 'bus_requests' ? 'Number of Bookings' : 'Bookings per Hour',
                data: dataPoints,
                backgroundColor: 'hsl(10, 90%, 60%)',
                borderColor: 'hsl(10, 80%, 40%)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: chartType === 'bus_requests' ? 'Most Requested Buses' : 'Busiest Booking Hours'
                },
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Bookings'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: chartType === 'bus_requests' ? 'Bus ID' : 'Hour of Day'
                    }
                }
            }
        }
    });
</script>

</body>
</html>
