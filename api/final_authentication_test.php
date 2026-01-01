<?php
/**
 * Final Authentication Test
 * Tests the Student API authentication after all fixes
 */

echo "=== Final Student API Authentication Test ===\n\n";

// Test configuration
$base_url = 'http://localhost/amt/api/';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool', 
    'Auth-Key: schoolAdmin@'
];

// Test credentials
$test_data = [
    'username' => 'student123',
    'password' => 'password123',
    'deviceToken' => 'test-device-token'
];

/**
 * Make HTTP request with proper error handling
 */
function makeRequest($url, $data, $headers) {
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => json_encode($data),
            'timeout' => 30,
            'ignore_errors' => true
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    $http_code = 200; // Default
    
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
        'success' => $response !== false
    ];
}

echo "1. Testing Authentication Endpoint:\n";
echo "===================================\n";

$result = makeRequest($base_url . 'auth/login', $test_data, $headers);

echo "HTTP Status: " . $result['http_code'] . "\n";
echo "Request Success: " . ($result['success'] ? 'YES' : 'NO') . "\n";

if ($result['success']) {
    echo "Response Length: " . strlen($result['response']) . " characters\n";
    
    // Check if response is JSON
    $json_data = json_decode($result['response'], true);
    
    if ($json_data) {
        echo "✅ Valid JSON Response\n";
        echo "Status: " . (isset($json_data['status']) ? $json_data['status'] : 'NOT SET') . "\n";
        echo "Message: " . (isset($json_data['message']) ? $json_data['message'] : 'NOT SET') . "\n";
        
        // Analyze the response
        if (isset($json_data['message'])) {
            $message = $json_data['message'];
            
            if ($message == 'Your account is suspended') {
                echo "❌ ISSUE: Still getting 'account suspended' message\n";
                echo "   This means the fix hasn't taken effect yet.\n";
                
                if (isset($json_data['debug_info'])) {
                    echo "   Debug Info: " . $json_data['debug_info'] . "\n";
                }
                
                if (isset($json_data['auto_fix_attempted'])) {
                    echo "   Auto-fix attempted: " . ($json_data['auto_fix_attempted'] ? 'YES' : 'NO') . "\n";
                }
                
            } elseif ($message == 'Invalid Username or Password') {
                echo "✅ SUCCESS: Authentication system is working!\n";
                echo "   The system is now properly validating credentials.\n";
                echo "   'Invalid Username or Password' means the fix worked.\n";
                
            } elseif (strpos($message, 'System') !== false) {
                echo "⚠️  SYSTEM ERROR: " . $message . "\n";
                echo "   There may be a configuration or database issue.\n";
                
            } else {
                echo "ℹ️  OTHER RESPONSE: " . $message . "\n";
                echo "   This is a different response than expected.\n";
            }
        }
        
        // Show full response for debugging
        echo "\nFull JSON Response:\n";
        echo json_encode($json_data, JSON_PRETTY_PRINT) . "\n";
        
    } else {
        echo "❌ Invalid JSON Response\n";
        echo "Raw Response (first 500 chars):\n";
        echo substr($result['response'], 0, 500) . "\n";
        
        // Check if it's an HTML error page
        if (strpos($result['response'], '<html>') !== false || strpos($result['response'], 'Error') !== false) {
            echo "⚠️  Received HTML error page - there may be a PHP error\n";
        }
    }
    
} else {
    echo "❌ Request Failed\n";
    echo "This could indicate:\n";
    echo "- Server is not running\n";
    echo "- URL is incorrect\n";
    echo "- Network connectivity issue\n";
}

echo "\n2. Summary and Recommendations:\n";
echo "================================\n";

if ($result['success'] && isset($json_data)) {
    if (isset($json_data['message'])) {
        $message = $json_data['message'];
        
        if ($message == 'Invalid Username or Password') {
            echo "🎉 AUTHENTICATION FIX SUCCESSFUL!\n";
            echo "\nWhat this means:\n";
            echo "- Student API authentication is now working correctly\n";
            echo "- The 'account suspended' issue has been resolved\n";
            echo "- System is properly validating user credentials\n";
            echo "- Students can now attempt to log in with valid credentials\n";
            
            echo "\nNext Steps:\n";
            echo "1. Test with actual valid student credentials\n";
            echo "2. Verify student registration/enrollment process\n";
            echo "3. Test other Student API endpoints\n";
            echo "4. Remove debug endpoints if not needed in production\n";
            
        } elseif ($message == 'Your account is suspended') {
            echo "⚠️  AUTHENTICATION FIX INCOMPLETE\n";
            echo "\nThe issue persists. Recommended actions:\n";
            echo "1. Check database manually: SELECT student_panel_login FROM sch_settings;\n";
            echo "2. Run SQL fix: UPDATE sch_settings SET student_panel_login = 'yes';\n";
            echo "3. Test debug endpoints: /debug-auth/enable-login\n";
            echo "4. Check application logs for detailed error messages\n";
            
        } else {
            echo "ℹ️  UNEXPECTED RESPONSE\n";
            echo "The response is different than expected. Review the full response above.\n";
        }
    }
} else {
    echo "❌ UNABLE TO TEST\n";
    echo "Cannot determine if the fix worked due to request failure.\n";
    echo "Please check server status and try again.\n";
}

echo "\n=== Test Complete ===\n";
?>
