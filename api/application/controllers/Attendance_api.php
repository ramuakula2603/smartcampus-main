<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Attendance_api extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('json_output');
        $this->load->model('staffattendancemodel');
    }

    /**
     * Staff Attendance Summary API
     * 
     * @method POST
     * @param staff_id (optional) - Specific staff member ID
     * @param from_date (optional) - Start date (YYYY-MM-DD)
     * @param to_date (optional) - End date (YYYY-MM-DD)
     * 
     * @return JSON response with attendance summary
     */
    public function summary()
    {
        $method = $this->input->server('REQUEST_METHOD');

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request. Only POST method allowed.'));
            return;
        }

        try {
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

            // Validate date formats if provided
            if ($from_date && !$this->isValidDate($from_date)) {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Invalid from_date format. Use YYYY-MM-DD format.'
                ));
                return;
            }

            if ($to_date && !$this->isValidDate($to_date)) {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Invalid to_date format. Use YYYY-MM-DD format.'
                ));
                return;
            }

            // Get attendance data from model
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
            // Log the error for debugging
            log_message('error', 'Attendance API Error: ' . $e->getMessage());
            
            json_output(500, array(
                'status' => 500,
                'message' => 'Internal server error occurred while processing the request.'
            ));
        }
    }

    /**
     * Validate date format (YYYY-MM-DD)
     */
    private function isValidDate($date)
    {
        if (empty($date)) {
            return false;
        }
        $d = date_create_from_format('Y-m-d', $date);
        return $d && date_format($d, 'Y-m-d') === $date;
    }
}
