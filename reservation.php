<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
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
    $phone = $_POST['phone'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $pickup = $_POST['pickup'];
    $destination = $_POST['destination'];

    if (empty($phone) || empty($date) || empty($time) || empty($pickup) || empty($destination)) {
        $_SESSION['message'] = "All fields are required.";
        header("Location: reservation.php");
        exit();
    }

    if ($pickup === $destination) {
        $_SESSION['message'] = "Pick-up and destination cannot be the same.";
        header("Location: reservation.php");
        exit();
    }

    $routeQuery = $conn->prepare("SELECT route_id FROM tblBusRoutes WHERE start_location = ? AND end_location = ?");
    $routeQuery->bind_param("ss", $pickup, $destination);
    $routeQuery->execute();
    $routeQuery->bind_result($routeId);
    $routeQuery->fetch();
    $routeQuery->close();

    if (empty($routeId)) {
        $_SESSION['message'] = "No route found for the selected locations.";
        header("Location: reservation.php");
        exit();
    }

    $busQuery = $conn->prepare("SELECT bus_id FROM tblBuses WHERE route_id = ? LIMIT 1");
    $busQuery->bind_param("i", $routeId);
    $busQuery->execute();
    $busQuery->bind_result($busId);
    $busQuery->fetch();
    $busQuery->close();

    if (empty($busId)) {
        $_SESSION['message'] = "No available bus for the selected route.";
        header("Location: reservation.php");
        exit();
    }

    $hour = date('H', strtotime($time));
    $bookingTime = "$date $hour:00:00";

    $capacityStmt = $conn->prepare("SELECT available_seats, booked_seats FROM tblBusCapacity WHERE bus_id = ?");
    $capacityStmt->bind_param("i", $busId);
    $capacityStmt->execute();
    $capacityStmt->bind_result($availableSeats, $bookedSeats);
    $capacityStmt->fetch();
    $capacityStmt->close();
$capacityStmt = $conn->prepare("SELECT available_seats, booked_seats FROM tblBusCapacity WHERE bus_id = ?");
$capacityStmt->bind_param("i", $busId);
$capacityStmt->execute();
$capacityStmt->bind_result($availableSeats, $bookedSeats);
$capacityStmt->fetch();
$capacityStmt->close();

if ($availableSeats === $bookedSeats) {
    $resetStmt = $conn->prepare("UPDATE tblBusCapacity SET booked_seats = 0 WHERE bus_id = ?");
    $resetStmt->bind_param("i", $busId);
    $resetStmt->execute();
    $resetStmt->close();

    $bookedSeats = 0;
}


    if (empty($availableSeats) && $availableSeats !== 0) {
        $_SESSION['message'] = "Bus capacity info not found.";
        header("Location: reservation.php");
        exit();
    }
    $nextSeat = $bookedSeats + 1;

    if ($nextSeat > $availableSeats) {
        $_SESSION['message'] = "No available seats for the selected time.";
        header("Location: reservation.php");
        exit();
    }

    $reservedSeat = $nextSeat;
    $bookingStmt = $conn->prepare("INSERT INTO tblBookings (student_number, bus_id, booking_time, reserved_seat) VALUES (?, ?, ?, ?)");
    $bookingStmt->bind_param("sisi", $studentNumber, $busId, $bookingTime, $reservedSeat);

    if ($bookingStmt->execute()) {
        $newBookedSeats = $bookedSeats + 1;
        $updateCapacityStmt = $conn->prepare("UPDATE tblBusCapacity SET booked_seats = ? WHERE bus_id = ?");
        $updateCapacityStmt->bind_param("ii", $newBookedSeats, $busId);
        $updateCapacityStmt->execute();
        $updateCapacityStmt->close();

        $_SESSION['booking_id'] = $bookingStmt->insert_id;

        $timestamp = date('Y-m-d H:i:s');
        $status = "unread";
        $message = "Dear $studentName, you have successfully reserved Bus $busId for $date at $hour:00 from $pickup to $destination.";

        $notifStmt = $conn->prepare("INSERT INTO tblNotifications (student_number, message, timestamp, status) VALUES (?, ?, ?, ?)");
        $notifStmt->bind_param("ssss", $studentNumber, $message, $timestamp, $status);
        $notifStmt->execute();
        $notifStmt->close();

        $receiverEmail = $_SESSION['email'];
        $receiverName = $studentName;
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->setLanguage(CONTACTFORM_LANGUAGE);
            $mail->SMTPDebug = CONTACTFORM_PHPMAILER_DEBUG_LEVEL;
            $mail->isSMTP();
            $mail->Host = CONTACTFORM_SMTP_HOSTNAME;
            $mail->SMTPAuth = true;
            $mail->Username = CONTACTFORM_SMTP_USERNAME;
            $mail->Password = CONTACTFORM_SMTP_PASSWORD;
            $mail->SMTPSecure = CONTACTFORM_SMTP_ENCRYPTION;
            $mail->Port = CONTACTFORM_SMTP_PORT;
            $mail->CharSet = CONTACTFORM_MAIL_CHARSET;
            $mail->Encoding = CONTACTFORM_MAIL_ENCODING;

            $mail->setFrom(CONTACTFORM_FROM_ADDRESS, CONTACTFORM_FROM_NAME);
            $mail->addAddress($receiverEmail, $receiverName);
            $mail->addReplyTo(CONTACTFORM_FROM_ADDRESS, CONTACTFORM_FROM_NAME);

            $mail->isHTML(true);
            $mail->Subject = "UJ STABUS Reservation Confirmation";
            $mail->Body = "
                <div style='background-color: #f0f8ff; padding: 20px; font-family: Arial, sans-serif; text-align: center;'>
                    <h2>Hello $receiverName,</h2>
                    <p>You have successfully reserved a bus seat.</p>
                    <ul style='text-align: left;'>
                        <li><strong>Bus ID:</strong> $busId</li>
                        <li><strong>Date:</strong> $date</li>
                        <li><strong>Time:</strong> $hour:00</li>
                        <li><strong>Pick-up:</strong> $pickup</li>
                        <li><strong>Destination:</strong> $destination</li>
                        <li><strong>Seat:</strong> $reservedSeat</li>
                    </ul>
                    <p>Thank you for using UJ STABUS!</p>
                </div>";

            $mail->send();
        } catch (Exception $e) {
            error_log("Email error: " . $mail->ErrorInfo);
        }

        $_SESSION['message'] = "Reservation successful!
        <div style='background-color: #f0f8ff; padding: 20px; font-family: Arial, sans-serif; text-align: center;'>
                    <h2>Hello $receiverName,</h2>
                    <p>You have successfully reserved a bus seat.</p>
                    <ul style='text-align: left;'>
                        <li><strong>Bus ID:</strong> $busId</li>
                        <li><strong>Date:</strong> $date</li>
                        <li><strong>Time:</strong> $hour:00</li>
                        <li><strong>Pick-up:</strong> $pickup</li>
                        <li><strong>Destination:</strong> $destination</li>
                        <li><strong>Seat:</strong> $reservedSeat</li>
                    </ul>
                    <p>Thank you for using UJ STABUS!
        ";
        $_SESSION['seat']=$reservedSeat;
        $_SESSION['bus_id']=$busId;

    } else {
        $_SESSION['message'] = "Failed to make reservation. Try Again Later";
    }

    $bookingStmt->close();
    header("Location: reserve-seat.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Reservation</title>
    <link rel="stylesheet" href="reservation.css">
    <link rel="stylesheet" href="back.css">
        <link rel="icon" type="image/jpeg" href="images/Stabus.jpeg">
</head>
<body>
<div class="splash">
    <div class="back-button">
        <a href="dashboard.php">&larr; Back to Dashboard</a>
    </div>
    <h1>Welcome to UJ STABUS Reservation</h1>
</div>

<div class="container">
    <h2>Book Your Ride</h2>
    <p>Reserve your seat for a comfortable ride</p>
    
    <?php if (isset($_SESSION['message'])): ?>
        <div style="background-color: #dff0d8; color: #3c763d; padding: 10px; margin-bottom: 15px; border: 1px solid #d6e9c6;">
            <?php
                echo $_SESSION['message'];
                unset($_SESSION['message']);
            ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
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

        <button type="submit">Confirm Reservation</button>
    </form>
</div>
<script src='reservation.js'></script>
</body>
</html>
