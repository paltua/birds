CREATE TABLE `about_us_user` ( `auu_id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(255) NOT NULL , `mobile` VARCHAR(20) NOT NULL , `email` VARCHAR(255) NOT NULL , `img` VARCHAR(255) NOT NULL , `last_updated_date` TIMESTAMP NOT NULL , `position` VARCHAR(100) NOT NULL , PRIMARY KEY (`auu_id`)) ENGINE = InnoDB;

INSERT INTO `about_us_user` (`auu_id`, `name`, `mobile`, `email`, `img`, `last_updated_date`, `position`) VALUES
(1, 'A', '98', 'a@gmail.com', '', '2018-09-23 14:19:15', 'owner'),
(2, 'BB', '9830', 'b@gmail.com', '', '2018-09-26 16:35:00', 'pos 1'),
(3, 'C', '98', 'c@gmail.com', '', '2018-09-23 14:19:58', 'pos 2'),
(4, 'D', '98', 'd@gmail.com', '', '2018-09-26 16:35:06', 'pos 2'),
(5, 'E', '98', 'e@gmail.com', '', '2018-09-23 14:27:44', 'pos 2'),
(6, 'FFF', '98', 'f@gmail.com', '', '2018-09-26 16:35:12', 'pos 3');
COMMIT;
