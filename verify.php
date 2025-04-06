<?php
session_start();
require_once "partial/connect.php";

// Check if user is logged in
if (!isset($_SESSION['student_number'])) {
    die("Unauthorized access: No student number found in session.");
}

$student_number = $_SESSION['student_number'];

// Fetch email
$email_stmt = $conn->prepare("SELECT email FROM Students WHERE studentNumber = ?");
$email_stmt->bind_param("s", $student_number);
$email_stmt->execute();
$email_stmt->bind_result($email);
$email_stmt->fetch();
$email_stmt->close();

if (!$email) {
    die("Error: Email not found for this student.");
}

// Generate & send code
if (!isset($_SESSION['verification_code'])) {
    $code = mt_rand(100000, 999999);
    $_SESSION['verification_code'] = $code;

    $subject = "UJ iTrack Verification Code";
    $message = "Hello,\n\nYour UJ iTrack verification code is: $code\n\nIf you did not request this, please ignore this message.";
    $headers = "From: no-reply@tryout.infy.uk\r\n";
    $headers .= "Reply-To: no-reply@tryout.infy.uk\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    if (!mail($email, $subject, $message, $headers)) {
        echo "<script>alert('Failed to send verification code. Check mail settings.');</script>";
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verification_code'])) {
    $user_code = trim($_POST['verification_code']);

    if ($user_code == $_SESSION['verification_code']) {
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
    <link rel="stylesheet" href="verify.css">
</head>
<body>
    <section id="verification-screen" class="screen verification-screen">
        <h2>Verify Your Account</h2>
        <p>A verification code was sent to: <strong><?= htmlspecialchars($email); ?></strong></p>
        <form method="post">
            <input type="text" name="verification_code" placeholder="Enter Verification Code" required>
            <button type="submit">Verify</button>
        </form>
    </section>
</body>
</html>
