-- Create advance payment tables for AMT Fee Collection System
-- Migration: 002_create_advance_payment_tables.sql

-- --------------------------------------------------------
--
-- Table structure for table `student_advance_payments`
-- Stores advance payments made by students
--
CREATE TABLE IF NOT EXISTS `student_advance_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_student_session_id` (`student_session_id`),
  KEY `idx_payment_date` (`payment_date`),
  KEY `idx_invoice_id` (`invoice_id`),
  KEY `idx_is_active` (`is_active`),
  CONSTRAINT `fk_advance_payments_student_session` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------
--
-- Table structure for table `advance_payment_usage`
-- Tracks how advance payments are used against fee payments
--
CREATE TABLE IF NOT EXISTS `advance_payment_usage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `advance_payment_id` int(11) NOT NULL,
  `student_fees_deposite_id` int(11) DEFAULT NULL,
  `student_fees_depositeadding_id` int(11) DEFAULT NULL,
  `amount_used` decimal(10,2) NOT NULL DEFAULT 0.00,
  `usage_date` date NOT NULL,
  `fee_category` varchar(50) NOT NULL DEFAULT 'fees',
  `description` text DEFAULT NULL,
  `is_reverted` varchar(10) NOT NULL DEFAULT 'no',
  `revert_reason` text DEFAULT NULL,
  `reverted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_advance_payment_id` (`advance_payment_id`),
  KEY `idx_student_fees_deposite_id` (`student_fees_deposite_id`),
  KEY `idx_student_fees_depositeadding_id` (`student_fees_depositeadding_id`),
  KEY `idx_usage_date` (`usage_date`),
  KEY `idx_fee_category` (`fee_category`),
  KEY `idx_is_reverted` (`is_reverted`),
  CONSTRAINT `fk_advance_usage_advance_payment` FOREIGN KEY (`advance_payment_id`) REFERENCES `student_advance_payments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_advance_usage_fees_deposite` FOREIGN KEY (`student_fees_deposite_id`) REFERENCES `student_fees_deposite` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_advance_usage_fees_depositeadding` FOREIGN KEY (`student_fees_depositeadding_id`) REFERENCES `student_fees_depositeadding` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------
--
-- Add indexes for performance optimization
--
ALTER TABLE `student_advance_payments` 
  ADD INDEX `idx_balance_active` (`balance`, `is_active`),
  ADD INDEX `idx_student_balance` (`student_session_id`, `balance`);

ALTER TABLE `advance_payment_usage`
  ADD INDEX `idx_advance_amount` (`advance_payment_id`, `amount_used`),
  ADD INDEX `idx_usage_category_date` (`fee_category`, `usage_date`);

-- --------------------------------------------------------
--
-- Create view for advance payment summary
--
CREATE OR REPLACE VIEW `v_student_advance_balance` AS
SELECT 
    sap.student_session_id,
    SUM(sap.amount) as total_advance_paid,
    SUM(sap.balance) as current_balance,
    COUNT(sap.id) as total_advance_payments,
    MAX(sap.payment_date) as last_advance_date
FROM student_advance_payments sap 
WHERE sap.is_active = 'yes' 
GROUP BY sap.student_session_id;
