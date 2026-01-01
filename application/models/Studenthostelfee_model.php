<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Studenthostelfee_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    
    public function add($data_insert, $student_session_id, $remove_ids, $hostel_room_id, $session_id = null)
    {
        $new_inserted = array();
        $this->db->trans_begin();

        // FIXED: Use provided session_id or default to current session
        if (empty($session_id)) {
            $session_id = $this->current_session;
        }

        // Get all hostel fees for this student, room, and session
        $this->db->select('student_hostel_fees.id, student_hostel_fees.hostel_feemaster_id');
        $this->db->from('student_hostel_fees');
        $this->db->join('hostel_feemaster', 'hostel_feemaster.id = student_hostel_fees.hostel_feemaster_id');
        $this->db->where('student_hostel_fees.student_session_id', $student_session_id);
        $this->db->where('student_hostel_fees.hostel_room_id', $hostel_room_id);
        $this->db->where('hostel_feemaster.session_id', $session_id);
        $existing_fees = $this->db->get()->result_array();

        // Create array of existing fee master IDs
        $existing_fee_master_ids = array();
        $existing_fee_ids_map = array(); // Map of fee_master_id => student_hostel_fee_id
        foreach ($existing_fees as $fee) {
            $existing_fee_master_ids[] = $fee['hostel_feemaster_id'];
            $existing_fee_ids_map[$fee['hostel_feemaster_id']] = $fee['id'];
        }

        // Get the fee master IDs that should be assigned
        $new_fee_master_ids = array();
        if (!empty($data_insert)) {
            foreach ($data_insert as $insert_value) {
                if (isset($insert_value['hostel_feemaster_id'])) {
                    $new_fee_master_ids[] = $insert_value['hostel_feemaster_id'];
                }
            }
        }

        // Determine which fees to remove (existing but not in new list)
        $fees_to_remove = array_diff($existing_fee_master_ids, $new_fee_master_ids);

        // Remove fees that are no longer selected (only for current session)
        if (!empty($fees_to_remove)) {
            $remove_student_hostel_fee_ids = array();
            foreach ($fees_to_remove as $fee_master_id) {
                if (isset($existing_fee_ids_map[$fee_master_id])) {
                    $remove_student_hostel_fee_ids[] = $existing_fee_ids_map[$fee_master_id];
                }
            }

            if (!empty($remove_student_hostel_fee_ids)) {
                $this->db->where_in('id', $remove_student_hostel_fee_ids);
                $this->db->where('student_session_id', $student_session_id);
                $this->db->where('hostel_room_id', $hostel_room_id);
                $this->db->delete('student_hostel_fees');
            }
        }

        // Validate and insert only fees for the selected session
        if (!empty($data_insert)) {
            // First, validate that all hostel_feemaster_ids belong to selected session
            $hostel_feemaster_ids = array();
            foreach ($data_insert as $insert_value) {
                if (isset($insert_value['hostel_feemaster_id'])) {
                    $hostel_feemaster_ids[] = $insert_value['hostel_feemaster_id'];
                }
            }

            // Get valid hostel_feemaster_ids for selected session
            if (!empty($hostel_feemaster_ids)) {
                $this->db->select('id');
                $this->db->from('hostel_feemaster');
                $this->db->where('session_id', $session_id);
                $this->db->where_in('id', $hostel_feemaster_ids);
                $valid_fees = $this->db->get()->result_array();

                $valid_fee_ids = array();
                foreach ($valid_fees as $fee) {
                    $valid_fee_ids[] = $fee['id'];
                }

                // Only insert fees that belong to selected session and don't already exist
                foreach ($data_insert as $insert_key => $insert_value) {
                    if (in_array($insert_value['hostel_feemaster_id'], $valid_fee_ids)) {
                        // Check if this fee master ID is not already assigned
                        if (!in_array($insert_value['hostel_feemaster_id'], $existing_fee_master_ids)) {
                            $this->db->insert('student_hostel_fees', $insert_value);
                        }
                    }
                }
            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function getStudentHostelFees($student_session_id, $hostel_room_id = null)
    {
        $this->db->select('student_hostel_fees.*, hostel_feemaster.month, hostel_feemaster.due_date,
                          hostel_feemaster.fine_type, hostel_feemaster.fine_percentage, hostel_feemaster.fine_amount,
                          hostel_rooms.cost_per_bed as amount, hostel_rooms.room_no, hostel.hostel_name')
                 ->from('student_hostel_fees')
                 ->join('hostel_feemaster', 'hostel_feemaster.id = student_hostel_fees.hostel_feemaster_id')
                 ->join('hostel_rooms', 'hostel_rooms.id = student_hostel_fees.hostel_room_id')
                 ->join('hostel', 'hostel.id = hostel_rooms.hostel_id')
                 ->where('student_hostel_fees.student_session_id', $student_session_id)
                 ->where('hostel_feemaster.session_id', $this->current_session); // FIXED: Filter by current session

        if ($hostel_room_id != null) {
            $this->db->where('student_hostel_fees.hostel_room_id', $hostel_room_id);
        }

        $this->db->order_by('hostel_feemaster.id', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Get student hostel fees for a specific session
     * @param int $student_session_id
     * @param int $hostel_room_id
     * @param int $session_id
     * @return array
     */
    public function getStudentHostelFeesBySession($student_session_id, $hostel_room_id = null, $session_id = null)
    {
        // Use provided session or default to current session
        if (empty($session_id)) {
            $session_id = $this->current_session;
        }

        $this->db->select('student_hostel_fees.*, hostel_feemaster.month, hostel_feemaster.due_date,
                          hostel_feemaster.fine_type, hostel_feemaster.fine_percentage, hostel_feemaster.fine_amount,
                          hostel_rooms.cost_per_bed as amount, hostel_rooms.room_no, hostel.hostel_name')
                 ->from('student_hostel_fees')
                 ->join('hostel_feemaster', 'hostel_feemaster.id = student_hostel_fees.hostel_feemaster_id')
                 ->join('hostel_rooms', 'hostel_rooms.id = student_hostel_fees.hostel_room_id')
                 ->join('hostel', 'hostel.id = hostel_rooms.hostel_id')
                 ->where('student_hostel_fees.student_session_id', $student_session_id)
                 ->where('hostel_feemaster.session_id', $session_id);

        if ($hostel_room_id != null) {
            $this->db->where('student_hostel_fees.hostel_room_id', $hostel_room_id);
        }

        $this->db->order_by('hostel_feemaster.id', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Get student hostel fees across all sessions (for display purposes)
     * @param int $student_session_id
     * @param int $hostel_room_id
     * @return array
     */
    public function getStudentHostelFeesAllSessions($student_session_id, $hostel_room_id = null)
    {
        // FIXED: Use GROUP BY to prevent duplicates and get payment details
        $this->db->select('student_hostel_fees.id, student_hostel_fees.hostel_feemaster_id,
                          student_hostel_fees.student_session_id, student_hostel_fees.hostel_room_id,
                          student_hostel_fees.generated_by, student_hostel_fees.created_at,
                          hostel_feemaster.month, hostel_feemaster.due_date,
                          hostel_feemaster.fine_type, hostel_feemaster.fine_percentage, hostel_feemaster.fine_amount,
                          hostel_feemaster.session_id, hostel_rooms.cost_per_bed as fees, hostel_rooms.room_no,
                          hostel.hostel_name,
                          (SELECT id FROM student_fees_deposite WHERE student_fees_deposite.student_hostel_fee_id = student_hostel_fees.id LIMIT 1) as student_fees_deposite_id,
                          (SELECT amount_detail FROM student_fees_deposite WHERE student_fees_deposite.student_hostel_fee_id = student_hostel_fees.id LIMIT 1) as amount_detail')
                 ->from('student_hostel_fees')
                 ->join('hostel_feemaster', 'hostel_feemaster.id = student_hostel_fees.hostel_feemaster_id')
                 ->join('hostel_rooms', 'hostel_rooms.id = student_hostel_fees.hostel_room_id')
                 ->join('hostel', 'hostel.id = hostel_rooms.hostel_id')
                 ->where('student_hostel_fees.student_session_id', $student_session_id);

        if ($hostel_room_id != null) {
            $this->db->where('student_hostel_fees.hostel_room_id', $hostel_room_id);
        }

        $this->db->group_by('student_hostel_fees.id');
        $this->db->order_by('hostel_feemaster.session_id', 'DESC');
        $this->db->order_by('hostel_feemaster.id', 'ASC');
        return $this->db->get()->result();
    }

    public function getHostelFeeByStudentSession($student_session_id, $hostel_room_id)
    {

        if ($student_session_id != null && $hostel_room_id != null) {

            $sql = "SELECT hostel_feemaster.*,student_hostel_fees.id as student_hostel_fee_id FROM `hostel_feemaster` LEFT JOIN student_hostel_fees on hostel_feemaster.id = student_hostel_fees.hostel_feemaster_id and student_hostel_fees.hostel_room_id=" . $hostel_room_id . " and student_hostel_fees.student_session_id=" . $student_session_id . " WHERE hostel_feemaster.session_id=" . $this->current_session . " ORDER by hostel_feemaster.id";

            $query = $this->db->query($sql);
            return $query->result();
        }

        return false;

    }

    public function getHostelFeeByMonthStudentSession($student_session_id, $hostel_room_id, $month)
    {

        if ($student_session_id != null && $hostel_room_id != null) {

            $sql = "SELECT hostel_feemaster.*,student_hostel_fees.id as student_hostel_fee_id FROM `hostel_feemaster` LEFT JOIN student_hostel_fees on hostel_feemaster.id = student_hostel_fees.hostel_feemaster_id and student_hostel_fees.hostel_room_id=" . $hostel_room_id . " and student_hostel_fees.student_session_id=" . $student_session_id . " WHERE hostel_feemaster.session_id=" . $this->current_session . " and hostel_feemaster.month='" . $month . "' ORDER by hostel_feemaster.id";

            $query = $this->db->query($sql);
            return $query->result();
        }

        return false;

    }

    public function getHostelFeeMasterByStudentHostelID($student_hostel_fee_id)
    {

        $this->db->select('hostel_feemaster.*,hostel_rooms.cost_per_bed as `amount`');
        $this->db->join('hostel_feemaster', 'hostel_feemaster.id=student_hostel_fees.hostel_feemaster_id');
        $this->db->join('hostel_rooms', 'hostel_rooms.id=student_hostel_fees.hostel_room_id');
        $this->db->where('student_hostel_fees.id', $student_hostel_fee_id);
        $q = $this->db->get('student_hostel_fees');

        return $q->row();

    }

    /**
     * Get student hostel fee by ID
     * @param int $id
     * @return object
     */
    public function get($id = null)
    {
        $this->db->select('student_hostel_fees.*, hostel_feemaster.month, hostel_feemaster.due_date,
                          hostel_rooms.cost_per_bed as amount, hostel_rooms.room_no, hostel.hostel_name')
                 ->from('student_hostel_fees')
                 ->join('hostel_feemaster', 'hostel_feemaster.id = student_hostel_fees.hostel_feemaster_id')
                 ->join('hostel_rooms', 'hostel_rooms.id = student_hostel_fees.hostel_room_id')
                 ->join('hostel', 'hostel.id = hostel_rooms.hostel_id')
                 ->where('hostel_feemaster.session_id', $this->current_session); // FIXED: Filter by current session

        if ($id != null) {
            $this->db->where('student_hostel_fees.id', $id);
        } else {
            $this->db->order_by('student_hostel_fees.id', 'DESC');
        }

        $query = $this->db->get();
        if ($id != null) {
            return $query->row();
        } else {
            return $query->result();
        }
    }

    /**
     * Remove student hostel fee
     * @param int $id
     * @return bool
     */
    public function remove($id)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        $this->db->where('id', $id);
        $this->db->delete('student_hostel_fees');
        
        $message = DELETE_RECORD_CONSTANT . " On student hostel fee id " . $id;
        $action = "Delete";
        $this->log($message, $id, $action);

        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    /**
     * Get students with hostel fees for a specific month
     * @param string $month
     * @param int $session_id
     * @return array
     */
    public function getStudentsByMonth($month, $session_id = null)
    {
        if ($session_id == null) {
            $session_id = $this->current_session;
        }

        $this->db->select('student_hostel_fees.*, students.firstname, students.middlename, students.lastname,
                          students.admission_no, classes.class, sections.section, hostel_rooms.room_no,
                          hostel.hostel_name, hostel_feemaster.due_date, hostel_rooms.cost_per_bed as amount')
                 ->from('student_hostel_fees')
                 ->join('hostel_feemaster', 'hostel_feemaster.id = student_hostel_fees.hostel_feemaster_id')
                 ->join('student_session', 'student_session.id = student_hostel_fees.student_session_id')
                 ->join('students', 'students.id = student_session.student_id')
                 ->join('classes', 'classes.id = student_session.class_id')
                 ->join('sections', 'sections.id = student_session.section_id')
                 ->join('hostel_rooms', 'hostel_rooms.id = student_hostel_fees.hostel_room_id')
                 ->join('hostel', 'hostel.id = hostel_rooms.hostel_id')
                 ->where('hostel_feemaster.month', $month)
                 ->where('hostel_feemaster.session_id', $session_id)
                 ->where('students.is_active', 'yes')
                 ->order_by('students.firstname', 'ASC');
        
        return $this->db->get()->result();
    }

    /**
     * Get hostel fee payment history for a specific hostel fee
     * @param int $hostel_fee_id
     * @return array
     */
    /**
     * Get hostel fee by ID (alias for get() method)
     * @param int $id The hostel fee ID
     * @return object|null
     */
    public function getHostelFeeByID($id)
    {
        return $this->get($id);
    }

    /**
     * Get hostel fee payment history
     * @param int $hostel_fee_id
     * @return array
     */
    public function getHostelFeePaymentHistory($hostel_fee_id)
    {
        $sql = "SELECT sfd.id as student_fees_deposite_id, sfd.amount_detail, sfd.date, sfd.payment_mode, sfd.description,
                       sfd.created_at, sfd.received_by, staff.name as received_by_name, staff.surname as received_by_surname
                FROM student_fees_deposite sfd
                LEFT JOIN staff ON staff.id = sfd.received_by
                WHERE sfd.student_hostel_fee_id = ?
                ORDER BY sfd.date DESC, sfd.id DESC";

        $query = $this->db->query($sql, array($hostel_fee_id));
        $results = $query->result();

        $payment_history = array();
        if (!empty($results)) {
            foreach ($results as $result) {
                $amount_detail = json_decode($result->amount_detail, true);
                if (!empty($amount_detail)) {
                    foreach ($amount_detail as $inv_no => $detail) {
                        $payment_history[] = (object) array(
                            'student_fees_deposite_id' => $result->student_fees_deposite_id,
                            'inv_no' => $inv_no,
                            'date' => $result->date,
                            'payment_mode' => $result->payment_mode,
                            'description' => isset($detail['description']) ? $detail['description'] : $result->description,
                            'amount' => isset($detail['amount']) ? $detail['amount'] : 0,
                            'amount_discount' => isset($detail['amount_discount']) ? $detail['amount_discount'] : 0,
                            'amount_fine' => isset($detail['amount_fine']) ? $detail['amount_fine'] : 0,
                            'received_by_name' => $result->received_by_name,
                            'received_by_surname' => $result->received_by_surname,
                            'created_at' => $result->created_at
                        );
                    }
                }
            }
        }

        return $payment_history;
    }
}
