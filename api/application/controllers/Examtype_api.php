<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Examtype_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('examtype_model');
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
     * Get all exam types
     * 
     * @return void Outputs JSON response with list of exam types
     */
    public function get_exam_types()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                $exam_types = $this->examtype_model->get();
                json_output(200, array('status' => 200, 'message' => 'Success', 'data' => $exam_types));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    /**
     * Get specific exam type by ID
     * 
     * @param int $id Exam type ID
     * @return void Outputs JSON response with exam type details
     */
    public function get_exam_type($id)
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                if (empty($id)) {
                    json_output(400, array('status' => 400, 'message' => 'Exam type ID is required.'));
                    return;
                }

                $exam_type = $this->examtype_model->get($id);
                
                if (empty($exam_type)) {
                    json_output(404, array('status' => 404, 'message' => 'Exam type not found.'));
                    return;
                }

                json_output(200, array('status' => 200, 'message' => 'Success', 'data' => $exam_type));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    /**
     * Create new exam type
     * 
     * @return void Outputs JSON response with creation status
     */
    public function create_exam_type()
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

                $examtype = isset($params['examtype']) ? trim($params['examtype']) : '';
                $session_id = isset($params['session_id']) ? $params['session_id'] : $this->setting_model->getCurrentSession();

                // Validate required fields
                if (empty($examtype)) {
                    json_output(400, array('status' => 400, 'message' => 'Exam type name is required.'));
                    return;
                }

                // Prepare data
                $data = array(
                    'examtype' => $examtype,
                    'session_id' => $session_id,
                    'is_active' => 'no'
                );

                // Insert the data
                // Insert the data
                $this->db->insert('examtype', $data);
                $insert_id = $this->db->insert_id();

                json_output(201, array(
                    'status' => 201,
                    'message' => 'Exam type created successfully.',
                    'data' => array(
                        'examtype' => $examtype,
                        'session_id' => $session_id
                    )
                ));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    /**
     * Update exam type
     * 
     * @param int $id Exam type ID
     * @return void Outputs JSON response with update status
     */
    public function update_exam_type($id)
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'PUT' && $method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                if (empty($id)) {
                    json_output(400, array('status' => 400, 'message' => 'Exam type ID is required.'));
                    return;
                }

                // Check if exam type exists
                $existing_exam_type = $this->examtype_model->get($id);
                if (empty($existing_exam_type)) {
                    json_output(404, array('status' => 404, 'message' => 'Exam type not found.'));
                    return;
                }

                $params = json_decode(file_get_contents('php://input'), TRUE);
                
                if (empty($params)) {
                    json_output(400, array('status' => 400, 'message' => 'Invalid JSON format or empty body.'));
                    return;
                }

                $examtype = isset($params['examtype']) ? trim($params['examtype']) : '';
                $session_id = isset($params['session_id']) ? $params['session_id'] : $existing_exam_type['session_id'];
                $is_active = isset($params['is_active']) ? $params['is_active'] : $existing_exam_type['is_active'];

                // Validate required fields
                if (empty($examtype)) {
                    json_output(400, array('status' => 400, 'message' => 'Exam type name is required.'));
                    return;
                }

                // Prepare data
                $data = array(
                    'id' => $id,
                    'examtype' => $examtype,
                    'session_id' => $session_id,
                    'is_active' => $is_active
                );

                // Update the data
                // Update the data
                $this->db->where('id', $id);
                $this->db->update('examtype', $data);

                json_output(200, array(
                    'status' => 200,
                    'message' => 'Exam type updated successfully.',
                    'data' => array(
                        'id' => $id,
                        'examtype' => $examtype,
                        'session_id' => $session_id,
                        'is_active' => $is_active
                    )
                ));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    /**
     * Delete exam type
     * 
     * @param int $id Exam type ID
     * @return void Outputs JSON response with deletion status
     */
    public function delete_exam_type($id)
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'DELETE' && $method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                if (empty($id)) {
                    json_output(400, array('status' => 400, 'message' => 'Exam type ID is required.'));
                    return;
                }

                // Check if exam type exists
                $existing_exam_type = $this->examtype_model->get($id);
                if (empty($existing_exam_type)) {
                    json_output(404, array('status' => 404, 'message' => 'Exam type not found.'));
                    return;
                }

                // Delete the exam type
                // Delete the exam type
                $this->db->where('id', $id);
                $this->db->delete('examtype');

                json_output(200, array(
                    'status' => 200,
                    'message' => 'Exam type deleted successfully.',
                    'data' => array('id' => $id)
                ));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }
}
