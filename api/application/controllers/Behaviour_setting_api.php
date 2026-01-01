<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Behaviour_setting_api extends CI_Controller
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
            'behavioursetting_model',
            'auth_model'
        ));
    }

    /**
     * Get behaviour settings
     * POST /behaviour/setting/get
     */
    public function get()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 0, 'message' => 'Bad request. Only POST method allowed.'));
            return;
        }

        $check_auth_client = $this->auth_model->check_auth_client();
        if ($check_auth_client == true) {
            try {
                // Get behaviour settings
                $result = $this->behavioursetting_model->get_settings();
                
                if ($result) {
                    // Decode comment_option if it exists
                    if (isset($result['comment_option'])) {
                        $result['comment_option'] = json_decode($result['comment_option']);
                    }
                    
                    json_output(200, array(
                        'status' => 1,
                        'data' => $result,
                        'message' => 'Behaviour settings retrieved successfully'
                    ));
                } else {
                    json_output(404, array(
                        'status' => 0,
                        'message' => 'Behaviour settings not found'
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
     * Update behaviour settings
     * POST /behaviour/setting/update
     */
    public function update()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 0, 'message' => 'Bad request. Only POST method allowed.'));
            return;
        }

        $check_auth_client = $this->auth_model->check_auth_client();
        if ($check_auth_client == true) {
            $_POST = json_decode(file_get_contents("php://input"), true);
            
            $id = $this->input->post('id');
            $comment_option = $this->input->post('comment_option');

            // Validation
            if (empty($id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Settings ID is required'
                ));
                return;
            }

            try {
                // Prepare data
                $data = array(
                    'id' => $id
                );
                
                // Add comment_option if provided
                if ($comment_option !== null) {
                    $data['comment_option'] = json_encode($comment_option);
                }
                
                // Update behaviour settings
                $result = $this->behavioursetting_model->update_settings($data);
                
                if ($result !== false) {
                    json_output(200, array(
                        'status' => 1,
                        'message' => 'Behaviour settings updated successfully'
                    ));
                } else {
                    json_output(500, array(
                        'status' => 0,
                        'message' => 'Failed to update behaviour settings'
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
