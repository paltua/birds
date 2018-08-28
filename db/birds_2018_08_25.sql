ALTER TABLE `animal_master` ADD `am_code` VARCHAR(20) NOT NULL AFTER `user_id`, ADD UNIQUE (`am_code`);

CREATE TABLE `contact_us` ( `con_id` BIGINT NOT NULL AUTO_INCREMENT , `name` VARCHAR(255) NOT NULL , `email` VARCHAR(255) NOT NULL , `mobile` BIGINT NOT NULL , `desc` TEXT NOT NULL , `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`con_id`)) ENGINE = InnoDB;

ALTER TABLE `contact_us` CHANGE `desc` `desccription` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `comments` CHANGE `com_id` `com_id` BIGINT NOT NULL AUTO_INCREMENT;

ALTER TABLE `animal_master_images` ADD `ami_default` TINYINT NOT NULL DEFAULT '0' AFTER `ami_created_date`;

ALTER TABLE `animal_master_details` CHANGE `amd_price` `amd_price` BIGINT(11) NOT NULL;