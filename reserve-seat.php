<?php
session_start();
if (!isset($_SESSION['seat']) || !isset($_SESSION['bus_id'])) {
    header("Location: reservation.php");
    exit();
}

include "partial/connect.php";

$busId = $_SESSION['bus_id'];
$userSeat = $_SESSION['seat'];

$query = $conn->prepare("SELECT seats FROM tblBuses WHERE bus_id = ?");
$query->bind_param("i", $busId);
$query->execute();
$query->bind_result($totalSeats);
$query->fetch();
$query->close();

$seatsPerRow = 5;
$leftSeats = 2;
$rightSeats = 3;
$rows = ceil($totalSeats / $seatsPerRow);
$seatNumber = 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reserved Seat Layout</title>
        <link rel="icon" type="image/jpeg" href="images/Stabus.jpeg">
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
        }
        .bus-container {
            max-width: 600px;
            margin: 0 auto;
        }
        .bus-info {
            margin-bottom: 30px;
        }
        .seat-layout {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .left-side, .right-side {
            display: flex;
            gap: 10px;
        }
        .aisle {
            width: 40px;
        }
        .seat {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            cursor: default;
        }
        .available {
            background-color: #008800;
        }
        .reserved {
            background-color: #cc0000;
        }
        .legend {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .legend-box {
            width: 20px;
            height: 20px;
            border-radius: 3px;
        }
        .green-box {
            background-color: #008800;
        }
        .red-box {
            background-color: #cc0000;
        }
    </style>
</head>
<body>
    <div class="bus-container">
        <div class="bus-info">
            <h2>BUS-<?php echo $busId; ?></h2>
            <p><strong>Your Reserved Seat:</strong> <?php echo $userSeat; ?></p>
        </div>

        <?php
        for ($row = 1; $row <= $rows; $row++) {
            echo '<div class="seat-layout">';

            echo '<div class="left-side">';
            for ($left = 1; $left <= $leftSeats; $left++) {
                if ($seatNumber > $totalSeats) break;
                $seatClass = ($seatNumber == $userSeat) ? 'reserved' : 'available';
                $seatLabel = "L" . $seatNumber;
                echo "<div class='seat $seatClass'>$seatLabel</div>";
                $seatNumber++;
            }
            echo '</div>';

            echo '<div class="aisle"></div>';

            
            echo '<div class="right-side">';
            for ($right = 1; $right <= $rightSeats; $right++) {
                if ($seatNumber > $totalSeats) break;
                $seatClass = ($seatNumber == $userSeat) ? 'reserved' : 'available';
                $seatLabel = "R" . $seatNumber;
                echo "<div class='seat $seatClass'>$seatLabel</div>";
                $seatNumber++;
            }
            echo '</div>';

            echo '</div>';
        }
        ?>

        <div class="legend">
            <div class="legend-item">
                <div class="legend-box green-box"></div>
                <span>Available Seat</span>
            </div>
            <div class="legend-item">
                <div class="legend-box red-box"></div>
                <span>Your Reserved Seat</span>
            </div>
        </div>
    </div>
</body>
</html>
