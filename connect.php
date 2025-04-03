<?php
$servername = "sql309.infinityfree.com";
$username = "if0_38514329";
$password = "NzPkYByqDBMU45L";
$dbname = "if0_38514329_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
