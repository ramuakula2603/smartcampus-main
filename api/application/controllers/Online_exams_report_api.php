<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Online Exams Report API Controller
 * 
 * Provides API endpoints for retrieving online exams reports showing
 * exam details, assigned students, and question counts.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Report APIs
 * @author     SMS Development Team
 * @version    1.0.0
 */
class Online_exams_report_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load required models - IMPORTANT: Load setting_model FIRST
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('onlineexam_model');
        
        // Load helpers
        $this->load->helper('url');
        $this->load->helper('security');
    }

    /**
     * Filter Online Exams Report
     * 
     * @method POST
     * @route  /api/online-exams-report/filter
     * 
     * @param  string $from_date Optional. Start date for date range filter
     * @param  string $to_date   Optional. End date for date range filter
     * 
     * @return JSON Response with status, message, filters_applied, total_records, data, and timestamp
     */
    public function filter()
    {
        try {
            if ($this->input->method() !== 'post') {
                $this->output
                    ->set_status_header(400)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 0,
                        'message' => 'Bad request. Only POST method allowed.'
                    ]));
                return;
            }

            if (!$this->auth_model->check_auth_client()) {
                $this->output
                    ->set_status_header(401)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 0,
                        'message' => 'Unauthorized access'
                    ]));
                return;
            }

            $json_input = json_decode(file_get_contents('php://input'), true);
            
            $from_date = isset($json_input['from_date']) ? $json_input['from_date'] : null;
            $to_date = isset($json_input['to_date']) ? $json_input['to_date'] : null;

            // Build condition for date range
            $condition = '';
            if (!empty($from_date) && !empty($to_date)) {
                $condition = " date_format(onlineexam.created_at,'%Y-%m-%d') between '" . $this->db->escape_str($from_date) . "' and '" . $this->db->escape_str($to_date) . "'";
            } elseif (!empty($from_date)) {
                $condition = " date_format(onlineexam.created_at,'%Y-%m-%d') >= '" . $this->db->escape_str($from_date) . "'";
            } elseif (!empty($to_date)) {
                $condition = " date_format(onlineexam.created_at,'%Y-%m-%d') <= '" . $this->db->escape_str($to_date) . "'";
            } else {
                // Default to current year if no dates provided
                $condition = " date_format(onlineexam.created_at,'%Y-%m-%d') between '" . date('Y') . "-01-01' and '" . date('Y') . "-12-31'";
            }

            // Get online exams report data
            $result = $this->onlineexam_model->onlineexamReport($condition);
            $resultlist = json_decode($result);
            
            $data = array();
            if (!empty($resultlist->data)) {
                $data = $resultlist->data;
            }

            $response = [
                'status' => 1,
                'message' => 'Online exams report retrieved successfully',
                'filters_applied' => [
                    'from_date' => $from_date,
                    'to_date' => $to_date
                ],
                'total_records' => isset($resultlist->recordsTotal) ? $resultlist->recordsTotal : count($data),
                'filtered_records' => isset($resultlist->recordsFiltered) ? $resultlist->recordsFiltered : count($data),
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Online Exams Report API Filter Error: ' . $e->getMessage());
            
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Internal server error occurred',
                    'error' => $e->getMessage(),
                    'data' => null
                ]));
        }
    }

    /**
     * List All Online Exams Report Data
     * 
     * @method POST
     * @route  /api/online-exams-report/list
     * 
     * @return JSON Response with status, message, total_records, data, and timestamp
     */
    public function list()
    {
        try {
            if ($this->input->method() !== 'post') {
                $this->output
                    ->set_status_header(400)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 0,
                        'message' => 'Bad request. Only POST method allowed.'
                    ]));
                return;
            }

            if (!$this->auth_model->check_auth_client()) {
                $this->output
                    ->set_status_header(401)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 0,
                        'message' => 'Unauthorized access'
                    ]));
                return;
            }

            // Default to current year
            $condition = " date_format(onlineexam.created_at,'%Y-%m-%d') between '" . date('Y') . "-01-01' and '" . date('Y') . "-12-31'";
            
            // Get online exams report data
            $result = $this->onlineexam_model->onlineexamReport($condition);
            $resultlist = json_decode($result);
            
            $data = array();
            if (!empty($resultlist->data)) {
                $data = $resultlist->data;
            }

            $response = [
                'status' => 1,
                'message' => 'Online exams report retrieved successfully',
                'year' => date('Y'),
                'total_records' => isset($resultlist->recordsTotal) ? $resultlist->recordsTotal : count($data),
                'filtered_records' => isset($resultlist->recordsFiltered) ? $resultlist->recordsFiltered : count($data),
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Online Exams Report API List Error: ' . $e->getMessage());
            
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Internal server error occurred',
                    'error' => $e->getMessage(),
                    'data' => null
                ]));
        }
    }
}

