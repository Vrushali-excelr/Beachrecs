<?php
include('config.php');

// Sample query for inserting a new report
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $report_type = $_POST['report_type'];
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];

    $query = "INSERT INTO reports (report_type, latitude, longitude) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$report_type, $lat, $lng]);
    
    echo "Report successfully submitted!";
}
?>
