-- phpMyAdmin SQL Dump
-- version 4.8.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 10, 2018 at 08:18 PM
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
-- Table structure for table `animal_category_relation`
--

CREATE TABLE `animal_category_relation` (
  `acr_id` int(11) NOT NULL,
  `am_id` int(11) NOT NULL,
  `acm_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `animal_master`
--

CREATE TABLE `animal_master` (
  `am_id` int(11) NOT NULL,
  `am_title` varchar(255) NOT NULL,
  `am_viewd_count` int(11) NOT NULL,
  `am_status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `am_deleted` enum('0','1') NOT NULL DEFAULT '0',
  `am_created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `animal_master_details`
--

CREATE TABLE `animal_master_details` (
  `amd_id` int(11) NOT NULL,
  `am_id` int(11) NOT NULL,
  `language` int(11) NOT NULL,
  `amd_price` int(11) NOT NULL,
  `amd_short_desc` int(11) NOT NULL,
  `amd_name` int(11) NOT NULL,
  `amd_desc` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `animal_master_images`
--

CREATE TABLE `animal_master_images` (
  `ami_id` int(11) NOT NULL,
  `am_id` int(11) NOT NULL,
  `ami_title` varchar(255) NOT NULL,
  `ami_path` varchar(255) NOT NULL,
  `ami_slider` tinyint(2) NOT NULL DEFAULT '0',
  `ami_created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `language`
--





ALTER TABLE `animal_category_relation`
  ADD PRIMARY KEY (`acr_id`);

--
-- Indexes for table `animal_master`
--
ALTER TABLE `animal_master`
  ADD PRIMARY KEY (`am_id`);

--
-- Indexes for table `animal_master_details`
--
ALTER TABLE `animal_master_details`
  ADD PRIMARY KEY (`amd_id`);

--
-- Indexes for table `animal_master_images`
--
ALTER TABLE `animal_master_images`
  ADD PRIMARY KEY (`ami_id`);

--
-- AUTO_INCREMENT for table `animal_category_relation`
--
ALTER TABLE `animal_category_relation`
  MODIFY `acr_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `animal_master`
--
ALTER TABLE `animal_master`
  MODIFY `am_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `animal_master_details`
--
ALTER TABLE `animal_master_details`
  MODIFY `amd_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `animal_master_images`
--
ALTER TABLE `animal_master_images`
  MODIFY `ami_id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `animal_master_details` CHANGE `language` `language` VARCHAR(20) NOT NULL;
ALTER TABLE `animal_master_details` CHANGE `amd_short_desc` `amd_short_desc` TEXT NOT NULL, CHANGE `amd_name` `amd_name` VARCHAR(255) NOT NULL, CHANGE `amd_desc` `amd_desc` LONGTEXT NOT NULL;
ALTER TABLE `animal_master` CHANGE `am_viewd_count` `am_viewed_count` INT(11) NOT NULL;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
