<?php
session_start();
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/functions.php';
require_once __DIR__.'/config.php';
require_once __DIR__.'/partial/connect.php'; 

if (!isset($_SESSION['email'], $_SESSION['first_name'], $_SESSION['student_number'])) {
    die("Session not initialized. Please log in.");
}

$receiverEmail = $_SESSION['email'];
$receiverName = $_SESSION['first_name'] . ' ' . ($_SESSION['last_name'] ?? '');

if (!isset($_SESSION['verification_code'])) {
    $verificationCode = rand(100000, 999999);
    $_SESSION['verification_code'] = strval($verificationCode);

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

        $mail->Subject = "Your Verification Code";
        $mail->isHTML(true);  
        $mail->Body = '
<div style="background-color: #f5f5dc; padding: 20px; font-family: Arial, sans-serif; text-align: center;">
    <img src="https://tryout.infy.uk/images/Stabus.jpeg" alt="Welcome" style="width: 100%; max-width: 600px; border-radius: 10px;">

    <p style="font-size: 18px; color: #333; margin-top: 20px;">Dear ' . $receiverName . ',</p>

    <p style="font-size: 18px; color: #333;">Your verification code is: 
        <span style="color: orange; font-weight: bold; font-size: 22px;">' . $verificationCode . '</span>
    </p>

    <p style="font-size: 16px; color: #333;">Please enter this code to verify your account.</p>

    <p style="font-size: 16px; color: #333;">Regards,<br>JavaScript Junkies</p>
</div>';


        $mail->send();
    } catch (Exception $e) {
        echo "Email sending failed: " . $mail->ErrorInfo;
        exit;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userCode = $_POST['verification_code'];
    if ($userCode === $_SESSION['verification_code']) {
        $studentNumber = $_SESSION['student_number'];
        $sql = "UPDATE `Students` SET `state` = 1 WHERE `StudentNumber` = ?";
        var_dump($_SESSION);
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $studentNumber);

            if ($stmt->execute()) {
                $_SESSION['role']='student';
                header("Location: dashboard.php");
                exit;
            } else {
                echo "Error executing query: " . $stmt->error;
            }
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
        <link rel="icon" type="image/jpeg" href="images/Stabus.jpeg">
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
