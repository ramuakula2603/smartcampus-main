<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Adminoimport_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('student_model');
        $this->load->library('encoding_lib');
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

    public function import()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                
                if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                    if ($ext == 'csv') {
                        $file = $_FILES['file']['tmp_name'];
                        $this->load->library('CSVReader');
                        $result = $this->csvreader->parse_file($file);

                        if (!empty($result)) {
                            $rowcount = 0;
                            $fields = array('admi_no','admission_no','session');
                            
                            for ($i = 1; $i <= count($result); $i++) {
                                $student_data[$i] = array();
                                $n = 0;
                                // Ensure we don't go out of bounds if CSV has fewer columns than expected
                                foreach ($result[$i] as $key => $value) {
                                    if (isset($fields[$n])) {
                                        $student_data[$i][$fields[$n]] = $this->encoding_lib->toUTF8($result[$i][$key]);
                                    }
                                    $n++;
                                }

                                $admi_no = isset($student_data[$i]["admi_no"]) ? $student_data[$i]["admi_no"] : '';
                                $application_no = isset($student_data[$i]["admission_no"]) ? $student_data[$i]["admission_no"] : '';
                                $session = isset($student_data[$i]["session"]) ? $student_data[$i]["session"] : '';

                                if (!empty($admi_no) && !empty($application_no)) {
                                    $check_admi_no_data_exists = $this->student_model->check_admi_no_data_exists($admi_no);
                                    if(!$check_admi_no_data_exists){
                                        $stid=$this->student_model->getuserid($application_no);
                                        if($stid) {
                                             $data= array(
                                                "admi_no"=>$admi_no,
                                                "admi_status"=>1
                                            );
                                            $cc = $this->student_model->admi_no_update($data, $stid);
                                            if($cc) {
                                                $rowcount++;
                                            }
                                        }
                                    }
                                }
                            }
                            json_output(200, array('status' => 200, 'message' => 'Import successful.', 'records_imported' => $rowcount));
                        } else {
                            json_output(400, array('status' => 400, 'message' => 'No records found in CSV file.'));
                        }
                    } else {
                        json_output(400, array('status' => 400, 'message' => 'Please upload CSV file only.'));
                    }
                } else {
                     json_output(400, array('status' => 400, 'message' => 'File is required.'));
                }

            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }
}
