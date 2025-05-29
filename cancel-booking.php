<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel Booking</title>
    <link rel="stylesheet" href="cancel-booking.css">
</head>
<body>

<div class="top-bar">
    <a href="dashboard.php" class="button">Go to Dashboard</a>
</div>

<?php
include "partial/connect.php";
session_start();

if (!isset($_SESSION['student_number'])) {
    die("<p class='message'>Please Login</p>");
}

$student_number = $_SESSION['student_number'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];

    $get_bus_query = "SELECT bus_id FROM tblBookings WHERE booking_id=? AND student_number=?";
    $stmt = $conn->prepare($get_bus_query);
    $stmt->bind_param("is", $delete_id, $student_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $bus_row = $result->fetch_assoc();
        $bus_id = $bus_row['bus_id'];

        $delete_query = "DELETE FROM tblBookings WHERE booking_id=? AND student_number=?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("is", $delete_id, $student_number);
        $stmt->execute();

        $update_capacity_query = "UPDATE tblBusCapacity SET booked_seats = booked_seats - 1 WHERE bus_id=?";
        $stmt = $conn->prepare($update_capacity_query);
        $stmt->bind_param("s", $bus_id);
        $stmt->execute();

        echo "<p class='message'>Booking ID $delete_id canceled. Booked seats updated.</p>";
    } else {
        echo "<p class='message'>Booking not found or doesn't belong to you.</p>";
    }
}


$select_query = "SELECT * FROM tblBookings WHERE student_number=?";
$stmt = $conn->prepare($select_query);
$stmt->bind_param("s", $student_number);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<table>
        <tr>
            <th>Booking ID</th>
            <th>Bus ID</th>
            <th>Booking Time</th>
            <th>Reserved Seat</th>
            <th>Action</th>
        </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>" . htmlspecialchars($row['booking_id']) . "</td>
            <td>" . htmlspecialchars($row['bus_id']) . "</td>
            <td>" . htmlspecialchars($row['booking_time']) . "</td>
            <td>" . htmlspecialchars($row['reserved_seat']) . "</td>
            <td>
                <form method='POST' onsubmit=\"return confirm('Are you sure you want to cancel this booking?');\">
                    <input type='hidden' name='delete_id' value='" . $row['booking_id'] . "'>
                    <input type='submit' value='Cancel Booking'>
                </form>
            </td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "<p class='message'>No bookings found.</p>";
}
?>

</body>
</html>
