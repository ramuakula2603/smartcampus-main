<?php
/**
 * Direct Test of Subjects API
 * This bypasses the web server and tests the controller directly
 */

// Set up the environment
define('BASEPATH', __DIR__ . '/system/');
define('APPPATH', __DIR__ . '/application/');
define('FCPATH', __DIR__ . '/');
define('SELF', 'index.php');
define('SYSDIR', 'system');
define('ENVIRONMENT', 'development');

// Start output buffering
ob_start();

// Set error reporting
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
ini_set('display_errors', 1);

echo "=== DIRECT SUBJECTS API TEST ===\n\n";

// Check if files exist
echo "1. FILE CHECKS:\n";
echo "   Subjects_api.php: " . (file_exists(__DIR__ . '/application/controllers/Subjects_api.php') ? "EXISTS" : "NOT FOUND") . "\n";
echo "   Subject_model.php: " . (file_exists(__DIR__ . '/application/models/Subject_model.php') ? "EXISTS" : "NOT FOUND") . "\n";
echo "   Sections_api.php: " . (file_exists(__DIR__ . '/application/controllers/Sections_api.php') ? "EXISTS" : "NOT FOUND") . "\n";
echo "   Section_model.php: " . (file_exists(__DIR__ . '/application/models/Section_model.php') ? "EXISTS" : "NOT FOUND") . "\n";

// Try to load CodeIgniter
echo "\n2. CODEIGNITER BOOTSTRAP:\n";
try {
    require_once BASEPATH . 'core/CodeIgniter.php';
    echo "   CodeIgniter loaded successfully\n";
} catch (Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== END TEST ===\n";
?>

