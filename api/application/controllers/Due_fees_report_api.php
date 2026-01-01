<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Due Fees Report API Controller
 * 
 * This controller handles API requests for student due fees reports
 * showing students with pending/due fees with filtering by class and section.
 * 
 * @package    CodeIgniter
 * @subpackage Controllers
 * @category   API
 */
class Due_fees_report_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load required models - IMPORTANT: setting_model MUST be loaded FIRST
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('studentfeemaster_model');
        $this->load->model('class_model');
        $this->load->model('student_model');
        $this->load->model('module_model');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->database();
    }

    /**
     * Filter due fees report
     *
     * POST /api/due-fees-report/filter
     *
     * Request body (all parameters optional):
     * {
     *   "class_id": "1",
     *   "section_id": "2",
     *   "session_id": "1"
     * }
     *
     * Empty request body {} returns all students with due fees for current session
     */
    public function filter()
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

            // Get JSON input
            $json_input = json_decode($this->input->raw_input_stream, true);

            // Get filter parameters (all optional)
            // Treat empty strings as null for graceful handling
            $class_id = (isset($json_input['class_id']) && $json_input['class_id'] !== '') ? $json_input['class_id'] : null;
            $section_id = (isset($json_input['section_id']) && $json_input['section_id'] !== '') ? $json_input['section_id'] : null;
            $session_id = (isset($json_input['session_id']) && $json_input['session_id'] !== '') ? $json_input['session_id'] : null;

            // Log filter parameters for debugging
            log_message('debug', 'Due Fees Report API - Filters: class_id=' . ($class_id ?? 'null') .
                                 ', section_id=' . ($section_id ?? 'null') .
                                 ', session_id=' . ($session_id ?? 'null'));

            // Get current date
            $date = date('Y-m-d');

            // Get due fees data
            // When session_id is provided: returns students enrolled in that session with their fees for that session
            // When session_id is null: returns all students with due fees (no session filter)
            $fees_dues = $this->studentfeemaster_model->getStudentDueFeeTypesByDatee($date, $class_id, $section_id, $session_id);

            log_message('debug', 'Due Fees Report API - Raw fees_dues count: ' . count($fees_dues));

            $students_list = array();

            if (!empty($fees_dues)) {
                foreach ($fees_dues as $fee_due_key => $fee_due_value) {
                    $amount_paid = 0;

                    if ($this->isJSON($fee_due_value->amount_detail)) {
                        $student_fees_array = json_decode($fee_due_value->amount_detail);
                        foreach ($student_fees_array as $fee_paid_key => $fee_paid_value) {
                            $amount_paid += ($fee_paid_value->amount + $fee_paid_value->amount_discount);
                        }
                    }

                    if ($amount_paid < $fee_due_value->fee_amount || ($amount_paid < $fee_due_value->amount && $fee_due_value->is_system)) {
                        $students_list[$fee_due_value->student_session_id]['admission_no'] = $fee_due_value->admission_no;
                        $students_list[$fee_due_value->student_session_id]['class_id'] = $fee_due_value->class_id;
                        $students_list[$fee_due_value->student_session_id]['section_id'] = $fee_due_value->section_id;
                        $students_list[$fee_due_value->student_session_id]['student_id'] = $fee_due_value->student_id;
                        $students_list[$fee_due_value->student_session_id]['roll_no'] = $fee_due_value->roll_no;
                        $students_list[$fee_due_value->student_session_id]['admission_date'] = $fee_due_value->admission_date;
                        $students_list[$fee_due_value->student_session_id]['firstname'] = $fee_due_value->firstname;
                        $students_list[$fee_due_value->student_session_id]['middlename'] = $fee_due_value->middlename;
                        $students_list[$fee_due_value->student_session_id]['lastname'] = $fee_due_value->lastname;
                        $students_list[$fee_due_value->student_session_id]['father_name'] = $fee_due_value->father_name;
                        $students_list[$fee_due_value->student_session_id]['image'] = $fee_due_value->image;
                        $students_list[$fee_due_value->student_session_id]['mobileno'] = $fee_due_value->mobileno;
                        $students_list[$fee_due_value->student_session_id]['email'] = $fee_due_value->email;
                        $students_list[$fee_due_value->student_session_id]['state'] = $fee_due_value->state;
                        $students_list[$fee_due_value->student_session_id]['city'] = $fee_due_value->city;
                        $students_list[$fee_due_value->student_session_id]['pincode'] = $fee_due_value->pincode;
                        $students_list[$fee_due_value->student_session_id]['class'] = $fee_due_value->class;
                        $students_list[$fee_due_value->student_session_id]['section'] = $fee_due_value->section;
                        $students_list[$fee_due_value->student_session_id]['fee_groups_feetype_ids'][] = $fee_due_value->fee_groups_feetype_id;
                    }
                }
            }

            if (!empty($students_list)) {
                foreach ($students_list as $student_key => $student_value) {
                    $students_list[$student_key]['fees_list'] = $this->studentfeemaster_model->studentDepositByFeeGroupFeeTypeArray($student_key, $student_value['fee_groups_feetype_ids']);
                    $students_list[$student_key]['transport_fees'] = array();

                    // Get transport fees if transport module is active
                    $transport_fees = [];
                    $module = $this->module_model->getPermissionByModulename('transport');

                    if (!empty($module) && isset($module['is_active']) && $module['is_active']) {
                        // Use student_session_id (student_key) directly instead of calling getByStudentSession
                        // Get route_pickup_point_id from student_session table
                        $student_session_data = $this->db->select('route_pickup_point_id')
                                                         ->from('student_session')
                                                         ->where('id', $student_key)
                                                         ->get()
                                                         ->row_array();

                        if (!empty($student_session_data) && isset($student_session_data['route_pickup_point_id'])) {
                            $route_pickup_point_id = $student_session_data['route_pickup_point_id'];
                            $transport_fees = $this->studentfeemaster_model->getStudentTransportFees($student_key, $route_pickup_point_id);
                        }
                    }

                    $students_list[$student_key]['transport_fees'] = $transport_fees;
                }
            }

            // Build response message based on filters
            $message = 'Due fees report retrieved successfully';
            if ($session_id !== null) {
                $message .= ' for session ID ' . $session_id;
            }
            if ($class_id !== null) {
                $message .= ', class ID ' . $class_id;
            }
            if ($section_id !== null) {
                $message .= ', section ID ' . $section_id;
            }

            $response = [
                'status' => 1,
                'message' => $message,
                'filters_applied' => [
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'session_id' => $session_id,
                    'date' => $date
                ],
                'filter_info' => [
                    'session_filter' => $session_id !== null ? 'Students enrolled in session ' . $session_id . ' with fees for that session' : 'All students with due fees (no session filter)',
                    'class_filter' => $class_id !== null ? 'Class ID ' . $class_id : 'All classes',
                    'section_filter' => $section_id !== null ? 'Section ID ' . $section_id : 'All sections',
                    'due_date_filter' => 'Fees due on or before ' . $date
                ],
                'total_records' => count($students_list),
                'data' => array_values($students_list),
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Due Fees Report API Error: ' . $e->getMessage());
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Internal server error',
                    'error' => $e->getMessage()
                ]));
        }
    }

    /**
     * List due fees filter options
     * 
     * POST /api/due-fees-report/list
     * 
     * Returns available classes for filtering
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

            // Get classes
            $classes = $this->class_model->get();

            $response = [
                'status' => 1,
                'message' => 'Due fees filter options retrieved successfully',
                'classes' => $classes,
                'note' => 'Use the filter endpoint with class_id and section_id to get due fees report',
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Due Fees Report API Error: ' . $e->getMessage());
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Internal server error',
                    'error' => $e->getMessage()
                ]));
        }
    }

    /**
     * Helper function to check if string is JSON
     */
    private function isJSON($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }
}

