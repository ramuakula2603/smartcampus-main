<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Fee_group_api extends CI_Controller
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
            'feegroup_model',
            'setting_model',
            'auth_model'
        ));
    }

    /**
     * List all fee groups
     * POST /fee-groups/list
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
                $fee_groups = $this->feegroup_model->get();
                
                $response = array(
                    'status' => 1,
                    'message' => 'Fee groups retrieved successfully',
                    'total_records' => count($fee_groups),
                    'data' => $fee_groups,
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
     * Get single fee group by ID
     * POST /fee-groups/get
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
                    'message' => 'Fee group ID is required'
                ));
            }

            try {
                $fee_group = $this->feegroup_model->get($id);
                
                if (empty($fee_group)) {
                    json_output(404, array(
                        'status' => 0,
                        'message' => 'Fee group not found'
                    ));
                }

                $response = array(
                    'status' => 1,
                    'message' => 'Fee group retrieved successfully',
                    'data' => $fee_group,
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
     * Create new fee group
     * POST /fee-groups/create
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
            
            $name = $this->input->post('name');
            $description = $this->input->post('description');

            // Validation
            if (empty($name)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Fee group name is required'
                ));
            }

            try {
                // Check if fee group already exists
                $existing = $this->feegroup_model->check_exists($name);
                if ($existing) {
                    json_output(409, array(
                        'status' => 0,
                        'message' => 'Fee group with this name already exists'
                    ));
                }

                $data = array(
                    'name' => $name,
                    'description' => $description,
                    'is_active' => 'yes'
                );

                $result = $this->feegroup_model->add($data);
                
                if ($result) {
                    $response = array(
                        'status' => 1,
                        'message' => 'Fee group created successfully',
                        'data' => array(
                            'name' => $name,
                            'description' => $description
                        ),
                        'timestamp' => date('Y-m-d H:i:s')
                    );
                    json_output(201, $response);
                } else {
                    json_output(500, array(
                        'status' => 0,
                        'message' => 'Failed to create fee group'
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
     * Update existing fee group
     * POST /fee-groups/update
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
            $name = $this->input->post('name');
            $description = $this->input->post('description');

            // Validation
            if (empty($id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Fee group ID is required'
                ));
            }

            if (empty($name)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Fee group name is required'
                ));
            }

            try {
                // Check if fee group exists
                $existing = $this->feegroup_model->get($id);
                if (empty($existing)) {
                    json_output(404, array(
                        'status' => 0,
                        'message' => 'Fee group not found'
                    ));
                }

                $data = array(
                    'id' => $id,
                    'name' => $name,
                    'description' => $description
                );

                $result = $this->feegroup_model->add($data);
                
                if ($result) {
                    $response = array(
                        'status' => 1,
                        'message' => 'Fee group updated successfully',
                        'data' => array(
                            'id' => $id,
                            'name' => $name,
                            'description' => $description
                        ),
                        'timestamp' => date('Y-m-d H:i:s')
                    );
                    json_output(200, $response);
                } else {
                    json_output(500, array(
                        'status' => 0,
                        'message' => 'Failed to update fee group'
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
     * Delete fee group
     * POST /fee-groups/delete
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
                    'message' => 'Fee group ID is required'
                ));
            }

            try {
                // Check if fee group exists
                $existing = $this->feegroup_model->get($id);
                if (empty($existing)) {
                    json_output(404, array(
                        'status' => 0,
                        'message' => 'Fee group not found'
                    ));
                }

                $result = $this->feegroup_model->remove($id);
                
                if ($result) {
                    $response = array(
                        'status' => 1,
                        'message' => 'Fee group deleted successfully',
                        'timestamp' => date('Y-m-d H:i:s')
                    );
                    json_output(200, $response);
                } else {
                    json_output(500, array(
                        'status' => 0,
                        'message' => 'Failed to delete fee group'
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
