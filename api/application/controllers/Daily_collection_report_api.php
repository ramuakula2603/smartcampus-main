<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Daily Collection Report API Controller
 * 
 * This controller handles API requests for daily fee collection reports
 * showing daily collection amounts with filtering by date range.
 * 
 * @package    CodeIgniter
 * @subpackage Controllers
 * @category   API
 */
class Daily_collection_report_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Load required models - IMPORTANT: setting_model MUST be loaded FIRST
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('studentfeemaster_model');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('custom');
        $this->load->database();
    }

    /**
     * Filter daily collection report
     * 
     * POST /api/daily-collection-report/filter
     * 
     * Request body (all parameters optional):
     * {
     *   "date_from": "2025-01-01",
     *   "date_to": "2025-01-31"
     * }
     * 
     * Empty request body {} returns current month's collection
     */
    public function filter()
    {
        try {
            // Check request method
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

            // Check authentication
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

            // Get JSON input
            $json_input = json_decode($this->input->raw_input_stream, true);
            
            // Get filter parameters (all optional)
            $date_from = isset($json_input['date_from']) ? $json_input['date_from'] : date('Y-m-01');
            $date_to = isset($json_input['date_to']) ? $json_input['date_to'] : date('Y-m-t');

            $formated_date_from = strtotime($date_from);
            $formated_date_to = strtotime($date_to);
            
            // Get student fees
            $st_fees = $this->studentfeemaster_model->getCurrentSessionStudentFeess();
            $st_other_fees = $this->studentfeemaster_model->getOtherfeesCurrentSessionStudentFeess();
            
            $fees_data = array();
            $other_fees_data = array();

            // Initialize date range
            for ($i = $formated_date_from; $i <= $formated_date_to; $i += 86400) {
                $fees_data[$i]['date'] = date('Y-m-d', $i);
                $fees_data[$i]['amt'] = 0;
                $fees_data[$i]['count'] = 0;
                $fees_data[$i]['student_fees_deposite_ids'] = array();
            }

            // Process regular fees
            if (!empty($st_fees)) {
                foreach ($st_fees as $fee_key => $fee_value) {
                    if (isJSON($fee_value->amount_detail)) {
                        $fees_details = json_decode($fee_value->amount_detail);
                        if (!empty($fees_details)) {
                            foreach ($fees_details as $fees_detail_key => $fees_detail_value) {
                                $date = strtotime($fees_detail_value->date);
                                if ($date >= $formated_date_from && $date <= $formated_date_to) {
                                    if (array_key_exists($date, $fees_data)) {
                                        $fees_data[$date]['amt'] += $fees_detail_value->amount + $fees_detail_value->amount_fine;
                                        $fees_data[$date]['count'] += 1;
                                        $fees_data[$date]['student_fees_deposite_ids'][] = $fee_value->student_fees_deposite_id;
                                    } else {
                                        $fees_data[$date]['date'] = date('Y-m-d', $date);
                                        $fees_data[$date]['amt'] = $fees_detail_value->amount + $fees_detail_value->amount_fine;
                                        $fees_data[$date]['count'] = 1;
                                        $fees_data[$date]['student_fees_deposite_ids'][] = $fee_value->student_fees_deposite_id;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // Process other fees
            if (!empty($st_other_fees)) {
                foreach ($st_other_fees as $fee_key => $fee_value) {
                    if (isJSON($fee_value->amount_detail)) {
                        $fees_details = json_decode($fee_value->amount_detail);
                        if (!empty($fees_details)) {
                            foreach ($fees_details as $fees_detail_key => $fees_detail_value) {
                                $date = strtotime($fees_detail_value->date);
                                if ($date >= $formated_date_from && $date <= $formated_date_to) {
                                    if (array_key_exists($date, $other_fees_data)) {
                                        $other_fees_data[$date]['amt'] += $fees_detail_value->amount + $fees_detail_value->amount_fine;
                                        $other_fees_data[$date]['count'] += 1;
                                        $other_fees_data[$date]['student_fees_deposite_ids'][] = $fee_value->student_fees_deposite_id;
                                    } else {
                                        $other_fees_data[$date]['date'] = date('Y-m-d', $date);
                                        $other_fees_data[$date]['amt'] = $fees_detail_value->amount + $fees_detail_value->amount_fine;
                                        $other_fees_data[$date]['count'] = 1;
                                        $other_fees_data[$date]['student_fees_deposite_ids'][] = $fee_value->student_fees_deposite_id;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $response = [
                'status' => 1,
                'message' => 'Daily collection report retrieved successfully',
                'filters_applied' => [
                    'date_from' => $date_from,
                    'date_to' => $date_to
                ],
                'total_records' => count($fees_data),
                'fees_data' => array_values($fees_data),
                'other_fees_data' => array_values($other_fees_data),
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Daily Collection Report API Error: ' . $e->getMessage());
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Internal server error',
                    'error' => $e->getMessage()
                ]));
        }
    }

    /**
     * List daily collection filter options
     * 
     * POST /api/daily-collection-report/list
     * 
     * Returns date range suggestions
     */
    public function list()
    {
        try {
            // Check request method
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

            // Check authentication
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

            $date_ranges = array(
                array('label' => 'This Month', 'date_from' => date('Y-m-01'), 'date_to' => date('Y-m-t')),
                array('label' => 'Last Month', 'date_from' => date('Y-m-01', strtotime('last month')), 'date_to' => date('Y-m-t', strtotime('last month'))),
                array('label' => 'This Year', 'date_from' => date('Y-01-01'), 'date_to' => date('Y-12-31'))
            );

            $response = [
                'status' => 1,
                'message' => 'Daily collection filter options retrieved successfully',
                'date_ranges' => $date_ranges,
                'note' => 'Use the filter endpoint with date_from and date_to to get daily collection report',
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Daily Collection Report API Error: ' . $e->getMessage());
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Internal server error',
                    'error' => $e->getMessage()
                ]));
        }
    }
}

