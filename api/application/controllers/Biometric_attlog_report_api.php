<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Biometric Attendance Log Report API Controller
 * 
 * Provides API endpoints for retrieving biometric attendance log records
 * with student details and filtering capabilities.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Report APIs
 * @author     SMS Development Team
 * @version    1.0.0
 */
class Biometric_attlog_report_api extends CI_Controller
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
     * Filter Biometric Attendance Log Report
     * 
     * @method POST
     * @route  /api/biometric-attlog-report/filter
     * 
     * @param  string     $from_date    Optional. Start date for date range filter
     * @param  string     $to_date      Optional. End date for date range filter
     * @param  int|array  $student_id   Optional. Student ID(s) to filter by
     * @param  int        $limit        Optional. Limit for pagination (default: 100)
     * @param  int        $offset       Optional. Offset for pagination (default: 0)
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
            
            $from_date = isset($json_input['from_date']) ? $json_input['from_date'] : null;
            $to_date = isset($json_input['to_date']) ? $json_input['to_date'] : null;
            $student_id = isset($json_input['student_id']) ? $json_input['student_id'] : null;
            $limit = isset($json_input['limit']) ? intval($json_input['limit']) : 100;
            $offset = isset($json_input['offset']) ? intval($json_input['offset']) : 0;

            // Convert single values to arrays for consistent handling
            if (!is_array($student_id) && !empty($student_id)) {
                $student_id = array($student_id);
            }

            // Filter out empty values from arrays
            if (is_array($student_id)) {
                $student_id = array_filter($student_id, function($value) { 
                    return !empty($value) && $value !== null && $value !== ''; 
                });
                $student_id = !empty($student_id) ? $student_id : null;
            }

            $data = $this->stuattendence_model->getBiometricAttlogReportByFilters($from_date, $to_date, $student_id, $limit, $offset);
            $total_count = $this->stuattendence_model->countBiometricAttlogReportByFilters($from_date, $to_date, $student_id);

            $response = [
                'status' => 1,
                'message' => 'Biometric attendance log report retrieved successfully',
                'filters_applied' => [
                    'from_date' => $from_date,
                    'to_date' => $to_date,
                    'student_id' => $student_id,
                    'limit' => $limit,
                    'offset' => $offset
                ],
                'total_records' => $total_count,
                'returned_records' => count($data),
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Biometric Attlog Report API Filter Error: ' . $e->getMessage());
            
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
     * List All Biometric Attendance Log Data
     * 
     * @method POST
     * @route  /api/biometric-attlog-report/list
     * 
     * @return JSON Response with status, message, total_records, data, and timestamp
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

            $data = $this->stuattendence_model->getBiometricAttlogReportByFilters(null, null, null, 100, 0);
            $total_count = $this->stuattendence_model->countBiometricAttlogReportByFilters(null, null, null);

            $response = [
                'status' => 1,
                'message' => 'Biometric attendance log report retrieved successfully',
                'total_records' => $total_count,
                'returned_records' => count($data),
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Biometric Attlog Report API List Error: ' . $e->getMessage());
            
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

