<?php
// Include database configuration
include 'db_config.php';

// Check if data is coming via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get POST values and sanitize
    $activity_name = $conn->real_escape_string($_POST['activity_name']);
    $condition = $conn->real_escape_string($_POST['condition']);
    $suitability = $conn->real_escape_string($_POST['suitability']);
    $latitude = floatval($_POST['latitude']);
    $longitude = floatval($_POST['longitude']);

    // Insert into database
    $sql = "INSERT INTO activities (activity_name, condition, suitability, latitude, longitude)
            VALUES ('$activity_name', '$condition', '$suitability', '$latitude', '$longitude')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Activity saved successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
    }

    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
