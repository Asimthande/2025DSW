document.addEventListener("DOMContentLoaded", function() {
    const reservedSeats = <?php echo json_encode($reservedSeats); ?>;
    const seatContainer = document.getElementById('seats-layout');
    let seatCount = 1;
    for (let row = 0; row < 12; row++) { 
        let rowHTML = '<div class="row">';
        for (let col = 0; col < 5; col++) {
            const seatLabelLeft = "L" + seatCount;
            rowHTML += createSeatHTML(seatLabelLeft, reservedSeats);
            seatCount++;
            if (col === 2) {
                rowHTML += '<div class="aisle"></div>';
            }
            if (col === 4) {
                const seatLabelRight = "R" + (seatCount - 1);
                rowHTML += createSeatHTML(seatLabelRight, reservedSeats);
            }
        }
        rowHTML += '</div>';
        seatContainer.innerHTML += rowHTML;
    }
    const seats = document.querySelectorAll('.seat');
    seats.forEach(seat => {
        seat.addEventListener('click', function () {
            const seatId = this.id;
            const seatStatus = this.classList.contains('reserved') ? 'reserved' : 'available';

            if (seatStatus === 'available') {
                notifyUser(`Seat ${seatId} is available. You can reserve it!`);
                reserveSeat(seatId);
            } else {
                notifyUser(`Seat ${seatId} is already reserved.`);
            }
        });
    });
    function notifyUser(message) {
        const notification = document.getElementById("seatNotification");
        const messageElement = document.getElementById("seatMessage");
        messageElement.innerText = message;
        notification.style.display = "block";

        setTimeout(() => {
            notification.style.display = "none";
        }, 3000);
    }
    function reserveSeat(seatId) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "reserve-seat-ajax.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    location.reload();
                } else {
                    notifyUser(response.message);
                }
            }
        };
        xhr.send("seat_id=" + seatId);
    }
});
