<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Evaluation Report API Controller
 * 
 * This controller handles API requests for homework evaluation reports
 * showing homework evaluation status with filtering by class, section, subject group, and subject.
 * 
 * @package    CodeIgniter
 * @subpackage Controllers
 * @category   API
 */
class Evaluation_report_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load required models - IMPORTANT: setting_model MUST be loaded FIRST
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('homework_model');
        $this->load->model('class_model');
        $this->load->model('subjectgroup_model');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->database();
    }

    /**
     * Filter evaluation report
     * 
     * POST /api/evaluation-report/filter
     * 
     * Request body (all parameters optional):
     * {
     *   "class_id": 10,
     *   "section_id": 5,
     *   "subject_group_id": 3,
     *   "subject_id": 15,
     *   "session_id": 21
     * }
     * 
     * Empty request body {} returns all homework evaluation data
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
            $class_id = isset($json_input['class_id']) ? $json_input['class_id'] : null;
            $section_id = isset($json_input['section_id']) ? $json_input['section_id'] : null;
            $subject_group_id = isset($json_input['subject_group_id']) ? $json_input['subject_group_id'] : null;
            $subject_id = isset($json_input['subject_id']) ? $json_input['subject_id'] : null;
            $session_id = isset($json_input['session_id']) ? $json_input['session_id'] : $this->setting_model->getCurrentSession();

            // Get homework data
            $resultlist = $this->homework_model->search_homework($class_id, $section_id, $subject_group_id, $subject_id, $session_id);
            
            // Calculate evaluation percentages for each homework
            $report = array();
            foreach ($resultlist as $key => $value) {
                $evaluation_data = $this->calculateEvaluationPercentage($value["id"], $value["class_id"], $value["section_id"], $session_id);
                $resultlist[$key]['evaluation_report'] = $evaluation_data;
                $report[$value['id']] = $evaluation_data;
            }

            $response = [
                'status' => 1,
                'message' => 'Evaluation report retrieved successfully',
                'filters_applied' => [
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'subject_group_id' => $subject_group_id,
                    'subject_id' => $subject_id,
                    'session_id' => (int)$session_id
                ],
                'total_records' => count($resultlist),
                'data' => $resultlist,
                'evaluation_summary' => $report,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Evaluation Report API Error: ' . $e->getMessage());
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
     * List evaluation report filter options
     * 
     * POST /api/evaluation-report/list
     * 
     * Returns available classes, sections, subject groups, and subjects for filtering
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
            
            // Get session
            $session_id = $this->setting_model->getCurrentSession();

            $response = [
                'status' => 1,
                'message' => 'Evaluation report filter options retrieved successfully',
                'total_classes' => count($classes),
                'classes' => $classes,
                'current_session_id' => (int)$session_id,
                'note' => 'Use the filter endpoint with class_id, section_id, subject_group_id, and subject_id to get evaluation report',
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Evaluation Report API Error: ' . $e->getMessage());
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
     * Calculate evaluation percentage for a homework
     */
    private function calculateEvaluationPercentage($homework_id, $class_id, $section_id, $session_id)
    {
        // Get total students in class/section
        $this->db->select('COUNT(DISTINCT students.id) as total_students');
        $this->db->from('student_session');
        $this->db->join('students', 'students.id = student_session.student_id');
        $this->db->where('student_session.class_id', $class_id);
        $this->db->where('student_session.section_id', $section_id);
        $this->db->where('student_session.session_id', $session_id);
        $this->db->where('students.is_active', 'yes');
        $query = $this->db->get();
        $total_students = $query->row()->total_students;

        // Get evaluated count
        $this->db->select('COUNT(DISTINCT homework_evaluation.student_session_id) as evaluated_count');
        $this->db->from('homework_evaluation');
        $this->db->where('homework_evaluation.homework_id', $homework_id);
        $query = $this->db->get();
        $evaluated_count = $query->row()->evaluated_count;

        // Get submitted count
        $this->db->select('COUNT(DISTINCT submit_assignment.student_id) as submitted_count');
        $this->db->from('submit_assignment');
        $this->db->where('submit_assignment.homework_id', $homework_id);
        $query = $this->db->get();
        $submitted_count = $query->row()->submitted_count;

        $evaluated_percentage = $total_students > 0 ? round(($evaluated_count / $total_students) * 100, 2) : 0;
        $submitted_percentage = $total_students > 0 ? round(($submitted_count / $total_students) * 100, 2) : 0;
        $pending_count = $total_students - $evaluated_count;

        return array(
            'total_students' => $total_students,
            'evaluated_count' => $evaluated_count,
            'submitted_count' => $submitted_count,
            'pending_count' => $pending_count,
            'evaluated_percentage' => $evaluated_percentage,
            'submitted_percentage' => $submitted_percentage
        );
    }
}

