function toggleEdit() {
    const inputs = document.querySelectorAll('#profile-form input');
    const button = document.getElementById('edit-button');
    const fileInput = document.getElementById('profile-pic-upload');
    let studentNumber = document.getElementById("student-number");

    let editable = false;

    // Ensure student number remains readonly at all times
    studentNumber.setAttribute('readonly', 'readonly');

    // Toggle readonly for all other inputs
    inputs.forEach(input => {
        if (input === studentNumber) return; // Skip student number field
        if (input.hasAttribute('readonly')) {
            input.removeAttribute('readonly');
            editable = true;
        } else {
            input.setAttribute('readonly', 'readonly');
        }
    });

    // Change the button text depending on edit mode
    if (editable) {
        button.textContent = 'Save Profile';
    } else {
        button.textContent = 'Edit Profile';
    }

    // Display the file input based on edit mode
    fileInput.style.display = editable ? 'block' : 'none';
    studentNumber.setAttribute('readonly','readonly');
}

document.getElementById('profile-pic').addEventListener('click', function () {
    document.getElementById('profile-pic-upload').click();
});

document.getElementById('profile-pic-upload').addEventListener('change', function (event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('profile-pic').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});
