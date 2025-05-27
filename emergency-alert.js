let map = L.map('map').setView([-26.2, 28.0], 10);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

let marker;

document.getElementById('share-location-btn').addEventListener('dblclick', function () {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            document.getElementById('coordinates').innerText = `Lat: ${lat}, Lng: ${lng}`;

            if (marker) {
                map.removeLayer(marker);
            }

            marker = L.marker([lat, lng]).addTo(map).bindPopup('Your Location').openPopup();
            map.setView([lat, lng], 14);
        });
    } else {
        alert("Geolocation is not supported by your browser.");
    }
});
