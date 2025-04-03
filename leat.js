var bus_map = L.map('bus-map').setView([0,0],30);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(bus_map);

navigator.geolocation.watchPosition(success, error);
let marker, circle, zoomed = false;

function success(pos) {
    const lat = pos.coords.latitude;
    const long = pos.coords.longitude;
    const accuracy = pos.coords.accuracy;
    if (marker) {
        bus_map.removeLayer(marker);
        bus_map.removeLayer(circle);
    }
    
    marker = L.marker([lat, long]).addTo(bus_map);
    circle = L.circle([lat, long], { radius: accuracy }).addTo(bus_map);
    
    if (!zoomed) {
        bus_map.fitBounds(circle.getBounds());
        zoomed = true; 
    }
}

function error(err) {
    if (err.code == 1) {
        alert("Please Allow Maps To Track Your Device");
    } else {
        alert("Unable To Load");
    }
}
