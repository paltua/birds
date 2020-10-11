CREATE TABLE `programs` (
  `program_id` int(11) NOT NULL AUTO_INCREMENT,
  `program_title` varchar(255) NOT NULL,
  `program_desc` longtext CHARACTER SET utf8 NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `program_status` enum('ongoing','upcoming','completed') DEFAULT 'ongoing',
  `org_by_custom_name` varchar(255) DEFAULT NULL,
  `program_about` longtext CHARACTER SET utf8,
  `program_objectives` longtext CHARACTER SET utf8,
  `pro_title_url` varchar(255) DEFAULT NULL,
  `program_short_desc` text CHARACTER SET utf8,
  `is_status` enum('active','inactive','delete') DEFAULT 'active',
  `last_update_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `programs_images` (
  `prog_img_id` int(11) NOT NULL AUTO_INCREMENT,
  `program_id` int(11) NOT NULL,
  `prog_img_name` varchar(100) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT NULL,
  `is_default` enum('1','0') DEFAULT '0',
  `is_completed` enum('1','0') DEFAULT '0',
  PRIMARY KEY (`prog_img_id`),
  KEY `index2` (`program_id`,`is_completed`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
