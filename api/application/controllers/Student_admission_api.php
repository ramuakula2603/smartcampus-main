<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Student Admission API Controller
 * 
 * This controller provides RESTful API endpoints for student admission management.
 * It mirrors the functionality of the web-based student admission form.
 * 
 * @package    Student Management System
 * @subpackage API Controllers
 * @category   Webservices
 * @author     School Management System
 * @version    1.0.0
 */
class Student_admission_api extends CI_Controller
{
    /**
     * Constructor
     * 
     * Initializes the controller, loads required models, libraries, and helpers.
     * Sets up error handling and timezone configuration.
     */
    public function __construct()
    {
        parent::__construct();

        // Start output buffering if not already started
        if (!ob_get_level()) {
            ob_start();
        }

        // Set JSON content type early
        $this->output->set_content_type('application/json');

        // Load essential helpers
        $this->load->helper('json_output');

        // Load required models
        try {
            $this->load->model(array(
                'student_model',
                'setting_model',
                'class_model',
                'section_model',
                'category_model',
                'hostel_model',
                'vehroute_model',
                'customfield_model',
                'feesessiongroup_model',
                'transportfee_model',
                'studentfeemaster_model',
                'studenttransportfee_model',
                'user_model',
                'role_model',
                'staff_model',
                'filetype_model'
            ));
        } catch (Exception $e) {
            log_message('error', 'Error loading models: ' . $e->getMessage());
        }

        // Load libraries
        try {
            $this->load->library(array('customlib', 'media_storage', 'mailsmsconf'));
        } catch (Exception $e) {
            log_message('error', 'Error loading libraries: ' . $e->getMessage());
        }

        // Load form validation library
        $this->load->library('form_validation');

        // Get school settings
        try {
            $this->sch_setting_detail = $this->setting_model->getSetting();
            
            // Set timezone
            if (isset($this->sch_setting_detail->timezone) && $this->sch_setting_detail->timezone != "") {
                date_default_timezone_set($this->sch_setting_detail->timezone);
            } else {
                date_default_timezone_set('UTC');
            }
        } catch (Exception $e) {
            log_message('error', 'Error loading school settings: ' . $e->getMessage());
            date_default_timezone_set('UTC');
        }

        // Define blood group options (same as web controller)
        $this->blood_group = array(
            'O+' => 'O+',
            'A+' => 'A+',
            'B+' => 'B+',
            'AB+' => 'AB+',
            'O-' => 'O-',
            'A-' => 'A-',
            'B-' => 'B-',
            'AB-' => 'AB-',
        );

        // Define login prefixes
        $this->student_login_prefix = 'std';
        $this->parent_login_prefix = 'par';

        // Set custom error handling for JSON responses
        set_error_handler(array($this, 'custom_error_handler'));
        set_exception_handler(array($this, 'custom_exception_handler'));
    }

    /**
     * Custom error handler for JSON responses
     * 
     * @param int $severity Error severity level
     * @param string $message Error message
     * @param string $file File where error occurred
     * @param int $line Line number where error occurred
     * @return bool
     */
    public function custom_error_handler($severity, $message, $file, $line)
    {
        if (!(error_reporting() & $severity)) {
            return false;
        }

        $error_response = array(
            'status' => 0,
            'message' => 'PHP Error occurred',
            'error' => array(
                'type' => 'PHP Error',
                'severity' => $severity,
                'message' => $message,
                'file' => basename($file),
                'line' => $line
            ),
            'timestamp' => date('Y-m-d H:i:s')
        );

        // Log the error
        log_message('error', "PHP Error: $message in $file on line $line");

        // Only send JSON error for database or critical errors
        if (stripos($message, 'database') !== false || 
            stripos($message, 'fatal') !== false ||
            stripos($message, 'call to') !== false) {
            
            if (ob_get_level()) ob_clean();
            header('Content-Type: application/json');
            echo json_encode($error_response);
            exit;
        }

        return false;
    }

