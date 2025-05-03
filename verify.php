<?php
session_start();
require_once "partial/connect.php";

// Include PHPMailer files from the 'mail' folder
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'mail/src/PHPMailer.php';
require 'mail/src/Exception.php';
require 'mail/src/SMTP.php';

// Check if user is logged in
if (!isset($_SESSION['student_number'])) {
    die("Unauthorized access: No student number found in session.");
}

$student_number = $_SESSION['student_number'];

// Fetch student email
$email_stmt = $conn->prepare("SELECT email FROM Students WHERE studentNumber = ?");
$email_stmt->bind_param("s", $student_number);
$email_stmt->execute();
$email_stmt->bind_result($email);
$email_stmt->fetch();
$email_stmt->close();

if (empty($email)) {
    die("Error: No email found for the student.");
}

// Function to send the verification code using PHPMailer with Gmail SMTP
function sendVerificationCode($email, $code) {
    $subject = "UJ iTrack Verification Code";
    $message = "Hello,\n\nYour UJ iTrack verification code is: $code\n\nIf you did not request this, please ignore this message.";

    // Create PHPMailer instance
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();                                           // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';                             // Set the SMTP server to Gmail
        $mail->SMTPAuth = true;                                     // Enable SMTP authentication
        $mail->Username = 'strikersavings@gmail.com';               // SMTP username (your Gmail address)
        $mail->Password = 'cdrc aetb yehj woqj';                    // SMTP password (your Gmail password or App Password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption
        $mail->Port = 587;                                          // TCP port to connect to

        // Recipients
        $mail->setFrom('strikersavings@gmail.com', 'UJ iTrack');    // Set the "From" email address
        $mail->addAddress($email);                                  // Add recipient email

        // Content
        $mail->isHTML(false);                                       // Set email format to plain text
        $mail->Subject = $subject;
        $mail->Body    = $message;

        // Send the email
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Error handling
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

// Generate or resend code
if (!isset($_SESSION['verification_code']) || isset($_GET['resend'])) {
    $code = mt_rand(100000, 999999);
    $_SESSION['verification_code'] = $code;

    if (!sendVerificationCode($email, $code)) {
        echo "<script>alert('Failed to send verification code.');</script>";
    } elseif (isset($_GET['resend'])) {
        echo "<script>alert('Verification code resent.');</script>";
    }
}

// Handle verification
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['verification_code'])) {
    $user_code = trim($_POST['verification_code']);

    if (!preg_match('/^\d{6}$/', $user_code)) {
        echo "<script>alert('Invalid format. Please enter a 6-digit code.');</script>";
    } elseif ($user_code == $_SESSION['verification_code']) {
        $update_stmt = $conn->prepare("UPDATE Students SET state = 1 WHERE studentNumber = ?");
        $update_stmt->bind_param("s", $student_number);
        $update_stmt->execute();

        if ($update_stmt->affected_rows > 0) {
            unset($_SESSION['verification_code']);
            echo "<script>alert('Verification successful!'); window.location.href='dashboard.php';</script>";
            exit;
        } else {
            echo "<script>alert('Code matched, but failed to update account.');</script>";
        }

        $update_stmt->close();
    } else {
        echo "<script>alert('Invalid verification code. Please try again.');</script>";
    }
}
?>

<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Account</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: sans-serif;
            background-color: #f9f9f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .verification-screen {
            background: white;
            padding: 2rem 3rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        input[type="text"] {
            padding: 10px;
            width: 100%;
            margin: 1rem 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #0066cc;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 0.5rem;
        }

        button:hover {
            background-color: #004999;
        }

        form {
            margin-bottom: 1rem;
        }

        p {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <section class="verification-screen">
        <h2>Verify Your Account</h2>
        <p>A verification code was sent to: <strong><?= htmlspecialchars($email); ?></strong></p>

        <form method="post">
            <input type="text" name="verification_code" placeholder="Enter 6-digit Verification Code" required>
            <button type="submit">Verify</button>
        </form>

        <form method="get">
            <button type="submit" name="resend" value="1">Resend Code</button>
        </form>
    </section>
</body>
</html>
