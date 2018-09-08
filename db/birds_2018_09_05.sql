

CREATE TABLE `animal_location` (
  `al_id` int(11) NOT NULL,
  `am_id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL COMMENT 'refer to column id from countries table ',
  `state_id` int(11) NOT NULL COMMENT 'refer to column id from states table ',
  `city_id` int(11) NOT NULL COMMENT 'refer to column id from cities table ',
  `area_id` int(11) NOT NULL DEFAULT '0' COMMENT 'refer to column id from areas table '
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `animal_location`
  ADD PRIMARY KEY (`al_id`);


ALTER TABLE `animal_location`
  MODIFY `al_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

ALTER TABLE `user_master` ADD `pwd_reset_unique_link_no` VARCHAR(100) NOT NULL AFTER `email_validate_date`;

