<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Student Attendance Type Report API Controller
 * 
 * Provides API endpoints for retrieving student attendance records filtered by specific attendance types.
 * This API corresponds to the "Student Attendance Type Report" page at:
 * http://localhost/amt/attendencereports/attendancereport
 * 
 * Key Features:
 * - Filter students by attendance type (Present, Absent, Late, Excuse, Half Day, Holiday)
 * - Support for date range filtering (search_type parameter)
 * - Class and section filtering
 * - Returns detailed student information with attendance type count
 * 
 * @package    School Management System - API
 * @subpackage API Controllers
 * @category   Student Attendance APIs
 * @author     SMS API Development Team
 * @version    1.0.0
 * @since      October 2025
 */
class Student_attendance_type_report_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load required models - IMPORTANT: Load setting_model FIRST
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('stuattendence_model');
        
        // Load library
        $this->load->library('customlib');
        
        // Load helpers
        $this->load->helper('url');
        $this->load->helper('security');
        
        // Get current session
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    /**
     * Filter Student Attendance Type Report
     * 
     * @method POST
     * @route  /api/student-attendance-type-report/filter
     * 
     * @param  string     $search_type       Optional. Date range filter type (today, this_week, last_week, this_month, last_month, last_3_months, last_6_months, last_12_months, this_year, last_year, period)
     * @param  string     $date_from         Optional. Start date for custom period (required if search_type = 'period')
     * @param  string     $date_to           Optional. End date for custom period (required if search_type = 'period')
     * @param  int        $attendance_type   Optional. Attendance type ID (1=Present, 2=Excuse, 3=Late, 4=Absent, 5=Holiday, 6=Half Day). If not provided, returns all types
     * @param  int|array  $class_id          Optional. Class ID(s) to filter by. If not provided, returns all classes
     * @param  int|array  $section_id        Optional. Section ID(s) to filter by
     * @param  int        $session_id        Optional. Session ID. If not provided, returns all sessions
     * 
     * @return JSON Response with status, message, filters_applied, date_range, total_records, data, and timestamp
     */
    public function filter()
    {
        try {
            if ($this->input->method() !== 'post') {
                $this->output
                    ->set_status_header(400)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => 'Bad request. Only POST method allowed.'
                    ]));
                return;
            }

            if (!$this->auth_model->check_auth_client()) {
                $this->output
                    ->set_status_header(401)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => 'Unauthorized access'
                    ]));
                return;
            }

            $json_input = json_decode(file_get_contents('php://input'), true);
            
            // Get input parameters
            $search_type = isset($json_input['search_type']) ? $json_input['search_type'] : null;
            $date_from = isset($json_input['date_from']) ? $json_input['date_from'] : null;
            $date_to = isset($json_input['date_to']) ? $json_input['date_to'] : null;
            $attendance_type = isset($json_input['attendance_type']) ? $json_input['attendance_type'] : null;
            $class_id = isset($json_input['class_id']) ? $json_input['class_id'] : null;
            $section_id = isset($json_input['section_id']) ? $json_input['section_id'] : null;
            $session_id = isset($json_input['session_id']) ? $json_input['session_id'] : null;

            // All parameters are now optional - can pass empty payload {}
            // If no search_type provided, return ALL historical data
            
            // Validate search_type for period
            if ($search_type === 'period' && (empty($date_from) || empty($date_to))) {
                $this->output
                    ->set_status_header(400)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => 'date_from and date_to are required when search_type is "period"'
                    ]));
                return;
            }

            // Convert single values to arrays for consistent handling
            if (!is_array($class_id)) {
                $class_id = !empty($class_id) ? array($class_id) : array();
            }
            if (!is_array($section_id)) {
                $section_id = !empty($section_id) ? array($section_id) : array();
            }

            // Filter out empty values from arrays
            $class_id = array_filter($class_id, function($value) { 
                return !empty($value) && $value !== null && $value !== ''; 
            });
            $section_id = array_filter($section_id, function($value) { 
                return !empty($value) && $value !== null && $value !== ''; 
            });

            // Convert empty arrays to null for graceful handling
            $class_id = !empty($class_id) ? $class_id : null;
            $section_id = !empty($section_id) ? $section_id : null;

            // Session ID is optional - if not provided, will query all sessions
            // No default fallback to current session

            // Get date range based on search_type or custom period
            if ($search_type === 'period' && !empty($date_from) && !empty($date_to)) {
                $from_date = date('Y-m-d', strtotime($date_from));
                $to_date = date('Y-m-d', strtotime($date_to));
            } elseif (!empty($search_type)) {
                // If search_type is provided (today, this_week, etc.), use it
                $between_date = $this->customlib->get_betweendate($search_type);
                $from_date = date('Y-m-d', strtotime($between_date['from_date']));
                $to_date = date('Y-m-d', strtotime($between_date['to_date']));
            } else {
                // If no search_type provided, return ALL historical data
                // Use a very wide date range (10 years back to 10 years forward)
                $from_date = date('Y-m-d', strtotime('-10 years'));
                $to_date = date('Y-m-d', strtotime('+10 years'));
            }

            // Build condition string for SQL query
            $condition = '';
            
            // Add attendance type condition - only if provided
            if (!empty($attendance_type)) {
                $condition .= " AND `student_attendences`.`attendence_type_id` = " . intval($attendance_type);
            }
            
            // Add class condition
            if ($class_id !== null && !empty($class_id)) {
                if (is_array($class_id) && count($class_id) > 0) {
                    $class_ids = implode(',', array_map('intval', $class_id));
                    $condition .= " AND `student_session`.`class_id` IN (" . $class_ids . ")";
                } elseif (!is_array($class_id)) {
                    $condition .= " AND `student_session`.`class_id` = " . intval($class_id);
                }
            }
            
            // Add section condition
            if ($section_id !== null && !empty($section_id)) {
                if (is_array($section_id) && count($section_id) > 0) {
                    $section_ids = implode(',', array_map('intval', $section_id));
                    $condition .= " AND `student_session`.`section_id` IN (" . $section_ids . ")";
                } elseif (!is_array($section_id)) {
                    $condition .= " AND `student_session`.`section_id` = " . intval($section_id);
                }
            }
            
            // Add date range condition
            $condition .= " AND DATE_FORMAT(student_attendences.date,'%Y-%m-%d') BETWEEN '" . $from_date . "' AND '" . $to_date . "'";

            // Get data from model
            $data = $this->stuattendence_model->getStudentAttendanceTypeReport($condition, $session_id);

            // Get attendance type name for response
            $attendance_types = [
                1 => ['type' => 'Present', 'key' => 'P'],
                2 => ['type' => 'Excuse', 'key' => 'E'],
                3 => ['type' => 'Late', 'key' => 'L'],
                4 => ['type' => 'Absent', 'key' => 'A'],
                5 => ['type' => 'Holiday', 'key' => 'H'],
                6 => ['type' => 'Half Day', 'key' => 'HD']
            ];

            $attendance_type_info = !empty($attendance_type) && isset($attendance_types[$attendance_type]) 
                ? $attendance_types[$attendance_type] 
                : ['type' => 'All Types', 'key' => 'ALL'];

            $filters_applied = [
                'search_type' => !empty($search_type) ? $search_type : 'all',
                'class_id' => $class_id,
                'section_id' => $section_id,
                'session_id' => !empty($session_id) ? (int)$session_id : 'all'
            ];

            // Add attendance type info only if provided
            if (!empty($attendance_type)) {
                $filters_applied['attendance_type'] = intval($attendance_type);
                $filters_applied['attendance_type_name'] = $attendance_type_info['type'];
                $filters_applied['attendance_type_key'] = $attendance_type_info['key'];
            } else {
                $filters_applied['attendance_type'] = 'all';
                $filters_applied['attendance_type_name'] = 'All Types';
                $filters_applied['attendance_type_key'] = 'ALL';
            }

            $response = [
                'status' => true,
                'message' => 'Student attendance type report retrieved successfully',
                'filters_applied' => $filters_applied,
                'date_range' => [
                    'from' => $from_date,
                    'to' => $to_date,
                    'display' => date('d M Y', strtotime($from_date)) . ' To ' . date('d M Y', strtotime($to_date))
                ],
                'total_records' => count($data),
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Student Attendance Type Report API Filter Error: ' . $e->getMessage());
            
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Internal server error occurred',
                    'error' => $e->getMessage(),
                    'data' => null
                ]));
        }
    }

    /**
     * List All Students with Specific Attendance Type
     * 
     * @method POST
     * @route  /api/student-attendance-type-report/list
     * 
     * @param  int $attendance_type Optional. Attendance type ID. If not provided, returns all attendance types
     * @param  string $search_type Optional. Defaults to 'this_week'
     * 
     * @return JSON Response with status, message, session_id, total_records, data, and timestamp
     */
    public function list()
    {
        try {
            if ($this->input->method() !== 'post') {
                $this->output
                    ->set_status_header(400)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => 'Bad request. Only POST method allowed.'
                    ]));
                return;
            }

            if (!$this->auth_model->check_auth_client()) {
                $this->output
                    ->set_status_header(401)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => 'Unauthorized access'
                    ]));
                return;
            }

            $json_input = json_decode(file_get_contents('php://input'), true);
            
            $attendance_type = isset($json_input['attendance_type']) ? $json_input['attendance_type'] : null;
            $search_type = isset($json_input['search_type']) ? $json_input['search_type'] : null;
            $session_id = isset($json_input['session_id']) ? $json_input['session_id'] : null;

            // Session ID is optional - if not provided, will query all sessions
            // No default fallback to current session

            // Get date range
            if (!empty($search_type)) {
                $between_date = $this->customlib->get_betweendate($search_type);
                $from_date = date('Y-m-d', strtotime($between_date['from_date']));
                $to_date = date('Y-m-d', strtotime($between_date['to_date']));
            } else {
                // If no search_type provided, return ALL historical data
                $from_date = date('Y-m-d', strtotime('-10 years'));
                $to_date = date('Y-m-d', strtotime('+10 years'));
            }

            // Build condition - attendance type is optional now
            $condition = '';
            if (!empty($attendance_type)) {
                $condition .= " AND `student_attendences`.`attendence_type_id` = " . intval($attendance_type);
            }
            $condition .= " AND DATE_FORMAT(student_attendences.date,'%Y-%m-%d') BETWEEN '" . $from_date . "' AND '" . $to_date . "'";

            $data = $this->stuattendence_model->getStudentAttendanceTypeReport($condition, $session_id);

            // Get attendance type name
            $attendance_types = [
                1 => ['type' => 'Present', 'key' => 'P'],
                2 => ['type' => 'Excuse', 'key' => 'E'],
                3 => ['type' => 'Late', 'key' => 'L'],
                4 => ['type' => 'Absent', 'key' => 'A'],
                5 => ['type' => 'Holiday', 'key' => 'H'],
                6 => ['type' => 'Half Day', 'key' => 'HD']
            ];

            $attendance_type_info = !empty($attendance_type) && isset($attendance_types[$attendance_type]) 
                ? $attendance_types[$attendance_type] 
                : ['type' => 'All Types', 'key' => 'ALL'];

            $filters_applied = [
                'search_type' => !empty($search_type) ? $search_type : 'all'
            ];

            // Add attendance type info
            if (!empty($attendance_type)) {
                $filters_applied['attendance_type'] = intval($attendance_type);
                $filters_applied['attendance_type_name'] = $attendance_type_info['type'];
                $filters_applied['attendance_type_key'] = $attendance_type_info['key'];
            } else {
                $filters_applied['attendance_type'] = 'all';
                $filters_applied['attendance_type_name'] = 'All Types';
                $filters_applied['attendance_type_key'] = 'ALL';
            }

            $response = [
                'status' => true,
                'message' => 'Student attendance type report retrieved successfully',
                'filters_applied' => $filters_applied,
                'date_range' => [
                    'from' => $from_date,
                    'to' => $to_date,
                    'display' => date('d M Y', strtotime($from_date)) . ' To ' . date('d M Y', strtotime($to_date))
                ],
                'session_id' => !empty($session_id) ? (int)$session_id : 'all',
                'total_records' => count($data),
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Student Attendance Type Report API List Error: ' . $e->getMessage());
            
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Internal server error occurred',
                    'error' => $e->getMessage(),
                    'data' => null
                ]));
        }
    }

    /**
     * Get Available Attendance Types
     * 
     * @method POST
     * @route  /api/student-attendance-type-report/attendance-types
     * 
     * @return JSON Response with list of available attendance types
     */
    public function attendance_types()
    {
        try {
            if (!$this->auth_model->check_auth_client()) {
                $this->output
                    ->set_status_header(401)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => 'Unauthorized access'
                    ]));
                return;
            }

            $attendance_types = [
                ['id' => 1, 'type' => 'Present', 'key' => 'P', 'description' => 'Student was present'],
                ['id' => 2, 'type' => 'Excuse', 'key' => 'E', 'description' => 'Student was excused'],
                ['id' => 3, 'type' => 'Late', 'key' => 'L', 'description' => 'Student was late'],
                ['id' => 4, 'type' => 'Absent', 'key' => 'A', 'description' => 'Student was absent'],
                ['id' => 5, 'type' => 'Holiday', 'key' => 'H', 'description' => 'Holiday'],
                ['id' => 6, 'type' => 'Half Day', 'key' => 'HD', 'description' => 'Student attended half day']
            ];

            $response = [
                'status' => true,
                'message' => 'Attendance types retrieved successfully',
                'total' => count($attendance_types),
                'data' => $attendance_types,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Student Attendance Type Report API Attendance Types Error: ' . $e->getMessage());
            
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Internal server error occurred',
                    'error' => $e->getMessage()
                ]));
        }
    }
}
