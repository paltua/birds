CREATE TABLE `ip_master` ( `ip_id` INT NOT NULL AUTO_INCREMENT , `ip_address` VARCHAR(20) NULL , `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` TIMESTAMP NULL , PRIMARY KEY (`ip_id`)) ENGINE = InnoDB;

CREATE TABLE `cookie_master` ( `cookie_id` INT NOT NULL AUTO_INCREMENT , `cookie_no` VARCHAR(50) NULL , `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` TIMESTAMP NULL , PRIMARY KEY (`cookie_id`)) ENGINE = InnoDB;

CREATE TABLE `browsing_details` ( `bd_id` INT NOT NULL AUTO_INCREMENT , `ip_id` INT NULL , `cookie_id` INT NULL , `HTTP_USER_AGENT` VARCHAR(255) NULL , `HTTP_REFERER` VARCHAR(255) NULL , `REQUEST_URI` VARCHAR(255) NULL , `REQUEST_TIME` TIMESTAMP NULL , `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` TIMESTAMP NULL , PRIMARY KEY (`bd_id`)) ENGINE = InnoDB;

ALTER TABLE `cookie_master` ADD `ip_id` INT NULL AFTER `cookie_no`;

CREATE TABLE `sidbi`.`user_master` ( `user_id` INT NOT NULL AUTO_INCREMENT , `full_name` VARCHAR(255) NULL , `email` VARCHAR(255) NULL , `mobile_no` VARCHAR(15) NULL , `password` VARCHAR(255) NULL ) ENGINE = InnoDB;

