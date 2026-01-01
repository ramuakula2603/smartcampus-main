<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Rank Report API Controller
 * 
 * Provides API endpoints for retrieving exam rank reports with student rankings
 * based on exam performance, including subject-wise results and overall rankings.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Report APIs
 * @author     SMS Development Team
 * @version    1.0.0
 */
class Rank_report_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load required models - IMPORTANT: Load setting_model FIRST
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('examgroup_model');
        $this->load->model('examgroupstudent_model');
        $this->load->model('examresult_model');
        $this->load->model('batchsubject_model');
        $this->load->model('grade_model');
        
        // Load helpers
        $this->load->helper('url');
        $this->load->helper('security');
    }

    /**
     * Filter Rank Report
     * 
     * @method POST
     * @route  /api/rank-report/filter
     * 
     * @param  int        $exam_group_id Optional. Exam group ID
     * @param  int        $exam_id       Optional. Exam ID
     * @param  int|array  $class_id      Optional. Class ID(s) to filter by
     * @param  int|array  $section_id    Optional. Section ID(s) to filter by
     * @param  int        $session_id    Optional. Session ID (defaults to current session)
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
            
            $exam_group_id = isset($json_input['exam_group_id']) ? $json_input['exam_group_id'] : null;
            $exam_id = isset($json_input['exam_id']) ? $json_input['exam_id'] : null;
            $class_id = isset($json_input['class_id']) ? $json_input['class_id'] : null;
            $section_id = isset($json_input['section_id']) ? $json_input['section_id'] : null;
            $session_id = isset($json_input['session_id']) ? $json_input['session_id'] : null;

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

            // If session_id is not provided, use current session
            if (empty($session_id)) {
                $session_id = $this->setting_model->getCurrentSession();
            }

            $data = array();
            $exam_details = null;
            $exam_subjects = array();

            // If exam_id is provided, fetch detailed rank report
            if (!empty($exam_id)) {
                $exam_details = $this->examgroup_model->getExamByID($exam_id);
                
                if (!empty($exam_details)) {
                    // Get exam subjects
                    $exam_subjects = $this->batchsubject_model->getExamSubjects($exam_id);
                    
                    // Get exam grades
                    $exam_grades = $this->grade_model->getByExamType($exam_details->exam_group_type);
                    
                    // Get student list with results
                    if (!empty($class_id) && !empty($section_id)) {
                        foreach ($class_id as $cls_id) {
                            foreach ($section_id as $sec_id) {
                                $studentList = $this->examgroupstudent_model->searchExamStudents($exam_group_id, $exam_id, $cls_id, $sec_id, $session_id);
                                
                                if (!empty($studentList)) {
                                    foreach ($studentList as $student_key => $student_value) {
                                        $studentList[$student_key]->subject_results = $this->examresult_model->getStudentResultByExam($exam_id, $student_value->exam_group_class_batch_exam_student_id);
                                    }
                                    $data = array_merge($data, $studentList);
                                }
                            }
                        }
                    } else {
                        // If no class/section specified, get all students for the exam
                        $studentList = $this->examgroupstudent_model->searchExamStudents($exam_group_id, $exam_id, null, null, $session_id);
                        
                        if (!empty($studentList)) {
                            foreach ($studentList as $student_key => $student_value) {
                                $studentList[$student_key]->subject_results = $this->examresult_model->getStudentResultByExam($exam_id, $student_value->exam_group_class_batch_exam_student_id);
                            }
                            $data = $studentList;
                        }
                    }
                }
            }

            $response = [
                'status' => 1,
                'message' => 'Rank report retrieved successfully',
                'filters_applied' => [
                    'exam_group_id' => $exam_group_id,
                    'exam_id' => $exam_id,
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'session_id' => (int)$session_id
                ],
                'exam_details' => $exam_details,
                'exam_subjects' => $exam_subjects,
                'total_records' => count($data),
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Rank Report API Filter Error: ' . $e->getMessage());
            
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
     * List All Rank Report Data
     * 
     * @method POST
     * @route  /api/rank-report/list
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
            
            // Get all exam groups for current session
            $examgroup_result = $this->examgroup_model->get();

            $response = [
                'status' => 1,
                'message' => 'Rank report data retrieved successfully',
                'session_id' => (int)$session_id,
                'exam_groups' => $examgroup_result,
                'total_records' => count($examgroup_result),
                'note' => 'Use the filter endpoint with exam_id to get detailed rank report for a specific exam',
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Rank Report API List Error: ' . $e->getMessage());
            
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

