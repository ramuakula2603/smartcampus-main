-- Create table for default class fee assignments
CREATE TABLE IF NOT EXISTS `default_class_fees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class_id` int(11) NOT NULL,
  `fee_group_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_class_fee_group` (`class_id`, `fee_group_id`),
  KEY `idx_class_id` (`class_id`),
  KEY `idx_fee_group_id` (`fee_group_id`),
  CONSTRAINT `fk_default_class_fees_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_default_class_fees_fee_group` FOREIGN KEY (`fee_group_id`) REFERENCES `fee_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




