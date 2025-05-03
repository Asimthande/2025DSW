<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

if ($_SESSION['role'] !== 'student') {
    header("Location: dashboard.php");
    exit();
}

$studentName = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
$studentNumber = $_SESSION['student_number'];

include "partial/connect.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    header('Content-Type: application/json');

    $phone = $_POST['phone'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $pickup = $_POST['pickup'];
    $destination = $_POST['destination'];
    $busId = $_POST['bus_id'];

    if (empty($phone) || empty($date) || empty($time) || empty($pickup) || empty($destination) || empty($busId)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit();
    }

    if ($pickup === $destination) {
        echo json_encode(['status' => 'error', 'message' => 'Departure and destination cannot be the same.']);
        exit();
    }

    // Extract only the hour from the time input
    $hour = date('H', strtotime($time));  // Extract just the hour part (24-hour format)

    // Store day, hour, and bus_id in session
    $_SESSION['day'] = $date;
    $_SESSION['hour'] = $hour;
    $_SESSION['bus_id'] = $busId;

    // Insert into tblBookings
    $bookingTime = "$date $hour:00:00"; // Use the hour and date to store the booking time
    $reservedSeat = 0; // default seat reserved (can be updated later)

    $bookingStmt = $conn->prepare("INSERT INTO tblBookings (student_number, bus_id, booking_time, reserved_seat) VALUES (?, ?, ?, ?)");
    $bookingStmt->bind_param("sisi", $studentNumber, $busId, $bookingTime, $reservedSeat);

    if ($bookingStmt->execute()) {
        $bookingId = $bookingStmt->insert_id;
        $_SESSION['booking_id'] = $bookingId;

        // Also insert into tblNotifications
        $timestamp = date('Y-m-d H:i:s');
        $status = "unread";
        $message = "Dear $studentName, you have successfully reserved Bus $busId for $date at $hour:00 from $pickup to $destination.";

        $notifStmt = $conn->prepare("INSERT INTO tblNotifications (student_number, message, timestamp, status) VALUES (?, ?, ?, ?)");
        $notifStmt->bind_param("ssss", $studentNumber, $message, $timestamp, $status);
        $notifStmt->execute();
        $notifStmt->close();

        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to book the bus.']);
    }

    $bookingStmt->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservation</title>
    <link rel="stylesheet" href="reservation.css">
</head>
<body>
    <div class="splash">
        <h1>Welcome to UJ STABUS Reservation</h1>
    </div>
    <div class="container">
        <h2>Book Your Ride</h2>
        <p>Reserve your seat for a comfortable ride</p>

        <form id="reservationForm" method="POST" action="">
            <label for="name">Full Name</label>
            <input type="text" id="name" value="<?php echo htmlspecialchars($studentName); ?>" readonly>

            <label for="student_id">Student Number</label>
            <input type="text" id="student_id" value="<?php echo htmlspecialchars($studentNumber); ?>" readonly>

            <label for="phone">Phone Number</label>
            <input type="tel" name="phone" id="phone" placeholder="Enter your phone number" required>

            <label for="date">Date</label>
            <input type="date" name="date" id="reservation-date" required>

            <label for="time">Time</label>
            <input type="time" name="time" id="time" min="06:00" max="22:00" required>

            <label for="pickup">Pick-up Location</label>
            <select name="pickup" id="pickup" required>
                <option value="" disabled selected>Select Pick-up Location</option>
                <option value="APK">APK Campus</option>
                <option value="SWC">Soweto Campus</option>
                <option value="DFC">DFC Campus</option>
                <option value="APB">APB Campus</option>
            </select>

            <label for="destination">Destination</label>
            <select name="destination" id="destination" required>
                <option value="" disabled selected>Select Destination</option>
            </select>

            <label for="bus_id">Bus ID</label>
            <select name="bus_id" id="bus_id" required>
                <option value="" disabled selected>Select Bus ID</option>
                <?php for ($i = 1; $i <= 10; $i++) : ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>

            <button type="submit">Confirm Reservation</button>
        </form>

        <p id="confirmation"></p>
    </div>

    <script src="reservation.js"></script>
</body>
</html>
