<?php
// Start the session
session_start();

// Database connection details
$servername = "sql309.infinityfree.com";
$username = "if0_38514329";
$password = "NzPkYByqDBMU45L";
$dbname = "if0_38514329_system";

// Create the connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if student_number session is set
if (!isset($_SESSION['student_number'])) {
    die("Unauthorized access: No student number found in session.");
}

// Generate and display the verification code if not already set
if (!isset($_SESSION['verification_code'])) {
    $_SESSION['verification_code'] = mt_rand(100000, 999999); // Generate a random 6-digit code
}

// Handle form submission for verification
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verification_code'])) {
    $user_code = trim($_POST['verification_code']);

    // Check if the entered code matches the session code
    if ($user_code == $_SESSION['verification_code']) {
        // Update the student's state in the database to 1
        $student_number = $_SESSION['student_number'];
        $sql = "UPDATE Students SET state = 1 WHERE StudentNumber = ?";
        
        // Prepare and execute the update query
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $student_number); // Bind the student number to the query
            $stmt->execute();
            
            // Check if the update was successful
            if ($stmt->affected_rows > 0) {
                echo "<script>alert('Verification successful! Account updated.'); window.location.href='dashboard.php';</script>";
            } else {
                echo "<script>alert('Error: Unable to update account.');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Error in preparing the query.');</script>";
        }

        unset($_SESSION['verification_code']); // Clear the code after success
        exit();
    } else {
        echo "<script>alert('Invalid verification code. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Screen</title>
    <link rel="stylesheet" href="verify.css">
</head>
<body>
    <section id="verification-screen" class="screen verification-screen">
        <h2>Verify Your Account</h2>
        <p>Enter the verification code sent to your email or phone.</p>
        
        <!-- Display the verification code on the screen -->
        <p><strong>Your verification code is: <?php echo $_SESSION['verification_code']; ?></strong></p>

        <!-- Form to enter the verification code -->
        <form action="verify.php" method="post">
            <input type="text" name="verification_code" placeholder="Enter Verification Code" required>
            <button type="submit">Verify</button>
        </form>
    </section>
</body>
</html>
