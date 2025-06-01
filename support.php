<?php
include 'partial/connect.php';

$successMessage = '';
$errorMessage = '';
$faqResult = null;

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

$faqQuery = "SELECT * FROM questions ORDER BY ID DESC";
$faqResult = $conn->query($faqQuery);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FAQs & Support</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="support.css">
    <link rel="icon" type="image/jpeg" href="images/Stabus.jpeg">
</head>
<body>
    <img src="images/Chatbot Chat Message.jpg" alt="Bot Pic" class="corner-image" style="border:3px solid orange;border-radius:30px;" onclick="window.location.href='AI.php'">

    <div class="container">
        <div class="back-button" style="background-color: beige; padding: 10px; border-radius: 5px;">
            <a href="dashboard.php" style="color: orange; text-decoration: none; font-weight: bold;">&larr; Back to Dashboard</a>
        </div>

        <h1>FAQs & Support</h1>

        <?php if (!empty($successMessage)): ?>
            <p style="color: green; font-weight: bold;"><?= htmlspecialchars($successMessage) ?></p>
        <?php elseif (!empty($errorMessage)): ?>
            <p style="color: red; font-weight: bold;"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>

        <section id="faqs">
            <h2>Frequently Asked Questions (FAQs)</h2>
            <div class="faq">
                <button class="faq-question">What is the service we provide?</button>
                <div class="faq-answer">
                    <p>We provide bus tracking services to monitor bus status and schedules in real-time.</p>
                </div>
            </div>
            <div class="faq">
                <button class="faq-question">How do I track my bus?</button>
                <div class="faq-answer">
                    <p>Log in to your account and go to the "Track Bus" section to view the bus location on the map.</p>
                </div>
            </div>
            <div class="faq">
                <button class="faq-question">What do I do if my bus is delayed?</button>
                <div class="faq-answer">
                    <p>If your bus is delayed, we will notify you via SMS or email with updated timings.</p>
                </div>
            </div>
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

    <div class="faq-container">
        <h2>Submitted Questions</h2>
        <?php if ($faqResult && $faqResult->num_rows > 0): ?>
            <?php while ($row = $faqResult->fetch_assoc()): ?>
                <div class="faq">
                    <div class="faq-question">
                        <strong><?= htmlspecialchars($row['Name']) ?></strong> 
                        (<em><?= htmlspecialchars($row['Email']) ?></em>) asked:
                        <p><?= htmlspecialchars($row['Question']) ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No questions found.</p>
        <?php endif; ?>
    </div>

</body>
</html>
