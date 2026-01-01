<?php
/**
 * Test debug endpoints using file_get_contents
 */

echo "=== Testing Debug Endpoints ===\n\n";

$base_url = 'http://localhost/amt/api/';

/**
 * Make HTTP request using file_get_contents
 */
function makeSimpleRequest($url) {
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => [
                'Content-Type: application/json',
                'Client-Service: smartschool',
                'Auth-Key: schoolAdmin@'
            ],
            'timeout' => 30
        ]
    ]);
    
    $result = @file_get_contents($url, false, $context);
    
    if ($result === false) {
        return array('error' => 'Request failed', 'response' => null);
    }
    
    return array('error' => null, 'response' => $result);
}

echo "1. Testing check-settings endpoint:\n";
echo "===================================\n";

$result1 = makeSimpleRequest($base_url . 'debug-auth/check-settings');

if ($result1['error']) {
    echo "❌ Error: " . $result1['error'] . "\n";
} else {
    echo "✅ Response received:\n";
    echo $result1['response'] . "\n";
    
    $data = json_decode($result1['response'], true);
    if ($data && isset($data['debug_info'])) {
        echo "\n📊 Analysis:\n";
        if (isset($data['debug_info']['student_panel_login'])) {
            $login_info = $data['debug_info']['student_panel_login'];
            echo "- student_panel_login value: '" . $login_info['value'] . "'\n";
            echo "- equals 'yes': " . ($login_info['equals_yes'] ? 'TRUE' : 'FALSE') . "\n";
        }
        
        if (isset($data['debug_info']['auth_logic_result'])) {
            echo "- Auth logic result: " . $data['debug_info']['auth_logic_result'] . "\n";
        }
    }
}

echo "\n2. Testing fix-settings endpoint:\n";
echo "==================================\n";

// For POST request, we need to use curl or a different approach
$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => [
            'Content-Type: application/json',
            'Client-Service: smartschool',
            'Auth-Key: schoolAdmin@'
        ],
        'content' => '{}',
        'timeout' => 30
    ]
]);

$result2 = @file_get_contents($base_url . 'debug-auth/fix-settings', false, $context);

if ($result2 === false) {
    echo "❌ Error: Request failed\n";
} else {
    echo "✅ Response received:\n";
    echo $result2 . "\n";
}

echo "\n3. Testing test-auth endpoint:\n";
echo "===============================\n";

$result3 = @file_get_contents($base_url . 'debug-auth/test-auth', false, $context);

if ($result3 === false) {
    echo "❌ Error: Request failed\n";
} else {
    echo "✅ Response received:\n";
    echo $result3 . "\n";
    
    $data = json_decode($result3, true);
    if ($data && isset($data['test_result'])) {
        echo "\n🧪 Test Result:\n";
        echo "- Result: " . $data['test_result']['result'] . "\n";
        if (isset($data['test_result']['student_panel_login_value'])) {
            echo "- Current value: '" . $data['test_result']['student_panel_login_value'] . "'\n";
        }
    }
}

echo "\n4. Testing original auth endpoint after fix:\n";
echo "=============================================\n";

$auth_context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => [
            'Content-Type: application/json',
            'Client-Service: smartschool',
            'Auth-Key: schoolAdmin@'
        ],
        'content' => json_encode([
            'username' => 'test',
            'password' => 'test',
            'deviceToken' => 'test-device'
        ]),
        'timeout' => 30
    ]
]);

$auth_result = @file_get_contents($base_url . 'auth/login', false, $auth_context);

if ($auth_result === false) {
    echo "❌ Error: Request failed\n";
} else {
    echo "✅ Response received:\n";
    echo $auth_result . "\n";
    
    $auth_data = json_decode($auth_result, true);
    if ($auth_data) {
        echo "\n🔐 Auth Result:\n";
        echo "- Status: " . $auth_data['status'] . "\n";
        echo "- Message: " . $auth_data['message'] . "\n";
        
        if ($auth_data['message'] == 'Your account is suspended') {
            echo "❌ Still getting 'account suspended' - fix didn't work\n";
        } elseif ($auth_data['message'] == 'Invalid Username or Password') {
            echo "✅ Fixed! Now getting proper login validation (credentials are invalid but system allows login attempts)\n";
        } else {
            echo "ℹ️  Different response: " . $auth_data['message'] . "\n";
        }
    }
}

echo "\n=== Test Complete ===\n";
?>
