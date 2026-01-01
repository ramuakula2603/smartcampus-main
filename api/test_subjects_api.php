<?php
/**
 * Test Subjects API
 * 
 * This script tests the Subjects API endpoints
 */

echo "=== Subjects API Test ===\n\n";

// Test 1: List Subjects
echo "Test 1: List Subjects\n";
echo "URL: http://localhost/amt/api/subjects/list\n";

$ch = curl_init('http://localhost/amt/api/subjects/list');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
));
curl_setopt($ch, CURLOPT_POSTFIELDS, '{}');
curl_setopt($ch, CURLOPT_VERBOSE, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

echo "HTTP Status Code: " . $http_code . "\n";
if ($error) {
    echo "cURL Error: " . $error . "\n";
}
echo "Response:\n";
echo $response . "\n\n";

// Test 2: Compare with Sections API
echo "\n=== Sections API Test (for comparison) ===\n";
echo "URL: http://localhost/amt/api/sections/list\n";

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

echo "HTTP Status Code: " . $http_code . "\n";
if ($error) {
    echo "cURL Error: " . $error . "\n";
}
echo "Response:\n";
echo substr($response, 0, 500) . "...\n\n";

// Test 3: Check if controller file exists
echo "\n=== File Existence Check ===\n";
$controller_path = __DIR__ . '/application/controllers/Subjects_api.php';
echo "Subjects_api.php exists: " . (file_exists($controller_path) ? "YES" : "NO") . "\n";

$sections_controller_path = __DIR__ . '/application/controllers/Sections_api.php';
echo "Sections_api.php exists: " . (file_exists($sections_controller_path) ? "YES" : "NO") . "\n";

// Test 4: Check routes file
echo "\n=== Routes Configuration Check ===\n";
$routes_path = __DIR__ . '/application/config/routes.php';
if (file_exists($routes_path)) {
    $routes_content = file_get_contents($routes_path);
    if (strpos($routes_content, "subjects_api") !== false) {
        echo "Subjects routes found in routes.php: YES\n";
    } else {
        echo "Subjects routes found in routes.php: NO\n";
    }
    
    if (strpos($routes_content, "sections_api") !== false) {
        echo "Sections routes found in routes.php: YES\n";
    } else {
        echo "Sections routes found in routes.php: NO\n";
    }
} else {
    echo "routes.php not found!\n";
}

echo "\n=== Test Complete ===\n";
?>

