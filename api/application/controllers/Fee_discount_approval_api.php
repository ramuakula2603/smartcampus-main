<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Fee Discount Approval API Controller
 * 
 * This controller provides RESTful API endpoints for managing fee discount approval records.
 * It handles listing, viewing, approving, rejecting, and reverting fee discount approvals.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Fee_discount_approval_api extends CI_Controller
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
        $this->load->model('Fee_discount_approval_model', 'approval_model');
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
     * List all fee discount approvals
     * 
     * Retrieves a list of all fee discount approvals with optional filtering.
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
            
            if (isset($input['class_id']) && !empty($input['class_id'])) {
                $filters['class_id'] = is_array($input['class_id']) ? $input['class_id'] : array($input['class_id']);
            }
            
            if (isset($input['section_id']) && !empty($input['section_id'])) {
                $filters['section_id'] = is_array($input['section_id']) ? $input['section_id'] : array($input['section_id']);
            }
            
            if (isset($input['session_id']) && !empty($input['session_id'])) {
                $filters['session_id'] = is_array($input['session_id']) ? $input['session_id'] : array($input['session_id']);
            }
            
            if (isset($input['approval_status']) && $input['approval_status'] !== null && $input['approval_status'] !== '') {
                $filters['approval_status'] = $input['approval_status'];
            }

            // Get approval list
            $approval_list = $this->approval_model->get_discount_approvals($filters);

            // Format response data
            $formatted_data = array();
            foreach ($approval_list as $approval) {
                $status_text = 'pending';
                if ($approval['approval_status'] == 1) {
                    $status_text = 'approved';
                } elseif ($approval['approval_status'] == 2) {
                    $status_text = 'rejected';
                }

                $formatted_data[] = array(
                    'approval_id' => $approval['approval_id'],
                    'student_id' => $approval['student_id'],
                    'student_session_id' => $approval['student_session_id'],
                    'admission_no' => $approval['admission_no'],
                    'student_name' => trim($approval['firstname'] . ' ' . $approval['middlename'] . ' ' . $approval['lastname']),
                    'father_name' => $approval['father_name'],
                    'class' => $approval['class'],
                    'class_id' => $approval['class_id'],
                    'section' => $approval['section'],
                    'section_id' => $approval['section_id'],
                    'date_of_birth' => $approval['dob'],
                    'gender' => $approval['gender'],
                    'category' => $approval['category'],
                    'mobile_number' => $approval['mobileno'],
                    'fee_group' => $approval['fee_group_name'],
                    'discount_amount' => $approval['amount'],
                    'discount_note' => $approval['discount_note'],
                    'approval_status' => $approval['approval_status'],
                    'approval_status_text' => $status_text,
                    'payment_id' => $approval['payment_id'],
                    'date' => $approval['date'],
                    'created_at' => $approval['created_at']
                );
            }

            json_output(200, array(
                'status' => 1,
                'message' => 'Fee discount approvals retrieved successfully',
                'total_records' => count($formatted_data),
                'data' => $formatted_data
            ));

        } catch (Exception $e) {
            log_message('error', 'Fee Discount Approval API List Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Get a specific fee discount approval
     * 
     * Retrieves details of a specific fee discount approval by its ID.
     * 
     * @param int $id Approval ID
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
                    'message' => 'Invalid or missing approval ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if approval exists first (simple check)
            if (!$this->approval_model->approval_exists($id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Fee discount approval not found',
                    'data' => null
                ));
                return;
            }

            // Get approval details
            $approval = $this->approval_model->get_approval($id);
            
            if (empty($approval)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Fee discount approval not found',
                    'data' => null
                ));
                return;
            }

            $status_text = 'pending';
            if ($approval['approval_status'] == 1) {
                $status_text = 'approved';
            } elseif ($approval['approval_status'] == 2) {
                $status_text = 'rejected';
            }

            $formatted_data = array(
                'approval_id' => $approval['id'],
                'student_session_id' => $approval['student_session_id'],
                'admission_no' => $approval['admission_no'],
                'student_name' => trim($approval['firstname'] . ' ' . $approval['middlename'] . ' ' . $approval['lastname']),
                'father_name' => $approval['father_name'],
                'class' => $approval['class'],
                'section' => $approval['section'],
                'date_of_birth' => $approval['dob'],
                'gender' => $approval['gender'],
                'mobile_number' => $approval['mobileno'],
                'fee_group' => $approval['fee_group_name'],
                'discount_amount' => $approval['amount'],
                'discount_note' => $approval['description'],
                'approval_status' => $approval['approval_status'],
                'approval_status_text' => $status_text,
                'payment_id' => $approval['payment_id'],
                'fee_groups_feetype_id' => $approval['fee_groups_feetype_id'],
                'student_fees_master_id' => $approval['student_fees_master_id'],
                'date' => $approval['date'],
                'created_at' => $approval['created_at']
            );

            json_output(200, array(
                'status' => 1,
                'message' => 'Fee discount approval retrieved successfully',
                'data' => $formatted_data
            ));

        } catch (Exception $e) {
            log_message('error', 'Fee Discount Approval API Get Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Approve a fee discount
     * 
     * Approves a fee discount approval. Note: This only updates the status.
     * Full approval process may require creating fee deposits which should be handled separately.
     * 
     * @param int $id Approval ID
     * @return void Outputs JSON response
     */
    public function approve($id = null)
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
                    'message' => 'Invalid or missing approval ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if approval exists first (simple check)
            if (!$this->approval_model->approval_exists($id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Fee discount approval not found',
                    'data' => null
                ));
                return;
            }

            // Get approval details for status check
            $approval = $this->approval_model->get_approval($id);
            
            // Check if already approved
            if (isset($approval['approval_status']) && $approval['approval_status'] == 1) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Discount approval is already approved',
                    'data' => null
                ));
                return;
            }

            // Update approval status
            $result = $this->approval_model->update_approval_status($id, 1);

            if ($result) {
                // Get updated approval
                $updated_approval = $this->approval_model->get_approval($id);
                
                json_output(200, array(
                    'status' => 1,
                    'message' => 'Fee discount approved successfully',
                    'data' => array(
                        'approval_id' => $id,
                        'approval_status' => 1,
                        'approval_status_text' => 'approved'
                    )
                ));
            } else {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to update approval status',
                    'data' => null
                ));
            }

        } catch (Exception $e) {
            log_message('error', 'Fee Discount Approval API Approve Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Reject a fee discount
     * 
     * Rejects a fee discount approval.
     * 
     * @param int $id Approval ID
     * @return void Outputs JSON response
     */
    public function reject($id = null)
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
                    'message' => 'Invalid or missing approval ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if approval exists first (simple check)
            if (!$this->approval_model->approval_exists($id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Fee discount approval not found',
                    'data' => null
                ));
                return;
            }

            // Get approval details for status check
            $approval = $this->approval_model->get_approval($id);
            
            // Check if already rejected
            if (isset($approval['approval_status']) && $approval['approval_status'] == 2) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Discount approval is already rejected',
                    'data' => null
                ));
                return;
            }

            // Update approval status
            $result = $this->approval_model->update_approval_status($id, 2);

            if ($result) {
                json_output(200, array(
                    'status' => 1,
                    'message' => 'Fee discount rejected successfully',
                    'data' => array(
                        'approval_id' => $id,
                        'approval_status' => 2,
                        'approval_status_text' => 'rejected'
                    )
                ));
            } else {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to update approval status',
                    'data' => null
                ));
            }

        } catch (Exception $e) {
            log_message('error', 'Fee Discount Approval API Reject Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }

    /**
     * Revert an approved fee discount
     * 
     * Reverts an approved fee discount back to pending status and clears payment ID.
     * 
     * @param int $id Approval ID
     * @return void Outputs JSON response
     */
    public function revert($id = null)
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
                    'message' => 'Invalid or missing approval ID',
                    'data' => null
                ));
                return;
            }

            $id = (int)$id;

            // Check if approval exists first (simple check)
            if (!$this->approval_model->approval_exists($id)) {
                json_output(404, array(
                    'status' => 0,
                    'message' => 'Fee discount approval not found',
                    'data' => null
                ));
                return;
            }

            // Get approval details for status check
            $approval = $this->approval_model->get_approval($id);
            
            // Check if already pending
            if (isset($approval['approval_status']) && $approval['approval_status'] == 0) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Discount approval is already in pending status',
                    'data' => null
                ));
                return;
            }

            // Revert approval
            $result = $this->approval_model->revert_approval($id);

            if ($result) {
                json_output(200, array(
                    'status' => 1,
                    'message' => 'Fee discount approval reverted successfully',
                    'data' => array(
                        'approval_id' => $id,
                        'approval_status' => 0,
                        'approval_status_text' => 'pending',
                        'payment_id' => null
                    )
                ));
            } else {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Failed to revert approval',
                    'data' => null
                ));
            }

        } catch (Exception $e) {
            log_message('error', 'Fee Discount Approval API Revert Error: ' . $e->getMessage());
            json_output(500, array(
                'status' => 0,
                'message' => 'Internal server error occurred',
                'data' => null
            ));
        }
    }
}

