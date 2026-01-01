<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Database_verification extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('staffattendancemodel');
        $this->load->helper('json_output');
    }

    /**
     * Verify database data and API consistency
     */
    public function index()
    {
        $from_date = '2024-08-01';
        $to_date = '2024-08-31';
        
        $verification_results = array(
            'test_date' => date('Y-m-d H:i:s'),
            'date_range' => array('from' => $from_date, 'to' => $to_date),
            'database_queries' => array(),
            'api_results' => array(),
            'comparison' => array()
        );

        try {
            // 1. Direct database queries
            $verification_results['database_queries'] = $this->performDirectQueries($from_date, $to_date);
            
            // 2. API model results
            $verification_results['api_results'] = $this->testApiModel($from_date, $to_date);
            
            // 3. Comparison and analysis
            $verification_results['comparison'] = $this->compareResults(
                $verification_results['database_queries'],
                $verification_results['api_results']
            );
            
            json_output(200, array(
                'status' => 1,
                'message' => 'Database verification completed successfully',
                'data' => $verification_results
            ));
            
        } catch (Exception $e) {
            json_output(500, array(
                'status' => 500,
                'message' => 'Verification error: ' . $e->getMessage()
            ));
        }
    }

    /**
     * Perform direct database queries
     */
    private function performDirectQueries($from_date, $to_date)
    {
        $results = array();
        
        // Total active staff
        $staff_query = $this->db->select('COUNT(*) as total')
                               ->from('staff')
                               ->where('is_active', 1)
                               ->get();
        $results['total_active_staff'] = $staff_query->row()->total;
        
        // Staff with attendance records in date range
        $attendance_staff_query = $this->db->select('staff_id, COUNT(*) as record_count')
                                          ->from('staff_attendance')
                                          ->where('date >=', $from_date)
                                          ->where('date <=', $to_date)
                                          ->group_by('staff_id')
                                          ->get();
        
        $staff_with_records = $attendance_staff_query->result();
        $results['staff_with_attendance'] = count($staff_with_records);
        $results['attendance_by_staff'] = array();
        
        $total_records = 0;
        foreach ($staff_with_records as $record) {
            $total_records += $record->record_count;
            $results['attendance_by_staff'][$record->staff_id] = $record->record_count;
        }
        $results['total_attendance_records'] = $total_records;
        
        // Attendance by type
        $type_query = $this->db->select('sat.attendencetype, COUNT(*) as count')
                              ->from('staff_attendance sa')
                              ->join('staff_attendance_type sat', 'sa.staff_attendance_type_id = sat.id')
                              ->where('sa.date >=', $from_date)
                              ->where('sa.date <=', $to_date)
                              ->group_by('sat.attendencetype')
                              ->get();
        
        $results['attendance_by_type'] = array();
        foreach ($type_query->result() as $type) {
            $results['attendance_by_type'][$type->attendencetype] = $type->count;
        }
        
        // Staff ID 6 specific data
        $staff_6_attendance = $this->db->select('sa.date, sat.attendencetype')
                                      ->from('staff_attendance sa')
                                      ->join('staff_attendance_type sat', 'sa.staff_attendance_type_id = sat.id')
                                      ->where('sa.staff_id', 6)
                                      ->where('sa.date >=', $from_date)
                                      ->where('sa.date <=', $to_date)
                                      ->order_by('sa.date', 'DESC')
                                      ->get();
        
        $staff_6_types = array();
        foreach ($staff_6_attendance->result() as $att) {
            if (!isset($staff_6_types[$att->attendencetype])) {
                $staff_6_types[$att->attendencetype] = 0;
            }
            $staff_6_types[$att->attendencetype]++;
        }
        
        $results['staff_6_details'] = array(
            'total_records' => $staff_6_attendance->num_rows(),
            'by_type' => $staff_6_types
        );
        
        return $results;
    }

    /**
     * Test API model methods
     */
    private function testApiModel($from_date, $to_date)
    {
        $results = array();
        
        // Test individual staff (ID 6)
        $individual_result = $this->staffattendancemodel->getAttendanceSummary(6, $from_date, $to_date);
        $results['individual_staff_6'] = array(
            'staff_id' => isset($individual_result['staff_id']) ? $individual_result['staff_id'] : null,
            'attendance_summary' => isset($individual_result['attendance_summary']) ? $individual_result['attendance_summary'] : array(),
            'total_attendance_dates' => isset($individual_result['attendance_dates']) ? count($individual_result['attendance_dates']) : 0
        );
        
        // Test all staff
        $all_staff_result = $this->staffattendancemodel->getAttendanceSummary(null, $from_date, $to_date);
        
        $staff_count = 0;
        $staff_with_data = 0;
        $total_present = 0;
        $total_absent = 0;
        $total_late = 0;
        $total_half_day = 0;
        
        if (isset($all_staff_result['staff_attendance_data'])) {
            $staff_count = count($all_staff_result['staff_attendance_data']);
            
            foreach ($all_staff_result['staff_attendance_data'] as $staff) {
                if (isset($staff['attendance_summary'])) {
                    $present = $staff['attendance_summary']['Present']['count'];
                    $absent = $staff['attendance_summary']['Absent']['count'];
                    $late = $staff['attendance_summary']['Late']['count'];
                    $half_day = $staff['attendance_summary']['Half Day']['count'];
                    
                    if ($present > 0 || $absent > 0 || $late > 0 || $half_day > 0) {
                        $staff_with_data++;
                    }
                    
                    $total_present += $present;
                    $total_absent += $absent;
                    $total_late += $late;
                    $total_half_day += $half_day;
                }
            }
        }
        
        $results['all_staff'] = array(
            'total_staff_returned' => $staff_count,
            'staff_with_attendance_data' => $staff_with_data,
            'totals' => array(
                'present' => $total_present,
                'absent' => $total_absent,
                'late' => $total_late,
                'half_day' => $total_half_day
            )
        );
        
        return $results;
    }

    /**
     * Compare database and API results
     */
    private function compareResults($db_results, $api_results)
    {
        $comparison = array();
        
        // Compare staff counts
        $comparison['staff_count_match'] = ($db_results['total_active_staff'] == $api_results['all_staff']['total_staff_returned']);
        $comparison['staff_with_data_match'] = ($db_results['staff_with_attendance'] == $api_results['all_staff']['staff_with_attendance_data']);
        
        // Compare staff 6 data
        $db_staff_6_present = isset($db_results['staff_6_details']['by_type']['Present']) ? $db_results['staff_6_details']['by_type']['Present'] : 0;
        $api_staff_6_present = $api_results['individual_staff_6']['attendance_summary']['Present']['count'];
        $comparison['staff_6_present_match'] = ($db_staff_6_present == $api_staff_6_present);
        
        // Compare totals
        $db_total_present = isset($db_results['attendance_by_type']['Present']) ? $db_results['attendance_by_type']['Present'] : 0;
        $api_total_present = $api_results['all_staff']['totals']['present'];
        $comparison['total_present_match'] = ($db_total_present == $api_total_present);
        
        $db_total_absent = isset($db_results['attendance_by_type']['Absent']) ? $db_results['attendance_by_type']['Absent'] : 0;
        $api_total_absent = $api_results['all_staff']['totals']['absent'];
        $comparison['total_absent_match'] = ($db_total_absent == $api_total_absent);
        
        // Summary
        $all_matches = $comparison['staff_count_match'] && 
                      $comparison['staff_with_data_match'] && 
                      $comparison['staff_6_present_match'] && 
                      $comparison['total_present_match'] && 
                      $comparison['total_absent_match'];
        
        $comparison['overall_consistency'] = $all_matches;
        
        return $comparison;
    }
}
