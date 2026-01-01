<?php
/**
 * Web-Based Database Migration Script for Database Updates
 * Access via: http://localhost/amt/web_migration.php
 * 
 * This script migrates all SQL files from the database_updates folder:
 * - biometric_timing_multiple_ranges.sql
 * - time_range_assignments.sql
 * - add_time_range_assignment_menu.sql
 * - biometric_checkin_report_menu.sql
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Security check - only allow from localhost
if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== '::1') {
    die('Access denied. This script can only be run from localhost.');
}

// Define migration files in order of execution
$migration_files = [
    'biometric_timing_multiple_ranges.sql' => 'Biometric Timing Multiple Ranges Setup',
    'time_range_assignments.sql' => 'Time Range Assignment Tables and Permissions',
    'add_time_range_assignment_menu.sql' => 'Time Range Assignment Menu',
    'biometric_checkin_report_menu.sql' => 'Biometric Check-in Report Menu and Permissions'
];

?>
<!DOCTYPE html>
<html>
<head>
    <title>Biometric Timing Migration</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #252526;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        }
        h1 {
            color: #4ec9b0;
            border-bottom: 2px solid #4ec9b0;
            padding-bottom: 10px;
        }
        h2 {
            color: #569cd6;
            margin-top: 30px;
        }
        .success {
            color: #4ec9b0;
            background: rgba(78, 201, 176, 0.1);
            padding: 10px;
            border-left: 4px solid #4ec9b0;
            margin: 10px 0;
        }
        .error {
            color: #f48771;
            background: rgba(244, 135, 113, 0.1);
            padding: 10px;
            border-left: 4px solid #f48771;
            margin: 10px 0;
        }
        .warning {
            color: #dcdcaa;
            background: rgba(220, 220, 170, 0.1);
            padding: 10px;
            border-left: 4px solid #dcdcaa;
            margin: 10px 0;
        }
        .info {
            color: #569cd6;
            background: rgba(86, 156, 214, 0.1);
            padding: 10px;
            border-left: 4px solid #569cd6;
            margin: 10px 0;
        }
        .step {
            margin: 20px 0;
            padding: 15px;
            background: #2d2d30;
            border-radius: 4px;
        }
        .step-title {
            font-weight: bold;
            color: #dcdcaa;
            margin-bottom: 10px;
        }
        pre {
            background: #1e1e1e;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            border: 1px solid #3e3e42;
        }
        .btn {
            background: #0e639c;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 5px 10px 0;
        }
        .btn:hover {
            background: #1177bb;
        }
        .btn-danger {
            background: #c5262d;
        }
        .btn-danger:hover {
            background: #e03e44;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #3e3e42;
        }
        th {
            background: #2d2d30;
            color: #4ec9b0;
        }
        tr:hover {
            background: #2d2d30;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>� Database Updates Migration Tool</h1>
        
        <?php
        // Database configuration
        $hostname = 'localhost';
        $username = 'root';
        $password = '';
        $database = 'amt';
        
        // Check if migration should be executed
        $execute = isset($_GET['execute']) && $_GET['execute'] === 'yes';
        $specific_file = isset($_GET['file']) ? $_GET['file'] : null;
        
        if (!$execute) {
            // Show pre-migration information
            ?>
            <div class="info">
                <strong>ℹ️ Database Updates Migration</strong><br>
                This script will execute all database update files from the database_updates folder.<br>
                <strong>Files to be processed:</strong>
                <ul>
                    <?php foreach ($migration_files as $file => $description) { ?>
                        <li><strong><?php echo htmlspecialchars($file); ?>:</strong> <?php echo htmlspecialchars($description); ?></li>
                    <?php } ?>
                </ul>
            </div>
            
            <div class="step">
                <div class="step-title">📋 What will be done:</div>
                <ul>
                    <li>🕐 <strong>Biometric Timing Setup:</strong> Create new biometric_timing_setup table with multiple time range support</li>
                    <li>👥 <strong>Time Range Assignments:</strong> Create staff and student time range assignment tables</li>
                    <li>📊 <strong>Attendance Columns:</strong> Add is_authorized_range columns to attendance tables</li>
                    <li>🔐 <strong>Permissions:</strong> Set up permissions for time range assignments and biometric reports</li>
                    <li>📱 <strong>Menu Items:</strong> Add menu items for time range assignment and biometric check-in report</li>
                    <li>📈 <strong>Database Views:</strong> Create views for active biometric timings</li>
                </ul>
            </div>
            
            <?php
            // Test database connection
            echo '<div class="step">';
            echo '<div class="step-title">🔌 Testing Database Connection...</div>';
            
            try {
                $mysqli = new mysqli($hostname, $username, $password, $database);
                
                if ($mysqli->connect_errno) {
                    echo '<div class="error">✗ Connection failed: ' . htmlspecialchars($mysqli->connect_error) . '</div>';
                    echo '<div class="warning">⚠️ Please make sure MySQL is running in XAMPP Control Panel.</div>';
                } else {
                    echo '<div class="success">✓ Connected successfully to database: ' . htmlspecialchars($database) . '</div>';
                    
                    // Check migration files
                    echo '<h3>📁 Checking Migration Files:</h3>';
                    $files_ready = true;
                    
                    foreach ($migration_files as $file => $description) {
                        $sql_file = __DIR__ . '/database_updates/' . $file;
                        if (file_exists($sql_file)) {
                            echo '<div class="success">✓ ' . htmlspecialchars($file) . ' (' . number_format(filesize($sql_file)) . ' bytes)</div>';
                        } else {
                            echo '<div class="error">✗ ' . htmlspecialchars($file) . ' - File not found</div>';
                            $files_ready = false;
                        }
                    }
                    
                    // Check existing tables
                    echo '<h3>🔍 Checking Existing Tables:</h3>';
                    $tables_to_check = [
                        'biometric_timing_setup' => 'Biometric timing configuration',
                        'staff_time_range_assignments' => 'Staff time range assignments',
                        'student_time_range_assignments' => 'Student time range assignments',
                        'staff_attendance' => 'Staff attendance records',
                        'student_attendences' => 'Student attendance records'
                    ];
                    
                    foreach ($tables_to_check as $table => $description) {
                        $result = $mysqli->query("SHOW TABLES LIKE '$table'");
                        if ($result && $result->num_rows > 0) {
                            if ($table === 'biometric_timing_setup') {
                                echo '<div class="warning">⚠️ ' . htmlspecialchars($table) . ' exists (will be recreated)</div>';
                            } else {
                                echo '<div class="info">ℹ️ ' . htmlspecialchars($table) . ' exists</div>';
                            }
                        } else {
                            if ($table === 'staff_attendance' || $table === 'student_attendences') {
                                echo '<div class="error">✗ ' . htmlspecialchars($table) . ' missing (required for migration)</div>';
                                $files_ready = false;
                            } else {
                                echo '<div class="info">ℹ️ ' . htmlspecialchars($table) . ' will be created</div>';
                            }
                        }
                    }
                    
                    $mysqli->close();
                    
                    if ($files_ready) {
                        echo '<br><button class="btn" onclick="window.location.href=\'?execute=yes\'">🚀 Execute All Migrations</button>';
                        echo '<div style="margin-top: 15px;">';
                        echo '<h4>Or execute individual files:</h4>';
                        foreach ($migration_files as $file => $description) {
                            echo '<button class="btn" onclick="window.location.href=\'?execute=yes&file=' . urlencode($file) . '\'" style="margin: 5px; font-size: 12px;">📄 ' . htmlspecialchars($file) . '</button><br>';
                        }
                        echo '</div>';
                    } else {
                        echo '<div class="error">❌ Cannot proceed - Some required files or tables are missing</div>';
                    }
                }
            } catch (Exception $e) {
                echo '<div class="error">✗ Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
            
            echo '</div>';
            
        } else {
            // Execute migration
            echo '<h2>🚀 Executing Database Migrations...</h2>';
            
            try {
                $mysqli = new mysqli($hostname, $username, $password, $database);
                
                if ($mysqli->connect_errno) {
                    echo '<div class="error">✗ Connection failed: ' . htmlspecialchars($mysqli->connect_error) . '</div>';
                    exit;
                }
                
                echo '<div class="success">✓ Connected to database</div>';
                
                // Determine which files to execute
                $files_to_execute = [];
                if ($specific_file && isset($migration_files[$specific_file])) {
                    $files_to_execute[$specific_file] = $migration_files[$specific_file];
                    echo '<div class="info">📄 Executing single file: ' . htmlspecialchars($specific_file) . '</div>';
                } else {
                    $files_to_execute = $migration_files;
                    echo '<div class="info">📁 Executing all migration files</div>';
                }
                
                $success_count = 0;
                $error_count = 0;
                
                foreach ($files_to_execute as $file => $description) {
                    echo '<div class="step">';
                    echo '<div class="step-title">📝 Executing: ' . htmlspecialchars($description) . '</div>';
                    echo '<div class="info">File: ' . htmlspecialchars($file) . '</div>';
                    
                    $sql_file = __DIR__ . '/database_updates/' . $file;
                    if (!file_exists($sql_file)) {
                        echo '<div class="error">✗ SQL file not found: ' . htmlspecialchars($sql_file) . '</div>';
                        $error_count++;
                        echo '</div>';
                        continue;
                    }
                    
                    $sql_content = file_get_contents($sql_file);
                    if (empty($sql_content)) {
                        echo '<div class="error">✗ SQL file is empty</div>';
                        $error_count++;
                        echo '</div>';
                        continue;
                    }
                    
                    echo '<div class="success">✓ SQL file loaded (' . number_format(strlen($sql_content)) . ' characters)</div>';
                    
                    // Execute SQL with improved parsing and error handling
                    $statements_executed = 0;
                    $statements_failed = 0;
                    
                    // Clean and parse SQL content
                    $cleaned_sql = cleanSqlContent($sql_content);
                    $statements = parseSqlStatements($cleaned_sql);
                    
                    foreach ($statements as $statement) {
                        $statement = trim($statement);
                        if (empty($statement)) {
                            continue;
                        }
                        
                        if ($mysqli->query($statement)) {
                            $statements_executed++;
                        } else {
                            $statements_failed++;
                            if ($statements_failed <= 3) { // Only show first 3 errors
                                echo '<div class="error">✗ SQL Error: ' . htmlspecialchars($mysqli->error) . '</div>';
                                echo '<div class="warning">Statement: ' . htmlspecialchars(substr($statement, 0, 200)) . '...</div>';
                            }
                        }
                    }
                    
                    if ($statements_failed == 0) {
                        echo '<div class="success">✓ All statements executed successfully (' . $statements_executed . ' statements)</div>';
                        $success_count++;
                    } else {
                        echo '<div class="warning">⚠️ Partial success: ' . $statements_executed . ' succeeded, ' . $statements_failed . ' failed</div>';
                        if ($statements_executed > 0) {
                            $success_count++;
                        } else {
                            $error_count++;
                        }
                    }
                    
                    echo '</div>';
                }
                
                // Summary
                echo '<div class="step">';
                echo '<div class="step-title">📊 Migration Summary</div>';
                echo '<div class="info">Files processed: ' . count($files_to_execute) . '</div>';
                echo '<div class="success">Successful: ' . $success_count . '</div>';
                if ($error_count > 0) {
                    echo '<div class="error">Failed: ' . $error_count . '</div>';
                }
                echo '</div>';
                
                // Verification
                echo '<div class="step">';
                echo '<div class="step-title">✅ Verifying Migration Results...</div>';
                
                // Check created tables
                $tables_to_verify = [
                    'biometric_timing_setup' => 'Biometric timing configuration',
                    'staff_time_range_assignments' => 'Staff time range assignments',
                    'student_time_range_assignments' => 'Student time range assignments'
                ];
                
                foreach ($tables_to_verify as $table => $description) {
                    $result = $mysqli->query("SHOW TABLES LIKE '$table'");
                    if ($result && $result->num_rows > 0) {
                        echo '<div class="success">✓ Table "' . htmlspecialchars($table) . '" exists</div>';
                        
                        // Count records
                        $count_result = $mysqli->query("SELECT COUNT(*) as count FROM `$table`");
                        if ($count_result) {
                            $row = $count_result->fetch_assoc();
                            echo '<div class="info">  → Records: ' . $row['count'] . '</div>';
                        }
                    } else {
                        echo '<div class="warning">⚠️ Table "' . htmlspecialchars($table) . '" not found</div>';
                    }
                }
                
                // Check added columns
                $columns_to_verify = [
                    'staff_attendance' => 'is_authorized_range',
                    'student_attendences' => 'is_authorized_range'
                ];
                
                foreach ($columns_to_verify as $table => $column) {
                    $result = $mysqli->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
                    if ($result && $result->num_rows > 0) {
                        echo '<div class="success">✓ Column "' . htmlspecialchars($column) . '" added to "' . htmlspecialchars($table) . '"</div>';
                    } else {
                        echo '<div class="warning">⚠️ Column "' . htmlspecialchars($column) . '" not found in "' . htmlspecialchars($table) . '"</div>';
                    }
                }
                
                // Check permissions
                $result = $mysqli->query("SELECT COUNT(*) as count FROM permission_category WHERE short_code = 'time_range_assignments'");
                if ($result) {
                    $row = $result->fetch_assoc();
                    if ($row['count'] > 0) {
                        echo '<div class="success">✓ Time range assignment permissions created</div>';
                    } else {
                        echo '<div class="warning">⚠️ Time range assignment permissions not found</div>';
                    }
                }
                
                $result = $mysqli->query("SELECT COUNT(*) as count FROM permission_category WHERE short_code = 'biometric_checkin_report'");
                if ($result) {
                    $row = $result->fetch_assoc();
                    if ($row['count'] > 0) {
                        echo '<div class="success">✓ Biometric check-in report permissions created</div>';
                    } else {
                        echo '<div class="warning">⚠️ Biometric check-in report permissions not found</div>';
                    }
                }
                
                // Check menu items
                $result = $mysqli->query("SELECT COUNT(*) as count FROM sidebar_sub_menus WHERE lang_key = 'time_range_assignments'");
                if ($result) {
                    $row = $result->fetch_assoc();
                    if ($row['count'] > 0) {
                        echo '<div class="success">✓ Time range assignment menu item created</div>';
                    } else {
                        echo '<div class="warning">⚠️ Time range assignment menu item not found</div>';
                    }
                }
                
                echo '</div>';
                
                // Show final status
                if ($error_count == 0) {
                    echo '<div class="success" style="margin-top: 30px;">';
                    echo '<h2>🎉 All Migrations Completed Successfully!</h2>';
                } else {
                    echo '<div class="warning" style="margin-top: 30px;">';
                    echo '<h2>⚠️ Migration Completed with Some Issues</h2>';
                }
                
                echo '<p><strong>Next Steps:</strong></p>';
                echo '<ol>';
                echo '<li>🔧 Access biometric settings: <a href="/amt/schsettings/biometricsetting" style="color: #4ec9b0;">Biometric Settings</a></li>';
                echo '<li>👥 Configure time range assignments: <a href="/amt/admin/time_range_assignment" style="color: #4ec9b0;">Time Range Assignments</a></li>';
                echo '<li>📊 View biometric reports: <a href="/amt/biometric_checkin_report" style="color: #4ec9b0;">Check-in Reports</a></li>';
                echo '<li>🧪 Test the system with sample attendance data</li>';
                echo '<li>📚 Review the documentation for configuration options</li>';
                echo '</ol>';
                
                echo '<div style="margin-top: 20px;">';
                echo '<button class="btn" onclick="window.location.href=\'?\'">&larr; Back to Migration Menu</button>';
                echo '</div>';
                
                echo '</div>';
                
                $mysqli->close();
                
            } catch (Exception $e) {
                echo '<div class="error">✗ Fatal Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        }
        ?>
        
    </div>
    
    <?php
    // Helper function to clean SQL content
    function cleanSqlContent($sql_content) {
        // Remove PHP code blocks
        $sql_content = preg_replace('/\/\*[\s\S]*?\*\//', '', $sql_content); // Remove /* */ comments
        
        // Split into lines for processing
        $lines = explode("\n", $sql_content);
        $cleaned_lines = [];
        $in_php_block = false;
        
        foreach ($lines as $line) {
            $trimmed_line = trim($line);
            
            // Skip empty lines
            if (empty($trimmed_line)) {
                continue;
            }
            
            // Check for PHP block start
            if (strpos($trimmed_line, '<?php') !== false || strpos($trimmed_line, '/*') !== false) {
                $in_php_block = true;
                continue;
            }
            
            // Check for PHP block end
            if (strpos($trimmed_line, '?>') !== false || strpos($trimmed_line, '*/') !== false) {
                $in_php_block = false;
                continue;
            }
            
            // Skip lines that are in PHP blocks
            if ($in_php_block) {
                continue;
            }
            
            // Skip SQL comments
            if (substr($trimmed_line, 0, 2) === '--') {
                continue;
            }
            
            // Skip lines that look like PHP or HTML
            if (strpos($trimmed_line, '<?') !== false || 
                strpos($trimmed_line, '?>') !== false ||
                strpos($trimmed_line, '$lang[') !== false ||
                strpos($trimmed_line, 'echo') !== false ||
                strpos($trimmed_line, '<a href') !== false ||
                strpos($trimmed_line, '<li class') !== false) {
                continue;
            }
            
            $cleaned_lines[] = $line;
        }
        
        return implode("\n", $cleaned_lines);
    }
    
    // Helper function to parse SQL statements
    function parseSqlStatements($sql_content) {
        $statements = [];
        $current_statement = '';
        $lines = explode("\n", $sql_content);
        
        foreach ($lines as $line) {
            $trimmed_line = trim($line);
            
            // Skip empty lines and comments
            if (empty($trimmed_line) || substr($trimmed_line, 0, 2) === '--') {
                continue;
            }
            
            $current_statement .= $line . "\n";
            
            // Check if this line ends a statement
            if (substr($trimmed_line, -1) === ';') {
                $statement = trim($current_statement);
                if (!empty($statement)) {
                    $statements[] = $statement;
                }
                $current_statement = '';
            }
        }
        
        // Add any remaining statement
        $statement = trim($current_statement);
        if (!empty($statement)) {
            $statements[] = $statement;
        }
        
        return $statements;
    }
    ?>
</body>
</html>

