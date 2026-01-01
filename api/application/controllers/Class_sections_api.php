<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Sections API Controller
 * 
 * This controller provides RESTful API endpoints for managing class-section relationships.
 * It handles linking and unlinking of classes to sections via the class_sections junction table.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Class_sections_api extends CI_Controller
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
        $this->load->model(array(
            'class_model',
            'section_model',
            'classsection_model',
            'setting_model'
        ));
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
     * Link a class to a section
     * 
     * Creates a new entry in the class_sections junction table to link a class to a section.
     * 
     * @return void Outputs JSON response
     */
    public function link()
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
            if (empty($input['class_id']) || !is_numeric($input['class_id']) || $input['class_id'] <= 0) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Class ID is required and must be a positive integer',
                    'data' => null
                ));
                return;
            }

            if (empty($input['section_id']) || !is_numeric($input['section_id']) || $input['section_id'] <= 0) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Section ID is required and must be a positive integer',
                    'data' => null
                ));
                return;
            }

            $class_id = (int)$input['class_id'];
            $section_id = (int)$input['section_id'];

            // Verify class exists
            $class_exists = $this->class_model->getAll($class_id);
            if (empty($class_exists)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Class not found',
                    'data' => null
                ));
                return;
            }

            // Verify section exists
            $section_exists = $this->section_model->get($section_id);
            if (empty($section_exists)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Section not found',
                    'data' => null
                ));
                return;
            }

            // Check if link already exists
            $link_exists = $this->classsection_model->check_data_exists(array(
                'class_id' => $class_id,
                'section_id' => $section_id
            ));

            if ($link_exists) {
                json_output(409, array(
                    'status' => 0,
                    'message' => 'This class-section link already exists',
                    'data' => null
                ));
                return;
            }

            // Prepare data for insertion
            $is_active = isset($input['is_active']) ? $input['is_active'] : 'yes';
            $data = array(
                'class_id' => $class_id,
                'section_id' => $section_id,
                'is_active' => $is_active
            );

            // Insert the link
            $this->db->insert('class_sections', $data);
            $link_id = $this->db->insert_id();

            // Get the created link with details
            $created_link = $this->get_link_details($link_id);

            json_output(201, array(
                'status' => 1,
                'message' => 'Class-section link created successfully',
                'data' => $created_link
            ));

        } catch (Exception $e) {
            log_message('error', 'Class Sections API Link Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Unlink a class from a section
     * 
     * Removes an entry from the class_sections junction table.
     * 
     * @param int $id Class-section link ID
     * @return void Outputs JSON response
     */
    public function unlink($id = null)
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
                    'message' => 'Invalid or missing class-section link ID',
                    'data' => null
                ));
                return;
            }

            // Check if link exists
            $link = $this->db->where('id', $id)->get('class_sections')->row_array();
            if (empty($link)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Class-section link not found',
                    'data' => null
                ));
                return;
            }

            // Delete the link
            $this->db->where('id', $id)->delete('class_sections');

            json_output(200, array(
                'status' => 1,
                'message' => 'Class-section link removed successfully',
                'data' => array(
                    'id' => $id,
                    'class_id' => $link['class_id'],
                    'section_id' => $link['section_id']
                )
            ));

        } catch (Exception $e) {
            log_message('error', 'Class Sections API Unlink Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * List all class-section links
     * 
     * Retrieves all class-section relationships with details.
     * 
     * @return void Outputs JSON response
     */
    public function list_links()
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

            // Get all class-section links with details
            $links = $this->db->select('cs.id, cs.class_id, cs.section_id, cs.is_active, cs.created_at, cs.updated_at, c.class, s.section')
                ->from('class_sections cs')
                ->join('classes c', 'c.id = cs.class_id')
                ->join('sections s', 's.id = cs.section_id')
                ->order_by('c.class, s.section')
                ->get()
                ->result_array();

            json_output(200, array(
                'status' => 1,
                'message' => 'Class-section links retrieved successfully',
                'total_records' => count($links),
                'data' => $links
            ));

        } catch (Exception $e) {
            log_message('error', 'Class Sections API List Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get a specific class-section link
     * 
     * @param int $id Class-section link ID
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
                    'message' => 'Invalid or missing class-section link ID',
                    'data' => null
                ));
                return;
            }

            // Get the link
            $link = $this->get_link_details($id);
            if (empty($link)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Class-section link not found',
                    'data' => null
                ));
                return;
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Class-section link retrieved successfully',
                'data' => $link
            ));

        } catch (Exception $e) {
            log_message('error', 'Class Sections API Get Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Helper method to get link details with class and section information
     * 
     * @param int $id Class-section link ID
     * @return array Link details or empty array if not found
     */
    private function get_link_details($id)
    {
        return $this->db->select('cs.id, cs.class_id, cs.section_id, cs.is_active, cs.created_at, cs.updated_at, c.class, s.section')
            ->from('class_sections cs')
            ->join('classes c', 'c.id = cs.class_id')
            ->join('sections s', 's.id = cs.section_id')
            ->where('cs.id', $id)
            ->get()
            ->row_array();
    }
}

