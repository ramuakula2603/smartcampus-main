<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Admino_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('student_model');
        $this->load->model('class_model');
        $this->load->model('section_model');
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

    public function search()
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

                    $class_id = isset($params['class_id']) ? $params['class_id'] : '';
                    $section_id = isset($params['section_id']) ? $params['section_id'] : '';
                    $admistatus = isset($params['admi_status']) ? $params['admi_status'] : '';

                    if ($admistatus == "withadmissionno") {
                        $vall = 1;
                    } elseif ($admistatus == "noadmissionno") {
                        $vall = 0;
                    } else {
                         json_output(400, array('status' => 400, 'message' => 'Invalid admi_status. Use "withadmissionno" or "noadmissionno".'));
                         return;
                    }

                    if (!empty($class_id)) {
                        $result = $this->student_model->admissionnostatusgetDatatableByClassSection($class_id, $section_id, $vall);
                        json_output(200, array('status' => 200, 'message' => 'Success', 'data' => $result));
                    } else {
                        json_output(400, array('status' => 400, 'message' => 'Class ID is required.'));
                    }
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    public function update_admission_no()
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

                    $student_id = isset($params['student_id']) ? $params['student_id'] : '';
                    $admi_no = isset($params['admi_no']) ? $params['admi_no'] : '';

                    if (!empty($student_id) && !empty($admi_no)) {
                        // Check if admission number already exists
                         $check_query = $this->db->get_where('student_admi', array('admi_no' => $admi_no));
                         if ($check_query->num_rows() > 0) {
                             json_output(400, array('status' => 400, 'message' => 'Admission Number already exists.'));
                             return;
                         }

                        $data = array(
                            'admi_no' => $admi_no,
                            'admi_status' => 1,
                            'student_id' => $student_id,
                        );

                        $result = $this->student_model->admi_no_update($data, $student_id);

                        if ($result) {
                            json_output(200, array('status' => 200, 'message' => 'Admission Number Updated Successfully.'));
                        } else {
                            json_output(500, array('status' => 500, 'message' => 'Failed to update Admission Number.'));
                        }
                    } else {
                        json_output(400, array('status' => 400, 'message' => 'Student ID and Admission Number are required.'));
                    }
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    public function searching()
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

                    $admission_no = isset($params['admission_no']) ? $params['admission_no'] : '';

                    if (!empty($admission_no)) {
                        // Get student details by admission number
                        $student_id = $this->student_model->getuseridfromadminotable($admission_no);

                        // If not found in student_admi, try to find in students table
                        if (!$student_id) {
                            $this->db->select('id');
                            $this->db->from('students');
                            $this->db->where('admission_no', $admission_no);
                            $query_students = $this->db->get();

                            if ($query_students->num_rows() > 0) {
                                $student_id = $query_students->row()->id;
                            }
                        }

                        if ($student_id) {
                            // Get student details
                            $studentDetails = $this->student_model->get($student_id);

                            // For student session details, we need to get the current class and section
                            $this->db->select('student_session.*, classes.class, sections.section');
                            $this->db->from('student_session');
                            $this->db->join('classes', 'classes.id = student_session.class_id');
                            $this->db->join('sections', 'sections.id = student_session.section_id');
                            $this->db->where('student_session.student_id', $student_id);
                            $this->db->where('student_session.session_id', $this->student_model->current_session);
                            $query = $this->db->get();
                            $student_session = $query->result_array();

                            json_output(200, array(
                                'status' => 200, 
                                'message' => 'Student found.', 
                                'data' => array(
                                    'studentDetails' => $studentDetails,
                                    'student_session' => $student_session,
                                    'admission_no' => $admission_no
                                )
                            ));
                        } else {
                            json_output(404, array('status' => 404, 'message' => 'Student not found.'));
                        }
                    } else {
                        json_output(400, array('status' => 400, 'message' => 'Admission Number is required.'));
                    }
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }
}
