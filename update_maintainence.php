<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: signin.php");
    exit();
}

require_once "partial/connect.php";

$AdminID = $_SESSION['user_id'];
$MaintainID = $_POST['MaintainID'];
$BusID = $_POST['BusID'];
$Date = $_POST['Date'];
$EstimatedReturn = $_POST['EstimatedReturn'];
$MaintainanceType = $_POST['MaintainanceType'];

$sql = "UPDATE Maintain 
        SET BusID = ?, Date = ?, `Estimated Return` = ?, `Maintainance Type` = ?, AdminID = ?
        WHERE MaintainID = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssssii", $BusID, $Date, $EstimatedReturn, $MaintainanceType, $AdminID, $MaintainID);

$success = mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Maintenance Updated</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; }
        .success-box { padding: 20px; background: #e0f7ff; border: 1px solid #3aaed8; display: inline-block; }
        a.button { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #2196F3; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="success-box">
        <h2><?= $success ? "Maintenance record updated successfully!" : "Failed to update record." ?></h2>
        <a class="button" href="admin.php">Done</a>
    </div>
</body>
</html>
