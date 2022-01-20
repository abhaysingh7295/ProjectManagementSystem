-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 08, 2021 at 08:43 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dev_pms`
--

-- --------------------------------------------------------

--
-- Table structure for table `sprintassigned_task`
--

CREATE TABLE `sprintassigned_task` (
  `sprinttask_id` int(11) NOT NULL,
  `sprinttask_sprintid` int(11) NOT NULL,
  `sprinttask_taskid` int(11) NOT NULL,
  `sprinttask_created` datetime NOT NULL DEFAULT current_timestamp(),
  `sprinttask_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sprintassigned_user`
--

CREATE TABLE `sprintassigned_user` (
  `sprintuser_id` int(11) NOT NULL,
  `sprintuser_sprintid` int(11) NOT NULL,
  `sprintuser_userid` int(11) NOT NULL,
  `sprintuser_created` datetime NOT NULL DEFAULT current_timestamp(),
  `sprintuser_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sprintassigned_task`
--
ALTER TABLE `sprintassigned_task`
  ADD PRIMARY KEY (`sprinttask_id`);

--
-- Indexes for table `sprintassigned_user`
--
ALTER TABLE `sprintassigned_user`
  ADD PRIMARY KEY (`sprintuser_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sprintassigned_task`
--
ALTER TABLE `sprintassigned_task`
  MODIFY `sprinttask_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sprintassigned_user`
--
ALTER TABLE `sprintassigned_user`
  MODIFY `sprintuser_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
