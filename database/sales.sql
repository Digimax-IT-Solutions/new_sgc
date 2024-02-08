-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 04, 2024 at 06:00 AM
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
-- Table structure for table `audit_trail`
--

CREATE TABLE `audit_trail` (
  `auditTrailID` int(11) NOT NULL,
  `tableName` varchar(255) DEFAULT NULL,
  `recordID` int(11) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `userID` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

CREATE TABLE `bills` (
  `bill_id` int(11) NOT NULL,
  `bankAccountName` varchar(255) NOT NULL,
  `vendor` varchar(255) NOT NULL,
  `reference_no` varchar(50) DEFAULT NULL,
  `address` text NOT NULL,
  `terms` varchar(255) NOT NULL,
  `bill_date` date NOT NULL,
  `bill_due` date NOT NULL,
  `memo` text DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bills_details`
--

CREATE TABLE `bills_details` (
  `detail_id` int(11) NOT NULL,
  `bill_id` int(11) NOT NULL,
  `account` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `memo` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_code` varchar(50) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `category_description` text DEFAULT NULL,
  `active_status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_code`, `category_name`, `category_description`, `active_status`, `created_at`, `updated_at`) VALUES
(11, '000', 'Category 1', 'Category 1', 1, '2024-01-07 19:27:49', '2024-01-07 19:27:49'),
(12, '001', 'Category 2', 'Category 2', 1, '2024-01-07 19:27:59', '2024-01-07 19:27:59');

-- --------------------------------------------------------

--
-- Table structure for table `chart_of_accounts`
--

CREATE TABLE `chart_of_accounts` (
  `account_id` int(11) NOT NULL,
  `account_no` varchar(50) NOT NULL,
  `account_type` varchar(50) DEFAULT NULL,
  `account_code` varchar(20) DEFAULT NULL,
  `sub_account_of` varchar(50) DEFAULT NULL,
  `account_name` varchar(100) DEFAULT NULL,
  `account_balance` varchar(50) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chart_of_accounts`
--

INSERT INTO `chart_of_accounts` (`account_id`, `account_no`, `account_type`, `account_code`, `sub_account_of`, `account_name`, `account_balance`, `description`) VALUES
(19, 'AC-000001', 'Bank', '10000', '', 'Petty Cash Fund', '', 'Petty Cash Fund'),
(20, 'AC-000020', 'Bank', '10100', '', 'Cash in Bank', '', 'Cash in Bank'),
(21, 'AC-000021', 'Accounts Receivable', '11000', '', 'Accounts Receivable', '42353.44', 'Accounts Receivable'),
(22, 'AC-000022', 'Other Current Assets', '12000', '', 'Inventory', '', 'Inventory'),
(23, 'AC-000023', 'Other Current Assets', '12100', '', 'Undeposited Fund', '12223092.05', 'Undeposited Fund'),
(24, 'AC-000024', 'Accounts Payable', '20000', '', 'Accounts Payable -  Trade', '', 'Accounts Payable - \r\nTrade'),
(25, 'AC-000025', 'Equity', '30000', '', 'Equity', '', 'Equity'),
(26, 'AC-000026', 'Income', '40000', '', 'Sales Income', '', 'Sales Income'),
(27, 'AC-000027', 'Cost of Goods Sold', '50000', '', 'Cost of Goods Sold ', '', 'Cost of Goods Sold '),
(28, 'AC-000028', 'Expense', '60100', '', 'Salaries and Wages', '', 'Salaries and Wages'),
(29, 'AC-000029', 'Expense', '60110', '', 'Repairs and  Maintenance', '', 'Repairs and \r\nMaintenance'),
(30, 'AC-000030', 'Expense', '60120', '', 'Gas and Fuel ', '', 'Gas and Fuel '),
(33, 'AC-000032', 'Accounts Payable', '20010', '', 'Accounts Payable - Non Trade', '', 'Accounts Payable - Non Trade');

-- --------------------------------------------------------

--
-- Table structure for table `checks`
--

CREATE TABLE `checks` (
  `checkID` int(11) NOT NULL,
  `bankAccountName` varchar(255) DEFAULT NULL,
  `payeeName` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `checkDate` date DEFAULT NULL,
  `referenceNo` varchar(50) DEFAULT NULL,
  `memo` text DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `check_expenses`
--

CREATE TABLE `check_expenses` (
  `expenseID` int(11) NOT NULL,
  `checkID` int(11) DEFAULT NULL,
  `accountName` varchar(255) DEFAULT NULL,
  `memo` text DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `check_items`
--

CREATE TABLE `check_items` (
  `itemID` int(11) NOT NULL,
  `checkID` int(11) DEFAULT NULL,
  `item` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `uom` varchar(50) DEFAULT NULL,
  `rate` decimal(15,2) DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customerID` int(11) NOT NULL,
  `customerCode` varchar(255) DEFAULT NULL,
  `customerName` varchar(255) DEFAULT NULL,
  `customerPaymentMethod` varchar(255) DEFAULT NULL,
  `customerBillingAddress` text DEFAULT NULL,
  `customerShippingAddress` text DEFAULT NULL,
  `customerTin` varchar(255) DEFAULT NULL,
  `contactNumber` varchar(255) DEFAULT NULL,
  `customerDeliveryType` varchar(255) DEFAULT NULL,
  `customerTerms` varchar(255) DEFAULT NULL,
  `customerEmail` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customerID`, `customerCode`, `customerName`, `customerPaymentMethod`, `customerBillingAddress`, `customerShippingAddress`, `customerTin`, `contactNumber`, `customerDeliveryType`, `customerTerms`, `customerEmail`, `created_at`) VALUES
(21, '001', 'Juan Dela Cruz', 'Cash', 'Blk 0 Lot 1 Juan St. Manila City', 'Blk 0 Lot 1 Juan St. Manila City', '44455111', '123123123', 'test', 'test', 'juandelacruz@email.com', '2024-01-07 19:30:07'),
(33, 'TC-001', 'Theresa Cabigting', 'COD', 'Quezon City', 'Quezon City', '123', '123123123', 'Pick-up', '7 days', 'gm@digisoftmanila.com', '2024-01-13 13:55:32');

-- --------------------------------------------------------

--
-- Table structure for table `general_journal`
--

CREATE TABLE `general_journal` (
  `id` int(11) NOT NULL,
  `entry_no` varchar(255) DEFAULT NULL,
  `journal_date` date DEFAULT NULL,
  `total_debit` decimal(10,2) DEFAULT NULL,
  `total_credit` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `itemID` int(11) NOT NULL,
  `itemCode` varchar(50) NOT NULL,
  `itemName` varchar(255) NOT NULL,
  `itemType` varchar(255) NOT NULL,
  `preferredVendor` varchar(255) NOT NULL,
  `reOrderPoint` int(11) DEFAULT 0,
  `itemSalesInfo` varchar(255) NOT NULL,
  `itemSrp` decimal(10,2) DEFAULT 0.00,
  `itemPurchaseInfo` varchar(255) NOT NULL,
  `itemCost` decimal(10,2) DEFAULT 0.00,
  `itemCategory` varchar(50) NOT NULL,
  `itemCogsAccount` varchar(255) NOT NULL,
  `itemIncomeAccount` varchar(255) NOT NULL,
  `itemAssetsAccount` varchar(255) NOT NULL,
  `itemQty` int(11) DEFAULT 0,
  `uom` varchar(20) DEFAULT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`itemID`, `itemCode`, `itemName`, `itemType`, `preferredVendor`, `reOrderPoint`, `itemSalesInfo`, `itemSrp`, `itemPurchaseInfo`, `itemCost`, `itemCategory`, `itemCogsAccount`, `itemIncomeAccount`, `itemAssetsAccount`, `itemQty`, `uom`, `createdAt`) VALUES
(26, '11kg pol ', '11kg pol ', 'Inventory', '', 0, '11kg pol', 0.00, '11kg pol', 500.00, '', 'Cost of Goods Sold ', 'Sales Income', 'Inventory', 4266, 'kg', '2024-01-13 13:52:11'),
(27, '11kg cv', '11kg cv', 'Inventory', '', 0, '11kg cv', 0.00, '11kg cv', 0.00, '', 'Cost of Goods Sold ', 'Sales Income', 'Inventory', -2327, NULL, '2024-01-13 13:53:53'),
(28, '22kg', '22kg', 'Inventory', '', 0, '22kg', 0.00, '22kg', 0.00, '', 'Cost of Goods Sold ', 'Sales Income', 'Inventory', 1643, NULL, '2024-01-13 13:54:11'),
(29, '50kg', '50kg', 'Inventory', '', 0, '50kg', 0.00, '50kg', 0.00, '', 'Cost of Goods Sold ', 'Sales Income', 'Inventory', -30, NULL, '2024-01-13 13:54:30'),
(30, 'Regulator', 'Regulator', 'Inventory', '', 0, 'Regulator', 0.00, 'Regulator', 0.00, '', 'Cost of Goods Sold ', 'Sales Income', 'Inventory', 740188, NULL, '2024-01-13 13:54:42'),
(31, 'Hose', 'Hose', 'Inventory', '', 0, 'Hose', 0.00, 'Hose', 0.00, '', 'Cost of Goods Sold ', 'Sales Income', 'Inventory', 333, '', '2024-01-13 13:54:54');

-- --------------------------------------------------------

--
-- Table structure for table `journal_entries`
--

CREATE TABLE `journal_entries` (
  `id` int(11) NOT NULL,
  `general_journal_id` int(11) NOT NULL,
  `account` varchar(255) NOT NULL,
  `debit` decimal(10,2) NOT NULL,
  `credit` decimal(10,2) NOT NULL,
  `memo` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `location_id` int(11) NOT NULL,
  `location_code` varchar(255) NOT NULL,
  `location_name` varchar(255) NOT NULL,
  `active_status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`location_id`, `location_code`, `location_name`, `active_status`) VALUES
(28, '000', 'Main Warehouse', 1);

-- --------------------------------------------------------

--
-- Table structure for table `other_names`
--

CREATE TABLE `other_names` (
  `otherNameID` int(11) NOT NULL,
  `otherCode` varchar(255) DEFAULT NULL,
  `otherName` varchar(255) DEFAULT NULL,
  `otherAccountNumber` varchar(255) DEFAULT NULL,
  `otherAddress` text DEFAULT NULL,
  `otherContactNumber` varchar(255) DEFAULT NULL,
  `otherEmail` varchar(255) DEFAULT NULL,
  `otherTerms` varchar(255) DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `payment_id` int(11) NOT NULL,
  `payment_code` varchar(255) NOT NULL,
  `payment_name` varchar(255) NOT NULL,
  `payment_description` text NOT NULL,
  `active_status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`payment_id`, `payment_code`, `payment_name`, `payment_description`, `active_status`) VALUES
(5, '000', 'Cash', 'Cash', 1),
(6, '001', 'Check', 'Check', 1),
(7, '002', 'Credit Card', 'Credit Card', 1);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order`
--

CREATE TABLE `purchase_order` (
  `poID` int(11) NOT NULL,
  `poNo` varchar(255) NOT NULL,
  `poDate` date NOT NULL,
  `poDueDate` date NOT NULL,
  `vendor` varchar(255) NOT NULL,
  `shippingAddress` text DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `terms` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `paymentMethod` varchar(255) NOT NULL,
  `grossAmount` decimal(10,2) NOT NULL,
  `discountPercentage` decimal(5,2) NOT NULL,
  `netAmountDue` decimal(10,2) NOT NULL,
  `vatPercentage` decimal(10,2) NOT NULL,
  `netOfVat` decimal(10,2) NOT NULL,
  `memo` text DEFAULT NULL,
  `totalAmountDue` decimal(10,2) NOT NULL,
  `poStatus` varchar(20) NOT NULL DEFAULT 'WAITING FOR DELIVERY',
  `status` varchar(225) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `itemID` int(11) NOT NULL,
  `poID` int(11) NOT NULL,
  `item` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `uom` varchar(255) NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'NOT RECEIVED'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `received_items`
--

CREATE TABLE `received_items` (
  `receiveID` int(11) NOT NULL,
  `poNo` varchar(50) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `terms` varchar(50) DEFAULT NULL,
  `dueDate` date DEFAULT NULL,
  `account` varchar(255) NOT NULL,
  `vendor` varchar(255) NOT NULL,
  `receiveDate` date DEFAULT NULL,
  `refNo` varchar(50) DEFAULT NULL,
  `totalAmount` decimal(10,2) DEFAULT NULL,
  `memo` text DEFAULT NULL,
  `grossAmount` decimal(10,2) DEFAULT NULL,
  `discountPercentage` decimal(10,2) DEFAULT NULL,
  `netAmountDue` decimal(10,2) DEFAULT NULL,
  `vatPercentage` decimal(10,2) DEFAULT NULL,
  `netOfVat` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `received_items_details`
--

CREATE TABLE `received_items_details` (
  `detailID` int(11) NOT NULL,
  `receiveID` int(11) DEFAULT NULL,
  `poItemID` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `item` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `uom` varchar(50) DEFAULT NULL,
  `rate` decimal(10,2) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_invoice`
--

CREATE TABLE `sales_invoice` (
  `invoiceID` int(11) NOT NULL,
  `invoiceNo` varchar(255) DEFAULT NULL,
  `invoiceDate` date DEFAULT NULL,
  `invoiceDueDate` date DEFAULT NULL,
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
  `invoiceStatus` varchar(20) NOT NULL DEFAULT 'UNPAID',
  `status` varchar(255) DEFAULT NULL,
  `memo` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_invoice_items`
--

CREATE TABLE `sales_invoice_items` (
  `itemID` int(11) NOT NULL,
  `salesInvoiceID` int(11) DEFAULT NULL,
  `item` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `uom` varchar(255) DEFAULT NULL,
  `rate` decimal(10,2) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `status` varchar(255) DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_tax`
--

CREATE TABLE `sales_tax` (
  `salesTaxID` int(11) NOT NULL,
  `salesTaxCode` varchar(255) NOT NULL,
  `salesTaxName` varchar(255) NOT NULL,
  `salesTaxRate` int(11) NOT NULL,
  `salesTaxDescription` varchar(255) NOT NULL,
  `activeStatus` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_tax`
--

INSERT INTO `sales_tax` (`salesTaxID`, `salesTaxCode`, `salesTaxName`, `salesTaxRate`, `salesTaxDescription`, `activeStatus`, `created_at`) VALUES
(4, '001', '12 % ', 12, 'VATable Sales', 1, '2024-01-07 19:23:04'),
(5, '002', 'VAT Exempt Sales', 0, 'VAT Exempt Sales', 1, '2024-01-07 19:23:11'),
(6, '003', 'Zero-Rated Sales', 0, 'Zero-Rated Sales', 1, '2024-01-07 19:24:13'),
(8, '004', 'Income', 10, 'Income Tax', 1, '2024-01-29 07:25:40');

-- --------------------------------------------------------

--
-- Table structure for table `terms`
--

CREATE TABLE `terms` (
  `term_id` int(11) NOT NULL,
  `term_code` varchar(255) NOT NULL,
  `term_name` varchar(255) NOT NULL,
  `term_days_due` int(11) NOT NULL,
  `term_description` text NOT NULL,
  `active_status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `terms`
--

INSERT INTO `terms` (`term_id`, `term_code`, `term_name`, `term_days_due`, `term_description`, `active_status`) VALUES
(16, '000', 'DUE ON RECIEPT', 0, 'DUE ON RECIEPT', 1),
(17, '001', 'NET 7', 7, 'NET 7', 1),
(18, '002', 'NET 15', 15, 'NET 15', 1),
(20, '003', 'Net 30', 30, 'None', 1);

-- --------------------------------------------------------

--
-- Table structure for table `uom`
--

CREATE TABLE `uom` (
  `uomID` int(11) NOT NULL,
  `uomCode` varchar(50) NOT NULL,
  `uomName` varchar(100) NOT NULL,
  `uomDescription` text DEFAULT NULL,
  `activeStatus` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uom`
--

INSERT INTO `uom` (`uomID`, `uomCode`, `uomName`, `uomDescription`, `activeStatus`, `created_at`) VALUES
(1, '123', 'kg', 'kg', 1, '2024-01-11 18:43:34');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `name`, `position`) VALUES
(1, 'admin', 'admin', 'Admin', 'admin'),
(4, 'cashier', 'cashier', 'cashier', 'cashier'),
(5, 'admin', 'digimaxadmin123', 'Digimax', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `vendorID` int(11) NOT NULL,
  `vendorCode` varchar(255) DEFAULT NULL,
  `vendorName` varchar(255) DEFAULT NULL,
  `vendorAccountNumber` varchar(255) DEFAULT NULL,
  `vendorAddress` text DEFAULT NULL,
  `vendorContactNumber` varchar(255) DEFAULT NULL,
  `vendorEmail` varchar(255) DEFAULT NULL,
  `vendorTerms` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`vendorID`, `vendorCode`, `vendorName`, `vendorAccountNumber`, `vendorAddress`, `vendorContactNumber`, `vendorEmail`, `vendorTerms`) VALUES
(46, 'V001', 'Phoenix Super LPG', '123123', 'phoenix ', '09999999', 'phoenix@gmail.com', '7 days');

-- --------------------------------------------------------

--
-- Table structure for table `wtax`
--

CREATE TABLE `wtax` (
  `wtaxID` int(11) NOT NULL,
  `wTaxCode` varchar(255) NOT NULL,
  `wTaxName` varchar(255) NOT NULL,
  `wTaxRate` int(11) NOT NULL,
  `wTaxDescription` varchar(255) NOT NULL,
  `activeStatus` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wtax`
--

INSERT INTO `wtax` (`wtaxID`, `wTaxCode`, `wTaxName`, `wTaxRate`, `wTaxDescription`, `activeStatus`, `created_at`) VALUES
(4, '001', 'Goods 1%', 1, 'Goods 1%', 1, '2024-01-07 19:21:22'),
(5, '002', 'Services 2%', 2, 'Services 2%', 1, '2024-01-07 19:21:32'),
(6, '003', 'Rent 5%', 5, 'Rent 5%', 1, '2024-01-07 19:21:45'),
(7, '004', 'Professional 10%', 10, 'Professional 10%', 1, '2024-01-07 19:21:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_trail`
--
ALTER TABLE `audit_trail`
  ADD PRIMARY KEY (`auditTrailID`);

--
-- Indexes for table `bills`
--
ALTER TABLE `bills`
  ADD PRIMARY KEY (`bill_id`);

--
-- Indexes for table `bills_details`
--
ALTER TABLE `bills_details`
  ADD PRIMARY KEY (`detail_id`),
  ADD KEY `bill_id` (`bill_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `chart_of_accounts`
--
ALTER TABLE `chart_of_accounts`
  ADD PRIMARY KEY (`account_id`);

--
-- Indexes for table `checks`
--
ALTER TABLE `checks`
  ADD PRIMARY KEY (`checkID`);

--
-- Indexes for table `check_expenses`
--
ALTER TABLE `check_expenses`
  ADD PRIMARY KEY (`expenseID`),
  ADD KEY `checkID` (`checkID`);

--
-- Indexes for table `check_items`
--
ALTER TABLE `check_items`
  ADD PRIMARY KEY (`itemID`),
  ADD KEY `checkID` (`checkID`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customerID`);

--
-- Indexes for table `general_journal`
--
ALTER TABLE `general_journal`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`itemID`);

--
-- Indexes for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `general_journal_id` (`general_journal_id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `other_names`
--
ALTER TABLE `other_names`
  ADD PRIMARY KEY (`otherNameID`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `purchase_order`
--
ALTER TABLE `purchase_order`
  ADD PRIMARY KEY (`poID`);

--
-- Indexes for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`itemID`),
  ADD KEY `poID` (`poID`);

--
-- Indexes for table `received_items`
--
ALTER TABLE `received_items`
  ADD PRIMARY KEY (`receiveID`);

--
-- Indexes for table `received_items_details`
--
ALTER TABLE `received_items_details`
  ADD PRIMARY KEY (`detailID`),
  ADD KEY `receiveID` (`receiveID`);

--
-- Indexes for table `sales_invoice`
--
ALTER TABLE `sales_invoice`
  ADD PRIMARY KEY (`invoiceID`);

--
-- Indexes for table `sales_invoice_items`
--
ALTER TABLE `sales_invoice_items`
  ADD PRIMARY KEY (`itemID`),
  ADD KEY `salesInvoiceID` (`salesInvoiceID`);

--
-- Indexes for table `sales_tax`
--
ALTER TABLE `sales_tax`
  ADD PRIMARY KEY (`salesTaxID`);

--
-- Indexes for table `terms`
--
ALTER TABLE `terms`
  ADD PRIMARY KEY (`term_id`);

--
-- Indexes for table `uom`
--
ALTER TABLE `uom`
  ADD PRIMARY KEY (`uomID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`vendorID`);

--
-- Indexes for table `wtax`
--
ALTER TABLE `wtax`
  ADD PRIMARY KEY (`wtaxID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_trail`
--
ALTER TABLE `audit_trail`
  MODIFY `auditTrailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `bills`
--
ALTER TABLE `bills`
  MODIFY `bill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `bills_details`
--
ALTER TABLE `bills_details`
  MODIFY `detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `chart_of_accounts`
--
ALTER TABLE `chart_of_accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `checks`
--
ALTER TABLE `checks`
  MODIFY `checkID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=209;

--
-- AUTO_INCREMENT for table `check_expenses`
--
ALTER TABLE `check_expenses`
  MODIFY `expenseID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `check_items`
--
ALTER TABLE `check_items`
  MODIFY `itemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `general_journal`
--
ALTER TABLE `general_journal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `itemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `journal_entries`
--
ALTER TABLE `journal_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `other_names`
--
ALTER TABLE `other_names`
  MODIFY `otherNameID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `purchase_order`
--
ALTER TABLE `purchase_order`
  MODIFY `poID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `itemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=255;

--
-- AUTO_INCREMENT for table `received_items`
--
ALTER TABLE `received_items`
  MODIFY `receiveID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

--
-- AUTO_INCREMENT for table `received_items_details`
--
ALTER TABLE `received_items_details`
  MODIFY `detailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=293;

--
-- AUTO_INCREMENT for table `sales_invoice`
--
ALTER TABLE `sales_invoice`
  MODIFY `invoiceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `sales_invoice_items`
--
ALTER TABLE `sales_invoice_items`
  MODIFY `itemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT for table `sales_tax`
--
ALTER TABLE `sales_tax`
  MODIFY `salesTaxID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `terms`
--
ALTER TABLE `terms`
  MODIFY `term_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `uom`
--
ALTER TABLE `uom`
  MODIFY `uomID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `vendorID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `wtax`
--
ALTER TABLE `wtax`
  MODIFY `wtaxID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bills_details`
--
ALTER TABLE `bills_details`
  ADD CONSTRAINT `bills_details_ibfk_1` FOREIGN KEY (`bill_id`) REFERENCES `bills` (`bill_id`);

--
-- Constraints for table `check_expenses`
--
ALTER TABLE `check_expenses`
  ADD CONSTRAINT `check_expenses_ibfk_1` FOREIGN KEY (`checkID`) REFERENCES `checks` (`checkID`) ON DELETE CASCADE;

--
-- Constraints for table `check_items`
--
ALTER TABLE `check_items`
  ADD CONSTRAINT `check_items_ibfk_1` FOREIGN KEY (`checkID`) REFERENCES `checks` (`checkID`) ON DELETE CASCADE;

--
-- Constraints for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD CONSTRAINT `journal_entries_ibfk_1` FOREIGN KEY (`general_journal_id`) REFERENCES `general_journal` (`id`);

--
-- Constraints for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `purchase_order_items_ibfk_1` FOREIGN KEY (`poID`) REFERENCES `purchase_order` (`poID`);

--
-- Constraints for table `received_items_details`
--
ALTER TABLE `received_items_details`
  ADD CONSTRAINT `received_items_details_ibfk_1` FOREIGN KEY (`receiveID`) REFERENCES `received_items` (`receiveID`);

--
-- Constraints for table `sales_invoice_items`
--
ALTER TABLE `sales_invoice_items`
  ADD CONSTRAINT `sales_invoice_items_ibfk_1` FOREIGN KEY (`salesInvoiceID`) REFERENCES `sales_invoice` (`invoiceID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
