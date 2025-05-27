document.addEventListener("DOMContentLoaded", () => {
    const questions = document.querySelectorAll(".faq-question");

    questions.forEach(button => {
        button.addEventListener("click", () => {
            const answer = button.nextElementSibling;

      
            document.querySelectorAll(".faq-answer").forEach(item => {
                if (item !== answer) item.style.display = "none";
            });

            if (answer.style.display === "block") {
                answer.style.display = "none";
            } else {
                answer.style.display = "block";
            }
        });
    });

 
    document.getElementById("support-form").addEventListener("submit", function (e) {
        e.preventDefault();
        alert("Thank you for reaching out. We’ll get back to you shortly.");
        this.reset();
    });
});
