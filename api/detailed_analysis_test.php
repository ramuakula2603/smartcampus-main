<?php
/**
 * Detailed Analysis Test for Staff Attendance API
 * Focus on investigating the specific issues mentioned
 */

echo "=== DETAILED STAFF ATTENDANCE API ANALYSIS ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// Test configuration
$base_url = 'http://localhost/amt/api';
$auth_headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

/**
 * Execute API call and return parsed response
 */
function callAPI($endpoint, $data, $authenticated = true) {
    global $base_url, $auth_headers;
    
    $url = "$base_url/$endpoint";
    $headers = $authenticated ? $auth_headers : ['Content-Type: application/json'];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code == 200) {
        return json_decode($response, true);
    }
    
    return null;
}

/**
 * Analyze staff attendance data in detail
 */
function analyzeStaffData($data, $test_name) {
    echo "🔍 DETAILED ANALYSIS: $test_name\n";
    echo str_repeat("=", 50) . "\n";
    
    if (isset($data['data']['staff_id'])) {
        // Individual staff analysis
        $staff_data = $data['data'];
        echo "📋 INDIVIDUAL STAFF ANALYSIS:\n";
        echo "Staff ID: {$staff_data['staff_id']}\n";
        echo "Staff Name: {$staff_data['staff_info']['name']} {$staff_data['staff_info']['surname']}\n";
        echo "Employee ID: {$staff_data['staff_info']['employee_id']}\n";
        echo "Department: {$staff_data['staff_info']['department_name']}\n";
        echo "Role: {$staff_data['staff_info']['role_name']}\n\n";
        
        echo "📊 ATTENDANCE SUMMARY:\n";
        if (isset($staff_data['attendance_summary'])) {
            foreach ($staff_data['attendance_summary'] as $type => $info) {
                echo "  {$type}: {$info['count']} days\n";
                if ($info['count'] > 0 && isset($info['dates'])) {
                    echo "    Sample dates: ";
                    $sample_dates = array_slice($info['dates'], 0, 3);
                    foreach ($sample_dates as $date_info) {
                        echo $date_info['date'] . " ";
                    }
                    echo "\n";
                }
            }
        }
        
        echo "\n📅 ATTENDANCE DATES:\n";
        if (isset($staff_data['attendance_dates'])) {
            echo "Total attendance records: " . count($staff_data['attendance_dates']) . "\n";
            
            $type_counts = [];
            foreach ($staff_data['attendance_dates'] as $date_record) {
                if (!isset($type_counts[$date_record['type']])) {
                    $type_counts[$date_record['type']] = 0;
                }
                $type_counts[$date_record['type']]++;
            }
            
            echo "Breakdown by type:\n";
            foreach ($type_counts as $type => $count) {
                echo "  {$type}: $count records\n";
            }
        }
        
    } elseif (isset($data['data']['staff_attendance_data'])) {
        // All staff analysis
        $all_staff_data = $data['data']['staff_attendance_data'];
        echo "📋 ALL STAFF ANALYSIS:\n";
        echo "Total staff returned: " . count($all_staff_data) . "\n\n";
        
        $staff_with_data = 0;
        $total_counts = [
            'Present' => 0,
            'Absent' => 0,
            'Late' => 0,
            'Half Day' => 0,
            'Holiday' => 0
        ];
        
        $staff_details = [];
        
        foreach ($all_staff_data as $staff) {
            $staff_id = $staff['staff_id'];
            $staff_name = $staff['staff_info']['name'] . ' ' . $staff['staff_info']['surname'];
            
            $has_data = false;
            $staff_summary = [];
            
            if (isset($staff['attendance_summary'])) {
                foreach ($staff['attendance_summary'] as $type => $info) {
                    $count = $info['count'];
                    $staff_summary[$type] = $count;
                    $total_counts[$type] += $count;
                    
                    if ($count > 0) {
                        $has_data = true;
                    }
                }
            }
            
            if ($has_data) {
                $staff_with_data++;
                $staff_details[] = [
                    'id' => $staff_id,
                    'name' => $staff_name,
                    'summary' => $staff_summary
                ];
            }
        }
        
        echo "📊 OVERALL STATISTICS:\n";
        echo "Staff with attendance data: $staff_with_data\n";
        echo "Staff without attendance data: " . (count($all_staff_data) - $staff_with_data) . "\n\n";
        
        echo "📈 TOTAL ATTENDANCE COUNTS:\n";
        foreach ($total_counts as $type => $count) {
            echo "  Total {$type}: $count days\n";
        }
        echo "\n";
        
        echo "👥 STAFF WITH ATTENDANCE DATA:\n";
        foreach ($staff_details as $staff) {
            echo "  Staff ID {$staff['id']} ({$staff['name']}):\n";
            foreach ($staff['summary'] as $type => $count) {
                if ($count > 0) {
                    echo "    {$type}: $count\n";
                }
            }
            echo "\n";
        }
        
        // Show staff without data
        echo "👤 STAFF WITHOUT ATTENDANCE DATA:\n";
        foreach ($all_staff_data as $staff) {
            $has_data = false;
            if (isset($staff['attendance_summary'])) {
                foreach ($staff['attendance_summary'] as $type => $info) {
                    if ($info['count'] > 0) {
                        $has_data = true;
                        break;
                    }
                }
            }
            
            if (!$has_data) {
                echo "  Staff ID {$staff['staff_id']}: {$staff['staff_info']['name']} {$staff['staff_info']['surname']}\n";
            }
        }
    }
    
    echo "\n" . str_repeat("=", 50) . "\n\n";
}

// Test scenarios
echo "🧪 RUNNING DETAILED TESTS\n";
echo "========================\n\n";

// Test 1: Individual staff with specific date range
echo "TEST 1: Individual Staff (ID 6) - August 2024\n";
$result1 = callAPI('teacher/attendance-summary', [
    'staff_id' => 6,
    'from_date' => '2024-08-01',
    'to_date' => '2024-08-31'
]);

if ($result1) {
    analyzeStaffData($result1, 'Individual Staff - August 2024');
} else {
    echo "❌ Failed to get response\n\n";
}

// Test 2: All staff for August 2024
echo "TEST 2: All Staff - August 2024\n";
$result2 = callAPI('teacher/attendance-summary', [
    'from_date' => '2024-08-01',
    'to_date' => '2024-08-31'
]);

if ($result2) {
    analyzeStaffData($result2, 'All Staff - August 2024');
} else {
    echo "❌ Failed to get response\n\n";
}

// Test 3: All staff for a different month to compare
echo "TEST 3: All Staff - November 2024\n";
$result3 = callAPI('teacher/attendance-summary', [
    'from_date' => '2024-11-01',
    'to_date' => '2024-11-30'
]);

if ($result3) {
    analyzeStaffData($result3, 'All Staff - November 2024');
} else {
    echo "❌ Failed to get response\n\n";
}

echo "=== ANALYSIS COMPLETED ===\n";
?>
