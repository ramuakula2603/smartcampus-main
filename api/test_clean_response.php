<?php
/**
 * Test for clean JSON response without PHP warnings
 */

$url = 'http://localhost/amt/api/auth/login';
$data = json_encode([
    'username' => 'std1457',
    'password' => '1ilhr5',
    'deviceToken' => 'optional_device_token'
]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => [
            'Content-Type: application/json',
            'Client-Service: smartschool',
            'Auth-Key: schoolAdmin@'
        ],
        'content' => $data,
        'timeout' => 30,
        'ignore_errors' => true
    ]
]);

echo "Testing for clean JSON response...\n\n";

$result = @file_get_contents($url, false, $context);

if ($result === false) {
    echo "❌ Request failed\n";
    exit(1);
}

echo "Response received (" . strlen($result) . " characters)\n\n";

// Check if response starts with JSON
if (substr(trim($result), 0, 1) === '{') {
    echo "✅ CLEAN JSON RESPONSE!\n";
    echo "No PHP errors or warnings detected.\n\n";
    
    $response = json_decode($result, true);
    if ($response) {
        echo "📊 Response Analysis:\n";
        echo "Status: " . $response['status'] . "\n";
        echo "Message: " . $response['message'] . "\n";
        
        if ($response['status'] == 1) {
            echo "✅ LOGIN SUCCESSFUL!\n";
            echo "User ID: " . $response['id'] . "\n";
            echo "Token: " . $response['token'] . "\n";
            echo "Role: " . $response['role'] . "\n";
            
            if (isset($response['record'])) {
                echo "\n👤 User Details:\n";
                echo "Name: " . $response['record']['username'] . "\n";
                echo "Admission No: " . $response['record']['admission_no'] . "\n";
                echo "Class: " . $response['record']['class'] . "\n";
                echo "Section: " . $response['record']['section'] . "\n";
                echo "Email: " . $response['record']['email'] . "\n";
                echo "Mobile: " . $response['record']['mobileno'] . "\n";
            }
            
            echo "\n🎉 PERFECT! Student API authentication is working flawlessly!\n";
        } else {
            echo "❌ Login failed: " . $response['message'] . "\n";
        }
    }
    
    echo "\n📄 Full JSON Response:\n";
    echo json_encode(json_decode($result), JSON_PRETTY_PRINT) . "\n";
    
} else {
    echo "⚠️  Response contains PHP errors/warnings\n";
    
    // Try to extract JSON part
    $json_start = strpos($result, '{');
    if ($json_start !== false) {
        $json_part = substr($result, $json_start);
        echo "\n📄 JSON Part (extracted):\n";
        echo $json_part . "\n";
        
        $response = json_decode($json_part, true);
        if ($response && $response['status'] == 1) {
            echo "\n✅ Despite warnings, login was successful!\n";
            echo "The authentication logic is working, but there are still PHP warnings to fix.\n";
        }
    }
    
    echo "\n⚠️  Full Response (showing PHP errors):\n";
    echo $result . "\n";
}

echo "\n=== Test Complete ===\n";
?>
