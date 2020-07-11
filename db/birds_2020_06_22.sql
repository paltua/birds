UPDATE `animal_category_master` SET `acm_url_name`='All' WHERE `acm_id`='1';
UPDATE `animal_category_master` SET `acm_url_name`='Bird' WHERE `acm_id`='2';

UPDATE `animal_category_master_details` SET `acmd_name`='All' WHERE `acmd_id`='1';
UPDATE `animal_category_master_details` SET `acmd_name`='All' WHERE `acmd_id`='2';
UPDATE `animal_category_master_details` SET `acmd_name`='All' WHERE `acmd_id`='3';
UPDATE `animal_category_master_details` SET `acmd_name`='Birds' WHERE `acmd_id`='4';
UPDATE `animal_category_master_details` SET `acmd_name`='Birds' WHERE `acmd_id`='5';
UPDATE `animal_category_master_details` SET `acmd_name`='Birds' WHERE `acmd_id`='6';

UPDATE animal_category_master SET parent_id = 2 WHERE parent_id=1;

CREATE TABLE `blogs` (
  `blog_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `blog_revision_id` bigint(20) NOT NULL,
  PRIMARY KEY (`blog_id`),
  UNIQUE KEY `revision_id_UNIQUE` (`blog_revision_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `blog_revisions` (
  `blog_revision_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `blog_id` bigint(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `title_url` varchar(300) NOT NULL,
  `short_desc` text,
  `long_desc` longtext,
  `is_status` enum('active','inactive','delete') NOT NULL DEFAULT 'active',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`blog_revision_id`),
  UNIQUE KEY `title_url_UNIQUE` (`title_url`),
  KEY `blog_id_is_status` (`blog_id`,`is_status`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `blog_images` (
  `blog_image_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `blog_id` bigint(20) NOT NULL,
  `image_alt` varchar(255) DEFAULT NULL,
  `image_path` varchar(45) NOT NULL,
  `created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_status` enum('active','inactive','delete') DEFAULT 'active',
  `updated_date` timestamp NULL DEFAULT NULL,
  `orders` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 is default',
  PRIMARY KEY (`blog_image_id`),
  KEY `blog_id` (`blog_id`,`is_status`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `blog_animal_categorys` (
  `blog_animal_category_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `blog_revision_id` bigint(20) NOT NULL,
  `acm_id` varchar(45) NOT NULL,
  PRIMARY KEY (`blog_animal_category_id`),
  KEY `blog_revision_id` (`blog_revision_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE `blog_comments` (
  `blog_com_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `parent_blog_com_id` int(11) NOT NULL DEFAULT '0',
  `comments` longtext NOT NULL,
  `user_id` int(11) NOT NULL,
  `blog_revision_id` bigint(20) NOT NULL,
  `com_status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`blog_com_id`),
  KEY `index2` (`blog_revision_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;








