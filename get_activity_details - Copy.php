<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

$activityId = $_GET['id'] ?? null;
if (!$activityId) {
    echo json_encode(['error' => 'Activity ID required']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM activitiess WHERE id = ?");
$stmt->execute([$activityId]);
$activity = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$activity) {
    echo json_encode(['error' => 'Activity not found']);
    exit;
}

// You might want to join with other tables to get more details
echo json_encode($activity);
?>