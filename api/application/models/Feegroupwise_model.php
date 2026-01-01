<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Feegroupwise_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    /**
     * Get Fee Group-wise Collection Summary
     * Returns aggregated data for each fee group
     * UPDATED: Now excludes additional fees - only returns regular fees
     */
    public function getFeeGroupwiseCollection($session_id = null, $class_ids = array(), $section_ids = array(), $feegroup_ids = array(), $from_date = null, $to_date = null)
    {
        if ($session_id === null) {
            $session_id = $this->current_session;
        }

        // Get regular fees data only (excluding additional fees)
        $regular_fees = $this->getRegularFeesCollection($session_id, $class_ids, $section_ids, $feegroup_ids, $from_date, $to_date);

        // Clean up and calculate percentages
        foreach ($regular_fees as $row) {
            $row->total_amount = floatval($row->total_amount);
            $row->amount_collected = floatval($row->amount_collected);

            // FIXED: Balance should never be negative - if collection >= total, balance = 0
            $row->balance_amount = max(0, $row->total_amount - $row->amount_collected);
            $row->total_students = intval($row->total_students);

            // Calculate collection percentage
            if ($row->total_amount > 0) {
                $row->collection_percentage = round(($row->amount_collected / $row->total_amount) * 100, 2);
            } else {
                $row->collection_percentage = 0;
            }

            // Add data quality flag
            if ($row->total_amount == 0 && $row->amount_collected > 0) {
                $row->data_issue = 'OVERPAYMENT';
                $row->data_issue_description = 'Payment collected but no fee amount assigned';
            } elseif ($row->total_amount == 0 && $row->amount_collected == 0) {
                $row->data_issue = 'NO_FEE_ASSIGNED';
                $row->data_issue_description = 'No fee amount assigned to students';
            } else {
                $row->data_issue = null;
                $row->data_issue_description = null;
            }
        }

        return $regular_fees;
    }

    /**
     * Get regular fees collection summary
     */
    private function getRegularFeesCollection($session_id, $class_ids, $section_ids, $feegroup_ids, $from_date, $to_date)
    {
        // First, get all fee groups with their total amounts from student_fees_master
        $sql = "
            SELECT
                fg.id as fee_group_id,
                fg.name as fee_group_name,
                COALESCE(SUM(sfm.amount), 0) as total_amount,
                COUNT(DISTINCT sfm.student_session_id) as total_students
            FROM fee_groups fg
            INNER JOIN fee_session_groups fsg ON fsg.fee_groups_id = fg.id AND fsg.session_id = ?
            LEFT JOIN student_fees_master sfm ON sfm.fee_session_group_id = fg.id
        ";

        $where_conditions = array();
        $params = array($session_id);

        $where_conditions[] = "fg.is_system = 0";

        if (!empty($feegroup_ids)) {
            $placeholders = implode(',', array_fill(0, count($feegroup_ids), '?'));
            $where_conditions[] = "fg.id IN ($placeholders)";
            $params = array_merge($params, $feegroup_ids);
        }

        if (!empty($class_ids) || !empty($section_ids)) {
            $sql .= " LEFT JOIN student_session ss ON ss.id = sfm.student_session_id";

            if (!empty($class_ids)) {
                $placeholders = implode(',', array_fill(0, count($class_ids), '?'));
                $where_conditions[] = "(ss.class_id IN ($placeholders) OR sfm.id IS NULL)";
                $params = array_merge($params, $class_ids);
            }

            if (!empty($section_ids)) {
                $placeholders = implode(',', array_fill(0, count($section_ids), '?'));
                $where_conditions[] = "(ss.section_id IN ($placeholders) OR sfm.id IS NULL)";
                $params = array_merge($params, $section_ids);
            }
        }

        if (!empty($where_conditions)) {
            $sql .= " WHERE " . implode(' AND ', $where_conditions);
        }

        $sql .= " GROUP BY fg.id, fg.name ORDER BY fg.name ASC";

        $query = $this->db->query($sql, $params);
        $fee_groups = $query->result();

        // Now calculate collected amounts for each fee group
        foreach ($fee_groups as $group) {
            $group->amount_collected = $this->calculateCollectedAmount(
                $group->fee_group_id,
                $session_id,
                $class_ids,
                $section_ids,
                $from_date,
                $to_date,
                'regular'
            );
        }

        return $fee_groups;

    }

    /**
     * Get additional fees collection summary
     */
    private function getAdditionalFeesCollection($session_id, $class_ids, $section_ids, $feegroup_ids, $from_date, $to_date)
    {
        // First, get all fee groups with their total amounts from student_fees_masteradding
        $sql = "
            SELECT
                fga.id as fee_group_id,
                fga.name as fee_group_name,
                COALESCE(SUM(sfma.amount), 0) as total_amount,
                COUNT(DISTINCT sfma.student_session_id) as total_students
            FROM fee_groupsadding fga
            INNER JOIN fee_session_groupsadding fsga ON fsga.fee_groups_id = fga.id AND fsga.session_id = ?
            LEFT JOIN student_fees_masteradding sfma ON sfma.fee_session_group_id = fga.id
        ";

        $where_conditions = array();
        $params = array($session_id);

        $where_conditions[] = "fga.is_system = 0";

        if (!empty($feegroup_ids)) {
            $placeholders = implode(',', array_fill(0, count($feegroup_ids), '?'));
            $where_conditions[] = "fga.id IN ($placeholders)";
            $params = array_merge($params, $feegroup_ids);
        }

        if (!empty($class_ids) || !empty($section_ids)) {
            $sql .= " LEFT JOIN student_session ssa ON ssa.id = sfma.student_session_id";

            if (!empty($class_ids)) {
                $placeholders = implode(',', array_fill(0, count($class_ids), '?'));
                $where_conditions[] = "(ssa.class_id IN ($placeholders) OR sfma.id IS NULL)";
                $params = array_merge($params, $class_ids);
            }

            if (!empty($section_ids)) {
                $placeholders = implode(',', array_fill(0, count($section_ids), '?'));
                $where_conditions[] = "(ssa.section_id IN ($placeholders) OR sfma.id IS NULL)";
                $params = array_merge($params, $section_ids);
            }
        }

        if (!empty($where_conditions)) {
            $sql .= " WHERE " . implode(' AND ', $where_conditions);
        }

        $sql .= " GROUP BY fga.id, fga.name ORDER BY fga.name ASC";

        $query = $this->db->query($sql, $params);
        $fee_groups = $query->result();

        // Now calculate collected amounts for each fee group
        foreach ($fee_groups as $group) {
            $group->amount_collected = $this->calculateCollectedAmount(
                $group->fee_group_id,
                $session_id,
                $class_ids,
                $section_ids,
                $from_date,
                $to_date,
                'additional'
            );
        }

        return $fee_groups;
    }

    /**
     * Calculate collected amount from deposit tables by parsing JSON amount_detail
     */
    private function calculateCollectedAmount($fee_group_id, $session_id, $class_ids, $section_ids, $from_date, $to_date, $type = 'regular')
    {
        $total_collected = 0;

        if ($type == 'regular') {
            // Query student_fees_deposite table
            $sql = "
                SELECT sfd.amount_detail
                FROM student_fees_deposite sfd
                INNER JOIN student_fees_master sfm ON sfm.id = sfd.student_fees_master_id
                WHERE sfm.fee_session_group_id = ?
            ";

            $params = array($fee_group_id);

            if (!empty($class_ids) || !empty($section_ids)) {
                $sql .= " INNER JOIN student_session ss ON ss.id = sfm.student_session_id";

                if (!empty($class_ids)) {
                    $placeholders = implode(',', array_fill(0, count($class_ids), '?'));
                    $sql .= " AND ss.class_id IN ($placeholders)";
                    $params = array_merge($params, $class_ids);
                }

                if (!empty($section_ids)) {
                    $placeholders = implode(',', array_fill(0, count($section_ids), '?'));
                    $sql .= " AND ss.section_id IN ($placeholders)";
                    $params = array_merge($params, $section_ids);
                }
            }

            $query = $this->db->query($sql, $params);

        } else {
            // Query student_fees_depositeadding table
            $sql = "
                SELECT sfda.amount_detail
                FROM student_fees_depositeadding sfda
                INNER JOIN student_fees_masteradding sfma ON sfma.id = sfda.student_fees_master_id
                WHERE sfma.fee_session_group_id = ?
            ";

            $params = array($fee_group_id);

            if (!empty($class_ids) || !empty($section_ids)) {
                $sql .= " INNER JOIN student_session ssa ON ssa.id = sfma.student_session_id";

                if (!empty($class_ids)) {
                    $placeholders = implode(',', array_fill(0, count($class_ids), '?'));
                    $sql .= " AND ssa.class_id IN ($placeholders)";
                    $params = array_merge($params, $class_ids);
                }

                if (!empty($section_ids)) {
                    $placeholders = implode(',', array_fill(0, count($section_ids), '?'));
                    $sql .= " AND ssa.section_id IN ($placeholders)";
                    $params = array_merge($params, $section_ids);
                }
            }

            $query = $this->db->query($sql, $params);
        }

        // Parse JSON amount_detail and sum up amounts
        $results = $query->result();
        foreach ($results as $row) {
            if (!empty($row->amount_detail)) {
                $amount_detail = json_decode($row->amount_detail);
                if (is_object($amount_detail) || is_array($amount_detail)) {
                    foreach ($amount_detail as $detail) {
                        if (is_object($detail)) {
                            // Check date filter if specified
                            $include_payment = true;
                            if (!empty($from_date) && !empty($to_date) && isset($detail->date)) {
                                $payment_date = date('Y-m-d', strtotime($detail->date));
                                if ($payment_date < $from_date || $payment_date > $to_date) {
                                    $include_payment = false;
                                }
                            }

                            if ($include_payment) {
                                $amount = isset($detail->amount) ? floatval($detail->amount) : 0;
                                $total_collected += $amount;
                            }
                        }
                    }
                }
            }
        }

        return $total_collected;
    }

    /**
     * Get Detailed Fee Group-wise Data (Student-level)
     * Returns individual student records with fee group details
     * UPDATED: Now excludes additional fees - only returns regular fees
     */
    public function getFeeGroupwiseDetailedData($session_id = null, $class_ids = array(), $section_ids = array(), $feegroup_ids = array(), $from_date = null, $to_date = null)
    {
        if ($session_id === null) {
            $session_id = $this->current_session;
        }

        // Get regular fees detailed data only (excluding additional fees)
        $regular_fees = $this->getRegularFeesDetailedData($session_id, $class_ids, $section_ids, $feegroup_ids, $from_date, $to_date);

        // Clean up and add calculated fields
        foreach ($regular_fees as $row) {
            $row->total_amount = floatval($row->total_amount);
            $row->amount_collected = floatval($row->amount_collected);

            // FIXED: Balance should never be negative - if collection >= total, balance = 0
            $row->balance_amount = max(0, $row->total_amount - $row->amount_collected);

            // Calculate collection percentage
            if ($row->total_amount > 0) {
                $row->collection_percentage = round(($row->amount_collected / $row->total_amount) * 100, 2);
            } else {
                $row->collection_percentage = 0;
            }

            // Determine payment status
            // FIXED: Check if collection >= total for "Paid" status
            if ($row->amount_collected >= $row->total_amount && $row->amount_collected > 0) {
                $row->payment_status = 'Paid';
            } elseif ($row->amount_collected > 0) {
                $row->payment_status = 'Partial';
            } else {
                $row->payment_status = 'Pending';
            }

            // Add data quality flag
            if ($row->total_amount == 0 && $row->amount_collected > 0) {
                $row->data_issue = 'OVERPAYMENT';
                $row->data_issue_description = 'Payment collected but no fee amount assigned';
            } elseif ($row->total_amount == 0 && $row->amount_collected == 0) {
                $row->data_issue = 'NO_FEE_ASSIGNED';
                $row->data_issue_description = 'No fee amount assigned to this student';
            } else {
                $row->data_issue = null;
                $row->data_issue_description = null;
            }
        }

        return $regular_fees;
    }

    /**
     * Get regular fees detailed data
     */
    private function getRegularFeesDetailedData($session_id, $class_ids, $section_ids, $feegroup_ids, $from_date, $to_date)
    {
        // Build SQL query
        $sql = "
            SELECT
                s.id as student_id,
                s.admission_no,
                CONCAT(s.firstname, ' ', IFNULL(s.middlename, ''), ' ', IFNULL(s.lastname, '')) as student_name,
                s.father_name,
                c.class as class_name,
                sec.section as section_name,
                fg.name as fee_group_name,
                sfm.amount as total_amount,
                sfm.id as student_fees_master_id,
                fg.id as fee_group_id
            FROM students s
            INNER JOIN student_session ss ON ss.student_id = s.id AND ss.session_id = ?
            INNER JOIN classes c ON c.id = ss.class_id
            INNER JOIN sections sec ON sec.id = ss.section_id
            INNER JOIN student_fees_master sfm ON sfm.student_session_id = ss.id
            INNER JOIN fee_groups fg ON fg.id = sfm.fee_session_group_id
            WHERE fg.is_system = 0
        ";

        $params = array($session_id);

        if (!empty($class_ids)) {
            $placeholders = implode(',', array_fill(0, count($class_ids), '?'));
            $sql .= " AND ss.class_id IN ($placeholders)";
            $params = array_merge($params, $class_ids);
        }

        if (!empty($section_ids)) {
            $placeholders = implode(',', array_fill(0, count($section_ids), '?'));
            $sql .= " AND ss.section_id IN ($placeholders)";
            $params = array_merge($params, $section_ids);
        }

        if (!empty($feegroup_ids)) {
            $placeholders = implode(',', array_fill(0, count($feegroup_ids), '?'));
            $sql .= " AND fg.id IN ($placeholders)";
            $params = array_merge($params, $feegroup_ids);
        }

        $sql .= " ORDER BY fg.name ASC, c.class ASC, s.firstname ASC";

        $query = $this->db->query($sql, $params);
        $results = $query->result();

        // Calculate collected amount for each student
        foreach ($results as $row) {
            $row->amount_collected = $this->calculateStudentCollectedAmount(
                $row->student_fees_master_id,
                $from_date,
                $to_date,
                'regular'
            );
        }

        return $results;

    }

    /**
     * Get additional fees detailed data
     */
    private function getAdditionalFeesDetailedData($session_id, $class_ids, $section_ids, $feegroup_ids, $from_date, $to_date)
    {
        // Build SQL query
        $sql = "
            SELECT
                s.id as student_id,
                s.admission_no,
                CONCAT(s.firstname, ' ', IFNULL(s.middlename, ''), ' ', IFNULL(s.lastname, '')) as student_name,
                s.father_name,
                c.class as class_name,
                sec.section as section_name,
                fga.name as fee_group_name,
                sfma.amount as total_amount,
                sfma.id as student_fees_master_id,
                fga.id as fee_group_id
            FROM students s
            INNER JOIN student_session ss ON ss.student_id = s.id AND ss.session_id = ?
            INNER JOIN classes c ON c.id = ss.class_id
            INNER JOIN sections sec ON sec.id = ss.section_id
            INNER JOIN student_fees_masteradding sfma ON sfma.student_session_id = ss.id
            INNER JOIN fee_groupsadding fga ON fga.id = sfma.fee_session_group_id
            WHERE fga.is_system = 0
        ";

        $params = array($session_id);

        if (!empty($class_ids)) {
            $placeholders = implode(',', array_fill(0, count($class_ids), '?'));
            $sql .= " AND ss.class_id IN ($placeholders)";
            $params = array_merge($params, $class_ids);
        }

        if (!empty($section_ids)) {
            $placeholders = implode(',', array_fill(0, count($section_ids), '?'));
            $sql .= " AND ss.section_id IN ($placeholders)";
            $params = array_merge($params, $section_ids);
        }

        if (!empty($feegroup_ids)) {
            $placeholders = implode(',', array_fill(0, count($feegroup_ids), '?'));
            $sql .= " AND fga.id IN ($placeholders)";
            $params = array_merge($params, $feegroup_ids);
        }

        $sql .= " ORDER BY fga.name ASC, c.class ASC, s.firstname ASC";

        $query = $this->db->query($sql, $params);
        $results = $query->result();

        // Calculate collected amount for each student
        foreach ($results as $row) {
            $row->amount_collected = $this->calculateStudentCollectedAmount(
                $row->student_fees_master_id,
                $from_date,
                $to_date,
                'additional'
            );
        }

        return $results;
    }

    /**
     * Calculate collected amount for a specific student from deposit tables
     */
    private function calculateStudentCollectedAmount($student_fees_master_id, $from_date, $to_date, $type = 'regular')
    {
        $total_collected = 0;

        if ($type == 'regular') {
            $this->db->select('amount_detail');
            $this->db->from('student_fees_deposite');
            $this->db->where('student_fees_master_id', $student_fees_master_id);
            $query = $this->db->get();
        } else {
            $this->db->select('amount_detail');
            $this->db->from('student_fees_depositeadding');
            $this->db->where('student_fees_master_id', $student_fees_master_id);
            $query = $this->db->get();
        }

        // Parse JSON amount_detail and sum up amounts
        $results = $query->result();
        foreach ($results as $row) {
            if (!empty($row->amount_detail)) {
                $amount_detail = json_decode($row->amount_detail);
                if (is_object($amount_detail) || is_array($amount_detail)) {
                    foreach ($amount_detail as $detail) {
                        if (is_object($detail)) {
                            // Check date filter if specified
                            $include_payment = true;
                            if (!empty($from_date) && !empty($to_date) && isset($detail->date)) {
                                $payment_date = date('Y-m-d', strtotime($detail->date));
                                if ($payment_date < $from_date || $payment_date > $to_date) {
                                    $include_payment = false;
                                }
                            }

                            if ($include_payment) {
                                $amount = isset($detail->amount) ? floatval($detail->amount) : 0;
                                $total_collected += $amount;
                            }
                        }
                    }
                }
            }
        }

        return $total_collected;
    }

    /**
     * Get all fee groups for filter dropdown
     * UPDATED: Now returns only regular fee groups (excluding additional fees)
     */
    public function getAllFeeGroups($session_id = null)
    {
        if ($session_id === null) {
            $session_id = $this->current_session;
        }

        // Get regular fee groups only (excluding additional fee groups)
        $this->db->select('fg.id, fg.name');
        $this->db->from('fee_groups fg');
        $this->db->join('fee_session_groups fsg', 'fsg.fee_groups_id = fg.id');
        $this->db->where('fg.is_system', 0);
        $this->db->where('fsg.session_id', $session_id);
        $this->db->order_by('fg.name', 'ASC');
        $query = $this->db->get();
        $regular_groups = $query->result_array();

        return $regular_groups;
    }
}

