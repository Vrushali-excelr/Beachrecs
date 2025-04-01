<?php
$activities = [
    "jet-ski" => [
        "name" => "Jet Ski",
        "position" => [15.5540, 73.7510],
        "icon" => "https://img.icons8.com/color/48/jet-ski.png",
        "condition" => "good",
        "suitability" => "Fetching..."
    ],
    "banana-boat" => [
        "name" => "Banana Boat",
        "position" => [15.5532, 73.7535],
        "icon" => "https://img.icons8.com/color/48/banana-boat.png",
        "condition" => "moderate",
        "suitability" => "Fetching..."
    ],
    "parasailing" => [
        "name" => "Parasailing",
        "position" => [15.5525, 73.7540],
        "icon" => "https://img.icons8.com/color/48/parasailing.png",
        "condition" => "good",
        "suitability" => "Fetching..."
    ],
    "scuba" => [
        "name" => "Scuba Diving",
        "position" => [15.5490, 73.7500],
        "icon" => "https://img.icons8.com/color/48/scuba-diving.png",
        "condition" => "good",
        "suitability" => "Fetching..."
    ],
    "volleyball" => [
        "name" => "Beach Volleyball",
        "position" => [15.5520, 73.7530],
        "icon" => "https://img.icons8.com/color/48/beach-volleyball.png",
        "condition" => "moderate",
        "suitability" => "Fetching..."
    ]
];

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
        body {
            overflow-x: hidden;
            margin: 0;
            padding: 0;
        }

        #map {
            height: 100vh;
            width: 100%;
            position: absolute;
            z-index: 0;
        }

        .sidebar {
            position: fixed;
            height: 100vh;
            width: 300px;
            background-color: #f9fafb;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
            right: 0;
            top: 0;
            transition: transform 0.3s ease;
            z-index: 1000;
            overflow-y: auto;
            transform: translateX(100%);
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .toggle-btn {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1100;
            background-color: #4a5568;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            cursor: pointer;
            border: none;
        }

        .activity-btn.active {
            background-color: #e2e8f0;
            border-radius: 0.25rem;
        }
    </style>
</head>
<body class="font-sans">
    <div id="map"></div>
    <div class="sidebar" id="sidebar">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">Legend</h2>

        <div class="mb-6">
            <h3 class="text-lg font-medium mb-2 text-gray-700">Recreational Activities</h3>
            <div class="space-y-2">
                <?php foreach ($activities as $id => $activity): ?>
                    <button class="flex items-center text-sm text-blue-600 hover:underline activity-btn" data-activity="<?= $id ?>">
                        <img src="<?= $activity['icon'] ?>" class="mr-2"> <?= $activity['name'] ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-lg font-medium mb-2 text-gray-700">Activity Conditions</h3>
            <div class="space-y-1">
                <div class="flex items-center">
                    <span class="inline-block w-3 h-3 rounded-full bg-green-500 mr-2"></span> Safe
                </div>
                <div class="flex items-center">
                    <span class="inline-block w-3 h-3 rounded-full bg-yellow-500 mr-2"></span> Moderate
                </div>
                <div class="flex items-center">
                    <span class="inline-block w-3 h-3 rounded-full bg-red-500 mr-2"></span> Dangerous
                </div>
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

        const toggleButton = document.getElementById('toggleSidebarBtn');
        const sidebar = document.getElementById('sidebar');
        const mapContainer = document.getElementById('map');

        toggleButton.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            if (sidebar.classList.contains('active')) {
                mapContainer.style.width = 'calc(100% - 300px)';
            } else {
                mapContainer.style.width = '100%';
            }
        });

        const activities = <?php echo $activities_json; ?>;
        const activityMarkers = {};
        
        Object.entries(activities).forEach(([id, activity]) => {
            activityMarkers[id] = L.marker(activity.position, {
                icon: L.icon({ iconUrl: activity.icon, iconSize: [30, 30] })
            }).bindPopup(`<b>${activity.name}</b><br>Condition: ${activity.condition.toUpperCase()}<br>Suitability: ${activity.suitability}`).addTo(map);
        });

        let selectedActivity = null;
        document.querySelectorAll('.activity-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const activityId = btn.dataset.activity;
                if (selectedActivity === activityId) {
                    document.querySelectorAll('.activity-btn').forEach(b => b.classList.remove('active'));
                    Object.values(activityMarkers).forEach(marker => marker.addTo(map));
                    selectedActivity = null;
                    return;
                }
                selectedActivity = activityId;
                document.querySelectorAll('.activity-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                Object.values(activityMarkers).forEach(marker => map.removeLayer(marker));
                activityMarkers[activityId].addTo(map);
                map.setView(activityMarkers[activityId].getLatLng(), 16);
            });
        });

        const beachAreas = {
            "main-beach": L.polygon([
                [15.563094850377809, 73.74847293942224],
                [15.56245512437215, 73.74824945421616],
                [15.561776811246645, 73.7479452819127],
                [15.560046629452572, 73.74908825508456],
                [15.558025642055, 73.7501296440715],
                [15.555208584864275, 73.7513297961199],
                [15.552295580866257, 73.75212680281203],
                [15.547517751408776, 73.7535829502209],
                [15.547806321232855, 73.75449024486849],
                [15.550467174220813, 73.75367234108893],
                [15.552331931749492, 73.75301320037079],
                [15.554410016335837, 73.75239498345206],
                [15.557149073978195, 73.75133559863411],
                [15.558761287116397, 73.75075627463522],
                [15.56050937494527, 73.7501572140514],
                [15.56154082846112, 73.74907258025868],
                [15.562434713910122, 73.74861196589879],
                [15.562996152227853, 73.74907693214459],
                [15.563017121611097, 73.7485129928447]
            ], {color: 'green', fillOpacity: 0.1}).bindPopup("<b>Main Beach Area</b> 🏖️"),
            
            "restricted": L.polygon([
                [15.566330107676805, 73.74488658746827],
                [15.565862392316347, 73.74528293328558],
                [15.565127408886482, 73.74601617304714],
                [15.563924703063861, 73.7468385906183],
                [15.564869686802012, 73.74754210444326],
                [15.565709668702624, 73.74854287763264],
                [15.566311017274558, 73.74924639145758],
                [15.567035135920392, 73.75012876116855],
                [15.567665116035045, 73.7509709960296],
                [15.56844782358587, 73.75184295125803],
                [15.569192340217768, 73.75256628237398],
                [15.569526415965413, 73.752922996168],
                [15.570465515663315, 73.75341959005311],
                [15.571936451040841, 73.75233717444979],
                [15.571601302115795, 73.75082952412146],
                [15.570391037622997, 73.74994039702113],
                [15.568901471548386, 73.74930254496996],
                [15.568305642097101, 73.74874200831752],
                [15.56797048712302, 73.74785288112412],
                [15.567579472195021, 73.7471763711618],
                [15.56729622416863, 73.74628821787752],
                [15.566663150378673, 73.74522513112436],
                [15.56632799287621, 73.74483855412299]
            ], {color: 'red', fillOpacity: 0.1}).bindPopup("<b>Restricted Area</b> ⚠️")
        };

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

        document.querySelectorAll('.report-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const reportType = btn.dataset.report;
                alert(`Reporting a ${reportType} area. Click on the map to place a marker.`);
                map.once('click', function(e) {
                    const lat = e.latlng.lat;
                    const lng = e.latlng.lng;
                    L.marker([lat, lng], { 
                        icon: L.icon({ 
                            iconUrl: 'https://img.icons8.com/color/48/report.png', 
                            iconSize: [30, 30] 
                        }) 
                    }).bindPopup(`<b>Reported as: ${reportType}</b><br>Location: [${lat.toFixed(4)}, ${lng.toFixed(4)}]`).addTo(map);
                });
            });
        });
    </script>
</body>
</html>