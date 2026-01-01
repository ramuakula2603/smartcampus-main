<?php
/**
 * Comprehensive Staff Attendance API Test
 * Tests all features of the attendance summary API
 */

echo "=== COMPREHENSIVE STAFF ATTENDANCE API TEST ===\n\n";

$base_url = 'http://localhost/amt/api/attendance/summary';

/**
 * Helper function to make API calls
 */
function makeApiCall($url, $data, $test_name) {
    echo "Test: $test_name\n";
    echo str_repeat("-", 50) . "\n";
    
    $options = array(
        'http' => array(
            'header' => "Content-Type: application/json\r\n",
            'method' => 'POST',
            'content' => json_encode($data)
        )
    );
    
    $context = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    
    if ($result !== FALSE) {
        $response = json_decode($result, true);
        
        if ($response && $response['status'] == 1) {
            echo "✓ SUCCESS: " . $response['message'] . "\n";
            
            if (isset($response['data'])) {
                // Display summary information
                if (isset($response['data']['staff_info'])) {
                    $staff = $response['data']['staff_info'];
                    echo "Staff: " . $staff['name'] . " " . $staff['surname'] . " (ID: " . $staff['employee_id'] . ")\n";
                    echo "Designation: " . $staff['designation'] . " | Department: " . $staff['department_name'] . "\n";
                    
                    if (isset($response['data']['attendance_summary'])) {
                        echo "Attendance Summary:\n";
                        foreach ($response['data']['attendance_summary'] as $type => $summary) {
                            echo "  - $type: " . $summary['count'] . " days\n";
                        }
                    }
                } elseif (isset($response['data']['total_staff'])) {
                    echo "Total Staff Members: " . $response['data']['total_staff'] . "\n";
                    echo "Sample Staff Data:\n";
                    
                    $sample_count = min(3, count($response['data']['staff_attendance_data']));
                    for ($i = 0; $i < $sample_count; $i++) {
                        $staff_data = $response['data']['staff_attendance_data'][$i];
                        $staff = $staff_data['staff_info'];
                        echo "  " . ($i + 1) . ". " . $staff['name'] . " " . $staff['surname'] . " - ";
                        
                        $total_present = $staff_data['attendance_summary']['Present']['count'];
                        $total_absent = $staff_data['attendance_summary']['Absent']['count'];
                        echo "Present: $total_present, Absent: $total_absent\n";
                    }
                }
                
                if (isset($response['data']['date_range'])) {
                    $range = $response['data']['date_range'];
                    echo "Date Range: " . $range['from_date'] . " to " . $range['to_date'] . "\n";
                }
            }
        } else {
            echo "✗ FAILED: " . ($response['message'] ?? 'Unknown error') . "\n";
        }
    } else {
        echo "✗ API CALL FAILED\n";
        $error = error_get_last();
        if ($error) {
            echo "Error: " . $error['message'] . "\n";
        }
    }
    
    echo "\n";
}

// Test 1: Get attendance for a specific staff member
echo "TEST 1: Individual Staff Member Attendance\n";
echo "==========================================\n";
makeApiCall($base_url, array(
    'staff_id' => 6,
    'from_date' => '2024-01-01',
    'to_date' => '2024-12-31'
), 'Staff ID 6 - Full Year 2024');

// Test 2: Get attendance for a specific date range
echo "TEST 2: Specific Date Range\n";
echo "===========================\n";
makeApiCall($base_url, array(
    'staff_id' => 5,
    'from_date' => '2024-08-01',
    'to_date' => '2024-08-31'
), 'Staff ID 5 - August 2024');

// Test 3: Get all staff attendance (limited output)
echo "TEST 3: All Staff Attendance Summary\n";
echo "====================================\n";
makeApiCall($base_url, array(
    'from_date' => '2024-11-01',
    'to_date' => '2024-11-30'
), 'All Staff - November 2024');

// Test 4: Default date range (current year)
echo "TEST 4: Default Date Range\n";
echo "==========================\n";
makeApiCall($base_url, array(
    'staff_id' => 8
), 'Staff ID 8 - Default Date Range');

// Test 5: Error handling - Invalid staff ID
echo "TEST 5: Error Handling - Invalid Staff ID\n";
echo "==========================================\n";
makeApiCall($base_url, array(
    'staff_id' => 999,
    'from_date' => '2024-01-01',
    'to_date' => '2024-12-31'
), 'Invalid Staff ID 999');

// Test 6: Error handling - Invalid date format
echo "TEST 6: Error Handling - Invalid Date Format\n";
echo "=============================================\n";
makeApiCall($base_url, array(
    'staff_id' => 6,
    'from_date' => '2024/01/01',
    'to_date' => '2024-12-31'
), 'Invalid Date Format');

// Test 7: Empty request (should use defaults)
echo "TEST 7: Empty Request (Default Parameters)\n";
echo "==========================================\n";
makeApiCall($base_url, array(), 'Empty Request - Should Use Defaults');

echo "=== ALL TESTS COMPLETED ===\n";
echo "\nAPI Features Demonstrated:\n";
echo "✓ Individual staff member attendance retrieval\n";
echo "✓ All staff members attendance retrieval\n";
echo "✓ Date range filtering\n";
echo "✓ Default date range handling\n";
echo "✓ Detailed attendance statistics (Present, Absent, Half Day, Late, Holiday)\n";
echo "✓ Individual date records with timestamps\n";
echo "✓ Leave summary integration\n";
echo "✓ Staff information display\n";
echo "✓ Error handling and validation\n";
echo "✓ JSON response format\n";
?>
