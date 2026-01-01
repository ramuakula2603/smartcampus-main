<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * School House Model
 * 
 * This model handles CRUD operations for school house records.
 * 
 * @package    Student Management System
 * @subpackage API Models
 * @category   Models
 * @author     School Management System
 * @version    1.0.0
 */
class Schoolhouse_model extends CI_Model
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get school house records
     * 
     * @param int|null $id Optional house ID to get specific record
     * @return array|null Array of house records or single record
     */
    public function get($id = null)
    {
        try {
            if (!empty($id)) {
                $query = $this->db->where("id", $id)->get("school_houses");
                return $query->row_array();
            } else {
                $query = $this->db->get("school_houses");
                return $query->result_array();
            }
        } catch (Exception $e) {
            log_message('error', 'Schoolhouse_model get() error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Add or update school house record
     * 
     * @param array $data House data to insert or update
     * @return bool True on success, false on failure
     */
    public function add($data)
    {
        try {
            $this->db->trans_start();
            $this->db->trans_strict(false);

            if (isset($data["id"]) && !empty($data["id"])) {
                // Update existing record
                $this->db->where("id", $data["id"])->update("school_houses", $data);
                $message = "Updated school house record with ID " . $data["id"];
                $action = "Update";
                $record_id = $data["id"];
                $this->log_action($message, $record_id, $action);
            } else {
                // Insert new record
                $this->db->insert("school_houses", $data);
                $id = $this->db->insert_id();
                $message = "Inserted new school house record with ID " . $id;
                $action = "Insert";
                $record_id = $id;
                $this->log_action($message, $record_id, $action);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return false;
            } else {
                return true;
            }
        } catch (Exception $e) {
            log_message('error', 'Schoolhouse_model add() error: ' . $e->getMessage());
            $this->db->trans_rollback();
            return false;
        }
    }

    /**
     * Delete school house record
     * 
     * @param int $id House ID to delete
     * @return bool True on success, false on failure
     */
    public function delete($id)
    {
        try {
            $this->db->trans_start();
            $this->db->trans_strict(false);

            $this->db->where("id", $id)->delete("school_houses");
            $message = "Deleted school house record with ID " . $id;
            $action = "Delete";
            $record_id = $id;
            $this->log_action($message, $record_id, $action);

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return false;
            } else {
                return true;
            }
        } catch (Exception $e) {
            log_message('error', 'Schoolhouse_model delete() error: ' . $e->getMessage());
            $this->db->trans_rollback();
            return false;
        }
    }

    /**
     * Log action for audit trail
     * 
     * @param string $message Log message
     * @param int $record_id Record ID
     * @param string $action Action type (Insert, Update, Delete)
     */
    private function log_action($message, $record_id, $action)
    {
        try {
            // Simple logging - can be enhanced with audit table if needed
            log_message('info', "School House API - $action: $message");
        } catch (Exception $e) {
            log_message('error', 'Schoolhouse_model log_action() error: ' . $e->getMessage());
        }
    }

    /**
     * Check if school house table exists
     * 
     * @return bool True if table exists, false otherwise
     */
    public function table_exists()
    {
        try {
            return $this->db->table_exists('school_houses');
        } catch (Exception $e) {
            log_message('error', 'Schoolhouse_model table_exists() error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get table structure
     * 
     * @return array Table field information
     */
    public function get_table_structure()
    {
        try {
            return $this->db->list_fields('school_houses');
        } catch (Exception $e) {
            log_message('error', 'Schoolhouse_model get_table_structure() error: ' . $e->getMessage());
            return array();
        }
    }
}
