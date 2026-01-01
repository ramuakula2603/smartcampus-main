<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Income_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Search income group report
     * Returns income records grouped by income head for a date range
     * 
     * @param string $start_date Start date (Y-m-d format)
     * @param string $end_date End date (Y-m-d format)
     * @param int $head_id Optional income head ID filter
     * @return array
     */
    public function searchincomegroup($start_date, $end_date, $head_id = null)
    {
        $this->db->select('income.id, income.name, income.invoice_no, income.date, income.amount, income_head.income_category, income_head.id as head_id, income.note, income.documents');
        $this->db->from('income');
        $this->db->join('income_head', 'income.income_head_id = income_head.id');
        $this->db->where('income.date >=', $start_date);
        $this->db->where('income.date <=', $end_date);
        
        if ($head_id != null && $head_id !== '') {
            $this->db->where('income.income_head_id', $head_id);
        }
        
        $this->db->order_by('income.income_head_id', 'desc');
        $this->db->order_by('income.date', 'desc');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get income summary grouped by income head
     * 
     * @param string $start_date Start date (Y-m-d format)
     * @param string $end_date End date (Y-m-d format)
     * @param int $head_id Optional income head ID filter
     * @return array
     */
    public function getIncomeSummaryByHead($start_date, $end_date, $head_id = null)
    {
        $this->db->select('income_head.id as head_id, income_head.income_category, COUNT(income.id) as income_count, SUM(income.amount) as total_amount');
        $this->db->from('income_head');
        $this->db->join('income', 'income_head.id = income.income_head_id', 'left');
        $this->db->where('income.date >=', $start_date);
        $this->db->where('income.date <=', $end_date);
        
        if ($head_id != null && $head_id !== '') {
            $this->db->where('income_head.id', $head_id);
        }
        
        $this->db->group_by('income_head.id');
        $this->db->order_by('total_amount', 'desc');
        
        $query = $this->db->get();
        return $query->result_array();
    }

}

