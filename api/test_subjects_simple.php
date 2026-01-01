<?php
/**
 * Comprehensive Diagnostic Test for Subjects API
 */

$output = "=== SUBJECTS API DIAGNOSTIC TEST ===\n";
$output .= "Time: " . date('Y-m-d H:i:s') . "\n\n";

// Test 1: Check files exist
$output .= "1. FILE CHECK:\n";
$controller = __DIR__ . '/application/controllers/Subjects_api.php';
$model = __DIR__ . '/application/models/Subject_model.php';

$output .= "   Controller exists: " . (file_exists($controller) ? "YES" : "NO") . "\n";
$output .= "   Model exists: " . (file_exists($model) ? "YES" : "NO") . "\n";

// Test 2: Check model base class
$output .= "\n2. MODEL BASE CLASS:\n";
if (file_exists($model)) {
    $content = file_get_contents($model);
    if (strpos($content, 'extends MY_Model') !== false) {
        $output .= "   ✓ Extends MY_Model\n";
    } else {
        $output .= "   ✗ Does NOT extend MY_Model\n";
    }
}

// Test 3: Make HTTP request to Subjects API
$output .= "\n3. SUBJECTS API TEST:\n";
$url = 'http://localhost/amt/api/subjects/list';
$output .= "   URL: $url\n";
$output .= "   Method: POST\n";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
));
curl_setopt($ch, CURLOPT_POSTFIELDS, '{}');
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);

curl_close($ch);

$output .= "   HTTP Code: $http_code\n";
if ($curl_error) {
    $output .= "   cURL Error: $curl_error\n";
}
$output .= "   Response (first 500 chars): " . substr($response, 0, 500) . "\n";

// Test 4: Compare with Sections API
$output .= "\n4. SECTIONS API TEST (for comparison):\n";
$sections_url = 'http://localhost/amt/api/sections/list';
$output .= "   URL: $sections_url\n";

$ch = curl_init($sections_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
));
curl_setopt($ch, CURLOPT_POSTFIELDS, '{}');
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$sections_response = curl_exec($ch);
$sections_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

$output .= "   HTTP Code: $sections_http_code\n";
$output .= "   Response (first 500 chars): " . substr($sections_response, 0, 500) . "\n";

// Write to file
file_put_contents(__DIR__ . '/test_output.txt', $output);

// Also output to screen
echo $output;
?>

