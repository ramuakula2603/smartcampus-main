<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Account_category_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get account categories
     * 
     * @param array $filters Optional filters
     * @return array List of account categories
     */
    public function get_account_categories($filters = array())
    {
        $this->db->select('id, name, description, is_system, is_active, created_at');
        $this->db->from('accountcategory');
        $this->db->where('is_system', 0);

        // Apply filters
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $this->db->where('is_active', $filters['is_active']);
        }

        if (isset($filters['search']) && !empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('name', $filters['search']);
            $this->db->or_like('description', $filters['search']);
            $this->db->group_end();
        }

        $this->db->order_by('name', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get account category by ID
     * 
     * @param int $id Category ID
     * @return array|null Category data
     */
    public function get_account_category($id)
    {
        $this->db->select('id, name, description, is_system, is_active, created_at');
        $this->db->from('accountcategory');
        $this->db->where('id', $id);
        $this->db->where('is_system', 0);
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Check if category exists
     * 
     * @param int $id Category ID
     * @return bool True if exists, false otherwise
     */
    public function category_exists($id)
    {
        $this->db->select('id');
        $this->db->from('accountcategory');
        $this->db->where('id', $id);
        $this->db->where('is_system', 0);
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    /**
     * Check if name exists
     * 
     * @param string $name Category name
     * @param int $exclude_id Category ID to exclude (for updates)
     * @return bool True if exists, false otherwise
     */
    public function name_exists($name, $exclude_id = 0)
    {
        $this->db->select('id');
        $this->db->from('accountcategory');
        $this->db->where('name', $name);
        $this->db->where('is_system', 0);
        if ($exclude_id > 0) {
            $this->db->where('id !=', $exclude_id);
        }
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    /**
     * Create account category
     * 
     * @param array $data Category data
     * @return int|false Category ID on success, false on failure
     */
    public function create_account_category($data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        try {
            $category_data = array(
                'name' => $data['name'],
                'description' => isset($data['description']) ? $data['description'] : '',
                'is_system' => 0,
                'is_active' => isset($data['is_active']) ? $data['is_active'] : 'yes'
            );

            $this->db->insert('accountcategory', $category_data);
            $category_id = $this->db->insert_id();

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                return false;
            }

            return $category_id;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Account Category Model Create Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update account category
     * 
     * @param int $id Category ID
     * @param array $data Category data
     * @return bool True on success, false on failure
     */
    public function update_account_category($id, $data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        try {
            $category_data = array();

            if (isset($data['name'])) {
                $category_data['name'] = $data['name'];
            }
            if (isset($data['description'])) {
                $category_data['description'] = $data['description'];
            }
            if (isset($data['is_active'])) {
                $category_data['is_active'] = $data['is_active'];
            }

            if (!empty($category_data)) {
                $this->db->where('id', $id);
                $this->db->where('is_system', 0);
                $this->db->update('accountcategory', $category_data);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                return false;
            }

            return true;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Account Category Model Update Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete account category
     * 
     * @param int $id Category ID
     * @return bool True on success, false on failure
     */
    public function delete_account_category($id)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        try {
            $this->db->where('id', $id);
            $this->db->where('is_system', 0);
            $this->db->delete('accountcategory');

            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                return false;
            }

            return true;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Account Category Model Delete Error: ' . $e->getMessage());
            return false;
        }
    }
}

