function markAsRead(studentNumber) {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `markAsRead.php?student_number=${studentNumber}`, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            const response = xhr.responseText.trim();
            if (response === "Notification marked as read.") {
                const notificationElement = document.getElementById(`notification-${studentNumber}`);
                notificationElement.classList.remove('unread');
                notificationElement.classList.add('read');

                
                const button = notificationElement.querySelector('button');
                button.textContent = 'Already Read';
                button.disabled = true;
            }
        }
    };
    xhr.send();
}
