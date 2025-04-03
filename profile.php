<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

if ($_SESSION['role'] !== 'student' && $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

$student_number = isset($_SESSION['student_number']) ? $_SESSION['student_number'] : '';
$first_name = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : '';
$last_name = isset($_SESSION['last_name']) ? $_SESSION['last_name'] : '';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$password = isset($_SESSION['password']) ? $_SESSION['password'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <div class="profile-container">
        <h1>Profile</h1>
        <form method="POST" action="" id="profile-form">
            <div class="input-group">
                <label for="profile-pic">Profile Picture:</label>
                <img id="profile-pic" src="https://via.placeholder.com/100" alt="Profile Picture" class="profile-img">
                <input type="file" id="profile-pic-upload" style="display:none;" accept="image/*">
            </div>
            <div class="input-group">
                <label for="student-number">Student Number:</label>
                <input type="text" id="student-number" name="student-number" placeholder="Enter Student Number" required readonly value="<?php echo htmlspecialchars($student_number); ?>">
            </div>
            <div class="input-group">
                <label for="firstname">First Name:</label>
                <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($first_name); ?>" readonly>
            </div>
            <div class="input-group">
                <label for="lastname">Last Name:</label>
                <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($last_name); ?>" readonly>
            </div>
            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly>
            </div>
            <div class="input-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($password); ?>" readonly>
            </div>
            <div class="input-group">
                <button type="button" id="edit-button" onclick="toggleEdit()">Edit Profile</button>
            </div>
        </form>
    </div>

    <script src="profile.js"></script>
</body>
</html>
