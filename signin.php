<?php
session_start();
include('partial/connect.php');
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/config.php';

$errorMessage = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($role) || empty($email) || empty($password)) {
        $errorMessage = "Please fill all fields correctly!";
    } else {
        $roles = [
            "admin" => ["Admins", "admin.php"],
            "driver" => ["tblDrivers", "driver.php"],
            "student" => ["Students", "dashboard.php"]
        ];

        if (!isset($roles[$role])) {
            $errorMessage = "Invalid role selected.";
        } else {
            list($table, $redirect) = $roles[$role];
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
                    $_SESSION['user_id'] = $user['ID'];
                    $_SESSION['role'] = $role;
                    $_SESSION['first_name'] = $user['FirstName'] ?? '';
                    $_SESSION['last_name'] = $user['LastName'] ?? '';
                    $_SESSION['email'] = $user['Email'] ?? '';
                    $_SESSION['bus_id'] = $user['BusID'];
                    $_SESSION['student_number'] = $user['StudentNumber'] ?? null;
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
        <link rel="icon" type="image/jpeg" href="images/Stabus.jpeg">
    <link rel="stylesheet" href="signin.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
 <script>
     function handleCredentialResponse(response) {
      
      const xhr = new XMLHttpRequest();
      xhr.open("POST", "Google.php");
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onload = () => {
        alert("Response from server: " + xhr.responseText);
      };
      xhr.send("id_token=" + response.credential);
    }

    window.onload = function () {
      google.accounts.id.initialize({
        client_id: "72387172929-9dmud5cuo2s1dimnch974rk96as6c583.apps.googleusercontent.com",
        callback: handleCredentialResponse
      });
      google.accounts.id.renderButton(
        document.getElementById("buttonDiv"),
        { theme: "outline", size: "large" }
      );
    };
  </script>
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
        <div id="forgot-password"><label><a href='forgot-password.php'>Forgot Password?</a></label></div>
        <button type="submit" class="signup-btn">Sign In</button>
        <div class="divider"><span>or</span></div>
        <div class="social-buttons">
  <div id="buttonDiv"></div>
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
  <script src="google.js"></script>
</body>
</html>
