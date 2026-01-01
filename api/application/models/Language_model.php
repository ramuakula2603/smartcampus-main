<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Language_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get language record(s)
     * @param int $id Language ID (optional)
     * @return mixed Array of language data or single language record
     */
    public function get($id = null)
    {
        $this->db->select()->from('languages');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('language asc');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    /**
     * Get enabled languages based on school settings
     * @return array Array of enabled languages
     */
    public function getEnable_languages()
    {
        $languages_id = $this->db->select('languages')->from('sch_settings')->get()->row_array();
        if ($languages_id && isset($languages_id['languages'])) {
            $language_ids = json_decode($languages_id['languages']);
            if ($language_ids && is_array($language_ids)) {
                $query = $this->db->select()->from('languages')->where_in('id', $language_ids)->get()->result_array();
                return $query;
            }
        }
        
        // Return default language if no settings found
        $query = $this->db->select()->from('languages')->limit(1)->get()->result_array();
        return $query;
    }

    /**
     * Get all languages with pagination support
     * @param array $params Parameters for pagination
     * @return mixed Array of language records or false
     */
    public function getRows($params = array())
    {
        $this->db->select('*');
        $this->db->from('languages');
        $this->db->order_by('created_at', 'desc');
        
        if (array_key_exists("start", $params) && array_key_exists("limit", $params)) {
            $this->db->limit($params['limit'], $params['start']);
        } elseif (!array_key_exists("start", $params) && array_key_exists("limit", $params)) {
            $this->db->limit($params['limit']);
        }

        $query = $this->db->get();
        return ($query->num_rows() > 0) ? $query->result_array() : false;
    }

    /**
     * Add or update language record
     * @param array $data Language data
     * @return bool Success status
     */
    public function add($data)
    {
        try {
            if (isset($data['id'])) {
                $this->db->where('id', $data['id']);
                $this->db->update('languages', $data);
            } else {
                $this->db->insert('languages', $data);
            }
            return true;
        } catch (Exception $e) {
            log_message('error', 'Language_model add error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove language record
     * @param int $id Language ID
     * @return bool Success status
     */
    public function remove($id)
    {
        try {
            $this->db->trans_start();
            $this->db->trans_strict(false);
            
            $this->db->where('id', $id);
            $this->db->delete('languages');
            
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return false;
            } else {
                return true;
            }
        } catch (Exception $e) {
            log_message('error', 'Language_model remove error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if language data exists
     * @param string $name Language name
     * @param int $id Language ID to exclude from check
     * @return bool True if exists, false otherwise
     */
    public function check_data_exists($name, $id)
    {
        $this->db->where('language', $name);
        $this->db->where('id !=', $id);
        $query = $this->db->get('languages');
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
}
