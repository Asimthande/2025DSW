<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: signin.php");
    exit();
}

require_once "partial/connect.php";

$AdminID = $_SESSION['user_id'];
$BusID = $_POST['BusID'];
$Date = $_POST['Date'];
$EstimatedReturn = $_POST['EstimatedReturn'];
$MaintainanceType = $_POST['MaintainanceType'];

$sql = "INSERT INTO Maintain (BusID, Date, `Estimated Return`, `Maintainance Type`, AdminID)
        VALUES (?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssssi", $BusID, $Date, $EstimatedReturn, $MaintainanceType, $AdminID);

$success = mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Maintenance Added</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; }
        .success-box { padding: 20px; background: #e0ffe0; border: 1px solid #6c6; display: inline-block; }
        a.button { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="success-box">
        <h2><?= $success ? "Maintenance record added successfully!" : "Failed to add record." ?></h2>
        <a class="button" href="admin.php">Done</a>
    </div>
</body>
</html>
