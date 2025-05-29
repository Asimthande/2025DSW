<?php
session_start();
include "partial/connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['seat_id'])) {
        $seatId = $_POST['seat_id'];
        $studentNumber = $_SESSION['student_number'];
        $busId = $_SESSION['bus_id'];
        $bookingId = $_SESSION['booking_id'];
        $checkSeatSql = "SELECT reserved_seat FROM tblBookings WHERE bus_id = ? AND reserved_seat = ?";
        $stmt = $conn->prepare($checkSeatSql);
        $stmt->bind_param("is", $busId, $seatId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo json_encode(['status' => 'error', 'message' => 'This seat is already reserved']);
            exit();
        }
        $bookingTime = date("Y-m-d H:i:s");
        $insertBookingSql = "INSERT INTO tblBookings (student_number, bus_id, booking_time, reserved_seat) 
                             VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertBookingSql);
        $stmt->bind_param("siss", $studentNumber, $busId, $bookingTime, $seatId);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Seat successfully reserved']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to reserve the seat']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Seat ID is required']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
