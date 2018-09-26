CREATE TABLE `gallery` ( `g_id` INT NOT NULL AUTO_INCREMENT , `g_path` VARCHAR(255) NOT NULL , `g_order` INT NOT NULL , `created_by` INT NOT NULL , `am_id` INT NOT NULL DEFAULT '0' , `created_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`g_id`)) ENGINE = InnoDB;

ALTER TABLE `gallery` ADD `g_alt` VARCHAR(255) NOT NULL AFTER `g_path`;

ALTER TABLE `gallery` CHANGE `g_alt` `g_alt` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

INSERT INTO `gallery` (`g_id`, `g_path`, `g_alt`, `g_order`, `created_by`, `am_id`, `created_date`) VALUES
(5, '20180921200906.jpg', NULL, 0, 1, 0, '2018-09-21 18:09:06'),
(6, '20180921200927.jpg', NULL, 0, 1, 0, '2018-09-21 18:09:27'),
(7, '20180921200941.jpg', NULL, 0, 1, 0, '2018-09-21 18:09:41'),
(8, '20180921200951.jpg', NULL, 0, 1, 0, '2018-09-21 18:09:51'),
(9, '20180921201007.jpg', NULL, 0, 1, 0, '2018-09-21 18:10:07'),
(10, '20180921201017.jpg', NULL, 0, 1, 0, '2018-09-21 18:10:17'),
(11, '20180921201030.jpg', NULL, 0, 1, 0, '2018-09-21 18:10:30'),
(12, '20180921201040.jpg', NULL, 0, 1, 0, '2018-09-21 18:10:40'),
(13, '20180921201050.jpg', NULL, 0, 1, 0, '2018-09-21 18:10:50'),
(14, '20180921201058.jpg', NULL, 0, 1, 0, '2018-09-21 18:10:58'),
(15, '20180921201108.jpg', NULL, 0, 1, 0, '2018-09-21 18:11:08'),
(16, '20180921201118.jpg', NULL, 0, 1, 0, '2018-09-21 18:11:18'),
(17, '20180921201146.jpg', NULL, 0, 1, 0, '2018-09-21 18:11:46'),
(18, '20180921201152.jpg', NULL, 0, 1, 0, '2018-09-21 18:11:52'),
(19, '20180921201200.jpg', NULL, 0, 1, 0, '2018-09-21 18:12:00'),
(20, '20180921201211.jpg', NULL, 0, 1, 0, '2018-09-21 18:12:11');
COMMIT;