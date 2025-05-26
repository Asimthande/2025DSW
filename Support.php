<?php
include 'partial/connect.php';

if (!$conn) {
    die('Error: Failed to connect to the database. Please check the connection settings.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $question = htmlspecialchars(trim($_POST['message']));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo 'Invalid email format.';
        exit;
    }

    if (empty($name) || empty($email) || empty($question)) {
        echo 'Please fill all fields.';
        exit;
    }

    $sql = "INSERT INTO questions (Name, Email, Question) VALUES (?, ?, ?)";
    $faqQuery = "SELECT * FROM questions ORDER BY ID DESC";
    $faqResult = $conn->query($faqQuery);

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sss", $name, $email, $question);

        if ($stmt->execute()) {
            echo "<h1 style=\"color:green;\">Your question has been submitted successfully!</h1>";
        } else {
            echo "<h1 style=\"color:red;\">Could Not Submit Question!</h1>";
        }

        $stmt->close();
    } else {
        echo 'Error preparing the query: ' . $conn->error;  
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQs & Support</title>
    <link rel="stylesheet" href="support.css">
        <link rel="icon" type="image/jpeg" href="images/Stabus.jpeg">
</head>
<body>

<div class="container">
    <h1>FAQs & Support</h1>

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
    <h2>Frequently Asked Questions</h2>


    <?php if ($faqResult && $faqResult->num_rows > 0): ?>
        <?php while ($row = $faqResult->fetch_assoc()): ?>
            <div class="faq">
                <div class="faq-question">
                    <strong><?= htmlspecialchars($row['Name']) ?></strong> 
                    (<em><?= htmlspecialchars($row['Email']) ?></em>) asked:
                    <p><?= htmlspecialchars($row['Question']) ?></p>
                </div>
                <div class="faq-answer">
                    <p></p>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No questions found.</p>
    <?php endif; ?>
</div>

<script src="support.js"></script>
</body>
</html>
