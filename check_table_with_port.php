<?php
try {
    // Database configuration with specific port
    $host = 'localhost:3306';
    $username = 'root';
    $password = '';
    $database = 'amt';
    
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected successfully to MySQL server using PDO on port 3306.\n";
    
    // Check table structure
    $sql = "DESCRIBE halltickect_generation";
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($results) > 0) {
        echo "Table 'halltickect_generation' structure:\n";
        echo str_pad("Field", 20) . str_pad("Type", 15) . str_pad("Null", 10) . str_pad("Key", 10) . str_pad("Default", 15) . "Extra\n";
        echo str_repeat("-", 80) . "\n";
        
        foreach ($results as $row) {
            echo str_pad($row['Field'], 20) . 
                 str_pad($row['Type'], 15) . 
                 str_pad($row['Null'], 10) . 
                 str_pad($row['Key'], 10) . 
                 str_pad($row['Default'] ?? 'NULL', 15) . 
                 $row['Extra'] . "\n";
        }
    } else {
        echo "Table 'halltickect_generation' is empty or does not exist.\n";
    }
    
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?>
