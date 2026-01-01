<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Fee_type_api extends CI_Controller
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
            'feetype_model',
            'setting_model',
            'auth_model'
        ));
    }

    /**
     * List all fee types
     * POST /fee-types/list
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
                $fee_types = $this->feetype_model->get();
                
                $response = array(
                    'status' => 1,
                    'message' => 'Fee types retrieved successfully',
                    'total_records' => count($fee_types),
                    'data' => $fee_types,
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
     * Get single fee type by ID
     * POST /fee-types/get
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
                    'message' => 'Fee type ID is required'
                ));
            }

            try {
                $fee_type = $this->feetype_model->get($id);
                
                if (empty($fee_type)) {
                    json_output(404, array(
                        'status' => 0,
                        'message' => 'Fee type not found'
                    ));
                }

                $response = array(
                    'status' => 1,
                    'message' => 'Fee type retrieved successfully',
                    'data' => $fee_type,
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
     * Create new fee type
     * POST /fee-types/create
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
            
            $type = $this->input->post('type');
            $code = $this->input->post('code');
            $description = $this->input->post('description');

            // Validation
            if (empty($type)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Fee type name is required'
                ));
            }

            if (empty($code)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Fee type code is required'
                ));
            }

            try {
                // Check if fee type code already exists
                $existing = $this->feetype_model->check_exists($code);
                if ($existing) {
                    json_output(409, array(
                        'status' => 0,
                        'message' => 'Fee type with this code already exists'
                    ));
                }

                $data = array(
                    'type' => $type,
                    'code' => $code,
                    'description' => $description,
                    'is_active' => 'yes'
                );

                $result = $this->feetype_model->add($data);
                
                if ($result !== false) {
                    $response = array(
                        'status' => 1,
                        'message' => 'Fee type created successfully',
                        'data' => array(
                            'type' => $type,
                            'code' => $code,
                            'description' => $description
                        ),
                        'timestamp' => date('Y-m-d H:i:s')
                    );
                    json_output(201, $response);
                } else {
                    json_output(500, array(
                        'status' => 0,
                        'message' => 'Failed to create fee type'
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
     * Update existing fee type
     * POST /fee-types/update
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
            $type = $this->input->post('type');
            $code = $this->input->post('code');
            $description = $this->input->post('description');

            // Validation
            if (empty($id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Fee type ID is required'
                ));
            }

            if (empty($type)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Fee type name is required'
                ));
            }

            if (empty($code)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Fee type code is required'
                ));
            }

            try {
                // Check if fee type exists
                $existing = $this->feetype_model->get($id);
                if (empty($existing)) {
                    json_output(404, array(
                        'status' => 0,
                        'message' => 'Fee type not found'
                    ));
                }

                $data = array(
                    'id' => $id,
                    'type' => $type,
                    'code' => $code,
                    'description' => $description
                );

                $result = $this->feetype_model->add($data);
                
                if ($result) {
                    $response = array(
                        'status' => 1,
                        'message' => 'Fee type updated successfully',
                        'data' => array(
                            'id' => $id,
                            'type' => $type,
                            'code' => $code,
                            'description' => $description
                        ),
                        'timestamp' => date('Y-m-d H:i:s')
                    );
                    json_output(200, $response);
                } else {
                    json_output(500, array(
                        'status' => 0,
                        'message' => 'Failed to update fee type'
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
     * Delete fee type
     * POST /fee-types/delete
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
                    'message' => 'Fee type ID is required'
                ));
            }

            try {
                // Check if fee type exists
                $existing = $this->feetype_model->get($id);
                if (empty($existing)) {
                    json_output(404, array(
                        'status' => 0,
                        'message' => 'Fee type not found'
                    ));
                }

                $result = $this->feetype_model->remove($id);
                
                if ($result) {
                    $response = array(
                        'status' => 1,
                        'message' => 'Fee type deleted successfully',
                        'timestamp' => date('Y-m-d H:i:s')
                    );
                    json_output(200, $response);
                } else {
                    json_output(500, array(
                        'status' => 0,
                        'message' => 'Failed to delete fee type'
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
