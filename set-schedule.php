<?php
session_start();
include 'partial/connect.php';

$message = "";
if(!isset($_SESSION['bus_id'])){

}
$busID = $_SESSION['bus_id'] ;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $busID) {
    $route_id = $_POST['route_id'] ?? null;
    $time_input = $_POST['departure_time'] ?? null;

    if ($route_id && $time_input) {
        $current_date = date('Y-m-d');
        if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $time_input)) {
            $time_input .= ':00';
        }
        $departure_datetime = $current_date . ' ' . $time_input;
        
        $eta_datetime = date('Y-m-d H:i:s', strtotime($departure_datetime . ' +20 minutes'));

        $stmt = $conn->prepare("INSERT INTO tblBusSchedules (bus_id, route_id, departure_time, eta) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $busID, $route_id, $departure_datetime, $eta_datetime);

        if ($stmt->execute()) {
            $message = "Schedule submitted for <strong>$departure_datetime</strong> (Route $route_id), ETA set to <strong>$eta_datetime</strong>";
        } else {
            $message = "Error: " . $stmt->error;
        }
    } else {
        $message = "Please select both route and departure time.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Set Schedule</title>
        <link rel="icon" type="image/jpeg" href="images/Stabus.jpeg">
    <style>
        body {
            background-color: #f5f5dc;
            font-family: Arial, sans-serif;
            padding: 30px;
            color: #333;
        }
        .form-container {
            max-width: 400px;
            margin: auto;
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }
        h2 {
            color: #d2691e;
            text-align: center;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        input[type="time"],
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 2px solid #ffa500;
            border-radius: 5px;
            font-size: 1em;
            background-color: #fff9f0;
        }
        button {
            background-color: #ffa500;
            border: none;
            padding: 12px;
            width: 100%;
            color: white;
            font-size: 1em;
            margin-top: 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #e69500;
        }
        .message {
            margin-top: 20px;
            padding: 10px;
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            border-radius: 5px;
            font-size: 0.95em;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Set Your Schedule</h2>

        <?php if ($message): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>

        <form method="post" novalidate>
            <label for="departure_time">Select Departure Time</label>
            <input type="time" id="departure_time" name="departure_time" required>

            <label for="route_id">Select Route</label>
            <select name="route_id" id="route_id" required>
                <?php for ($i = 1; $i <= 12; $i++): ?>
                    <option value="<?= $i ?>">Route <?= $i ?></option>
                <?php endfor; ?>
            </select>

            <button type="submit">Submit Schedule</button>
        </form>
    </div>
</body>
</html>
