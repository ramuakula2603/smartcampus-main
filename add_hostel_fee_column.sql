-- SQL script to add missing student_hostel_fee_id column to student_fees_deposite table

-- Check if the column already exists
SELECT COLUMN_NAME 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'student_fees_deposite' 
AND COLUMN_NAME = 'student_hostel_fee_id';

-- Add the missing column
ALTER TABLE `student_fees_deposite` 
ADD COLUMN `student_hostel_fee_id` int(11) DEFAULT NULL 
AFTER `student_transport_fee_id`;

-- Add index for better performance
ALTER TABLE `student_fees_deposite` 
ADD INDEX `idx_student_hostel_fee_id` (`student_hostel_fee_id`);

-- Verify the column was added
DESCRIBE student_fees_deposite;
