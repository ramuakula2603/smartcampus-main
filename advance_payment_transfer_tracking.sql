-- Create table to track advance payment transfers for detailed reporting
-- This table will store comprehensive information about each advance payment transfer

CREATE TABLE IF NOT EXISTS `advance_payment_transfers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `transfer_description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_student_session` (`student_session_id`),
  KEY `idx_advance_payment` (`advance_payment_id`),
  KEY `idx_fee_receipt` (`fee_receipt_id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Add indexes for better performance
ALTER TABLE `advance_payment_transfers` 
ADD INDEX `idx_fee_category` (`fee_category`),
ADD INDEX `idx_transfer_type` (`transfer_type`),
ADD INDEX `idx_student_advance` (`student_session_id`, `advance_payment_id`);

-- Insert sample comment
INSERT INTO `advance_payment_transfers` 
(`student_session_id`, `advance_payment_id`, `fee_receipt_id`, `fee_category`, `transfer_amount`, `advance_balance_before`, `advance_balance_after`, `transfer_type`, `transfer_description`, `created_by`) 
VALUES 
(0, 0, 'SAMPLE-001', 'fee', 0.00, 0.00, 0.00, 'Partial', 'Sample record for table structure - can be deleted', 1);

-- Delete the sample record
DELETE FROM `advance_payment_transfers` WHERE `fee_receipt_id` = 'SAMPLE-001';
