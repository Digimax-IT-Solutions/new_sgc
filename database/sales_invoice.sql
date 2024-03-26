-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 26, 2024 at 06:40 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sales`
--

-- --------------------------------------------------------

--
-- Table structure for table `sales_invoice`
--

CREATE TABLE `sales_invoice` (
  `invoiceID` int(11) NOT NULL,
  `invoiceNo` varchar(255) DEFAULT NULL,
  `invoiceDate` date DEFAULT NULL,
  `invoiceDueDate` date DEFAULT NULL,
  `invoiceBusinessStyle` varchar(255) NOT NULL,
  `invoiceTin` varchar(255) NOT NULL,
  `customer` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `shippingAddress` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `account` varchar(255) DEFAULT NULL,
  `terms` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `paymentMethod` varchar(255) DEFAULT NULL,
  `grossAmount` decimal(10,2) DEFAULT NULL,
  `discountPercentage` decimal(5,2) DEFAULT NULL,
  `netAmountDue` decimal(10,2) DEFAULT NULL,
  `vatPercentage` decimal(5,2) DEFAULT NULL,
  `netOfVat` decimal(10,2) DEFAULT NULL,
  `taxWithheldPercentage` decimal(5,2) DEFAULT NULL,
  `totalAmountDue` decimal(10,2) DEFAULT NULL,
  `amountReceived` decimal(10,2) DEFAULT 0.00,
  `invoiceStatus` varchar(20) NOT NULL DEFAULT 'UNPAID',
  `status` varchar(255) DEFAULT 'active',
  `memo` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sales_invoice`
--
ALTER TABLE `sales_invoice`
  ADD PRIMARY KEY (`invoiceID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sales_invoice`
--
ALTER TABLE `sales_invoice`
  MODIFY `invoiceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
