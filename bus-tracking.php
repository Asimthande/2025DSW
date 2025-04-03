<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Live Tracking</title>

  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <style>
    body {
      background-color: #f0f8ff;
      font-family: 'Arial', sans-serif;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
      color: #333;
      padding: 20px;
    }

    h3 {
      font-size: 30px;
      margin-bottom: 20px;
      color: #2c3e50;
    }

    #coordinates {
      font-size: 18px;
      margin-top: 15px;
      font-weight: bold;
      color: #2980b9;
    }

    #map {
      height: 500px;
      width: 100%;
      max-width: 800px;
      border: 3px solid #2980b9;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      margin-top: 20px;
    }

    .content {
      max-width: 900px;
      text-align: center;
    }

    .user-info {
      font-size: 18px;
      margin-top: 30px;
      color: #333;
      font-weight: normal;
    }

    .user-info p {
      margin: 5px 0;
    }

    .user-info a {
      font-size: 16px;
      color: #007bff;
      text-decoration: none;
    }

    .user-info a:hover {
      text-decoration: underline;
    }

    @media (max-width: 768px) {
      h3 {
        font-size: 24px;
      }

      #coordinates {
        font-size: 16px;
      }

      .user-info {
        font-size: 16px;
      }

      #map {
        height: 300px;
      }
    }
  </style>
</head>
<body>

<div class="content">
  <h3>Driver's Live Tracking</h3>
  <p id="coordinates">Fetching coordinates...</p>
  <div id="map"></div>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
  var map = L.map('map').setView([51.505, -0.09], 13);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  }).addTo(map);

  var marker = L.marker([51.505, -0.09]).addTo(map);

  function updateLocation(lat, lng) {
    marker.setLatLng([lat, lng]);  
    map.setView([lat, lng]);
    document.getElementById('coordinates').innerText = `Latitude: ${lat}, Longitude: ${lng}`;
  }

  if (navigator.geolocation) {
    navigator.geolocation.watchPosition(function(position) {
      var lat = position.coords.latitude;
      var lng = position.coords.longitude;
      updateLocation(lat, lng);
    }, function(error) {
      console.error('Error getting geolocation:', error);
      document.getElementById('coordinates').innerText = 'Unable to retrieve location.';
    }, {
      enableHighAccuracy: true,  
      maximumAge: 0            
    });
  } else {
    console.log("Geolocation is not supported by this browser.");
    document.getElementById('coordinates').innerText = 'Geolocation not supported.';
  }
</script>

</body>
</html>
