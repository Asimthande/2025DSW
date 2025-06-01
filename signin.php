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
        <script src="https://accounts.google.com/gsi/client" async defer></script>
    <link rel="stylesheet" href="signin.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"><script>
  function handleCredentialResponse(response) {
      let studentNumber=document.getElementById("studentNumber").value;

 if (!/^\d{9}$/.test(studentNumber)) {
      alert("Student number must be exactly 9 digits.");
      return;
    }
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "signin-auth.php");
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = () => {
      const res = xhr.responseText.trim();
      if (res === "success") {
        window.location.href = "dashboard.php";
      } else if (res === "verify") {
        window.location.href = "verify.php";
      } else {
        alert("Login failed: " + res);
      }
    };

    xhr.send("id_token=" + encodeURIComponent(response.credential) +
             "&student_number=" + encodeURIComponent(studentNumber));
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
    <div class="signup-left" style="color:black">
      <img src="images/Icon-2.jpeg" alt="Bus Illustration" class="bus-img">
      <h2>Welcome Back to JsJ Bus Tracker</h2>
      <p>Sign in to your account to access the bus tracking features!</p>
      
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
          <select id="role" name="role" required>
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
        <div class="input-group" id="student-number-container" style="display:none">
          <label for="studentNumber">Student Number</label>
        <input type="text" id="studentNumber" placeholder="Enter Student Number" minlength="9" maxlength="9" required/>
            </div>
        <button type="submit" class="signup-btn" id="submit">Sign In</button>
        <div id="forgot-password"><label><a href='forgot-password.php'>Forgot Password?</a></label></div>
        <div class="divider"><span>or</span></div>
        <div class="social-buttons" id="google-container">
  <div id="buttonDiv"></div>
  
        </div>
        <p class="login-link">Don't have an account? <a href="signup.php">Sign Up</a></p>
      </form>
    </div>
  </div>

  <script>
  function driver(){
      let role=document.getElementById("role").value;
      let student_number=document.getElementById("student-number-container");
      let student_identity=document.getElementById("studentNumber");
      if(role!=='student'){
          student_identity.required=false;
          student_number.style.display='none';
      }
      else{
          student_identity.required=true;
          student_number.style.display='flex';
      }
  }  
  document.getElementById("role").onchange=driver;
    function togglePassword() {
      const pw = document.getElementById('password');
      pw.type = pw.type === 'password' ? 'text' : 'password';
    }
  </script>
  <script src="google.js"></script>
</body>
</html>
