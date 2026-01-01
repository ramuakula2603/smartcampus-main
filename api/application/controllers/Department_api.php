<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Department API Controller
 * 
 * This controller provides RESTful API endpoints for department management.
 * It handles CRUD operations for staff departments.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Department_api extends CI_Controller
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
                'department_model',
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
     * List all departments
     * 
     * Retrieves a list of all department records.
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

            // Get all departments
            $departments = $this->department_model->getDepartmentType();

            // Format response data
            $formatted_departments = array();
            if (!empty($departments)) {
                foreach ($departments as $department) {
                    $formatted_departments[] = array(
                        'id' => $department['id'],
                        'department_name' => $department['department_name'],
                        'is_active' => $department['is_active']
                    );
                }
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Departments retrieved successfully',
                'total_records' => count($formatted_departments),
                'data' => $formatted_departments
            ));

        } catch (Exception $e) {
            log_message('error', 'Department API List Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get single department record
     * 
     * Retrieves detailed information for a specific department record.
     * 
     * @param int $id Department ID
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
                    'message' => 'Invalid or missing department ID',
                    'data' => null
                ));
                return;
            }

            // Get department record
            $department = $this->department_model->getDepartmentType($id);

            if (empty($department)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Department record not found',
                    'data' => null
                ));
                return;
            }

            // Format response data
            $formatted_department = array(
                'id' => $department['id'],
                'department_name' => $department['department_name'],
                'is_active' => $department['is_active']
            );

            json_output(200, array(
                'status' => 1,
                'message' => 'Department record retrieved successfully',
                'data' => $formatted_department
            ));

        } catch (Exception $e) {
            log_message('error', 'Department API Get Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Create new department
     * 
     * Creates a new department record.
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
            if (empty($input['department_name']) || trim($input['department_name']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Department name is required and cannot be empty',
                    'data' => null
                ));
                return;
            }

            // Check if department already exists
            if ($this->department_model->check_department_exists(trim($input['department_name']), 0)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Department name already exists',
                    'data' => null
                ));
                return;
            }

            // Prepare data for insertion
            $data = array(
                'department_name' => trim($input['department_name']),
                'is_active' => isset($input['is_active']) ? $input['is_active'] : 'yes'
            );

            // Create the record
            $new_id = $this->department_model->addDepartmentType($data);

            // Get the created record
            $created_department = $this->department_model->getDepartmentType($new_id);

            json_output(201, array(
                'status' => 1,
                'message' => 'Department created successfully',
                'data' => array(
                    'id' => $created_department['id'],
                    'department_name' => $created_department['department_name'],
                    'is_active' => $created_department['is_active']
                )
            ));

        } catch (Exception $e) {
            log_message('error', 'Department API Create Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Update existing department
     *
     * Updates an existing department record.
     *
     * @param int $id Department ID to update
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
                    'message' => 'Invalid or missing department ID',
                    'data' => null
                ));
                return;
            }

            // Check if record exists
            $existing_department = $this->department_model->getDepartmentType($id);
            if (empty($existing_department)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Department record not found',
                    'data' => null
                ));
                return;
            }

            // Get input data
            $input = json_decode($this->input->raw_input_stream, true);

            // Validate required fields
            if (empty($input['department_name']) || trim($input['department_name']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Department name is required and cannot be empty',
                    'data' => null
                ));
                return;
            }

            // Check if department name already exists (excluding current record)
            if ($this->department_model->check_department_exists(trim($input['department_name']), $id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Department name already exists',
                    'data' => null
                ));
                return;
            }

            // Prepare data for update
            $data = array(
                'id' => $id,
                'department_name' => trim($input['department_name']),
                'is_active' => isset($input['is_active']) ? $input['is_active'] : $existing_department['is_active']
            );

            // Update the record
            $this->department_model->addDepartmentType($data);

            // Get the updated record
            $updated_department = $this->department_model->getDepartmentType($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Department updated successfully',
                'data' => array(
                    'id' => $updated_department['id'],
                    'department_name' => $updated_department['department_name'],
                    'is_active' => $updated_department['is_active']
                )
            ));

        } catch (Exception $e) {
            log_message('error', 'Department API Update Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Delete department
     *
     * Deletes an existing department record.
     *
     * @param int $id Department ID to delete
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
                    'message' => 'Invalid or missing department ID',
                    'data' => null
                ));
                return;
            }

            // Check if record exists
            $existing_department = $this->department_model->getDepartmentType($id);
            if (empty($existing_department)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Department record not found',
                    'data' => null
                ));
                return;
            }

            // Store department info before deletion
            $deleted_department_info = array(
                'id' => $existing_department['id'],
                'department_name' => $existing_department['department_name']
            );

            // Delete the record
            $this->department_model->deleteDepartment($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Department deleted successfully',
                'data' => $deleted_department_info
            ));

        } catch (Exception $e) {
            log_message('error', 'Department API Delete Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }
}
