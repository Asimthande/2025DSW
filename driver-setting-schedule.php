<?php
session_start();

require 'vendor/autoload.php';
include 'partial/connect.php';
require_once __DIR__.'/config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['bus_id'])) {
    exit("Bus ID not set in session.");
}

$busID = $_SESSION['bus_id'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $time_input = $_POST['departure_time'] ?? null;

    if ($time_input) {
        // Get route_id from tblBuses for this bus
        $stmtRoute = $conn->prepare("SELECT route_id FROM tblBuses WHERE bus_id = ?");
        $stmtRoute->bind_param("i", $busID);
        $stmtRoute->execute();
        $resultRoute = $stmtRoute->get_result();
        if ($resultRoute->num_rows === 0) {
            $message = "Route not found for this bus.";
        } else {
            $rowRoute = $resultRoute->fetch_assoc();
            $route_id = $rowRoute['route_id'];

            $current_date = date('Y-m-d');

            // Append ':00' if seconds missing (e.g., "14:30" => "14:30:00")
            if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $time_input)) {
                $time_input .= ':00';
            } elseif (strlen($time_input) === 5) {
                $time_input .= ':00';
            }

            $departure_datetime = $current_date . ' ' . $time_input;
            $eta_datetime = date('Y-m-d H:i:s', strtotime($departure_datetime . ' +20 minutes'));

            // Insert the new schedule
            $stmt = $conn->prepare("INSERT INTO tblBusSchedules (bus_id, route_id, departure_time, eta) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $busID, $route_id, $departure_datetime, $eta_datetime);

            if ($stmt->execute()) {
                $message = "Schedule submitted for <strong>$departure_datetime</strong> (Route $route_id), ETA set to <strong>$eta_datetime</strong>.";

                // Extract date and hour from departure datetime for booking match
                $departure_date = date('Y-m-d', strtotime($departure_datetime));
                $departure_hour = date('H', strtotime($departure_datetime));

                // Select students who booked the bus for that exact date and hour
                $stmt2 = $conn->prepare("
                    SELECT s.Email, s.FirstName, s.LastName
                    FROM tblBookings b
                    JOIN Students s ON s.StudentNumber = b.student_number
                    WHERE b.bus_id = ?
                      AND DATE(b.booking_time) = ?
                      AND HOUR(b.booking_time) = ?
                ");
                $stmt2->bind_param("isi", $busID, $departure_date, $departure_hour);
                $stmt2->execute();
                $result = $stmt2->get_result();

                $notificationCount = 0;

                while ($row = $result->fetch_assoc()) {
                    $email = $row['Email'];
                    $name = trim($row['FirstName'] . ' ' . $row['LastName']);

                    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                    try {
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
                        $mail->addAddress($email, $name);
                        $mail->Subject = "Bus Departure Notification";
                        $mail->isHTML(true);
                        $mail->Body = '
                            <div style="background-color: #f5f5dc; padding: 20px; font-family: Arial, sans-serif;">
                                <p>Dear ' . htmlspecialchars($name) . ',</p>
                                <p>The bus <strong>(ID: ' . htmlspecialchars($busID) . ')</strong> for <strong>Route ' . htmlspecialchars($route_id) . '</strong> will depart at <strong>' . htmlspecialchars($departure_datetime) . '</strong>.</p>
                                <p>Please get ready and be on time.</p>
                                <p>Estimated Time of Arrival: <strong>' . htmlspecialchars($eta_datetime) . '</strong></p>
                                <p>Regards,<br>Stabus Team</p>
                            </div>
                        ';
                        $mail->send();
                        $notificationCount++;
                    } catch (Exception $e) {
                        error_log("Failed to send email to $email: " . $mail->ErrorInfo);
                    }
                }

                $message .= "<br><strong>$notificationCount</strong> students have been notified.";
            } else {
                $message = "Error: " . $stmt->error;
            }
        }
    } else {
        $message = "Please select a departure time.";
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
        input[type="time"] {
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

            <button type="submit">Submit Schedule</button>
        </form>
    </div>
</body>
</html>
