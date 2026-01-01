<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Expense_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Search expenses
     * Returns expense records for a date range
     * 
     * @param string $text Optional search text
     * @param string $start_date Start date (Y-m-d format)
     * @param string $end_date End date (Y-m-d format)
     * @return array
     */
    public function search($text = null, $start_date = null, $end_date = null)
    {
        $this->db->select('expenses.id, expenses.date, expenses.invoice_no, expenses.name, expenses.amount, expenses.documents, expenses.note, expense_head.exp_category, expenses.exp_head_id');
        $this->db->from('expenses');
        $this->db->join('expense_head', 'expenses.exp_head_id = expense_head.id');
        
        if (!empty($text)) {
            $this->db->like('expenses.name', $text);
        }
        
        if ($start_date != null && $end_date != null) {
            $this->db->where('expenses.date >=', $start_date);
            $this->db->where('expenses.date <=', $end_date);
        }
        
        $this->db->order_by('expenses.date', 'desc');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get expense summary grouped by expense head
     * 
     * @param string $start_date Start date (Y-m-d format)
     * @param string $end_date End date (Y-m-d format)
     * @return array
     */
    public function getExpenseSummaryByHead($start_date, $end_date)
    {
        $this->db->select('expense_head.id as head_id, expense_head.exp_category, COUNT(expenses.id) as expense_count, SUM(expenses.amount) as total_amount');
        $this->db->from('expense_head');
        $this->db->join('expenses', 'expense_head.id = expenses.exp_head_id', 'left');
        $this->db->where('expenses.date >=', $start_date);
        $this->db->where('expenses.date <=', $end_date);
        $this->db->group_by('expense_head.id');
        $this->db->order_by('total_amount', 'desc');
        
        $query = $this->db->get();
        return $query->result_array();
    }

}

