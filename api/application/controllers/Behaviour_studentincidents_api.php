<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Behaviour_studentincidents_api extends CI_Controller
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
            'studentincidents_model',
            'auth_model'
        ));
    }

    /**
     * Get student incidents by student ID
     * POST /behaviour/studentincidents/get-by-student
     */
    public function get_by_student()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 0, 'message' => 'Bad request. Only POST method allowed.'));
            return;
        }

        $check_auth_client = $this->auth_model->check_auth_client();
        if ($check_auth_client == true) {
            $_POST = json_decode(file_get_contents("php://input"), true);
            
            $student_id = $this->input->post('student_id');
            $session_value = $this->input->post('session_value');

            // Validation
            if (empty($student_id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Student ID is required'
                ));
                return;
            }

            try {
                // Get student incidents
                $result = $this->studentincidents_model->assignstudent($student_id, $session_value);
                
                if ($result) {
                    json_output(200, array(
                        'status' => 1,
                        'data' => json_decode($result, true),
                        'message' => 'Student incidents retrieved successfully'
                    ));
                } else {
                    json_output(500, array(
                        'status' => 0,
                        'message' => 'Failed to retrieve student incidents'
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
     * Get student total points by student ID
     * POST /behaviour/studentincidents/total-points
     */
    public function total_points()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 0, 'message' => 'Bad request. Only POST method allowed.'));
            return;
        }

        $check_auth_client = $this->auth_model->check_auth_client();
        if ($check_auth_client == true) {
            $_POST = json_decode(file_get_contents("php://input"), true);
            
            $student_id = $this->input->post('student_id');

            // Validation
            if (empty($student_id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Student ID is required'
                ));
                return;
            }

            try {
                // Get student total points
                $result = $this->studentincidents_model->totalpoints($student_id);
                
                if ($result) {
                    json_output(200, array(
                        'status' => 1,
                        'data' => $result,
                        'message' => 'Student total points retrieved successfully'
                    ));
                } else {
                    json_output(500, array(
                        'status' => 0,
                        'message' => 'Failed to retrieve student total points'
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
     * Get student behavior records
     * POST /behaviour/studentincidents/student-behavior
     */
    public function student_behavior()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 0, 'message' => 'Bad request. Only POST method allowed.'));
            return;
        }

        $check_auth_client = $this->auth_model->check_auth_client();
        if ($check_auth_client == true) {
            $_POST = json_decode(file_get_contents("php://input"), true);
            
            $student_id = $this->input->post('student_id');

            // Validation
            if (empty($student_id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Student ID is required'
                ));
                return;
            }

            try {
                // Get student behavior records
                $result = $this->studentincidents_model->studentbehaviour($student_id);
                
                if ($result !== false) {
                    json_output(200, array(
                        'status' => 1,
                        'data' => $result,
                        'message' => 'Student behavior records retrieved successfully'
                    ));
                } else {
                    json_output(500, array(
                        'status' => 0,
                        'message' => 'Failed to retrieve student behavior records'
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
     * Delete student incident by ID
     * POST /behaviour/studentincidents/delete
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
                    'message' => 'Incident ID is required'
                ));
                return;
            }

            try {
                // Delete student incident
                $result = $this->studentincidents_model->delete($id);
                
                if ($result !== false) {
                    json_output(200, array(
                        'status' => 1,
                        'message' => 'Student incident deleted successfully'
                    ));
                } else {
                    json_output(500, array(
                        'status' => 0,
                        'message' => 'Failed to delete student incident'
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
     * Add or update student incident comment
     * POST /behaviour/studentincidents/add-comment
     */
    public function add_comment()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 0, 'message' => 'Bad request. Only POST method allowed.'));
            return;
        }

        $check_auth_client = $this->auth_model->check_auth_client();
        if ($check_auth_client == true) {
            $_POST = json_decode(file_get_contents("php://input"), true);
            
            $data = $this->input->post('data');

            // Validation
            if (empty($data)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Comment data is required'
                ));
                return;
            }

            try {
                // Add student incident comment
                $result = $this->studentincidents_model->addmessage($data);
                
                if ($result) {
                    json_output(200, array(
                        'status' => 1,
                        'data' => array('id' => $result),
                        'message' => 'Student incident comment added successfully'
                    ));
                } else {
                    json_output(500, array(
                        'status' => 0,
                        'message' => 'Failed to add student incident comment'
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
     * Get student incident comments by incident ID
     * POST /behaviour/studentincidents/get-comments
     */
    public function get_comments()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 0, 'message' => 'Bad request. Only POST method allowed.'));
            return;
        }

        $check_auth_client = $this->auth_model->check_auth_client();
        if ($check_auth_client == true) {
            $_POST = json_decode(file_get_contents("php://input"), true);
            
            $student_incident_id = $this->input->post('student_incident_id');

            // Validation
            if (empty($student_incident_id)) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Student incident ID is required'
                ));
                return;
            }

            try {
                // Get student incident comments
                $result = $this->studentincidents_model->getmessage($student_incident_id);
                
                if ($result !== false) {
                    json_output(200, array(
                        'status' => 1,
                        'data' => $result,
                        'message' => 'Student incident comments retrieved successfully'
                    ));
                } else {
                    json_output(500, array(
                        'status' => 0,
                        'message' => 'Failed to retrieve student incident comments'
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
     * Delete student incident comment by ID
     * POST /behaviour/studentincidents/delete-comment
     */
    public function delete_comment()
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
                    'message' => 'Comment ID is required'
                ));
                return;
            }

            try {
                // Delete student incident comment
                $this->studentincidents_model->delete_comment($id);
                
                json_output(200, array(
                    'status' => 1,
                    'message' => 'Student incident comment deleted successfully'
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
     * Get behavior report with filters
     * POST /behaviour/studentincidents/report
     */
    public function report()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 0, 'message' => 'Bad request. Only POST method allowed.'));
            return;
        }

        $check_auth_client = $this->auth_model->check_auth_client();
        if ($check_auth_client == true) {
            $_POST = json_decode(file_get_contents("php://input"), true);
            
            $class_id = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');
            $student_id = $this->input->post('student_id');
            $from_date = $this->input->post('from_date');
            $to_date = $this->input->post('to_date');
            $incident_id = $this->input->post('incident_id');

            try {
                // Get behavior report data
                $this->load->model('student_model');
                $result = $this->studentincidents_model->get_report_data($class_id, $section_id, $student_id, $from_date, $to_date, $incident_id);
                
                if ($result !== false) {
                    json_output(200, array(
                        'status' => 1,
                        'data' => $result,
                        'message' => 'Behavior report retrieved successfully'
                    ));
                } else {
                    json_output(500, array(
                        'status' => 0,
                        'message' => 'Failed to retrieve behavior report'
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
