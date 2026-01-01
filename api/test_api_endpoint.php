<?php
/**
 * Test the API endpoint directly with corrected parameters
 */

header('Content-Type: text/plain; charset=utf-8');

echo "=== Testing Other Collection Report API Endpoint ===\n\n";

// Test parameters (CORRECTED)
$test_cases = [
    [
        'name' => 'Test 1: All Filters (Corrected Fee Type ID)',
        'data' => [
            'session_id' => '21',
            'class_id' => '16',
            'section_id' => '26',
            'feetype_id' => '4',  // CORRECTED: EAMCET = 4
            'collect_by_id' => '6',
            'from_date' => '2025-09-01',
            'to_date' => '2025-10-11'
        ]
    ],
    [
        'name' => 'Test 2: Without Collector Filter',
        'data' => [
            'session_id' => '21',
            'class_id' => '16',
            'section_id' => '26',
            'feetype_id' => '4',
            'from_date' => '2025-09-01',
            'to_date' => '2025-10-11'
        ]
    ],
    [
        'name' => 'Test 3: Without Fee Type Filter',
        'data' => [
            'session_id' => '21',
            'class_id' => '16',
            'section_id' => '26',
            'collect_by_id' => '6',
            'from_date' => '2025-09-01',
            'to_date' => '2025-10-11'
        ]
    ],
    [
        'name' => 'Test 4: Without Session Filter',
        'data' => [
            'class_id' => '16',
            'section_id' => '26',
            'feetype_id' => '4',
            'collect_by_id' => '6',
            'from_date' => '2025-09-01',
            'to_date' => '2025-10-11'
        ]
    ],
    [
        'name' => 'Test 5: Only Date Range',
        'data' => [
            'from_date' => '2025-09-01',
            'to_date' => '2025-10-11'
        ]
    ]
];

foreach ($test_cases as $test) {
    echo "=== {$test['name']} ===\n";
    echo "Request Body:\n";
    echo json_encode($test['data'], JSON_PRETTY_PRINT) . "\n\n";
    
    // Make API call
    $url = 'http://localhost/amt/api/other-collection-report/filter';
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($test['data']));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Client-Service: smartschool',
        'Auth-Key: schoolAdmin@',
        'Content-Type: application/json'
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code != 200) {
        echo "✗ HTTP Error: $http_code\n";
        echo "Response: $response\n\n";
        continue;
    }
    
    $result = json_decode($response, true);
    
    if (!$result) {
        echo "✗ Failed to parse JSON response\n";
        echo "Raw response: $response\n\n";
        continue;
    }
    
    echo "Response:\n";
    echo "  Status: {$result['status']}\n";
    echo "  Message: {$result['message']}\n";
    echo "  Total Records: {$result['summary']['total_records']}\n";
    echo "  Total Paid: {$result['summary']['total_paid']}\n\n";
    
    if ($result['summary']['total_records'] > 0) {
        echo "✓ Data Found!\n";
        echo "First record:\n";
        $first = $result['data'][0];
        echo "  Payment ID: {$first['payment_id']}\n";
        echo "  Date: {$first['date']}\n";
        echo "  Student: {$first['student_name']}\n";
        echo "  Class: {$first['class']}\n";
        echo "  Fee Type: {$first['fee_type']}\n";
        echo "  Amount: {$first['paid']}\n";
        
        // Check if payment 945 is in results
        $found_945 = false;
        foreach ($result['data'] as $record) {
            if (strpos($record['payment_id'], '945/') === 0) {
                $found_945 = true;
                echo "\n  ✓ Payment 945 FOUND in results!\n";
                break;
            }
        }
        if (!$found_945) {
            echo "\n  ✗ Payment 945 NOT in results\n";
        }
    } else {
        echo "✗ No data returned\n";
        if (isset($result['debug'])) {
            echo "Debug Info:\n";
            echo "  " . $result['debug']['note'] . "\n";
        }
    }
    
    echo "\n" . str_repeat("-", 80) . "\n\n";
}

echo "=== Testing Complete ===\n";

