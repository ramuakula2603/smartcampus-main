<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Account_report_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get transactions sum for an account
     * 
     * @param int $account_id Account ID
     * @param string $status Transaction status ('debit' or 'credit')
     * @param string $start_date Start date (Y-m-d format)
     * @param string $end_date End date (Y-m-d format)
     * @return float|null Sum of transactions
     */
    public function get_transactions_sum($account_id, $status, $start_date, $end_date)
    {
        $this->db->select('SUM(accountreceipts.amount) AS total_amount');
        $this->db->from('accountreceipts');
        $this->db->where('accountreceipts.accountid', $account_id);
        $this->db->where('accountreceipts.status', $status);
        $this->db->where('accountreceipts.date >=', $start_date);
        $this->db->where('accountreceipts.date <=', $end_date);
        
        $query = $this->db->get();
        $row = $query->row_array();
        return $row['total_amount'];
    }

    /**
     * Get account transactions
     * 
     * @param int $account_id Account ID
     * @param string $start_date Start date (Y-m-d format)
     * @param string $end_date End date (Y-m-d format)
     * @param string $status Transaction status ('debit' or 'credit' or null for all)
     * @return array List of transactions
     */
    public function get_account_transactions($account_id, $start_date = null, $end_date = null, $status = null)
    {
        $this->db->select('accountreceipts.*');
        $this->db->from('accountreceipts');
        $this->db->where('accountreceipts.accountid', $account_id);
        
        if ($start_date != null && $end_date != null) {
            $this->db->where('accountreceipts.date >=', $start_date);
            $this->db->where('accountreceipts.date <=', $end_date);
        }
        
        if ($status == 'credit' || $status == 'debit') {
            $this->db->where('accountreceipts.status', $status);
        }
        
        $this->db->order_by('accountreceipts.date', 'ASC');
        $this->db->order_by('accountreceipts.id', 'ASC');
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
     * Get account report with opening/closing balances and daily transactions
     * 
     * @param int $account_id Account ID
     * @param string $start_date Start date (Y-m-d format)
     * @param string $end_date End date (Y-m-d format)
     * @return array Report data with balances and transactions
     */
    public function get_account_report($account_id, $start_date, $end_date)
    {
        // Get active financial year
        $financial_year = $this->get_active_financial_year();
        
        if (empty($financial_year)) {
            return null;
        }
        
        $financial_year_start = $financial_year['start_date'];
        
        // Calculate previous date (day before start_date)
        $previous_date = date('Y-m-d', strtotime('-1 day', strtotime($start_date)));
        
        // Get opening balances (from financial year start to day before start_date)
        $opening_debit = $this->get_transactions_sum($account_id, 'debit', $financial_year_start, $previous_date);
        $opening_credit = $this->get_transactions_sum($account_id, 'credit', $financial_year_start, $previous_date);
        
        // Get closing balances (from financial year start to end_date)
        $closing_debit = $this->get_transactions_sum($account_id, 'debit', $financial_year_start, $end_date);
        $closing_credit = $this->get_transactions_sum($account_id, 'credit', $financial_year_start, $end_date);
        
        // Calculate balances
        $opening_balance = ($opening_credit ?: 0) - ($opening_debit ?: 0);
        $closing_balance = ($closing_credit ?: 0) - ($closing_debit ?: 0);
        
        // Get all transactions for the date range
        $transactions = $this->get_account_transactions($account_id, $start_date, $end_date);
        
        // Get daily breakdown
        $daily_data = array();
        $current_date = $start_date;
        
        while (strtotime($current_date) <= strtotime($end_date)) {
            $day_previous_date = date('Y-m-d', strtotime('-1 day', strtotime($current_date)));
            
            // Get opening balance for this day
            $day_opening_debit = $this->get_transactions_sum($account_id, 'debit', $financial_year_start, $day_previous_date);
            $day_opening_credit = $this->get_transactions_sum($account_id, 'credit', $financial_year_start, $day_previous_date);
            $day_opening_balance = ($day_opening_credit ?: 0) - ($day_opening_debit ?: 0);
            
            // Get transactions for this day
            $day_transactions = $this->get_account_transactions($account_id, $current_date, $current_date);
            
            // Get closing balance for this day
            $day_closing_debit = $this->get_transactions_sum($account_id, 'debit', $financial_year_start, $current_date);
            $day_closing_credit = $this->get_transactions_sum($account_id, 'credit', $financial_year_start, $current_date);
            $day_closing_balance = ($day_closing_credit ?: 0) - ($day_closing_debit ?: 0);
            
            $daily_data[] = array(
                'date' => $current_date,
                'opening_balance' => $day_opening_balance,
                'transactions' => $day_transactions,
                'closing_balance' => $day_closing_balance
            );
            
            $current_date = date('Y-m-d', strtotime('+1 day', strtotime($current_date)));
        }
        
        // Get account details
        $this->db->select('accountcategory.name as account_category_name, accounttype.type as account_type, addaccount.*');
        $this->db->from('addaccount');
        $this->db->join('accounttype', 'accounttype.id = addaccount.account_type', 'left');
        $this->db->join('accountcategory', 'accountcategory.id = addaccount.account_category', 'left');
        $this->db->where('addaccount.id', $account_id);
        $query = $this->db->get();
        $account = $query->row_array();
        
        return array(
            'account' => $account,
            'financial_year' => $financial_year,
            'report_period' => array(
                'start_date' => $start_date,
                'end_date' => $end_date
            ),
            'opening_balance' => $opening_balance,
            'closing_balance' => $closing_balance,
            'opening_debit' => $opening_debit ?: 0,
            'opening_credit' => $opening_credit ?: 0,
            'closing_debit' => $closing_debit ?: 0,
            'closing_credit' => $closing_credit ?: 0,
            'transactions' => $transactions,
            'daily_data' => $daily_data
        );
    }

    /**
     * Check if account exists
     * 
     * @param int $account_id Account ID
     * @return bool True if exists, false otherwise
     */
    public function account_exists($account_id)
    {
        $this->db->select('id');
        $this->db->from('addaccount');
        $this->db->where('id', $account_id);
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
}

