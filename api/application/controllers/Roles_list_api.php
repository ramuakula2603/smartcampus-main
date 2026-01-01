<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Roles List API Controller
 * 
 * This controller provides API endpoint for retrieving all role records.
 * It handles listing operations for role management.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Roles_list_api extends CI_Controller
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
                'role_model',
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
     * List all roles
     * 
     * Retrieves a list of all role records.
     * Handles empty request body {} gracefully by returning all records.
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

            // Get all roles
            $roles = $this->role_model->get();

            // Format response data
            $formatted_roles = array();
            if (!empty($roles)) {
                foreach ($roles as $role) {
                    $formatted_roles[] = array(
                        'id' => $role['id'],
                        'name' => $role['name'],
                        'slug' => isset($role['slug']) ? $role['slug'] : null,
                        'is_active' => $role['is_active'],
                        'is_system' => isset($role['is_system']) ? $role['is_system'] : 0,
                        'is_superadmin' => isset($role['is_superadmin']) ? $role['is_superadmin'] : 0,
                        'created_at' => isset($role['created_at']) ? $role['created_at'] : null,
                        'updated_at' => isset($role['updated_at']) ? $role['updated_at'] : null
                    );
                }
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Roles retrieved successfully',
                'total_records' => count($formatted_roles),
                'data' => $formatted_roles,
                'timestamp' => date('Y-m-d H:i:s')
            ));

        } catch (Exception $e) {
            log_message('error', 'Roles List API Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }
}

