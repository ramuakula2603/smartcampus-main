-- ============================================
-- FACE ATTENDANCE SYSTEM - COMPLETE SETUP
-- ============================================
-- This script includes:
-- 1. Database tables creation
-- 2. RBAC permissions setup
-- 3. Role permissions assignment
-- ============================================
-- Date: December 2, 2025
-- Database: amt
-- ============================================

USE amt;

-- ============================================
-- STEP 1: CREATE DATABASE TABLES
-- ============================================

-- Table: face_attendance_students
-- Stores registered students with face recognition data
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

-- Table: face_attendance_records
-- Stores attendance records marked via face recognition
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

-- Table: face_attendance_logs
-- Logs all face recognition attempts for debugging and audit
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

-- ============================================
-- STEP 2: ADD RBAC PERMISSION
-- ============================================

-- Add Face Attendance Register permission to permission_category
-- This allows the system to check user access rights
INSERT INTO permission_category (perm_group_id, name, short_code, enable_view, enable_add, enable_edit, enable_delete, created_at) 
VALUES (5, 'Face Attendance Register', 'face_attendance_register', 1, 1, 0, 1, NOW())
ON DUPLICATE KEY UPDATE 
    name = VALUES(name),
    perm_group_id = VALUES(perm_group_id),
    enable_view = VALUES(enable_view),
    enable_add = VALUES(enable_add),
    enable_edit = VALUES(enable_edit),
    enable_delete = VALUES(enable_delete);

-- Get the permission ID for role assignment
SET @face_perm_id = (SELECT id FROM permission_category WHERE short_code = 'face_attendance_register' LIMIT 1);

-- ============================================
-- STEP 3: ADD SIDEBAR MAIN MENU AND SUBMENU
-- ============================================

-- Create separate main menu for Face Attendance
INSERT INTO sidebar_menus (
    permission_group_id,
    icon,
    menu,
    activate_menu,
    lang_key,
    system_level,
    level,
    sidebar_display,
    access_permissions,
    is_active,
    created_at
) VALUES (
    5,                              -- Student Attendance permission group
    'fa fa-user-circle',            -- Icon for face/user
    'Face Attendance',              -- Main menu display name
    'face_attendance',              -- Activate menu key
    'face_attendance',              -- Language key
    0,                              -- System level
    11,                             -- Level (after Attendance menu)
    1,                              -- Display in sidebar
    NULL,                           -- No access permissions for main menu
    1,                              -- Active
    NOW()
)
ON DUPLICATE KEY UPDATE 
    menu = VALUES(menu),
    icon = VALUES(icon),
    is_active = 1;

-- Get the main menu ID
SET @face_menu_id = (SELECT id FROM sidebar_menus WHERE lang_key = 'face_attendance' LIMIT 1);

-- Add submenu under Face Attendance main menu
INSERT INTO sidebar_sub_menus (
    sidebar_menu_id, 
    menu, 
    `key`, 
    lang_key, 
    url, 
    level, 
    access_permissions, 
    permission_group_id, 
    activate_controller, 
    activate_methods, 
    addon_permission, 
    is_active, 
    created_at
) VALUES (
    @face_menu_id,                                         -- Face Attendance main menu
    'Student Registration',                                -- Submenu display name
    'face_attendance_student_registration',                -- Key
    'face_attendance_student_registration',                -- Language key
    'admin/face_attendance_register',                      -- URL route
    1,                                                     -- Level 1 (first submenu)
    '(''face_attendance_register'', ''can_view'')',        -- Access permission
    NULL,                                                  -- Permission group
    'Face_attendance_register',                            -- Controller name
    'index,register_student,get_students,check_registration,delete_student', -- Methods
    '',                                                    -- No addon permission
    1,                                                     -- Active
    NOW()                                                  -- Created timestamp
)
ON DUPLICATE KEY UPDATE 
    menu = VALUES(menu),
    url = VALUES(url),
    access_permissions = VALUES(access_permissions),
    is_active = 1;

-- Get the submenu ID for verification
SET @submenu_id = (SELECT id FROM sidebar_sub_menus WHERE lang_key = 'face_attendance_student_registration' LIMIT 1);

-- Add "Mark Attendance" submenu under Face Attendance main menu
INSERT INTO sidebar_sub_menus (
    sidebar_menu_id, 
    menu, 
    `key`, 
    lang_key, 
    url, 
    level, 
    access_permissions, 
    permission_group_id, 
    activate_controller, 
    activate_methods, 
    addon_permission, 
    is_active, 
    created_at
) VALUES (
    @face_menu_id,                                         -- Face Attendance main menu
    'Mark Attendance',                                     -- Submenu display name
    'face_attendance_mark_attendance',                     -- Key
    'face_attendance_mark_attendance',                     -- Language key
    'admin/face_attendance_register/mark_attendance',      -- URL route
    2,                                                     -- Level 2 (second submenu)
    '(''face_attendance_register'', ''can_view'')',        -- Access permission
    NULL,                                                  -- Permission group
    'Face_attendance_register',                            -- Controller name
    'mark_attendance,get_registered_students,save_attendance,get_attendance_records', -- Methods
    '',                                                    -- No addon permission
    1,                                                     -- Active
    NOW()                                                  -- Created timestamp
)
ON DUPLICATE KEY UPDATE 
    menu = VALUES(menu),
    url = VALUES(url),
    access_permissions = VALUES(access_permissions),
    is_active = 1;

-- Get the submenu ID for verification
SET @submenu_mark_id = (SELECT id FROM sidebar_sub_menus WHERE lang_key = 'face_attendance_mark_attendance' LIMIT 1);

-- ============================================
-- STEP 4: ASSIGN PERMISSIONS TO ROLES
-- ============================================

