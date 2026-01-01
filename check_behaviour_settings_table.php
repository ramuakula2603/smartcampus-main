<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'amt';

// Create connection
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connected successfully to MySQL server.\n";
    
    // Check if database exists
    $sql = "SHOW DATABASES LIKE '$database'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        echo "Database '$database' exists.\n";
        
        // Select the database
        $conn->select_db($database);
        
        // Check for behaviour_settings table
        $table = 'behaviour_settings';
        echo "\n--- Checking table '$table' ---\n";
        $sql = "SHOW TABLES LIKE '$table'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            echo "Table '$table' exists.\n";
            
            // Show table structure
            $sql = "DESCRIBE $table";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                echo "Table structure:\n";
                echo str_pad("Field", 25) . str_pad("Type", 20) . str_pad("Null", 10) . str_pad("Key", 10) . str_pad("Default", 15) . "Extra\n";
                echo str_repeat("-", 90) . "\n";
                
                while($row = $result->fetch_assoc()) {
                    echo str_pad($row['Field'], 25) . 
                         str_pad($row['Type'], 20) . 
                         str_pad($row['Null'], 10) . 
                         str_pad($row['Key'], 10) . 
                         str_pad($row['Default'] ?? 'NULL', 15) . 
                         $row['Extra'] . "\n";
                }
            }
            
            // Show sample data
            echo "\nSample data from '$table':\n";
            $sql = "SELECT * FROM $table LIMIT 5";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    print_r($row);
                }
            } else {
                echo "No data found in table '$table'.\n";
            }
        } else {
            echo "Table '$table' does not exist.\n";
        }
    } else {
        echo "Database '$database' does not exist.\n";
    }
}

$conn->close();
?>
