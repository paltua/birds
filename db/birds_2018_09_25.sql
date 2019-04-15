CREATE TABLE `about_us_user` ( `auu_id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(255) NOT NULL , `mobile` VARCHAR(20) NOT NULL , `email` VARCHAR(255) NOT NULL , `img` VARCHAR(255) NOT NULL , `last_updated_date` TIMESTAMP NOT NULL , `position` VARCHAR(100) NOT NULL , PRIMARY KEY (`auu_id`)) ENGINE = InnoDB;

INSERT INTO `about_us_user` (`auu_id`, `name`, `mobile`, `email`, `img`, `last_updated_date`, `position`) VALUES
(1, 'A', '98', 'a@gmail.com', '', '2018-09-23 14:19:15', 'owner'),
(2, 'BB', '9830', 'b@gmail.com', '', '2018-09-26 16:35:00', 'pos 1'),
(3, 'C', '98', 'c@gmail.com', '', '2018-09-23 14:19:58', 'pos 2'),
(4, 'D', '98', 'd@gmail.com', '', '2018-09-26 16:35:06', 'pos 2'),
(5, 'E', '98', 'e@gmail.com', '', '2018-09-23 14:27:44', 'pos 2'),
(6, 'FFF', '98', 'f@gmail.com', '', '2018-09-26 16:35:12', 'pos 3');
COMMIT;

ALTER TABLE `animal_master` ADD `am_dip_choice` ENUM('yes','no') NOT NULL DEFAULT 'no' AFTER `am_is_booked`;

CREATE TABLE `settings` ( `id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(255) NOT NULL , `name_val` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , `last_updated_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated_by` INT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
INSERT INTO `settings` (`id`, `name`, `name_val`, `last_updated_date`, `updated_by`) VALUES (NULL, 'you_tube_link', '', CURRENT_TIMESTAMP, '0');
INSERT INTO `settings` (`id`, `name`, `name_val`, `last_updated_date`, `updated_by`) VALUES (NULL, 'about_bird_en', '', CURRENT_TIMESTAMP, '0'),(NULL, 'about_bird_ben', '', CURRENT_TIMESTAMP, '0'),(NULL, 'about_bird_hi', '', CURRENT_TIMESTAMP, '0');
INSERT INTO `settings` (`id`, `name`, `name_val`, `last_updated_date`, `updated_by`) VALUES (NULL, 'short_about_bird_en', '', CURRENT_TIMESTAMP, '0'),(NULL, 'short_about_bird_ben', '', CURRENT_TIMESTAMP, '0'),(NULL, 'short_about_bird_hi', '', CURRENT_TIMESTAMP, '0');

ALTER TABLE `animal_master` ADD `am_pet_choice` ENUM('yes','no') NOT NULL DEFAULT 'no' AFTER `am_dip_choice`, ADD `am_food_choice` ENUM('yes','no') NOT NULL DEFAULT 'no' AFTER `am_pet_choice`;

--
-- Table structure for table `contact_us_reply`
--

CREATE TABLE `contact_us_reply` (
  `cur_id` bigint(20) NOT NULL,
  `con_id` int(11) NOT NULL,
  `comment` longtext NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_us_reply`
--
ALTER TABLE `contact_us_reply`
  ADD PRIMARY KEY (`cur_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_us_reply`
--
ALTER TABLE `contact_us_reply`
  MODIFY `cur_id` bigint(20) NOT NULL AUTO_INCREMENT;
COMMIT;
