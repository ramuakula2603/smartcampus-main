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

// SQL to check table structure
$sql = "DESCRIBE halltickect_generation";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "Table 'halltickect_generation' structure:\n";
    echo str_pad("Field", 20) . str_pad("Type", 15) . str_pad("Null", 10) . str_pad("Key", 10) . str_pad("Default", 15) . "Extra\n";
    echo str_repeat("-", 80) . "\n";
    
    while($row = $result->fetch_assoc()) {
        echo str_pad($row['Field'], 20) . 
             str_pad($row['Type'], 15) . 
             str_pad($row['Null'], 10) . 
             str_pad($row['Key'], 10) . 
             str_pad($row['Default'] ?? 'NULL', 15) . 
             $row['Extra'] . "\n";
    }
} else {
    echo "Table 'halltickect_generation' does not exist or is empty.\n";
}

$conn->close();
?>
