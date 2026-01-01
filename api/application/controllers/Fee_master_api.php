<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Fee_master_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Set JSON content type early
        $this->output->set_content_type('application/json');
        
        // Load essential helpers
        $this->load->helper('json_output');
        
        // Load required models
        $this->load->model(array(
            'feesessiongroup_model',
            'feegrouptype_model',
            'feegroup_model',
            'feetype_model',
            'setting_model',
            'auth_model'
        ));
    }

    /**
     * List all fee masters
     * POST /fee-masters/list
     */
    public function list()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 0, 'message' => 'Bad request. Only POST method allowed.'));
        }

        $check_auth_client = $this->auth_model->check_auth_client();
        if ($check_auth_client == true) {
            try {
                $fee_masters = $this->feesessiongroup_model->getFeesByGroup(null, 0);
                
                $response = array(
                    'status' => 1,
                    'message' => 'Fee masters retrieved successfully',
                    'total_records' => count($fee_masters),
                    'data' => $fee_masters,
                    'timestamp' => date('Y-m-d H:i:s')
                );
                
                json_output(200, $response);
            } catch (Exception $e) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Internal server error: ' . $e->getMessage()
                ));
            }
        }
    }

    /**
     * Get single fee master by ID
     * POST /fee-masters/get
     */
    public function get()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 0, 'message' => 'Bad request. Only POST method allowed.'));
        }

        $check_auth_client = $this->auth_model->check_auth_client();
        if ($check_auth_client == true) {
            $_POST = json_decode(file_get_contents("php://input"), true);
            $id = $this->input->post('id');

            if (empty($id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Fee master ID is required'
                ));
            }

            try {
                $fee_master = $this->feegrouptype_model->getFeeGroupByID($id);
                
                if (empty($fee_master)) {
                    json_output(404, array(
                        'status' => 0,
                        'message' => 'Fee master not found'
                    ));
                }

                $response = array(
                    'status' => 1,
                    'message' => 'Fee master retrieved successfully',
                    'data' => $fee_master,
                    'timestamp' => date('Y-m-d H:i:s')
                );
                
                json_output(200, $response);
            } catch (Exception $e) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Internal server error: ' . $e->getMessage()
                ));
            }
        }
    }

    /**
     * Create new fee master
     * POST /fee-masters/create
     */
    public function create()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 0, 'message' => 'Bad request. Only POST method allowed.'));
        }

        $check_auth_client = $this->auth_model->check_auth_client();
        if ($check_auth_client == true) {
            $_POST = json_decode(file_get_contents("php://input"), true);
            
            $fee_groups_id = $this->input->post('fee_groups_id');
            $feetype_id = $this->input->post('feetype_id');
            $amount = $this->input->post('amount');
            $due_date = $this->input->post('due_date');
            $fine_type = $this->input->post('fine_type');
            $fine_percentage = $this->input->post('fine_percentage');
            $fine_amount = $this->input->post('fine_amount');

            // Validation
            if (empty($fee_groups_id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Fee group ID is required'
                ));
            }

            if (empty($feetype_id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Fee type ID is required'
                ));
            }

            if (empty($amount) || !is_numeric($amount)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Valid amount is required'
                ));
            }

            try {
                // Get current session
                $current_session = $this->setting_model->getCurrentSession();
                
                // Prepare fine amount based on type
                $calculated_fine_amount = 0;
                if ($fine_type == 'fix' && !empty($fine_amount)) {
                    $calculated_fine_amount = $fine_amount;
                } elseif ($fine_type == 'percentage' && !empty($fine_percentage)) {
                    $calculated_fine_amount = 0; // Will be calculated dynamically
                }

                $insert_array = array(
                    'fee_groups_id' => $fee_groups_id,
                    'feetype_id' => $feetype_id,
                    'session_id' => $current_session,
                    'due_date' => !empty($due_date) ? date('Y-m-d', strtotime($due_date)) : null,
                    'amount' => $amount,
                    'fine_type' => !empty($fine_type) ? $fine_type : 'none',
                    'fine_percentage' => !empty($fine_percentage) ? $fine_percentage : 0,
                    'fine_amount' => $calculated_fine_amount,
                    'is_active' => 'yes'
                );

                $result = $this->feesessiongroup_model->add($insert_array);
                
                if ($result) {
                    $response = array(
                        'status' => 1,
                        'message' => 'Fee master created successfully',
                        'data' => $insert_array,
                        'timestamp' => date('Y-m-d H:i:s')
                    );
                    json_output(201, $response);
                } else {
                    json_output(500, array(
                        'status' => 0,
                        'message' => 'Failed to create fee master'
                    ));
                }
            } catch (Exception $e) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Internal server error: ' . $e->getMessage()
                ));
            }
        }
    }

    /**
     * Update existing fee master
     * POST /fee-masters/update
     */
    public function update()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 0, 'message' => 'Bad request. Only POST method allowed.'));
        }

        $check_auth_client = $this->auth_model->check_auth_client();
        if ($check_auth_client == true) {
            $_POST = json_decode(file_get_contents("php://input"), true);
            
            $id = $this->input->post('id');
            $feetype_id = $this->input->post('feetype_id');
            $amount = $this->input->post('amount');
            $due_date = $this->input->post('due_date');
            $fine_type = $this->input->post('fine_type');
            $fine_percentage = $this->input->post('fine_percentage');
            $fine_amount = $this->input->post('fine_amount');

            // Validation
            if (empty($id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Fee master ID is required'
                ));
            }

            if (empty($feetype_id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Fee type ID is required'
                ));
            }

            if (empty($amount) || !is_numeric($amount)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Valid amount is required'
                ));
            }

            try {
                // Check if fee master exists
                $existing = $this->feegrouptype_model->getFeeGroupByID($id);
                if (empty($existing)) {
                    json_output(404, array(
                        'status' => 0,
                        'message' => 'Fee master not found'
                    ));
                }

                // Prepare fine amount based on type
                $calculated_fine_amount = 0;
                if ($fine_type == 'fix' && !empty($fine_amount)) {
                    $calculated_fine_amount = $fine_amount;
                } elseif ($fine_type == 'percentage' && !empty($fine_percentage)) {
                    $calculated_fine_amount = 0; // Will be calculated dynamically
                }

                $update_array = array(
                    'id' => $id,
                    'feetype_id' => $feetype_id,
                    'due_date' => !empty($due_date) ? date('Y-m-d', strtotime($due_date)) : null,
                    'amount' => $amount,
                    'fine_type' => !empty($fine_type) ? $fine_type : 'none',
                    'fine_percentage' => !empty($fine_percentage) ? $fine_percentage : 0,
                    'fine_amount' => $calculated_fine_amount
                );

                $result = $this->feegrouptype_model->add($update_array);
                
                if ($result) {
                    $response = array(
                        'status' => 1,
                        'message' => 'Fee master updated successfully',
                        'data' => $update_array,
                        'timestamp' => date('Y-m-d H:i:s')
                    );
                    json_output(200, $response);
                } else {
                    json_output(500, array(
                        'status' => 0,
                        'message' => 'Failed to update fee master'
                    ));
                }
            } catch (Exception $e) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Internal server error: ' . $e->getMessage()
                ));
            }
        }
    }

    /**
     * Delete fee master
     * POST /fee-masters/delete
     */
    public function delete()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 0, 'message' => 'Bad request. Only POST method allowed.'));
        }

        $check_auth_client = $this->auth_model->check_auth_client();
        if ($check_auth_client == true) {
            $_POST = json_decode(file_get_contents("php://input"), true);
            $id = $this->input->post('id');

            if (empty($id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Fee master ID is required'
                ));
            }

            try {
                // Check if fee master exists
                $existing = $this->feegrouptype_model->getFeeGroupByID($id);
                if (empty($existing)) {
                    json_output(404, array(
                        'status' => 0,
                        'message' => 'Fee master not found'
                    ));
                }

                $result = $this->feegrouptype_model->remove($id);
                
                if ($result) {
                    $response = array(
                        'status' => 1,
                        'message' => 'Fee master deleted successfully',
                        'timestamp' => date('Y-m-d H:i:s')
                    );
                    json_output(200, $response);
                } else {
                    json_output(500, array(
                        'status' => 0,
                        'message' => 'Failed to delete fee master'
                    ));
                }
            } catch (Exception $e) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Internal server error: ' . $e->getMessage()
                ));
            }
        }
    }
}
