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
$sql = "SHOW CREATE TABLE halltickect_generation";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_row();
    echo "Table 'halltickect_generation' structure:\n\n";
    echo $row[1] . ";\n";
} else {
    echo "Table 'halltickect_generation' does not exist.\n";
}

$conn->close();
?>
