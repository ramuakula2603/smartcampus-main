<?php
/**
 * Test Subjects API After Fix
 * This script tests the Subjects API to verify the MY_Model fix works
 */

echo "=== SUBJECTS API FIX VERIFICATION TEST ===\n\n";

// Test 1: Verify model file was updated
echo "1. MODEL CLASS VERIFICATION:\n";
$model_file = __DIR__ . '/application/models/Subject_model.php';
if (file_exists($model_file)) {
    $content = file_get_contents($model_file);
    if (strpos($content, 'class Subject_model extends MY_Model') !== false) {
        echo "   ✓ Subject_model correctly extends MY_Model\n";
    } else {
        echo "   ✗ Subject_model does NOT extend MY_Model\n";
    }
} else {
    echo "   ✗ Subject_model.php not found\n";
}

// Test 2: Test Subjects API endpoint
echo "\n2. SUBJECTS API ENDPOINT TEST:\n";
echo "   URL: http://localhost/amt/api/subjects/list\n";
echo "   Method: POST\n";

$ch = curl_init('http://localhost/amt/api/subjects/list');
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
} else {
    if ($http_code == 200) {
        echo "   ✓ SUCCESS - API returned 200 OK\n";
        $data = json_decode($response, true);
        if (isset($data['status']) && $data['status'] == 1) {
            echo "   ✓ Response status is 1 (success)\n";
            echo "   ✓ Message: " . $data['message'] . "\n";
            if (isset($data['total_records'])) {
                echo "   ✓ Total records: " . $data['total_records'] . "\n";
            }
        } else {
            echo "   ✗ Response status is not 1\n";
            echo "   Response: " . substr($response, 0, 200) . "\n";
        }
    } else if ($http_code == 403) {
        echo "   ✗ FAILED - Still getting 403 Forbidden\n";
        echo "   Response: " . substr($response, 0, 200) . "\n";
    } else if ($http_code == 401) {
        echo "   ✗ FAILED - Getting 401 Unauthorized (authentication issue)\n";
        echo "   Response: " . substr($response, 0, 200) . "\n";
    } else {
        echo "   ✗ FAILED - HTTP " . $http_code . "\n";
        echo "   Response: " . substr($response, 0, 200) . "\n";
    }
}

// Test 3: Compare with Sections API
echo "\n3. SECTIONS API COMPARISON:\n";
echo "   URL: http://localhost/amt/api/sections/list\n";

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

curl_close($ch);

echo "   HTTP Status Code: " . $http_code . "\n";
if ($http_code == 200) {
    echo "   ✓ Sections API working correctly\n";
} else {
    echo "   ✗ Sections API also failing with HTTP " . $http_code . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>

