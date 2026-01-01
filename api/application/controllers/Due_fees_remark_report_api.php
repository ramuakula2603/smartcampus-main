<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Due Fees Remark Report API Controller
 * 
 * Provides API endpoints for balance fees report with remark
 * Shows students with due fees by class and section
 * 
 * @package    Smart School
 * @subpackage API
 * @category   Finance Reports
 * @author     Smart School Team
 */
class Due_fees_remark_report_api extends CI_Controller
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
            $this->load->model('class_model');
            $this->load->model('section_model');

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
     * Filter endpoint - Get due fees remark report with filters
     *
     * POST /api/due-fees-remark-report/filter
     *
     * Request Body (all optional):
     * {
     *   "class_id": "1",           // Optional - if not provided, returns all classes
     *   "section_id": "2",         // Optional - if not provided, returns all sections
     *   "session_id": "25"         // Optional - if not provided, uses current session
     * }
     *
     * Empty request {} returns all due fees records for current session
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
            $class_id = (isset($input['class_id']) && $input['class_id'] !== '') ? $input['class_id'] : null;
            $section_id = (isset($input['section_id']) && $input['section_id'] !== '') ? $input['section_id'] : null;
            $session_id = (isset($input['session_id']) && $input['session_id'] !== '') ? $input['session_id'] : null;

            // Get session ID - use provided session_id or current session
            if ($session_id === null) {
                $session_id = $this->setting_model->getCurrentSession();
            }

            // Get current date
            $date = date('Y-m-d');

            // Build WHERE conditions for the query
            $where_conditions = array();

            // Always filter by session
            $where_conditions[] = "student_session.session_id = " . $this->db->escape($session_id);

            // Add class filter if provided
            if ($class_id != null) {
                $where_conditions[] = "student_session.class_id = " . $this->db->escape($class_id);
            }

            // Add section filter if provided
            if ($section_id != null) {
                $where_conditions[] = "student_session.section_id = " . $this->db->escape($section_id);
            }

            // Add due date filter
            if ($date != null) {
                $where_conditions[] = "fee_groups_feetype.due_date < " . $this->db->escape($date);
            }

            $where_string = !empty($where_conditions) ? " AND " . implode(" AND ", $where_conditions) : "";

            $query = "SELECT student_fees_master.amount as `previous_balance_amount`,
                IFNULL(student_fees_deposite.id, 0) as student_fees_deposite_id,
                IFNULL(student_fees_deposite.fee_groups_feetype_id, 0) as fee_groups_feetype_id,
                IFNULL(student_fees_deposite.amount_detail, 0) as amount_detail,
                student_fees_master.id as `fee_master_id`,
                fee_groups_feetype.feetype_id,
                fee_groups_feetype.amount,
                fee_groups_feetype.due_date,
                classes.id AS `class_id`,
                student_session.id as `student_session_id`,
                students.id as student_id,
                classes.class,
                sections.id AS `section_id`,
                sections.section,
                students.admission_no,
                students.firstname,
                students.middlename,
                students.lastname,
                students.guardian_phone,
                fee_groups.name as `name`,
                feetype.type as `type`,
                feetype.code as `code`,
                fee_groups.is_system
                FROM `student_fees_master`
                INNER JOIN fee_session_groups on fee_session_groups.id = student_fees_master.fee_session_group_id
                INNER JOIN fee_groups on fee_groups.id = fee_session_groups.fee_groups_id
                INNER JOIN fee_groups_feetype on fee_groups_feetype.fee_session_group_id = student_fees_master.fee_session_group_id
                INNER JOIN feetype on feetype.id = fee_groups_feetype.feetype_id
                LEFT JOIN student_fees_deposite on student_fees_deposite.student_fees_master_id = student_fees_master.id
                    and student_fees_deposite.fee_groups_feetype_id = fee_groups_feetype.id
                INNER JOIN student_session on student_session.id = student_fees_master.student_session_id
                INNER JOIN students on students.id = student_session.student_id
                JOIN `classes` ON `student_session`.`class_id` = `classes`.`id`
                JOIN `sections` ON `sections`.`id` = `student_session`.`section_id`
                WHERE `students`.`is_active` = 'yes' " . $where_string . "
                ORDER BY student_fees_master.id asc";

            $result = $this->db->query($query);
            $student_due_fee = $result->result_array();

            $students = array();
            $total_amount = 0;
            $total_paid = 0;
            $total_balance = 0;

            if (!empty($student_due_fee)) {
                foreach ($student_due_fee as $student_due_fee_value) {
                    $amt_due = ($student_due_fee_value['is_system']) ? $student_due_fee_value['previous_balance_amount'] : $student_due_fee_value['amount'];

                    $a = json_decode($student_due_fee_value['amount_detail']);
                    $amount = 0;
                    $amount_discount = 0;
                    $amount_fine = 0;

                    if (!empty($a)) {
                        foreach ($a as $a_key => $a_value) {
                            $amount += $a_value->amount;
                            $amount_discount += $a_value->amount_discount;
                            $amount_fine += $a_value->amount_fine;
                        }
                    }

                    $student_id = $student_due_fee_value['student_id'];

                    if (!isset($students[$student_id])) {
                        $students[$student_id] = array(
                            'student_id' => $student_id,
                            'admission_no' => $student_due_fee_value['admission_no'],
                            'firstname' => $student_due_fee_value['firstname'],
                            'middlename' => isset($student_due_fee_value['middlename']) ? $student_due_fee_value['middlename'] : '',
                            'lastname' => isset($student_due_fee_value['lastname']) ? $student_due_fee_value['lastname'] : '',
                            'class' => $student_due_fee_value['class'],
                            'section' => $student_due_fee_value['section'],
                            'guardian_phone' => isset($student_due_fee_value['guardian_phone']) ? $student_due_fee_value['guardian_phone'] : '',
                            'remark' => isset($student_due_fee_value['remark']) ? $student_due_fee_value['remark'] : '',
                            'fees' => array(),
                            'total_amount' => 0,
                            'total_paid' => 0,
                            'total_balance' => 0
                        );
                    }

                    // Add fee details
                    $fee_amount = floatval($amt_due);
                    $fee_paid = floatval($amount);
                    $fee_balance = $fee_amount - $fee_paid;

                    $students[$student_id]['fees'][] = array(
                        'fee_group' => $student_due_fee_value['name'],
                        'fee_type' => $student_due_fee_value['type'],
                        'due_date' => $student_due_fee_value['due_date'],
                        'amount' => number_format($fee_amount, 2, '.', ''),
                        'paid' => number_format($fee_paid, 2, '.', ''),
                        'balance' => number_format($fee_balance, 2, '.', '')
                    );

                    $students[$student_id]['total_amount'] += $fee_amount;
                    $students[$student_id]['total_paid'] += $fee_paid;
                    $students[$student_id]['total_balance'] += $fee_balance;

                    $total_amount += $fee_amount;
                    $total_paid += $fee_paid;
                    $total_balance += $fee_balance;
                }

                // Format totals for each student
                foreach ($students as &$student) {
                    $student['total_amount'] = number_format($student['total_amount'], 2, '.', '');
                    $student['total_paid'] = number_format($student['total_paid'], 2, '.', '');
                    $student['total_balance'] = number_format($student['total_balance'], 2, '.', '');
                }
            }

            // Prepare response
            $response = array(
                'status' => 1,
                'message' => 'Due fees remark report retrieved successfully',
                'filters_applied' => array(
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'session_id' => $session_id,
                    'date' => $date
                ),
                'summary' => array(
                    'total_students' => count($students),
                    'total_amount' => number_format($total_amount, 2, '.', ''),
                    'total_paid' => number_format($total_paid, 2, '.', ''),
                    'total_balance' => number_format($total_balance, 2, '.', '')
                ),
                'total_records' => count($students),
                'data' => array_values($students),
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
     * POST /api/due-fees-remark-report/list
     * 
     * Returns available classes and sections for filtering
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

            // Get all classes
            $classes = $this->class_model->get();

            // Prepare response
            $response = array(
                'status' => 1,
                'message' => 'Filter options retrieved successfully',
                'data' => array(
                    'classes' => $classes
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

}

