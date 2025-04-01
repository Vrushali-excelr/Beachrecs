<?php
$host = 'localhost';
$dbname = 'activities_mapss';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    echo "Connected successfully!";
    
    // Test query
    $stmt = $conn->query("SHOW TABLES");
    echo "<pre>Tables: ";
    print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>