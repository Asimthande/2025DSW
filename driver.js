let departure_locations = ["APB", "APK", "DFC", "SWC"];
let selected_departure = "";
function Destinations() {
    selected_departure = document.getElementById("select-departure").value;
    let select_destination = document.getElementById("select-destination");
    select_destination.innerHTML = "";

    departure_locations.forEach(function (element) {
        if (element !== selected_departure) {
            let destination = document.createElement('option');
            destination.textContent = element;
            select_destination.appendChild(destination);
        }
    });
}
