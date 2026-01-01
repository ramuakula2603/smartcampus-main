<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Student Referral API Controller
 * 
 * This controller provides RESTful API endpoints for managing student referral records.
 * It handles CRUD operations for student referrals, tracking which staff members referred which students.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Student_referral_api extends CI_Controller
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
        $this->load->model('Student_referral_model', 'referral_model');
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
     * List all student referrals
     * 
     * Retrieves a list of all student referrals with optional filtering.
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
            
            if (isset($input['class_id']) && !empty($input['class_id'])) {
                $filters['class_id'] = is_array($input['class_id']) ? $input['class_id'] : array($input['class_id']);
            }
            
            if (isset($input['section_id']) && !empty($input['section_id'])) {
                $filters['section_id'] = is_array($input['section_id']) ? $input['section_id'] : array($input['section_id']);
            }
            
            if (isset($input['reference_id']) && !empty($input['reference_id'])) {
                $filters['reference_id'] = is_array($input['reference_id']) ? $input['reference_id'] : array($input['reference_id']);
            }
            
            if (isset($input['session_id']) && !empty($input['session_id'])) {
                $filters['session_id'] = is_array($input['session_id']) ? $input['session_id'] : array($input['session_id']);
            }

            // Get referral list
            $referral_list = $this->referral_model->get_student_referrals($filters);

            // Format response data
            $formatted_data = array();
            foreach ($referral_list as $referral) {
                $formatted_data[] = array(
                    'referral_id' => $referral['referral_id'],
                    'student_id' => $referral['student_id'],
                    'admission_no' => $referral['admission_no'],
                    'student_name' => trim($referral['firstname'] . ' ' . $referral['middlename'] . ' ' . $referral['lastname']),
                    'father_name' => $referral['father_name'],
                    'class' => $referral['class'],
                    'class_id' => $referral['class_id'],
                    'section' => $referral['section'],
                    'section_id' => $referral['section_id'],
                    'date_of_birth' => $referral['dob'],
                    'phone' => !empty($referral['guardian_phone']) ? $referral['guardian_phone'] : $referral['mobileno'],
                    'reference_id' => $referral['reference_id'],
                    'reference_by' => trim($referral['staff_firstname'] . ' ' . $referral['staff_lastname']),
                    'reference_employee_id' => $referral['employee_id'],
                    'session_id' => $referral['session_id'],
                    'created_at' => $referral['created_at']
                );
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Student referrals retrieved successfully',
                'total_records' => count($formatted_data),
                'data' => $formatted_data
            ));

        } catch (Exception $e) {
            log_message('error', 'Student Referral API List Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get a specific student referral
     * 
     * Retrieves details of a specific student referral by its ID.
     * 
     * @param int $id Referral ID
     * @return void Outputs JSON response
     */
    public function get($id = null)
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
                    'message' => 'Invalid or missing referral ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if referral exists first (simple check)
            if (!$this->referral_model->referral_exists($id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Student referral not found',
                    'data' => null
                ));
                return;
            }

            // Get referral details
            $referral = $this->referral_model->get_referral($id);
            
            if (empty($referral)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Student referral not found',
                    'data' => null
                ));
                return;
            }

            $formatted_data = array(
                'referral_id' => $referral['id'],
                'student_id' => $referral['student_id'],
                'admission_no' => $referral['admission_no'],
                'student_name' => trim($referral['firstname'] . ' ' . $referral['middlename'] . ' ' . $referral['lastname']),
                'father_name' => $referral['father_name'],
                'class' => $referral['class'],
                'section' => $referral['section'],
                'date_of_birth' => $referral['dob'],
                'gender' => $referral['gender'],
                'phone' => !empty($referral['guardian_phone']) ? $referral['guardian_phone'] : $referral['mobileno'],
                'reference_id' => $referral['staff_id'],
                'reference_by' => trim($referral['staff_firstname'] . ' ' . $referral['staff_lastname']),
                'reference_employee_id' => $referral['employee_id'],
                'session_id' => $referral['session_id'],
                'created_at' => $referral['created_at']
            );

            json_output(200, array(
                'status' => 1,
                'message' => 'Student referral retrieved successfully',
                'data' => $formatted_data
            ));

        } catch (Exception $e) {
            log_message('error', 'Student Referral API Get Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Create a new student referral
     * 
     * Creates a new student referral record.
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

            // Validate required fields
            if (empty($input['student_id']) || !is_numeric($input['student_id']) || $input['student_id'] <= 0) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Student ID is required and must be a valid number',
                    'data' => null
                ));
                return;
            }

            if (empty($input['reference_id']) || !is_numeric($input['reference_id']) || $input['reference_id'] <= 0) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Reference ID (Staff ID) is required and must be a valid number',
                    'data' => null
                ));
                return;
            }

            // Prepare data for insertion
            $referral_data = array(
                'student_id' => (int)$input['student_id'],
                'staff_id' => (int)$input['reference_id'],
                'session_id' => isset($input['session_id']) && !empty($input['session_id']) ? (int)$input['session_id'] : null
            );

            // Insert the referral
            $referral_id = $this->referral_model->add($referral_data);

            // Get the created referral
            $created_referral = $this->referral_model->get_referral($referral_id);

            json_output(201, array(
                'status' => 1,
                'message' => 'Student referral created successfully',
                'data' => array(
                    'referral_id' => $referral_id,
                    'student_id' => $created_referral['student_id'],
                    'reference_id' => $created_referral['staff_id'],
                    'session_id' => $created_referral['session_id']
                )
            ));

        } catch (Exception $e) {
            log_message('error', 'Student Referral API Create Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Update a student referral
     * 
     * Updates an existing student referral record.
     * 
     * @param int $id Referral ID
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
                    'message' => 'Invalid or missing referral ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if referral exists
            if (!$this->referral_model->referral_exists($id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Student referral not found',
                    'data' => null
                ));
                return;
            }

            // Get input data
            $input = json_decode($this->input->raw_input_stream, true);
            if ($input === null) {
                $input = array();
            }

            // Prepare data for update
            $referral_data = array();
            
            if (isset($input['student_id']) && !empty($input['student_id'])) {
                if (!is_numeric($input['student_id']) || $input['student_id'] <= 0) {
                    json_output(400, array(
                        'status' => 0,
                        'message' => 'Student ID must be a valid number',
                        'data' => null
                    ));
                    return;
                }
                $referral_data['student_id'] = (int)$input['student_id'];
            }

            if (isset($input['reference_id']) && !empty($input['reference_id'])) {
                if (!is_numeric($input['reference_id']) || $input['reference_id'] <= 0) {
                    json_output(400, array(
                        'status' => 0,
                        'message' => 'Reference ID (Staff ID) must be a valid number',
                        'data' => null
                    ));
                    return;
                }
                $referral_data['staff_id'] = (int)$input['reference_id'];
            }

            if (isset($input['session_id']) && !empty($input['session_id'])) {
                $referral_data['session_id'] = (int)$input['session_id'];
            }

            if (empty($referral_data)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'No valid fields provided for update',
                    'data' => null
                ));
                return;
            }

            // Update the referral
            $result = $this->referral_model->update($id, $referral_data);

            if ($result) {
                // Get the updated referral
                $updated_referral = $this->referral_model->get_referral($id);

                json_output(200, array(
                    'status' => 1,
                    'message' => 'Student referral updated successfully',
                    'data' => array(
                        'referral_id' => $id,
                        'student_id' => $updated_referral['student_id'],
                        'reference_id' => $updated_referral['staff_id'],
                        'session_id' => $updated_referral['session_id']
                    )
                ));
            } else {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to update referral or no changes made',
                    'data' => null
                ));
            }

        } catch (Exception $e) {
            log_message('error', 'Student Referral API Update Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Delete a student referral
     * 
     * Deletes a student referral record.
     * 
     * @param int $id Referral ID
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
                    'message' => 'Invalid or missing referral ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if referral exists
            if (!$this->referral_model->referral_exists($id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Student referral not found',
                    'data' => null
                ));
                return;
            }

            // Delete the referral
            $result = $this->referral_model->delete($id);

            if ($result) {
                json_output(200, array(
                    'status' => 1,
                    'message' => 'Student referral deleted successfully',
                    'data' => array('referral_id' => $id)
                ));
            } else {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to delete referral',
                    'data' => null
                ));
            }

        } catch (Exception $e) {
            log_message('error', 'Student Referral API Delete Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }
}

