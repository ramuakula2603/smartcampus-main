<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Fee Collection Columnwise Report API Controller
 * 
 * Provides API endpoints for fee collection reports in column-wise format by fee type
 * 
 * @package    School Management System API
 * @subpackage Controllers
 * @category   Finance Reports
 * @author     SMS Development Team
 * @version    1.0
 */
class Fee_collection_columnwise_report_api extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        
        // Suppress errors for clean JSON output
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
            
            // Load required models
            $this->load->model('setting_model');
            $this->load->model('auth_model');
            $this->load->model('module_model');
            $this->load->model('class_model');
            $this->load->model('section_model');
            $this->load->model('studentfeemaster_model');
            
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
     * POST /api/fee-collection-columnwise-report/list
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
     * Filter endpoint - Get fee collection report in columnwise format
     * 
     * POST /api/fee-collection-columnwise-report/filter
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
            $feetype_id = isset($input['feetype_id']) && $input['feetype_id'] !== '' ? $input['feetype_id'] : null;
            $received_by = isset($input['received_by']) && $input['received_by'] !== '' ? $input['received_by'] : null;

            // Get date range
            if ($search_type) {
                $dates = $this->get_date_range($search_type);
                $start_date = $dates['from_date'];
                $end_date = $dates['to_date'];
            } elseif ($date_from && $date_to) {
                $start_date = $date_from;
                $end_date = $date_to;
            } else {
                // Default to current year
                $dates = $this->get_date_range('this_year');
                $start_date = $dates['from_date'];
                $end_date = $dates['to_date'];
            }

            // Get session ID - use provided or current
            if ($session_id === null) {
                $session_id = $this->setting_model->getCurrentSession();
            }

            // Get regular fee collection
            $regular_fees = $this->get_regular_fees($start_date, $end_date, $session_id, $class_id, $section_id, $feetype_id, $received_by);
            
            // Get other fee collection
            $other_fees = $this->get_other_fees($start_date, $end_date, $session_id, $class_id, $section_id, $feetype_id, $received_by);
            
            // Combine results
            $combined_results = array_merge($regular_fees, $other_fees);

            // Get unique fee types from results
            $fee_types = array();
            $fee_type_names = array();
            foreach ($combined_results as $row) {
                if (!in_array($row['type'], $fee_type_names)) {
                    $fee_type_names[] = $row['type'];
                    $fee_types[] = array(
                        'type' => $row['type'],
                        'code' => isset($row['code']) ? $row['code'] : ''
                    );
                }
            }

            // Organize data by student with fee types as columns
            $student_data = array();
            $total_amount = 0;
            $fee_type_totals = array();

            foreach ($combined_results as $row) {
                // Decode amount_detail JSON to get actual payment amounts
                $amount_detail = isset($row['amount_detail']) ? json_decode($row['amount_detail']) : null;

                if (empty($amount_detail)) {
                    continue; // Skip if no payment details
                }

                // Process each payment in amount_detail
                foreach ($amount_detail as $payment) {
                    // Check if payment is within date range
                    $payment_date = strtotime($payment->date);
                    $start_timestamp = strtotime($start_date);
                    $end_timestamp = strtotime($end_date);

                    if ($payment_date < $start_timestamp || $payment_date > $end_timestamp) {
                        continue; // Skip payments outside date range
                    }

                    // Check received_by filter if specified
                    if ($received_by !== null && isset($payment->received_by) && $payment->received_by != $received_by) {
                        continue; // Skip if doesn't match received_by filter
                    }

                    $student_key = $row['student_id'] . '_' . $row['admission_no'];

                    if (!isset($student_data[$student_key])) {
                        $student_data[$student_key] = array(
                            'student_id' => $row['student_id'],
                            'admission_no' => $row['admission_no'],
                            'student_name' => trim($row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname']),
                            'class' => $row['class'],
                            'section' => $row['section'],
                            'fee_payments' => array(),
                            'total' => 0
                        );
                    }

                    $fee_type = $row['type'];
                    if (!isset($student_data[$student_key]['fee_payments'][$fee_type])) {
                        $student_data[$student_key]['fee_payments'][$fee_type] = 0;
                    }

                    $amount = floatval($payment->amount);
                    $student_data[$student_key]['fee_payments'][$fee_type] += $amount;
                    $student_data[$student_key]['total'] += $amount;
                    $total_amount += $amount;

                    if (!isset($fee_type_totals[$fee_type])) {
                        $fee_type_totals[$fee_type] = 0;
                    }
                    $fee_type_totals[$fee_type] += $amount;
                }
            }

            $response = array(
                'status' => 1,
                'message' => 'Fee collection columnwise report retrieved successfully',
                'filters_applied' => array(
                    'search_type' => $search_type,
                    'date_from' => $start_date,
                    'date_to' => $end_date,
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'session_id' => $session_id,
                    'feetype_id' => $feetype_id,
                    'received_by' => $received_by
                ),
                'summary' => array(
                    'total_students' => count($student_data),
                    'total_records' => count($combined_results),
                    'total_amount' => number_format($total_amount, 2, '.', ''),
                    'fee_type_totals' => $fee_type_totals
                ),
                'fee_types' => $fee_types,
                'total_records' => count($student_data),
                'data' => array_values($student_data),
                'timestamp' => date('Y-m-d H:i:s')
            );

            echo json_encode($response);

        } catch (Exception $e) {
            echo json_encode(array(
                'status' => 0,
                'message' => 'Error retrieving fee collection columnwise report',
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ));
        }
    }

    private function get_regular_fees($start_date, $end_date, $session_id, $class_id, $section_id, $feetype_id, $received_by)
    {
        $this->db->select('student_fees_deposite.*, students.firstname, students.middlename, students.lastname,
            student_session.class_id, classes.class, sections.section, student_session.section_id,
            student_session.student_id, fee_groups.name, feetype.type, feetype.code,
            feetype.is_system, student_fees_master.student_session_id, students.admission_no');
        $this->db->from('student_fees_deposite');
        $this->db->join('fee_groups_feetype', 'fee_groups_feetype.id = student_fees_deposite.fee_groups_feetype_id');
        $this->db->join('fee_groups', 'fee_groups.id = fee_groups_feetype.fee_groups_id');
        $this->db->join('feetype', 'feetype.id = fee_groups_feetype.feetype_id');
        $this->db->join('student_fees_master', 'student_fees_master.id = student_fees_deposite.student_fees_master_id');
        $this->db->join('student_session', 'student_session.id = student_fees_master.student_session_id');
        $this->db->join('classes', 'classes.id = student_session.class_id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('students', 'students.id = student_session.student_id');

        // Don't filter by created_at here - we'll filter by date in amount_detail JSON
        $this->db->where('student_session.session_id', $session_id);

        if ($class_id !== null) $this->db->where('student_session.class_id', $class_id);
        if ($section_id !== null) $this->db->where('student_session.section_id', $section_id);
        if ($feetype_id !== null && $feetype_id !== 'transport_fees') $this->db->where('feetype.id', $feetype_id);
        // Don't filter by received_by here - we'll filter in amount_detail JSON

        $this->db->order_by('students.admission_no', 'asc');

        return $this->db->get()->result_array();
    }

    private function get_other_fees($start_date, $end_date, $session_id, $class_id, $section_id, $feetype_id, $received_by)
    {
        $this->db->select('student_fees_depositeadding.*, students.firstname, students.middlename, students.lastname,
            student_session.class_id, classes.class, sections.section, student_session.section_id,
            student_session.student_id, fee_groupsadding.name, feetypeadding.type, feetypeadding.code,
            feetypeadding.is_system, student_fees_masteradding.student_session_id, students.admission_no');
        $this->db->from('student_fees_depositeadding');
        $this->db->join('fee_groups_feetypeadding', 'fee_groups_feetypeadding.id = student_fees_depositeadding.fee_groups_feetype_id');
        $this->db->join('fee_groupsadding', 'fee_groupsadding.id = fee_groups_feetypeadding.fee_groups_id');
        $this->db->join('feetypeadding', 'feetypeadding.id = fee_groups_feetypeadding.feetype_id');
        $this->db->join('student_fees_masteradding', 'student_fees_masteradding.id = student_fees_depositeadding.student_fees_master_id');
        $this->db->join('student_session', 'student_session.id = student_fees_masteradding.student_session_id');
        $this->db->join('classes', 'classes.id = student_session.class_id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('students', 'students.id = student_session.student_id');

        // Don't filter by created_at here - we'll filter by date in amount_detail JSON
        $this->db->where('student_session.session_id', $session_id);

        if ($class_id !== null) $this->db->where('student_session.class_id', $class_id);
        if ($section_id !== null) $this->db->where('student_session.section_id', $section_id);
        if ($feetype_id !== null) $this->db->where('feetypeadding.id', $feetype_id);
        // Don't filter by received_by here - we'll filter in amount_detail JSON

        $this->db->order_by('students.admission_no', 'asc');

        return $this->db->get()->result_array();
    }

    private function get_date_range($search_type)
    {
        $today = date('Y-m-d');
        switch ($search_type) {
            case 'today': return array('from_date' => $today, 'to_date' => $today);
            case 'this_week': return array('from_date' => date('Y-m-d', strtotime('monday this week')), 'to_date' => $today);
            case 'this_month': return array('from_date' => date('Y-m-01'), 'to_date' => $today);
            case 'last_month': return array('from_date' => date('Y-m-01', strtotime('last month')), 'to_date' => date('Y-m-t', strtotime('last month')));
            case 'this_year':
            default: return array('from_date' => date('Y-01-01'), 'to_date' => date('Y-12-31'));
        }
    }
}

