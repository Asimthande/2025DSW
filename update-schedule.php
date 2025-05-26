<?php
include 'partial/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bus_id = $_POST['bus_id'] ?? null;
    $route_id = $_POST['route_id'] ?? null;
    $time_input = $_POST['departure_time'] ?? null;

    if (!$bus_id || !$route_id || !$time_input) {
        exit("Missing required fields.");
    }

    $current_date = date('Y-m-d');

    $time_input = preg_match('/^\d{2}:\d{2}$/', $time_input) ? $time_input . ':00' : $time_input;

    $departure_datetime = $current_date . ' ' . $time_input;

    $conn = OpenCon();

    $stmt = $conn->prepare("
        INSERT INTO tblBusSchedules (bus_id, route_id, departure_time)
        VALUES (?, ?, ?)
    ");

    $stmt->bind_param("iis", $bus_id, $route_id, $departure_datetime);

    if ($stmt->execute()) {
        echo "Schedule submitted for $departure_datetime!";
    } else {
        echo "Error: " . $stmt->error;
    }

    CloseCon($conn);
} else {
    echo "Invalid request method.";
}
