CREATE TABLE `about_us_user` ( `auu_id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(255) NOT NULL , `mobile` VARCHAR(20) NOT NULL , `email` VARCHAR(255) NOT NULL , `img` VARCHAR(255) NOT NULL , `last_updated_date` TIMESTAMP NOT NULL , `position` VARCHAR(100) NOT NULL , PRIMARY KEY (`auu_id`)) ENGINE = InnoDB;