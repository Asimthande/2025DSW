document.addEventListener('DOMContentLoaded', () => {
    const editBtn = document.getElementById('edit-btn');
    const saveBtn = document.getElementById('save-btn');
    const inputs = ['first_name', 'last_name', 'email', 'password'];

    editBtn.addEventListener('click', () => {
        inputs.forEach(id => {
            document.getElementById(id).removeAttribute('readonly');
        });
        editBtn.style.display = 'none';
        saveBtn.style.display = 'inline-block';
    });
});
