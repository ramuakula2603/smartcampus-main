<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Teacher_webservice_minimal extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        
        // Load only essential models
        $this->load->model('teacher_auth_model');
        $this->load->helper('json_output');
    }

    /**
     * Test method
     */
    public function test()
    {
        json_output(200, array(
            'status' => 1,
            'message' => 'Minimal Teacher webservice test successful',
            'timestamp' => date('Y-m-d H:i:s')
        ));
    }

    /**
     * Attendance summary with minimal dependencies
     */
    public function attendance_summary()
    {
        try {
            $method = $this->input->server('REQUEST_METHOD');

            if ($method != 'POST') {
                json_output(400, array('status' => 400, 'message' => 'Bad request. Only POST method allowed.'));
                return;
            }

            $check_auth_client = $this->teacher_auth_model->check_auth_client();
            if (!$check_auth_client) {
                json_output(401, array('status' => 401, 'message' => 'Unauthorized. Please check Client-Service and Auth-Key headers.'));
                return;
            }

            // Load required models
            $this->load->model('staffattendancemodel');

            // Get request parameters
            $params = json_decode(file_get_contents('php://input'), true);

            // Validate JSON input
            if (json_last_error() !== JSON_ERROR_NONE) {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Invalid JSON format in request body.'
                ));
                return;
            }

            // Extract parameters with defaults
            $staff_id = isset($params['staff_id']) ? (int)$params['staff_id'] : null;
            $from_date = isset($params['from_date']) ? trim($params['from_date']) : null;
            $to_date = isset($params['to_date']) ? trim($params['to_date']) : null;

            // Get attendance summary data
            $attendance_data = $this->staffattendancemodel->getAttendanceSummary($staff_id, $from_date, $to_date);

            // Check for errors in the model response
            if (isset($attendance_data['error'])) {
                json_output(400, array(
                    'status' => 400,
                    'message' => $attendance_data['error']
                ));
                return;
            }

            // Prepare successful response
            $response = array(
                'status' => 1,
                'message' => 'Attendance summary retrieved successfully.',
                'data' => $attendance_data,
                'request_info' => array(
                    'staff_id' => $staff_id,
                    'from_date' => $from_date ?: date('Y-01-01'),
                    'to_date' => $to_date ?: date('Y-12-31'),
                    'generated_at' => date('Y-m-d H:i:s')
                )
            );

            json_output(200, $response);

        } catch (Exception $e) {
            json_output(500, array(
                'status' => 500,
                'message' => 'Internal server error: ' . $e->getMessage()
            ));
        }
    }
}
