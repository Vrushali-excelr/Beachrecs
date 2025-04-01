<?php
// Include the database connection file
include('config.php');

// Fetch activity parameters from the database
$sql = "SELECT * FROM activity_parameters";
$result = $conn->query($sql);

$activitiesData = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $activitiesData[] = $row;  // Store data from DB for later use
    }
} else {
    $activitiesData = [];
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
        body {
            overflow-x: hidden;
            margin: 0;
            padding: 0;
        }

        #map {
            height: 100vh; /* Set map height to the full viewport */
            width: 100vw; /* Set map width to the full viewport */
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
                <?php
                // Loop through activity data from the database
                foreach ($activitiesData as $parameter) {
                    echo '<button class="flex items-center text-sm text-blue-600 hover:underline activity-btn" data-activity="' . $parameter['id'] . '" data-suit="' . $parameter['suitability'] . '" data-condition="' . $parameter['condition'] . '" data-lat="' . $parameter['latitude'] . '" data-lng="' . $parameter['longitude'] . '">
                            <img src="https://img.icons8.com/color/24/jet-ski.png" class="mr-2"> ' . $parameter['activity_name'] . '
                          </button>';
                }
                ?>
            </div>
        </div>
        <!-- Other content as before -->
    </div>

    <button class="toggle-btn" id="toggleSidebarBtn">Toggle Sidebar</button>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Initialize the map
        const map = L.map('map').setView([15.5515, 73.7520], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Fetch activities dynamically
        const activities = <?php echo json_encode($activitiesData); ?>;
        
        // Create activity markers
        const activityMarkers = {};
        activities.forEach(activity => {
            const { id, activity_name, suitability, condition, latitude, longitude } = activity;
            activityMarkers[id] = L.marker([latitude, longitude], {
                icon: L.icon({ iconUrl: 'https://img.icons8.com/color/48/jet-ski.png', iconSize: [30, 30] })
            }).bindPopup(`
                <b>${activity_name}</b><br>
                Condition: ${condition.toUpperCase()}<br>
                Suitability: ${suitability}
            ).addTo(map);
        });

        Handle activity button clicks
        document.querySelectorAll('.activity-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const activityId = btn.dataset.activity;
                const selectedActivity = activityMarkers[activityId];

                 Show only the selected activity on the map
                Object.values(activityMarkers).forEach(marker => marker.removeFrom(map));
                selectedActivity.addTo(map);

                map.setView(selectedActivity.getLatLng(), 16);
            });
        });

     Toggle sidebar functionality
        document.getElementById('toggleSidebarBtn').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
            
            Adjust map width when sidebar is open/closed
            if (sidebar.classList.contains('active')) {
                document.getElementById('map').style.width = 'calc(100vw - 300px)';
            } else {
                document.getElementById('map').style.width = '100vw';
            }
        });
    </script>
</body>
</html>
