<?php
session_start(); // Ensure the session is started

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/functions.php';
require_once __DIR__.'/config.php';

// Ensure user is logged in and session has email and name
if (!isset($_SESSION['email'], $_SESSION['first_name'])) {
    die("Session not initialized. Please log in.");
}

$receiverEmail = $_SESSION['email'];
$receiverName = $_SESSION['first_name'] . ' ' . ($_SESSION['last_name'] ?? '');

// Generate a 6-digit verification code
$verificationCode = rand(100000, 999999);
$_SESSION['verification_code'] = strval($verificationCode);

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

    $mail->send();

    // Debugging: Print session data before redirecting
    echo "<pre>";
    print_r($_SESSION); // Check if verification_code is set in session
    echo "</pre>";

    // Optional: Redirect to verify.php
    header("Location: verify.php");
    exit;

} catch (Exception $e) {
    echo "Email sending failed: " . $mail->ErrorInfo;
}
