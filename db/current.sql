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
