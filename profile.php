<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: signin.php");
    exit();
}

include 'partial/connect.php';

$studentNumber = $_SESSION['student_number'];
$firstName = $_SESSION['first_name'] ?? '';
$lastName = $_SESSION['last_name'] ?? '';
$email = $_SESSION['email'] ?? '';
$password = $_SESSION['password'] ?? '';
$message = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
    $updatedFirstName = $_POST['first_name'];
    $updatedLastName = $_POST['last_name'];
    $updatedEmail = $_POST['email'];
    $updatedPassword = $_POST['password'];

    $hashedPassword = password_hash($updatedPassword, PASSWORD_DEFAULT);

    $updateQuery = "UPDATE Students SET FirstName = ?, LastName = ?, Email = ?, Password = ? state=0 WHERE StudentNumber = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("sssss", $updatedFirstName, $updatedLastName, $updatedEmail, $hashedPassword, $studentNumber);

    if ($updateStmt->execute()) {
        $_SESSION['first_name'] = $updatedFirstName;
        $_SESSION['last_name'] = $updatedLastName;
        $_SESSION['email'] = $updatedEmail;
        $_SESSION['password'] = $updatedPassword; 
        header("Location: dashboard.php");
        exit();
    } else {
        $message = "Failed to update profile.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($studentNumber); ?>'s Profile</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <div class="profile-box">
        <h1><?php echo htmlspecialchars($studentNumber); ?></h1>
        
        <form method="POST" id="profile-form">
            <label>First Name:</label>
            <input type="text" name="first_name" id="first_name" value="<?php echo htmlspecialchars($firstName); ?>" readonly>

            <label>Last Name:</label>
            <input type="text" name="last_name" id="last_name" value="<?php echo htmlspecialchars($lastName); ?>" readonly>

            <label>Email:</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" readonly>

            <label>Password:</label>
            <input type="password" name="password" id="password" value="<?php echo htmlspecialchars($password); ?>" readonly>

            <button type="button" id="edit-btn">Edit Profile</button>
            <button type="submit" id="save-btn" name="save_profile" style="display: none;">Save Profile</button>
        </form>
        <p class="message"><?php echo $message ?? ''; ?></p>
    </div>

    <script src="profile.js"></script>
</body>
</html>
