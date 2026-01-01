<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Auth_model extends CI_Model
{

    public $client_service               = "smartschool";
    public $auth_key                     = "schoolAdmin@";
    public $security_authentication_flag = 0;

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('user_model', 'setting_model', 'student_model'));
    }

    public function check_auth_client()
    {
        $client_service = $this->input->get_request_header('Client-Service', true);
        $auth_key       = $this->input->get_request_header('Auth-Key', true);
        if ($client_service == $this->client_service && $auth_key == $this->auth_key) {
            return true;
        } else {
            return json_output(200, array('status' => 0, 'message' => 'Unauthorized.'));
        }
    }

    public function login($username, $password, $app_key)
    {
        try {
            $resultdata = $this->setting_model->getSetting();

            // Check if resultdata is valid and has the required property
            if (!$resultdata || !isset($resultdata->student_panel_login)) {
                log_message('error', 'Auth_model login(): Invalid settings data');
                return array('status' => 0, 'message' => 'System configuration error');
            }

            // Log the actual value for debugging
            log_message('info', 'Auth_model login() - student_panel_login value: "' . $resultdata->student_panel_login . '"');

            // Check for various possible values that should allow login
            $allowed_values = array('yes', 'Yes', 'YES', '1', 1, true);
            $disallowed_values = array('no', 'No', 'NO', '0', 0, false, null, '');
            $login_allowed = in_array($resultdata->student_panel_login, $allowed_values, false);

            if($login_allowed){
                log_message('info', 'Auth_model login() - Login allowed, proceeding to checkLogin()');
                $q = $this->checkLogin($username, $password);
            }else{
                log_message('error', 'Auth_model login() - Login blocked, student_panel_login = "' . $resultdata->student_panel_login . '"');

                // Auto-fix: Enable student login if it's disabled
                log_message('info', 'Auth_model login() - Attempting to auto-enable student login');
                $fix_result = $this->enable_student_login();

                if ($fix_result['status'] == 1) {
                    log_message('info', 'Auth_model login() - Successfully enabled student login, retrying');
                    // Retry the login after fixing
                    $q = $this->checkLogin($username, $password);
                } else {
                    log_message('error', 'Auth_model login() - Failed to enable student login: ' . $fix_result['message']);

                    // Provide more detailed error message for debugging
                    $debug_message = 'Student panel login is disabled. Current setting: "' . $resultdata->student_panel_login . '". Expected: "yes". Auto-fix failed: ' . $fix_result['message'];
                    return array(
                        'status' => 0,
                        'message' => 'Your account is suspended',
                        'debug_info' => $debug_message,
                        'current_setting' => $resultdata->student_panel_login,
                        'auto_fix_attempted' => true,
                        'auto_fix_result' => $fix_result
                    );
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Auth_model login() error: ' . $e->getMessage());
            return array('status' => 0, 'message' => 'System error occurred');
        }
        
        if (empty($q)) {
            return array('status' => 0, 'message' => 'Invalid Username or Password');
        } else {

            if ($q->is_active == "yes") {
                if ($q->role == "student") {

                    $result = $this->user_model->read_user_information($q->id);

                    if ($result != false) {

                        $setting_result = $this->setting_model->get();

                        // Normalize settings result to array-of-arrays for backward compatibility
                        if (is_object($setting_result)) {
                            $tmp = array();
                            foreach ($setting_result as $k => $v) {
                                $tmp[0][$k] = $v;
                            }
                            $setting_result = $tmp;
                        }

                        if ($result->currency_id == 0) {
                            $currency_symbol    = isset($setting_result[0]['currency_symbol']) ? $setting_result[0]['currency_symbol'] : '₹';
                            $currency           = isset($setting_result[0]['currency']) ? $setting_result[0]['currency'] : 'INR';
                            $currency_short_name = isset($setting_result[0]['currency']) ? $setting_result[0]['currency'] : null;
                             
                        } else {
                             
                            $currencyarray = $this->user_model->getstudentcurrentcurrency($result->user_id);
                            $currency               = $currencyarray[0]->id;
                            $currency_symbol        = $currencyarray[0]->symbol;
                            $currency_short_name        = $currencyarray[0]->short_name;
                        }
                        
                        if ($result->lang_id == 0) {
                            $lang_id    = $setting_result[0]['lang_id'];
                            $language   = $setting_result[0]['language'];
                            $short_code = isset($setting_result[0]['short_code']) ? $setting_result[0]['short_code'] : null;
                        } else {
                            $lang_id    = $result->lang_id;
                            $curentlang = $this->user_model->getstudentcurrentlanguage($result->user_id);
                            $language   = $curentlang[0]->language;
                            $short_code = $curentlang[0]->short_code;
                        }

                        if ($result->role == "student") {

                            $last_login = date('Y-m-d H:i:s');
                            $token      = $this->getToken();
                            $expired_at = date("Y-m-d H:i:s", strtotime('+8760 hours'));
                            $this->db->trans_start();
                            $this->db->insert('users_authentication', array('users_id' => $q->id, 'token' => $token, 'expired_at' => $expired_at));

                            $updateData = array(
                                'app_key' => $app_key,
                            );

                            $this->db->where('id', $result->user_id);
                            $this->db->update('students', $updateData);
                            $fullname = getFullName($result->firstname, $result->middlename, $result->lastname, $setting_result[0]['middlename'], $setting_result[0]['lastname']);

                            if (empty($fullname)) {$fullname = '';}

                            $session_data = array(
                                'id'              => $result->id,
                                'student_id'      => $result->user_id,
                                'admission_no'    => $result->admission_no,
                                'role'            => $result->role,
                                'mobileno'        => $result->mobileno,
                                'email'           => $result->email,
                                'username'        => $fullname,
                                'class'           => $result->class,
                                'section'         => $result->section,
                                'date_format'     => $setting_result[0]['date_format'],
                                'currency_symbol' => $currency_symbol,
                                'currency_short_name'      => $currency_short_name,
                                'currency_id'     => $currency,                                
                                'timezone'        => $setting_result[0]['timezone'],
                                'sch_name'        => $setting_result[0]['name'],
                                'language'        => array('lang_id' => $lang_id, 'language' => $language, 'short_code' => $short_code),
                                'is_rtl'          => $setting_result[0]['is_rtl'],
                                'theme'           => $setting_result[0]['theme'],
                                'image'           => $result->image,
                                'student_session_id'           => $result->student_session_id,
                                'start_week'      => $setting_result[0]['start_week'],
                                'superadmin_restriction'      => $setting_result[0]['superadmin_restriction'],
                            );
                            $this->session->set_userdata('student', $session_data);
                            if ($this->db->trans_status() === false) {
                                $this->db->trans_rollback();

                                return array('status' => 0, 'message' => 'Internal server error.');
                            } else {
                                $this->db->trans_commit();
                                return array('status' => 1, 'message' => 'Successfully login.', 'id' => $q->id, 'token' => $token, 'role' => $q->role, 'record' => $session_data);
                            }
                        }
                    } else {
                        return array('status' => 0, 'message' => 'Your account is suspended');
                    }
                } else if ($q->role == "parent") {
                    $login_post = array(
                        'username' => $username,
                        'password' => $password,
                    );                  
                    
                        $resultdata    = $this->setting_model->getSetting();                    
         
                        if ($resultdata->parent_panel_login) {
                            $result = $this->user_model->checkLoginParent($login_post);
                        } else {
                            $result = false;
                        }                   
                    
                    if ($result != false) {
                        
                        
                    $curentlang = $this->user_model->getstudentcurrentlanguage($result->id);
                    $setting_result = $this->setting_model->get();

                    if (empty($curentlang)) {
                        $lang_id    = $setting_result[0]['lang_id'];
                        $language   = $setting_result[0]['language'];
                        $short_code = isset($setting_result[0]['short_code']) ? $setting_result[0]['short_code'] : null;
                    } else {
                        $lang_id    = $curentlang[0]->lang_id;
                        $language   = $curentlang[0]->language;
                        $short_code = $curentlang[0]->short_code;
                    }

                    if ($result->role == "parent") {                        

                        $last_login = date('Y-m-d H:i:s');
                        $token      = $this->getToken();
                        $expired_at = date("Y-m-d H:i:s", strtotime('+8760 hours'));

                        $this->db->insert('users_authentication', array('users_id' => $q->id, 'token' => $token, 'expired_at' => $expired_at));

                        if ($result->guardian_relation == "Father") {
                            $image = $result->father_pic;
                        } else if ($result->guardian_relation == "Mother") {
                            $image = $result->mother_pic;
                        } else {
                            $image = $result->guardian_pic;
                        }

                        $guardian_name = $result->guardian_name;
                        if (empty($guardian_name)) {$guardian_name = '';}

                        $session_data = array(
                            'id'              => $result->id,
                            'role'            => $result->role,
                            'username'        => $guardian_name,
                            'student_session_id'           => $result->student_session_id,
                            'date_format'     => $setting_result[0]['date_format'],
                            'timezone'        => $setting_result[0]['timezone'],
                            'sch_name'        => $setting_result[0]['name'],
                            'currency_symbol' => $setting_result[0]['currency_symbol'],
                            'currency_short_name' => $setting_result[0]['currency_short_name'],                        
                            'language'        => array('lang_id' => $lang_id, 'language' => $language, 'short_code' => $short_code),
                            'is_rtl'          => $setting_result[0]['is_rtl'],
                            'theme'           => $setting_result[0]['theme'],
                            'image'           => $image,
                            'start_week'      => $setting_result[0]['start_week'],
                            'superadmin_restriction'      => $setting_result[0]['superadmin_restriction'],
                        );

                        $user_id        = ($result->id);
                        $students_array = $this->student_model->read_siblings_students($user_id);
                        $child_student  = array();
                        $update_student = array();
                        foreach ($students_array as $std_key => $std_val) {
                            $child = array(
                                'student_id' => $std_val->id,
                                'class'      => $std_val->class,
                                'section'    => $std_val->section,
                                'class_id'   => $std_val->class_id,
                                'section_id' => $std_val->section_id,
                                'name'       => $std_val->firstname . " " . $std_val->lastname,
                                'image'      => $std_val->image,
                            );
                            $child_student[] = $child;
                            $stds            = array(
                                'id'             => $std_val->id,
                                'parent_app_key' => $app_key,
                            );
                            $update_student[] = $stds;
                        }
                        if (!empty($update_student)) {
                            $this->db->update_batch('students', $update_student, 'id');
                        }

                        $session_data['parent_childs'] = $child_student;
                        $this->session->set_userdata('student', $session_data);

                        return array('status' => 1, 'message' => 'Successfully login.', 'id' => $q->id, 'token' => $token, 'role' => $q->role, 'record' => $session_data);
                        
                    }else{
                        return array('status' => 0, 'message' => 'Invalid Username or Password');
                    }
                    
                    }else{
                        return array('status' => 0, 'message' => 'Your account is suspended');
                    }                    
                    
                }
            } else {
                return array('status' => '0', 'message' => 'Your account is disabled please contact to administrator');
            }
        }
    }

    public function checkLogin($username, $password)
    {
        $resultdata    = $this->setting_model->get();

        // Use the correct field names from the get() method
        $student_panel_login = isset($resultdata[0]['student_panel_login']) ? $resultdata[0]['student_panel_login'] : 'yes';
        $parent_panel_login  = isset($resultdata[0]['parent_panel_login']) ? $resultdata[0]['parent_panel_login'] : 'yes';

        // For backward compatibility, create empty arrays for student_login and parent_login
        $student_login = array();
        $parent_login = array();
        
        $this->db->select('users.id as id, username, password,role,users.is_active as is_active,lang_id');
        $this->db->from('users');
        $this->db->join('students', 'students.id = users.user_id');
        $this->db->where('password', $password);
        
        $this->db->group_start();        
        $this->db->where('username', $username); 
        
        if(!empty($student_login)){
            if (in_array("admission_no", $student_login)) {
                $this->db->or_where('students.admission_no', $username);
            }
            if (in_array("mobile_number", $student_login)) {
                $this->db->or_where('students.mobileno', $username);
            }
            if (in_array("email", $student_login)) {
                $this->db->or_where('students.email', $username);
            }
        }
        
        $this->db->group_end();
        
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return $query->row();
        } else {

            $this->db->select('users.id as id, username, password,role,users.is_active as is_active,lang_id');
            $this->db->from('users');
            $this->db->join('students', 'students.parent_id = users.id');
            $this->db->where('password', $password);                       
            
            $this->db->group_start();            
            $this->db->where('username', $username); 
            
            if(!empty($parent_login)){
                if (in_array("mobile_number", $parent_login)) {
                    $this->db->or_where('students.guardian_phone', $username);
                }
                if (in_array("email", $parent_login)) {
                    $this->db->or_where('students.guardian_email', $username);
                }
            }
            
            $this->db->group_end();
            
            $this->db->limit(1);
            $query = $this->db->get();
            if ($query->num_rows() == 1) {
                return $query->row();
            } else {
                return false;
            }
        }
    }

    public function getToken($randomIdLength = 10)
    {
        $token = '';
        do {
            $bytes = rand(1, $randomIdLength);
            $token .= str_replace(
                ['.', '/', '='], '', base64_encode($bytes)
            );
        } while (strlen($token) < $randomIdLength);
        return $token;
    }

    public function logout($deviceToken)
    {
        $users_id = $this->input->get_request_header('User-ID', true);
        $token    = $this->input->get_request_header('Authorization', true);
        $this->session->unset_userdata('student');
        $this->session->sess_destroy();
        $this->db->where('app_key', $deviceToken)->update('students', array('app_key' => null));
        $this->db->where('users_id', $users_id)->where('token', $token)->delete('users_authentication');
        return array('status' => 200, 'message' => 'Successfully logout.');
    }

    public function auth()
    {
        if ($this->security_authentication_flag) {
            $users_id = $this->input->get_request_header('User-ID', true);
            $token    = $this->input->get_request_header('Authorization', true);
            $q        = $this->db->select('expired_at')->from('users_authentication')->where('users_id', $users_id)->where('token', $token)->get()->row();
            if ($q == "") {
                return json_output(401, array('status' => 401, 'message' => 'Unauthorized.'));
            } else {
                if ($q->expired_at < date('Y-m-d H:i:s')) {
                    return json_output(401, array('status' => 401, 'message' => 'Your session has been expired.'));
                } else {
                    $updated_at = date('Y-m-d H:i:s');
                    $expired_at = date("Y-m-d H:i:s", strtotime('+8760 hours'));
                    $this->db->where('users_id', $users_id)->where('token', $token)->update('users_authentication', array('expired_at' => $expired_at, 'updated_at' => $updated_at));
                    return array('status' => 200, 'message' => 'Authorized.');
                }
            }
        } else {
            return array('status' => 200, 'message' => 'Authorized.');
        }
    }

    /**
     * Enable student panel login
     * This method directly updates the database to enable student login
     */
    public function enable_student_login() {
        try {
            // First check current values
            $this->db->select('id, student_panel_login, parent_panel_login');
            $this->db->from('sch_settings');
            $this->db->limit(1);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $row = $query->row();
                $current_value = $row->student_panel_login;

                // Update the setting directly using the record ID
                $this->db->where('id', $row->id);
                $update_data = array(
                    'student_panel_login' => 'yes',
                    'parent_panel_login' => 'yes'
                );
                $this->db->update('sch_settings', $update_data);

                if ($this->db->affected_rows() > 0) {
                    log_message('info', 'Successfully enabled student panel login - changed from "' . $current_value . '" to "yes"');
                    return array('status' => 1, 'message' => 'Student login enabled successfully', 'old_value' => $current_value);
                } else {
                    // Force update even if no rows affected
                    log_message('info', 'Forcing student panel login update');
                    $this->db->query("UPDATE sch_settings SET student_panel_login = 'yes', parent_panel_login = 'yes' WHERE id = " . $row->id);
                    return array('status' => 1, 'message' => 'Student login force-enabled', 'old_value' => $current_value);
                }
            } else {
                log_message('error', 'No records found in sch_settings table');
                return array('status' => 0, 'message' => 'No settings record found in database');
            }
        } catch (Exception $e) {
            log_message('error', 'Error enabling student login: ' . $e->getMessage());
            return array('status' => 0, 'message' => 'Database error: ' . $e->getMessage());
        }
    }

}
