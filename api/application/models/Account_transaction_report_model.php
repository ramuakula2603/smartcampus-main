<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Account_transaction_report_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get account transactions report by date range
     * 
     * @param string $start_date Start date (Y-m-d format)
     * @param string $end_date End date (Y-m-d format)
     * @param array $filters Additional filters (account_id, is_active)
     * @return array List of account transactions
     */
    public function get_transactions_report($start_date = null, $end_date = null, $filters = array())
    {
        $this->db->select('accounttranscations.*, 
                           from_account.name as from_account_name,
                           from_account.code as from_account_number,
                           to_account.name as to_account_name,
                           to_account.code as to_account_number');
        $this->db->from('accounttranscations');
        $this->db->join('addaccount as from_account', 'from_account.id = accounttranscations.fromaccountid', 'left');
        $this->db->join('addaccount as to_account', 'to_account.id = accounttranscations.toaccountid', 'left');
        
        // Apply date filters
        if ($start_date != null && $end_date != null) {
            $this->db->where('accounttranscations.date >=', $start_date);
            $this->db->where('accounttranscations.date <=', $end_date);
        }
        
        // Apply additional filters
        if (isset($filters['from_account_id']) && !empty($filters['from_account_id'])) {
            $this->db->where('accounttranscations.fromaccountid', $filters['from_account_id']);
        }
        
        if (isset($filters['to_account_id']) && !empty($filters['to_account_id'])) {
            $this->db->where('accounttranscations.toaccountid', $filters['to_account_id']);
        }
        
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $this->db->where('accounttranscations.is_active', $filters['is_active']);
        }
        
        $this->db->order_by('accounttranscations.date', 'DESC');
        $this->db->order_by('accounttranscations.id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get all accounts list
     * 
     * @return array List of accounts
     */
    public function get_accounts()
    {
        $this->db->select('accountcategory.name as account_category_name, accounttype.type as account_type, addaccount.*');
        $this->db->from('addaccount');
        $this->db->join('accounttype', 'accounttype.id = addaccount.account_type', 'left');
        $this->db->join('accountcategory', 'accountcategory.id = addaccount.account_category', 'left');
        $this->db->order_by('addaccount.name', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get financial years list
     * 
     * @return array List of financial years
     */
    public function get_financial_years()
    {
        $this->db->select('*');
        $this->db->from('financialyear');
        $this->db->order_by('year_id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get active financial year
     * 
     * @return array|null Active financial year data
     */
    public function get_active_financial_year()
    {
        $this->db->select('*');
        $this->db->from('financialyear');
        $this->db->where('is_active', 1);
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Get transaction summary by date range
     * 
     * @param string $start_date Start date (Y-m-d format)
     * @param string $end_date End date (Y-m-d format)
     * @return array Summary data (total_amount, transaction_count, etc.)
     */
    public function get_transaction_summary($start_date = null, $end_date = null)
    {
        $this->db->select('COUNT(*) as total_transactions, SUM(amount) as total_amount');
        $this->db->from('accounttranscations');
        
        if ($start_date != null && $end_date != null) {
            $this->db->where('date >=', $start_date);
            $this->db->where('date <=', $end_date);
        }
        
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Get a specific transaction
     * 
     * @param int $id Transaction ID
     * @return array|null Transaction data
     */
    public function get_transaction($id)
    {
        $this->db->select('accounttranscations.*, 
                           from_account.name as from_account_name,
                           from_account.code as from_account_number,
                           to_account.name as to_account_name,
                           to_account.code as to_account_number');
        $this->db->from('accounttranscations');
        $this->db->join('addaccount as from_account', 'from_account.id = accounttranscations.fromaccountid', 'left');
        $this->db->join('addaccount as to_account', 'to_account.id = accounttranscations.toaccountid', 'left');
        $this->db->where('accounttranscations.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Check if transaction exists
     * 
     * @param int $id Transaction ID
     * @return bool True if exists, false otherwise
     */
    public function transaction_exists($id)
    {
        $this->db->select('id');
        $this->db->from('accounttranscations');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    /**
     * Delete transaction
     * 
     * @param int $id Transaction ID
     * @return bool True on success, false on failure
     */
    public function delete_transaction($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('accounttranscations');
    }
}

