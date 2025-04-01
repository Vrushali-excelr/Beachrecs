<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);

$stmt = $conn->prepare("INSERT INTO reports (type, name, lat, lng, timestamp) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([
    $data['type'],
    $data['name'],
    $data['lat'],
    $data['lng'],
    $data['timestamp'] ?? date('Y-m-d H:i:s')
]);

echo json_encode(['status' => 'success']);
?>