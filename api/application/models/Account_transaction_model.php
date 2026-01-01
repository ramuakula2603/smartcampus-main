<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Account_transaction_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get debit accounts (accounts that can be debited)
     * 
     * @return array List of debit accounts
     */
    public function get_debit_accounts()
    {
        $this->db->select('accountcategory.name as account_category_name, accounttype.type as account_type, addaccount.*');
        $this->db->from('addaccount');
        $this->db->join('accounttype', 'accounttype.id = addaccount.account_type', 'left');
        $this->db->join('accountcategory', 'accountcategory.id = addaccount.account_category', 'left');
        $this->db->where_not_in('addaccount.account_role', array('creditor'));
        $this->db->order_by('addaccount.name', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get credit accounts (accounts that can be credited)
     * 
     * @return array List of credit accounts
     */
    public function get_credit_accounts()
    {
        $this->db->select('accountcategory.name as account_category_name, accounttype.type as account_type, addaccount.*');
        $this->db->from('addaccount');
        $this->db->join('accounttype', 'accounttype.id = addaccount.account_type', 'left');
        $this->db->join('accountcategory', 'accountcategory.id = addaccount.account_category', 'left');
        $this->db->where_not_in('addaccount.account_role', array('debitor'));
        $this->db->order_by('addaccount.name', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get all accounts
     * 
     * @return array List of all accounts
     */
    public function get_all_accounts()
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
     * Get account by ID
     * 
     * @param int $account_id Account ID
     * @return array|null Account data
     */
    public function get_account($account_id)
    {
        $this->db->select('accountcategory.name as account_category_name, accounttype.type as account_type, addaccount.*');
        $this->db->from('addaccount');
        $this->db->join('accounttype', 'accounttype.id = addaccount.account_type', 'left');
        $this->db->join('accountcategory', 'accountcategory.id = addaccount.account_category', 'left');
        $this->db->where('addaccount.id', $account_id);
        $query = $this->db->get();
        return $query->row_array();
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

    /**
     * Create account transaction
     * 
     * Creates a transaction in accounttranscations table and corresponding entries in accountreceipts
     * 
     * @param array $data Transaction data
     * @return int|false Transaction ID on success, false on failure
     */
    public function create_transaction($data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        try {
            // Insert into accounttranscations
            $transaction_data = array(
                'fromaccountid' => $data['from_account_id'],
                'toaccountid' => $data['to_account_id'],
                'amount' => $data['amount'],
                'date' => $data['date'],
                'note' => isset($data['note']) ? $data['note'] : '',
                'is_active' => isset($data['is_active']) ? $data['is_active'] : 'yes'
            );

            $this->db->insert('accounttranscations', $transaction_data);
            $transaction_id = $this->db->insert_id();

            if ($transaction_id === false || $transaction_id <= 0) {
                $this->db->trans_rollback();
                return false;
            }

            // Get account names for receipt entries
            $from_account = $this->get_account($data['from_account_id']);
            $to_account = $this->get_account($data['to_account_id']);

            if (empty($from_account) || empty($to_account)) {
                $this->db->trans_rollback();
                return false;
            }

            // Create debit entry in accountreceipts
            $debit_data = array(
                'receiptid' => $transaction_id,
                'accountid' => $data['from_account_id'],
                'amount' => $data['amount'],
                'date' => $data['date'],
                'type' => $to_account['name'],
                'description' => isset($data['note']) ? $data['note'] : '',
                'status' => 'debit'
            );
            $this->db->insert('accountreceipts', $debit_data);

            // Create credit entry in accountreceipts
            $credit_data = array(
                'receiptid' => $transaction_id,
                'accountid' => $data['to_account_id'],
                'amount' => $data['amount'],
                'date' => $data['date'],
                'type' => $from_account['name'],
                'description' => isset($data['note']) ? $data['note'] : '',
                'status' => 'credit'
            );
            $this->db->insert('accountreceipts', $credit_data);

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                return false;
            }

            return $transaction_id;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Account Transaction Model Create Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get transaction by ID
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
     * Update transaction
     * 
     * @param int $id Transaction ID
     * @param array $data Transaction data
     * @return bool True on success, false on failure
     */
    public function update_transaction($id, $data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        try {
            // Update accounttranscations
            $transaction_data = array();
            if (isset($data['from_account_id'])) {
                $transaction_data['fromaccountid'] = $data['from_account_id'];
            }
            if (isset($data['to_account_id'])) {
                $transaction_data['toaccountid'] = $data['to_account_id'];
            }
            if (isset($data['amount'])) {
                $transaction_data['amount'] = $data['amount'];
            }
            if (isset($data['date'])) {
                $transaction_data['date'] = $data['date'];
            }
            if (isset($data['note'])) {
                $transaction_data['note'] = $data['note'];
            }
            if (isset($data['is_active'])) {
                $transaction_data['is_active'] = $data['is_active'];
            }

            if (!empty($transaction_data)) {
                $this->db->where('id', $id);
                $this->db->update('accounttranscations', $transaction_data);
            }

            // If amount, date, or accounts changed, update accountreceipts entries
            if (isset($data['amount']) || isset($data['date']) || isset($data['from_account_id']) || isset($data['to_account_id'])) {
                // Get current transaction
                $current_transaction = $this->get_transaction($id);
                
                if (!empty($current_transaction)) {
                    // Get account names
                    $from_account_id = isset($data['from_account_id']) ? $data['from_account_id'] : $current_transaction['fromaccountid'];
                    $to_account_id = isset($data['to_account_id']) ? $data['to_account_id'] : $current_transaction['toaccountid'];
                    $amount = isset($data['amount']) ? $data['amount'] : $current_transaction['amount'];
                    $date = isset($data['date']) ? $data['date'] : $current_transaction['date'];
                    $note = isset($data['note']) ? $data['note'] : $current_transaction['note'];

                    $from_account = $this->get_account($from_account_id);
                    $to_account = $this->get_account($to_account_id);

                    if (!empty($from_account) && !empty($to_account)) {
                        // Update debit entry
                        $debit_data = array(
                            'accountid' => $from_account_id,
                            'amount' => $amount,
                            'date' => $date,
                            'type' => $to_account['name'],
                            'description' => $note
                        );
                        $this->db->where('receiptid', $id);
                        $this->db->where('status', 'debit');
                        $this->db->update('accountreceipts', $debit_data);

                        // Update credit entry
                        $credit_data = array(
                            'accountid' => $to_account_id,
                            'amount' => $amount,
                            'date' => $date,
                            'type' => $from_account['name'],
                            'description' => $note
                        );
                        $this->db->where('receiptid', $id);
                        $this->db->where('status', 'credit');
                        $this->db->update('accountreceipts', $credit_data);
                    }
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                return false;
            }

            return true;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Account Transaction Model Update Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete transaction
     * 
     * @param int $id Transaction ID
     * @return bool True on success, false on failure
     */
    public function delete_transaction($id)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        try {
            // Delete from accountreceipts first (foreign key constraint)
            $this->db->where('receiptid', $id);
            $this->db->delete('accountreceipts');

            // Delete from accounttranscations
            $this->db->where('id', $id);
            $this->db->delete('accounttranscations');

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                return false;
            }

            return true;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Account Transaction Model Delete Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * List transactions with filters
     * 
     * @param array $filters Filter parameters
     * @return array List of transactions
     */
    public function list_transactions($filters = array())
    {
        $this->db->select('accounttranscations.*, 
                           from_account.name as from_account_name,
                           from_account.code as from_account_number,
                           to_account.name as to_account_name,
                           to_account.code as to_account_number');
        $this->db->from('accounttranscations');
        $this->db->join('addaccount as from_account', 'from_account.id = accounttranscations.fromaccountid', 'left');
        $this->db->join('addaccount as to_account', 'to_account.id = accounttranscations.toaccountid', 'left');

        // Apply filters
        if (isset($filters['from_account_id']) && !empty($filters['from_account_id'])) {
            $this->db->where('accounttranscations.fromaccountid', $filters['from_account_id']);
        }

        if (isset($filters['to_account_id']) && !empty($filters['to_account_id'])) {
            $this->db->where('accounttranscations.toaccountid', $filters['to_account_id']);
        }

        if (isset($filters['date_from']) && !empty($filters['date_from'])) {
            $this->db->where('accounttranscations.date >=', $filters['date_from']);
        }

        if (isset($filters['date_to']) && !empty($filters['date_to'])) {
            $this->db->where('accounttranscations.date <=', $filters['date_to']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $this->db->where('accounttranscations.is_active', $filters['is_active']);
        }

        $this->db->order_by('accounttranscations.date', 'DESC');
        $this->db->order_by('accounttranscations.id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }
}

