<?php
include('database.php');

$sql = "SELECT * FROM beach_areas";
$result = $conn->query($sql);

$beachAreas = [];
while ($row = $result->fetch_assoc()) {
    $beachAreas[] = $row;
}

echo json_encode($beachAreas);
?>
