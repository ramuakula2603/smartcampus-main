<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Attendance Report API Controller
 * 
 * Provides API endpoints for retrieving class-wise monthly attendance statistics
 * with detailed attendance breakdown by student.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Report APIs
 * @author     SMS Development Team
 * @version    1.0.0
 */
class Class_attendance_report_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load required models - IMPORTANT: Load setting_model FIRST
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('stuattendence_model');
        
        // Load helpers
        $this->load->helper('url');
        $this->load->helper('security');
    }

    /**
     * Filter Class Attendance Report
     * 
     * @method POST
     * @route  /api/class-attendance-report/filter
     * 
     * @param  int|array  $class_id    Optional. Class ID(s) to filter by
     * @param  int|array  $section_id  Optional. Section ID(s) to filter by
     * @param  int        $month       Optional. Month number (1-12, defaults to current month)
     * @param  int        $year        Optional. Year (YYYY format, defaults to current year)
     * @param  int        $session_id  Optional. Session ID (defaults to current session)
     * 
     * @return JSON Response with status, message, filters_applied, total_records, summary, data, and timestamp
     */
    public function filter()
    {
        try {
            if ($this->input->method() !== 'post') {
                $this->output
                    ->set_status_header(400)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 0,
                        'message' => 'Bad request. Only POST method allowed.'
                    ]));
                return;
            }

            if (!$this->auth_model->check_auth_client()) {
                $this->output
                    ->set_status_header(401)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 0,
                        'message' => 'Unauthorized access'
                    ]));
                return;
            }

            $json_input = json_decode(file_get_contents('php://input'), true);
            
            $class_id = isset($json_input['class_id']) ? $json_input['class_id'] : null;
            $section_id = isset($json_input['section_id']) ? $json_input['section_id'] : null;
            $month = isset($json_input['month']) ? intval($json_input['month']) : date('m');
            $year = isset($json_input['year']) ? intval($json_input['year']) : date('Y');
            $session_id = isset($json_input['session_id']) ? $json_input['session_id'] : null;

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

            // If session_id is not provided, use current session
            if (empty($session_id)) {
                $session_id = $this->setting_model->getCurrentSession();
            }

            $data = $this->stuattendence_model->getClassAttendanceReportByFilters($class_id, $section_id, $month, $year, $session_id);

            // Calculate summary statistics
            $total_students = count($data);
            $total_present = 0;
            $total_absent = 0;
            $total_days = 0;

            foreach ($data as $row) {
                $total_present += $row['total_present'];
                $total_absent += $row['absent_count'];
                $total_days += $row['total_days'];
            }

            $response = [
                'status' => 1,
                'message' => 'Class attendance report retrieved successfully',
                'filters_applied' => [
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'month' => $month,
                    'year' => $year,
                    'session_id' => (int)$session_id
                ],
                'total_records' => $total_students,
                'summary' => [
                    'total_students' => $total_students,
                    'total_present' => $total_present,
                    'total_absent' => $total_absent,
                    'total_attendance_days' => $total_days
                ],
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Class Attendance Report API Filter Error: ' . $e->getMessage());
            
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Internal server error occurred',
                    'error' => $e->getMessage(),
                    'data' => null
                ]));
        }
    }

    /**
     * List All Class Attendance Data
     * 
     * @method POST
     * @route  /api/class-attendance-report/list
     * 
     * @return JSON Response with status, message, session_id, month, year, total_records, summary, data, and timestamp
     */
    public function list()
    {
        try {
            if ($this->input->method() !== 'post') {
                $this->output
                    ->set_status_header(400)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 0,
                        'message' => 'Bad request. Only POST method allowed.'
                    ]));
                return;
            }

            if (!$this->auth_model->check_auth_client()) {
                $this->output
                    ->set_status_header(401)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 0,
                        'message' => 'Unauthorized access'
                    ]));
                return;
            }

            $session_id = $this->setting_model->getCurrentSession();
            $month = date('m');
            $year = date('Y');
            
            $data = $this->stuattendence_model->getClassAttendanceReportByFilters(null, null, $month, $year, $session_id);

            // Calculate summary statistics
            $total_students = count($data);
            $total_present = 0;
            $total_absent = 0;
            $total_days = 0;

            foreach ($data as $row) {
                $total_present += $row['total_present'];
                $total_absent += $row['absent_count'];
                $total_days += $row['total_days'];
            }

            $response = [
                'status' => 1,
                'message' => 'Class attendance report retrieved successfully',
                'session_id' => (int)$session_id,
                'month' => (int)$month,
                'year' => (int)$year,
                'total_records' => $total_students,
                'summary' => [
                    'total_students' => $total_students,
                    'total_present' => $total_present,
                    'total_absent' => $total_absent,
                    'total_attendance_days' => $total_days
                ],
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Class Attendance Report API List Error: ' . $e->getMessage());
            
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Internal server error occurred',
                    'error' => $e->getMessage(),
                    'data' => null
                ]));
        }
    }
}

