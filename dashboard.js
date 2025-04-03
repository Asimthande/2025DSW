var map = L.map('map').setView([-26.20002778, 28.0551667], 1);

let main_lat = 0;
let main_lng = 0;
document.getElementById('menu-btn').addEventListener('click', function () {
    const sidebar = document.getElementById('sidebar');
    if (sidebar.style.left === "0px") {
        sidebar.style.left = "-250px";
    } else {
        sidebar.style.left = "0px";
    }
});

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

navigator.geolocation.watchPosition(success, error);
let marker, circle, zoomed = false;

function success(pos) {
    const lat = pos.coords.latitude;
    const long = pos.coords.longitude;
    main_lat = lat;
    main_lng = long;
    const accuracy = pos.coords.accuracy;

    if (marker) {
        map.removeLayer(marker);
        map.removeLayer(circle);
    }

    marker = L.marker([lat, long]).addTo(map);
    circle = L.circle([lat, long], { radius: accuracy }).addTo(map);

    if (!zoomed) {
        map.fitBounds(circle.getBounds());
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

document.getElementById('bell-btn').addEventListener('click', function () {
    const rightPanel = document.getElementById('right-panel');
    rightPanel.style.right = rightPanel.style.right === "0px" ? "-350px" : "0px";
});

document.getElementById('right-panel').addEventListener('click', function () {
    const rightPanel = document.getElementById('right-panel');
    rightPanel.style.right = "-350px"; // Close the panel when clicked inside
});

async function getWeather() {
    try {
        const response = await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${main_lat}&longitude=${main_lng}&current_weather=true`);
        const data = await response.json();

        const temperature = data.current_weather.temperature;
        const weatherDescription = data.current_weather.weathercode;
        const weatherCodes = {
            0: "Clear",
            1: "Partly Cloudy",
            2: "Cloudy",
            3: "Rain",
            4: "Thunderstorms",
            5: "Snow",
            6: "Fog"
        };

        const weatherText = weatherCodes[weatherDescription] || "Unknown Weather";
        const currentTime = new Date();
        const hours = currentTime.getHours().toString().padStart(2, '0');
        const minutes = currentTime.getMinutes().toString().padStart(2, '0');
        const seconds = currentTime.getSeconds().toString().padStart(2, '0');
        const currentTimeString = `${hours}:${minutes}:${seconds}`;

        const weatherElement = document.getElementById('weather');
        weatherElement.innerHTML = `Current Temperature: ${temperature}°C, Weather: ${weatherText}, Time: ${currentTimeString}`;
    } catch (error) {
        console.error('Error fetching weather data:', error);
        document.getElementById('weather').innerHTML = 'Failed to load weather data';
    }
}

getWeather();
