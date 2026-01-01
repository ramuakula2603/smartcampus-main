<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Lesson Plan Report API Controller
 * 
 * This controller handles API requests for lesson plan reports
 * showing subject completion status by class, section, and subject group.
 * 
 * @package    CodeIgniter
 * @subpackage Controllers
 * @category   API
 */
class Lesson_plan_report_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load required models - IMPORTANT: setting_model MUST be loaded FIRST
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('class_model');
        $this->load->model('subjectgroup_model');
        $this->load->model('syllabus_model');
        $this->load->model('lessonplan_model');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->database();
    }

    /**
     * Filter lesson plan report
     *
     * POST /api/lesson-plan-report/filter
     *
     * Request body (all parameters optional):
     * {
     *   "class_id": 1,
     *   "section_id": 2,
     *   "subject_group_id": 3,
     *   "session_id": 18
     * }
     *
     * Empty request body {} returns all available subject groups
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
            $session_id = isset($json_input['session_id']) ? $json_input['session_id'] : null;

            // If session_id is not provided, use current session
            if (empty($session_id)) {
                $session_id = $this->setting_model->getCurrentSession();
            }

            // Graceful handling: If no filters provided, return all available lesson plan data
            if (empty($class_id) && empty($section_id) && empty($subject_group_id)) {
                // Get all classes
                $classes = $this->class_model->get();
                $all_lesson_plans = array();

                // For each class, get all sections and subject groups
                foreach ($classes as $class) {
                    $this->db->select('sections.id as section_id, sections.section');
                    $this->db->from('class_sections');
                    $this->db->join('sections', 'sections.id = class_sections.section_id');
                    $this->db->where('class_sections.class_id', $class['id']);
                    $sections_query = $this->db->get();
                    $sections = $sections_query->result_array();

                    foreach ($sections as $section) {
                        // Get subject groups for this class and section
                        $subject_groups = $this->subjectgroup_model->getGroupByClassandSection($class['id'], $section['section_id'], $session_id);

                        foreach ($subject_groups as $subject_group) {
                            // Get subjects in the group
                            $subjects = $this->subjectgroup_model->getGroupsubjects($subject_group['subject_group_id'], $session_id);

                            $subjects_data = array();
                            foreach ($subjects as $subject) {
                                $lebel = ($subject->code == '') ? $subject->name : $subject->name . ' (' . $subject->code . ')';

                                // Get subject status
                                $subject_details = $this->syllabus_model->get_subjectstatus($subject->id, $subject_group['id']);

                                if (!empty($subject_details) && $subject_details[0]->total != 0) {
                                    $complete = ($subject_details[0]->complete / $subject_details[0]->total) * 100;
                                    $incomplete = ($subject_details[0]->incomplete / $subject_details[0]->total) * 100;

                                    $subjects_data[] = array(
                                        'subject_id' => $subject->id,
                                        'subject_name' => $subject->name,
                                        'subject_code' => $subject->code,
                                        'label' => $lebel,
                                        'complete_percentage' => round($complete, 2),
                                        'incomplete_percentage' => round($incomplete, 2),
                                        'complete_count' => $subject_details[0]->complete,
                                        'incomplete_count' => $subject_details[0]->incomplete,
                                        'total_topics' => $subject_details[0]->total
                                    );
                                } else {
                                    $subjects_data[] = array(
                                        'subject_id' => $subject->id,
                                        'subject_name' => $subject->name,
                                        'subject_code' => $subject->code,
                                        'label' => $lebel,
                                        'complete_percentage' => 0,
                                        'incomplete_percentage' => 0,
                                        'complete_count' => 0,
                                        'incomplete_count' => 0,
                                        'total_topics' => 0
                                    );
                                }
                            }

                            if (!empty($subjects_data)) {
                                $all_lesson_plans[] = array(
                                    'class_id' => $class['id'],
                                    'class_name' => $class['class'],
                                    'section_id' => $section['section_id'],
                                    'section_name' => $section['section'],
                                    'subject_group_id' => $subject_group['subject_group_id'],
                                    'subject_group_name' => $subject_group['name'],
                                    'total_subjects' => count($subjects_data),
                                    'subjects' => $subjects_data
                                );
                            }
                        }
                    }
                }

                $response = [
                    'status' => 1,
                    'message' => 'All lesson plan reports retrieved successfully',
                    'filters_applied' => [
                        'class_id' => null,
                        'section_id' => null,
                        'subject_group_id' => null,
                        'session_id' => $session_id
                    ],
                    'total_records' => count($all_lesson_plans),
                    'data' => $all_lesson_plans,
                    'timestamp' => date('Y-m-d H:i:s')
                ];

                $this->output
                    ->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($response));
                return;
            }

            // If only partial filters provided, return helpful message
            if (empty($class_id) || empty($section_id) || empty($subject_group_id)) {
                $response = [
                    'status' => 1,
                    'message' => 'Partial filters provided. All three parameters (class_id, section_id, subject_group_id) are needed for detailed report.',
                    'filters_applied' => [
                        'class_id' => $class_id,
                        'section_id' => $section_id,
                        'subject_group_id' => $subject_group_id,
                        'session_id' => $session_id
                    ],
                    'note' => 'Please provide class_id, section_id, and subject_group_id to get lesson plan report',
                    'timestamp' => date('Y-m-d H:i:s')
                ];

                $this->output
                    ->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($response));
                return;
            }

            // Get subject group class sections ID
            $subject_group_class_sectionsId = $this->lessonplan_model->getsubject_group_class_sectionsId($class_id, $section_id, $subject_group_id, $session_id);

            if (empty($subject_group_class_sectionsId)) {
                $this->output
                    ->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 1,
                        'message' => 'No subject group found for the specified class, section, and subject group',
                        'filters_applied' => [
                            'class_id' => $class_id,
                            'section_id' => $section_id,
                            'subject_group_id' => $subject_group_id,
                            'session_id' => $session_id
                        ],
                        'total_subjects' => 0,
                        'subjects' => [],
                        'timestamp' => date('Y-m-d H:i:s')
                    ]));
                return;
            }

            // Get subjects in the group
            $subjects = $this->subjectgroup_model->getGroupsubjects($subject_group_id, $session_id);

            $subjects_data = array();

            foreach ($subjects as $key => $value) {
                $lebel = ($value->code == '') ? $value->name : $value->name . ' (' . $value->code . ')';

                // Get subject status
                $subject_details = $this->syllabus_model->get_subjectstatus($value->id, $subject_group_class_sectionsId['id']);

                if (!empty($subject_details) && $subject_details[0]->total != 0) {
                    $complete = ($subject_details[0]->complete / $subject_details[0]->total) * 100;
                    $incomplete = ($subject_details[0]->incomplete / $subject_details[0]->total) * 100;

                    $subjects_data[] = array(
                        'subject_id' => $value->id,
                        'subject_name' => $value->name,
                        'subject_code' => $value->code,
                        'label' => $lebel,
                        'complete_percentage' => round($complete, 2),
                        'incomplete_percentage' => round($incomplete, 2),
                        'complete_count' => $subject_details[0]->complete,
                        'incomplete_count' => $subject_details[0]->incomplete,
                        'total_topics' => $subject_details[0]->total
                    );
                } else {
                    $subjects_data[] = array(
                        'subject_id' => $value->id,
                        'subject_name' => $value->name,
                        'subject_code' => $value->code,
                        'label' => $lebel,
                        'complete_percentage' => 0,
                        'incomplete_percentage' => 0,
                        'complete_count' => 0,
                        'incomplete_count' => 0,
                        'total_topics' => 0
                    );
                }
            }

            $response = [
                'status' => 1,
                'message' => 'Lesson plan report retrieved successfully',
                'filters_applied' => [
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'subject_group_id' => $subject_group_id,
                    'session_id' => $session_id
                ],
                'subject_group_info' => [
                    'id' => $subject_group_class_sectionsId['id'],
                    'name' => $subject_group_class_sectionsId['name']
                ],
                'total_subjects' => count($subjects_data),
                'subjects' => $subjects_data,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Lesson Plan Report API Error: ' . $e->getMessage());
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
     * List all classes
     * 
     * POST /api/lesson-plan-report/list
     * 
     * Returns all available classes for selection
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

            // Get all classes
            $classes = $this->class_model->get();

            $response = [
                'status' => 1,
                'message' => 'Classes retrieved successfully',
                'total_classes' => count($classes),
                'classes' => $classes,
                'note' => 'Use the filter endpoint with class_id, section_id, and subject_group_id to get lesson plan report',
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Lesson Plan Report API Error: ' . $e->getMessage());
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
}

