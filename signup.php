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
                $_SESSION['last_name']=$last_name;
                $_SESSION['student_number'] = $student_number;
                $_SESSION['email']=$email;
                $_SESSION['first_name']=$first_name;
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
        <link rel="icon" type="image/jpeg" href="images/Stabus.jpeg">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="signin.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <meta name="google-signin-client_id" content="72387172929-9dmud5cuo2s1dimnch974rk96as6c583.apps.googleusercontent.com">
  <script src="https://accounts.google.com/gsi/client" async defer></script>
<script>
  function handleCredentialResponse(response) {
    const studentNumber = document.getElementById("studentid");
    
    if (studentNumber.value.length < 8) {
      alert("Please enter a valid student number.");
      studentNumber.focus();
      return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "google.php");
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    const postData = "id_token=" + encodeURIComponent(response.credential) +
                     "&student_number=" + encodeURIComponent(studentNumber.value);
    xhr.onload = function () {
  if (xhr.status === 200) {
    if (xhr.responseText.trim() === "success") {
      window.location.href = "verify.php"; 
    } else {
      alert(xhr.responseText); 
    }
  } else {
    alert("An error occurred: " + xhr.statusText);
  }
};

    xhr.send(postData);
    console.log(xhr);
  }

  window.onload = function () {
    google.accounts.id.initialize({
      client_id: "72387172929-9dmud5cuo2s1dimnch974rk96as6c583.apps.googleusercontent.com",
      callback: handleCredentialResponse
    });

    google.accounts.id.renderButton(
      document.getElementById("buttonDiv"),
      {
        theme: "outline",
        size: "large",
        text: "signup_with",
        width: "100%"
      }
    );
  };
</script>

</head>
<body>
  <div class="signup-container">
    <div class="signup-left" style="color:black;">
      <img src="images/Icon-2.jpeg" alt="Bus Illustration" class="bus-img">
      <h2>Welcome to JsJ Bus Tracker</h2>
      <p>Track your campus bus in real-time. Sign up to get started!</p>
      <form method="get">
        <p class="login-link" ><a href="dashboard.php?role='guest'" name="guest" >Login as Guest</a></p>
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
          <input type="text" id="studentid" name="student_number" value="<?php echo htmlspecialchars($_POST['student_number'] ?? '', ENT_QUOTES); ?>" maxlength="9" minlength="9" required>
        </div>
        <div class="input-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES); ?>" required placeholder="student@uj.ac.za">
        </div>
        <div class="input-group password-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required>
          <span class="toggle-password" onclick="togglePassword()">👁</span> 
        <small id="passwordError" style="color:red; display:none;">Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.</small>

        </div>
        <div class="input-group password-group">
          <label for="confirm_password">Confirm Password</label>
          <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="signup-btn" id="signup" disabled>Sign Up</button>
        <div id="forgot-password"><label><a href='forgot-password.php'>Forgot Password?</a></label></div>
        <div class="divider"><span>or</span></div>
        <div class="input-group" id="google-container">
            <div id="buttonDiv" ></div>
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
    function togglePassword() {
  const input = document.getElementById('password');
  input.type = input.type === 'password' ? 'text' : 'password';
}

function validatePassword(password) {
  return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/.test(password);
}

const passwordInput = document.getElementById('password');
const signupBtn = document.getElementById('signup');
const errorMsg = document.getElementById('passwordError');

passwordInput.addEventListener('input', () => {
  if (validatePassword(passwordInput.value)) {
    signupBtn.disabled = false;
    errorMsg.style.display = 'none';
  } else {
    signupBtn.disabled = true;
    errorMsg.style.display = 'block';
  }
});

  </script>
</body>
</html>
