<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Expense Head List API Controller
 * 
 * This controller provides API endpoint for retrieving all expense head records.
 * It handles listing operations for expense head management.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Expense_head_list_api extends CI_Controller
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
                'expensehead_model',
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
     * List all expense heads
     * 
     * Retrieves a list of all expense head records.
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

            // Get all expense heads
            $expense_heads = $this->expensehead_model->get();

            // Format response data
            $formatted_heads = array();
            if (!empty($expense_heads)) {
                foreach ($expense_heads as $head) {
                    $formatted_heads[] = array(
                        'id' => $head['id'],
                        'exp_category' => $head['exp_category'],
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
                'message' => 'Expense heads retrieved successfully',
                'total_records' => count($formatted_heads),
                'data' => $formatted_heads,
                'timestamp' => date('Y-m-d H:i:s')
            ));

        } catch (Exception $e) {
            log_message('error', 'Expense Head List API Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }
}

