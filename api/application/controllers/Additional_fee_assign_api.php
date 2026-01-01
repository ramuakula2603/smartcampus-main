<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Additional_fee_assign_api extends CI_Controller
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
            'feesessiongroupadding_model',
            'auth_model'
        ));
    }

    /**
     * Update additional fee amount
     * POST /additional-fee-assign/update
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
            $amount = $this->input->post('amount');

            // Validation
            if (empty($id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Additional fee assignment ID is required'
                ));
            }

            if (empty($amount) || !is_numeric($amount)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Valid amount is required'
                ));
            }

            try {
                // Prepare data for update
                $data = array(
                    'id' => $id,
                    'amount' => $amount
                );

                // Update the additional fee amount
                $result = $this->feesessiongroupadding_model->updateadditionalfee($data);
                
                if ($result) {
                    $response = array(
                        'status' => 'success',
                        'error' => ''
                    );
                    json_output(200, $response);
                } else {
                    json_output(500, array(
                        'status' => 0,
                        'message' => 'Failed to update additional fee amount'
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
