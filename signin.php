<?php
session_start();

// Include the database connection file
include('partial/connect.php');  // This includes the connection from partial/connect.php

$errorMessage = "";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($role) || empty($email) || empty($password)) {
        $errorMessage = "Please fill all fields correctly!";
    } else {
        // Define roles and their corresponding tables
        $roles = [
            "admin" => ["Admins", "admin.php"],
            "driver" => ["tblDrivers", "driver.php"],
            "student" => ["Students", "dashboard.php"]
        ];

        if (!isset($roles[$role])) {
            $errorMessage = "Invalid role selected.";
        } else {
            list($table, $redirect) = $roles[$role];

            // Prepare SQL query to fetch user data from the corresponding table
            $sql = "SELECT * FROM $table WHERE Email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $dbPassword = $user['Password'] ?? '';
                $passwordMatch = password_verify($password, $dbPassword) || $password === $dbPassword;

                if ($passwordMatch) {
                    // Store user details in session
                    $_SESSION['user_id'] = $user['ID'];
                    $_SESSION['role'] = $role;
                    $_SESSION['first_name'] = $user['FirstName'] ?? '';
                    $_SESSION['last_name'] = $user['LastName'] ?? '';
                    $_SESSION['email'] = $user['Email'] ?? '';
                    $_SESSION['student_number'] = $user['StudentNumber'] ?? null;

                    // Redirect to the role-specific page
                    if ($role === "student" && isset($user['state']) && $user['state'] == 0) {
                        header("Location: verify.php");
                        exit();
                    }

                    header("Location: $redirect");
                    exit();
                } else {
                    $errorMessage = "Invalid email or password!";
                }
            } else {
                $errorMessage = "No user found with this email!";
            }
        }
    }
    $conn->close();
}

// Guest login functionality
if (isset($_GET['guest_login']) && $_GET['guest_login'] === 'true') {
    $_SESSION['user_id'] = 'guest';
    $_SESSION['role'] = 'guest';
    $_SESSION['first_name'] = 'Guest';
    $_SESSION['last_name'] = 'Guest';
    $_SESSION['email'] = 'guest@example.com';
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Sign In</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="signin.css"> <!-- Use the same signup.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Add Font Awesome -->
</head>
<body>
  <div class="signup-container">
    <div class="signup-left">
      <img src="https://i.pinimg.com/474x/81/e9/27/81e9273c1fb5426abf62956af6ee6b38.jpg" alt="University of Johannesburg Logo" class="logo">
      <h2>Welcome Back to UJ Bus Tracker</h2>
      <p>Sign in to your account to access the bus tracking features!</p>
      <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQXjKYvyZjXDmVdD1nZVX7uhjqRR3LGDIMHOQ&s" alt="Bus Illustration" class="bus-img">
      
      <p class="login-link"><a href="dashboard.php?role='guest'">Login as Guest</a></p>
    </div>
    <div class="signup-right">
      <form class="signup-form" method="POST" autocomplete="off">
        <h3>Sign In</h3>
        <?php if (!empty($errorMessage)): ?>
          <div class="error-message"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        <div class="input-group">
          <label for="role">Select Role</label>
          <select name="role" required>
            <option value="" disabled selected >Select Role</option>
            <option value="admin">Admin</option>
            <option value="driver">Driver</option>
            <option value="student">Student</option>
          </select>
        </div>
        <div class="input-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES); ?>" required>
        </div>
        <div class="input-group password-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required>
          <span class="toggle-password" onclick="togglePassword()">👁</span>
        </div>
        <button type="submit" class="signup-btn">Sign In</button>
        <div class="divider"><span>or</span></div>
        <div class="social-buttons">
          <button type="button" class="google-btn"><i class="fab fa-google"></i> Sign in with Google</button>
          <button type="button" class="facebook-btn"><i class="fab fa-facebook-f"></i> Sign in with Facebook</button>
        </div>
        <p class="login-link">Don't have an account? <a href="signup.php">Sign Up</a></p>
      </form>
    </div>
  </div>

  <script>
    function togglePassword() {
      const pw = document.getElementById('password');
      pw.type = pw.type === 'password' ? 'text' : 'password';
    }
  </script>
</body>
</html>
