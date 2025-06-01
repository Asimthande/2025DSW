<?php
session_start();

if (!isset($_SESSION['student_number']) || $_SESSION['role'] !== 'student') {
    header("Location: dashboard.php");
    exit();
}

include('partial/connect.php');

$student_number = $_SESSION['student_number'];
$sortBy = $_GET['sort'] ?? 'newest';

switch ($sortBy) {
    case 'oldest':
        $orderBy = 'timestamp ASC';
        break;
    case 'name':
        $orderBy = 'message ASC';
        break;
    default:
        $orderBy = 'timestamp DESC';
        $sortBy = 'newest';
}

$stmt = $conn->prepare("SELECT id, message, timestamp, status FROM tblNotifications WHERE student_number = ? ORDER BY $orderBy");
$stmt->bind_param('s', $student_number);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Notification Center</title>
  <link rel="stylesheet" href="notifications.css" />
  <link rel="stylesheet" href="back.css" />
  <link rel="icon" type="image/jpeg" href="images/Stabus.jpeg" />
</head>
<body>
  <div class="notification-center">
    <h2>Notification Center</h2>
    <div class="back-button">
      <a href="dashboard.php">&larr; Back to Dashboard</a>
    </div>

    <div class="filters">
      <label for="order-select">Sort Notifications:</label>
      <select id="order-select" onchange="location.href='?sort=' + this.value;">
        <option value="newest" <?= $sortBy === 'newest' ? 'selected' : '' ?>>Newest First</option>
        <option value="oldest" <?= $sortBy === 'oldest' ? 'selected' : '' ?>>Oldest First</option>
        <option value="name" <?= $sortBy === 'name' ? 'selected' : '' ?>>By Message</option>
      </select>
    </div>

    <ul id="notifications-list">
      <?php if ($result->num_rows === 0): ?>
        <li>No new notifications</li>
      <?php else: ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <li class="notification <?= $row['status'] === 'unread' ? 'unread' : 'read'; ?>">
            <strong>Message:</strong> <?= htmlspecialchars($row['message']); ?><br>
            <strong>Time:</strong> <?= date('Y-m-d H:i:s', strtotime($row['timestamp'])); ?><br>

            <?php if ($row['status'] === 'unread'): ?>
              <form action="markAsRead.php" method="POST">
                <input type="hidden" name="notification_id" value="<?= $row['id']; ?>" />
                <button type="submit">Mark as Read</button>
              </form>
            <?php else: ?>
              <button disabled>Already Read</button>
            <?php endif; ?>
          </li><br>
        <?php endwhile; ?>
      <?php endif; ?>
    </ul>
  </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const forms = document.querySelectorAll('form');
  forms.forEach(form => {
    form.addEventListener('submit', function () {
      const li = this.closest('li.notification');
      li.classList.remove('unread');
      li.classList.add('read');
    });
  });
});
</script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
