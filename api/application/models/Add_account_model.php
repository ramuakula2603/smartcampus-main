<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Add_account_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all accounts with related information
     * 
     * @param array $filters Optional filters
     * @return array List of accounts
     */
    public function get_accounts($filters = array())
    {
        $this->db->select('accountcategory.name as account_category_name, accounttype.type as account_type_name, addaccount.*');
        $this->db->from('addaccount');
        $this->db->join('accounttype', 'accounttype.id = addaccount.account_type', 'left');
        $this->db->join('accountcategory', 'accountcategory.id = addaccount.account_category', 'left');
        $this->db->where('addaccount.is_system', 0);

        // Apply filters
        if (isset($filters['account_category']) && !empty($filters['account_category'])) {
            $this->db->where('addaccount.account_category', $filters['account_category']);
        }

        if (isset($filters['account_type']) && !empty($filters['account_type'])) {
            $this->db->where('addaccount.account_type', $filters['account_type']);
        }

        if (isset($filters['account_role']) && !empty($filters['account_role'])) {
            $this->db->where('addaccount.account_role', $filters['account_role']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $this->db->where('addaccount.is_active', $filters['is_active']);
        }

        $this->db->order_by('addaccount.name', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get account by ID
     * 
     * @param int $id Account ID
     * @return array|null Account data
     */
    public function get_account($id)
    {
        $this->db->select('accountcategory.name as account_category_name, accounttype.type as account_type_name, addaccount.*');
        $this->db->from('addaccount');
        $this->db->join('accounttype', 'accounttype.id = addaccount.account_type', 'left');
        $this->db->join('accountcategory', 'accountcategory.id = addaccount.account_category', 'left');
        $this->db->where('addaccount.id', $id);
        $this->db->where('addaccount.is_system', 0);
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Check if account exists
     * 
     * @param int $id Account ID
     * @return bool True if exists, false otherwise
     */
    public function account_exists($id)
    {
        $this->db->select('id');
        $this->db->from('addaccount');
        $this->db->where('id', $id);
        $this->db->where('is_system', 0);
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    /**
     * Check if account name exists
     * 
     * @param string $name Account name
     * @param int $exclude_id Account ID to exclude (for updates)
     * @return bool True if exists, false otherwise
     */
    public function name_exists($name, $exclude_id = 0)
    {
        $this->db->select('id');
        $this->db->from('addaccount');
        $this->db->where('name', $name);
        $this->db->where('is_system', 0);
        if ($exclude_id > 0) {
            $this->db->where('id !=', $exclude_id);
        }
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    /**
     * Check if account code exists
     * 
     * @param string $code Account code
     * @param int $exclude_id Account ID to exclude (for updates)
     * @return bool True if exists, false otherwise
     */
    public function code_exists($code, $exclude_id = 0)
    {
        $this->db->select('id');
        $this->db->from('addaccount');
        $this->db->where('code', $code);
        $this->db->where('is_system', 0);
        if ($exclude_id > 0) {
            $this->db->where('id !=', $exclude_id);
        }
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    /**
     * Create account
     * 
     * @param array $data Account data
     * @return int|false Account ID on success, false on failure
     */
    public function create_account($data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        try {
            $account_data = array(
                'name' => $data['name'],
                'code' => $data['code'],
                'account_category' => $data['account_category'],
                'account_type' => $data['account_type'],
                'account_role' => $data['account_role'],
                'description' => isset($data['description']) ? $data['description'] : '',
                'is_active' => isset($data['is_active']) ? $data['is_active'] : 'yes',
                'cash' => isset($data['cash']) ? (int)$data['cash'] : 0,
                'cheque' => isset($data['cheque']) ? (int)$data['cheque'] : 0,
                'dd' => isset($data['dd']) ? (int)$data['dd'] : 0,
                'bank_transfer' => isset($data['bank_transfer']) ? (int)$data['bank_transfer'] : 0,
                'upi' => isset($data['upi']) ? (int)$data['upi'] : 0,
                'card' => isset($data['card']) ? (int)$data['card'] : 0,
                'is_system' => 0
            );

            $this->db->insert('addaccount', $account_data);
            $account_id = $this->db->insert_id();

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                return false;
            }

            return $account_id;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Add Account Model Create Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update account
     * 
     * @param int $id Account ID
     * @param array $data Account data
     * @return bool True on success, false on failure
     */
    public function update_account($id, $data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        try {
            $account_data = array();

            if (isset($data['name'])) {
                $account_data['name'] = $data['name'];
            }
            if (isset($data['code'])) {
                $account_data['code'] = $data['code'];
            }
            if (isset($data['account_category'])) {
                $account_data['account_category'] = $data['account_category'];
            }
            if (isset($data['account_type'])) {
                $account_data['account_type'] = $data['account_type'];
            }
            if (isset($data['account_role'])) {
                $account_data['account_role'] = $data['account_role'];
            }
            if (isset($data['description'])) {
                $account_data['description'] = $data['description'];
            }
            if (isset($data['is_active'])) {
                $account_data['is_active'] = $data['is_active'];
            }
            if (isset($data['cash'])) {
                $account_data['cash'] = (int)$data['cash'];
            }
            if (isset($data['cheque'])) {
                $account_data['cheque'] = (int)$data['cheque'];
            }
            if (isset($data['dd'])) {
                $account_data['dd'] = (int)$data['dd'];
            }
            if (isset($data['bank_transfer'])) {
                $account_data['bank_transfer'] = (int)$data['bank_transfer'];
            }
            if (isset($data['upi'])) {
                $account_data['upi'] = (int)$data['upi'];
            }
            if (isset($data['card'])) {
                $account_data['card'] = (int)$data['card'];
            }

            if (!empty($account_data)) {
                $this->db->where('id', $id);
                $this->db->where('is_system', 0);
                $this->db->update('addaccount', $account_data);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                return false;
            }

            return true;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Add Account Model Update Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete account
     * 
     * @param int $id Account ID
     * @return bool True on success, false on failure
     */
    public function delete_account($id)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        try {
            $this->db->where('id', $id);
            $this->db->where('is_system', 0);
            $this->db->delete('addaccount');

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                return false;
            }

            return true;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Add Account Model Delete Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get account categories
     * 
     * @return array List of account categories
     */
    public function get_account_categories()
    {
        $this->db->select('*');
        $this->db->from('accountcategory');
        $this->db->where('is_system', 0);
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get account types by category
     * 
     * @param int $category_id Account category ID
     * @return array List of account types
     */
    public function get_account_types_by_category($category_id)
    {
        $this->db->select('accounttype.id, accounttype.type as name');
        $this->db->from('accountcategorygroup');
        $this->db->join('accounttype', 'accounttype.id = accountcategorygroup.accounttype_id', 'left');
        $this->db->where('accountcategorygroup.accountcategory_id', $category_id);
        $this->db->where('accounttype.is_system', 0);
        $this->db->order_by('accounttype.type', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get all account types
     * 
     * @return array List of account types
     */
    public function get_account_types()
    {
        $this->db->select('*');
        $this->db->from('accounttype');
        $this->db->where('is_system', 0);
        $this->db->order_by('type', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get account roles
     * 
     * Returns the standard account roles: both, debitor, creditor
     * 
     * @return array List of account roles
     */
    public function get_account_roles()
    {
        return array(
            array('value' => 'both', 'label' => 'Both'),
            array('value' => 'debitor', 'label' => 'Debitor'),
            array('value' => 'creditor', 'label' => 'Creditor')
        );
    }
}

