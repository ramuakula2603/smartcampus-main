<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Online Exam Rank Report API Controller
 * 
 * Provides API endpoints for retrieving online exam rank reports with student rankings
 * based on online exam performance, including question-wise results and scores.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Report APIs
 * @author     SMS Development Team
 * @version    1.0.0
 */
class Online_exam_rank_report_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load required models - IMPORTANT: Load setting_model FIRST
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('onlineexam_model');
        $this->load->model('onlineexamresult_model');
        $this->load->model('class_model');
        
        // Load helpers
        $this->load->helper('url');
        $this->load->helper('security');
    }

    /**
     * Filter Online Exam Rank Report
     * 
     * @method POST
     * @route  /api/online-exam-rank-report/filter
     * 
     * @param  int        $exam_id    Optional. Online exam ID
     * @param  int|array  $class_id   Optional. Class ID(s) to filter by
     * @param  int|array  $section_id Optional. Section ID(s) to filter by
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
            
            $exam_id = isset($json_input['exam_id']) ? $json_input['exam_id'] : null;
            $class_id = isset($json_input['class_id']) ? $json_input['class_id'] : null;
            $section_id = isset($json_input['section_id']) ? $json_input['section_id'] : null;

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

            $data = array();
            $exam = null;
            $student_data = array();

            // If exam_id is provided, fetch detailed rank report
            if (!empty($exam_id)) {
                $exam = $this->onlineexam_model->get($exam_id);
                
                if (!empty($exam)) {
                    // Get student data with results
                    if (!empty($class_id) && !empty($section_id)) {
                        foreach ($class_id as $cls_id) {
                            foreach ($section_id as $sec_id) {
                                $students = $this->onlineexam_model->searchAllOnlineExamStudents($exam_id, $cls_id, $sec_id, 1);
                                
                                if (!empty($students)) {
                                    foreach ($students as $student_key => $student_value) {
                                        $students[$student_key]['questions_results'] = $this->onlineexamresult_model->getResultByStudent($student_value['onlineexam_student_id'], $exam_id);
                                    }
                                    $student_data = array_merge($student_data, $students);
                                }
                            }
                        }
                    } else {
                        // If no class/section specified, get all students for the exam
                        $student_data = $this->onlineexam_model->searchAllOnlineExamStudents($exam_id, null, null, 1);
                        
                        if (!empty($student_data)) {
                            foreach ($student_data as $student_key => $student_value) {
                                $student_data[$student_key]['questions_results'] = $this->onlineexamresult_model->getResultByStudent($student_value['onlineexam_student_id'], $exam_id);
                            }
                        }
                    }
                }
            }

            $response = [
                'status' => 1,
                'message' => 'Online exam rank report retrieved successfully',
                'filters_applied' => [
                    'exam_id' => $exam_id,
                    'class_id' => $class_id,
                    'section_id' => $section_id
                ],
                'exam' => $exam,
                'total_records' => count($student_data),
                'data' => $student_data,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Online Exam Rank Report API Filter Error: ' . $e->getMessage());
            
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
     * List All Online Exam Rank Report Data
     * 
     * @method POST
     * @route  /api/online-exam-rank-report/list
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

            // Get all online exams
            $examList = $this->onlineexam_model->get();

            $response = [
                'status' => 1,
                'message' => 'Online exam rank report data retrieved successfully',
                'exams' => $examList,
                'total_records' => count($examList),
                'note' => 'Use the filter endpoint with exam_id to get detailed rank report for a specific online exam',
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Online Exam Rank Report API List Error: ' . $e->getMessage());
            
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

