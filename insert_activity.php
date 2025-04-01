<?php
header("Content-Type: text/plain");

// Connect to DB
$conn = new mysqli('localhost', 'root', '', 'activity_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get JSON data
$data = json_decode(file_get_contents("php://input"), true);

$activity_name = $conn->real_escape_string($data['activity_name']);
$condition = $conn->real_escape_string($data['condition']);
$suitability = $conn->real_escape_string($data['suitability']);
$latitude = floatval($data['latitude']);
$longitude = floatval($data['longitude']);

// Insert data
$sql = "INSERT INTO activities 
        (activity_name, `condition`, suitability, latitude, longitude)
        VALUES ('$activity_name', '$condition', '$suitability', $latitude, $longitude)";

if ($conn->query($sql) === TRUE) {
    echo "Activity '$activity_name' added successfully!";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
