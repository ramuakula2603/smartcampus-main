<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Expensehead_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all expense heads
     * @param int $id Optional expense head ID
     * @return array
     */
    public function get($id = null)
    {
        $this->db->select()->from('expense_head');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    /**
     * Search expense group report
     * Returns expenses grouped by expense head for a date range
     * 
     * @param string $start_date Start date (Y-m-d format)
     * @param string $end_date End date (Y-m-d format)
     * @param int $head_id Optional expense head ID filter
     * @return array
     */
    public function searchexpensegroup($start_date, $end_date, $head_id = null)
    {
        $this->db->select('expenses.id, expenses.date, expenses.name, expenses.invoice_no, expenses.amount, expense_head.exp_category, expenses.exp_head_id, expenses.amount as total_amount, expenses.note, expenses.documents');
        $this->db->from('expenses');
        $this->db->join('expense_head', 'expenses.exp_head_id = expense_head.id');
        $this->db->where('expenses.date >=', $start_date);
        $this->db->where('expenses.date <=', $end_date);
        
        if ($head_id != null && $head_id !== '') {
            $this->db->where('expenses.exp_head_id', $head_id);
        }
        
        $this->db->order_by('expenses.exp_head_id', 'desc');
        $this->db->order_by('expenses.date', 'desc');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get expense summary grouped by expense head
     * 
     * @param string $start_date Start date (Y-m-d format)
     * @param string $end_date End date (Y-m-d format)
     * @param int $head_id Optional expense head ID filter
     * @return array
     */
    public function getExpenseSummaryByHead($start_date, $end_date, $head_id = null)
    {
        $this->db->select('expense_head.id as head_id, expense_head.exp_category, COUNT(expenses.id) as expense_count, SUM(expenses.amount) as total_amount');
        $this->db->from('expense_head');
        $this->db->join('expenses', 'expense_head.id = expenses.exp_head_id', 'left');
        $this->db->where('expenses.date >=', $start_date);
        $this->db->where('expenses.date <=', $end_date);
        
        if ($head_id != null && $head_id !== '') {
            $this->db->where('expense_head.id', $head_id);
        }
        
        $this->db->group_by('expense_head.id');
        $this->db->order_by('total_amount', 'desc');
        
        $query = $this->db->get();
        return $query->result_array();
    }

}

