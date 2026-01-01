-- ============================================
-- FACE ATTENDANCE SYSTEM - QUICK SETUP
-- ============================================
-- Run this script to set up the face attendance system
-- Database: amt
-- ============================================

USE amt;

-- Step 1: Create face_attendance_students table
CREATE TABLE IF NOT EXISTS `face_attendance_students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL COMMENT 'Reference to existing student table if needed',
  `registration_number` varchar(100) NOT NULL UNIQUE,
  `admission_no` varchar(50) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `face_images` text COMMENT 'JSON array of image file names',
  `face_descriptors` longtext COMMENT 'JSON array of face descriptors for recognition',
  `is_active` tinyint(1) DEFAULT 1,
  `registered_by` int(11) DEFAULT NULL,
  `registration_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `last_updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_registration_number` (`registration_number`),
  KEY `idx_student_id` (`student_id`),
  KEY `idx_class_section` (`class_id`, `section_id`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Step 2: Create face_attendance_records table
CREATE TABLE IF NOT EXISTS `face_attendance_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `face_student_id` int(11) NOT NULL COMMENT 'Reference to face_attendance_students',
  `registration_number` varchar(100) NOT NULL,
  `attendance_date` date NOT NULL,
  `attendance_time` time NOT NULL,
  `attendance_status` enum('Present','Absent','Late') NOT NULL DEFAULT 'Present',
  `confidence_score` decimal(5,2) DEFAULT NULL COMMENT 'Face recognition match confidence',
  `captured_image` varchar(255) DEFAULT NULL COMMENT 'Path to captured attendance image',
  `session_id` int(11) DEFAULT NULL COMMENT 'Link to school session',
  `class_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `marked_by` int(11) DEFAULT NULL COMMENT 'User who initiated attendance',
  `recognition_method` enum('Auto','Manual','Verified') DEFAULT 'Auto',
  `notes` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_face_student` (`face_student_id`),
  KEY `idx_registration_number` (`registration_number`),
  KEY `idx_attendance_date` (`attendance_date`),
  KEY `idx_date_student` (`attendance_date`, `face_student_id`),
  KEY `idx_status` (`attendance_status`),
  CONSTRAINT `fk_face_attendance_student` FOREIGN KEY (`face_student_id`) REFERENCES `face_attendance_students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Step 3: Create face_attendance_logs table
CREATE TABLE IF NOT EXISTS `face_attendance_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_date` date NOT NULL,
  `recognition_time` datetime NOT NULL,
  `detected_faces` int(11) DEFAULT 0,
  `recognized_faces` int(11) DEFAULT 0,
  `unknown_faces` int(11) DEFAULT 0,
  `recognition_details` text COMMENT 'JSON details of all recognitions',
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_session_date` (`session_date`),
  KEY `idx_recognition_time` (`recognition_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Verification queries
SELECT 'Tables created successfully!' as status;

SELECT 
    table_name,
    table_rows,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) as 'Size (MB)'
FROM information_schema.tables
WHERE table_schema = 'amt' 
  AND table_name LIKE 'face_attendance_%'
ORDER BY table_name;

-- Success message
SELECT 'Setup Complete! You can now access: http://localhost/amt/admin/face_attendance_register' as message;
