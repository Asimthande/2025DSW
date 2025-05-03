document.querySelectorAll(".update-btn").forEach(button => {
    button.addEventListener("click", () => {
        const row = button.closest("tr");
        const id = row.getAttribute("data-id");
        const firstName = row.querySelector('[data-field="first_name"]').innerText.trim();
        const lastName = row.querySelector('[data-field="last_name"]').innerText.trim();
        const email = row.querySelector('[data-field="email"]').innerText.trim();
        const state = row.querySelector('.state-select').value;

        fetch('update_user.php', {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                id,
                first_name: firstName,
                last_name: lastName,
                email,
                state
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("User updated successfully!");
            } else {
                alert("Update failed: " + data.error);
            }
        })
        .catch(error => {
            console.error("Fetch error:", error);
            alert("Fetch error: " + error);
        });
    });
});
