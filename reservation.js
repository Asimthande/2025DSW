document.addEventListener("DOMContentLoaded", () => {
  const pickup = document.getElementById("pickup");
  const destination = document.getElementById("destination");
  const confirmation = document.getElementById("confirmation");
  const dateInput = document.getElementById('reservation-date');

  const destinationOptions = {
    APK: ["SWC", "DFC", "APB"],
    SWC: ["APK", "DFC", "APB"],
    DFC: ["APK", "SWC", "APB"],
    APB: ["APK", "SWC", "DFC"]
  };

  pickup.addEventListener("change", function () {
    const selectedPickup = this.value;
    destination.innerHTML = `<option value="" disabled selected>Select Destination</option>`;

    if (destinationOptions[selectedPickup]) {
      destinationOptions[selectedPickup].forEach(loc => {
        const option = document.createElement("option");
        option.value = loc;
        option.textContent = loc;
        destination.appendChild(option);
      });
    }
  });

  const today = new Date();
  const minDate = new Date(today);
  minDate.setDate(today.getDate() + 1);
  const maxDate = new Date(today);
  maxDate.setDate(today.getDate() + 4);

  const formatDate = (date) => {
    const year = date.getFullYear();
    const month = (`0${date.getMonth() + 1}`).slice(-2);
    const day = (`0${date.getDate()}`).slice(-2);
    return `${year}-${month}-${day}`;
  };

  dateInput.min = formatDate(minDate);
  dateInput.max = formatDate(maxDate);

  document.getElementById("reservationForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const pickupLocation = pickup.value;
    const destinationLocation = destination.value;

    if (pickupLocation === destinationLocation) {
      confirmation.textContent = "Departure and destination cannot be the same.";
      confirmation.style.color = "red";
      return;
    }

    confirmation.textContent = "Bus booking simulated successfully!";
    confirmation.style.color = "green";
  });
});
