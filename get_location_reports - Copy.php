<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

$lat = $_GET['lat'] ?? null;
$lng = $_GET['lng'] ?? null;
if (!$lat || !$lng) {
    echo json_encode(['error' => 'Coordinates required']);
    exit;
}

// Search for reports within 0.01 degrees (approx. 1km) of the given coordinates
$stmt = $conn->prepare("
    SELECT r.*, rt.name as type_name 
    FROM reports r
    JOIN report_types rt ON r.type = rt.id
    WHERE ABS(lat - ?) < 0.01 AND ABS(lng - ?) < 0.01
    ORDER BY timestamp DESC
");
$stmt->execute([$lat, $lng]);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($reports);
?>
