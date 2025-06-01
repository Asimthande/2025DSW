<?php
session_start();
require_once 'partial/connect.php';

if (!isset($_SESSION['student_number'])) {
    header("Location: login.php");
    exit();
}

$studentNumber = $_SESSION['student_number'];
$message = '';
$error = '';
$stmt = $conn->prepare("SELECT * FROM Students WHERE studentNumber = ?");
$stmt->bind_param("s", $studentNumber);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("User not found.");
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        // Update user info

        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $newPassword = $_POST['password'];
        $nameParts = explode(' ', $username, 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';
        if (empty($email)) {
            $error = "Email cannot be empty.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format.";
        }

        if (!$error) {
            if (!empty($newPassword)) {
                $hashedPwd = password_hash($newPassword, PASSWORD_BCRYPT);
                $updateSql = "UPDATE Students SET FirstName=?, LastName=?, Email=?, Password=? WHERE studentNumber=?";
                $stmt = $conn->prepare($updateSql);
                $stmt->bind_param("sssss", $firstName, $lastName, $email, $hashedPwd, $studentNumber);
            } else {
                $updateSql = "UPDATE Students SET FirstName=?, LastName=?, Email=? WHERE studentNumber=?";
                $stmt = $conn->prepare($updateSql);
                $stmt->bind_param("ssss", $firstName, $lastName, $email, $studentNumber);
            }

            if ($stmt->execute()) {
                $message = "Profile updated successfully.";
                $_SESSION['first_name'] = $firstName;
                $_SESSION['last_name'] = $lastName;
                $_SESSION['email'] = $email;

                $user['FirstName'] = $firstName;
                $user['LastName'] = $lastName;
                $user['Email'] = $email;
            } else {
                $error = "Failed to update profile.";
            }
            $stmt->close();
        }
    } elseif (isset($_POST['delete'])) {


        $passwordConfirm = $_POST['password_confirm'] ?? '';

        if (empty($passwordConfirm)) {
            $error = "Please enter your password to confirm deletion.";
        } elseif (!password_verify($passwordConfirm, $user['Password'])) {
            $error = "Incorrect password.";
        } else {
            $stmt = $conn->prepare("DELETE FROM Students WHERE studentNumber = ?");
            $stmt->bind_param("s", $studentNumber);

            if ($stmt->execute()) {
                $stmt->close();
                session_destroy();
                header("Location: signup.php"); 
                exit();
            } else {
                $error = "Failed to delete account.";
            }
            $stmt->close();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>User Settings</title>
        <link rel="icon" type="image/jpeg" href="images/Stabus.jpeg">
<style>body {
    font-family: Arial, sans-serif;
    background: #f5f5dc;
    padding: 30px;
    color: #333;
}

.container {
    max-width: 600px;
    margin: auto;
    background: #fffaf0;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 0 15px rgba(255, 165, 0, 0.2);
}

h1 {
    text-align: center;
    margin-bottom: 20px;
    color: orange;
}

label {
    display: block;
    margin-top: 15px;
    font-weight: bold;
    color: #444;
}

input[type="text"],
input[type="email"],
input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border-radius: 4px;
    border: 1px solid #d4a373;
    background-color: #fffaf0;
}

.btn {
    margin-top: 20px;
    padding: 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
}

.btn-save {
    background-color: orange;
    color: white;
}

.btn-delete {
    background-color: #cc7000;
    color: white;
}

.btn-save:hover {
    background-color: #e69500;
}

.btn-delete:hover {
    background-color: #a65600;
}

.message {
    margin-top: 15px;
    padding: 10px;
    border-radius: 4px;
}

.success {
    background-color: #ffe5b4;
    color: #7a4e00;
}

.error {
    background-color: #fddbb0;
    color: #803300;
}

.small-note {
    font-size: 0.85rem;
    color: #7a5c28;
    margin-top: 5px;
}
</style>
</head>
<body>
<div class="container">
<div class="back-button" style="background-color: beige; padding: 10px; border-radius: 5px;">
    <a href="dashboard.php" style="color: orange; text-decoration: none; font-weight: bold;">&larr; Back to Dashboard</a>
</div>

    <h1>User Settings</h1>

    <?php if ($message): ?>
        <div class="message success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <label for="username">Username (First Last)</label>
        <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['FirstName'] . ' ' . $user['LastName']) ?>" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['Email']) ?>" required>

        <label for="password">New Password (leave blank to keep current)</label>
        <input type="password" id="password" name="password" placeholder="Enter new password">

        <button type="submit" name="update" class="btn btn-save">Save Changes</button>
    </form>

    <hr style="margin: 40px 0;">

    <form method="post" action="" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
        <label for="password_confirm">Confirm Password to Delete Account</label>
        <input type="password" id="password_confirm" name="password_confirm" required>
        <button type="submit" name="delete" class="btn btn-delete">Delete Account</button>
    </form>
</div>
</body>
</html>
