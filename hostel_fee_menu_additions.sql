-- Additional SQL statements to add hostel fee menu items
-- Run this if you've already executed the original hostel.sql file

-- Add new permission categories for hostel fees
INSERT INTO `permission_category` (`id`, `perm_group_id`, `name`, `short_code`, `enable_view`, `enable_add`, `enable_edit`, `enable_delete`, `created_at`) VALUES
(43, 12, 'Hostel Fees Master', 'hostel_fees_master', 1, 1, 1, 1, '2024-01-01 00:00:00'),
(44, 12, 'Assign Hostel Fees', 'assign_hostel_fees', 1, 1, 1, 1, '2024-01-01 00:00:00');

-- Add role permissions for Super Admin (role_id = 1)
INSERT INTO `roles_permissions` (`id`, `role_id`, `perm_cat_id`, `can_view`, `can_add`, `can_edit`, `can_delete`, `created_at`) VALUES
(162, 1, 43, 1, 1, 1, 1, '2024-01-01 00:00:00'),
(163, 1, 44, 1, 1, 1, 1, '2024-01-01 00:00:00');

-- Update the main Hostel menu to include new permissions
UPDATE `sidebar_menus` 
SET `access_permissions` = '(\'hostel_rooms\', \'can_view\') || (\'room_type\', \'can_view\') || (\'hostel\', \'can_view\') || (\'hostel_fees_master\', \'can_view\') || (\'assign_hostel_fees\', \'can_view\')'
WHERE `id` = 22;

-- Add new sidebar submenu items
INSERT INTO `sidebar_sub_menus` (`id`, `sidebar_menu_id`, `menu`, `key`, `lang_key`, `url`, `level`, `access_permissions`, `permission_group_id`, `activate_controller`, `activate_methods`, `addon_permission`, `is_active`, `created_at`) VALUES
(116, 22, 'hostel_fees_master', NULL, 'hostel_fees_master', 'admin/hostel/feemaster', 4, '(\'hostel_fees_master\', \'can_view\')', NULL, 'hostel', 'feemaster', '', 1, '2024-01-01 00:00:00'),
(117, 22, 'assign_hostel_fees', NULL, 'assign_hostel_fees', 'admin/hostel/assignhostelfee', 5, '(\'assign_hostel_fees\', \'can_view\')', NULL, 'hostel', 'assignhostelfee,assignhostelfeestudent,assignhostelfeepost', '', 1, '2024-01-01 00:00:00');
