<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Sections API Controller
 * 
 * This controller provides RESTful API endpoints for section management.
 * It handles CRUD operations for school sections.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Sections_api extends CI_Controller
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
                'section_model',
                'setting_model',
                'customlib'
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
     * List all sections
     * 
     * Retrieves a list of all section records.
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

            // Get all sections
            $sections = $this->section_model->get();

            // Format response data
            $formatted_sections = array();
            if (!empty($sections)) {
                foreach ($sections as $section) {
                    $formatted_sections[] = array(
                        'id' => $section['id'],
                        'section' => $section['section'],
                        'is_active' => $section['is_active'],
                        'created_at' => isset($section['created_at']) ? $section['created_at'] : null,
                        'updated_at' => isset($section['updated_at']) ? $section['updated_at'] : null
                    );
                }
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Sections retrieved successfully',
                'total_records' => count($formatted_sections),
                'data' => $formatted_sections
            ));

        } catch (Exception $e) {
            log_message('error', 'Sections API List Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get single section record
     * 
     * Retrieves detailed information for a specific section record.
     * 
     * @param int $id Section ID
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
                    'message' => 'Invalid or missing section ID',
                    'data' => null
                ));
                return;
            }

            // Get section record
            $section = $this->section_model->get($id);

            if (empty($section)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Section record not found',
                    'data' => null
                ));
                return;
            }

            // Format response data
            $formatted_section = array(
                'id' => $section['id'],
                'section' => $section['section'],
                'is_active' => $section['is_active'],
                'created_at' => isset($section['created_at']) ? $section['created_at'] : null,
                'updated_at' => isset($section['updated_at']) ? $section['updated_at'] : null
            );

            json_output(200, array(
                'status' => 1,
                'message' => 'Section record retrieved successfully',
                'data' => $formatted_section
            ));

        } catch (Exception $e) {
            log_message('error', 'Sections API Get Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Create new section
     * 
     * Creates a new section record.
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
            if (empty($input['section']) || trim($input['section']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Section name is required and cannot be empty',
                    'data' => null
                ));
                return;
            }

            // Prepare data for insertion
            $data = array(
                'section' => trim($input['section']),
                'is_active' => isset($input['is_active']) ? $input['is_active'] : 'yes'
            );

            // Create the record and get the inserted ID
            $new_id = $this->section_model->add($data);

            // Get the created record
            $created_section = $this->section_model->get($new_id);

            json_output(201, array(
                'status' => 1,
                'message' => 'Section created successfully',
                'data' => array(
                    'id' => $created_section['id'],
                    'section' => $created_section['section'],
                    'is_active' => $created_section['is_active'],
                    'created_at' => isset($created_section['created_at']) ? $created_section['created_at'] : null
                )
            ));

        } catch (Exception $e) {
            log_message('error', 'Sections API Create Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Update existing section
     *
     * Updates an existing section record.
     *
     * @param int $id Section ID to update
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
                    'message' => 'Invalid or missing section ID',
                    'data' => null
                ));
                return;
            }

            // Check if record exists
            $existing_section = $this->section_model->get($id);
            if (empty($existing_section)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Section record not found',
                    'data' => null
                ));
                return;
            }

            // Get input data
            $input = json_decode($this->input->raw_input_stream, true);

            // Validate required fields
            if (empty($input['section']) || trim($input['section']) === '') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Section name is required and cannot be empty',
                    'data' => null
                ));
                return;
            }

            // Prepare data for update
            $data = array(
                'id' => $id,
                'section' => trim($input['section']),
                'is_active' => isset($input['is_active']) ? $input['is_active'] : $existing_section['is_active']
            );

            // Update the record
            $this->section_model->add($data);

            // Get the updated record
            $updated_section = $this->section_model->get($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Section updated successfully',
                'data' => array(
                    'id' => $updated_section['id'],
                    'section' => $updated_section['section'],
                    'is_active' => $updated_section['is_active'],
                    'updated_at' => isset($updated_section['updated_at']) ? $updated_section['updated_at'] : null
                )
            ));

        } catch (Exception $e) {
            log_message('error', 'Sections API Update Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Delete section
     *
     * Deletes an existing section record.
     *
     * @param int $id Section ID to delete
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
                    'message' => 'Invalid or missing section ID',
                    'data' => null
                ));
                return;
            }

            // Check if record exists
            $existing_section = $this->section_model->get($id);
            if (empty($existing_section)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Section record not found',
                    'data' => null
                ));
                return;
            }

            // Store section info before deletion
            $deleted_section_info = array(
                'id' => $existing_section['id'],
                'section' => $existing_section['section']
            );

            // Delete the record
            $this->section_model->remove($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Section deleted successfully',
                'data' => $deleted_section_info
            ));

        } catch (Exception $e) {
            log_message('error', 'Sections API Delete Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }
}
