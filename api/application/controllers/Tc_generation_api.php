<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * TC Generation API Controller
 * 
 * This controller provides RESTful API endpoints for managing Transfer Certificate (TC) generation.
 * It handles listing TC certificate templates and students eligible for TC generation.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Tc_generation_api extends CI_Controller
{
    /**
     * Constructor
     * 
     * Initializes the controller, loads required models, libraries, and helpers.
     */
    public function __construct()
    {
        parent::__construct();

        // Start output buffering if not already started
        if (!ob_get_level()) {
            ob_start();
        }

        // Set JSON content type
        $this->output->set_content_type('application/json');

        // Load essential helpers
        $this->load->helper('json_output');

        // Load required models
        $this->load->model('Tc_generation_model', 'tc_model');
    }

    /**
     * Validate required headers
     * 
     * @return bool True if headers are valid, false otherwise
     */
    private function validate_headers()
    {
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key = $this->input->get_request_header('Auth-Key', TRUE);
        
        return ($client_service === 'smartschool' && $auth_key === 'schoolAdmin@');
    }

    /**
     * List TC certificate templates
     * 
     * Retrieves a list of all active TC certificate templates.
     * 
     * @return void Outputs JSON response
     */
    public function list_templates()
    {
        try {
            // Validate request method
            if ($this->input->method() !== 'post') {
                json_output(405, array(
                    'status' => 0,
                    'message' => 'Method not allowed. Use POST method.',
                    'data' => null
                ));
                return;
            }

            // Validate required headers
            if (!$this->validate_headers()) {
                json_output(401, array(
                    'status' => 0,
                    'message' => 'Unauthorized access. Invalid headers.',
                    'data' => null
                ));
                return;
            }

            // Get TC certificate templates
            $templates = $this->tc_model->get_tc_certificates();

            // Format response data
            $formatted_data = array();
            foreach ($templates as $template) {
                $formatted_data[] = array(
                    'id' => $template['id'],
                    'tc_name' => $template['tc_name'],
                    'school_name' => $template['school_name'],
                    'tc_head_tittle' => $template['tc_head_tittle'],
                    'tc_description' => $template['tc_description'],
                    'status' => $template['status']
                );
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'TC certificate templates retrieved successfully',
                'total_records' => count($formatted_data),
                'data' => $formatted_data
            ));

        } catch (Exception $e) {
            log_message('error', 'TC Generation API List Templates Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get a specific TC certificate template
     * 
     * Retrieves details of a specific TC certificate template by its ID.
     * 
     * @param int $id Template ID
     * @return void Outputs JSON response
     */
    public function get_template($id = null)
    {
        try {
            // Validate request method
            if ($this->input->method() !== 'post') {
                json_output(405, array(
                    'status' => 0,
                    'message' => 'Method not allowed. Use POST method.',
                    'data' => null
                ));
                return;
            }

            // Validate required headers
            if (!$this->validate_headers()) {
                json_output(401, array(
                    'status' => 0,
                    'message' => 'Unauthorized access. Invalid headers.',
                    'data' => null
                ));
                return;
            }

            // Validate ID
            if (empty($id) || !is_numeric($id) || $id <= 0) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing template ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Get template details
            $template = $this->tc_model->get_tc_certificates($id);
            
            if (empty($template)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'TC certificate template not found',
                    'data' => null
                ));
                return;
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'TC certificate template retrieved successfully',
                'data' => $template
            ));

        } catch (Exception $e) {
            log_message('error', 'TC Generation API Get Template Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * List students for TC generation
     * 
     * Retrieves a list of students eligible for TC generation with optional filtering.
     * 
     * @return void Outputs JSON response
     */
    public function list_students()
    {
        try {
            // Validate request method
            if ($this->input->method() !== 'post') {
                json_output(405, array(
                    'status' => 0,
                    'message' => 'Method not allowed. Use POST method.',
                    'data' => null
                ));
                return;
            }

            // Validate required headers
            if (!$this->validate_headers()) {
                json_output(401, array(
                    'status' => 0,
                    'message' => 'Unauthorized access. Invalid headers.',
                    'data' => null
                ));
                return;
            }

            // Get input data
            $input = json_decode($this->input->raw_input_stream, true);
            if ($input === null) {
                $input = array();
            }

            // Extract filter parameters
            $filters = array();
            
            if (isset($input['class_id']) && !empty($input['class_id'])) {
                $filters['class_id'] = is_array($input['class_id']) ? $input['class_id'] : array($input['class_id']);
            }
            
            if (isset($input['section_id']) && !empty($input['section_id'])) {
                $filters['section_id'] = is_array($input['section_id']) ? $input['section_id'] : array($input['section_id']);
            }
            
            if (isset($input['session_id']) && !empty($input['session_id'])) {
                $filters['session_id'] = is_array($input['session_id']) ? $input['session_id'] : array($input['session_id']);
            }

            // Get student list
            $student_list = $this->tc_model->get_students_for_tc($filters);

            // Format response data
            $formatted_data = array();
            foreach ($student_list as $student) {
                $formatted_data[] = array(
                    'student_id' => $student['student_id'],
                    'admission_no' => $student['admission_no'],
                    'student_name' => trim($student['firstname'] . ' ' . $student['middlename'] . ' ' . $student['lastname']),
                    'father_name' => $student['father_name'],
                    'mother_name' => isset($student['mother_name']) ? $student['mother_name'] : '',
                    'class' => $student['class'],
                    'class_id' => $student['class_id'],
                    'section' => $student['section'],
                    'section_id' => $student['section_id'],
                    'date_of_birth' => $student['dob'],
                    'gender' => $student['gender'],
                    'phone' => !empty($student['guardian_phone']) ? $student['guardian_phone'] : $student['mobileno'],
                    'cast' => isset($student['cast']) ? $student['cast'] : '',
                    'religion' => isset($student['religion']) ? $student['religion'] : '',
                    'category' => isset($student['category']) ? $student['category'] : '',
                    'admission_date' => $student['admission_date']
                );
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Students for TC generation retrieved successfully',
                'total_records' => count($formatted_data),
                'data' => $formatted_data
            ));

        } catch (Exception $e) {
            log_message('error', 'TC Generation API List Students Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get a specific student for TC generation
     * 
     * Retrieves details of a specific student eligible for TC generation.
     * 
     * @param int $student_id Student ID
     * @return void Outputs JSON response
     */
    public function get_student($student_id = null)
    {
        try {
            // Validate request method
            if ($this->input->method() !== 'post') {
                json_output(405, array(
                    'status' => 0,
                    'message' => 'Method not allowed. Use POST method.',
                    'data' => null
                ));
                return;
            }

            // Validate required headers
            if (!$this->validate_headers()) {
                json_output(401, array(
                    'status' => 0,
                    'message' => 'Unauthorized access. Invalid headers.',
                    'data' => null
                ));
                return;
            }

            // Validate ID
            if (empty($student_id) || !is_numeric($student_id) || $student_id <= 0) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing student ID',
                    'data' => null
                ));
                return;
            }

            $student_id = (int)$student_id;

            // Get student details
            $student = $this->tc_model->get_student_for_tc($student_id);
            
            if (empty($student)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Student not found or not eligible for TC generation',
                    'data' => null
                ));
                return;
            }

            $formatted_data = array(
                'student_id' => $student['id'],
                'admission_no' => $student['admission_no'],
                'student_name' => trim($student['firstname'] . ' ' . $student['middlename'] . ' ' . $student['lastname']),
                'father_name' => $student['father_name'],
                'mother_name' => isset($student['mother_name']) ? $student['mother_name'] : '',
                'class' => $student['class'],
                'section' => $student['section'],
                'date_of_birth' => $student['dob'],
                'gender' => $student['gender'],
                'phone' => !empty($student['guardian_phone']) ? $student['guardian_phone'] : $student['mobileno'],
                'cast' => isset($student['cast']) ? $student['cast'] : '',
                'religion' => isset($student['religion']) ? $student['religion'] : '',
                'category' => isset($student['category']) ? $student['category'] : '',
                'admission_date' => $student['admission_date']
            );

            json_output(200, array(
                'status' => 1,
                'message' => 'Student details retrieved successfully',
                'data' => $formatted_data
            ));

        } catch (Exception $e) {
            log_message('error', 'TC Generation API Get Student Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * List all TC templates (for management)
     * 
     * Retrieves a list of all TC templates including inactive ones, with optional filtering.
     * 
     * @return void Outputs JSON response
     */
    public function list()
    {
        try {
            // Validate request method
            if ($this->input->method() !== 'post') {
                json_output(405, array(
                    'status' => 0,
                    'message' => 'Method not allowed. Use POST method.',
                    'data' => null
                ));
                return;
            }

            // Validate required headers
            if (!$this->validate_headers()) {
                json_output(401, array(
                    'status' => 0,
                    'message' => 'Unauthorized access. Invalid headers.',
                    'data' => null
                ));
                return;
            }

            // Get input data
            $input = json_decode($this->input->raw_input_stream, true);
            if ($input === null) {
                $input = array();
            }

            // Extract filter parameters
            $filters = array();
            
            if (isset($input['search']) && !empty($input['search'])) {
                $filters['search'] = $input['search'];
            }
            
            if (isset($input['status']) && $input['status'] !== '') {
                $filters['status'] = (int)$input['status'];
            }

            // Get templates
            $templates = $this->tc_model->get_all_templates($filters);

            json_output(200, array(
                'status' => 1,
                'message' => 'TC templates retrieved successfully',
                'total_records' => count($templates),
                'data' => $templates
            ));

        } catch (Exception $e) {
            log_message('error', 'TC Generation API List Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Create new TC template
     * 
     * Creates a new TC certificate template.
     * 
     * @return void Outputs JSON response
     */
    public function create()
    {
        try {
            // Validate request method
            if ($this->input->method() !== 'post') {
                json_output(405, array(
                    'status' => 0,
                    'message' => 'Method not allowed. Use POST method.',
                    'data' => null
                ));
                return;
            }

            // Validate required headers
            if (!$this->validate_headers()) {
                json_output(401, array(
                    'status' => 0,
                    'message' => 'Unauthorized access. Invalid headers.',
                    'data' => null
                ));
                return;
            }

            // Get input data
            $input = json_decode($this->input->raw_input_stream, true);
            if ($input === null || empty($input)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or empty request data',
                    'data' => null
                ));
                return;
            }

            // Validate required fields
            $required_fields = array('tc_name', 'tc_head_tittle', 'school_name', 'tc_description', 'tc_body', 'tc_address', 'tc_footer', 'tc_conduct', 'tc_mother_tongue', 'firstlang_id', 'secondlang_id', 'tc_date_left', 'tc_nationality', 'tc_second_year_course', 'tc_eligible_university_course', 'tc_receipt_scholarship', 'tc_receipt_concession', 'tc_punishment_during_period', 'tc_optional_lang');
            
            foreach ($required_fields as $field) {
                if (!isset($input[$field]) || empty($input[$field])) {
                    json_output(400, array(
                        'status' => 0,
                        'message' => "Required field missing: {$field}",
                        'data' => null
                    ));
                    return;
                }
            }

            // Prepare data
            $data = array(
                'tc_name' => $input['tc_name'],
                'school_name' => $input['school_name'],
                'tc_head_tittle' => $input['tc_head_tittle'],
                'tc_description' => $input['tc_description'],
                'tc_address' => $input['tc_address'],
                'tc_body' => $input['tc_body'],
                'tc_date_left' => $input['tc_date_left'],
                'tc_nationality' => $input['tc_nationality'],
                'tc_second_year_course' => $input['tc_second_year_course'],
                'tc_eligible_university_course' => $input['tc_eligible_university_course'],
                'tc_receipt_scholarship' => $input['tc_receipt_scholarship'],
                'tc_receipt_concession' => $input['tc_receipt_concession'],
                'tc_punishment_during_period' => $input['tc_punishment_during_period'],
                'tc_optional_lang' => $input['tc_optional_lang'],
                'tc_first_lang' => $input['firstlang_id'],
                'tc_second_lang' => $input['secondlang_id'],
                'tc_footer' => $input['tc_footer'],
                'tc_conduct' => $input['tc_conduct'],
                'tc_mother_tongue' => $input['tc_mother_tongue'],
                'enable_student_name' => isset($input['enable_student_name']) ? (int)$input['enable_student_name'] : 0,
                'enable_admission_date' => isset($input['enable_admission_date']) ? (int)$input['enable_admission_date'] : 0,
                'enable_parents_name' => isset($input['enable_parents_name']) ? (int)$input['enable_parents_name'] : 0,
                'enable_mother_tongue' => isset($input['enable_mother_tongue']) ? (int)$input['enable_mother_tongue'] : 0,
                'enable_date_tc' => isset($input['enable_date_tc']) ? (int)$input['enable_date_tc'] : 0,
                'enable_caste' => isset($input['enable_caste']) ? (int)$input['enable_caste'] : 0,
                'enable_dob' => isset($input['enable_dob']) ? (int)$input['enable_dob'] : 0,
                'logo' => isset($input['logo']) ? $input['logo'] : '',
                'status' => isset($input['status']) ? (int)$input['status'] : 1
            );

            // Insert template
            $insert_id = $this->tc_model->add_template($data);
            
            if ($insert_id === false) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to create TC template',
                    'data' => null
                ));
                return;
            }

            // Get created template
            $template = $this->tc_model->get_tc_certificates($insert_id);

            json_output(201, array(
                'status' => 1,
                'message' => 'TC template created successfully',
                'data' => $template
            ));

        } catch (Exception $e) {
            log_message('error', 'TC Generation API Create Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Update TC template
     * 
     * Updates an existing TC certificate template.
     * 
     * @param int $id Template ID
     * @return void Outputs JSON response
     */
    public function update($id = null)
    {
        try {
            // Validate request method
            if ($this->input->method() !== 'post') {
                json_output(405, array(
                    'status' => 0,
                    'message' => 'Method not allowed. Use POST method.',
                    'data' => null
                ));
                return;
            }

            // Validate required headers
            if (!$this->validate_headers()) {
                json_output(401, array(
                    'status' => 0,
                    'message' => 'Unauthorized access. Invalid headers.',
                    'data' => null
                ));
                return;
            }

            // Validate ID
            if (empty($id) || !is_numeric($id) || $id <= 0) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing template ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if template exists
            if (!$this->tc_model->template_exists($id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'TC template not found',
                    'data' => null
                ));
                return;
            }

            // Get input data
            $input = json_decode($this->input->raw_input_stream, true);
            if ($input === null || empty($input)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or empty request data',
                    'data' => null
                ));
                return;
            }

            // Prepare data (only update provided fields)
            $data = array();
            
            $fields = array('tc_name', 'school_name', 'tc_head_tittle', 'tc_description', 'tc_address', 'tc_body', 'tc_date_left', 'tc_nationality', 'tc_second_year_course', 'tc_eligible_university_course', 'tc_receipt_scholarship', 'tc_receipt_concession', 'tc_punishment_during_period', 'tc_optional_lang', 'tc_footer', 'tc_conduct', 'tc_mother_tongue', 'logo', 'status');
            
            foreach ($fields as $field) {
                if (isset($input[$field])) {
                    $data[$field] = $input[$field];
                }
            }
            
            if (isset($input['firstlang_id'])) {
                $data['tc_first_lang'] = $input['firstlang_id'];
            }
            
            if (isset($input['secondlang_id'])) {
                $data['tc_second_lang'] = $input['secondlang_id'];
            }
            
            // Handle enable fields
            $enable_fields = array('enable_student_name', 'enable_admission_date', 'enable_parents_name', 'enable_mother_tongue', 'enable_date_tc', 'enable_caste', 'enable_dob');
            foreach ($enable_fields as $field) {
                if (isset($input[$field])) {
                    $data[$field] = (int)$input[$field];
                }
            }

            if (empty($data)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'No data provided for update',
                    'data' => null
                ));
                return;
            }

            // Update template
            $result = $this->tc_model->update_template($id, $data);
            
            if ($result === false) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to update TC template',
                    'data' => null
                ));
                return;
            }

            // Get updated template
            $template = $this->tc_model->get_tc_certificates($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'TC template updated successfully',
                'data' => $template
            ));

        } catch (Exception $e) {
            log_message('error', 'TC Generation API Update Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Delete TC template
     * 
     * Deletes a TC certificate template.
     * 
     * @param int $id Template ID
     * @return void Outputs JSON response
     */
    public function delete($id = null)
    {
        try {
            // Validate request method
            if ($this->input->method() !== 'post') {
                json_output(405, array(
                    'status' => 0,
                    'message' => 'Method not allowed. Use POST method.',
                    'data' => null
                ));
                return;
            }

            // Validate required headers
            if (!$this->validate_headers()) {
                json_output(401, array(
                    'status' => 0,
                    'message' => 'Unauthorized access. Invalid headers.',
                    'data' => null
                ));
                return;
            }

            // Validate ID
            if (empty($id) || !is_numeric($id) || $id <= 0) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing template ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if template exists
            if (!$this->tc_model->template_exists($id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'TC template not found',
                    'data' => null
                ));
                return;
            }

            // Delete template
            $result = $this->tc_model->delete_template($id);
            
            if ($result === false) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to delete TC template',
                    'data' => null
                ));
                return;
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'TC template deleted successfully',
                'data' => null
            ));

        } catch (Exception $e) {
            log_message('error', 'TC Generation API Delete Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get subjects list (for language selection)
     * 
     * Retrieves a list of subjects that can be used for first and second language selection.
     * 
     * @return void Outputs JSON response
     */
    public function subjects()
    {
        try {
            // Validate request method
            if ($this->input->method() !== 'post') {
                json_output(405, array(
                    'status' => 0,
                    'message' => 'Method not allowed. Use POST method.',
                    'data' => null
                ));
                return;
            }

            // Validate required headers
            if (!$this->validate_headers()) {
                json_output(401, array(
                    'status' => 0,
                    'message' => 'Unauthorized access. Invalid headers.',
                    'data' => null
                ));
                return;
            }

            // Get subjects
            $subjects = $this->tc_model->get_subjects();

            json_output(200, array(
                'status' => 1,
                'message' => 'Subjects retrieved successfully',
                'total_records' => count($subjects),
                'data' => $subjects
            ));

        } catch (Exception $e) {
            log_message('error', 'TC Generation API Subjects Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }
}

