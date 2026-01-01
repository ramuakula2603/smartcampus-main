<?php
/**
 * Test Student House Database Structure
 */

echo "=== Student House Database Test ===\n\n";

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'amt';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Database connection successful\n\n";
    
    // Check if school_houses table exists
    echo "1. Checking if 'school_houses' table exists:\n";
    echo "============================================\n";
    
    $stmt = $pdo->query("SHOW TABLES LIKE 'school_houses'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Table 'school_houses' exists\n\n";
        
        // Get table structure
        echo "2. Table structure:\n";
        echo "===================\n";
        
        $stmt = $pdo->query("DESCRIBE school_houses");
        $fields = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($fields as $field) {
            echo "- {$field['Field']} ({$field['Type']}) ";
            if ($field['Null'] == 'NO') echo "[NOT NULL] ";
            if ($field['Key'] == 'PRI') echo "[PRIMARY KEY] ";
            if ($field['Default'] !== null) echo "[DEFAULT: {$field['Default']}] ";
            if ($field['Extra']) echo "[{$field['Extra']}]";
            echo "\n";
        }
        
        echo "\n3. Sample data:\n";
        echo "===============\n";
        
        $stmt = $pdo->query("SELECT * FROM school_houses LIMIT 5");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($rows)) {
            echo "❌ No data found in school_houses table\n";
            echo "Creating sample data...\n";
            
            // Insert sample data
            $sample_houses = [
                ['house_name' => 'Red House', 'description' => 'The Red House represents courage and strength', 'is_active' => 'yes'],
                ['house_name' => 'Blue House', 'description' => 'The Blue House represents wisdom and knowledge', 'is_active' => 'yes'],
                ['house_name' => 'Green House', 'description' => 'The Green House represents growth and harmony', 'is_active' => 'yes'],
                ['house_name' => 'Yellow House', 'description' => 'The Yellow House represents energy and creativity', 'is_active' => 'yes']
            ];
            
            foreach ($sample_houses as $house) {
                $stmt = $pdo->prepare("INSERT INTO school_houses (house_name, description, is_active) VALUES (?, ?, ?)");
                $stmt->execute([$house['house_name'], $house['description'], $house['is_active']]);
            }
            
            echo "✅ Sample data created\n";
            
            // Get the data again
            $stmt = $pdo->query("SELECT * FROM school_houses LIMIT 5");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        foreach ($rows as $row) {
            echo "ID: {$row['id']}, Name: {$row['house_name']}, Active: {$row['is_active']}\n";
        }
        
    } else {
        echo "❌ Table 'school_houses' does not exist\n";
        echo "Creating table...\n";
        
        // Create the table
        $create_sql = "
        CREATE TABLE `school_houses` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `house_name` varchar(255) NOT NULL,
            `description` text,
            `is_active` enum('yes','no') DEFAULT 'yes',
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";
        
        $pdo->exec($create_sql);
        echo "✅ Table 'school_houses' created successfully\n";
        
        // Insert sample data
        echo "Creating sample data...\n";
        
        $sample_houses = [
            ['house_name' => 'Red House', 'description' => 'The Red House represents courage and strength', 'is_active' => 'yes'],
            ['house_name' => 'Blue House', 'description' => 'The Blue House represents wisdom and knowledge', 'is_active' => 'yes'],
            ['house_name' => 'Green House', 'description' => 'The Green House represents growth and harmony', 'is_active' => 'yes'],
            ['house_name' => 'Yellow House', 'description' => 'The Yellow House represents energy and creativity', 'is_active' => 'yes']
        ];
        
        foreach ($sample_houses as $house) {
            $stmt = $pdo->prepare("INSERT INTO school_houses (house_name, description, is_active) VALUES (?, ?, ?)");
            $stmt->execute([$house['house_name'], $house['description'], $house['is_active']]);
        }
        
        echo "✅ Sample data created\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Database Test Complete ===\n";
?>
