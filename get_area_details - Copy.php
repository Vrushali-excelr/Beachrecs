<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

$areaId = $_GET['id'] ?? null;
if (!$areaId) {
    echo json_encode(['error' => 'Area ID required']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM beach_areas WHERE id = ?");
$stmt->execute([$areaId]);
$area = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$area) {
    echo json_encode(['error' => 'Area not found']);
    exit;
}

// You might want to join with other tables to get more details
echo json_encode($area);
?>