    /**
     * Custom exception handler for JSON responses
     * 
     * @param Exception $exception The exception object
     */
    public function custom_exception_handler($exception)
    {
        $error_response = array(
            'status' => 0,
            'message' => 'Exception occurred',
            'error' => array(
                'type' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => basename($exception->getFile()),
                'line' => $exception->getLine()
            ),
            'timestamp' => date('Y-m-d H:i:s')
        );

        // Log the exception
        log_message('error', "Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine());

        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode($error_response);
        exit;
    }

    /**
     * Create Student Admission API Endpoint
     * 
     * POST /student_admission_api/create
     * 
     * This endpoint creates a new student admission with all related data.
     * It replicates the exact functionality of the web-based student admission form.
     * 
     * Request Body (JSON):
     * {
     *   "firstname": "John",
     *   "lastname": "Doe",
     *   "gender": "Male",
     *   "dob": "2010-01-15",
     *   "class_id": 1,
     *   "section_id": 1,
     *   "guardian_name": "Jane Doe",
     *   "guardian_phone": "1234567890",
     *   "guardian_email": "jane@example.com",
     *   "email": "john@example.com",
     *   "mobileno": "9876543210",
     *   "reference_id": 1,
     *   ... (other optional fields)
     * }
     * 
     * Response (JSON):
     * {
     *   "status": 1,
     *   "message": "Student admission created successfully",
     *   "data": {
     *     "student_id": 123,
     *     "admission_no": "ADM001",
     *     "student_session_id": 456,
     *     "username": "std123",
     *     "password": "abc123",
     *     "parent_username": "par123",
     *     "parent_password": "xyz789"
     *   }
     * }
     * 
     * @return void Outputs JSON response
     */
    public function create()
    {
        try {
            $method = $this->input->server('REQUEST_METHOD');

            if ($method != 'POST') {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Bad request. Only POST method allowed.',
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Get JSON input
            $json_input = json_decode($this->input->raw_input_stream, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                json_output(400, array(
                    'status' => 0,
                    'message' => 'Invalid JSON format in request body',
                    'error' => json_last_error_msg(),
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Check database connection
            if (!$this->db->conn_id) {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Database connection failed',
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Set POST data for form validation
            $_POST = $json_input;

            // Perform validation and student creation
            $result = $this->process_student_admission($json_input);

            if ($result['status'] == 1) {
                json_output(201, $result);
            } else {
                json_output(400, $result);
            }

        } catch (Exception $e) {
            $error_response = array(
                'status' => 0,
                'message' => 'Exception occurred while creating student admission',
                'error' => array(
                    'type' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine()
                ),
                'timestamp' => date('Y-m-d H:i:s')
            );
            
            log_message('error', 'Student Admission Exception: ' . $e->getMessage());
            json_output(500, $error_response);
        }
    }

    /**
     * Process Student Admission
     *
     * Validates input data and creates student record with all related data.
     * This method replicates the exact logic from Student::create() controller.
     *
     * @param array $input_data Input data from API request
     * @return array Response array with status and data
     */
    private function process_student_admission($input_data)
    {
        // Set up validation rules (same as web controller)
        $this->setup_validation_rules($input_data);

        // Run validation
        if ($this->form_validation->run() == false) {
            // Validation failed - return errors
            $errors = array();
            foreach ($input_data as $key => $value) {
                $error = form_error($key);
                if ($error) {
                    $errors[$key] = strip_tags($error);
                }
            }

            return array(
                'status' => 0,
                'message' => 'Validation failed',
                'errors' => $errors,
                'timestamp' => date('Y-m-d H:i:s')
            );
        }

        // Validation passed - proceed with student creation
        return $this->create_student_admission_record($input_data);
    }

    /**
     * Setup Validation Rules
     *
     * Sets up all validation rules exactly as in the web controller.
     *
     * @param array $input_data Input data
     * @return void
     */
    private function setup_validation_rules($input_data)
    {
        // Required fields
        $this->form_validation->set_rules('firstname', 'First Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('gender', 'Gender', 'trim|required|xss_clean');
        $this->form_validation->set_rules('dob', 'Date of Birth', 'trim|required|xss_clean');
        $this->form_validation->set_rules('class_id', 'Class', 'trim|required|xss_clean');
        $this->form_validation->set_rules('section_id', 'Section', 'trim|required|xss_clean');
        $this->form_validation->set_rules('reference_id', 'Reference Staff', 'trim|required|xss_clean');

        // Guardian fields (conditional based on school settings)
        if ($this->sch_setting_detail->guardian_name) {
            $this->form_validation->set_rules('guardian_name', 'Guardian Name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('guardian_is', 'Guardian', 'trim|required|xss_clean');
        }

        if ($this->sch_setting_detail->guardian_phone) {
            $this->form_validation->set_rules('guardian_phone', 'Guardian Phone', 'trim|required|xss_clean');
        }

        // Email validation with uniqueness check
        $this->form_validation->set_rules(
            'email', 'Email', array(
                'valid_email',
                array('check_student_email_exists', array($this, 'check_student_email_exists')),
            )
        );

        // Admission number validation
        $this->form_validation->set_rules(
            'admi_no', 'Admission Number', array(
                'xss_clean',
                array('check_student_admi_no_exists', array($this, 'check_student_admi_no_exists')),
            )
        );

        // Mobile number validation (conditional based on school settings)
        if ($this->sch_setting_detail->mobile_no) {
            $this->form_validation->set_rules(
                'mobileno',
                'Mobile Number',
                'trim|required|exact_length[10]|regex_match[/^[6-9]\d{9}$/]|xss_clean'
            );
        }

        // Last name validation (conditional based on school settings)
        if ($this->sch_setting_detail->lastname) {
            $this->form_validation->set_rules('lastname', 'Last Name', 'trim|required|xss_clean');
        }

        // Guardian email validation (conditional - not required if sibling exists)
        $sibling_id = isset($input_data['sibling_id']) ? $input_data['sibling_id'] : 0;
        if ($sibling_id <= 0) {
            $this->form_validation->set_rules(
                'guardian_email', 'Guardian Email', array(
                    'valid_email',
                    array('check_guardian_email_exists', array($this, 'check_guardian_email_exists')),
                )
            );
        }

        // Admission number validation (if not auto-generated)
        if (!$this->sch_setting_detail->adm_auto_insert) {
            $this->form_validation->set_rules('admission_no', 'Admission Number', 'trim|required|xss_clean|is_unique[students.admission_no]');
        }

        // Roll number validation (if enabled and not auto-generated)
        if ($this->sch_setting_detail->roll_no) {
            if (!$this->sch_setting_detail->sroll_auto_insert) {
                $this->form_validation->set_rules('roll_no', 'Roll Number', 'trim|required|xss_clean|callback_is_unique_roll_no');
            }
        }

        // Transport fee validation
        $transport_feemaster_id = isset($input_data['transport_feemaster_id']) ? $input_data['transport_feemaster_id'] : null;
        if (!empty($transport_feemaster_id)) {
            $this->form_validation->set_rules('vehroute_id', 'Route List', 'trim|required|xss_clean');
            $this->form_validation->set_rules('route_pickup_point_id', 'Pickup Point', 'trim|required|xss_clean');
            $this->form_validation->set_rules('transport_feemaster_id[]', 'Fees Month', 'trim|required|xss_clean');
        }
    }

    /**
     * Check if student email already exists
     *
     * @param string $str Email to check
     * @return bool
     */
    public function check_student_email_exists($str)
    {
        $email = $this->security->xss_clean($str);
        if ($email != "") {
            $id = isset($_POST['student_id']) ? $_POST['student_id'] : 0;

            if ($this->check_email_data_exists($email, $id)) {
                $this->form_validation->set_message('check_student_email_exists', 'Email already exists');
                return false;
            }
        }
        return true;
    }

    /**
     * Check if email exists in database
     *
     * @param string $email Email to check
     * @param int $id Student ID to exclude
     * @return bool
     */
    private function check_email_data_exists($email, $id)
    {
        $this->db->where('email', $email);
        $this->db->where('id !=', $id);
        $query = $this->db->get('students');
        return ($query->num_rows() > 0);
    }

    /**
     * Check if guardian email already exists
     *
     * @param string $str Email to check
     * @return bool
     */
    public function check_guardian_email_exists($str)
    {
        $email = $this->security->xss_clean($str);
        if ($email != "") {
            $id = isset($_POST['student_id']) ? $_POST['student_id'] : 0;

            if ($this->check_guardian_email_data_exists($email, $id)) {
                $this->form_validation->set_message('check_guardian_email_exists', 'Guardian email already exists');
                return false;
            }
        }
        return true;
    }

    /**
     * Check if guardian email exists in database
     *
     * @param string $email Email to check
     * @param int $id Student ID to exclude
     * @return bool
     */
    private function check_guardian_email_data_exists($email, $id)
    {
        $this->db->where('guardian_email', $email);
        $this->db->where('id !=', $id);
        $query = $this->db->get('students');
        return ($query->num_rows() > 0);
    }

    /**
     * Check if admission number already exists
     *
     * @param string $str Admission number to check
     * @return bool
     */
    public function check_student_admi_no_exists($str)
    {
        $admi_no = $this->security->xss_clean($str);
        if ($admi_no != "") {
            $isexist = $this->check_admi_no_data_exists($admi_no);
            if ($isexist) {
                $this->form_validation->set_message('check_student_admi_no_exists', 'Admission number already exists');
                return false;
            }
        }
        return true;
    }

    /**
     * Check if admission number exists in database
     *
     * @param string $admi_no Admission number to check
     * @return bool
     */
    private function check_admi_no_data_exists($admi_no)
    {
        $this->db->where('admi_no', $admi_no);
        $query = $this->db->get('student_admi');
        return ($query->num_rows() > 0);
    }

    /**
     * Check if roll number is unique for the class/section
     *
     * @param string $str Roll number to check
     * @return bool
     */
    public function is_unique_roll_no($str)
    {
        $roll_no = $this->security->xss_clean($str);
        $class_id = isset($_POST['class_id']) ? $_POST['class_id'] : 0;
        $section_id = isset($_POST['section_id']) ? $_POST['section_id'] : 0;
        $student_id = isset($_POST['student_id']) ? $_POST['student_id'] : 0;

        if ($roll_no != "" && $class_id > 0 && $section_id > 0) {
            if ($this->check_roll_no_exists($roll_no, $student_id, $class_id, $section_id)) {
                $this->form_validation->set_message('is_unique_roll_no', 'Roll number already exists for this class/section');
                return false;
            }
        }
        return true;
    }

    /**
     * Check if roll number exists for class/section
     *
     * @param string $roll_no Roll number
     * @param int $student_id Student ID to exclude
     * @param int $class_id Class ID
     * @param int $section_id Section ID
     * @return bool
     */
    private function check_roll_no_exists($roll_no, $student_id, $class_id, $section_id)
    {
        $session = $this->setting_model->getCurrentSession();

        $this->db->where(array(
            'class_id' => $class_id,
            'roll_no' => $roll_no,
            'section_id' => $section_id,
            'session_id' => $session
        ));

        if ($student_id > 0) {
            $this->db->where('student_id !=', $student_id);
        }

        $query = $this->db->join("student_session", "students.id = student_session.student_id")->get('students');
        return ($query->num_rows() > 0);
    }

    /**
     * Create Student Admission Record
     *
     * Creates the student record and all related data in the database.
     * This replicates the exact logic from Student::create() controller.
     *
     * @param array $input_data Input data
     * @return array Response array
     */
    private function create_student_admission_record($input_data)
    {
        // Start transaction
        $this->db->trans_start();

        try {
            // Get current session
            $session = $this->setting_model->getCurrentSession();

            // Extract input data
            $class_id = $input_data['class_id'];
            $section_id = $input_data['section_id'];
            $fees_discount = isset($input_data['fees_discount']) ? $input_data['fees_discount'] : 0;
            $route_pickup_point_id = isset($input_data['route_pickup_point_id']) ? $input_data['route_pickup_point_id'] : null;
            $vehroute_id = isset($input_data['vehroute_id']) ? $input_data['vehroute_id'] : null;
            $hostel_room_id = isset($input_data['hostel_room_id']) ? $input_data['hostel_room_id'] : 0;
            $staff_id = $input_data['reference_id'];
            $admi_noo = isset($input_data['admi_no']) ? $input_data['admi_no'] : '';

            if (empty($vehroute_id)) {
                $vehroute_id = null;
            }
            if (empty($route_pickup_point_id)) {
                $route_pickup_point_id = null;
            }
            if (empty($hostel_room_id)) {
                $hostel_room_id = 0;
            }

            // Prepare student data
            $data_insert = array(
                'firstname'         => $input_data['firstname'],
                'rte'               => isset($input_data['rte']) ? $input_data['rte'] : '',
                'state'             => isset($input_data['state']) ? $input_data['state'] : '',
                'city'              => isset($input_data['city']) ? $input_data['city'] : '',
                'pincode'           => isset($input_data['pincode']) ? $input_data['pincode'] : '',
                'cast'              => isset($input_data['cast']) ? $input_data['cast'] : '',
                'previous_school'   => isset($input_data['previous_school']) ? $input_data['previous_school'] : '',
                'dob'               => $this->customlib->dateFormatToYYYYMMDD($input_data['dob']),
                'current_address'   => isset($input_data['current_address']) ? $input_data['current_address'] : '',
                'permanent_address' => isset($input_data['permanent_address']) ? $input_data['permanent_address'] : '',
                'adhar_no'          => isset($input_data['adhar_no']) ? $input_data['adhar_no'] : '',
                'samagra_id'        => isset($input_data['samagra_id']) ? $input_data['samagra_id'] : '',
                'bank_account_no'   => isset($input_data['bank_account_no']) ? $input_data['bank_account_no'] : '',
                'bank_name'         => isset($input_data['bank_name']) ? $input_data['bank_name'] : '',
                'ifsc_code'         => isset($input_data['ifsc_code']) ? $input_data['ifsc_code'] : '',
                'guardian_email'    => isset($input_data['guardian_email']) ? $input_data['guardian_email'] : '',
                'gender'            => $input_data['gender'],
                'guardian_name'     => isset($input_data['guardian_name']) ? $input_data['guardian_name'] : '',
                'guardian_relation' => isset($input_data['guardian_relation']) ? $input_data['guardian_relation'] : '',
                'guardian_phone'    => isset($input_data['guardian_phone']) ? $input_data['guardian_phone'] : '',
                'guardian_address'  => isset($input_data['guardian_address']) ? $input_data['guardian_address'] : '',
                'hostel_room_id'    => $hostel_room_id,
                'note'              => isset($input_data['note']) ? $input_data['note'] : '',
                'is_active'         => 'yes',
            );

            // Add optional fields
            if ($this->sch_setting_detail->guardian_occupation) {
                $data_insert['guardian_occupation'] = isset($input_data['guardian_occupation']) ? $input_data['guardian_occupation'] : '';
            }

            if ($this->sch_setting_detail->guardian_name) {
                $data_insert['guardian_is'] = isset($input_data['guardian_is']) ? $input_data['guardian_is'] : '';
            }

            // Add other optional fields
            $optional_fields = array(
                'house' => 'school_house_id',
                'blood_group' => 'blood_group',
                'roll_no' => 'roll_no',
                'lastname' => 'lastname',
                'middlename' => 'middlename',
                'category_id' => 'category_id',
                'religion' => 'religion',
                'mobileno' => 'mobileno',
                'email' => 'email',
                'height' => 'height',
                'weight' => 'weight',
                'father_name' => 'father_name',
                'father_phone' => 'father_phone',
                'father_occupation' => 'father_occupation',
                'mother_name' => 'mother_name',
                'mother_phone' => 'mother_phone',
                'mother_occupation' => 'mother_occupation',
            );

            foreach ($optional_fields as $input_key => $db_key) {
                if (isset($input_data[$input_key])) {
                    $data_insert[$db_key] = $input_data[$input_key];
                }
            }

            // Handle dates
            if (isset($input_data['admission_date'])) {
                $data_insert['admission_date'] = $this->customlib->dateFormatToYYYYMMDD($input_data['admission_date']);
            }
            if (isset($input_data['measure_date'])) {
                $data_insert['measurement_date'] = $this->customlib->dateFormatToYYYYMMDD($input_data['measure_date']);
            }

            // Handle admission number generation
            $insert = true;
            $data_setting = array();
            $data_setting['id'] = $this->sch_setting_detail->id;
            $data_setting['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
            $data_setting['adm_update_status'] = $this->sch_setting_detail->adm_update_status;
            $data_setting['sroll_auto_insert'] = $this->sch_setting_detail->sroll_auto_insert;
            $data_setting['sroll_update_status'] = $this->sch_setting_detail->sroll_update_status;
            $admission_no = 0;

            if ($this->sch_setting_detail->adm_auto_insert) {
                if ($this->sch_setting_detail->adm_update_status) {
                    $admission_no = $this->sch_setting_detail->adm_prefix . $this->sch_setting_detail->adm_start_from;

                    $last_student = $this->student_model->lastRecord();
                    if (!empty($last_student)) {
                        $last_admission_digit = str_replace($this->sch_setting_detail->adm_prefix, "", $last_student->admission_no);
                        $admission_no = $this->sch_setting_detail->adm_prefix . sprintf("%0" . $this->sch_setting_detail->adm_no_digit . "d", $last_admission_digit + 1);
                        $data_insert['admission_no'] = $admission_no;
                    } else {
                        $admission_no = $this->sch_setting_detail->adm_prefix . $this->sch_setting_detail->adm_start_from;
                        $data_insert['admission_no'] = $admission_no;
                    }
                } else {
                    $admission_no = $this->sch_setting_detail->adm_prefix . $this->sch_setting_detail->adm_start_from;
                    $data_insert['admission_no'] = $admission_no;
                }

                $admission_no_exists = $this->student_model->check_adm_exists($admission_no);
                if ($admission_no_exists) {
                    $insert = false;
                }
            } else {
                $data_insert['admission_no'] = isset($input_data['admission_no']) ? $input_data['admission_no'] : '';
            }

            // Handle roll number generation
            if ($this->sch_setting_detail->roll_no) {
                if ($this->sch_setting_detail->sroll_auto_insert) {
                    if ($this->sch_setting_detail->sroll_update_status) {
                        $sroll_no = $this->sch_setting_detail->sroll_prefix . $this->sch_setting_detail->sroll_start_from;

                        $last_student = $this->student_model->lastrollRecord($class_id, $section_id);
                        if (!empty($last_student)) {
                            $last_roll_no_digit = str_replace($this->sch_setting_detail->sroll_prefix, "", $last_student->roll_no);
                            $sroll_no = $this->sch_setting_detail->sroll_prefix . sprintf("%0" . $this->sch_setting_detail->sroll_no_digit . "d", $last_roll_no_digit + 1);
                            $data_insert['roll_no'] = $sroll_no;
                        } else {
                            $sroll_no = $this->sch_setting_detail->sroll_prefix . $this->sch_setting_detail->sroll_start_from;
                            $data_insert['roll_no'] = $sroll_no;
                        }
                    } else {
                        $sroll_no = $this->sch_setting_detail->sroll_prefix . $this->sch_setting_detail->sroll_start_from;
                        $data_insert['roll_no'] = $sroll_no;
                    }

                    $roll_no_exists = $this->student_model->check_sroll_exists($sroll_no, $class_id, $section_id);
                    if ($roll_no_exists) {
                        $insert = false;
                    }
                } else {
                    $data_insert['roll_no'] = isset($input_data['roll_no']) ? $input_data['roll_no'] : '';
                }
            }

            // Set default image based on gender
            if ($input_data['gender'] == 'Female') {
                $data_insert['image'] = 'uploads/student_images/default_female.jpg';
            } else {
                $data_insert['image'] = 'uploads/student_images/default_male.jpg';
            }

            // Check if we can proceed with insertion
            if (!$insert) {
                $this->db->trans_rollback();
                return array(
                    'status' => 0,
                    'message' => 'Admission number ' . $admission_no . ' already exists',
                    'timestamp' => date('Y-m-d H:i:s')
                );
            }

            // Insert student record
            $insert_id = $this->student_model->add($data_insert, $data_setting);

            if (!$insert_id) {
                $this->db->trans_rollback();
                return array(
                    'status' => 0,
                    'message' => 'Failed to create student admission record',
                    'timestamp' => date('Y-m-d H:i:s')
                );
            }

            // Create student session record
            $data_new = array(
                'student_id'            => $insert_id,
                'class_id'              => $class_id,
                'section_id'            => $section_id,
                'session_id'            => $session,
                'fees_discount'         => $fees_discount,
                'route_pickup_point_id' => $route_pickup_point_id,
                'vehroute_id'           => $vehroute_id,
            );

            $student_session_id = $this->student_model->add_student_session($data_new);

            // Create student reference record
            $reference_array = array(
                'session_id' => $session,
                'student_id' => $insert_id,
                'staff_id'   => $staff_id,
            );
            $this->student_model->add_student_reference($reference_array);

            // Create admission number record
            if (!empty($admi_noo)) {
                $admi_no_array = array(
                    'student_id'  => $insert_id,
                    'admi_status' => 1,
                    'admi_no'     => $admi_noo,
                );
            } else {
                $admi_no_array = array(
                    'student_id'  => $insert_id,
                    'admi_status' => 0,
                );
            }
            $this->student_model->admi_no_add($admi_no_array);

            // Handle fee assignment
            $fee_session_group_id = isset($input_data['fee_session_group_id']) ? $input_data['fee_session_group_id'] : null;
            if ($fee_session_group_id) {
                $this->studentfeemaster_model->assign_bulk_fees($fee_session_group_id, $student_session_id, array());
            }

            // Handle transport fees
            $transport_feemaster_id = isset($input_data['transport_feemaster_id']) ? $input_data['transport_feemaster_id'] : null;
            if (!empty($transport_feemaster_id)) {
                $trns_data_insert = array();
                foreach ($transport_feemaster_id as $transport_feemaster_value) {
                    $trns_data_insert[] = array(
                        'student_session_id'     => $student_session_id,
                        'route_pickup_point_id'  => $route_pickup_point_id,
                        'transport_feemaster_id' => $transport_feemaster_value,
                    );
                }
                $this->studenttransportfee_model->add($trns_data_insert, $student_session_id, array(), $route_pickup_point_id);
            }

            // Generate student login credentials
            $user_password = $this->role_model->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);

            $data_student_login = array(
                'username' => $this->student_login_prefix . $insert_id,
                'password' => $user_password,
                'user_id'  => $insert_id,
                'role'     => 'student',
                'lang_id'  => $this->sch_setting_detail->lang_id,
            );
            $this->user_model->add($data_student_login);

            // Handle sibling or create parent account
            $sibling_id = isset($input_data['sibling_id']) ? $input_data['sibling_id'] : 0;
            $parent_password = '';
            $parent_username = '';

            if ($sibling_id > 0) {
                $student_sibling = $this->student_model->get($sibling_id);
                $update_student = array(
                    'id'        => $insert_id,
                    'parent_id' => $student_sibling['parent_id'],
                );
                $this->student_model->add($update_student);
            } else {
                $parent_password = $this->role_model->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);
                $temp = $insert_id;
                $data_parent_login = array(
                    'username' => $this->parent_login_prefix . $insert_id,
                    'password' => $parent_password,
                    'user_id'  => 0,
                    'role'     => 'parent',
                    'childs'   => $temp,
                );
                $ins_parent_id = $this->user_model->add($data_parent_login);
                $update_student = array(
                    'id'        => $insert_id,
                    'parent_id' => $ins_parent_id,
                );
                $this->student_model->add($update_student);
                $parent_username = $this->parent_login_prefix . $insert_id;
            }

            // Complete transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                return array(
                    'status' => 0,
                    'message' => 'Transaction failed while creating student admission',
                    'timestamp' => date('Y-m-d H:i:s')
                );
            }

            // Prepare success response
            $response_data = array(
                'student_id' => $insert_id,
                'admission_no' => $data_insert['admission_no'],
                'roll_no' => isset($data_insert['roll_no']) ? $data_insert['roll_no'] : '',
                'student_session_id' => $student_session_id,
                'student_username' => $this->student_login_prefix . $insert_id,
                'student_password' => $user_password,
                'firstname' => $input_data['firstname'],
                'lastname' => isset($input_data['lastname']) ? $input_data['lastname'] : '',
                'gender' => $input_data['gender'],
                'class_id' => $class_id,
                'section_id' => $section_id,
            );

            if ($sibling_id <= 0) {
                $response_data['parent_username'] = $parent_username;
                $response_data['parent_password'] = $parent_password;
            }

            return array(
                'status' => 1,
                'message' => 'Student admission created successfully',
                'data' => $response_data,
                'timestamp' => date('Y-m-d H:i:s')
            );

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Student admission creation error: ' . $e->getMessage());

            return array(
                'status' => 0,
                'message' => 'Error creating student admission: ' . $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            );
        }
    }
}
