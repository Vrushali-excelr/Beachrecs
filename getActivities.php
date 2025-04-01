<?php
include('database.php');

$sql = "SELECT * FROM activities";
$result = $conn->query($sql);

$activities = [];
while ($row = $result->fetch_assoc()) {
    $activities[] = $row;
}

echo json_encode($activities);
?>
