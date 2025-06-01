document.getElementById('accept-btn').addEventListener('click', () => {
    alert("Thank you for accepting the Terms and Conditions.");
    window.location.href = "signup.php"; 
});

document.getElementById('decline-btn').addEventListener('click', () => {
    alert("You must accept the terms to use the service.");
});
