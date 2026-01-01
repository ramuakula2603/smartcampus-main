<?php
/**
 * Test with user's actual credentials
 */

echo "=== Testing with User Credentials ===\n\n";

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

echo "Testing authentication with credentials:\n";
echo "Username: std1457\n";
echo "Password: 1ilhr5\n\n";

$result = @file_get_contents($url, false, $context);

if ($result === false) {
    echo "❌ Request failed\n";
} else {
    echo "✅ Response received:\n";
    
    // Check if response contains PHP errors
    if (strpos($result, 'A PHP Error was encountered') !== false) {
        echo "⚠️  Response contains PHP warnings/errors\n";
        
        // Extract just the JSON part
        $json_start = strpos($result, '{');
        if ($json_start !== false) {
            $json_part = substr($result, $json_start);
            echo "\n📄 JSON Response (extracted):\n";
            echo $json_part . "\n";
            
            $response = json_decode($json_part, true);
            if ($response) {
                echo "\n📊 Analysis:\n";
                echo "Status: " . $response['status'] . "\n";
                echo "Message: " . $response['message'] . "\n";
                
                if ($response['status'] == 1) {
                    echo "✅ SUCCESS: Login successful!\n";
                    echo "User ID: " . $response['id'] . "\n";
                    echo "Token: " . $response['token'] . "\n";
                    echo "Role: " . $response['role'] . "\n";
                    
                    if (isset($response['record'])) {
                        echo "Student Name: " . $response['record']['username'] . "\n";
                        echo "Class: " . $response['record']['class'] . "\n";
                        echo "Section: " . $response['record']['section'] . "\n";
                    }
                } else {
                    echo "❌ Login failed: " . $response['message'] . "\n";
                }
            }
        } else {
            echo "❌ No JSON found in response\n";
        }
        
        echo "\n🔧 PHP Errors detected - need to fix remaining warnings\n";
        
    } else {
        echo "✅ Clean response (no PHP errors)\n";
        echo $result . "\n";
        
        $response = json_decode($result, true);
        if ($response && $response['status'] == 1) {
            echo "\n🎉 PERFECT! Clean successful login response\n";
        }
    }
}

echo "\n=== Test Complete ===\n";
?>
