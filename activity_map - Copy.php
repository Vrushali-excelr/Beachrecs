<?php
// Database connection
$host = 'localhost';
$dbname = 'activities_mapss';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch activities
    $stmt = $conn->query("SELECT * FROM activitiess");
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch conditions
    $stmt = $conn->query("SELECT * FROM activity_conditions");
    $conditions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch beach areas
    $stmt = $conn->query("SELECT * FROM beach_areas");
    $beach_areas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch report types
    $stmt = $conn->query("SELECT * FROM report_types");
    $report_types = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}
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
        body { margin: 0; padding: 0; overflow-x: hidden; }
        #map { height: 100vh; width: 100vw; position: absolute; z-index: 0; }
        .sidebar {
            position: fixed; right: 0; top: 0; width: 300px; height: 100vh;
            background: #f9fafb; box-shadow: -2px 0 5px rgba(0,0,0,0.1);
            transform: translateX(100%); transition: transform 0.3s ease;
            z-index: 1000; overflow-y: auto; padding: 20px;
        }
        .sidebar.active { transform: translateX(0); }
        .toggle-btn {
            position: fixed; top: 1rem; right: 1rem; z-index: 1100;
            background: #4a5568; color: white; padding: 0.5rem 1rem;
            border-radius: 0.25rem; cursor: pointer; border: none;
        }
        .activity-btn.active { background-color: #e2e8f0; }
    </style>
</head>
<body class="font-sans">
    <div id="map"></div>
    <button class="toggle-btn" id="toggleSidebarBtn">Toggle Sidebar</button>
    <div class="sidebar" id="sidebar">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">Legend</h2>
        
        <div class="mb-6">
            <h3 class="text-lg font-medium mb-2 text-gray-700">Recreational Activities</h3>
            <div class="space-y-2">
                <?php foreach ($activities as $activity): ?>
                <button class="flex items-center text-sm text-blue-600 hover:underline activity-btn" 
                        data-activity="<?= $activity['id'] ?>"
                        data-lat="<?= $activity['latitude'] ?>"
                        data-lng="<?= $activity['longitude'] ?>">
                    <img src="<?= $activity['icon'] ?>" class="mr-2 w-6 h-6"> 
                    <?= $activity['name'] ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-lg font-medium mb-2 text-gray-700">Activity Conditions</h3>
            <div class="space-y-1">
                <?php foreach ($conditions as $condition): ?>
                <div class="flex items-center">
                    <span class="inline-block w-3 h-3 rounded-full mr-2 
                        <?= $condition['color'] === 'green' ? 'bg-green-500' : 
                           ($condition['color'] === 'yellow' ? 'bg-yellow-500' : 'bg-red-500') ?>"></span>
                    <?= $condition['name'] ?> (<?= $condition['suitability'] ?>)
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-lg font-medium mb-2 text-gray-700">Beach Areas</h3>
            <div class="space-y-2">
                <?php foreach ($beach_areas as $area): ?>
                <button class="flex items-center text-sm <?= $area['restricted'] ? 'text-red-600' : 'text-blue-600' ?> hover:underline area-btn" 
                        data-area="<?= $area['id'] ?>">
                    <i class="fas <?= $area['restricted'] ? 'fa-ban' : 'fa-umbrella-beach' ?> mr-2"></i>
                    <?= $area['name'] ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-lg font-medium mb-2 text-gray-700">User Report System</h3>
            <div class="space-y-2">
                <?php foreach ($report_types as $report): ?>
                <button class="flex items-center text-sm text-gray-700 hover:underline report-btn" 
                        data-report="<?= $report['id'] ?>">
                    <i class="fas <?= $report['icon'] ?> mr-2"></i>
                    <?= $report['name'] ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Initialize map
        const map = L.map('map').setView([15.5515, 73.7520], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Toggle sidebar
        document.getElementById('toggleSidebarBtn').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
            document.getElementById('map').style.width = sidebar.classList.contains('active') ? 'calc(100vw - 300px)' : '100vw';
        });

        // Activity markers
        const activityMarkers = {};
        <?php foreach ($activities as $activity): ?>
        activityMarkers[<?= $activity['id'] ?>] = L.marker(
            [<?= $activity['latitude'] ?>, <?= $activity['longitude'] ?>], {
                icon: L.icon({
                    iconUrl: '<?= $activity['icon'] ?>',
                    iconSize: [30, 30]
                })
            }
        ).bindPopup(`
            <b><?= $activity['name'] ?></b><br>
            Condition: <?= $activity['condition'] ?><br>
            Suitability: <?= $activity['suitability'] ?>
        `).addTo(map);
        <?php endforeach; ?>

        // Activity button handlers
        document.querySelectorAll('.activity-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const lat = parseFloat(btn.dataset.lat);
                const lng = parseFloat(btn.dataset.lng);
                map.flyTo([lat, lng], 16);
            });
        });

        // Beach areas (polygons)
        const beachAreas = {};
        <?php foreach ($beach_areas as $area): ?>
        beachAreas[<?= $area['id'] ?>] = L.polygon(
            JSON.parse('<?= $area['coordinates'] ?>'),
            {color: '<?= $area['restricted'] ? 'red' : 'green' ?>', fillOpacity: 0.1}
        ).bindPopup("<b><?= $area['name'] ?></b>");
        <?php endforeach; ?>

        // Beach area toggles
        document.querySelectorAll('.area-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const areaId = btn.dataset.area;
                if (map.hasLayer(beachAreas[areaId])) {
                    map.removeLayer(beachAreas[areaId]);
                } else {
                    beachAreas[areaId].addTo(map);
                    map.fitBounds(beachAreas[areaId].getBounds());
                }
            });
        });

        // Report system
        document.querySelectorAll('.report-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const reportType = btn.dataset.report;
                alert(`Reporting ${reportType} - click on map`);
                
                map.once('click', function(e) {
                    const lat = e.latlng.lat;
                    const lng = e.latlng.lng;
                    
                    // Create marker
                    L.marker([lat, lng], {
                        icon: L.icon({
                            iconUrl: 'https://img.icons8.com/color/48/report.png',
                            iconSize: [30, 30]
                        })
                    }).bindPopup(`
                        <b>Reported: ${reportType}</b><br>
                        Location: [${lat.toFixed(4)}, ${lng.toFixed(4)}]
                    `).addTo(map);
                    
                    // Send to server
                    fetch('save_report.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({
                            type: reportType,
                            lat: lat,
                            lng: lng
                        })
                    });
                });
            });
        });
    </script>
</body>
</html>