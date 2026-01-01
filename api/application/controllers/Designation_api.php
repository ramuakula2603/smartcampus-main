<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Designation API Controller
 * 
 * This controller provides RESTful API endpoints for designation management.
 * It handles CRUD operations for staff designations.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Designation_api extends CI_Controller
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
                'designation_model',
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
     * List all designations
     * 
     * Retrieves a list of all designation records.
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

            // Get all designations
            $designations = $this->designation_model->get();

            // Format response data
            $formatted_designations = array();
            if (!empty($designations)) {
                foreach ($designations as $designation) {
                    $formatted_designations[] = array(
                        'id' => $designation['id'],
                        'designation' => $designation['designation'],
                        'is_active' => $designation['is_active']
                    );
                }
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Designations retrieved successfully',
                'total_records' => count($formatted_designations),
                'data' => $formatted_designations
            ));

        } catch (Exception $e) {
            log_message('error', 'Designation API List Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get single designation record
     * 
     * Retrieves detailed information for a specific designation record.
     * 
     * @param int $id Designation ID
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
                    'message' => 'Invalid or missing designation ID',
                    'data' => null
                ));
                return;
            }

            // Get designation record
            $designation = $this->designation_model->get($id);

            if (empty($designation)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Designation record not found',
                    'data' => null
                ));
                return;
            }

            // Format response data
            $formatted_designation = array(
                'id' => $designation['id'],
                'designation' => $designation['designation'],
                'is_active' => $designation['is_active']
            );

            json_output(200, array(
                'status' => 1,
                'message' => 'Designation record retrieved successfully',
                'data' => $formatted_designation
            ));

        } catch (Exception $e) {
            log_message('error', 'Designation API Get Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Create new designation
     * 
     * Creates a new designation record.
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
            if (empty($input['designation']) || trim($input['designation']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Designation name is required and cannot be empty',
                    'data' => null
                ));
                return;
            }

            // Check if designation already exists
            if ($this->designation_model->check_designation_exists(trim($input['designation']), 0)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Designation name already exists',
                    'data' => null
                ));
                return;
            }

            // Prepare data for insertion
            $data = array(
                'designation' => trim($input['designation']),
                'is_active' => isset($input['is_active']) ? $input['is_active'] : 'yes'
            );

            // Create the record
            $new_id = $this->designation_model->addDesignation($data);

            // Get the created record
            $created_designation = $this->designation_model->get($new_id);

            json_output(201, array(
                'status' => 1,
                'message' => 'Designation created successfully',
                'data' => array(
                    'id' => $created_designation['id'],
                    'designation' => $created_designation['designation'],
                    'is_active' => $created_designation['is_active']
                )
            ));

        } catch (Exception $e) {
            log_message('error', 'Designation API Create Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Update existing designation
     *
     * Updates an existing designation record.
     *
     * @param int $id Designation ID to update
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
                    'message' => 'Invalid or missing designation ID',
                    'data' => null
                ));
                return;
            }

            // Check if record exists
            $existing_designation = $this->designation_model->get($id);
            if (empty($existing_designation)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Designation record not found',
                    'data' => null
                ));
                return;
            }

            // Get input data
            $input = json_decode($this->input->raw_input_stream, true);

            // Validate required fields
            if (empty($input['designation']) || trim($input['designation']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Designation name is required and cannot be empty',
                    'data' => null
                ));
                return;
            }

            // Check if designation name already exists (excluding current record)
            if ($this->designation_model->check_designation_exists(trim($input['designation']), $id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Designation name already exists',
                    'data' => null
                ));
                return;
            }

            // Prepare data for update
            $data = array(
                'id' => $id,
                'designation' => trim($input['designation']),
                'is_active' => isset($input['is_active']) ? $input['is_active'] : $existing_designation['is_active']
            );

            // Update the record
            $this->designation_model->addDesignation($data);

            // Get the updated record
            $updated_designation = $this->designation_model->get($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Designation updated successfully',
                'data' => array(
                    'id' => $updated_designation['id'],
                    'designation' => $updated_designation['designation'],
                    'is_active' => $updated_designation['is_active']
                )
            ));

        } catch (Exception $e) {
            log_message('error', 'Designation API Update Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Delete designation
     *
     * Deletes an existing designation record.
     *
     * @param int $id Designation ID to delete
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
                    'message' => 'Invalid or missing designation ID',
                    'data' => null
                ));
                return;
            }

            // Check if record exists
            $existing_designation = $this->designation_model->get($id);
            if (empty($existing_designation)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Designation record not found',
                    'data' => null
                ));
                return;
            }

            // Store designation info before deletion
            $deleted_designation_info = array(
                'id' => $existing_designation['id'],
                'designation' => $existing_designation['designation']
            );

            // Delete the record
            $this->designation_model->deleteDesignation($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Designation deleted successfully',
                'data' => $deleted_designation_info
            ));

        } catch (Exception $e) {
            log_message('error', 'Designation API Delete Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }
}
