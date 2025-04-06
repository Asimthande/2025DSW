// verify.js

document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form");

    form.addEventListener("submit", (e) => {
        const codeInput = form.querySelector("input[name='verification_code']");
        const code = codeInput.value.trim();

        if (!/^\d{6}$/.test(code)) {
            alert("Please enter a valid 6-digit verification code.");
            codeInput.focus();
            e.preventDefault();
        }
    });
});
