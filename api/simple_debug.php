<?php
/**
 * Simple debug script for student login settings
 */

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'amt';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== Student Login Settings Debug ===\n\n";
    
    // Check if sch_settings table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'sch_settings'");
    if ($stmt->rowCount() == 0) {
        echo "❌ Table 'sch_settings' does not exist!\n";
        exit(1);
    }
    
    echo "✅ Table 'sch_settings' exists\n\n";
    
    // Check table structure for login-related fields
    echo "1. Login-related fields in sch_settings:\n";
    echo "=========================================\n";
    
    $stmt = $pdo->query("DESCRIBE sch_settings");
    $fields = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $login_fields = [];
    foreach ($fields as $field) {
        $field_name = $field['Field'];
        if (strpos(strtolower($field_name), 'login') !== false || 
            strpos(strtolower($field_name), 'panel') !== false) {
            $login_fields[] = $field_name;
            echo "- {$field_name} ({$field['Type']}, Default: {$field['Default']})\n";
        }
    }
    
    if (empty($login_fields)) {
        echo "❌ No login-related fields found!\n";
    }
    
    echo "\n2. Current values in sch_settings:\n";
    echo "===================================\n";
    
    // Get current values
    $login_fields_str = implode(', ', $login_fields);
    $stmt = $pdo->query("SELECT id, $login_fields_str FROM sch_settings LIMIT 5");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($rows)) {
        echo "❌ No records found in sch_settings table!\n";
    } else {
        foreach ($rows as $row) {
            echo "Record ID: {$row['id']}\n";
            foreach ($login_fields as $field) {
                if (isset($row[$field])) {
                    $value = $row[$field];
                    $hex = bin2hex($value);
                    echo "  $field: '$value' (hex: $hex)\n";
                }
            }
            echo "\n";
        }
    }
    
    // Focus on student_panel_login specifically
    if (in_array('student_panel_login', $login_fields)) {
        echo "3. Detailed analysis of student_panel_login:\n";
        echo "=============================================\n";
        
        $stmt = $pdo->query("SELECT student_panel_login FROM sch_settings LIMIT 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $value = $result['student_panel_login'];
            echo "Current value: '$value'\n";
            echo "Length: " . strlen($value) . "\n";
            echo "Hex: " . bin2hex($value) . "\n";
            echo "Equals 'yes': " . ($value == 'yes' ? 'TRUE' : 'FALSE') . "\n";
            echo "Equals 'no': " . ($value == 'no' ? 'TRUE' : 'FALSE') . "\n";
            echo "Equals '1': " . ($value == '1' ? 'TRUE' : 'FALSE') . "\n";
            echo "Equals '0': " . ($value == '0' ? 'TRUE' : 'FALSE') . "\n";
            
            if ($value != 'yes') {
                echo "\n🔧 ISSUE FOUND: student_panel_login is '$value', should be 'yes'\n";
                echo "Fix: UPDATE sch_settings SET student_panel_login = 'yes';\n";
            } else {
                echo "\n✅ Value is correct ('yes')\n";
            }
        }
    } else {
        echo "❌ student_panel_login field not found!\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Debug Complete ===\n";
?>
