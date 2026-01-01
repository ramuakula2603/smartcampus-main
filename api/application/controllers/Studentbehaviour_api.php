<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Studentbehaviour_api extends CI_Controller
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
            'studentbehaviour_model',
            'auth_model'
        ));
    }

    /**
     * Get all student behaviour types
     * POST /studentbehaviour/list
     */
    public function list()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 0, 'message' => 'Bad request. Only POST method allowed.'));
            return;
        }

        $check_auth_client = $this->auth_model->check_auth_client();
        if ($check_auth_client == true) {
            $_POST = json_decode(file_get_contents("php://input"), true);
            
            // Get pagination parameters
            $limit = $this->input->post('limit');
            $offset = $this->input->post('offset') ?: 0;
            
            try {
                // Get all student behaviour types
                $result = $this->studentbehaviour_model->get_all($limit, $offset);
                $total_count = $this->studentbehaviour_model->get_count();
                
                json_output(200, array(
                    'status' => 1,
                    'data' => $result,
                    'total_count' => $total_count,
                    'message' => 'Student behaviour types retrieved successfully'
                ));
            } catch (Exception $e) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Internal server error: ' . $e->getMessage()
                ));
            }
        }
    }

    /**
     * Get student behaviour type by ID
     * POST /studentbehaviour/get
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
            $_POST = json_decode(file_get_contents("php://input"), true);
            
            $id = $this->input->post('id');

            // Validation
            if (empty($id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Behaviour ID is required'
                ));
                return;
            }

            try {
                // Get student behaviour type by ID
                $result = $this->studentbehaviour_model->get_by_id($id);
                
                if ($result) {
                    json_output(200, array(
                        'status' => 1,
                        'data' => $result,
                        'message' => 'Student behaviour type retrieved successfully'
                    ));
                } else {
                    json_output(404, array(
                        'status' => 0,
                        'message' => 'Student behaviour type not found'
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
     * Create new student behaviour type
     * POST /studentbehaviour/create
     */
    public function create()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 0, 'message' => 'Bad request. Only POST method allowed.'));
            return;
        }

        $check_auth_client = $this->auth_model->check_auth_client();
        if ($check_auth_client == true) {
            $_POST = json_decode(file_get_contents("php://input"), true);
            
            $title = $this->input->post('title');
            $point = $this->input->post('point');
            $description = $this->input->post('description');

            // Validation
            if (empty($title)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Title is required'
                ));
                return;
            }

            if (!is_numeric($point)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Point must be a number'
                ));
                return;
            }

            try {
                // Prepare data
                $data = array(
                    'title' => $title,
                    'point' => $point,
                    'description' => $description
                );
                
                // Create new student behaviour type
                $result = $this->studentbehaviour_model->create($data);
                
                if ($result) {
                    json_output(200, array(
                        'status' => 1,
                        'data' => array('id' => $result),
                        'message' => 'Student behaviour type created successfully'
                    ));
                } else {
                    json_output(500, array(
                        'status' => 0,
                        'message' => 'Failed to create student behaviour type'
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
     * Update student behaviour type by ID
     * POST /studentbehaviour/update
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
            $title = $this->input->post('title');
            $point = $this->input->post('point');
            $description = $this->input->post('description');

            // Validation
            if (empty($id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Behaviour ID is required'
                ));
                return;
            }

            if (empty($title)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Title is required'
                ));
                return;
            }

            if (!is_numeric($point)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Point must be a number'
                ));
                return;
            }

            try {
                // Prepare data
                $data = array(
                    'title' => $title,
                    'point' => $point,
                    'description' => $description
                );
                
                // Update student behaviour type
                $result = $this->studentbehaviour_model->update($id, $data);
                
                if ($result !== false) {
                    json_output(200, array(
                        'status' => 1,
                        'message' => 'Student behaviour type updated successfully'
                    ));
                } else {
                    json_output(500, array(
                        'status' => 0,
                        'message' => 'Failed to update student behaviour type'
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
     * Delete student behaviour type by ID
     * POST /studentbehaviour/delete
     */
    public function delete()
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

            // Validation
            if (empty($id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Behaviour ID is required'
                ));
                return;
            }

            try {
                // Check if this behaviour type is used in any incidents
                $this->load->model('studentincidents_model');
                $this->db->where('incident_id', $id);
                $incident_count = $this->db->count_all_results('student_incidents');
                
                if ($incident_count > 0) {
                    json_output(400, array(
                        'status' => 0,
                        'message' => 'Cannot delete behaviour type. It is used in ' . $incident_count . ' incident(s).'
                    ));
                    return;
                }
                
                // Delete student behaviour type
                $result = $this->studentbehaviour_model->delete($id);
                
                if ($result !== false) {
                    json_output(200, array(
                        'status' => 1,
                        'message' => 'Student behaviour type deleted successfully'
                    ));
                } else {
                    json_output(500, array(
                        'status' => 0,
                        'message' => 'Failed to delete student behaviour type'
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
