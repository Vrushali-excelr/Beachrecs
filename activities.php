<?php
include('config.php');

// Sample query to fetch activity data from the database
$query = "SELECT * FROM activities";
$stmt = $pdo->prepare($query);
$stmt->execute();
$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Output the data in JSON format (this could be used for an AJAX request)
echo json_encode($activities);
?>
