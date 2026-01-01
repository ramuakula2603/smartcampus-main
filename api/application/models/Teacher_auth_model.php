<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Teacher_auth_model extends CI_Model
{

    public $client_service               = "smartschool";
    public $auth_key                     = "schoolAdmin@";
    public $security_authentication_flag = 1; // Enable authentication for teachers

    public function __construct()
    {
        parent::__construct();
        // Load models
        $this->load->model(array('staff_model', 'setting_model'));

        // Load libraries with error handling
        $this->load->library('encryption');
        $this->load->library('enc_lib'); // Load encryption library for password verification

        // Try to load JWT library, but don't fail if it's not available
        if (file_exists(APPPATH . 'libraries/JWT_lib.php')) {
            try {
                $this->load->library('JWT_lib');
            } catch (Exception $e) {
                // JWT library not available, continue without it
                log_message('info', 'JWT library not available: ' . $e->getMessage());
            }
        }
    }

    public function check_auth_client()
    {
        $client_service = $this->input->get_request_header('Client-Service', true);
        $auth_key       = $this->input->get_request_header('Auth-Key', true);
        if ($client_service == $this->client_service && $auth_key == $this->auth_key) {
            return true;
        } else {
            return false;
        }
    }

    public function login($email, $password, $app_key)
    {
        // Check teacher login credentials
        $q = $this->checkTeacherLogin($email, $password);

        if (empty($q)) {
            return array('status' => 0, 'message' => 'Invalid Email or Password');
        } else {
            if ($q->is_active == 1) {
                $result = $this->getTeacherInformation($q->id);

                if ($result != false) {
                    $setting_result = $this->setting_model->get();

                    // Handle currency settings with defaults
                    $currency_symbol = isset($setting_result[0]['currency_symbol']) ? $setting_result[0]['currency_symbol'] : '$';
                    $currency = isset($setting_result[0]['currency']) ? $setting_result[0]['currency'] : 'USD';
                    $currency_short_name = isset($setting_result[0]['currency']) ? $setting_result[0]['currency'] : 'USD';

                    // Handle language settings with defaults
                    $lang_id = isset($setting_result[0]['lang_id']) ? $setting_result[0]['lang_id'] : 1;
                    $language = isset($setting_result[0]['language']) ? $setting_result[0]['language'] : 'English';
                    $short_code = 'en';

                    $last_login = date('Y-m-d H:i:s');

                    // Generate JWT token with teacher information (if JWT library is available)
                    $jwt_token = null;
                    if (isset($this->JWT_lib) && is_object($this->JWT_lib)) {
                        try {
                            $jwt_payload = array(
                                'user_id' => $q->id,
                                'staff_id' => $result->id,
                                'email' => $result->email,
                                'role' => 'teacher',
                                'employee_id' => $result->employee_id,
                                'name' => $result->name . ' ' . $result->surname
                            );
                            $jwt_token = $this->JWT_lib->generate_token($jwt_payload);
                        } catch (Exception $e) {
                            // JWT generation failed, continue without it
                            $jwt_token = null;
                            log_message('error', 'JWT token generation failed: ' . $e->getMessage());
                        }
                    }

                    // Also generate simple token for backward compatibility
                    $simple_token = $this->getToken();
                    $expired_at = date("Y-m-d H:i:s", strtotime('+8760 hours'));

                    $this->db->trans_start();
                    $this->db->insert('users_authentication', array(
                        'users_id' => $q->id,
                        'token' => $simple_token,
                        'staff_id' => $result->id,
                        'expired_at' => $expired_at
                    ));

                    // Update app key if provided
                    if ($app_key) {
                        $updateData = array('app_key' => $app_key);
                        $this->db->where('id', $result->id);
                        $this->db->update('staff', $updateData);
                    }

                    $fullname = trim($result->name . ' ' . $result->surname);
                    if (empty($fullname)) {
                        $fullname = $result->name;
                    }

                    $session_data = array(
                        'id' => $result->id,
                        'staff_id' => $result->id,
                        'employee_id' => $result->employee_id,
                        'role' => 'teacher',
                        'email' => $result->email,
                        'contact_no' => $result->contact_no,
                        'username' => $fullname,
                        'name' => $result->name,
                        'surname' => $result->surname,
                        'designation' => $result->designation,
                        'department' => $result->department,
                        'date_format' => isset($setting_result[0]['date_format']) ? $setting_result[0]['date_format'] : 'd-m-Y',
                        'currency_symbol' => $currency_symbol,
                        'currency_short_name' => $currency_short_name,
                        'currency_id' => $currency,
                        'timezone' => isset($setting_result[0]['timezone']) ? $setting_result[0]['timezone'] : 'UTC',
                        'sch_name' => isset($setting_result[0]['name']) ? $setting_result[0]['name'] : 'School',
                        'language' => array('lang_id' => $lang_id, 'language' => $language, 'short_code' => $short_code),
                        'is_rtl' => isset($setting_result[0]['is_rtl']) ? $setting_result[0]['is_rtl'] : '0',
                        'theme' => isset($setting_result[0]['theme']) ? $setting_result[0]['theme'] : 'default.jpg',
                        'image' => $result->image,
                        'start_week' => isset($setting_result[0]['start_week']) ? $setting_result[0]['start_week'] : 'Monday',
                        'superadmin_restriction' => isset($setting_result[0]['superadmin_restriction']) ? $setting_result[0]['superadmin_restriction'] : '0',
                    );

                    if ($this->db->trans_status() === false) {
                        $this->db->trans_rollback();
                        return array('status' => 0, 'message' => 'Internal server error.');
                    } else {
                        $this->db->trans_commit();
                        return array(
                            'status' => 1,
                            'message' => 'Successfully logged in.',
                            'id' => $q->id,
                            'token' => $simple_token,
                            'jwt_token' => $jwt_token,
                            'role' => 'teacher',
                            'record' => $session_data
                        );
                    }
                } else {
                    return array('status' => 0, 'message' => 'Your account is suspended');
                }
            } else {
                return array('status' => 0, 'message' => 'Your account is disabled. Please contact administrator.');
            }
        }
    }

    public function checkTeacherLogin($email, $password)
    {
        // Get the teacher record by email (same approach as main project's Staff_model->getByEmail)
        $this->db->select('staff.id, staff.email, staff.password, staff.is_active, staff.lang_id');
        $this->db->from('staff');
        $this->db->where('staff.email', $email);
        $this->db->where('staff.is_active', 1);
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            $staff = $query->row();

            // Use the same password verification approach as main project
            // Staff_model->checkLogin() uses $this->enc_lib->passHashDyc($password, $record->password)
            try {
                $pass_verify = $this->enc_lib->passHashDyc($password, $staff->password);

                if ($pass_verify) {
                    return $staff;
                } else {
                    return false;
                }
            } catch (Exception $e) {
                log_message('error', 'Password verification error: ' . $e->getMessage());
                return false;
            }
        } else {
            return false;
        }
    }

    public function getTeacherInformation($staff_id)
    {
        $this->db->select('staff.*, staff_designation.designation as designation_name, department.department_name');
        $this->db->from('staff');
        $this->db->join('staff_designation', 'staff_designation.id = staff.designation', 'left');
        $this->db->join('department', 'department.id = staff.department', 'left');
        $this->db->where('staff.id', $staff_id);
        $this->db->where('staff.is_active', 1);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function getTeacherCurrency($staff_id)
    {
        // Implement currency logic if needed
        $setting_result = $this->setting_model->get();
        return array((object)array(
            'id' => $setting_result[0]['currency_id'],
            'symbol' => $setting_result[0]['currency_symbol'],
            'short_name' => $setting_result[0]['short_name']
        ));
    }

    public function getTeacherLanguage($staff_id)
    {
        $this->db->select('languages.*');
        $this->db->from('languages');
        $this->db->join('staff', 'staff.lang_id = languages.id');
        $this->db->where('staff.id', $staff_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            $setting_result = $this->setting_model->get();
            return array((object)array(
                'lang_id' => $setting_result[0]['lang_id'],
                'language' => $setting_result[0]['language'],
                'short_code' => $setting_result[0]['short_code']
            ));
        }
    }

    public function getToken($randomIdLength = 32)
    {
        $token = '';
        do {
            $bytes = random_bytes($randomIdLength);
            $token .= str_replace(
                ['.', '/', '='], '', base64_encode($bytes)
            );
        } while (strlen($token) < $randomIdLength);
        return substr($token, 0, $randomIdLength);
    }

    public function logout($deviceToken)
    {
        $users_id = $this->input->get_request_header('User-ID', true);
        $token = $this->input->get_request_header('Authorization', true);

        // Clear app key if device token provided
        if ($deviceToken) {
            $this->db->where('app_key', $deviceToken)->update('staff', array('app_key' => null));
        }

        // Remove authentication token
        $this->db->where('users_id', $users_id)->where('token', $token)->delete('users_authentication');

        return array('status' => 200, 'message' => 'Successfully logged out.');
    }

    public function auth()
    {
        if ($this->security_authentication_flag) {
            $users_id = $this->input->get_request_header('User-ID', true);
            $token = $this->input->get_request_header('Authorization', true);
            $jwt_token = $this->input->get_request_header('JWT-Token', true);

            // Try JWT authentication first (if JWT library is available)
            if ($jwt_token && isset($this->JWT_lib) && is_object($this->JWT_lib)) {
                try {
                    $jwt_payload = $this->JWT_lib->verify_token($jwt_token);
                    if ($jwt_payload) {
                        return array(
                            'status' => 200,
                            'message' => 'Authorized via JWT.',
                            'staff_id' => $jwt_payload['staff_id'],
                            'user_id' => $jwt_payload['user_id'],
                            'auth_type' => 'jwt'
                        );
                    } else {
                        return array('status' => 401, 'message' => 'Invalid or expired JWT token.');
                    }
                } catch (Exception $e) {
                    // JWT verification failed, fall back to traditional auth
                    log_message('error', 'JWT verification failed: ' . $e->getMessage());
                }
            }

            // Fallback to traditional token authentication
            $q = $this->db->select('expired_at, staff_id, users_id')
                          ->from('users_authentication')
                          ->where('users_id', $users_id)
                          ->where('token', $token)
                          ->get()->row();

            if ($q == "") {
                return array('status' => 401, 'message' => 'Unauthorized.');
            } else {
                if ($q->expired_at < date('Y-m-d H:i:s')) {
                    return array('status' => 401, 'message' => 'Your session has expired.');
                } else {
                    // Update token expiration
                    $updated_at = date('Y-m-d H:i:s');
                    $expired_at = date("Y-m-d H:i:s", strtotime('+8760 hours'));
                    $this->db->where('users_id', $users_id)
                             ->where('token', $token)
                             ->update('users_authentication', array(
                                 'expired_at' => $expired_at,
                                 'updated_at' => $updated_at
                             ));
                    return array(
                        'status' => 200,
                        'message' => 'Authorized via token.',
                        'staff_id' => $q->staff_id,
                        'user_id' => $q->users_id,
                        'auth_type' => 'token'
                    );
                }
            }
        } else {
            return array('status' => 200, 'message' => 'Authorized.');
        }
    }

    public function get_profile()
    {
        $auth_check = $this->auth();
        if ($auth_check['status'] != 200) {
            return $auth_check;
        }

        $staff_id = $auth_check['staff_id'];
        $result = $this->getTeacherInformation($staff_id);

        if ($result) {
            $profile_data = array(
                'id' => $result->id,
                'employee_id' => $result->employee_id,
                'name' => $result->name,
                'surname' => $result->surname,
                'father_name' => $result->father_name,
                'mother_name' => $result->mother_name,
                'email' => $result->email,
                'contact_no' => $result->contact_no,
                'emergency_contact_no' => $result->emergency_contact_no,
                'dob' => $result->dob,
                'marital_status' => $result->marital_status,
                'date_of_joining' => $result->date_of_joining,
                'designation' => $result->designation_name,
                'department' => $result->department_name,
                'qualification' => $result->qualification,
                'work_exp' => $result->work_exp,
                'local_address' => $result->local_address,
                'permanent_address' => $result->permanent_address,
                'image' => $result->image,
                'gender' => $result->gender,
                'account_title' => $result->account_title,
                'bank_account_no' => $result->bank_account_no,
                'bank_name' => $result->bank_name,
                'ifsc_code' => $result->ifsc_code,
                'bank_branch' => $result->bank_branch,
                'payscale' => $result->payscale,
                'basic_salary' => $result->basic_salary,
                'epf_no' => $result->epf_no,
                'contract_type' => $result->contract_type,
                'work_shift' => $result->work_shift,
                'work_location' => $result->work_location,
                'note' => $result->note,
                'is_active' => $result->is_active
            );

            return array('status' => 1, 'message' => 'Profile retrieved successfully.', 'data' => $profile_data);
        } else {
            return array('status' => 0, 'message' => 'Profile not found.');
        }
    }

    public function update_profile($params)
    {
        $auth_check = $this->auth();
        if ($auth_check['status'] != 200) {
            return $auth_check;
        }

        $staff_id = $auth_check['staff_id'];

        // Define updatable fields
        $updatable_fields = array(
            'name', 'surname', 'father_name', 'mother_name', 'contact_no',
            'emergency_contact_no', 'local_address', 'permanent_address',
            'qualification', 'work_exp', 'note', 'account_title',
            'bank_account_no', 'bank_name', 'ifsc_code', 'bank_branch'
        );

        $update_data = array();
        foreach ($updatable_fields as $field) {
            if (isset($params[$field])) {
                $update_data[$field] = $params[$field];
            }
        }

        if (!empty($update_data)) {
            $this->db->where('id', $staff_id);
            $this->db->update('staff', $update_data);

            if ($this->db->affected_rows() > 0) {
                return array('status' => 1, 'message' => 'Profile updated successfully.');
            } else {
                return array('status' => 0, 'message' => 'No changes made to profile.');
            }
        } else {
            return array('status' => 0, 'message' => 'No valid fields provided for update.');
        }
    }

    public function change_password($current_password, $new_password)
    {
        $auth_check = $this->auth();
        if ($auth_check['status'] != 200) {
            return $auth_check;
        }

        $staff_id = $auth_check['staff_id'];

        // Verify current password
        $this->db->select('password');
        $this->db->from('staff');
        $this->db->where('id', $staff_id);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            $staff = $query->row();

            // In production, use proper password verification
            if ($staff->password == $current_password) {
                // Update password (in production, hash the new password)
                $this->db->where('id', $staff_id);
                $this->db->update('staff', array('password' => $new_password));

                return array('status' => 1, 'message' => 'Password changed successfully.');
            } else {
                return array('status' => 0, 'message' => 'Current password is incorrect.');
            }
        } else {
            return array('status' => 0, 'message' => 'Staff not found.');
        }
    }

    /**
     * Get complete staff profile with QR code functionality
     */
    public function getCompleteProfile($staff_id)
    {
        // Get basic staff profile information
        $staff_profile = $this->getTeacherInformation($staff_id);

        if (!$staff_profile) {
            return array('status' => 0, 'message' => 'Staff member not found.');
        }

        // Get custom fields data
        $custom_fields = $this->getCustomFields($staff_id);

        // Get school settings for field visibility
        $this->load->model('setting_model');
        $school_settings = $this->setting_model->get();

        // Get additional data sections with error handling
        try {
            $payroll_data = $this->getPayrollData($staff_id);
        } catch (Exception $e) {
            log_message('error', 'Payroll data error: ' . $e->getMessage());
            $payroll_data = array('payroll_records' => array(), 'salary_summary' => array());
        }

        try {
            $timeline_data = $this->getTimelineData($staff_id);
        } catch (Exception $e) {
            log_message('error', 'Timeline data error: ' . $e->getMessage());
            $timeline_data = array('timeline_events' => array(), 'total_events' => 0);
        }

        try {
            $leave_data = $this->getLeaveData($staff_id);
        } catch (Exception $e) {
            log_message('error', 'Leave data error: ' . $e->getMessage());
            $leave_data = array('leave_requests' => array(), 'leave_balance' => array(), 'total_requests' => 0);
        }

        try {
            $attendance_data = $this->getAttendanceData($staff_id);
        } catch (Exception $e) {
            log_message('error', 'Attendance data error: ' . $e->getMessage());
            $attendance_data = array('attendance_summary' => array(), 'recent_attendance' => array(), 'attendance_types' => array());
        }

        try {
            $rating_data = $this->getRatingData($staff_id);
        } catch (Exception $e) {
            log_message('error', 'Rating data error: ' . $e->getMessage());
            $rating_data = array('average_rating' => 0, 'total_reviews' => 0, 'can_view_rating' => false, 'reviews' => array());
        }

        // Get file paths with timestamps (v1.2 structure)
        $file_paths = $this->getStaffFilePaths($staff_profile);

        // Prepare comprehensive profile response
        $profile_data = array(
            'status' => 1,
            'message' => 'Profile retrieved successfully.',
            'staff_id' => $staff_profile->id,
            'basic_info' => $this->formatBasicInfo($staff_profile),
            'contact_info' => $this->formatContactInfo($staff_profile),
            'personal_info' => $this->formatPersonalInfo($staff_profile),
            'address_info' => $this->formatAddressInfo($staff_profile),
            'bank_details' => $this->formatBankDetails($staff_profile),
            'social_media' => $this->formatSocialMedia($staff_profile),
            'documents' => $this->formatDocuments($staff_profile),
            'custom_fields' => $custom_fields,
            'payroll_details' => $payroll_data,
            'timeline' => $timeline_data,
            'leave_records' => $leave_data,
            'attendance_information' => $attendance_data,  // Changed from attendance_records to attendance_information
            'ratings_reviews' => $rating_data,
            'file_paths' => $file_paths,  // v1.2 structure with timestamps
            'school_settings' => $this->formatSchoolSettings($school_settings)
        );

        return $profile_data;
    }

    /**
     * Get payroll data for staff
     */
    private function getPayrollData($staff_id)
    {
        // Get staff payroll records
        $this->db->select('*');
        $this->db->from('staff_payslip');
        $this->db->where('staff_id', $staff_id);
        $this->db->order_by('year DESC, month DESC');
        $payroll_records = $this->db->get()->result_array();

        // Get salary summary directly from database
        $this->db->select('sum(net_salary) as net_salary, sum(total_allowance) as earnings, sum(total_deduction) as deduction, sum(basic) as basic_salary, sum(tax) as tax');
        $this->db->from('staff_payslip');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('status', 'paid');
        $salary_summary = $this->db->get()->row_array();

        return array(
            'payroll_records' => $payroll_records,
            'salary_summary' => $salary_summary ?? array()
        );
    }

    /**
     * Get timeline/activity data for staff
     */
    private function getTimelineData($staff_id)
    {
        // Get staff timeline directly from database
        $this->db->select('*');
        $this->db->from('staff_timeline');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('status', 'yes');
        $this->db->order_by('timeline_date', 'ASC');
        $timeline_list = $this->db->get()->result_array();

        return array(
            'timeline_events' => $timeline_list,
            'total_events' => count($timeline_list)
        );
    }

    /**
     * Get leave data for staff
     */
    private function getLeaveData($staff_id)
    {
        // Get staff leave requests directly from database
        $this->db->select('staff.name,staff.surname,staff.employee_id,staff_leave_request.*,leave_types.type');
        $this->db->from('staff_leave_request');
        $this->db->join('staff', 'staff.id = staff_leave_request.staff_id');
        $this->db->join('leave_types', 'leave_types.id = staff_leave_request.leave_type_id');
        $this->db->where('staff_leave_request.staff_id', $staff_id);
        $this->db->where('staff.is_active', '1');
        $this->db->order_by('staff_leave_request.id', 'desc');
        $staff_leaves = $this->db->get()->result_array();

        // Get allotted leave types
        $this->db->select('staff_leave_details.*,leave_types.type');
        $this->db->from('staff_leave_details');
        $this->db->join('leave_types', 'staff_leave_details.leave_type_id = leave_types.id');
        $this->db->where('staff_id', $staff_id);
        $alloted_leavetype = $this->db->get()->result_array();

        // Calculate leave details
        $leave_details = array();
        foreach ($alloted_leavetype as $key => $value) {
            // Count approved leaves
            $this->db->select('sum(leave_days) as approve_leave');
            $this->db->from('staff_leave_request');
            $this->db->where('staff_id', $staff_id);
            $this->db->where('leave_type_id', $value["leave_type_id"]);
            $this->db->where('status', 'approve');
            $approved = $this->db->get()->row_array();

            $leave_details[] = array(
                'type' => $value["type"],
                'alloted_leave' => $value["alloted_leave"],
                'approve_leave' => $approved['approve_leave'] ?? 0,
                'remaining_leave' => $value["alloted_leave"] - ($approved['approve_leave'] ?? 0)
            );
        }

        return array(
            'leave_requests' => $staff_leaves,
            'leave_balance' => $leave_details,
            'total_requests' => count($staff_leaves)
        );
    }

    /**
     * Get attendance data for staff (v1.2 Enhanced Structure)
     */
    private function getAttendanceData($staff_id)
    {
        // Get attendance types with color coding
        $this->db->select('id, type, key_value');
        $this->db->from('staff_attendance_type');
        $this->db->where('is_active', 'yes');
        $this->db->order_by('id');
        $attendance_types_raw = $this->db->get()->result_array();

        // Define color mapping for attendance types
        $color_map = array(
            'P' => '#4CAF50',  // Green for Present
            'L' => '#FF9800',  // Orange for Late
            'A' => '#F44336',  // Red for Absent
            'H' => '#2196F3',  // Blue for Half Day
            'F' => '#9C27B0',  // Purple for Holiday
        );

        // Format attendance types with colors
        $attendance_types = array();
        foreach ($attendance_types_raw as $type) {
            // Clean key_value - remove HTML tags if present
            $key_value_raw = $type['key_value'];
            $key_value_clean = strip_tags($key_value_raw);
            $key_value_clean = trim($key_value_clean);
            $key = strtoupper($key_value_clean);

            $attendance_types[] = array(
                'id' => (int)$type['id'],
                'type' => $type['type'],
                'key_value' => $key_value_clean,  // Use cleaned key_value
                'color' => isset($color_map[$key]) ? $color_map[$key] : '#9E9E9E'
            );
        }

        // Get all attendance records ordered by date DESC (most recent first)
        $this->db->select('sa.*, sat.type as attendance_type, sat.key_value');
        $this->db->from('staff_attendance sa');
        $this->db->join('staff_attendance_type sat', 'sat.id = sa.staff_attendance_type_id', 'left');
        $this->db->where('sa.staff_id', $staff_id);
        $this->db->order_by('sa.date', 'DESC');
        $attendance_records = $this->db->get()->result_array();

        // Initialize counters
        $present_count = 0;
        $late_count = 0;
        $absent_count = 0;
        $half_day_count = 0;
        $holiday_count = 0;

        // Group records by month and year
        $monthly_data = array();

        foreach ($attendance_records as $record) {
            $date = $record['date'];

            // Clean key_value - remove HTML tags if present
            $key_value_raw = $record['key_value'];
            // Strip HTML tags and get just the letter
            $key_value_clean = strip_tags($key_value_raw);
            $key_value_clean = trim($key_value_clean);
            $key_value = strtoupper($key_value_clean);

            // Extract month and year
            $date_obj = new DateTime($date);
            $month = $date_obj->format('F');  // Full month name
            $year = $date_obj->format('Y');
            $day_name = $date_obj->format('l');  // Full day name (Monday, Tuesday, etc.)
            $month_year_key = $year . '-' . $date_obj->format('m');

            // Initialize month array if not exists
            if (!isset($monthly_data[$month_year_key])) {
                $monthly_data[$month_year_key] = array(
                    'month' => $month,
                    'year' => $year,
                    'month_number' => (int)$date_obj->format('m'),
                    'days' => array(),
                    'month_summary' => array(
                        'present' => 0,
                        'absent' => 0,
                        'late' => 0,
                        'half_day' => 0,
                        'holiday' => 0
                    )
                );
            }

            // Determine status label
            $status = 'unknown';
            if ($key_value == 'P') {
                $status = 'present';
                $present_count++;
                $monthly_data[$month_year_key]['month_summary']['present']++;
            } elseif ($key_value == 'L') {
                $status = 'late';
                $late_count++;
                $monthly_data[$month_year_key]['month_summary']['late']++;
            } elseif ($key_value == 'A') {
                $status = 'absent';
                $absent_count++;
                $monthly_data[$month_year_key]['month_summary']['absent']++;
            } elseif ($key_value == 'H') {
                $status = 'half_day';
                $half_day_count++;
                $monthly_data[$month_year_key]['month_summary']['half_day']++;
            } elseif ($key_value == 'F') {
                $status = 'holiday';
                $holiday_count++;
                $monthly_data[$month_year_key]['month_summary']['holiday']++;
            }

            // Add day record with cleaned key_value
            $monthly_data[$month_year_key]['days'][] = array(
                'date' => $date,
                'day_name' => $day_name,
                'status' => $status,
                'status_key' => $key_value,  // Use cleaned key_value
                'remark' => $record['remark'] ? $record['remark'] : ''
            );
        }

        // Convert monthly data to indexed array and sort by year-month DESC
        $monthly_breakdown = array_values($monthly_data);

        // Sort by year and month (most recent first)
        usort($monthly_breakdown, function($a, $b) {
            if ($a['year'] != $b['year']) {
                return $b['year'] - $a['year'];
            }
            return $b['month_number'] - $a['month_number'];
        });

        // Remove month_number from final output (it was only for sorting)
        foreach ($monthly_breakdown as &$month_data) {
            unset($month_data['month_number']);
        }

        // Calculate total records
        $total_records = count($attendance_records);

        // Calculate attendance percentage (present + half_day considered as attendance)
        $attendance_percentage = 0;
        if ($total_records > 0) {
            $attended = $present_count + ($half_day_count * 0.5);
            $attendance_percentage = round(($attended / $total_records) * 100, 2);
        }

        // Build final response (v1.2 structure)
        return array(
            'summary' => array(
                'total_present' => $present_count,
                'total_absent' => $absent_count,
                'total_late' => $late_count,
                'total_half_day' => $half_day_count,
                'total_holiday' => $holiday_count,
                'total_records' => $total_records,
                'attendance_percentage' => $attendance_percentage
            ),
            'monthly_breakdown' => $monthly_breakdown,
            'attendance_types' => $attendance_types
        );
    }

    /**
     * Get rating and review data for staff
     */
    private function getRatingData($staff_id)
    {
        // Skip ratings for admin user (id = 1)
        if ($staff_id == '1') {
            return array(
                'average_rating' => 0,
                'total_reviews' => 0,
                'can_view_rating' => false,
                'reviews' => array()
            );
        }

        // Get staff rating summary
        $this->db->select('sum(rate) as rate, count(*) as total');
        $this->db->from('staff_rating');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('status', 1);
        $staff_rating = $this->db->get()->row_array();

        $average_rating = 0;
        $can_view_rating = false;

        if ($staff_rating['total'] >= 3) {
            $average_rating = ($staff_rating['rate'] / $staff_rating['total']);
            $can_view_rating = true;
        }

        // Get detailed reviews
        $this->db->select('staff_rating.rate,staff_rating.comment,staff_rating.role,students.firstname,students.middlename,students.lastname,students.guardian_name');
        $this->db->from('staff_rating');
        $this->db->join('users', 'users.id = staff_rating.user_id', 'inner');
        $this->db->join('staff', 'staff_rating.staff_id = staff.id', 'inner');
        $this->db->join('students', 'students.id = staff_rating.user_id', 'left');
        $this->db->where('staff.is_active', 1);
        $this->db->where('staff_rating.staff_id', $staff_id);
        $this->db->where('staff_rating.status', 1);
        $user_reviews = $this->db->get()->result_array();

        return array(
            'average_rating' => round($average_rating, 2),
            'total_reviews' => $staff_rating['total'] ?? 0,
            'can_view_rating' => $can_view_rating,
            'reviews' => $user_reviews
        );
    }

    /**
     * Format basic staff information
     */
    private function formatBasicInfo($staff)
    {
        return array(
            'id' => $staff->id,
            'employee_id' => $staff->employee_id,
            'name' => $staff->name,
            'surname' => $staff->surname,
            'full_name' => trim($staff->name . ' ' . $staff->surname),
            'designation' => $staff->designation,
            'designation_name' => $staff->designation_name ?? '',
            'department' => $staff->department,
            'department_name' => $staff->department_name ?? '',
            'user_type' => $staff->user_type ?? '',
            'role_id' => $staff->role_id ?? '',
            'is_active' => $staff->is_active,
            'date_of_joining' => $staff->date_of_joining,
            'date_of_leaving' => $staff->date_of_leaving,
            'disable_at' => $staff->disable_at ?? null
        );
    }

    /**
     * Format contact information
     */
    private function formatContactInfo($staff)
    {
        return array(
            'email' => $staff->email,
            'contact_no' => $staff->contact_no,
            'emergency_contact_no' => $staff->emergency_contact_no
        );
    }

    /**
     * Format personal information
     */
    private function formatPersonalInfo($staff)
    {
        return array(
            'gender' => $staff->gender,
            'dob' => $staff->dob,
            'marital_status' => $staff->marital_status,
            'father_name' => $staff->father_name,
            'mother_name' => $staff->mother_name,
            'qualification' => $staff->qualification,
            'work_exp' => $staff->work_exp,
            'note' => $staff->note
        );
    }

    /**
     * Format address information
     */
    private function formatAddressInfo($staff)
    {
        return array(
            'local_address' => $staff->local_address,
            'permanent_address' => $staff->permanent_address
        );
    }

    /**
     * Format bank details
     */
    private function formatBankDetails($staff)
    {
        return array(
            'account_title' => $staff->account_title,
            'bank_name' => $staff->bank_name,
            'bank_branch' => $staff->bank_branch,
            'bank_account_no' => $staff->bank_account_no,
            'ifsc_code' => $staff->ifsc_code,
            'payscale' => $staff->payscale,
            'basic_salary' => $staff->basic_salary,
            'epf_no' => $staff->epf_no,
            'contract_type' => $staff->contract_type,
            'shift' => $staff->shift,
            'location' => $staff->location
        );
    }

    /**
     * Format social media links
     */
    private function formatSocialMedia($staff)
    {
        return array(
            'facebook' => $staff->facebook,
            'twitter' => $staff->twitter,
            'linkedin' => $staff->linkedin,
            'instagram' => $staff->instagram
        );
    }

    /**
     * Format document information
     */
    private function formatDocuments($staff)
    {
        $documents = array();

        if (!empty($staff->resume)) {
            $documents['resume'] = array(
                'filename' => $staff->resume,
                'download_url' => base_url() . 'api/teacher/download-document/' . $staff->id . '/resume',
                'type' => 'resume'
            );
        }

        if (!empty($staff->joining_letter)) {
            $documents['joining_letter'] = array(
                'filename' => $staff->joining_letter,
                'download_url' => base_url() . 'api/teacher/download-document/' . $staff->id . '/joining_letter',
                'type' => 'joining_letter'
            );
        }

        if (!empty($staff->resignation_letter)) {
            $documents['resignation_letter'] = array(
                'filename' => $staff->resignation_letter,
                'download_url' => base_url() . 'api/teacher/download-document/' . $staff->id . '/resignation_letter',
                'type' => 'resignation_letter'
            );
        }

        if (!empty($staff->other_document_file)) {
            $documents['other_document'] = array(
                'filename' => $staff->other_document_file,
                'name' => $staff->other_document_name ?? 'Other Document',
                'download_url' => base_url() . 'api/teacher/download-document/' . $staff->id . '/other_document_file',
                'type' => 'other_document'
            );
        }

        return $documents;
    }

    /**
     * Get custom fields for staff
     */
    private function getCustomFields($staff_id)
    {
        $this->db->select('cf.name, cf.type, cfv.field_value');
        $this->db->from('custom_field_values cfv');
        $this->db->join('custom_fields cf', 'cf.id = cfv.custom_field_id');
        $this->db->where('cfv.belong_table_id', $staff_id);
        $this->db->where('cf.belong_to', 'staff');
        $query = $this->db->get();

        $custom_fields = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $field) {
                $field_value = $field->field_value;

                // Handle JSON arrays for multi-select fields
                if (is_string($field_value) && is_array(json_decode($field_value, true)) && (json_last_error() == JSON_ERROR_NONE)) {
                    $field_value = json_decode($field_value, true);
                }

                $custom_fields[] = array(
                    'name' => $field->name,
                    'type' => $field->type,
                    'value' => $field_value
                );
            }
        }

        return $custom_fields;
    }

    /**
     * Generate QR code data for staff profile
     */
    private function generateQRCodeData($staff)
    {
        $qr_data = array(
            'type' => 'staff_profile',
            'staff_id' => $staff->id,
            'employee_id' => $staff->employee_id,
            'name' => trim($staff->name . ' ' . $staff->surname),
            'designation' => $staff->designation_name ?? '',
            'department' => $staff->department_name ?? '',
            'email' => $staff->email,
            'contact' => $staff->contact_no,
            'profile_url' => base_url() . 'api/teacher/profile/' . $staff->id
        );

        // Generate QR code string (JSON format)
        $qr_string = json_encode($qr_data);

        return array(
            'data' => $qr_data,
            'qr_string' => $qr_string,
            'qr_code_url' => $this->generateQRCodeImage($qr_string, $staff->id)
        );
    }

    /**
     * Generate QR code image URL (placeholder - implement with actual QR library)
     */
    private function generateQRCodeImage($qr_string, $staff_id)
    {
        // For now, return a placeholder URL
        // In production, implement with a QR code library like phpqrcode
        return base_url() . 'api/teacher/qr-code/' . $staff_id;
    }

    /**
     * Get profile image URL with fallback
     */
    private function getProfileImageURL($image, $gender)
    {
        if (!empty($image)) {
            return base_url() . 'uploads/staff_images/' . $image;
        } else {
            // Default image based on gender
            $default_image = ($gender == 'Male') ? 'default_male.jpg' : 'default_female.jpg';
            return base_url() . 'uploads/staff_images/' . $default_image;
        }
    }

    /**
     * Get staff file paths with timestamps (v1.2 structure)
     */
    private function getStaffFilePaths($staff_info)
    {
        $base_url = base_url();

        // Get timestamp for cache busting
        $timestamp = '?' . time();

        // Profile image path with timestamp
        $profile_image = '';
        if (!empty($staff_info->image)) {
            $profile_image = $base_url . 'uploads/staff_images/' . $staff_info->image . $timestamp;
        } else {
            if ($staff_info->gender == 'Male') {
                $profile_image = $base_url . 'uploads/staff_images/default_male.jpg' . $timestamp;
            } else {
                $profile_image = $base_url . 'uploads/staff_images/default_female.jpg' . $timestamp;
            }
        }

        // QR code and barcode paths with timestamp
        $qr_code_path = '';
        $barcode_path = '';

        if (!empty($staff_info->employee_id)) {
            // Check if QR code file exists
            // Use FCPATH to get the correct base path (goes up from api/ directory)
            $qr_file = FCPATH . '../uploads/staff_id_card/qrcode/' . $staff_info->employee_id . '.png';
            if (file_exists($qr_file)) {
                $qr_code_path = $base_url . 'uploads/staff_id_card/qrcode/' . $staff_info->employee_id . '.png' . $timestamp;
            }

            // Check if barcode file exists
            $barcode_file = FCPATH . '../uploads/staff_id_card/barcodes/' . $staff_info->employee_id . '.png';
            if (file_exists($barcode_file)) {
                $barcode_path = $base_url . 'uploads/staff_id_card/barcodes/' . $staff_info->employee_id . '.png' . $timestamp;
            }
        }

        // Document paths
        $documents = array();

        if (!empty($staff_info->resume)) {
            $documents['resume'] = array(
                'filename' => $staff_info->resume,
                'path' => $base_url . 'uploads/staff_documents/' . $staff_info->id . '/' . $staff_info->resume,
                'type' => 'resume'
            );
        }

        if (!empty($staff_info->joining_letter)) {
            $documents['joining_letter'] = array(
                'filename' => $staff_info->joining_letter,
                'path' => $base_url . 'uploads/staff_documents/' . $staff_info->id . '/' . $staff_info->joining_letter,
                'type' => 'joining_letter'
            );
        }

        if (!empty($staff_info->resignation_letter)) {
            $documents['resignation_letter'] = array(
                'filename' => $staff_info->resignation_letter,
                'path' => $base_url . 'uploads/staff_documents/' . $staff_info->id . '/' . $staff_info->resignation_letter,
                'type' => 'resignation_letter'
            );
        }

        if (!empty($staff_info->other_document_file)) {
            $documents['other_document'] = array(
                'filename' => $staff_info->other_document_file,
                'name' => $staff_info->other_document_name,
                'path' => $base_url . 'uploads/staff_documents/' . $staff_info->id . '/' . $staff_info->other_document_file,
                'type' => 'other_document'
            );
        }

        return array(
            'profile_image' => $profile_image,
            'qr_code' => $qr_code_path,
            'barcode' => $barcode_path,
            'documents' => $documents
        );
    }

    /**
     * Format school settings for field visibility
     */
    private function formatSchoolSettings($settings)
    {
        if (empty($settings) || !is_array($settings)) {
            return array();
        }

        $setting = $settings[0]; // Get first setting record

        return array(
            'staff_phone' => $setting['staff_phone'] ?? 1,
            'staff_emergency_contact' => $setting['staff_emergency_contact'] ?? 1,
            'staff_marital_status' => $setting['staff_marital_status'] ?? 1,
            'staff_father_name' => $setting['staff_father_name'] ?? 1,
            'staff_mother_name' => $setting['staff_mother_name'] ?? 1,
            'staff_qualification' => $setting['staff_qualification'] ?? 1,
            'staff_work_experience' => $setting['staff_work_experience'] ?? 1,
            'staff_note' => $setting['staff_note'] ?? 1,
            'staff_current_address' => $setting['staff_current_address'] ?? 1,
            'staff_permanent_address' => $setting['staff_permanent_address'] ?? 1,
            'staff_account_details' => $setting['staff_account_details'] ?? 1,
            'staff_social_media' => $setting['staff_social_media'] ?? 1,
            'staff_upload_documents' => $setting['staff_upload_documents'] ?? 1,
            'staff_barcode' => $setting['staff_barcode'] ?? 1
        );
    }

    public function get_dashboard_data()
    {
        $auth_check = $this->auth();
        if ($auth_check['status'] != 200) {
            return $auth_check;
        }

        $staff_id = $auth_check['staff_id'];

        // Get basic teacher information
        $teacher_info = $this->getTeacherInformation($staff_id);

        // Get assigned classes (if class_teacher table exists)
        $this->db->select('classes.class, sections.section, class_teacher.session_id');
        $this->db->from('class_teacher');
        $this->db->join('classes', 'classes.id = class_teacher.class_id');
        $this->db->join('sections', 'sections.id = class_teacher.section_id');
        $this->db->where('class_teacher.staff_id', $staff_id);
        $assigned_classes = $this->db->get()->result_array();

        // Get subject assignments (if teacher_subject table exists)
        $this->db->select('subjects.name as subject_name, subjects.code as subject_code');
        $this->db->from('teacher_subject');
        $this->db->join('subjects', 'subjects.id = teacher_subject.subject_id');
        $this->db->where('teacher_subject.teacher_id', $staff_id);
        $assigned_subjects = $this->db->get()->result_array();

        $dashboard_data = array(
            'teacher_info' => array(
                'name' => $teacher_info->name . ' ' . $teacher_info->surname,
                'employee_id' => $teacher_info->employee_id,
                'designation' => $teacher_info->designation_name,
                'department' => $teacher_info->department_name,
                'email' => $teacher_info->email,
                'image' => $teacher_info->image
            ),
            'assigned_classes' => $assigned_classes,
            'assigned_subjects' => $assigned_subjects,
            'total_classes' => count($assigned_classes),
            'total_subjects' => count($assigned_subjects)
        );

        return array('status' => 1, 'message' => 'Dashboard data retrieved successfully.', 'data' => $dashboard_data);
    }

    public function refresh_jwt_token($jwt_token)
    {
        if (!isset($this->JWT_lib) || !is_object($this->JWT_lib)) {
            return array('status' => 0, 'message' => 'JWT library not available.');
        }

        try {
            $new_token = $this->JWT_lib->refresh_token($jwt_token);

            if ($new_token) {
                return array(
                    'status' => 1,
                    'message' => 'Token refreshed successfully.',
                    'jwt_token' => $new_token,
                    'expires_in' => $this->JWT_lib->get_expiration_time() * 3600 // Convert hours to seconds
                );
            } else {
                return array('status' => 0, 'message' => 'Invalid or expired token. Please login again.');
            }
        } catch (Exception $e) {
            return array('status' => 0, 'message' => 'Token refresh failed: ' . $e->getMessage());
        }
    }

    public function validate_jwt_token($jwt_token)
    {
        if (!isset($this->JWT_lib) || !is_object($this->JWT_lib)) {
            return array('status' => 0, 'message' => 'JWT library not available.');
        }

        try {
            $payload = $this->JWT_lib->verify_token($jwt_token);

            if ($payload) {
                $remaining_time = $this->JWT_lib->get_remaining_time($jwt_token);
                $is_expiring_soon = $this->JWT_lib->is_token_expiring_soon($jwt_token);

                return array(
                    'status' => 1,
                    'message' => 'Token is valid.',
                    'payload' => $payload,
                    'remaining_time' => $remaining_time,
                    'expires_in_hours' => round($remaining_time / 3600, 2),
                    'is_expiring_soon' => $is_expiring_soon
                );
            } else {
                return array('status' => 0, 'message' => 'Invalid or expired token.');
            }
        } catch (Exception $e) {
            return array('status' => 0, 'message' => 'Token validation failed: ' . $e->getMessage());
        }
    }
}
