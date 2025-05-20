function loadGoogleAuth() {
    gapi.load('auth2', function() {
        gapi.auth2.init({
            client_id: '72387172929-9dmud5cuo2s1dimnch974rk96as6c583.apps.googleusercontent.com',
        }).then(() => {
            // We don't need to attach the handler here anymore since we handle it on button click
        });
    });
}

// Function to enable Google Sign-In once student number is entered
document.getElementById('studentid').addEventListener('input', function() {
    const studentNumber = document.getElementById('studentid').value;
    const googleButton = document.getElementById('googleAuth');

    // Enable the Google Sign-In button if student number is entered
    if (studentNumber.trim() !== '') {
        googleButton.disabled = false;  // Enable Google Sign-In button
    } else {
        googleButton.disabled = true;   // Disable Google Sign-In button if student number is empty
    }
});

// When the Google Sign-In button is clicked
document.getElementById('googleAuth').addEventListener('click', function() {
    const auth2 = gapi.auth2.getAuthInstance();

    auth2.signIn().then(function(googleUser) {
        // Get user profile information
        const profile = googleUser.getBasicProfile();
        const userData = {
            firstName: profile.getGivenName(),  // Get first name
            lastName: profile.getFamilyName(),  // Get surname (last name)
            email: profile.getEmail(),          // Get email
        };

        // Show the user details in an alert
        alert(`User Signed In:\n\nName: ${userData.firstName} ${userData.lastName}\nEmail: ${userData.email}`);

        // Example: send data to the server (replace with your backend URL)
        // fetch('/your-backend-url', {
        //     method: 'POST',
        //     headers: { 'Content-Type': 'application/json' },
        //     body: JSON.stringify(userData)
        // });

    }).catch(function(error) {
        console.error('Google sign-in error:', error);
    });
});

// Load the Google authentication when the page is ready
window.onload = function() {
    loadGoogleAuth();
};
 