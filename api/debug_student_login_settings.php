<?php
/**
 * Debug script to investigate student login settings
 * This script will check the database configuration and settings
 */

// Include CodeIgniter bootstrap
define('BASEPATH', '');
require_once('index.php');

echo "=== Student Login Settings Debug ===\n\n";

try {
    $CI =& get_instance();
    
    // Load required models
    $CI->load->model('setting_model');
    
    echo "1. Testing Setting_model->getSetting() method:\n";
    echo "================================================\n";
    
    $settings = $CI->setting_model->getSetting();
    
    if ($settings) {
        echo "✅ getSetting() returned data\n";
        echo "Type: " . gettype($settings) . "\n";
        
        if (isset($settings->student_panel_login)) {
            echo "✅ student_panel_login property exists\n";
            echo "Value: '" . $settings->student_panel_login . "'\n";
            echo "Type: " . gettype($settings->student_panel_login) . "\n";
            echo "Length: " . strlen($settings->student_panel_login) . "\n";
            
            // Check for hidden characters
            $hex_value = bin2hex($settings->student_panel_login);
            echo "Hex representation: " . $hex_value . "\n";
            
            // Test the comparison
            $is_yes = ($settings->student_panel_login == 'yes');
            $is_yes_strict = ($settings->student_panel_login === 'yes');
            echo "Equals 'yes' (==): " . ($is_yes ? 'TRUE' : 'FALSE') . "\n";
            echo "Equals 'yes' (===): " . ($is_yes_strict ? 'TRUE' : 'FALSE') . "\n";
            
        } else {
            echo "❌ student_panel_login property does NOT exist\n";
        }
        
        // Show other relevant properties
        echo "\nOther relevant properties:\n";
        $relevant_props = ['id', 'name', 'student_login', 'parent_login', 'parent_panel_login'];
        foreach ($relevant_props as $prop) {
            if (isset($settings->$prop)) {
                echo "- $prop: '" . $settings->$prop . "'\n";
            }
        }
        
    } else {
        echo "❌ getSetting() returned null or false\n";
    }
    
    echo "\n2. Direct database query to sch_settings:\n";
    echo "==========================================\n";
    
    // Direct database query
    $query = $CI->db->select('id, student_panel_login, parent_panel_login, student_login, parent_login')
                   ->from('sch_settings')
                   ->get();
    
    if ($query->num_rows() > 0) {
        echo "✅ Found " . $query->num_rows() . " record(s) in sch_settings\n";
        
        $rows = $query->result();
        foreach ($rows as $row) {
            echo "\nRecord ID: " . $row->id . "\n";
            echo "student_panel_login: '" . $row->student_panel_login . "'\n";
            echo "parent_panel_login: '" . (isset($row->parent_panel_login) ? $row->parent_panel_login : 'NOT SET') . "'\n";
            echo "student_login: '" . (isset($row->student_login) ? $row->student_login : 'NOT SET') . "'\n";
            echo "parent_login: '" . (isset($row->parent_login) ? $row->parent_login : 'NOT SET') . "'\n";
        }
    } else {
        echo "❌ No records found in sch_settings table\n";
    }
    
    echo "\n3. Check table structure:\n";
    echo "=========================\n";
    
    $fields = $CI->db->list_fields('sch_settings');
    $login_fields = array_filter($fields, function($field) {
        return strpos(strtolower($field), 'login') !== false || strpos(strtolower($field), 'panel') !== false;
    });
    
    echo "Login-related fields in sch_settings:\n";
    foreach ($login_fields as $field) {
        echo "- $field\n";
    }
    
    echo "\n4. Test Auth_model logic:\n";
    echo "=========================\n";
    
    $CI->load->model('auth_model');
    
    // Simulate the exact logic from Auth_model
    $resultdata = $CI->setting_model->getSetting();
    
    if (!$resultdata || !isset($resultdata->student_panel_login)) {
        echo "❌ Invalid settings data - would return 'System configuration error'\n";
    } else {
        echo "Settings data is valid\n";
        echo "student_panel_login value: '" . $resultdata->student_panel_login . "'\n";
        
        if($resultdata->student_panel_login == 'yes'){
            echo "✅ Condition passed - would proceed to checkLogin()\n";
        } else {
            echo "❌ Condition FAILED - would return 'Your account is suspended'\n";
            echo "Expected: 'yes'\n";
            echo "Actual: '" . $resultdata->student_panel_login . "'\n";
        }
    }
    
    echo "\n5. Possible solutions:\n";
    echo "======================\n";
    
    if (isset($resultdata->student_panel_login)) {
        $current_value = $resultdata->student_panel_login;
        
        if ($current_value != 'yes') {
            echo "🔧 SOLUTION: Update student_panel_login from '$current_value' to 'yes'\n";
            echo "SQL: UPDATE sch_settings SET student_panel_login = 'yes';\n";
        } else {
            echo "🤔 Value is already 'yes' - there might be a hidden character issue\n";
            echo "🔧 SOLUTION: Clean the value\n";
            echo "SQL: UPDATE sch_settings SET student_panel_login = 'yes';\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Debug Complete ===\n";
?>
