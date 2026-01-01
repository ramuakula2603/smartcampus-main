<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Subjects API Controller
 * 
 * This controller provides RESTful API endpoints for subject management.
 * It handles CRUD operations for school subjects.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Subjects_api extends CI_Controller
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

        // Set JSON content type early
        $this->output->set_content_type('application/json');

        // Load essential helpers
        $this->load->helper('json_output');

        // Load required models
        try {
            $this->load->model(array(
                'subject_model',
                'setting_model'
            ));
        } catch (Exception $e) {
            log_message('error', 'Error loading models: ' . $e->getMessage());
        }

        // Load libraries
        try {
            $this->load->library(array('customlib'));
        } catch (Exception $e) {
            log_message('error', 'Error loading libraries: ' . $e->getMessage());
        }
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
     * List all subjects
     * 
     * Retrieves a list of all subject records.
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

            // Get all subjects
            $subjects = $this->subject_model->get();

            // Format response data
            $formatted_subjects = array();
            if (!empty($subjects)) {
                foreach ($subjects as $subject) {
                    $formatted_subjects[] = array(
                        'id' => $subject['id'],
                        'name' => $subject['name'],
                        'code' => isset($subject['code']) ? $subject['code'] : null,
                        'is_active' => isset($subject['is_active']) ? $subject['is_active'] : 'yes',
                        'created_at' => isset($subject['created_at']) ? $subject['created_at'] : null,
                        'updated_at' => isset($subject['updated_at']) ? $subject['updated_at'] : null
                    );
                }
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Subjects retrieved successfully',
                'total_records' => count($formatted_subjects),
                'data' => $formatted_subjects
            ));

        } catch (Exception $e) {
            log_message('error', 'Subjects API List Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get single subject record
     * 
     * Retrieves detailed information for a specific subject record.
     * 
     * @param int $id Subject ID
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

            // Validate ID parameter
            if ($id === null || !is_numeric($id) || $id <= 0) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing subject ID',
                    'data' => null
                ));
                return;
            }

            // Get subject record
            $subject = $this->subject_model->get($id);

            if (empty($subject)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Subject record not found',
                    'data' => null
                ));
                return;
            }

            // Format response data
            $formatted_subject = array(
                'id' => $subject['id'],
                'name' => $subject['name'],
                'code' => isset($subject['code']) ? $subject['code'] : null,
                'is_active' => isset($subject['is_active']) ? $subject['is_active'] : 'yes',
                'created_at' => isset($subject['created_at']) ? $subject['created_at'] : null,
                'updated_at' => isset($subject['updated_at']) ? $subject['updated_at'] : null
            );

            json_output(200, array(
                'status' => 1,
                'message' => 'Subject record retrieved successfully',
                'data' => $formatted_subject
            ));

        } catch (Exception $e) {
            log_message('error', 'Subjects API Get Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Create new subject
     * 
     * Creates a new subject record.
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
            if (empty($input['name']) || trim($input['name']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Subject name is required and cannot be empty',
                    'data' => null
                ));
                return;
            }

            // Prepare data for insertion
            $data = array(
                'name' => trim($input['name']),
                'code' => isset($input['code']) ? trim($input['code']) : null,
                'is_active' => isset($input['is_active']) ? $input['is_active'] : 'yes'
            );

            // Create the record
            $new_id = $this->subject_model->add($data);

            // Get the created record
            $created_subject = $this->subject_model->get($new_id);

            json_output(201, array(
                'status' => 1,
                'message' => 'Subject created successfully',
                'data' => array(
                    'id' => $created_subject['id'],
                    'name' => $created_subject['name'],
                    'code' => isset($created_subject['code']) ? $created_subject['code'] : null,
                    'is_active' => isset($created_subject['is_active']) ? $created_subject['is_active'] : 'yes',
                    'created_at' => isset($created_subject['created_at']) ? $created_subject['created_at'] : null
                )
            ));

        } catch (Exception $e) {
            log_message('error', 'Subjects API Create Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Update existing subject
     *
     * Updates an existing subject record.
     *
     * @param int $id Subject ID to update
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

            // Validate ID parameter
            if ($id === null || !is_numeric($id) || $id <= 0) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing subject ID',
                    'data' => null
                ));
                return;
            }

            // Check if record exists
            $existing_subject = $this->subject_model->get($id);
            if (empty($existing_subject)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Subject record not found',
                    'data' => null
                ));
                return;
            }

            // Get input data
            $input = json_decode($this->input->raw_input_stream, true);

            // Validate required fields
            if (empty($input['name']) || trim($input['name']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Subject name is required and cannot be empty',
                    'data' => null
                ));
                return;
            }

            // Prepare data for update
            $data = array(
                'id' => $id,
                'name' => trim($input['name']),
                'code' => isset($input['code']) ? trim($input['code']) : $existing_subject['code'],
                'is_active' => isset($input['is_active']) ? $input['is_active'] : (isset($existing_subject['is_active']) ? $existing_subject['is_active'] : 'yes')
            );

            // Update the record
            $this->subject_model->add($data);

            // Get the updated record
            $updated_subject = $this->subject_model->get($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Subject updated successfully',
                'data' => array(
                    'id' => $updated_subject['id'],
                    'name' => $updated_subject['name'],
                    'code' => isset($updated_subject['code']) ? $updated_subject['code'] : null,
                    'is_active' => isset($updated_subject['is_active']) ? $updated_subject['is_active'] : 'yes',
                    'updated_at' => isset($updated_subject['updated_at']) ? $updated_subject['updated_at'] : null
                )
            ));

        } catch (Exception $e) {
            log_message('error', 'Subjects API Update Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Delete subject
     *
     * Deletes an existing subject record.
     *
     * @param int $id Subject ID to delete
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

            // Validate ID parameter
            if ($id === null || !is_numeric($id) || $id <= 0) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing subject ID',
                    'data' => null
                ));
                return;
            }

            // Check if record exists
            $existing_subject = $this->subject_model->get($id);
            if (empty($existing_subject)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Subject record not found',
                    'data' => null
                ));
                return;
            }

            // Store subject info before deletion
            $deleted_subject_info = array(
                'id' => $existing_subject['id'],
                'name' => $existing_subject['name'],
                'code' => isset($existing_subject['code']) ? $existing_subject['code'] : null
            );

            // Delete the record
            $this->subject_model->remove($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Subject deleted successfully',
                'data' => $deleted_subject_info
            ));

        } catch (Exception $e) {
            log_message('error', 'Subjects API Delete Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }
}

