ALTER TABLE `halltickect_generation` 
ADD COLUMN `is_active` VARCHAR(10) NOT NULL DEFAULT 'yes' AFTER `examheading`;
