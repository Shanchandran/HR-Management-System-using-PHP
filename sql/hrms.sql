-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2025 at 03:40 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hrms`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_user`
--

CREATE TABLE `admin_user` (
  `ID` int(10) NOT NULL,
  `Name` text NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Password` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_user`
--

INSERT INTO `admin_user` (`ID`, `Name`, `Email`, `Password`) VALUES
(1, 'Admin', 'admin@gmail.com', 'Admin@123');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `Employee_ID` int(10) NOT NULL,
  `First_Name` text NOT NULL,
  `Middle_Name` text NOT NULL,
  `Last_Name` text NOT NULL,
  `Mobile_Number` varchar(10) NOT NULL,
  `Designation` text NOT NULL,
  `Salary` decimal(10,2) NOT NULL,
  `Email` varchar(30) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Profile_Photo` varchar(255) NOT NULL,
  `Gender` text NOT NULL,
  `Date_of_Birth` date DEFAULT NULL,
  `Aadhaar_Number` varchar(12) DEFAULT NULL,
  `Marital_Status` text NOT NULL,
  `Address_Line_1` varchar(100) NOT NULL,
  `Address_Line_2` varchar(100) NOT NULL,
  `Address_Line_3` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`Employee_ID`, `First_Name`, `Middle_Name`, `Last_Name`, `Mobile_Number`, `Designation`, `Salary`, `Email`, `Password`, `Profile_Photo`, `Gender`, `Date_of_Birth`, `Aadhaar_Number`, `Marital_Status`, `Address_Line_1`, `Address_Line_2`, `Address_Line_3`) VALUES
(1, 'Test', '', '', '9346251798', 'HR', 80000.00, 'test@gmail.com', '$2y$10$bbtn9qmkKsaq/GVHcBXDN.wy43bLdSuh/nbyAaXSNxqAgGhzTjqdO', '', 'Female', '2002-11-08', '123456789123', 'Single', 'TN', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `leave_request`
--

CREATE TABLE `leave_request` (
  `Employee_ID` int(11) DEFAULT NULL,
  `Leave_Type` varchar(50) DEFAULT NULL,
  `Start_Date` date DEFAULT NULL,
  `End_Date` date DEFAULT NULL,
  `Reason` text DEFAULT NULL,
  `Status` enum('Pending','Approved','Denied') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_request`
--

INSERT INTO `leave_request` (`Employee_ID`, `Leave_Type`, `Start_Date`, `End_Date`, `Reason`, `Status`) VALUES
(1, 'Sick Leave', '2025-02-12', '2025-03-13', 'FEVER', 'Approved'),
(1, 'Casual Leave', '2025-03-20', '2025-03-29', 'Trip', 'Denied');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_user`
--
ALTER TABLE `admin_user`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`Employee_ID`),
  ADD UNIQUE KEY `Mobile_Number` (`Mobile_Number`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `Aadhaar_Number` (`Aadhaar_Number`);

--
-- Indexes for table `leave_request`
--
ALTER TABLE `leave_request`
  ADD KEY `Employee_ID` (`Employee_ID`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `leave_request`
--
ALTER TABLE `leave_request`
  ADD CONSTRAINT `leave_request_ibfk_1` FOREIGN KEY (`Employee_ID`) REFERENCES `employees` (`Employee_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
