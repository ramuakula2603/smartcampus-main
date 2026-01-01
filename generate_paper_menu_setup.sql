-- ============================================
-- GENERATE PAPER SYSTEM - COMPLETE SETUP
-- ============================================
-- This script sets up the complete Generate Paper module including:
-- 1. Database tables (chat messages storage)
-- 2. RBAC permissions setup
-- 3. Sidebar main menu creation
-- 4. Submenu items
-- 5. Role permissions assignment
-- ============================================
-- Date: December 10, 2025
-- Database: amt
-- Usage: Run this file once to set up the entire module
-- ============================================

USE amt;

-- ============================================
-- STEP 0: CREATE DATABASE TABLES
-- ============================================

-- Table: chat_messages_gp
-- Stores chat messages for AI question paper generation
CREATE TABLE IF NOT EXISTS `chat_messages_gp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `sender` enum('user','ai') NOT NULL DEFAULT 'user',
  `message_type` varchar(50) DEFAULT 'text',
  `file_path` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_session_id` (`session_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Table: papers (optional - for future use)
CREATE TABLE IF NOT EXISTS `papers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `total_questions` int(11) NOT NULL,
  `instructions` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ============================================
-- STEP 1: ADD RBAC PERMISSION
-- ============================================
-- Note: This will NOT create duplicates if permission already exists

-- Add Generate Paper permission to permission_category
-- This allows the system to check user access rights
INSERT INTO permission_category (perm_group_id, name, short_code, enable_view, enable_add, enable_edit, enable_delete, created_at) 
VALUES (1, 'Generate Paper', 'generate_paper', 1, 1, 0, 1, NOW())
ON DUPLICATE KEY UPDATE 
    name = VALUES(name),
    perm_group_id = VALUES(perm_group_id),
    enable_view = VALUES(enable_view),
    enable_add = VALUES(enable_add),
    enable_edit = VALUES(enable_edit),
    enable_delete = VALUES(enable_delete);

-- Get the permission ID for role assignment
SET @generate_paper_perm_id = (SELECT id FROM permission_category WHERE short_code = 'generate_paper' LIMIT 1);

-- ============================================
-- STEP 2: ADD SIDEBAR MAIN MENU
-- ============================================

-- Create separate main menu for Generate Paper
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
    1,                              -- Student Information permission group
    'fa fa-file-text-o',            -- Icon for paper/document
    'Generate Paper',               -- Main menu display name
    'generate_paper',               -- Activate menu key
    'generate_paper',               -- Language key
    0,                              -- System level
    12,                             -- Level (after Face Attendance menu)
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
SET @generate_paper_menu_id = (SELECT id FROM sidebar_menus WHERE lang_key = 'generate_paper' LIMIT 1);

-- ============================================
-- STEP 3: ADD SUBMENU ITEMS
-- ============================================

-- Add "AI Question Paper Generator" submenu under Generate Paper main menu
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
    @generate_paper_menu_id,                                   -- Generate Paper main menu
    'AI Question Paper Generator',                             -- Submenu display name
    'generate_paper_ai_generator',                             -- Key
    'generate_paper_ai_generator',                             -- Language key
    'admin/generatepaper',                                     -- URL route
    1,                                                         -- Level 1 (first submenu)
    '(''generate_paper'', ''can_view'')',                      -- Access permission
    NULL,                                                      -- Permission group
    'Generatepaper',                                           -- Controller name
    'index,save_message,upload_file,generate_pdf,preview',     -- Methods
    '',                                                        -- No addon permission
    1,                                                         -- Active
    NOW()                                                      -- Created timestamp
)
ON DUPLICATE KEY UPDATE 
    menu = VALUES(menu),
    url = VALUES(url),
    access_permissions = VALUES(access_permissions),
    is_active = 1;

-- Get the submenu ID for verification
SET @submenu_id = (SELECT id FROM sidebar_sub_menus WHERE lang_key = 'generate_paper_ai_generator' LIMIT 1);

-- ============================================
-- STEP 4: ASSIGN PERMISSIONS TO ROLES
-- ============================================

-- Assign Generate Paper permission to Super Admin (role_id = 7)
INSERT INTO roles_permissions (role_id, perm_cat_id, can_view, can_add, can_edit, can_delete)
SELECT 7, @generate_paper_perm_id, 1, 1, 0, 1
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM roles_permissions 
    WHERE role_id = 7 AND perm_cat_id = @generate_paper_perm_id
);

