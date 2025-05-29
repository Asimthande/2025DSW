<?php
session_start();
header('Content-Type: application/json');
include 'partial/connect.php';

if (!isset($_SESSION['bus_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$bus_id = intval($_SESSION['bus_id']);
$filter = $_GET['filter'] ?? 'monthly';

$conn = OpenCon();
$driverName = '';
$driverQuery = $conn->prepare("SELECT d.DriverName FROM tblDrivers d JOIN tblBuses b ON d.driver_id = b.driver_id WHERE b.bus_id = ?");
$driverQuery->bind_param("i", $bus_id);
$driverQuery->execute();
$driverResult = $driverQuery->get_result();
if ($driverRow = $driverResult->fetch_assoc()) {
    $driverName = $driverRow['DriverName'];
} else {
    echo json_encode(['error' => 'Driver not found']);
    CloseCon($conn);
    exit;
}
$sql = "
    SELECT DATE_FORMAT(tb.booking_time, '%Y-%m') AS month, COUNT(*) AS total_bookings
    FROM tblBookings tb
    JOIN tblBuses b ON tb.bus_id = b.bus_id
    JOIN tblDrivers d ON b.driver_id = d.driver_id
    WHERE b.bus_id = ?
    GROUP BY month
    ORDER BY month
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $bus_id);
$stmt->execute();
$result = $stmt->get_result();

$labels = [];
$counts = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['month'];
    $counts[] = (int)$row['total_bookings'];
    $total += (int)$row['total_bookings'];
}

CloseCon($conn);

echo json_encode([
    'labels' => $labels,
    'counts' => $counts,
    'summary' => "Driver: $driverName | Total Bookings: $total"
]);
