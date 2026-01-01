<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Online Exam Attend Report API Controller
 * 
 * Provides API endpoints for retrieving online exam attendance reports showing
 * which students have attempted which online exams.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Report APIs
 * @author     SMS Development Team
 * @version    1.0.0
 */
class Online_exam_attend_report_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load required models - IMPORTANT: Load setting_model FIRST
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('onlineexam_model');
        $this->load->model('class_model');
        
        // Load helpers
        $this->load->helper('url');
        $this->load->helper('security');
    }

    /**
     * Filter Online Exam Attend Report
     * 
     * @method POST
     * @route  /api/online-exam-attend-report/filter
     * 
     * @param  string $from_date Optional. Start date for date range filter
     * @param  string $to_date   Optional. End date for date range filter
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

            // Build condition for date range
            $condition = '';
            if (!empty($from_date) && !empty($to_date)) {
                $condition = " date_format(onlineexam.created_at,'%Y-%m-%d') between '" . $this->db->escape_str($from_date) . "' and '" . $this->db->escape_str($to_date) . "'";
            } elseif (!empty($from_date)) {
                $condition = " date_format(onlineexam.created_at,'%Y-%m-%d') >= '" . $this->db->escape_str($from_date) . "'";
            } elseif (!empty($to_date)) {
                $condition = " date_format(onlineexam.created_at,'%Y-%m-%d') <= '" . $this->db->escape_str($to_date) . "'";
            } else {
                // Default to current year if no dates provided
                $condition = " date_format(onlineexam.created_at,'%Y-%m-%d') between '" . date('Y') . "-01-01' and '" . date('Y') . "-12-31'";
            }

            // Get online exam attendance report data
            $result = $this->onlineexam_model->onlineexamatteptreport($condition);
            $resultlist = json_decode($result);
            
            $data = array();
            if (!empty($resultlist->data)) {
                foreach ($resultlist->data as $student_value) {
                    $exams = explode(',', $student_value->exams);
                    $exam_details = array();
                    
                    foreach ($exams as $exam) {
                        $exam_parts = explode('@', $exam);
                        if (count($exam_parts) >= 9) {
                            $exam_details[] = array(
                                'exam_id' => $exam_parts[0],
                                'exam_name' => $exam_parts[1],
                                'attempt' => $exam_parts[2],
                                'exam_from' => $exam_parts[3],
                                'exam_to' => $exam_parts[4],
                                'duration' => $exam_parts[5],
                                'passing_percentage' => $exam_parts[6],
                                'is_active' => $exam_parts[7],
                                'publish_result' => $exam_parts[8]
                            );
                        }
                    }
                    
                    $data[] = array(
                        'student_session_id' => $student_value->student_session_id,
                        'student_id' => $student_value->sid,
                        'admission_no' => $student_value->admission_no,
                        'name' => $student_value->name,
                        'firstname' => $student_value->firstname,
                        'middlename' => $student_value->middlename,
                        'lastname' => $student_value->lastname,
                        'class_id' => $student_value->class_id,
                        'class' => $student_value->class,
                        'section_id' => $student_value->section_id,
                        'section' => $student_value->section,
                        'exams' => $exam_details,
                        'total_exams_attempted' => count($exam_details)
                    );
                }
            }

            $response = [
                'status' => 1,
                'message' => 'Online exam attend report retrieved successfully',
                'filters_applied' => [
                    'from_date' => $from_date,
                    'to_date' => $to_date
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
            log_message('error', 'Online Exam Attend Report API Filter Error: ' . $e->getMessage());
            
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
     * List All Online Exam Attend Report Data
     * 
     * @method POST
     * @route  /api/online-exam-attend-report/list
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

            // Default to current year
            $condition = " date_format(onlineexam.created_at,'%Y-%m-%d') between '" . date('Y') . "-01-01' and '" . date('Y') . "-12-31'";
            
            // Get online exam attendance report data
            $result = $this->onlineexam_model->onlineexamatteptreport($condition);
            $resultlist = json_decode($result);
            
            $data = array();
            if (!empty($resultlist->data)) {
                foreach ($resultlist->data as $student_value) {
                    $exams = explode(',', $student_value->exams);
                    $exam_details = array();
                    
                    foreach ($exams as $exam) {
                        $exam_parts = explode('@', $exam);
                        if (count($exam_parts) >= 9) {
                            $exam_details[] = array(
                                'exam_id' => $exam_parts[0],
                                'exam_name' => $exam_parts[1],
                                'attempt' => $exam_parts[2],
                                'exam_from' => $exam_parts[3],
                                'exam_to' => $exam_parts[4],
                                'duration' => $exam_parts[5],
                                'passing_percentage' => $exam_parts[6],
                                'is_active' => $exam_parts[7],
                                'publish_result' => $exam_parts[8]
                            );
                        }
                    }
                    
                    $data[] = array(
                        'student_session_id' => $student_value->student_session_id,
                        'student_id' => $student_value->sid,
                        'admission_no' => $student_value->admission_no,
                        'name' => $student_value->name,
                        'firstname' => $student_value->firstname,
                        'middlename' => $student_value->middlename,
                        'lastname' => $student_value->lastname,
                        'class_id' => $student_value->class_id,
                        'class' => $student_value->class,
                        'section_id' => $student_value->section_id,
                        'section' => $student_value->section,
                        'exams' => $exam_details,
                        'total_exams_attempted' => count($exam_details)
                    );
                }
            }

            $response = [
                'status' => 1,
                'message' => 'Online exam attend report retrieved successfully',
                'year' => date('Y'),
                'total_records' => count($data),
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Online Exam Attend Report API List Error: ' . $e->getMessage());
            
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

