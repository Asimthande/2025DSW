<?php
include 'partial/connect.php';

header('Content-Type: application/json');

$sql = "SELECT BusID, Latitude, Longitude, UpdateTime FROM live";
$result = mysqli_query($conn, $sql);

$buses = [];
while ($row = mysqli_fetch_assoc($result)) {
    $buses[] = $row;
}

echo json_encode($buses);
?>
