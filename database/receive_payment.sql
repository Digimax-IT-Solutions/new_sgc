-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 21, 2024 at 04:19 PM
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
-- Database: `sgc_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `receive_payment`
--

CREATE TABLE `receive_payment` (
  `ID` int(11) NOT NULL,
  `receivedID` varchar(255) DEFAULT NULL,
  `ar_account` varchar(255) NOT NULL,
  `invoiceNo` varchar(255) NOT NULL,
  `customerName` varchar(255) DEFAULT NULL,
  `payment_amount` int(11) DEFAULT NULL,
  `receivedDate` date DEFAULT NULL,
  `paymentType` varchar(255) DEFAULT NULL,
  `RefNo` int(11) DEFAULT NULL,
  `discCredapplied` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receive_payment`
--

INSERT INTO `receive_payment` (`ID`, `receivedID`, `ar_account`, `invoiceNo`, `customerName`, `payment_amount`, `receivedDate`, `paymentType`, `RefNo`, `discCredapplied`) VALUES
(14, NULL, 'Accounts Receivable', '97', 'Theresa Cabigting', 100, '2024-10-10', 'cash', 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `receive_payment`
--
ALTER TABLE `receive_payment`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `receive_payment`
--
ALTER TABLE `receive_payment`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
