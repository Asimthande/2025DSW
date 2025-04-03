<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "sql309.infinityfree.com";
$username = "if0_38514329";
$password = "NzPkYByqDBMU45L";
$dbname = "if0_38514329_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $student_number = mysqli_real_escape_string($conn, $_POST['student_number']); 
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "Passwords do not match.";
        exit;
    }

    // Hash the password before storing it in the database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the student_number already exists in the database
    $check_sql = "SELECT studentNumber FROM Students WHERE studentNumber = '$student_number'";
    $result = $conn->query($check_sql);

    if ($result->num_rows > 0) {
        echo "This student number is already registered.";
        exit;
    } else {
        // Insert the user data into the database if the student_number is unique
        $insert_sql = "INSERT INTO Students (studentNumber, firstName, lastName, email, password, state) 
                       VALUES ('$student_number', '$first_name', '$last_name', '$email', '$hashed_password', 0)";

        if ($conn->query($insert_sql) === TRUE) {
            // Start the session and store the student_number
            session_start();
            $_SESSION['student_number'] = $student_number;  // Store student number in session
            echo "User successfully registered! Please proceed to verification.";
            header("Location: verify.php");  // Redirect to verification page after successful registration
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Screen</title>
    <link rel="stylesheet" href="signup.css">
</head>
<body>
    <section id="signup-screen" class="screen signup-screen">
        <h2>Sign Up</h2>
        <form id="signup-form" method="POST">
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="student_number" placeholder="Student Number" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <div class="social-login">
                <button type="button">Sign Up with Google</button>
                <button type="button">Sign Up with Facebook</button>
            </div>
            <a href="signin.php">Already have an account? Sign In</a><a href="signin.php">Login as Guest</a>
            <button type="submit">Sign Up</button>
        </form>
    </section>
</body>
</html>
