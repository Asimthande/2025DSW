<?php
session_start();
include "partial/connect.php";

function decodeIdToken($id_token) {
    $parts = explode('.', $id_token);
    if (count($parts) !== 3) return null;

    $payload = str_replace(['-', '_'], ['+', '/'], $parts[1]);
    $decoded = base64_decode($payload);
    return json_decode($decoded, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_token = $_POST['id_token'] ?? '';
    $student_number = $_POST['student_number'] ?? '';

    $user_data = decodeIdToken($id_token);

    if (isset($user_data['email']) && !empty($student_number)) {
        $email = $user_data['email'];

        $sql = "SELECT * FROM Students WHERE Email = ? AND StudentNumber = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $student_number);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $student = $result->fetch_assoc();

            $_SESSION['user_id'] = $student['ID'];
            $_SESSION['role'] = 'student';
            $_SESSION['first_name'] = $student['FirstName'];
            $_SESSION['last_name'] = $student['LastName'];
            $_SESSION['email'] = $student['Email'];
            $_SESSION['bus_id'] = $student['BusID'] ?? null;
            $_SESSION['student_number'] = $student['StudentNumber'];
            $_SESSION['LAST_ACTIVITY'] = time();

            if ((int)$student['state'] === 0) {
                echo "verify";
            } else {
                echo "success";
            }
        } else {
            echo "No matching student found for this email and student number.";
        }
    } else {
        echo "Invalid token or missing student number.";
    }
} else {
    echo "Invalid request method.";
}
?>