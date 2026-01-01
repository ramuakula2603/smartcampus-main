<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Fee_discount_approval_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get fee discount approval list with filtering
     * 
     * @param array $filters Filter parameters (class_id, section_id, session_id, approval_status)
     * @return array List of discount approvals
     */
    public function get_discount_approvals($filters = array())
    {
        $this->db->select('fees_discount_approval.id as approval_id, fees_discount_approval.payment_id, fees_discount_approval.amount, fees_discount_approval.description as discount_note, fees_discount_approval.approval_status, fees_discount_approval.student_session_id, fees_discount_approval.fee_groups_feetype_id, fees_discount_approval.student_fees_master_id, fees_discount_approval.date, fees_discount_approval.created_at, students.id as student_id, students.admission_no, students.firstname, students.middlename, students.lastname, students.father_name, students.dob, students.gender, students.mobileno, students.category_id, categories.category, classes.id as class_id, classes.class, sections.id as section_id, sections.section, fee_groups.name as fee_group_name');
        $this->db->from('fees_discount_approval');
        $this->db->join('student_session', 'student_session.id = fees_discount_approval.student_session_id');
        $this->db->join('students', 'students.id = student_session.student_id');
        $this->db->join('classes', 'classes.id = student_session.class_id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('categories', 'categories.id = students.category_id', 'left');
        $this->db->join('student_fees_master', 'student_fees_master.id = fees_discount_approval.student_fees_master_id');
        $this->db->join('fee_session_groups', 'fee_session_groups.id = student_fees_master.fee_session_group_id');
        $this->db->join('fee_groups', 'fee_groups.id = fee_session_groups.fee_groups_id');
        
        $this->db->where('students.is_active', 'yes');

        // Apply filters
        if (isset($filters['class_id']) && !empty($filters['class_id'])) {
            if (is_array($filters['class_id'])) {
                $this->db->where_in('student_session.class_id', $filters['class_id']);
            } else {
                $this->db->where('student_session.class_id', $filters['class_id']);
            }
        }

        if (isset($filters['section_id']) && !empty($filters['section_id'])) {
            if (is_array($filters['section_id'])) {
                $this->db->where_in('student_session.section_id', $filters['section_id']);
            } else {
                $this->db->where('student_session.section_id', $filters['section_id']);
            }
        }

        if (isset($filters['session_id']) && !empty($filters['session_id'])) {
            if (is_array($filters['session_id'])) {
                $this->db->where_in('fees_discount_approval.session_id', $filters['session_id']);
            } else {
                $this->db->where('fees_discount_approval.session_id', $filters['session_id']);
            }
        }

        if (isset($filters['approval_status']) && $filters['approval_status'] !== null && $filters['approval_status'] !== '') {
            $status = $filters['approval_status'];
            if (is_array($status)) {
                $status_conditions = array();
                foreach ($status as $s) {
                    if ($s == "approved" || $s == 1) {
                        $status_conditions[] = 1;
                    } elseif ($s == "rejected" || $s == 2) {
                        $status_conditions[] = 2;
                    } elseif ($s == "pending" || $s == 0) {
                        $status_conditions[] = 0;
                    }
                }
                if (!empty($status_conditions)) {
                    $this->db->where_in('fees_discount_approval.approval_status', $status_conditions);
                }
            } else {
                if ($status == "approved" || $status == 1) {
                    $this->db->where('fees_discount_approval.approval_status', 1);
                } elseif ($status == "rejected" || $status == 2) {
                    $this->db->where('fees_discount_approval.approval_status', 2);
                } elseif ($status == "pending" || $status == 0) {
                    $this->db->where('fees_discount_approval.approval_status', 0);
                }
            }
        }

        $this->db->order_by('fees_discount_approval.id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Check if approval exists
     * 
     * @param int $id Approval ID
     * @return bool True if approval exists
     */
    public function approval_exists($id)
    {
        $this->db->select('id');
        $this->db->from('fees_discount_approval');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    /**
     * Get a specific discount approval by ID
     * 
     * @param int $id Approval ID
     * @return array|null Discount approval data
     */
    public function get_approval($id)
    {
        $this->db->select('fees_discount_approval.*, students.admission_no, students.firstname, students.middlename, students.lastname, students.father_name, students.dob, students.gender, students.mobileno, classes.class, sections.section, fee_groups.name as fee_group_name');
        $this->db->from('fees_discount_approval');
        $this->db->join('student_session', 'student_session.id = fees_discount_approval.student_session_id', 'left');
        $this->db->join('students', 'students.id = student_session.student_id', 'left');
        $this->db->join('classes', 'classes.id = student_session.class_id', 'left');
        $this->db->join('sections', 'sections.id = student_session.section_id', 'left');
        $this->db->join('student_fees_master', 'student_fees_master.id = fees_discount_approval.student_fees_master_id', 'left');
        $this->db->join('fee_session_groups', 'fee_session_groups.id = student_fees_master.fee_session_group_id', 'left');
        $this->db->join('fee_groups', 'fee_groups.id = fee_session_groups.fee_groups_id', 'left');
        $this->db->where('fees_discount_approval.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Update approval status
     * 
     * @param int $id Approval ID
     * @param int $status Approval status (0=pending, 1=approved, 2=rejected)
     * @return bool True if update successful
     */
    public function update_approval_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update('fees_discount_approval', array('approval_status' => $status));
        return $this->db->affected_rows() > 0;
    }

    /**
     * Update payment ID for an approval
     * 
     * @param int $id Approval ID
     * @param string $payment_id Payment ID
     * @return bool True if update successful
     */
    public function update_payment_id($id, $payment_id)
    {
        $this->db->where('id', $id);
        $this->db->update('fees_discount_approval', array('payment_id' => $payment_id));
        return $this->db->affected_rows() > 0;
    }

    /**
     * Clear payment ID and revert status to pending
     * 
     * @param int $id Approval ID
     * @return bool True if update successful
     */
    public function revert_approval($id)
    {
        $this->db->where('id', $id);
        $this->db->update('fees_discount_approval', array(
            'payment_id' => null,
            'approval_status' => 0
        ));
        return $this->db->affected_rows() > 0;
    }
}

