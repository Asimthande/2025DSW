<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('partial/connect.php');

$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $student_number = mysqli_real_escape_string($conn, $_POST['student_number']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $errorMessage = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $check_sql = "SELECT studentNumber FROM Students WHERE studentNumber = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("s", $student_number);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errorMessage = "This student number is already registered.";
        } else {
            $insert_sql = "INSERT INTO Students (studentNumber, firstName, lastName, email, password, state) 
                           VALUES (?, ?, ?, ?, ?, 0)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("sssss", $student_number, $first_name, $last_name, $email, $hashed_password);

            if ($stmt->execute()) {
                session_start();
                $_SESSION['student_number'] = $student_number;
                header("Location: verify.php");
                exit();
            } else {
                $errorMessage = "Error: " . $conn->error;
            }
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign Up</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="signin.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <script src="https://apis.google.com/js/platform.js" async defer></script>
</head>
<body>
  <div class="signup-container">
    <div class="signup-left">
      <img src="https://i.pinimg.com/474x/81/e9/27/81e9273c1fb5426abf62956af6ee6b38.jpg" alt="University of Johannesburg Logo" class="logo">
      <h2>Welcome to UJ Bus Tracker</h2>
      <p>Track your campus bus in real-time. Sign up to get started!</p>
      <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQXjKYvyZjXDmVdD1nZVX7uhjqRR3LGDIMHOQ&s" alt="Bus Illustration" class="bus-img">
      <form method="get">
        <p class="login-link"><a href="dashboard.php?role='guest'" name="guest" >Login as Guest</a></p>
        </form>
        <?php
        if(isset($_GET['guest'])){
            session_start();
            $_SESSION['role']='guest';
            $_SESSION['name']='guest';
            $_SESSION['surname']='guest';
            $_SESSION['studentnumber']='99';
        }
        ?>
    </div>
    <div class="signup-right">
      <form class="signup-form" method="POST" autocomplete="off">
        <h3>Create Account</h3>
        <?php if (!empty($errorMessage)): ?>
          <div class="error-message"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        <div class="input-group">
          <label for="first-name">First Name</label>
          <input type="text" id="first-name" name="first_name" value="<?php echo htmlspecialchars($_POST['first_name'] ?? '', ENT_QUOTES); ?>" required autofocus>
        </div>
        <div class="input-group">
          <label for="last-name">Last Name</label>
          <input type="text" id="last-name" name="last_name" value="<?php echo htmlspecialchars($_POST['last_name'] ?? '', ENT_QUOTES); ?>" required>
        </div>
        <div class="input-group">
          <label for="studentid">Student Number</label>
          <input type="text" id="studentid" name="student_number" value="<?php echo htmlspecialchars($_POST['student_number'] ?? '', ENT_QUOTES); ?>" required>
        </div>
        <div class="input-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES); ?>" required placeholder="student@uj.ac.za">
        </div>
        <div class="input-group password-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required>
          <span class="toggle-password" onclick="togglePassword()">👁</span>
        </div>
        <div class="input-group password-group">
          <label for="confirm_password">Confirm Password</label>
          <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="signup-btn">Sign Up</button>
        <div class="divider"><span>or</span></div>
        <div class="social-buttons">
          <button type="button" class="google-btn" id="googleAuth" onclick="studentNumber()"><i class="fab fa-google"></i> Sign up with Google</button>
          <button type="button" class="facebook-btn" id="facebookAuth" ><i class="fab fa-facebook-f"></i> Sign up with Facebook</button>
        </div>
        <p class="login-link">Already have an account? <a href="signin.php">Log in</a></p>
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
