<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Online Admission Fee Report API Controller
 * 
 * Provides API endpoints for online admission fee collection reports
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Finance Reports
 * @author     SMS Development Team
 * @version    1.0.0
 */
class Online_admission_fee_report_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Disable error display - API should only return JSON
        ini_set('display_errors', 0);
        error_reporting(0);

        // Load required models in correct order
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('onlinestudent_model');

        // Load library
        $this->load->library('customlib');

        // Load helper
        $this->load->helper('custom');
    }

    /**
     * Filter endpoint - Get online admission report with filters
     * POST /api/online-admission-report/filter
     */
    public function filter()
    {
        // Authenticate request
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

        try {
            // Get JSON input
            $json_input = json_decode($this->input->raw_input_stream, true);
            
            // Get filter parameters - gracefully handle null/empty values
            $search_type = (isset($json_input['search_type']) && $json_input['search_type'] !== '') ? $json_input['search_type'] : null;
            $date_from = (isset($json_input['date_from']) && $json_input['date_from'] !== '') ? $json_input['date_from'] : null;
            $date_to = (isset($json_input['date_to']) && $json_input['date_to'] !== '') ? $json_input['date_to'] : null;

            // Determine date range
            if ($search_type !== null && $search_type !== '') {
                $dates = $this->customlib->get_betweendate($search_type);
            } elseif ($date_from !== null && $date_to !== null && $date_from !== '' && $date_to !== '') {
                $dates = array(
                    'from_date' => $date_from,
                    'to_date' => $date_to
                );
            } else {
                // Default to current year if no dates provided
                $search_year = date('Y');
                $dates = array(
                    'from_date' => $search_year . '-01-01',
                    'to_date' => $search_year . '-12-31'
                );
            }

            $start_date = date('Y-m-d', strtotime($dates['from_date']));
            $end_date = date('Y-m-d', strtotime($dates['to_date']));

            // Get online admission data
            $admissions = $this->onlinestudent_model->getOnlineAdmissionFeeCollectionReport($start_date, $end_date);

            // Get summary data
            $payment_summary = $this->onlinestudent_model->getOnlineAdmissionPaymentSummary($start_date, $end_date);
            $class_summary = $this->onlinestudent_model->getOnlineAdmissionsByClass($start_date, $end_date);

            // Calculate totals
            $total_amount = 0;
            $total_admissions = 0;
            $unique_admissions = array();

            foreach ($admissions as $admission) {
                $total_amount += floatval($admission['paid_amount']);
                if (!in_array($admission['online_admission_id'], $unique_admissions)) {
                    $unique_admissions[] = $admission['online_admission_id'];
                    $total_admissions++;
                }
            }

            $response = [
                'status' => 1,
                'message' => 'Online admission report retrieved successfully',
                'filters_applied' => [
                    'search_type' => $search_type,
                    'date_from' => $start_date,
                    'date_to' => $end_date
                ],
                'date_range' => [
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'label' => date($this->customlib->getSchoolDateFormat(), strtotime($start_date)) . " to " . date($this->customlib->getSchoolDateFormat(), strtotime($end_date))
                ],
                'summary' => [
                    'total_admissions' => $total_admissions,
                    'total_payments' => count($admissions),
                    'total_amount' => number_format($total_amount, 2),
                    'by_payment_mode' => $payment_summary,
                    'by_class' => $class_summary
                ],
                'total_records' => count($admissions),
                'data' => $admissions,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Error retrieving online admission report: ' . $e->getMessage()
                ]));
        }
    }

    /**
     * List endpoint - Get filter options
     * POST /api/online-admission-report/list
     */
    public function list()
    {
        // Authenticate request
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

        try {
            // Get search types (date ranges)
            $search_types = $this->customlib->get_searchtype();

            // Get group by options
            $group_by = $this->customlib->get_groupby();

            $response = [
                'status' => 1,
                'message' => 'Filter options retrieved successfully',
                'data' => [
                    'search_types' => $search_types,
                    'group_by' => $group_by
                ],
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Error retrieving filter options: ' . $e->getMessage()
                ]));
        }
    }
}

