<?php
/**
 * Test Subjects API using cURL
 * This script tests the Subjects API endpoint and compares it with the Sections API
 */

echo "=== SUBJECTS API CURL TEST ===\n\n";

// Test URLs
$subjects_url = 'http://localhost/amt/api/subjects/list';
$sections_url = 'http://localhost/amt/api/sections/list';

// Headers
$headers = array(
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
);

// Request body
$body = json_encode(array());

// Function to test an endpoint
function test_endpoint($url, $headers, $body, $name) {
    echo "Testing: $name\n";
    echo "URL: $url\n";
    echo "---\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    
    // Capture verbose output
    $verbose = fopen('php://temp', 'r+');
    curl_setopt($ch, CURLOPT_STDERR, $verbose);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    echo "HTTP Status Code: $http_code\n";
    
    if ($error) {
        echo "cURL Error: $error\n";
    }
    
    // Split headers and body
    $parts = explode("\r\n\r\n", $response, 2);
    $response_headers = $parts[0];
    $response_body = isset($parts[1]) ? $parts[1] : '';
    
    echo "Response Headers:\n";
    echo $response_headers . "\n\n";
    
    echo "Response Body:\n";
    echo $response_body . "\n";
    echo "\n---\n\n";
    
    return array(
        'status_code' => $http_code,
        'headers' => $response_headers,
        'body' => $response_body
    );
}

// Test Sections API (should work)
echo "1. TESTING SECTIONS API (WORKING REFERENCE)\n";
echo "===========================================\n\n";
$sections_result = test_endpoint($sections_url, $headers, $body, 'Sections API');

// Test Subjects API (currently failing)
echo "2. TESTING SUBJECTS API (CURRENTLY FAILING)\n";
echo "===========================================\n\n";
$subjects_result = test_endpoint($subjects_url, $headers, $body, 'Subjects API');

// Comparison
echo "3. COMPARISON\n";
echo "=============\n\n";
echo "Sections API Status: " . $sections_result['status_code'] . "\n";
echo "Subjects API Status: " . $subjects_result['status_code'] . "\n";

if ($sections_result['status_code'] === $subjects_result['status_code']) {
    echo "✅ Both APIs returned the same status code\n";
} else {
    echo "❌ APIs returned different status codes\n";
    echo "   Difference: " . ($subjects_result['status_code'] - $sections_result['status_code']) . "\n";
}

echo "\n=== END TEST ===\n";
?>

