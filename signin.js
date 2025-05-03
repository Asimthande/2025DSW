document.addEventListener("DOMContentLoaded", () => {
    const togglePassword = document.getElementById("togglePassword");
    const passwordInput = document.getElementById("password");

    togglePassword.addEventListener("change", () => {
        passwordInput.type = togglePassword.checked ? "text" : "password";
    });

    document.getElementById("signin-form").addEventListener("submit", function (event) {
        const role = this.querySelector("select[name='role']");
        const email = this.querySelector("input[name='email']");
        const password = this.querySelector("input[name='password']");

        if (!role.value || !email.value || !password.value) {
            event.preventDefault();
            alert("Please fill out all fields!");
        }
    });
});
