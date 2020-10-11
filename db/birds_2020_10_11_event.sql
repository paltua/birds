CREATE TABLE `event_images` (
  `ei_id` int(11) NOT NULL AUTO_INCREMENT,
  `em_id` int(11) NOT NULL,
  `ei_image_name` varchar(100) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NULL DEFAULT NULL,
  `is_default` enum('1','0') DEFAULT '0',
  `is_completed` enum('1','0') DEFAULT '0',
  PRIMARY KEY (`ei_id`),
  KEY `index2` (`em_id`,`is_completed`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `event_location` (
  `el_id` int(11) NOT NULL AUTO_INCREMENT,
  `eml_id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  `address` text,
  `pin` int(11) DEFAULT NULL,
  PRIMARY KEY (`el_id`),
  KEY `eml_id` (`eml_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `event_master` (
  `em_id` int(11) NOT NULL AUTO_INCREMENT,
  `eml_id` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`em_id`),
  UNIQUE KEY `eml_id_UNIQUE` (`eml_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `event_master_log` (
  `eml_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `em_id` int(11) NOT NULL,
  `event_title` varchar(255) NOT NULL,
  `event_long_desc` longtext CHARACTER SET utf8,
  `event_status` enum('active','inactive','delete') DEFAULT 'active',
  `event_created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `event_created_by` int(11) NOT NULL,
  `event_start_date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `event_end_date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `event_about` longtext CHARACTER SET utf8,
  `event_objectives` longtext CHARACTER SET utf8,
  `event_title_url` varchar(255) NOT NULL,
  `event_youtube_url` varchar(255) DEFAULT NULL,
  `event_short_desc` text CHARACTER SET utf8,
  PRIMARY KEY (`eml_id`),
  UNIQUE KEY `event_title_url_UNIQUE` (`event_title_url`),
  KEY `event_id` (`em_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `event_programs_rel` (
  `epr_id` int(11) NOT NULL AUTO_INCREMENT,
  `eml_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  PRIMARY KEY (`epr_id`),
  KEY `eml_id` (`eml_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
