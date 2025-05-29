<?php
session_start();

if (!isset($_SESSION['bus_id'])) {
    http_response_code(403);
    echo "Unauthorized";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['latitude'], $_POST['longitude'], $_POST['datetime'])) {
    include 'partial/connect.php';

    $busId = $_SESSION['bus_id'];
    $latitude = floatval($_POST['latitude']);
    $longitude = floatval($_POST['longitude']);
    $datetime = $_POST['datetime']; // Expect format YYYY-MM-DD HH:MM:SS

    // Validate datetime format (simple check)
    if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $datetime)) {
        http_response_code(400);
        echo "Invalid datetime format";
        exit();
    }

    $stmt = $conn->prepare("UPDATE live SET latitude = ?, longitude = ?, UpdateTime = ? WHERE BusID = ?");
    if (!$stmt) {
        http_response_code(500);
        echo "Database prepare error: " . $conn->error;
        exit();
    }

    $stmt->bind_param("ddsi", $latitude, $longitude, $datetime, $busId);

    if ($stmt->execute()) {
        http_response_code(200);
        echo "Location updated successfully";
    } else {
        http_response_code(500);
        echo "Failed to update location: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(400);
    echo "Invalid request";
}
?>
