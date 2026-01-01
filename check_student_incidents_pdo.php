<?php
try {
    // Database configuration
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'amt';
    
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected successfully to MySQL server using PDO.\n";
    
    // Check for tables related to incidents
    $tables_to_check = ['student_incidents', 'student_behaviour', 'student_incident_comments'];
    
    foreach ($tables_to_check as $table) {
        echo "\n--- Checking table '$table' ---\n";
        
        // Check if table exists
        $sql = "SHOW TABLES LIKE '$table'";
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($results) > 0) {
            echo "Table '$table' exists.\n";
            
            // Show table structure
            $sql = "DESCRIBE $table";
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($results) > 0) {
                echo "Table structure:\n";
                echo str_pad("Field", 25) . str_pad("Type", 20) . str_pad("Null", 10) . str_pad("Key", 10) . str_pad("Default", 15) . "Extra\n";
                echo str_repeat("-", 90) . "\n";
                
                foreach ($results as $row) {
                    echo str_pad($row['Field'], 25) . 
                         str_pad($row['Type'], 20) . 
                         str_pad($row['Null'], 10) . 
                         str_pad($row['Key'], 10) . 
                         str_pad($row['Default'] ?? 'NULL', 15) . 
                         $row['Extra'] . "\n";
                }
            }
        } else {
            echo "Table '$table' does not exist.\n";
        }
    }
    
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?>
