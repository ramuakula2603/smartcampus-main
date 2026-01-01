CREATE TABLE `v_student_advance_balance` (
`student_session_id` int(11),
`total_advance_paid` decimal(32,2),
`current_balance` decimal(32,2),
`total_advance_payments` bigint(21),
`last_advance_date` date
);





CREATE TABLE `student_hostel_fees` (
  `id` int(11) NOT NULL,
  `hostel_feemaster_id` int(10) NOT NULL,
  `student_session_id` int(11) NOT NULL,
  `hostel_room_id` int(11) NOT NULL,
  `generated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;





CREATE TABLE `student_advance_payments` (
  `id` int(11) NOT NULL,
  `student_session_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_date` date NOT NULL,
  `payment_mode` varchar(50) NOT NULL DEFAULT 'cash',
  `description` text DEFAULT NULL,
  `collected_by` varchar(255) DEFAULT NULL,
  `received_by` int(11) DEFAULT NULL,
  `invoice_id` varchar(50) DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `is_active` varchar(10) NOT NULL DEFAULT 'yes',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;






CREATE TABLE `hostel_feemaster` (
  `id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `month` varchar(50) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `fine_amount` float(10,2) DEFAULT 0.00,
  `fine_type` varchar(50) DEFAULT NULL,
  `fine_percentage` float(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;






CREATE TABLE `advance_payment_usage` (
  `id` int(11) NOT NULL,
  `advance_payment_id` int(11) NOT NULL,
  `student_fees_deposite_id` int(11) DEFAULT NULL,
  `student_fees_depositeadding_id` int(11) DEFAULT NULL,
  `amount_used` decimal(10,2) NOT NULL DEFAULT 0.00,
  `usage_date` date NOT NULL,
  `fee_category` varchar(50) NOT NULL DEFAULT 'fees',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_reverted` varchar(10) NOT NULL DEFAULT 'no',
  `revert_reason` text DEFAULT NULL,
  `reverted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;






CREATE TABLE `advance_payment_transfers` (
  `id` int(11) NOT NULL,
  `student_session_id` int(11) NOT NULL,
  `advance_payment_id` int(11) NOT NULL,
  `fee_receipt_id` varchar(50) NOT NULL,
  `fee_category` varchar(50) DEFAULT NULL,
  `transfer_amount` decimal(10,2) NOT NULL,
  `advance_balance_before` decimal(10,2) NOT NULL,
  `advance_balance_after` decimal(10,2) NOT NULL,
  `original_advance_amount` decimal(10,2) DEFAULT NULL,
  `original_advance_date` date DEFAULT NULL,
  `transfer_type` enum('Complete','Partial') DEFAULT 'Partial',
  `account_impact` varchar(100) DEFAULT 'Zero Cash Entry - Direct Advance Utilization',
  `transfer_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;






CREATE TABLE `advance_payments` (
  `id` int(11) NOT NULL,
  `student_session_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `balance` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `payment_mode` varchar(50) NOT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `collected_by` int(11) NOT NULL,
  `received_by` int(11) NOT NULL,
  `invoice_id` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` varchar(10) DEFAULT 'yes',
  `payment_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;





-- ALTER TABLE `student_fees_deposite`
-- ADD COLUMN `student_session_id` int(11) DEFAULT NULL AFTER `id`,
-- ADD COLUMN `student_hostel_fee_id` int(11) DEFAULT NULL AFTER `student_transport_fee_id`;


ALTER TABLE `student_fees_deposite`
ADD COLUMN `student_hostel_fee_id` int(11) DEFAULT NULL AFTER `student_transport_fee_id`;


ALTER TABLE `student_fees_depositeadding`
ADD COLUMN `student_hostel_fee_id` int(11) DEFAULT NULL AFTER `student_transport_fee_id`;



INSERT INTO `sidebar_sub_menus` (`id`, `sidebar_menu_id`, `menu`, `key`, `lang_key`, `url`, `level`, `access_permissions`, `permission_group_id`, `activate_controller`, `activate_methods`, `addon_permission`, `is_active`, `created_at`) VALUES
(499, 22, 'Hostel Fees Master', 'hostel_fees_master', 'hostel_fees_master', 'admin/hostel/feemaster', 1, 'hostel_fees_master|can_view', 12, 'hostel', 'feemaster', NULL, 1, '2025-08-29 03:11:27'),
(500, 22, 'Assign Hostel Fees', 'assign_hostel_fees', 'assign_hostel_fees', 'admin/hostel/assignhostelfee', 1, 'assign_hostel_fees|can_view', 12, 'hostel', 'assignhostelfee,assignhostelfeestudent,assignhostelfeepost', NULL, 1, '2025-08-29 03:11:27'),
(501, 21, 'Transport Fees Master', 'transport_fees_master', 'transport_fees_master', 'admin/transport/feemaster', 1, 'transport_fees_master|can_view', 11, 'transport', 'feemaster', NULL, 1, '2025-08-29 03:11:27');


