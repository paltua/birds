-- phpMyAdmin SQL Dump
-- version 4.8.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 05, 2018 at 06:31 PM
-- Server version: 10.1.31-MariaDB
-- PHP Version: 7.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `birds`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_user_master`
--

CREATE TABLE `admin_user_master` (
  `aum_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `unique_link_no` text NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin_user_master`
--

INSERT INTO `admin_user_master` (`aum_id`, `name`, `email`, `password`, `status`, `unique_link_no`, `created_date`, `updated_date`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$11$XrmYZbnlwNS5E.4AlzNTd.oit/1I9LLaToJwF3rVfBtA/0c5Em3sG', 'active', '', '0000-00-00 00:00:00', '2018-08-03 02:36:38');

-- --------------------------------------------------------

--
-- Table structure for table `animal_category_master`
--

CREATE TABLE `animal_category_master` (
  `acm_id` int(11) NOT NULL,
  `acm_url_name` varchar(255) NOT NULL,
  `acm_status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `acm_is_deleted` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `animal_category_master`
--

INSERT INTO `animal_category_master` (`acm_id`, `acm_url_name`, `acm_status`, `acm_is_deleted`) VALUES
(1, 'sound-aaa', 'active', '1'),
(2, 'Dog', 'active', '0'),
(3, 'Cat', 'active', '0');

-- --------------------------------------------------------

--
-- Table structure for table `animal_category_master_details`
--

CREATE TABLE `animal_category_master_details` (
  `acmd_id` int(11) NOT NULL,
  `language` varchar(20) NOT NULL,
  `acm_id` int(11) NOT NULL,
  `acmd_name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `acmd_short_desc` text CHARACTER SET utf8,
  `acmd_created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `acmd_updated_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `animal_category_master_details`
--

INSERT INTO `animal_category_master_details` (`acmd_id`, `language`, `acm_id`, `acmd_name`, `acmd_short_desc`, `acmd_created_date`, `acmd_updated_date`) VALUES
(1, 'en', 1, 'sound aaa', 'sdfs s ssf sf  s ', '2018-08-05 11:53:20', '0000-00-00 00:00:00'),
(2, 'ben', 1, 'শব্দ কথা সংবাদ', 'শব্দ কথা সংবাদ শব্দ কথা সংবাদ', '2018-08-05 11:53:20', '0000-00-00 00:00:00'),
(3, 'hi', 1, 'हिन्दी', ' हिन्दी', '2018-08-05 11:53:20', '0000-00-00 00:00:00'),
(4, 'en', 2, 'Dog', 'Dog', '2018-08-05 12:21:14', '2018-08-05 16:26:37'),
(5, 'ben', 2, 'কুকুর', 'কুকুর', '2018-08-05 12:21:14', '2018-08-05 16:26:46'),
(6, 'hi', 2, 'कुत्ता', 'कुत्ता', '2018-08-05 12:21:14', '2018-08-05 16:26:58'),
(7, 'en', 3, 'Cat', '', '2018-08-05 12:27:22', '0000-00-00 00:00:00'),
(8, 'ben', 3, 'বিড়াল', '', '2018-08-05 12:27:22', '0000-00-00 00:00:00'),
(9, 'hi', 3, 'बिल्ली', '', '2018-08-05 12:27:22', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

CREATE TABLE `language` (
  `lang_id` int(11) NOT NULL,
  `lang_name` varchar(255) NOT NULL,
  `language` varchar(20) NOT NULL,
  `lang_url_name` varchar(20) NOT NULL,
  `lang_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `language`
--

INSERT INTO `language` (`lang_id`, `lang_name`, `language`, `lang_url_name`, `lang_path`) VALUES
(1, 'English', 'en', 'en', ''),
(2, 'Bengali', 'ben', 'ben', ''),
(3, 'Hindi', 'hi', 'hi', '');

-- --------------------------------------------------------

--
-- Table structure for table `session_master`
--

CREATE TABLE `session_master` (
  `id` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `data` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_user_master`
--
ALTER TABLE `admin_user_master`
  ADD PRIMARY KEY (`aum_id`);

--
-- Indexes for table `animal_category_master`
--
ALTER TABLE `animal_category_master`
  ADD PRIMARY KEY (`acm_id`),
  ADD UNIQUE KEY `url_name` (`acm_url_name`);

--
-- Indexes for table `animal_category_master_details`
--
ALTER TABLE `animal_category_master_details`
  ADD PRIMARY KEY (`acmd_id`);

--
-- Indexes for table `language`
--
ALTER TABLE `language`
  ADD PRIMARY KEY (`lang_id`),
  ADD UNIQUE KEY `language` (`language`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_user_master`
--
ALTER TABLE `admin_user_master`
  MODIFY `aum_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `animal_category_master`
--
ALTER TABLE `animal_category_master`
  MODIFY `acm_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `animal_category_master_details`
--
ALTER TABLE `animal_category_master_details`
  MODIFY `acmd_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `language`
--
ALTER TABLE `language`
  MODIFY `lang_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
