<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'partial/connect.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}
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
    header("Location: signin.php");
    exit();
}
if (isset($_POST['add_driver'])) {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $busID = $_POST['bus_id'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $state = $_POST['state'];

    $sql = "INSERT INTO tblDrivers (FirstName, LastName, BusID, Email, Password, state)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisss", $firstName, $lastName, $busID, $email, $password, $state);
    $stmt->execute();
}
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM tblDrivers WHERE ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}
if (isset($_POST['update_driver'])) {
    $id = $_POST['id'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $busID = $_POST['bus_id'];
    $email = $_POST['email'];
    $state = $_POST['state'];

    $sql = "UPDATE tblDrivers SET FirstName=?, LastName=?, BusID=?, Email=?, state=? WHERE ID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssissi", $firstName, $lastName, $busID, $email, $state, $id);
    $stmt->execute();
}
$result = $conn->query("SELECT * FROM tblDrivers");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Drivers</title>
    <link rel="stylesheet" href="manage_drivers.css">
        <link rel="icon" type="image/jpeg" href="images/Stabus.jpeg">

</head>
<body>
    <h2>Add New Driver</h2>
    <form method="post" action="">
        <input type="text" name="first_name" placeholder="First Name" required>
        <input type="text" name="last_name" placeholder="Last Name" required>
        <input type="number" name="bus_id" placeholder="Bus ID" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="text" name="state" placeholder="State" required>
        <button type="submit" name="add_driver">Add Driver</button>
    </form>

    <h2>Existing Drivers</h2>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>ID</th><th>First Name</th><th>Last Name</th><th>Bus ID</th><th>Email</th><th>State</th><th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <form method="post" action="">
                    <td><?php echo $row['ID']; ?><input type="hidden" name="id" value="<?php echo $row['ID']; ?>"></td>
                    <td><input type="text" name="first_name" value="<?php echo $row['FirstName']; ?>"></td>
                    <td><input type="text" name="last_name" value="<?php echo $row['LastName']; ?>"></td>
                    <td><input type="number" name="bus_id" value="<?php echo $row['BusID']; ?>"></td>
                    <td><input type="email" name="email" value="<?php echo $row['Email']; ?>"></td>
                    <td><input type="text" name="state" value="<?php echo $row['state']; ?>"></td>
                    <td>
                        <button type="submit" name="update_driver">Update</button>
                        <a href="?delete=<?php echo $row['ID']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </form>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
