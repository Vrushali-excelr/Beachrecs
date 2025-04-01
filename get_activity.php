<?php
include "config.php";

if (isset($_GET['activity'])) {
    $activity = $_GET['activity'];
    $stmt = $conn->prepare("SELECT condition, suitability FROM activities WHERE activity_name = ?");
    $stmt->bind_param("s", $activity);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        echo json_encode(["condition" => "unknown", "suitability" => "No data available"]);
    }
    
    $stmt->close();
}
$conn->close();
?>
