<?php
/**
 * Test the invalid ID fix
 */

echo "=== Testing Invalid ID Fix ===\n\n";

$base_url = 'http://localhost/amt/api/';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

function makeRequest($url, $data = null, $headers = []) {
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => $data ? json_encode($data) : '{}',
            'timeout' => 30,
            'ignore_errors' => true
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    $http_code = 200;
    
    if (isset($http_response_header)) {
        foreach ($http_response_header as $header) {
            if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $header, $matches)) {
                $http_code = intval($matches[1]);
                break;
            }
        }
    }
    
    return [
        'response' => $response,
        'http_code' => $http_code,
        'success' => $response !== false,
        'json' => $response ? json_decode($response, true) : null
    ];
}

echo "Testing invalid ID formats:\n";
echo "============================\n";

$test_cases = [
    'abc' => 'alphabetic',
    '0' => 'zero',
    '-1' => 'negative',
    '1.5' => 'decimal',
    'null' => 'null string'
];

foreach ($test_cases as $id => $description) {
    echo "Testing ID '$id' ($description):\n";
    
    $result = makeRequest($base_url . 'student-house/get/' . $id, [], $headers);
    
    echo "HTTP Status: " . $result['http_code'] . "\n";
    
    if ($result['json']) {
        echo "Status: " . $result['json']['status'] . "\n";
        echo "Message: " . $result['json']['message'] . "\n";
        
        if ($result['http_code'] == 400 && $result['json']['status'] == 0) {
            echo "✅ Correct error handling\n";
        } else {
            echo "⚠️  Unexpected response\n";
        }
    } else {
        echo "❌ Invalid JSON response\n";
    }
    
    echo "\n";
}

echo "=== Test Complete ===\n";
?>
