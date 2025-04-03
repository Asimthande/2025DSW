// support.js

// Toggle FAQ answers
const faqQuestions = document.querySelectorAll('.faq-question');

faqQuestions.forEach(question => {
    question.addEventListener('click', () => {
        const answer = question.nextElementSibling;
        
        // Toggle visibility of the FAQ answer
        if (answer.style.display === "block") {
            answer.style.display = "none";
        } else {
            answer.style.display = "block";
        }
    });
});

// Handle support form submission
document.getElementById('support-form').addEventListener('submit', function(event) {
    event.preventDefault();
    alert('Your message has been sent successfully! We will get back to you soon.');
});
