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
        <h1>Welcome to UJ STUBUS Reservation</h1>
    </div>
    <div class="container">
        <h2>Book Your Ride</h2>
        <p>Reserve your seat for a comfortable ride</p>
        <form id="reservationForm">
            <label for="name">Full Name</label>
            <input type="text" id="name" placeholder="Enter your full name" required>
            
            <label for="student_id">Student Number</label>
            <input type="text" id="student_id" placeholder="Enter your student number" required>
            
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
                <option value="Kingsway">Kingsway</option>
            </select>
            
            <label for="destination">Destination</label>
            <select id="destination" required>
                <option value="">Select Destination</option>
                <option value="Kingsway">Kingsway</option>
                <option value="Doornfontein">Doornfontein</option>
                <option value="Soweto">Soweto</option>
            </select>
            
            <button type="submit">Confirm Reservation</button>
        </form>
        <p id="confirmation"></p>
    </div>
    
    <script src="reservetion.js"></script>
</body>
</html>
