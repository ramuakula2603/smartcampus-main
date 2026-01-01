<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Income Search API Controller
 * 
 * This controller provides RESTful API endpoints for income search functionality.
 * It handles search and filtering operations for income records.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Income_search_api extends CI_Controller
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
                'income_model',
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
     * Search income records
     * 
     * Searches income records based on various criteria.
     * 
     * @return void Outputs JSON response
     */
    public function search()
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

            // Extract search parameters
            $search_text = isset($input['search_text']) ? trim($input['search_text']) : null;
            $start_date = isset($input['start_date']) ? $input['start_date'] : null;
            $end_date = isset($input['end_date']) ? $input['end_date'] : null;
            $income_head_id = isset($input['income_head_id']) ? $input['income_head_id'] : null;

            // Validate date format if provided
            if ($start_date && !$this->validate_date($start_date)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid start date format. Use YYYY-MM-DD format.',
                    'data' => null
                ));
                return;
            }

            if ($end_date && !$this->validate_date($end_date)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid end date format. Use YYYY-MM-DD format.',
                    'data' => null
                ));
                return;
            }

            // Set default date range if not provided
            if (!$start_date && !$end_date && !$search_text) {
                $start_date = date('Y-m-01'); // First day of current month
                $end_date = date('Y-m-t');   // Last day of current month
            }

            // Get income records based on search criteria
            $income_records = array();
            
            if ($search_text) {
                // Search by text
                $income_records = $this->get_income_by_text($search_text);
            } elseif ($start_date && $end_date) {
                // Search by date range
                if ($income_head_id) {
                    $income_records = $this->get_income_by_date_and_head($start_date, $end_date, $income_head_id);
                } else {
                    $income_records = $this->get_income_by_date($start_date, $end_date);
                }
            } else {
                // Get all income records
                $income_records = $this->income_model->get();
            }

            // Format response data
            $formatted_records = array();
            if (!empty($income_records)) {
                foreach ($income_records as $record) {
                    $formatted_records[] = array(
                        'id' => $record['id'],
                        'name' => $record['name'],
                        'invoice_no' => $record['invoice_no'],
                        'date' => $record['date'],
                        'amount' => $record['amount'],
                        'income_category' => isset($record['income_category']) ? $record['income_category'] : null,
                        'income_head_id' => $record['income_head_id'],
                        'note' => isset($record['note']) ? $record['note'] : null,
                        'documents' => isset($record['documents']) ? $record['documents'] : null
                    );
                }
            }

            // Prepare filters applied info
            $filters_applied = array();
            if ($search_text) $filters_applied['search_text'] = $search_text;
            if ($start_date) $filters_applied['start_date'] = $start_date;
            if ($end_date) $filters_applied['end_date'] = $end_date;
            if ($income_head_id) $filters_applied['income_head_id'] = $income_head_id;

            json_output(200, array(
                'status' => 1,
                'message' => 'Income search completed successfully',
                'filters_applied' => $filters_applied,
                'total_records' => count($formatted_records),
                'data' => $formatted_records
            ));

        } catch (Exception $e) {
            log_message('error', 'Income Search API Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get income heads for filtering
     * 
     * Retrieves all income heads for use in search filters.
     * 
     * @return void Outputs JSON response
     */
    public function income_heads()
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
                        'is_active' => $head['is_active']
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
            log_message('error', 'Income Search API Income Heads Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Validate date format
     * 
     * @param string $date Date string to validate
     * @return bool True if valid, false otherwise
     */
    private function validate_date($date)
    {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    /**
     * Get income records by text search
     * 
     * @param string $text Search text
     * @return array Income records
     */
    private function get_income_by_text($text)
    {
        $this->db->select('income.id, income.name, income.invoice_no, income.date, income.amount, income.note, income.documents, income.income_head_id, income_head.income_category');
        $this->db->from('income');
        $this->db->join('income_head', 'income.income_head_id = income_head.id');
        $this->db->like('income.name', $text);
        $this->db->or_like('income.invoice_no', $text);
        $this->db->or_like('income_head.income_category', $text);
        $this->db->order_by('income.date', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get income records by date range
     * 
     * @param string $start_date Start date
     * @param string $end_date End date
     * @return array Income records
     */
    private function get_income_by_date($start_date, $end_date)
    {
        $this->db->select('income.id, income.name, income.invoice_no, income.date, income.amount, income.note, income.documents, income.income_head_id, income_head.income_category');
        $this->db->from('income');
        $this->db->join('income_head', 'income.income_head_id = income_head.id');
        $this->db->where('income.date >=', $start_date);
        $this->db->where('income.date <=', $end_date);
        $this->db->order_by('income.date', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get income records by date range and income head
     * 
     * @param string $start_date Start date
     * @param string $end_date End date
     * @param int $income_head_id Income head ID
     * @return array Income records
     */
    private function get_income_by_date_and_head($start_date, $end_date, $income_head_id)
    {
        $this->db->select('income.id, income.name, income.invoice_no, income.date, income.amount, income.note, income.documents, income.income_head_id, income_head.income_category');
        $this->db->from('income');
        $this->db->join('income_head', 'income.income_head_id = income_head.id');
        $this->db->where('income.date >=', $start_date);
        $this->db->where('income.date <=', $end_date);
        $this->db->where('income.income_head_id', $income_head_id);
        $this->db->order_by('income.date', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }
}
