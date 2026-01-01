<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Daily Attendance Report API Controller
 * 
 * Provides API endpoints for retrieving daily attendance statistics
 * grouped by class and section with attendance type breakdowns.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Report APIs
 * @author     SMS Development Team
 * @version    1.0.0
 */
class Daily_attendance_report_api extends CI_Controller
{
    /**
     * Constructor
     * 
     * Loads required models and helpers for the API
     */
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
     * Filter Daily Attendance Report
     * 
     * Retrieves daily attendance statistics based on optional filter parameters.
     * Returns aggregated attendance counts (present, absent, late, excuse, half_day) grouped by class and section.
     * 
     * @method POST
     * @route  /api/daily-attendance-report/filter
     * 
     * @param  string     $date         Optional. Specific date for attendance (YYYY-MM-DD format)
     * @param  string     $from_date    Optional. Start date for date range filter
     * @param  string     $to_date      Optional. End date for date range filter
     * @param  int        $session_id   Optional. Session ID (defaults to current session)
     * 
     * @return JSON Response with status, message, filters_applied, total_records, summary, data, and timestamp
     */
    public function filter()
    {
        try {
            // Validate request method
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

            // Validate authentication
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

            // Get JSON input
            $json_input = json_decode(file_get_contents('php://input'), true);
            
            // Extract filter parameters
            $date = isset($json_input['date']) ? $json_input['date'] : null;
            $from_date = isset($json_input['from_date']) ? $json_input['from_date'] : null;
            $to_date = isset($json_input['to_date']) ? $json_input['to_date'] : null;
            $session_id = isset($json_input['session_id']) ? $json_input['session_id'] : null;

            // If session_id is not provided, use current session
            if (empty($session_id)) {
                $session_id = $this->setting_model->getCurrentSession();
            }

            // Get daily attendance report data from model
            $data = $this->stuattendence_model->getDailyAttendanceReportByFilters($date, $from_date, $to_date, $session_id);

            // Calculate summary statistics
            $all_student = 0;
            $all_present = 0;
            $all_absent = 0;

            foreach ($data as $row) {
                $total_present = $row['present'] + $row['excuse'] + $row['late'] + $row['half_day'];
                $total_student = $total_present + $row['absent'];
                $all_student += $total_student;
                $all_present += $total_present;
                $all_absent += $row['absent'];
            }

            // Calculate percentages
            if ($all_student > 0) {
                $all_present_percent = round(($all_present / $all_student) * 100) . "%";
                $all_absent_percent = round(($all_absent / $all_student) * 100) . "%";
            } else {
                $all_present_percent = "0%";
                $all_absent_percent = "0%";
            }

            // Prepare response
            $response = [
                'status' => 1,
                'message' => 'Daily attendance report retrieved successfully',
                'filters_applied' => [
                    'date' => $date,
                    'from_date' => $from_date,
                    'to_date' => $to_date,
                    'session_id' => (int)$session_id
                ],
                'total_records' => count($data),
                'summary' => [
                    'total_students' => $all_student,
                    'total_present' => $all_present,
                    'total_absent' => $all_absent,
                    'present_percentage' => $all_present_percent,
                    'absent_percentage' => $all_absent_percent
                ],
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Daily Attendance Report API Filter Error: ' . $e->getMessage());
            
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
     * List All Daily Attendance Data
     * 
     * Retrieves all daily attendance statistics for the current session.
     * Returns today's attendance data by default.
     * 
     * @method POST
     * @route  /api/daily-attendance-report/list
     * 
     * @return JSON Response with status, message, session_id, total_records, summary, data, and timestamp
     */
    public function list()
    {
        try {
            // Validate request method
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

            // Validate authentication
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

            // Get current session and today's date
            $session_id = $this->setting_model->getCurrentSession();
            $today = date('Y-m-d');

            // Get today's attendance data
            $data = $this->stuattendence_model->getDailyAttendanceReportByFilters($today, null, null, $session_id);

            // Calculate summary statistics
            $all_student = 0;
            $all_present = 0;
            $all_absent = 0;

            foreach ($data as $row) {
                $total_present = $row['present'] + $row['excuse'] + $row['late'] + $row['half_day'];
                $total_student = $total_present + $row['absent'];
                $all_student += $total_student;
                $all_present += $total_present;
                $all_absent += $row['absent'];
            }

            // Calculate percentages
            if ($all_student > 0) {
                $all_present_percent = round(($all_present / $all_student) * 100) . "%";
                $all_absent_percent = round(($all_absent / $all_student) * 100) . "%";
            } else {
                $all_present_percent = "0%";
                $all_absent_percent = "0%";
            }

            // Prepare response
            $response = [
                'status' => 1,
                'message' => 'Daily attendance report retrieved successfully',
                'date' => $today,
                'session_id' => (int)$session_id,
                'total_records' => count($data),
                'summary' => [
                    'total_students' => $all_student,
                    'total_present' => $all_present,
                    'total_absent' => $all_absent,
                    'present_percentage' => $all_present_percent,
                    'absent_percentage' => $all_absent_percent
                ],
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Daily Attendance Report API List Error: ' . $e->getMessage());
            
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

