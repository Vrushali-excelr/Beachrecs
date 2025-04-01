<?php
header('Content-Type: application/json');
include 'db_config.php';

if (isset($_GET['activity'])) {
    $activity = $conn->real_escape_string($_GET['activity']);

    $sql = "SELECT condition_status, suitability FROM activities WHERE activity_id = '$activity'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            'condition' => $row['condition_status'],
            'suitability' => $row['suitability']
        ]);
    } else {
        echo json_encode([
            'condition' => 'unknown',
            'suitability' => 'Data not available'
        ]);
    }
} else {
    echo json_encode([
        'error' => 'No activity specified'
    ]);
}

$conn->close();
?>
