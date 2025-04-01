<?php
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'activities_mapss';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $stmt = $conn->prepare("INSERT INTO reports (type, lat, lng) VALUES (?, ?, ?)");
    $stmt->execute([$data['type'], $data['lat'], $data['lng']]);
    
    echo json_encode(['status' => 'success']);
} catch(PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}