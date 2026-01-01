-- SQL script to create Hostel Fee Master tables
-- Based on Transport Fee Master structure

-- --------------------------------------------------------
--
-- Table structure for table `hostel_feemaster`
-- Similar to transport_feemaster but for hostel fees
--
CREATE TABLE IF NOT EXISTS `hostel_feemaster` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) NOT NULL,
  `month` varchar(50) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `fine_amount` float(10,2) DEFAULT 0.00,
  `fine_type` varchar(50) DEFAULT NULL,
  `fine_percentage` float(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `session_id` (`session_id`),
  KEY `month` (`month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------
--
-- Table structure for table `student_hostel_fees`
-- Similar to student_transport_fees but for hostel fees
--
CREATE TABLE IF NOT EXISTS `student_hostel_fees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hostel_feemaster_id` int(10) NOT NULL,
  `student_session_id` int(11) NOT NULL,
  `hostel_room_id` int(11) NOT NULL,
  `generated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `student_session_id` (`student_session_id`),
  KEY `hostel_room_id` (`hostel_room_id`),
  KEY `hostel_feemaster_id` (`hostel_feemaster_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------
--
-- Add foreign key constraints
--
ALTER TABLE `student_hostel_fees`
  ADD CONSTRAINT `fk_student_hostel_fees_hostel_feemaster` 
  FOREIGN KEY (`hostel_feemaster_id`) REFERENCES `hostel_feemaster` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_student_hostel_fees_student_session` 
  FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_student_hostel_fees_hostel_room` 
  FOREIGN KEY (`hostel_room_id`) REFERENCES `hostel_rooms` (`id`) ON DELETE CASCADE;

-- --------------------------------------------------------
--
-- Update existing tables to support hostel fees in fee collection system
--

-- Add hostel fee support to student_fees_deposite table
ALTER TABLE `student_fees_deposite` 
ADD COLUMN `student_hostel_fee_id` int(11) DEFAULT NULL AFTER `student_transport_fee_id`,
ADD KEY `student_hostel_fee_id` (`student_hostel_fee_id`);

-- Add hostel fee support to student_fees_depositeadding table  
ALTER TABLE `student_fees_depositeadding`
ADD COLUMN `student_hostel_fee_id` int(11) DEFAULT NULL AFTER `student_transport_fee_id`,
ADD KEY `student_hostel_fee_id` (`student_hostel_fee_id`);

-- Add hostel fee support to student_fees_processing table
ALTER TABLE `student_fees_processing`
ADD COLUMN `student_hostel_fee_id` int(11) DEFAULT NULL AFTER `student_transport_fee_id`,
ADD KEY `student_hostel_fee_id` (`student_hostel_fee_id`);

-- Add hostel fee support to offline_fees_payments table
ALTER TABLE `offline_fees_payments`
ADD COLUMN `student_hostel_fee_id` int(11) DEFAULT NULL AFTER `student_transport_fee_id`,
ADD KEY `student_hostel_fee_id` (`student_hostel_fee_id`);

-- --------------------------------------------------------
--
-- Add foreign key constraints for hostel fee support
--
ALTER TABLE `student_fees_deposite`
  ADD CONSTRAINT `fk_student_fees_deposite_hostel_fee` 
  FOREIGN KEY (`student_hostel_fee_id`) REFERENCES `student_hostel_fees` (`id`) ON DELETE CASCADE;

ALTER TABLE `student_fees_depositeadding`
  ADD CONSTRAINT `fk_student_fees_depositeadding_hostel_fee` 
  FOREIGN KEY (`student_hostel_fee_id`) REFERENCES `student_hostel_fees` (`id`) ON DELETE CASCADE;

ALTER TABLE `student_fees_processing`
  ADD CONSTRAINT `fk_student_fees_processing_hostel_fee` 
  FOREIGN KEY (`student_hostel_fee_id`) REFERENCES `student_hostel_fees` (`id`) ON DELETE CASCADE;

ALTER TABLE `offline_fees_payments`
  ADD CONSTRAINT `fk_offline_fees_payments_hostel_fee` 
  FOREIGN KEY (`student_hostel_fee_id`) REFERENCES `student_hostel_fees` (`id`) ON DELETE CASCADE;

-- --------------------------------------------------------
--
-- Sample data for testing (optional)
--
-- INSERT INTO `hostel_feemaster` (`session_id`, `month`, `due_date`, `fine_type`, `fine_percentage`, `fine_amount`) VALUES
-- (1, 'April', '2024-04-10', 'percentage', 5.00, 0.00),
-- (1, 'May', '2024-05-10', 'percentage', 5.00, 0.00),
-- (1, 'June', '2024-06-10', 'percentage', 5.00, 0.00);

-- --------------------------------------------------------
--
-- Notes:
-- 1. This structure mirrors the transport fee system
-- 2. hostel_feemaster stores monthly fee configurations
-- 3. student_hostel_fees links students to hostel fees and rooms
-- 4. Integration with existing fee collection system through additional columns
-- 5. Foreign key constraints ensure data integrity
--
