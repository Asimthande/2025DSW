// termsandconditions.js

// Handle Accept/Decline buttons
document.getElementById('accept-btn').addEventListener('click', function() {
    alert('You have accepted the Terms and Conditions.');
    // Redirect or proceed with registration/login, etc.
    window.location.href = "dashboard.php";  // Example redirect
});

document.getElementById('decline-btn').addEventListener('click', function() {
    alert('You must accept the Terms and Conditions to proceed.');
    // Redirect user to a different page or close the window
    window.location.href = '/';  // Example redirect
});
