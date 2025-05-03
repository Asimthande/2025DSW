document.addEventListener("DOMContentLoaded", function() {
    const reservedSeats = <?php echo json_encode($reservedSeats); ?>;
    const seatContainer = document.getElementById('seats-layout');
    let seatCount = 1;

    // Create 12 rows for 60 seats
    for (let row = 0; row < 12; row++) { // 12 rows for 60 seats
        let rowHTML = '<div class="row">';
        for (let col = 0; col < 5; col++) {
            // Left seats (seat1, seat2, seat6, seat7, ...)
            const seatLabelLeft = "L" + seatCount;
            rowHTML += createSeatHTML(seatLabelLeft, reservedSeats);
            seatCount++;

            // Aisle in between
            if (col === 2) {
                rowHTML += '<div class="aisle"></div>';
            }

            // Right seats (seat3, seat4, seat8, seat9, ...)
            if (col === 4) {
                const seatLabelRight = "R" + (seatCount - 1);
                rowHTML += createSeatHTML(seatLabelRight, reservedSeats);
            }
        }
        rowHTML += '</div>';
        seatContainer.innerHTML += rowHTML;
    }

    // Handle seat click event and show a notification to the user
    const seats = document.querySelectorAll('.seat');
    seats.forEach(seat => {
        seat.addEventListener('click', function () {
            const seatId = this.id;
            const seatStatus = this.classList.contains('reserved') ? 'reserved' : 'available';

            if (seatStatus === 'available') {
                // Notify user that the seat is available and initiate reservation via AJAX
                notifyUser(`Seat ${seatId} is available. You can reserve it!`);
                reserveSeat(seatId);
            } else {
                // Notify user that the seat is already reserved
                notifyUser(`Seat ${seatId} is already reserved.`);
            }
        });
    });

    // Function to show the notification to the user
    function notifyUser(message) {
        const notification = document.getElementById("seatNotification");
        const messageElement = document.getElementById("seatMessage");
        messageElement.innerText = message;
        notification.style.display = "block";

        setTimeout(() => {
            notification.style.display = "none";
        }, 3000); // Hide after 3 seconds
    }

    // Function to send an AJAX request to reserve the seat
    function reserveSeat(seatId) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "reserve-seat-ajax.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    location.reload(); // Reload the page to reflect the updated seat status
                } else {
                    notifyUser(response.message);
                }
            }
        };
        xhr.send("seat_id=" + seatId); // Send seat_id to the server
    }
});
