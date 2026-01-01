<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Income Head API Controller
 * 
 * This controller provides RESTful API endpoints for income head management.
 * It handles CRUD operations for income head records.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Income_head_api extends CI_Controller
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
                'incomehead_model',
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
     * List all income heads
     * 
     * Retrieves a list of all income head records.
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

            // Get all income heads
            $income_heads = $this->incomehead_model->get();

            // Format response data
            $formatted_heads = array();
            if (!empty($income_heads)) {
                foreach ($income_heads as $head) {
                    $formatted_heads[] = array(
                        'id' => $head['id'],
                        'income_category' => $head['income_category'],
                        'description' => isset($head['description']) ? $head['description'] : null,
                        'is_active' => $head['is_active'],
                        'is_deleted' => isset($head['is_deleted']) ? $head['is_deleted'] : 'no',
                        'created_at' => isset($head['created_at']) ? $head['created_at'] : null,
                        'updated_at' => isset($head['updated_at']) ? $head['updated_at'] : null
                    );
                }
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Income heads retrieved successfully',
                'total_records' => count($formatted_heads),
                'data' => $formatted_heads
            ));

        } catch (Exception $e) {
            log_message('error', 'Income Head API List Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get single income head record
     * 
     * Retrieves detailed information for a specific income head record.
     * 
     * @param int $id Income head ID
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
                    'message' => 'Invalid or missing income head ID',
                    'data' => null
                ));
                return;
            }

            // Get income head record
            $income_head = $this->incomehead_model->get($id);

            if (empty($income_head)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Income head record not found',
                    'data' => null
                ));
                return;
            }

            // Format response data
            $formatted_head = array(
                'id' => $income_head['id'],
                'income_category' => $income_head['income_category'],
                'description' => isset($income_head['description']) ? $income_head['description'] : null,
                'is_active' => $income_head['is_active'],
                'is_deleted' => isset($income_head['is_deleted']) ? $income_head['is_deleted'] : 'no',
                'created_at' => isset($income_head['created_at']) ? $income_head['created_at'] : null,
                'updated_at' => isset($income_head['updated_at']) ? $income_head['updated_at'] : null
            );

            json_output(200, array(
                'status' => 1,
                'message' => 'Income head record retrieved successfully',
                'data' => $formatted_head
            ));

        } catch (Exception $e) {
            log_message('error', 'Income Head API Get Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Create new income head
     * 
     * Creates a new income head record.
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
            if (empty($input['income_category']) || trim($input['income_category']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Income category is required and cannot be empty',
                    'data' => null
                ));
                return;
            }

            // Check if income category already exists
            if ($this->incomehead_model->check_category_exists(trim($input['income_category']), 0)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Income category already exists',
                    'data' => null
                ));
                return;
            }

            // Prepare data for insertion
            $data = array(
                'income_category' => trim($input['income_category']),
                'description' => isset($input['description']) ? trim($input['description']) : null,
                'is_active' => isset($input['is_active']) ? $input['is_active'] : 'yes',
                'is_deleted' => 'no',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );

            // Create the record
            $new_id = $this->incomehead_model->add($data);

            // Get the created record
            $created_head = $this->incomehead_model->get($new_id);

            json_output(201, array(
                'status' => 1,
                'message' => 'Income head created successfully',
                'data' => array(
                    'id' => $created_head['id'],
                    'income_category' => $created_head['income_category'],
                    'description' => $created_head['description'],
                    'is_active' => $created_head['is_active'],
                    'created_at' => $created_head['created_at']
                )
            ));

        } catch (Exception $e) {
            log_message('error', 'Income Head API Create Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Update existing income head
     *
     * Updates an existing income head record.
     *
     * @param int $id Income head ID to update
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
                    'message' => 'Invalid or missing income head ID',
                    'data' => null
                ));
                return;
            }

            // Check if record exists
            $existing_head = $this->incomehead_model->get($id);
            if (empty($existing_head)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Income head record not found',
                    'data' => null
                ));
                return;
            }

            // Get input data
            $input = json_decode($this->input->raw_input_stream, true);

            // Validate required fields
            if (empty($input['income_category']) || trim($input['income_category']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Income category is required and cannot be empty',
                    'data' => null
                ));
                return;
            }

            // Check if income category already exists (excluding current record)
            if ($this->incomehead_model->check_category_exists(trim($input['income_category']), $id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Income category already exists',
                    'data' => null
                ));
                return;
            }

            // Prepare data for update
            $data = array(
                'id' => $id,
                'income_category' => trim($input['income_category']),
                'description' => isset($input['description']) ? trim($input['description']) : $existing_head['description'],
                'is_active' => isset($input['is_active']) ? $input['is_active'] : $existing_head['is_active'],
                'updated_at' => date('Y-m-d H:i:s')
            );

            // Update the record
            $this->incomehead_model->add($data);

            // Get the updated record
            $updated_head = $this->incomehead_model->get($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Income head updated successfully',
                'data' => array(
                    'id' => $updated_head['id'],
                    'income_category' => $updated_head['income_category'],
                    'description' => $updated_head['description'],
                    'is_active' => $updated_head['is_active'],
                    'updated_at' => $updated_head['updated_at']
                )
            ));

        } catch (Exception $e) {
            log_message('error', 'Income Head API Update Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Delete income head
     *
     * Deletes an existing income head record.
     *
     * @param int $id Income head ID to delete
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
                    'message' => 'Invalid or missing income head ID',
                    'data' => null
                ));
                return;
            }

            // Check if record exists
            $existing_head = $this->incomehead_model->get($id);
            if (empty($existing_head)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Income head record not found',
                    'data' => null
                ));
                return;
            }

            // Store income head info before deletion
            $deleted_head_info = array(
                'id' => $existing_head['id'],
                'income_category' => $existing_head['income_category']
            );

            // Delete the record
            $this->incomehead_model->remove($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Income head deleted successfully',
                'data' => $deleted_head_info
            ));

        } catch (Exception $e) {
            log_message('error', 'Income Head API Delete Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }
}
