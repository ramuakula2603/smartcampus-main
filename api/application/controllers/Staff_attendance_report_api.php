<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Staff Attendance Report API Controller
 * 
 * Provides API endpoints for retrieving staff attendance records
 * with filtering capabilities by role, date range, and staff ID.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Report APIs
 * @author     SMS Development Team
 * @version    1.0.0
 */
class Staff_attendance_report_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load required models - IMPORTANT: Load setting_model FIRST
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('staffattendancemodel');
        
        // Load helpers
        $this->load->helper('url');
        $this->load->helper('security');
    }

    /**
     * Filter Staff Attendance Report
     * 
     * @method POST
     * @route  /api/staff-attendance-report/filter
     * 
     * @param  int|array  $role_id    Optional. Role ID(s) to filter by
     * @param  string     $from_date  Optional. Start date for date range filter
     * @param  string     $to_date    Optional. End date for date range filter
     * @param  int|array  $staff_id   Optional. Staff ID(s) to filter by
     * @param  int        $session_id Optional. Session ID (defaults to current session)
     * 
     * @return JSON Response with status, message, filters_applied, total_records, data, and timestamp
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
            
            $role_id = isset($json_input['role_id']) ? $json_input['role_id'] : null;
            $from_date = isset($json_input['from_date']) ? $json_input['from_date'] : null;
            $to_date = isset($json_input['to_date']) ? $json_input['to_date'] : null;
            $staff_id = isset($json_input['staff_id']) ? $json_input['staff_id'] : null;
            $session_id = isset($json_input['session_id']) ? $json_input['session_id'] : null;

            // Convert single values to arrays for consistent handling
            if (!is_array($role_id)) {
                $role_id = !empty($role_id) ? array($role_id) : array();
            }
            if (!is_array($staff_id)) {
                $staff_id = !empty($staff_id) ? array($staff_id) : array();
            }

            // Filter out empty values from arrays
            $role_id = array_filter($role_id, function($value) { 
                return !empty($value) && $value !== null && $value !== ''; 
            });
            $staff_id = array_filter($staff_id, function($value) { 
                return !empty($value) && $value !== null && $value !== ''; 
            });

            // Convert empty arrays to null for graceful handling
            $role_id = !empty($role_id) ? $role_id : null;
            $staff_id = !empty($staff_id) ? $staff_id : null;

            // If session_id is not provided, use current session
            if (empty($session_id)) {
                $session_id = $this->setting_model->getCurrentSession();
            }

            $data = $this->staffattendancemodel->getStaffAttendanceReportByFilters($role_id, $from_date, $to_date, $staff_id, $session_id);

            $response = [
                'status' => 1,
                'message' => 'Staff attendance report retrieved successfully',
                'filters_applied' => [
                    'role_id' => $role_id,
                    'from_date' => $from_date,
                    'to_date' => $to_date,
                    'staff_id' => $staff_id,
                    'session_id' => (int)$session_id
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
            log_message('error', 'Staff Attendance Report API Filter Error: ' . $e->getMessage());
            
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
     * List All Staff Attendance Data
     * 
     * @method POST
     * @route  /api/staff-attendance-report/list
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
            $data = $this->staffattendancemodel->getStaffAttendanceReportByFilters(null, null, null, null, $session_id);

            $response = [
                'status' => 1,
                'message' => 'Staff attendance report retrieved successfully',
                'session_id' => (int)$session_id,
                'total_records' => count($data),
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Staff Attendance Report API List Error: ' . $e->getMessage());
            
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

