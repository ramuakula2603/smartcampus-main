<?php
/**
 * Debug Subjects API
 * This script tests the Subjects API and captures detailed information
 */

echo "=== SUBJECTS API DEBUG TEST ===\n\n";

// Test 1: Check if controller file exists
echo "1. FILE EXISTENCE CHECK:\n";
$controller_file = __DIR__ . '/application/controllers/Subjects_api.php';
echo "   Subjects_api.php exists: " . (file_exists($controller_file) ? "YES" : "NO") . "\n";
if (file_exists($controller_file)) {
    echo "   File size: " . filesize($controller_file) . " bytes\n";
    echo "   Last modified: " . date('Y-m-d H:i:s', filemtime($controller_file)) . "\n";
}

// Test 2: Check if subject_model exists
echo "\n2. MODEL EXISTENCE CHECK:\n";
$model_file = __DIR__ . '/application/models/Subject_model.php';
echo "   Subject_model.php exists: " . (file_exists($model_file) ? "YES" : "NO") . "\n";

// Test 3: Check routes
echo "\n3. ROUTES CHECK:\n";
$routes_file = __DIR__ . '/application/config/routes.php';
if (file_exists($routes_file)) {
    $routes_content = file_get_contents($routes_file);
    $subjects_routes = substr_count($routes_content, "subjects_api");
    echo "   Routes file exists: YES\n";
    echo "   Subjects routes found: " . $subjects_routes . "\n";
} else {
    echo "   Routes file exists: NO\n";
}

// Test 4: Test API endpoint
echo "\n4. API ENDPOINT TEST:\n";
echo "   Testing: http://localhost/amt/api/subjects/list\n";

$ch = curl_init('http://localhost/amt/api/subjects/list');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
));
curl_setopt($ch, CURLOPT_POSTFIELDS, '{}');
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$error = curl_error($ch);

curl_close($ch);

echo "   HTTP Status Code: " . $http_code . "\n";
if ($error) {
    echo "   cURL Error: " . $error . "\n";
}

// Parse headers and body
$headers = substr($response, 0, $header_size);
$body = substr($response, $header_size);

echo "\n   Response Headers:\n";
foreach (explode("\n", $headers) as $header) {
    if (trim($header)) {
        echo "   " . $header . "\n";
    }
}

echo "\n   Response Body:\n";
echo "   " . $body . "\n";

// Test 5: Compare with Sections API
echo "\n5. SECTIONS API COMPARISON TEST:\n";
echo "   Testing: http://localhost/amt/api/sections/list\n";

$ch = curl_init('http://localhost/amt/api/sections/list');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
));
curl_setopt($ch, CURLOPT_POSTFIELDS, '{}');

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

echo "   HTTP Status Code: " . $http_code . "\n";
if ($error) {
    echo "   cURL Error: " . $error . "\n";
}
echo "   Response (first 200 chars): " . substr($response, 0, 200) . "...\n";

echo "\n=== END DEBUG TEST ===\n";
?>

