<?php
session_start();
include 'partial/connect.php';

$bus_id = $_SESSION['bus_id'];
$departure_time = $_POST['departure_time'];
$eta = $_POST['eta'];

$sql = "INSERT INTO tblBusSchedules (bus_id, departure_time, eta) 
        VALUES ('$bus_id', '$departure_time', '$eta')";

if (mysqli_query($conn, $sql)) {
    echo "Schedule added successfully.";
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
