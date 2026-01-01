<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Studentfeemaster_model extends CI_Model
{

    protected $balance_group;
    protected $balance_type;

    public function __construct()
    {
        parent::__construct();
        $this->load->config('ci-blog');
        $this->load->model('setting_model');
        $this->load->model('module_model');
        $this->load->model('feediscount_model');
        $this->balance_group   = $this->config->item('ci_balance_group');
        $this->balance_type    = $this->config->item('ci_balance_type');
        $this->current_session = $this->setting_model->getCurrentSession();
    }
    
    public function getStudentFees($student_session_id)
    {
        $sql    = "SELECT `student_fees_master`.*,fee_groups.name FROM `student_fees_master` INNER JOIN fee_session_groups on student_fees_master.fee_session_group_id=fee_session_groups.id INNER JOIN fee_groups on fee_groups.id=fee_session_groups.fee_groups_id  WHERE `student_session_id` = " . $student_session_id . " ORDER BY `student_fees_master`.`id`";
        $query  = $this->db->query($sql);
        $result = $query->result();
        if (!empty($result)) {
            foreach ($result as $result_key => $result_value) {
                $fee_session_group_id   = $result_value->fee_session_group_id;
                $student_fees_master_id = $result_value->id;
                $result_value->fees     = $this->getDueFeeByFeeSessionGroup($fee_session_group_id, $student_fees_master_id);

                if ($result_value->is_system != 0) {
                    $result_value->fees[0]->amount = $result_value->amount;
                }

                if ($result_value->fees[0]->due_date == 'null' || $result_value->fees[0]->due_date == '') {
                    $result_value->fees[0]->due_date = '';
                }
            }
        }

        return $result;
    }

    public function getStudentTransportFees($student_session_id, $route_pickup_point_id)
    {
        if($student_session_id != NULL && $route_pickup_point_id != NULL){

            $sql = "SELECT student_transport_fees.*,transport_feemaster.month,transport_feemaster.due_date ,route_pickup_point.fees,transport_feemaster.fine_amount, transport_feemaster.fine_type,transport_feemaster.fine_percentage,IFNULL(student_fees_deposite.id,0) as `student_fees_deposite_id`, IFNULL(student_fees_deposite.amount_detail,0) as `amount_detail` FROM `student_transport_fees` INNER JOIN transport_feemaster on transport_feemaster.id =student_transport_fees.transport_feemaster_id LEFT JOIN student_fees_deposite on student_fees_deposite.student_transport_fee_id=student_transport_fees.id INNER JOIN route_pickup_point on route_pickup_point.id = student_transport_fees.route_pickup_point_id  where student_transport_fees.student_session_id=".$student_session_id." and student_transport_fees.route_pickup_point_id=".$route_pickup_point_id." ORDER BY student_transport_fees.id asc";       
            $query = $this->db->query($sql);
            return $query->result();

        }
        return false;
    }

    public function getDueFeeByFeeSessionGroup($fee_session_groups_id, $student_fees_master_id) 
    {
        $sql = "SELECT student_fees_master.*,fee_groups_feetype.id as `fee_groups_feetype_id`,`fee_groups_feetype`.`fine_amount`,IFNULL(fee_groups_feetype.amount,0) as `amount`
        ,IFNULL(fee_groups_feetype.due_date,'') as `due_date`,fee_groups_feetype.fee_groups_id,fee_groups.name,fee_groups_feetype.feetype_id,feetype.code,feetype.type, IFNULL(student_fees_deposite.id,0) as `student_fees_deposite_id`, IFNULL(student_fees_deposite.amount_detail,0) as `amount_detail` FROM `student_fees_master` INNER JOIN fee_session_groups on fee_session_groups.id = student_fees_master.fee_session_group_id INNER JOIN fee_groups_feetype on  fee_groups_feetype.fee_session_group_id = fee_session_groups.id  INNER JOIN fee_groups on fee_groups.id=fee_groups_feetype.fee_groups_id INNER JOIN feetype on feetype.id=fee_groups_feetype.feetype_id LEFT JOIN student_fees_deposite on student_fees_deposite.student_fees_master_id=student_fees_master.id and student_fees_deposite.fee_groups_feetype_id=fee_groups_feetype.id WHERE student_fees_master.fee_session_group_id =" . $fee_session_groups_id . " and student_fees_master.id=" . $student_fees_master_id . " order by fee_groups_feetype.due_date asc";

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function studentDeposit($data)
    {
        $sql = "SELECT fee_groups.is_system,student_fees_master.amount as `student_fees_master_amount`, fee_groups.name as `fee_group_name`,feetype.code as `fee_type_code`,fee_groups_feetype.amount,fee_groups_feetype.due_date,`fee_groups_feetype`.`fine_amount`,IFNULL(student_fees_deposite.amount_detail,0) as `amount_detail` from student_fees_master
               INNER JOIN fee_session_groups on fee_session_groups.id=student_fees_master.fee_session_group_id
              INNER JOIN fee_groups_feetype on fee_groups_feetype.fee_groups_id=fee_session_groups.fee_groups_id
              INNER JOIN fee_groups on fee_groups_feetype.fee_groups_id=fee_groups.id
              INNER JOIN feetype on fee_groups_feetype.feetype_id=feetype.id
         LEFT JOIN student_fees_deposite on student_fees_deposite.student_fees_master_id=student_fees_master.id and student_fees_deposite.fee_groups_feetype_id=fee_groups_feetype.id WHERE student_fees_master.id =" . $data['student_fees_master_id'] . " and fee_groups_feetype.id =" . $data['fee_groups_feetype_id'];
        $query = $this->db->query($sql);
        return $query->row();
    }
    
    public function studentTransportDeposit($student_transport_fee_id)
    {
        $sql = "SELECT student_transport_fees.*,transport_feemaster.month,transport_feemaster.due_date ,route_pickup_point.fees,transport_feemaster.fine_amount, transport_feemaster.fine_type,transport_feemaster.fine_percentage,IFNULL(student_fees_deposite.id,0) as `student_fees_deposite_id`, IFNULL(student_fees_deposite.amount_detail,0) as `amount_detail` FROM `student_transport_fees` INNER JOIN transport_feemaster on transport_feemaster.id =student_transport_fees.transport_feemaster_id  LEFT JOIN student_fees_deposite on student_fees_deposite.student_transport_fee_id=student_transport_fees.id INNER JOIN route_pickup_point on route_pickup_point.id = student_transport_fees.route_pickup_point_id  where student_transport_fees.id=".$this->db->escape($student_transport_fee_id);    
        $query = $this->db->query($sql);
        return $query->row();
    }
    
    public function fee_deposit($data, $send_to, $student_fees_discount_id)
    {
        if(isset($data['student_transport_fee_id']) && !empty($data['student_transport_fee_id']) ){
            $this->db->where('student_transport_fee_id', $data['student_transport_fee_id']);
        
        }else{
            $this->db->where('student_fees_master_id', $data['student_fees_master_id']);
        $this->db->where('fee_groups_feetype_id', $data['fee_groups_feetype_id']);
        }
        unset($data['fee_category']);
        $q = $this->db->get('student_fees_deposite');
        if ($q->num_rows() > 0) {

            $desc = $data['amount_detail']['description'];
            $this->db->trans_start(); // Query will be rolled back
            $row = $q->row();
            $this->db->where('id', $row->id);
            $a                               = json_decode($row->amount_detail, true);
            $inv_no                          = max(array_keys($a)) + 1;
            $data['amount_detail']['inv_no'] = $inv_no;
            $a[$inv_no]                      = $data['amount_detail'];
            $data['amount_detail']           = json_encode($a);
            $this->db->update('student_fees_deposite', $data);

            if ($student_fees_discount_id != "") {
                $this->db->where('id', $student_fees_discount_id);
                $this->db->update('student_fees_discounts', array('status' => 'applied', 'description' => $desc, 'payment_id' => $row->id . "/" . $inv_no));
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();

                return false;
            } else {
                $this->db->trans_commit();
                return json_encode(array('invoice_id' => $row->id, 'sub_invoice_id' => $inv_no));
            }
        } else {

            $this->db->trans_start(); // Query will be rolled back
            $data['amount_detail']['inv_no'] = 1;
            $desc                            = $data['amount_detail']['description'];
            $data['amount_detail']           = json_encode(array('1' => $data['amount_detail']));
            $this->db->insert('student_fees_deposite', $data);
            $inserted_id = $this->db->insert_id();
            if ($student_fees_discount_id != "") {
                $this->db->where('id', $student_fees_discount_id);
                $this->db->update('student_fees_discounts', array('status' => 'applied', 'description' => $desc, 'payment_id' => $inserted_id . "/" . "1"));
            }

            $this->db->trans_complete(); # Completing transaction

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return false;
            } else {
                $this->db->trans_commit();
                return json_encode(array('invoice_id' => $inserted_id, 'sub_invoice_id' => 1));
            }
        }
    }

    public function getFeeByInvoice($invoice_id, $sub_invoice_id)
    {
        $this->db->select('`student_fees_deposite`.*,students.firstname,students.lastname,student_session.class_id,classes.class,sections.section,student_session.section_id,student_session.student_id,`fee_groups`.`name`, `feetype`.`type`, `feetype`.`code`,student_fees_master.student_session_id')->from('student_fees_deposite');
        $this->db->join('fee_groups_feetype', 'fee_groups_feetype.id = student_fees_deposite.fee_groups_feetype_id');
        $this->db->join('fee_groups', 'fee_groups.id = fee_groups_feetype.fee_groups_id');
        $this->db->join('feetype', 'feetype.id = fee_groups_feetype.feetype_id');
        $this->db->join('student_fees_master', 'student_fees_master.id=student_fees_deposite.student_fees_master_id');
        $this->db->join('student_session', 'student_session.id= student_fees_master.student_session_id');
        $this->db->join('classes', 'classes.id= student_session.class_id');
        $this->db->join('sections', 'sections.id= student_session.section_id');
        $this->db->join('students', 'students.id=student_session.student_id');
        $this->db->where('student_fees_deposite.id', $invoice_id);
        $q = $this->db->get();

        if ($q->num_rows() > 0) {
            $result = $q->row();
            $res    = json_decode($result->amount_detail);
            $a      = (array) $res;

            foreach ($a as $key => $value) {
                if ($key == $sub_invoice_id) {

                    return $result;
                }
            }
        }

        return false;
    }

    public function getDueFeesByStudent($student_session_id, $date)
    {
        $sql = "SELECT student_fees_master.*,fee_session_groups.fee_groups_id,fee_session_groups.session_id,fee_groups.name,fee_groups.is_system,fee_groups_feetype.amount as `fee_amount`,fee_groups_feetype.id as fee_groups_feetype_id,fee_groups_feetype.fine_type,fee_groups_feetype.due_date,fee_groups_feetype.fine_percentage,fee_groups_feetype.fine_amount,IFNULL(student_fees_deposite.id,0) as `student_fees_deposite_id`, IFNULL(student_fees_deposite.amount_detail,0) as `amount_detail`,students.is_active,classes.class,sections.section,feetype.type,feetype.code FROM `student_fees_master` INNER JOIN fee_session_groups on fee_session_groups.id=student_fees_master.fee_session_group_id INNER JOIN student_session on student_session.id=student_fees_master.student_session_id INNER JOIN students on students.id=student_session.student_id inner join classes on student_session.class_id=classes.id INNER JOIN sections on sections.id=student_session.section_id  INNER JOIN fee_groups_feetype on student_fees_master.fee_session_group_id=fee_groups_feetype.fee_session_group_id inner join fee_groups on fee_groups.id=fee_session_groups.fee_groups_id  INNER JOIN feetype on feetype.id= fee_groups_feetype.feetype_id LEFT JOIN student_fees_deposite on student_fees_deposite.student_fees_master_id=student_fees_master.id and student_fees_deposite.fee_groups_feetype_id=fee_groups_feetype.id WHERE student_fees_master.student_session_id='" . $student_session_id . "' AND student_session.session_id='" . $this->current_session . "' and  fee_session_groups.session_id='" . $this->current_session . "'  and fee_groups_feetype.due_date <  '".$date."' ORDER BY `student_fees_master`.`id` DESC";

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getDueTransportFeeByStudent($student_session_id, $route_pickup_point_id, $date)
    {
        if($student_session_id != NULL && $route_pickup_point_id != NULL){

        $sql = "SELECT student_transport_fees.*,transport_feemaster.month,transport_feemaster.due_date ,transport_feemaster.fine_amount, transport_feemaster.fine_type,transport_feemaster.fine_percentage,IFNULL(student_fees_deposite.id,0) as `student_fees_deposite_id`, IFNULL(student_fees_deposite.amount_detail,0) as `amount_detail` ,route_pickup_point.fees FROM `student_transport_fees` INNER JOIN transport_feemaster on transport_feemaster.id =student_transport_fees.transport_feemaster_id LEFT JOIN student_fees_deposite on student_fees_deposite.student_transport_fee_id=student_transport_fees.id  INNER JOIN route_pickup_point on route_pickup_point.id = student_transport_fees.route_pickup_point_id where student_transport_fees.student_session_id=".$student_session_id." and student_transport_fees.route_pickup_point_id=".$route_pickup_point_id." and transport_feemaster.due_date < '".$date."' ORDER BY student_transport_fees.id asc";
        
        $query = $this->db->query($sql);

        return $query->result();

        }
        return false;
    }

    public function getStudentProcessingFees($student_session_id)
    {
        $sql = "SELECT student_fees_processing.*,student_fees_master.student_session_id,fee_groups.id as fee_group_id,fee_groups.name,feetype.type,feetype.code,gateway_ins.unique_id,fee_groups_feetype.due_date FROM `student_fees_processing` inner join student_fees_master on student_fees_master.id=student_fees_processing.student_fees_master_id INNER JOIN fee_groups_feetype on fee_groups_feetype.id=student_fees_processing.fee_groups_feetype_id and fee_groups_feetype.fee_session_group_id=student_fees_master.fee_session_group_id INNER join feetype on feetype.id=fee_groups_feetype.feetype_id  inner join fee_session_groups on fee_session_groups.id=student_fees_master.fee_session_group_id INNER join fee_groups on fee_groups.id =fee_session_groups.fee_groups_id  inner join gateway_ins on gateway_ins.id=student_fees_processing.gateway_ins_id where student_fees_master.student_session_id=" . $student_session_id. " order by student_fees_processing.id asc";

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getProcessingTransportFees($student_session_id, $route_pickup_point_id)
    {
        $sql = "SELECT student_transport_fees.*, 'Transport Fees' as `transport_fee` ,transport_feemaster.month,transport_feemaster.due_date ,route_pickup_point.fees,transport_feemaster.fine_amount, transport_feemaster.fine_type,transport_feemaster.fine_percentage,student_fees_processing.student_transport_fee_id,IFNULL(student_fees_processing.id,0) as `student_fees_processing_id`, IFNULL(student_fees_processing.amount_detail,0) as `amount_detail`,gateway_ins.unique_id
        FROM `student_transport_fees` INNER JOIN transport_feemaster on transport_feemaster.id =student_transport_fees.transport_feemaster_id INNER JOIN student_fees_processing on student_fees_processing.student_transport_fee_id=student_transport_fees.id INNER JOIN route_pickup_point on route_pickup_point.id = student_transport_fees.route_pickup_point_id inner join gateway_ins on gateway_ins.id=student_fees_processing.gateway_ins_id where student_transport_fees.student_session_id=".$student_session_id." and student_transport_fees.route_pickup_point_id=".$route_pickup_point_id." ORDER BY student_transport_fees.id asc";

        $query = $this->db->query($sql);
        return $query->result();
    }
    
    public function getProcessingFeeByFeeSessionGroup1($fee_session_groups_id, $student_fees_master_id)
    {
        $sql = "SELECT student_fees_master.*,fee_groups_feetype.id as `fee_groups_feetype_id`,fee_groups_feetype.amount,fee_groups_feetype.due_date,fee_groups_feetype.fine_amount,fee_groups_feetype.fee_groups_id,fee_groups.name,fee_groups_feetype.feetype_id,feetype.code,feetype.type, IFNULL(student_fees_processing.id,0) as `student_fees_deposite_id`, IFNULL(student_fees_processing.amount_detail,0) as `amount_detail`,gateway_ins.unique_id FROM `student_fees_master` INNER JOIN fee_session_groups on fee_session_groups.id = student_fees_master.fee_session_group_id INNER JOIN fee_groups_feetype on  fee_groups_feetype.fee_session_group_id = fee_session_groups.id  INNER JOIN fee_groups on fee_groups.id=fee_groups_feetype.fee_groups_id INNER JOIN feetype on feetype.id=fee_groups_feetype.feetype_id LEFT JOIN student_fees_processing on student_fees_processing.student_fees_master_id=student_fees_master.id and student_fees_processing.fee_groups_feetype_id=fee_groups_feetype.id inner join gateway_ins on gateway_ins.id=student_fees_processing.gateway_ins_id WHERE student_fees_master.fee_session_group_id =" . $fee_session_groups_id . " and student_fees_master.id=" . $student_fees_master_id . " order by fee_groups_feetype.due_date ASC";

        $query = $this->db->query($sql);
        return $query->result();
    }

    /**
     * Get student due fee types by date
     * Simplified version for API with session support
     * Gracefully handles null parameters - null means return ALL records for that parameter
     *
     * IMPORTANT: This method matches the web version logic exactly
     * - Joins fee_groups_feetype by fee_groups_id (not fee_session_group_id)
     * - When session_id is provided: filters students enrolled in that session
     * - Shows all fee types for those students regardless of session assignment
     */
    public function getStudentDueFeeTypesByDatee($date, $class_id = null, $section_id = null, $session_id = null)
    {
        // Build WHERE conditions dynamically based on provided parameters
        $where_condition = array();

        // Add session filter only if session_id is explicitly provided
        // Filter student enrollment by session
        if ($session_id !== null && $session_id !== '') {
            $where_condition[] = "student_session.session_id = " . $this->db->escape($session_id);
        }

        // Add class filter only if class_id is provided
        if ($class_id !== null && $class_id !== '') {
            $where_condition[] = "student_session.class_id = " . $this->db->escape($class_id);
        }

        // Add section filter only if section_id is provided
        if ($section_id !== null && $section_id !== '') {
            $where_condition[] = "student_session.section_id = " . $this->db->escape($section_id);
        }

        // Add due date filter (always required)
        $where_condition[] = "fee_groups_feetype.due_date <= " . $this->db->escape($date);

        // Build WHERE clause
        $where_clause = "WHERE " . implode(" AND ", $where_condition);

        // IMPORTANT: This SQL matches the web version exactly
        // Join fee_groups_feetype by fee_groups_id, NOT by fee_session_group_id
        // This ensures all fee types for the fee group are included
        $sql = "SELECT student_fees_master.*,
                fee_session_groups.fee_groups_id,
                fee_session_groups.session_id,
                fee_groups.name,
                fee_groups.is_system,
                fee_groups_feetype.amount as fee_amount,
                fee_groups_feetype.id as fee_groups_feetype_id,
                student_fees_deposite.amount_detail,
                students.admission_no,
                students.roll_no,
                students.admission_date,
                students.firstname,
                students.middlename,
                students.lastname,
                students.father_name,
                students.image,
                students.mobileno,
                students.email,
                students.state,
                students.city,
                students.pincode,
                students.is_active,
                classes.class,
                classes.id as class_id,
                sections.section,
                sections.id as section_id,
                students.id as student_id,
                student_session.id as student_session_id
                FROM student_fees_master
                INNER JOIN fee_session_groups ON fee_session_groups.id = student_fees_master.fee_session_group_id
                INNER JOIN student_session ON student_session.id = student_fees_master.student_session_id
                INNER JOIN students ON students.id = student_session.student_id
                INNER JOIN classes ON student_session.class_id = classes.id
                INNER JOIN sections ON sections.id = student_session.section_id
                INNER JOIN fee_groups ON fee_groups.id = fee_session_groups.fee_groups_id
                INNER JOIN fee_groups_feetype ON fee_groups.id = fee_groups_feetype.fee_groups_id
                LEFT JOIN student_fees_deposite ON student_fees_deposite.student_fees_master_id = student_fees_master.id
                    AND student_fees_deposite.fee_groups_feetype_id = fee_groups_feetype.id
                " . $where_clause . "
                ORDER BY students.admission_no ASC";

        $query = $this->db->query($sql);
        $result = $query->result();
        return $result;
    }

    /**
     * Get student deposit by fee group fee type array
     * Matches the web version exactly for consistency
     *
     * This method retrieves detailed fee information for a specific student session
     * and specific fee types.
     */
    public function studentDepositByFeeGroupFeeTypeArray($student_session_id, $fee_type_array)
    {
        // Validate input
        if (empty($fee_type_array) || !is_array($fee_type_array)) {
            return array();
        }

        $fee_groups_feetype_ids = implode(', ', array_map('intval', $fee_type_array));

        // IMPORTANT: This SQL matches the web version exactly
        // Join fee_groups_feetype by fee_session_group_id to get the correct fee assignments
        $sql = "SELECT fee_groups_feetype.*,
                student_fees_master.student_session_id,
                student_fees_master.amount as previous_amount,
                student_fees_master.is_system,
                student_fees_master.id as student_fees_master_id,
                feetype.code,
                feetype.type,
                IFNULL(student_fees_deposite.id, 0) as student_fees_deposite_id,
                student_fees_deposite.amount_detail,
                fee_groups.name as fee_group_name
                FROM fee_groups_feetype
                INNER JOIN student_fees_master ON student_fees_master.fee_session_group_id = fee_groups_feetype.fee_session_group_id
                INNER JOIN feetype ON feetype.id = fee_groups_feetype.feetype_id
                INNER JOIN fee_groups ON fee_groups.id = fee_groups_feetype.fee_groups_id
                LEFT JOIN student_fees_deposite ON student_fees_deposite.student_fees_master_id = student_fees_master.id
                    AND student_fees_deposite.fee_groups_feetype_id = fee_groups_feetype.id
                WHERE fee_groups_feetype.id IN (" . $fee_groups_feetype_ids . ")
                AND student_fees_master.student_session_id = " . $this->db->escape($student_session_id) . "
                ORDER BY fee_groups_feetype.due_date ASC";

        $query = $this->db->query($sql);
        return $query->result();
    }

    /**
     * Get current session student fees for daily collection
     * Matches web version exactly - returns all fee deposits with student details
     */
    public function getCurrentSessionStudentFeess()
    {
        $sql = "SELECT student_fees_master.*,fee_session_groups.fee_groups_id,fee_session_groups.session_id,fee_groups.name,fee_groups.is_system,fee_groups_feetype.amount as `fee_amount`,fee_groups_feetype.id as fee_groups_feetype_id,student_fees_deposite.id as `student_fees_deposite_id`,student_fees_deposite.amount_detail,students.admission_no , students.roll_no,students.admission_date,students.firstname,students.middlename,  students.lastname,students.father_name,students.image, students.mobileno, students.email ,students.state ,   students.city , students.pincode ,students.is_active,classes.class,sections.section FROM `student_fees_master` INNER JOIN fee_session_groups on fee_session_groups.id=student_fees_master.fee_session_group_id INNER JOIN student_session on student_session.id=student_fees_master.student_session_id INNER JOIN students on students.id=student_session.student_id inner join classes on student_session.class_id=classes.id INNER JOIN sections on sections.id=student_session.section_id inner join fee_groups on fee_groups.id=fee_session_groups.fee_groups_id INNER JOIN fee_groups_feetype on fee_groups.id=fee_groups_feetype.fee_groups_id LEFT JOIN student_fees_deposite on student_fees_deposite.student_fees_master_id=student_fees_master.id and student_fees_deposite.fee_groups_feetype_id=fee_groups_feetype.id ";

        $query  = $this->db->query($sql);
        $result_value = $query->result();

        // Check if transport module is active and add transport fees
        $module = $this->module_model->getPermissionByModulename('transport');
        if (!empty($module) && isset($module['is_active']) && $module['is_active']) {
            $this->db->select('`student_fees_deposite`.*,student_fees_deposite.id as `student_fees_deposite_id`,students.firstname,students.middlename,students.lastname,student_session.class_id,classes.class,sections.section,student_session.section_id,student_session.student_id,"Transport Fees" as name, "Transport Fees" as `type`, "" as `code`,0 as is_system,student_transport_fees.student_session_id,students.admission_no')->from('student_fees_deposite');

            $this->db->join('student_transport_fees', 'student_transport_fees.id = `student_fees_deposite`.`student_transport_fee_id`');
            $this->db->join('transport_feemaster', '`student_transport_fees`.`transport_feemaster_id` = `transport_feemaster`.`id`');
            $this->db->join('student_session', 'student_session.id= `student_transport_fees`.`student_session_id`', 'INNER');
            $this->db->join('classes', 'classes.id= student_session.class_id');
            $this->db->join('sections', 'sections.id= student_session.section_id');
            $this->db->join('students', 'students.id=student_session.student_id');
            $this->db->order_by('student_fees_deposite.id','desc');

            $query1 = $this->db->get();
            $result_value1 = $query1->result();
        } else {
            $result_value1 = array();
        }

        // Merge regular fees and transport fees
        if (empty($result_value)) {
            $result_value2 = $result_value1;
        } elseif (empty($result_value1)) {
            $result_value2 = $result_value;
        } else {
            $result_value2 = array_merge($result_value, $result_value1);
        }

        return $result_value2;
    }

    /**
     * Get other fees for current session for daily collection
     * Matches web version - returns additional fees deposits
     */
    public function getOtherfeesCurrentSessionStudentFeess()
    {
        $sql = "SELECT
            student_fees_masteradding.*,
            fee_session_groupsadding.fee_groups_id,
            fee_session_groupsadding.session_id,
            fee_groupsadding.name,
            fee_groupsadding.is_system,
            fee_groups_feetypeadding.amount as `fee_amount`,
            fee_groups_feetypeadding.id as fee_groups_feetype_id,
            student_fees_depositeadding.id as `student_fees_deposite_id`,
            student_fees_depositeadding.amount_detail,
            students.admission_no,
            students.roll_no,
            students.admission_date,
            students.firstname,
            students.middlename,
            students.lastname,
            students.father_name,
            students.image,
            students.mobileno,
            students.email,
            students.state,
            students.city,
            students.pincode,
            students.is_active,
            classes.class,
            sections.section
        FROM `student_fees_masteradding`
        INNER JOIN fee_session_groupsadding ON fee_session_groupsadding.id = student_fees_masteradding.fee_session_group_id
        INNER JOIN student_session ON student_session.id = student_fees_masteradding.student_session_id
        INNER JOIN students ON students.id = student_session.student_id
        INNER JOIN classes ON student_session.class_id = classes.id
        INNER JOIN sections ON sections.id = student_session.section_id
        INNER JOIN fee_groupsadding ON fee_groupsadding.id = fee_session_groupsadding.fee_groups_id
        INNER JOIN fee_groups_feetypeadding ON fee_groupsadding.id = fee_groups_feetypeadding.fee_groups_id
        LEFT JOIN student_fees_depositeadding ON student_fees_depositeadding.student_fees_master_id = student_fees_masteradding.id
            AND student_fees_depositeadding.fee_groups_feetype_id = fee_groups_feetypeadding.id";

        $query = $this->db->query($sql);
        return $query->result();
    }

    /**
     * Get type wise balance report
     * Simplified version for API
     */
    public function gettypewisereportt($session = null, $feetype_id = null, $group_id = null, $class_id = null, $section_id = null)
    {
        $this->db->select('fee_groups.name as feegroupname, student_fees_master.id as stfeemasid,
                          fee_groups_feetype.amount as total, fee_groups_feetype.id as fgtid,
                          fee_groups_feetype.fine_amount as fine, feetype.type, sections.section,
                          classes.class, students.admission_no, students.mobileno, students.firstname,
                          students.middlename, students.lastname')
                 ->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'classes.id = student_session.class_id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('student_fees_master', 'student_fees_master.student_session_id = student_session.id');
        $this->db->join('fee_session_groups', 'fee_session_groups.id = student_fees_master.fee_session_group_id');
        $this->db->join('fee_groups', 'fee_session_groups.fee_groups_id = fee_groups.id');
        $this->db->join('fee_groups_feetype', 'fee_groups_feetype.fee_session_group_id = fee_session_groups.id');
        $this->db->join('feetype', 'feetype.id = fee_groups_feetype.feetype_id');

        // Apply filters
        if ($session != null) {
            $this->db->where('student_session.session_id', $session);
        }

        if ($feetype_id != null && !empty($feetype_id) && is_array($feetype_id)) {
            $this->db->where_in('fee_groups_feetype.feetype_id', $feetype_id);
        }

        if ($group_id != null && !empty($group_id) && is_array($group_id)) {
            $this->db->where_in('fee_groups.id', $group_id);
        }

        if ($class_id != null && !empty($class_id)) {
            if (is_array($class_id)) {
                $this->db->where_in('student_session.class_id', $class_id);
            } else {
                $this->db->where('student_session.class_id', $class_id);
            }
        }

        if ($section_id != null && !empty($section_id)) {
            if (is_array($section_id)) {
                $this->db->where_in('student_session.section_id', $section_id);
            } else {
                $this->db->where('student_session.section_id', $section_id);
            }
        }

        $this->db->order_by('students.id', 'desc');

        $query = $this->db->get();
        $results = $query->result_array();

        // Calculate paid amounts
        foreach ($results as &$result) {
            $amountres = $this->getdepositeamount($result['stfeemasid'], $result['fgtid']);
            if ($amountres) {
                $amount_detail = json_decode($amountres, true);
                $total_amount = 0;
                $total_fine = 0;
                $total_discount = 0;
                foreach ($amount_detail as $detail) {
                    $total_amount += $detail['amount'];
                    $total_discount += $detail['amount_discount'];
                    $total_fine += $detail['amount_fine'];
                }
                $result['total_amount'] = $total_amount;
                $result['total_fine'] = $total_fine;
                $result['total_discount'] = $total_discount;
                $result['balance'] = $result['total'] - $total_amount;
            } else {
                $result['total_amount'] = 0;
                $result['total_fine'] = 0;
                $result['total_discount'] = 0;
                $result['balance'] = $result['total'];
            }
        }

        return $results;
    }

    /**
     * Get deposited amount for a student fee master and fee group fee type
     * Helper method for type wise report
     */
    public function getdepositeamount($student_fees_master_id, $fee_groups_feetype_id)
    {
        $this->db->select('amount_detail');
        $this->db->from('student_fees_deposite');
        $this->db->where('student_fees_master_id', $student_fees_master_id);
        $this->db->where('fee_groups_feetype_id', $fee_groups_feetype_id);
        $query = $this->db->get();
        $result = $query->row();

        return $result ? $result->amount_detail : null;
    }

    /**
     * Get staff members who can receive fees
     */
    public function get_feesreceived_by()
    {
        $result = $this->db->select('CONCAT_WS(" ",staff.name,staff.surname) as name, staff.employee_id,staff.id')
                           ->from('staff')
                           ->join('staff_roles', 'staff.id=staff_roles.staff_id')
                           ->where('staff.is_active', '1')
                           ->get()
                           ->result_array();
        $data = array();
        foreach ($result as $key => $value) {
            $data[$value['id']] = $value['name'] . " (" . $value['employee_id'] . ")";
        }
        return $data;
    }

    /**
     * Find objects by date range
     */
    public function findObjectById($array, $st_date, $ed_date)
    {
        $ar = json_decode($array->amount_detail);
        $result_array = array();

        if (!empty($ar)) {
            for ($i = $st_date; $i <= $ed_date; $i += 86400) {
                $find = date('Y-m-d', $i);
                foreach ($ar as $row_key => $row_value) {
                    if ($row_value->date == $find) {
                        $result_array[] = $row_value;
                    }
                }
            }
        }

        return $result_array;
    }

    /**
     * Find objects by collect ID and date range
     */
    public function findObjectByCollectId($array, $st_date, $ed_date, $receivedBy)
    {
        $ar = json_decode($array->amount_detail);
        $result_array = array();

        if (!empty($ar)) {
            for ($i = $st_date; $i <= $ed_date; $i += 86400) {
                $find = date('Y-m-d', $i);
                foreach ($ar as $row_key => $row_value) {
                    if (isset($row_value->received_by)) {
                        $match = false;

                        if (is_array($receivedBy)) {
                            $match = in_array($row_value->received_by, $receivedBy);
                        } else {
                            $match = ($row_value->received_by == $receivedBy);
                        }

                        if ($row_value->date == $find && $match) {
                            $result_array[] = $row_value;
                        }
                    }
                }
            }
        }

        return $result_array;
    }

    /**
     * Get fee collection report with filters
     * Supports graceful null/empty parameter handling
     */
    public function getFeeCollectionReport($start_date, $end_date, $feetype_id = null, $received_by = null, $group = null, $class_id = null, $section_id = null, $session_id = null)
    {
        // Get regular fees
        $this->db->select('student_fees_deposite.*,students.firstname,students.middlename,students.lastname,student_session.class_id,classes.class,sections.section,student_session.section_id,student_session.student_id,fee_groups.name, feetype.type, feetype.code,feetype.is_system,student_fees_master.student_session_id,students.admission_no')
                 ->from('student_fees_deposite');
        $this->db->join('fee_groups_feetype', 'fee_groups_feetype.id = student_fees_deposite.fee_groups_feetype_id');
        $this->db->join('fee_groups', 'fee_groups.id = fee_groups_feetype.fee_groups_id');
        $this->db->join('feetype', 'feetype.id = fee_groups_feetype.feetype_id');
        $this->db->join('student_fees_master', 'student_fees_master.id=student_fees_deposite.student_fees_master_id');
        $this->db->join('student_session', 'student_session.id= student_fees_master.student_session_id', 'left');
        $this->db->join('classes', 'classes.id= student_session.class_id');
        $this->db->join('sections', 'sections.id= student_session.section_id');
        $this->db->join('students', 'students.id=student_session.student_id');

        // Apply filters only if provided
        // Handle both single values and arrays for multi-select functionality - feetype_id
        if ($feetype_id !== null && $feetype_id !== '' && $feetype_id !== 'transport_fees') {
            if (is_array($feetype_id) && count($feetype_id) > 0) {
                // Array of fee type IDs - use WHERE_IN
                $this->db->where_in('fee_groups_feetype.feetype_id', $feetype_id);
            } elseif (!is_array($feetype_id)) {
                // Single fee type ID - use WHERE
                $this->db->where('fee_groups_feetype.feetype_id', $feetype_id);
            }
        }

        if ($class_id !== null && $class_id !== '') {
            $this->db->where('student_session.class_id', $class_id);
        }

        if ($section_id !== null && $section_id !== '') {
            $this->db->where('student_session.section_id', $section_id);
        }

        if ($session_id !== null && $session_id !== '') {
            $this->db->where('student_session.session_id', $session_id);
        }

        $this->db->order_by('student_fees_deposite.id', 'desc');

        $query = $this->db->get();
        $result_value = $query->result();

        // Get transport fees if module is active
        $result_value1 = array();
        $module = $this->module_model->getPermissionByModulename('transport');

        if (!empty($module) && isset($module['is_active']) && $module['is_active']) {
            if ($feetype_id === null || $feetype_id === '' || $feetype_id === 'transport_fees') {
                $this->db->select('student_fees_deposite.*,students.firstname,students.middlename,students.lastname,student_session.class_id,classes.class,sections.section,student_session.section_id,student_session.student_id,"Transport Fees" as name, "Transport Fees" as type, "" as code,0 as is_system,student_transport_fees.student_session_id,students.admission_no')
                         ->from('student_fees_deposite');
                $this->db->join('student_transport_fees', 'student_transport_fees.id = student_fees_deposite.student_transport_fee_id');
                $this->db->join('transport_feemaster', 'student_transport_fees.transport_feemaster_id = transport_feemaster.id');
                $this->db->join('student_session', 'student_session.id= student_transport_fees.student_session_id', 'INNER');
                $this->db->join('classes', 'classes.id= student_session.class_id');
                $this->db->join('sections', 'sections.id= student_session.section_id');
                $this->db->join('students', 'students.id=student_session.student_id');

                if ($class_id !== null && $class_id !== '') {
                    $this->db->where('student_session.class_id', $class_id);
                }

                if ($section_id !== null && $section_id !== '') {
                    $this->db->where('student_session.section_id', $section_id);
                }

                if ($session_id !== null && $session_id !== '') {
                    $this->db->where('student_session.session_id', $session_id);
                }

                $query1 = $this->db->get();
                $result_value1 = $query1->result();
            }
        }

        // Merge results
        if (empty($result_value)) {
            $result_value2 = $result_value1;
        } elseif (empty($result_value1)) {
            $result_value2 = $result_value;
        } else {
            $result_value2 = array_merge($result_value, $result_value1);
        }

        // Process results by date range
        $return_array = array();
        if (!empty($result_value2)) {
            $st_date = strtotime($start_date);
            $ed_date = strtotime($end_date);

            foreach ($result_value2 as $key => $value) {
                if ($received_by !== null && $received_by !== '') {
                    $return = $this->findObjectByCollectId($value, $st_date, $ed_date, $received_by);
                } else {
                    $return = $this->findObjectById($value, $st_date, $ed_date);
                }

                if (!empty($return)) {
                    foreach ($return as $r_key => $r_value) {
                        $a = array();
                        $a['id'] = $value->id;
                        $a['student_fees_master_id'] = isset($value->student_fees_master_id) ? $value->student_fees_master_id : '';
                        $a['fee_groups_feetype_id'] = isset($value->fee_groups_feetype_id) ? $value->fee_groups_feetype_id : '';
                        $a['admission_no'] = $value->admission_no;
                        $a['firstname'] = $value->firstname;
                        $a['middlename'] = isset($value->middlename) ? $value->middlename : '';
                        $a['lastname'] = $value->lastname;
                        $a['class_id'] = $value->class_id;
                        $a['class'] = $value->class;
                        $a['section'] = $value->section;
                        $a['section_id'] = $value->section_id;
                        $a['student_id'] = $value->student_id;
                        $a['name'] = $value->name;
                        $a['type'] = $value->type;
                        $a['code'] = isset($value->code) ? $value->code : '';
                        $a['student_session_id'] = $value->student_session_id;
                        $a['is_system'] = isset($value->is_system) ? $value->is_system : 0;
                        $a['amount'] = $r_value->amount;
                        $a['date'] = $r_value->date;
                        $a['amount_discount'] = isset($r_value->amount_discount) ? $r_value->amount_discount : 0;
                        $a['amount_fine'] = isset($r_value->amount_fine) ? $r_value->amount_fine : 0;
                        $a['description'] = isset($r_value->description) ? $r_value->description : '';
                        $a['payment_mode'] = isset($r_value->payment_mode) ? $r_value->payment_mode : '';
                        $a['inv_no'] = isset($r_value->inv_no) ? $r_value->inv_no : '';
                        $a['received_by'] = isset($r_value->received_by) ? $r_value->received_by : '';

                        $return_array[] = $a;
                    }
                }
            }
        }

        return $return_array;
    }

    /**
     * Get student fees by class, section, and student
     * Used for Report By Name API
     * Returns detailed fee structure with fee groups and payment history
     *
     * @param int|null $class_id Class ID filter
     * @param int|null $section_id Section ID filter
     * @param int|null $student_id Student ID filter
     * @param int|null $session_id Session ID filter (defaults to current session if null)
     * @return array Array of student fees data
     */
    public function getStudentFeesByClassSectionStudent($class_id = null, $section_id = null, $student_id = null, $session_id = null)
    {
        // Use current session if session_id not provided
        if ($session_id === null) {
            $session_id = $this->current_session;
        }

        $where_condition = array();
        if ($class_id != null) {
            $where_condition[] = " and student_session.class_id=" . $this->db->escape($class_id);
        }
        if ($section_id != null) {
            $where_condition[] = " and student_session.section_id=" . $this->db->escape($section_id);
        }
        if ($student_id != null) {
            $where_condition[] = " and student_session.student_id=" . $this->db->escape($student_id);
        }

        $where_condition_string = implode(" ", $where_condition);

        $sql = "SELECT student_fees_master.*,student_session.id as `student_session_id`,students.firstname,students.middlename,students.lastname,student_session.class_id,classes.class,sections.section,students.category_id,students.image,students.id as student_id,students.father_name,students.admission_no,students.mobileno,students.roll_no,students.rte, IFNULL(categories.category, '') as `category`
                FROM `student_fees_master`
                INNER JOIN student_session on student_session.id=student_fees_master.student_session_id
                INNER JOIN students on students.id=student_session.student_id
                INNER JOIN classes on classes.id =student_session.class_id
                LEFT JOIN categories on students.category_id = categories.id
                INNER JOIN sections on sections.id=student_session.section_id
                WHERE student_session.session_id=" . $this->db->escape($session_id) . $where_condition_string;

        $query = $this->db->query($sql);
        $result = $query->result();
        $student_fees = array();

        if (!empty($result)) {
            foreach ($result as $result_key => $result_value) {
                $fee_session_group_id = $result_value->fee_session_group_id;
                $student_fees_master_id = $result_value->id;
                $result_value->fees = $this->getDueFeeByFeeSessionGroup($fee_session_group_id, $student_fees_master_id);

                if ($result_value->is_system != 0) {
                    $result_value->fees[0]->amount = $result_value->amount;
                }

                if (!array_key_exists($result_value->student_session_id, $student_fees)) {
                    $student_fees[$result_value->student_session_id] = array(
                        'student_session_id' => $result_value->student_session_id,
                        'firstname' => $result_value->firstname,
                        'student_id' => $result_value->student_id,
                        'middlename' => $result_value->middlename,
                        'lastname' => $result_value->lastname,
                        'class_id' => $result_value->class_id,
                        'class' => $result_value->class,
                        'section' => $result_value->section,
                        'father_name' => $result_value->father_name,
                        'admission_no' => $result_value->admission_no,
                        'mobileno' => $result_value->mobileno,
                        'roll_no' => $result_value->roll_no,
                        'category_id' => $result_value->category_id,
                        'category' => $result_value->category,
                        'rte' => $result_value->rte,
                        'image' => $result_value->image
                    );

                    // Get student discount information
                    $student_fees[$result_value->student_session_id]['student_discount_fee'] = $this->feediscount_model->getStudentFeesDiscount($result_value->student_session_id);
                }

                $student_fees[$result_value->student_session_id]['fees'][] = $result_value->fees;
            }
        }

        return $student_fees;
    }

    /**
     * Get student fees including transport fees
     * Used for Total Student Academic Report API
     */
    public function getTransStudentFees($student_session_id)
    {
        // Get regular fees
        $sql = "SELECT `student_fees_master`.*,fee_groups.name FROM `student_fees_master`
                INNER JOIN fee_session_groups on student_fees_master.fee_session_group_id=fee_session_groups.id
                INNER JOIN fee_groups on fee_groups.id=fee_session_groups.fee_groups_id
                WHERE `student_session_id` = " . $this->db->escape($student_session_id) . "
                ORDER BY `student_fees_master`.`id`";
        $query = $this->db->query($sql);
        $result_value = $query->result();

        // Get transport fees if module is active
        $module = $this->module_model->getPermissionByModulename('transport');
        $result_value1 = array();

        if (!empty($module) && isset($module['is_active']) && $module['is_active']) {
            $this->db->select('student_transport_fees.id,0 as previous_balance_amount,route_pickup_point.fees as amount,students.firstname,students.middlename,students.lastname,student_session.class_id,classes.class,sections.section,student_session.section_id,student_session.student_id,"Transport Fees" as fee_group,"Transport Fees" as name, "Transport Fees" as `fee_type`, "" as `fee_code`,0 as is_system,student_transport_fees.student_session_id,students.admission_no, `student_session`.`id` as `student_session_id`,0 as is_system, "" as fee_session_group_id')->from('student_transport_fees');
            $this->db->join('transport_feemaster', '`student_transport_fees`.`transport_feemaster_id` = `transport_feemaster`.`id`');
            $this->db->join('student_session', 'student_session.id= `student_transport_fees`.`student_session_id`', 'INNER');
            $this->db->join('route_pickup_point', 'route_pickup_point.id = student_transport_fees.route_pickup_point_id');
            $this->db->join('classes', 'classes.id= student_session.class_id');
            $this->db->join('sections', 'sections.id= student_session.section_id');
            $this->db->join('students', 'students.id=student_session.student_id');
            $this->db->where('student_session.id', $student_session_id);

            $query1 = $this->db->get();
            $result_value1 = $query1->result();
        }

        // Merge results
        if (empty($result_value)) {
            $result_value2 = $result_value1;
        } elseif (empty($result_value1)) {
            $result_value2 = $result_value;
        } else {
            $result_value2 = array_merge($result_value, $result_value1);
        }

        // Process fees - get detailed fee information
        if (!empty($result_value2)) {
            foreach ($result_value2 as $result_key => $result_value) {
                $result_value->fees = array();
                $fee_session_group_id = isset($result_value->fee_session_group_id) ? $result_value->fee_session_group_id : null;
                $student_fees_master_id = isset($result_value->id) ? $result_value->id : null;

                if (empty($result_value->fee_session_group_id)) {
                    // For transport fees or fees without fee session group
                    $fee = new stdClass();
                    $fee->amount_detail = isset($result_value->amount_detail) ? $result_value->amount_detail : '';
                    $fee->amount = isset($result_value->amount) ? $result_value->amount : 0;
                    $result_value->fees[0] = $fee;
                } else {
                    // For regular fees - get detailed fee breakdown
                    $result_value->fees = $this->getDueFeeByFeeSessionGroup($fee_session_group_id, $student_fees_master_id);
                }

                if (isset($result_value->is_system) && $result_value->is_system != 0) {
                    if (isset($result_value->fees[0])) {
                        $result_value->fees[0]->amount = isset($result_value->amount) ? $result_value->amount : 0;
                    }
                }
            }
        }

        return $result_value2;
    }

}

