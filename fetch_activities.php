<?php
header('Content-Type: application/json');

// Connect to DB
$conn = new mysqli('localhost', 'root', '', 'activity_db');
if ($conn->connect_error) {
    die(json_encode([]));
}

// Get last 10 activities (or more if you want)
$result = $conn->query("SELECT * FROM activities ORDER BY report_time DESC LIMIT 10");

$activities = [];
while ($row = $result->fetch_assoc()) {
    $activities[] = $row;
}

echo json_encode($activities);
$conn->close();
?>
