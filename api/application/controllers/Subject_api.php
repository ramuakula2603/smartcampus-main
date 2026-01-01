<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Subject_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('subject_model');
        $this->load->helper('json_output');
    }

    private function validate_headers()
    {
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key = $this->input->get_request_header('Auth-Key', TRUE);
        
        if ($client_service == 'smartschool' && $auth_key == 'schoolAdmin@') {
            return true;
        }
        return false;
    }

    /**
     * Get all subjects
     * 
     * @return void Outputs JSON response with list of subjects
     */
    public function get_subjects()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                $subjects = $this->subject_model->get();
                json_output(200, array('status' => 200, 'message' => 'Success', 'data' => $subjects));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    /**
     * Get specific subject by ID
     * 
     * @param int $id Subject ID
     * @return void Outputs JSON response with subject details
     */
    public function get_subject($id)
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                if (empty($id)) {
                    json_output(400, array('status' => 400, 'message' => 'Subject ID is required.'));
                    return;
                }

                $subject = $this->subject_model->get($id);
                
                if (empty($subject)) {
                    json_output(404, array('status' => 404, 'message' => 'Subject not found.'));
                    return;
                }

                json_output(200, array('status' => 200, 'message' => 'Success', 'data' => $subject));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    /**
     * Create new subject
     * 
     * @return void Outputs JSON response with creation status
     */
    public function create_subject()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                
                if (empty($params)) {
                    json_output(400, array('status' => 400, 'message' => 'Invalid JSON format or empty body.'));
                    return;
                }

                $name = isset($params['name']) ? trim($params['name']) : '';
                $code = isset($params['code']) ? trim($params['code']) : '';
                $type = isset($params['type']) ? $params['type'] : '';

                // Validate required fields
                if (empty($name)) {
                    json_output(400, array('status' => 400, 'message' => 'Subject name is required.'));
                    return;
                }

                if (empty($type)) {
                    json_output(400, array('status' => 400, 'message' => 'Subject type is required.'));
                    return;
                }

                // Validate type
                if (!in_array($type, array('Theory', 'Practical'))) {
                    json_output(400, array('status' => 400, 'message' => 'Subject type must be either "Theory" or "Practical".'));
                    return;
                }

                // Check if name already exists
                if ($this->subject_model->check_data_exists(array('name' => $name))) {
                    json_output(400, array('status' => 400, 'message' => 'Subject name already exists.'));
                    return;
                }

                // Check if code already exists (if provided)
                if (!empty($code) && $this->subject_model->check_code_exists(array('code' => $code))) {
                    json_output(400, array('status' => 400, 'message' => 'Subject code already exists.'));
                    return;
                }

                // Prepare data
                $data = array(
                    'name' => $name,
                    'code' => $code,
                    'type' => $type
                );

                // Insert the data
                $insert_id = $this->subject_model->add($data);

                json_output(201, array(
                    'status' => 201,
                    'message' => 'Subject created successfully.',
                    'data' => array(
                        'id' => $insert_id,
                        'name' => $name,
                        'code' => $code,
                        'type' => $type
                    )
                ));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    /**
     * Update subject
     * 
     * @param int $id Subject ID
     * @return void Outputs JSON response with update status
     */
    public function update_subject($id)
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'PUT' && $method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                if (empty($id)) {
                    json_output(400, array('status' => 400, 'message' => 'Subject ID is required.'));
                    return;
                }

                // Check if subject exists
                $existing_subject = $this->subject_model->get($id);
                if (empty($existing_subject)) {
                    json_output(404, array('status' => 404, 'message' => 'Subject not found.'));
                    return;
                }

                $params = json_decode(file_get_contents('php://input'), TRUE);
                
                if (empty($params)) {
                    json_output(400, array('status' => 400, 'message' => 'Invalid JSON format or empty body.'));
                    return;
                }

                $name = isset($params['name']) ? trim($params['name']) : '';
                $code = isset($params['code']) ? trim($params['code']) : '';
                $type = isset($params['type']) ? $params['type'] : '';

                // Validate required fields
                if (empty($name)) {
                    json_output(400, array('status' => 400, 'message' => 'Subject name is required.'));
                    return;
                }

                if (empty($type)) {
                    json_output(400, array('status' => 400, 'message' => 'Subject type is required.'));
                    return;
                }

                // Validate type
                if (!in_array($type, array('Theory', 'Practical'))) {
                    json_output(400, array('status' => 400, 'message' => 'Subject type must be either "Theory" or "Practical".'));
                    return;
                }

                // Prepare data
                $data = array(
                    'id' => $id,
                    'name' => $name,
                    'code' => $code,
                    'type' => $type
                );

                // Update the data
                $this->subject_model->add($data);

                json_output(200, array(
                    'status' => 200,
                    'message' => 'Subject updated successfully.',
                    'data' => array(
                        'id' => $id,
                        'name' => $name,
                        'code' => $code,
                        'type' => $type
                    )
                ));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    /**
     * Delete subject
     * 
     * @param int $id Subject ID
     * @return void Outputs JSON response with deletion status
     */
    public function delete_subject($id)
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'DELETE' && $method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                if (empty($id)) {
                    json_output(400, array('status' => 400, 'message' => 'Subject ID is required.'));
                    return;
                }

                // Check if subject exists
                $existing_subject = $this->subject_model->get($id);
                if (empty($existing_subject)) {
                    json_output(404, array('status' => 404, 'message' => 'Subject not found.'));
                    return;
                }

                // Delete the subject
                $this->subject_model->remove($id);

                json_output(200, array(
                    'status' => 200,
                    'message' => 'Subject deleted successfully.',
                    'data' => array('id' => $id)
                ));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }
}
