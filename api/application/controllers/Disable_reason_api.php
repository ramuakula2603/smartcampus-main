<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Disable Reason API Controller
 * 
 * This controller provides RESTful API endpoints for disable reason management.
 * It handles CRUD operations for student disable reasons.
 * 
 * @package    Student Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Disable_reason_api extends CI_Controller
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
            $this->load->model('disable_reason_model');
            $this->load->model('setting_model');
        } catch (Exception $e) {
            log_message('error', 'Error loading models: ' . $e->getMessage());
            // Set default values if models fail to load
            $this->setting_model = null;
            $this->disable_reason_model = null;
        }

        // Load form validation library
        $this->load->library('form_validation');

        // Get school settings
        try {
            if ($this->setting_model !== null) {
                $this->sch_setting_detail = $this->setting_model->getSetting();

                // Set timezone
                if (isset($this->sch_setting_detail->timezone) && $this->sch_setting_detail->timezone != "") {
                    date_default_timezone_set($this->sch_setting_detail->timezone);
                } else {
                    date_default_timezone_set('UTC');
                }
            } else {
                log_message('warning', 'Setting model not loaded, using default timezone');
                date_default_timezone_set('UTC');
                $this->sch_setting_detail = null;
            }
        } catch (Exception $e) {
            log_message('error', 'Error loading school settings: ' . $e->getMessage());
            date_default_timezone_set('UTC');
            $this->sch_setting_detail = null;
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
     * List all disable reasons
     * 
     * Retrieves a list of all disable reason records.
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

            // Get all disable reasons
            $disable_reasons = $this->disable_reason_model->get();

            // Format response data
            $formatted_reasons = array();
            if (!empty($disable_reasons)) {
                foreach ($disable_reasons as $reason) {
                    $formatted_reasons[] = array(
                        'id' => $reason['id'],
                        'reason' => $reason['reason'],
                        'created_at' => isset($reason['created_at']) ? $reason['created_at'] : null,
                        'updated_at' => isset($reason['updated_at']) ? $reason['updated_at'] : null
                    );
                }
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Disable reasons retrieved successfully',
                'total_records' => count($formatted_reasons),
                'data' => $formatted_reasons
            ));

        } catch (Exception $e) {
            log_message('error', 'Disable Reason API List Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get single disable reason record
     * 
     * Retrieves detailed information for a specific disable reason record.
     * 
     * @param int $id Disable reason ID
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
                    'message' => 'Invalid or missing disable reason ID',
                    'data' => null
                ));
                return;
            }

            // Get disable reason record
            $disable_reason = $this->disable_reason_model->get($id);

            if (empty($disable_reason)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Disable reason record not found',
                    'data' => null
                ));
                return;
            }

            // Format response data
            $formatted_reason = array(
                'id' => $disable_reason['id'],
                'reason' => $disable_reason['reason'],
                'created_at' => isset($disable_reason['created_at']) ? $disable_reason['created_at'] : null,
                'updated_at' => isset($disable_reason['updated_at']) ? $disable_reason['updated_at'] : null
            );

            json_output(200, array(
                'status' => 1,
                'message' => 'Disable reason record retrieved successfully',
                'data' => $formatted_reason
            ));

        } catch (Exception $e) {
            log_message('error', 'Disable Reason API Get Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Create new disable reason
     * 
     * Creates a new disable reason record.
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
            if (empty($input['reason']) || trim($input['reason']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Disable reason is required and cannot be empty',
                    'data' => null
                ));
                return;
            }

            // Prepare data for insertion
            $data = array(
                'reason' => trim($input['reason'])
            );

            // Insert the record
            $insert_id = $this->disable_reason_model->add($data);

            if ($insert_id) {
                // Get the created record
                $created_reason = $this->disable_reason_model->get($insert_id);

                json_output(201, array(
                    'status' => 1,
                    'message' => 'Disable reason created successfully',
                    'data' => array(
                        'id' => $created_reason['id'],
                        'reason' => $created_reason['reason'],
                        'created_at' => isset($created_reason['created_at']) ? $created_reason['created_at'] : null
                    )
                ));
            } else {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to create disable reason',
                    'data' => null
                ));
            }

        } catch (Exception $e) {
            log_message('error', 'Disable Reason API Create Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Update existing disable reason
     * 
     * Updates an existing disable reason record.
     * 
     * @param int $id Disable reason ID
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
                    'message' => 'Invalid or missing disable reason ID',
                    'data' => null
                ));
                return;
            }

            // Check if record exists
            $existing_reason = $this->disable_reason_model->get($id);
            if (empty($existing_reason)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Disable reason record not found',
                    'data' => null
                ));
                return;
            }

            // Get input data
            $input = json_decode($this->input->raw_input_stream, true);

            // Validate required fields
            if (empty($input['reason']) || trim($input['reason']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Disable reason is required and cannot be empty',
                    'data' => null
                ));
                return;
            }

            // Prepare data for update
            $data = array(
                'id' => $id,
                'reason' => trim($input['reason'])
            );

            // Update the record
            $this->disable_reason_model->add($data);

            // Get the updated record
            $updated_reason = $this->disable_reason_model->get($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Disable reason updated successfully',
                'data' => array(
                    'id' => $updated_reason['id'],
                    'reason' => $updated_reason['reason'],
                    'updated_at' => isset($updated_reason['updated_at']) ? $updated_reason['updated_at'] : null
                )
            ));

        } catch (Exception $e) {
            log_message('error', 'Disable Reason API Update Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Delete disable reason
     *
     * Deletes an existing disable reason record.
     *
     * @param int $id Disable reason ID
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
                    'message' => 'Invalid or missing disable reason ID',
                    'data' => null
                ));
                return;
            }

            // Check if record exists
            $existing_reason = $this->disable_reason_model->get($id);
            if (empty($existing_reason)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Disable reason record not found',
                    'data' => null
                ));
                return;
            }

            // Store the reason data before deletion for response
            $deleted_reason = array(
                'id' => $existing_reason['id'],
                'reason' => $existing_reason['reason']
            );

            // Delete the record
            $this->disable_reason_model->remove($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Disable reason deleted successfully',
                'data' => $deleted_reason
            ));

        } catch (Exception $e) {
            log_message('error', 'Disable Reason API Delete Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }
}
