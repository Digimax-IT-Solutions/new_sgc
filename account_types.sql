-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 18, 2024 at 09:29 AM
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
-- Database: `eurospec`
--

-- --------------------------------------------------------

--
-- Table structure for table `account_types`
--

CREATE TABLE `account_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `type_order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account_types`
--

INSERT INTO `account_types` (`id`, `name`, `category`, `type_order`) VALUES
(1, 'Accounts Payable', 'LIABILITIES', 6),
(2, 'Accounts Receivable', 'ASSETS', 2),
(3, 'Other Current Assets', 'ASSETS', 3),
(4, 'Other Current Liabilities', 'LIABILITIES', 7),
(5, 'Other Expense', 'EXPENSE', 15),
(6, 'Other Income', 'INCOME', 14),
(7, 'Fixed Assets', 'ASSETS', 4),
(8, 'Loans Payable', 'LIABILITIES', 8),
(9, 'Cost of Goods Sold', 'EXPENSE', 12),
(10, 'Equity', 'EQUITY', 10),
(11, 'Expenses', 'EXPENSE', 13),
(12, 'Income', 'INCOME', 11),
(13, 'Non-current Liabilities', 'LIABILITIES', 0),
(14, 'Cash and Cash Equivalents', 'ASSETS', 1),
(15, 'Other Non-current Liabilities', 'LIABILITIES', 9),
(16, 'Other Non-current Assets', 'ASSETS', 5);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_types`
--
ALTER TABLE `account_types`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account_types`
--
ALTER TABLE `account_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
