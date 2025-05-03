<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

include "partial/connect.php";

// Fetch the current booking details
$bookingId = $_SESSION['booking_id'];  // Assume booking_id is stored in session
$sql = "SELECT bus_id, booking_time FROM tblBookings WHERE booking_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $bookingId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "No booking found!";
    exit();
}

$booking = $result->fetch_assoc();
$busId = $booking['bus_id'];
$currentHour = date('H', strtotime($booking['booking_time']));

// Get reserved seats at the same hour and bus_id
$sql = "SELECT reserved_seat FROM tblBookings 
        WHERE bus_id = ? AND HOUR(booking_time) = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $busId, $currentHour);
$stmt->execute();
$result = $stmt->get_result();

$reservedSeats = [];
while ($row = $result->fetch_assoc()) {
    $reservedSeats[] = $row['reserved_seat'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserve Seat</title>
    <link rel="stylesheet" href="reserve-seat.css">
</head>
<body>
    <div class="bus-container">
        <div class="driver-seat">
            <div class="bus-plate">BUS-<?php echo $busId; ?></div>
            <div class="driver-id">Driver ID: 987654</div>
            Driver
        </div>

        <!-- Dynamically generate seats using PHP and JavaScript -->
        <div id="seats-layout">
            <!-- Seat layout will be injected here by JS -->
        </div>
    </div>

    <!-- Modal for notification -->
    <div id="seatNotification" class="notification">
        <p id="seatMessage"></p>
    </div>

    <script src="reserve-seat.js"></script>
    <script>
        // Create the seat layout dynamically in JavaScript
        document.addEventListener("DOMContentLoaded", function() {
            const reservedSeats = <?php echo json_encode($reservedSeats); ?>;
            const seatContainer = document.getElementById('seats-layout');
            let seatCount = 1;

            // Create 12 rows for 60 seats
            for (let row = 0; row < 12; row++) { // 12 rows for 60 seats
                let rowHTML = '<div class="row">';
                for (let col = 0; col < 5; col++) {
                    // Left seats (seat1, seat2, seat6, seat7, ...)
                    const seatLabelLeft = "L" + seatCount;
                    rowHTML += createSeatHTML(seatLabelLeft, reservedSeats);
                    seatCount++;

                    // Aisle in between
                    if (col === 2) {
                        rowHTML += '<div class="aisle"></div>';
                    }

                    // Right seats (seat3, seat4, seat8, seat9, ...)
                    if (col === 4) {
                        const seatLabelRight = "R" + (seatCount - 1);
                        rowHTML += createSeatHTML(seatLabelRight, reservedSeats);
                    }
                }
                rowHTML += '</div>';
                seatContainer.innerHTML += rowHTML;
            }
        });

        // Function to create the HTML for each seat (reserved or available)
        function createSeatHTML(seatLabel, reservedSeats) {
            const isReserved = reservedSeats.includes(seatLabel); // Check if seat is reserved
            const seatClass = isReserved ? 'reserved' : 'available'; // Add the appropriate class for the seat
            return `<div class="seat ${seatClass}" id="${seatLabel}">${seatLabel}</div>`;
        }
    </script>
</body>
</html>
