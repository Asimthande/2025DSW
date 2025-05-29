<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: signin.php");
    exit();
}
require_once "partial/connect.php";

$students = mysqli_query($conn, "SELECT ID, StudentNumber, FirstName, LastName, Email, state FROM Students");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link rel="stylesheet" href="admin.css">
        <link rel="icon" type="image/jpeg" href="images/Stabus.jpeg">
</head>
<body>
<div class="admin-container">
    <header>
        <h1>Manage Users</h1>
    </header>

    <section id="manage-users">
        <h2>All Students</h2>
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Student Number</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>State</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($student = mysqli_fetch_assoc($students)): ?>
                <tr data-id="<?= $student['ID'] ?>">
                    <td><?= $student['ID'] ?></td>
                    <td><?= htmlspecialchars($student['StudentNumber']) ?></td>
                    <td contenteditable="true" class="editable" data-field="first_name"><?= htmlspecialchars($student['FirstName']) ?></td>
                    <td contenteditable="true" class="editable" data-field="last_name"><?= htmlspecialchars($student['LastName']) ?></td>
                    <td contenteditable="true" class="editable" data-field="email"><?= htmlspecialchars($student['Email']) ?></td>
                    <td>
                        <select class="state-select" data-field="state">
                            <option value="1" <?= $student['state'] == 1 ? 'selected' : '' ?>>Verified</option>
                            <option value="0" <?= $student['state'] == 0 ? 'selected' : '' ?>>Unverified</option>
                        </select>
                    </td>
                    <td><button class="update-btn">Update</button></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</div>

<script src="manage_users.js"></script>
</body>
</html>
