<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Bookissue_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function book_issuedByMemberID($member_id)
    {
        $this->db->select('book_issues.return_date,book_issues.duereturn_date as `due_return_date`,books.book_no,book_issues.issue_date,book_issues.is_returned,books.book_title,books.author,books.book_no,books.subject')
            ->from('book_issues')
            ->join('libarary_members', 'libarary_members.id = book_issues.member_id', 'left')
            ->join('books', 'books.id = book_issues.book_id', 'left')
            ->where('libarary_members.id', $member_id)
            ->order_by('book_issues.is_returned', 'asc');
        $result = $this->db->get();
        return $result->result_array();
    }

    /**
     * Get issue return report data
     * Simplified version for API
     */
    public function getIssueReturnReport($start_date, $end_date)
    {
        $condition = "";
        if ($start_date != "" && $end_date != "") {
            $condition = " and date_format(book_issues.issue_date,'%Y-%m-%d') between '" . $this->db->escape_str($start_date) . "' and '" . $this->db->escape_str($end_date) . "'";
        }

        $sql = "SELECT book_issues.id, book_issues.issue_date, book_issues.return_date, book_issues.is_returned,
                books.book_title, books.book_no, books.author,
                libarary_members.id as members_id, libarary_members.library_card_no, libarary_members.member_type,
                students.firstname, students.middlename, students.lastname, students.admission_no as admission,
                staff.name as fname, staff.surname as lname, staff.employee_id
                FROM book_issues
                LEFT JOIN books ON books.id = book_issues.book_id
                LEFT JOIN libarary_members ON libarary_members.id = book_issues.member_id
                LEFT JOIN students ON students.id = libarary_members.member_id AND libarary_members.member_type = 'student'
                LEFT JOIN staff ON staff.id = libarary_members.member_id AND libarary_members.member_type = 'teacher'
                WHERE 1=1 " . $condition . "
                ORDER BY book_issues.issue_date DESC";

        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * Get student book issue report data
     * Simplified version for API
     */
    public function getStudentBookIssueReport($start_date, $end_date, $member_type = null)
    {
        $condition = "";
        if ($start_date != "" && $end_date != "") {
            $condition = " and date_format(book_issues.issue_date,'%Y-%m-%d') between '" . $this->db->escape_str($start_date) . "' and '" . $this->db->escape_str($end_date) . "'";
        }
        if (!empty($member_type)) {
            $condition .= " and libarary_members.member_type = '" . $this->db->escape_str($member_type) . "'";
        }

        $sql = "SELECT book_issues.id, book_issues.issue_date, book_issues.duereturn_date, book_issues.return_date, book_issues.is_returned,
                books.book_title, books.book_no, books.author,
                libarary_members.id as members_id, libarary_members.library_card_no, libarary_members.member_type,
                students.firstname, students.middlename, students.lastname, students.admission_no as admission,
                staff.name as staff_name, staff.surname as staff_surname, staff.employee_id
                FROM book_issues
                LEFT JOIN books ON books.id = book_issues.book_id
                LEFT JOIN libarary_members ON libarary_members.id = book_issues.member_id
                LEFT JOIN students ON students.id = libarary_members.member_id AND libarary_members.member_type = 'student'
                LEFT JOIN staff ON staff.id = libarary_members.member_id AND libarary_members.member_type = 'teacher'
                WHERE 1=1 " . $condition . "
                ORDER BY book_issues.issue_date DESC";

        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * Get book due report data
     * Simplified version for API
     */
    public function getBookDueReport($start_date, $end_date, $member_type = null)
    {
        $condition = "";
        if ($start_date != "" && $end_date != "") {
            $condition = " and date_format(book_issues.duereturn_date,'%Y-%m-%d') between '" . $this->db->escape_str($start_date) . "' and '" . $this->db->escape_str($end_date) . "'";
        }
        if (!empty($member_type)) {
            $condition .= " and libarary_members.member_type = '" . $this->db->escape_str($member_type) . "'";
        }

        $sql = "SELECT book_issues.id, book_issues.issue_date, book_issues.duereturn_date, book_issues.return_date, book_issues.is_returned,
                books.book_title, books.book_no, books.author,
                libarary_members.id as members_id, libarary_members.library_card_no, libarary_members.member_type,
                students.firstname, students.middlename, students.lastname, students.admission_no as admission,
                staff.name as fname, staff.surname as lname, staff.employee_id
                FROM book_issues
                LEFT JOIN books ON books.id = book_issues.book_id
                LEFT JOIN libarary_members ON libarary_members.id = book_issues.member_id
                LEFT JOIN students ON students.id = libarary_members.member_id AND libarary_members.member_type = 'student'
                LEFT JOIN staff ON staff.id = libarary_members.member_id AND libarary_members.member_type = 'teacher'
                WHERE book_issues.is_returned = '0' " . $condition . "
                ORDER BY book_issues.duereturn_date ASC";

        $query = $this->db->query($sql);
        return $query->result_array();
    }

}
