<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Feesessiongroupadding_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Update additional fee amount
     * 
     * @param array $data
     * @return bool
     */
    public function updateadditionalfee($data)
    {
        // Validate required data
        if (empty($data['id']) || !isset($data['amount'])) {
            return false;
        }

        // Prepare data for update
        $update_data = array(
            'amount' => $data['amount']
        );

        // Update the record
        $this->db->where('id', $data['id']);
        $result = $this->db->update('student_fees_amountadding', $update_data);
        
        return $result;
    }
    
    /**
     * Get additional fee by ID
     * 
     * @param int $id
     * @return object|bool
     */
    public function get_additional_fee_by_id($id)
    {
        if (empty($id)) {
            return false;
        }
        
        $query = $this->db->get_where('student_fees_amountadding', array('id' => $id));
        return $query->row();
    }
}
