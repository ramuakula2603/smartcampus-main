<?php
/**
 * Minimal test - just check if we can access the API at all
 */

// This file is in the api directory, so we can test directly
echo "=== MINIMAL API TEST ===\n\n";

// Test 1: Check if we're in the API directory
echo "1. LOCATION CHECK:\n";
echo "   Current file: " . __FILE__ . "\n";
echo "   Directory: " . __DIR__ . "\n";

// Test 2: Check if index.php exists
echo "\n2. INDEX.PHP CHECK:\n";
$index_file = __DIR__ . '/index.php';
echo "   Index.php exists: " . (file_exists($index_file) ? "YES" : "NO") . "\n";

// Test 3: Check if Subjects_api.php exists
echo "\n3. SUBJECTS_API.PHP CHECK:\n";
$controller_file = __DIR__ . '/application/controllers/Subjects_api.php';
echo "   Controller exists: " . (file_exists($controller_file) ? "YES" : "NO") . "\n";

// Test 4: Try to include and instantiate the controller directly
echo "\n4. DIRECT CONTROLLER TEST:\n";
try {
    // Load CodeIgniter
    require_once(__DIR__ . '/index.php');
    echo "   CodeIgniter loaded successfully\n";
} catch (Exception $e) {
    echo "   Error loading CodeIgniter: " . $e->getMessage() . "\n";
}

echo "\n=== END TEST ===\n";
?>

