<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

$stmt = $conn->query("SELECT * FROM activitiess");
$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($activities);
?>