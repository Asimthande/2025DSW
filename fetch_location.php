<?php
require 'partial/connect.php';

if (isset($_GET['BusID'])) {
    $busID = intval($_GET['BusID']);

    $stmt = $conn->prepare("SELECT Latitude, Longitude FROM live WHERE BusID = ?");
    $stmt->bind_param("i", $busID);
    $stmt->execute();

    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    echo json_encode($data);
} else {
    echo json_encode(["error" => "Missing BusID"]);
}
?>
