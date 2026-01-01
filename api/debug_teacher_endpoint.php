<?php
/**
 * Debug the teacher endpoint issue
 */

echo "Testing Teacher Webservice Endpoint\n";
echo "===================================\n\n";

// Test 1: Check if the endpoint is accessible
echo "Test 1: Basic endpoint accessibility\n";
$url = 'http://localhost/amt/api/teacher/attendance-summary';
$data = array('staff_id' => 6);

$options = array(
    'http' => array(
        'header' => "Content-Type: application/json\r\n" .
                   "Client-Service: smartschool\r\n" .
                   "Auth-Key: schoolAdmin@\r\n",
        'method' => 'POST',
        'content' => json_encode($data)
    )
);

$context = stream_context_create($options);
$result = @file_get_contents($url, false, $context);

if ($result !== FALSE) {
    echo "✓ Got response: " . strlen($result) . " bytes\n";
    if (!empty($result)) {
        echo "Response: " . $result . "\n";
    } else {
        echo "Response is empty\n";
    }
} else {
    echo "✗ No response received\n";
    $error = error_get_last();
    if ($error) {
        echo "Error: " . $error['message'] . "\n";
    }
}

// Test 2: Check HTTP response headers
echo "\nTest 2: HTTP Response Headers\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, false);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status Code: $http_code\n";
if ($response) {
    $header_size = strpos($response, "\r\n\r\n");
    $headers = substr($response, 0, $header_size);
    $body = substr($response, $header_size + 4);
    
    echo "Headers:\n$headers\n";
    echo "Body length: " . strlen($body) . " bytes\n";
    if (!empty($body)) {
        echo "Body: $body\n";
    }
}

// Test 3: Check if route is working
echo "\nTest 3: Route configuration check\n";
$simple_test_url = 'http://localhost/amt/api/teacher';
$simple_result = @file_get_contents($simple_test_url);
if ($simple_result !== FALSE) {
    echo "✓ Base teacher endpoint accessible\n";
} else {
    echo "✗ Base teacher endpoint not accessible\n";
}

echo "\n=== Debug completed ===\n";
?>
