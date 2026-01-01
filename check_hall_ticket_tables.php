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

// Tables to check
$tables = array(
    'halltickect_generation',
    'halltickectsubjects',
    'halltickectsubgrp',
    'halltickectsubjectcombo'
);

echo "Checking hall ticket tables:\n\n";

foreach ($tables as $table) {
    $sql = "SHOW TABLES LIKE '$table'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        echo "✓ Table '$table' exists\n";
        
        // Show table structure
        echo "  Structure:\n";
        $structure_sql = "DESCRIBE $table";
        $structure_result = $conn->query($structure_sql);
        
        if ($structure_result && $structure_result->num_rows > 0) {
            while ($row = $structure_result->fetch_assoc()) {
                echo "    " . $row['Field'] . " " . $row['Type'] . 
                     (($row['Null'] == 'NO') ? " NOT NULL" : "") . 
                     (($row['Key'] == 'PRI') ? " PRIMARY KEY" : "") . 
                     (($row['Extra'] == 'auto_increment') ? " AUTO_INCREMENT" : "") . "\n";
            }
        }
    } else {
        echo "✗ Table '$table' does not exist\n";
    }
    echo "\n";
}

$conn->close();
?>
