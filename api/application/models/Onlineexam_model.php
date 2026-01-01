<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Onlineexam_model extends CI_model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($id = null, $publish = null)
    {
        $this->db->select('onlineexam.*')->from('onlineexam');
        if ($id != null) {
            $this->db->where('onlineexam.id', $id);
        } else {
            $this->db->order_by('onlineexam.id');
        }
        if ($publish != null) {
            $this->db->where('is_active', ($publish == "publish") ? 1 : 0);
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result();
        }
    }

    public function getExamByOnlineexamStudent($onlineexam_student_id)
    {
        $this->db->select('onlineexam_students.*,onlineexam.id as `onlineexam_id`,onlineexam.is_quiz');
        $this->db->from('onlineexam_students');
        $this->db->join('onlineexam', 'onlineexam.id = onlineexam_students.onlineexam_id');
        $this->db->where('onlineexam_students.id', $onlineexam_student_id);
        $query = $this->db->get();
        return $query->row();
    }

    public function updateExamSubmitted($onlineexam_student_id)
    {
        $this->db->where('id', $onlineexam_student_id)->update('onlineexam_students', array('is_attempted' => 1));
        return array('status' => 200, 'message' => 'Data has been updated.');
    }

    public function getStudentexam($student_session_id)
    {
        $today_date = date('Y-m-d H:i:s');
        $this->db->select('onlineexam.*,onlineexam_students.id as `onlineexam_student_id`,onlineexam_students.is_attempted as `is_attempted`,(select count(*) from onlineexam_attempts WHERE onlineexam_attempts.onlineexam_student_id = onlineexam_students.id) as counter');
        $this->db->from('onlineexam');
        $this->db->join("onlineexam_students", "onlineexam_students.onlineexam_id=onlineexam.id", "left");
        $this->db->where("onlineexam_students.student_session_id", $student_session_id);
        $this->db->where("onlineexam.is_active", 1);
        $this->db->where('onlineexam.exam_to>= ', $today_date);
        $this->db->order_by('onlineexam.exam_from', 'desc');
        $query = $this->db->get();
        return $query->result();
    }

    public function getstudentclosedexamlist($student_session_id)
    {
        $today_date = date('Y-m-d H:i:s');

        $this->db->select('onlineexam.*,onlineexam_students.id as `onlineexam_student_id`,onlineexam_students.is_attempted as `is_attempted`,(select count(*) from onlineexam_attempts WHERE onlineexam_attempts.onlineexam_student_id = onlineexam_students.id) as counter');
        $this->db->from('onlineexam');
        $this->db->where("onlineexam_students.student_session_id", $student_session_id);
        $this->db->join("onlineexam_students", "onlineexam_students.onlineexam_id=onlineexam.id", "left");
        $this->db->order_by('onlineexam.exam_from', 'desc');
        $this->db->where('onlineexam.exam_to  <= ', $today_date);
        $query = $this->db->get();
        return $query->result();
    }

    public function getExamQuestions($id = null, $random_type = false)
    {
        $this->db->select('onlineexam_questions.*,questions.subject_id,questions.question,questions.opt_a,questions.opt_b,questions.opt_c,questions.opt_d,questions.opt_e,questions.correct,questions.question_type')->from('onlineexam_questions');
        $this->db->join('questions', 'questions.id = onlineexam_questions.question_id');
        $this->db->where('onlineexam_questions.onlineexam_id', $id);
        if ($random_type) {
            $this->db->order_by('rand()');
        } else {
            $this->db->order_by('onlineexam_questions.id', 'DESC');
        }
        $query = $this->db->get();
        return $query->result();
    }

    public function examstudentsID($student_session_id, $onlineexam_id)
    {
        $this->db->select('onlineexam_students.*,IF((select count(*) from onlineexam_student_results WHERE onlineexam_student_results.onlineexam_student_id = onlineexam_students.id) > 0,1,0) as is_submitted');
        $this->db->from('onlineexam_students');
        $this->db->where('student_session_id', $student_session_id);
        $this->db->where('onlineexam_id', $onlineexam_id);
        $query = $this->db->get();
        return $query->row();
    }

    public function getStudentAttemts($onlineexam_student_id)
    {
        $this->db->where('onlineexam_student_id', $onlineexam_student_id);
        $total_rows = $this->db->count_all_results('onlineexam_attempts');
        return $total_rows;
    }

    public function addStudentAttemts($data)
    {
        $this->db->insert('onlineexam_attempts', $data);
        return $this->db->insert_id();
    }

    public function add($data, $onlineexam_student_id)
    {
        $status = 0;
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        $this->db->where('onlineexam_student_id', $onlineexam_student_id);
        $q = $this->db->get('onlineexam_student_results');
        if ($q->num_rows() > 0) {
            $status = 2;
        } else {
            $this->db->insert_batch('onlineexam_student_results', $data);
            $status = 1;
        }

        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return 0;
        } else {
            return $status;
        }
    }

    public function getResultByStudent($onlineexam_student_id, $exam_id)
    {
        $query = "SELECT onlineexam_questions.*,subjects.name as subject_name,subjects.code as subjects_code, onlineexam_student_results.id as `onlineexam_student_result_id`,questions.question,questions.question_type,onlineexam_student_results.marks as `score_marks`,questions.opt_a, questions.opt_b,questions.opt_c,questions.opt_d,questions.opt_e,questions.correct,IFNULL(onlineexam_student_results.select_option, '') as `select_option`,IFNULL(onlineexam_student_results.remark, '') as `remark` FROM `onlineexam_questions` left JOIN onlineexam_student_results on onlineexam_student_results.onlineexam_question_id=onlineexam_questions.id and onlineexam_student_results.onlineexam_student_id=" . $this->db->escape($onlineexam_student_id) . " INNER JOIN questions on questions.id=onlineexam_questions.question_id INNER JOIN subjects on subjects.id=questions.subject_id WHERE onlineexam_questions.onlineexam_id=" . $this->db->escape($exam_id);
        $query = $this->db->query($query);
        return $query->result();
    }

    public function getquestiondetails($exam_id)
    {
        $query = "SELECT count(onlineexam_questions.id) as total_question,SUM(CASE WHEN questions.question_type= 'descriptive' THEN 1 ELSE 0 END) AS total_descriptive FROM `onlineexam_questions` left join questions on questions.id=onlineexam_questions.question_id where onlineexam_questions.onlineexam_id =" . $this->db->escape($exam_id) . " group by onlineexam_questions.onlineexam_id";
        $query = $this->db->query($query);
        return $query->row();
    }

    public function searchAllOnlineExamStudents($onlineexam_id, $class_id = null, $section_id = null, $is_attempted = null)
    {
        $this->db->select('class_sections.id as class_section_id,classes.id AS `class_id`,student_session.id as student_session_id,students.id,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname,students.middlename,students.lastname,students.image,   students.mobileno,students.email,students.state,students.city , students.pincode,students.religion,students.dob ,students.current_address,students.permanent_address,IFNULL(students.category_id, 0) as `category_id`,IFNULL(categories.category, "") as `category`,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name, students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.rte,students.gender,IFNULL(onlineexam_students.id, 0) as onlineexam_student_id,IFNULL(onlineexam_students.student_session_id, 0) as onlineexam_student_session_id,IFNULL(onlineexam_students.rank, 0) as exam_rank,onlineexam_students.is_attempted')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id', 'left');
        $this->db->join('class_sections', 'class_sections.class_id = classes.id and class_sections.section_id = sections.id');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');
        $this->db->join('onlineexam_students', 'onlineexam_students.student_session_id = student_session.id and onlineexam_students.onlineexam_id = ' . $this->db->escape($onlineexam_id), 'inner');
        $this->db->where('students.is_active', 'yes');

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

        if ($is_attempted != null) {
            $this->db->where('onlineexam_students.is_attempted', $is_attempted);
        }

        $this->db->order_by('onlineexam_students.rank', 'asc');
        $this->db->order_by('students.id', 'asc');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $result = $query->result_array();
        } else {
            $result = array();
        }
        return $result;
    }

    public function onlineexamReport($condition)
    {
        $where_clause = "1=1";

        // Add condition with AND if condition is not empty
        if (!empty($condition)) {
            $where_clause .= " AND " . $condition;
        }

        $query = "SELECT onlineexam.*,(select count(*) from onlineexam_students WHERE onlineexam_students.onlineexam_id = onlineexam.id) as assign,(select count(*) from onlineexam_questions where onlineexam_questions.onlineexam_id=onlineexam.id) as questions FROM `onlineexam` inner join onlineexam_students on onlineexam_students.onlineexam_id=onlineexam.id inner join student_session on student_session.id=onlineexam_students.student_session_id where " . $where_clause . " GROUP BY onlineexam.id ORDER BY onlineexam.id DESC";

        $query = $this->db->query($query);
        $result = $query->result();

        return json_encode(array(
            'data' => $result,
            'recordsTotal' => count($result),
            'recordsFiltered' => count($result)
        ));
    }

    public function onlineexamatteptreport($condition)
    {
        $where_clause = "students.is_active='yes'";

        // Add condition with AND if condition is not empty
        if (!empty($condition)) {
            $where_clause .= " AND " . $condition;
        }

        $query = "SELECT student_session.id,students.admission_no,students.id as sid, CONCAT_WS(' ',firstname,middlename,lastname) as name,firstname,middlename,lastname,GROUP_CONCAT(onlineexam.id,'@',onlineexam.exam,'@',onlineexam.attempt,'@',onlineexam.exam_from,'@',onlineexam.exam_to,'@',onlineexam.duration,'@',onlineexam.passing_percentage,'@',onlineexam.is_active,'@',onlineexam.publish_result) as exams,GROUP_CONCAT(onlineexam_students.onlineexam_id) as attempt,`classes`.`id` AS `class_id`, `student_session`.`id` as `student_session_id`, `students`.`id`, `classes`.`class`, `sections`.`id` AS `section_id`, `sections`.`section`, `students`.`id`, `students`.`admission_no` FROM `student_session` INNER JOIN onlineexam_students on onlineexam_students.student_session_id=student_session.id INNER JOIN students on students.id=student_session.student_id JOIN `classes` ON `student_session`.`class_id` = `classes`.`id` JOIN `sections` ON `sections`.`id` = `student_session`.`section_id` LEFT JOIN `categories` ON `students`.`category_id` = `categories`.`id` INNER JOIN onlineexam on onlineexam_students.onlineexam_id=onlineexam.id WHERE " . $where_clause;

        $query .= " GROUP BY student_session.id ORDER BY students.id";

        $query = $this->db->query($query);
        $std_data = $query->result();

        if (!empty($std_data)) {
            return json_encode(array(
                'data' => $std_data,
                'recordsTotal' => count($std_data),
                'recordsFiltered' => count($std_data)
            ));
        } else {
            return json_encode(array(
                'data' => array(),
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ));
        }
    }
}
