<?php
// Database connection
$host = 'localhost';
$dbname = 'activities_mapss';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch activity parameters from database
    $stmt = $conn->query("SELECT * FROM activity_parameters");
    $activityParams = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch activities data (you'll need to create this table)
    $stmt = $conn->query("SELECT * FROM activities");
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Convert to JSON for JavaScript
$activities_json = json_encode($activities);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Map</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { overflow-x: hidden; margin: 0; padding: 0; }
        #map { height: 100vh; width: 100%; position: absolute; z-index: 0; }
        .sidebar {
            position: fixed; height: 100vh; width: 300px; background-color: #f9fafb;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1); right: 0; top: 0;
            transition: transform 0.3s ease; z-index: 1000; overflow-y: auto; transform: translateX(100%);
        }
        .sidebar.active { transform: translateX(0); }
        .toggle-btn {
            position: fixed; top: 1rem; right: 1rem; z-index: 1100; background-color: #4a5568;
            color: white; padding: 0.5rem 1rem; border-radius: 0.25rem; cursor: pointer; border: none;
        }
        .activity-btn.active { background-color: #e2e8f0; border-radius: 0.25rem; }
    </style>
</head>
<body class="font-sans">
    <div id="map"></div>
    <div class="sidebar" id="sidebar">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">Legend</h2>

        <div class="mb-6">
            <h3 class="text-lg font-medium mb-2 text-gray-700">Recreational Activities</h3>
            <div class="space-y-2">
                <?php foreach ($activities as $activity): ?>
                    <button class="flex items-center text-sm text-blue-600 hover:underline activity-btn" 
                            data-activity="<?= $activity['id'] ?>"
                            data-suitability="<?= $activity['suitability'] ?>"
                            data-condition="<?= $activity['condition'] ?>">
                        <img src="<?= $activity['icon'] ?>" class="mr-2"> <?= $activity['name'] ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-lg font-medium mb-2 text-gray-700">Activity Conditions</h3>
            <div class="space-y-1">
                <?php foreach ($activityParams as $param): ?>
                    <div class="flex items-center">
                        <span class="inline-block w-3 h-3 rounded-full 
                            <?= $param['condition'] === 'good' ? 'bg-green-500' : 
                               ($param['condition'] === 'moderate' ? 'bg-yellow-500' : 'bg-red-500') ?> 
                            mr-2"></span> 
                        <?= ucfirst($param['condition']) ?> (<?= $param['suitability'] ?>)
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-lg font-medium mb-2 text-gray-700">Beach Areas</h3>
            <div class="space-y-2">
                <button class="flex items-center text-sm text-blue-600 hover:underline area-btn" data-area="main-beach">
                    <i class="fas fa-umbrella-beach mr-2"></i> Main Beach
                </button>
                <button class="flex items-center text-sm text-red-600 hover:underline area-btn" data-area="restricted">
                    <i class="fas fa-ban mr-2"></i> Restricted Area
                </button>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-lg font-medium mb-2 text-gray-700">User Report System</h3>
            <div class="space-y-2">
                <button class="flex items-center text-sm text-gray-700 hover:underline report-btn" data-report="polluted">
                    <i class="fas fa-trash-alt mr-2"></i> Polluted Area
                </button>
                <button class="flex items-center text-sm text-gray-700 hover:underline report-btn" data-report="crowded">
                    <i class="fas fa-users mr-2"></i> Crowded Area
                </button>
                <button class="flex items-center text-sm text-gray-700 hover:underline report-btn" data-report="risky">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Risky Area
                </button>
            </div>
        </div>
    </div>
    <button class="toggle-btn" id="toggleSidebarBtn">Toggle Sidebar</button>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        const map = L.map('map').setView([15.5515, 73.7520], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Toggle sidebar
        document.getElementById('toggleSidebarBtn').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
            document.getElementById('map').style.width = sidebar.classList.contains('active') ? 'calc(100% - 300px)' : '100%';
        });

        // Activities from PHP
        const activities = <?= $activities_json ?>;
        const activityMarkers = {};
        
        Object.entries(activities).forEach(([id, activity]) => {
            activityMarkers[activity.id] = L.marker([activity.latitude, activity.longitude], {
                icon: L.icon({ iconUrl: activity.icon, iconSize: [30, 30] })
            }).bindPopup(`
                <b>${activity.name}</b><br>
                Condition: ${activity.condition.toUpperCase()}<br>
                Suitability: ${activity.suitability}<br>
                Crowded: ${activity.crowded_area}<br>
                Pollution: ${activity.polluted_area}
            `).addTo(map);
        });

        // Activity button handlers
        document.querySelectorAll('.activity-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const activityId = btn.dataset.activity;
                const marker = activityMarkers[activityId];
                
                if (marker._map) {
                    map.setView(marker.getLatLng(), 16);
                } else {
                    Object.values(activityMarkers).forEach(m => map.removeLayer(m));
                    marker.addTo(map);
                    map.setView(marker.getLatLng(), 16);
                }
            });
        });

        // Beach areas
        const beachAreas = {
            "main-beach": L.polygon([...], {color: 'green', fillOpacity: 0.1}),
            "restricted": L.polygon([...], {color: 'red', fillOpacity: 0.1})
        };

        document.querySelectorAll('.area-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const areaId = btn.dataset.area;
                beachAreas[areaId][map.hasLayer(beachAreas[areaId]) ? 'removeFrom' : 'addTo'](map);
                if (!map.hasLayer(beachAreas[areaId])) map.fitBounds(beachAreas[areaId].getBounds());
            });
        });

        // Report system
        document.querySelectorAll('.report-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const reportType = btn.dataset.report;
                alert(`Reporting ${reportType} area - click on map`);
                map.once('click', e => {
                    L.marker([e.latlng.lat, e.latlng.lng], {
                        icon: L.icon({iconUrl: 'https://img.icons8.com/color/48/report.png', iconSize: [30, 30]})
                    }).bindPopup(`<b>${reportType}</b><br>Location: [${e.latlng.lat.toFixed(4)}, ${e.latlng.lng.toFixed(4)}]`).addTo(map);
                    
                    // Here you would typically send data to server
                    fetch('save_report.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({
                            type: reportType,
                            lat: e.latlng.lat,
                            lng: e.latlng.lng
                        })
                    });
                });
            });
        });
    </script>
</body>
</html>