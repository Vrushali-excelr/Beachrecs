<?php
$host = 'localhost';
$user = 'root';
$password = '';  // Default XAMPP password is empty
$dbname = 'activity_mapss';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
