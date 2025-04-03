document.addEventListener("DOMContentLoaded", function() {
    const seats = document.querySelectorAll('.seat');
    const messageBox = document.createElement('div');
    messageBox.style.position = 'absolute';
    messageBox.style.top = '20px';
    messageBox.style.left = '50%';
    messageBox.style.transform = 'translateX(-50%)';
    messageBox.style.padding = '10px';
    messageBox.style.backgroundColor = '#fff';
    messageBox.style.borderRadius = '5px';
    messageBox.style.fontSize = '16px';
    messageBox.style.fontWeight = 'bold';
    document.body.appendChild(messageBox);

    function checkIfBusIsFull() {
        const reservedSeats = document.querySelectorAll('.seat.reserved');
        if (reservedSeats.length === seats.length) {
            messageBox.textContent = 'This bus is full!';
            messageBox.style.backgroundColor = '#e74c3c';
            messageBox.style.color = '#fff';
        }
    }

    seats.forEach(seat => {
        seat.addEventListener('click', function() {
            if (seat.classList.contains('reserved')) {
                messageBox.textContent = 'This seat is already reserved!';
                messageBox.style.backgroundColor = '#e74c3c';
                messageBox.style.color = '#fff';
            } else {
                seat.classList.add('reserved');
                seat.classList.remove('available');
                seat.style.backgroundColor = '#c0392b';
                messageBox.textContent = `Thank you for choosing seat ${seat.textContent}`;
                messageBox.style.backgroundColor = '#27ae60';
                messageBox.style.color = '#fff';
                
                checkIfBusIsFull();
            }
        });
    });
});
