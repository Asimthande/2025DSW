<?php
// Start the session
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    // If not authenticated, redirect to the login page
    header("Location: signin.php");
    exit();
}

// Check if the user is a student (only allow students)
if ($_SESSION['role'] !== 'student') {
    // If the user is not a student, redirect to another page (e.g., dashboard)
    header("Location: dashboard.php");
    exit();
}

// Fetch student details from the session
$student_name = $_SESSION['first_name']; // Assuming first_name is stored in the session
$student_id = $_SESSION['studentnumber']; // Assuming studentnumber is stored in the session
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UJ iReservation</title>
    <link rel="stylesheet" href="reservation.css">
</head>
<body>
    <div class="splash">
        <h1>Welcome to UJ STABUS Reservation</h1>
    </div>
    <div class="container">
        <h2>Book Your Ride</h2>
        <p>Reserve your seat for a comfortable ride</p>
        <form id="reservationForm">
            <label for="name">Full Name</label>
            <input type="text" id="name" value="<?php echo htmlspecialchars($student_name); ?>" placeholder="Enter your full name" readonly>
            
            <label for="student_id">Student Number</label>
            <input type="text" id="student_id" value="<?php echo htmlspecialchars($student_id); ?>" placeholder="Enter your student number" readonly>
            
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" placeholder="Enter your phone number" required>
            
            <label for="date">Date</label>
            <input type="date" id="date" required>
            
            <label for="time">Time</label>
            <input type="time" id="time" required>
            
            <label for="pickup">Pick-up Location</label>
            <select id="pickup" required>
                <option value="" disabled selected>Select Pick-up Location</option>
                <option value="APK">APK Campus</option>
                <option value="DFC">DFC Campus</option>
                <option value="SWC">Soweto Campus</option>
                <option value="APB">APB Campus</option>
            </select>
            
            <label for="destination">Destination</label>
            <select id="destination" required>
                <option value="">Select Destination</option>
                <option value="APK">Kingsway</option>
                <option value="Doornfontein">Doornfontein</option>
                <option value="Soweto">Soweto</option>
                <option value="APB">APB Campus</option>
            </select>
            
            <button type="submit">Confirm Reservation</button>
        </form>
        <p id="confirmation"></p>
    </div>
    
    <script src="reservetion.js"></script>
</body>
</html>
