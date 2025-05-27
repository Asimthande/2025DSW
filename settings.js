document.getElementById('account-form').addEventListener('submit', function(event) {
    event.preventDefault();
    alert('Account settings saved successfully!');
});

document.getElementById('notifications-form').addEventListener('submit', function(event) {
    event.preventDefault();
    alert('Notification preferences saved successfully!');
});
