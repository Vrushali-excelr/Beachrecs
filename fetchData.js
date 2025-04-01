// Initialize the map
const map = L.map('map').setView([15.5515, 73.7520], 15); // Use valid coordinates and zoom
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);
// Toggle sidebar functionality
document.getElementById('toggleSidebarBtn').addEventListener('click', function() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('active');
    
    // Adjust map width when sidebar is open/closed
    if (sidebar.classList.contains('active')) {
        document.getElementById('map').style.width = 'calc(100vw - 300px)';
    } else {
        document.getElementById('map').style.width = '100vw';
    }
});

// Fetch activity data from the server
function fetchActivityData() {
    fetch('getActivities.php')
        .then(response => response.json())
        .then(data => {
            // Process data and add markers on the map
            data.forEach(activity => {
                L.marker([activity.position_lat, activity.position_lng], {
                    icon: L.icon({ iconUrl: activity.icon_url, iconSize: [30, 30] })
                }).bindPopup(`<b>${activity.name}</b><br>Condition: ${activity.condition}<br>Suitability: ${activity.suitability}`).addTo(map);
            });
        })
        .catch(error => console.error('Error fetching activity data:', error));
}

// Fetch beach areas data from the server
function fetchBeachAreas() {
    fetch('getBeachAreas.php')
        .then(response => response.json())
        .then(data => {
            data.forEach(area => {
                let coordinates = JSON.parse(area.coordinates); // Assuming coordinates are stored as JSON
                L.polygon(coordinates, { color: area.type === 'main' ? 'green' : 'red', fillOpacity: 0.1 })
                    .bindPopup(`<b>${area.name}</b>`)
                    .addTo(map);
            });
        })
        .catch(error => console.error('Error fetching beach area data:', error));
}

// Run these functions when the page loads
window.onload = function() {
    fetchActivityData();
    fetchBeachAreas();
};
