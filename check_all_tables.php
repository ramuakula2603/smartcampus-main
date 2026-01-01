<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'amt';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL to show all tables
$sql = "SHOW TABLES";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "Tables in database '$database':\n";
    echo str_repeat("-", 50) . "\n";
    
    while($row = $result->fetch_array()) {
        echo $row[0] . "\n";
    }
} else {
    echo "No tables found in database '$database'.\n";
}

$conn->close();
?>
