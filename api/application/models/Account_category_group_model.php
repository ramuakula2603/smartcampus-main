<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Account_category_group_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all category groups with related information
     * 
     * Groups by account category and includes associated account types
     * 
     * @param array $filters Optional filters
     * @return array List of category groups
     */
    public function get_category_groups($filters = array())
    {
        $this->db->select('accountcategory.id, accountcategory.name as category_name, accountcategory.is_system');
        $this->db->from('accountcategory');
        $this->db->where('accountcategory.is_system', 0);

        // Apply filters
        if (isset($filters['account_category_id']) && !empty($filters['account_category_id'])) {
            $this->db->where('accountcategory.id', $filters['account_category_id']);
        }

        $this->db->order_by('accountcategory.name', 'ASC');
        $query = $this->db->get();
        $categories = $query->result_array();

        // Get account types for each category
        foreach ($categories as $key => $category) {
            $categories[$key]['account_types'] = $this->get_account_types_by_category($category['id']);
        }

        return $categories;
    }

    /**
     * Get account types by category
     * 
     * @param int $category_id Account category ID
     * @return array List of account types
     */
    public function get_account_types_by_category($category_id)
    {
        $this->db->select('accountcategorygroup.id, 
                           accountcategorygroup.accountcategory_id,
                           accountcategorygroup.accounttype_id,
                           accountcategorygroup.is_active,
                           accountcategorygroup.created_at,
                           accounttype.type as account_type_name,
                           accounttype.code as account_type_code');
        $this->db->from('accountcategorygroup');
        $this->db->join('accounttype', 'accounttype.id = accountcategorygroup.accounttype_id', 'left');
        $this->db->where('accountcategorygroup.accountcategory_id', $category_id);
        $this->db->order_by('accountcategorygroup.id', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get category group by ID
     * 
     * @param int $id Category group ID
     * @return array|null Category group data
     */
    public function get_category_group($id)
    {
        $this->db->select('accountcategorygroup.*,
                           accountcategory.name as account_category_name,
                           accounttype.type as account_type_name,
                           accounttype.code as account_type_code');
        $this->db->from('accountcategorygroup');
        $this->db->join('accountcategory', 'accountcategory.id = accountcategorygroup.accountcategory_id', 'left');
        $this->db->join('accounttype', 'accounttype.id = accountcategorygroup.accounttype_id', 'left');
        $this->db->where('accountcategorygroup.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Check if category group exists
     * 
     * @param int $id Category group ID
     * @return bool True if exists, false otherwise
     */
    public function category_group_exists($id)
    {
        $this->db->select('id');
        $this->db->from('accountcategorygroup');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    /**
     * Check if combination exists
     * 
     * @param int $account_category_id Account category ID
     * @param int $account_type_id Account type ID
     * @param int $exclude_id Category group ID to exclude (for updates)
     * @return bool True if exists, false otherwise
     */
    public function combination_exists($account_category_id, $account_type_id, $exclude_id = 0)
    {
        $this->db->select('id');
        $this->db->from('accountcategorygroup');
        $this->db->where('accountcategory_id', $account_category_id);
        $this->db->where('accounttype_id', $account_type_id);
        if ($exclude_id > 0) {
            $this->db->where('id !=', $exclude_id);
        }
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    /**
     * Create category group
     * 
     * @param array $data Category group data
     * @return int|false Category group ID on success, false on failure
     */
    public function create_category_group($data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        try {
            $group_data = array(
                'accountcategory_id' => $data['account_category_id'],
                'accounttype_id' => $data['account_type_id'],
                'is_active' => isset($data['is_active']) ? $data['is_active'] : 'yes'
            );

            $this->db->insert('accountcategorygroup', $group_data);
            $group_id = $this->db->insert_id();

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                return false;
            }

            return $group_id;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Account Category Group Model Create Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update category group
     * 
     * @param int $id Category group ID
     * @param array $data Category group data
     * @return bool True on success, false on failure
     */
    public function update_category_group($id, $data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        try {
            $group_data = array();

            if (isset($data['account_category_id'])) {
                $group_data['accountcategory_id'] = $data['account_category_id'];
            }
            if (isset($data['account_type_id'])) {
                $group_data['accounttype_id'] = $data['account_type_id'];
            }
            if (isset($data['is_active'])) {
                $group_data['is_active'] = $data['is_active'];
            }

            if (!empty($group_data)) {
                $this->db->where('id', $id);
                $this->db->update('accountcategorygroup', $group_data);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                return false;
            }

            return true;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Account Category Group Model Update Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete category group
     * 
     * @param int $id Category group ID
     * @return bool True on success, false on failure
     */
    public function delete_category_group($id)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        try {
            $this->db->where('id', $id);
            $this->db->delete('accountcategorygroup');

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                return false;
            }

            return true;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Account Category Group Model Delete Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete all category groups by category
     * 
     * @param int $category_id Account category ID
     * @return bool True on success, false on failure
     */
    public function delete_by_category($category_id)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        try {
            $this->db->where('accountcategory_id', $category_id);
            $this->db->delete('accountcategorygroup');

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                return false;
            }

            return true;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Account Category Group Model Delete By Category Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all category groups (flat list)
     * 
     * Returns all category groups as a flat list (not grouped)
     * 
     * @param array $filters Optional filters
     * @return array List of category groups
     */
    public function list_category_groups($filters = array())
    {
        $this->db->select('accountcategorygroup.*,
                           accountcategory.name as account_category_name,
                           accounttype.type as account_type_name,
                           accounttype.code as account_type_code');
        $this->db->from('accountcategorygroup');
        $this->db->join('accountcategory', 'accountcategory.id = accountcategorygroup.accountcategory_id', 'left');
        $this->db->join('accounttype', 'accounttype.id = accountcategorygroup.accounttype_id', 'left');
        $this->db->where('accountcategory.is_system', 0);

        // Apply filters
        if (isset($filters['account_category_id']) && !empty($filters['account_category_id'])) {
            $this->db->where('accountcategorygroup.accountcategory_id', $filters['account_category_id']);
        }

        if (isset($filters['account_type_id']) && !empty($filters['account_type_id'])) {
            $this->db->where('accountcategorygroup.accounttype_id', $filters['account_type_id']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $this->db->where('accountcategorygroup.is_active', $filters['is_active']);
        }

        $this->db->order_by('accountcategory.name', 'ASC');
        $this->db->order_by('accounttype.type', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }
}
