<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Student_fee_payment_search_api extends CI_Controller
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
            'studentfeemaster_model',
            'studenttransportfee_model',
            'setting_model',
            'auth_model'
        ));
    }

    /**
     * Search payment by payment ID
     * POST /student-fee-payment-search/by-payment-id
     */
    public function by_payment_id()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 0, 'message' => 'Bad request. Only POST method allowed.'));
        }

        $check_auth_client = $this->auth_model->check_auth_client();
        if ($check_auth_client == true) {
            $_POST = json_decode(file_get_contents("php://input"), true);
            
            $payment_id = $this->input->post('payment_id');

            // Validation
            if (empty($payment_id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Payment ID is required'
                ));
            }

            try {
                // Parse payment ID (format: invoice_id/sub_invoice_id)
                $invoice_parts = explode("/", $payment_id);
                
                $fee_list = array();
                $transport_fee_list = array();
                
                if (count($invoice_parts) >= 2) {
                    // Standard fee payment format
                    $invoice_id = $invoice_parts[0];
                    $sub_invoice_id = $invoice_parts[1];
                    
                    $fee_list = $this->studentfeemaster_model->getFeeByInvoice($invoice_id, $sub_invoice_id);
                } else {
                    // Single payment ID - check both fee and transport fee tables
                    $fee_list = $this->studentfeemaster_model->getFeeByInvoice($payment_id, null);
                    $transport_fee_list = $this->studenttransportfee_model->getfeeByID($payment_id);
                }

                // Prepare response data
                $response_data = array();
                
                if (!empty($fee_list)) {
                    $response_data['fee_payment'] = $fee_list;
                }
                
                if (!empty($transport_fee_list)) {
                    $response_data['transport_fee_payment'] = $transport_fee_list;
                }

                if (empty($response_data)) {
                    json_output(404, array(
                        'status' => 0,
                        'message' => 'No payment found with the provided payment ID',
                        'payment_id' => $payment_id
                    ));
                }

                $response = array(
                    'status' => 1,
                    'message' => 'Payment details retrieved successfully',
                    'payment_id' => $payment_id,
                    'data' => $response_data,
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
     * Search payment by invoice ID
     * POST /student-fee-payment-search/by-invoice-id
     */
    public function by_invoice_id()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 0, 'message' => 'Bad request. Only POST method allowed.'));
        }

        $check_auth_client = $this->auth_model->check_auth_client();
        if ($check_auth_client == true) {
            $_POST = json_decode(file_get_contents("php://input"), true);
            
            $invoice_id = $this->input->post('invoice_id');
            $sub_invoice_id = $this->input->post('sub_invoice_id');

            // Validation
            if (empty($invoice_id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invoice ID is required'
                ));
            }

            try {
                $fee_list = $this->studentfeemaster_model->getFeeByInvoice($invoice_id, $sub_invoice_id);
                
                if (empty($fee_list)) {
                    json_output(404, array(
                        'status' => 0,
                        'message' => 'No payment found with the provided invoice ID',
                        'invoice_id' => $invoice_id,
                        'sub_invoice_id' => $sub_invoice_id
                    ));
                }

                $response = array(
                    'status' => 1,
                    'message' => 'Payment details retrieved successfully',
                    'invoice_id' => $invoice_id,
                    'sub_invoice_id' => $sub_invoice_id,
                    'data' => $fee_list,
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
     * Search transport fee payment by payment ID
     * POST /student-fee-payment-search/transport-fee
     */
    public function transport_fee()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 0, 'message' => 'Bad request. Only POST method allowed.'));
        }

        $check_auth_client = $this->auth_model->check_auth_client();
        if ($check_auth_client == true) {
            $_POST = json_decode(file_get_contents("php://input"), true);
            
            $payment_id = $this->input->post('payment_id');

            // Validation
            if (empty($payment_id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Payment ID is required'
                ));
            }

            try {
                $transport_fee_list = $this->studenttransportfee_model->getfeeByID($payment_id);
                
                if (empty($transport_fee_list)) {
                    json_output(404, array(
                        'status' => 0,
                        'message' => 'No transport fee payment found with the provided payment ID',
                        'payment_id' => $payment_id
                    ));
                }

                $response = array(
                    'status' => 1,
                    'message' => 'Transport fee payment details retrieved successfully',
                    'payment_id' => $payment_id,
                    'data' => $transport_fee_list,
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
     * Get payment receipt details
     * POST /student-fee-payment-search/receipt
     */
    public function receipt()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 0, 'message' => 'Bad request. Only POST method allowed.'));
        }

        $check_auth_client = $this->auth_model->check_auth_client();
        if ($check_auth_client == true) {
            $_POST = json_decode(file_get_contents("php://input"), true);
            
            $payment_id = $this->input->post('payment_id');

            // Validation
            if (empty($payment_id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Payment ID is required'
                ));
            }

            try {
                // Parse payment ID
                $invoice_parts = explode("/", $payment_id);
                
                $receipt_data = array();
                
                if (count($invoice_parts) >= 2) {
                    $invoice_id = $invoice_parts[0];
                    $sub_invoice_id = $invoice_parts[1];
                    
                    $fee_details = $this->studentfeemaster_model->getFeeByInvoice($invoice_id, $sub_invoice_id);
                    
                    if (!empty($fee_details)) {
                        $receipt_data = array(
                            'type' => 'fee_payment',
                            'payment_details' => $fee_details,
                            'invoice_id' => $invoice_id,
                            'sub_invoice_id' => $sub_invoice_id
                        );
                    }
                } else {
                    // Check transport fee
                    $transport_fee_details = $this->studenttransportfee_model->getfeeByID($payment_id);
                    
                    if (!empty($transport_fee_details)) {
                        $receipt_data = array(
                            'type' => 'transport_fee_payment',
                            'payment_details' => $transport_fee_details
                        );
                    }
                }

                if (empty($receipt_data)) {
                    json_output(404, array(
                        'status' => 0,
                        'message' => 'No receipt found for the provided payment ID',
                        'payment_id' => $payment_id
                    ));
                }

                $response = array(
                    'status' => 1,
                    'message' => 'Receipt details retrieved successfully',
                    'payment_id' => $payment_id,
                    'data' => $receipt_data,
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
     * Validate payment ID format
     * POST /student-fee-payment-search/validate-payment-id
     */
    public function validate_payment_id()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 0, 'message' => 'Bad request. Only POST method allowed.'));
        }

        $check_auth_client = $this->auth_model->check_auth_client();
        if ($check_auth_client == true) {
            $input_data = json_decode(file_get_contents("php://input"), true);

            $payment_id = isset($input_data['payment_id']) ? $input_data['payment_id'] : null;

            // Validation
            if (empty($payment_id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Payment ID is required'
                ));
            }

            try {
                $validation_result = array(
                    'payment_id' => $payment_id,
                    'is_valid' => false,
                    'format' => 'unknown',
                    'parts' => array()
                );

                // Check if it's in invoice/sub_invoice format
                $invoice_parts = explode("/", $payment_id);
                
                if (count($invoice_parts) >= 2) {
                    $validation_result['is_valid'] = true;
                    $validation_result['format'] = 'invoice_format';
                    $validation_result['parts'] = array(
                        'invoice_id' => $invoice_parts[0],
                        'sub_invoice_id' => $invoice_parts[1]
                    );
                } elseif (is_numeric($payment_id)) {
                    $validation_result['is_valid'] = true;
                    $validation_result['format'] = 'numeric_id';
                    $validation_result['parts'] = array(
                        'payment_id' => $payment_id
                    );
                }

                $response = array(
                    'status' => 1,
                    'message' => 'Payment ID validation completed',
                    'data' => $validation_result,
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
}
