<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Online Fees Report API Controller
 * 
 * Provides API endpoints for online fee collection reports
 * Shows online fee payments collected through payment gateways
 * 
 * @package    Smart School
 * @subpackage API
 * @category   Finance Reports
 * @author     Smart School Team
 */
class Online_fees_report_api extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        // Suppress errors for clean JSON output (ignore deprecation warnings)
        ini_set('display_errors', 0);
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

        // Set JSON response header
        header('Content-Type: application/json');

        // Try to load database with error handling
        try {
            $this->load->database();

            // Test database connection
            if (!$this->db->conn_id) {
                throw new Exception('Database connection failed');
            }

            // Load required models in correct order
            $this->load->model('setting_model');
            $this->load->model('auth_model');
            $this->load->model('module_model');

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
     * Filter endpoint - Get online fees report with filters
     * 
     * POST /api/online-fees-report/filter
     * 
     * Request Body (all optional):
     * {
     *   "search_type": "today|this_week|this_month|last_month|this_year|period",
     *   "date_from": "2025-01-01",
     *   "date_to": "2025-12-31"
     * }
     * 
     * Empty request {} returns all online fees for current year
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
            $search_type = (isset($input['search_type']) && $input['search_type'] !== '') ? $input['search_type'] : null;
            $date_from = (isset($input['date_from']) && $input['date_from'] !== '') ? $input['date_from'] : null;
            $date_to = (isset($input['date_to']) && $input['date_to'] !== '') ? $input['date_to'] : null;

            // Determine date range
            if ($search_type !== null) {
                // Use search_type to determine dates
                $dates = $this->getDateRangeBySearchType($search_type);
                $start_date = $dates['from_date'];
                $end_date = $dates['to_date'];
                $date_label = $dates['label'];
            } elseif ($date_from !== null && $date_to !== null) {
                // Use custom date range
                $start_date = $date_from;
                $end_date = $date_to;
                $date_label = date('d/m/Y', strtotime($start_date)) . ' to ' . date('d/m/Y', strtotime($end_date));
            } else {
                // Default to current year
                $dates = $this->getDateRangeBySearchType('this_year');
                $start_date = $dates['from_date'];
                $end_date = $dates['to_date'];
                $date_label = $dates['label'];
            }

            // Get online fee collection data using direct database query
            // Get current session
            $current_session = $this->setting_model->getCurrentSession();

            $this->db->select('student_fees_deposite.*, students.firstname, students.middlename, students.lastname,
                student_session.class_id, classes.class, sections.section, student_session.section_id,
                student_session.student_id, fee_groups.name, feetype.type, feetype.code, feetype.is_system,
                student_fees_master.student_session_id, students.admission_no');
            $this->db->from('student_fees_deposite');
            $this->db->join('fee_groups_feetype', 'fee_groups_feetype.id = student_fees_deposite.fee_groups_feetype_id');
            $this->db->join('fee_groups', 'fee_groups.id = fee_groups_feetype.fee_groups_id');
            $this->db->join('feetype', 'feetype.id = fee_groups_feetype.feetype_id');
            $this->db->join('student_fees_master', 'student_fees_master.id = student_fees_deposite.student_fees_master_id');
            $this->db->join('student_session', 'student_session.id = student_fees_master.student_session_id');
            $this->db->join('classes', 'classes.id = student_session.class_id');
            $this->db->join('sections', 'sections.id = student_session.section_id');
            $this->db->join('students', 'students.id = student_session.student_id');
            $this->db->where('student_session.session_id', $current_session);
            $this->db->order_by('student_fees_deposite.id');

            $query = $this->db->get();
            $collectlist = $query->result();

            // Process and format data
            $total_amount = 0;
            $total_records = 0;
            $online_fees = array();

            if (!empty($collectlist)) {
                foreach ($collectlist as $collection) {
                    // Parse amount_detail JSON
                    $amount_detail = json_decode($collection->amount_detail);
                    $amount = 0;
                    $payment_date = '';
                    $payment_mode = '';

                    if (!empty($amount_detail)) {
                        foreach ($amount_detail as $detail) {
                            if (isset($detail->amount)) {
                                $amount += floatval($detail->amount);
                            }
                            if (isset($detail->date) && empty($payment_date)) {
                                $payment_date = $detail->date;
                            }
                            if (isset($detail->payment_mode) && empty($payment_mode)) {
                                $payment_mode = $detail->payment_mode;
                            }
                        }
                    }

                    $total_amount += $amount;
                    $total_records++;

                    // Build student name
                    $student_name = $collection->firstname;
                    if (!empty($collection->middlename)) {
                        $student_name .= ' ' . $collection->middlename;
                    }
                    if (!empty($collection->lastname)) {
                        $student_name .= ' ' . $collection->lastname;
                    }

                    $online_fees[] = array(
                        'id' => $collection->id,
                        'student_id' => $collection->student_id,
                        'admission_no' => $collection->admission_no,
                        'student_name' => $student_name,
                        'class' => $collection->class,
                        'section' => $collection->section,
                        'fee_group' => $collection->name,
                        'fee_type' => $collection->type,
                        'fee_code' => isset($collection->code) ? $collection->code : '',
                        'amount' => number_format($amount, 2, '.', ''),
                        'payment_date' => $payment_date,
                        'payment_mode' => $payment_mode
                    );
                }
            }

            // Prepare response
            $response = array(
                'status' => 1,
                'message' => 'Online fees report retrieved successfully',
                'filters_applied' => array(
                    'search_type' => $search_type,
                    'date_from' => $start_date,
                    'date_to' => $end_date
                ),
                'date_range' => array(
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'label' => $date_label
                ),
                'summary' => array(
                    'total_records' => $total_records,
                    'total_amount' => number_format($total_amount, 2, '.', '')
                ),
                'total_records' => $total_records,
                'data' => $online_fees,
                'timestamp' => date('Y-m-d H:i:s')
            );

            echo json_encode($response);

        } catch (Exception $e) {
            echo json_encode(array(
                'status' => 0,
                'message' => 'An error occurred while processing the request',
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ));
        }
    }

    /**
     * List endpoint - Get filter options
     * 
     * POST /api/online-fees-report/list
     * 
     * Returns available search types for filtering
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

            // Prepare response
            $response = array(
                'status' => 1,
                'message' => 'Filter options retrieved successfully',
                'data' => array(
                    'search_types' => array(
                        array('key' => 'today', 'label' => 'Today'),
                        array('key' => 'this_week', 'label' => 'This Week'),
                        array('key' => 'this_month', 'label' => 'This Month'),
                        array('key' => 'last_month', 'label' => 'Last Month'),
                        array('key' => 'this_year', 'label' => 'This Year'),
                        array('key' => 'period', 'label' => 'Custom Period')
                    )
                ),
                'timestamp' => date('Y-m-d H:i:s')
            );

            echo json_encode($response);

        } catch (Exception $e) {
            echo json_encode(array(
                'status' => 0,
                'message' => 'An error occurred while processing the request',
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ));
        }
    }

    /**
     * Private helper method to get date range by search type
     */
    private function getDateRangeBySearchType($search_type)
    {
        $today = date('Y-m-d');
        
        switch ($search_type) {
            case 'today':
                return array(
                    'from_date' => $today,
                    'to_date' => $today,
                    'label' => date('d/m/Y')
                );
            
            case 'this_week':
                $start_of_week = date('Y-m-d', strtotime('monday this week'));
                $end_of_week = date('Y-m-d', strtotime('sunday this week'));
                return array(
                    'from_date' => $start_of_week,
                    'to_date' => $end_of_week,
                    'label' => date('d/m/Y', strtotime($start_of_week)) . ' to ' . date('d/m/Y', strtotime($end_of_week))
                );
            
            case 'this_month':
                $start_of_month = date('Y-m-01');
                $end_of_month = date('Y-m-t');
                return array(
                    'from_date' => $start_of_month,
                    'to_date' => $end_of_month,
                    'label' => date('F Y')
                );
            
            case 'last_month':
                $start_of_last_month = date('Y-m-01', strtotime('first day of last month'));
                $end_of_last_month = date('Y-m-t', strtotime('last day of last month'));
                return array(
                    'from_date' => $start_of_last_month,
                    'to_date' => $end_of_last_month,
                    'label' => date('F Y', strtotime($start_of_last_month))
                );
            
            case 'this_year':
            default:
                $start_of_year = date('Y-01-01');
                $end_of_year = date('Y-12-31');
                return array(
                    'from_date' => $start_of_year,
                    'to_date' => $end_of_year,
                    'label' => date('01/01/Y') . ' to ' . date('31/12/Y')
                );
        }
    }

}

