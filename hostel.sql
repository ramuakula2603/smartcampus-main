
INSERT INTO `permission_category` (`id`, `perm_group_id`, `name`, `short_code`, `enable_view`, `enable_add`, `enable_edit`, `enable_delete`, `created_at`) VALUES
(11046, 12, 'Hostel Fees Master', 'hostel_fees_master', 1, 1, 1, 1, '2024-01-01 00:00:00'),
(11047, 12, 'Assign Hostel Fees', 'assign_hostel_fees', 1, 1, 1, 1, '2024-01-01 00:00:00');


INSERT INTO `roles_permissions` (`id`, `role_id`, `perm_cat_id`, `can_view`, `can_add`, `can_edit`, `can_delete`, `created_at`) VALUES
(10021, 1, 11046, 1, 1, 1, 1, '2024-01-01 00:00:00'),
(10022, 1, 11047, 1, 1, 1, 1, '2024-01-01 00:00:00');


INSERT INTO `sidebar_sub_menus` (`id`, `sidebar_menu_id`, `menu`, `key`, `lang_key`, `url`, `level`, `access_permissions`, `permission_group_id`, `activate_controller`, `activate_methods`, `addon_permission`, `is_active`, `created_at`) VALUES
(502, 22, 'hostel_fees_master', NULL, 'hostel_fees_master', 'admin/hostel/feemaster', 4, '(\'hostel_fees_master\', \'can_view\')', NULL, 'hostel', 'feemaster', '', 1, '2024-01-01 00:00:00'),
(503, 22, 'assign_hostel_fees', NULL, 'assign_hostel_fees', 'admin/hostel/assignhostelfee', 5, '(\'assign_hostel_fees\', \'can_view\')', NULL, 'hostel', 'assignhostelfee,assignhostelfeestudent,assignhostelfeepost', '', 1, '2024-01-01 00:00:00');
