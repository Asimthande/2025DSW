function markAsRead(studentNumber) {
    // Send AJAX request to mark notification as read
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `markAsRead.php?student_number=${studentNumber}`, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            const response = xhr.responseText.trim();
            if (response === "Notification marked as read.") {
                // If successful, change the notification color to green
                const notificationElement = document.getElementById(`notification-${studentNumber}`);
                notificationElement.classList.remove('unread');
                notificationElement.classList.add('read');

                // Optionally, change the button text to indicate it's read
                const button = notificationElement.querySelector('button');
                button.textContent = 'Already Read';
                button.disabled = true; // Disable the button after it's read
            }
        }
    };
    xhr.send();
}
