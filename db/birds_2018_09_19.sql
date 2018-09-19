CREATE TABLE `animal_master_viewed` (
  `amv_id` bigint(20) NOT NULL,
  `am_id` int(11) NOT NULL COMMENT 'refer from animal_master table',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(20) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `animal_master_viewed`
--
ALTER TABLE `animal_master_viewed`
  ADD PRIMARY KEY (`amv_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `animal_master_viewed`
--
ALTER TABLE `animal_master_viewed`
  MODIFY `amv_id` bigint(20) NOT NULL AUTO_INCREMENT;
COMMIT;

ALTER TABLE `animal_master` ADD `am_is_booked` ENUM('yes','no') NOT NULL DEFAULT 'no' AFTER `buy_or_sell`;

CREATE TABLE `animal_booked_details` ( `abd_id` INT NOT NULL AUTO_INCREMENT , `am_id` INT NOT NULL , `booked_by` INT NOT NULL , `booked_to` INT NOT NULL , `booked_date` INT NOT NULL , `booked_price` DOUBLE NOT NULL , PRIMARY KEY (`abd_id`)) ENGINE = InnoDB;
ALTER TABLE `animal_booked_details` ADD `booked_by_type` ENUM('admin','user') NOT NULL DEFAULT 'admin' AFTER `booked_by`;
ALTER TABLE `animal_booked_details` CHANGE `booked_date` `booked_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

