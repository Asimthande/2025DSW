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

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errorMessage = "";

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = isset($_POST['role']) ? trim($_POST['role']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Validate the input
    if (empty($role) || empty($email) || empty($password)) {
        $errorMessage = "Please fill all fields correctly!";
    } else {
        // Determine which table to query based on the role
        $table = "";
        $location = "";

        // Set the table and the location based on the role
        if ($role === "admin") {
            $table = "Admins";
            $location = "admin.php";
        } elseif ($role === "driver") {
            $table = "tblDrivers";
            $location = "driver.php";
        } elseif ($role === "student") {
            $table = "Students";
            $location = "dashboard.php";
        } else {
            $errorMessage = "Invalid role selected.";
        }

        // If a valid table was set, query the database
        if ($table) {
            $sql = "SELECT * FROM $table WHERE Email = '$email'";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                // Fetch the user data
                $user = $result->fetch_assoc();

                // Check if the password matches
                if (isset($user['Password']) && !empty($user['Password'])) {
                    if (password_verify($password, $user['Password'])) {
                        // Check if the state is 0 (for verification)
                        if ($user['state'] == 0) {
                            // Set session variables and redirect to verify.php for verification
                            $_SESSION['user_id'] = $user['ID'];
                            $_SESSION['role'] = $role;
                            $_SESSION['first_name'] = $user['FirstName'];
                            $_SESSION['last_name'] = $user['LastName'];
                            $_SESSION['email'] = $user['Email'];
                            $_SESSION['student_number'] = $user['StudentNumber'];
                            $_SESSION['state'] = $user['state']; // Store the state value in session
                            header("Location: verify.php"); // Redirect to verification page
                            exit();
                        } elseif ($user['state'] == 1) {
                            // If the state is 1, direct the user to the dashboard
                            $_SESSION['user_id'] = $user['ID'];
                            $_SESSION['role'] = $role;
                            $_SESSION['first_name'] = $user['FirstName'];
                            $_SESSION['last_name'] = $user['LastName'];
                            $_SESSION['email'] = $user['Email'];
                            $_SESSION['student_number'] = $user['StudentNumber'];
                            header("Location: dashboard.php"); // Redirect to dashboard
                            exit();
                        }
                    } else {
                        $errorMessage = "Invalid email or password!";
                    }
                } else {
                    $errorMessage = "No password found in the database!";
                }
            } else {
                $errorMessage = "No user found with this email!";
            }
        }
    }

    // Close the database connection
    $conn->close();
}

// Check if the "Login as Guest" button is clicked
if (isset($_GET['guest_login']) && $_GET['guest_login'] === 'true') {
    $_SESSION['user_id'] = 'guest';
    $_SESSION['role'] = 'guest';
    $_SESSION['first_name'] = 'Guest';
    $_SESSION['last_name'] = 'Guest';
    $_SESSION['email'] = 'guest@example.com';
    header("Location: dashboard.php"); // Redirect to dashboard for guest
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Sign In</title>
    <link rel="stylesheet" href="signin.css">
</head>
<body>
    <section id="signin-screen" class="screen signin-screen">
        <h2>User Sign In</h2>

        <?php if ($errorMessage): ?>
            <div class="error-message" style="color: red; margin-bottom: 10px;">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>

        <form id="signin-form" method="post">
            <select name="role" required>
                <option value="" disabled selected>Select Role</option>
                <option value="admin" <?php echo isset($role) && $role === "admin" ? "selected" : ""; ?>>Admin</option>
                <option value="driver" <?php echo isset($role) && $role === "driver" ? "selected" : ""; ?>>Driver</option>
                <option value="student" <?php echo isset($role) && $role === "student" ? "selected" : ""; ?>>Student</option>
            </select>

            <input type="email" name="email" placeholder="Email" required value="<?php echo isset($email) ? $email : ''; ?>">
            <input type="password" name="password" placeholder="Password" required>
            
            <div class="social-login">
                <button type="button">Sign In with Google</button>
                <button type="button">Sign In with Facebook</button>
            </div>
            
            <a href="signup.php">Don't have an account? Sign Up</a>
            <a href="verify.php">Forgot Password?</a>
            
            <button type="submit" id="submit">Sign In</button>
        </form>

        <a href="signin.php?guest_login=true" id="guest-login-btn">Login as Guest</a>
    </section>

    <script>
        const form = document.getElementById("signin-form");

        form.addEventListener("submit", function(event) {
            const emailInput = form.querySelector('input[name="email"]');
            const passwordInput = form.querySelector('input[name="password"]');
            const roleInput = form.querySelector('select[name="role"]');
            
            if (!emailInput.value || !passwordInput.value || !roleInput.value) {
                event.preventDefault();
                alert("Please fill out all fields including selecting a role.");
            }
        });
    </script>
</body>
</html>
