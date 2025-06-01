<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'partial/connect.php';

$successMessage = '';
$errorMessage = '';
$faqResult = null;
$answers = [];

// Submit new question
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $question = $_POST['message'] ?? '';

    $stmt = $conn->prepare("INSERT INTO questions (Name, Email, Question) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sss", $name, $email, $question);
        if ($stmt->execute()) {
            $successMessage = 'Your question has been submitted successfully!';
        } else {
            $errorMessage = 'Could not submit your question.';
        }
        $stmt->close();
    } else {
        $errorMessage = 'Database error.';
    }
}

// Get all questions
$faqQuery = "SELECT * FROM questions ORDER BY ID DESC";
$faqResult = $conn->query($faqQuery);

// Get all answers, map by QuestionID
$answerQuery = "SELECT * FROM Answers";
$answerResult = $conn->query($answerQuery);
if ($answerResult && $answerResult->num_rows > 0) {
    while ($row = $answerResult->fetch_assoc()) {
        $answers[$row['QuestionID']] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FAQs & Support</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/jpeg" href="images/Stabus.jpeg">
    <link rel="stylesheet" href="support.css">
    <style>
        body {
            background-color: #f5f5dc;
            font-family: Arial, sans-serif;
            color: #333;
            padding: 20px;
        }
        h1, h2 {
            color: #d35400;
        }
        .faq, .faq-question, .faq-answer {
            margin-bottom: 15px;
        }.faq-question {
    background-color: #fff8dc;
    border: 2px solid #ffa500;
    padding: 15px;
    cursor: pointer;
    border-radius: 8px;
    color: #000; /* <-- Makes question text black */
}

.faq-question p {
    color: #000; /* <-- Ensures paragraph inside is black */
}

        .faq-answer {
            display: none;
            background-color: #fff3e0;
            border-left: 4px solid #ffa500;
            padding: 10px 15px;
            border-radius: 0 0 8px 8px;
        }
        .container {
            margin-bottom: 40px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .submit-btn {
            background-color: #ffa500;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #e67e22;
        }
        .back-button a {
            color: orange;
            text-decoration: none;
            font-weight: bold;
        }
        .corner-image {
            width: 70px;
            position: fixed;
            top: 20px;
            right: 20px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <img src="images/Chatbot Chat Message.jpg" alt="Bot Pic" class="corner-image" style="border:3px solid orange;border-radius:30px;" onclick="window.location.href='AI.php'">

    <div class="container">
        <div class="back-button" style="background-color: beige; padding: 10px; border-radius: 5px;">
            <a href="dashboard.php">&larr; Back to Dashboard</a>
        </div>

        <h1>FAQs & Support</h1>

        <?php if (!empty($successMessage)): ?>
            <p style="color: green; font-weight: bold;"><?= htmlspecialchars($successMessage) ?></p>
        <?php elseif (!empty($errorMessage)): ?>
            <p style="color: red; font-weight: bold;"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>

        <section id="faqs">
            <h2>Frequently Asked Questions</h2>

            <?php if ($faqResult && $faqResult->num_rows > 0): ?>
                <?php while ($row = $faqResult->fetch_assoc()): ?>
                    <div class="faq">
                        <div class="faq-question" onclick="toggleAnswer(this)">
                            <strong><?= htmlspecialchars($row['Name']) ?></strong> asked:
                            <p><?= htmlspecialchars($row['Question']) ?></p>
                        </div>
                        <div class="faq-answer">
                            <?php if (isset($answers[$row['ID']])): ?>
                                <p><strong>Answer by <?= htmlspecialchars($answers[$row['ID']]['AdminName']) ?>:</strong><br>
                                <?= nl2br(htmlspecialchars($answers[$row['ID']]['Answer'])) ?></p>
                                <small>Answered on <?= $answers[$row['ID']]['AnsweredAt'] ?></small>
                            <?php else: ?>
                                <p><em>This question has not been answered yet.</em></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No questions found.</p>
            <?php endif; ?>
        </section>

        <section id="support">
            <h2>Contact Support</h2>
            <form id="support-form" method="POST">
                <label for="name">Your Name:</label>
                <input type="text" id="name" name="name" required>

                <label for="email">Your Email:</label>
                <input type="email" id="email" name="email" required>

                <label for="message">Your Message:</label>
                <textarea id="message" name="message" rows="4" required></textarea>

                <button type="submit" class="submit-btn">Send Message</button>
            </form>
        </section>
    </div>

    <script>
        function toggleAnswer(element) {
            const answer = element.nextElementSibling;
            answer.style.display = answer.style.display === "block" ? "none" : "block";
        }
    </script>
</body>
</html>
