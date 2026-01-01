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

// SQL to check if the column exists
$sql = "SHOW COLUMNS FROM halltickect_generation LIKE 'is_active'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "Column 'is_active' exists in 'halltickect_generation' table.\n";
    
    // Show the column details
    $row = $result->fetch_assoc();
    echo "Column details:\n";
    echo "Field: " . $row['Field'] . "\n";
    echo "Type: " . $row['Type'] . "\n";
    echo "Null: " . $row['Null'] . "\n";
    echo "Key: " . $row['Key'] . "\n";
    echo "Default: " . $row['Default'] . "\n";
    echo "Extra: " . $row['Extra'] . "\n";
} else {
    echo "Column 'is_active' does not exist in 'halltickect_generation' table.\n";
}

$conn->close();
?>
