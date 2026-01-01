<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Resultsubject_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('resultsubjects_model');
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
     * Get all result subjects
     * 
     * @return void Outputs JSON response with list of result subjects
     */
    public function get_result_subjects()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                $subjects = $this->resultsubjects_model->get();
                
                // Map 'examtype' to 'subject_name' for better API readability
                $formatted_subjects = array();
                if (!empty($subjects)) {
                    foreach ($subjects as $subject) {
                        $formatted_subjects[] = array(
                            'id' => $subject['id'],
                            'subject_name' => $subject['examtype'], // Mapping examtype to subject_name
                            'subject_code' => $subject['subject_code']
                        );
                    }
                }
                
                json_output(200, array('status' => 200, 'message' => 'Success', 'data' => $formatted_subjects));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    /**
     * Get specific result subject by ID
     * 
     * @param int $id Result subject ID
     * @return void Outputs JSON response with result subject details
     */
    public function get_result_subject($id)
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                if (empty($id)) {
                    json_output(400, array('status' => 400, 'message' => 'Result subject ID is required.'));
                    return;
                }

                $subject = $this->resultsubjects_model->get($id);
                
                if (empty($subject)) {
                    json_output(404, array('status' => 404, 'message' => 'Result subject not found.'));
                    return;
                }

                // Map 'examtype' to 'subject_name'
                $formatted_subject = array(
                    'id' => $subject['id'],
                    'subject_name' => $subject['examtype'],
                    'subject_code' => $subject['subject_code']
                );

                json_output(200, array('status' => 200, 'message' => 'Success', 'data' => $formatted_subject));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    /**
     * Create new result subject
     * 
     * @return void Outputs JSON response with creation status
     */
    public function create_result_subject()
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

                $subject_name = isset($params['subject_name']) ? trim($params['subject_name']) : '';
                $subject_code = isset($params['subject_code']) ? trim($params['subject_code']) : '';

                // Validate required fields
                if (empty($subject_name)) {
                    json_output(400, array('status' => 400, 'message' => 'Subject name is required.'));
                    return;
                }
                if (empty($subject_code)) {
                    json_output(400, array('status' => 400, 'message' => 'Subject code is required.'));
                    return;
                }

                // Check for duplicate subject code
                // Note: The model doesn't have a check method, so we'll do a manual check
                // Ideally we should add a method to the model, but for now we'll use direct DB query
                // to avoid modifying core files too much.
                $this->db->where('subject_code', $subject_code);
                $query = $this->db->get('resultsubjects');
                if ($query->num_rows() > 0) {
                    json_output(400, array('status' => 400, 'message' => 'Subject code already exists.'));
                    return;
                }

                // Prepare data
                // Note: Database column is 'examtype' for subject name
                $data = array(
                    'examtype' => $subject_name,
                    'subject_code' => strtoupper($subject_code)
                );

                // Insert the data
                $insert_id = $this->resultsubjects_model->add($data);

                json_output(201, array(
                    'status' => 201,
                    'message' => 'Result subject created successfully.',
                    'data' => array(
                        'id' => $insert_id,
                        'subject_name' => $subject_name,
                        'subject_code' => strtoupper($subject_code)
                    )
                ));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    /**
     * Update result subject
     * 
     * @param int $id Result subject ID
     * @return void Outputs JSON response with update status
     */
    public function update_result_subject($id)
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'PUT' && $method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                if (empty($id)) {
                    json_output(400, array('status' => 400, 'message' => 'Result subject ID is required.'));
                    return;
                }

                // Check if subject exists
                $existing_subject = $this->resultsubjects_model->get($id);
                if (empty($existing_subject)) {
                    json_output(404, array('status' => 404, 'message' => 'Result subject not found.'));
                    return;
                }

                $params = json_decode(file_get_contents('php://input'), TRUE);
                
                if (empty($params)) {
                    json_output(400, array('status' => 400, 'message' => 'Invalid JSON format or empty body.'));
                    return;
                }

                $subject_name = isset($params['subject_name']) ? trim($params['subject_name']) : '';
                $subject_code = isset($params['subject_code']) ? trim($params['subject_code']) : '';

                // Validate required fields
                if (empty($subject_name)) {
                    json_output(400, array('status' => 400, 'message' => 'Subject name is required.'));
                    return;
                }
                if (empty($subject_code)) {
                    json_output(400, array('status' => 400, 'message' => 'Subject code is required.'));
                    return;
                }

                // Check for duplicate subject code (excluding current record)
                $this->db->where('subject_code', $subject_code);
                $this->db->where('id !=', $id);
                $query = $this->db->get('resultsubjects');
                if ($query->num_rows() > 0) {
                    json_output(400, array('status' => 400, 'message' => 'Subject code already exists.'));
                    return;
                }

                // Prepare data
                $data = array(
                    'id' => $id,
                    'examtype' => $subject_name,
                    'subject_code' => strtoupper($subject_code)
                );

                // Update the data
                $this->resultsubjects_model->add($data);

                json_output(200, array(
                    'status' => 200,
                    'message' => 'Result subject updated successfully.',
                    'data' => array(
                        'id' => $id,
                        'subject_name' => $subject_name,
                        'subject_code' => strtoupper($subject_code)
                    )
                ));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    /**
     * Delete result subject
     * 
     * @param int $id Result subject ID
     * @return void Outputs JSON response with deletion status
     */
    public function delete_result_subject($id)
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'DELETE' && $method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                if (empty($id)) {
                    json_output(400, array('status' => 400, 'message' => 'Result subject ID is required.'));
                    return;
                }

                // Check if subject exists
                $existing_subject = $this->resultsubjects_model->get($id);
                if (empty($existing_subject)) {
                    json_output(404, array('status' => 404, 'message' => 'Result subject not found.'));
                    return;
                }

                // Delete the subject
                $this->resultsubjects_model->remove($id);

                json_output(200, array(
                    'status' => 200,
                    'message' => 'Result subject deleted successfully.',
                    'data' => array('id' => $id)
                ));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }
}
