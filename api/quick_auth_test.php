<?php
/**
 * Quick authentication test
 */

$url = 'http://localhost/amt/api/auth/login';
$data = json_encode([
    'username' => 'test',
    'password' => 'test',
    'deviceToken' => 'test-device'
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
        'timeout' => 30
    ]
]);

echo "Testing authentication endpoint...\n";

$result = @file_get_contents($url, false, $context);

if ($result === false) {
    echo "❌ Request failed\n";
} else {
    echo "✅ Response received:\n";
    echo $result . "\n";
    
    $response = json_decode($result, true);
    if ($response) {
        echo "\nAnalysis:\n";
        echo "Status: " . $response['status'] . "\n";
        echo "Message: " . $response['message'] . "\n";
        
        if (isset($response['debug_info'])) {
            echo "Debug Info: " . $response['debug_info'] . "\n";
        }
        
        if (isset($response['auto_fix_attempted'])) {
            echo "Auto-fix attempted: " . ($response['auto_fix_attempted'] ? 'YES' : 'NO') . "\n";
        }
        
        if (isset($response['auto_fix_result'])) {
            echo "Auto-fix result: " . json_encode($response['auto_fix_result']) . "\n";
        }
        
        // Check if we're getting the expected progression
        if ($response['message'] == 'Your account is suspended') {
            echo "❌ Still suspended - auto-fix may have failed\n";
        } elseif ($response['message'] == 'Invalid Username or Password') {
            echo "✅ SUCCESS! Now getting proper login validation\n";
        } else {
            echo "ℹ️  Different response: " . $response['message'] . "\n";
        }
    }
}

echo "\nTest complete.\n";
?>
