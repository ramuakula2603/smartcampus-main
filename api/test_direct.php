<?php
/**
 * Direct test to check if the controller can be loaded
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set the base path
define('BASEPATH', true);
define('APPPATH', __DIR__ . '/application/');

echo "Testing Fee Group-wise Collection Report API Controller\n";
echo "========================================================\n\n";

// Check if controller file exists
$controller_file = __DIR__ . '/application/controllers/Feegroupwise_collection_report_api.php';
echo "Controller file: $controller_file\n";
echo "File exists: " . (file_exists($controller_file) ? "YES" : "NO") . "\n\n";

// Check if model file exists in main application
$model_file = dirname(__DIR__) . '/application/models/Feegroupwise_model.php';
echo "Model file: $model_file\n";
echo "File exists: " . (file_exists($model_file) ? "YES" : "NO") . "\n\n";

// Try to include the controller
if (file_exists($controller_file)) {
    echo "Attempting to parse controller file...\n";
    $content = file_get_contents($controller_file);
    $result = php_check_syntax($controller_file, $error_message);
    if ($result === false) {
        echo "Syntax Error: $error_message\n";
    } else {
        echo "Controller syntax is valid\n";
    }
}

// Try to include the model
if (file_exists($model_file)) {
    echo "\nAttempting to parse model file...\n";
    $content = file_get_contents($model_file);
    // Check for syntax errors
    $temp_file = tempnam(sys_get_temp_dir(), 'php');
    file_put_contents($temp_file, $content);
    exec("php -l $temp_file 2>&1", $output, $return_var);
    if ($return_var === 0) {
        echo "Model syntax is valid\n";
    } else {
        echo "Model syntax error:\n";
        echo implode("\n", $output) . "\n";
    }
    unlink($temp_file);
}

echo "\n\nDone.\n";
?>

