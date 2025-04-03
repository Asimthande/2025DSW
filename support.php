<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQs & Support</title>
    <link rel="stylesheet" href="support.css">
</head>
<body>

<div class="container">
    <h1>FAQs & Support</h1>

    <!-- FAQs Section -->
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

    <!-- Support Section -->
    <section id="support">
        <h2>Contact Support</h2>
        <form id="support-form">
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

<script src="support.js"></script>

</body>
</html>
