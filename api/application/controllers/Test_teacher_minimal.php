<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Test_teacher_minimal extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('json_output');
    }

    /**
     * Test minimal teacher endpoint
     */
    public function index()
    {
        try {
            $response = array(
                'status' => 1,
                'message' => 'Minimal teacher test successful',
                'timestamp' => date('Y-m-d H:i:s')
            );

            json_output(200, $response);

        } catch (Exception $e) {
            json_output(500, array(
                'status' => 500,
                'message' => 'Error: ' . $e->getMessage()
            ));
        }
    }

    /**
     * Test with teacher auth model loading
     */
    public function test_auth_model()
    {
        try {
            $this->load->model('teacher_auth_model');
            
            $response = array(
                'status' => 1,
                'message' => 'Teacher auth model loaded successfully',
                'timestamp' => date('Y-m-d H:i:s')
            );

            json_output(200, $response);

        } catch (Exception $e) {
            json_output(500, array(
                'status' => 500,
                'message' => 'Auth model error: ' . $e->getMessage()
            ));
        }
    }

    /**
     * Test client authentication
     */
    public function test_client_auth()
    {
        try {
            $this->load->model('teacher_auth_model');

            $check_auth_client = $this->teacher_auth_model->check_auth_client();

            $response = array(
                'status' => 1,
                'message' => 'Client auth test completed',
                'client_auth_result' => $check_auth_client,
                'timestamp' => date('Y-m-d H:i:s')
            );

            json_output(200, $response);

        } catch (Exception $e) {
            json_output(500, array(
                'status' => 500,
                'message' => 'Client auth error: ' . $e->getMessage()
            ));
        }
    }

    /**
     * Test setting model
     */
    public function test_setting_model()
    {
        try {
            $this->load->model('setting_model');

            $setting = $this->setting_model->getSchoolDetail();

            $response = array(
                'status' => 1,
                'message' => 'Setting model test completed',
                'has_setting' => !empty($setting),
                'timezone' => isset($setting->timezone) ? $setting->timezone : 'not set',
                'timestamp' => date('Y-m-d H:i:s')
            );

            json_output(200, $response);

        } catch (Exception $e) {
            json_output(500, array(
                'status' => 500,
                'message' => 'Setting model error: ' . $e->getMessage()
            ));
        }
    }

    /**
     * Test loading libraries
     */
    public function test_libraries()
    {
        try {
            $results = array();

            // Test customlib
            try {
                $this->load->library('customlib');
                $results['customlib'] = 'loaded';
            } catch (Exception $e) {
                $results['customlib'] = 'error: ' . $e->getMessage();
            }

            // Test teacher_middleware
            try {
                $this->load->library('teacher_middleware');
                $results['teacher_middleware'] = 'loaded';
            } catch (Exception $e) {
                $results['teacher_middleware'] = 'error: ' . $e->getMessage();
            }

            $response = array(
                'status' => 1,
                'message' => 'Library test completed',
                'results' => $results,
                'timestamp' => date('Y-m-d H:i:s')
            );

            json_output(200, $response);

        } catch (Exception $e) {
            json_output(500, array(
                'status' => 500,
                'message' => 'Library test error: ' . $e->getMessage()
            ));
        }
    }
}