-- Assign Generate Paper permission to Admin (role_id = 1)
INSERT INTO roles_permissions (role_id, perm_cat_id, can_view, can_add, can_edit, can_delete)
SELECT 1, @generate_paper_perm_id, 1, 1, 0, 1
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM roles_permissions 
    WHERE role_id = 1 AND perm_cat_id = @generate_paper_perm_id
);

-- ============================================
-- STEP 5: VERIFICATION QUERIES
-- ============================================

-- Verify tables were created
SELECT 'Tables Created:' AS status;
SELECT TABLE_NAME, TABLE_ROWS, ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) AS 'Size (MB)'
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'amt' 
  AND TABLE_NAME IN ('chat_messages_gp', 'papers')
ORDER BY TABLE_NAME;

-- Verify permission was created
SELECT 'Permission Created:' AS status;
SELECT id, name, short_code, perm_group_id 
FROM permission_category 
WHERE short_code = 'generate_paper';

-- Verify role permissions were assigned
SELECT 'Role Permissions Assigned:' AS status;
SELECT rp.role_id, r.name as role_name, rp.can_view, rp.can_add, rp.can_edit, rp.can_delete
FROM roles_permissions rp
JOIN roles r ON rp.role_id = r.id
WHERE rp.perm_cat_id = @generate_paper_perm_id;

-- Verify main menu was added
SELECT 'Main Menu Added:' AS status;
SELECT id, menu, lang_key, icon, level, is_active
FROM sidebar_menus
WHERE lang_key = 'generate_paper';

-- Verify submenus were added
SELECT 'Submenus Added:' AS status;
SELECT ssm.id, sm.menu as main_menu, ssm.menu as submenu_name, ssm.lang_key, ssm.url, ssm.level, ssm.is_active
FROM sidebar_sub_menus ssm
JOIN sidebar_menus sm ON sm.id = ssm.sidebar_menu_id
WHERE sm.lang_key = 'generate_paper'
ORDER BY ssm.level;

-- ============================================
-- SETUP COMPLETE MESSAGE
-- ============================================
SELECT 
'========================================
✓ Generate Paper System Setup Complete!
========================================

Sidebar Menu Structure:
📋 Generate Paper (Main Menu - NEW!)
  └─ AI Question Paper Generator (Submenu)

Access URLs:
1. Via Menu: Generate Paper → AI Question Paper Generator
2. Direct URL: http://localhost/amt/admin/generatepaper

Features:
- Separate main menu with custom icon (fa fa-file-text-o)
- AI-powered question paper generation with chat interface
- Upload documents and generate PDF question papers
- Language keys added to system_lang.php for proper menu display
- Permission-based access control

Language Files Updated (Manual Step Required):
- application/language/English/app_files/system_lang.php
  * Add: $lang[''generate_paper''] = "Generate Paper";
  * Add: $lang[''generate_paper_ai_generator''] = "AI Question Paper Generator";

Controller File:
- application/controllers/admin/Generatepaper.php
- Methods: index, save_message, upload_file, generate_pdf, preview

Model Files:
- application/models/Chatmessage_model.php

Required Directories (create manually if needed):
- uploads/chat_uploads/ (for uploaded documents)
- uploads/chat_documents/ (for processed files)

Login with Admin or Super Admin account to access.
========================================' AS 'SETUP COMPLETE';

-- ============================================
-- CLEANUP QUERIES (OPTIONAL - USE ONLY IF NEEDED TO ROLLBACK)
-- ============================================
-- Uncomment and run these queries if you need to remove the Generate Paper menu

-- Remove role permissions
-- DELETE FROM roles_permissions WHERE perm_cat_id = (SELECT id FROM permission_category WHERE short_code = 'generate_paper');

-- Remove submenus
-- DELETE FROM sidebar_sub_menus WHERE sidebar_menu_id = (SELECT id FROM sidebar_menus WHERE lang_key = 'generate_paper');

-- Remove main menu
-- DELETE FROM sidebar_menus WHERE lang_key = 'generate_paper';

-- Remove permission category
-- DELETE FROM permission_category WHERE short_code = 'generate_paper';
