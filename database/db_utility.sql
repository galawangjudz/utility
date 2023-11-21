-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 21, 2023 at 01:15 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_utility`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `UserName` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `updationDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `UserName`, `Password`, `updationDate`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', '2020-11-03 05:55:30');

-- --------------------------------------------------------

--
-- Table structure for table `tbldepartments`
--

CREATE TABLE `tbldepartments` (
  `id` int(11) NOT NULL,
  `DepartmentName` varchar(150) DEFAULT NULL,
  `DepartmentShortName` varchar(100) NOT NULL,
  `CreationDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbldepartments`
--

INSERT INTO `tbldepartments` (`id`, `DepartmentName`, `DepartmentShortName`, `CreationDate`) VALUES
(1, 'Information Technologies', 'ICT', '2017-11-01 07:19:37'),
(3, 'Project Admin', 'PA', '2021-05-21 08:27:45'),
(4, 'Treasury Department', 'TSR', '2021-05-21 08:27:45'),
(6, 'Cashier Supervisor', 'CSPV', '2021-05-21 08:27:45');

-- --------------------------------------------------------

--
-- Table structure for table `tblemployees`
--

CREATE TABLE `tblemployees` (
  `emp_id` int(11) NOT NULL,
  `FirstName` varchar(150) NOT NULL,
  `LastName` varchar(150) NOT NULL,
  `EmailId` varchar(200) NOT NULL,
  `Password` varchar(180) NOT NULL,
  `Gender` varchar(100) NOT NULL,
  `Department` varchar(255) NOT NULL,
  `Phonenumber` char(11) NOT NULL,
  `Status` int(1) NOT NULL,
  `RegDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` varchar(30) NOT NULL,
  `user_session_id` varchar(100) NOT NULL,
  `location` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblemployees`
--

INSERT INTO `tblemployees` (`emp_id`, `FirstName`, `LastName`, `EmailId`, `Password`, `Gender`, `Department`, `Phonenumber`, `Status`, `RegDate`, `role`, `user_session_id`, `location`) VALUES
(10007, 'Ma. Theresa', 'Rabulan', 'head@gmail.com', '0192023a7bbd73250516f069df18b500', 'Male', 'TSR', '09561305511', 1, '2023-10-20 02:55:01', 'Head', '24mio6b8kk190p6j52dp62oni7', 'NO-IMAGE-AVAILABLE.jpg'),
(10093, 'Jude', 'Dela Cruz', 'admin@gmail.com', '0192023a7bbd73250516f069df18b500', 'Male', 'ICT', '09561305511', 1, '2017-11-10 13:40:02', 'Admin', 'ohb08b4pig2rcchkukad1hfoq0', 'avatar-1.png'),
(10184, 'Joycelyn', 'Aguinaldo', 'head2@gmail.com', '0192023a7bbd73250516f069df18b500', 'Female', 'CSPV', '09561305511', 1, '2023-10-20 02:55:01', 'Head', 'fs770d57rvivddvfnnqo45pgjt', 'NO-IMAGE-AVAILABLE.jpg'),
(20008, 'Elena', 'Millo', 'cashier@gmail.com', '0192023a7bbd73250516f069df18b500', 'Female', 'TSR', '587944255', 1, '2017-11-10 13:40:02', 'Cashier', '78suvemvp216gvfc7qntdklbqe', 'photo5.jpg'),
(20160, 'Roseann', 'Capule', 'staff@gmail.com', '0192023a7bbd73250516f069df18b500', 'Female', 'PA', '0248865955', 1, '2017-11-10 11:29:59', 'Staff', 'op3scnj154aj5534h86agstenr', 'DESKTOP 2023 Approved.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` int(30) NOT NULL,
  `subject` text NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '0=Pending,1=on process,2= Closed',
  `priority` varchar(10) NOT NULL,
  `department_id` int(30) NOT NULL,
  `customer_id` int(30) NOT NULL,
  `admin_id` int(30) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `subject`, `description`, `status`, `priority`, `department_id`, `customer_id`, `admin_id`, `date_created`) VALUES
(21, 'ATC DATE', '&lt;p&gt;pa update po atc date&lt;/p&gt;', 0, 'Highest', 1, 10093, 0, '2023-11-15 07:15:43'),
(22, 'PTO Date', '&lt;p&gt;atc date&lt;/p&gt;', 0, 'High', 1, 10093, 0, '2023-11-15 07:19:27'),
(23, 'PTO', '&lt;p&gt;fsfsfsfs&lt;/p&gt;', 0, 'High', 1, 4, 0, '2023-11-15 07:19:45'),
(24, 'racqreqce', '&lt;p&gt;qcrqeqe&lt;/p&gt;', 2, 'Highest', 1, 4, 0, '2023-11-15 07:19:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbldepartments`
--
ALTER TABLE `tbldepartments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblemployees`
--
ALTER TABLE `tblemployees`
  ADD PRIMARY KEY (`emp_id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbldepartments`
--
ALTER TABLE `tbldepartments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
