<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Internalresult_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('publicresultsubjectgroup_model');
        $this->load->model('examtype_model');
        $this->load->model('class_model');
        $this->load->model('section_model');
        $this->load->helper('json_output');
    }

    private function validate_headers()
    {
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key = $this->input->get_request_header('Auth-Key', TRUE);
        
        if ($client_service == 'smartschool' && $auth_key == 'schoolAdmin@') {
            return true;
        }
        return false;
    }

    public function search()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    
                    if (empty($params)) {
                        json_output(400, array('status' => 400, 'message' => 'Invalid JSON format or empty body.'));
                        return;
                    }

                    $admission_no = isset($params['admission_no']) ? $params['admission_no'] : '';
                    $academic_id = isset($params['academic_id']) ? $params['academic_id'] : '';
                    $exam_id = isset($params['exam_id']) ? $params['exam_id'] : '';

                    if (empty($admission_no) || empty($academic_id) || empty($exam_id)) {
                        json_output(400, array('status' => 400, 'message' => 'Admission Number, Academic Year, and Exam are required.'));
                        return;
                    }

                    // Get student ID
                    $student_id = $this->publicresultsubjectgroup_model->getadstudentid($admission_no);

                    if (!$student_id) {
                        json_output(404, array('status' => 404, 'message' => 'Student not found.'));
                        return;
                    }

                    // Get result name
                    $result_name = $this->publicresultsubjectgroup_model->getresultname($exam_id);

                    // Get student data
                    $student_data = $this->publicresultsubjectgroup_model->gtstudentdata($student_id);

                    // Get result status
                    $result_status = $this->publicresultsubjectgroup_model->getresultstatus($student_id, $exam_id, $academic_id);

                    // Get student results
                    $result_data = $this->publicresultsubjectgroup_model->getadstudentresults($admission_no, $exam_id, $academic_id);

                    json_output(200, array(
                        'status' => 200,
                        'message' => 'Success',
                        'data' => array(
                            'result_name' => $result_name,
                            'student_data' => $student_data,
                            'result_status' => $result_status,
                            'results' => $result_data,
                            'admission_no' => $admission_no,
                            'academic_id' => $academic_id
                        )
                    ));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    public function get_sessions()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                $sessions = $this->examtype_model->sessions();
                json_output(200, array('status' => 200, 'message' => 'Success', 'data' => $sessions));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    public function get_exam_types()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                $exam_types = $this->examtype_model->get();
                json_output(200, array('status' => 200, 'message' => 'Success', 'data' => $exam_types));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    /**
     * Get all classes
     * 
     * @return void Outputs JSON response with list of all classes
     */
    public function get_classes()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                $classes = $this->class_model->get();
                json_output(200, array('status' => 200, 'message' => 'Success', 'data' => $classes));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    /**
     * Get sections by class ID (optional)
     * 
     * @return void Outputs JSON response with list of sections
     */
    public function get_sections()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                $class_id = $this->input->get('class_id');
                
                if (!empty($class_id)) {
                    // Get sections for specific class
                    $sections = $this->section_model->get($class_id);
                } else {
                    // Get all sections
                    $sections = $this->section_model->get();
                }
                
                json_output(200, array('status' => 200, 'message' => 'Success', 'data' => $sections));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    /**
     * Get internal result types (exam types)
     * 
     * @return void Outputs JSON response with list of internal result types
     */
    public function get_internal_result_types()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                $session_id = $this->input->get('session_id');
                
                if (!empty($session_id)) {
                    // Filter by session if provided
                    $this->db->where('session_id', $session_id);
                }
                
                $exam_types = $this->examtype_model->get();
                json_output(200, array('status' => 200, 'message' => 'Success', 'data' => $exam_types));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    /**
     * Get result adding status options
     * 
     * @return void Outputs JSON response with predefined status options
     */
    public function get_result_statuses()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                $statuses = array(
                    array('id' => 'all', 'name' => 'All'),
                    array('id' => 'not_added', 'name' => 'Not Added'),
                    array('id' => 'added', 'name' => 'Added')
                );
                json_output(200, array('status' => 200, 'message' => 'Success', 'data' => $statuses));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    /**
     * Search students by filters
     * 
     * @return void Outputs JSON response with filtered student list
     */
    public function search_students()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                
                if (empty($params)) {
                    json_output(400, array('status' => 400, 'message' => 'Invalid JSON format or empty body.'));
                    return;
                }

                $class_id = isset($params['class_id']) ? $params['class_id'] : '';
                $section_id = isset($params['section_id']) ? $params['section_id'] : '';
                $internal_result_type_id = isset($params['internal_result_type_id']) ? $params['internal_result_type_id'] : '';
                $result_adding_status = isset($params['result_adding_status']) ? $params['result_adding_status'] : 'all';
                $session_id = isset($params['session_id']) ? $params['session_id'] : '';

                // Validate required fields
                if (empty($class_id) || empty($section_id) || empty($internal_result_type_id)) {
                    json_output(400, array('status' => 400, 'message' => 'Class, Section, and Internal Result Type are required.'));
                    return;
                }

                // Build query to get students
                $this->db->select('students.id, students.admission_no, students.firstname, students.middlename, students.lastname, students.roll_no, students.mobileno, students.email, classes.class, sections.section, resultaddingstatus.assign_status');
                $this->db->from('students');
                $this->db->join('student_session', 'student_session.student_id = students.id');
                $this->db->join('classes', 'classes.id = student_session.class_id');
                $this->db->join('sections', 'sections.id = student_session.section_id');
                $this->db->join('resultaddingstatus', 'resultaddingstatus.stid = students.id AND resultaddingstatus.resultype_id = ' . $this->db->escape($internal_result_type_id) . ($session_id ? ' AND resultaddingstatus.session_id = ' . $this->db->escape($session_id) : ''), 'left');
                
                $this->db->where('student_session.class_id', $class_id);
                $this->db->where('student_session.section_id', $section_id);
                $this->db->where('students.is_active', 'yes');
                
                // Filter by session if provided
                if (!empty($session_id)) {
                    $this->db->where('student_session.session_id', $session_id);
                }
                
                // Apply status filter
                if ($result_adding_status === 'not_added') {
                    $this->db->where('(resultaddingstatus.assign_status IS NULL OR resultaddingstatus.assign_status = 0)');
                } elseif ($result_adding_status === 'added') {
                    $this->db->where('resultaddingstatus.assign_status', 1);
                }
                // 'all' doesn't need additional filtering
                
                $this->db->order_by('students.admission_no', 'ASC');
                
                $query = $this->db->get();
                $students = $query->result_array();
                
                // Format the response
                foreach ($students as &$student) {
                    $student['full_name'] = trim($student['firstname'] . ' ' . ($student['middlename'] ? $student['middlename'] . ' ' : '') . $student['lastname']);
                    $student['result_status'] = !empty($student['assign_status']) ? 'Added' : 'Not Added';
                }
                
                json_output(200, array(
                    'status' => 200,
                    'message' => 'Success',
                    'data' => array(
                        'students' => $students,
                        'total_count' => count($students)
                    )
                ));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }
}
