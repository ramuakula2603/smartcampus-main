<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Fee Group-wise Collection Report API Controller
 * 
 * Provides API endpoints for fee group-wise collection reports with filtering by:
 * - Session
 * - Class and Section
 * - Fee Groups
 * - Date range (from_date/to_date)
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Finance Reports
 * @author     SMS Development Team
 * @version    1.0.0
 */
class Feegroupwise_collection_report_api extends CI_Controller
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
        $this->load->model('feegroupwise_model');

        // Load library
        $this->load->library('customlib');

        // Load helper for JSON validation
        $this->load->helper('custom');

        // Get current session
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    /**
     * Filter endpoint - Get fee group-wise collection report with filters
     * POST /api/feegroupwise-collection-report/filter
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
            
            // Get filter parameters (all optional)
            // Treat empty strings and empty arrays as null for graceful handling
            $session_id = (isset($json_input['session_id']) && $json_input['session_id'] !== '') ? $json_input['session_id'] : $this->current_session;
            
            $class_ids = (isset($json_input['class_ids']) && is_array($json_input['class_ids']) && !empty($json_input['class_ids'])) ? $json_input['class_ids'] : array();
            
            $section_ids = (isset($json_input['section_ids']) && is_array($json_input['section_ids']) && !empty($json_input['section_ids'])) ? $json_input['section_ids'] : array();
            
            $feegroup_ids = (isset($json_input['feegroup_ids']) && is_array($json_input['feegroup_ids']) && !empty($json_input['feegroup_ids'])) ? $json_input['feegroup_ids'] : array();
            
            $from_date = (isset($json_input['from_date']) && $json_input['from_date'] !== '') ? $json_input['from_date'] : null;
            
            $to_date = (isset($json_input['to_date']) && $json_input['to_date'] !== '') ? $json_input['to_date'] : null;

            // Get fee group-wise collection data (summary)
            $grid_data = $this->feegroupwise_model->getFeeGroupwiseCollection(
                $session_id,
                $class_ids,
                $section_ids,
                $feegroup_ids,
                $from_date,
                $to_date
            );

            // Get detailed student records
            $detailed_data = $this->feegroupwise_model->getFeeGroupwiseDetailedData(
                $session_id,
                $class_ids,
                $section_ids,
                $feegroup_ids,
                $from_date,
                $to_date
            );

            // Calculate summary statistics
            // Note: balance_amount from model is already calculated as max(0, total - collected)
            $summary = array(
                'total_fee_groups' => count($grid_data),
                'total_amount' => 0,
                'total_collected' => 0,
                'total_balance' => 0,
                'collection_percentage' => 0
            );

            foreach ($grid_data as $row) {
                $summary['total_amount'] += floatval($row->total_amount);
                $summary['total_collected'] += floatval($row->amount_collected);
                $summary['total_balance'] += floatval($row->balance_amount);
            }

            // Ensure total balance is never negative (in case of overpayments)
            $summary['total_balance'] = max(0, $summary['total_balance']);

            if ($summary['total_amount'] > 0) {
                $summary['collection_percentage'] = round(($summary['total_collected'] / $summary['total_amount']) * 100, 2);
            }

            // Format amounts to 2 decimal places
            $summary['total_amount'] = number_format($summary['total_amount'], 2, '.', '');
            $summary['total_collected'] = number_format($summary['total_collected'], 2, '.', '');
            $summary['total_balance'] = number_format($summary['total_balance'], 2, '.', '');

            // Format grid data amounts
            foreach ($grid_data as $row) {
                $row->total_amount = number_format(floatval($row->total_amount), 2, '.', '');
                $row->amount_collected = number_format(floatval($row->amount_collected), 2, '.', '');
                $row->balance_amount = number_format(floatval($row->balance_amount), 2, '.', '');
            }

            // Format detailed data amounts
            foreach ($detailed_data as $row) {
                $row->total_amount = number_format(floatval($row->total_amount), 2, '.', '');
                $row->amount_collected = number_format(floatval($row->amount_collected), 2, '.', '');
                $row->balance_amount = number_format(floatval($row->balance_amount), 2, '.', '');
            }

            $response = [
                'status' => 1,
                'message' => 'Fee group-wise collection report retrieved successfully',
                'filters_applied' => [
                    'session_id' => $session_id,
                    'class_ids' => $class_ids,
                    'section_ids' => $section_ids,
                    'feegroup_ids' => $feegroup_ids,
                    'from_date' => $from_date,
                    'to_date' => $to_date
                ],
                'summary' => $summary,
                'grid_data' => $grid_data,
                'detailed_data' => $detailed_data,
                'total_fee_groups' => count($grid_data),
                'total_detailed_records' => count($detailed_data),
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
                    'message' => 'Error retrieving fee group-wise collection report: ' . $e->getMessage()
                ]));
        }
    }

    /**
     * List endpoint - Get filter options (fee groups, classes, sections)
     * POST /api/feegroupwise-collection-report/list
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
            // Get JSON input
            $json_input = json_decode($this->input->raw_input_stream, true);
            
            // Get session_id parameter (optional)
            $session_id = (isset($json_input['session_id']) && $json_input['session_id'] !== '') ? $json_input['session_id'] : $this->current_session;

            // Load required models
            $this->load->model('class_model');
            $this->load->model('session_model');

            // Get all fee groups for the session
            $fee_groups = $this->feegroupwise_model->getAllFeeGroups($session_id);

            // Get all classes
            $classes = $this->class_model->get();

            // Get all sessions
            $sessions = $this->session_model->get();

            $response = [
                'status' => 1,
                'message' => 'Filter options retrieved successfully',
                'current_session' => $this->current_session,
                'fee_groups' => $fee_groups,
                'classes' => $classes,
                'sessions' => $sessions,
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

