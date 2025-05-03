document.addEventListener("DOMContentLoaded", () => {
    const showPasswordCheckbox = document.getElementById("show-password");
    const passwordField = document.getElementById("password");
    const confirmPasswordField = document.getElementById("confirm_password");

    showPasswordCheckbox.addEventListener("change", () => {
        const type = showPasswordCheckbox.checked ? "text" : "password";
        passwordField.type = type;
        confirmPasswordField.type = type;
    });

    document.getElementById("signup-form").addEventListener("submit", function (e) {
        const password = passwordField.value;
        const confirm = confirmPasswordField.value;

        if (password !== confirm) {
            e.preventDefault();
            alert("Passwords do not match!");
        }
    });
});
