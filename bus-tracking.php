<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Bus Tracking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    />
    <link rel="icon" type="image/jpeg" href="images/Stabus.jpeg" />

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background-color: hsl(30, 30%, 95%);
        }

        .control-panel {
            padding: 20px;
            text-align: center;
            background: orange;
            color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            font-size: 20px;
        }

        .control-panel label {
            font-weight: bold;
            margin-right: 15px;
        }

        .control-panel a {
            display: inline-block;
            margin-bottom: 10px;
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        select {
            padding: 12px 20px;
            font-size: 18px;
            border-radius: 10px;
            border: none;
            outline: none;
            background-color: white;
            color: hsl(275, 80%, 50%);
            font-weight: bold;
        }

        #map {
            height: 88vh;
            width: 100%;
            z-index: 1;
        }

        .bus-label {
            background: hsl(10, 90%, 50%);
            color: white;
            padding: 2px 6px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
            display: inline-block;
            white-space: nowrap;
            margin-bottom: 4px;
        }
    </style>
</head>
<body>
    <div class="control-panel">
        <div class="back-button">
            <a href="dashboard.php">&larr; Back to Dashboard</a>
        </div>
        <label for="bus-select">Select Bus To Track:</label>
        <select id="bus-select">
            <?php for ($i = 1; $i <= 10; $i++): ?>
                <option value="<?= $i ?>">Bus <?= $i ?></option>
            <?php endfor; ?>
        </select>
    </div>

    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        let map = L.map('map').setView([0, 0], 15);
        let marker = null;
        let selectedBusID = document.getElementById("bus-select").value;
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        function createLabeledMarker(lat, lng, busID) {
            const label = `<div class="bus-label">Bus ${busID}</div>`;

            const labelIcon = L.divIcon({
                className: '',
                html: label,
                iconSize: null
            });

            const labelMarker = L.marker([lat, lng], { icon: labelIcon }).addTo(map);
            const busMarker = L.marker([lat, lng]).addTo(map);

            return [labelMarker, busMarker];
        }

        function fetchLocation(centerMap = false) {
            $.getJSON("bus-location.php?BusID=" + selectedBusID, function (data) {
                if (data && data.Latitude && data.Longitude) {
                    const lat = parseFloat(data.Latitude);
                    const lng = parseFloat(data.Longitude);

                    if (marker) {
                        marker.forEach(m => map.removeLayer(m));
                    }

                    marker = createLabeledMarker(lat, lng, selectedBusID);

                    if (centerMap) {
                        map.setView([lat, lng], 15);
                    }
                }
            });
        }

        fetchLocation(true);
        setInterval(() => fetchLocation(false), 1000);

        document.getElementById("bus-select").addEventListener("change", function () {
            selectedBusID = this.value;
            fetchLocation(true);
        });
    </script>
</body>
</html>
