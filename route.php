<?php
// Start the session to check if the user is logged in
session_start();

// Check if the role session variable is set and if the role is 'student'
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    // Redirect to login page if the user is not a student
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Routes</title>
    <link rel="stylesheet" href="Route.css">
</head>
<body>

<div class="container">
    <h1>Bus Routes</h1>
    
    <div class="view-toggle">
        <button id="table-view-btn">Table View</button>
        <button id="picture-view-btn">Route Picture View</button>
    </div>
    <div id="table-view" class="view">
        <table id="routes-table">
            <thead>
                <tr>
                    <th>Route ID</th>
                    <th>Route Name</th>
                    <th>Start Point</th>
                    <th>End Point</th>
                    <th>Estimated Time</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Route A</td>
                    <td>Station 1</td>
                    <td>Station 5</td>
                    <td>15:30 PM</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Route B</td>
                    <td>Station 2</td>
                    <td>Station 6</td>
                    <td>16:45 PM</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Route C</td>
                    <td>Station 3</td>
                    <td>Station 7</td>
                    <td>18:00 PM</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div id="picture-view" class="view" style="display:none;">
        <div class="route-cards">
            <div class="route-card">
                <h3>Route: Route A</h3>
                <p><strong>Start:</strong> Station 1</p>
                <p><strong>End:</strong> Station 5</p>
                <p><strong>Estimated Time:</strong> 15:30 PM</p>
                <p><strong>Description:</strong> This route connects Station 1 to Station 5 with stops in between.</p>
            </div>
            <div class="route-card">
                <h3>Route: Route B</h3>
                <p><strong>Start:</strong> Station 2</p>
                <p><strong>End:</strong> Station 6</p>
                <p><strong>Estimated Time:</strong> 16:45 PM</p>
                <p><strong>Description:</strong> This route connects Station 2 to Station 6 with regular intervals.</p>
            </div>
            <div class="route-card">
                <h3>Route: Route C</h3>
                <p><strong>Start:</strong> Station 3</p>
                <p><strong>End:</strong> Station 7</p>
                <p><strong>Estimated Time:</strong> 18:00 PM</p>
                <p><strong>Description:</strong> A long route from Station 3 to Station 7 with scenic views.</p>
            </div>
        </div>
    </div>

</div>

<script src="Route.js"></script>

</body>
</html>
