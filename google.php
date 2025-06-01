<?php
include "partial/connect.php";

function decodeIdToken($id_token) {
    $parts = explode('.', $id_token);
    if (count($parts) !== 3) {
        return null; 
    }

    $payload = $parts[1];
    $payload = str_replace(['-', '_'], ['+', '/'], $payload);
    $payload = base64_decode($payload);
    return json_decode($payload, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_token = $_POST['id_token'] ?? '';
    $student_number = $_POST['student_number'] ?? '';

    $user_data = decodeIdToken($id_token);

    if (isset($user_data['email'])) {
        $first_name = $user_data['given_name'] ?? '';
        $last_name = $user_data['family_name'] ?? '';
        $email = $user_data['email'];

        $check_sql = "SELECT studentNumber FROM Students WHERE studentNumber = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("s", $student_number);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "This student number is already registered.";
        } else {
            $insert_sql = "INSERT INTO Students (studentNumber, firstName, lastName, email, state) 
                           VALUES (?, ?, ?, ?, 0)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("ssss", $student_number, $first_name, $last_name, $email);

            if ($stmt->execute()) {
                session_start();
                $_SESSION['student_number'] = $student_number;
                $_SESSION['email'] = $email;
                $_SESSION['first_name'] = $first_name;
                $_SESSION['last_name'] = $last_name;
                $_SESSION['role'] = 'student';

                echo "success";
                exit();
            } else {
                echo "Database Error: " . $conn->error;
            }
        }
    } else {
        echo "Invalid ID token.";
    }
} else {
    echo "No token received.";
}
