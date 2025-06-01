<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'partial/connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: signin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answer'], $_POST['question_id'])) {
    $question_id = intval($_POST['question_id']);
    $answer = trim($_POST['answer']);
    $admin_name = $_SESSION['username'] ?? 'Admin';

    if (!empty($answer)) {
        $stmt = $conn->prepare("INSERT INTO Answers (QuestionID, Answer, AdminName) VALUES (?, ?, ?)");
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("iss", $question_id, $answer, $admin_name);
        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }
        $stmt->close();
    }
}

$questions = [];
$result = $conn->query("SELECT ID, Name, Email, Question FROM questions");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Answer Questions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5dc;
            color: #333;
            padding: 20px;
        }
        h1 {
            color: #d35400;
            text-align: center;
        }
        .question-box {
            background-color: #fff8dc;
            border: 2px solid #ffa500;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(255, 165, 0, 0.2);
        }
        textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: vertical;
        }
        button {
            background-color: #ffa500;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background-color: #e67e22;
        }
        label {
            font-weight: bold;
            color: #d35400;
        }
        .question-box p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <h1>Answer Submitted Questions</h1>

    <?php foreach ($questions as $q): ?>
        <div class="question-box">
            <p><strong>ID:</strong> <?= htmlspecialchars($q['ID']) ?></p>
            <p><strong>Name:</strong> <?= htmlspecialchars($q['Name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($q['Email']) ?></p>
            <p><strong>Question:</strong><br><?= nl2br(htmlspecialchars($q['Question'])) ?></p>

            <form method="POST" action="">
                <input type="hidden" name="question_id" value="<?= $q['ID'] ?>">
                <label for="answer_<?= $q['ID'] ?>">Your Answer:</label><br>
                <textarea name="answer" id="answer_<?= $q['ID'] ?>" required></textarea><br>
                <button type="submit">Submit Answer</button>
            </form>
        </div>
    <?php endforeach; ?>
</body>
</html>
