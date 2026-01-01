<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Account Category API Controller
 * 
 * This controller provides RESTful API endpoints for managing account categories.
 * It handles creating, reading, updating, and deleting account categories.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Account_category_api extends CI_Controller
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
        $this->load->model('Account_category_model', 'category_model');
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
     * List account categories
     * 
     * Retrieves a list of all account categories with optional filtering.
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
            
            if (isset($input['is_active']) && $input['is_active'] !== '') {
                $filters['is_active'] = $input['is_active'];
            }

            if (isset($input['search']) && !empty($input['search'])) {
                $filters['search'] = $input['search'];
            }

            // Get account categories
            $categories = $this->category_model->get_account_categories($filters);

            json_output(200, array(
                'status' => 1,
                'message' => 'Account categories retrieved successfully',
                'total_records' => count($categories),
                'data' => $categories
            ));

        } catch (Exception $e) {
            log_message('error', 'Account Category API List Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get specific account category
     * 
     * Retrieves detailed information about a specific account category by its ID.
     * 
     * @param int $id Category ID
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
                    'message' => 'Invalid or missing category ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Get account category
            $category = $this->category_model->get_account_category($id);
            
            if (empty($category)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Account category not found',
                    'data' => null
                ));
                return;
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Account category retrieved successfully',
                'data' => $category
            ));

        } catch (Exception $e) {
            log_message('error', 'Account Category API Get Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Create account category
     * 
     * Creates a new account category.
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
            if (empty($input['name']) || trim($input['name']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Name is required and cannot be empty',
                    'data' => null
                ));
                return;
            }

            $name = trim($input['name']);

            // Check if name already exists
            if ($this->category_model->name_exists($name)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Account category with this name already exists',
                    'data' => null
                ));
                return;
            }

            // Prepare category data
            $category_data = array(
                'name' => $name,
                'description' => isset($input['description']) ? trim($input['description']) : '',
                'is_active' => isset($input['is_active']) ? $input['is_active'] : 'yes'
            );

            // Create account category
            $category_id = $this->category_model->create_account_category($category_data);

            if ($category_id === false) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to create account category',
                    'data' => null
                ));
                return;
            }

            // Get created category
            $category = $this->category_model->get_account_category($category_id);

            json_output(201, array(
                'status' => 1,
                'message' => 'Account category created successfully',
                'data' => $category
            ));

        } catch (Exception $e) {
            log_message('error', 'Account Category API Create Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Update account category
     * 
     * Updates an existing account category.
     * 
     * @param int $id Category ID
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
                    'message' => 'Invalid or missing category ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if category exists
            if (!$this->category_model->category_exists($id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Account category not found',
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

            // Validate name if provided
            if (isset($input['name'])) {
                $name = trim($input['name']);
                if ($name === '') {
                    json_output(400, array(
                        'status' => 0,
                        'message' => 'Name cannot be empty',
                        'data' => null
                    ));
                    return;
                }

                // Check if name already exists (excluding current record)
                if ($this->category_model->name_exists($name, $id)) {
                    json_output(400, array(
                        'status' => 0,
                        'message' => 'Account category with this name already exists',
                        'data' => null
                    ));
                    return;
                }
            }

            // Prepare update data
            $update_data = array();
            if (isset($input['name'])) {
                $update_data['name'] = trim($input['name']);
            }
            if (isset($input['description'])) {
                $update_data['description'] = trim($input['description']);
            }
            if (isset($input['is_active'])) {
                $update_data['is_active'] = $input['is_active'];
            }

            if (empty($update_data)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'No data provided for update',
                    'data' => null
                ));
                return;
            }

            // Update account category
            $result = $this->category_model->update_account_category($id, $update_data);

            if ($result === false) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to update account category',
                    'data' => null
                ));
                return;
            }

            // Get updated category
            $category = $this->category_model->get_account_category($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Account category updated successfully',
                'data' => $category
            ));

        } catch (Exception $e) {
            log_message('error', 'Account Category API Update Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Delete account category
     * 
     * Deletes an account category.
     * 
     * @param int $id Category ID
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
                    'message' => 'Invalid or missing category ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if category exists
            if (!$this->category_model->category_exists($id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Account category not found',
                    'data' => null
                ));
                return;
            }

            // Delete account category
            $result = $this->category_model->delete_account_category($id);

            if ($result === false) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to delete account category',
                    'data' => null
                ));
                return;
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Account category deleted successfully',
                'data' => null
            ));

        } catch (Exception $e) {
            log_message('error', 'Account Category API Delete Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }
}

