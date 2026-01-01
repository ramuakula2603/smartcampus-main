<?php
/**
 * Database Verification Test
 * Cross-check API results against direct database queries
 */

// Include CodeIgniter framework
define('BASEPATH', true);
require_once dirname(__FILE__) . '/../index.php';

echo "=== DATABASE VERIFICATION TEST ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// Get CI instance
$CI =& get_instance();
$CI->load->database();

// Test date range
$from_date = '2024-08-01';
$to_date = '2024-08-31';

echo "🔍 DIRECT DATABASE QUERIES\n";
echo "=========================\n\n";

// 1. Check total staff count
echo "1. Total Staff Count:\n";
$staff_query = $CI->db->select('COUNT(*) as total')
                     ->from('staff')
                     ->where('is_active', 1)
                     ->get();
$total_staff = $staff_query->row()->total;
echo "   Active Staff: $total_staff\n\n";

// 2. Check staff attendance records for the date range
echo "2. Staff Attendance Records ($from_date to $to_date):\n";
$attendance_query = $CI->db->select('staff_id, COUNT(*) as record_count')
                          ->from('staff_attendance')
                          ->where('date >=', $from_date)
                          ->where('date <=', $to_date)
                          ->group_by('staff_id')
                          ->get();

$attendance_records = $attendance_query->result();
echo "   Staff with attendance records: " . count($attendance_records) . "\n";

$total_records = 0;
foreach ($attendance_records as $record) {
    $total_records += $record->record_count;
    echo "   Staff ID {$record->staff_id}: {$record->record_count} records\n";
}
echo "   Total attendance records: $total_records\n\n";

// 3. Check attendance by type
echo "3. Attendance by Type ($from_date to $to_date):\n";
$type_query = $CI->db->select('sat.attendencetype, COUNT(*) as count')
                    ->from('staff_attendance sa')
                    ->join('staff_attendance_type sat', 'sa.staff_attendance_type_id = sat.id')
                    ->where('sa.date >=', $from_date)
                    ->where('sa.date <=', $to_date)
                    ->group_by('sat.attendencetype')
                    ->get();

$type_records = $type_query->result();
foreach ($type_records as $type) {
    echo "   {$type->attendencetype}: {$type->count} records\n";
}
echo "\n";

// 4. Check specific staff member (ID 6) details
echo "4. Staff ID 6 Details ($from_date to $to_date):\n";
$staff_6_query = $CI->db->select('s.name, s.surname, s.employee_id')
                       ->from('staff s')
                       ->where('s.id', 6)
                       ->get();

if ($staff_6_query->num_rows() > 0) {
    $staff_6 = $staff_6_query->row();
    echo "   Name: {$staff_6->name} {$staff_6->surname}\n";
    echo "   Employee ID: {$staff_6->employee_id}\n";
    
    // Get attendance records for staff 6
    $staff_6_attendance = $CI->db->select('sa.date, sat.attendencetype, sa.remark')
                                ->from('staff_attendance sa')
                                ->join('staff_attendance_type sat', 'sa.staff_attendance_type_id = sat.id')
                                ->where('sa.staff_id', 6)
                                ->where('sa.date >=', $from_date)
                                ->where('sa.date <=', $to_date)
                                ->order_by('sa.date', 'DESC')
                                ->get();
    
    echo "   Attendance Records: " . $staff_6_attendance->num_rows() . "\n";
    
    $attendance_types = [];
    foreach ($staff_6_attendance->result() as $att) {
        if (!isset($attendance_types[$att->attendencetype])) {
            $attendance_types[$att->attendencetype] = 0;
        }
        $attendance_types[$att->attendencetype]++;
        echo "   - {$att->date}: {$att->attendencetype}\n";
    }
    
    echo "   Summary:\n";
    foreach ($attendance_types as $type => $count) {
        echo "     {$type}: $count days\n";
    }
} else {
    echo "   Staff ID 6 not found!\n";
}
echo "\n";

// 5. Check attendance type mapping
echo "5. Attendance Type Mapping:\n";
$type_mapping_query = $CI->db->select('id, attendencetype, key_value')
                            ->from('staff_attendance_type')
                            ->get();

foreach ($type_mapping_query->result() as $type) {
    echo "   ID {$type->id}: {$type->attendencetype} ({$type->key_value})\n";
}
echo "\n";

// 6. Test API model method directly
echo "6. Testing Staffattendancemodel directly:\n";
$CI->load->model('staffattendancemodel');

try {
    // Test individual staff
    $api_result_individual = $CI->staffattendancemodel->getAttendanceSummary(6, $from_date, $to_date);
    echo "   Individual Staff API Result:\n";
    if (isset($api_result_individual['attendance_summary'])) {
        foreach ($api_result_individual['attendance_summary'] as $type => $data) {
            echo "     {$type}: {$data['count']} days\n";
        }
    }
    echo "\n";
    
    // Test all staff
    $api_result_all = $CI->staffattendancemodel->getAttendanceSummary(null, $from_date, $to_date);
    echo "   All Staff API Result:\n";
    if (isset($api_result_all['staff_attendance_data'])) {
        echo "     Total staff returned: " . count($api_result_all['staff_attendance_data']) . "\n";
        
        $staff_with_data = 0;
        $total_present_api = 0;
        $total_absent_api = 0;
        
        foreach ($api_result_all['staff_attendance_data'] as $staff) {
            $present = $staff['attendance_summary']['Present']['count'];
            $absent = $staff['attendance_summary']['Absent']['count'];
            
            if ($present > 0 || $absent > 0) {
                $staff_with_data++;
            }
            
            $total_present_api += $present;
            $total_absent_api += $absent;
        }
        
        echo "     Staff with attendance data: $staff_with_data\n";
        echo "     Total present (API): $total_present_api\n";
        echo "     Total absent (API): $total_absent_api\n";
    }
    
} catch (Exception $e) {
    echo "   Error testing model: " . $e->getMessage() . "\n";
}

echo "\n=== VERIFICATION COMPLETED ===\n";
?>
