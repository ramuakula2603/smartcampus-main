<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Staff Attendance Years API Controller
 * 
 * Provides API endpoint for retrieving available years with staff attendance records
 * for the staff attendance report feature.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Report APIs
 * @author     SMS Development Team
 * @version    1.0.0
 */
class Staff_attendance_years_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load required models
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('staffattendancemodel');
        
        // Load helpers
        $this->load->helper('url');
        $this->load->helper('security');
    }

    /**
     * Get Available Years
     * 
     * Returns a list of distinct years that have staff attendance records in the system.
     * This endpoint is used to populate year dropdowns in the staff attendance report.
     * 
     * @method POST
     * @route  /api/staff-attendance-years/list
     * 
     * @return JSON Response with status, message, total_years, data (array of years), and timestamp
     * 
     * @example Response:
     * {
     *   "status": 1,
     *   "message": "Available staff attendance years retrieved successfully",
     *   "total_years": 3,
     *   "data": [
     *     {"year": "2025"},
     *     {"year": "2024"},
     *     {"year": "2023"}
     *   ],
     *   "timestamp": "2025-10-13 10:30:45"
     * }
     */
    public function list()
    {
        try {
            // Check request method
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

            // Check authentication
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

            // Get available years from the model
            $years = $this->staffattendancemodel->getStaffAttendanceYears();

            // Prepare response
            $response = [
                'status' => 1,
                'message' => 'Available staff attendance years retrieved successfully',
                'total_years' => count($years),
                'data' => $years,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Staff Attendance Years API Error: ' . $e->getMessage());
            
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Internal server error occurred',
                    'error' => $e->getMessage(),
                    'data' => []
                ]));
        }
    }

    /**
     * Get Available Years with Details
     * 
     * Returns a list of years with additional statistics about staff attendance records.
     * 
     * @method POST
     * @route  /api/staff-attendance-years/details
     * 
     * @return JSON Response with status, message, total_years, data (years with record counts), and timestamp
     * 
     * @example Response:
     * {
     *   "status": 1,
     *   "message": "Staff attendance years details retrieved successfully",
     *   "total_years": 3,
     *   "data": [
     *     {
     *       "year": "2025",
     *       "total_records": 8520,
     *       "total_staff": 45,
     *       "earliest_date": "2025-01-01",
     *       "latest_date": "2025-10-13"
     *     }
     *   ],
     *   "timestamp": "2025-10-13 10:30:45"
     * }
     */
    public function details()
    {
        try {
            // Check request method
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

            // Check authentication
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

            // Get available years with details from the model
            $years_details = $this->staffattendancemodel->getStaffAttendanceYearsWithDetails();

            // Prepare response
            $response = [
                'status' => 1,
                'message' => 'Staff attendance years details retrieved successfully',
                'total_years' => count($years_details),
                'data' => $years_details,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Staff Attendance Years Details API Error: ' . $e->getMessage());
            
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Internal server error occurred',
                    'error' => $e->getMessage(),
                    'data' => []
                ]));
        }
    }
}
