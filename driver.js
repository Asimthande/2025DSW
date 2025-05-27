let statusDisplay = document.getElementById("status");
let startBtn = document.getElementById("startBtn");
let stopBtn = document.getElementById("stopBtn");

let intervalID = null;
let counter = 0;

const latEl = document.getElementById('lat');
const lngEl = document.getElementById('lng');

let map, marker;

function initMap() {
    map = L.map('map').setView([0, 0], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    marker = L.marker([0, 0]).addTo(map);
}

function sendLocation(position) {
    let latitude = position.coords.latitude;
    let longitude = position.coords.longitude;
    counter++;
    if(latEl) latEl.innerText = latitude.toFixed(6);
    if(lngEl) lngEl.innerText = longitude.toFixed(6);
    if (map && marker) {
        let latlng = [latitude, longitude];
        marker.setLatLng(latlng);
        map.setView(latlng);
    }

    fetch('update-location.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            latitude: latitude,
            longitude: longitude,
            storeHistory: (counter % 60 === 0).toString()
        })
    }).then(() => {
        statusDisplay.innerText = `Location updated: Lat=${latitude.toFixed(6)}, Long=${longitude.toFixed(6)} (Count: ${counter})`;
    }).catch(err => {
        statusDisplay.innerText = `Error sending location: ${err.message}`;
        console.error(err);
    });
}

function errorHandler(err) {
    statusDisplay.innerText = "Error getting location: " + err.message;
}

function startSharing() {
    if (navigator.geolocation) {
        intervalID = setInterval(() => {
            navigator.geolocation.getCurrentPosition(sendLocation, errorHandler);
        }, 5000);

        statusDisplay.innerText = "Sharing started...";
        startBtn.disabled = true;
        stopBtn.disabled = false;
    } else {
        statusDisplay.innerText = "Geolocation is not supported.";
    }
}

function stopSharing() {
    clearInterval(intervalID);
    intervalID = null;
    statusDisplay.innerText = "Sharing stopped.";
    startBtn.disabled = false;
    stopBtn.disabled = true;
}
document.addEventListener('DOMContentLoaded', () => {
    initMap();

    startBtn.addEventListener('click', startSharing);
    stopBtn.addEventListener('click', stopSharing);
});
