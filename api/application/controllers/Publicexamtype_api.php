<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Publicexamtype_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('publicexamtype_model');
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
     * Get all external result types
     * 
     * @return void Outputs JSON response with list of external result types
     */
    public function get_public_exam_types()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                $exam_types = $this->publicexamtype_model->get();
                json_output(200, array('status' => 200, 'message' => 'Success', 'data' => $exam_types));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    /**
     * Get specific external result type by ID
     * 
     * @param int $id External result type ID
     * @return void Outputs JSON response with external result type details
     */
    public function get_public_exam_type($id)
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                if (empty($id)) {
                    json_output(400, array('status' => 400, 'message' => 'External result type ID is required.'));
                    return;
                }

                $exam_type = $this->publicexamtype_model->get($id);
                
                if (empty($exam_type)) {
                    json_output(404, array('status' => 404, 'message' => 'External result type not found.'));
                    return;
                }

                json_output(200, array('status' => 200, 'message' => 'Success', 'data' => $exam_type));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    /**
     * Create new external result type
     * 
     * @return void Outputs JSON response with creation status
     */
    public function create_public_exam_type()
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
                    json_output(400, array('status' => 400, 'message' => 'External result type name is required.'));
                    return;
                }

                // Prepare data
                $data = array(
                    'examtype' => $examtype,
                    'session_id' => $session_id,
                    'is_active' => 'no'
                );

                // Insert the data - Note: The model doesn't have a dedicated add method, 
                // so we'll use direct DB insertion or add a method to the model if needed.
                // Checking model again... it seems Publicexamtype_model only has 'get' methods.
                // We should probably add an 'add' method to the model, but for now let's use direct DB insertion
                // to avoid modifying the existing model too much unless necessary.
                // Actually, let's check if there's another model used for adding.
                // The admin controller uses `publicexamtype_model` but let's double check if it has add/update methods.
                // Based on previous view, it didn't seem to have write methods.
                // Let's use direct DB operations for now as it's safer than modifying core models without full context.
                
                $this->db->insert('publicexamtype', $data);
                $insert_id = $this->db->insert_id();

                json_output(201, array(
                    'status' => 201,
                    'message' => 'External result type created successfully.',
                    'data' => array(
                        'id' => $insert_id,
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
     * Update external result type
     * 
     * @param int $id External result type ID
     * @return void Outputs JSON response with update status
     */
    public function update_public_exam_type($id)
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'PUT' && $method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                if (empty($id)) {
                    json_output(400, array('status' => 400, 'message' => 'External result type ID is required.'));
                    return;
                }

                // Check if external result type exists
                $existing_exam_type = $this->publicexamtype_model->get($id);
                if (empty($existing_exam_type)) {
                    json_output(404, array('status' => 404, 'message' => 'External result type not found.'));
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
                    json_output(400, array('status' => 400, 'message' => 'External result type name is required.'));
                    return;
                }

                // Prepare data
                $data = array(
                    'examtype' => $examtype,
                    'session_id' => $session_id,
                    'is_active' => $is_active
                );

                // Update the data
                $this->db->where('id', $id);
                $this->db->update('publicexamtype', $data);

                json_output(200, array(
                    'status' => 200,
                    'message' => 'External result type updated successfully.',
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
     * Delete external result type
     * 
     * @param int $id External result type ID
     * @return void Outputs JSON response with deletion status
     */
    public function delete_public_exam_type($id)
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'DELETE' && $method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                if (empty($id)) {
                    json_output(400, array('status' => 400, 'message' => 'External result type ID is required.'));
                    return;
                }

                // Check if external result type exists
                $existing_exam_type = $this->publicexamtype_model->get($id);
                if (empty($existing_exam_type)) {
                    json_output(404, array('status' => 404, 'message' => 'External result type not found.'));
                    return;
                }

                // Delete the external result type
                $this->db->where('id', $id);
                $this->db->delete('publicexamtype');

                json_output(200, array(
                    'status' => 200,
                    'message' => 'External result type deleted successfully.',
                    'data' => array('id' => $id)
                ));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }
}
