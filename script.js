// Leaflet Map Setup
const map = L.map('map').setView([15.5515, 73.7520], 15);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

// Toggle Sidebar
document.getElementById('toggleSidebarBtn').addEventListener('click', function() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('active');
    
    if (sidebar.classList.contains('active')) {
        document.getElementById('map').style.width = 'calc(100vw - 300px)';
    } else {
        document.getElementById('map').style.width = '100vw';
    }
});
