<?php
session_start();
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/functions.php';
require_once __DIR__.'/config.php';
require_once __DIR__.'/partial/connect.php'; // Include the database connection

// Ensure user is logged in and session has email, name, and student_number
if (!isset($_SESSION['email'], $_SESSION['first_name'], $_SESSION['student_number'])) {
    die("Session not initialized. Please log in.");
}

$receiverEmail = $_SESSION['email'];
$receiverName = $_SESSION['first_name'] . ' ' . ($_SESSION['last_name'] ?? '');

// Generate the verification code only once
if (!isset($_SESSION['verification_code'])) {
    $verificationCode = rand(100000, 999999);
    $_SESSION['verification_code'] = strval($verificationCode);

    // Send the verification code to the user
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    try {
        // SMTP settings
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

        // Recipients
        $mail->setFrom(CONTACTFORM_FROM_ADDRESS, CONTACTFORM_FROM_NAME);
        $mail->addAddress($receiverEmail, $receiverName);
        $mail->addReplyTo(CONTACTFORM_FROM_ADDRESS, CONTACTFORM_FROM_NAME);

        // Content
        $mail->Subject = "Your Verification Code";
        $mail->isHTML(true);  // This ensures the email content is treated as HTML
        $mail->Body = '
<div style="background-color: #f5f5dc; padding: 20px; font-family: Arial, sans-serif; text-align: center;">
    <img src="https://via.placeholder.com/600x200.png?text=Welcome" alt="Welcome" style="width: 100%; max-width: 600px; border-radius: 10px;">

    <p style="font-size: 18px; color: #333; margin-top: 20px;">Dear ' . $receiverName . ',</p>

    <p style="font-size: 18px; color: #333;">Your verification code is: 
        <span style="color: orange; font-weight: bold; font-size: 22px;">' . $verificationCode . '</span>
    </p>

    <p style="font-size: 16px; color: #333;">Please enter this code to verify your account.</p>

    <p style="font-size: 16px; color: #333;">Regards,<br>Your Team</p>
</div>';


        $mail->send();
    } catch (Exception $e) {
        echo "Email sending failed: " . $mail->ErrorInfo;
        exit;
    }
}

// Check if the verification code is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userCode = $_POST['verification_code'];

    // Verify if the submitted code matches the one sent
    if ($userCode === $_SESSION['verification_code']) {
        // Update user verification status in the database using student_number from the session
        $studentNumber = $_SESSION['student_number'];

        // Prepare and bind the update query using mysqli (from connect.php)
        $sql = "UPDATE `Students` SET `state` = 1 WHERE `StudentNumber` = ?";
        
        // Prepare statement
        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters (i for integer)
            $stmt->bind_param("i", $studentNumber);

            // Execute the statement
            if ($stmt->execute()) {
                // Redirect to dashboard if verification is successful
                header("Location: dashboard.php");
                exit;
            } else {
                echo "Error executing query: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            echo "Error preparing the statement: " . $conn->error;
        }
    } else {
        $errorMessage = "Invalid verification code. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email</title>
    <link rel="stylesheet" href="verify.css">
</head>
<body>

<div class="container">
    <h2>Verify Your Email</h2>
    <p>A verification code has been sent to your email address. Please enter the code below to verify your account.</p>

    <form method="POST" class="verification-form">
        <input type="text" name="verification_code" placeholder="Enter Verification Code" required>
        <button type="submit" class="verify-btn">Verify</button>
    </form>

    <?php if (isset($errorMessage)): ?>
        <p class="error-message"><?= $errorMessage ?></p>
    <?php endif; ?>
</div>

</body>
</html>
