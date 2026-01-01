<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Total Student Academic Report API Controller
 * 
 * Provides API endpoints for total student academic fee reports
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Finance Reports
 * @author     SMS Development Team
 * @version    1.0.0
 */
class Total_student_academic_report_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Disable error display - API should only return JSON
        ini_set('display_errors', 0);
        error_reporting(0);

        // Load required models in correct order
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('studentfeemaster_model');
        $this->load->model('student_model');
        $this->load->model('class_model');
        $this->load->model('session_model');

        // Load library
        $this->load->library('customlib');

        // Load helper
        $this->load->helper('custom');
    }

    /**
     * Filter endpoint - Get total student academic report with filters
     * POST /api/total-student-academic-report/filter
     */
    public function filter()
    {
        // Authenticate request
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

        try {
            // Get JSON input
            $json_input = json_decode($this->input->raw_input_stream, true);
            
            // Get filter parameters (all optional)
            $class_id = (isset($json_input['class_id']) && $json_input['class_id'] !== '') ? $json_input['class_id'] : null;
            $section_id = (isset($json_input['section_id']) && $json_input['section_id'] !== '') ? $json_input['section_id'] : null;
            $session_id = (isset($json_input['session_id']) && $json_input['session_id'] !== '') ? $json_input['session_id'] : null;

            // Get student list based on filters
            if ($class_id !== null) {
                $studentlist = $this->student_model->totalsearchByClassSectionWithSession($session_id, $class_id, $section_id);
            } else {
                $studentlist = $this->student_model->gettotalStudents($session_id);
            }

            $student_array = array();
            if (!empty($studentlist)) {
                foreach ($studentlist as $key => $eachstudent) {
                    $obj = new stdClass();
                    $obj->name = $eachstudent['firstname'] . ' ' . ($eachstudent['middlename'] ?? '') . ' ' . $eachstudent['lastname'];
                    $obj->class = $eachstudent['class'];
                    $obj->section = $eachstudent['section'];
                    $obj->admission_no = $eachstudent['admission_no'];
                    $obj->roll_no = $eachstudent['roll_no'] ?? '';
                    $obj->father_name = $eachstudent['father_name'] ?? '';
                    $student_session_id = $eachstudent['student_session_id'];
                    
                    // Get student fees
                    $student_total_fees = $this->studentfeemaster_model->getTransStudentFees($student_session_id);
                    
                    $totalfee = 0;
                    $deposit = 0;
                    $discount = 0;
                    $balance = 0;
                    $fine = 0;
                    
                    if (!empty($student_total_fees)) {
                        foreach ($student_total_fees as $student_total_fees_value) {
                            if (!empty($student_total_fees_value->fees)) {
                                foreach ($student_total_fees_value->fees as $each_fee_value) {
                                    // Add the fee amount to total
                                    $totalfee += isset($each_fee_value->amount) ? $each_fee_value->amount : 0;

                                    // Parse amount_detail JSON to get deposit, discount, and fine
                                    if (isset($each_fee_value->amount_detail) && !empty($each_fee_value->amount_detail)) {
                                        $amount_detail = json_decode($each_fee_value->amount_detail);

                                        if (is_object($amount_detail) && !empty($amount_detail)) {
                                            foreach ($amount_detail as $amount_detail_value) {
                                                $deposit += isset($amount_detail_value->amount) ? $amount_detail_value->amount : 0;
                                                $fine += isset($amount_detail_value->amount_fine) ? $amount_detail_value->amount_fine : 0;
                                                $discount += isset($amount_detail_value->amount_discount) ? $amount_detail_value->amount_discount : 0;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    // Balance calculation: Total Fee - (Deposit + Discount)
                    // This matches the web page calculation
                    $balance = $totalfee - ($deposit + $discount);
                    
                    $obj->total_fee = number_format($totalfee, 2);
                    $obj->deposit = number_format($deposit, 2);
                    $obj->discount = number_format($discount, 2);
                    $obj->fine = number_format($fine, 2);
                    $obj->balance = number_format($balance, 2);
                    
                    $student_array[] = $obj;
                }
            }

            $response = [
                'status' => 1,
                'message' => 'Total student academic report retrieved successfully',
                'filters_applied' => [
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'session_id' => $session_id
                ],
                'total_records' => count($student_array),
                'data' => $student_array,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Error retrieving total student academic report: ' . $e->getMessage()
                ]));
        }
    }

    /**
     * List endpoint - Get filter options
     * POST /api/total-student-academic-report/list
     */
    public function list()
    {
        // Authenticate request
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

        try {
            // Get classes
            $classes = $this->class_model->get();
            
            // Get sessions
            $sessions = $this->session_model->get();

            $response = [
                'status' => 1,
                'message' => 'Filter options retrieved successfully',
                'data' => [
                    'classes' => $classes,
                    'sessions' => $sessions
                ],
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Error retrieving filter options: ' . $e->getMessage()
                ]));
        }
    }
}

