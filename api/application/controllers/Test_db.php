<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Test_db extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('json_output');
    }

    /**
     * Test database connection
     */
    public function index()
    {
        try {
            // Test basic database connection
            $query = $this->db->query("SELECT 1 as test");
            $result = $query->row();
            
            if ($result && $result->test == 1) {
                json_output(200, array(
                    'status' => 1,
                    'message' => 'Database connection successful',
                    'database' => $this->db->database
                ));
            } else {
                json_output(500, array(
                    'status' => 0,
                    'message' => 'Database connection failed'
                ));
            }
        } catch (Exception $e) {
            json_output(500, array(
                'status' => 0,
                'message' => 'Database error: ' . $e->getMessage()
            ));
        }
    }

    /**
     * Test staff table
     */
    public function test_staff()
    {
        try {
            $this->db->select('COUNT(*) as count');
            $this->db->from('staff');
            $query = $this->db->get();
            $result = $query->row();
            
            json_output(200, array(
                'status' => 1,
                'message' => 'Staff table accessible',
                'staff_count' => $result->count
            ));
        } catch (Exception $e) {
            json_output(500, array(
                'status' => 0,
                'message' => 'Staff table error: ' . $e->getMessage()
            ));
        }
    }

    /**
     * Test settings table
     */
    public function test_settings()
    {
        try {
            $this->db->select('name, email');
            $this->db->from('sch_settings');
            $this->db->limit(1);
            $query = $this->db->get();
            $result = $query->row();
            
            json_output(200, array(
                'status' => 1,
                'message' => 'Settings table accessible',
                'school_name' => $result ? $result->name : 'No data'
            ));
        } catch (Exception $e) {
            json_output(500, array(
                'status' => 0,
                'message' => 'Settings table error: ' . $e->getMessage()
            ));
        }
    }

    /**
     * Test authentication tables
     */
    public function test_auth_tables()
    {
        try {
            $tables = array('staff', 'users_authentication', 'roles', 'staff_roles');
            $results = array();
            
            foreach ($tables as $table) {
                $this->db->select('COUNT(*) as count');
                $this->db->from($table);
                $query = $this->db->get();
                $result = $query->row();
                $results[$table] = $result->count;
            }
            
            json_output(200, array(
                'status' => 1,
                'message' => 'Authentication tables accessible',
                'table_counts' => $results
            ));
        } catch (Exception $e) {
            json_output(500, array(
                'status' => 0,
                'message' => 'Authentication tables error: ' . $e->getMessage()
            ));
        }
    }
}
