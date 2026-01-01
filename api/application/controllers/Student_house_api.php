<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Student House API Controller
 * 
 * This controller provides RESTful API endpoints for student house management.
 * It handles CRUD operations for school house records.
 * 
 * @package    Student Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Student_house_api extends CI_Controller
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
                'schoolhouse_model',
                'setting_model'
            ));
        } catch (Exception $e) {
            log_message('error', 'Error loading models: ' . $e->getMessage());
        }

        // Load form validation library
        $this->load->library('form_validation');

        // Get school settings
        try {
            $this->sch_setting_detail = $this->setting_model->getSetting();
            
            // Set timezone
            if (isset($this->sch_setting_detail->timezone) && $this->sch_setting_detail->timezone != "") {
                date_default_timezone_set($this->sch_setting_detail->timezone);
            } else {
                date_default_timezone_set('UTC');
            }
        } catch (Exception $e) {
            log_message('error', 'Error loading school settings: ' . $e->getMessage());
            date_default_timezone_set('UTC');
        }
    }

    /**
     * Validate request headers
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
     * List all student houses
     * 
     * Retrieves a list of all student house records.
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

            // Get all student houses
            $houses = $this->schoolhouse_model->get();

            // Format response data
            $formatted_houses = array();
            if (!empty($houses)) {
                foreach ($houses as $house) {
                    $formatted_houses[] = array(
                        'id' => $house['id'],
                        'house_name' => $house['house_name'],
                        'description' => $house['description'],
                        'is_active' => $house['is_active'],
                        'created_at' => isset($house['created_at']) ? $house['created_at'] : null,
                        'updated_at' => isset($house['updated_at']) ? $house['updated_at'] : null
                    );
                }
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Student houses retrieved successfully',
                'total_records' => count($formatted_houses),
                'data' => $formatted_houses
            ));

        } catch (Exception $e) {
            log_message('error', 'Student House API List Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get single student house record
     * 
     * Retrieves detailed information for a specific student house record.
     * 
     * @param int $id Student house ID
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
                    'message' => 'Invalid or missing student house ID',
                    'data' => null
                ));
                return;
            }

            // Get student house record
            $house = $this->schoolhouse_model->get($id);

            if (empty($house)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Student house record not found',
                    'data' => null
                ));
                return;
            }

            // Format response data
            $formatted_house = array(
                'id' => $house['id'],
                'house_name' => $house['house_name'],
                'description' => $house['description'],
                'is_active' => $house['is_active'],
                'created_at' => isset($house['created_at']) ? $house['created_at'] : null,
                'updated_at' => isset($house['updated_at']) ? $house['updated_at'] : null
            );

            json_output(200, array(
                'status' => 1,
                'message' => 'Student house record retrieved successfully',
                'data' => $formatted_house
            ));

        } catch (Exception $e) {
            log_message('error', 'Student House API Get Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Create new student house
     * 
     * Creates a new student house record.
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
            if (empty($input['house_name']) || trim($input['house_name']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'House name is required and cannot be empty',
                    'data' => null
                ));
                return;
            }

            // Prepare data for insertion
            $data = array(
                'house_name' => trim($input['house_name']),
                'description' => isset($input['description']) ? trim($input['description']) : '',
                'is_active' => 'yes'
            );

            // Insert the record
            $this->schoolhouse_model->add($data);

            // Get the created record (since add method doesn't return ID, we'll get the latest)
            $houses = $this->schoolhouse_model->get();
            $created_house = null;
            
            // Find the house we just created by name (assuming it's unique)
            foreach ($houses as $house) {
                if ($house['house_name'] === $data['house_name']) {
                    $created_house = $house;
                    break;
                }
            }

            if ($created_house) {
                json_output(201, array(
                    'status' => 1,
                    'message' => 'Student house created successfully',
                    'data' => array(
                        'id' => $created_house['id'],
                        'house_name' => $created_house['house_name'],
                        'description' => $created_house['description'],
                        'is_active' => $created_house['is_active'],
                        'created_at' => isset($created_house['created_at']) ? $created_house['created_at'] : null
                    )
                ));
            } else {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to create student house',
                    'data' => null
                ));
            }

        } catch (Exception $e) {
            log_message('error', 'Student House API Create Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Update existing student house
     * 
     * Updates an existing student house record.
     * 
     * @param int $id Student house ID
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
                    'message' => 'Invalid or missing student house ID',
                    'data' => null
                ));
                return;
            }

            // Check if record exists
            $existing_house = $this->schoolhouse_model->get($id);
            if (empty($existing_house)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Student house record not found',
                    'data' => null
                ));
                return;
            }

            // Get input data
            $input = json_decode($this->input->raw_input_stream, true);

            // Validate required fields
            if (empty($input['house_name']) || trim($input['house_name']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'House name is required and cannot be empty',
                    'data' => null
                ));
                return;
            }

            // Prepare data for update
            $data = array(
                'id' => $id,
                'house_name' => trim($input['house_name']),
                'description' => isset($input['description']) ? trim($input['description']) : $existing_house['description'],
                'is_active' => isset($input['is_active']) ? $input['is_active'] : $existing_house['is_active']
            );

            // Update the record
            $this->schoolhouse_model->add($data);

            // Get the updated record
            $updated_house = $this->schoolhouse_model->get($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Student house updated successfully',
                'data' => array(
                    'id' => $updated_house['id'],
                    'house_name' => $updated_house['house_name'],
                    'description' => $updated_house['description'],
                    'is_active' => $updated_house['is_active'],
                    'updated_at' => isset($updated_house['updated_at']) ? $updated_house['updated_at'] : null
                )
            ));

        } catch (Exception $e) {
            log_message('error', 'Student House API Update Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Delete student house
     *
     * Deletes an existing student house record.
     *
     * @param int $id Student house ID
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
                    'message' => 'Invalid or missing student house ID',
                    'data' => null
                ));
                return;
            }

            // Check if record exists
            $existing_house = $this->schoolhouse_model->get($id);
            if (empty($existing_house)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Student house record not found',
                    'data' => null
                ));
                return;
            }

            // Store the house data before deletion for response
            $deleted_house = array(
                'id' => $existing_house['id'],
                'house_name' => $existing_house['house_name'],
                'description' => $existing_house['description']
            );

            // Delete the record
            $this->schoolhouse_model->delete($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Student house deleted successfully',
                'data' => $deleted_house
            ));

        } catch (Exception $e) {
            log_message('error', 'Student House API Delete Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }
}
