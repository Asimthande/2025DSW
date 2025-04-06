document.getElementById('accept-btn').addEventListener('click', () => {
    alert("Thank you for accepting the Terms and Conditions.");
    // Optionally redirect or store agreement in session/localStorage
    window.location.href = "signup.php"; // or wherever appropriate
});

document.getElementById('decline-btn').addEventListener('click', () => {
    alert("You must accept the terms to use the service.");
    // Optionally disable functionality or redirect
});
