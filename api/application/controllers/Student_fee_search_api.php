<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Student_fee_search_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Set JSON content type early
        $this->output->set_content_type('application/json');
        
        // Load essential helpers
        $this->load->helper('json_output');
        
        // Load required models
        $this->load->model(array(
            'student_model',
            'studentfeemaster_model',
            'setting_model',
            'auth_model'
        ));
    }

    /**
     * Search students by class and section
     * POST /student-fee-search/by-class
     */
    public function by_class()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 0, 'message' => 'Bad request. Only POST method allowed.'));
        }

        $check_auth_client = $this->auth_model->check_auth_client();
        if ($check_auth_client == true) {
            $_POST = json_decode(file_get_contents("php://input"), true);
            
            $class_id = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');

            // Validation
            if (empty($class_id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Class ID is required'
                ));
            }

            try {
                $students = $this->student_model->searchByClassSection($class_id, $section_id);
                
                $response = array(
                    'status' => 1,
                    'message' => 'Students retrieved successfully',
                    'total_records' => count($students),
                    'search_criteria' => array(
                        'class_id' => $class_id,
                        'section_id' => $section_id
                    ),
                    'data' => $students,
                    'timestamp' => date('Y-m-d H:i:s')
                );
                
                json_output(200, $response);
            } catch (Exception $e) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Internal server error: ' . $e->getMessage()
                ));
            }
        }
    }

    /**
     * Search students by keyword (full text search)
     * POST /student-fee-search/by-keyword
     */
    public function by_keyword()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 0, 'message' => 'Bad request. Only POST method allowed.'));
        }

        $check_auth_client = $this->auth_model->check_auth_client();
        if ($check_auth_client == true) {
            $_POST = json_decode(file_get_contents("php://input"), true);
            
            $search_text = $this->input->post('search_text');

            // Validation
            if (empty($search_text)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Search text is required'
                ));
            }

            try {
                $students = $this->student_model->searchFullText($search_text, array());
                
                $response = array(
                    'status' => 1,
                    'message' => 'Students retrieved successfully',
                    'total_records' => count($students),
                    'search_criteria' => array(
                        'search_text' => $search_text
                    ),
                    'data' => $students,
                    'timestamp' => date('Y-m-d H:i:s')
                );
                
                json_output(200, $response);
            } catch (Exception $e) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Internal server error: ' . $e->getMessage()
                ));
            }
        }
    }

    /**
     * Search student fees by fee category
     * POST /student-fee-search/by-category
     */
    public function by_category()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 0, 'message' => 'Bad request. Only POST method allowed.'));
        }

        $check_auth_client = $this->auth_model->check_auth_client();
        if ($check_auth_client == true) {
            $_POST = json_decode(file_get_contents("php://input"), true);
            
            $feecategory_id = $this->input->post('feecategory_id');
            $class_id = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');

            // Validation
            if (empty($feecategory_id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Fee category ID is required'
                ));
            }

            try {
                // Get students based on search criteria
                $students = array();
                if (!empty($class_id)) {
                    $students = $this->student_model->searchByClassSection($class_id, $section_id);
                } else {
                    // Get all active students if no class specified
                    $students = $this->student_model->get();
                }

                // Get fee details for each student
                $student_fee_data = array();
                foreach ($students as $student) {
                    $student_session_id = $student['student_session_id'] ?? $student['id'];
                    $fee_details = $this->studentfeemaster_model->getStudentFees($student_session_id);
                    
                    $student_fee_data[] = array(
                        'student_info' => $student,
                        'fee_details' => $fee_details
                    );
                }
                
                $response = array(
                    'status' => 1,
                    'message' => 'Student fees retrieved successfully',
                    'total_records' => count($student_fee_data),
                    'search_criteria' => array(
                        'feecategory_id' => $feecategory_id,
                        'class_id' => $class_id,
                        'section_id' => $section_id
                    ),
                    'data' => $student_fee_data,
                    'timestamp' => date('Y-m-d H:i:s')
                );
                
                json_output(200, $response);
            } catch (Exception $e) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Internal server error: ' . $e->getMessage()
                ));
            }
        }
    }

    /**
     * Get all classes for dropdown
     * POST /student-fee-search/classes
     */
    public function classes()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 0, 'message' => 'Bad request. Only POST method allowed.'));
        }

        $check_auth_client = $this->auth_model->check_auth_client();
        if ($check_auth_client == true) {
            try {
                // Direct database query to avoid Customlib dependency
                $this->db->select('id, class, is_active');
                $this->db->from('classes');
                $this->db->where('is_active', 'yes');
                $this->db->order_by('id');
                $query = $this->db->get();
                $classes = $query->result_array();

                $response = array(
                    'status' => 1,
                    'message' => 'Classes retrieved successfully',
                    'total_records' => count($classes),
                    'data' => $classes,
                    'timestamp' => date('Y-m-d H:i:s')
                );

                json_output(200, $response);
            } catch (Exception $e) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Internal server error: ' . $e->getMessage()
                ));
            }
        }
    }

    /**
     * Get sections by class ID
     * POST /student-fee-search/sections
     */
    public function sections()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 0, 'message' => 'Bad request. Only POST method allowed.'));
        }

        $check_auth_client = $this->auth_model->check_auth_client();
        if ($check_auth_client == true) {
            $_POST = json_decode(file_get_contents("php://input"), true);
            $class_id = $this->input->post('class_id');

            if (empty($class_id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Class ID is required'
                ));
            }

            try {
                // Direct database query to avoid model dependencies
                $this->db->select('sections.id, sections.section, sections.is_active');
                $this->db->from('class_sections');
                $this->db->join('sections', 'sections.id = class_sections.section_id');
                $this->db->where('class_sections.class_id', $class_id);
                $this->db->where('sections.is_active', 'yes');
                $this->db->order_by('sections.id');
                $query = $this->db->get();
                $sections = $query->result_array();

                $response = array(
                    'status' => 1,
                    'message' => 'Sections retrieved successfully',
                    'total_records' => count($sections),
                    'class_id' => $class_id,
                    'data' => $sections,
                    'timestamp' => date('Y-m-d H:i:s')
                );

                json_output(200, $response);
            } catch (Exception $e) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Internal server error: ' . $e->getMessage()
                ));
            }
        }
    }

    /**
     * Get all fee categories
     * POST /student-fee-search/fee-categories
     */
    public function fee_categories()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 0, 'message' => 'Bad request. Only POST method allowed.'));
        }

        $check_auth_client = $this->auth_model->check_auth_client();
        if ($check_auth_client == true) {
            try {
                // Direct database query to avoid model dependencies
                $this->db->select('id, category, is_active');
                $this->db->from('feecategory');
                $this->db->where('is_active', 'yes');
                $this->db->order_by('id');
                $query = $this->db->get();
                $fee_categories = $query->result_array();

                $response = array(
                    'status' => 1,
                    'message' => 'Fee categories retrieved successfully',
                    'total_records' => count($fee_categories),
                    'data' => $fee_categories,
                    'timestamp' => date('Y-m-d H:i:s')
                );

                json_output(200, $response);
            } catch (Exception $e) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Internal server error: ' . $e->getMessage()
                ));
            }
        }
    }

    /**
     * Get student fee details by student session ID
     * POST /student-fee-search/student-fees
     */
    public function student_fees()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 0, 'message' => 'Bad request. Only POST method allowed.'));
        }

        $check_auth_client = $this->auth_model->check_auth_client();
        if ($check_auth_client == true) {
            $_POST = json_decode(file_get_contents("php://input"), true);
            $student_session_id = $this->input->post('student_session_id');

            if (empty($student_session_id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Student session ID is required'
                ));
            }

            try {
                $student_fees = $this->studentfeemaster_model->getStudentFees($student_session_id);
                
                $response = array(
                    'status' => 1,
                    'message' => 'Student fees retrieved successfully',
                    'student_session_id' => $student_session_id,
                    'data' => $student_fees,
                    'timestamp' => date('Y-m-d H:i:s')
                );
                
                json_output(200, $response);
            } catch (Exception $e) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Internal server error: ' . $e->getMessage()
                ));
            }
        }
    }
}
