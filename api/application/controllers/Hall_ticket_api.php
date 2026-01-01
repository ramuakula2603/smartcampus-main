<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Hall Ticket API Controller
 * 
 * This controller provides RESTful API endpoints for managing hall tickets.
 * It handles creating, reading, updating, and deleting hall ticket templates,
 * subjects, subject groups, and generating hall tickets for students.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Hall_ticket_api extends CI_Controller
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
        $this->load->model('Hall_ticket_model', 'hall_ticket_model');
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
     * List hall ticket templates
     * 
     * Retrieves a list of hall ticket templates with optional filters.
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

            // Get hall ticket templates
            $templates = $this->hall_ticket_model->get_templates($filters);

            json_output(200, array(
                'status' => 1,
                'message' => 'Hall ticket templates retrieved successfully',
                'total_records' => count($templates),
                'data' => $templates
            ));

        } catch (Exception $e) {
            log_message('error', 'Hall Ticket API List Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get specific hall ticket template
     * 
     * Retrieves detailed information about a specific hall ticket template by its ID.
     * 
     * @param int $id Template ID
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
                    'message' => 'Invalid or missing template ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Get template
            $template = $this->hall_ticket_model->get_template($id);
            
            if (empty($template)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Hall ticket template not found',
                    'data' => null
                ));
                return;
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Hall ticket template retrieved successfully',
                'data' => $template
            ));

        } catch (Exception $e) {
            log_message('error', 'Hall Ticket API Get Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Create hall ticket template
     * 
     * Creates a new hall ticket template.
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
            if (empty($input['halltickect_name']) || !is_string($input['halltickect_name'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing halltickect_name',
                    'data' => null
                ));
                return;
            }

            if (empty($input['schoolname']) || !is_string($input['schoolname'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing schoolname',
                    'data' => null
                ));
                return;
            }

            if (empty($input['address']) || !is_string($input['address'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing address',
                    'data' => null
                ));
                return;
            }

            if (empty($input['email']) || !is_string($input['email'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing email',
                    'data' => null
                ));
                return;
            }

            if (empty($input['phone']) || !is_string($input['phone'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing phone',
                    'data' => null
                ));
                return;
            }

            if (empty($input['examheading']) || !is_string($input['examheading'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing examheading',
                    'data' => null
                ));
                return;
            }

            // Prepare template data
            $template_data = array(
                'halltickect_name' => trim($input['halltickect_name']),
                'schoolname' => trim($input['schoolname']),
                'address' => trim($input['address']),
                'email' => trim($input['email']),
                'phone' => trim($input['phone']),
                'toplefttext' => isset($input['toplefttext']) ? $input['toplefttext'] : '',
                'topmiddletext' => isset($input['topmiddletext']) ? $input['topmiddletext'] : '',
                'toprighttext' => isset($input['toprighttext']) ? $input['toprighttext'] : '',
                'bottomlefttext' => isset($input['bottomlefttext']) ? $input['bottomlefttext'] : '',
                'bottommiddletext' => isset($input['bottommiddletext']) ? $input['bottommiddletext'] : '',
                'bottomrighttext' => isset($input['bottomrighttext']) ? $input['bottomrighttext'] : '',
                'examheading' => trim($input['examheading']),
                'sessionid' => isset($input['sessionid']) ? (int)$input['sessionid'] : 0,
                'is_active' => isset($input['is_active']) ? $input['is_active'] : 'yes'
            );

            // Create template
            $template_id = $this->hall_ticket_model->create_template($template_data);

            if ($template_id === false) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to create hall ticket template',
                    'data' => null
                ));
                return;
            }

            // Get created template
            $template = $this->hall_ticket_model->get_template($template_id);

            json_output(201, array(
                'status' => 1,
                'message' => 'Hall ticket template created successfully',
                'data' => $template
            ));

        } catch (Exception $e) {
            log_message('error', 'Hall Ticket API Create Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Update hall ticket template
     * 
     * Updates an existing hall ticket template.
     * 
     * @param int $id Template ID
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
                    'message' => 'Invalid or missing template ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if template exists
            if (!$this->hall_ticket_model->template_exists($id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Hall ticket template not found',
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

            // Prepare update data
            $update_data = array();
            
            if (isset($input['halltickect_name'])) {
                if (empty($input['halltickect_name']) || !is_string($input['halltickect_name'])) {
                    json_output(400, array(
                        'status' => 0,
                        'message' => 'Invalid halltickect_name',
                        'data' => null
                    ));
                    return;
                }
                $update_data['halltickect_name'] = trim($input['halltickect_name']);
            }
            
            if (isset($input['schoolname'])) {
                if (empty($input['schoolname']) || !is_string($input['schoolname'])) {
                    json_output(400, array(
                        'status' => 0,
                        'message' => 'Invalid schoolname',
                        'data' => null
                    ));
                    return;
                }
                $update_data['schoolname'] = trim($input['schoolname']);
            }
            
            if (isset($input['address'])) {
                if (empty($input['address']) || !is_string($input['address'])) {
                    json_output(400, array(
                        'status' => 0,
                        'message' => 'Invalid address',
                        'data' => null
                    ));
                    return;
                }
                $update_data['address'] = trim($input['address']);
            }
            
            if (isset($input['email'])) {
                if (empty($input['email']) || !is_string($input['email'])) {
                    json_output(400, array(
                        'status' => 0,
                        'message' => 'Invalid email',
                        'data' => null
                    ));
                    return;
                }
                $update_data['email'] = trim($input['email']);
            }
            
            if (isset($input['phone'])) {
                if (empty($input['phone']) || !is_string($input['phone'])) {
                    json_output(400, array(
                        'status' => 0,
                        'message' => 'Invalid phone',
                        'data' => null
                    ));
                    return;
                }
                $update_data['phone'] = trim($input['phone']);
            }
            
            if (isset($input['examheading'])) {
                if (empty($input['examheading']) || !is_string($input['examheading'])) {
                    json_output(400, array(
                        'status' => 0,
                        'message' => 'Invalid examheading',
                        'data' => null
                    ));
                    return;
                }
                $update_data['examheading'] = trim($input['examheading']);
            }
            
            if (isset($input['toplefttext'])) {
                $update_data['toplefttext'] = $input['toplefttext'];
            }
            
            if (isset($input['topmiddletext'])) {
                $update_data['topmiddletext'] = $input['topmiddletext'];
            }
            
            if (isset($input['toprighttext'])) {
                $update_data['toprighttext'] = $input['toprighttext'];
            }
            
            if (isset($input['bottomlefttext'])) {
                $update_data['bottomlefttext'] = $input['bottomlefttext'];
            }
            
            if (isset($input['bottommiddletext'])) {
                $update_data['bottommiddletext'] = $input['bottommiddletext'];
            }
            
            if (isset($input['bottomrighttext'])) {
                $update_data['bottomrighttext'] = $input['bottomrighttext'];
            }
            
            if (isset($input['sessionid'])) {
                $update_data['sessionid'] = (int)$input['sessionid'];
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

            // Update template
            $result = $this->hall_ticket_model->update_template($id, $update_data);

            if ($result === false) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to update hall ticket template',
                    'data' => null
                ));
                return;
            }

            // Get updated template
            $template = $this->hall_ticket_model->get_template($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Hall ticket template updated successfully',
                'data' => $template
            ));

        } catch (Exception $e) {
            log_message('error', 'Hall Ticket API Update Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Delete hall ticket template
     * 
     * Deletes a hall ticket template.
     * 
     * @param int $id Template ID
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
                    'message' => 'Invalid or missing template ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if template exists
            if (!$this->hall_ticket_model->template_exists($id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Hall ticket template not found',
                    'data' => null
                ));
                return;
            }

            // Delete template
            $result = $this->hall_ticket_model->delete_template($id);

            if ($result === false) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to delete hall ticket template',
                    'data' => null
                ));
                return;
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Hall ticket template deleted successfully',
                'data' => null
            ));

        } catch (Exception $e) {
            log_message('error', 'Hall Ticket API Delete Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * List subjects
     * 
     * Retrieves a list of subjects for hall tickets.
     * 
     * @return void Outputs JSON response
     */
    public function subjects()
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

            // Get subjects
            $subjects = $this->hall_ticket_model->get_subjects();

            json_output(200, array(
                'status' => 1,
                'message' => 'Subjects retrieved successfully',
                'total_records' => count($subjects),
                'data' => $subjects
            ));

        } catch (Exception $e) {
            log_message('error', 'Hall Ticket API Subjects Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Create subject
     * 
     * Creates a new subject for hall tickets.
     * 
     * @return void Outputs JSON response
     */
    public function create_subject()
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
            if (empty($input['name']) || !is_string($input['name'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing name',
                    'data' => null
                ));
                return;
            }

            if (empty($input['subject_code']) || !is_string($input['subject_code'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing subject_code',
                    'data' => null
                ));
                return;
            }

            // Check if subject code already exists
            if ($this->hall_ticket_model->subject_code_exists($input['subject_code'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Subject code already exists',
                    'data' => null
                ));
                return;
            }

            // Prepare subject data
            $subject_data = array(
                'name' => trim($input['name']),
                'subject_code' => strtoupper(trim($input['subject_code']))
            );

            // Create subject
            $subject_id = $this->hall_ticket_model->create_subject($subject_data);

            if ($subject_id === false) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to create subject',
                    'data' => null
                ));
                return;
            }

            // Get created subject
            $subject = $this->hall_ticket_model->get_subject($subject_id);

            json_output(201, array(
                'status' => 1,
                'message' => 'Subject created successfully',
                'data' => $subject
            ));

        } catch (Exception $e) {
            log_message('error', 'Hall Ticket API Create Subject Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Update subject
     * 
     * Updates an existing subject.
     * 
     * @param int $id Subject ID
     * @return void Outputs JSON response
     */
    public function update_subject($id = null)
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
                    'message' => 'Invalid or missing subject ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if subject exists
            if (!$this->hall_ticket_model->subject_exists($id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Subject not found',
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

            // Validate fields if provided
            if (isset($input['name']) && (empty($input['name']) || !is_string($input['name']))) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid name',
                    'data' => null
                ));
                return;
            }

            if (isset($input['subject_code']) && (empty($input['subject_code']) || !is_string($input['subject_code']))) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid subject_code',
                    'data' => null
                ));
                return;
            }

            // Check if subject code already exists (excluding current subject)
            if (isset($input['subject_code']) && $this->hall_ticket_model->subject_code_exists($input['subject_code'], $id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Subject code already exists',
                    'data' => null
                ));
                return;
            }

            // Prepare update data
            $update_data = array();
            
            if (isset($input['name'])) {
                $update_data['name'] = trim($input['name']);
            }
            if (isset($input['subject_code'])) {
                $update_data['subject_code'] = strtoupper(trim($input['subject_code']));
            }

            if (empty($update_data)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'No data provided for update',
                    'data' => null
                ));
                return;
            }

            // Update subject
            $result = $this->hall_ticket_model->update_subject($id, $update_data);

            if ($result === false) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to update subject',
                    'data' => null
                ));
                return;
            }

            // Get updated subject
            $subject = $this->hall_ticket_model->get_subject($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Subject updated successfully',
                'data' => $subject
            ));

        } catch (Exception $e) {
            log_message('error', 'Hall Ticket API Update Subject Error: ' . $e->getMessage());
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
     * Deletes a subject.
     * 
     * @param int $id Subject ID
     * @return void Outputs JSON response
     */
    public function delete_subject($id = null)
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
                    'message' => 'Invalid or missing subject ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if subject exists
            if (!$this->hall_ticket_model->subject_exists($id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Subject not found',
                    'data' => null
                ));
                return;
            }

            // Delete subject
            $result = $this->hall_ticket_model->delete_subject($id);

            if ($result === false) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to delete subject',
                    'data' => null
                ));
                return;
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Subject deleted successfully',
                'data' => null
            ));

        } catch (Exception $e) {
            log_message('error', 'Hall Ticket API Delete Subject Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * List subject groups
     * 
     * Retrieves a list of subject groups for hall tickets.
     * 
     * @return void Outputs JSON response
     */
    public function subject_groups()
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

            // Get subject groups
            $groups = $this->hall_ticket_model->get_subject_groups();

            json_output(200, array(
                'status' => 1,
                'message' => 'Subject groups retrieved successfully',
                'total_records' => count($groups),
                'data' => $groups
            ));

        } catch (Exception $e) {
            log_message('error', 'Hall Ticket API Subject Groups Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Create subject group
     * 
     * Creates a new subject group for hall tickets.
     * 
     * @return void Outputs JSON response
     */
    public function create_subject_group()
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
            if (empty($input['name']) || !is_string($input['name'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing name',
                    'data' => null
                ));
                return;
            }

            // Prepare subject group data
            $group_data = array(
                'name' => trim($input['name'])
            );

            // Create subject group
            $group_id = $this->hall_ticket_model->create_subject_group($group_data);

            if ($group_id === false) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to create subject group',
                    'data' => null
                ));
                return;
            }

            // Get created subject group
            $group = $this->hall_ticket_model->get_subject_group($group_id);

            json_output(201, array(
                'status' => 1,
                'message' => 'Subject group created successfully',
                'data' => $group
            ));

        } catch (Exception $e) {
            log_message('error', 'Hall Ticket API Create Subject Group Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Update subject group
     * 
     * Updates an existing subject group.
     * 
     * @param int $id Subject Group ID
     * @return void Outputs JSON response
     */
    public function update_subject_group($id = null)
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
                    'message' => 'Invalid or missing subject group ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if subject group exists
            if (!$this->hall_ticket_model->subject_group_exists($id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Subject group not found',
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

            // Validate fields if provided
            if (isset($input['name']) && (empty($input['name']) || !is_string($input['name']))) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid name',
                    'data' => null
                ));
                return;
            }

            // Prepare update data
            $update_data = array();
            
            if (isset($input['name'])) {
                $update_data['name'] = trim($input['name']);
            }

            if (empty($update_data)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'No data provided for update',
                    'data' => null
                ));
                return;
            }

            // Update subject group
            $result = $this->hall_ticket_model->update_subject_group($id, $update_data);

            if ($result === false) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to update subject group',
                    'data' => null
                ));
                return;
            }

            // Get updated subject group
            $group = $this->hall_ticket_model->get_subject_group($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Subject group updated successfully',
                'data' => $group
            ));

        } catch (Exception $e) {
            log_message('error', 'Hall Ticket API Update Subject Group Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Delete subject group
     * 
     * Deletes a subject group.
     * 
     * @param int $id Subject Group ID
     * @return void Outputs JSON response
     */
    public function delete_subject_group($id = null)
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
                    'message' => 'Invalid or missing subject group ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if subject group exists
            if (!$this->hall_ticket_model->subject_group_exists($id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Subject group not found',
                    'data' => null
                ));
                return;
            }

            // Delete subject group
            $result = $this->hall_ticket_model->delete_subject_group($id);

            if ($result === false) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to delete subject group',
                    'data' => null
                ));
                return;
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Subject group deleted successfully',
                'data' => null
            ));

        } catch (Exception $e) {
            log_message('error', 'Hall Ticket API Delete Subject Group Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * List subject combinations
     * 
     * Retrieves a list of subject combinations for hall tickets.
     * 
     * @return void Outputs JSON response
     */
    public function subject_combinations()
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

            // Get input data for optional filtering
            $input = json_decode($this->input->raw_input_stream, true);
            if ($input === null) {
                $input = array();
            }

            // Get subject combinations with optional filtering
            $filters = array();
            if (isset($input['subjectgrp_id']) && !empty($input['subjectgrp_id'])) {
                $filters['subjectgrp_id'] = (int)$input['subjectgrp_id'];
            }

            $combinations = $this->hall_ticket_model->get_subject_combinations($filters);

            json_output(200, array(
                'status' => 1,
                'message' => 'Subject combinations retrieved successfully',
                'total_records' => count($combinations),
                'data' => $combinations
            ));

        } catch (Exception $e) {
            log_message('error', 'Hall Ticket API Subject Combinations Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Create subject combination
     * 
     * Creates a new subject combination for hall tickets.
     * 
     * @return void Outputs JSON response
     */
    public function create_subject_combination()
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
            if (empty($input['subjectgrp_id']) || !is_numeric($input['subjectgrp_id'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing subjectgrp_id',
                    'data' => null
                ));
                return;
            }

            if (empty($input['subject_id']) || !is_numeric($input['subject_id'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing subject_id',
                    'data' => null
                ));
                return;
            }

            if (empty($input['date']) || !is_string($input['date'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing date',
                    'data' => null
                ));
                return;
            }

            if (empty($input['starttime']) || !is_string($input['starttime'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing starttime',
                    'data' => null
                ));
                return;
            }

            if (empty($input['endtime']) || !is_string($input['endtime'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing endtime',
                    'data' => null
                ));
                return;
            }

            if (empty($input['maxmark']) || !is_numeric($input['maxmark'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing maxmark',
                    'data' => null
                ));
                return;
            }

            if (empty($input['minmark']) || !is_numeric($input['minmark'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing minmark',
                    'data' => null
                ));
                return;
            }

            // Check if combination already exists
            if ($this->hall_ticket_model->combination_exists($input['subjectgrp_id'], $input['subject_id'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'This subject combination already exists',
                    'data' => null
                ));
                return;
            }

            // Prepare subject combination data
            $combination_data = array(
                'subjectgrp_id' => (int)$input['subjectgrp_id'],
                'subject_id' => (int)$input['subject_id'],
                'date' => $input['date'],
                'starttime' => $input['starttime'],
                'endtime' => $input['endtime'],
                'maxmark' => (int)$input['maxmark'],
                'minmark' => (int)$input['minmark'],
                'is_active' => isset($input['is_active']) ? $input['is_active'] : '1'
            );

            // Create subject combination
            $combination_id = $this->hall_ticket_model->create_subject_combination($combination_data);

            if ($combination_id === false) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to create subject combination',
                    'data' => null
                ));
                return;
            }

            // Get created subject combination
            $combination = $this->hall_ticket_model->get_subject_combination($combination_id);

            json_output(201, array(
                'status' => 1,
                'message' => 'Subject combination created successfully',
                'data' => $combination
            ));

        } catch (Exception $e) {
            log_message('error', 'Hall Ticket API Create Subject Combination Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Update subject combination
     * 
     * Updates an existing subject combination.
     * 
     * @param int $id Subject Combination ID
     * @return void Outputs JSON response
     */
    public function update_subject_combination($id = null)
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
                    'message' => 'Invalid or missing subject combination ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if subject combination exists
            if (!$this->hall_ticket_model->subject_combination_exists($id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Subject combination not found',
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

            // Validate fields if provided
            if (isset($input['subjectgrp_id']) && (!is_numeric($input['subjectgrp_id']) || empty($input['subjectgrp_id']))) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid subjectgrp_id',
                    'data' => null
                ));
                return;
            }

            if (isset($input['subject_id']) && (!is_numeric($input['subject_id']) || empty($input['subject_id']))) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid subject_id',
                    'data' => null
                ));
                return;
            }

            if (isset($input['date']) && !is_string($input['date'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid date',
                    'data' => null
                ));
                return;
            }

            if (isset($input['starttime']) && !is_string($input['starttime'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid starttime',
                    'data' => null
                ));
                return;
            }

            if (isset($input['endtime']) && !is_string($input['endtime'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid endtime',
                    'data' => null
                ));
                return;
            }

            if (isset($input['maxmark']) && !is_numeric($input['maxmark'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid maxmark',
                    'data' => null
                ));
                return;
            }

            if (isset($input['minmark']) && !is_numeric($input['minmark'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid minmark',
                    'data' => null
                ));
                return;
            }

            // Check if combination already exists (excluding current combination)
            if (isset($input['subjectgrp_id']) && isset($input['subject_id']) && 
                $this->hall_ticket_model->combination_exists($input['subjectgrp_id'], $input['subject_id'], $id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'This subject combination already exists',
                    'data' => null
                ));
                return;
            }

            // Prepare update data
            $update_data = array();
            
            if (isset($input['subjectgrp_id'])) {
                $update_data['subjectgrp_id'] = (int)$input['subjectgrp_id'];
            }
            if (isset($input['subject_id'])) {
                $update_data['subject_id'] = (int)$input['subject_id'];
            }
            if (isset($input['date'])) {
                $update_data['date'] = $input['date'];
            }
            if (isset($input['starttime'])) {
                $update_data['starttime'] = $input['starttime'];
            }
            if (isset($input['endtime'])) {
                $update_data['endtime'] = $input['endtime'];
            }
            if (isset($input['maxmark'])) {
                $update_data['maxmark'] = (int)$input['maxmark'];
            }
            if (isset($input['minmark'])) {
                $update_data['minmark'] = (int)$input['minmark'];
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

            // Update subject combination
            $result = $this->hall_ticket_model->update_subject_combination($id, $update_data);

            if ($result === false) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to update subject combination',
                    'data' => null
                ));
                return;
            }

            // Get updated subject combination
            $combination = $this->hall_ticket_model->get_subject_combination($id);

            json_output(200, array(
                'status' => 1,
                'message' => 'Subject combination updated successfully',
                'data' => $combination
            ));

        } catch (Exception $e) {
            log_message('error', 'Hall Ticket API Update Subject Combination Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Delete subject combination
     * 
     * Deletes a subject combination.
     * 
     * @param int $id Subject Combination ID
     * @return void Outputs JSON response
     */
    public function delete_subject_combination($id = null)
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
                    'message' => 'Invalid or missing subject combination ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if subject combination exists
            if (!$this->hall_ticket_model->subject_combination_exists($id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Subject combination not found',
                    'data' => null
                ));
                return;
            }

            // Delete subject combination
            $result = $this->hall_ticket_model->delete_subject_combination($id);

            if ($result === false) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to delete subject combination',
                    'data' => null
                ));
                return;
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Subject combination deleted successfully',
                'data' => null
            ));

        } catch (Exception $e) {
            log_message('error', 'Hall Ticket API Delete Subject Combination Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Generate hall tickets
     * 
     * Generates hall tickets for students based on provided criteria.
     * 
     * @return void Outputs JSON response
     */
    public function generate()
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
            if (empty($input['student_ids']) || !is_array($input['student_ids'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing student_ids',
                    'data' => null
                ));
                return;
            }

            if (empty($input['template_id']) || !is_numeric($input['template_id'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing template_id',
                    'data' => null
                ));
                return;
            }

            if (empty($input['subjectgrp_id']) || !is_numeric($input['subjectgrp_id'])) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid or missing subjectgrp_id',
                    'data' => null
                ));
                return;
            }

            // Check if template exists
            $template = $this->hall_ticket_model->get_template($input['template_id']);
            if (empty($template)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Hall ticket template not found',
                    'data' => null
                ));
                return;
            }

            // Get students
            $students = $this->hall_ticket_model->get_students_by_ids($input['student_ids']);
            if (empty($students)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'No valid students found',
                    'data' => null
                ));
                return;
            }

            // Get subject combinations for the group
            $subject_combinations = $this->hall_ticket_model->get_subject_combinations(array('subjectgrp_id' => $input['subjectgrp_id']));
            if (empty($subject_combinations)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'No subject combinations found for the specified group',
                    'data' => null
                ));
                return;
            }

            // Generate hall tickets data
            $hall_tickets = array();
            foreach ($students as $student) {
                $hall_ticket = array(
                    'student' => $student,
                    'template' => $template,
                    'subjects' => $subject_combinations
                );
                $hall_tickets[] = $hall_ticket;
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Hall tickets generated successfully',
                'total_records' => count($hall_tickets),
                'data' => $hall_tickets
            ));

        } catch (Exception $e) {
            log_message('error', 'Hall Ticket API Generate Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }
}
