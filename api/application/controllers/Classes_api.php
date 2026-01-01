<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Classes API Controller
 * 
 * This controller provides RESTful API endpoints for class management.
 * It handles CRUD operations for school classes.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Classes_api extends CI_Controller
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
                'class_model',
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
     * List all classes
     * 
     * Retrieves a list of all class records.
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

            // Get all classes
            $classes = $this->class_model->getAll();

            // Format response data
            $formatted_classes = array();
            if (!empty($classes)) {
                foreach ($classes as $class) {
                    $formatted_classes[] = array(
                        'id' => $class['id'],
                        'class' => $class['class'],
                        'is_active' => $class['is_active'],
                        'created_at' => isset($class['created_at']) ? $class['created_at'] : null,
                        'updated_at' => isset($class['updated_at']) ? $class['updated_at'] : null
                    );
                }
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Classes retrieved successfully',
                'total_records' => count($formatted_classes),
                'data' => $formatted_classes
            ));

        } catch (Exception $e) {
            log_message('error', 'Classes API List Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get single class record
     * 
     * Retrieves detailed information for a specific class record.
     * 
     * @param int $id Class ID
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
                    'message' => 'Invalid or missing class ID',
                    'data' => null
                ));
                return;
            }

            // Get class record
            $class = $this->class_model->getAll($id);

            if (empty($class)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Class record not found',
                    'data' => null
                ));
                return;
            }

            // Format response data
            $formatted_class = array(
                'id' => $class['id'],
                'class' => $class['class'],
                'is_active' => $class['is_active'],
                'created_at' => isset($class['created_at']) ? $class['created_at'] : null,
                'updated_at' => isset($class['updated_at']) ? $class['updated_at'] : null
            );

            json_output(200, array(
                'status' => 1,
                'message' => 'Class record retrieved successfully',
                'data' => $formatted_class
            ));

        } catch (Exception $e) {
            log_message('error', 'Classes API Get Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Create new class
     * 
     * Creates a new class record.
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
            if (empty($input['class']) || trim($input['class']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Class name is required and cannot be empty',
                    'data' => null
                ));
                return;
            }

            // Prepare data for insertion
            $data = array(
                'class' => trim($input['class']),
                'is_active' => isset($input['is_active']) ? $input['is_active'] : 'yes'
            );

            // Create the record and get the inserted ID
            $new_id = $this->class_model->add($data);

            // Get the created record
            $created_class = $this->class_model->getAll($new_id);

            json_output(201, array(
                'status' => 1,
                'message' => 'Class created successfully',
                'data' => array(
                    'id' => $created_class['id'],
                    'class' => $created_class['class'],
                    'is_active' => $created_class['is_active'],
                    'created_at' => isset($created_class['created_at']) ? $created_class['created_at'] : null
                )
            ));

        } catch (Exception $e) {
            log_message('error', 'Classes API Create Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Update existing class
     *
     * Updates an existing class record.
     *
     * @param int $id Class ID to update
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
                    'message' => 'Invalid or missing class ID',
                    'data' => null
                ));
                return;
            }

            // Check if record exists
            $existing_class = $this->class_model->getAll($id);
            if (empty($existing_class)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Class record not found',
                    'data' => null
                ));
                return;
            }

            // Get input data
            $input = json_decode($this->input->raw_input_stream, true);

            // Validate required fields
            if (empty($input['class']) || trim($input['class']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Class name is required and cannot be empty',
                    'data' => null
                ));
                return;
            }

            // Prepare data for update
            $data = array(
                'id' => $id,
                'class' => trim($input['class']),
                'is_active' => isset($input['is_active']) ? $input['is_active'] : $existing_class['is_active']
            );

            // Update the record
            $this->class_model->add($data);

            // Get the updated record
            $updated_class = $this->class_model->getAll($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Class updated successfully',
                'data' => array(
                    'id' => $updated_class['id'],
                    'class' => $updated_class['class'],
                    'is_active' => $updated_class['is_active'],
                    'updated_at' => isset($updated_class['updated_at']) ? $updated_class['updated_at'] : null
                )
            ));

        } catch (Exception $e) {
            log_message('error', 'Classes API Update Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Delete class
     *
     * Deletes an existing class record.
     *
     * @param int $id Class ID to delete
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
                    'message' => 'Invalid or missing class ID',
                    'data' => null
                ));
                return;
            }

            // Check if record exists
            $existing_class = $this->class_model->getAll($id);
            if (empty($existing_class)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Class record not found',
                    'data' => null
                ));
                return;
            }

            // Store class info before deletion
            $deleted_class_info = array(
                'id' => $existing_class['id'],
                'class' => $existing_class['class']
            );

            // Delete the record
            $this->class_model->remove($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Class deleted successfully',
                'data' => $deleted_class_info
            ));

        } catch (Exception $e) {
            log_message('error', 'Classes API Delete Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }
}
