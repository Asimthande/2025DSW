<?php
session_start();

$timeout_duration = 10;

if (isset($_SESSION['LAST_ACTIVITY'])) {
    $elapsed_time = time() - $_SESSION['LAST_ACTIVITY'];
    if ($elapsed_time > $timeout_duration * 60) {
        session_unset();
        session_destroy();
        header("Location: signin.php");
        exit();
    }
}

$_SESSION['LAST_ACTIVITY'] = time();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 'guest';
    $_SESSION['role'] = 'guest';
    $_SESSION['first_name'] = 'Guest';
    $_SESSION['last_name'] = 'Guest';
    $_SESSION['email'] = 'guest@example.com';
}

if ($_SESSION['role'] !== 'student' && $_SESSION['role'] !== 'admin') {
    $_SESSION['role'] = 'guest';
    $_SESSION['first_name'] = 'Guest';
    $_SESSION['last_name'] = 'Guest';
    $_SESSION['email'] = 'guest@example.com';
}

$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$email = $_SESSION['email'];
$role = $_SESSION['role'];
?>

<section id="home">
    <h1>Welcome, <?= htmlspecialchars($first_name) . " " . htmlspecialchars($last_name); ?>!</h1>
    <div id="map-display">
        <div id="map"></div>
    </div>
    <p>Next Bus: Route 5 - 10:30 AM (ETA: 8 mins)</p>
</section>

<section id="tracking">
    <h2>Live Bus Tracking</h2>
    <div id="tracking-map" style="height: 500px; width: 100%;"></div>

    <script>
        let trackingMap = L.map('tracking-map').setView([0, 0], 2);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(trackingMap);

        let busMarkers = {};

        // Function to show device's current location
        function locateDevice() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;
                    trackingMap.setView([latitude, longitude], 13);

                    L.marker([latitude, longitude]).addTo(trackingMap)
                        .bindPopup("You are here");
                });
            }
        }

        // Fetch bus locations from the server
        function fetchAllBusLocations() {
            fetch('fetch_all_locations.php')
                .then(response => response.json())
                .then(data => {
                    if (!Array.isArray(data)) return;

                    data.forEach(bus => {
                        const { BusID, Latitude, Longitude } = bus;

                        if (busMarkers[BusID]) {
                            busMarkers[BusID].setLatLng([Latitude, Longitude]);
                        } else {
                            const marker = L.marker([Latitude, Longitude])
                                .addTo(trackingMap)
                                .bindPopup(`Bus ID: ${BusID}`);
                            busMarkers[BusID] = marker;
                        }
                    });

                    if (Object.keys(busMarkers).length > 0) {
                        let bounds = L.featureGroup(Object.values(busMarkers)).getBounds().pad(0.2);
                        trackingMap.fitBounds(bounds);
                    }
                })
                .catch(err => console.error('Error fetching buses:', err));
        }

        locateDevice();
        fetchAllBusLocations();
        setInterval(fetchAllBusLocations, 5000); // Update every 5 seconds
    </script>
</section>
