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

// SQL to add the missing column
$sql = "ALTER TABLE halltickect_generation ADD COLUMN is_active VARCHAR(10) NOT NULL DEFAULT 'yes' AFTER examheading";

if ($conn->query($sql) === TRUE) {
    echo "Column 'is_active' added successfully to 'halltickect_generation' table.";
} else {
    echo "Error adding column: " . $conn->error;
}

$conn->close();
?>
