<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Book_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    /**
     * Get book inventory report data
     * Simplified version for API
     */
    public function getBookInventoryReport($start_date, $end_date)
    {
        $condition = "";
        if ($start_date != "" && $end_date != "") {
            $condition = " and date_format(books.postdate,'%Y-%m-% d') between '" . $this->db->escape_str($start_date) . "' and '" . $this->db->escape_str($end_date) . "'";
        }

        $sql = "SELECT books.*, 
                IFNULL(book_count.total_issue, 0) as total_issue,
                (books.qty - IFNULL(book_count.total_issue, 0)) as available_qty,
                IFNULL(book_count.total_issue, 0) as issued_qty
                FROM books
                LEFT JOIN (
                    SELECT COUNT(*) as total_issue, book_id 
                    FROM book_issues 
                    WHERE is_returned = 0 
                    GROUP BY book_id
                ) as book_count ON books.id = book_count.book_id
                WHERE 1=1 " . $condition . "
                ORDER BY books.postdate DESC";

        $query = $this->db->query($sql);
        return $query->result_array();
    }

}

