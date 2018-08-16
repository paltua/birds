ALTER TABLE `animal_category_master` ADD `parent_id` INT NOT NULL DEFAULT '0' AFTER `acm_is_deleted`;

ALTER TABLE `user_master` ADD `random_unique_id` VARCHAR(255) NOT NULL AFTER `um_created_date`;