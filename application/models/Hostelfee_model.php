<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Hostelfee_model extends MY_Model
{

    public function add($insert_data, $update_data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (!empty($insert_data)) {
            $this->db->insert_batch('hostel_feemaster', $insert_data);
        }
        if (!empty($update_data)) {
            $this->db->update_batch('hostel_feemaster', $update_data, 'id');
        }

        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }

    }

    public function getSessionFees($session_id)
    {
        $data = $this->db->select('*')->from('hostel_feemaster')->where('session_id', $session_id)->get()->result_array();
        return $data;
    }

    public function hostelfesstype($session_id, $month)
    {
        $data = $this->db->select('hostel_feemaster.*,hostel_feemaster.month as type,"Fees" as code')->from('hostel_feemaster')->where('month', $month)->where('session_id', $session_id)->get()->result();
        
        if (!empty($data)) {
            return $data[0];
        } else {
            // Return a default object structure when no data is found
            $default = new stdClass();
            $default->id = null;
            $default->month = $month;
            $default->due_date = null;
            $default->fine_type = null;
            $default->fine_percentage = null;
            $default->fine_amount = null;
            $default->session_id = $session_id;
            $default->type = $month;
            $default->code = "Fees";
            return $default;
        }
    }

    /**
     * Get hostel fee master by ID
     * @param int $id
     * @return object
     */
    public function get($id = null)
    {
        $this->db->select('*')->from('hostel_feemaster');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('id', 'DESC');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row();
        } else {
            return $query->result();
        }
    }

    /**
     * Get all hostel fee masters for a session
     * @param int $session_id
     * @return array
     */
    public function getBySession($session_id)
    {
        $this->db->select('*')
                 ->from('hostel_feemaster')
                 ->where('session_id', $session_id)
                 ->order_by('month', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Delete hostel fee master
     * @param int $id
     * @return bool
     */
    public function remove($id)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        $this->db->where('id', $id);
        $this->db->delete('hostel_feemaster');
        
        $message = DELETE_RECORD_CONSTANT . " On hostel feemaster id " . $id;
        $action = "Delete";
        $this->log($message, $id, $action);

        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    /**
     * Get hostel fee master by month and session
     * @param int $session_id
     * @param string $month
     * @return object
     */
    public function getByMonthAndSession($session_id, $month)
    {
        $this->db->select('*')
                 ->from('hostel_feemaster')
                 ->where('session_id', $session_id)
                 ->where('month', $month);
        return $this->db->get()->row();
    }

    /**
     * Check if hostel fee master exists for session and month
     * @param int $session_id
     * @param string $month
     * @return bool
     */
    public function exists($session_id, $month)
    {
        $this->db->select('COUNT(*) as count')
                 ->from('hostel_feemaster')
                 ->where('session_id', $session_id)
                 ->where('month', $month);
        $result = $this->db->get()->row();
        return ($result && $result->count > 0);
    }

    /**
     * Get months with hostel fees configured for a session
     * @param int $session_id
     * @return array
     */
    public function getConfiguredMonths($session_id)
    {
        $this->db->select('month')
                 ->from('hostel_feemaster')
                 ->where('session_id', $session_id)
                 ->order_by('id', 'ASC');
        $result = $this->db->get()->result();
        
        $months = array();
        foreach ($result as $row) {
            $months[] = $row->month;
        }
        return $months;
    }

    /**
     * Get hostel fee statistics for a session
     * @param int $session_id
     * @return object
     */
    public function getSessionStats($session_id)
    {
        $this->db->select('COUNT(*) as total_months, 
                          SUM(CASE WHEN fine_type != "" AND fine_type IS NOT NULL THEN 1 ELSE 0 END) as months_with_fine,
                          AVG(CASE WHEN fine_type = "percentage" THEN fine_percentage ELSE 0 END) as avg_fine_percentage,
                          AVG(CASE WHEN fine_type = "fix" THEN fine_amount ELSE 0 END) as avg_fine_amount')
                 ->from('hostel_feemaster')
                 ->where('session_id', $session_id);
        return $this->db->get()->row();
    }
}
