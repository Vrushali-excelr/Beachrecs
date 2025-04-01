<?php
// save_report.php

// Set headers for JSON response and CORS (optional if working locally)
header('Content-Type: application/json');

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

// Check if 'report' is received
if (!isset($data['report'])) {
    echo json_encode(['status' => 'error', 'message' => 'No report data received']);
    exit;
}

// Database connection settings
$host = "localhost";
$dbname = "activity_db";  // <-- your database name
$username = "root";       // default XAMPP username
$password = "";           // default XAMPP password (blank)

// Create a connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

$report = $conn->real_escape_string($data['report']);
$timestamp = date('Y-m-d H:i:s');

// Insert query
$sql = "INSERT INTO reports (report_type, report_time) VALUES ('$report', '$timestamp')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['status' => 'success', 'message' => 'Report saved']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error saving report']);
}

$conn->close();
?>
