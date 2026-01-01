<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Debug_auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->helper('json_output');
    }

    /**
     * Debug student login settings
     * GET /debug_auth/check_settings
     */
    public function check_settings()
    {
        try {
            $debug_info = array();
            
            // 1. Check getSetting() method
            $debug_info['step1'] = 'Testing getSetting() method';
            $settings = $this->setting_model->getSetting();
            
            if ($settings) {
                $debug_info['getSetting_success'] = true;
                $debug_info['student_panel_login'] = array(
                    'value' => isset($settings->student_panel_login) ? $settings->student_panel_login : 'NOT SET',
                    'type' => isset($settings->student_panel_login) ? gettype($settings->student_panel_login) : 'N/A',
                    'equals_yes' => isset($settings->student_panel_login) ? ($settings->student_panel_login == 'yes') : false
                );
            } else {
                $debug_info['getSetting_success'] = false;
                $debug_info['getSetting_error'] = 'Method returned null';
            }
            
            // 2. Test the exact Auth_model logic
            $debug_info['step2'] = 'Testing Auth_model logic';
            
            $resultdata = $this->setting_model->getSetting();
            
            if (!$resultdata || !isset($resultdata->student_panel_login)) {
                $debug_info['auth_logic_result'] = 'System configuration error';
            } else {
                if($resultdata->student_panel_login == 'yes'){
                    $debug_info['auth_logic_result'] = 'Would proceed to checkLogin()';
                } else {
                    $debug_info['auth_logic_result'] = 'Would return "Your account is suspended"';
                    $debug_info['auth_logic_reason'] = 'student_panel_login is "' . $resultdata->student_panel_login . '", not "yes"';
                }
            }
            
            // 3. Direct database check
            $debug_info['step3'] = 'Direct database check';
            $this->db->select('id, student_panel_login, parent_panel_login');
            $this->db->from('sch_settings');
            $this->db->limit(1);
            $query = $this->db->get();
            
            if ($query->num_rows() > 0) {
                $row = $query->row();
                $debug_info['direct_db_check'] = array(
                    'success' => true,
                    'record_id' => $row->id,
                    'student_panel_login' => $row->student_panel_login,
                    'parent_panel_login' => isset($row->parent_panel_login) ? $row->parent_panel_login : 'NOT SET'
                );
            } else {
                $debug_info['direct_db_check'] = array(
                    'success' => false,
                    'error' => 'No records found in sch_settings'
                );
            }
            
            json_output(200, array(
                'status' => 1,
                'message' => 'Debug information collected',
                'debug_info' => $debug_info,
                'timestamp' => date('Y-m-d H:i:s')
            ));
            
        } catch (Exception $e) {
            json_output(500, array(
                'status' => 0,
                'message' => 'Debug error: ' . $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ));
        }
    }

    /**
     * Fix student login settings
     * POST /debug_auth/fix_settings
     */
    public function fix_settings()
    {
        try {
            $result = $this->setting_model->check_and_fix_student_login();
            
            json_output(200, array(
                'status' => $result['status'],
                'message' => $result['message'],
                'details' => isset($result['old_value']) ? 'Changed from "' . $result['old_value'] . '" to "yes"' : null,
                'timestamp' => date('Y-m-d H:i:s')
            ));
            
        } catch (Exception $e) {
            json_output(500, array(
                'status' => 0,
                'message' => 'Fix error: ' . $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ));
        }
    }

    /**
     * Test authentication after fix
     * POST /debug_auth/test_auth
     */
    public function test_auth()
    {
        try {
            // Test the exact same logic as Auth_model->login()
            $resultdata = $this->setting_model->getSetting();
            
            $test_result = array();
            
            if (!$resultdata || !isset($resultdata->student_panel_login)) {
                $test_result['result'] = 'System configuration error';
                $test_result['would_return'] = array('status' => 0, 'message' => 'System configuration error');
            } else {
                $test_result['student_panel_login_value'] = $resultdata->student_panel_login;
                
                if($resultdata->student_panel_login == 'yes'){
                    $test_result['result'] = 'SUCCESS - Would proceed to checkLogin()';
                    $test_result['would_return'] = 'Would call checkLogin() method';
                } else {
                    $test_result['result'] = 'FAIL - Would return account suspended';
                    $test_result['would_return'] = array('status' => 0, 'message' => 'Your account is suspended');
                }
            }
            
            json_output(200, array(
                'status' => 1,
                'message' => 'Authentication test completed',
                'test_result' => $test_result,
                'timestamp' => date('Y-m-d H:i:s')
            ));
            
        } catch (Exception $e) {
            json_output(500, array(
                'status' => 0,
                'message' => 'Test error: ' . $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ));
        }
    }

    /**
     * Enable student login directly
     * POST /debug_auth/enable_login
     */
    public function enable_login()
    {
        try {
            $result = $this->auth_model->enable_student_login();

            json_output(200, array(
                'status' => $result['status'],
                'message' => $result['message'],
                'timestamp' => date('Y-m-d H:i:s')
            ));

        } catch (Exception $e) {
            json_output(500, array(
                'status' => 0,
                'message' => 'Enable login error: ' . $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ));
        }
    }
}
