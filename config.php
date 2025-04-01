<?php
$servername = "localhost";  // Your server name, usually "localhost"
$username = "root";         // MySQL username (default for XAMPP is root)
$password = "";             // MySQL password (default for XAMPP is empty)
$dbname = "activities_mapss"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
