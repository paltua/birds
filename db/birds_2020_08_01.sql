ALTER TABLE `blog_comments` 
ADD COLUMN `is_deleted` ENUM('0', '1') NULL DEFAULT '0' AFTER `created_date`;


ALTER TABLE `animal_category_master_details` 
CHANGE COLUMN `acmd_short_desc` `acmd_short_desc` TEXT CHARACTER SET 'utf8' NULL ,
CHANGE COLUMN `acmd_updated_date` `acmd_updated_date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
ADD COLUMN `acmd_desc` LONGTEXT NULL AFTER `acmd_short_desc`;

INSERT INTO `settings` (`name`, `name_val`, `updated_by`) VALUES ('pd_charitable_trust', 'pd charitable', '1');

INSERT INTO `settings` (`name`, `name_val`, `updated_by`) VALUES ('about_us', 'about us', '1');



