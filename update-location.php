<?php
require 'partial/connect.php';

$busID = $_POST['BusID'];
$latitude = $_POST['Latitude'];
$longitude = $_POST['Longitude'];

$stmt = $conn->prepare("UPDATE live SET Latitude = ?, Longitude = ? WHERE BusID = ?");
$stmt->bind_param("ssi", $latitude, $longitude, $busID);
$stmt->execute();
?>
