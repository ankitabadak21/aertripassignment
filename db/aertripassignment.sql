-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 11, 2022 at 07:02 AM
-- Server version: 10.4.13-MariaDB
-- PHP Version: 7.2.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aertripassignment`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_admin`
--

INSERT INTO `tbl_admin` (`id`, `username`, `email`, `password`, `created_at`, `last_login`) VALUES
(1, 'admin', 'admin@ex.com', 'e10adc3949ba59abbe56e057f20f883e', '2022-03-10 20:01:26', '2022-03-11 06:46:49');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_department_master`
--

CREATE TABLE `tbl_department_master` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_department_master`
--

INSERT INTO `tbl_department_master` (`id`, `name`, `created_date`) VALUES
(1, 'IT', '2022-03-10 20:37:38'),
(2, 'Accounts', '2022-03-10 20:37:46'),
(3, 'Finance', '2022-03-10 20:37:54');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_employee`
--

CREATE TABLE `tbl_employee` (
  `emp_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `employee_name` varchar(100) NOT NULL,
  `isactive` tinyint(4) NOT NULL DEFAULT 1,
  `created_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_employee`
--

INSERT INTO `tbl_employee` (`emp_id`, `department_id`, `employee_name`, `isactive`, `created_date`) VALUES
(9, 1, 'John Smith', 1, '2022-03-11 06:59:04');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_employee_address`
--

CREATE TABLE `tbl_employee_address` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `address` text NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_employee_address`
--

INSERT INTO `tbl_employee_address` (`id`, `emp_id`, `address`, `created_date`) VALUES
(37, 9, 'Panvel', '2022-03-11 11:29:04'),
(38, 9, 'Kalamboli', '2022-03-11 11:29:04');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_employee_contacts`
--

CREATE TABLE `tbl_employee_contacts` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `contact_no` varchar(50) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_employee_contacts`
--

INSERT INTO `tbl_employee_contacts` (`id`, `emp_id`, `contact_no`, `created_date`) VALUES
(36, 9, '9999999999', '2022-03-11 11:29:04'),
(37, 9, '8888888888', '2022-03-11 11:29:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_department_master`
--
ALTER TABLE `tbl_department_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_employee`
--
ALTER TABLE `tbl_employee`
  ADD PRIMARY KEY (`emp_id`);

--
-- Indexes for table `tbl_employee_address`
--
ALTER TABLE `tbl_employee_address`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_employee_contacts`
--
ALTER TABLE `tbl_employee_contacts`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_department_master`
--
ALTER TABLE `tbl_department_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_employee`
--
ALTER TABLE `tbl_employee`
  MODIFY `emp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbl_employee_address`
--
ALTER TABLE `tbl_employee_address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `tbl_employee_contacts`
--
ALTER TABLE `tbl_employee_contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
