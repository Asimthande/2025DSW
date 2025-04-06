<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

require_once "partial/connect.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'], $data['first_name'], $data['last_name'], $data['email'], $data['state'])) {
    echo json_encode(['success' => false, 'error' => 'Missing fields']);
    exit();
}

$id = intval($data['id']);
$firstName = mysqli_real_escape_string($conn, $data['first_name']);
$lastName = mysqli_real_escape_string($conn, $data['last_name']);
$email = mysqli_real_escape_string($conn, $data['email']);
$state = intval($data['state']);

$sql = "UPDATE Students SET FirstName = ?, LastName = ?, Email = ?, state = ? WHERE ID = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'error' => $conn->error]);
    exit();
}

$stmt->bind_param("sssii", $firstName, $lastName, $email, $state, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