/* Added by Ranajit*/
DROP TABLE IF EXISTS `energy_sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `energy_sources` (
  `es_id` int(11) NOT NULL AUTO_INCREMENT,
  `es_name` varchar(100) NOT NULL,
  `es_class` varchar(50) NOT NULL,
  `es_unit_of_measure_quantity` varchar(20) NOT NULL,
  `es_density_unit` varchar(20) NOT NULL,
  `es_density_unit_value` double NOT NULL DEFAULT '0',
  `es_gcv_energy_conversion_factor_unit` varchar(20) NOT NULL,
  `es_gcv_energy_conversion_factor_unit_value` double NOT NULL DEFAULT '0',
  `es_cost_unit` varchar(20) NOT NULL,
  `es_emission_factor_unit` varchar(50) NOT NULL,
  `es_emission_factor_unit_value` double NOT NULL DEFAULT '0',
  `es_status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `es_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `es_updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`es_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `energy_sources`
--

LOCK TABLES `energy_sources` WRITE;
/*!40000 ALTER TABLE `energy_sources` DISABLE KEYS */;
INSERT INTO `energy_sources` VALUES (1,'Coal','Solid Fuel','kg','',0,'kCal/kg',3600,'INR/kg','tCO<sub>2</sub>e/kg',0.001816,'active','2017-05-26 05:48:43','0000-00-00 00:00:00'),(2,'Coke','Solid Fuel','kg','',0,'kCal/kg',3500,'INR/kg','tCO<sub>2</sub>e/kg',0,'active','2017-05-26 05:48:52','0000-00-00 00:00:00'),(3,'Petcoke','Solid Fuel','kg','',0,'kCal/kg',8000,'INR/kg','tCO<sub>2</sub>e/kg',0.00317,'active','2017-05-26 05:48:43','0000-00-00 00:00:00'),(4,'Firewood','Solid Fuel','kg','',0,'kCal/kg',4000,'INR/kg','tCO<sub>2</sub>e/kg',0.00112,'active','2017-05-26 05:48:43','0000-00-00 00:00:00'),(5,'Rice Husk','Solid Fuel','kg','',0,'kCal/kg',4261,'INR/kg','tCO<sub>2</sub>e/kg',0.00112,'active','2017-05-26 05:48:43','0000-00-00 00:00:00'),(6,'Liquefied Petroleum Gas (LPG)','Solid Fuel','kg','',0,'kCal/kg',12500,'INR/kg','tCO<sub>2</sub>e/kg',0.002985,'active','2017-05-26 05:48:43','0000-00-00 00:00:00'),(7,'Steam (Imported)','Solid Fuel','kg','',0,'kCal/kg',0,'INR/kg','tCO<sub>2</sub>e/kg',0,'active','2017-05-26 05:48:43','0000-00-00 00:00:00'),(8,'Charcoal','Solid Fuel','kg','',0,'kCal/kg',5648,'INR/kg','tCO<sub>2</sub>e/kg',0.002625,'active','2017-05-26 05:48:43','0000-00-00 00:00:00'),(9,'Light Diesel Oil (LDO)','Liquid Fuel','l','Kg/l',0.85,'kCal/l',9202,'INR/l','tCO<sub>2</sub>e/l',0.0027395,'active','2017-05-26 05:56:57','0000-00-00 00:00:00'),(10,'High Speed Diesel (HSD)','Liquid Fuel','l','kg/l',0.8263,'kCal/l',9783,'INR/l','tCO<sub>2</sub>e/l',0.00253674,'active','2017-05-26 05:56:57','0000-00-00 00:00:00'),(11,'Furnace Oil','Liquid Fuel','l','Kg/l',0.9337,'kCal/l',9804,'INR/l','tCO<sub>2</sub>e/l',0.0028954,'active','2017-05-26 05:56:57','0000-00-00 00:00:00'),(12,'Low Sulphur Heavy Stock (LSHS)','Liquid Fuel','l','kg/l',0,'kCal/l',9804,'INR/l','tCO<sub>2</sub>e/l',0,'active','2017-05-26 05:56:57','0000-00-00 00:00:00'),(13,'Fuel Oil','Liquid Fuel','l','Kg/l',0,'kCal/l',9600,'INR/l','tCO<sub>2</sub>e/l',0,'active','2017-05-26 05:56:57','0000-00-00 00:00:00'),(14,'Kerosene','Liquid Fuel','l','kg/l',0.7782,'kCal/l',8646,'INR/l','tCO<sub>2</sub>e/l',0,'active','2017-05-26 05:56:57','0000-00-00 00:00:00'),(15,'Liquefied Natural Gas (LNG)','Gaseous Fuel','SCM','',0,'kCal/SCM',0,'INR/SCM','tCO<sub>2</sub>e/SCM',0,'active','2017-05-25 06:02:52','0000-00-00 00:00:00'),(16,'Compressed Natural Gas (CNG)','Gaseous Fuel','SCM','',0,'kCal/SCM',0,'INR/SCM','tCO<sub>2</sub>e/SCM',0,'active','2017-05-25 06:02:52','0000-00-00 00:00:00'),(17,'Processed Natural Gas (PNG)','Gaseous Fuel','SCM','',0.7,'kCal/SCM',9000,'INR/SCM','tCO<sub>2</sub>e/SCM',0.0018851,'active','2017-05-26 06:00:47','0000-00-00 00:00:00'),(18,'Natural Gas','Gaseous Fuel','SCM','',0,'kCal/SCM',8500,'INR/SCM','tCO<sub>2</sub>e/SCM',0.00175034,'active','2017-05-26 06:00:47','0000-00-00 00:00:00'),(19,'Naphtha','Gaseous Fuel','SCM','',0.69,'kCal/SCM',7314,'INR/SCM','tCO<sub>2</sub>e/SCM',0,'active','2017-05-26 06:00:47','0000-00-00 00:00:00'),(20,'Propane','Gaseous Fuel','SCM','',0,'kCal/SCM',0,'INR/SCM','tCO<sub>2</sub>e/SCM',0,'active','2017-05-25 06:02:52','0000-00-00 00:00:00'),(21,'Electricity','Electricity','kWh','',0,'kCal/kWh',860,'INR/kWh','tCO<sub>2</sub>e/kWh',0.00089,'active','2017-05-26 06:00:47','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `energy_sources` ENABLE KEYS */;
UNLOCK TABLES;

/* Added by Partha on 29-05-2017 */
DROP TABLE IF EXISTS `admin_query_content`;
CREATE TABLE `admin_query_content` (
  `aqc_id` int(11) NOT NULL AUTO_INCREMENT,
  `aum_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `query_content` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `status` enum('Read','Unread') NOT NULL,
  `read_at` datetime NOT NULL,
  PRIMARY KEY (`aqc_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `admin_role_master`;
CREATE TABLE `admin_role_master` (
  `arm_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(255) NOT NULL,
  PRIMARY KEY (`arm_id`),
  UNIQUE KEY `role_name_UNIQUE` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO `admin_role_master` (`arm_id`, `role_name`) VALUES
(1, 'super admin'),
(2, 'admin');


DROP TABLE IF EXISTS `admin_user_master`;
CREATE TABLE `admin_user_master` (
  `aum_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `phone_no` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`aum_id`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO `admin_user_master` (`aum_id`, `first_name`, `last_name`, `phone_no`, `role_id`, `email`, `password`) VALUES
(1, 'rahul', 'das', 9876543, 2, 'admin@gmail.com', '123456'),
(2, 'ramesh', 'das', 2147483647, 1, 'admin@user.com', 'admin');


DROP TABLE IF EXISTS `contact_us`;
CREATE TABLE `contact_us` (
  `contact_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile_no` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` varchar(255) NOT NULL,
  PRIMARY KEY (`contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


/*------sanchita--------*/
UPDATE `sector_master` SET `sector_name` = 'Furnace Oil fired Conduction Furnace' WHERE `sector_master`.`sector_id` = 7;

ALTER TABLE `user_master` ADD `user_category` ENUM('msme_unit','bank_unit','tech_unit') NULL AFTER `user_status`;


-- --------------------------------------------------------

--
-- Table structure for table `district`
--

CREATE TABLE IF NOT EXISTS `district` (
  `DistCode` int(11) NOT NULL,
  `StCode` int(11) DEFAULT NULL,
  `DistrictName` varchar(200) DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=651 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `district`
--

INSERT INTO `district` (`DistCode`, `StCode`, `DistrictName`) VALUES
(1, 1, 'North and Middle Andama'),
(2, 1, 'South Andama'),
(3, 1, 'Nicobar'),
(4, 2, 'Anantapur'),
(5, 2, 'Chittoor'),
(6, 2, 'East Godavari'),
(7, 2, 'Guntur'),
(8, 2, 'Krishna'),
(9, 2, 'Kurnool'),
(10, 2, 'Prakasam'),
(11, 2, 'Srikakulam'),
(12, 2, 'Sri Potti Sri Ramulu Nellore'),
(13, 2, 'Vishakhapatnam'),
(14, 2, 'Vizianagaram'),
(15, 2, 'West Godavari'),
(16, 2, 'Cudappah'),
(17, 3, 'Anjaw'),
(18, 3, 'Changlang'),
(19, 3, 'East Siang'),
(20, 3, 'East Kameng'),
(21, 3, 'Kurung Kumey'),
(22, 3, 'Lohit'),
(23, 3, 'Lower Dibang Valley'),
(24, 3, 'Lower Subansiri'),
(25, 3, 'Papum Pare'),
(26, 3, 'Tawang'),
(27, 3, 'Tirap'),
(28, 3, 'Dibang Valley'),
(29, 3, 'Upper Siang'),
(30, 3, 'Upper Subansiri'),
(31, 3, 'West Kameng'),
(32, 3, 'West Siang'),
(33, 4, 'Baksa'),
(34, 4, 'Barpeta'),
(35, 4, 'Bongaigao'),
(36, 4, 'Cachar'),
(37, 4, 'Chirang'),
(38, 4, 'Darrang'),
(39, 4, 'Dhemaji'),
(40, 4, 'Dima Hasao'),
(41, 4, 'Dhubri'),
(42, 4, 'Dibrugarh'),
(43, 4, 'Goalpara'),
(44, 4, 'Golaghat'),
(45, 4, 'Hailakandi'),
(46, 4, 'Jorhat'),
(47, 4, 'Kamrup'),
(48, 4, 'Kamrup Metropolita'),
(49, 4, 'Karbi Anglong'),
(50, 4, 'Karimganj'),
(51, 4, 'Kokrajhar'),
(52, 4, 'Lakhimpur'),
(53, 4, 'Morigao'),
(54, 4, 'Nagao'),
(55, 4, 'Nalbari'),
(56, 4, 'Sivasagar'),
(57, 4, 'Sonitpur'),
(58, 4, 'Tinsukia'),
(59, 4, 'Udalguri'),
(60, 5, 'Araria'),
(61, 5, 'Arwal'),
(62, 5, 'Aurangabad'),
(63, 5, 'Banka'),
(64, 5, 'Begusarai'),
(65, 5, 'Bhagalpur'),
(66, 5, 'Bhojpur'),
(67, 5, 'Buxar'),
(68, 5, 'Darbhanga'),
(69, 5, 'East Champara'),
(70, 5, 'Gaya'),
(71, 5, 'Gopalganj'),
(72, 5, 'Jamui'),
(73, 5, 'Jehanabad'),
(74, 5, 'Kaimur'),
(75, 5, 'Katihar'),
(76, 5, 'Khagaria'),
(77, 5, 'Kishanganj'),
(78, 5, 'Lakhisarai'),
(79, 5, 'Madhepura'),
(80, 5, 'Madhubani'),
(81, 5, 'Munger'),
(82, 5, 'Muzaffarpur'),
(83, 5, 'Nalanda'),
(84, 5, 'Nawada'),
(85, 5, 'Patna'),
(86, 5, 'Purnia'),
(87, 5, 'Rohtas'),
(88, 5, 'Saharsa'),
(89, 5, 'Samastipur'),
(90, 5, 'Sara'),
(91, 5, 'Sheikhpura'),
(92, 5, 'Sheohar'),
(93, 5, 'Sitamarhi'),
(94, 5, 'Siwa'),
(95, 5, 'Supaul'),
(96, 5, 'Vaishali'),
(97, 5, 'West Champara'),
(98, 6, 'Chandigarh'),
(99, 7, 'Bastar'),
(100, 7, 'Bijapur'),
(101, 7, 'Bilaspur'),
(102, 7, 'Dantewada'),
(103, 7, 'Dhamtari'),
(104, 7, 'Durg'),
(105, 7, 'Jashpur'),
(106, 7, 'Janjgir-Champa'),
(107, 7, 'Korba'),
(108, 7, 'Koriya'),
(109, 7, 'Kanker'),
(110, 7, 'Kabirdham (formerly Kawardha);'),
(111, 7, 'Mahasamund'),
(112, 7, 'Narayanpur'),
(113, 7, 'Raigarh'),
(114, 7, 'Rajnandgao'),
(115, 7, 'Raipur'),
(116, 7, 'Surajpur'),
(117, 8, 'Dadra and Nagar Haveli'),
(118, 9, 'Dama'),
(119, 9, 'Diu'),
(120, 10, 'Central Delhi'),
(121, 10, 'East Delhi'),
(122, 10, 'New Delhi'),
(123, 10, 'North Delhi'),
(124, 10, 'North East Delhi'),
(125, 10, 'North West Delhi'),
(126, 10, 'South Delhi'),
(127, 10, 'South West Delhi'),
(128, 10, 'West Delhi'),
(129, 11, 'North Goa'),
(130, 11, 'South Goa'),
(131, 12, 'Ahmedabad'),
(132, 12, 'Amreli'),
(133, 12, 'Anand'),
(134, 12, 'Aravalli'),
(135, 12, 'Banaskantha'),
(136, 12, 'Bharuch'),
(137, 12, 'Bhavnagar'),
(138, 12, 'Dahod'),
(139, 12, 'Dang'),
(140, 12, 'Gandhinagar'),
(141, 12, 'Jamnagar'),
(142, 12, 'Junagadh'),
(143, 12, 'Kutch'),
(144, 12, 'Kheda'),
(145, 12, 'Mehsana'),
(146, 12, 'Narmada'),
(147, 12, 'Navsari'),
(148, 12, 'Pata'),
(149, 12, 'Panchmahal'),
(150, 12, 'Porbandar'),
(151, 12, 'Rajkot'),
(152, 12, 'Sabarkantha'),
(153, 12, 'Surendranagar'),
(154, 12, 'Surat'),
(155, 12, 'Tapi'),
(156, 12, 'Vadodara'),
(157, 12, 'Valsad'),
(158, 13, 'Ambala'),
(159, 13, 'Bhiwani'),
(160, 13, 'Faridabad'),
(161, 13, 'Fatehabad'),
(162, 13, 'Gurgao'),
(163, 13, 'Hissar'),
(164, 13, 'Jhajjar'),
(165, 13, 'Jind'),
(166, 13, 'Karnal'),
(167, 13, 'Kaithal'),
(168, 13, 'Kurukshetra'),
(169, 13, 'Mahendragarh'),
(170, 13, 'Mewat'),
(171, 13, 'Palwal'),
(172, 13, 'Panchkula'),
(173, 13, 'Panipat'),
(174, 13, 'Rewari'),
(175, 13, 'Rohtak'),
(176, 13, 'Sirsa'),
(177, 13, 'Sonipat'),
(178, 13, 'Yamuna Nagar'),
(179, 14, 'Bilaspur'),
(180, 14, 'Chamba'),
(181, 14, 'Hamirpur'),
(182, 14, 'Kangra'),
(183, 14, 'Kinnaur'),
(184, 14, 'Kullu'),
(185, 14, 'Lahaul and Spiti'),
(186, 14, 'Mandi'),
(187, 14, 'Shimla'),
(188, 14, 'Sirmaur'),
(189, 14, 'Sola'),
(190, 14, 'Una'),
(191, 15, 'Anantnag'),
(192, 15, 'Badgam'),
(193, 15, 'Bandipora'),
(194, 15, 'Baramulla'),
(195, 15, 'Doda'),
(196, 15, 'Ganderbal'),
(197, 15, 'Jammu'),
(198, 15, 'Kargil'),
(199, 15, 'Kathua'),
(200, 15, 'Kishtwar'),
(202, 15, 'Kupwara'),
(203, 15, 'Kulgam'),
(204, 15, 'Leh'),
(205, 15, 'Poonch'),
(206, 15, 'Pulwama'),
(207, 15, 'Rajouri'),
(208, 15, 'Ramba'),
(209, 15, 'Reasi'),
(210, 15, 'Samba'),
(211, 15, 'Shopia'),
(212, 15, 'Srinagar'),
(213, 15, 'Udhampur'),
(214, 16, 'Bokaro'),
(215, 16, 'Chatra'),
(216, 16, 'Deoghar'),
(217, 16, 'Dhanbad'),
(218, 16, 'Dumka'),
(219, 16, 'East Singhbhum'),
(220, 16, 'Garhwa'),
(221, 16, 'Giridih'),
(222, 16, 'Godda'),
(223, 16, 'Gumla'),
(224, 16, 'Hazaribag'),
(225, 16, 'Jamtara'),
(226, 16, 'Khunti'),
(227, 16, 'Koderma'),
(228, 16, 'Latehar'),
(229, 16, 'Lohardaga'),
(230, 16, 'Pakur'),
(231, 16, 'Palamu'),
(232, 16, 'Ramgarh'),
(233, 16, 'Ranchi'),
(234, 16, 'Sahibganj'),
(235, 16, 'Seraikela Kharsawa'),
(236, 16, 'Simdega'),
(237, 16, 'West Singhbhum'),
(238, 17, 'Bagalkot'),
(239, 17, 'Bangalore Rural'),
(240, 17, 'Bangalore Urba'),
(241, 17, 'Belgaum'),
(242, 17, 'Bellary'),
(243, 17, 'Bidar'),
(244, 17, 'Bijapur'),
(245, 17, 'Chamarajnagar'),
(246, 17, 'Chikkamagaluru'),
(247, 17, 'Chikkaballapur'),
(248, 17, 'Chitradurga'),
(249, 17, 'Davanagere'),
(250, 17, 'Dharwad'),
(251, 17, 'Dakshina Kannada'),
(252, 17, 'Gadag'),
(253, 17, 'Gulbarga'),
(254, 17, 'Hassa'),
(255, 17, 'Haveri'),
(256, 17, 'Kodagu'),
(257, 17, 'Kolar'),
(258, 17, 'Koppal'),
(259, 17, 'Mandya'),
(260, 17, 'Mysore'),
(261, 17, 'Raichur'),
(262, 17, 'Shimoga'),
(263, 17, 'Tumkur'),
(264, 17, 'Udupi'),
(265, 17, 'Uttara Kannada'),
(266, 17, 'Ramanagara'),
(267, 17, 'Yadgir'),
(268, 18, 'Alappuzha'),
(269, 18, 'Ernakulam'),
(270, 18, 'Idukki'),
(271, 18, 'Kannur'),
(272, 18, 'Kasaragod'),
(273, 18, 'Kollam'),
(274, 18, 'Kottayam'),
(275, 18, 'Kozhikode'),
(276, 18, 'Malappuram'),
(277, 18, 'Palakkad'),
(278, 18, 'Pathanamthitta'),
(279, 18, 'Thrissur'),
(280, 18, 'Thiruvananthapuram'),
(281, 18, 'Wayanad'),
(282, 19, 'Lakshadweep'),
(283, 20, 'Agar'),
(284, 20, 'Alirajpur'),
(285, 20, 'Anuppur'),
(286, 20, 'Ashok Nagar'),
(287, 20, 'Balaghat'),
(288, 20, 'Barwani'),
(289, 20, 'Betul'),
(290, 20, 'Bhind'),
(291, 20, 'Bhopal'),
(292, 20, 'Burhanpur'),
(293, 20, 'Chhatarpur'),
(294, 20, 'Chhindwara'),
(295, 20, 'Damoh'),
(296, 20, 'Datia'),
(297, 20, 'Dewas'),
(298, 20, 'Dhar'),
(299, 20, 'Dindori'),
(300, 20, 'Guna'),
(301, 20, 'Gwalior'),
(302, 20, 'Harda'),
(303, 20, 'Hoshangabad'),
(304, 20, 'Indore'),
(305, 20, 'Jabalpur'),
(306, 20, 'Jhabua'),
(307, 20, 'Katni'),
(308, 20, 'Khandwa (East Nimar);'),
(309, 20, 'Khargone (West Nimar);'),
(310, 20, 'Mandla'),
(311, 20, 'Mandsaur'),
(312, 20, 'Morena'),
(313, 20, 'Narsinghpur'),
(314, 20, 'Neemuch'),
(315, 20, 'Panna'),
(316, 20, 'Raise'),
(317, 20, 'Rajgarh'),
(318, 20, 'Ratlam'),
(319, 20, 'Rewa'),
(320, 20, 'Sagar'),
(321, 20, 'Satna'),
(322, 20, 'Sehore'),
(323, 20, 'Seoni'),
(324, 20, 'Shahdol'),
(325, 20, 'Shajapur'),
(326, 20, 'Sheopur'),
(327, 20, 'Shivpuri'),
(328, 20, 'Sidhi'),
(329, 20, 'Singrauli'),
(330, 20, 'Tikamgarh'),
(331, 20, 'Ujjai'),
(332, 20, 'Umaria'),
(333, 20, 'Vidisha'),
(334, 21, 'Ahmednagar'),
(335, 21, 'Akola'),
(336, 21, 'Amravati'),
(337, 21, 'Aurangabad'),
(338, 21, 'Beed'),
(339, 21, 'Bhandara'),
(340, 21, 'Buldhana'),
(341, 21, 'Chandrapur'),
(342, 21, 'Dhule'),
(343, 21, 'Gadchiroli'),
(344, 21, 'Gondia'),
(345, 21, 'Hingoli'),
(346, 21, 'Jalgao'),
(347, 21, 'Jalna'),
(348, 21, 'Kolhapur'),
(349, 21, 'Latur'),
(350, 21, 'Mumbai City'),
(351, 21, 'Mumbai suburba'),
(352, 21, 'Nanded'),
(353, 21, 'Nandurbar'),
(354, 21, 'Nagpur'),
(355, 21, 'Nashik'),
(356, 21, 'Osmanabad'),
(357, 21, 'Parbhani'),
(358, 21, 'Pune'),
(359, 21, 'Raigad'),
(360, 21, 'Ratnagiri'),
(361, 21, 'Sangli'),
(362, 21, 'Satara'),
(363, 21, 'Sindhudurg'),
(364, 21, 'Solapur'),
(365, 21, 'Thane'),
(366, 21, 'Wardha'),
(367, 21, 'Washim'),
(368, 21, 'Yavatmal'),
(369, 22, 'Bishnupur'),
(370, 22, 'Churachandpur'),
(371, 22, 'Chandel'),
(372, 22, 'Imphal East'),
(373, 22, 'Senapati'),
(374, 22, 'Tamenglong'),
(375, 22, 'Thoubal'),
(376, 22, 'Ukhrul'),
(377, 22, 'Imphal West'),
(378, 23, 'East Garo Hills'),
(379, 23, 'East Khasi Hills'),
(380, 23, 'Jaintia Hills'),
(381, 23, 'Ri Bhoi'),
(382, 23, 'South Garo Hills'),
(383, 23, 'West Garo Hills'),
(384, 23, 'West Khasi Hills'),
(385, 24, 'Aizawl'),
(386, 24, 'Champhai'),
(387, 24, 'Kolasib'),
(388, 24, 'Lawngtlai'),
(389, 24, 'Lunglei'),
(390, 24, 'Mamit'),
(391, 24, 'Saiha'),
(392, 24, 'Serchhip'),
(393, 25, 'Dimapur'),
(394, 25, 'Kiphire'),
(395, 25, 'Kohima'),
(396, 25, 'Longleng'),
(397, 25, 'Mokokchung'),
(398, 25, 'Mo'),
(399, 25, 'Pere'),
(400, 25, 'Phek'),
(401, 25, 'Tuensang'),
(402, 25, 'Wokha'),
(403, 25, 'Zunheboto'),
(404, 26, 'Angul'),
(405, 26, 'Boudh (Bauda);'),
(406, 26, 'Bhadrak'),
(407, 26, 'Balangir'),
(408, 26, 'Bargarh (Baragarh);'),
(409, 26, 'Balasore'),
(410, 26, 'Cuttack'),
(411, 26, 'Debagarh (Deogarh);'),
(412, 26, 'Dhenkanal'),
(413, 26, 'Ganjam'),
(414, 26, 'Gajapati'),
(415, 26, 'Jharsuguda'),
(416, 26, 'Jajpur'),
(417, 26, 'Jagatsinghpur'),
(418, 26, 'Khordha'),
(419, 26, 'Kendujhar (Keonjhar);'),
(420, 26, 'Kalahandi'),
(421, 26, 'Kandhamal'),
(422, 26, 'Koraput'),
(423, 26, 'Kendrapara'),
(424, 26, 'Malkangiri'),
(425, 26, 'Mayurbhanj'),
(426, 26, 'Nabarangpur'),
(427, 26, 'Nuapada'),
(428, 26, 'Nayagarh'),
(429, 26, 'Puri'),
(430, 26, 'Rayagada'),
(431, 26, 'Sambalpur'),
(432, 26, 'Subarnapur (Sonepur);'),
(433, 26, 'Sundergarh'),
(434, 27, 'Karaikal'),
(435, 27, 'Mahe'),
(436, 27, 'Pondicherry'),
(437, 27, 'Yanam'),
(438, 28, 'Amritsar'),
(439, 28, 'Barnala'),
(440, 28, 'Bathinda'),
(441, 28, 'Firozpur'),
(442, 28, 'Faridkot'),
(443, 28, 'Fatehgarh Sahib'),
(444, 28, 'Fazilka'),
(445, 28, 'Gurdaspur'),
(446, 28, 'Hoshiarpur'),
(447, 28, 'Jalandhar'),
(448, 28, 'Kapurthala'),
(449, 28, 'Ludhiana'),
(450, 28, 'Mansa'),
(451, 28, 'Moga'),
(452, 28, 'Sri Muktsar Sahib'),
(453, 28, 'Pathankot'),
(454, 28, 'Patiala'),
(455, 28, 'Rupnagar'),
(456, 28, 'Ajitgarh (Mohali);'),
(457, 28, 'Sangrur'),
(458, 28, 'Shahid Bhagat Singh Nagar'),
(459, 28, 'Tarn Tara'),
(460, 29, 'Ajmer'),
(461, 29, 'Alwar'),
(462, 29, 'Bikaner'),
(463, 29, 'Barmer'),
(464, 29, 'Banswara'),
(465, 29, 'Bharatpur'),
(466, 29, 'Bara'),
(467, 29, 'Bundi'),
(468, 29, 'Bhilwara'),
(469, 29, 'Churu'),
(470, 29, 'Chittorgarh'),
(471, 29, 'Dausa'),
(472, 29, 'Dholpur'),
(473, 29, 'Dungapur'),
(474, 29, 'Ganganagar'),
(475, 29, 'Hanumangarh'),
(476, 29, 'Jhunjhunu'),
(477, 29, 'Jalore'),
(478, 29, 'Jodhpur'),
(479, 29, 'Jaipur'),
(480, 29, 'Jaisalmer'),
(481, 29, 'Jhalawar'),
(482, 29, 'Karauli'),
(483, 29, 'Kota'),
(484, 29, 'Nagaur'),
(485, 29, 'Pali'),
(486, 29, 'Pratapgarh'),
(487, 29, 'Rajsamand'),
(488, 29, 'Sikar'),
(489, 29, 'Sawai Madhopur'),
(490, 29, 'Sirohi'),
(491, 29, 'Tonk'),
(492, 29, 'Udaipur'),
(493, 30, 'East Sikkim'),
(494, 30, 'North Sikkim'),
(495, 30, 'South Sikkim'),
(496, 30, 'West Sikkim'),
(497, 31, 'Ariyalur'),
(498, 31, 'Chennai'),
(499, 31, 'Coimbatore'),
(500, 31, 'Cuddalore'),
(501, 31, 'Dharmapuri'),
(502, 31, 'Dindigul'),
(503, 31, 'Erode'),
(504, 31, 'Kanchipuram'),
(505, 31, 'Kanyakumari'),
(506, 31, 'Karur'),
(507, 31, 'Krishnagiri'),
(508, 31, 'Madurai'),
(509, 31, 'Nagapattinam'),
(510, 31, 'Nilgiris'),
(511, 31, 'Namakkal'),
(512, 31, 'Perambalur'),
(513, 31, 'Pudukkottai'),
(514, 31, 'Ramanathapuram'),
(515, 31, 'Salem'),
(516, 31, 'Sivaganga'),
(517, 31, 'Tirupur'),
(518, 31, 'Tiruchirappalli'),
(519, 31, 'Theni'),
(520, 31, 'Tirunelveli'),
(521, 31, 'Thanjavur'),
(522, 31, 'Thoothukudi'),
(523, 31, 'Tiruvallur'),
(524, 31, 'Tiruvarur'),
(525, 31, 'Tiruvannamalai'),
(526, 31, 'Vellore'),
(527, 31, 'Viluppuram'),
(528, 31, 'Virudhunagar'),
(529, 32, 'Adilabad'),
(530, 32, 'Hyderabad'),
(531, 32, 'Karimnagar'),
(532, 32, 'Khammam'),
(533, 32, 'Mahbubnagar'),
(534, 32, 'Medak'),
(535, 32, 'Nalgonda'),
(536, 32, 'Nizamabad'),
(537, 32, 'Ranga Reddy'),
(538, 32, 'Warangal'),
(539, 33, 'Dhalai'),
(540, 33, 'North Tripura'),
(541, 33, 'South Tripura'),
(542, 33, 'Khowai'),
(543, 33, 'West Tripura'),
(544, 35, 'Agra'),
(545, 35, 'Aligarh'),
(546, 35, 'Allahabad'),
(547, 35, 'Ambedkar Nagar'),
(548, 35, 'Auraiya'),
(549, 35, 'Azamgarh'),
(550, 35, 'Bagpat'),
(551, 35, 'Bahraich'),
(552, 35, 'Ballia'),
(553, 35, 'Balrampur'),
(554, 35, 'Banda'),
(555, 35, 'Barabanki'),
(556, 35, 'Bareilly'),
(557, 35, 'Basti'),
(558, 35, 'Bijnor'),
(559, 35, 'Budau'),
(560, 35, 'Bulandshahr'),
(561, 35, 'Chandauli'),
(562, 35, 'Amethi (Chhatrapati Shahuji Maharaj Nagar)'),
(563, 35, 'Chitrakoot'),
(564, 35, 'Deoria'),
(565, 35, 'Etah'),
(566, 35, 'Etawah'),
(567, 35, 'Faizabad'),
(568, 35, 'Farrukhabad'),
(569, 35, 'Fatehpur'),
(570, 35, 'Firozabad'),
(571, 35, 'Gautam Buddh Nagar'),
(572, 35, 'Ghaziabad'),
(573, 35, 'Ghazipur'),
(574, 35, 'Gonda'),
(575, 35, 'Gorakhpur'),
(576, 35, 'Hamirpur'),
(577, 35, 'Hardoi'),
(578, 35, 'Hathras (Mahamaya Nagar);'),
(579, 35, 'Jalau'),
(580, 35, 'Jaunpur'),
(581, 35, 'Jhansi'),
(582, 35, 'Jyotiba Phule Nagar'),
(583, 35, 'Kannauj'),
(584, 35, 'Kanpur Dehat (Ramabai Nagar);'),
(585, 35, 'Kanpur Nagar'),
(586, 35, 'Kanshi Ram Nagar'),
(587, 35, 'Kaushambi'),
(588, 35, 'Kushinagar'),
(589, 35, 'Lakhimpur Kheri'),
(590, 35, 'Lalitpur'),
(591, 35, 'Lucknow'),
(592, 35, 'Maharajganj'),
(593, 35, 'Mahoba'),
(594, 35, 'Mainpuri'),
(595, 35, 'Mathura'),
(596, 35, 'Mau'),
(597, 35, 'Meerut'),
(598, 35, 'Mirzapur'),
(599, 35, 'Moradabad'),
(600, 35, 'Muzaffarnagar'),
(601, 35, 'Panchsheel Nagar (Hapur);'),
(602, 35, 'Pilibhit'),
(603, 35, 'Pratapgarh'),
(604, 35, 'Raebareli'),
(605, 35, 'Rampur'),
(606, 35, 'Saharanpur'),
(607, 35, 'Sambhal(Bheem Nagar);'),
(608, 35, 'Sant Kabir Nagar'),
(609, 35, 'Sant Ravidas Nagar'),
(610, 35, 'Shahjahanpur'),
(611, 35, 'Shamli'),
(612, 35, 'Shravasti'),
(613, 35, 'Siddharthnagar'),
(614, 35, 'Sitapur'),
(615, 35, 'Sonbhadra'),
(616, 35, 'Sultanpur'),
(617, 35, 'Unnao'),
(618, 35, 'Varanasi'),
(619, 34, 'Almora'),
(620, 34, 'Bageshwar'),
(621, 34, 'Chamoli'),
(622, 34, 'Champawat'),
(623, 34, 'Dehradu'),
(624, 34, 'Haridwar'),
(625, 34, 'Nainital'),
(626, 34, 'Pauri Garhwal'),
(627, 34, 'Pithoragarh'),
(628, 34, 'Rudraprayag'),
(629, 34, 'Tehri Garhwal'),
(630, 34, 'Udham Singh Nagar'),
(631, 34, 'Uttarkashi'),
(632, 36, 'Bankura'),
(633, 36, 'Bardhama'),
(634, 36, 'Birbhum'),
(635, 36, 'Cooch Behar'),
(636, 36, 'Dakshin Dinajpur'),
(637, 36, 'Darjeeling'),
(638, 36, 'Hooghly'),
(639, 36, 'Howrah'),
(640, 36, 'Jalpaiguri'),
(641, 36, 'Kolkata'),
(642, 36, 'Maldah'),
(643, 36, 'Murshidabad'),
(644, 36, 'Nadia'),
(645, 36, 'North 24 Parganas'),
(646, 36, 'Paschim Medinipur'),
(647, 36, 'Purba Medinipur'),
(648, 36, 'Purulia'),
(649, 36, 'South 24 Parganas'),
(650, 36, 'Uttar Dinajpur');

-- --------------------------------------------------------

--
-- Table structure for table `state`
--

CREATE TABLE IF NOT EXISTS `state` (
  `StCode` int(11) NOT NULL,
  `StateName` varchar(150) DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `state`
--

INSERT INTO `state` (`StCode`, `StateName`) VALUES
(1, 'Andaman and Nicobar Island (UT)'),
(2, 'Andhra Pradesh'),
(3, 'Arunachal Pradesh'),
(4, 'Assam'),
(5, 'Bihar'),
(6, 'Chandigarh (UT)'),
(7, 'Chhattisgarh'),
(8, 'Dadra and Nagar Haveli (UT)'),
(9, 'Daman and Diu (UT)'),
(10, 'Delhi (NCT)'),
(11, 'Goa'),
(12, 'Gujarat'),
(13, 'Haryana'),
(14, 'Himachal Pradesh'),
(15, 'Jammu and Kashmir'),
(16, 'Jharkhand'),
(17, 'Karnataka'),
(18, 'Kerala'),
(19, 'Lakshadweep (UT)'),
(20, 'Madhya Pradesh'),
(21, 'Maharashtra'),
(22, 'Manipur'),
(23, 'Meghalaya'),
(24, 'Mizoram'),
(25, 'Nagaland'),
(26, 'Odisha'),
(27, 'Puducherry (UT)'),
(28, 'Punjab'),
(29, 'Rajastha'),
(30, 'Sikkim'),
(31, 'Tamil Nadu'),
(32, 'Telangana'),
(33, 'Tripura'),
(34, 'Uttarakhand'),
(35, 'Uttar Pradesh'),
(36, 'West Bengal');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `district`
--
ALTER TABLE `district`
  ADD PRIMARY KEY (`DistCode`),
  ADD KEY `StCode` (`StCode`);

--
-- Indexes for table `state`
--
ALTER TABLE `state`
  ADD PRIMARY KEY (`StCode`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `district`
--
ALTER TABLE `district`
  MODIFY `DistCode` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=651;
--
-- AUTO_INCREMENT for table `state`
--
ALTER TABLE `state`
  MODIFY `StCode` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=37;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



/*----------06.06.2017------------*/
CREATE TABLE `stakeholder_data` (
  `shd_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT 'pkey of user_master',
  `stakeholder_name` varchar(100) DEFAULT NULL,
  `branch_name` varchar(150) NOT NULL,
  `stakeholder_address` text,
  `stakeholder_category` enum('msme_unit','bank_unit','financial_unit','tech_unit') DEFAULT NULL,
  `state` int(11) DEFAULT NULL COMMENT 'pkey of tbl state',
  `district` int(11) DEFAULT NULL COMMENT 'pkey of tbl district',
  `pin` varchar(20) DEFAULT NULL,
  `phone_no` varchar(50) DEFAULT NULL,
  `fax_no` varchar(50) DEFAULT NULL,
  `sector` int(11) DEFAULT NULL,
  `subsector` int(11) DEFAULT NULL,
  `category` int(11) DEFAULT NULL,
  `cluster` int(11) DEFAULT NULL,
  PRIMARY KEY (`shd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='stores all stakeholder data entered by user';



ALTER TABLE `user_master` 
CHANGE COLUMN `email` `email` VARCHAR(255) NOT NULL ,
ADD UNIQUE INDEX `email_UNIQUE` (`email` ASC);


ALTER TABLE `stakeholder_data` 
CHANGE COLUMN `branch_name` `branch_name` VARCHAR(150) NULL ;



/*-------------sanchita----12/06/2017----------*/
-- --------------------------------------------------------

--
-- Table structure for table `registration_form_builder_fields`
--

CREATE TABLE IF NOT EXISTS `registration_form_builder_fields` (
  `rfbf_id` int(11) NOT NULL,
  `field_label` varchar(255) DEFAULT NULL,
  `field_id` varchar(50) DEFAULT NULL,
  `field_name` varchar(100) DEFAULT NULL,
  `field_type` varchar(50) DEFAULT NULL,
  `field_value` text,
  `field_class` varchar(255) DEFAULT NULL,
  `field_placeholder` varchar(255) DEFAULT NULL,
  `field_options` text,
  `field_group` varchar(50) DEFAULT NULL,
  `field_unique_name` varchar(150) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `registration_form_builder_fields`
--

INSERT INTO `registration_form_builder_fields` (`rfbf_id`, `field_label`, `field_id`, `field_name`, `field_type`, `field_value`, `field_class`, `field_placeholder`, `field_options`, `field_group`, `field_unique_name`) VALUES
(1, 'Capacity (TPA):', NULL, 'factory[capacity_tpa]', 'input', '300', 'form-control numeric', '300', NULL, 'factory', 'factory_capacity_tpa'),
(2, 'Operating Hours per Day:', NULL, 'factory[operating_hours_per_day]', 'input', '24', 'form-control numeric', '24', NULL, 'factory', 'factory_operating_hours_per_day'),
(3, 'Operating Days per Year:', NULL, 'factory[operating_days_per_year]', 'input', '365', 'form-control numeric', '365', NULL, 'factory', 'factory_operating_days_per_year'),
(4, 'No. of Years in Operation:', NULL, 'factory[no_of_years_in_operation]', 'input', '19', 'form-control numeric', '19', NULL, 'factory', 'factory_no_of_years_in_operation'),
(5, 'Major Process Equipment:', NULL, 'process[major_process_equipment][]', 'dropdown', NULL, 'form-control pEquipement selectpicker2', NULL, NULL, 'process', 'process_major_process_equipment'),
(6, 'Capacity (TPH):', NULL, 'process[capacity_tph][]', 'input', '130', 'form-control capacityTph numeric', '130', NULL, 'process', 'process_capacity_tph'),
(7, 'Fuel/Energy Source:', NULL, 'process[fuel_id][]', 'dropdown', NULL, 'form-control eSource selectpicker2', NULL, NULL, 'process', 'process_fuel_id'),
(8, 'Product Material Type:', '', 'product[product_category_1]', 'dropdown', '', 'form-control selectpicker2', '', '', 'product', ''),
(9, 'Select Major Product Material Type:', '', 'product[select_major_1]', 'dropdown', '', 'form-control selectpicker2', '', '', 'product', NULL),
(10, 'Final Product Name:', '', 'product[product_category_2]', 'dropdown', '', 'form-control selectpicker2', '', '', 'product', NULL),
(11, 'Select Major Final Product Name:', '', 'product[select_major_2]', 'dropdown', '', 'form-control selectpicker2', '', '', 'product', NULL),
(12, 'Final Product Type:', '', 'product[product_category_3]', 'dropdown', '', 'form-control selectpicker2', '', '', 'product', NULL),
(13, 'Select Major Final Product Type:', '', 'product[select_major_3]', 'dropdown', '', 'form-control selectpicker2', '', '', 'product', NULL),
(14, 'Annual Production (Ton): ', '', 'product[annual_production_ton]', 'input', '120', 'form-control numeric', '120', '', 'product', NULL),
(15, 'Select Energy Source:', '', 'energy[energy_source][]', 'dropdown', '', 'form-control selectpicker2 selectEnergySorce', '', '', 'energy', NULL),
(16, 'Annual Consumption :', '', 'energy[annual_high_speed_diesel_consumption][]', 'input', '', 'form-control ahsdc numeric', '', '', 'energy', NULL),
(17, 'Thermal Energy Equivalent/GCV :', '', 'energy[thermal_energy_equivalant_gcv][]', 'input', '', 'form-control teegcv numeric', '', '', 'energy', NULL),
(18, 'GHG Emission Factor :', '', 'energy[ghg_emission_factor][]', 'input', '', 'form-control ghgef numeric', '', '', 'energy', NULL),
(19, 'Cost :', '', 'energy[cost][]', 'input', '', 'form-control cost numeric', '', '', 'energy', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `registration_form_builder_fields`
--
ALTER TABLE `registration_form_builder_fields`
  ADD PRIMARY KEY (`rfbf_id`), ADD UNIQUE KEY `field_unique_name` (`field_unique_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `registration_form_builder_fields`
--
ALTER TABLE `registration_form_builder_fields`
  MODIFY `rfbf_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=20;



  -- --------------------------------------------------------

--
-- Table structure for table `registration_form_builder_relation`
--

CREATE TABLE IF NOT EXISTS `registration_form_builder_relation` (
  `rfbr_id` int(11) NOT NULL,
  `rfbf_id` int(11) DEFAULT NULL,
  `sector_id` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `registration_form_builder_relation`
--

INSERT INTO `registration_form_builder_relation` (`rfbr_id`, `rfbf_id`, `sector_id`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1),
(4, 4, 1),
(5, 5, 1),
(6, 6, 1),
(7, 7, 1),
(8, 8, 1),
(9, 9, 1),
(10, 10, 1),
(11, 11, 1),
(12, 12, 1),
(13, 13, 1),
(14, 14, 1),
(15, 15, 1),
(16, 16, 1),
(17, 17, 1),
(18, 18, 1),
(19, 19, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `registration_form_builder_relation`
--
ALTER TABLE `registration_form_builder_relation`
  ADD PRIMARY KEY (`rfbr_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `registration_form_builder_relation`
--
ALTER TABLE `registration_form_builder_relation`
  MODIFY `rfbr_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=20;



