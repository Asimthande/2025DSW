<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

require_once "partial/connect.php";

$user_id = $_SESSION['user_id'];
$query = "SELECT role_id FROM Admins WHERE ID = ?";
$stmt = mysqli_prepare($conn, $query);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $role_id);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
} else {
    die("Error preparing query: " . mysqli_error($conn));
}

if ($role_id !== 1) {
    header("Location: admin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_admin'])) {
    $first_name = $_POST['FirstName'];
    $last_name = $_POST['LastName'];
    $email = $_POST['Email'];
    $password = password_hash($_POST['Password'], PASSWORD_DEFAULT);
    $role_id = $_POST['role_id']; 
    $end_of_contract = $_POST['EndOfContract'];
    $state = $_POST['state'];

    $sql = "INSERT INTO Admins (FirstName, LastName, Email, Password, role_id, EndOfContract, state) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssssis", $first_name, $last_name, $email, $password, $role_id, $end_of_contract, $state);
        if (mysqli_stmt_execute($stmt)) {
            $message = "Admin added successfully.";
        } else {
            $message = "Error adding admin: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $message = "Error preparing query: " . mysqli_error($conn);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_admin'])) {
    $admin_id = $_POST['ID'];
    $first_name = $_POST['FirstName'];
    $last_name = $_POST['LastName'];
    $email = $_POST['Email'];
    $role_id = $_POST['role_id'];
    $end_of_contract = $_POST['EndOfContract'];
    $state = $_POST['state'];

    $sql = "UPDATE Admins SET FirstName = ?, LastName = ?, Email = ?, role_id = ?, EndOfContract = ?, state = ? WHERE ID = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssssi", $first_name, $last_name, $email, $role_id, $end_of_contract, $state, $admin_id);
        if (mysqli_stmt_execute($stmt)) {
            $message = "Admin updated successfully.";
        } else {
            $message = "Error updating admin: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $message = "Error preparing query: " . mysqli_error($conn);
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_admin'])) {
    $admin_id = $_POST['ID'];

    $sql = "DELETE FROM Admins WHERE ID = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $admin_id);
        if (mysqli_stmt_execute($stmt)) {
            $message = "Admin removed successfully.";
        } else {
            $message = "Error removing admin: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $message = "Error preparing query: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Management</title>
    <link rel="stylesheet" href="admin-management.css">
</head>
<body>
    <div class="container">
        <h1>Admin Management</h1>

        <?php if (isset($message)): ?>
            <p class="<?= strpos($message, 'Error') === false ? 'success' : 'error' ?>"><?= $message ?></p>
        <?php endif; ?>
        <div class="form-container">
            <h2>Add New Admin</h2>
            <form method="POST" action="admin-management.php">
                <label for="FirstName">First Name:</label>
                <input type="text" name="FirstName" required><br><br>
                <label for="LastName">Last Name:</label>
                <input type="text" name="LastName" required><br><br>
                <label for="Email">Email:</label>
                <input type="email" name="Email" required><br><br>
                <label for="Password">Password:</label>
                <input type="password" name="Password" required><br><br>
                <label for="role_id">Role:</label>
                <select name="role_id">
                    <option value="1">Admin</option>
                    <option value="2">Manager</option>
                </select><br><br>
                <label for="EndOfContract">End of Contract:</label>
                <input type="date" name="EndOfContract" required><br><br>
                <label for="state">State:</label>
                <select name="state">
                    <option value="1">Verified</option>
                    <option value="0">Unverified</option>
                </select><br><br>
                <button class="button" type="submit" name="add_admin">Add Admin</button>
            </form>
        </div>
        <div class="form-container">
            <h2>Update Admin</h2>
            <form method="POST" action="admin-management.php">
                <label for="ID">Select Admin:</label>
                <select name="ID" required>
                    <option value="">--Select Admin--</option>
                    <?php
                    $sql = "SELECT * FROM Admins WHERE ID != ?";
                    $stmt = mysqli_prepare($conn, $sql);
                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "i", $user_id);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        while ($row = mysqli_fetch_assoc($result)): ?>
                            <option value="<?= $row['ID'] ?>"><?= $row['FirstName'] ?> <?= $row['LastName'] ?> (<?= $row['Email'] ?>)</option>
                        <?php endwhile;
                        mysqli_stmt_close($stmt);
                    }
                    ?>
                </select><br><br>
                <label for="FirstName">First Name:</label>
                <input type="text" name="FirstName" required><br><br>
                <label for="LastName">Last Name:</label>
                <input type="text" name="LastName" required><br><br>
                <label for="Email">Email:</label>
                <input type="email" name="Email" required><br><br>
                <label for="role_id">Role:</label>
                <select name="role_id">
                    <option value="1">Admin</option>
                    <option value="2">Manager</option>
                </select><br><br>
                <label for="EndOfContract">End of Contract:</label>
                <input type="date" name="EndOfContract" required><br><br>
                <label for="state">State:</label>
                <select name="state">
                    <option value="1">Verified</option>
                    <option value="0">Unverified</option>
                </select><br><br>
                <button class="button" type="submit" name="update_admin">Update Admin</button>
            </form>
        </div>
        <div class="form-container">
            <h2>Remove Admin</h2>
            <form method="POST" action="admin-management.php">
                <label for="ID">Select Admin:</label>
                <select name="ID" required>
                    <option value="">--Select Admin--</option>
                    <?php
                    $sql = "SELECT * FROM Admins WHERE ID != ?";
                    $stmt = mysqli_prepare($conn, $sql);
                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "i", $user_id);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        while ($row = mysqli_fetch_assoc($result)): ?>
                            <option value="<?= $row['ID'] ?>"><?= $row['FirstName'] ?> <?= $row['LastName'] ?> (<?= $row['Email'] ?>)</option>
                        <?php endwhile;
                        mysqli_stmt_close($stmt);
                    }
                    ?>
                </select><br><br>
                <button class="button" type="submit" name="remove_admin">Remove Admin</button>
            </form>
        </div>
        <h2>Admin List</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>End of Contract</th>
                    <th>State</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM Admins WHERE ID != ?";
                $stmt = mysqli_prepare($conn, $sql);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "i", $user_id);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $row['ID'] ?></td>
                            <td><?= $row['FirstName'] ?></td>
                            <td><?= $row['LastName'] ?></td>
                            <td><?= $row['Email'] ?></td>
                            <td><?= $row['role_id'] == 1 ? 'Admin' : 'Manager' ?></td>
                            <td><?= $row['EndOfContract'] ?></td>
                            <td><?= $row['state'] == 1 ? 'Verified' : 'Unverified' ?></td>
                        </tr>
                    <?php endwhile;
                    mysqli_stmt_close($stmt);
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
