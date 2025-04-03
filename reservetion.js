document.addEventListener("DOMContentLoaded", function () {
    setTimeout(() => {
        document.querySelector(".splash").style.display = "none";
        document.querySelector(".container").style.display = "block";
    }, 2000);
});

document.getElementById("reservationForm").addEventListener("submit", function(event) {
    event.preventDefault();
    let name = document.getElementById("name").value;
    let phone = document.getElementById("phone").value;
    let pickup = document.getElementById("pickup").value;
    let destination = document.getElementById("destination").value;
    let date = document.getElementById("date").value;
    let time = document.getElementById("time").value;
    
    if (name === "" || phone === "" || date === "" || time === "") {
        alert("Please fill in all fields before confirming your reservation.");
        return;
    }
    
    let confirmationMessage = `Reservation Confirmed!\nName: ${name}\nPhone: ${phone}\nDate: ${date}\nTime: ${time}\nPickup: ${pickup}\nDestination: ${destination}`;
    alert(confirmationMessage);
    
    document.getElementById("reservationForm").reset();
});
