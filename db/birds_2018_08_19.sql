ALTER TABLE `animal_category_master` ADD `parent_id` INT NOT NULL DEFAULT '0' AFTER `acm_is_deleted`;

ALTER TABLE `user_master` ADD `random_unique_id` VARCHAR(255) NOT NULL AFTER `um_created_date`;

ALTER TABLE `user_master` CHANGE `um_created_date` `um_created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE `user_master` ADD `email_validate_date` TIMESTAMP NOT NULL AFTER `random_unique_id`;

CREATE TABLE `comments` ( `com_id` INT NOT NULL AUTO_INCREMENT , `com_parent_id` INT NOT NULL , `comments` LONGTEXT NOT NULL , `user_id` INT NOT NULL , `am_id` INT NOT NULL , `com_status` ENUM('active','inactive') NOT NULL DEFAULT 'active' , `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`com_id`)) ENGINE = InnoDB;

ALTER TABLE `comments` CHANGE `com_parent_id` `com_parent_id` INT(11) NOT NULL DEFAULT '0';

ALTER TABLE `animal_category_master` ADD `image_name` VARCHAR(255) NOT NULL AFTER `parent_id`;