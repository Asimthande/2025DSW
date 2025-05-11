// ===================== MAP INITIALIZATION FOR USER ===========================
var map = L.map('map', { zoomControl: false }).setView([-26.20002778, 28.0551667], 17);

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

// ===================== USER LOCATION TRACKING ===========================
let main_lat = 0;
let main_lng = 0;
let marker, circle;

navigator.geolocation.watchPosition(success, error, { enableHighAccuracy: true });

function success(pos) {
    const lat = pos.coords.latitude;
    const lng = pos.coords.longitude;
    const accuracy = pos.coords.accuracy;
    main_lat = lat;
    main_lng = lng;

    if (marker) {
        marker.setLatLng([lat, lng]);
        circle.setLatLng([lat, lng]).setRadius(accuracy);
    } else {
        marker = L.marker([lat, lng], {
            icon: L.icon({
                iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
                iconSize: [30, 30],
                iconAnchor: [15, 30]
            })
        }).addTo(map);

        circle = L.circle([lat, lng], {
            radius: accuracy,
            color: 'hsl(10, 90%, 50%)',
            fillColor: 'hsl(10, 90%, 50%)',
            fillOpacity: 0.2
        }).addTo(map);
    }

    map.setView([lat, lng], 17);
}

function error(err) {
    if (err.code === 1) {
        alert("Please allow location access.");
    } else {
        alert("Unable to retrieve location.");
    }
}

// ===================== WEATHER ===========================
async function getWeather() {
    try {
        const response = await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${main_lat}&longitude=${main_lng}&current_weather=true`);
        const data = await response.json();

        const temperature = data.current_weather.temperature;
        const weatherCode = data.current_weather.weathercode;

        const weatherCodes = {
            0: "Clear",
            1: "Partly Cloudy",
            2: "Cloudy",
            3: "Rain",
            4: "Thunderstorms",
            5: "Snow",
            6: "Fog"
        };

        const weatherText = weatherCodes[weatherCode] || "Unknown Weather";
        const timeStr = new Date().toLocaleTimeString();

        document.getElementById('weather').innerHTML =
            `Current Temperature: ${temperature}°C<br>Weather: ${weatherText}<br>Time: ${timeStr}`;
    } catch (error) {
        console.error('Error fetching weather data:', error);
        document.getElementById('weather').innerHTML = 'Failed to load weather data';
    }
}
setInterval(getWeather, 1000);
getWeather();

// ===================== TRACKING MAP FOR ALL BUSES ===========================
let trackingMap;
let busMarkers = {};

function initTrackingMap() {
    trackingMap = L.map('tracking-map'); // No default view
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(trackingMap);

    fetchAllBusLocations(); // Load once
    setInterval(fetchAllBusLocations, 5000); // Update every 5s
}

function fetchAllBusLocations() {
    fetch('fetch_all_locations.php')
        .then(res => res.json())
        .then(data => {
            const bounds = [];

            data.forEach(bus => {
                const id = bus.BusID;
                const lat = parseFloat(bus.Latitude);
                const lon = parseFloat(bus.Longitude);
                const updateTime = bus.UpdateTime;

                bounds.push([lat, lon]); // For fitBounds

                // Create or update markers
                if (busMarkers[id]) {
                    busMarkers[id].setLatLng([lat, lon]);
                    busMarkers[id].setPopupContent(`<strong>Bus:</strong> ${id}<br><strong>Updated:</strong> ${updateTime}`);
                } else {
                    const marker = L.marker([lat, lon], {
                        icon: L.icon({
                            iconUrl: 'https://cdn-icons-png.flaticon.com/512/61/61168.png',
                            iconSize: [30, 30],
                            iconAnchor: [15, 30]
                        })
                    }).addTo(trackingMap)
                        .bindPopup(`<strong>Bus:</strong> ${id}<br><strong>Updated:</strong> ${updateTime}`);
                    busMarkers[id] = marker;
                }
            });

            // Adjust zoom automatically to fit all markers
            if (bounds.length > 0) {
                trackingMap.fitBounds(bounds, {
                    padding: [50, 50],
                    maxZoom: 17
                });
            }
        })
        .catch(err => console.error("Error fetching live data: ", err));
}

document.addEventListener("DOMContentLoaded", function () {
    initTrackingMap();
});

// ===================== SIDEBAR TOGGLE ===========================
document.addEventListener('DOMContentLoaded', () => {
    const menuBtn = document.getElementById('menu-btn');
    const bellBtn = document.getElementById('bell-btn');
    const sidebar = document.getElementById('sidebar');
    const rightPanel = document.getElementById('right-panel');
    const mainContent = document.querySelector('.main-content');

    menuBtn.addEventListener('click', () => {
        sidebar.classList.toggle('show');
        mainContent.classList.toggle('sidebar-open');
    });

    bellBtn.addEventListener('click', () => {
        rightPanel.classList.toggle('show');
    });

    document.addEventListener('click', (e) => {
        if (!sidebar.contains(e.target) && !menuBtn.contains(e.target)) {
            sidebar.classList.remove('show');
            mainContent.classList.remove('sidebar-open');
        }

        if (!rightPanel.contains(e.target) && !bellBtn.contains(e.target)) {
            rightPanel.classList.remove('show');
        }
    });
});
