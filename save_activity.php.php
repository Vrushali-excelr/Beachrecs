<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "beach_reports";  // Change if your DB is different

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Connection failed"]);
    exit();
}

// Read raw JSON input
$input = file_get_contents('php://input');
$data = json_decode($input);

if ($data === null) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON data"]);
    exit();
}

// Safely access data properties
$category = $conn->real_escape_string($data->category);
$latitude = floatval($data->latitude);
$longitude = floatval($data->longitude);

$sql = "INSERT INTO reports (report_type, latitude, longitude)
        VALUES ('$category', '$latitude', '$longitude')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["status" => "success", "message" => "Report saved successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
}

$conn->close();
?>
