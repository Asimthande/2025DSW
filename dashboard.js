// Sidebar toggle logic
const sidebar = document.getElementById('sidebar');
const menuToggle = document.getElementById('menu-toggle');
const mainContent = document.getElementById('main-content');
let isSidebarVisible = false;

// Set initial hamburger color
menuToggle.style.color = 'orange';

menuToggle.addEventListener('click', () => {
  if (isSidebarVisible) {
    // Hide sidebar
    sidebar.style.left = '-250px';
    mainContent.classList.remove('shifted');
    menuToggle.style.color = 'orange'; // revert color to orange
  } else {
    // Show sidebar
    sidebar.style.left = '0';
    mainContent.classList.add('shifted');
    menuToggle.style.color = 'black'; // set color to black
  }
  isSidebarVisible = !isSidebarVisible;
});

// ====================== USER LOCATION MAP ========================
var map = L.map('map', { zoomControl: false }).setView([-26.20002778, 28.0551667], 17);

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 19,
  attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

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

// ================= WEATHER FETCH =========================
async function getWeather() {
  if (main_lat === 0 && main_lng === 0) return; // skip if no location yet

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
setInterval(getWeather, 5000);
getWeather();

// ================ BUS TRACKING MAP ======================
let trackingMap;
let busMarkers = {};

function initTrackingMap() {
  trackingMap = L.map('tracking-map');
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(trackingMap);

  fetchAllBusLocations();
  setInterval(fetchAllBusLocations, 5000);
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

        bounds.push([lat, lon]);
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

      if (bounds.length > 0) {
        trackingMap.fitBounds(bounds, {
          padding: [50, 50],
          maxZoom: 17
        });
      }
    })
    .catch(err => console.error("Error fetching live data: ", err));
}

// Init after DOM loads
document.addEventListener('DOMContentLoaded', () => {
  initTrackingMap();
});
