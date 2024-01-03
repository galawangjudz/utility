-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 03, 2024 at 06:21 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbldepartments`
--

INSERT INTO `tbldepartments` (`id`, `DepartmentName`, `DepartmentShortName`, `CreationDate`) VALUES
(1, 'Information Technologies', 'ICT', '2017-11-01 07:19:37'),
(2, 'Accounting', 'ACCT', '2021-05-21 08:27:45'),
(3, 'Project Admin', 'PA', '2021-05-21 08:27:45'),
(4, 'Treasury Department', 'TSR', '2021-05-21 08:27:45'),
(5, 'Finance', 'FIN', '2023-12-19 05:10:05'),
(6, 'Cashier Supervisor', 'CSPV', '2021-05-21 08:27:45'),
(7, 'Engineering', 'ENGR', '2021-05-21 08:27:45');

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblemployees`
--

INSERT INTO `tblemployees` (`emp_id`, `FirstName`, `LastName`, `EmailId`, `Password`, `Gender`, `Department`, `Phonenumber`, `Status`, `RegDate`, `role`, `user_session_id`, `location`) VALUES
(10007, 'Ma. Theresa', 'Rabulan', 'head@gmail.com', '9cdf26568d166bc6793ef8da5afa0846', 'Male', 'CSPV', '', 1, '2023-10-20 02:55:01', 'Head', 'bqg7bapsb98o20n4r8l5kje5oa', 'NO-IMAGE-AVAILABLE.jpg'),
(10017, 'Arlene', 'San Pedro', 'cashier_supervisor2@gmail.com', '24064e6576a74af1b8eda89277c6b659', 'Female', 'CSPV', '123', 1, '2023-10-20 02:55:01', 'Head', 'b3icvpbnehs0g27mojjplg6dup', 'NO-IMAGE-AVAILABLE.jpg'),
(10093, 'Jude', 'Dela Cruz', 'admin@gmail.com', 'ba466eea2bf1a6bbfdb7dba96b713e7b', 'Male', 'ICT', '09561305511', 1, '2017-11-10 13:40:02', 'Admin', 'jmavr321g5qn9k14o5q70tpbog', 'NO-IMAGE-AVAILABLE.jpg'),
(10135, 'Christine Joy', 'Tolentino', 'staff3@gmail.com', '36bedb6eb7152f39b16328448942822b', 'Female', 'PA', '', 1, '2017-11-10 11:29:59', 'Staff', 'dbf5i3j8itk3dim8omf0qhsed2', 'NO-IMAGE-AVAILABLE.jpg'),
(10147, 'Donita Rose', 'Tantoco', 'donitarose@gmail.com', 'ba466eea2bf1a6bbfdb7dba96b713e7b', 'Female', 'ICT', '1234567', 1, '2017-11-10 13:40:02', 'Admin', 'jmavr321g5qn9k14o5q70tpbog', 'NO-IMAGE-AVAILABLE.jpg'),
(10157, 'Edhen', 'Sese', 'edhensese@gmail.com', 'ba466eea2bf1a6bbfdb7dba96b713e7b', 'Female', 'ICT', '1234567', 1, '2017-11-10 13:40:02', 'Admin', 'jmavr321g5qn9k14o5q70tpbog', 'NO-IMAGE-AVAILABLE.jpg'),
(10167, 'Jaycell', 'Crisostomo', 'counter8@gmail.com', 'd3eb9a9233e52948740d7eb8c3062d14', 'Female', 'FIN', '', 1, '2017-11-10 11:29:59', 'Staff', '242g332ul795dhjou29896554h', 'NO-IMAGE-AVAILABLE.jpg'),
(10182, 'Jena Katrina', 'Adriano', 'housepermitsupervisor@gmail.com', 'e847d18006f6d945e8a9ee2f4d3e23f5', 'Female', 'ENGR', '', 1, '2017-11-10 11:29:59', 'Staff', 'escd53q18vhcq2ke5jns0r99lg', 'NO-IMAGE-AVAILABLE.jpg'),
(10184, 'Joycelyn', 'Aguinaldo', 'head2@gmail.com', '4e86eaf2685a67b743a475f86c7c0086', 'Female', 'ACCT', '1313', 1, '2023-10-20 02:55:01', 'Head', '2g6oo8oibj0vu8isk394nv68ii', 'NO-IMAGE-AVAILABLE.jpg'),
(20008, 'Elena', 'Millo', 'cashier@gmail.com', 'ac3f1cb73bc8810830788e8c68a03a4a', 'Female', 'TSR', '', 1, '2017-11-10 13:40:02', 'Cashier', 'g38tjlsufabjqjfc27qhhrv3sk', 'NO-IMAGE-AVAILABLE.jpg'),
(20074, 'Eliza', 'Figueroa', 'cashier2@gmail.com', '00053f5e11d1fe4e49a221165b39abc9', 'Female', 'TSR', '', 1, '2023-11-24 02:32:02', 'Cashier', 'v2ak7r9gjfr3v1ohb30c6gmip9', 'NO-IMAGE-AVAILABLE.jpg'),
(20098, 'Rizza Lyn', 'Dellota', 'counter2@gmail.com', 'f0d7cd6a8d00b6d0a18533c5975731fd', 'Female', 'FIN', '', 1, '2017-11-10 11:29:59', 'Staff', '9p1na14f2l4f5g5jobp9o56ajg', 'NO-IMAGE-AVAILABLE.jpg'),
(20131, 'Ma. Lourdes', 'Posillo', 'staff2@gmail.com', '565d9a3631f5940b9facd0f153b5f569', 'Female', 'PA', '', 1, '2023-11-24 02:36:59', 'Cashier', 'jk8bofi6ob30re7l0bhrl33nr4', 'NO-IMAGE-AVAILABLE.jpg'),
(20160, 'Rose anne', 'Capule', 'staff@gmail.com', '293131be3fe9d60523ebbc6dd0c2e5c3', 'Female', 'PA', '', 1, '2017-11-10 11:29:59', 'Cashier', '4jbnhbndp67nc11pb6ihati0uq', 'NO-IMAGE-AVAILABLE.jpg'),
(20182, 'Teresita', 'Cruz', 'counter4@gmail.com', 'fb56db5736e9e08a951803b8f72e9e6c', 'Female', 'FIN', '', 1, '2017-11-10 11:29:59', 'Staff', '242g332ul795dhjou29896554h', 'NO-IMAGE-AVAILABLE.jpg'),
(20200, 'Flare', 'MENDIOLA', 'counter3@gmail.com', '6ce70275d3db0a080384a366c7dcfe3c', 'Female', 'FIN', '', 1, '2017-11-10 11:29:59', 'Staff', 'heikil95ko2bvthro91m4k24uj', 'NO-IMAGE-AVAILABLE.jpg'),
(20201, 'Angelou', 'Talucod', 'counter5@gmail.com', 'd683533d66f266d524cbf68d5df0ee9c', 'Female', 'FIN', '', 1, '2017-11-10 11:29:59', 'Staff', '242g332ul795dhjou29896554h', 'NO-IMAGE-AVAILABLE.jpg'),
(99999, 'Cha', 'Rodriguez', 'staff3@gmail.com', 'd3eb9a9233e52948740d7eb8c3062d14', 'Female', 'PA', '', 1, '2017-11-10 11:29:59', 'Staff', 'sdlskmfci7qcho6ph6dajrls4u', 'NO-IMAGE-AVAILABLE.jpg');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
