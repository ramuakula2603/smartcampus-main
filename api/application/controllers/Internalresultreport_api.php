<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Internalresultreport_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('internalresult_model');
        $this->load->model('examtype_model');
        $this->load->model('class_model');
        $this->load->model('section_model');
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
     * Get internal result report based on filters
     * 
     * @return void Outputs JSON response with report data
     */
    public function get_internal_result_report()
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

                // Extract filters
                $filters = array();
                
                if (isset($params['session_id']) && !empty($params['session_id'])) {
                    $filters['session_id'] = $params['session_id'];
                }
                
                if (isset($params['exam_type_id']) && !empty($params['exam_type_id'])) {
                    $filters['exam_type_id'] = $params['exam_type_id'];
                }
                
                if (isset($params['class_id']) && !empty($params['class_id'])) {
                    $filters['class_id'] = $params['class_id'];
                }
                
                if (isset($params['section_id']) && !empty($params['section_id'])) {
                    $filters['section_id'] = $params['section_id'];
                }
                
                if (isset($params['status']) && !empty($params['status'])) {
                    $filters['status'] = $params['status']; // pass, fail, absent, all
                }

                // Get report data
                $report_data = $this->internalresult_model->getInternalResultsReport($filters);
                
                json_output(200, array(
                    'status' => 200, 
                    'message' => 'Success', 
                    'data' => array(
                        'report' => $report_data,
                        'count' => count($report_data)
                    )
                ));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }

    /**
     * Get filter options for the report
     * 
     * @return void Outputs JSON response with filter options
     */
    public function get_filter_options()
    {
        $method = $this->input->server('REQUEST_METHOD');
        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            if ($this->validate_headers()) {
                $data = array();
                
                // Get sessions
                $data['sessions'] = $this->internalresult_model->getSessionsWithResults();
                
                // Get exam types (internal result types)
                $data['exam_types'] = $this->examtype_model->get();
                
                // Get classes
                $data['classes'] = $this->class_model->get();
                
                // Get sections (all)
                $data['sections'] = $this->section_model->get();
                
                // Status options
                $data['statuses'] = array(
                    array('id' => 'all', 'name' => 'All'),
                    array('id' => 'pass', 'name' => 'Pass'),
                    array('id' => 'fail', 'name' => 'Fail'),
                    array('id' => 'absent', 'name' => 'Absent')
                );
                
                json_output(200, array('status' => 200, 'message' => 'Success', 'data' => $data));
            } else {
                json_output(401, array('status' => 401, 'message' => 'Client Service or Auth Key is invalid.'));
            }
        }
    }
}
