<?php
session_start();
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/functions.php';
require_once __DIR__.'/config.php';
require_once __DIR__.'/partial/connect.php'; 

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $studentNumber = trim($_POST['student-number']);

    $sql = "SELECT Password FROM Students WHERE StudentNumber = ? AND Email = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("is", $studentNumber, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($password);
            $stmt->fetch();
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
                $mail->addAddress($email);
                $mail->addReplyTo(CONTACTFORM_FROM_ADDRESS, CONTACTFORM_FROM_NAME);

                $mail->Subject = "Your Password Recovery";
                $mail->isHTML(true);
                $mail->Body = '
                <div style="background-color: #f5f5dc; padding: 20px; font-family: Arial, sans-serif; text-align: center;">
                    <img src="https://tryout.infy.uk/images/Stabus.jpeg" alt="Password Recovery" style="width: 100%; max-width: 600px; border-radius: 10px;">
                    <p style="font-size: 18px; color: #333;">Your password is: 
                        <span style="color: orange; font-weight: bold; font-size: 22px;">' . htmlspecialchars($password) . '</span>
                    </p>
                    <p style="font-size: 16px; color: #333;">Please keep it safe and do not share it with others.</p>
                    <p style="font-size: 16px; color: #333;">Regards,<br>JavaScript Junkies</p>
                </div>';

                $mail->send();

                $successMessage = "Your password has been sent to your email.";
            } catch (Exception $e) {
                $errorMessage = "Failed to send email: " . $mail->ErrorInfo;
            }
        } else {
            $errorMessage = "No matching student found with that email and student number.";
        }

        $stmt->close();
    } else {
        $errorMessage = "Error preparing query: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="verify.css">
        <link rel="icon" type="image/jpeg" href="images/Stabus.jpeg">
</head>
<body>

<div class="container">
    <h2>Forgot Password</h2>
    <p>Please enter your student number and email. If a match is found, your password will be sent to your email address.</p>

    <form method="POST" class="verification-form">
        <input type="text" name="student-number" placeholder="Enter Student Number" required>
        <input type="email" name="email" placeholder="Enter Email" required>
        <button type="submit" class="verify-btn">Send Password</button>
    </form>

    <?php if (isset($errorMessage)): ?>
        <p class="error-message"><?= htmlspecialchars($errorMessage) ?></p>
    <?php endif; ?>

    <?php if (isset($successMessage)): ?>
        <p class="success-message"><?= htmlspecialchars($successMessage) ?></p>
    <?php endif; ?>
</div>

</body>
</html>

