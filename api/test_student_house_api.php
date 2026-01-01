<?php
/**
 * Test Student House API Endpoints
 */

echo "=== Student House API Test ===\n\n";

$base_url = 'http://localhost/amt/api/';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

/**
 * Make HTTP request
 */
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

echo "1. Testing List Student Houses Endpoint:\n";
echo "=========================================\n";

$result = makeRequest($base_url . 'student-house/list', [], $headers);

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
        
        if (isset($json_data['data']) && is_array($json_data['data'])) {
            echo "Total Records: " . count($json_data['data']) . "\n";
            if (!empty($json_data['data'])) {
                echo "First Record: " . json_encode($json_data['data'][0]) . "\n";
            }
        }
        
        if ($json_data['status'] == 1) {
            echo "✅ List endpoint working correctly\n";
        } else {
            echo "❌ List endpoint returned error: " . $json_data['message'] . "\n";
        }
    } else {
        echo "❌ Invalid JSON Response\n";
        echo "Raw Response (first 500 chars):\n";
        echo substr($result['response'], 0, 500) . "\n";
    }
} else {
    echo "❌ Request Failed\n";
}

echo "\n2. Testing Get Single House Endpoint:\n";
echo "======================================\n";

$result = makeRequest($base_url . 'student-house/get/1', [], $headers);

echo "HTTP Status: " . $result['http_code'] . "\n";
echo "Request Success: " . ($result['success'] ? 'YES' : 'NO') . "\n";

if ($result['success']) {
    $json_data = json_decode($result['response'], true);
    
    if ($json_data) {
        echo "✅ Valid JSON Response\n";
        echo "Status: " . $json_data['status'] . "\n";
        echo "Message: " . $json_data['message'] . "\n";
        
        if ($json_data['status'] == 1) {
            echo "✅ Get endpoint working correctly\n";
            if (isset($json_data['data'])) {
                echo "Retrieved House: " . json_encode($json_data['data']) . "\n";
            }
        } else {
            echo "❌ Get endpoint returned error: " . $json_data['message'] . "\n";
        }
    } else {
        echo "❌ Invalid JSON Response\n";
        echo "Raw Response: " . substr($result['response'], 0, 500) . "\n";
    }
} else {
    echo "❌ Request Failed\n";
}

echo "\n3. Testing Create House Endpoint:\n";
echo "==================================\n";

$create_data = [
    'house_name' => 'Purple House',
    'description' => 'The Purple House represents leadership and innovation'
];

$result = makeRequest($base_url . 'student-house/create', $create_data, $headers);

echo "HTTP Status: " . $result['http_code'] . "\n";
echo "Request Success: " . ($result['success'] ? 'YES' : 'NO') . "\n";

if ($result['success']) {
    $json_data = json_decode($result['response'], true);
    
    if ($json_data) {
        echo "✅ Valid JSON Response\n";
        echo "Status: " . $json_data['status'] . "\n";
        echo "Message: " . $json_data['message'] . "\n";
        
        if ($json_data['status'] == 1) {
            echo "✅ Create endpoint working correctly\n";
            if (isset($json_data['data'])) {
                echo "Created House: " . json_encode($json_data['data']) . "\n";
                $created_id = $json_data['data']['id'];
            }
        } else {
            echo "❌ Create endpoint returned error: " . $json_data['message'] . "\n";
        }
    } else {
        echo "❌ Invalid JSON Response\n";
        echo "Raw Response: " . substr($result['response'], 0, 500) . "\n";
    }
} else {
    echo "❌ Request Failed\n";
}

echo "\n4. Testing Update House Endpoint:\n";
echo "==================================\n";

if (isset($created_id)) {
    $update_data = [
        'house_name' => 'Updated Purple House',
        'description' => 'The Purple House represents leadership, innovation, and creativity',
        'is_active' => 'yes'
    ];
    
    $result = makeRequest($base_url . 'student-house/update/' . $created_id, $update_data, $headers);
    
    echo "HTTP Status: " . $result['http_code'] . "\n";
    echo "Request Success: " . ($result['success'] ? 'YES' : 'NO') . "\n";
    
    if ($result['success']) {
        $json_data = json_decode($result['response'], true);
        
        if ($json_data) {
            echo "✅ Valid JSON Response\n";
            echo "Status: " . $json_data['status'] . "\n";
            echo "Message: " . $json_data['message'] . "\n";
            
            if ($json_data['status'] == 1) {
                echo "✅ Update endpoint working correctly\n";
                if (isset($json_data['data'])) {
                    echo "Updated House: " . json_encode($json_data['data']) . "\n";
                }
            } else {
                echo "❌ Update endpoint returned error: " . $json_data['message'] . "\n";
            }
        } else {
            echo "❌ Invalid JSON Response\n";
            echo "Raw Response: " . substr($result['response'], 0, 500) . "\n";
        }
    } else {
        echo "❌ Request Failed\n";
    }
} else {
    echo "⚠️  Skipping update test - no created ID available\n";
}

echo "\n5. Testing Delete House Endpoint:\n";
echo "==================================\n";

if (isset($created_id)) {
    $result = makeRequest($base_url . 'student-house/delete/' . $created_id, [], $headers);
    
    echo "HTTP Status: " . $result['http_code'] . "\n";
    echo "Request Success: " . ($result['success'] ? 'YES' : 'NO') . "\n";
    
    if ($result['success']) {
        $json_data = json_decode($result['response'], true);
        
        if ($json_data) {
            echo "✅ Valid JSON Response\n";
            echo "Status: " . $json_data['status'] . "\n";
            echo "Message: " . $json_data['message'] . "\n";
            
            if ($json_data['status'] == 1) {
                echo "✅ Delete endpoint working correctly\n";
                if (isset($json_data['data'])) {
                    echo "Deleted House: " . json_encode($json_data['data']) . "\n";
                }
            } else {
                echo "❌ Delete endpoint returned error: " . $json_data['message'] . "\n";
            }
        } else {
            echo "❌ Invalid JSON Response\n";
            echo "Raw Response: " . substr($result['response'], 0, 500) . "\n";
        }
    } else {
        echo "❌ Request Failed\n";
    }
} else {
    echo "⚠️  Skipping delete test - no created ID available\n";
}

echo "\n=== API Test Complete ===\n";
?>
