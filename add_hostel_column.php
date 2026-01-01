<?php
// Add student_hostel_fee_id column if it doesn't exist
require_once 'index.php';
$CI =& get_instance();

$columns = $CI->db->list_fields('student_fees_deposite');
if (!in_array('student_hostel_fee_id', $columns)) {
    $CI->db->query("ALTER TABLE `student_fees_deposite` ADD COLUMN `student_hostel_fee_id` int(11) DEFAULT NULL AFTER `student_transport_fee_id`");
    echo "Column added successfully";
} else {
    echo "Column already exists";
}
?>
