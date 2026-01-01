<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Teacher Syllabus Status Report API Controller
 * 
 * This controller handles API requests for teacher syllabus status reports
 * showing teacher-wise syllabus completion for a specific subject.
 * 
 * @package    CodeIgniter
 * @subpackage Controllers
 * @category   API
 */
class Teacher_syllabus_status_report_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load required models - IMPORTANT: setting_model MUST be loaded FIRST
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('class_model');
        $this->load->model('subject_model');
        $this->load->model('subjectgroup_model');
        $this->load->model('syllabus_model');
        $this->load->model('lessonplan_model');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->database();
    }

    /**
     * Filter teacher syllabus status report
     *
     * POST /api/teacher-syllabus-status-report/filter
     *
     * Request body (all parameters optional):
     * {
     *   "class_id": 1,
     *   "section_id": 2,
     *   "subject_group_id": 3,
     *   "subject_id": 5,
     *   "session_id": 18
     * }
     *
     * Empty request body {} returns all available classes
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
            $session_id = isset($json_input['session_id']) ? $json_input['session_id'] : null;

            // If session_id is not provided, use current session
            if (empty($session_id)) {
                $session_id = $this->setting_model->getCurrentSession();
            }

            // Graceful handling: If no filters provided, return all teacher syllabus status data
            if (empty($class_id) && empty($section_id) && empty($subject_group_id) && empty($subject_id)) {
                // Get all classes
                $classes = $this->class_model->get();
                $all_teacher_reports = array();

                // For each class, get all sections, subject groups, and subjects
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

                            foreach ($subjects as $subject) {
                                // Get subject data
                                $subjectdata = $this->subject_model->get($subject->id);

                                if (empty($subjectdata)) {
                                    continue;
                                }

                                // Get subject status
                                $subject_details = $this->syllabus_model->get_subjectstatus($subject->id, $subject_group['id']);

                                $subject_info = array();
                                if (!empty($subject_details) && $subject_details[0]->total != 0) {
                                    $complete = ($subject_details[0]->complete / $subject_details[0]->total) * 100;
                                    $incomplete = ($subject_details[0]->incomplete / $subject_details[0]->total) * 100;

                                    $lebel = ($subjectdata['code'] == '') ? $subjectdata['name'] : $subjectdata['name'] . ' (' . $subjectdata['code'] . ')';

                                    $subject_info = array(
                                        'subject_id' => $subjectdata['id'],
                                        'subject_name' => $subjectdata['name'],
                                        'subject_code' => $subjectdata['code'],
                                        'label' => $lebel,
                                        'complete_percentage' => round($complete, 2),
                                        'incomplete_percentage' => round($incomplete, 2),
                                        'complete_count' => $subject_details[0]->complete,
                                        'incomplete_count' => $subject_details[0]->incomplete,
                                        'total_topics' => $subject_details[0]->total
                                    );
                                } else {
                                    $lebel = ($subjectdata['code'] == '') ? $subjectdata['name'] : $subjectdata['name'] . ' (' . $subjectdata['code'] . ')';

                                    $subject_info = array(
                                        'subject_id' => $subjectdata['id'],
                                        'subject_name' => $subjectdata['name'],
                                        'subject_code' => $subjectdata['code'],
                                        'label' => $lebel,
                                        'complete_percentage' => 0,
                                        'incomplete_percentage' => 0,
                                        'complete_count' => 0,
                                        'incomplete_count' => 0,
                                        'total_topics' => 0
                                    );
                                }

                                // Get teachers report
                                $teachers_report = $this->syllabus_model->get_subjectteachersreport($subject->id, $subject_group['id']);

                                $teachers_summary = array();
                                foreach ($teachers_report as $teachers_reportvalue) {
                                    $syllabus_id = explode(',', $teachers_reportvalue['subject_syllabus_id']);
                                    $staff_periodsdata = array();

                                    foreach ($syllabus_id as $syllabus_idvalue) {
                                        $staff_periods = $this->syllabus_model->get_subjectsyllabusbyid($syllabus_idvalue);
                                        if (!empty($staff_periods)) {
                                            $staff_periodsdata[] = $staff_periods;
                                        }
                                    }

                                    $teachers_summary[] = array(
                                        'teacher_name' => $teachers_reportvalue['name'],
                                        'total_periods' => $teachers_reportvalue['total_priodes'],
                                        'syllabus_details' => $staff_periodsdata
                                    );
                                }

                                if (!empty($teachers_summary) || !empty($subject_info)) {
                                    $all_teacher_reports[] = array(
                                        'class_id' => $class['id'],
                                        'class_name' => $class['class'],
                                        'section_id' => $section['section_id'],
                                        'section_name' => $section['section'],
                                        'subject_group_id' => $subject_group['subject_group_id'],
                                        'subject_group_name' => $subject_group['name'],
                                        'subject_info' => $subject_info,
                                        'total_teachers' => count($teachers_summary),
                                        'teachers_summary' => $teachers_summary
                                    );
                                }
                            }
                        }
                    }
                }

                $response = [
                    'status' => 1,
                    'message' => 'All teacher syllabus status reports retrieved successfully',
                    'filters_applied' => [
                        'class_id' => null,
                        'section_id' => null,
                        'subject_group_id' => null,
                        'subject_id' => null,
                        'session_id' => $session_id
                    ],
                    'total_records' => count($all_teacher_reports),
                    'data' => $all_teacher_reports,
                    'timestamp' => date('Y-m-d H:i:s')
                ];

                $this->output
                    ->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode($response));
                return;
            }

            // If only partial filters provided, return helpful message
            if (empty($class_id) || empty($section_id) || empty($subject_group_id) || empty($subject_id)) {
                $response = [
                    'status' => 1,
                    'message' => 'Partial filters provided. All four parameters (class_id, section_id, subject_group_id, subject_id) are needed for detailed report.',
                    'filters_applied' => [
                        'class_id' => $class_id,
                        'section_id' => $section_id,
                        'subject_group_id' => $subject_group_id,
                        'subject_id' => $subject_id,
                        'session_id' => $session_id
                    ],
                    'note' => 'Please provide class_id, section_id, subject_group_id, and subject_id to get teacher syllabus status report',
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
                            'subject_id' => $subject_id,
                            'session_id' => $session_id
                        ],
                        'total_teachers' => 0,
                        'teachers_summary' => [],
                        'timestamp' => date('Y-m-d H:i:s')
                    ]));
                return;
            }

            // Get subject data
            $subjectdata = $this->subject_model->get($subject_id);

            if (empty($subjectdata)) {
                $this->output
                    ->set_status_header(200)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 1,
                        'message' => 'Subject not found',
                        'filters_applied' => [
                            'class_id' => $class_id,
                            'section_id' => $section_id,
                            'subject_group_id' => $subject_group_id,
                            'subject_id' => $subject_id,
                            'session_id' => $session_id
                        ],
                        'total_teachers' => 0,
                        'teachers_summary' => [],
                        'timestamp' => date('Y-m-d H:i:s')
                    ]));
                return;
            }

            // Get subject status
            $subject_details = $this->syllabus_model->get_subjectstatus($subject_id, $subject_group_class_sectionsId['id']);

            $subject_info = array();
            $complete_percentage = 0;

            if (!empty($subject_details) && $subject_details[0]->total != 0) {
                $complete = ($subject_details[0]->complete / $subject_details[0]->total) * 100;
                $incomplete = ($subject_details[0]->incomplete / $subject_details[0]->total) * 100;
                $complete_percentage = round($complete, 2);

                $lebel = ($subjectdata['code'] == '') ? $subjectdata['name'] : $subjectdata['name'] . ' (' . $subjectdata['code'] . ')';

                $subject_info = array(
                    'subject_id' => $subjectdata['id'],
                    'subject_name' => $subjectdata['name'],
                    'subject_code' => $subjectdata['code'],
                    'label' => $lebel,
                    'complete_percentage' => round($complete, 2),
                    'incomplete_percentage' => round($incomplete, 2),
                    'complete_count' => $subject_details[0]->complete,
                    'incomplete_count' => $subject_details[0]->incomplete,
                    'total_topics' => $subject_details[0]->total
                );
            } else {
                $lebel = ($subjectdata['code'] == '') ? $subjectdata['name'] : $subjectdata['name'] . ' (' . $subjectdata['code'] . ')';

                $subject_info = array(
                    'subject_id' => $subjectdata['id'],
                    'subject_name' => $subjectdata['name'],
                    'subject_code' => $subjectdata['code'],
                    'label' => $lebel,
                    'complete_percentage' => 0,
                    'incomplete_percentage' => 0,
                    'complete_count' => 0,
                    'incomplete_count' => 0,
                    'total_topics' => 0
                );
            }

            // Get teachers report
            $teachers_report = $this->syllabus_model->get_subjectteachersreport($subject_id, $subject_group_class_sectionsId['id']);

            $teachers_summary = array();

            foreach ($teachers_report as $teachers_reportkey => $teachers_reportvalue) {
                $syllabus_id = explode(',', $teachers_reportvalue['subject_syllabus_id']);
                $staff_periodsdata = array();

                foreach ($syllabus_id as $syllabus_idkey => $syllabus_idvalue) {
                    $staff_periods = $this->syllabus_model->get_subjectsyllabusbyid($syllabus_idvalue);
                    if (!empty($staff_periods)) {
                        $staff_periodsdata[] = $staff_periods;
                    }
                }

                $teachers_summary[] = array(
                    'teacher_name' => $teachers_reportvalue['name'],
                    'total_periods' => $teachers_reportvalue['total_priodes'],
                    'syllabus_details' => $staff_periodsdata
                );
            }

            $response = [
                'status' => 1,
                'message' => 'Teacher syllabus status report retrieved successfully',
                'filters_applied' => [
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'subject_group_id' => $subject_group_id,
                    'subject_id' => $subject_id,
                    'session_id' => $session_id
                ],
                'subject_group_info' => [
                    'id' => $subject_group_class_sectionsId['id'],
                    'name' => $subject_group_class_sectionsId['name']
                ],
                'subject_info' => $subject_info,
                'total_teachers' => count($teachers_summary),
                'teachers_summary' => $teachers_summary,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Teacher Syllabus Status Report API Error: ' . $e->getMessage());
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
     * POST /api/teacher-syllabus-status-report/list
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
                'note' => 'Use the filter endpoint with class_id, section_id, subject_group_id, and subject_id to get teacher syllabus status report',
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Teacher Syllabus Status Report API Error: ' . $e->getMessage());
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

