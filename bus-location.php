<?php
include 'partial/connect.php';

header('Content-Type: application/json');

if (isset($_GET['BusID'])) {
    $busId = intval($_GET['BusID']);

    $stmt = $conn->prepare("SELECT Latitude, Longitude FROM live WHERE BusID = ?");
    $stmt->bind_param("i", $busId);
    $stmt->execute();
    $stmt->bind_result($lat, $lng);

    if ($stmt->fetch()) {
        echo json_encode([
            'Latitude' => $lat,
            'Longitude' => $lng,
            'BusID' => $busId
        ]);
    } else {
        echo json_encode(['error' => 'Bus not found']);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Missing BusID']);
}

$conn->close();
?>
