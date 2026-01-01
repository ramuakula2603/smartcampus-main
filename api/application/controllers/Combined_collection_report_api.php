<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Combined Collection Report API Controller
 * 
 * Provides API endpoints for combined fee collection reports (regular + other + transport fees)
 * 
 * @package    School Management System API
 * @subpackage Controllers
 * @category   Finance Reports
 * @author     SMS Development Team
 * @version    1.0
 */
class Combined_collection_report_api extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        // Suppress errors for clean JSON output
        ini_set('display_errors', 0);
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE);

        // Set JSON response header
        header('Content-Type: application/json');

        // Try to load database with error handling
        try {
            $this->load->database();

            // Test database connection
            if (!$this->db->conn_id) {
                throw new Exception('Database connection failed');
            }

            // Load required models
            $this->load->model('setting_model');
            $this->load->model('auth_model');
            $this->load->model('module_model');
            $this->load->model('class_model');
            $this->load->model('section_model');
            $this->load->model('studentfeemaster_model');
            $this->load->model('staff_model'); // Needed by studentfeemasteradding_model

            // Load customlib library for date calculations
            $this->load->library('customlib');

            // Load model from main application directory
            // The model exists in application/models/ not api/application/models/
            // FCPATH points to api/ directory, so we need to go up one level with ../
            // First load MY_Model from main application if not already loaded
            if (!class_exists('MY_Model', false)) {
                require_once(FCPATH . '../application/core/MY_Model.php');
            }
            require_once(FCPATH . '../application/models/Studentfeemasteradding_model.php');
            $this->studentfeemasteradding_model = new Studentfeemasteradding_model();

        } catch (Exception $e) {
            // Return JSON error response
            echo json_encode(array(
                'status' => 0,
                'message' => 'Database connection error. Please ensure MySQL is running in XAMPP.',
                'error' => 'Unable to connect to database server',
                'timestamp' => date('Y-m-d H:i:s')
            ));
            exit;
        }
    }

    /**
     * List endpoint - Get filter options
     * 
     * POST /api/combined-collection-report/list
     */
    public function list()
    {
        try {
            // Authenticate request
            if (!$this->auth_model->check_auth_client()) {
                echo json_encode(array(
                    'status' => 0,
                    'message' => 'Unauthorized access',
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Get filter options
            $search_types = array(
                array('key' => 'today', 'label' => 'Today'),
                array('key' => 'this_week', 'label' => 'This Week'),
                array('key' => 'this_month', 'label' => 'This Month'),
                array('key' => 'last_month', 'label' => 'Last Month'),
                array('key' => 'this_year', 'label' => 'This Year'),
                array('key' => 'period', 'label' => 'Custom Period')
            );

            $group_by = array(
                array('key' => 'class', 'label' => 'Group By Class'),
                array('key' => 'collection', 'label' => 'Group By Collection'),
                array('key' => 'mode', 'label' => 'Group By Payment Mode')
            );

            // Get classes with sections
            $classes = $this->class_model->get();
            
            // Get regular fee types
            $this->db->select('id, type');
            $this->db->from('feetype');
            $this->db->order_by('type', 'asc');
            $regular_fee_types = $this->db->get()->result_array();
            
            // Add transport fees
            $regular_fee_types[] = array('id' => 'transport_fees', 'type' => 'Transport Fees');
            
            // Get other fee types
            $this->db->select('id, type');
            $this->db->from('feetypeadding');
            $this->db->order_by('type', 'asc');
            $other_fee_types = $this->db->get()->result_array();
            
            // Combine all fee types
            $combined_fee_types = array_merge($regular_fee_types, $other_fee_types);

            // Get received by list from staff table (collectors)
            // Note: received_by is stored in JSON amount_detail field in both tables
            // So we get the list of staff who can collect fees
            $collect_by_data = $this->studentfeemaster_model->get_feesreceived_by();

            // Convert to array format for API response
            $received_by_list = array();
            if (!empty($collect_by_data)) {
                foreach ($collect_by_data as $staff_id => $staff_name) {
                    $received_by_list[] = array(
                        'id' => $staff_id,
                        'name' => $staff_name
                    );
                }
            }

            $response = array(
                'status' => 1,
                'message' => 'Filter options retrieved successfully',
                'data' => array(
                    'search_types' => $search_types,
                    'group_by' => $group_by,
                    'classes' => $classes,
                    'fee_types' => $combined_fee_types,
                    'received_by' => array_values($received_by_list)
                ),
                'timestamp' => date('Y-m-d H:i:s')
            );

            echo json_encode($response);

        } catch (Exception $e) {
            echo json_encode(array(
                'status' => 0,
                'message' => 'Error retrieving filter options',
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ));
        }
    }

    /**
     * Filter endpoint - Get combined collection report with filters
     * 
     * POST /api/combined-collection-report/filter
     */
    public function filter()
    {
        try {
            // Authenticate request
            if (!$this->auth_model->check_auth_client()) {
                echo json_encode(array(
                    'status' => 0,
                    'message' => 'Unauthorized access',
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Get input
            $input = json_decode(file_get_contents('php://input'), true);
            if ($input === null) {
                $input = array();
            }

            // Extract parameters with graceful null handling
            $search_type = isset($input['search_type']) && $input['search_type'] !== '' ? $input['search_type'] : null;
            $date_from = isset($input['date_from']) && $input['date_from'] !== '' ? $input['date_from'] : null;
            $date_to = isset($input['date_to']) && $input['date_to'] !== '' ? $input['date_to'] : null;
            $class_id = isset($input['class_id']) && $input['class_id'] !== '' ? $input['class_id'] : null;
            $section_id = isset($input['section_id']) && $input['section_id'] !== '' ? $input['section_id'] : null;
            $session_id = isset($input['session_id']) && $input['session_id'] !== '' ? $input['session_id'] : null;
            // Fee type filtering removed - API always returns ALL fee types
            $feetype_id = null;
            $received_by = isset($input['received_by']) && $input['received_by'] !== '' ? $input['received_by'] : null;
            $group = isset($input['group']) && $input['group'] !== '' ? $input['group'] : null;

            // Get date range - use customlib to match web page behavior
            if ($search_type) {
                $dates = $this->customlib->get_betweendate($search_type);
                $start_date = date('Y-m-d', strtotime($dates['from_date']));
                $end_date = date('Y-m-d', strtotime($dates['to_date']));
            } elseif ($date_from && $date_to) {
                $start_date = $date_from;
                $end_date = $date_to;
            } else {
                // Default to current year
                $dates = $this->customlib->get_betweendate('this_year');
                $start_date = date('Y-m-d', strtotime($dates['from_date']));
                $end_date = date('Y-m-d', strtotime($dates['to_date']));
            }

            // Get session ID - use provided value or null for all sessions
            // When session_id is null, the model methods will return data from ALL sessions
            // When session_id is provided, filter by that specific session
            // This allows the API to return all sessions by default

            // Debug: Log parameters before calling model
            log_message('debug', '=== API: Calling model methods ===');
            log_message('debug', 'API: start_date: ' . $start_date);
            log_message('debug', 'API: end_date: ' . $end_date);
            log_message('debug', 'API: feetype_id: ' . var_export($feetype_id, true));
            log_message('debug', 'API: received_by: ' . var_export($received_by, true));
            log_message('debug', 'API: class_id: ' . var_export($class_id, true));
            log_message('debug', 'API: section_id: ' . var_export($section_id, true));
            log_message('debug', 'API: session_id: ' . var_export($session_id, true));

            // Use model methods to get fee collection (handles received_by JSON filtering correctly)
            // Note: Pass null for group parameter to get ungrouped data, we'll group in API if needed
            $regular_fees = $this->studentfeemaster_model->getFeeCollectionReport(
                $start_date, $end_date, $feetype_id, $received_by, null,
                $class_id, $section_id, $session_id
            );

            log_message('debug', 'API: Regular fees count: ' . count($regular_fees));

            $other_fees = $this->studentfeemasteradding_model->getFeeCollectionReport(
                $start_date, $end_date, $feetype_id, $received_by, null,
                $class_id, $section_id, $session_id
            );

            log_message('debug', 'API: Other fees count: ' . count($other_fees));

            // Combine results
            $combined_results = array_merge($regular_fees, $other_fees);

            log_message('debug', 'API: Combined results count: ' . count($combined_results));

            // Calculate total amount and group if needed
            // FIX: Calculate correct total to match web page
            // Total = amount + amount_fine (discount is NOT subtracted, only shown separately)
            // This matches the web page calculation at line 445 and 492 in combined_collection_report.php
            $grouped_results = array();
            $total_amount = 0;
            $total_discount = 0;
            $total_fine = 0;

            if ($group && !empty($combined_results)) {
                $group_by_field = $this->get_group_field($group);
                foreach ($combined_results as $row) {
                    $key = isset($row[$group_by_field]) ? $row[$group_by_field] : 'Unknown';
                    if (!isset($grouped_results[$key])) {
                        $grouped_results[$key] = array(
                            'group_name' => $key,
                            'records' => array(),
                            'subtotal_amount' => 0,
                            'subtotal_discount' => 0,
                            'subtotal_fine' => 0,
                            'subtotal_total' => 0
                        );
                    }
                    $grouped_results[$key]['records'][] = $row;

                    // Calculate amounts correctly - matches web page formula
                    $amount = isset($row['amount']) ? floatval($row['amount']) : 0;
                    $discount = isset($row['amount_discount']) ? floatval($row['amount_discount']) : 0;
                    $fine = isset($row['amount_fine']) ? floatval($row['amount_fine']) : 0;
                    $record_total = $amount + $fine; // Discount NOT subtracted

                    $grouped_results[$key]['subtotal_amount'] += $amount;
                    $grouped_results[$key]['subtotal_discount'] += $discount;
                    $grouped_results[$key]['subtotal_fine'] += $fine;
                    $grouped_results[$key]['subtotal_total'] += $record_total;

                    $total_amount += $amount;
                    $total_discount += $discount;
                    $total_fine += $fine;
                }
                $grouped_results = array_values($grouped_results);
            } else {
                foreach ($combined_results as $row) {
                    $amount = isset($row['amount']) ? floatval($row['amount']) : 0;
                    $discount = isset($row['amount_discount']) ? floatval($row['amount_discount']) : 0;
                    $fine = isset($row['amount_fine']) ? floatval($row['amount_fine']) : 0;

                    $total_amount += $amount;
                    $total_discount += $discount;
                    $total_fine += $fine;
                }
            }

            // Calculate grand total: amount + fine (discount NOT subtracted)
            // This matches the web page calculation
            $grand_total = $total_amount + $total_fine;

            $response = array(
                'status' => 1,
                'message' => 'Combined collection report retrieved successfully',
                'filters_applied' => array(
                    'search_type' => $search_type,
                    'date_from' => $start_date,
                    'date_to' => $end_date,
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'session_id' => $session_id,
                    'received_by' => $received_by,
                    'group' => $group
                ),
                'summary' => array(
                    'total_records' => count($combined_results),
                    'total_amount' => number_format($total_amount, 2, '.', ''),
                    'total_discount' => number_format($total_discount, 2, '.', ''),
                    'total_fine' => number_format($total_fine, 2, '.', ''),
                    'grand_total' => number_format($grand_total, 2, '.', ''),
                    'regular_fees_count' => count($regular_fees),
                    'other_fees_count' => count($other_fees)
                ),
                'total_records' => count($combined_results),
                'data' => $group ? $grouped_results : $combined_results,
                'timestamp' => date('Y-m-d H:i:s')
            );

            echo json_encode($response);

        } catch (Exception $e) {
            echo json_encode(array(
                'status' => 0,
                'message' => 'Error retrieving combined collection report',
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ));
        }
    }





    private function get_group_field($group)
    {
        switch ($group) {
            case 'class': return 'class_id';
            case 'collection': return 'received_by';
            case 'mode': return 'payment_mode';
            default: return 'class_id';
        }
    }
}

