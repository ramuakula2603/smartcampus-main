<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Hallticket_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('student_model');
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
                    $hall_ticket_status = isset($params['hall_ticket_status']) ? $params['hall_ticket_status'] : '';

                    if ($hall_ticket_status == "withhallticket") {
                        $vall = 1;
                    } elseif ($hall_ticket_status == "nohallticket") {
                        $vall = 0;
                    } else {
                         json_output(400, array('status' => 400, 'message' => 'Invalid hall_ticket_status. Use "withhallticket" or "nohallticket".'));
                         return;
                    }

                    if (!empty($class_id)) {
                        $result = $this->student_model->hallticketnostatusgetDatatableByClassSection($class_id, $section_id, $vall);
                        json_output(200, array('status' => 200, 'message' => 'Success', 'data' => $result));
                    } else {
                        json_output(400, array('status' => 400, 'message' => 'Class ID is required.'));
                    }
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    public function update_hall_ticket()
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
                    $hall_ticket_no = isset($params['hall_ticket_no']) ? $params['hall_ticket_no'] : '';

                    if (!empty($student_id) && !empty($hall_ticket_no)) {
                        // Check if hall ticket number already exists
                         $check_query = $this->db->get_where('student_hallticket', array('std_hallticket' => $hall_ticket_no));
                         if ($check_query->num_rows() > 0) {
                             json_output(400, array('status' => 400, 'message' => 'Hall Ticket Number already exists.'));
                             return;
                         }

                        $admi_no_id = $this->student_model->getadmi_no_id($student_id);
                        
                        if (!$admi_no_id) {
                            json_output(404, array('status' => 404, 'message' => 'Student admission record not found.'));
                            return;
                        }

                        $data = array(
                            'std_hallticket' => $hall_ticket_no,
                            'hallticket_status' => 1,
                            'admi_no_id' => $admi_no_id,
                        );

                        $check = $this->student_model->gethallticket_noo($admi_no_id);

                        if($check){
                            $result = $this->student_model->hallticket_no_update($data, $admi_no_id);
                        }else{
                            $result = $this->student_model->hallticket_no_add($data);
                        }

                        if ($result) {
                            json_output(200, array('status' => 200, 'message' => 'Hall Ticket Number Updated Successfully.'));
                        } else {
                            json_output(500, array('status' => 500, 'message' => 'Failed to update Hall Ticket Number.'));
                        }
                    } else {
                        json_output(400, array('status' => 400, 'message' => 'Student ID and Hall Ticket Number are required.'));
                    }
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    public function get_hall_ticket()
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

                    if (!empty($student_id)) {
                        $admi_no_id = $this->student_model->getadmi_no_id($student_id);
                        
                        if (!$admi_no_id) {
                            json_output(404, array('status' => 404, 'message' => 'Student admission record not found.'));
                            return;
                        }

                        $hall_ticket_no = $this->student_model->gethallticket_no($admi_no_id);

                        if ($hall_ticket_no !== false) {
                            json_output(200, array('status' => 200, 'message' => 'Success', 'hall_ticket_no' => $hall_ticket_no));
                        } else {
                            json_output(404, array('status' => 404, 'message' => 'Hall Ticket Number not found.'));
                        }
                    } else {
                        json_output(400, array('status' => 400, 'message' => 'Student ID is required.'));
                    }
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }
}
