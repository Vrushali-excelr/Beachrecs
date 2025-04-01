<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "school";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get sorting parameters
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name';
$order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';

// Fetch student records
$sql = "SELECT * FROM students ORDER BY $sort $order";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['name']}</td>
                <td>{$row['grade']}</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='2'>No records found</td></tr>";
}

$conn->close();
?>
