ALTER TABLE `gallery` 
ADD COLUMN `g_status` ENUM('active', 'inactive') NULL DEFAULT 'active' AFTER `created_date`;
