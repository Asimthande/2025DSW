<?php
// ✅ CORS HEADERS — Required especially for InfinityFree and fetch()
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

session_start();

// ✅ AUTH CHECK
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

// ✅ DB CONNECT
require_once "partial/connect.php";

// ✅ PARSE JSON PAYLOAD
$data = json_decode(file_get_contents("php://input"), true);

// ✅ VALIDATION
if (!isset($data['id'], $data['first_name'], $data['last_name'], $data['email'], $data['state'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit();
}

// ✅ SANITIZE & ASSIGN
$id = intval($data['id']);
$firstName = mysqli_real_escape_string($conn, $data['first_name']);
$lastName = mysqli_real_escape_string($conn, $data['last_name']);
$email = mysqli_real_escape_string($conn, $data['email']);
$state = intval($data['state']);

// ✅ UPDATE QUERY
$sql = "UPDATE Students SET FirstName = ?, LastName = ?, Email = ?, state = ? WHERE ID = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $conn->error]);
    exit();
}

$stmt->bind_param("sssii", $firstName, $lastName, $email, $state, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Execute failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