-- Assign to Super Admin role (role_id = 7)
INSERT INTO roles_permissions (role_id, perm_cat_id, can_view, can_add, can_edit, can_delete, created_at)
VALUES (7, @face_perm_id, 1, 1, 0, 1, NOW())
ON DUPLICATE KEY UPDATE 
    can_view = 1, 
    can_add = 1, 
    can_delete = 1;

-- Assign to Admin role (role_id = 1)
INSERT INTO roles_permissions (role_id, perm_cat_id, can_view, can_add, can_edit, can_delete, created_at)
VALUES (1, @face_perm_id, 1, 1, 0, 1, NOW())
ON DUPLICATE KEY UPDATE 
    can_view = 1, 
    can_add = 1, 
    can_delete = 1;

-- Optional: Assign to Teacher role (role_id = 2) - Uncomment if needed
-- INSERT INTO roles_permissions (role_id, perm_cat_id, can_view, can_add, can_edit, can_delete, created_at)
-- VALUES (2, @face_perm_id, 1, 1, 0, 0, NOW())
-- ON DUPLICATE KEY UPDATE can_view = 1, can_add = 1;

-- ============================================
-- STEP 5: VERIFICATION QUERIES
-- ============================================

-- Verify tables were created
SELECT 'Tables Created:' as status;
SELECT 
    table_name,
    table_rows,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) as 'Size (MB)'
FROM information_schema.tables
WHERE table_schema = 'amt' 
  AND table_name LIKE 'face_attendance_%'
ORDER BY table_name;

-- Verify permission was added
SELECT 'Permission Created:' as status;
SELECT id, name, short_code, perm_group_id 
FROM permission_category 
WHERE short_code = 'face_attendance_register';

-- Verify role permissions were assigned
SELECT 'Role Permissions Assigned:' as status;
SELECT 
    r.id as role_id,
    r.name as role_name, 
    rp.can_view, 
    rp.can_add, 
    rp.can_edit,
    rp.can_delete 
FROM roles_permissions rp 
JOIN roles r ON rp.role_id = r.id 
WHERE rp.perm_cat_id = @face_perm_id;

-- Verify sidebar main menu was added
SELECT 'Main Menu Added:' as status;
SELECT 
    id,
    menu,
    lang_key,
    icon,
    level,
    is_active
FROM sidebar_menus
WHERE lang_key = 'face_attendance';

-- Verify sidebar submenus were added
SELECT 'Submenus Added:' as status;
SELECT 
    ssm.id,
    sm.menu as main_menu,
    ssm.menu as submenu_name,
    ssm.lang_key,
    ssm.url,
    ssm.level,
    ssm.is_active
FROM sidebar_sub_menus ssm
JOIN sidebar_menus sm ON sm.id = ssm.sidebar_menu_id
WHERE ssm.lang_key IN ('face_attendance_student_registration', 'face_attendance_mark_attendance')
ORDER BY ssm.level;

-- ============================================
-- SETUP COMPLETE!
-- ============================================

SELECT '
========================================
✓ Face Attendance System Setup Complete!
========================================

Sidebar Menu Structure:
📋 Face Attendance (Main Menu - NEW!)
  └─ Student Registration (Submenu)

Access URLs:
1. Via Menu: Face Attendance → Student Registration
2. Direct URL: http://localhost/amt/admin/face_attendance_register

Features:
- Separate main menu with custom icon (fa fa-user-circle)
- Student Registration submenu for registering students with face data
- Language keys added to system_lang.php for proper menu display
- Permission-based access control

Language Files Updated:
- application/language/English/app_files/system_lang.php
  * face_attendance = "Face Attendance"
  * face_attendance_student_registration = "Student Registration"

Required Directories (create manually if needed):
- uploads/face_attendance_images/
- uploads/face_attendance_captures/

Model Files Required:
- assets/face_attendance_models/ (18 Face-API.js files)

Login with Admin or Super Admin account to access.
========================================
' as 'SETUP COMPLETE';

-- ============================================
-- OPTIONAL: ADD MORE ROLES
-- ============================================

-- To add permission to additional roles, use this template:
/*
-- For Accountant (role_id = 3)
INSERT INTO roles_permissions (role_id, perm_cat_id, can_view, can_add, can_edit, can_delete, created_at)
VALUES (3, @face_perm_id, 1, 0, 0, 0, NOW())
ON DUPLICATE KEY UPDATE can_view = 1;

-- For Teacher (role_id = 2)
INSERT INTO roles_permissions (role_id, perm_cat_id, can_view, can_add, can_edit, can_delete, created_at)
VALUES (2, @face_perm_id, 1, 1, 0, 0, NOW())
ON DUPLICATE KEY UPDATE can_view = 1, can_add = 1;

-- For Librarian (role_id = 4)
INSERT INTO roles_permissions (role_id, perm_cat_id, can_view, can_add, can_edit, can_delete, created_at)
VALUES (4, @face_perm_id, 1, 0, 0, 0, NOW())
ON DUPLICATE KEY UPDATE can_view = 1;
*/

-- ============================================
-- TROUBLESHOOTING QUERIES
-- ============================================

-- Check if permission exists
-- SELECT * FROM permission_category WHERE short_code = 'face_attendance_register';

-- Check which roles have access
-- SELECT r.name, rp.* FROM roles_permissions rp JOIN roles r ON rp.role_id = r.id WHERE rp.perm_cat_id = (SELECT id FROM permission_category WHERE short_code = 'face_attendance_register');

-- Remove permission (if needed to reinstall)
-- DELETE FROM roles_permissions WHERE perm_cat_id = (SELECT id FROM permission_category WHERE short_code = 'face_attendance_register');
-- DELETE FROM permission_category WHERE short_code = 'face_attendance_register';

-- End of script
