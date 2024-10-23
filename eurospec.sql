-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 18, 2024 at 05:53 AM
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

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`` PROCEDURE `GetTrialBalance` (IN `startDate` VARCHAR(10), IN `endDate` VARCHAR(10))   BEGIN
    SELECT 
        coa.account_code,
        coa.account_description,
        COALESCE(te.beginning_balance, coa.balance) AS beginning_balance,
        COALESCE(te.total_trial_balance, 0) AS total_trial_balance,
        COALESCE(te.ending_balance, coa.balance) AS ending_balance
    FROM 
        chart_of_account coa
    LEFT JOIN 
        (SELECT 
            account_id, 
            SUM(CASE WHEN transaction_date < STR_TO_DATE(startDate, '%d/%m/%Y') THEN balance ELSE 0 END) AS beginning_balance,
            SUM(CASE WHEN transaction_date BETWEEN STR_TO_DATE(startDate, '%d/%m/%Y') AND STR_TO_DATE(endDate, '%d/%m/%Y') THEN balance ELSE 0 END) AS total_trial_balance,
            SUM(CASE WHEN transaction_date <= STR_TO_DATE(endDate, '%d/%m/%Y') THEN balance ELSE 0 END) AS ending_balance
         FROM 
            transaction_entries
         GROUP BY 
            account_id
        ) te ON coa.id = te.account_id
    WHERE 
        COALESCE(te.ending_balance, coa.balance) != 0;
END$$

DELIMITER ;

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

-- --------------------------------------------------------

--
-- Table structure for table `apv`
--

CREATE TABLE `apv` (
  `id` int(11) NOT NULL,
  `account_id` int(11) DEFAULT NULL,
  `apv_no` varchar(100) DEFAULT NULL,
  `ref_no` varchar(100) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `apv_date` date DEFAULT NULL,
  `apv_due_date` date DEFAULT NULL,
  `terms_id` varchar(50) DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `locations` int(11) NOT NULL,
  `gross_amount` double(10,2) DEFAULT NULL,
  `discount_amount` double(10,2) DEFAULT NULL,
  `net_amount_due` double(10,2) DEFAULT NULL,
  `vat_percentage_amount` double(10,2) DEFAULT NULL,
  `net_of_vat` double(10,2) DEFAULT NULL,
  `tax_withheld_amount` double(10,2) DEFAULT NULL,
  `tax_withheld_percentage` int(11) NOT NULL,
  `wtax_account_id` int(11) DEFAULT NULL,
  `total_amount_due` double(10,2) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) DEFAULT NULL,
  `print_status` int(11) NOT NULL DEFAULT 0,
  `po_no` varchar(50) DEFAULT NULL,
  `rr_no` varchar(50) DEFAULT NULL,
  `vendor_tin` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `apv_details`
--

CREATE TABLE `apv_details` (
  `id` int(11) NOT NULL,
  `apv_id` int(11) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `cost_center_id` int(11) DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `amount` double(10,2) DEFAULT NULL,
  `discount_percentage` double(5,2) NOT NULL DEFAULT 0.00,
  `discount_amount` double(10,2) DEFAULT NULL,
  `net_amount_before_vat` double(10,2) DEFAULT NULL,
  `net_amount` double(10,2) DEFAULT NULL,
  `vat_percentage` double(5,2) NOT NULL DEFAULT 0.00,
  `input_vat` double(10,2) DEFAULT NULL,
  `discount_account_id` int(11) NOT NULL,
  `input_vat_account_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_trail`
--

CREATE TABLE `audit_trail` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `transaction_type` varchar(50) NOT NULL,
  `transaction_date` date DEFAULT NULL,
  `ref_no` varchar(50) NOT NULL,
  `location` int(11) DEFAULT 0,
  `name` varchar(255) DEFAULT NULL,
  `item` varchar(255) DEFAULT NULL,
  `qty_sold` int(11) DEFAULT NULL,
  `qty_purch` decimal(15,2) NOT NULL DEFAULT 0.00,
  `ave_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sell_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `cogs_sold` decimal(15,2) NOT NULL DEFAULT 0.00,
  `amt_sold` decimal(15,2) NOT NULL DEFAULT 0.00,
  `account_id` int(11) NOT NULL,
  `debit` double(15,2) DEFAULT 0.00,
  `credit` double(15,2) DEFAULT 0.00,
  `created_by` varchar(50) NOT NULL,
  `state` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_trail`
--

INSERT INTO `audit_trail` (`id`, `transaction_id`, `transaction_type`, `transaction_date`, `ref_no`, `location`, `name`, `item`, `qty_sold`, `qty_purch`, `ave_cost`, `cost`, `sell_price`, `cogs_sold`, `amt_sold`, `account_id`, `debit`, `credit`, `created_by`, `state`, `created_at`, `modified_at`) VALUES
(1, 1, 'General Journal', '2024-10-18', 'GJ000000001', 4, '3M MARBLE ENTERPRISES', NULL, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, 100.00, 0.00, 'superadmin', 1, '2024-10-17 16:02:03', '2024-10-17 16:02:03'),
(2, 1, 'General Journal', '2024-10-18', 'GJ000000001', 4, '3M MARBLE ENTERPRISES', NULL, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 3, 0.00, 100.00, 'superadmin', 1, '2024-10-17 16:02:03', '2024-10-17 16:02:03'),
(3, 8, 'Invoice', '2024-10-18', 'SI000000007', 4, 'CHULIANTE MARKETING CORPORATION', NULL, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 3, 4250000.00, 0.00, 'superadmin', 1, '2024-10-17 16:04:03', '2024-10-17 16:04:03'),
(4, 8, 'Invoice', '2024-10-18', 'SI000000007', 4, 'CHULIANTE MARKETING CORPORATION', NULL, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, 2352678.57, 0.00, 'superadmin', 1, '2024-10-17 16:04:03', '2024-10-17 16:04:03'),
(5, 8, 'Invoice', '2024-10-18', 'SI000000007', 4, 'CHULIANTE MARKETING CORPORATION', NULL, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 3, 0.00, 2352678.57, 'superadmin', 1, '2024-10-17 16:04:03', '2024-10-17 16:04:03'),
(6, 8, 'Invoice', '2024-10-18', 'SI000000007', 4, 'CHULIANTE MARKETING CORPORATION', 'Monster Energy Drink', 500, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 6039, 0.00, 0.00, 'superadmin', 1, '2024-10-17 16:04:03', '2024-10-17 16:04:03'),
(7, 8, 'Invoice', '2024-10-18', 'SI000000007', 4, 'CHULIANTE MARKETING CORPORATION', 'Monster Energy Drink', 500, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 11, 0.00, 1897321.43, 'superadmin', 1, '2024-10-17 16:04:03', '2024-10-17 16:04:03'),
(8, 8, 'Invoice', '2024-10-18', 'SI000000007', 4, 'CHULIANTE MARKETING CORPORATION', 'Monster Energy Drink', 500, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, 4250000.00, 0.00, 'superadmin', 1, '2024-10-17 16:04:03', '2024-10-17 16:04:03'),
(9, 8, 'Invoice', '2024-10-18', 'SI000000007', 4, 'CHULIANTE MARKETING CORPORATION', 'Monster Energy Drink', 500, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 10, 0.00, 4250000.00, 'superadmin', 1, '2024-10-17 16:04:03', '2024-10-17 16:04:03'),
(10, 8, 'Invoice', '2024-10-18', 'SI000000007', 4, 'CHULIANTE MARKETING CORPORATION', NULL, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 3, 0.00, 0.00, 'superadmin', 1, '2024-10-17 16:04:03', '2024-10-17 16:04:03'),
(11, 8, 'Invoice', '2024-10-18', 'SI000000007', 4, 'CHULIANTE MARKETING CORPORATION', NULL, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 3, 0.00, 2352678.57, 'superadmin', 1, '2024-10-17 16:04:03', '2024-10-17 16:04:03');

--
-- Triggers `audit_trail`
--
DELIMITER $$
CREATE TRIGGER `after_audit_trail_insert` AFTER INSERT ON `audit_trail` FOR EACH ROW BEGIN
    INSERT INTO transaction_entries (
        transaction_id,
        transaction_type,
        transaction_date,
        ref_no,
        name,
        item,
        qty_sold,
        account_id,
        debit,
        credit,
        balance
    )
    VALUES (
        NEW.transaction_id,
        NEW.transaction_type,
        NEW.transaction_date,
        NEW.ref_no,
        NEW.name,
        NEW.item,
        NEW.qty_sold,
        NEW.account_id,
        NEW.debit,
        NEW.credit,
        NEW.debit - NEW.credit
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(44, 'Category 1'),
(46, 'Maintenance Supplies'),
(22, 'Sample'),
(55, 'test');

-- --------------------------------------------------------

--
-- Table structure for table `chart_of_account`
--

CREATE TABLE `chart_of_account` (
  `id` int(11) NOT NULL,
  `account_code` varchar(50) NOT NULL,
  `account_type_id` int(11) NOT NULL,
  `account_name` varchar(50) NOT NULL,
  `account_description` varchar(250) DEFAULT NULL,
  `balance` double(15,2) DEFAULT 0.00,
  `sub_account_id` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chart_of_account`
--

INSERT INTO `chart_of_account` (`id`, `account_code`, `account_type_id`, `account_name`, `account_description`, `balance`, `sub_account_id`, `created_at`) VALUES
(1, '10100', 9, 'Cost of Goods Sold', 'Cost of Goods Sold', 0.00, 0, '2024-10-18 03:52:55'),
(2, '101001', 9, 'Cost of Goods Sold Raw', 'Cost of Goods Sold Raw', 0.00, 0, '2024-10-18 03:52:55'),
(3, '10000', 2, 'Accounts Receivable ss', 'Accounts Receivable ss', 0.00, 0, '2024-10-18 03:52:55'),
(4, '10001', 2, 'Accounts Receivable - Test', 'Accounts Receivable - Test', 0.00, 0, '2024-10-18 03:52:55'),
(5, '20011', 4, 'Liabilites Account', 'Liabilites Account', 0.00, 0, '2024-10-18 03:52:55'),
(6, '25001', 2, 'BDO CALAMBA 2', 'BDO CALAMBA 2', 0.00, 0, '2024-10-18 03:52:55'),
(7, '33333', 5, 'BDO 3444', 'BDO 3444', 0.00, 0, '2024-10-18 03:52:55'),
(8, '555', 10, 'qwewq', 'qweqwe', 0.00, 0, '2024-10-18 03:52:55'),
(9, '224424', 4, 'QQQ', 'QQQ', 0.00, 0, '2024-10-18 03:52:55'),
(10, '6666', 12, 'Income', 'Income', 0.00, 0, '2024-10-18 03:52:55'),
(11, '9899', 4, 'Other testing account', 'Other testing account', 0.00, 0, '2024-10-18 03:52:55');

-- --------------------------------------------------------

--
-- Table structure for table `checks`
--

CREATE TABLE `checks` (
  `id` int(11) NOT NULL,
  `cv_no` varchar(255) NOT NULL,
  `ref_no` varchar(255) NOT NULL,
  `payee_type` varchar(50) NOT NULL,
  `payee_id` int(11) NOT NULL,
  `check_date` datetime NOT NULL,
  `account_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `gross_amount` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `net_amount` decimal(10,2) NOT NULL,
  `taxable_amount` decimal(10,2) NOT NULL,
  `input_vat_amount` decimal(10,2) NOT NULL,
  `tax_withheld_amount` decimal(10,2) NOT NULL,
  `total_amount_due` decimal(10,2) NOT NULL,
  `create_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_settings`
--

CREATE TABLE `company_settings` (
  `id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `zip_code` varchar(20) NOT NULL,
  `contact` varchar(50) NOT NULL,
  `tin` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_settings`
--

INSERT INTO `company_settings` (`id`, `company_name`, `logo`, `address`, `zip_code`, `contact`, `tin`, `created_at`, `updated_at`) VALUES
(1, 'Eurospec', NULL, '5125', '125', '125', '125', '2024-10-17 15:37:58', '2024-10-17 16:29:14');

-- --------------------------------------------------------

--
-- Table structure for table `cost_center`
--

CREATE TABLE `cost_center` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `particular` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cost_center`
--

INSERT INTO `cost_center` (`id`, `code`, `particular`) VALUES
(2, '100', 'Office of the President'),
(3, '200', 'Office of the Asst.to the  President'),
(4, '201', 'Office of the Finance Controller'),
(5, '202', 'Office of the Purchasing Mgr.'),
(6, '300', 'Office of the Resident Mgr.'),
(7, '303', 'Office of the Asst. Resident Mgr.'),
(8, '301', 'Office of the Purchasing Mgr.'),
(9, '302', 'Purchasing Section'),
(10, '400', 'Office of the Plant Accountant'),
(11, '401', 'General Accounting Section'),
(12, '402', 'Production Acctg. Section'),
(13, '403', 'Cashiering Section'),
(14, '500', 'Office of the Admin. Mgr.'),
(16, '502', 'Medical / Dental Section'),
(17, '503', 'Service Vehicle Section'),
(18, '504', 'Civil Works Section'),
(19, '505', 'CDT Section'),
(20, '506', 'Office of the Trans. Supt.'),
(21, '507', 'Heavy Equipment Section'),
(22, '508', 'Mudpress/Bagasse Section'),
(23, '509', 'Trans. Maintenance Section'),
(24, '600', 'Office of  the Gen. Services Mgr.'),
(25, '601', 'Audit & Budget Section'),
(26, '602', 'Material Warehouse Section'),
(27, '603', 'Sugar/Molasses Section'),
(28, '700', 'Office of the Operation Div. Mgr.'),
(29, '701', 'Office of the Factory Maint. Supt.'),
(30, '702', 'Machine/Factory Maint. Section'),
(31, '703', 'Office of the Mill Caneyard Supt.'),
(32, '704', 'Mill Section'),
(33, '705', 'Cane Yard Section'),
(34, '706', 'Office of the Boiler Supt.'),
(35, '707', 'Boiler Section'),
(36, '708', 'Office of the PHESD Supt.'),
(37, '709', 'PowerHouseESD Section'),
(38, '710', 'Cooling Tower'),
(39, '800', 'Office of the Production Div. Mgr.'),
(40, '801', 'Refinery Department'),
(41, '802', 'Office of the Boiling House Supt.'),
(42, '803', 'Boiling House Department'),
(43, '804', 'Office of the QA Supt.'),
(44, '805', 'Quality Assurance Department'),
(47, '900', 'Makati');

-- --------------------------------------------------------

--
-- Table structure for table `credit_memo`
--

CREATE TABLE `credit_memo` (
  `id` int(11) NOT NULL,
  `credit_no` varchar(50) DEFAULT NULL,
  `credit_date` date NOT NULL,
  `customer_id` int(11) NOT NULL,
  `credit_account` varchar(100) NOT NULL,
  `memo` text DEFAULT NULL,
  `location` int(11) NOT NULL,
  `gross_amount` decimal(10,2) NOT NULL,
  `net_amount_due` decimal(10,2) NOT NULL,
  `vat_percentage_amount` decimal(10,2) DEFAULT NULL,
  `net_of_vat` decimal(10,2) DEFAULT NULL,
  `tax_withheld_amount` decimal(10,2) DEFAULT NULL,
  `tax_withheld_percentage` int(11) DEFAULT NULL,
  `total_amount_due` decimal(10,2) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `print_status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `credit_memo`
--

INSERT INTO `credit_memo` (`id`, `credit_no`, `credit_date`, `customer_id`, `credit_account`, `memo`, `location`, `gross_amount`, `net_amount_due`, `vat_percentage_amount`, `net_of_vat`, `tax_withheld_amount`, `tax_withheld_percentage`, `total_amount_due`, `status`, `created_at`, `updated_at`, `print_status`) VALUES
(2, 'CM000000001', '2024-09-20', 5, '1', '', 0, 124.00, 124.00, 0.00, 124.00, 2.48, 2, 121.52, 0, '2024-09-19 16:02:26', '2024-09-19 16:02:26', 0),
(3, 'CM000000001', '2024-09-20', 5, '1', '12412421412412', 0, 124.00, 124.00, 0.00, 124.00, 2.48, 2, 121.52, 0, '2024-09-19 16:03:21', '2024-09-19 16:03:21', 0),
(5, 'CM000000002', '2024-09-23', 6, '10', 'mfgvp;dmj', 0, 2000.00, 2000.00, 214.29, 1785.71, 35.71, 2, 1964.29, 0, '2024-09-23 06:25:32', '2024-09-23 06:25:32', 0),
(6, NULL, '2024-09-23', 6, '1', 'fdgfh', 0, 30.00, 30.00, 3.00, 26.00, 535.71, 2, 29.00, 4, '2024-09-23 06:29:03', '2024-09-23 06:29:42', 0),
(10, 'CM000000003', '2024-09-24', 5, '1', '124', 0, 124.00, 124.00, 13.29, 110.71, 4.43, 4, 119.57, 0, '2024-09-23 08:13:22', '2024-09-23 08:13:22', 0),
(11, 'CM000000004', '2024-09-24', 5, '1', '124', 0, 124.00, 124.00, 13.29, 110.71, 5.54, 5, 118.46, 0, '2024-09-23 08:14:02', '2024-09-23 08:14:03', 1),
(14, 'CM000000005', '2024-09-24', 5, '10', '124', 0, 124.00, 124.00, 13.29, 110.71, 5.54, 5, 118.46, 0, '2024-09-23 08:17:32', '2024-09-23 08:17:32', 0),
(15, 'CM000000006', '2024-09-25', 5, '10', '124124', 0, 100.00, 100.00, 10.71, 89.29, 1.79, 2, 98.21, 0, '2024-09-24 20:53:43', '2024-09-24 20:53:43', 0),
(16, 'CM000000007', '2024-09-25', 5, '10', '123', 0, 123.00, 123.00, 13.18, 109.82, 2.20, 2, 120.80, 0, '2024-09-24 21:14:03', '2024-09-24 21:14:03', 0),
(18, 'CM000000008', '2024-09-29', 5, '10', '124', 4, 124.00, 124.00, 13.29, 110.71, 2.21, 2, 121.79, 0, '2024-09-29 03:23:22', '2024-09-29 03:23:22', 0);

-- --------------------------------------------------------

--
-- Table structure for table `credit_memo_details`
--

CREATE TABLE `credit_memo_details` (
  `id` int(11) NOT NULL,
  `credit_memo_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `cost_center_id` int(11) DEFAULT NULL,
  `memo` text DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `net_amount` decimal(10,2) DEFAULT NULL,
  `taxable_amount` decimal(10,2) DEFAULT NULL,
  `vat_percentage` varchar(55) DEFAULT NULL,
  `sales_tax` decimal(10,2) DEFAULT NULL,
  `sales_tax_account_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `credit_memo_details`
--

INSERT INTO `credit_memo_details` (`id`, `credit_memo_id`, `account_id`, `cost_center_id`, `memo`, `amount`, `net_amount`, `taxable_amount`, `vat_percentage`, `sales_tax`, `sales_tax_account_id`, `created_at`, `updated_at`) VALUES
(2, 2, 1, 2, '', 124.00, 124.00, 124.00, '0', 0.00, 4034, '2024-09-19 16:02:26', '2024-09-19 16:02:26'),
(3, 3, 1, 2, '12412', 124.00, 124.00, 124.00, '0', 0.00, 4034, '2024-09-19 16:03:21', '2024-09-19 16:03:21'),
(5, 5, 6039, 28, 'fkdlsfds', 2000.00, 1785.71, 2000.00, '12', 214.29, 4037, '2024-09-23 06:25:32', '2024-09-23 06:25:32'),
(7, 6, 6039, 2, 'cfgcg', 30.00, 26.00, 30.00, '12', 3214.29, 3219, '2024-09-23 06:29:42', '2024-09-23 06:29:42'),
(11, 10, 4, 2, '124', 124.00, 110.71, 124.00, '12', 13.29, 4037, '2024-09-23 08:13:22', '2024-09-23 08:13:22'),
(12, 11, 5, 2, '124', 124.00, 110.71, 124.00, '12', 13.29, 4037, '2024-09-23 08:14:02', '2024-09-23 08:14:02'),
(15, 14, 13, 2, '124', 124.00, 110.71, 124.00, '12', 13.29, 4037, '2024-09-23 08:17:32', '2024-09-23 08:17:32'),
(16, 15, 1, 2, '124', 100.00, 89.29, 100.00, '12', 10.71, 4034, '2024-09-24 20:53:43', '2024-09-24 20:53:43'),
(17, 16, 4, 2, '123', 123.00, 109.82, 123.00, '12', 13.18, 4034, '2024-09-24 21:14:03', '2024-09-24 21:14:03'),
(19, 18, 1, 2, '124', 124.00, 110.71, 124.00, '12', 13.29, 4034, '2024-09-29 03:23:22', '2024-09-29 03:23:22');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_code` varchar(50) DEFAULT NULL,
  `customer_contact` varchar(50) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `billing_address` text DEFAULT NULL,
  `business_style` varchar(50) DEFAULT NULL,
  `customer_terms` varchar(50) DEFAULT NULL,
  `customer_tin` varchar(50) DEFAULT NULL,
  `customer_email` varchar(50) DEFAULT NULL,
  `credit_balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_invoiced` double(15,2) DEFAULT NULL,
  `total_paid` double(15,2) DEFAULT NULL,
  `total_credit_memo` double(15,2) DEFAULT NULL,
  `balance_due` double(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `customer_name`, `customer_code`, `customer_contact`, `shipping_address`, `billing_address`, `business_style`, `customer_terms`, `customer_tin`, `customer_email`, `credit_balance`, `total_invoiced`, `total_paid`, `total_credit_memo`, `balance_due`) VALUES
(5, 'CHULIANTE MARKETING CORPORATION', '001', '0', 'ORMOC CITY', 'ORMOC CITY', 'CHULIANTE MARKETING CORPORATION', '', '005-758-783', '', 1961961.33, NULL, NULL, 340.80, NULL),
(6, 'LA PERLA SUGAR EXPORT', '002', '0', 'MAKATI CITY', 'MAKATI CITY', 'LA PERLA SUGAR EXPORT', '', '023-825-855', '', 256188.33, NULL, NULL, 0.00, NULL),
(7, 'LEYTE AGRI CORPORATION', '003', '0', 'IPIL ORMOC CITY', 'IPIL ORMOC CITY', 'LEYTE AGRI CORPORATION', '', '005-758-549', '', 0.00, NULL, NULL, 0.00, NULL),
(8, 'ORMOC SIMEON', '004', '0', 'ORMOC CITY', 'ORMOC CITY', 'ORMOC SIMEON', '', '459-652-327', '', 0.00, NULL, NULL, 0.00, NULL),
(9, 'RIVER VALLEY DISTRIBUTOR', '005', '0', 'ORMOC CITY', 'ORMOC CITY', 'RIVER VALLEY DISTRIBUTOR', '', '006-042-175', '', 0.00, NULL, NULL, 0.00, NULL),
(10, 'SVG PHILIPPINES INC', '006', '0', 'BACOLOD CITY', 'BACOLOD CITY', 'SVG PHILIPPINES INC', '', '006-113-471', '', 0.00, NULL, NULL, 0.00, NULL),
(18, 'SCHUURMANS AND VAN GINNEKEN PHILS. INC.', '007', '-', '-', '-', 'SCHUURMANS AND VAN GINNEKEN PHILS. INC.', '', '-', '-', 0.00, NULL, NULL, 0.00, NULL),
(19, 'RECHIE T. LUCAÑAS', '008', '-', '-', '-', 'RECHIE T. LUCAÑAS', '', '-', '-', 0.00, NULL, NULL, 0.00, NULL),
(20, 'CHRISTOPHER C. BATICAN', '009', '-', '-', '-', 'CHRISTOPHER C. BATICAN', '', '-', '-', 0.00, NULL, NULL, 0.00, NULL),
(21, 'GENARO O. OCANG', '0010', '-', '-', '-', 'GENARO O. OCANG', '', '-', '-', 0.00, NULL, NULL, 0.00, NULL),
(22, 'SUGAR INDUSTRY FOUNDATION, INC.', '-', '-', '-', '-', '-', 'Due on Receipt', '-', '-', 0.00, NULL, NULL, 0.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `discount`
--

CREATE TABLE `discount` (
  `id` int(11) NOT NULL,
  `discount_name` varchar(50) NOT NULL,
  `discount_rate` float NOT NULL,
  `discount_description` text NOT NULL,
  `discount_account_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `discount`
--

INSERT INTO `discount` (`id`, `discount_name`, `discount_rate`, `discount_description`, `discount_account_id`, `created_at`) VALUES
(26, 'N/A', 0, 'N/A', 6039, '2024-07-14 20:34:29'),
(27, 'S01', 1, '1% Sales Discount', 6039, '2024-07-14 20:34:41'),
(28, 'S50', 50, '50% Sales Discount', 6039, '2024-07-14 20:34:50'),
(30, 'S10', 10, '10% Sales Discount', 6039, '2024-07-16 05:58:51'),
(31, 'S05', 5, '5% Sales Discount', 6039, '2024-07-16 05:59:32'),
(32, 'S02', 2, 'S02', 6039, '2024-09-11 03:56:21');

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `id` int(11) NOT NULL,
  `employee_code` int(11) NOT NULL,
  `employment_status` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `ext_name` varchar(255) NOT NULL,
  `co_name` varchar(255) NOT NULL,
  `tin` varchar(255) NOT NULL,
  `terms` varchar(255) NOT NULL,
  `house_lot_number` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `barangay` varchar(255) NOT NULL,
  `town` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `zip` int(11) NOT NULL,
  `sss` int(11) NOT NULL,
  `philhealth` varchar(255) NOT NULL,
  `pagibig` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`id`, `employee_code`, `employment_status`, `first_name`, `middle_name`, `last_name`, `ext_name`, `co_name`, `tin`, `terms`, `house_lot_number`, `street`, `barangay`, `town`, `city`, `zip`, `sss`, `philhealth`, `pagibig`) VALUES
(4, 0, 'KP', 'Lloyd', 'Banggay', 'Golez', 'N/A', 'Digiamx', '123-1235', '', 'BJ4', 'ST.', 'Mahogany', 'Calamba', 'Calamba', 2047, 12312312, '', 1231231);

-- --------------------------------------------------------

--
-- Table structure for table `fs_classification`
--

CREATE TABLE `fs_classification` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fs_classification`
--

INSERT INTO `fs_classification` (`id`, `name`) VALUES
(1, 'Cash'),
(2, 'Receivables'),
(3, 'Other Current Assets'),
(4, 'Inventories'),
(5, 'Other Noncurrent Assets'),
(6, 'Other Current Assests'),
(7, 'Investment and Advances to a Subsidiary'),
(8, 'PPE'),
(9, 'Accumulated Depreciation'),
(10, 'Accounts and Other Payables'),
(11, 'Deferred Tax Liability'),
(12, 'Loans Payable'),
(13, 'Authorized Capital Stock'),
(14, 'Unissued Capital Stock'),
(15, 'Capital Stock'),
(16, 'Retained Earnings (Deficit)'),
(17, 'Prior Period Adjustment'),
(18, 'Deposit on Future Stock Subscription'),
(19, 'Revenue & Expense Summary'),
(20, 'Revenue from contract with customers'),
(21, 'Interest Income'),
(22, 'EQUITY in Net Loss of Affiliated Co.'),
(23, 'Cost of Raw Sugar Sold'),
(24, 'Repairs and maintenance'),
(25, 'Fuel, Oil and Lubricant'),
(26, 'Supplies'),
(27, 'Salaries and Wages'),
(28, 'Employee Benefits'),
(29, 'Trucking hauling and trash incentives'),
(30, 'Contract Services'),
(31, 'Sugar & Molasses Handling'),
(32, 'Sugar Lien Expenses'),
(33, 'Finance Costs'),
(34, 'Provision for Impairment on Investment'),
(35, 'Provision For Income Tax'),
(36, 'Insurance Expenses'),
(37, 'Taxes & Licenses'),
(38, 'Security Services (Outside Services)'),
(39, 'Professional, Legal & Audit Fees'),
(40, 'Light & Power Expenses'),
(41, 'Freight & Handling'),
(42, 'Transportation & Travelling'),
(43, 'Miscellaneous Expenses'),
(44, 'Recruitment, Trainings & Seminars'),
(45, 'Medical & Dental Supplies'),
(46, 'Recreations & Other Social Activities'),
(47, 'Membership/Condominium dues'),
(48, 'Ads, Donations & Promotions'),
(49, 'Provision for impairment losses on CWT'),
(50, 'Depreciation Expenses'),
(51, 'Sales Discount'),
(52, 'Purchase Discount'),
(53, 'Cost of Refined Sugar Sold');

-- --------------------------------------------------------

--
-- Table structure for table `fs_notes_classification`
--

CREATE TABLE `fs_notes_classification` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fs_notes_classification`
--

INSERT INTO `fs_notes_classification` (`id`, `name`) VALUES
(1, 'Cash on hand'),
(2, 'Cash in bank'),
(3, 'Trade'),
(4, 'Others'),
(5, 'Planters'),
(6, 'Advances to Officers and Employees'),
(7, 'Other Current Assets'),
(8, 'Prepayments'),
(9, 'ALLOWANCE FOR DOUBTFUL ACCOUNT'),
(10, 'Sugar Inventory'),
(11, 'Molasses Inventory'),
(12, 'Materials, supplies and spare parts'),
(13, 'Fuel, Oil and Lubricant'),
(14, 'Material in Transit'),
(15, 'Allowance For Inventory Obsolescence'),
(16, 'Allowance For Impairment Loss on Input Vat'),
(17, 'Input Vat'),
(18, 'Allowance for Inv. Obsolescene'),
(19, 'Advance VAT'),
(20, 'Creditable Withholding Tax (CWT)'),
(21, 'Allowance For Impairment Loss on CWT'),
(22, 'Allowance For Impairment Loss on Taxes'),
(23, 'Advances'),
(24, 'Investment at Cost'),
(25, 'Allowance for Impairment on Investment'),
(26, 'Allowance for Impairment on Advances to Subsidiary'),
(27, 'Land'),
(28, 'Land Improvements'),
(29, 'Building, Office Condominium and Structures'),
(30, 'Mill, Refinery and Machinery Equipment'),
(31, 'Transportation Equipment'),
(32, 'Office Furniture and Equipment'),
(33, 'Laboratory and Communication Equipment'),
(34, 'Construction in Progress'),
(35, 'Deposits'),
(36, 'Accounts Payable'),
(37, 'Payable to Government Agencies'),
(38, 'Advances from Customers'),
(39, 'Deferred Tax liability'),
(40, 'Accrued Expenses'),
(41, 'Loans Payable'),
(42, 'Authorized Capital Stock'),
(43, 'Unissued Capital Stock'),
(44, 'Capital Stock'),
(45, 'Retained Earnings (Deficit)'),
(46, 'Provision for impairment losses on CWT'),
(47, 'Deposit on Future Stock Subscription'),
(48, 'Revenue & Expense Summary'),
(49, 'Sale of Raw Sugar'),
(50, 'Sale of Refined Sugar'),
(51, 'Sale of Molasses'),
(52, 'Tolling Services'),
(53, 'Milling Services'),
(54, 'Interest Income'),
(55, 'Storage, Handling, Hauling Fees and others'),
(56, 'EQUITY in Net Loss of Affiliated Co.'),
(57, 'Rental Income'),
(58, 'Cost of Raw Sugar Sold'),
(59, 'Repairs and maintenance'),
(60, 'Supplies'),
(61, 'Salaries and Wages'),
(62, 'Employee Benefits'),
(63, 'Trucking hauling and trash incentives'),
(64, 'Contract Services'),
(65, 'Sugar & Molasses Handling'),
(66, 'Sugar Lien Expenses'),
(67, 'Interest & Bank Charges'),
(68, 'Provision for Impairment on Investment'),
(69, 'Current'),
(70, 'Miscellaneous Expenses'),
(71, 'Taxes & Licenses'),
(72, 'Security Services (Outside Services)'),
(73, 'Professional, Legal & Audit Fees'),
(74, 'Light & Power Expenses'),
(75, 'Freight & Handling'),
(76, 'Transportation & Travelling'),
(77, 'Recruitment, Trainings & Seminars'),
(78, 'Medical & Dental Supplies'),
(79, 'Recreations & Other Social Activities'),
(80, 'Membership/Condominium dues'),
(81, 'Ads, Donations & Promotions'),
(82, 'Depreciation Expenses'),
(83, 'Undeposited Funds'),
(84, 'Sales Discount'),
(85, 'Purchase Discount');

-- --------------------------------------------------------

--
-- Table structure for table `general_journal`
--

CREATE TABLE `general_journal` (
  `id` int(11) NOT NULL,
  `entry_no` varchar(50) DEFAULT NULL,
  `journal_date` date NOT NULL,
  `total_debit` decimal(15,2) NOT NULL,
  `total_credit` decimal(15,2) NOT NULL,
  `memo` text DEFAULT NULL,
  `location` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `print_status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `general_journal`
--

INSERT INTO `general_journal` (`id`, `entry_no`, `journal_date`, `total_debit`, `total_credit`, `memo`, `location`, `status`, `created_at`, `updated_at`, `print_status`) VALUES
(1, 'GJ000000001', '2024-10-18', 100.00, 100.00, '123', 4, 1, '2024-10-17 16:02:03', '2024-10-17 16:31:13', 2);

-- --------------------------------------------------------

--
-- Table structure for table `general_journal_details`
--

CREATE TABLE `general_journal_details` (
  `id` int(11) NOT NULL,
  `general_journal_id` int(11) NOT NULL,
  `cost_center_id` int(11) DEFAULT NULL,
  `account_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `debit` decimal(15,2) DEFAULT 0.00,
  `credit` decimal(15,2) DEFAULT 0.00,
  `memo` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `general_journal_details`
--

INSERT INTO `general_journal_details` (`id`, `general_journal_id`, `cost_center_id`, `account_id`, `name`, `debit`, `credit`, `memo`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 1, '3M MARBLE ENTERPRISES', 100.00, 0.00, '123', '2024-10-17 16:02:03', '2024-10-17 16:02:03'),
(2, 1, 2, 3, '3M MARBLE ENTERPRISES', 0.00, 100.00, '123', '2024-10-17 16:02:03', '2024-10-17 16:02:03');

-- --------------------------------------------------------

--
-- Table structure for table `input_vat`
--

CREATE TABLE `input_vat` (
  `id` int(11) NOT NULL,
  `input_vat_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `input_vat_rate` int(11) NOT NULL,
  `input_vat_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `input_vat_account_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `input_vat`
--

INSERT INTO `input_vat` (`id`, `input_vat_name`, `input_vat_rate`, `input_vat_description`, `input_vat_account_id`, `created_at`) VALUES
(31, '12%', 12, '12%', 3219, '2024-07-14 14:58:47'),
(32, 'E', 0, 'VAT Exempt', 3219, '2024-07-14 20:41:01'),
(33, 'Z', 0, 'Zero-rated', 3219, '2024-07-14 20:41:24'),
(34, 'N/A', 0, 'N/A', 3219, '2024-07-20 02:01:23'),
(35, 'NV', 0, 'Non-VAT', 3219, '2024-08-14 05:50:54');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `ref_no` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `name` varchar(100) NOT NULL,
  `item_id` int(11) NOT NULL,
  `qty_purchased` int(11) DEFAULT 0,
  `qty_sold` int(11) DEFAULT 0,
  `qty_on_hand` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_valuation`
--

CREATE TABLE `inventory_valuation` (
  `id` int(11) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `ref_no` varchar(50) DEFAULT '',
  `date` date DEFAULT NULL,
  `name` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `qty_purchased` decimal(10,2) DEFAULT 0.00,
  `qty_sold` decimal(10,2) DEFAULT 0.22,
  `qty_on_hand` decimal(10,2) DEFAULT 0.00,
  `cost` decimal(15,2) DEFAULT 0.00,
  `total_cost` decimal(15,2) DEFAULT 0.00,
  `purchase_discount_rate` decimal(15,2) DEFAULT 0.00,
  `purchase_discount_per_item` decimal(15,2) DEFAULT 0.00,
  `purchase_discount_amount` decimal(15,2) DEFAULT 0.00,
  `net_amount` decimal(15,2) DEFAULT 0.00,
  `input_vat_rate` decimal(15,2) DEFAULT 0.00,
  `input_vat` decimal(15,2) DEFAULT 0.00,
  `taxable_purchased_amount` decimal(15,2) DEFAULT 0.00,
  `cost_per_unit` decimal(15,2) DEFAULT 0.00,
  `selling_price` decimal(15,2) DEFAULT 0.00,
  `gross_sales` decimal(15,2) DEFAULT 0.00,
  `sales_discount_rate` decimal(15,2) DEFAULT 0.00,
  `sales_discount_amount` decimal(15,2) DEFAULT 0.00,
  `net_sales` decimal(15,2) DEFAULT 0.00,
  `sales_tax` decimal(15,2) DEFAULT 0.00,
  `output_vat` decimal(15,2) DEFAULT 0.00,
  `taxable_sales_amount` decimal(15,2) DEFAULT 0.00,
  `selling_price_per_unit` decimal(15,2) DEFAULT 0.00,
  `weighted_average_cost` decimal(15,2) DEFAULT 0.00,
  `asset_value_wa` decimal(15,2) DEFAULT 0.00,
  `fifo_cost` decimal(15,2) DEFAULT 0.00,
  `cost_of_goods_sold` decimal(15,2) DEFAULT 0.00,
  `asset_value_fifo` decimal(15,2) DEFAULT 0.00,
  `gross_margin` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_valuation`
--

INSERT INTO `inventory_valuation` (`id`, `type`, `transaction_id`, `ref_no`, `date`, `name`, `item_id`, `qty_purchased`, `qty_sold`, `qty_on_hand`, `cost`, `total_cost`, `purchase_discount_rate`, `purchase_discount_per_item`, `purchase_discount_amount`, `net_amount`, `input_vat_rate`, `input_vat`, `taxable_purchased_amount`, `cost_per_unit`, `selling_price`, `gross_sales`, `sales_discount_rate`, `sales_discount_amount`, `net_sales`, `sales_tax`, `output_vat`, `taxable_sales_amount`, `selling_price_per_unit`, `weighted_average_cost`, `asset_value_wa`, `fifo_cost`, `cost_of_goods_sold`, `asset_value_fifo`, `gross_margin`) VALUES
(1, 'Invoice', 5, 'SI000000004', '2024-09-24', 5, 1, 0.00, 1000.00, -1000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 250.00, 250000.00, 0.00, 0.00, 250000.00, 0.00, 0.00, 250000.00, 250.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(2, 'Invoice', 7, 'SI000000006', '2024-09-24', 7, 2, 0.00, 150.00, -150.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 250.00, 37500.00, 1.00, 375.00, 37125.00, 0.00, 0.00, 37125.00, 247.50, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(3, 'Invoice', 7, 'SI000000006', '2024-09-24', 7, 1, 0.00, 200.00, -1200.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 150.00, 30000.00, 50.00, 15000.00, 15000.00, 0.00, 0.00, 15000.00, 75.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(4, 'Invoice', 1, 'SI000000001', '2024-09-25', 6, 1, 0.00, 124.00, -1324.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 124.00, 15376.00, 1.00, 153.76, 15222.24, 12.00, 1630.95, 13591.29, 109.61, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(5, 'Purchase', 3, 'RR000000002', '2024-09-25', 1, 5, 100.00, 0.00, 100.00, 250.00, 25000.00, 0.00, 0.00, 0.00, 25000.00, 12.00, 2678.57, 22321.43, 223.21, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 250.00, 25000.00, 223.21, 0.00, 22321.00, 0.00),
(6, 'Invoice', 3, 'SI000000003', '2024-09-25', 5, 5, 0.00, 100.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 500.00, 50000.00, 0.00, 0.00, 50000.00, 12.00, 5357.14, 44642.86, 446.43, 250.00, 0.00, 250.00, 0.00, 0.00, 0.00),
(7, 'Purchase', 4, 'RR000000003', '2024-09-25', 1, 5, 250.00, 0.00, 250.00, 500.00, 125000.00, 0.00, 0.00, 0.00, 125000.00, 12.00, 13392.86, 111607.14, 446.43, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 500.00, 125000.00, 446.43, 0.00, 111607.50, 0.00),
(8, 'Purchase', 5, 'RR000000004', '2024-09-25', 1, 5, 350.00, 0.00, 600.00, 124.00, 43400.00, 0.00, 0.00, 0.00, 43400.00, 12.00, 4650.00, 38750.00, 110.71, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 280.67, 168402.00, 110.71, 0.00, 66426.00, 0.00),
(9, 'Purchase', 6, 'RR000000005', '2024-09-30', 1, 1, 100.00, 0.00, -1224.00, 1000.00, 100000.00, 0.00, 0.00, 0.00, 100000.00, 0.00, 0.00, 100000.00, 1000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1000.00, 100000.00, 1000.00, 0.00, -1224000.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_code` varchar(50) DEFAULT NULL,
  `item_type` varchar(50) DEFAULT NULL,
  `item_vendor_id` int(11) DEFAULT 0,
  `item_uom_id` int(11) DEFAULT 0,
  `item_reorder_point` double(10,2) DEFAULT 0.00,
  `item_category_id` int(11) DEFAULT 0,
  `item_quantity` int(11) DEFAULT 0,
  `item_sales_description` text DEFAULT NULL,
  `item_purchase_description` text DEFAULT NULL,
  `item_selling_price` decimal(10,2) DEFAULT 0.00,
  `item_cost_price` decimal(10,2) DEFAULT 0.00,
  `item_cogs_account_id` int(11) DEFAULT NULL,
  `item_income_account_id` int(11) DEFAULT NULL,
  `item_asset_account_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `item_name`, `item_code`, `item_type`, `item_vendor_id`, `item_uom_id`, `item_reorder_point`, `item_category_id`, `item_quantity`, `item_sales_description`, `item_purchase_description`, `item_selling_price`, `item_cost_price`, `item_cogs_account_id`, `item_income_account_id`, `item_asset_account_id`, `created_at`) VALUES
(1, 'Capsule, Paracetamol Ibuprofen', '7005003', 'Inventory', NULL, NULL, NULL, NULL, 500, 'Capsule, Paracetamol Ibuprofen', NULL, NULL, NULL, 5904, 4603, 3198, '2024-09-19 07:27:25'),
(2, 'Hydrogen Peroxide 150ml.', '7096002', 'Inventory', NULL, NULL, NULL, NULL, 100, 'Hydrogen Peroxide 150ml.', NULL, NULL, NULL, 5904, 4603, 3198, '2024-09-19 07:27:25'),
(3, 'Alcohol, Casino 70% sol. 500ml', '7123001', 'Inventory', NULL, NULL, NULL, NULL, 200, 'Alcohol, Casino 70% sol. 500ml', NULL, NULL, NULL, 5904, 4603, 3198, '2024-09-19 07:27:25'),
(4, 'Alcohol, Rubbing 70% Solution', '7123009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Alcohol, Rubbing 70% Solution', NULL, NULL, NULL, 5904, 4603, 3198, '2024-09-19 07:27:25'),
(5, 'Capsule, Carbocisteine 500mg.', '7137004', 'Inventory', NULL, NULL, NULL, NULL, 1100, 'Capsule, Carbocisteine 500mg.', NULL, NULL, NULL, 5904, 4603, 3198, '2024-09-19 07:27:25'),
(6, 'Bandage, Gauze 4\" x 4\"', '7173003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bandage, Gauze 4\" x 4\"', NULL, NULL, NULL, 5904, 4603, 3198, '2024-09-19 07:27:25'),
(7, 'Plaster, Micropore 3M', '7173011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plaster, Micropore 3M', NULL, NULL, NULL, 5904, 4603, 3198, '2024-09-19 07:27:25'),
(8, 'Paracetamol, Symdex-D', '7191003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paracetamol, Symdex-D', NULL, NULL, NULL, 5904, 4603, 3198, '2024-09-19 07:27:25'),
(9, 'Paracetamol, Coldzep', '7191004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paracetamol, Coldzep', NULL, NULL, NULL, 5904, 4603, 3198, '2024-09-19 07:27:25'),
(10, 'Tablet, Histacort', '7206002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tablet, Histacort', NULL, NULL, NULL, 5904, 4603, 3198, '2024-09-19 07:27:25'),
(11, 'Tablet, Hyoscine-N-Butylbromide 10mg.', '7207003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tablet, Hyoscine-N-Butylbromide 10mg.', NULL, NULL, NULL, 5904, 4603, 3198, '2024-09-19 07:27:25'),
(12, 'Capsule, Loperamide 2mg.', '7217004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capsule, Loperamide 2mg.', NULL, NULL, NULL, 5904, 4603, 3198, '2024-09-19 07:27:25'),
(13, 'Plaster, Leukoplast 5cmx5m', '7237004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plaster, Leukoplast 5cmx5m', NULL, NULL, NULL, 5904, 4603, 3198, '2024-09-19 07:27:25'),
(14, 'Plaster, 2.5cm x 9m', '7237052', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plaster, 2.5cm x 9m', NULL, NULL, NULL, 5904, 4603, 3198, '2024-09-19 07:27:25'),
(15, 'Tablet, Aluminum Magnesium', '7307002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tablet, Aluminum Magnesium', NULL, NULL, NULL, 5904, 4603, 3198, '2024-09-19 07:27:25'),
(16, 'Paracetamol 500mg.', '7326004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paracetamol 500mg.', NULL, NULL, NULL, 5904, 4603, 3198, '2024-09-19 07:27:25'),
(17, 'Eye Mo Red 7.5ml.', '7349003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Eye Mo Red 7.5ml.', NULL, NULL, NULL, 5904, 4603, 3198, '2024-09-19 07:27:25'),
(18, 'Capsule, Mefenamic Acid 500mg.', '7350002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capsule, Mefenamic Acid 500mg.', NULL, NULL, NULL, 5904, 4603, 3198, '2024-09-19 07:27:25'),
(19, 'Tablet, Mefenamic Acid 500mg.', '7350003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tablet, Mefenamic Acid 500mg.', NULL, NULL, NULL, 5904, 4603, 3198, '2024-09-19 07:27:25'),
(20, 'Ointment, Skin Terramycin 3.5g', '7402003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ointment, Skin Terramycin 3.5g', NULL, NULL, NULL, 5904, 4603, 3198, '2024-09-19 07:27:25'),
(21, 'Tablet, Maleate Prednisolone+', '7402007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tablet, Maleate Prednisolone+', NULL, NULL, NULL, 5904, 4603, 3198, '2024-09-19 07:27:25'),
(22, 'Needle, Guide PN-104033', '1001005', 'Inventory', NULL, 11, 0.00, 46, 0, 'Needle, Guide PN-104033', '', 0.00, 0.00, 1, 4, 5, '2024-09-19 07:27:25'),
(23, 'Needle, Guide PN-104251', '1001006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Needle, Guide PN-104251', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(24, 'Bearing, #309 RDT 1½\"', '1004004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #309 RDT 1½\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(25, 'Bearing, #1211 ETN9/C3 SKF', '1004008B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #1211 ETN9/C3 SKF', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(26, 'Bearing, Ball #2309', '1004010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #2309', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(27, 'Bearing,Ball #6010', '1004011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing,Ball #6010', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(28, 'Bearing, Ball 6203/C3', '1004011A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6203/C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(29, 'Bearing, Ball #6203 2Z/C3 FAG', '1004012A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6203 2Z/C3 FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(30, 'Bearing, Ball 4208 BB.TBH FAG', '1004013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 4208 BB.TBH FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(31, 'Bearing, Ball 6205 C3 \"FAG\"', '1004014A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6205 C3 \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(32, 'Bearing, Ball #6205/C3', '1004014B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6205/C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(33, 'Bearing, Ball #6205 C C3', '1004014C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6205 C C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(34, 'Bearing, Ball 6207 2ZR \"FAG\"', '1004016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6207 2ZR \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(35, 'Bearing, Ball #6208 2Z/C3 FAG', '1004018A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6208 2Z/C3 FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(36, 'Bearing, Ball 6305 H 2Z/C3  \"F', '1004019C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6305 H 2Z/C3  \"F', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(37, 'Bearing, #6307 C3 \"FAG\"', '1004020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #6307 C3 \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(38, 'Bearing, Ball #6202 2Z/C3 FAG', '1004023A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6202 2Z/C3 FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(39, 'Bearing, #7202 C3 SKF', '1004024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #7202 C3 SKF', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(40, 'Bearing, #6211 C3', '1004026', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #6211 C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(41, 'Bearing, 6211 2ZRC3 \"FAG\"', '1004026D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, 6211 2ZRC3 \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(42, 'Bearing, Ball 6304/C3 FAG', '1004027', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6304/C3 FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(43, 'Bearing, Ball #6304 2Z/C3', '1004027B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6304 2Z/C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(44, 'Bearing, Ball 6210 2Z/C3 FAG', '1004028A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6210 2Z/C3 FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(45, 'Bearing, Ball 6204 C3 \"FAG\"', '1004030A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6204 C3 \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(46, 'Bearing, Ball 6204 2ZRC3', '1004030D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6204 2ZRC3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(47, 'Bearing, #6209 2Z SKF', '1004031', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #6209 2Z SKF', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(48, 'Bearing, Ball #6209 2Z/C3', '1004031C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6209 2Z/C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(49, 'Bearing, Ball 6310 C3 \"FAG\"', '1004033C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6310 C3 \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(50, 'Bearing, Ball #6318', '1004034', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6318', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(51, 'Bearing, Ball 6318 2Z/C3', '1004034A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6318 2Z/C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(52, 'Bearing, 6310 2ZRC3 \"FAG\"', '1004035A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, 6310 2ZRC3 \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(53, 'Bearing, Ball #6311 2Z/C3 FAG', '1004039B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6311 2Z/C3 FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(54, 'Bearing, Ball 6311/C3 FAG', '1004039C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6311/C3 FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(55, 'Bearing, #6314 2Z SKF', '1004040', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #6314 2Z SKF', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(56, 'Bearing, Ball #6320 Deep Gro', '1004041', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6320 Deep Gro', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(57, 'Bearing, Ball 6212/C3', '1004042B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6212/C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(58, 'Bearing, Ball #3209', '1004044', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #3209', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(59, 'Bearing, Ball 6000 C-2ZC3 FAG', '1004045B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6000 C-2ZC3 FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(60, 'Bearing, Ball # 6314 2Z/C3 FAG', '1004047', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball # 6314 2Z/C3 FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(61, 'Bearing, Ball 6024 C3 \"FAG\"', '1004048', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6024 C3 \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(62, 'Bearing, Ball 6411 C3 \"FAG\"', '1004050', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6411 C3 \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(63, 'Bearing, Ball 6411 C-C3', '1004050A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6411 C-C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(64, 'Bearing, Ball #6411 C3 \"SKF\"', '1004052', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6411 C3 \"SKF\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(65, 'Bearing, Ball #6307 2Z/C3', '1004055', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6307 2Z/C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(66, 'Bearing, Ball 7315 B-XL-MP', '1004057B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 7315 B-XL-MP', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(67, 'Bearing, Ball #6414 2Z/C3', '1004059A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6414 2Z/C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(68, 'Bearing, Ball #6020', '1004061', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6020', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(69, 'Bearing, Ball #6201 2Z', '1004063', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6201 2Z', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(70, 'Bearing, Ball 6201 ZZ-C3', '1004063D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6201 ZZ-C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(71, 'Bearing, Ball #6214 2Z/C3', '1004064B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6214 2Z/C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(72, 'Bearing, Ball #202', '1004068', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #202', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(73, 'Bearing, Ball #6410 DeepGror', '1004069', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6410 DeepGror', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(74, 'Bearing, Ball 7321BMP \"FAG\"', '1004070B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 7321BMP \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(75, 'Bearing, Ball #7321 B-XL-MP', '1004070C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #7321 B-XL-MP', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(76, 'Bearing, Ball #6309 2Z/C3 FAG', '1004071B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6309 2Z/C3 FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(77, 'Bearing, Ball #6317 2Z/C3', '1004075B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6317 2Z/C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(78, 'Bearing, Ball #6004 C C3', '1004076A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6004 C C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(79, 'Bearing, Ball 6004 2Z/C3 FAG', '1004076C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6004 2Z/C3 FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(80, 'Bearing,Ball3310B-TVH-L285 F', '1004080', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing,Ball3310B-TVH-L285 F', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(81, 'Bearing, Ball #6406 (632)', '1004081', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6406 (632)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(82, 'Bearing,Ball 6406 A-C3 #E FAG', '1004081A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing,Ball 6406 A-C3 #E FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(83, 'Bearing, Ball 6408 C3 \"FAG\"', '1004082', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6408 C3 \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(84, 'Bearing, Ball 7222.XL.MP.UA', '1004084D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 7222.XL.MP.UA', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(85, 'Bearing, Ball #7305 BEP SKF', '1004085', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #7305 BEP SKF', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(86, 'Bearing, Ball #7322', '1004088', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #7322', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(87, 'Bearing, Ball #22222 ZKL', '1004089', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #22222 ZKL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(88, 'Bearing, 22222 E1K C3+H322 F', '1004089A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, 22222 E1K C3+H322 F', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(89, 'Bearing, 22222E1.C3 \"FAG\"', '1004089C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, 22222E1.C3 \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(90, 'Bearing, Ball #22222E1-XL C3', '1004089D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #22222E1-XL C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(91, 'Bearing, #7322 BJP', '1004090A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #7322 BJP', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(92, 'Bearing, Ball #6906 ZZ', '1004091', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6906 ZZ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(93, 'Bearing, Ball #6312 2Z/C3 FAG', '1004091A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6312 2Z/C3 FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(94, 'Bearing, Ball 6220 2Z/C3 FAG', '1004092', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6220 2Z/C3 FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(95, 'Bearing, Ball #6220', '1004093', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6220', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(96, 'Bearing, #22216ES - C3', '1004094', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #22216ES - C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(97, 'Bearing, Ball #409', '1004095', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #409', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(98, 'Bearing, 6315 2ZC3 \"FAG\"', '1004096A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, 6315 2ZC3 \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(99, 'Bearing, Ball 6315 2ZR/C3 FA', '1004096B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6315 2ZR/C3 FA', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(100, 'Bearing, Ball #6219 2Z/C3', '1004097A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6219 2Z/C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(101, 'Bearing, Ball 6312-C C3', '1004098D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6312-C C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(102, 'Bearing, Ball #6316 ZZ', '1004099', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6316 ZZ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(103, 'Bearing, Ball #6316 2Z/C3 SK', '1004099A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6316 2Z/C3 SK', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(104, 'Bearing, Ball 6316 2Z/C3 FAG', '1004099B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6316 2Z/C3 FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(105, 'Bearing, Ball #6021 C3', '1004100', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6021 C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(106, 'Bearing, Ball #6213 2Z/C3 FAG', '1004104B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6213 2Z/C3 FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(107, 'Bearing, 6306 2ZRC3 \"FAG\"', '1004105B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, 6306 2ZRC3 \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(108, 'Bearing, Ball 6306/C3', '1004105C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6306/C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(109, 'Bearing, Ball #6306', '1004105D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6306', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(110, 'Bearing, Ball #6319', '1004106', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6319', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(111, 'Bearing 6319-2Z-C3 \"FAG\"', '1004106A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing 6319-2Z-C3 \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(112, 'Bearing, Ball #6216', '1004108', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6216', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(113, 'Bearing, Ball 7306 B-TVP FAG', '1004109A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 7306 B-TVP FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(114, 'Bearing, Ball 7306B.XL.JP', '1004109B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 7306B.XL.JP', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(115, 'Bearing, Cyl Rllr 3NU 04/C3', '1004111A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Cyl Rllr 3NU 04/C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(116, 'Bearing, Ball #6313 2Z/C3', '1004111B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6313 2Z/C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(117, 'Bearing,Ball NU306 E-XL-TVP2', '1004112B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing,Ball NU306 E-XL-TVP2', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(118, 'Bearing, Ball QJ310 MPA FAG', '1004114', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball QJ310 MPA FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(119, 'Bearing, Ball 6206-C-C3', '1004116', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6206-C-C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(120, 'Bearing, Ball 6206 H-2Z C3', '1004116A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6206 H-2Z C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(121, 'Bearing, #NJ317', '1004118', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #NJ317', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(122, 'Bearing, Ball #6404', '1004121', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6404', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(123, 'Bearing, Ball # 6405-A', '1004122A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball # 6405-A', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(124, 'Bearing, Ball # 3304 B-TVH', '1004124B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball # 3304 B-TVH', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(125, 'Bearing, Anglr. Cntct. #732', '1004125', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Anglr. Cntct. #732', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(126, 'Bearing, Ball 7320B.TVP', '1004125A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 7320B.TVP', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(127, 'Bearing, Ball #7221 EC3', '1004126', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #7221 EC3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(128, 'Bearing, Ball #7309 BEP SKF', '1004127', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #7309 BEP SKF', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(129, 'Bearing, Ball 7309.XL.MP.UA', '1004127A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 7309.XL.MP.UA', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(130, 'Bearing, Ball 7309B.XL.TVP', '1004129D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 7309B.XL.TVP', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(131, 'Bearing, Ball #6212 2Z/C3', '1004130', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6212 2Z/C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(132, 'Bearing, Rllr #3303B TVH FA', '1004131', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Rllr #3303B TVH FA', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(133, 'Bearing, Ball #3306 BD-XL-TVH', '1004134D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #3306 BD-XL-TVH', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(134, 'Bearing, Ball 1205C3 FAG', '1004135A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 1205C3 FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(135, 'Bearing, Roller CRL36 AMB.C3', '1004136', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller CRL36 AMB.C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(136, 'Bearing, Roller LRJ4 1/2', '1004136A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller LRJ4 1/2', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(137, 'Bearing, Ball 6208/C3 FAG', '1004140A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6208/C3 FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(138, 'Bearing,Ball #3222', '1004142', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing,Ball #3222', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(139, 'Bearing, Self-Aligning #1312', '1004144', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Self-Aligning #1312', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(140, 'Bearing, NU208 EKTVP2C3+H208', '1004145B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, NU208 EKTVP2C3+H208', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(141, 'Bearing, Roller # NU208 E-C3', '1004145C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller # NU208 E-C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(142, 'Bearing, Ball 1217K-TVH-C3 F', '1004146', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 1217K-TVH-C3 F', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(143, 'Bearing, Ball 3309 BD-TVH-L2', '1004153', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 3309 BD-TVH-L2', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(144, 'Bearing, Ball 3309BD.XL.TVH', '1004153A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 3309BD.XL.TVH', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(145, 'Bearing, Ball MRC 309 RDT', '1004156', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball MRC 309 RDT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(146, 'Bearing, Ball 3304BD.XL', '1004157', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 3304BD.XL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(147, 'Bearing, Ball #3304 BD-XL C3', '1004157B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #3304 BD-XL C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(148, 'Bearing, Ball #1218-K', '1004161', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #1218-K', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(149, 'Bearing, Ball 1218k.TVH.C3 F', '1004162', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 1218k.TVH.C3 F', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(150, 'Bearing, Ball # 6204-H-2Z C3 \"', '1004163B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball # 6204-H-2Z C3 \"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(151, 'Bearing, Ball 6207 C3 \"FAG\"', '1004164', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6207 C3 \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(152, 'Bearing, #6305/C3 \"FAG\"', '1004172', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #6305/C3 \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(153, 'Bearing, Ball #6200/C3 FAG', '1004176B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6200/C3 FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(154, 'Bearing, Ball #6022', '1004177', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6022', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(155, 'Bearing, Ball # 6204 C-C3', '1004178A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball # 6204 C-C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(156, 'Bearing, Ball 6205-C-2Z-C3', '1004180C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6205-C-2Z-C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(157, 'Bearing, Ball 6205 H-2Z/C3', '1004180D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6205 H-2Z/C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(158, 'Bearing, BAll 6407 C3 \"FAG\"', '1004181A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, BAll 6407 C3 \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(159, 'Bearing, Ball #7322', '1004182', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #7322', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(160, 'Bearing, Ball 7220B-XL-TVP', '1004186B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 7220B-XL-TVP', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(161, 'Bearing, #2305 TV C3 \"FAG\"', '1004187', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #2305 TV C3 \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(162, 'Bearing, #6308/C3 \"FAG\"', '1004188', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #6308/C3 \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(163, 'Bearing, Ball #6308 H-2Z C3', '1004188A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6308 H-2Z C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(164, 'Bearing, Ball #UCS-206', '1004189', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #UCS-206', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(165, 'Bearing, Ball 3211BD.TVH L28', '1004194A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 3211BD.TVH L28', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(166, 'Bearing, Ball 3211 A', '1004194B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 3211 A', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(167, 'Bearing, #6303/C3 Deep Groove', '1004199A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #6303/C3 Deep Groove', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(168, 'Bearing, Ball 6303 \"FAG\"', '1004199D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6303 \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(169, 'Bearing, Roller 22207-E1-XL-C3', '1004200A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller 22207-E1-XL-C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(170, 'Bearing, Ball #6305 2Z/C3\"FAG\"', '1004201', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6305 2Z/C3\"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(171, 'Bearing, ball #6305 ZR', '1004201B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, ball #6305 ZR', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(172, 'Bearing, Roller #NU2307-E-XL-T', '1004202A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #NU2307-E-XL-T', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(173, 'Bearing, Ball 1213K.TVH.C3', '1004204C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 1213K.TVH.C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(174, 'Bearing, Ball 3206 2Z/C3', '1004207', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 3206 2Z/C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(175, 'Bearing, Ball 3206BTVHC3 \"FA', '1004207A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 3206BTVHC3 \"FA', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(176, 'Bearing, Tapered #30306', '1004216', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Tapered #30306', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(177, 'Bearing, Tapered #30308', '1004217', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Tapered #30308', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(178, 'Bearing, Row Ball #1220K dbl', '1004218', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Row Ball #1220K dbl', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(179, 'Bearing, Ball #20309', '1004221', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #20309', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(180, 'Bearing, Ball #6217-ZZ', '1004222', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6217-ZZ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(181, 'Bearing, Ball #6217 2Z/C3 FAG', '1004222B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6217 2Z/C3 FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(182, 'Bearing, Ball # 7220 B-XL-MP', '1004226', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball # 7220 B-XL-MP', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(183, 'Bearing, Ball #16024', '1004227A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #16024', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(184, 'Bearing, Ball #16040', '1004228', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #16040', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(185, 'Bearing, Roller #NU217', '1004231', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #NU217', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(186, 'Bearing, Ball #7309 BECBP', '1004238', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #7309 BECBP', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(187, 'Bearing, Ball #6204 Z', '1004240', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6204 Z', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(188, 'Bearing, Ball 3312 A', '1004507B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 3312 A', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(189, 'Bearing, Ball 3308BD.XL.TVH', '1004509', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 3308BD.XL.TVH', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(190, 'Bearing, Ball 7222 BMP, FAG Ge', '1004514', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 7222 BMP, FAG Ge', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(191, 'Bearing, Ball #3208 BD-XL-TVH', '1004518', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #3208 BD-XL-TVH', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(192, 'Bearing, Ball #3208 BD-TVH-L285 C3', '1004518A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #3208 BD-TVH-L285 C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(193, 'Coupler, Grease Gun', '1005002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupler, Grease Gun', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(194, 'Bearing, Thrust Ball #51318', '1006004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Thrust Ball #51318', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(195, 'Bearing, Thrust #51103', '1006008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Thrust #51103', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(196, 'Bearing, Thrust Ball #51115', '1006010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Thrust Ball #51115', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(197, 'Bearing,Thrust # 51104', '1006028', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing,Thrust # 51104', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(198, 'Bearing, GE 240 DO-2RS-A', '1007001C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, GE 240 DO-2RS-A', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(199, 'Bearing, Roller #22226', '1007003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #22226', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(200, 'Bearing, Roller 387-A/382', '1007004A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller 387-A/382', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(201, 'Bearing, Cup & Cone #201236 A', '1007004D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Cup & Cone #201236 A', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(202, 'Bearing, Roller 30212-XL \"FAG\"', '1007005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller 30212-XL \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(203, 'Bearing, Tapered #30313', '1007006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Tapered #30313', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(204, 'Bearing, NU316ETVP2C3 \"FAG\"', '1007007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, NU316ETVP2C3 \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(205, 'Bearing, NU 317 ETVP2 C3 \"FAG\"', '1007008B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, NU 317 ETVP2 C3 \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(206, 'Bearing, Roller #NU318', '1007009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #NU318', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(207, 'Bearing, Roller #NU2330', '1007010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #NU2330', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(208, 'Bearing, NU305E.TVP2.C3 \"FAG', '1007015C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, NU305E.TVP2.C3 \"FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(209, 'Bearing, NU 312E.XL.TVP2', '1007016C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, NU 312E.XL.TVP2', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(210, 'Bearing, Rllr NU 311-E-TVP2', '1007017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Rllr NU 311-E-TVP2', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(211, 'Bearing, Roller #22211-A', '1007020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #22211-A', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(212, 'Bearing, Roller #22218 C3', '1007021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #22218 C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(213, 'Bearing, Ball #32014 FAG', '1007023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #32014 FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(214, 'Bearing, Ball #221803', '1007024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #221803', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(215, 'Bearing, Roller 22310-E1-XL-C3', '1007036C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller 22310-E1-XL-C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(216, 'Bearing, Ball # 22310 E1-C3', '1007036D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball # 22310 E1-C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(217, 'Bearing, Roller #22312', '1007037', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #22312', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(218, 'Bearing, Roller #22313E1.C3', '1007038C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #22313E1.C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(219, 'Bearing, Roller #22313 E1-XL C3', '1007038D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #22313 E1-XL C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(220, 'Bearing, #22314 E/C3 SKF', '1007039', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #22314 E/C3 SKF', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(221, 'Bearing, Roller #22215 C', '1007041', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #22215 C', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(222, 'Bearing, Roller 22215 E1-XL C3 \"FAG\"', '1007041C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller 22215 E1-XL C3 \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(223, 'Bearing, Roller #22215 E1-XL-K', '1007041D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #22215 E1-XL-K', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(224, 'Bearing,23224-E1A-K-M-C3 H23', '1007042A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing,23224-E1A-K-M-C3 H23', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(225, 'Brearing, Roller #NU220', '1007047B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brearing, Roller #NU220', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(226, 'Bearing, Roller #NUP 224', '1007048', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #NUP 224', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(227, 'Bearing, Sphl Rllr #22230ES', '1007053', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Sphl Rllr #22230ES', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(228, 'Bearing, #3019 AC3', '1007054', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #3019 AC3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(229, 'Bearing, Adapter Sleeve cmp', '1007055', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Adapter Sleeve cmp', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(230, 'Bearing, Rllr #22211-E1-XL-K-C3/H311', '1007057D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Rllr #22211-E1-XL-K-C3/H311', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(231, 'Bearing, 22216-E1-XL-K C3 FA', '1007059A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, 22216-E1-XL-K C3 FA', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(232, 'Bearing, #0216 ES C3', '1007063', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #0216 ES C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(233, 'Bearing, Tprd #K-0967/K09195', '1007065', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Tprd #K-0967/K09195', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(234, 'Bearing, Spherical Rllr 22220E', '1007068A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Spherical Rllr 22220E', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(235, 'Bearing, Tprd K-LM67048/LM60', '1007072', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Tprd K-LM67048/LM60', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(236, 'Bearing, Tprd KLM11949/LM119', '1007073', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Tprd KLM11949/LM119', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(237, 'Bearing, Timken #M86649', '1007074', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Timken #M86649', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(238, 'Bearing, Roller #22309 E.C3', '1007075B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #22309 E.C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(239, 'Bearing, Roller #21309 E C3', '1007077', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #21309 E C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(240, 'Bearing, Timken #25580/25520', '1007078', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Timken #25580/25520', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(241, 'Bearing, Adptr Slv#NU208 E2', '1007082', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Adptr Slv#NU208 E2', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(242, 'Bearing, Roller #23220', '1007083', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #23220', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(243, 'Bearing, Roller #22228 E1-XL-K', '1007084', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #22228 E1-XL-K', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(244, 'Bearing, Sphrl Rllr #2221', '1007086', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Sphrl Rllr #2221', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(245, 'Bearing, Roller #NU322', '1007087', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #NU322', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(246, 'Bearing, #NU322 ETVP2 C3 \"FA', '1007087A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #NU322 ETVP2 C3 \"FA', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(247, 'Bearing, Roller #30319 comp', '1007088', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #30319 comp', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(248, 'Bearing, Roller # NU2218 E-XL-', '1007089A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller # NU2218 E-XL-', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(249, 'Bearing,2215-K-TVH-C3+H315 F', '1007091A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing,2215-K-TVH-C3+H315 F', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(250, 'Bearing, Roller NU2215 E-XL-M1 C3', '1007091B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller NU2215 E-XL-M1 C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(251, 'Bearing, NU2218 ETVP2C3 \"FAG', '1007092A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, NU2218 ETVP2C3 \"FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(252, 'Bearing, Taprd Rllr 32212 J2', '1007093', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Taprd Rllr 32212 J2', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(253, 'Bearing, #32319 A \"FAG\"', '1007093A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #32319 A \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(254, 'Bearing,Rller32213 VKHB2709', '1007094A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing,Rller32213 VKHB2709', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(255, 'Bearing, Spherical Rollr Cm', '1007097', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Spherical Rollr Cm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(256, 'Bearing, NU314 ETVP2C3 FAG', '1007097A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, NU314 ETVP2C3 FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(257, 'Bearing, Roller #NU314', '1007098', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #NU314', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(258, 'Bearing, Roller #32312', '1007101', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #32312', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(259, 'Bearing, Roller Cylindrical #N313 E-XL-M1', '1007102B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller Cylindrical #N313 E-XL-M1', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(260, 'Bearing,Rllr NU313-E-XL-TVP2C3', '1007103D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing,Rllr NU313-E-XL-TVP2C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(261, 'Bearing, NU320 ETVP2C3 \"FAG\"', '1007104E', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, NU320 ETVP2C3 \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(262, 'Bearing, Rllr NU1022MI C3 FAG', '1007108A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Rllr NU1022MI C3 FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(263, 'Bearing, Ball #30216 C3', '1007111', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #30216 C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(264, 'Bearing, 30215 \"Timken\"', '1007111A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, 30215 \"Timken\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(265, 'Bearing, Roller23138 E1A-M-C', '1007113B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller23138 E1A-M-C', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(266, 'Bearing, N305 ETVP2C3 \"FAG\"', '1007115A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, N305 ETVP2C3 \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(267, 'Bearing, Roller #32310', '1007117', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #32310', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(268, 'Bearing, Roller #33211', '1007118', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #33211', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(269, 'Bearing, Roller #221', '1007119', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #221', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(270, 'Bearing, Roller #NU212', '1007119A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #NU212', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(271, 'Bearing, Roller #32308', '1007122', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #32308', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(272, 'Bearing, Roller #NU2313', '1007125', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #NU2313', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(273, 'Bearing, Cylr\'l Roller #NJ4', '1007126', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Cylr\'l Roller #NJ4', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(274, 'Bearing, Roller #NU2211', '1007129', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #NU2211', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(275, 'Bearing, Roller 30216A \"FAG\"', '1007130', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller 30216A \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(276, 'Bearing, Roller #30219', '1007131', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #30219', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(277, 'Bearing, Roller #30217A', '1007132', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #30217A', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(278, 'Bearing, Roller #NU 418', '1007133', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #NU 418', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(279, 'Bearing, Roller #NU 2213', '1007134', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #NU 2213', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(280, 'Bearing, Roller Tapered 302', '1007137', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller Tapered 302', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(281, 'Bearing, Roller #NU221 (SR)', '1007141', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #NU221 (SR)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(282, 'Bearing, Cyld\'l Roller NU21', '1007142', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Cyld\'l Roller NU21', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(283, 'Bearing, Roller NU2210-E-XL-TV', '1007146', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller NU2210-E-XL-TV', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(284, 'Bearing, Roller Cone A 4059', '1007148', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller Cone A 4059', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(285, 'Bearing, Sphrl Rllr #22213', '1007155', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Sphrl Rllr #22213', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25');
INSERT INTO `items` (`id`, `item_name`, `item_code`, `item_type`, `item_vendor_id`, `item_uom_id`, `item_reorder_point`, `item_category_id`, `item_quantity`, `item_sales_description`, `item_purchase_description`, `item_selling_price`, `item_cost_price`, `item_cogs_account_id`, `item_income_account_id`, `item_asset_account_id`, `created_at`) VALUES
(286, 'Bearing, 23048 MB.C3 FAG', '1007158A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, 23048 MB.C3 FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(287, 'Bearing, 23030E1A.M.C3 FAG', '1007159', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, 23030E1A.M.C3 FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(288, 'Bearing, Ball #21308', '1007160A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #21308', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(289, 'Bearing, 23984MBC3 \"FAG\"', '1007162', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, 23984MBC3 \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(290, 'Bearing, Ball #5204', '1007167', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #5204', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(291, 'Bearing, Roller 23040 E1A.M.C3 FAG', '1007168B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller 23040 E1A.M.C3 FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(292, 'Bearing, #23222 E1A-XL-K-M', '1007173D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #23222 E1A-XL-K-M', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(293, 'Bearing, Roller 21314E1.XL.C3', '1007174', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller 21314E1.XL.C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(294, 'Bearing, 23222 E1A-K-M C3+H2322', '1007175', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, 23222 E1A-K-M C3+H2322', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(295, 'Bearing, Ball #7308B.XL.TVP.UA', '1007176', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #7308B.XL.TVP.UA', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(296, 'Bearing, 23222-E1A-XL-K-M C3', '1007178', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, 23222-E1A-XL-K-M C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(297, 'Bearing, Cross U-joint 35R', '1007180', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Cross U-joint 35R', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(298, 'Bearing, Tapered Roller #53176', '1007181', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Tapered Roller #53176', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(299, 'Bearing, Cup #53375', '1007181A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Cup #53375', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(300, 'Bearing, Roller NU 315 E-XL-TV', '1007182', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller NU 315 E-XL-TV', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(301, 'Bearing, #30209 A', '1007183A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #30209 A', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(302, 'Bearing,UCP 209-28 Plummer B', '1008006A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing,UCP 209-28 Plummer B', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(303, 'Bearing, UC211-2\" FAG', '1008008B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, UC211-2\" FAG', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(304, 'Y-Bearing #YAR 213-208-2F', '1008010A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Y-Bearing #YAR 213-208-2F', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(305, 'Bearing-Y, YAR 214-2F \"SKF\"', '1008011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing-Y, YAR 214-2F \"SKF\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(306, 'Bearing, UC-215-48', '1008012A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, UC-215-48', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(307, 'Bearing-Y, UC215', '1008012B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing-Y, UC215', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(308, 'Bearing, Cyl.Rllr#NJ214E TVP', '1009007B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Cyl.Rllr#NJ214E TVP', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(309, 'Bearing, Flange #SY60', '1009015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Flange #SY60', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(310, 'Flange Brg.w/Square Housing', '1009029', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Flange Brg.w/Square Housing', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(311, 'Bearing-Y, UCFCX-13-40', '1009036', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing-Y, UCFCX-13-40', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(312, 'Bearing-Y, UCP 210 \"FAG\"', '1009037', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing-Y, UCP 210 \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(313, 'Bearing, Y, # UCF 213', '1009039', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Y, # UCF 213', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(314, 'Bearing, Roller #NJ221 E-M1-C3', '1009111B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #NJ221 E-M1-C3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(315, 'Bearing, Needle #573', '1013003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Needle #573', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(316, 'Bearing, Needle #HK2216', '1013004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Needle #HK2216', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(317, 'Bearing, Needle #HR-2020', '1013005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Needle #HR-2020', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(318, 'Bearing, Needle HK 2020', '1013005A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Needle HK 2020', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(319, 'Bearing, Needle HK 2030', '1013006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Needle HK 2030', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(320, 'Bearing, Plmmr Block Hsg 52', '1014004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Plmmr Block Hsg 52', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(321, 'Bearing, Adapter Sleeve #H2', '1014008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Adapter Sleeve #H2', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(322, 'Bearing, Adapter Slve #H-31', '1014009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Adapter Slve #H-31', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(323, 'Bearing, Adtr Slve #H3', '1014011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Adtr Slve #H3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(324, 'Bearng,BlockPlummrHousing SES511-609-L/EDH511', '1014017A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearng,BlockPlummrHousing SES511-609-L/EDH511', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(325, 'Bearing, Plmmr Block w/roll', '1014019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Plmmr Block w/roll', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(326, 'Bearing, Flange', '1014021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Flange', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(327, 'Bearing, Pllw Blck #UKT-211-', '1014022', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Pllw Blck #UKT-211-', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(328, 'Bearing, Pillow BlockUCP210-', '1014028', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Pillow BlockUCP210-', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(329, 'Bearing, UC210 \"FAG\"', '1014031', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, UC210 \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(330, 'Bearing, Y #UC213-40 \"PKH\"', '1014032', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Y #UC213-40 \"PKH\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(331, 'Bearing, Ball 4305 B.TVH \"FA', '1014035', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 4305 B.TVH \"FA', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(332, 'Bearing, Y #YAR206-104', '1014036', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Y #YAR206-104', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(333, 'Bearing, #30208', '1014045', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #30208', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(334, 'Bearing, #30208 XL', '1014045B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #30208 XL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(335, 'Bearing, #UC-204', '1014048', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #UC-204', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(336, 'Bearing, UC 204 \"FAG\"', '1014048A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, UC 204 \"FAG\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(337, 'Bearing, Center -Vac Pan', '1014056', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Center -Vac Pan', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(338, 'Bearing, Y Flange UCFX-13-40 (', '1014075A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Y Flange UCFX-13-40 (', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(339, 'Belt-V, B-131', '1016003A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, B-131', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(340, 'Belt, BucktRawSugar(EP250)457x50x12mmTx5p 18\"', '1016004A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt, BucktRawSugar(EP250)457x50x12mmTx5p 18\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(341, 'Belt-V, Z-26', '1016005B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, Z-26', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(342, 'Belt-V, #C-196 (2pcs./set)', '1016007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, #C-196 (2pcs./set)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(343, 'Belt-V, A-85 (3pcs./set)', '1016010A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, A-85 (3pcs./set)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(344, 'Belt-V, C-120 @ 4pcs./set', '1016014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, C-120 @ 4pcs./set', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(345, 'Belt-V, #3V 1180', '1016015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, #3V 1180', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(346, 'Belt-V, SPZ3000-3V1180 6pcs.', '1016015A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, SPZ3000-3V1180 6pcs.', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(347, 'Belt-V, #A-112 (4pcs./set)', '1016016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, #A-112 (4pcs./set)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(348, 'Belt-V, SPZ1420-3V560 6pcs./ma', '1016019B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, SPZ1420-3V560 6pcs./ma', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(349, 'Belt-V, #C-85 (4pcs.Match/set)', '1016020A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, #C-85 (4pcs.Match/set)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(350, 'Belt-V, #C112  (3 pcs./Set)', '1016021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, #C112  (3 pcs./Set)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(351, 'Belt-V, B-85 @ 3pcs./mtch/set', '1016025', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, B-85 @ 3pcs./mtch/set', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(352, 'Fastener,Conveyor Belt 1-1/2', '1016029', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fastener,Conveyor Belt 1-1/2', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(353, 'Belt, Drive P/N 22189054', '1016030', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt, Drive P/N 22189054', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(354, 'Belt-V, #C104', '1016034', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, #C104', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(355, 'Belt-V, #B90 (3pcs./set)', '1016039', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, #B90 (3pcs./set)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(356, 'Belt-V, #C90 (3pcs/set)', '1016040', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, #C90 (3pcs/set)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(357, 'Belt-V, C#109 (3pcs/Set)', '1016042', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, C#109 (3pcs/Set)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(358, 'Belt-V, #B-124 (5pcs/Set)', '1016043', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, #B-124 (5pcs/Set)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(359, 'Belt-V, B-124 (6pcs/set)', '1016043B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, B-124 (6pcs/set)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(360, 'Belt-V, C-108 @ 4pcs.match/set', '1016046', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, C-108 @ 4pcs.match/set', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(361, 'Belt-V, B-47 (2pcs/set)', '1016049A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, B-47 (2pcs/set)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(362, 'Belt-V, C-158 @ 5pcs match/set', '1016051', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, C-158 @ 5pcs match/set', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(363, 'Belt-V, C-158', '1016051B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, C-158', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(364, 'Belt-V, B-58 (4pcs./set)', '1016054', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, B-58 (4pcs./set)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(365, 'Belt-V, B-56 (4pcs./set)', '1016057', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, B-56 (4pcs./set)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(366, 'Belt-V, B-36 @ 3pcs.Match/Set', '1016058A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, B-36 @ 3pcs.Match/Set', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(367, 'Belt-V, #SPC-5000 (5pcs./set', '1016064', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, #SPC-5000 (5pcs./set', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(368, 'Belt,w/Bucket Att\'mt.Fitting', '1016068', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt,w/Bucket Att\'mt.Fitting', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(369, 'Belt-V, #SPC-3000 (5pcs/Set)', '1016069', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, #SPC-3000 (5pcs/Set)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(370, 'Belt-V, #C-185 @ 5 pcs./set', '1016093', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, #C-185 @ 5 pcs./set', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(371, 'Belt-V, A-47 @ 2pcs./set', '1016099', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, A-47 @ 2pcs./set', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(372, 'Belt-V, B-82 @ 4pcs./Match/set', '1016102A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, B-82 @ 4pcs./Match/set', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(373, 'Belt-V, #SPZ850', '1016106', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, #SPZ850', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(374, 'Belt-V,SPZ 850 @2pcs.match/set', '1016106A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V,SPZ 850 @2pcs.match/set', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(375, 'Belt-V, HC #SPZ1337 (3pcs/se', '1016108', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, HC #SPZ1337 (3pcs/se', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(376, 'Belt-V, #3V x 630 @4pcs./set', '1016110', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, #3V x 630 @4pcs./set', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(377, 'Belt-V, B-148 (5pcs./set)', '1016114', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, B-148 (5pcs./set)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(378, 'Belt, Variable Speed8x23x600mm', '1016116', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt, Variable Speed8x23x600mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(379, 'Belt-V, SPC 2800 @3pcs.match/s', '1016117', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, SPC 2800 @3pcs.match/s', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(380, 'Belt, Conveyor 600mmWx10mmTx10', '1016120', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt, Conveyor 600mmWx10mmTx10', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(381, 'Belt-V, #63 PN-39158324', '1016121B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, #63 PN-39158324', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(382, 'V-Belt# 63,Part#39158340 @ 4pcs.set', '1016122', 'Inventory', NULL, NULL, NULL, NULL, 0, 'V-Belt# 63,Part#39158340 @ 4pcs.set', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(383, 'Belt-V, XPZ 1520/3VX600 @ 4pcs./set', '1016123', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, XPZ 1520/3VX600 @ 4pcs./set', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(384, 'Bit, Tool Carbide 1\"sq.x6\"L', '1017001A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bit, Tool Carbide 1\"sq.x6\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(385, 'Bit, Tool Assab 1\"sq.x6\"L', '1017001B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bit, Tool Assab 1\"sq.x6\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(386, 'Bit, Tool Assab 3/8\"sq.x3\"L', '1017002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bit, Tool Assab 3/8\"sq.x3\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(387, 'Bit, Tool Assab1/4\"sq.x2-1/2\"L', '1017003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bit, Tool Assab1/4\"sq.x2-1/2\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(388, 'Bit, Tool Assab 1/2\"sq.x4\"L', '1017004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bit, Tool Assab 1/2\"sq.x4\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(389, 'Bit, Tool Assab 7/16\"sq.x3½\"L', '1017005A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bit, Tool Assab 7/16\"sq.x3½\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(390, 'Bit, Tool 1\"sq.x7\"L Carbide', '1017006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bit, Tool 1\"sq.x7\"L Carbide', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(391, 'Bit, Tool ASSAB 1\"sq. x 8\"L', '1017006A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bit, Tool ASSAB 1\"sq. x 8\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(392, 'Bit, Tool \"ASSAB 17\" 1\"sqx7\"L', '1017006D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bit, Tool \"ASSAB 17\" 1\"sqx7\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(393, 'Bit,Tool Carbide 3/4\"sq.x4\"', '1017007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bit,Tool Carbide 3/4\"sq.x4\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(394, 'Bit, Tool Assab 5/16\"sq.x6\"L', '1017008A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bit, Tool Assab 5/16\"sq.x6\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(395, 'Bit, Tool Assab 5/16\"sq.x4\"L', '1017008B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bit, Tool Assab 5/16\"sq.x4\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(396, 'Bit, Tool 3/4\"x5\"L', '1017009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bit, Tool 3/4\"x5\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(397, 'Bit, Tool Carbide 1/4\"sq.x2½', '1017010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bit, Tool Carbide 1/4\"sq.x2½', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(398, 'Bit, Tool CARBIDE 3/8\"sq.x2½\"L', '1017011A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bit, Tool CARBIDE 3/8\"sq.x2½\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(399, 'Bit, Tool Carbide 7/16 x 3½', '1017012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bit, Tool Carbide 7/16 x 3½', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(400, 'Bit, Tool Carbide 1/2\"sq.x3½', '1017013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bit, Tool Carbide 1/2\"sq.x3½', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(401, 'Blade,Hi-SpeedCutoff7x7/8x1/8\"', '1017014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Blade,Hi-SpeedCutoff7x7/8x1/8\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(402, 'Bit, Tool Carbide 5/16\"sqx2½', '1017015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bit, Tool Carbide 5/16\"sqx2½', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(403, 'Bit, Tool ASSAB 5/16\"sq.x2½\"', '1017015A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bit, Tool ASSAB 5/16\"sq.x2½\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(404, 'Bit, Tool 5/16\"sq.x8\"L', '1017018A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bit, Tool 5/16\"sq.x8\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(405, 'Bit, Tool Carbide 5/8\"sq.x4\"', '1017020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bit, Tool Carbide 5/8\"sq.x4\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(406, 'Bit, Drill 8mmφ', '1017028', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bit, Drill 8mmφ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(407, 'Bit, Drill 6mmφ', '1017060', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bit, Drill 6mmφ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(408, 'File, Triangular 4\"', '1017073', 'Inventory', NULL, NULL, NULL, NULL, 0, 'File, Triangular 4\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(409, 'Blade, Wiper 342mmLx4mmx17mm', '1017075A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Blade, Wiper 342mmLx4mmx17mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(410, 'File, Flat 1\"Wx12\"L', '1017077A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'File, Flat 1\"Wx12\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(411, 'Chuck, Drive ½\"φx12\"', '1017084', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Chuck, Drive ½\"φx12\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(412, 'File, Round 3/16\"φ', '1017087B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'File, Round 3/16\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(413, 'Bolt, Hex Hd w/nut 3/8\"x3/4\"', '1018006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 3/8\"x3/4\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(414, 'Bolt, Hex Hd w/nut 1/4\"φx1\"L GD', '1018008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 1/4\"φx1\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(415, 'Bolt, Hex Hd w/nt 1/4\"x1½\"L GD', '1018009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nt 1/4\"x1½\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(416, 'Bolt, Hex Hd w/nut 1/4\"x2\"L GD', '1018010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 1/4\"x2\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(417, 'Bolt,Flathead w/nut 5/8\"x2\"', '1018011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt,Flathead w/nut 5/8\"x2\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(418, 'Bolt, Hex Hd GDw/nut5/16\"x3/', '1018012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd GDw/nut5/16\"x3/', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(419, 'Bolt, Hex Hd w/nt 5/16\"x1\"L GD', '1018013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nt 5/16\"x1\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(420, 'Bolt, Hex Hd w/nt 5/16\"x1½\" GD', '1018014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nt 5/16\"x1½\" GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(421, 'Bolt, Hex Hd w/nt 5/16\"x2\"L GD', '1018016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nt 5/16\"x2\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(422, 'Bolt,Hex Hd w/nt 5/16\"x2½\"L GD', '1018017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt,Hex Hd w/nt 5/16\"x2½\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(423, 'Bolt, Hex Hd w/nut 3/8\"φx1\" GD', '1018018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 3/8\"φx1\" GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(424, 'Bolt, Hex Hd w/nut 3/8\"φx1½\"L GD', '1018019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 3/8\"φx1½\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(425, 'Bolt, Hex Hd w/nt 3/8\"φx2\"L GD', '1018020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nt 3/8\"φx2\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(426, 'Bolt, Hex Hd 3/8\"x2½\"L GD', '1018021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd 3/8\"x2½\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(427, 'Bolt,Hex Hd w/nt 7/16\"φx1\"L GD', '1018022', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt,Hex Hd w/nt 7/16\"φx1\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(428, 'Bolt, Hex Hd w/nut 7/16\"x2 GD', '1018024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 7/16\"x2 GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(429, 'Bolt, Hex Hd w/nut 1/2\"x1\"L GD', '1018025', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 1/2\"x1\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(430, 'Bolt, Hex Hd w/nt 1/2\"x1½\"L GD', '1018026', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nt 1/2\"x1½\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(431, 'Bolt, Hex Hd w/nt 7/16\"x1½\" GD', '1018027', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nt 7/16\"x1½\" GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(432, 'Bolt, Hex Hd 1/2\"x2½\"L GD', '1018029', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd 1/2\"x2½\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(433, 'Bolt, Hex. Hd w/nut ½\"φx2½\"L', '1018029A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex. Hd w/nut ½\"φx2½\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(434, 'Bolt, Hex. Head w/nut SS 5/8x5', '1018032', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex. Head w/nut SS 5/8x5', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(435, 'Bolt, Hex. Head w/nut SS 5/8\"φ', '1018032A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex. Head w/nut SS 5/8\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(436, 'Bolt, Hex Hd w/nut 9/16\"x1\"L GD', '1018033', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 9/16\"x1\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(437, 'Bolt, Hex Hd w/nut 5/8\"φx3\"L HT GD', '1018037', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 5/8\"φx3\"L HT GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(438, 'Bolt,Hex Hd w/nt 5/8x3½L HT GD', '1018039', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt,Hex Hd w/nt 5/8x3½L HT GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(439, 'Bolt, Hex HD w/nut 5/8x3½L GD', '1018039A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex HD w/nut 5/8x3½L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(440, 'Bolt, Hex Hd w/nut 1/2\"φx3\"L HT GD', '1018041', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 1/2\"φx3\"L HT GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(441, 'Bolt, Hex. GD 1/2\"φx4\"L w/ n', '1018042', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex. GD 1/2\"φx4\"L w/ n', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(442, 'Bolt, Hex. Head w/nut 5/8\"φx9\"', '1018045', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex. Head w/nut 5/8\"φx9\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(443, 'Bolt, Hex. Head w/nut 5/8\"φx11', '1018047', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex. Head w/nut 5/8\"φx11', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(444, 'Bolt, Hex Hd w/nut 3/4\"x1\"L GD', '1018048', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 3/4\"x1\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(445, 'Bolt, Capscrew 3/4\"x2\"L', '1018050', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Capscrew 3/4\"x2\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(446, 'Bolt, Hex Hd 3/4\"φ x 3½\"L GD', '1018053', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd 3/4\"φ x 3½\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(447, 'Bolt, Hex Hd w/nut 3/4\"φ x 3-1/2\"L', '1018053A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 3/4\"φ x 3-1/2\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(448, 'Bolt, HexHd w/nt 7/16\"x2½\"L GD', '1018055', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, HexHd w/nt 7/16\"x2½\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(449, 'Bolt, Hex Hd w/nut 3/4\"φx3\" HT GD', '1018061', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 3/4\"φx3\" HT GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(450, 'Bolt, Hex Hd 5/8\"x1\"L GD', '1018068', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd 5/8\"x1\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(451, 'Bolt, Flanges Stud w/ 2 Nut', '1018069', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Flanges Stud w/ 2 Nut', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(452, 'Bolt, Hex Hd w/nt 12mmx25mm GD', '1018081', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nt 12mmx25mm GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(453, 'Bolts, Cane knives w/nuts 33mm', '1018082A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolts, Cane knives w/nuts 33mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(454, 'Bolt, Hex Hd 18mmx35mmL', '1018085', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd 18mmx35mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(455, 'Bolt, Hex Hd w/nut 18mmφx50mm GD', '1018088', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 18mmφx50mm GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(456, 'Bolt, Hex HD w/out nut 5/8x7\"', '1018089A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex HD w/out nut 5/8x7\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(457, 'Bolt, Hex Hd 20mmφ x 50mmL GD', '1018091', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd 20mmφ x 50mmL GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(458, 'Bolt, Hex Hd w/nut 20mmx40 GD', '1018092', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 20mmx40 GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(459, 'Bolt, Hex Hd w/nut 20mmφx75mm GD', '1018094', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 20mmφx75mm GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(460, 'Bolt, HexHead GD3/4\"x7\"L w/nut', '1018098', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, HexHead GD3/4\"x7\"L w/nut', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(461, 'Bolt, Hex. HD. w/nut GD 3/4\"φx7\"L', '1018098B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex. HD. w/nut GD 3/4\"φx7\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(462, 'Bolt, Hex Hd 20mm x 100mmL GD', '1018100', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd 20mm x 100mmL GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(463, 'Bolt,Hex Hd w/nut 20mmx100mmL', '1018100A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt,Hex Hd w/nut 20mmx100mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(464, 'Bolt, Hex Head 20mmφx110mm GD', '1018101', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Head 20mmφx110mm GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(465, 'Bolt, Stud GD 3/4\"φx1½\"L', '1018103', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Stud GD 3/4\"φx1½\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(466, 'Bolt-U, 3/4\" w/ nut', '1018108', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt-U, 3/4\" w/ nut', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(467, 'Bolt, Hex Hd 3/4\" x 2½\"L GD', '1018109', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd 3/4\" x 2½\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(468, 'Bolt, Hex HD w/nut 3/4\"φx2-1/2\"L GD', '1018109A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex HD w/nut 3/4\"φx2-1/2\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(469, 'Bolt, Carriage 1/2\"x2\"L NC', '1018110', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Carriage 1/2\"x2\"L NC', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(470, 'Bolt-U, SS 1/2\"φx2½\"H', '1018112', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt-U, SS 1/2\"φx2½\"H', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(471, 'Bolt,Screw No.4 x ½in.', '1018118', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt,Screw No.4 x ½in.', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(472, 'Bolt, Hex Hd w/nut 1/2\"φx2 GD', '1018121', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 1/2\"φx2 GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(473, 'Bolt, Hex Hd w/nut 5/8\"φx2\"L GD', '1018122', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 5/8\"φx2\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(474, 'Bolt, Hex Hd 5/8\"φ x 2½\"L GD', '1018124', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd 5/8\"φ x 2½\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(475, 'Bolt, Hex Hd w/nut 3/8\"x3\"L GD', '1018133', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 3/8\"x3\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(476, 'Bolt, Hex Hd 5\"x16\"x3\"L', '1018137', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd 5\"x16\"x3\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(477, 'Bolt, Hex Hd 5/16\"x2\"L', '1018138', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd 5/16\"x2\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(478, 'Bolt, Hex Hd 5/16\"x4\"L', '1018139', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd 5/16\"x4\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(479, 'Bolt, Hex Hd 1/4\" x 2½\"L GD', '1018141', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd 1/4\" x 2½\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(480, 'Bolt, Hex Hd 1/4\"x', '1018143', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd 1/4\"x', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(481, 'Bolt, Hex Hd 7/16\"x3\"', '1018149', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd 7/16\"x3\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(482, 'Bolt, Hex Hd 7/16\"x3½\"', '1018150', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd 7/16\"x3½\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(483, 'Bolt, Hex Hd 7/16\"x4\"L', '1018151', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd 7/16\"x4\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(484, 'Bolt, Hex Hd w/nt 9/16\"x2\"L GD', '1018161', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nt 9/16\"x2\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(485, 'Bolt,Flat Hd 1/2φ\"x4-1/2\"', '1018163A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt,Flat Hd 1/2φ\"x4-1/2\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(486, 'Bolt, Hex Hd w/nut 9/16\"x3\"L', '1018164', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 9/16\"x3\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(487, 'Bolt, Hex Hd 9/16\" x 2½\"L GD', '1018166', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd 9/16\" x 2½\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(488, 'Bolt,Hex HD 9/16\"x2½\"L GDw/nut', '1018166A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt,Hex HD 9/16\"x2½\"L GDw/nut', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(489, 'Bolt, Sltd Flat HD 8mmx60mL', '1018168', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Sltd Flat HD 8mmx60mL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(490, 'Bolt, Flat Hd w/nut ¼\"φx1½\"L', '1018169', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Flat Hd w/nut ¼\"φx1½\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(491, 'Bolt, FlatHead w/nut 3/4\"x7½', '1018173', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, FlatHead w/nut 3/4\"x7½', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(492, 'Bolt, Flathead w/nut 3/4\"φx7-1/2\"L', '1018173A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Flathead w/nut 3/4\"φx7-1/2\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(493, 'Bolt, Hex Hd 9/16\"x1½\"L NC', '1018177', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd 9/16\"x1½\"L NC', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(494, 'Bolt, Hex Hd GD 5/8\"x4½\"L', '1018182', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd GD 5/8\"x4½\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(495, 'Bolt, Carriage  1/2\"', '1018194', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Carriage  1/2\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(496, 'Bolt,HexHd w/nt 5/8x1-1/2\"L GD', '1018200', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt,HexHd w/nt 5/8x1-1/2\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(497, 'Bolt, Hex Hd 3/4\" x 1½\"L GD', '1018203', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd 3/4\" x 1½\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(498, 'Bolt, Carriage 1/2\"x1½\"L', '1018204', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Carriage 1/2\"x1½\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(499, 'Bolt, Hex Hd w/nut 3/4\"x2\"L GD', '1018214', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 3/4\"x2\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(500, 'Bolt, Carriage 3/8\"x3½\"L', '1018217', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Carriage 3/8\"x3½\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(501, 'Bolt, Stud 12mmx35mmL', '1018231', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Stud 12mmx35mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(502, 'Bolt, Hex Hd w/nut 16mmx50mmL GD', '1018232', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 16mmx50mmL GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(503, 'Bolt, Hex Hd 20mmx65mmL GD', '1018234', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd 20mmx65mmL GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(504, 'Bolt, Stud 5/8\"φx8½\"L w/ nut', '1018237A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Stud 5/8\"φx8½\"L w/ nut', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(505, 'Bolt, Stud 14mmx55mmL', '1018242', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Stud 14mmx55mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(506, 'Bolt, Stud w/nut 1/2\"x2½\"L', '1018263', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Stud w/nut 1/2\"x2½\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(507, 'Bolt, Hex Hd w/nt 18mmx65mm GD', '1018273', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nt 18mmx65mm GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(508, 'Bolt, Hex Hd w/nut 18mmx75mm GD', '1018273A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 18mmx75mm GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(509, 'Bolt, Hex HD GD 14mmx35mmL', '1018279', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex HD GD 14mmx35mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(510, 'Bolt,Hex Hd w/nt 3/4\"x4L HT GD', '1018309', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt,Hex Hd w/nt 3/4\"x4L HT GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(511, 'Bolt, Hex Hd w/nut 3/4\"φx4\"L FT GD', '1018309A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 3/4\"φx4\"L FT GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(512, 'Bolt, Hex Hd w/nut 1/2\"φx3½\"L GD', '1018310', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 1/2\"φx3½\"L GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(513, 'Bolt, Hex Hd w/nut 1/2\"φx3-1/2\"L FT GD', '1018310A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 1/2\"φx3-1/2\"L FT GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(514, 'Bolt, Hex Hd 14mmx25mmL', '1018322', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd 14mmx25mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(515, 'Bolt, Hex Hd 6mmx115mmL', '1018331', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd 6mmx115mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(516, 'Bolt,Hex Hd w/nt 5/8\"x4L HT GD', '1018340', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt,Hex Hd w/nt 5/8\"x4L HT GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(517, 'Bolt,HexHd w/nt3/4\"x4½\"L HT GD', '1018344', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt,HexHd w/nt3/4\"x4½\"L HT GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(518, 'Bolt, Expansion 1/2\"φx2\"L', '1018352', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Expansion 1/2\"φx2\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(519, 'Bolt, w/nut & Washer SS 5/8\"φx', '1018357A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, w/nut & Washer SS 5/8\"φx', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(520, 'Bolt, w/nut 3/4\"φx1-3/4\" GD', '1018358', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, w/nut 3/4\"φx1-3/4\" GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(521, 'Bolt, Stud M16x35', '1018367', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Stud M16x35', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(522, 'Bolt, w/nut 1-1/8\"x4-1/2\"', '1018369A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, w/nut 1-1/8\"x4-1/2\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(523, 'Bolt, SS Allen Socket 6mmx10mm', '1018370A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, SS Allen Socket 6mmx10mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(524, 'Blade, Hacksaw 1\"W x 14\"L', '1019001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Blade, Hacksaw 1\"W x 14\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(525, 'Blade, Hacksaw 1-1/4\"x14\"L', '1019001B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Blade, Hacksaw 1-1/4\"x14\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(526, 'Blade, Hacksaw 1/2\"W x 12\"L', '1019002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Blade, Hacksaw 1/2\"W x 12\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(527, 'Blade, Wheel Blended', '1019003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Blade, Wheel Blended', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(528, 'Blades, N1900B 220V 580W', '1019004A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Blades, N1900B 220V 580W', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(529, 'Blade, Cutter 14\"φx1\"boreφ', '1019005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Blade, Cutter 14\"φx1\"boreφ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(530, 'Brake, Assy - H.G. Basket', '1021002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brake, Assy - H.G. Basket', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(531, 'Brake, Ring conical type', '1021004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brake, Ring conical type', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(532, 'Brake, NM336R2', '1021007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brake, NM336R2', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(533, 'Cyl., BrakeCoolingPN 20020-1', '1022001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cyl., BrakeCoolingPN 20020-1', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(534, 'Cylinder, Cooling Fan Air PN-2', '1022001A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cylinder, Cooling Fan Air PN-2', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(535, 'Bladder, Rubber 230ODx1440OAL', '1023002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bladder, Rubber 230ODx1440OAL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(536, 'Bronze, Tiger 1/2\"', '1025001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bronze, Tiger 1/2\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(537, 'Bronze, Tiger 1/2\"x 13\"', '1025022', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bronze, Tiger 1/2\"x 13\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(538, 'Brush, Steel w/wooden handle', '1026003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush, Steel w/wooden handle', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(539, 'Brush, Steel', '1026003A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush, Steel', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(540, 'Brush, Spiral 4\"x6\"L w/ handle', '1026007A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush, Spiral 4\"x6\"L w/ handle', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(541, 'Brushes, Pipe Cleaning 15mm', '1026010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brushes, Pipe Cleaning 15mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(542, 'Brushes, Pipe Cleaning 22mm', '1026011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brushes, Pipe Cleaning 22mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(543, 'Brush, Spiral 2\"x6\"Lw/o handle', '1026013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush, Spiral 2\"x6\"Lw/o handle', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(544, 'Brush, Filter Rgultr 22870-0', '1026015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush, Filter Rgultr 22870-0', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(545, 'Brush cup #110 PN79435-9', '1026019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush cup #110 PN79435-9', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(546, 'Buffer, Coupling Rubber 18mmx38mmx6.5/9.5mmT', '1027003A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Buffer, Coupling Rubber 18mmx38mmx6.5/9.5mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(547, 'Buffer, Coupling Rubber', '1027008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Buffer, Coupling Rubber', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(548, 'Buffer, Rubber26.30x57.30x11.5', '1027014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Buffer, Rubber26.30x57.30x11.5', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(549, 'Spring, Rubber 100mmx55mmH', '1027015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Rubber 100mmx55mmH', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(550, 'Buffer, Rubber Coupling', '1027017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Buffer, Rubber Coupling', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(551, 'Buffer, Rbbr Cplng 26x42/62x64', '1027020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Buffer, Rbbr Cplng 26x42/62x64', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(552, 'Buffer, Rubber Coupling 81x46x23', '1027024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Buffer, Rubber Coupling 81x46x23', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(553, 'Buffer, Rbbr Cplng 40x50/70x', '1027025', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Buffer, Rbbr Cplng 40x50/70x', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(554, 'Buffer, Rbbr. 50mmx26mmx12.9', '1027027', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Buffer, Rbbr. 50mmx26mmx12.9', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(555, 'Buffer, Rubber 96x45.20x19.70', '1027033', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Buffer, Rubber 96x45.20x19.70', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(556, 'Buffer, Coupling Rbbr 58/70m', '1027034', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Buffer, Coupling Rbbr 58/70m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(557, 'Buffer, Rubber H-Type 30mmHx31.5mmTx40/36mmW', '1027038', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Buffer, Rubber H-Type 30mmHx31.5mmTx40/36mmW', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(558, 'Buffer, RbberCouplngPumpStab 64.4IDx110ODx56W', '1027039', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Buffer, RbberCouplngPumpStab 64.4IDx110ODx56W', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(559, 'Bushing, Bronze 25x50x90', '1029001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Bronze 25x50x90', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(560, 'Bushing, Rubber 25x32.8x20mmH', '1029002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Rubber 25x32.8x20mmH', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(561, 'Bushing, Bronze 74x120x140', '1029003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Bronze 74x120x140', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(562, 'Sleeve, Hollow SS SS304', '1029005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sleeve, Hollow SS SS304', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(563, 'Sleeves,SSHollow 75x95x210', '1029006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sleeves,SSHollow 75x95x210', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25');
INSERT INTO `items` (`id`, `item_name`, `item_code`, `item_type`, `item_vendor_id`, `item_uom_id`, `item_reorder_point`, `item_category_id`, `item_quantity`, `item_sales_description`, `item_purchase_description`, `item_selling_price`, `item_cost_price`, `item_cogs_account_id`, `item_income_account_id`, `item_asset_account_id`, `created_at`) VALUES
(564, 'Bushing, Bronze 68x105x100', '1029007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Bronze 68x105x100', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(565, 'Bushing, Bronze 45x80x100', '1029011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Bronze 45x80x100', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(566, 'Bushing, Bronze 60x90x100mmL', '1029012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Bronze 60x90x100mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(567, 'Sleeve, SS Hllw Shaftng 50x90x', '1029018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sleeve, SS Hllw Shaftng 50x90x', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(568, 'Sleeve, SS Hollow 50mmx90mmx15', '1029018B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sleeve, SS Hollow 50mmx90mmx15', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(569, 'Sleeve, SS Hallow 65x105x18', '1029021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sleeve, SS Hallow 65x105x18', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(570, 'Bushing, Bronze 50x80x100', '1029024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Bronze 50x80x100', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(571, 'Bushing, Bronze 40x70x200mmL', '1029030', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Bronze 40x70x200mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(572, 'Sleeve, SS Hollow 50x65x250', '1029032D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sleeve, SS Hollow 50x65x250', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(573, 'Sleeves, SS Hollow 60x80x200', '1029033', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sleeves, SS Hollow 60x80x200', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(574, 'Bushing, Rubber for Man Dies', '1029034', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Rubber for Man Dies', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(575, 'Sleeve, SS Hallow 40x60x100', '1029036A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sleeve, SS Hallow 40x60x100', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(576, 'Sleeve, SS Hollow 40mmx60mmx12', '1029036B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sleeve, SS Hollow 40mmx60mmx12', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(577, 'Bushing, Bronze 85x100x160', '1029039', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Bronze 85x100x160', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(578, 'Bushing, Hollow SS 40x70x40m', '1029042', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Hollow SS 40x70x40m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(579, 'Bushing, Bronze 60x90x160', '1029050B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Bronze 60x90x160', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(580, 'Bushing, Tiger Bronze 115mm', '1029051', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Tiger Bronze 115mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(581, 'Sleeve, SS Hollow 14x34x256', '1029057A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sleeve, SS Hollow 14x34x256', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(582, 'Bushing, Bronze 100x125x350m', '1029059', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Bronze 100x125x350m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(583, 'Sleeve, SS Hollow 30x54x325', '1029060A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sleeve, SS Hollow 30x54x325', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(584, 'Bushing, Bronze 220x215x400m', '1029065', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Bronze 220x215x400m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(585, 'Bushing, Bronze 85x150x200', '1029066', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Bronze 85x150x200', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(586, 'Bushing, w/Flange 30000', '1029067', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, w/Flange 30000', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(587, 'Bushing, Bronze 120x180x100mmL', '1029069', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Bronze 120x180x100mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(588, 'Bushing, Bronze 70x115x150mmL', '1029074B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Bronze 70x115x150mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(589, 'Bushing, Bronze 125x180x120m', '1029075A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Bronze 125x180x120m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(590, 'Bushing, Bronze Split 125mmIDx', '1029075B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Bronze Split 125mmIDx', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(591, 'Sleeve, SS Hollow 22x46x280', '1029076A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sleeve, SS Hollow 22x46x280', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(592, 'Bushing, Bronze 125x93mmx15m', '1029079', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Bronze 125x93mmx15m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(593, 'Bushing, Bronze 125IDx180x10', '1029082', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Bronze 125IDx180x10', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(594, 'Bushing, Bronze 195mmx265mmx', '1029083', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Bronze 195mmx265mmx', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(595, 'Bushing, Bronze 2.75mmx5.25m', '1029091', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Bronze 2.75mmx5.25m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(596, 'Bushing, Bronze 163mmx160mmL', '1029100', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Bronze 163mmx160mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(597, 'Sleeve, SS Hollow 45x85x150', '1029104', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sleeve, SS Hollow 45x85x150', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(598, 'Sleeve, SS Hollow 58x85x200', '1029106', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sleeve, SS Hollow 58x85x200', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(599, 'Sleeve, SS Hollow 40x65x105mmL', '1029109A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sleeve, SS Hollow 40x65x105mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(600, 'Sleeve, SS Hollow 50x65x240m', '1029110', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sleeve, SS Hollow 50x65x240m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(601, 'Bushing,Bronze Split85x135x1', '1029123', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing,Bronze Split85x135x1', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(602, 'Sleeve, SS Hollow 45x65x150mmL', '1029130A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sleeve, SS Hollow 45x65x150mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(603, 'Sleeve, SS Hollow 45x65x105', '1029130C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sleeve, SS Hollow 45x65x105', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(604, 'Bushing, Tiger Bronze 90x300', '1029132', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Tiger Bronze 90x300', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(605, 'Bushing, Comprising 1563212-', '1029135', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Comprising 1563212-', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(606, 'Bushing, SS Shafting 85x110m', '1029138', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, SS Shafting 85x110m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(607, 'Bushing, C Bronze 90x100x300', '1029144', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, C Bronze 90x100x300', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(608, 'Shafting, Hollow Stl 45x75x5', '1029151', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Shafting, Hollow Stl 45x75x5', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(609, 'Bushing, Bronze 162x124mm', '1029159', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Bronze 162x124mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(610, 'Bushing, Hollow Bronze', '1029171', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Hollow Bronze', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(611, 'Bushing, Bronze 185mmx165mm', '1029173', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Bronze 185mmx165mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(612, 'Bushing, Hllw.Bronze 210x230', '1029191', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Hllw.Bronze 210x230', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(613, 'Sleeve, SS Hollow 35x55x120mmL', '1029196A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sleeve, SS Hollow 35x55x120mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(614, 'Bushing, Bronze 35x75x60mmL', '1029198A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Bronze 35x75x60mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(615, 'Bushing, Bronze 83x120x100', '1029200', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Bronze 83x120x100', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(616, 'Bushing, Crankshaft PN-10108', '1029203', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Crankshaft PN-10108', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(617, 'Bushing, Crank Shaft PN-061111', '1029206', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Crank Shaft PN-061111', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(618, 'Bushing, Bronze 80x150x125mmL', '1029207', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Bronze 80x150x125mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(619, 'Sleeve, SS Hollow 15x35x90', '1029211', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sleeve, SS Hollow 15x35x90', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(620, 'Sleeve, SS Hollow 50x70x200', '1029212', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sleeve, SS Hollow 50x70x200', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(621, 'Bushing, Bronze Split 140mmIDx', '1029213', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Bronze Split 140mmIDx', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(622, 'Bushing, Bronze Split 65mmIDx150mmxODx80mmL', '1029219', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Bronze Split 65mmIDx150mmxODx80mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(623, 'Bushing, Bronze Split 65mmIDx155mmODX80mmL', '1029219A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Bronze Split 65mmIDx155mmODX80mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(624, 'Bushing,Bronze 118mmIDx155mmODx175mmODx195mmL', '1029223', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing,Bronze 118mmIDx155mmODx175mmODx195mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(625, 'Capscrew, Hex Hd  1/4\"x2\'', '1031006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capscrew, Hex Hd  1/4\"x2\'', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(626, 'Capscrew, Hex 3/8\"x3/4\" NF', '1031012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capscrew, Hex 3/8\"x3/4\" NF', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(627, 'Bolt, Hex Hd GD w/nut 8mmx65', '1031013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd GD w/nut 8mmx65', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(628, 'Capscrew, Hex 3/8\"x2½\"L', '1031016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capscrew, Hex 3/8\"x2½\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(629, 'Capscrew, Hex 7/16\"x3/4\"L NF', '1031018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capscrew, Hex 7/16\"x3/4\"L NF', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(630, 'Capscrew, Hex 7/16\"x1\"L', '1031019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capscrew, Hex 7/16\"x1\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(631, 'Capscrew, Hex 1/2\"x2\"L', '1031027', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capscrew, Hex 1/2\"x2\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(632, 'Capscrew, Hex 1/2\"x3\"L', '1031029', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capscrew, Hex 1/2\"x3\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(633, 'Capscrew, Hex 3/4\"x1\"L NF', '1031034', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capscrew, Hex 3/4\"x1\"L NF', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(634, 'Capscrew, Hex 3/4\"x2\"L', '1031038', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capscrew, Hex 3/4\"x2\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(635, 'Bolt, Hex. HD 14mmφ x 50mmL GD', '1031039', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex. HD 14mmφ x 50mmL GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(636, 'Capscrew, Hex 7/8\"x1½\"L NF', '1031042', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capscrew, Hex 7/8\"x1½\"L NF', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(637, 'Capscrew, Hex 7/8\"x2\"L NF', '1031044', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capscrew, Hex 7/8\"x2\"L NF', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(638, 'Capscrew, Hex 1\"x1½\"L NF', '1031046', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capscrew, Hex 1\"x1½\"L NF', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(639, 'Capscrew, Hex 1\"x2\"L', '1031048', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capscrew, Hex 1\"x2\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(640, 'Capscrew, Hex 1\"x2½\"L NF', '1031049', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capscrew, Hex 1\"x2½\"L NF', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(641, 'Bolt, Hex Hd w/nut 6mmx25mmL GD', '1031051', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 6mmx25mmL GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(642, 'Bolt, Hex HD w/ nut 6mmx25mm GD w/ washer', '1031051B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex HD w/ nut 6mmx25mm GD w/ washer', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(643, 'Bolt, Hex Hd w/nut 8mmx35mm GD', '1031054', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 8mmx35mm GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(644, 'Bolt, Hex. HD GD w/nut 10x25', '1031057', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex. HD GD w/nut 10x25', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(645, 'Bolt, Bronze w/nut ¼\"x3/4\"', '1031058', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Bronze w/nut ¼\"x3/4\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(646, 'Bolt, Hex Hd  w/nut 10mmx35 GD', '1031059', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd  w/nut 10mmx35 GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(647, 'Bolt, Hex Hd 10mmφx50mmL GD', '1031060', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd 10mmφx50mmL GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(648, 'Bolt, Hex Hd w/nut 12mmx35mm GD', '1031063', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 12mmx35mm GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(649, 'Bolt, Hex Hd w/nut 12mmx50mm GD', '1031064', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 12mmx50mm GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(650, 'Bolt, Hex Hd w/nut 6mmx35mm GD', '1031069', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 6mmx35mm GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(651, 'Capscrew, Hex 9mmx90mmL', '1031070', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capscrew, Hex 9mmx90mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(652, 'Bolt, Hex Hd GD w/nut16mmx25', '1031076', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd GD w/nut16mmx25', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(653, 'Bolt, Hex Hd GD w/nut 16mmx37.5mm', '1031076A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd GD w/nut 16mmx37.5mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(654, 'Bolt, Hex Hd w/nut 16mmx35mmL GD', '1031080', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 16mmx35mmL GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(655, 'Bolt, Hex Hd w/nut 12mmx45 GD', '1031081A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 12mmx45 GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(656, 'Capscrew, Hex 3/8\"x3½\"L NC', '1031095', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capscrew, Hex 3/8\"x3½\"L NC', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(657, 'Capscrew, Hex 3/8\"x4\"L', '1031096', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capscrew, Hex 3/8\"x4\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(658, 'Capscrew, Hex 1/4\"x3\"', '1031104', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capscrew, Hex 1/4\"x3\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(659, 'Capscrew, Hex 1/4\"x3½\"', '1031105', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capscrew, Hex 1/4\"x3½\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(660, 'Capscrew, Hex 1/2\"', '1031107', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capscrew, Hex 1/2\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(661, 'Capscrew, Hex 7/16\"x3\"L', '1031109', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capscrew, Hex 7/16\"x3\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(662, 'Capscrew, Hex 7/16\"X3½\"L', '1031110', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capscrew, Hex 7/16\"X3½\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(663, 'Capscrew, Hex 7/16\"x4\"L', '1031111', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capscrew, Hex 7/16\"x4\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(664, 'Capscrew, Hex 3/8\"x3\"L', '1031114', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capscrew, Hex 3/8\"x3\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(665, 'Capscrew, Hex 10mmx44mmL', '1031121', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capscrew, Hex 10mmx44mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(666, 'Bolt, Hex Hd w/nut 8mmx25mm GD', '1031122', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd w/nut 8mmx25mm GD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(667, 'Capscrew, Brass Flt HD 6mmx1', '1031128', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capscrew, Brass Flt HD 6mmx1', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(668, 'Capscrew, Sltd Flat 12x30x11', '1031130', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capscrew, Sltd Flat 12x30x11', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(669, 'Capscrew, Hex 10mmx15mmL', '1031131', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capscrew, Hex 10mmx15mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(670, 'Capscrew, SS Hex Socket 6mmx10mm', '1031132', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capscrew, SS Hex Socket 6mmx10mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(671, 'Apron, CaneCarrierSlat395x2100', '1032003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Apron, CaneCarrierSlat395x2100', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(672, 'Blades, Cane Cutter 183x662x16T', '1032007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Blades, Cane Cutter 183x662x16T', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(673, 'Chain, Bucket Elevator Conveyor', '1035001A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Chain, Bucket Elevator Conveyor', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(674, 'Chain, Roller 1.00\" RS-80', '1035004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Chain, Roller 1.00\" RS-80', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(675, 'Links, Offset', '1035005A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Links, Offset', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(676, 'Chain, Roller #80', '1035008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Chain, Roller #80', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(677, 'Chain, SS Link 4103', '1035009C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Chain, SS Link 4103', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(678, 'Chain, Links ICC HD #4103 SS 3.075', '1035035A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Chain, Links ICC HD #4103 SS 3.075', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(679, 'Chain, Roller SY-120-2, Silv', '1035037', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Chain, Roller SY-120-2, Silv', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(680, 'Chain,Roller PHC 200-ICx10ft', '1035041', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Chain,Roller PHC 200-ICx10ft', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(681, 'Chain,Roller PHC 200-1 O/L S', '1035042', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Chain,Roller PHC 200-1 O/L S', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(682, 'Chain, Cane Truck 45mmx65mmxLx13mm CS', '1035045', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Chain, Cane Truck 45mmx65mmxLx13mm CS', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(683, 'Clip, Hose 1/2\"', '1038002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Clip, Hose 1/2\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(684, 'Clamp, Pipe 65mm', '1038005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Clamp, Pipe 65mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(685, 'Clip, Hose PN-92184787', '1038028', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Clip, Hose PN-92184787', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(686, 'Clamp, Hose PN-39110556', '1038029', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Clamp, Hose PN-39110556', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(687, 'Clip, Hose SS Banding Worn Drive', '1038030', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Clip, Hose SS Banding Worn Drive', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(688, 'Joint, Ball Assy. PN-066072A', '1045008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Joint, Ball Assy. PN-066072A', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(689, 'Conoflow', '1046001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Conoflow', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(690, 'Coupling, Air Compressor', '1050001A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupling, Air Compressor', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(691, 'Coupling, Falk Flex 6F', '1050011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupling, Falk Flex 6F', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(692, 'Coupling, Rod #2600', '1050024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupling, Rod #2600', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(693, 'Coupling, #2650', '1050027', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupling, #2650', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(694, 'Coupling, #2650', '1050028', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupling, #2650', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(695, 'Coupling, Part#00 GG11 2653', '1050028A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupling, Part#00 GG11 2653', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(696, 'Coupling, Bronze Male 1/4\"ID', '1050029', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupling, Bronze Male 1/4\"ID', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(697, 'Coupling, Wraplex Rubber 40R', '1050033', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupling, Wraplex Rubber 40R', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(698, 'Coupling, Seal Ring', '1050035', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupling, Seal Ring', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(699, 'Coupling, Rod', '1050036', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupling, Rod', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(700, 'Rubber, Cushion Doughnut type', '1052001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rubber, Cushion Doughnut type', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(701, 'Rubber,Connector Flexible 145mmx160mmx295mmL', '1052002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rubber,Connector Flexible 145mmx160mmx295mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(702, 'Cutter, Straight TE-05 Skatoskalo', '1053003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cutter, Straight TE-05 Skatoskalo', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(703, 'Cutter, Right TE-05 Skatoskalo', '1053004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cutter, Right TE-05 Skatoskalo', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(704, 'Cutter, Left TE-05 Skatoskalo', '1053006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cutter, Left TE-05 Skatoskalo', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(705, 'Spindle, TE-05 Skatoskalo', '1053008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spindle, TE-05 Skatoskalo', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(706, 'Cylinder, AirHood LfgTD203 6', '1054008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cylinder, AirHood LfgTD203 6', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(707, 'Diaphragm, Rubber  w/Metal', '1056007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Diaphragm, Rubber  w/Metal', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(708, 'Diaphragm, Rubber w/insrtd.m', '1056009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Diaphragm, Rubber w/insrtd.m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(709, 'Diaphragm, Rubber 385x253', '1056012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Diaphragm, Rubber 385x253', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(710, 'Diaphragm, Self Setting Valv', '1056015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Diaphragm, Self Setting Valv', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(711, 'Diaphragm, Mtd w/relay P2398', '1056017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Diaphragm, Mtd w/relay P2398', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(712, 'Diaphragm, Hyd. Air 2way val', '1056018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Diaphragm, Hyd. Air 2way val', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(713, 'Diaphragm, Rubber 10bars/65°', '1056019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Diaphragm, Rubber 10bars/65°', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(714, 'Diaphragm, Rubber 12\"ODx1/8\"', '1056020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Diaphragm, Rubber 12\"ODx1/8\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(715, 'Diaphragm, Rubber 535mmODx5m', '1056029', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Diaphragm, Rubber 535mmODx5m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(716, 'Felt 065511', '1058002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Felt 065511', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(717, 'Felt, Mechanical 3/8\"x36\"x36\"', '1058005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Felt, Mechanical 3/8\"x36\"x36\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(718, 'Filter, Air Element', '1059003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Air Element', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(719, 'Filter, Oil 110mmx195mmx18mm', '1059004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil 110mmx195mmx18mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(720, 'Filter, Oil', '1059011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(721, 'Filter, Disp. Oil Element', '1059012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Disp. Oil Element', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(722, 'Filter,Element 102x50IDx200O', '1059021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter,Element 102x50IDx200O', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(723, 'Filter, Oil Hi-Performance', '1059023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil Hi-Performance', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(724, 'Filter, Air Element #2220309', '1059025', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Air Element #2220309', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(725, 'Filter, Air #39588470', '1059027', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Air #39588470', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(726, 'Filter, Oil 7-3/8\"-H,4\"-OD,1', '1059028', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil 7-3/8\"-H,4\"-OD,1', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(727, 'Filter, IR Coolant #54672654', '1059032', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, IR Coolant #54672654', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(728, 'Filter, Air Elem. CCN 855657', '1059033', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Air Elem. CCN 855657', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(729, 'Filter, AirElem. CCN85565752', '1059034', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, AirElem. CCN85565752', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(730, 'Filter, Oil Stauff SF 6520 25psi', '1059039', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil Stauff SF 6520 25psi', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(731, 'Filter, Assy. PN-22234967', '1059042', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Assy. PN-22234967', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(732, 'Connector, Push In 4mmφ tubi', '1060002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Connector, Push In 4mmφ tubi', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(733, 'Connector, Push-in KQZE04-00', '1060007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Connector, Push-in KQZE04-00', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(734, 'Nipple, Grease ¼\"NPT Straight', '1060009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nipple, Grease ¼\"NPT Straight', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(735, 'Fitting, Grease 1/4\"NPT', '1060009A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fitting, Grease 1/4\"NPT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(736, 'Fitting, Grease ½\"NPT Straight', '1060010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fitting, Grease ½\"NPT Straight', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(737, 'Fitting, Aeroquip HP 4271-16', '1060013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fitting, Aeroquip HP 4271-16', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(738, 'Fitting, Push-in Bulkhead 6mmφ', '1060014B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fitting, Push-in Bulkhead 6mmφ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(739, 'Fittings,Push-in8mm KQ2E08-00A', '1060015A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fittings,Push-in8mm KQ2E08-00A', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(740, 'Fitting, Push-in Bulkhead 8mmφ', '1060015B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fitting, Push-in Bulkhead 8mmφ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(741, 'Fitting, Push-in Straight Unio', '1060017C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fitting, Push-in Straight Unio', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(742, 'Fitting, Grease 1/8\" NPT', '1060018A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fitting, Grease 1/8\" NPT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(743, 'Fitting, Bushing 20mm', '1060021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fitting, Bushing 20mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(744, 'Fitting, Push In 10mmφ ¼\"NPT', '1060026', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fitting, Push In 10mmφ ¼\"NPT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(745, 'Fitting, Bronze F Cnnctr. 3\"', '1060028', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fitting, Bronze F Cnnctr. 3\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(746, 'Conector, P/IN QS-B-12-PH', '1060029', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Conector, P/IN QS-B-12-PH', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(747, 'Connector, Male SS#316', '1060033', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Connector, Male SS#316', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(748, 'Fitting, Push-in straight unio', '1060034B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fitting, Push-in straight unio', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(749, 'Fittings, Elbow Equal 6mmφ NPQ', '1060049C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fittings, Elbow Equal 6mmφ NPQ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(750, 'Fittings,Push-in Equal Elbow 6', '1060055B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fittings,Push-in Equal Elbow 6', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(751, 'Fittings, Elbow Equal 8mm NPQE', '1060055C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fittings, Elbow Equal 8mm NPQE', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(752, 'Fitting, Flareless Tube 7/8\"', '1060061', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fitting, Flareless Tube 7/8\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(753, 'Fitting, Push-in Tee 8mmφ tube', '1060066A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fitting, Push-in Tee 8mmφ tube', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(754, 'Fittings,Push-in Tee 8mmφ NPQE-T-Q8-EP10', '1060066B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fittings,Push-in Tee 8mmφ NPQE-T-Q8-EP10', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(755, 'Fitting, Push-in Tee 6mmφ tube', '1060067A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fitting, Push-in Tee 6mmφ tube', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(756, 'Fittings,Push-in Tee 6mmφ NPQE', '1060067B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fittings,Push-in Tee 6mmφ NPQE', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(757, 'Connector-T, Push-inKQ2T10-0', '1060068', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Connector-T, Push-inKQ2T10-0', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(758, 'Fittings, Push-in half union 1', '1060081', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fittings, Push-in half union 1', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(759, 'Fttngs,Push-in Half union 1/4\"NPT 6mmφQS1/4-6', '1060081A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fttngs,Push-in Half union 1/4\"NPT 6mmφQS1/4-6', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(760, 'Fittings, Push-in half union 1/4\" NPT 8mmdia', '1060082', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fittings, Push-in half union 1/4\" NPT 8mmdia', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(761, 'Fttngs,Push-in Half union 1/4\"', '1060082A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fttngs,Push-in Half union 1/4\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(762, 'Fittings, Push-in half union 1/8\" NPT 6mmφ', '1060087', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fittings, Push-in half union 1/8\" NPT 6mmφ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(763, 'Fittings, Push-in half union 1/8\" NPT 8mmφ', '1060088', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fittings, Push-in half union 1/8\" NPT 8mmφ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(764, 'Flame, Detectortype450V 5#11', '1061001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Flame, Detectortype450V 5#11', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(765, 'Flange, CS Slip-on 12\"φx150psig', '1063005A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Flange, CS Slip-on 12\"φx150psig', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(766, 'Flange, Steel 12\"x150lbs', '1063017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Flange, Steel 12\"x150lbs', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(767, 'Flange, Pipe 3\"dia.x 150lbs.', '1063018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Flange, Pipe 3\"dia.x 150lbs.', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(768, 'Flange Weld Ner S\" 150lbs', '1063019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Flange Weld Ner S\" 150lbs', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(769, 'Flange, Slip-on 4\"φ', '1063024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Flange, Slip-on 4\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(770, 'Flange, Slip-on 2\"φ', '1063034', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Flange, Slip-on 2\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(771, 'Flange, Slip-on 3\"φ', '1063034A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Flange, Slip-on 3\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(772, 'Flange Weld Neck 12\"x150lbs', '1063044', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Flange Weld Neck 12\"x150lbs', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(773, 'Flange, CS Pipe 300mmx400lbs', '1063056', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Flange, CS Pipe 300mmx400lbs', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(774, 'Flange, CS Pipe 600mmx150lbs', '1063057', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Flange, CS Pipe 600mmx150lbs', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(775, 'Flange, CS Slip-on 8\"φ 150psi', '1063068A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Flange, CS Slip-on 8\"φ 150psi', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(776, 'Flange, 2\"φ', '1063076A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Flange, 2\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(777, 'Flange ( 100 NB ANSI 150)', '1063077', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Flange ( 100 NB ANSI 150)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(778, 'Flange (80 NB ANSI 150)', '1063078', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Flange (80 NB ANSI 150)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(779, 'Powder, Brazing Flux', '1064002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Powder, Brazing Flux', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(780, 'Soldering, Flux Magna-88', '1064008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Soldering, Flux Magna-88', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(781, 'Magna, 940', '1064010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Magna, 940', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(782, 'Gasket, Compressed Non-Asbestos 1/8\"X50\"x50\"', '1065005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Compressed Non-Asbestos 1/8\"X50\"x50\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(783, 'Gasket, Comp.Asbestos1/8x60x60', '1065006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Comp.Asbestos1/8x60x60', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(784, 'Gasket, Compressed Non-Asbestos 1/16\"x50\"x50\"', '1065009A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Compressed Non-Asbestos 1/16\"x50\"x50\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(785, 'Gasket, Copper Metal', '1065010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Copper Metal', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(786, 'Gasket, Copper Metal Seals 325IDx346ODx3.0T', '1065010A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Copper Metal Seals 325IDx346ODx3.0T', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(787, 'Gasket, Compressed Non-Asbestos 1/32\"x50\"x50\"', '1065013A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Compressed Non-Asbestos 1/32\"x50\"x50\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(788, 'Gasket, NeopreneRbbr14x15x13', '1065014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, NeopreneRbbr14x15x13', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(789, 'Gasket, Neoprene Rubber 1/4\"x1mtr.x3mtrs.', '1065016C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Neoprene Rubber 1/4\"x1mtr.x3mtrs.', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(790, 'Gasket, Neoprene Rubber 1/4\"x1mx60\"', '1065016D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Neoprene Rubber 1/4\"x1mx60\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(791, 'Gasket, Oil Damper 39140-049', '1065019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Oil Damper 39140-049', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(792, 'Gasket, Neoprene Rubber 1/8\"x1mx60\"', '1065020D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Neoprene Rubber 1/8\"x1mx60\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(793, 'Gasket, Copper Metal Seal ri', '1065021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Copper Metal Seal ri', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(794, 'Gasket, Asbsts 1/16\"x60\"x60\"', '1065023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Asbsts 1/16\"x60\"x60\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(795, 'Gasket,Non-Asbsts1/64\"x60\"x60\"', '1065032', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket,Non-Asbsts1/64\"x60\"x60\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(796, 'Gasket,NeopreneRbbr 12.8x10x14', '1065034', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket,NeopreneRbbr 12.8x10x14', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(797, 'Gasket, Neoprene Rubber Trapezoidal 1565x1515', '1065034A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Neoprene Rubber Trapezoidal 1565x1515', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(798, 'Gasket, Neoprene Rbbr 16x11x19', '1065040', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Neoprene Rbbr 16x11x19', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(799, 'Gasket, Neoprene Rbbr 11x14x11', '1065043', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Neoprene Rbbr 11x14x11', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(800, 'Gasket #289-3-1093', '1065051', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket #289-3-1093', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(801, 'Gasket, Cppr Mtl Seals 150mm', '1065052', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Cppr Mtl Seals 150mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(802, 'Gasket #289-3-1094', '1065055', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket #289-3-1094', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(803, 'Gasket #289-3-1095', '1065056', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket #289-3-1095', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(804, 'Gasket, Spiral Round 6\"', '1065060', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Spiral Round 6\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(805, 'Gasket, Copper [3pcs/set]', '1065062', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Copper [3pcs/set]', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(806, 'Gasket, Cppr Metal 3.2x260x3', '1065066A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Cppr Metal 3.2x260x3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(807, 'Gasket, Cppr Metal 3.2x305x3', '1065067A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Cppr Metal 3.2x305x3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(808, 'Gasket, Cppr Metal 3.2x300x3', '1065068A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Cppr Metal 3.2x300x3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(809, 'Gasket, Air Filter Hsng#3945', '1065074', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Air Filter Hsng#3945', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(810, 'Gasket-Washer, Copper Ring', '1065078', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket-Washer, Copper Ring', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(811, 'Gasket, Sep. Cover PN-397725', '1065089', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Sep. Cover PN-397725', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(812, 'Gasket, Neoprn Rbbr 1/8\"x1mx3m', '1065163', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Neoprn Rbbr 1/8\"x1mx3m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(813, 'Glass, Sight 330°C', '1066006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Glass, Sight 330°C', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(814, 'Gauge, Pressure 0-60 psi', '1066011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gauge, Pressure 0-60 psi', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(815, 'Gauge, Pressure 0-100psi', '1066012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gauge, Pressure 0-100psi', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(816, 'Gauge,PressureVacuum 30in Hg 0-60psi 4\"/100mm', '1066026A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gauge,PressureVacuum 30in Hg 0-60psi 4\"/100mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(817, 'Gauge,Pressure Vacuum 30\"HgVAC-0/76cm 1/4\"NPT', '1066030A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gauge,Pressure Vacuum 30\"HgVAC-0/76cm 1/4\"NPT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(818, 'Gauge, Pressure 0-30psi 1/4\"NPT', '1066031', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gauge, Pressure 0-30psi 1/4\"NPT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(819, 'Gauge, Pressure 0-600psi', '1066036', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gauge, Pressure 0-600psi', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(820, 'Gauge, Pressure 0-160psi 2-1/2\"', '1066056B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gauge, Pressure 0-160psi 2-1/2\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(821, 'Gauge,Press.0-40KGF/CM2&600PSI', '1066084A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gauge,Press.0-40KGF/CM2&600PSI', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(822, 'Glass, Microscope Slide', '1067012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Glass, Microscope Slide', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(823, 'Glass, Hard Press Clr 250x25', '1067015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Glass, Hard Press Clr 250x25', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(824, 'Glass, Tubing 1/4\"IDx3/8\"OD', '1067016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Glass, Tubing 1/4\"IDx3/8\"OD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(825, 'Glass, Soda Lime Disc 197x15', '1067021R', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Glass, Soda Lime Disc 197x15', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(826, 'Tube, Test 16mmOD 150mmL w/rim', '1067023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tube, Test 16mmOD 150mmL w/rim', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(827, 'Grid, Member Falk #16F', '1069001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Grid, Member Falk #16F', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(828, 'ICC CHAIN ATTACHMENT(4103-F30)', '1071001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'ICC CHAIN ATTACHMENT(4103-F30)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(829, 'Wings, Flight #2C', '1071002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wings, Flight #2C', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(830, 'Holder, Slat Attachment #104', '1071005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Holder, Slat Attachment #104', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(831, 'Holder, Electrode 300amps', '1071007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Holder, Electrode 300amps', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(832, 'Collar, Bearing Holder 101071', '1071014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Collar, Bearing Holder 101071', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(833, 'Hose, Spiral w/wire Insertio', '1072004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Spiral w/wire Insertio', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(834, 'Hose, Hydraulic 1/2\"φ(12.7mm', '1072018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Hydraulic 1/2\"φ(12.7mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(835, 'Hose, Hydraulic complete', '1072031', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Hydraulic complete', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(836, 'Hose, Flexible 0.75\"Dia.x24\"', '1072033', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Flexible 0.75\"Dia.x24\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(837, 'Hose, Insert 23.4\"x38.5\"x40m', '1072050', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Insert 23.4\"x38.5\"x40m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(838, 'Hose, SS Flexible 1/2\"φx12\"', '1072051A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, SS Flexible 1/2\"φx12\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(839, 'Hose, Airend Discharge', '1072055', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Airend Discharge', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(840, 'Hose, Assembly 85562460', '1072057', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Assembly 85562460', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25');
INSERT INTO `items` (`id`, `item_name`, `item_code`, `item_type`, `item_vendor_id`, `item_uom_id`, `item_reorder_point`, `item_category_id`, `item_quantity`, `item_sales_description`, `item_purchase_description`, `item_selling_price`, `item_cost_price`, `item_cogs_account_id`, `item_income_account_id`, `item_asset_account_id`, `created_at`) VALUES
(841, 'Hose, Hydraulic ¼\"(6mmID)', '1072058', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Hydraulic ¼\"(6mmID)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(842, 'Hose, Operating 3/8 NPT', '1072072', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Operating 3/8 NPT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(843, 'Hydraulic Hose', '1072074', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hydraulic Hose', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(844, 'Hose, Hydraulic 1/2\" ID', '1072075', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Hydraulic 1/2\" ID', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(845, 'Hose PN-22505960', '1072076', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose PN-22505960', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(846, 'Hose, Blowdown #39 PN-85558963', '1072078', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Blowdown #39 PN-85558963', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(847, 'Injector, Nozzle', '1073002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Injector, Nozzle', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(848, 'Injector, Nozzle [small]', '1073003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Injector, Nozzle [small]', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(849, 'Impeller, Bronze', '1074004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Impeller, Bronze', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(850, 'Pump, Centrifugal Suction', '1074006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pump, Centrifugal Suction', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(851, 'Pump, Centrifugal Impeller', '1074007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pump, Centrifugal Impeller', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(852, 'Pump, HD Submersible', '1074009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pump, HD Submersible', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(853, 'Impeller, SS 7 vanes 210m', '1074013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Impeller, SS 7 vanes 210m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(854, 'Impeller, Cent. Pump 314x55x', '1074017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Impeller, Cent. Pump 314x55x', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(855, 'Impeller, Centrifugal Pump', '1074022', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Impeller, Centrifugal Pump', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(856, 'Impeller, Cast Iron 210mm 6V', '1074023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Impeller, Cast Iron 210mm 6V', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(857, 'Impeller, Centrifugal Pump 324x40x115', '1074025A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Impeller, Centrifugal Pump 324x40x115', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(858, 'Impeller, Bronze', '1074026', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Impeller, Bronze', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(859, 'Impeller, Pump 288mmx3.5T', '1074032', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Impeller, Pump 288mmx3.5T', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(860, 'Impeller, Cast 160mmODx9 van', '1074037', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Impeller, Cast 160mmODx9 van', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(861, 'Impeller, CI Mach. Finished', '1074038', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Impeller, CI Mach. Finished', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(862, 'Impeller,Sbmrsble Pump50mmx3', '1074039', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Impeller,Sbmrsble Pump50mmx3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(863, 'Impeller, 3pcs/set PN230', '1074040', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Impeller, 3pcs/set PN230', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(864, 'Impeller, Pump Injection 7 Vanes', '1074048', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Impeller, Pump Injection 7 Vanes', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(865, 'Impeller, Pump Centrifugal MTP 2 Vanes', '1074049', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Impeller, Pump Centrifugal MTP 2 Vanes', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(866, 'Kit, Mechanical Seal SPV-23', '1076004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Kit, Mechanical Seal SPV-23', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(867, 'Kit, Mechanical seal MF23-3028', '1076004B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Kit, Mechanical seal MF23-3028', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(868, 'Kit, Repair Plough Horz. Cyl.', '1076006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Kit, Repair Plough Horz. Cyl.', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(869, 'Kit, Discharge ValveCyl.Repair', '1076009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Kit, Discharge ValveCyl.Repair', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(870, 'Kit, SeparatorElement#423612', '1076011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Kit, SeparatorElement#423612', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(871, 'Kit, Repair Plough Vert. Cyl.', '1076013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Kit, Repair Plough Vert. Cyl.', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(872, 'Kit,Repair Plough Horizontal Cyl.PN:20050-129', '1076013A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Kit,Repair Plough Horizontal Cyl.PN:20050-129', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(873, 'Kit, Valve Inlet Unloader', '1076018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Kit, Valve Inlet Unloader', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(874, 'Kit, Repair Wash valve 3/4\"', '1076019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Kit, Repair Wash valve 3/4\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(875, 'Kit, Repair 50mm CA 1N50A-PS', '1076021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Kit, Repair 50mm CA 1N50A-PS', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(876, 'Kit, Drum Brake Cyl. Repair', '1076022', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Kit, Drum Brake Cyl. Repair', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(877, 'Kit, Drum Brake Cyl. Repair PN-20070-476', '1076023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Kit, Drum Brake Cyl. Repair PN-20070-476', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(878, 'Kit, Repair CS1FN180-240', '1076031', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Kit, Repair CS1FN180-240', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(879, 'Gasket, Air Filter Housing', '1076074', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Air Filter Housing', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(880, 'Kit Repair Aircylinder 80mm', '1076079', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Kit Repair Aircylinder 80mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(881, 'Looper,Con.Rod Ball PN-06311', '1077003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Looper,Con.Rod Ball PN-06311', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(882, 'Looper,Bell Crank PN-063041', '1077004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Looper,Bell Crank PN-063041', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(883, 'Looper, Bell Crank PN-063011', '1077007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Looper, Bell Crank PN-063011', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(884, 'Screw, Lug 1/2\"x2-1/2\"L', '1079003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, Lug 1/2\"x2-1/2\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(885, 'Screw, Lug 1/2\"x4\"L', '1079005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, Lug 1/2\"x4\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(886, 'Bolt,ScrewHeadSloted M3x16mm', '1079006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt,ScrewHeadSloted M3x16mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(887, 'Bolt,ScrewheadSloted M4x25mm', '1079007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt,ScrewheadSloted M4x25mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(888, 'Bolt,ScrewHeadSlottedM5x25mm', '1079008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt,ScrewHeadSlottedM5x25mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(889, 'Screw, PN-1/4S40502', '1079010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, PN-1/4S40502', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(890, 'Nipper,NeedleThread Assy65141A', '1081050', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nipper,NeedleThread Assy65141A', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(891, 'Nipper, Needle Thread Assy. PN-065141B', '1081050A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nipper, Needle Thread Assy. PN-065141B', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(892, 'Nipper,NeedleThread Cam 0650', '1081051', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nipper,NeedleThread Cam 0650', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(893, 'Nut, Hex GD 1/4\"φ', '1082001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Hex GD 1/4\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(894, 'Nut, Hex GD 5/8\"φ UNC', '1082002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Hex GD 5/8\"φ UNC', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(895, 'Nut, Hex GD 5/16\"φ', '1082003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Hex GD 5/16\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(896, 'Nut, Hex GD 3/8\"φ', '1082004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Hex GD 3/8\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(897, 'Nut, Hex 7/16\"φ NC', '1082005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Hex 7/16\"φ NC', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(898, 'Nut, Hex GD 7/8\"φ NC', '1082009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Hex GD 7/8\"φ NC', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(899, 'Nut, Hex GD 3/4\"φ UNC', '1082013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Hex GD 3/4\"φ UNC', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(900, 'Nut, Hex SS 10mmφ', '1082019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Hex SS 10mmφ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(901, 'Nut, Hex 10mmφ', '1082020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Hex 10mmφ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(902, 'Nut, Hex Elastic 14mm', '1082021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Hex Elastic 14mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(903, 'Nut, Hex.Head GD 8mmφ', '1082027', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Hex.Head GD 8mmφ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(904, 'Nut, Hex 12mmφ', '1082029', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Hex 12mmφ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(905, 'Nut, Hex 14mmφ', '1082030', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Hex 14mmφ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(906, 'Nut 18mm NC', '1082032', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut 18mm NC', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(907, 'Nut, Hex GD 19mm B7 NC', '1082035', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Hex GD 19mm B7 NC', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(908, 'Nut, Hex GD 9/16\"φ', '1082036', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Hex GD 9/16\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(909, 'Nut, Lock (SKF)', '1082037', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Lock (SKF)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(910, 'Nut, Hex GD 1/2\"φ UNC', '1082039', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Hex GD 1/2\"φ UNC', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(911, 'Nut, Hex HD 9/16\"φ NF', '1082041', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Hex HD 9/16\"φ NF', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(912, 'Nut, Hex GD 1/2\" NF', '1082046', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Hex GD 1/2\" NF', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(913, 'Nut, Hex GD 1\"', '1082050', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Hex GD 1\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(914, 'Nut, Hex GD 25mm', '1082057', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Hex GD 25mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(915, 'Nut, Sanding Lock 30#224501-', '1082059', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Sanding Lock 30#224501-', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(916, 'Nut, Hex 6mm NC', '1082063', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Hex 6mm NC', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(917, 'Nut, 3/8\"φ N28214', '1082074', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, 3/8\"φ N28214', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(918, 'Nut, 5/16\" 24201', '1082075', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, 5/16\" 24201', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(919, 'Nut, 5/16N 24210', '1082075A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, 5/16N 24210', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(920, 'Nut, 11/64\" N40204 for 064151', '1082076', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, 11/64\" N40204 for 064151', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(921, 'Nut, Needle Clamp PN-062151', '1082078', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Needle Clamp PN-062151', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(922, 'Nut, PN-15/64N28207', '1082079', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, PN-15/64N28207', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(923, 'Nut, Hexagon M16-05', '1082082', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Hexagon M16-05', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(924, 'Nut, Hexagon M16-04', '1082082A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Hexagon M16-04', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(925, 'Nut, PN-5/16N24301', '1082084', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, PN-5/16N24301', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(926, 'Nozzle, Assy #100.093.00200', '1083011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nozzle, Assy #100.093.00200', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(927, 'Cutting, Oxy/Acetylene #2', '1083015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cutting, Oxy/Acetylene #2', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(928, 'Cutting, Oxy/Acetylene Size:3', '1083016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cutting, Oxy/Acetylene Size:3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(929, 'Cutting, Oxy/Acetylene Size:4', '1083017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cutting, Oxy/Acetylene Size:4', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(930, 'Nozzle, Spray 22760-094 30°', '1083023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nozzle, Spray 22760-094 30°', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(931, 'Nozzle, Spray 22760-095 60°', '1083024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nozzle, Spray 22760-095 60°', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(932, 'Nozzle, Spray 22760-096 90°', '1083025', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nozzle, Spray 22760-096 90°', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(933, 'Nozzle, disoldering Abeco 2m', '1083026', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nozzle, disoldering Abeco 2m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(934, 'Slinger, Oil Aluminum', '1084002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Slinger, Oil Aluminum', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(935, 'Packing, Non-asbestos 1/4\"sq', '1085010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Packing, Non-asbestos 1/4\"sq', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(936, 'Packing, Asbestos Brded ¼\"sq', '1085010A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Packing, Asbestos Brded ¼\"sq', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(937, 'Packing,Braided Asbestos 1/2', '1085013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Packing,Braided Asbestos 1/2', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(938, 'Packing, Non-Asbestos 1/2\"sq.', '1085014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Packing, Non-Asbestos 1/2\"sq.', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(939, 'Packing, Non-Asbestos 1\"sq.', '1085015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Packing, Non-Asbestos 1\"sq.', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(940, 'Packing, Non-Asbestos 7/16\"sq.', '1085020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Packing, Non-Asbestos 7/16\"sq.', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(941, 'Packing, Asbestos 3/16\"sq.', '1085020A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Packing, Asbestos 3/16\"sq.', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(942, 'Packing, Non-Asbestos 3/4\"sq', '1085025', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Packing, Non-Asbestos 3/4\"sq', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(943, 'Packing, Asbestos 9/16\"Sq.', '1085026', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Packing, Asbestos 9/16\"Sq.', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(944, 'Packing, Non-Asbestos 3/8\"sq.', '1085027', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Packing, Non-Asbestos 3/8\"sq.', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(945, 'Packing,Asbestos Braided 5/1', '1085034A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Packing,Asbestos Braided 5/1', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(946, 'Packing, Non-Asbestos 5/16\"sq.', '1085035', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Packing, Non-Asbestos 5/16\"sq.', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(947, 'Packing, Asbestos 5/8\"sq.', '1085037', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Packing, Asbestos 5/8\"sq.', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(948, 'Packing, Non-Asbestos Acrylic 5/8\" sq.', '1085037A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Packing, Non-Asbestos Acrylic 5/8\" sq.', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(949, 'Packing, Mech. Seal 108x135x20', '1085072', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Packing, Mech. Seal 108x135x20', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(950, 'Packing, Asbestos 3/8\"sq.', '1085074', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Packing, Asbestos 3/8\"sq.', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(951, 'Packing, Graphite ½\"sq. W/OUT', '1085086', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Packing, Graphite ½\"sq. W/OUT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(952, 'Packing,Grphite w/oinconel7/16', '1085087', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Packing,Grphite w/oinconel7/16', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(953, 'Packing, Asbestos 7/16 w/ wire', '1085088', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Packing, Asbestos 7/16 w/ wire', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(954, 'Packing, Mech\'l Carbon Seal', '1085095', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Packing, Mech\'l Carbon Seal', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(955, 'Seal, Pack.Mech.90x133.2x17m', '1085096', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Pack.Mech.90x133.2x17m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(956, 'Packing, Non-Asbestos 3/16\"sq. (5kgs./roll)', '1085102', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Packing, Non-Asbestos 3/16\"sq. (5kgs./roll)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(957, 'Pin, Cutter for skatoskalo', '1087002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pin, Cutter for skatoskalo', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(958, 'Pin, SS Cotter 1/4\"x1½\"L', '1087004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pin, SS Cotter 1/4\"x1½\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(959, 'Pin, Dowellfor Turbo Alterna', '1087007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pin, Dowellfor Turbo Alterna', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(960, 'Pin, SS Cotter 5/16\"x3\"', '1087012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pin, SS Cotter 5/16\"x3\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(961, 'Pin & Bushing', '1087017A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pin & Bushing', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(962, 'Pin, ChainCaneCarier22.2x110', '1087022', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pin, ChainCaneCarier22.2x110', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(963, 'Pin, Bearing #AS157', '1087024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pin, Bearing #AS157', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(964, 'Pin, Screw forWorthington Tr', '1087025', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pin, Screw forWorthington Tr', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(965, 'Pin, Cap Mill w/ 2 lock nut', '1087027', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pin, Cap Mill w/ 2 lock nut', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(966, 'Pin,ExtProng Soft Steel Cott', '1087030', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pin,ExtProng Soft Steel Cott', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(967, 'Pin, GI Cotter 1/4\"φx1-1/4\"L', '1087032B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pin, GI Cotter 1/4\"φx1-1/4\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(968, 'Pin, SS Cotter 5/16\"x2\"L', '1087036', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pin, SS Cotter 5/16\"x2\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(969, 'Pin, Wrist Piston LP', '1087038', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pin, Wrist Piston LP', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(970, 'Pin, Cotter 5/16\"φx1½\"L', '1087042', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pin, Cotter 5/16\"φx1½\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(971, 'Pin, Slats Conv. 16mmφx65mmL', '1087043', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pin, Slats Conv. 16mmφx65mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(972, 'Pin, Cotter 8mmφx50mmL', '1087045', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pin, Cotter 8mmφx50mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(973, 'Plate, Throat PN-244122', '1089005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plate, Throat PN-244122', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(974, 'Plate, Throat PN-064165', '1089006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plate, Throat PN-064165', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(975, 'Plate, Throat PN-064231', '1089007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plate, Throat PN-064231', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(976, 'Plate, SS Protector Wear 150', '1089009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plate, SS Protector Wear 150', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(977, 'Plate, Swash #004168', '1089012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plate, Swash #004168', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(978, 'Plate, SS Protector Wear', '1089016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plate, SS Protector Wear', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(979, 'Plate, SS Pump Wear-Suction', '1089017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plate, SS Pump Wear-Suction', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(980, 'Plate, Wear 149x3mat 1.4571', '1089018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plate, Wear 149x3mat 1.4571', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(981, 'Plate, Pump Back Wear SS', '1089019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plate, Pump Back Wear SS', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(982, 'Plate, Pump Protector', '1089041', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plate, Pump Protector', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(983, 'Plate, Wear MJP Front 165mmIDx', '1089042', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plate, Wear MJP Front 165mmIDx', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(984, 'Plug, Rubber', '1090005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plug, Rubber', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(985, 'Seal, Rubber Ring 612x663x30', '1091005A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rubber Ring 612x663x30', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(986, 'Rivets, 1/2 X 1/2', '1092003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rivets, 1/2 X 1/2', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(987, 'Pulley, PN-061071', '1093004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pulley, PN-061071', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(988, 'Ring, Copper 12/18mm.x1.5mm', '1096002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Copper 12/18mm.x1.5mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(989, 'Ring, copper 32/40mmx2mm', '1096009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, copper 32/40mmx2mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(990, 'Ring-O, Rubber 255mmIDx7.5mm C/Sφ', '1096015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, Rubber 255mmIDx7.5mm C/Sφ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(991, 'Ring, Cutting & Keying 12mm', '1096017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Cutting & Keying 12mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(992, 'Ring, Cutting 21mmID', '1096020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Cutting 21mmID', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(993, 'Ring, Keying & Cutting 36mm', '1096021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Keying & Cutting 36mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(994, 'Ring-O, HD 16mmIDx2.5mmC/S', '1096025', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, HD 16mmIDx2.5mmC/S', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(995, 'Ring, Tip 30mmx20mmx6.5mm', '1096028', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Tip 30mmx20mmx6.5mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(996, 'Ring-V, Sealing V-60', '1096029', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-V, Sealing V-60', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(997, 'Ring-V, Sealing V-70', '1096031', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-V, Sealing V-70', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(998, 'Ring, Seal Model:# 708-U', '1096032', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Seal Model:# 708-U', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(999, 'Ring-O, Rubber 78mmIDx6.0mm C/Sφ', '1096038', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, Rubber 78mmIDx6.0mm C/Sφ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1000, 'Ring, Sealing 500mmx1\"x3/4\"', '1096039', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Sealing 500mmx1\"x3/4\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1001, 'Ring-O, Rubber 28.8mm.x3.25m', '1096042', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, Rubber 28.8mm.x3.25m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1002, 'Ring-O, Rubber \"Erika\" 127mm', '1096043', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, Rubber \"Erika\" 127mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1003, 'Ferrules size: 7/8\" dia. O.D', '1096049', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ferrules size: 7/8\" dia. O.D', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1004, 'Ring, Plastc Sealing 40/48x2', '1096050', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Plastc Sealing 40/48x2', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1005, 'Ring, Plastic 4mmx78mm', '1096053', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Plastic 4mmx78mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1006, 'Ring-O, Rubber 118x128ODx15m', '1096056', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, Rubber 118x128ODx15m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1007, 'Ring-O, Parker #12 15mmID', '1096067', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, Parker #12 15mmID', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1008, 'Ring-O, Parker #2-114', '1096068', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, Parker #2-114', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1009, 'Ring-O, Parker #19 25mmID', '1096070', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, Parker #19 25mmID', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1010, 'Ring-O, Parker #2-110', '1096076', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, Parker #2-110', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1011, 'Ring-O, Rbbr 200mmx7.5mmC/Sφ', '1096099', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, Rbbr 200mmx7.5mmC/Sφ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1012, 'Ring,\"O\"Rubbr Valve 26mmx2mm', '1096127', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring,\"O\"Rubbr Valve 26mmx2mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1013, 'Ring-O, Chord Seal 9mmφx6mL', '1096140', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, Chord Seal 9mmφx6mL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1014, 'Ring, Retainer P#36790 50x70', '1096147', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Retainer P#36790 50x70', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1015, 'Ring-O, 7.5mm@CSx190mmIDx205', '1096148', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, 7.5mm@CSx190mmIDx205', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1016, 'Ring, O- Rbbr 200mm.x212mmx6', '1096153', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, O- Rbbr 200mm.x212mmx6', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1017, 'Ring, Sltd R&F#08000 100/500', '1096162', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Sltd R&F#08000 100/500', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1018, 'Ring, O (Burner Gun)', '1096171', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, O (Burner Gun)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1019, 'Ring, Wear Casing PN 502', '1096172A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Wear Casing PN 502', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1020, 'Ring, Lip 15mm.x 20mm.', '1096181', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Lip 15mm.x 20mm.', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1021, '\"O\"Ring Rbr.70mmx75mmx5mm', '1096183', 'Inventory', NULL, NULL, NULL, NULL, 0, '\"O\"Ring Rbr.70mmx75mmx5mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1022, 'Ring, Bronze 200mm.x 304mm.', '1096196', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Bronze 200mm.x 304mm.', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1023, 'Ring, Side Brg Cover-Cast Br', '1096202', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Side Brg Cover-Cast Br', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1024, 'Ring, Cap Slve Set78/63x16.5', '1096206', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Cap Slve Set78/63x16.5', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1025, 'Round Seal Ring #161-1-1008', '1096207', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Round Seal Ring #161-1-1008', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1026, 'Seal Ring #161-1-1013', '1096209', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal Ring #161-1-1013', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1027, 'Ring, O- 29mmx3mm', '1096213', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, O- 29mmx3mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1028, 'Ring, Sealing A60x68', '1096223', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Sealing A60x68', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1029, 'Ring, O- Rubber 46.5mmx64.4m', '1096229', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, O- Rubber 46.5mmx64.4m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1030, 'Ring, Cutting & Keying 10mm', '1096250', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Cutting & Keying 10mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1031, 'Sealing Ring(Tiger Bronze)', '1096254', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sealing Ring(Tiger Bronze)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1032, 'Ring, w/ fork [mark]#AS156M', '1096275', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, w/ fork [mark]#AS156M', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1033, 'Ring Toric Joint 5mm x 80mm', '1096308', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring Toric Joint 5mm x 80mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1034, 'Ring, Rubber', '1096310', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Rubber', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1035, 'Ring, Circular Clip 4mmTx116', '1096321', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Circular Clip 4mmTx116', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1036, 'Ring, Circular Clip 5mmTx164', '1096322', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Circular Clip 5mmTx164', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1037, 'Ring, Circular Clip 5mmT1151', '1096323', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Circular Clip 5mmT1151', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1038, 'Ring, Circlip Lock 117mm.x4m', '1096335', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Circlip Lock 117mm.x4m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1039, 'Ring, Circlip Lock', '1096336', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Circlip Lock', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1040, 'Rivet, Tabular 8mm x 60mm', '1097001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rivet, Tabular 8mm x 60mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1041, 'Rivets, Blind 1/8\"φx1/2\"L', '1097002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rivets, Blind 1/8\"φx1/2\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1042, 'Rivets, Blind 3/16\"φ x ½\"L', '1097005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rivets, Blind 3/16\"φ x ½\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1043, 'Rod, Tig Filler 3/32\"x36\"', '1098004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rod, Tig Filler 3/32\"x36\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1044, 'Rod, Welding Bronze Acetylene 1/8\"φx30\"L', '1098006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rod, Welding Bronze Acetylene 1/8\"φx30\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1045, 'Rod, SS Tig filler 1/16\"x3\'', '1098007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rod, SS Tig filler 1/16\"x3\'', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1046, 'Rod, SS TIG Filler 3/32\"x3\'L', '1098007A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rod, SS TIG Filler 3/32\"x3\'L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1047, 'Rod, Bronze TIG Filler 3/16\"x36\"', '1098008A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rod, Bronze TIG Filler 3/16\"x36\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1048, 'Rod, Bronze Tig Filler 5/32x18', '1098009C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rod, Bronze Tig Filler 5/32x18', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1049, 'Rod, Tig Filler MG 521T 3/32', '1098011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rod, Tig Filler MG 521T 3/32', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1050, 'Electrode,Tngstn AW-TH-2 3/32\"', '1098013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Electrode,Tngstn AW-TH-2 3/32\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1051, 'Rod, Silver (Ordinary)', '1098058', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rod, Silver (Ordinary)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1052, 'Roller, 20-05-034', '1099007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Roller, 20-05-034', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1053, 'Roller, Belt Conv.Through 50', '1099008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Roller, Belt Conv.Through 50', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1054, 'Rollr,Outboard w/Bshng 50.5mmx158/127mmx56mm', '1099010C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rollr,Outboard w/Bshng 50.5mmx158/127mmx56mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1055, 'Rope, CableWire Steel Ctr 5/8\"', '1100006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rope, CableWire Steel Ctr 5/8\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1056, 'Rope,Ungalvanized Steel Wire 5/8\"x6x19', '1100006D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rope,Ungalvanized Steel Wire 5/8\"x6x19', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1057, 'Rope, ungalvanized 1\"dia.x6x19', '1100007A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rope, ungalvanized 1\"dia.x6x19', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1058, 'Rope, Wire 9mmφ 8x19 IWRC,RHRL', '1100010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rope, Wire 9mmφ 8x19 IWRC,RHRL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1059, 'Rope, WireCable 20mmφx8strndx3', '1100032', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rope, WireCable 20mmφx8strndx3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1060, 'Belting, Rubber Neoprene 3/4x4x8x3ply', '1101003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belting, Rubber Neoprene 3/4x4x8x3ply', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1061, 'Screen, Segment 0.06x2.8mm Slots', '1102002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screen, Segment 0.06x2.8mm Slots', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1062, 'Screen, Orifice Scavenge', '1102003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screen, Orifice Scavenge', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1063, 'Screen, SS 0.50mmTx2,450mmOALx360mmOAW', '1102004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screen, SS 0.50mmTx2,450mmOALx360mmOAW', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1064, 'Screen, Working 799mmx3,980mmL', '1102006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screen, Working 799mmx3,980mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1065, 'Screen, SS BMA 0.06x2.2mm slots', '1102007A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screen, SS BMA 0.06x2.2mm slots', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1066, 'Screen,Perforated Top 914mmW', '1102017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screen,Perforated Top 914mmW', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1067, 'Screen,SS Perforated Top 914mm Rectanglr Mesh', '1102017A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screen,SS Perforated Top 914mm Rectanglr Mesh', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1068, 'Screen, Perfrtd TopBrass 914mmW,3910mmOAL Rec', '1102017B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screen, Perfrtd TopBrass 914mmW,3910mmOAL Rec', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1069, 'Screen, SS Perforated Top', '1102018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screen, SS Perforated Top', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1070, 'Screen, Intermidiate 7x7 mes', '1102021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screen, Intermidiate 7x7 mes', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1071, 'Screen, Eng\'g Plastic Backing', '1102022', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screen, Eng\'g Plastic Backing', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1072, 'Screen, SS Perforation Sheet', '1102028', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screen, SS Perforation Sheet', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1073, 'Screen,Wire SS304 Wvn Mesh Cttng #8x4ft.x10ft', '1102037', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screen,Wire SS304 Wvn Mesh Cttng #8x4ft.x10ft', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1074, 'Shim, Brass 6\"Wx60\"Lx0.001\"T', '1102254', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Shim, Brass 6\"Wx60\"Lx0.001\"T', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1075, 'Shim, Brass 6\"Wx60\"Lx0.002\"T', '1102255', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Shim, Brass 6\"Wx60\"Lx0.002\"T', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1076, 'Shim, Brass 6\"Wx60\"Lx0.003\"T', '1102256', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Shim, Brass 6\"Wx60\"Lx0.003\"T', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1077, 'Shim, Brass 6\"Wx60\"Lx0.005\"T', '1102257', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Shim, Brass 6\"Wx60\"Lx0.005\"T', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1078, 'Shim, Brass 6\"Wx60\"Lx0.010\"T', '1102258', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Shim, Brass 6\"Wx60\"Lx0.010\"T', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1079, 'Shims, Brass6\"Wx60\"Lx0.020\"T', '1102259', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Shims, Brass6\"Wx60\"Lx0.020\"T', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1080, 'Shim, Brass 6\"Wx60\"Lx0.004\"T', '1102260', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Shim, Brass 6\"Wx60\"Lx0.004\"T', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1081, 'Screw, PN-11/64S40001', '1103005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, PN-11/64S40001', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1082, 'Screw, PN-3/16S32034> 244111', '1103006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, PN-3/16S32034> 244111', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1083, 'Screw, PN-9/64S40043> 244122', '1103008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, PN-9/64S40043> 244122', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1084, 'Screw, S40001 #11/64', '1103009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, S40001 #11/64', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1085, 'Screw, Cylinder Taping', '1103012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, Cylinder Taping', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1086, 'Tecscrew 2\" (Full Thread)', '1103015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tecscrew 2\" (Full Thread)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1087, 'Screw PN-1/8S40003> 246061', '1103017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw PN-1/8S40003> 246061', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1088, 'Screw PN-1/8S40003> 246071', '1103019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw PN-1/8S40003> 246071', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1089, 'Screw, PN-15/64S28516', '1103020A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, PN-15/64S28516', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1090, 'Screw, PN-15/64S28012', '1103020B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, PN-15/64S28012', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1091, 'Screw, PN-11/64S40074> 064165', '1103021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, PN-11/64S40074> 064165', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1092, 'Screw, PN-7/32S32007', '1103022', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, PN-7/32S32007', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1093, 'Screw, #065302', '1103023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, #065302', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1094, 'Screw, PN-3/16S28002', '1103025', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, PN-3/16S28002', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1095, 'Screw, 11/64\" 009 for 063121', '1103026', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, 11/64\" 009 for 063121', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1096, 'Screw, #066142/11164S', '1103027', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, #066142/11164S', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1097, 'Screw, PN-11/64S40084> 066142', '1103028', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, PN-11/64S40084> 066142', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1098, 'Adaptor, Tecscrew', '1103030', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Adaptor, Tecscrew', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1099, 'Screw, PN#9/64S40005', '1103031', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, PN#9/64S40005', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1100, 'Screw, Part#951-04352', '1103042', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, Part#951-04352', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1101, 'Screw, Stud 062021', '1103059', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, Stud 062021', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1102, 'Screw, PN-9/32S28014', '1103060', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, PN-9/32S28014', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1103, 'Screw, PN-9/32S32014> 062091', '1103060A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, PN-9/32S32014> 062091', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1104, 'Screw for 062091, 9/32\" S280', '1103061', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw for 062091, 9/32\" S280', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1105, 'Screw, 9/32\" S28001 for 0620', '1103062', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, 9/32\" S28001 for 0620', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1106, 'Screw, 11/64S40505 for 064151', '1103063', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, 11/64S40505 for 064151', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1107, 'Screw, 11/64S40073', '1103065', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, 11/64S40073', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1108, 'Screw, Head Cheese M16x35', '1103081', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, Head Cheese M16x35', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1109, 'Screw, Hexagon M12x30', '1103082', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, Hexagon M12x30', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1110, 'Screw, Hexagon M12x50', '1103083', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, Hexagon M12x50', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1111, 'Screw, Hexagon M16x35', '1103084', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, Hexagon M16x35', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1112, 'Screw,Slotted SS Housing', '1103085', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw,Slotted SS Housing', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1113, 'Screw, PN-9/64S40011', '1103086', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, PN-9/64S40011', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1114, 'Screw, PN-11/64S40029', '1103087', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, PN-11/64S40029', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1115, 'Screw PN-15/64S28034', '1103088', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw PN-15/64S28034', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1116, 'Screw, PN-11/64S40505', '1103089', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, PN-11/64S40505', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1117, 'Seal, Oil 19mmx32mmx7mm', '1105002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 19mmx32mmx7mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1118, 'Seal, Metal 14mmx24mmx7mm', '1105003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Metal 14mmx24mmx7mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1119, 'Seal, Oil 12mmx24mmx7mmThk.', '1105003A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 12mmx24mmx7mmThk.', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1120, 'Seal, Oil 7mmTx12mmIDx25mmOD', '1105003B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 7mmTx12mmIDx25mmOD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1121, 'Seal, Oil 7/7.5Tx12mmIDx25mm', '1105003C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 7/7.5Tx12mmIDx25mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1122, 'Seal, Oil 12mmID x 25mmOD x 7mmthk', '1105003D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 12mmID x 25mmOD x 7mmthk', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1123, 'Seal, Metal 18mmx22mmx5mm', '1105004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Metal 18mmx22mmx5mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25');
INSERT INTO `items` (`id`, `item_name`, `item_code`, `item_type`, `item_vendor_id`, `item_uom_id`, `item_reorder_point`, `item_category_id`, `item_quantity`, `item_sales_description`, `item_purchase_description`, `item_selling_price`, `item_cost_price`, `item_cogs_account_id`, `item_income_account_id`, `item_asset_account_id`, `created_at`) VALUES
(1124, 'Seal, Oil 28mmx37mmx4mm', '1105005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 28mmx37mmx4mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1125, 'Seal, Leather', '1105009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Leather', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1126, 'Seal, Oil (APS) 58mmx98mmx13', '1105010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil (APS) 58mmx98mmx13', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1127, 'Seal, Oil 40mmIDx52mmODx8mmT', '1105011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 40mmIDx52mmODx8mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1128, 'Seal, Oil 40mmx52mmx7mm', '1105011A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 40mmx52mmx7mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1129, 'Seal, Oil 27x47x7mm', '1105013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 27x47x7mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1130, 'Seal,Oil Rubber 90IDx125ODx12T', '1105014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal,Oil Rubber 90IDx125ODx12T', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1131, 'Seal, Oil 65mmIDx90mmODx13mmT', '1105016A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 65mmIDx90mmODx13mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1132, 'Seal, Rubber Oil 65mmIDx90mmOD', '1105016B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rubber Oil 65mmIDx90mmOD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1133, 'Seal,Oil 30x45x8mm', '1105017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal,Oil 30x45x8mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1134, 'Seal, Oil Rbbr 60IDx80ODx12T', '1105018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Rbbr 60IDx80ODx12T', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1135, 'Seal, Oil 60x80x13mm', '1105018A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 60x80x13mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1136, 'Seal, Rubber Oil 60mmIDx80mmODx10mmT', '1105018B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rubber Oil 60mmIDx80mmODx10mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1137, 'Seal, Oil 36mmx47mmx8mm', '1105021A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 36mmx47mmx8mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1138, 'Seal, Oil 36mmx47mmx7mm', '1105021B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 36mmx47mmx7mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1139, 'Seal, Oil Rubber 95mmIDx120mmODx12mmT', '1105022A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Rubber 95mmIDx120mmODx12mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1140, 'RadialShft Seal 20x40x10mm', '1105023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'RadialShft Seal 20x40x10mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1141, 'Seal,Oil 40x52x8mmt', '1105024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal,Oil 40x52x8mmt', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1142, 'Seal, Oil 40mmx53mmx8mm', '1105024A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 40mmx53mmx8mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1143, 'Seal, Oil 38mmx60mmx10mm', '1105025', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 38mmx60mmx10mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1144, 'Seal, Oil 30mmIDx42mmODx7mmT', '1105026', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 30mmIDx42mmODx7mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1145, 'Seal, Oil Rubber 80x125x12', '1105027', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Rubber 80x125x12', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1146, 'Seal, Oil 50mmx80mmx10mm', '1105029', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 50mmx80mmx10mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1147, 'Seal, Oil 115mmx150mmx13mm', '1105031', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 115mmx150mmx13mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1148, 'Seal, Oil 80mmx110mmx10mm', '1105032', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 80mmx110mmx10mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1149, 'Seal, Oil 90mmx120mmx10mm', '1105033', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 90mmx120mmx10mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1150, 'Seal, Oil 62mmx86mmx39mm', '1105034', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 62mmx86mmx39mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1151, 'Seal,Oil 13mmx115mmx150mm', '1105035', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal,Oil 13mmx115mmx150mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1152, 'Seal, Coupling SKF BMA 518-A', '1105040', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Coupling SKF BMA 518-A', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1153, 'Seal, Oil 40mmx80mmx10mm', '1105041', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 40mmx80mmx10mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1154, 'Seal, Rubber Oil 40mmIDx55mmODx8mmT', '1105042', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rubber Oil 40mmIDx55mmODx8mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1155, 'Seal, Oil 45mmx55mmx8mm', '1105042B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 45mmx55mmx8mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1156, 'Seal, Oil 72mmx52mmx10mm', '1105045', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 72mmx52mmx10mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1157, 'Seal, Oil 48mmx80mmx14mm', '1105046', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 48mmx80mmx14mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1158, 'Seal, Oil 48mmx80mmx8mm', '1105047', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 48mmx80mmx8mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1159, 'Seal, Oil 48mmx65mmx12mm', '1105048', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 48mmx65mmx12mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1160, 'Seal, Oil Rubber 70mmIDx110mmO', '1105050B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Rubber 70mmIDx110mmO', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1161, 'Seal, Rubber Oil 35mmIDx52mmODx10mmT', '1105051A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rubber Oil 35mmIDx52mmODx10mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1162, 'Seal, Oil 50mmx70mmx12mmT', '1105052A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 50mmx70mmx12mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1163, 'Seal, Rubber Oil 50mmIDx70mmODx10mmT', '1105052D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rubber Oil 50mmIDx70mmODx10mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1164, 'Seal, Oil 40mmx57mmx8mm', '1105053', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 40mmx57mmx8mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1165, 'Seal, Oil 40mmIDx55mmODx12mmT', '1105054B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 40mmIDx55mmODx12mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1166, 'Seal, Oil 50mmIDx72mmODx10mmT', '1105055', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 50mmIDx72mmODx10mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1167, 'Seal, Oil 55mmIDx80mmODx12mm', '1105059', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 55mmIDx80mmODx12mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1168, 'Seal, Rubber Oil 30mmIDx47mmOD', '1105060A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rubber Oil 30mmIDx47mmOD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1169, 'Seal, Oil 38mmx80mmx10mm', '1105062', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 38mmx80mmx10mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1170, 'Seal, Oil 22mmIDx42mmODx8mmT', '1105065', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 22mmIDx42mmODx8mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1171, 'Seal, Rubber Oil 22mmIDx42mmOD', '1105065C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rubber Oil 22mmIDx42mmOD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1172, 'Seal, Rubber 238IDx309ODx23.', '1105066', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rubber 238IDx309ODx23.', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1173, 'Seal, Rubber 40mmx49mmx4.5mm', '1105067', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rubber 40mmx49mmx4.5mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1174, 'Seal, Oil 55mmx70mmx8mm', '1105068A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 55mmx70mmx8mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1175, 'Seal,Oil 28x52x5', '1105069', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal,Oil 28x52x5', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1176, 'Seal, Oil 7.5mmTx11.0mmx24.0', '1105071', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 7.5mmTx11.0mmx24.0', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1177, 'Seal, Oil 28mmx52mmx6mm', '1105071A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 28mmx52mmx6mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1178, 'Seal, Flared Lip Rubber Oil', '1105073', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Flared Lip Rubber Oil', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1179, 'Seal, Rubber 70mmx80mmx5mm', '1105075', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rubber 70mmx80mmx5mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1180, 'Seal, Oil 55mmx70mmx18mm', '1105076', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 55mmx70mmx18mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1181, 'Seal, Oil 55mmx17mmx7mm', '1105076A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 55mmx17mmx7mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1182, 'Seal, Oil 125mmx150mmx14mm', '1105077', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 125mmx150mmx14mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1183, 'Seal, Oil 75mmx100mmx13mm', '1105078', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 75mmx100mmx13mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1184, 'Seal, Oil 28mmx52mmx12mm', '1105079', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 28mmx52mmx12mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1185, 'Seal, Rubber Oil 35mmIDx52ODmx8mmT', '1105080', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rubber Oil 35mmIDx52ODmx8mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1186, 'Seal,Oil Rbbr 85mmx110mmx12m', '1105081', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal,Oil Rbbr 85mmx110mmx12m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1187, 'Seal, Oil 40mmx62mmx10mm', '1105082', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 40mmx62mmx10mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1188, 'Seal, Oil 40mm x 62mm x 7mm', '1105082A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 40mm x 62mm x 7mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1189, 'Seal, Oil 40mmx62mmx8mm', '1105082B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 40mmx62mmx8mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1190, 'Seal, Oil 42mmIDx68mmODx9mmT', '1105084', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 42mmIDx68mmODx9mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1191, 'Seal, Oil 30mmIDx42mmODx8mmT', '1105085', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 30mmIDx42mmODx8mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1192, 'Seal, Oil 50mmx90mmx10mm', '1105086', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 50mmx90mmx10mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1193, 'Seal, Oil 37mmx62mmx8mm', '1105087', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 37mmx62mmx8mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1194, 'Seal, Oil 33mmx52mmx9mm', '1105089', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 33mmx52mmx9mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1195, 'Seal, Oil Rubber 8Tx25IDx38O', '1105090A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Rubber 8Tx25IDx38O', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1196, 'Seal, Rubber 48mmx63mmx7.6mm', '1105091', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rubber 48mmx63mmx7.6mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1197, 'Seal, Oil 30mmx45mmx7mm', '1105093', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 30mmx45mmx7mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1198, 'Seal, Oil 70mmx120mmx7mm', '1105096A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 70mmx120mmx7mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1199, 'Seal, Oil 50mmIDx60mmODx8mmT', '1105097', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 50mmIDx60mmODx8mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1200, 'Seal, Oil 20mmx32mmx8mm', '1105100', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 20mmx32mmx8mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1201, 'Seal, Oil 40mmx120mmx10mm', '1105101', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 40mmx120mmx10mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1202, 'Seal, Oil 28mmIDx47mmODx7mmT', '1105102', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 28mmIDx47mmODx7mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1203, 'Seal, Oil 25mmIDx45mmODx10mm', '1105103', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 25mmIDx45mmODx10mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1204, 'Seal, Mono 100mmx120x22mmT', '1105104', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Mono 100mmx120x22mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1205, 'Seal, Oil 8mmTx20mmIDx42mmOD', '1105105', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 8mmTx20mmIDx42mmOD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1206, 'Seal, Oil 20mmx42mmx7mm', '1105105A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 20mmx42mmx7mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1207, 'Seal, Oil 100mmx120mmx11mm', '1105107', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 100mmx120mmx11mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1208, 'Seal, Oil Rubber 100mmIDx120mmODx10mmT', '1105107A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Rubber 100mmIDx120mmODx10mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1209, 'Seal, Oil 140mmx165mmx15mm', '1105108', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 140mmx165mmx15mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1210, 'Seal,Oil Rubber114IDx140ODx1', '1105108B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal,Oil Rubber114IDx140ODx1', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1211, 'Seal, Spacer Carbon Ring', '1105110', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Spacer Carbon Ring', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1212, 'Seal, Oil 40mmx62mmx8mm', '1105111', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 40mmx62mmx8mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1213, 'Seal, Oil Rubber 55x75x12', '1105112', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Rubber 55x75x12', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1214, 'Seal, Rbbr Oil 40mmx80mmx8mm', '1105113', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rbbr Oil 40mmx80mmx8mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1215, 'Seal, Oil 30mmx52mmx10mm', '1105114A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 30mmx52mmx10mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1216, 'Seal, Oil 85mmx110mmx3mm', '1105118', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 85mmx110mmx3mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1217, 'Seal, Oil 110mmODx90mmIDx5mmT', '1105119A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 110mmODx90mmIDx5mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1218, 'Seal, Oil 64mmx80mmx8mm', '1105120', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 64mmx80mmx8mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1219, 'Seal, Oil 20mmx42mmx7mm', '1105124', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 20mmx42mmx7mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1220, 'Seal, Oil 125mmx160mmx14mm', '1105125', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 125mmx160mmx14mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1221, 'Seal, Oil Rbbr 63mmx89x12mmT', '1105126', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Rbbr 63mmx89x12mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1222, 'Seal, Oil 40mmx60mmx8mm', '1105127', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 40mmx60mmx8mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1223, 'Seal, Oil 40mmx60mmx7mm', '1105127A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 40mmx60mmx7mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1224, 'Seal, Oil Rbbr 57mmx82mmx10m', '1105128', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Rbbr 57mmx82mmx10m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1225, 'Seal, Oil 85mmx110mmx13mm', '1105132', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 85mmx110mmx13mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1226, 'Seal, Mono 240mmx270mmx17mmT', '1105133', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Mono 240mmx270mmx17mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1227, 'Seal, Rotary 25mmx47mmx13mm', '1105135', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rotary 25mmx47mmx13mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1228, 'Seal, Oil 35mmx47mmx12mm', '1105136A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 35mmx47mmx12mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1229, 'Seal, Oil TC 32mmx50mmx10mm', '1105137', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil TC 32mmx50mmx10mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1230, 'Seal, Pump NNP 56/70', '1105139', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Pump NNP 56/70', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1231, 'Seal, Pump NNP 36/36', '1105140', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Pump NNP 36/36', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1232, 'Seal, Oil 25mmx35mmx7mm', '1105143A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 25mmx35mmx7mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1233, 'Seal, Oil 130mmx160mmx12mm', '1105147', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 130mmx160mmx12mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1234, 'Seal, Oil 60mmx100mmx10mm', '1105148', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 60mmx100mmx10mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1235, 'Seal, Oil 12mmx13mmx6mm', '1105151', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 12mmx13mmx6mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1236, 'Seal, Mntd w/Hermetique AS81', '1105153', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Mntd w/Hermetique AS81', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1237, 'Seal, Rubber Oil 54mmIDx65mmOD', '1105155B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rubber Oil 54mmIDx65mmOD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1238, 'Seal, Oil 45mmx68mmx12mm', '1105156', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 45mmx68mmx12mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1239, 'Seal, Oil 43mmIDx68mmODx8mmT', '1105156B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 43mmIDx68mmODx8mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1240, 'Seal, Oil 45mmIDx68mmODx10mm', '1105157', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 45mmIDx68mmODx10mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1241, 'Seal, Oil 135mmx160mmx13mm', '1105158', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 135mmx160mmx13mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1242, 'Seal, Oil 40mmx64mmx8mm', '1105164', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 40mmx64mmx8mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1243, 'Seal, Oil 35mmx60mmx8mm', '1105165', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 35mmx60mmx8mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1244, 'Seal, Oil Rubber 52IDx72ODx10T', '1105167', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Rubber 52IDx72ODx10T', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1245, 'Seal, Oil 114mmx140mmx12mm', '1105168', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 114mmx140mmx12mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1246, 'Oil Seal 114x140x13mm', '1105168A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil Seal 114x140x13mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1247, 'Seal, Rubber U-Cap 3.7IDx8/8.5x7mmL', '1105169', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rubber U-Cap 3.7IDx8/8.5x7mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1248, 'Seal, Rubber 119x100x15.5mmT', '1105170C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rubber 119x100x15.5mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1249, 'Seal, Rubber 99mmIDx121mmODx15.5mmThk', '1105170D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rubber 99mmIDx121mmODx15.5mmThk', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1250, 'Seal, Oil 100mmx127mmx12mm', '1105172', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 100mmx127mmx12mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1251, 'Seal, Oil 70mmx95mmx12mm', '1105173', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 70mmx95mmx12mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1252, 'Seal, Oil 50mmIDx65mmODx8mmT', '1105174', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 50mmIDx65mmODx8mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1253, 'Seal, Mono 60x80x12, 2000psi', '1105175', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Mono 60x80x12, 2000psi', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1254, 'Seal,Oil Rubber 35mmx62mmx10', '1105175A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal,Oil Rubber 35mmx62mmx10', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1255, 'Seal,Rubber Oil 60mmIDx82ODx12', '1105181', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal,Rubber Oil 60mmIDx82ODx12', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1256, 'Seal, Oil 35mmx51mmx8mm', '1105181B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 35mmx51mmx8mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1257, 'Seal, Oil 35mmIDx47mmODx8mmT', '1105183A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 35mmIDx47mmODx8mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1258, 'Seal, Oil 20mmx30mmx7mm', '1105185', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 20mmx30mmx7mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1259, 'Seal, Oil 20mmx35mmx7mm', '1105186', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 20mmx35mmx7mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1260, 'Seal, Oil 20mmx35mmx10mm', '1105186B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 20mmx35mmx10mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1261, 'Seal, Oil 15mmx24mmx5mm', '1105187', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 15mmx24mmx5mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1262, 'Seal, Oil 17mmx30mmx7mm', '1105188', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 17mmx30mmx7mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1263, 'Seal, Oil 25mmx52mmx7mm', '1105189', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 25mmx52mmx7mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1264, 'Seal,oil rubber 25mmx52mmx10', '1105189A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal,oil rubber 25mmx52mmx10', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1265, 'Seal, Oil 180mmx160mmx13mm', '1105190', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 180mmx160mmx13mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1266, 'Seal, Rbbr Ring46.5x34x20/19', '1105191', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rbbr Ring46.5x34x20/19', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1267, 'Seal, Oil 14mmx20mmx5mm', '1105194', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 14mmx20mmx5mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1268, 'Seals,oil rubber 14mmx28mmx8', '1105194B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seals,oil rubber 14mmx28mmx8', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1269, 'Seal, Oil 43mmx58mmx7mm', '1105197', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 43mmx58mmx7mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1270, 'Seal, Oil 45mmx60mmx8mm', '1105198', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 45mmx60mmx8mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1271, 'Seal, Rubber 159IDx183ODx12mm', '1105200', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rubber 159IDx183ODx12mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1272, 'Seal, Oil 65mmx88mmx12mm', '1105201', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 65mmx88mmx12mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1273, 'Seal, Rubber Oil 65mmIDx85mmOD', '1105201B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rubber Oil 65mmIDx85mmOD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1274, 'Seal, Oil 35mmx50mmx10mm', '1105203', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 35mmx50mmx10mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1275, 'Seal, Oil 24mmIDx40mmODx8mmT', '1105206', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 24mmIDx40mmODx8mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1276, 'Seal, Rubber Oil 30mmIDx48mmOD', '1105207', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rubber Oil 30mmIDx48mmOD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1277, 'Seal, Oil 30mmx47mmx9mm', '1105213', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 30mmx47mmx9mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1278, 'Seal, Oil 20mmx42mmx6mm', '1105214', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 20mmx42mmx6mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1279, 'Seal, Oil 20mmx40mmx6mm', '1105214A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 20mmx40mmx6mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1280, 'Seal, Oil 36mmx47mmx17mm', '1105216', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 36mmx47mmx17mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1281, 'Seal, Oil 64mmx90mmx13mm', '1105218', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 64mmx90mmx13mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1282, 'Seal, Oil Rubber 68x90x10mmT', '1105219', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Rubber 68x90x10mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1283, 'Seal, Oil 8mmTx45mmIDx65mmOD', '1105220', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 8mmTx45mmIDx65mmOD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1284, 'Seal, Oil 45mmx65mmx7mm', '1105220A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 45mmx65mmx7mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1285, 'Seal, Oil 30mmIDx40mmODx7mmT', '1105221A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 30mmIDx40mmODx7mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1286, 'Seal, Oil Rbbr 25IDx38ODx7.8', '1105222A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Rbbr 25IDx38ODx7.8', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1287, 'Seal, Oil 35mmx45mmx8mm', '1105223A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 35mmx45mmx8mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1288, 'Seal, Oil Rubber 35mmx50mmx8mm', '1105223B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Rubber 35mmx50mmx8mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1289, 'Seal, Oil 90mmIDx115mmODx12m', '1105225A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 90mmIDx115mmODx12m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1290, 'Seal, Oil Rubber 90mmIDx115mmO', '1105226B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Rubber 90mmIDx115mmO', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1291, 'Seal, Oil 75mmx110mmx10mm', '1105231', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 75mmx110mmx10mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1292, 'Seal, Oil 39mmx50mmx8mm', '1105234', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 39mmx50mmx8mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1293, 'Seal, Oil 25mmIDx38mmODx10mm', '1105236A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 25mmIDx38mmODx10mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1294, 'Seal, Rubber Oil 55mmIDx75mmOD', '1105238A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rubber Oil 55mmIDx75mmOD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1295, 'Seal, Oil  30mmx47mmx14mm', '1105239', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil  30mmx47mmx14mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1296, 'Seal, Oil 45x60x12mm', '1105241A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 45x60x12mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1297, 'Seal, Oil 35mmx47mmx7mm', '1105243', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 35mmx47mmx7mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1298, 'Wiper, Super 5000, 63x73x7mm', '1105245', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wiper, Super 5000, 63x73x7mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1299, 'Seal, Oil Rubber 72ODx50IDx12', '1105247', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Rubber 72ODx50IDx12', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1300, 'Seal, Oil Rubber 50mmIDx73mmODx12mmT', '1105247A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Rubber 50mmIDx73mmODx12mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1301, 'Seal, Ultra 63mmIDx78mmODx16.5', '1105248B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Ultra 63mmIDx78mmODx16.5', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1302, 'Seal, Oil 25mmx37mmx7mm', '1105254A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 25mmx37mmx7mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1303, 'Seal, Oil Rbbr 90IDx115ODx13', '1105255', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Rbbr 90IDx115ODx13', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1304, 'Seal, Oil Rubber 15x32x7', '1105256', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Rubber 15x32x7', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1305, 'Seal, Oil Rubber 100x120x12', '1105260', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Rubber 100x120x12', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1306, 'Seal, Oil Rubber 38x50x8', '1105261', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Rubber 38x50x8', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1307, 'Seal, Oil 25mmIDx37.7mmODx12', '1105263', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 25mmIDx37.7mmODx12', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1308, 'Seal,Rubber 85mmIDx100.5mmODx8mmT', '1105264', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal,Rubber 85mmIDx100.5mmODx8mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1309, 'Seal, Oil 30mmx47mmx8mm', '1105278', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 30mmx47mmx8mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1310, 'Seal, Oil 160mmIDx190mmODx15mm', '1105281', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 160mmIDx190mmODx15mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1311, 'Seal, Rubber 14mmφIDx51mmφOD', '1105282A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rubber 14mmφIDx51mmφOD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1312, 'RbberOilSealw/Sprng290mmx265', '1105284', 'Inventory', NULL, NULL, NULL, NULL, 0, 'RbberOilSealw/Sprng290mmx265', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1313, 'Seal, Oil Rubber 130X160X13', '1105285', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Rubber 130X160X13', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1314, 'Seal, Oil Rubber 70x90x10mmT', '1105286A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Rubber 70x90x10mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1315, 'Seal,Oil Rubber 70mmx92mmx12', '1105289A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal,Oil Rubber 70mmx92mmx12', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1316, 'Seal, Oil Rubber 16mmx28x7mm', '1105291', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Rubber 16mmx28x7mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1317, 'Oil,Seal 16mmIDx26mmODx7mmT', '1105291A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil,Seal 16mmIDx26mmODx7mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1318, 'Seal, Oil Rubber 69mmIDx89mmOD', '1105294', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Rubber 69mmIDx89mmOD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1319, 'Seal,Oil Rubber 44IDx650ODX1', '1105297', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal,Oil Rubber 44IDx650ODX1', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1320, 'Seal, Oil 17mmx28mmx7mm', '1105300', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 17mmx28mmx7mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1321, 'Seal, Oil 10mmTx35mmIDx51mmO', '1105301', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 10mmTx35mmIDx51mmO', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1322, 'Seal, Oil 30mmx48mmx7mm', '1105302', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 30mmx48mmx7mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1323, 'Seal, RubberDamper 83x69.7x8', '1105304', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, RubberDamper 83x69.7x8', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1324, 'Seal, Rubber Oil 25mmIDx40mmODx8mmT', '1105308A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rubber Oil 25mmIDx40mmODx8mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1325, 'Seal, Oil 25mmx40mmx6mm', '1105309A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 25mmx40mmx6mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1326, 'Seal, Oil49.9x37.0w/steel ca', '1105311', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil49.9x37.0w/steel ca', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1327, 'Seal, Cover 22x28.5mmx4.8mmT', '1105314', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Cover 22x28.5mmx4.8mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1328, 'Seal, Oil 15mmx30mmx7mm', '1105402', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 15mmx30mmx7mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1329, 'Seal, Rbbr 20φID,10bars,60°C', '1105406A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rbbr 20φID,10bars,60°C', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1330, 'Seal, Rbbr 8.5ID,10bars,60°C', '1105406B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rbbr 8.5ID,10bars,60°C', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1331, 'Seal, Oil 10 bars,60°C,9.8ID', '1105407A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 10 bars,60°C,9.8ID', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1332, 'Seal, Oil with spring', '1105407B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil with spring', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1333, 'Seal, Oil w/ spring 40x30x7mmT', '1105408', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil w/ spring 40x30x7mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1334, 'Seal, Oil w/spring 10bars,70°C', '1105410A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil w/spring 10bars,70°C', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1335, 'Seal, Oil 35mmIDx54mmODx8mmT', '1105411', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 35mmIDx54mmODx8mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1336, 'Seal, Oil 40.2mmx24.8mmx10mmT', '1105413', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 40.2mmx24.8mmx10mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1337, 'Seal, Oil 40.3mmx17.3mmx8.6m', '1105414', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 40.3mmx17.3mmx8.6m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1338, 'Seal, Oil 51.5mmx37mmx10mmT', '1105415', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 51.5mmx37mmx10mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1339, 'Seal, Rubber 36mmx8.5mmx12mmT', '1105416', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rubber 36mmx8.5mmx12mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1340, 'Seal, Oil Rubber 29mmIDx40mmx10mmT', '1105420B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Rubber 29mmIDx40mmx10mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1341, 'Seal, Oil 65mmODx44mmIDx11mmT', '1105421', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 65mmODx44mmIDx11mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1342, 'Seal, Rubber Oil 20mmIDx47mmOD', '1105422', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rubber Oil 20mmIDx47mmOD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1343, 'Seal, Oil Rubber 300mmIDx340mm', '1105423', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Rubber 300mmIDx340mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1344, 'Seal, Rubber 68mmIDx80.5mmODx6', '1105425', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Rubber 68mmIDx80.5mmODx6', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1345, 'Seal, Oil 18mmx35mmx8mmT', '1105426', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 18mmx35mmx8mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1346, 'Seal, Oil Rbbr double lips w/s', '1105427', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Rbbr double lips w/s', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1347, 'Seal,Oil Rubber 63mmIDx90mmx12mmT', '1105428', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal,Oil Rubber 63mmIDx90mmx12mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1348, 'Sealing, Libyrant 37x62mmx8m', '1106004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sealing, Libyrant 37x62mmx8m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1349, 'Sealing, Type Quide 5130', '1106007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sealing, Type Quide 5130', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1350, 'Sealing, Upper Rbbr 52x5mmx6', '1106012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sealing, Upper Rbbr 52x5mmx6', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1351, 'Screw, Cap Allen 8mmφx40Lx1.', '1107005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, Cap Allen 8mmφx40Lx1.', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1352, 'Bolt, Hex Socket Hd 5/8\"x3\"L', '1107007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Socket Hd 5/8\"x3\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1353, 'Screw, Cap Allen ½\"φx2½\"L UNC', '1107011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, Cap Allen ½\"φx2½\"L UNC', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1354, 'Screw, Cap Allen 22x60mmLx2.', '1107012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, Cap Allen 22x60mmLx2.', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1355, 'Bolt, Hllw HexSS 14φx40Lx2.0', '1107016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hllw HexSS 14φx40Lx2.0', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1356, 'Screw, Cap Allen 5/8\"φx3\"L U', '1107017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, Cap Allen 5/8\"φx3\"L U', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1357, 'Screw, Hex Socket Set 12mm D', '1107020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, Hex Socket Set 12mm D', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1358, 'Screw, Hex SocketSet 16mmx25', '1107021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, Hex SocketSet 16mmx25', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1359, 'Bolt, Hex Socket HD 16mmx25m', '1107021A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Socket HD 16mmx25m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1360, 'Screw, Set Square Hd 3/4\"x1\"', '1107026', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, Set Square Hd 3/4\"x1\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1361, 'Bolt, Hex Socket Hd 20mmx53m', '1107030', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Socket Hd 20mmx53m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1362, 'Screw, Counter Sunk Head Cross', '1107039', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, Counter Sunk Head Cross', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1363, 'Screw, Allen Hd Set 1/2\"x 3/', '1107069', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, Allen Hd Set 1/2\"x 3/', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1364, 'Screw, Allen Hd Set 1/2\"x 1\"', '1107070', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, Allen Hd Set 1/2\"x 1\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1365, 'Shaft, Flexible 29mmx15mmx32\'L', '1111001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Shaft, Flexible 29mmx15mmx32\'L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1366, 'Shafting, Roller 160φx3600L', '1111011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Shafting, Roller 160φx3600L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1367, 'Shafting, Steel 60mm x 2mtrs', '1111029', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Shafting, Steel 60mm x 2mtrs', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1368, 'Shafting, Hex Steel 46mmx3M', '1111031', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Shafting, Hex Steel 46mmx3M', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1369, 'Shafting, Steel Hex 16mmφx3m', '1111032', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Shafting, Steel Hex 16mmφx3m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1370, 'Shafting, Steel 1¼\"φx20\'', '1111063B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Shafting, Steel 1¼\"φx20\'', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1371, 'Spring, Lever PN-066111', '1111074', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Lever PN-066111', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1372, 'Shaft,Arm Rcker crnk 062031', '1111103', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Shaft,Arm Rcker crnk 062031', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1373, 'Shaft, Arm Rocker Crank 0620', '1111104', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Shaft, Arm Rocker Crank 0620', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1374, 'Shaft, Arm Rocker PN-062042', '1111114', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Shaft, Arm Rocker PN-062042', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1375, 'Shaft, Feed Rocker PN-074011', '1111115', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Shaft, Feed Rocker PN-074011', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1376, 'Shafting, Round Steel Solid 75', '1111138', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Shafting, Round Steel Solid 75', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1377, 'Shafting, Round Steel Solid 60', '1111139', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Shafting, Round Steel Solid 60', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1378, 'Shaft,Axle Cane Car 100mmφx920mmL w/lck  nut', '1111141', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Shaft,Axle Cane Car 100mmφx920mmL w/lck  nut', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1379, 'Split, Steel Sleeve 90x140x220', '1112007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Split, Steel Sleeve 90x140x220', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1380, 'Sleeve, Pump Casing 230mmx25', '1112011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sleeve, Pump Casing 230mmx25', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1381, 'Sleeve, Pump Casing 230mmx15', '1112012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sleeve, Pump Casing 230mmx15', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1382, 'Sleeve, Pump Casing 230mmx35', '1112013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sleeve, Pump Casing 230mmx35', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1383, 'Sleeve, Ring 027-1028', '1112029', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sleeve, Ring 027-1028', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1384, 'Sleeves, Brg Housing 155x135', '1112035', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sleeves, Brg Housing 155x135', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1385, 'Sleeve, Brg Hsng 155x135x200', '1112039', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sleeve, Brg Hsng 155x135x200', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1386, 'Paste, Soldering', '1113002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paste, Soldering', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1387, 'Spring for skatoskalo TE-05', '1115005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring for skatoskalo TE-05', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1388, 'Spring, Spiral 2.3mm.x 50mm.', '1115010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Spiral 2.3mm.x 50mm.', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1389, 'Spring, Presser Small 102101', '1115028', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Presser Small 102101', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1390, 'Spring, Presser Regulator Screw PN-102171', '1115029', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Presser Regulator Screw PN-102171', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1391, 'Spring, 25mm.x 205mm.', '1115033', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, 25mm.x 205mm.', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1392, 'Spring, 92mm.x68mm.x92mm. co', '1115037', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, 92mm.x68mm.x92mm. co', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1393, 'Spring SA Turbo Alternator', '1115043', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring SA Turbo Alternator', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1394, 'Spring, Startup Valve 30x94m', '1115045', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Startup Valve 30x94m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1395, 'Spring,Brake 140kg.PN#629840', '1115047A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring,Brake 140kg.PN#629840', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1396, 'Spring, Coil 28mm.x36mmx276m', '1115049', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Coil 28mm.x36mmx276m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1397, 'Spring, Comprsn 2.8 35.6x30m', '1115051', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Comprsn 2.8 35.6x30m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1398, 'Spring, ComprnConical Std Co', '1115052', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, ComprnConical Std Co', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1399, 'Spring, Mark 21 #AS-128', '1115059', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Mark 21 #AS-128', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1400, 'Spring, Compression 42x51mmC', '1115062', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Compression 42x51mmC', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1401, 'Stator, RHD 30-2200', '1116003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Stator, RHD 30-2200', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1402, 'Stator, Rubber PN-2200/CB-05', '1116011A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Stator, Rubber PN-2200/CB-05', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25');
INSERT INTO `items` (`id`, `item_name`, `item_code`, `item_type`, `item_vendor_id`, `item_uom_id`, `item_reorder_point`, `item_category_id`, `item_quantity`, `item_sales_description`, `item_purchase_description`, `item_selling_price`, `item_cost_price`, `item_cogs_account_id`, `item_income_account_id`, `item_asset_account_id`, `created_at`) VALUES
(1403, 'Stator, Rubber #22 CAA12H1H5', '1116014A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Stator, Rubber #22 CAA12H1H5', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1404, 'Stator, Rubber #2200 SB07B5/#110', '1116015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Stator, Rubber #2200 SB07B5/#110', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1405, 'Stator, Rubber #2200 CGF 113 R', '1116016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Stator, Rubber #2200 CGF 113 R', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1406, 'Stator, PN-3005', '1116017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Stator, PN-3005', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1407, 'Tension, Thread Assy. 065241A', '1117001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tension, Thread Assy. 065241A', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1408, 'Tension, Thread Assy. 065151A', '1117002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tension, Thread Assy. 065151A', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1409, 'Post, Tension PN-065151', '1117002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Post, Tension PN-065151', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1410, 'Tape, Teflon ½\"x.075mmTx10mL', '1120002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tape, Teflon ½\"x.075mmTx10mL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1411, 'Tape, Teflon 3/4\"x.075mmTx10mL', '1120003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tape, Teflon 3/4\"x.075mmTx10mL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1412, 'Assy. Universal Joint #GU109', '1121005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Assy. Universal Joint #GU109', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1413, 'Head, Tool Skatoskalo TE-05', '1123001A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Head, Tool Skatoskalo TE-05', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1414, 'Tube, Rubber w/Nylon 49x61x1', '1123006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tube, Rubber w/Nylon 49x61x1', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1415, 'Plate, Trash Pitch: 30mmx405mmW (TP3)', '1124003A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plate, Trash Pitch: 30mmx405mmW (TP3)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1416, 'Plate, Trash (TP1)', '1124011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plate, Trash (TP1)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1417, 'Plate, Trash (TP5)', '1124013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plate, Trash (TP5)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1418, 'Tube, Torque long', '1125019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tube, Torque long', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1419, 'Tube, PU-4 Black', '1125021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tube, PU-4 Black', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1420, 'Hose, Plastic PUN-H-8X1.25-BL', '1125023A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Plastic PUN-H-8X1.25-BL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1421, 'Hose, Plastic 4mmφ 10 bars', '1125024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Plastic 4mmφ 10 bars', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1422, 'Hose, Plastic PUN-H-6X1-BL', '1125033A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Plastic PUN-H-6X1-BL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1423, 'Hose, Plastic Nylon Tube 6mmφ', '1125033B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Plastic Nylon Tube 6mmφ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1424, 'Hose, Plastic 12mm dia. (OD) Tube', '1125035', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Plastic 12mm dia. (OD) Tube', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1425, 'Valve, Check  5\"', '1127003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Check  5\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1426, 'Valve, Gate 2\"φ', '1127004A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Gate 2\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1427, 'Valve, Butterfly 4\"φx150psi', '1127005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Butterfly 4\"φx150psi', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1428, 'Valve, Gate Brass 4\"φx125psi', '1127008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Gate Brass 4\"φx125psi', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1429, 'Valve, Swing Check 1/2\"', '1127012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Swing Check 1/2\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1430, 'Valve, Gate 3/4\"φ, 200psi', '1127013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Gate 3/4\"φ, 200psi', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1431, 'Valve, Gate Brass 2\"φ', '1127016B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Gate Brass 2\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1432, 'Valve, Globe 8\"x150psi', '1127018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Globe 8\"x150psi', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1433, 'Valve, Ball HD 1½\"φ', '1127020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Ball HD 1½\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1434, 'Valve, Gate 4\"φ', '1127023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Gate 4\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1435, 'Valve, Gate 4\"φ (Non-Rising St', '1127023D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Gate 4\"φ (Non-Rising St', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1436, 'Valve, Globe 5\"', '1127024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Globe 5\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1437, 'Valve, Gate 6\"', '1127026', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Gate 6\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1438, 'Valve, Gate 6\"φ (Rising Stem)', '1127026A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Gate 6\"φ (Rising Stem)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1439, 'Valve, Flow Control AS2200/AS2', '1127027A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Flow Control AS2200/AS2', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1440, 'Valve, Gate Knife 50mmx150ps', '1127031', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Gate Knife 50mmx150ps', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1441, 'Valve, Non Return NU SD', '1127043', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Non Return NU SD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1442, 'Valve, Check 6\"x150psi', '1127044', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Check 6\"x150psi', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1443, 'Valve, Solenoid 1/4\" 60Hz', '1127050', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Solenoid 1/4\" 60Hz', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1444, 'Valve, Ball 1/2\"φ', '1127051', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Ball 1/2\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1445, 'Valve, 3-way#110V/60 complet', '1127052', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, 3-way#110V/60 complet', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1446, 'Valve, 3Way Westinghouse', '1127053', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, 3Way Westinghouse', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1447, 'Valve, Gate 8\"φx150psi', '1127056', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Gate 8\"φx150psi', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1448, 'Valve, Butterfly 3\"φx150psig', '1127063', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Butterfly 3\"φx150psig', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1449, 'Valve, Butterfly 3\"φ', '1127063B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Butterfly 3\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1450, 'Valve, Check 6\"φ', '1127065A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Check 6\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1451, 'Valve, for Temporary Contrl', '1127066', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, for Temporary Contrl', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1452, 'Valve, Ball 3/4\"φ PN-534305', '1127071', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Ball 3/4\"φ PN-534305', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1453, 'Valve, Butterfly 200mmx150 S', '1127072', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Butterfly 200mmx150 S', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1454, 'Valve, Butterfly 2\"φ', '1127072A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Butterfly 2\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1455, 'Valve,Feed Encoder Cable 48x', '1127079', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve,Feed Encoder Cable 48x', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1456, 'Actuator, Feed ValvePN22840031', '1127079A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Actuator, Feed ValvePN22840031', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1457, 'Valve, Intercooler Safety 14', '1127081', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Intercooler Safety 14', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1458, 'Valve, Butterfly 2\"φx150psig', '1127083', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Butterfly 2\"φx150psig', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1459, 'Valve, Butterfly 2½\"φx150psi', '1127083A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Butterfly 2½\"φx150psi', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1460, 'Needle ValveSS-316 1/4\"NPT', '1127090', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Needle ValveSS-316 1/4\"NPT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1461, 'Valve, Motor Driven 110VAC', '1127095', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Motor Driven 110VAC', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1462, 'Valve, Bronze Check1-1/2\"cra', '1127097', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Bronze Check1-1/2\"cra', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1463, 'Valve, Gate 3\"φ @ 150psi', '1127099B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Gate 3\"φ @ 150psi', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1464, 'Valve, Steam DN32 Complete', '1127117', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Steam DN32 Complete', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1465, 'Valve, Water DN 20 complete', '1127118', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Water DN 20 complete', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1466, 'Valve, Butterfly 4\"φ', '1127119', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Butterfly 4\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1467, 'Valve, Butterfly 6\"φx150psig', '1127132', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Butterfly 6\"φx150psig', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1468, 'Valve, Butterfly 6\"φ', '1127132B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Butterfly 6\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1469, 'Valve, Shute #39127261', '1127150', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Shute #39127261', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1470, 'Valve, Ball 1\"φ Forge Brass', '1127153A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Ball 1\"φ Forge Brass', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1471, 'Valve, Ball 1\"φ HD', '1127162', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Ball 1\"φ HD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1472, 'Valve, Ball PN-1692201', '1127181', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Ball PN-1692201', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1473, 'Valve, Electro', '1127182', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Electro', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1474, 'Valve,Control Directional HD', '1127192', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve,Control Directional HD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1475, 'Valve, Ball VAPB-3/4-F-40-FO3', '1127201', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Ball VAPB-3/4-F-40-FO3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1476, 'Valve, Access/Charging 1/4\"', '1127206', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Access/Charging 1/4\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1477, 'Valve, Gate 12\"φ', '1127207', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Gate 12\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1478, 'Valve, Unloader PN 39840418', '1127209', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Unloader PN 39840418', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1479, 'Valve, Check 3\"φ x 150lb.', '1127210', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Check 3\"φ x 150lb.', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1480, 'Valve, Gate 3\"φ x 150lb.', '1127211', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Gate 3\"φ x 150lb.', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1481, 'Valve, Check 1-1/2\"', '1127212', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Check 1-1/2\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1482, 'Washer, PN-065531', '1129007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer, PN-065531', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1483, 'Spring, Washer 3W5', '1129010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Washer 3W5', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1484, 'Washer, Lock 5/16\"φ', '1129012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer, Lock 5/16\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1485, 'Washer, PN-15/64W20101', '1129014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer, PN-15/64W20101', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1486, 'Washer, Lock 1/2\"φ', '1129015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer, Lock 1/2\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1487, 'Washer, Lock 5/8\"φ', '1129016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer, Lock 5/8\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1488, 'Washer, Lock 7/8\"φ', '1129018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer, Lock 7/8\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1489, 'Washer, Lock 1\"', '1129019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer, Lock 1\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1490, 'Washer, Plain 9/16\"φ', '1129021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer, Plain 9/16\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1491, 'Washer, Lock 1/4\"φ', '1129024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer, Lock 1/4\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1492, 'Washer, Lock 3/8\"', '1129025', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer, Lock 3/8\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1493, 'Washer, Lock 7/16\"φ', '1129026', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer, Lock 7/16\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1494, 'Washer, Plain 1/4\"φ', '1129053', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer, Plain 1/4\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1495, 'Washer, Plain 5/16\"φ', '1129054', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer, Plain 5/16\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1496, 'Washer, Plain 3/8\"φ', '1129055', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer, Plain 3/8\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1497, 'Washer, Plain 7/16\"φ', '1129056', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer, Plain 7/16\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1498, 'Washer, Plain 1/2\"φ', '1129057', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer, Plain 1/2\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1499, 'Washer, Plain 5/8\"φ', '1129058', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer, Plain 5/8\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1500, 'Washer, Plain 3/4\"φ', '1129059', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer, Plain 3/4\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1501, 'Washer, Plain 7/8\"φ', '1129060', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer, Plain 7/8\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1502, 'Washer, Plain 1\"', '1129061', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer, Plain 1\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1503, 'Lock washer 17', '1129066', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lock washer 17', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1504, 'Washer, Lock 9/16\"φ', '1129067', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer, Lock 9/16\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1505, 'Washer, Tab 17', '1129069', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer, Tab 17', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1506, 'Washer, Safety 12', '1129070', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer, Safety 12', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1507, 'Washer, Safety 16', '1129071', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer, Safety 16', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1508, 'Washer, Tab 13', '1129072', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer, Tab 13', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1509, 'Washer, Spring PN-7/32W05201', '1129073', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer, Spring PN-7/32W05201', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1510, 'Washer, Spring PN-5/16W05201', '1129074', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer, Spring PN-5/16W05201', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1511, 'Electrode, Welding #7018 5/32\"', '1131006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Electrode, Welding #7018 5/32\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1512, 'Electrode, Welding Champer Rod 1/8\"φ', '1131008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Electrode, Welding Champer Rod 1/8\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1513, 'Elctrde, Welding #188 3/32\"φ', '1131009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elctrde, Welding #188 3/32\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1514, 'Electrode, Welding #7018 1/8\"φ', '1131012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Electrode, Welding #7018 1/8\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1515, 'Electrode,Welding Eutectec1/8\"', '1131013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Electrode,Welding Eutectec1/8\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1516, 'Electrode, Wldng XHD 646 1/8\"φ', '1131014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Electrode, Wldng XHD 646 1/8\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1517, 'Rod, A/S Broonze #24 1/8\"φ', '1131015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rod, A/S Broonze #24 1/8\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1518, 'Elctrd, Wldng RollMatrix5/32\"φ', '1131016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elctrd, Wldng RollMatrix5/32\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1519, 'Elctrd, Wldng HS #400 1/8\"Φ', '1131017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elctrd, Wldng HS #400 1/8\"Φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1520, 'Electrode, Welding CI #4 1/8\"φ', '1131018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Electrode, Welding CI #4 1/8\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1521, 'Elctrde, Welding HS 12IP 1/8', '1131019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elctrde, Welding HS 12IP 1/8', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1522, 'Elctrde, Welding A/S #4-IP 1/8', '1131020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elctrde, Welding A/S #4-IP 1/8', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1523, 'Elctrd, Wldng RollMatrix 1/8\"φ', '1131021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elctrd, Wldng RollMatrix 1/8\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1524, 'Elctrde, WldngAzucal SW#70 1/8', '1131022', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elctrde, WldngAzucal SW#70 1/8', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1525, 'Electrode, Welding 1/8\"φ MG 600', '1131024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Electrode, Welding 1/8\"φ MG 600', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1526, 'Electrode,Welding SS#1711 3/32', '1131026', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Electrode,Welding SS#1711 3/32', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1527, 'Electrode, Welding #6013 1/8\"φ', '1131028', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Electrode, Welding #6013 1/8\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1528, 'Electrode, Welding #6013 5/32\"', '1131029', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Electrode, Welding #6013 5/32\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1529, 'Electrode, Welding SS #1711 3/16\"φ', '1131032', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Electrode, Welding SS #1711 3/16\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1530, 'Electrode, Welding SS#275 1/8\"', '1131034', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Electrode, Welding SS#275 1/8\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1531, 'Glass, Welding Clear1/8\"x2\"x4\"', '1131080', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Glass, Welding Clear1/8\"x2\"x4\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1532, 'Glass, Welding Dark #12', '1131081', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Glass, Welding Dark #12', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1533, 'Electrode, Welding SS#188 1/8\"', '1131082', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Electrode, Welding SS#188 1/8\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1534, 'Electrode, Welding SS #1711 1/8\"φ', '1131083', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Electrode, Welding SS #1711 1/8\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1535, 'Elctrd, Wldng CI#8-60 1/8\"φ', '1131103', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elctrd, Wldng CI#8-60 1/8\"φ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1536, 'Stone, GrindingWhl 12x1-3/4x2', '1132003A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Stone, GrindingWhl 12x1-3/4x2', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1537, 'Cut-off Wheels 7\"ODx1/8\"Tx7/8\"ID Standard ««', '1132004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cut-off Wheels 7\"ODx1/8\"Tx7/8\"ID Standard ««', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1538, 'Grinding Wheels 4\"x15/64\"x5/8\" Premium «««', '1132005A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Grinding Wheels 4\"x15/64\"x5/8\" Premium «««', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1539, 'Disc, Grinding 7\"x1/4\"x7/8\"', '1132007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Disc, Grinding 7\"x1/4\"x7/8\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1540, 'Cut-off Wheels 4\"ODx3/32\"Tx5/8\"ID Standard ««', '1132011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cut-off Wheels 4\"ODx3/32\"Tx5/8\"ID Standard ««', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1541, 'Stone, Conical grinding Point 6mmx32mmx32mmH', '1132013A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Stone, Conical grinding Point 6mmx32mmx32mmH', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1542, 'Wheel, Tangent Mill Turbne &', '1132015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wheel, Tangent Mill Turbne &', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1543, 'Wheel, Gear N2/1 4 PR Cmprsn', '1132019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wheel, Gear N2/1 4 PR Cmprsn', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1544, 'Disc, Abrsve4\" Grit:24, 100mmφ', '1132022', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Disc, Abrsve4\" Grit:24, 100mmφ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1545, 'Stone, Cyl. Grinding Point', '1132023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Stone, Cyl. Grinding Point', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1546, 'Stone,Cylndrical Grinding Point6mmx25mmx25mmL', '1132023A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Stone,Cylndrical Grinding Point6mmx25mmx25mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1547, 'Wire, Retaining 4x237M#49901', '1133004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Retaining 4x237M#49901', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1548, 'Gear, 1stStge PnionGear 22T & DrivenGear 155T', '1135007B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gear, 1stStge PnionGear 22T & DrivenGear 155T', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1549, 'Gear, Pinion w/ Shaft 44mmODx196OAL 22T', '1135007C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gear, Pinion w/ Shaft 44mmODx196OAL 22T', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1550, 'Gear,HelicalDrivn 40mmIDx283mmODx48.5mmW 155T', '1135007D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gear,HelicalDrivn 40mmIDx283mmODx48.5mmW 155T', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1551, '2nd Helical Gear Stg Cmprs H', '1135008', 'Inventory', NULL, NULL, NULL, NULL, 0, '2nd Helical Gear Stg Cmprs H', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1552, 'Gear, 2nd Stage Drive 53mmODx428.5mm OAL 18T', '1135008A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gear, 2nd Stage Drive 53mmODx428.5mm OAL 18T', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1553, 'Gear, 2ndStgeDrive 53mmx428.5mm,18T,106T, 45W', '1135008B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gear, 2ndStgeDrive 53mmx428.5mm,18T,106T, 45W', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1554, 'Gear, 3rd Stage Drive 85mmφx86mmWx238mm 18T', '1135009A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gear, 3rd Stage Drive 85mmφx86mmWx238mm 18T', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1555, 'Gear, 3rd Stage Drive 85mmx86mmWx238OAL, 18T', '1135009B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gear, 3rd Stage Drive 85mmx86mmWx238OAL, 18T', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1556, 'Reduction Gear-Helical Gear', '1135014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Reduction Gear-Helical Gear', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1557, 'Gear, Helical Pinion 22T', '1135020A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gear, Helical Pinion 22T', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1558, 'Gear,Pinion 155T w/shaft 22T', '1135020C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gear,Pinion 155T w/shaft 22T', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1559, 'Gear, 20T for Xerox', '1135024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gear, 20T for Xerox', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1560, 'Chain Coil', '1135027', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Chain Coil', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1561, 'Gear, Helical - 2 pairs', '1135032', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gear, Helical - 2 pairs', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1562, 'Gear, Reduction 2nd Stage', '1135038', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gear, Reduction 2nd Stage', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1563, 'Gear, Helical 102 teeth', '1135039', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gear, Helical 102 teeth', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1564, 'Gear, Helical 18T 27.20mmφ', '1135088', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gear, Helical 18T 27.20mmφ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1565, 'Gear, Hel. Pin. 22T Angle 18°', '1135091', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gear, Hel. Pin. 22T Angle 18°', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1566, 'Gear, Helical Pinion 22Teeth 42.5IDx76ODx40W', '1135095', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gear, Helical Pinion 22Teeth 42.5IDx76ODx40W', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1567, 'Gear, 4th Stage Set 20T, 125mmW, 140mmφ', '1135096', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gear, 4th Stage Set 20T, 125mmW, 140mmφ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1568, 'Gear, Drvn Spur 87Tx111mmODx28.59mmIDx15.2mmW', '1135097', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gear, Drvn Spur 87Tx111mmODx28.59mmIDx15.2mmW', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1569, 'Gear, Drive Spur 72T 94mmODx15mmW', '1135099', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gear, Drive Spur 72T 94mmODx15mmW', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1570, 'Wheel, Blower', '1136003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wheel, Blower', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1571, 'Cast SStypeIDME CSN74X404', '1139006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cast SStypeIDME CSN74X404', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1572, 'Housing, Centrifugal Pump 10', '1139007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Housing, Centrifugal Pump 10', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1573, 'Casing, Pmp WS MEE 405mmx59m', '1139013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Casing, Pmp WS MEE 405mmx59m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1574, 'Casing, Rotary Pump (APS)GRA', '1139014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Casing, Rotary Pump (APS)GRA', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1575, 'Casting, 660mm.IDx736mm.x680', '1139015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Casting, 660mm.IDx736mm.x680', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1576, 'Casing P.01000 M GG 25', '1139019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Casing P.01000 M GG 25', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1577, 'Casing,Cent.Pump 425x227x573.5', '1139042', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Casing,Cent.Pump 425x227x573.5', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1578, 'End Bshng Housing,135x239x23', '1141003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'End Bshng Housing,135x239x23', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1579, 'Hanger Housing-Split type #1', '1141007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hanger Housing-Split type #1', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1580, 'Rubber, Jacket', '1143001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rubber, Jacket', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1581, 'OIl Pump Type KEM 1-35 M-42-', '1145015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'OIl Pump Type KEM 1-35 M-42-', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1582, 'Epoxy, Marine (A & B)', '1149005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Epoxy, Marine (A & B)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1583, 'Putty, Plastic 56.8g/set', '1149011A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Putty, Plastic 56.8g/set', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1584, 'Thimble 1/2\"', '1150002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Thimble 1/2\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1585, 'Thimble 7/8\"', '1150003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Thimble 7/8\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1586, 'Thimble 3/4\"', '1150004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Thimble 3/4\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1587, 'Thimble for Wire Rope, 5/8\"', '1150005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Thimble for Wire Rope, 5/8\"', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1588, 'Wall Wea;ing Rear Stage CP', '1152001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wall Wea;ing Rear Stage CP', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1589, 'Softstone - 1/4\"x1/2\"x5\"L', '1157001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Softstone - 1/4\"x1/2\"x5\"L', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1590, 'Distributor, Product K850/35', '1158001A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Distributor, Product K850/35', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1591, 'Distributors, Metering Elements Dual Line', '1158002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Distributors, Metering Elements Dual Line', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1592, 'Lever, Hand PN-542703', '1160002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lever, Hand PN-542703', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1593, 'Electrofact Resistant Thermt', '1161003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Electrofact Resistant Thermt', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1594, 'Face Cover, upper #065302', '1162007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Face Cover, upper #065302', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1595, 'Face Cover, #065302', '1162008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Face Cover, #065302', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1596, 'Blade, Wheel Dresser', '1166003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Blade, Wheel Dresser', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1597, 'Stone, Sharpening Carborandum', '1167001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Stone, Sharpening Carborandum', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1598, 'Rubber Stator (For Dresser)', '1168008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rubber Stator (For Dresser)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1599, 'SS Expanded Metal Pattern Me', '1169002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'SS Expanded Metal Pattern Me', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1600, 'Rope, Guide Assy PN 57838544', '1172006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rope, Guide Assy PN 57838544', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1601, 'UNELEC Motor Fan Mat. Alumin', '1173001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'UNELEC Motor Fan Mat. Alumin', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1602, 'Fan, Cooling  BE Motor Drive', '1173015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fan, Cooling  BE Motor Drive', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1603, 'Fa, Cooling HP-20 Amp-25 440', '1173016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fa, Cooling HP-20 Amp-25 440', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1604, 'Fan, Cooling for Feed Pump #', '1173033', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fan, Cooling for Feed Pump #', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1605, 'Fan 18\"°, Part#39836069', '1173042', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fan 18\"°, Part#39836069', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1606, 'Moving Knife #066143', '1174002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Moving Knife #066143', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1607, 'Knife Moving PN-246061', '1174005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Knife Moving PN-246061', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1608, 'Knife Stationary PN-246071', '1174006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Knife Stationary PN-246071', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1609, 'Knife, PN-066142 B', '1174007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Knife, PN-066142 B', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1610, 'Knife Lever PN-066083', '1174008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Knife Lever PN-066083', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1611, 'Knife A, PN-066132', '1174009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Knife A, PN-066132', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1612, 'Key, PN-1K4x25', '1174013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Key, PN-1K4x25', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1613, 'Bar, Needle Conn. PN-62101', '1175008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Needle Conn. PN-62101', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1614, 'Bar, Needle Connecting 06211', '1175009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Needle Connecting 06211', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1615, 'Bar, Needle Connecting 06210', '1175010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Needle Connecting 06210', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1616, 'Bar, Needle Conn. Stud 06212', '1175011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Needle Conn. Stud 06212', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1617, 'Bar, Needle Assy PN-062141A', '1175013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Needle Assy PN-062141A', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1618, 'Graphite #2 @ 1lb/can', '1177001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Graphite #2 @ 1lb/can', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1619, 'Diode, Rectifier Recvry 12A', '1179001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Diode, Rectifier Recvry 12A', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1620, 'Sprocket, Driving  800-01400', '1181002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sprocket, Driving  800-01400', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1621, 'Gear, Pinion Helical 22T 44m', '1181009A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gear, Pinion Helical 22T 44m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1622, 'Sprocket, Roller Chain13 tee', '1181013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sprocket, Roller Chain13 tee', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1623, 'Sprocket, Driven 55 teeth', '1181015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sprocket, Driven 55 teeth', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1624, 'Sprocket, Roller Chain 38tee', '1181017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sprocket, Roller Chain 38tee', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1625, 'Sprocket, Boiler Bag. Convey', '1181024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sprocket, Boiler Bag. Convey', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1626, 'Element,Separator 54595442', '1182002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Element,Separator 54595442', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1627, 'Separator, Cart. PN 54749247', '1182003A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Separator, Cart. PN 54749247', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1628, 'Feed, Dog PN-064151', '1184004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Feed, Dog PN-064151', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1629, 'Feed Dog PN-244111', '1184005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Feed Dog PN-244111', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1630, 'Feed Dog PN-064151 (DS-211)', '1184006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Feed Dog PN-064151 (DS-211)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1631, 'Feed Dog #064241', '1184007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Feed Dog #064241', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1632, 'Feed, Lift Eccntric Cam 1041', '1184008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Feed, Lift Eccntric Cam 1041', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1633, 'Regulator, Feed Assy. 064061A', '1184009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Regulator, Feed Assy. 064061A', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1634, 'Feed,Connecting Rod PN-06404', '1184010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Feed,Connecting Rod PN-06404', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1635, 'Feed, Bar Shaft PN-064123', '1184011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Feed, Bar Shaft PN-064123', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1636, 'Feed, Lift Eccentric Cam PN-064141', '1184015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Feed, Lift Eccentric Cam PN-064141', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1637, 'Torch, Handle MOdel # 263', '1186002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Torch, Handle MOdel # 263', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1638, 'Attachment, Cutting Torch 73-3', '1186009A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Attachment, Cutting Torch 73-3', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1639, 'Attachment, L-152mm, H-81mm', '1186010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Attachment, L-152mm, H-81mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1640, 'Attachment, Bucket Load 20mmφx117mm Outr lgth', '1186013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Attachment, Bucket Load 20mmφx117mm Outr lgth', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1641, 'Plastic,Engineering Strip 12mm\"Tx4\"x4\'', '1187003A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plastic,Engineering Strip 12mm\"Tx4\"x4\'', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1642, 'Bushing,Eng\'gPlastic 60x90x210', '1187006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing,Eng\'gPlastic 60x90x210', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1643, 'Bushing,Eng\'g Poly-hi 35x85x70', '1187012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing,Eng\'g Poly-hi 35x85x70', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1644, 'Bushing, Eng\'g Plastic 200mmIDx260mmODx350mmL', '1187014A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Eng\'g Plastic 200mmIDx260mmODx350mmL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1645, 'Strip, Eng\'g Plastic 12mmTx3\"x', '1187016A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Strip, Eng\'g Plastic 12mmTx3\"x', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1646, 'Bushing, Eng\'gPlastic25x50x2', '1187021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Eng\'gPlastic25x50x2', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1647, 'Plastic, Eng\'g Poly-Hi 50x300', '1187024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plastic, Eng\'g Poly-Hi 50x300', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1648, 'Plastic, Eng\'g Strip 4\"x3ft 11', '1187028', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plastic, Eng\'g Strip 4\"x3ft 11', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1649, 'Overspeed Lever Trip', '1189001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Overspeed Lever Trip', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1650, 'Ext. Circlip Lock 12mmx1mmT', '1191004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ext. Circlip Lock 12mmx1mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1651, 'Circlip, 36mm', '1191005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Circlip, 36mm', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1652, 'Circlips, 76mmφx3mmT (Int.)', '1191007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Circlips, 76mmφx3mmT (Int.)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1653, 'InternalCirclipLock 51mmx1.5', '1191009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'InternalCirclipLock 51mmx1.5', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1654, 'Circlip, Internal Lock 76x2.', '1191010A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Circlip, Internal Lock 76x2.', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1655, 'Circlip,External Size 10mmx1', '1191011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Circlip,External Size 10mmx1', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1656, 'Ring, Circlip Lock 67IDx2mmT', '1191012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Circlip Lock 67IDx2mmT', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1657, 'Circlips, 72mmφx3mmT (Ext.)', '1191014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Circlips, 72mmφx3mmT (Ext.)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1658, 'Circlip, Internal 44mmφ', '1191018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Circlip, Internal 44mmφ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1659, 'Circlip, External 40mmφ', '1191020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Circlip, External 40mmφ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1660, 'Circlip, Internal 73mmφ', '1191021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Circlip, Internal 73mmφ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1661, 'Circlip, Internal 56mmφ', '1191022', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Circlip, Internal 56mmφ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1662, 'Lining,Brake Asbestos 6pcs/set', '1193009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lining,Brake Asbestos 6pcs/set', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1663, 'Lining, Brake Non-Asbestos 6pcs./set', '1193009C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lining, Brake Non-Asbestos 6pcs./set', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1664, 'Rotor, CAA', '1196007R', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rotor, CAA', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1665, 'Rotor, #2500 MM,ML,GF,GG,GH', '1196008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rotor, #2500 MM,ML,GF,GG,GH', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1666, 'Rotor, #2500 CAA 12H1H5/A0594', '1196008B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rotor, #2500 CAA 12H1H5/A0594', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1667, 'Rotor, #2500 SB07B5/H110', '1196008C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rotor, #2500 SB07B5/H110', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1668, 'Rotor, #2500', '1196012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rotor, #2500', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1669, 'Rotator, PN-1999', '1196013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rotator, PN-1999', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1670, 'Rotor, Molasses Pump', '1196014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rotor, Molasses Pump', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1671, 'Rotor, SB GF 2521', '1196016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rotor, SB GF 2521', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1672, 'Shaft, Rotor PN-2500/CB-051', '1196019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Shaft, Rotor PN-2500/CB-051', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1673, 'Rod. Piston w/needle', '1198050', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rod. Piston w/needle', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1674, 'Servo Balance Scale Parts', '1201015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Servo Balance Scale Parts', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1675, 'Ribbon, Steel-U 68x10.5x0.5m', '1201016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ribbon, Steel-U 68x10.5x0.5m', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1676, 'Socket, Rotor Mill Turbine', '1202006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Socket, Rotor Mill Turbine', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1677, 'Socket, Steel', '1202007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Socket, Steel', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1678, 'Socket, Steel SA for Turbo A', '1202008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Socket, Steel SA for Turbo A', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1679, 'Coupled Bushes, 20-01-019', '1205002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupled Bushes, 20-01-019', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1680, 'Nylatron, Push Rod Cap', '1207011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nylatron, Push Rod Cap', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1681, 'Bearing,Liner Bronze (Bottom', '1211002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing,Liner Bronze (Bottom', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1682, 'Bearing,Bronze Liner Bottom', '1212002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing,Bronze Liner Bottom', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1683, 'Pneumatic Recording Controll', '1212003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pneumatic Recording Controll', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25');
INSERT INTO `items` (`id`, `item_name`, `item_code`, `item_type`, `item_vendor_id`, `item_uom_id`, `item_reorder_point`, `item_category_id`, `item_quantity`, `item_sales_description`, `item_purchase_description`, `item_selling_price`, `item_cost_price`, `item_cogs_account_id`, `item_income_account_id`, `item_asset_account_id`, `created_at`) VALUES
(1684, 'Presser Foot Left PN-062391A', '1221003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Presser Foot Left PN-062391A', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1685, 'Presser, Foot Left PN-062391', '1221004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Presser, Foot Left PN-062391', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1686, 'Presser, Foot Right PN-062381', '1221005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Presser, Foot Right PN-062381', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1687, 'Presser, Foot Right PN-062261', '1221006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Presser, Foot Right PN-062261', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1688, 'Presser,FootAssy. Left 062251A', '1221007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Presser,FootAssy. Left 062251A', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1689, 'Presser, Bar Lifter PN-102071', '1221009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Presser, Bar Lifter PN-102071', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1690, 'Presser, Bar Lifter Hinge Stud PN-012181', '1221010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Presser, Bar Lifter Hinge Stud PN-012181', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1691, 'Presser, Bar Lifting Link PN-102082', '1221011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Presser, Bar Lifting Link PN-102082', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1692, 'Seat assy #7036A-11000190', '1224002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seat assy #7036A-11000190', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1693, 'Seat, Rubber 74mm - ERHARD ECL', '1224008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seat, Rubber 74mm - ERHARD ECL', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1694, 'Seat, Rubber 73mmφ', '1224008A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seat, Rubber 73mmφ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1695, 'Seat, Rubber 49.5mmφ', '1224009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seat, Rubber 49.5mmφ', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1696, 'Seat, Rubber 354mmOD', '1224019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seat, Rubber 354mmOD', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1697, 'Axle Box for Cane Cars', '1226003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Axle Box for Cane Cars', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1698, 'Pump Stuffing Box(Juice Pump', '1226005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pump Stuffing Box(Juice Pump', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1699, 'SS Stuffing Box Housing', '1226006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'SS Stuffing Box Housing', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1700, 'SS Pump Wear PLate Stuffing', '1226009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'SS Pump Wear PLate Stuffing', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1701, 'Lock Nut, HN-3096', '1228002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lock Nut, HN-3096', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1702, 'Proportional Unt Spndl#23983', '1231002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Proportional Unt Spndl#23983', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1703, 'Governor Spindle Connection', '1231003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Governor Spindle Connection', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1704, 'Spindle for DIN typ itm#46', '1231006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spindle for DIN typ itm#46', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1705, 'Control Knob', '1232001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Control Knob', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1706, 'Switch, Control STIHL 070', '1232002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Control STIHL 070', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1707, 'Adjustment Screw Flapper', '1233002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Adjustment Screw Flapper', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1708, 'Flow Relay', '1235001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Flow Relay', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1709, 'Diffuser, Last stage', '1242004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Diffuser, Last stage', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1710, 'Diffuser, Last stage @ 3pcs./set PN-171', '1242007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Diffuser, Last stage @ 3pcs./set PN-171', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1711, 'Rail Line', '1243000', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rail Line', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1712, 'Rail Line (Head 60.33mm)', '1243000A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rail Line (Head 60.33mm)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1713, 'Rail, Line Joint Bar', '1243001A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rail, Line Joint Bar', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1714, 'Rail, Line Joint Bar (Head 60.33mm)', '1243001B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rail, Line Joint Bar (Head 60.33mm)', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1715, 'Bearing, #NU316-E-TVP2-C3 FA', '1316006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #NU316-E-TVP2-C3 FA', NULL, NULL, NULL, 4655, 4603, 3191, '2024-09-19 07:27:25'),
(1716, 'Adaptor, Power AC/DC 12V, 2A', '2002012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Adaptor, Power AC/DC 12V, 2A', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1717, 'Ammeter 0-100 apm', '2003003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ammeter 0-100 apm', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1718, 'Ammeter, 0-800A 72 400/5A', '2003004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ammeter, 0-800A 72 400/5A', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1719, 'Ammeter 0-400A, 300/5A', '2003004A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ammeter 0-400A, 300/5A', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1720, 'Ammeter, w/ATS 0-200A 200/5', '2003005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ammeter, w/ATS 0-200A 200/5', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1721, 'Ammeter, 0-100Amps. SA-100T', '2003007A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ammeter, 0-100Amps. SA-100T', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1722, 'Ammeter, A.C.', '2003009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ammeter, A.C.', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1723, 'Ammeter w/current transforme', '2003011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ammeter w/current transforme', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1724, 'Voltmeter, 0-600V', '2003012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Voltmeter, 0-600V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1725, 'Armature, Assembly', '2004019A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Armature, Assembly', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1726, 'Armature, Assembly 6016', '2004020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Armature, Assembly 6016', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1727, 'Adapter, Socket BNC', '2004101', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Adapter, Socket BNC', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1728, 'Adapter, Plug BNC', '2004102', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Adapter, Plug BNC', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1729, 'Adapter, Pc USB', '2004103', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Adapter, Pc USB', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1730, 'Adapter, Brass 1/4\"x1/8\" NPT', '2004201', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Adapter, Brass 1/4\"x1/8\" NPT', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1731, 'Adapter, Brass 1/4\"x1/2\" NPT', '2004202', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Adapter, Brass 1/4\"x1/2\" NPT', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1732, 'Ballast, Flourescent 20W/230', '2006002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ballast, Flourescent 20W/230', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1733, 'Ballast, Fluorescent 40W/230V', '2006003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ballast, Fluorescent 40W/230V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1734, 'Ballast, Fldlight 400W 220V', '2006006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ballast, Fldlight 400W 220V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1735, 'Board, Painted Circuit Assy.', '2011007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Board, Painted Circuit Assy.', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1736, 'Bolt, Galvanized Cable Tray', '2012001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Galvanized Cable Tray', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1737, 'Box, Junction 4\"x4\" w/cover', '2013002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Box, Junction 4\"x4\" w/cover', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1738, 'Box, Utility 2\" x 4\"', '2013004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Box, Utility 2\" x 4\"', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1739, 'Breaker, Cir.2-poleCTL Type', '2015009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker, Cir.2-poleCTL Type', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1740, 'Breaker, Circuit 30A w/housing', '2015010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker, Circuit 30A w/housing', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1741, 'Breaker,Circuit Molded Case', '2015013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker,Circuit Molded Case', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1742, 'Breaker,Circuit 30A/230V', '2015015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker,Circuit 30A/230V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1743, 'Circuit Breaker 30A/230V', '2015015A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Circuit Breaker 30A/230V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1744, 'Breaker, Cir. 50A 220 Single', '2015016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker, Cir. 50A 220 Single', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1745, 'Breaker, Circuit 60A w/housing', '2015017C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker, Circuit 60A w/housing', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1746, 'Breaker, Circuit 16A 440V 60Hz', '2015018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker, Circuit 16A 440V 60Hz', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1747, 'Breaker, Circuit (Break Rela', '2015020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker, Circuit (Break Rela', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1748, 'Breaker, Air Circuit 3 pole', '2015021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker, Air Circuit 3 pole', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1749, 'Breaker, Circuit18-25amp.,440V', '2015027', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker, Circuit18-25amp.,440V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1750, 'Breaker, CircuitMoldedCase440V', '2015030A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker, CircuitMoldedCase440V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1751, 'Breaker,Circuit 28-40 Amp.', '2015031', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker,Circuit 28-40 Amp.', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1752, 'Breaker,Circuit 100A/230V/60hz', '2015033', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker,Circuit 100A/230V/60hz', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1753, 'Breaker, Circuit Molded Case 100A 3P', '2015033C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker, Circuit Molded Case 100A 3P', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1754, 'Breaker,Crcuit Miniature 6A 2P', '2015034', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker,Crcuit Miniature 6A 2P', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1755, 'Breaker, Circuit 40A/230V/60', '2015037', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker, Circuit 40A/230V/60', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1756, 'Breaker,Crcuit Miniature 6A', '2015040', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker,Crcuit Miniature 6A', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1757, 'Breaker,Crcuit Miniature25A 3P', '2015042', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker,Crcuit Miniature25A 3P', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1758, 'Breaker, Molded Circuit 440V', '2015073', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker, Molded Circuit 440V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1759, 'Breaker, Circuit plug-in 20A', '2015077', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker, Circuit plug-in 20A', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1760, 'Breaker, Circuit Miniature IC60n 10A 2P 400V', '2015081A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker, Circuit Miniature IC60n 10A 2P 400V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1761, 'Breaker, Circuit 20A/440V/60Hz', '2015097', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker, Circuit 20A/440V/60Hz', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1762, 'Breaker, Circuit 40A/440V/60Hz', '2015099', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker, Circuit 40A/440V/60Hz', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1763, 'Breaker, Circuit 75A/440V/60Hz', '2015102A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker, Circuit 75A/440V/60Hz', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1764, 'Breaker, Circuit Molded Case 250amp, 3 pole', '2015106', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker, Circuit Molded Case 250amp, 3 pole', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1765, 'Breaker, Circuit Molded Case 320amp, 3 pole', '2015107', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker, Circuit Molded Case 320amp, 3 pole', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1766, 'Breaker, Circuit Molded Case 160A, 3pole', '2015109', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker, Circuit Molded Case 160A, 3pole', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1767, 'Breaker, Circuit Molded Case 125A 3 Pole', '2015113', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker, Circuit Molded Case 125A 3 Pole', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1768, 'Breaker, Circuit Molded Case 50A 3P', '2015114', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker, Circuit Molded Case 50A 3P', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1769, 'Breaker,Circuit Molded Case 30amp,440V, 3Pole', '2015115', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Breaker,Circuit Molded Case 30amp,440V, 3Pole', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1770, 'Brush, Carbon MORGANITE', '2016001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush, Carbon MORGANITE', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1771, 'Carbon Brush 56.5x35x19.90mm', '2016005A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Carbon Brush 56.5x35x19.90mm', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1772, 'Carbon Brush, BLACK & DECKER', '2016006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Carbon Brush, BLACK & DECKER', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1773, 'Brush, Carbon #CB-152', '2016008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush, Carbon #CB-152', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1774, 'Brush, Carbon 1-1/2\"x1\"x1/2\"', '2016013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush, Carbon 1-1/2\"x1\"x1/2\"', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1775, 'Brush, Starter - Grinder', '2016014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush, Starter - Grinder', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1776, 'Brush, Carbon #204 2pcs./box', '2016015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush, Carbon #204 2pcs./box', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1777, 'Brush, Carbon CB-57 @2pcs/box', '2016016B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush, Carbon CB-57 @2pcs/box', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1778, 'Brush, Carbon Grade E-88', '2016018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush, Carbon Grade E-88', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1779, 'Brush, Carbon #153', '2016019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush, Carbon #153', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1780, 'Brush, Carbon #CB 72 @ 2pcs/', '2016020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush, Carbon #CB 72 @ 2pcs/', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1781, 'Brush,Carbon #411 2pcs./box', '2016023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush,Carbon #411 2pcs./box', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1782, 'Brush, Carbon 12.3x9.8x30', '2016025', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush, Carbon 12.3x9.8x30', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1783, 'Brush, Carbon', '2016027', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush, Carbon', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1784, 'Wire, Guy SMT-Strand', '2016074', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Guy SMT-Strand', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1785, 'Lamps, Heat 250W/230V', '2017009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lamps, Heat 250W/230V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1786, 'Bulb, Mercury 250W/230V BS', '2017010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Mercury 250W/230V BS', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1787, 'Lamp, Tungsten - 6405', '2017011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lamp, Tungsten - 6405', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1788, 'Bulb, Mercury 160W/230V SS', '2017013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Mercury 160W/230V SS', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1789, 'Bulb, Caram Lamp 6V/5A', '2017017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Caram Lamp 6V/5A', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1790, 'Bulb, Osram 100W', '2017018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Osram 100W', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1791, 'Bulb, Osram 100W/24V', '2017018A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Osram 100W/24V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1792, 'Bulb, Peanut 12V/2W', '2017020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Peanut 12V/2W', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1793, 'Bulb,Pilot 24V/3W Bayonet Type', '2017027', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb,Pilot 24V/3W Bayonet Type', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1794, 'Bulb, Pilot 24V/3W', '2017028', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Pilot 24V/3W', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1795, 'Bulb, Pilot Light 220V/5W', '2017029', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Pilot Light 220V/5W', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1796, 'Bulb,Pilot 24Vdc/3W screw ty', '2017030', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb,Pilot 24Vdc/3W screw ty', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1797, 'Bulb, Pilot 3W 110V Screw type', '2017032A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Pilot 3W 110V Screw type', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1798, 'Bulb, Floodlight HPI-T 400W/230V', '2017035D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Floodlight HPI-T 400W/230V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1799, 'Bulb, Mercury 500W/230V BS', '2017036', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Mercury 500W/230V BS', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1800, 'Bulb, Pilot 10/110V Dble. Co', '2017040', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Pilot 10/110V Dble. Co', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1801, 'Bulb, Pilot 2-3W 6V Screw Ty', '2017041', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Pilot 2-3W 6V Screw Ty', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1802, 'Bulb, Pilot 3W/28V screw typ', '2017043', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Pilot 3W/28V screw typ', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1803, 'Bulb, Pilot 30V/W screw type', '2017043A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Pilot 30V/W screw type', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1804, 'Bulb, Candelight 9 (small)', '2017045', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Candelight 9 (small)', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1805, 'Bulb, Elect. Clr Glass150W11', '2017048', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Elect. Clr Glass150W11', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1806, 'Bulb, Pilot light 24VDC/10W', '2017049', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Pilot light 24VDC/10W', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1807, 'Pilot, Bulb 220V/10W Screw T', '2017050', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pilot, Bulb 220V/10W Screw T', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1808, 'Bulb, Pilot Bayonet Type 6.3', '2017052', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Pilot Bayonet Type 6.3', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1809, 'Bulb, Pilot Bayonet Type', '2017056', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Pilot Bayonet Type', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1810, 'Bulb, Pilot Light 110V/10W', '2017061', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Pilot Light 110V/10W', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1811, 'Bulb, Pilot 6W/220V Bayonet', '2017062', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Pilot 6W/220V Bayonet', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1812, 'Bulb, Pilot 5W/110VAC Bayonet', '2017063A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Pilot 5W/110VAC Bayonet', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1813, 'Bulb, Pilot100-125V/2.5mA/60Hz', '2017064A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Pilot100-125V/2.5mA/60Hz', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1814, 'Bulb, Pilot Light 200mA/6V', '2017079', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Pilot Light 200mA/6V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1815, 'Bulb, Pilot Light 24V/1.2W', '2017080', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Pilot Light 24V/1.2W', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1816, 'Bulb, LED 7W/230V Warm White', '2017093', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, LED 7W/230V Warm White', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1817, 'Bulb,LED 7W/110-220V WarmWhite', '2017093C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb,LED 7W/110-220V WarmWhite', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1818, 'Bulb, LED 7W/ 110-240V Daylight', '2017093D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, LED 7W/ 110-240V Daylight', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1819, 'Bulb, LED 13W/ 110-240V Daylight', '2017095A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, LED 13W/ 110-240V Daylight', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1820, 'Bulb, LED 11W/100-240V', '2017102', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, LED 11W/100-240V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1821, 'Bulb, Pilot Light LED 24Vdc', '2017104', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Pilot Light LED 24Vdc', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1822, 'Bulb, Led 40W Daylight Capsule', '2017105A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Led 40W Daylight Capsule', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1823, 'Pilot,Light Red 22mmφ, 110Vac', '2017108', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pilot,Light Red 22mmφ, 110Vac', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1824, 'Tee, PN 39155346', '2018009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tee, PN 39155346', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1825, 'Button, Push PN-23271-012', '2019016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Button, Push PN-23271-012', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1826, 'Button, Emergency Key Reset', '2019016A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Button, Emergency Key Reset', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1827, 'Button, Push Start/Stop XALD213 w/ enclosure', '2019019A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Button, Push Start/Stop XALD213 w/ enclosure', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1828, 'Button, Push Start/Stop XAL-D2', '2019020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Button, Push Start/Stop XAL-D2', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1829, 'Button, Push Start XB5AA31', '2019021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Button, Push Start XB5AA31', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1830, 'Button, Push Stop ZB5AA4/ZB5AZ102', '2019022', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Button, Push Stop ZB5AA4/ZB5AZ102', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1831, 'Button, Push Stop XB5AA42', '2019022A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Button, Push Stop XB5AA42', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1832, 'Capacitor, 22MFD 600V', '2022002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capacitor, 22MFD 600V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1833, 'Capacitor, Running 35MFD 2#B', '2022004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capacitor, Running 35MFD 2#B', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1834, 'Capacitor, 4uf 400W', '2022005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capacitor, 4uf 400W', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1835, 'Capacitor, 4uf 450V', '2022005A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capacitor, 4uf 450V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1836, 'Capacitor, Dual Type 50+7uf/450V', '2022006A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capacitor, Dual Type 50+7uf/450V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1837, 'Capacitor, Electrolytic 20WP', '2022008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capacitor, Electrolytic 20WP', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1838, 'Capacitor, 100 MFD 50V', '2022009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capacitor, 100 MFD 50V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1839, 'Capacitor, 47MFD 50V', '2022010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capacitor, 47MFD 50V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1840, 'Capacitor, 50 MFD 100V', '2022011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capacitor, 50 MFD 100V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1841, 'Capacitor, Electrolytic 4mfd', '2022012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capacitor, Electrolytic 4mfd', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1842, 'Capacitor, Running 30mf.450V', '2022016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capacitor, Running 30mf.450V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1843, 'Capacitor, 3.3W/25V', '2022018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capacitor, 3.3W/25V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1844, 'Capacitor, 1 MFD 25V', '2022019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capacitor, 1 MFD 25V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1845, 'Capacitor, 10 MFD 25V', '2022020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capacitor, 10 MFD 25V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1846, 'Capacitor, 33 MFD 25V', '2022021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capacitor, 33 MFD 25V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1847, 'Capacitor, Dual Type 30+7uf/450V', '2022024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capacitor, Dual Type 30+7uf/450V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1848, 'Capacitor, Starting 110-127V', '2022033', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capacitor, Starting 110-127V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1849, 'Capacitor, Starting50uf,250V', '2022034', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capacitor, Starting50uf,250V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1850, 'Capacitor,0.22 UF  110/220V', '2022035', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capacitor,0.22 UF  110/220V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1851, 'Capacitor, Running 50-60Hz.', '2022040', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capacitor, Running 50-60Hz.', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1852, 'Capacitor,Running 40+7uf450Vac', '2022051', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capacitor,Running 40+7uf450Vac', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1853, 'Capacitor,Running 50+7uf440Vac', '2022052', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capacitor,Running 50+7uf440Vac', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1854, 'Capacitor, 20uf±10%250V/50/60Hz', '2022054', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capacitor, 20uf±10%250V/50/60Hz', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1855, 'Capacitor, Paper 100nF 1kV dc', '2022055', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capacitor, Paper 100nF 1kV dc', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1856, 'Capacitor, Dual Type 20mf+2mf/450V', '2022060', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capacitor, Dual Type 20mf+2mf/450V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1857, 'Capacitor, Dual Type 17mf+2mf/450V', '2022061', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capacitor, Dual Type 17mf+2mf/450V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1858, 'Channel, Galvanized Uni-Trap', '2023002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Channel, Galvanized Uni-Trap', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1859, 'Cable, Gland Round 23mm', '2024006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cable, Gland Round 23mm', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1860, 'Cable, Twisted #22AWG 2-wires', '2024010A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cable, Twisted #22AWG 2-wires', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1861, 'Cable, Twisted # 22AWG 2 Wires', '2024010C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cable, Twisted # 22AWG 2 Wires', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1862, 'Cable, Gland Round 19mm', '2024012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cable, Gland Round 19mm', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1863, 'Cable, Gland Round 37mm', '2024014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cable, Gland Round 37mm', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1864, 'Cable, Gland Round 15.2mm', '2024015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cable, Gland Round 15.2mm', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1865, 'Cable, Feed Valve Encoder 2m', '2024030', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cable, Feed Valve Encoder 2m', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1866, 'Cable,Twisted Wire#8761 #22', '2024031', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cable,Twisted Wire#8761 #22', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1867, 'Cable, Twisted #22AWG 3-wires', '2024031A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cable, Twisted #22AWG 3-wires', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1868, 'Cable, Special Sheilded', '2024038', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cable, Special Sheilded', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1869, 'Clamp, Anchor Rod Bonding', '2025001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Clamp, Anchor Rod Bonding', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1870, 'Cleves, Dead End 3-6/16', '2026001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cleves, Dead End 3-6/16', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1871, 'Block, Support Pneumatic Inl', '2027001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Block, Support Pneumatic Inl', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1872, 'Block,Exhaust Pneumatic Supp', '2027002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Block,Exhaust Pneumatic Supp', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1873, 'Coil, 0.224x0.47x878043C Ohm', '2028001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coil, 0.224x0.47x878043C Ohm', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1874, 'Coil, #.224mm .470mm 878044', '2028002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coil, #.224mm .470mm 878044', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1875, 'Coil, Holding (Coil Voltage)', '2028004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coil, Holding (Coil Voltage)', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1876, 'Coil, Holding 3TY1-28300G-11', '2028006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coil, Holding 3TY1-28300G-11', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1877, 'Coil, 3TY1212-OC3TA65 110v60', '2028010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coil, 3TY1212-OC3TA65 110v60', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1878, 'Coil, 3TY 1213 OG-3TA65 110v', '2028011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coil, 3TY 1213 OG-3TA65 110v', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1879, 'Coil, 3TY1233 3TA23 110 60hz', '2028014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coil, 3TY1233 3TA23 110 60hz', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1880, 'Coil, 3TY1223 com. 3TA22 220', '2028015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coil, 3TY1223 com. 3TA22 220', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1881, 'Coil, Holding for Contactor', '2028016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coil, Holding for Contactor', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1882, 'Coil, Holding 110-120 Vdc', '2028016A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coil, Holding 110-120 Vdc', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1883, 'Coil, Magnet 110V 3TY-2230G', '2028018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coil, Magnet 110V 3TY-2230G', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1884, 'Coil, Magnet 3ATY 1-263 100V', '2028019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coil, Magnet 3ATY 1-263 100V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1885, 'Coil, Magnet 3TY1-243 ON 100', '2028021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coil, Magnet 3TY1-243 ON 100', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1886, 'Coil, Magnet 3TY1-233 ON 110', '2028022', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coil, Magnet 3TY1-233 ON 110', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1887, 'Coil, Magnet 3TY1-223 ON 220', '2028023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coil, Magnet 3TY1-223 ON 220', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1888, 'Coil, Holding 3TF48 110V', '2028024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coil, Holding 3TF48 110V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1889, 'Coil, Magnet 3TY1-21300G 3TA', '2028026', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coil, Magnet 3TY1-21300G 3TA', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1890, 'Coil, Magnet 3TY1-233oG 110V', '2028028', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coil, Magnet 3TY1-233oG 110V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1891, 'Coil, Magnet 3TY1-223oG 3TA2', '2028029', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coil, Magnet 3TY1-223oG 3TA2', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1892, 'Coil, Ref 75096(R-125)110v 6', '2028030', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coil, Ref 75096(R-125)110v 6', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1893, 'Coil, 120/60 FB#95-866-1', '2028036', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coil, 120/60 FB#95-866-1', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1894, 'Coil, Holder 230v/60hz', '2028037', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coil, Holder 230v/60hz', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1895, 'Coil, Magnet type 3TAY1-283-', '2028038', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coil, Magnet type 3TAY1-283-', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1896, 'Coil, Hldng Magnetic Contact', '2028040', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coil, Hldng Magnetic Contact', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1897, 'Coil, Holding 3TF50', '2028043', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coil, Holding 3TF50', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1898, 'Connector, Butt #10 Insulate', '2029003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Connector, Butt #10 Insulate', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1899, 'Connector, Solderless', '2030006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Connector, Solderless', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1900, 'Lead, M12 3W R/A SKT#9344022', '2030011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lead, M12 3W R/A SKT#9344022', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1901, 'Cable,M12 4W STR PLG PG7', '2030011A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cable,M12 4W STR PLG PG7', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1902, 'Connector, Solderless #6', '2030014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Connector, Solderless #6', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1903, 'Connector, Long Barrel 11000', '2030014A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Connector, Long Barrel 11000', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1904, 'Connector, Solderless #2', '2030018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Connector, Solderless #2', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1905, 'Bolt, Connector #10-#6 SBC6S-C', '2030019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Connector #10-#6 SBC6S-C', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1906, 'Bolt, Connector #6-#2 SBC2S-', '2030021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Connector #6-#2 SBC2S-', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1907, 'Connector, Terminal BNC Male', '2030032', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Connector, Terminal BNC Male', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1908, 'Connector, Terminal BNC Female', '2030033', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Connector, Terminal BNC Female', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1909, 'Contactor,Magntic3TF46 2NO+2', '2030037', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contactor,Magntic3TF46 2NO+2', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1910, 'Contact, Auxillary 1NC/1NO', '2031001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Auxillary 1NC/1NO', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1911, 'Contact, Block 42 SWIL 301-O', '2031002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Block 42 SWIL 301-O', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1912, 'Contact, Fix&Moving 3TY7520-OX', '2031003A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Fix&Moving 3TY7520-OX', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1913, 'Contact, Fix & Moving 3TY7520', '2031003B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Fix & Moving 3TY7520', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1914, 'Contact, Angle Hard Tip 1x1', '2031004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Angle Hard Tip 1x1', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1915, 'Contact, Fixed and Moving', '2031005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Fixed and Moving', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1916, 'Contact, Fix&Moving 3TY7450-', '2031005A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Fix&Moving 3TY7450-', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1917, 'Contact, Mvbl for Main Break', '2031006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Mvbl for Main Break', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1918, 'Contact, Fix & Moving 3TY7480-', '2031007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Fix & Moving 3TY7480-', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1919, 'Contact, Mvbl for11100KVA Tr', '2031008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Mvbl for11100KVA Tr', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1920, 'Contact, Fixed for1100KVA Tr', '2031009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Fixed for1100KVA Tr', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1921, 'Contact, Fix & Moving 3TF44', '2031010A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Fix & Moving 3TF44', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1922, 'Contact, Fix & Moving 3TF-56', '2031012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Fix & Moving 3TF-56', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1923, 'Contact, Fix & Moving 3TF-54', '2031013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Fix & Moving 3TF-54', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1924, 'Contact, Fix&Moving 3TY7540-', '2031013A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Fix&Moving 3TY7540-', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1925, 'Contact, Fix & Moving 3TF-50', '2031014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Fix & Moving 3TF-50', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1926, 'Contact, Set for 3 TA 30', '2031016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Set for 3 TA 30', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1927, 'Contact, Fix & Moving 3TF34', '2031019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Fix & Moving 3TF34', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1928, 'Contact, Fix & Moving 3TF47', '2031022', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Fix & Moving 3TF47', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1929, 'Contact, Fix & Movng3TY7500-OX', '2031023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Fix & Movng3TY7500-OX', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1930, 'Contact, Fix&Moving 3TY7460-', '2031024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Fix&Moving 3TY7460-', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1931, 'Contacts, Tip Air Circuit Br', '2031024A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contacts, Tip Air Circuit Br', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1932, 'Contact, Fix & Moving 3TF51', '2031027', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Fix & Moving 3TF51', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1933, 'Contact, Fix&Moving 3TY7510-', '2031029', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Fix&Moving 3TY7510-', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1934, 'Contacts, Fix & Moving 3TF 5', '2031032', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contacts, Fix & Moving 3TF 5', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1935, 'Contact, Fix & Moving 3TF 43', '2031038', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Fix & Moving 3TF 43', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1936, 'Contact, Fix & Moving 3RT2938-', '2031039', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Fix & Moving 3RT2938-', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1937, 'Contact, Fix & Moving 3RT1956-', '2031041', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Fix & Moving 3RT1956-', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1938, 'Contact, Fix & Moving 3RT1965-', '2031042', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Fix & Moving 3RT1965-', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1939, 'Contact, Fix & Moving 3RT1975-', '2031043', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Fix & Moving 3RT1975-', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1940, 'Contact, Fix & Moving 3RT2938-', '2031044', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Fix & Moving 3RT2938-', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1941, 'Connector, Angle 10x10x80x10', '2032001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Connector, Angle 10x10x80x10', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1942, 'Contactor, 3TA 26-110v 60hz', '2032008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contactor, 3TA 26-110v 60hz', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1943, 'Contactor, Magnetic 3TF 42', '2032009A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contactor, Magnetic 3TF 42', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1944, 'Contactor, Magnetic 5.5kw 22', '2032015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contactor, Magnetic 5.5kw 22', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1945, 'Contactor, 3TA 2315-GA T 156', '2032016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contactor, 3TA 2315-GA T 156', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1946, 'Contactor, Magnetic 3TF50', '2032033', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contactor, Magnetic 3TF50', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1947, 'Contactor, Magnetic 3TF46', '2032037', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contactor, Magnetic 3TF46', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1948, 'Contactor, 3TB54 14-OAN 1', '2032040', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contactor, 3TB54 14-OAN 1', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1949, 'Contactor, Mgntc 3TF52 2NO/2NC', '2032042A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contactor, Mgntc 3TF52 2NO/2NC', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1950, 'Contactor, Magnetic 3TF5222', '2032042B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contactor, Magnetic 3TF5222', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1951, 'Contactor, Auxillary LAIDN40', '2032044', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contactor, Auxillary LAIDN40', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1952, 'Contactor, Auxillary LA1DN11', '2032045', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contactor, Auxillary LA1DN11', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1953, 'Contact, Auxillary A300/Q300', '2032046', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Auxillary A300/Q300', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1954, 'Contact, Auxillary 2NO-2NC', '2032047', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Auxillary 2NO-2NC', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1955, 'Cntact,Aux3TY7561-1AAOO1NO/1', '2032049', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cntact,Aux3TY7561-1AAOO1NO/1', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1956, 'Contactor Magnetic 3TF5022-0', '2032050', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contactor Magnetic 3TF5022-0', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1957, 'Contactor, Magntc 3TF5122-OX', '2032051A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contactor, Magntc 3TF5122-OX', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1958, 'Contactor, Mgntc 3TF47 2NO/2', '2032052', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contactor, Mgntc 3TF47 2NO/2', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1959, 'Contactor, Magnetic 3TF54', '2032053A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contactor, Magnetic 3TF54', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1960, 'Contactor, Magnetic 3TF48/22', '2032055', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contactor, Magnetic 3TF48/22', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1961, 'Contactor, Magnetic 3TF48 110V', '2032055A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contactor, Magnetic 3TF48 110V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1962, 'Contactor,Magnetic 110V AC', '2032058', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contactor,Magnetic 110V AC', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25');
INSERT INTO `items` (`id`, `item_name`, `item_code`, `item_type`, `item_vendor_id`, `item_uom_id`, `item_reorder_point`, `item_category_id`, `item_quantity`, `item_sales_description`, `item_purchase_description`, `item_selling_price`, `item_cost_price`, `item_cogs_account_id`, `item_income_account_id`, `item_asset_account_id`, `created_at`) VALUES
(1963, 'Contactor, Magnetic 110V coil', '2032058A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contactor, Magnetic 110V coil', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1964, 'Contactor, Magentic A210-30-', '2032059', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contactor, Magentic A210-30-', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1965, 'Control, Level 1500D-L3-S7', '2032060B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Control, Level 1500D-L3-S7', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1966, 'Control,Level1500-C-L3-S7-OC-X', '2032060C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Control,Level1500-C-L3-S7-OC-X', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1967, 'Control,Level1500-D-L3-S7-OC-X', '2032060D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Control,Level1500-D-L3-S7-OC-X', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1968, 'Contactor, Magnetic 3TF 44', '2032064', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contactor, Magnetic 3TF 44', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1969, 'Magnetic, Contactor 3TH4244-OAFO', '2032067', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Magnetic, Contactor 3TH4244-OAFO', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1970, 'Contactor, Magnetic 3RT2027-1AG24 110Vac', '2032070', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contactor, Magnetic 3RT2027-1AG24 110Vac', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1971, 'Contactor, Magnetic 3RT2038-1AG24', '2032071', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contactor, Magnetic 3RT2038-1AG24', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1972, 'Contactor, Magnetic 3RT2038-1AG24 110Vac', '2032071A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contactor, Magnetic 3RT2038-1AG24 110Vac', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1973, 'Contactor, Magnetic 3RT1056-6A', '2032072', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contactor, Magnetic 3RT1056-6A', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1974, 'Contactor, Magnetic 3RT1065-6A', '2032073', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contactor, Magnetic 3RT1065-6A', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1975, 'Contactor, Magnetic 3RT1075-6A', '2032074', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contactor, Magnetic 3RT1075-6A', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1976, 'Contactor, Magnetic 3RT 2018-1AFO1 110Vac', '2032075', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contactor, Magnetic 3RT 2018-1AFO1 110Vac', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1977, 'Cntactor, Magntic LC1-D25E7 48VAC 2NO-2NC 25A', '2032076', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cntactor, Magntic LC1-D25E7 48VAC 2NO-2NC 25A', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1978, 'Controller, SLC 5/03 16K', '2033005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Controller, SLC 5/03 16K', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1979, 'SLC 500 DH 485 Isolated Link', '2033006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'SLC 500 DH 485 Isolated Link', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1980, 'SLC, Isolated Link Coupler PN-1747-AIC', '2033006A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'SLC, Isolated Link Coupler PN-1747-AIC', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1981, 'Logo, Basis 6ED1052-1FB00-OBA6', '2033010A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Logo, Basis 6ED1052-1FB00-OBA6', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1982, 'Module, Logo Expansion DM8230R', '2033011A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Module, Logo Expansion DM8230R', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1983, 'Module, SLC 500 Output 16 way', '2033012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Module, SLC 500 Output 16 way', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1984, 'Rack, Slot PN-1746-A10', '2033013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rack, Slot PN-1746-A10', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1985, 'Controller, Joystick IP65', '2033014A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Controller, Joystick IP65', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1986, 'Supply, Power SLC 500 24VDC', '2033016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Supply, Power SLC 500 24VDC', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1987, 'Transformer, Stepdown', '2033027', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Transformer, Stepdown', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1988, 'Controller, Digital SDC25', '2033030', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Controller, Digital SDC25', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1989, 'Controller, Digital', '2033030A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Controller, Digital', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1990, 'Control, Liquid Level Line Vol', '2033039', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Control, Liquid Level Line Vol', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1991, 'Module, SLC 500 Input PN 1746-IB16', '2033040', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Module, SLC 500 Input PN 1746-IB16', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1992, 'Module, SLC 500 Input PN: 1746-ITB16', '2033040A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Module, SLC 500 Input PN: 1746-ITB16', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1993, 'Module, SLC 500 Output PN: 1746-OB16', '2033041', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Module, SLC 500 Output PN: 1746-OB16', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1994, 'Module, SLC 500 Output PN: 1746-OW16', '2033041A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Module, SLC 500 Output PN: 1746-OW16', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1995, 'Converter, 2-3 Wire  23470-040', '2034002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Converter, 2-3 Wire  23470-040', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1996, 'Converter, Bolt5-24 #54550-0', '2034003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Converter, Bolt5-24 #54550-0', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1997, 'Frequency to Current Converter PN-23460-014', '2034009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Frequency to Current Converter PN-23460-014', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1998, 'Trip, Current Unit ACR-N 220', '2034013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Trip, Current Unit ACR-N 220', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(1999, 'Trnsmittr, Frquency M2XPA3-B4Z1-M2/UL 100-240', '2034014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Trnsmittr, Frquency M2XPA3-B4Z1-M2/UL 100-240', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2000, 'Cord, Royal', '2035001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cord, Royal', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2001, 'Cord, Royal #6/4C', '2035002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cord, Royal #6/4C', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2002, 'Cord, Royal #50mmsq./4C', '2035003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cord, Royal #50mmsq./4C', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2003, 'Cord, Royal #14/4C 75mtrs/roll', '2035009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cord, Royal #14/4C 75mtrs/roll', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2004, 'Cord, Royal #16/2C', '2035012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cord, Royal #16/2C', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2005, 'Cord, Royal #16 AWG/2 Conductor', '2035012A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cord, Royal #16 AWG/2 Conductor', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2006, 'Cord, Royal #16/6 (1.5mm.sq.', '2035013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cord, Royal #16/6 (1.5mm.sq.', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2007, 'Coupling, Conduit 1/2\"', '2036016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupling, Conduit 1/2\"', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2008, 'Coupling, Conduit 1\"', '2036020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupling, Conduit 1\"', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2009, 'Cover, Glazed fuse #5sh2-02', '2037008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cover, Glazed fuse #5sh2-02', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2010, 'Diode, 60A 220V (23140-052)', '2039003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Diode, 60A 220V (23140-052)', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2011, 'Diode, Thyristor PN-23530-011', '2039005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Diode, Thyristor PN-23530-011', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2012, 'Diode, IN 34', '2039006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Diode, IN 34', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2013, 'Diode, 150A 1200PIV', '2039007A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Diode, 150A 1200PIV', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2014, 'Diode, Selicon PHmeter #9635', '2039014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Diode, Selicon PHmeter #9635', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2015, 'Diode, PH for Beckham#938764', '2039015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Diode, PH for Beckham#938764', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2016, 'Diode 3A40', '2039020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Diode 3A40', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2017, 'Elbow, Conduit 3/4\"', '2040004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elbow, Conduit 3/4\"', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2018, 'Elbow, Conduit 4\"', '2040012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elbow, Conduit 4\"', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2019, 'Expansion, Shield S6 Fischer', '2042001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Expansion, Shield S6 Fischer', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2020, 'Expansion, Shield 8Fischer D', '2042002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Expansion, Shield 8Fischer D', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2021, 'Cylinder, FeedLimiter20020-121', '2043001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cylinder, FeedLimiter20020-121', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2022, 'Assy, Field MAKITA #9609NB', '2044003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Assy, Field MAKITA #9609NB', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2023, 'Filter, Oil AE-25L, OAL=8.5', '2044004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil AE-25L, OAL=8.5', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2024, 'Field, Assembly 6016', '2044015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Field, Assembly 6016', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2025, 'Fixture, Flourescent2x40W/23', '2045002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fixture, Flourescent2x40W/23', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2026, 'Fuse, Blade Type 16A./500V', '2046001A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Blade Type 16A./500V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2027, 'Fuse, Blade type 80A/500V', '2046004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Blade type 80A/500V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2028, 'Fuse, Blade Type 40Amps/660V', '2046008D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Blade Type 40Amps/660V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2029, 'Fuse, SIBA 500A/500V', '2046009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, SIBA 500A/500V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2030, 'Fuse, Blade type 32A/500V SI', '2046011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Blade type 32A/500V SI', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2031, 'Fuse, Type 500A 2/T 3NAI 434', '2046014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Type 500A 2/T 3NAI 434', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2032, 'Fuse,Blade Type 63A/500V NH000', '2046016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse,Blade Type 63A/500V NH000', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2033, 'Fuse, Type MG#7-2 KV 200CT', '2046017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Type MG#7-2 KV 200CT', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2034, 'Fuse, 300 2T 500V3NAI 328', '2046018A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, 300 2T 500V3NAI 328', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2035, 'Fuse, Blade type 400Amps/500', '2046019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Blade type 400Amps/500', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2036, 'Fuse, SIBA 10A/500V', '2046021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, SIBA 10A/500V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2037, 'Fuse, Cartridge 30A/230V', '2046022', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Cartridge 30A/230V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2038, 'Fuse, Cartridge 60A/230V', '2046023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Cartridge 60A/230V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2039, 'Fuse, Diazed 2A/500v 55A211', '2046024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Diazed 2A/500v 55A211', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2040, 'Fuse, Diazed 500V AC GL-GG', '2046024A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Diazed 500V AC GL-GG', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2041, 'Fuse, Blade Type 63A/500V NHQ', '2046026', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Blade Type 63A/500V NHQ', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2042, 'Fuse, Diazed 6A/500V Bottle Ty', '2046027A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Diazed 6A/500V Bottle Ty', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2043, 'Fuse, Diazed 20A/500V', '2046028', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Diazed 20A/500V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2044, 'Fuse, Diazed 10A/500V Bottle T', '2046030', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Diazed 10A/500V Bottle T', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2045, 'Fuse, Blade type 50A/500V', '2046031', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Blade type 50A/500V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2046, 'Fuse, Diazed 16A/500V', '2046032', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Diazed 16A/500V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2047, 'Fuse, Diazed 25A/500V Bottle T', '2046034', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Diazed 25A/500V Bottle T', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2048, 'Fuse, Voight&Haeffner 63A/50', '2046035', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Voight&Haeffner 63A/50', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2049, 'Fuse, Diazed 50A/500V', '2046038', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Diazed 50A/500V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2050, 'Fuse, Diazed 63A/500V', '2046039', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Diazed 63A/500V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2051, 'Fuse,Blade type 630A/500V NH', '2046040', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse,Blade type 630A/500V NH', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2052, 'Fuse, Blade Type 250A/500V', '2046041', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Blade Type 250A/500V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2053, 'Fuse, Glass 7A/230V', '2046042', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Glass 7A/230V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2054, 'Fuse,Diazed 35A/500V(Bot.Typ', '2046044', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse,Diazed 35A/500V(Bot.Typ', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2055, 'Fuse, SIBA 125A/500V', '2046047', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, SIBA 125A/500V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2056, 'Fuse,Blade type 160A/500V NH', '2046048', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse,Blade type 160A/500V NH', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2057, 'Fuse, Blade Type 160A/500V NH1', '2046048A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Blade Type 160A/500V NH1', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2058, 'Fuse, Blade type 160A/500V', '2046048B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Blade type 160A/500V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2059, 'Fuse, Blade Type 315A/500V', '2046051', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Blade Type 315A/500V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2060, 'Fuse, Blade type 355A 500V', '2046052', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Blade type 355A 500V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2061, 'Base, Fuse 160Amps./500V NHI', '2046057A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Base, Fuse 160Amps./500V NHI', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2062, 'Base, Fuse 630apms/500V NH3', '2046060', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Base, Fuse 630apms/500V NH3', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2063, 'Fuse, Link semi cndctr200A/6', '2046063A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Link semi cndctr200A/6', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2064, 'Fuse, Diazed 80A/500V', '2046064', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Diazed 80A/500V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2065, 'Fuse, Blade type 100A/500V', '2046065', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Blade type 100A/500V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2066, 'Fuse, Link Primary 15A-5-20K', '2046066', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Link Primary 15A-5-20K', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2067, 'Fuse, Link Type K10A', '2046068', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Link Type K10A', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2068, 'Fuse,Link Time Lug 30A', '2046070', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse,Link Time Lug 30A', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2069, 'Fuse, Link 25Amps.', '2046073A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Link 25Amps.', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2070, 'Fuse, Link 16amp./240VAC', '2046074', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Link 16amp./240VAC', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2071, 'Fuse, Link 140Amps, Type K', '2046077', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Link 140Amps, Type K', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2072, 'Fuse, Link 20A', '2046079', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Link 20A', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2073, 'Fuse, Link 40 Amps Type K', '2046080', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Link 40 Amps Type K', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2074, 'Fuse, Cart.4Amp. 600V 10x38mm', '2046083', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Cart.4Amp. 600V 10x38mm', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2075, 'Fuse, Cart. type 4Amps. 600v', '2046084', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Cart. type 4Amps. 600v', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2076, 'Fuse, Cart. type 6A/500V AC', '2046086', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Cart. type 6A/500V AC', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2077, 'Fuse, Link 6A/600V', '2046087A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Link 6A/600V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2078, 'Fuse, Glass 5A/110V', '2046101', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Glass 5A/110V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2079, 'HRC Cylindrical fuse 10amps.', '2046102', 'Inventory', NULL, NULL, NULL, NULL, 0, 'HRC Cylindrical fuse 10amps.', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2080, 'Fuse, Link 60A', '2046103', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Link 60A', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2081, 'Fuse, Blade type 25A/500V', '2046111', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Blade type 25A/500V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2082, 'Fuse, Renewable 200A/250V AC', '2046117A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Renewable 200A/250V AC', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2083, 'Fuse, Reneuwable 200A/220V', '2046118', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Reneuwable 200A/220V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2084, 'Fuse, Diazed 267-009 6A', '2046119', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Diazed 267-009 6A', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2085, 'Fuse, 26700-007 Link NS 20M3', '2046121', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, 26700-007 Link NS 20M3', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2086, 'Fuse, Blade type 224A/500V', '2046122', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Blade type 224A/500V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2087, 'Fuse, Link 150A type: K', '2046125', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Link 150A type: K', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2088, 'Fuse, Link 100A Type: K', '2046126', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Link 100A Type: K', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2089, 'Fuse,Gould 100amps./250V AC', '2046129', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse,Gould 100amps./250V AC', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2090, 'Fuse, Blade Type 32amp. 500V', '2046130', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Blade Type 32amp. 500V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2091, 'Fuse, Renewable 100Amp/250V (Slow Lag)', '2046133A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Renewable 100Amp/250V (Slow Lag)', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2092, 'Cut-out, High Voltage Fuse', '2046134', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cut-out, High Voltage Fuse', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2093, 'Base, Fuse 250A/690V NHI (3 pc', '2046135B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Base, Fuse 250A/690V NHI (3 pc', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2094, 'Base, Fuse 160amp/500V NHO', '2046136', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Base, Fuse 160amp/500V NHO', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2095, 'Base, Fuse 160amp/660V 3 Pole', '2046136A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Base, Fuse 160amp/660V 3 Pole', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2096, 'Fuse, Cut-out 100A,15Kv,110kv', '2046142', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Cut-out 100A,15Kv,110kv', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2097, 'Fuse, Glass Cartridge 200mA', '2046144', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Glass Cartridge 200mA', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2098, 'Link, Fuse HRC 690VAC- 200Amps', '2046145', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Link, Fuse HRC 690VAC- 200Amps', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2099, 'Base, Fuse Blade Type NH4, Sin', '2046148', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Base, Fuse Blade Type NH4, Sin', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2100, 'Insulator, Fuse 3NP7 250/500', '2052005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Insulator, Fuse 3NP7 250/500', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2101, 'Insulator, Groove Spool 1-3/', '2052014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Insulator, Groove Spool 1-3/', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2102, 'Ceramic, Insulators', '2052015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ceramic, Insulators', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2103, 'Isolator, 3 Pole PN-23200-039', '2052016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Isolator, 3 Pole PN-23200-039', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2104, 'Lamp, Artificial 220V', '2053001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lamp, Artificial 220V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2105, 'Lamp, Mercury Part 5NA159 1-', '2053002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lamp, Mercury Part 5NA159 1-', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2106, 'Lamp, Mercury Gls 5NA 1501-O', '2053004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lamp, Mercury Gls 5NA 1501-O', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2107, 'Lamp, Halogen w/bshng100W/22', '2053006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lamp, Halogen w/bshng100W/22', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2108, 'Lamp, Shade Guard GHS', '2053012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lamp, Shade Guard GHS', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2109, 'Lead, Soldering 1.2mmφ', '2054005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lead, Soldering 1.2mmφ', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2110, 'Lighting, Fixture Square 500', '2057002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lighting, Fixture Square 500', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2111, 'Floodlight, Halogen 150W', '2057009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Floodlight, Halogen 150W', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2112, 'Lighting, Arester 9L1500B007', '2058001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lighting, Arester 9L1500B007', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2113, 'Lighting, Arester 9L 1544', '2058002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lighting, Arester 9L 1544', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2114, 'Lighting, Surge Arrester 650', '2058003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lighting, Surge Arrester 650', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2115, 'Lighting, Arrestor 3 phase', '2058004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lighting, Arrestor 3 phase', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2116, 'Reflectr, 12\"φsmll sockt&bended 1/2\"φ GI Pipe', '2058006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Reflectr, 12\"φsmll sockt&bended 1/2\"φ GI Pipe', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2117, 'Lighting, Arester NKV', '2058008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lighting, Arester NKV', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2118, 'Lighting, Arester indoor 5KV', '2058010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lighting, Arester indoor 5KV', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2119, 'Lighting, Surge Arrester L-3', '2058011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lighting, Surge Arrester L-3', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2120, 'Reflector, Lighting', '2058013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Reflector, Lighting', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2121, 'Light, indicator Clear 6.5V', '2058017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Light, indicator Clear 6.5V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2122, 'Locknut, Conduit 1\"', '2059002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Locknut, Conduit 1\"', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2123, 'Lug, Terminal for 2 Wire', '2062003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lug, Terminal for 2 Wire', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2124, 'Lug, Terminal TW wire#1/0 55', '2062008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lug, Terminal TW wire#1/0 55', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2125, 'Lug, Terminal TW wire#2(30mm', '2062009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lug, Terminal TW wire#2(30mm', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2126, 'Lug, Terminal TW wire#4(22mm', '2062011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lug, Terminal TW wire#4(22mm', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2127, 'Lug,Terminal RT 60mm, Short Barrel, 1 hole', '2062012A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lug,Terminal RT 60mm, Short Barrel, 1 hole', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2128, 'Lug, Terminal RT 38mm, Short Barrel, 1 hole', '2062014A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lug, Terminal RT 38mm, Short Barrel, 1 hole', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2129, 'Lug, Terminal RT 14mm, Short Barrel, 1 hole', '2062020A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lug, Terminal RT 14mm, Short Barrel, 1 hole', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2130, 'Lug, Terminal RT 5.5mm, Short Barrel, 1 hole', '2062025A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lug, Terminal RT 5.5mm, Short Barrel, 1 hole', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2131, 'Lug, Terminal #1 PN-31028', '2062027A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lug, Terminal #1 PN-31028', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2132, 'Lug, Terminal #6 PN-31015', '2062032', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lug, Terminal #6 PN-31015', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2133, 'Lugs,Terminal Wire 8AWG Stud 10mm Crimp Type', '2062034A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lugs,Terminal Wire 8AWG Stud 10mm Crimp Type', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2134, 'Lugs, Terminal 160amps,440V,', '2062092', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lugs, Terminal 160amps,440V,', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2135, 'Lug, Terminal 1-2.6mm.sq M4', '2062103A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lug, Terminal 1-2.6mm.sq M4', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2136, 'Lug, Terminal 3/8 stud size', '2062105', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lug, Terminal 3/8 stud size', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2137, 'Lug, Terminal 5mm stud size', '2062106', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lug, Terminal 5mm stud size', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2138, 'Lug, Terminal 1mm-2.6mm', '2062107', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lug, Terminal 1mm-2.6mm', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2139, 'Lug, Terminal 2.6mm-6.6mm', '2062108', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lug, Terminal 2.6mm-6.6mm', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2140, 'Lug, Terminal RT 150mm, Short Barrel, 1 hole', '2062109', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lug, Terminal RT 150mm, Short Barrel, 1 hole', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2141, 'Terminal, Lugs Crimp Type 6 AWG', '2062110', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Terminal, Lugs Crimp Type 6 AWG', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2142, 'Terminal Lugs, Crimp type 2 AWG', '2062111', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Terminal Lugs, Crimp type 2 AWG', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2143, 'Motor, Tibb (TD 10.0872)', '2063001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Motor, Tibb (TD 10.0872)', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2144, 'Motor, Squirrel Cage Inducti', '2063013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Motor, Squirrel Cage Inducti', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2145, 'Control,Pendant XACA06SPECO2', '2063019A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Control,Pendant XACA06SPECO2', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2146, 'Motor, Elect. 7.5KW, 440V.60', '2063054', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Motor, Elect. 7.5KW, 440V.60', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2147, 'Motor, BBC Electric 3KW/855r', '2063058', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Motor, BBC Electric 3KW/855r', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2148, 'Motor, Reducer ElectricBBCty', '2063061', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Motor, Reducer ElectricBBCty', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2149, 'Motor, Pneumatic for Valve 6', '2063066', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Motor, Pneumatic for Valve 6', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2150, 'Multiflier, 931A', '2063101', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Multiflier, 931A', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2151, 'Nut, Lock 3/8\"dia.', '2064013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Lock 3/8\"dia.', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2152, 'Outlet, Welding w/Male Plug', '2065002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Outlet, Welding w/Male Plug', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2153, 'Outlet, Convenience 3 gang', '2065003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Outlet, Convenience 3 gang', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2154, 'Outlet, Socket \"Veto\"', '2065004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Outlet, Socket \"Veto\"', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2155, 'Oulet,Convenient Plate', '2065007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oulet,Convenient Plate', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2156, 'Outlet, Waterproof, 2 Gang', '2065018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Outlet, Waterproof, 2 Gang', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2157, 'Plug, Rubber Male', '2069001B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plug, Rubber Male', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2158, 'Connector, RJ-45', '2069006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Connector, RJ-45', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2159, 'Outlet,3 Prong Aircon w/ plate', '2069009A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Outlet,3 Prong Aircon w/ plate', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2160, 'Vibration Probe PN-33379 VE', '2072001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Vibration Probe PN-33379 VE', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2161, 'Vibration Probe 32987', '2072002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Vibration Probe 32987', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2162, 'Relay, Overlaod Thermal 3RU6116-1BB0,1.4-2.0A', '2076002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Overlaod Thermal 3RU6116-1BB0,1.4-2.0A', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2163, 'Relay, Overload 75-105', '2076004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Overload 75-105', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2164, 'Relay, Overload 3UA 63-80A', '2076005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Overload 3UA 63-80A', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2165, 'Relay, Ovrld 3UA58 40 2U 63-80', '2076005A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Ovrld 3UA58 40 2U 63-80', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2166, 'Relay,Overload Thermal 3RU6116-4AB0,11-16A', '2076008A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay,Overload Thermal 3RU6116-4AB0,11-16A', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2167, 'Relay,Ovrload 3UA5240 2C 16-25', '2076010B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay,Ovrload 3UA5240 2C 16-25', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2168, 'Relay, Overload Thermal 3US50', '2076011A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Overload Thermal 3US50', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2169, 'Relay, Overload 3UN5000 4A', '2076011B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Overload 3UN5000 4A', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2170, 'Relay,Overload Thermal 3RU5156-2XB2 80-110Amp', '2076012A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay,Overload Thermal 3RU5156-2XB2 80-110Amp', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2171, 'Relay, 3Va 4 660vx1400 20 SM', '2076013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, 3Va 4 660vx1400 20 SM', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2172, 'Relay, Overload Thermal 3RU616-1GB0,4.5-6.3A', '2076014B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Overload Thermal 3RU616-1GB0,4.5-6.3A', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2173, 'Relay, Ovrld 3UA58 40 2P 50-63', '2076017A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Ovrld 3UA58 40 2P 50-63', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2174, 'Relay, Overload 3UA58', '2076019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Overload 3UA58', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2175, 'Relay, Ovrld 3UA58 40 2V 57-70', '2076019B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Ovrld 3UA58 40 2V 57-70', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2176, 'Relay, Overload 3UA62 135-16', '2076021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Overload 3UA62 135-16', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2177, 'Relay, Timer 0-3-30sec110/60', '2076024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Timer 0-3-30sec110/60', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2178, 'Relay, Bimetal 3UAC4101 06 1', '2076033', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Bimetal 3UAC4101 06 1', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2179, 'Relay, Ovrld 3UA6040-2X 80-110', '2076045A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Ovrld 3UA6040-2X 80-110', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2180, 'Relay, Solid State', '2076046A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Solid State', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2181, 'Relay, Overload 32-50A 3UA58', '2076047', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Overload 32-50A 3UA58', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2182, 'Relay,Overload 3UA58 25-40Amps', '2076047A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay,Overload 3UA58 25-40Amps', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2183, 'Relay, Overload 100-160A', '2076048', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Overload 100-160A', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2184, 'Relay,Ovrload 3UA5840 2F 32-50', '2076050', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay,Ovrload 3UA5840 2F 32-50', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2185, 'Relay, Overload 50-63A', '2076051', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Overload 50-63A', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2186, 'Relay, Overload 10-13A', '2076054', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Overload 10-13A', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2187, 'Relay, Overload CEM 16-2417', '2076059', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Overload CEM 16-2417', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2188, 'Relay, Motor Prct\'n 10R-125', '2076060', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Motor Prct\'n 10R-125', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2189, 'Relay, DefiniteTimeOvercurre', '2076062', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, DefiniteTimeOvercurre', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2190, 'Relay, Overload 3UA66 40 3D', '2076067', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Overload 3UA66 40 3D', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2191, 'Relay,Ovrld3UA66 40 3B 125-2', '2076067A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay,Ovrld3UA66 40 3B 125-2', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2192, 'Relay, Ovrld TSA 11-12 4-6A', '2076069', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Ovrld TSA 11-12 4-6A', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2193, 'Relay, Ovrld 3UA55 40 2R 32-40', '2076070C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Ovrld 3UA55 40 2R 32-40', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2194, 'Relay, AC RS-349-327 w/socke', '2076071', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, AC RS-349-327 w/socke', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2195, 'Relay Overload 51-75A/440V', '2076074', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay Overload 51-75A/440V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2196, 'Relay,Ovrlod 3UA5540 2D 20-32A', '2076075B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay,Ovrlod 3UA5540 2D 20-32A', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2197, 'Relay,Ovrload3UA5040 1J 6.3-10', '2076079B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay,Ovrload3UA5040 1J 6.3-10', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2198, 'Relay, Overload 24V VDO', '2076080', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Overload 24V VDO', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2199, 'Relay, Overload 3UA50 1-1.26', '2076083A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Overload 3UA50 1-1.26', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2200, 'Relay,Ovrlod3UA5040 IC 1.6-2.5', '2076085', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay,Ovrlod3UA5040 IC 1.6-2.5', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2201, 'Relay, Ovrld 3UA50 40 1A 1-1.6', '2076086', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Ovrld 3UA50 40 1A 1-1.6', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2202, 'Relay, 7PU402-2AJ20', '2076088', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, 7PU402-2AJ20', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2203, 'Relay, AveragingM2ADS-AAA-R2', '2076089', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, AveragingM2ADS-AAA-R2', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2204, 'Relay, Series AgNI 1.5VA', '2076099', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Series AgNI 1.5VA', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2205, 'Relay,Ovrld3UA61 40 3J 110-1', '2076100', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay,Ovrld3UA61 40 3J 110-1', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2206, 'Relay, Overload 3UA66 160-25', '2076102', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Overload 3UA66 160-25', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2207, 'Relay, Overload PN-26750-038', '2076103', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Overload PN-26750-038', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2208, 'Relay, Overload PN-26750-041', '2076104', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Overload PN-26750-041', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2209, 'Relay, Themistor S1MO 110V', '2076105A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Themistor S1MO 110V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2210, 'Relay, Thermistor 24 VAC', '2076107B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Thermistor 24 VAC', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2211, 'Relay, 24Vdc PN-23100-067', '2076108', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, 24Vdc PN-23100-067', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2212, 'Relay, Thermal Overload 3UA50', '2076109', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Thermal Overload 3UA50', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2213, 'Relay, Plug-in 8Pin 24V 50/60', '2076110', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Plug-in 8Pin 24V 50/60', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2214, 'Relay, Thermistor 100-230V AC/', '2076112', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Thermistor 100-230V AC/', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2215, 'Relay, Auxillary w/ Socket', '2076113', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Auxillary w/ Socket', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2216, 'Relay, Generator Control Unit (GCU2)', '2076148', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Generator Control Unit (GCU2)', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2217, 'Relay, Generator Circuit Breaker Panel', '2076149', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Generator Circuit Breaker Panel', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2218, 'Relay, Generator Circuit Breakr Panel MM2XP-D', '2076149A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Generator Circuit Breakr Panel MM2XP-D', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2219, 'Relay, Generator Control Panel', '2076150', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Generator Control Panel', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2220, 'Timer, Analog w/8pin socket(PF', '2077005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Timer, Analog w/8pin socket(PF', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2221, 'Resistor, Wire 5w 20k 5ohms', '2078001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor, Wire 5w 20k 5ohms', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2222, 'Resistor, 15/25mm x 170mm', '2078002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor, 15/25mm x 170mm', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2223, 'Resistor, CWS 5/10mmx55mm 22', '2078003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor, CWS 5/10mmx55mm 22', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2224, 'Resistor, 10.000 ohms 1 watt', '2078004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor, 10.000 ohms 1 watt', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2225, 'Resistor, for Speaker VC 20O', '2078005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor, for Speaker VC 20O', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2226, 'Resistor, 2.2 ohms .5 watts', '2078006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor, 2.2 ohms .5 watts', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2227, 'Resistor, 3.9 ohms .5 watts', '2078007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor, 3.9 ohms .5 watts', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2228, 'Resistor, 10k ohms .5 watts', '2078008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor, 10k ohms .5 watts', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2229, 'Resistor, 22k ohms .5 watts', '2078009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor, 22k ohms .5 watts', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2230, 'Resistor, 100k ohms .5 watts', '2078012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor, 100k ohms .5 watts', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2231, 'Resistor, 150k ohms .5 wats', '2078013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor, 150k ohms .5 wats', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2232, 'Resistor, 500 ohms .5 watts', '2078015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor, 500 ohms .5 watts', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2233, 'Resistor, 1k 10w', '2078016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor, 1k 10w', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2234, 'Resistor,3k ohms kh 19058 5w', '2078017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor,3k ohms kh 19058 5w', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2235, 'Resistor, 5k 5 watts', '2078018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor, 5k 5 watts', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2236, 'Resistor, 8k ohms KH 16038 5', '2078020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor, 8k ohms KH 16038 5', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2237, 'Resistor, 10k 5 watts', '2078021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor, 10k 5 watts', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2238, 'Resistor, 10 /80W Wirewound-', '2078022', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor, 10 /80W Wirewound-', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2239, 'Resistor, 75 ohms 10ws', '2078023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor, 75 ohms 10ws', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25');
INSERT INTO `items` (`id`, `item_name`, `item_code`, `item_type`, `item_vendor_id`, `item_uom_id`, `item_reorder_point`, `item_category_id`, `item_quantity`, `item_sales_description`, `item_purchase_description`, `item_selling_price`, `item_cost_price`, `item_cogs_account_id`, `item_income_account_id`, `item_asset_account_id`, `created_at`) VALUES
(2240, 'Resistor,Quadroc Q400KLT4A/2', '2078024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor,Quadroc Q400KLT4A/2', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2241, 'Resistor, 33A 1/2W', '2078025', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor, 33A 1/2W', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2242, 'Resistor, 510 mega ohms 5% 5', '2078026', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor, 510 mega ohms 5% 5', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2243, 'Resistor, 10 /100W Wirewound', '2078027', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor, 10 /100W Wirewound', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2244, 'Resistor, #853291 Beckman', '2078028', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor, #853291 Beckman', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2245, 'Resistor, 7K/25W', '2078029', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor, 7K/25W', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2246, 'Resistor, #959092', '2078030', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor, #959092', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2247, 'Resistor, PH Amplifier 93872', '2078033', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor, PH Amplifier 93872', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2248, 'Resistor, fr PHMeter 3x93 21', '2078034', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor, fr PHMeter 3x93 21', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2249, 'Resistor, Variable', '2078035', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor, Variable', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2250, 'Resistor, 150 ohms 25 watts', '2078039', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor, 150 ohms 25 watts', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2251, 'Resistor, 10 ohms 25 watts', '2078040', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Resistor, 10 ohms 25 watts', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2252, 'Rectifier', '2079003A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rectifier', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2253, 'Regulator, Precision IR2000-02G-A', '2079004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Regulator, Precision IR2000-02G-A', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2254, 'Rectifier, 1113044', '2079005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rectifier, 1113044', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2255, 'Secondary Rock W/ Insulator', '2083001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Secondary Rock W/ Insulator', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2256, 'Rack, Secondary 3 phase', '2083001A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rack, Secondary 3 phase', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2257, 'Socket, Porcelain (Big)', '2084006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Socket, Porcelain (Big)', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2258, 'Socket, Porcelain (Small)', '2084008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Socket, Porcelain (Small)', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2259, 'Socket, Wall Light', '2084016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Socket, Wall Light', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2260, 'Socket, Bulb - Rubber', '2084017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Socket, Bulb - Rubber', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2261, 'Socket 8 Pin', '2084021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Socket 8 Pin', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2262, 'Spring, Coil, 45mm.x250mm.x7', '2085002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Coil, 45mm.x250mm.x7', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2263, 'Spring, Coil 65mmx289mmx11mm', '2085003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Coil 65mmx289mmx11mm', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2264, 'Spring, Moving#879294Contact', '2085006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Moving#879294Contact', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2265, 'Spring, Plain Merlin Gerin', '2085009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Plain Merlin Gerin', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2266, 'Spring, Rewind Drive', '2085010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Rewind Drive', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2267, 'Spring, Tension 2&3 Pen P/N', '2085011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Tension 2&3 Pen P/N', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2268, 'Drain, Auto Non-Clog w/ Timer', '2085018A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Drain, Auto Non-Clog w/ Timer', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2269, 'Starter, Fluorescent 4-65W/230', '2087006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Starter, Fluorescent 4-65W/230', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2270, 'Starter, Magnetic', '2087011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Starter, Magnetic', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2271, 'Starter, Magnetic (Relay)', '2087012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Starter, Magnetic (Relay)', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2272, 'Plug, Rubber Male', '2089001B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plug, Rubber Male', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2273, 'Switch, Knife 60A/600V', '2089005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Knife 60A/600V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2274, 'Switch, Momentary35A 6103K23', '2089012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Momentary35A 6103K23', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2275, 'Switch, Start/Stop Push Butt', '2089013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Start/Stop Push Butt', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2276, 'Switch, Micro 200A Fuse Line P', '2089021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Micro 200A Fuse Line P', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2277, 'Switch, Magnetic 2HP', '2089023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Magnetic 2HP', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2278, 'Switch, w/plate 2gang w/ screw', '2089029', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, w/plate 2gang w/ screw', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2279, 'Switch, Convenience Surface', '2089033', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Convenience Surface', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2280, 'Switch Limit Model:XCK-S141', '2089036', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch Limit Model:XCK-S141', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2281, 'Switch, 2gang w/plate', '2089039', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, 2gang w/plate', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2282, 'Switch, Pressure Diaphram op', '2089042', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Pressure Diaphram op', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2283, 'Switch, Micro UD #453-SIMA 1', '2089044', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Micro UD #453-SIMA 1', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2284, 'Switch, Limit (Metal)', '2089046', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Limit (Metal)', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2285, 'Switch, Float 5A, @ 110/220V', '2089048', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Float 5A, @ 110/220V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2286, 'Switch,Magnetic 1.5kW 3PST D', '2089053', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch,Magnetic 1.5kW 3PST D', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2287, 'Switch, Pressure', '2089060', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Pressure', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2288, 'Switch, Magnetic 7kw 230v 60', '2089064', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Magnetic 7kw 230v 60', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2289, 'Switch, Miniature OMRON', '2089065', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Miniature OMRON', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2290, 'Switch, Limit ZCK-M1 (Body) ZC', '2089068A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Limit ZCK-M1 (Body) ZC', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2291, 'Switch, Limit Part #P1FA030', '2089069', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Limit Part #P1FA030', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2292, 'Switch, Limit D4N 112G', '2089072A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Limit D4N 112G', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2293, 'Switch, Pressure HD 0.1-5.1b', '2089073', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Pressure HD 0.1-5.1b', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2294, 'Switch, Proximity PN-28390-938', '2089078', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Proximity PN-28390-938', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2295, 'Switch, Selector CA10A048/GB', '2089085', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Selector CA10A048/GB', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2296, 'Switch, Selector XB5AD33', '2089088', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Selector XB5AD33', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2297, 'Switch, Magnetic 12-18 amp.', '2089098', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Magnetic 12-18 amp.', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2298, 'Switch, Magnetic w/ housing SP', '2089101', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Magnetic w/ housing SP', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2299, 'Tape, Cotton 1/2\"', '2090004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tape, Cotton 1/2\"', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2300, 'Tape, Cotton 3/4\"', '2090005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tape, Cotton 3/4\"', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2301, 'Tape, Electrical Plastic', '2090010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tape, Electrical Plastic', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2302, 'Tape, Rubber 20milx3/4\"Wx8mL', '2090014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tape, Rubber 20milx3/4\"Wx8mL', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2303, 'Tee, Conduit 1\"', '2093001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tee, Conduit 1\"', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2304, 'Terminal, Block AB1-W435U', '2094001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Terminal, Block AB1-W435U', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2305, 'Terminal, Connector #500MCM', '2094004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Terminal, Connector #500MCM', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2306, 'Terminal, Block, 500A EL-W23', '2094007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Terminal, Block, 500A EL-W23', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2307, 'Terminal, Black Std. 4mm2, 35A', '2094011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Terminal, Black Std. 4mm2, 35A', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2308, 'Terminal, Grey Std. 6mm2, 46Amps, 750V', '2094012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Terminal, Grey Std. 6mm2, 46Amps, 750V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2309, 'Terminal, Grey Std. 10mm2, 63Amps, 750V', '2094013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Terminal, Grey Std. 10mm2, 63Amps, 750V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2310, 'Terminal, Center', '2094014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Terminal, Center', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2311, 'Sequence, Phase 440Vac, 3φ, 60', '2096002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sequence, Phase 440Vac, 3φ, 60', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2312, 'Sequence, Phase 220Vac, 3φ, 60', '2096003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sequence, Phase 220Vac, 3φ, 60', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2313, 'Shock, Absorber  w/Rubber Bush', '2097001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Shock, Absorber  w/Rubber Bush', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2314, 'Control, Trnsfrmr200VA 240/440', '2099005B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Control, Trnsfrmr200VA 240/440', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2315, 'Transformer, type UV120 74/6', '2099006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Transformer, type UV120 74/6', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2316, 'Transformer, Crnt AETO5KL0.5', '2099007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Transformer, Crnt AETO5KL0.5', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2317, 'Transformer, CrntKSD741005A2', '2099010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Transformer, CrntKSD741005A2', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2318, 'Transformer, 912 159 Beckman', '2099017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Transformer, 912 159 Beckman', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2319, 'Transmitter, Pressure P611', '2100003A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Transmitter, Pressure P611', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2320, 'Transmitter, Press.P611 4-20mA', '2100003B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Transmitter, Press.P611 4-20mA', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2321, 'Tube, Fluorescent 20W/230V', '2102002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tube, Fluorescent 20W/230V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2322, 'Tube, Flourescent 10W/230V GE', '2102004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tube, Flourescent 10W/230V GE', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2323, 'Lamp, Circular 32W/220V/60Hz', '2102005A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lamp, Circular 32W/220V/60Hz', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2324, 'Tube, Fluorescent LED T5 20W/230V', '2102010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tube, Fluorescent LED T5 20W/230V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2325, 'Valve, Solenoid 5/2 way', '2104002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Solenoid 5/2 way', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2326, 'Valve, Solenoid 3 way 110V/6', '2104004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Solenoid 3 way 110V/6', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2327, 'Valve, Solenoid 4KB219-M1C3', '2104008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Solenoid 4KB219-M1C3', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2328, 'Valve, Solenoid 4KB219-00-M1C3', '2104008B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Solenoid 4KB219-00-M1C3', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2329, 'Valve, Solenoid PN 39538251 3/2 WAY', '2104012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Solenoid PN 39538251 3/2 WAY', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2330, 'Valve, Solenoid23446750 3/2W', '2104012A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Solenoid23446750 3/2W', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2331, 'Valve, Solenoid 2/2way 100VA', '2104013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Solenoid 2/2way 100VA', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2332, 'Valve,Solenoid PN 39538052 2/2 WAY', '2104013A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve,Solenoid PN 39538052 2/2 WAY', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2333, 'Valve, Solenoid 110VAC 5/2way', '2104015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Solenoid 110VAC 5/2way', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2334, 'Valve, Solenoid 3/2 way', '2104016A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Solenoid 3/2 way', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2335, 'Valve,Solenoid PN-23446750', '2104022', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve,Solenoid PN-23446750', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2336, 'Valve, Solenoid 5/2 way 24V DC 1/4\"NPT', '2104076A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Solenoid 5/2 way 24V DC 1/4\"NPT', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2337, 'Valve, Solenoid 5/2 way, 220 Vac', '2104080', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Solenoid 5/2 way, 220 Vac', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2338, 'Varnish, Red Insulating', '2105001A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Varnish, Red Insulating', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2339, 'Varnish, Insulating Electrical', '2105001B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Varnish, Insulating Electrical', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2340, 'Varistor, 1700pF 100A 340V', '2105030', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Varistor, 1700pF 100A 340V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2341, 'Wire, Elect\'l 3.5mm2 THHN', '2106002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Elect\'l 3.5mm2 THHN', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2342, 'Wire, THW #12 (std) @150m/roll', '2106004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, THW #12 (std) @150m/roll', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2343, 'Wire, Stranded THHN #12', '2106007A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Stranded THHN #12', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2344, 'Wire, Electric #12 THHN', '2106007C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Electric #12 THHN', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2345, 'Wire, Magnet #18 (30kgs/roll', '2106018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Magnet #18 (30kgs/roll', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2346, 'Wire Magnet #21', '2106021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire Magnet #21', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2347, 'Wire, Magnet #23', '2106023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Magnet #23', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2348, 'Wire, Magnet #24', '2106024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Magnet #24', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2349, 'Wire, Magnet #25', '2106025', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Magnet #25', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2350, 'Wire, Magnet #26', '2106026', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Magnet #26', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2351, 'Wire, Magnet #28', '2106028', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Magnet #28', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2352, 'Wire, Magnet #30', '2106030', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Magnet #30', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2353, 'Wire, Magnet #31', '2106031', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Magnet #31', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2354, 'Wire, Magnet #33', '2106033', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Magnet #33', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2355, 'Wire,Magnet #34', '2106034', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire,Magnet #34', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2356, 'Wire, Magnet #35', '2106035', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Magnet #35', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2357, 'Wire, Magnet #37 HF 270 lbs.', '2106050', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Magnet #37 HF 270 lbs.', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2358, 'Wire, Magnet #38', '2106051', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Magnet #38', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2359, 'Wire, Magnet #8', '2106057', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Magnet #8', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2360, 'Wire, Magnet #9', '2106058', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Magnet #9', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2361, 'Wire, Magnet #41', '2106059', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Magnet #41', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2362, 'Wire, Magnet #4', '2106060', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Magnet #4', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2363, 'Wire, Magnet #7', '2106071', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Magnet #7', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2364, 'Wire, Flat', '2106073', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Flat', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2365, 'Wire, Magnet #22 AWG', '2106078', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Magnet #22 AWG', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2366, 'Wire,Electric #150mm THW Stranded', '2106092', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire,Electric #150mm THW Stranded', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2367, 'Wire,Electric #60mm THW Stranded', '2106093', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire,Electric #60mm THW Stranded', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2368, 'Wire,Electric #38mm THW Stranded', '2106094', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire,Electric #38mm THW Stranded', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2369, 'Wire, Electric #14mm THW Stranded', '2106095', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Electric #14mm THW Stranded', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2370, 'Wire,Electric # 5.5mm THW Stranded', '2106096', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire,Electric # 5.5mm THW Stranded', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2371, 'Wire,Electric#8AWG THHN Standard 150mtr./roll', '2106097', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire,Electric#8AWG THHN Standard 150mtr./roll', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2372, 'Thermocouple, Ungrounded 1/4\"φ', '2107002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Thermocouple, Ungrounded 1/4\"φ', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2373, 'Thermocouple, Single Type J', '2107003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Thermocouple, Single Type J', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2374, 'Split Knob w/screw', '2108001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Split Knob w/screw', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2375, 'Mylar Polyster Film .15 mils', '2110005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Mylar Polyster Film .15 mils', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2376, 'Transistor, BCY 56', '2111005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Transistor, BCY 56', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2377, 'Transistor, 2N 1893', '2111009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Transistor, 2N 1893', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2378, 'Transistor, 2N 5172', '2111010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Transistor, 2N 5172', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2379, 'Transistor, 2SB56', '2111013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Transistor, 2SB56', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2380, 'Transistor, C1061', '2111019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Transistor, C1061', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2381, 'Transistor, #2N346', '2111020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Transistor, #2N346', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2382, 'Transistor, BC 107', '2111021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Transistor, BC 107', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2383, 'Transistor, N2905A', '2111023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Transistor, N2905A', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2384, 'Main Contact point 3TA26 sz', '2112001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Main Contact point 3TA26 sz', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2385, 'Block, Contact NC 125V/300V', '2112003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Block, Contact NC 125V/300V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2386, 'Block, ContactTimeDelay 1NO1', '2112004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Block, ContactTimeDelay 1NO1', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2387, 'Block, Contact NC Push Butto', '2112004A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Block, Contact NC Push Butto', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2388, 'Contact Block', '2112006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact Block', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2389, 'Contact, Block ZB4BZ104 2NC', '2112010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Block ZB4BZ104 2NC', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2390, 'Contact, Block ZBE101 1 N/O', '2112011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Block ZBE101 1 N/O', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2391, 'Contact, Block ZBE101 1 N/C', '2112011A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Block ZBE101 1 N/C', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2392, 'Contact, Block ZBE102 1 N/C', '2112012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Contact, Block ZBE102 1 N/C', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2393, 'Aux.,Contact Black N-22', '2112014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Aux.,Contact Black N-22', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2394, 'Rod coupling #649A117 x 99', '2113003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rod coupling #649A117 x 99', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2395, 'Rod, Ground 5/8\"φ x 8\'', '2113007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rod, Ground 5/8\"φ x 8\'', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2396, 'CPU, 5/02 SLC 500 PN-1747-L524', '2116005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'CPU, 5/02 SLC 500 PN-1747-L524', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2397, 'CPU, 5/02 SLC 500 EEPROM PN-17', '2116005A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'CPU, 5/02 SLC 500 EEPROM PN-17', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2398, 'CPU, 5/03 SLC 500 PN 1747-L532', '2116006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'CPU, 5/03 SLC 500 PN 1747-L532', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2399, 'SLC, EEPROM Memory Module PN-1', '2116006A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'SLC, EEPROM Memory Module PN-1', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2400, 'Power, Supply 120 - 230Vac', '2117007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Power, Supply 120 - 230Vac', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2401, 'Rotor Assy. for P.H.', '2119003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rotor Assy. for P.H.', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2402, 'Filter Dryer, weld type', '2121002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter Dryer, weld type', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2403, 'Foam, Air Filter Element', '2121005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Foam, Air Filter Element', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2404, 'Coil, Heating 220V, 8\"φ', '2122004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coil, Heating 220V, 8\"φ', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2405, 'Element, Heating', '2122005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Element, Heating', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2406, 'Heat Sink, 4 x 12\"', '2122007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Heat Sink, 4 x 12\"', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2407, 'Detector, RT 3m PVC/PVC cabl', '2125003A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Detector, RT 3m PVC/PVC cabl', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2408, 'Detector, Resist.Temp. 0-200', '2125004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Detector, Resist.Temp. 0-200', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2409, 'Detctor, Resistnce Temp. 0-500', '2125008D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Detctor, Resistnce Temp. 0-500', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2410, 'Detector,RT 180mm 0-200°C 6mmφ', '2125010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Detector,RT 180mm 0-200°C 6mmφ', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2411, 'Detctor, Resistnce Temp. 0-500', '2125011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Detctor, Resistnce Temp. 0-500', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2412, 'Load Cell 50kgs.cap.Model:10', '2135103', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Load Cell 50kgs.cap.Model:10', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2413, 'Chart Roll for pneumatic Rec', '2137006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Chart Roll for pneumatic Rec', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2414, 'Conduit Fitting, 1\"', '2139002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Conduit Fitting, 1\"', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2415, 'Conduit LB Fitting 3/4\"', '2139009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Conduit LB Fitting 3/4\"', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2416, '\"T\" Conduit, 3/4\"', '2139011', 'Inventory', NULL, NULL, NULL, NULL, 0, '\"T\" Conduit, 3/4\"', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2417, 'Conduit Fitting 1-1/2\"', '2139015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Conduit Fitting 1-1/2\"', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2418, 'Diaprhagm,Rbbr.Neopreme w/pl', '2140008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Diaprhagm,Rbbr.Neopreme w/pl', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2419, 'Brake Disc 104/5', '2141001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brake Disc 104/5', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2420, 'Positioner, Pneumatic4-20mA DC', '2143005A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Positioner, Pneumatic4-20mA DC', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2421, 'Positioner, Pneumatic 3-15psi', '2143011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Positioner, Pneumatic 3-15psi', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2422, 'Gum, Seal Virginia', '2145008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gum, Seal Virginia', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2423, 'Holder, Flourescent L-Type', '2147002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Holder, Flourescent L-Type', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2424, 'Holder, Fluorescent Round Type', '2147004A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Holder, Fluorescent Round Type', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2425, 'Holder, Flou. w/out socket', '2147005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Holder, Flou. w/out socket', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2426, 'Holder Insulator (small) 5.8', '2147007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Holder Insulator (small) 5.8', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2427, 'Holder, Fuse Diazed Bottle Typ', '2147009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Holder, Fuse Diazed Bottle Typ', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2428, 'Digital Panel Indicator', '2148002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Digital Panel Indicator', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2429, 'Panel, Touch Screen 6\"', '2148009A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Panel, Touch Screen 6\"', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2430, 'Ignitor, SN58 100-600W', '2150002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ignitor, SN58 100-600W', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2431, 'Module, Simatic Digital Outp', '2151003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Module, Simatic Digital Outp', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2432, 'Module, Simatic Digital Inpu', '2151004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Module, Simatic Digital Inpu', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2433, 'Oscillator Firing Card', '2154100', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oscillator Firing Card', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2434, 'Module, Tryister Oscillator PN', '2154101A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Module, Tryister Oscillator PN', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2435, 'Sounder, Electronic 110/230V', '2155003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sounder, Electronic 110/230V', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2436, 'Sounder, Electronic', '2155006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sounder, Electronic', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2437, 'Arm, Rocker 630Amps 2400V, 3', '2156001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Arm, Rocker 630Amps 2400V, 3', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2438, '\"O\" Ring Bages RN IN 2689000', '2161010', 'Inventory', NULL, NULL, NULL, NULL, 0, '\"O\" Ring Bages RN IN 2689000', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2439, '\"O\"Ring Bagues RN 7N 2689011', '2161012', 'Inventory', NULL, NULL, NULL, NULL, 0, '\"O\"Ring Bagues RN 7N 2689011', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2440, '\"O\"Ring Pheumatic Recorder E', '2161014', 'Inventory', NULL, NULL, NULL, NULL, 0, '\"O\"Ring Pheumatic Recorder E', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2441, '\"O\"Ring Retaining #97188055', '2161015', 'Inventory', NULL, NULL, NULL, NULL, 0, '\"O\"Ring Retaining #97188055', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2442, 'Ring, Terminal 2.7-6.6mm²xM8', '2161024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Terminal 2.7-6.6mm²xM8', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2443, 'Ring, Terminal 7.0-10.5mm²xM', '2161025', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Terminal 7.0-10.5mm²xM', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2444, 'Potentiometer, 2W 500V 250ohms', '2164002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Potentiometer, 2W 500V 250ohms', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2445, 'Rheostat, Wirewound 25W 150o', '2164004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rheostat, Wirewound 25W 150o', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2446, 'Thermometer, BiMetal 0-200°C', '2165001B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Thermometer, BiMetal 0-200°C', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2447, 'Thrmomtr,Bi-Mtllic 0-200°C Stem L6\"w/Thrmowll', '2165001D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Thrmomtr,Bi-Mtllic 0-200°C Stem L6\"w/Thrmowll', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2448, 'Thermometer, Industrial  50mm', '2165007C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Thermometer, Industrial  50mm', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2449, 'Thermometer, Industrial 4\"stem', '2165017A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Thermometer, Industrial 4\"stem', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2450, 'Thermometer, Vapor 30°-300°C', '2165024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Thermometer, Vapor 30°-300°C', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2451, 'Thermometer, Bi-metallic 0-250', '2165033', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Thermometer, Bi-metallic 0-250', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2452, 'Thermowell, BI Metal 1/2\" NPTF', '2165035', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Thermowell, BI Metal 1/2\" NPTF', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2453, 'Transmitter,Pressure 4-20mAdc', '2167010A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Transmitter,Pressure 4-20mAdc', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2454, 'Transducer, (I/P)3-15psi,2wire', '2167017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Transducer, (I/P)3-15psi,2wire', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2455, 'Transducer, Current Measurin', '2167021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Transducer, Current Measurin', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2456, 'Trnsmtr,Diff.Pres.GTX30D-BAAADCB-AXXAXA1-Q1W1', '2167022A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Trnsmtr,Diff.Pres.GTX30D-BAAADCB-AXXAXA1-Q1W1', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2457, 'Turnbuckle, #18-28141 Masone', '2168001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Turnbuckle, #18-28141 Masone', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2458, 'Buckle, Turn 20mm Eye Hook Type', '2168005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Buckle, Turn 20mm Eye Hook Type', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2459, 'Fulmen Storage Battery', '2175004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fulmen Storage Battery', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2460, 'Liquid, Tight Metal Round 15', '2177004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Liquid, Tight Metal Round 15', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2461, 'Magnetic Pick-up MN: MP-62TA', '2179002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Magnetic Pick-up MN: MP-62TA', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2462, 'Prox.SensorTachmtr.#28390-09', '2179004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Prox.SensorTachmtr.#28390-09', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2463, 'Sensor, Proximity NJ5-11-NG', '2179007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sensor, Proximity NJ5-11-NG', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2464, 'Capacitive Sensor', '2179013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Capacitive Sensor', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2465, 'Sensor,Prox NBB5-18GM50-E2V1', '2179021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sensor,Prox NBB5-18GM50-E2V1', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2466, 'Sensor, Prox. NBB8-18GM50-E2-V1', '2179021A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sensor, Prox. NBB8-18GM50-E2-V1', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2467, 'Sensor, Pressure PN-29853809', '2179030', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sensor, Pressure PN-29853809', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2468, 'Sensor,Prox. SIED-M12NB-ZS-K-L', '2179031', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sensor,Prox. SIED-M12NB-ZS-K-L', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2469, 'Sensor, Speed Magnetic Pick-up', '2179032', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sensor, Speed Magnetic Pick-up', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2470, 'ARC Chamber size 2 type31Y', '2181002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'ARC Chamber size 2 type31Y', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2471, 'Chamber, Arch', '2181003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Chamber, Arch', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2472, 'Thyrister', '2183001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Thyrister', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2473, 'Thyrstr Fuse, 1000V(3pcs./se', '2183003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Thyrstr Fuse, 1000V(3pcs./se', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2474, 'Thyrstr Fuse, Modicators', '2183004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Thyrstr Fuse, Modicators', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2475, 'Flexible,Conduit Connector 2', '2185004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Flexible,Conduit Connector 2', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2476, 'Thyristor, PN-23510-005', '2185005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Thyristor, PN-23510-005', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2477, 'Flexible, Conduit Cnnctr 1\"', '2185007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Flexible, Conduit Cnnctr 1\"', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2478, 'Flexible, Conduit Cnnctr.3/4', '2185008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Flexible, Conduit Cnnctr.3/4', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2479, 'Flexible Conduit Connector 1', '2185009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Flexible Conduit Connector 1', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2480, 'Front Panel for 1/a control', '2186001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Front Panel for 1/a control', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2481, 'Voltage,Monitor Type: JVM-2', '2186003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Voltage,Monitor Type: JVM-2', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2482, 'Voltage, Over-Under440Vac,60', '2186011A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Voltage, Over-Under440Vac,60', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2483, 'Module #6DR2800/85', '2187005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Module #6DR2800/85', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2484, 'Control, Pendant PN-609-4483', '2187021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Control, Pendant PN-609-4483', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2485, 'Cylinder, Plough Hor. 4\"φBor', '2189006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cylinder, Plough Hor. 4\"φBor', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2486, 'Cylinder, Pneumatic DSBG-160-', '2189010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cylinder, Pneumatic DSBG-160-', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2487, 'Actuator, Pneumatic DFPD-40-RP-90-RD-F0507', '2189013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Actuator, Pneumatic DFPD-40-RP-90-RD-F0507', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2488, 'Actuator, Pneumatic DFPD-80-RP-90-RD-F0507', '2189014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Actuator, Pneumatic DFPD-80-RP-90-RD-F0507', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2489, 'Actuator, Pneumatic Double act', '2189015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Actuator, Pneumatic Double act', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2490, 'Actuator, Pneumatc Double Acting Model: S150D', '2189018A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Actuator, Pneumatc Double Acting Model: S150D', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2491, 'Silencer, Pneumatic Adjustable', '2191001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Silencer, Pneumatic Adjustable', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2492, 'End Section', '2194001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'End Section', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2493, 'Rail, DIN Deep Top Hat 1x35x', '2195002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rail, DIN Deep Top Hat 1x35x', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2494, 'Rail, Din', '2195002B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rail, Din', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2495, 'Mounting, Wall 4x1/2 NPT', '2195003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Mounting, Wall 4x1/2 NPT', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2496, 'Analogue, SLC 4 I/O Module', '2195004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Analogue, SLC 4 I/O Module', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2497, 'Analogue, SLC 500 I/O Module P', '2195004A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Analogue, SLC 500 I/O Module P', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2498, 'Fuse, Class CC 2Amps/600Vac', '2195005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, Class CC 2Amps/600Vac', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2499, 'Moulding,PVC Electric Wire Cable Trunking', '2196001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Moulding,PVC Electric Wire Cable Trunking', NULL, NULL, NULL, 4615, 4603, 3192, '2024-09-19 07:27:25'),
(2500, 'Armature, Starter 24V', '3008009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Armature, Starter 24V', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2501, 'Piston, for Brake Caliper', '3011001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Piston, for Brake Caliper', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2502, 'Battery, 12V/9plates 2SMF/N50L', '3012002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Battery, 12V/9plates 2SMF/N50L', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2503, 'Battery, 12V/21 Plate w/ sol.', '3012004B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Battery, 12V/21 Plate w/ sol.', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2504, 'Battery, 6SMF/N100 w/ solution', '3012010B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Battery, 6SMF/N100 w/ solution', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2505, 'Battery, 12V NS60', '3012012A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Battery, 12V NS60', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2506, 'Battery, 12V/7Plates', '3012017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Battery, 12V/7Plates', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2507, 'Battery, N70 3SMF', '3012017A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Battery, N70 3SMF', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2508, 'Battery, 3SMF 270ZL D3IL', '3012021A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Battery, 3SMF 270ZL D3IL', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2509, 'Battery, N120L/2D/F51/17Plates', '3012022', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Battery, N120L/2D/F51/17Plates', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2510, 'Hub,Bearing w/Cup#47686/4768', '3013020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hub,Bearing w/Cup#47686/4768', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2511, 'Bearing,Ball # 3211/6211', '3013022', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing,Ball # 3211/6211', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2512, 'Bearing Con-Rod  0.25', '3013024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing Con-Rod  0.25', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2513, 'Bearing w/cup 30212', '3013026', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing w/cup 30212', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2514, 'Bearing Pilot TC-05 (93516)', '3013031', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing Pilot TC-05 (93516)', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2515, 'Bearing, Ball #6204 ZZ', '3013034', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6204 ZZ', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2516, 'Bearing, Stick # 4908', '3013047', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Stick # 4908', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2517, 'Bearing, #6211 Hoover', '3013048', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #6211 Hoover', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2518, 'Bearing, Ball #640522', '3013049', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #640522', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2519, 'Bearing, Ball 6205 ZZ', '3013051B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6205 ZZ', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2520, 'Bearing, Roller PN-1A01002', '3013056A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller PN-1A01002', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2521, 'Bearing, Ball #6002 2RSR', '3013066', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6002 2RSR', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2522, 'Bearing, Ball #6006ZZ', '3013066D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6006ZZ', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25');
INSERT INTO `items` (`id`, `item_name`, `item_code`, `item_type`, `item_vendor_id`, `item_uom_id`, `item_reorder_point`, `item_category_id`, `item_quantity`, `item_sales_description`, `item_purchase_description`, `item_selling_price`, `item_cost_price`, `item_cogs_account_id`, `item_income_account_id`, `item_asset_account_id`, `created_at`) VALUES
(2523, 'Bearing, Roller PN-1A01001', '3013068', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller PN-1A01001', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2524, 'Bearing, Timing Belt Tensioner', '3013069B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Timing Belt Tensioner', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2525, 'Brg.Ball #6011 Single Row', '3013073', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brg.Ball #6011 Single Row', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2526, 'Bearing, Ball 6207 2ZCM', '3013079A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6207 2ZCM', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2527, 'Bearing, 1B4109', '3013080', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, 1B4109', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2528, 'Bearing, 1B4116', '3013081', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, 1B4116', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2529, 'Bearing, Cone Rlr#380-890132', '3013091', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Cone Rlr#380-890132', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2530, 'Brg.Ball #BL211N Single Row', '3013092', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brg.Ball #BL211N Single Row', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2531, 'Bearing, Roller#30305DJ/3030', '3013102', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller#30305DJ/3030', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2532, 'Bearing, Clutch ReleaseCT55B', '3013107', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Clutch ReleaseCT55B', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2533, 'Bearing, Tprd w/ cup #32209J', '3013111C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Tprd w/ cup #32209J', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2534, 'Brg.Roller w/ Cup #3778/3720', '3013117', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brg.Roller w/ Cup #3778/3720', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2535, 'Bearing, Needle # 7F-7983', '3013120', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Needle # 7F-7983', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2536, 'Bearing, Tapered Roller 2587', '3013123', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Tapered Roller 2587', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2537, 'Brg.Ball #3310', '3013126', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brg.Ball #3310', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2538, 'Bearing, Needle 380-107246-1', '3013127', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Needle 380-107246-1', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2539, 'Bearing, #593A/594 w/ cup', '3013131', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #593A/594 w/ cup', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2540, 'Bearing, Needle 395-1040456-', '3013134', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Needle 395-1040456-', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2541, 'Bearing,Roller #36690/36620', '3013138', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing,Roller #36690/36620', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2542, 'Bearing,Tprd Rllr 2-5/8x1-3/16', '3013147', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing,Tprd Rllr 2-5/8x1-3/16', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2543, 'Bearing, Tprd Rllr 2-7/16x7/', '3013148', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Tprd Rllr 2-7/16x7/', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2544, 'Bearing, Thrust (STD)', '3013150', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Thrust (STD)', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2545, 'Bearing, Ball Inner/Outer Ra', '3013151', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball Inner/Outer Ra', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2546, 'Bearing, Cross Assy. Universal', '3013152', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Cross Assy. Universal', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2547, 'Bearing, M86610 NR', '3013156', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, M86610 NR', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2548, 'Bearing, Tapered Roller w/cu', '3013159', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Tapered Roller w/cu', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2549, 'Bearing, Cross GUT-21', '3013160B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Cross GUT-21', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2550, 'Bearing, Cup#46720/Cone#4678', '3013164', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Cup#46720/Cone#4678', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2551, 'Bearing, Cone JD7445', '3013169', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Cone JD7445', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2552, 'Bearing, HR 32213 J', '3013182', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, HR 32213 J', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2553, 'Bearing, Clutch Release FC4D', '3013186', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Clutch Release FC4D', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2554, 'Bearing, Cross', '3013191', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Cross', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2555, 'Bearing, Cross', '3013191A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Cross', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2556, 'Bearing,IT90(Sleeve)', '3013193', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing,IT90(Sleeve)', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2557, 'Bearing,Hub w/cup Frnt #32313J', '3013197A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing,Hub w/cup Frnt #32313J', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2558, 'Bearing, Tapered Roller#0564', '3013199', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Tapered Roller#0564', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2559, 'Bearing, Pilot #93516-C', '3013201', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Pilot #93516-C', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2560, 'Bearing, Ball P/N 380-221438', '3013202', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball P/N 380-221438', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2561, 'Bearing,Ball 6209 380-889022', '3013204', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing,Ball 6209 380-889022', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2562, 'Bearing, #6308 40IDx118ODx34', '3013213', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #6308 40IDx118ODx34', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2563, 'Bearing, RollerTorqueConvert', '3013214', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, RollerTorqueConvert', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2564, 'Bearing, Ball BCA-308F Seal', '3013219A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball BCA-308F Seal', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2565, 'Bearing PN#380-888930-1', '3013223', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing PN#380-888930-1', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2566, 'Bearing, Hub #LM48548/LM4851', '3013227', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Hub #LM48548/LM4851', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2567, 'Bearing, Spider 2K3631A', '3013248C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Spider 2K3631A', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2568, 'Bearing, Spider Assy. IS-967', '3013259', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Spider Assy. IS-967', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2569, 'Bearing, Ball 6210 RS', '3013264', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6210 RS', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2570, 'Bearing, PN #6310NR', '3013265', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, PN #6310NR', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2571, 'Bearing, Tpred Rllr 32220a', '3013267', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Tpred Rllr 32220a', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2572, 'Bearing, Cup #46104', '3013270', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Cup #46104', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2573, 'Bearing,Tapered R92Z-6gA', '3013277', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing,Tapered R92Z-6gA', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2574, 'Bearing, Cross KC 3838D', '3013280A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Cross KC 3838D', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2575, 'Bearing, Ball #380-118046-1', '3013285', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #380-118046-1', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2576, 'Bearing, Ball #3L10', '3013296', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #3L10', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2577, 'Bearing, Cross Universal Joint GMB MC-7030', '3013299D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Cross Universal Joint GMB MC-7030', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2578, 'Bearing,Cross Spider PN6H2577', '3013300A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing,Cross Spider PN6H2577', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2579, 'Bearing, Roller w/cup #1220', '3013306', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller w/cup #1220', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2580, 'Ball Bearing #6210 2Z', '3013313', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ball Bearing #6210 2Z', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2581, 'Bearing, 7M1154', '3013316', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, 7M1154', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2582, 'Bearing, #42381', '3013318B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #42381', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2583, 'Bearing, Cone w/cup #2K9295/', '3013319', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Cone w/cup #2K9295/', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2584, 'Bearing, Cup4T-68712/Cone684', '3013320A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Cup4T-68712/Cone684', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2585, 'Bearing, Tapared Rllr PN-028', '3013322A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Tapared Rllr PN-028', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2586, 'Bearing, #4T-43125', '3013322B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #4T-43125', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2587, 'Bearing, Ball # 6003-2RS PL-', '3013329', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball # 6003-2RS PL-', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2588, 'Bearing, Uppr/Lwr Arm #32010JR', '3013330A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Uppr/Lwr Arm #32010JR', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2589, 'Bearing, Hub Wheel #32310 JR', '3013330B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Hub Wheel #32310 JR', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2590, 'Bearing, Rllr #44163/44363D', '3013333', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Rllr #44163/44363D', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2591, 'Sleeve, Bearing #380-889022-', '3013334', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sleeve, Bearing #380-889022-', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2592, 'Bearing, Roller #380-215440-', '3013342', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller #380-215440-', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2593, 'Bearing, Ball 6307Z NR', '3013343A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 6307Z NR', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2594, 'Bearing, Roller Assy. #6B-51', '3013347', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller Assy. #6B-51', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2595, 'Bearing, Ball #3L10/6010ZZE', '3013358', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #3L10/6010ZZE', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2596, 'Bearing, Tprd#30214JR/302214', '3013359', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Tprd#30214JR/302214', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2597, 'Brg.Roller Pilot #94622', '3013360', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brg.Roller Pilot #94622', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2598, 'Bearing, Pilot Needle #7120', '3013365', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Pilot Needle #7120', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2599, 'Bearing, Connecting Rod Journal', '3013390A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Connecting Rod Journal', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2600, 'Bearing, Hub w/cup #LM11949', '3013450', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Hub w/cup #LM11949', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2601, 'Bearing, RllrTpprd #LM 67010', '3013452', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, RllrTpprd #LM 67010', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2602, 'Bearing, Cone # HH 506349', '3013467', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Cone # HH 506349', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2603, 'Hub Bearing w/cup frt.501310', '3013516', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hub Bearing w/cup frt.501310', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2604, 'Hub Bearing w/cup outer fron', '3013518', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hub Bearing w/cup outer fron', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2605, 'Bearing, cup #2K9296', '3013523', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, cup #2K9296', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2606, 'Bearing, Hub Cone/Cup #395', '3013533C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Hub Cone/Cup #395', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2607, 'Bearing, Pilot #1-09810-018-', '3013535', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Pilot #1-09810-018-', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2608, 'Bearing, Pinion w/ Cup #3031', '3013536', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Pinion w/ Cup #3031', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2609, 'Bearing, Ball #6216', '3013540', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6216', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2610, 'Bearing, Roller U35-8BCG38', '3013551B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller U35-8BCG38', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2611, 'Bearing,Tprd Rllr cup#55176C', '3013553', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing,Tprd Rllr cup#55176C', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2612, 'Bearing,Tprd Rllr cup#55187C', '3013556', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing,Tprd Rllr cup#55187C', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2613, 'Bearing, Rllr Cup 380-890130', '3013566', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Rllr Cup 380-890130', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2614, 'Bearing, #5P-9176 (6B5163)', '3013613', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #5P-9176 (6B5163)', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2615, 'Bearing, Ball #6211 w/cup', '3013621', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6211 w/cup', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2616, 'Bearing #394-A (Cone & Cup)', '3013642', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing #394-A (Cone & Cup)', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2617, 'Bearing, #394A Cup # 399A Cone', '3013642A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #394A Cup # 399A Cone', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2618, 'Bearing, Tprd Rllr w/cup inn', '3013647', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Tprd Rllr w/cup inn', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2619, 'Bearing, Tprd Rllr w/cup otr', '3013648', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Tprd Rllr w/cup otr', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2620, 'Bearing, Planetary Cup & Con', '3013675', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Planetary Cup & Con', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2621, 'Bearing, Ball #6207 \"NSK\" /', '3013678', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball #6207 \"NSK\" /', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2622, 'Bearing, w/cup T 12788-2720', '3013701', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, w/cup T 12788-2720', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2623, 'Bearing, Steering Sector [sm', '3013714', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Steering Sector [sm', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2624, 'Bearing, #4M-9183', '3013719', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, #4M-9183', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2625, 'Bearing, Hyatt 25.4x34.9x2.4', '3013729', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Hyatt 25.4x34.9x2.4', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2626, 'Bearing, Tapared Rllr outer', '3013734', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Tapared Rllr outer', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2627, 'Bearing,Roller SC070821DVSH2', '3013789', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing,Roller SC070821DVSH2', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2628, 'Bearing, Tensioner Balancer', '3013802A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Tensioner Balancer', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2629, 'Bearing, Ball NUP309NR', '3013811', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball NUP309NR', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2630, 'Bearing, Cross #GUIS-65', '3013813', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Cross #GUIS-65', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2631, 'Bearing, Cross # GUIS-65', '3013813A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Cross # GUIS-65', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2632, 'Bearing, Main Crank Journal', '3013827', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Main Crank Journal', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2633, 'Bearing, Ball 1T-0993', '3013828', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 1T-0993', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2634, 'Bearing, Ball 1T-0994', '3013829', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Ball 1T-0994', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2635, 'Bearing, Sleeve #2M2827', '3013830', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Sleeve #2M2827', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2636, 'Bearing, Main PN 232-3233', '3013831', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Main PN 232-3233', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2637, 'Bearing, Connecting Rod PN 8N-8220', '3013832', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Connecting Rod PN 8N-8220', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2638, 'Bearing, Thrust Plate PN 6N-8940 @2pc./set', '3013833', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Thrust Plate PN 6N-8940 @2pc./set', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2639, 'Bearing,Roller JM714249/10 75mmx120mmx31mmT', '3013834', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing,Roller JM714249/10 75mmx120mmx31mmT', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2640, 'Bearing, Roller LM613410/LM613449', '3013835', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Roller LM613410/LM613449', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2641, 'Tensioner Adjuster (Sway Chain SJ24944)', '3014008A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tensioner Adjuster (Sway Chain SJ24944)', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2642, 'Belt-V, #A-60 Serrated type', '3015002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, #A-60 Serrated type', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2643, 'Belt-V, # 5470 17x1170Li', '3015012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, # 5470 17x1170Li', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2644, 'Belt, Fan (B-45)', '3015012A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt, Fan (B-45)', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2645, 'Belt, Timing-SUZUKI 12ValveF', '3015013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt, Timing-SUZUKI 12ValveF', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2646, 'Belt-V, RECMF-9410', '3015025A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, RECMF-9410', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2647, 'Belt-V, A45 V13 X 1180', '3015038A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, A45 V13 X 1180', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2648, 'Belt-V, #RECMF 6290 - steeri', '3015067', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, #RECMF 6290 - steeri', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2649, 'Belt-V, #9.5x650', '3015078', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, #9.5x650', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2650, 'Belt, Alternator', '3015088A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt, Alternator', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2651, 'Belt, Aircon', '3015090', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt, Aircon', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2652, 'Belt, Timing', '3015095', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt, Timing', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2653, 'Belt-V, PN-900341 Non-Serrat', '3015097', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, PN-900341 Non-Serrat', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2654, 'Belt, Balancer', '3015099A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt, Balancer', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2655, 'Belt-V, #B-52 Serrated type', '3015104A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, #B-52 Serrated type', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2656, 'Belt, V B-52 Non Serrated', '3015104B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt, V B-52 Non Serrated', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2657, 'Belt-V,  A-43 Serrated type', '3015107A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V,  A-43 Serrated type', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2658, 'Belt, Fan #3470-13X1170Li', '3015118A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt, Fan #3470-13X1170Li', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2659, 'Belt, Fan 4PK1095', '3015124', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt, Fan 4PK1095', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2660, 'Belt, Fan #8490', '3015128', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt, Fan #8490', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2661, 'Belt-V, 17X1090Li Serrated', '3015131A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, 17X1090Li Serrated', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2662, 'Belt, Fan #6480', '3015133', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt, Fan #6480', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2663, 'Belt-V, RECMF-9910', '3015135', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, RECMF-9910', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2664, 'Belt-V, HD #7410/22x990L', '3015135A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, HD #7410/22x990L', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2665, 'Belt-V, #5630/17x1575LI B-61', '3015136', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, #5630/17x1575LI B-61', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2666, 'Belt-V, #9450', '3015141', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, #9450', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2667, 'Belt-V, #13x1370Li Serrated', '3015142', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, #13x1370Li Serrated', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2668, 'Belt-V, #8870', '3015143', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Belt-V, #8870', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2669, 'Blade, Wiper 17\"L', '3016014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Blade, Wiper 17\"L', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2670, 'Blade, Wiper 40\"L', '3016039', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Blade, Wiper 40\"L', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2671, 'Bolt, Torque Rod 18mmφx280mm', '3017006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Torque Rod 18mmφx280mm', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2672, 'Bolt, Center w/ Hi-Nut 12x250', '3017014A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Center w/ Hi-Nut 12x250', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2673, 'Bolt, Drum Front RH Universal Type', '3017018A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Drum Front RH Universal Type', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2674, 'Bolt, Drum Front LH Thread', '3017019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Drum Front LH Thread', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2675, 'Bolt, Drum Front LH Universal Type', '3017019A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Drum Front LH Universal Type', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2676, 'Bolt, Drum Rear Right Universal', '3017019B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Drum Rear Right Universal', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2677, 'Bolt, Drum Rear Left Universal', '3017019C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Drum Rear Left Universal', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2678, 'Bolt, Torque Rod 18mmx110mm', '3017037C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Torque Rod 18mmx110mm', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2679, 'Bolt, Drum w/nut RH Hand Threa', '3017082', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Drum w/nut RH Hand Threa', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2680, 'Bolt, Hex Hd Graded 3/8x2½', '3017090', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Hex Hd Graded 3/8x2½', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2681, 'Bolt, Eye Nozzle Holder', '3017097', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Eye Nozzle Holder', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2682, 'Bolt, Center 16mmx300mm Rear', '3017105A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Center 16mmx300mm Rear', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2683, 'Bolt, Center 16mmx220mmL', '3017105B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Center 16mmx220mmL', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2684, 'Bolt-U Rear 27mmφx93mmWx350mmL', '3017141', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt-U Rear 27mmφx93mmWx350mmL', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2685, 'Bolt, U (Rear) 22mmx83mmx460mm', '3017143A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, U (Rear) 22mmx83mmx460mm', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2686, 'Bolt, Axle 14mmx59mmx2mm thread', '3017166A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Axle 14mmx59mmx2mm thread', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2687, 'Bolt, Torque Rod w/nut 18x280', '3017219', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Torque Rod w/nut 18x280', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2688, 'Bolt,Graded w/ nut 3/8\"x2-1/', '3017261', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt,Graded w/ nut 3/8\"x2-1/', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2689, 'Bolt, Caliper 8mmx23mm', '3017288', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Caliper 8mmx23mm', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2690, 'Bolt, U (Rear) 19mmφx83mmWx405mmL', '3017315', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, U (Rear) 19mmφx83mmWx405mmL', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2691, 'Bolt, Graded PN-1D4616', '3017316', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Graded PN-1D4616', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2692, 'Bolt, w/ nut 1/2\"φx3\"L Graded', '3017317', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, w/ nut 1/2\"φx3\"L Graded', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2693, 'Boots, Rubber', '3019004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Boots, Rubber', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2694, 'Boots, Rubber 1-3/8\"', '3019028', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Boots, Rubber 1-3/8\"', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2695, 'Boots, Rubber 7/8\" (TLC)', '3019032', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Boots, Rubber 7/8\" (TLC)', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2696, 'Boots, Rubber SC-80334/C12-8', '3019034', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Boots, Rubber SC-80334/C12-8', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2697, 'Brush, Carbon SM-37', '3023010A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush, Carbon SM-37', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2698, 'Brush, Carbon-Starter #SK-29', '3023017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush, Carbon-Starter #SK-29', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2699, 'Brush, Carbon SK 22/23 \"NCC\"', '3023022', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush, Carbon SK 22/23 \"NCC\"', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2700, 'Bulb, Auto 12V/5W S.C. Small', '3024002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Auto 12V/5W S.C. Small', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2701, 'Bulb, Auto 12V/5W D.C. Small', '3024005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Auto 12V/5W D.C. Small', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2702, 'Bulb, Auto 24V/21W SC Big', '3024009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Auto 24V/21W SC Big', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2703, 'Bulb, Auto 24V/21W Big DC', '3024011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Auto 24V/21W Big DC', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2704, 'Bulb, Peanut 12V/5W Small', '3024012A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Peanut 12V/5W Small', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2705, 'Bulb, Halogen 12V 100/90W', '3024016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Halogen 12V 100/90W', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2706, 'Bulb, Auto 12V 21/5W', '3024035', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Auto 12V 21/5W', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2707, 'Bulb, Halogen 24V 100/90W', '3024043', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bulb, Halogen 24V 100/90W', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2708, 'Bushing, for 6x6', '3025001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, for 6x6', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2709, 'Bushing, #R-30829R', '3025016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, #R-30829R', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2710, 'Bushing, Piston Pin FD30JT-1', '3025029', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Piston Pin FD30JT-1', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2711, 'Bushing, Leaf Spring Rubber', '3025046', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Leaf Spring Rubber', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2712, 'Bushing, Leaf Spring Rbber P', '3025047', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Leaf Spring Rbber P', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2713, 'Bushing, Shock Absorber', '3025064', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Shock Absorber', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2714, 'Bushing, ShockAbsorber16x33x', '3025065', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, ShockAbsorber16x33x', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2715, 'Bushing, Upper Swing Arm Rubbe', '3025071B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Upper Swing Arm Rubbe', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2716, 'Bushing, #SH3938', '3025085', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, #SH3938', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2717, 'Bushing, #380-222214-1', '3025097', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, #380-222214-1', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2718, 'Bushing, Stabilizer 1/2\"', '3025126', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, Stabilizer 1/2\"', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2719, 'Bushing 3/4\"x1\"x2-1/2L', '3025194', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing 3/4\"x1\"x2-1/2L', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2720, 'Bushing, PN-07177-07540', '3025204', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bushing, PN-07177-07540', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2721, 'Cable, Control PN-381-980617', '3026003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cable, Control PN-381-980617', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2722, 'Cable, Spark Plug', '3026042', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cable, Spark Plug', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2723, 'Plug, Spark Cap', '3026043', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plug, Spark Cap', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2724, 'Cable, Engine Stop', '3026047', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cable, Engine Stop', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2725, 'Cable, Selector', '3026048', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cable, Selector', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2726, 'Boots,Rubber 7/8\"dia.', '3027002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Boots,Rubber 7/8\"dia.', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2727, 'Cup, Brake Rubber 2-5/16\" Ring', '3027010A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cup, Brake Rubber 2-5/16\" Ring', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2728, 'Cup, Brake Rubber 2-5/16φ', '3027011A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cup, Brake Rubber 2-5/16φ', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2729, 'Cap, Rubber Filler', '3027015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cap, Rubber Filler', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2730, 'Cup, Clutch Rubber 24.15x14', '3027017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cup, Clutch Rubber 24.15x14', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2731, 'Cup, Brake Rubber 47.5mm Rin', '3027022', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cup, Brake Rubber 47.5mm Rin', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2732, 'Cup, Brake Rubber 1-3/8\" Ring', '3027025', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cup, Brake Rubber 1-3/8\" Ring', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2733, 'Cup, Brake Rubber 40.0mm Rin', '3027026', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cup, Brake Rubber 40.0mm Rin', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2734, 'Cup, Brake Rubber 53.5mmφ', '3027031', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cup, Brake Rubber 53.5mmφ', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2735, 'Cup, Brake Rubber 1½\"φ', '3027035A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cup, Brake Rubber 1½\"φ', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2736, 'Cup, Brake Rubber 55.5mm Ring', '3027037', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cup, Brake Rubber 55.5mm Ring', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2737, 'Cup, Brake Rubber 50.8mm Ring Type', '3027038', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cup, Brake Rubber 50.8mm Ring Type', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2738, 'Cup, Brake Rubber 1¼\" Cup', '3027112', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cup, Brake Rubber 1¼\" Cup', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2739, 'Cup, Brake Rubber 1-5/8\"φ Ri', '3027114', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cup, Brake Rubber 1-5/8\"φ Ri', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2740, 'Cup, Brake Rubber 7/8\" Ring', '3027125', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cup, Brake Rubber 7/8\" Ring', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2741, 'Cup, Brake Rubber 1-3/8\"φ', '3027126', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cup, Brake Rubber 1-3/8\"φ', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2742, 'Cup, Brake Rubber 1-3/8\"φ Cup type', '3027126A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cup, Brake Rubber 1-3/8\"φ Cup type', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2743, 'Cup, Brake Rubber 2\"φ Cup Ty', '3027158', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cup, Brake Rubber 2\"φ Cup Ty', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2744, 'Clamp, Battery HD Bronze', '3032001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Clamp, Battery HD Bronze', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2745, 'Clip,Hose 3/4 stainless steel', '3033022', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Clip,Hose 3/4 stainless steel', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2746, 'Clip, Hose 30mm-40mm Average', '3033030A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Clip, Hose 30mm-40mm Average', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2747, 'Clip, Hose 40mm-60mm Average', '3033031A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Clip, Hose 40mm-60mm Average', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2748, 'Clip, Hose 40-55mm dia.', '3033032', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Clip, Hose 40-55mm dia.', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2749, 'Clip, Hose 40mm-60mm', '3033033', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Clip, Hose 40mm-60mm', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2750, 'Booster, Clutch Assy.', '3034004A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Booster, Clutch Assy.', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2751, 'Booster, Clutch PN-1-31800-072-0', '3034005A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Booster, Clutch PN-1-31800-072-0', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2752, 'Clutch, Pressure Assy.', '3034010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Clutch, Pressure Assy.', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2753, 'Disc, Clutch Assy.', '3034023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Disc, Clutch Assy.', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2754, 'Disc, Clutch Assy.', '3034024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Disc, Clutch Assy.', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2755, 'Disc, Clutch 12\"x16T', '3034024A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Disc, Clutch 12\"x16T', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2756, 'Clutch, Master Assy. 7/8\"φ', '3034034', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Clutch, Master Assy. 7/8\"φ', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2757, 'Clutch, Master Cylinder 13/16\"φ', '3034074', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Clutch, Master Cylinder 13/16\"φ', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2758, 'Clutch,Sleeve Cyl. Assy.1\"φLwr', '3034096A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Clutch,Sleeve Cyl. Assy.1\"φLwr', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2759, 'Clutch, Master 5/8', '3034111', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Clutch, Master 5/8', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2760, 'Coil, Ignition', '3035034', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coil, Ignition', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2761, 'Condenser, TOYOTA 12R', '3038022', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Condenser, TOYOTA 12R', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2762, 'Condenser, TOYOTA 2F', '3038042', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Condenser, TOYOTA 2F', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2763, 'Converter, Torque Assy.', '3038049', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Converter, Torque Assy.', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2764, 'Point, Contact', '3040002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Point, Contact', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2765, 'Fuso,Engine 6D15 6 Cylinder 4 stroke', '3043024A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuso,Engine 6D15 6 Cylinder 4 stroke', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2766, 'Cushion, Hood', '3044003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cushion, Hood', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2767, 'Cushion, Shock #90948-01080', '3044010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cushion, Shock #90948-01080', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2768, 'Booster, Brake Air Master 1478007590', '3045002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Booster, Brake Air Master 1478007590', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2769, 'Cylinder, Brake Wheel 50.8mm', '3045017A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cylinder, Brake Wheel 50.8mm', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2770, 'Caliper, Brake Assy. Front (LH & RH)', '3045062', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Caliper, Brake Assy. Front (LH & RH)', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2771, 'Brake, Air Master', '3045065', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brake, Air Master', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2772, 'Brake,Master - 1-3/8\"', '3045072', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brake,Master - 1-3/8\"', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2773, 'Hydrovac, Brake Assy PN 1-47800-323-0', '3045073', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hydrovac, Brake Assy PN 1-47800-323-0', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2774, 'Hydrovac, PN 1-47800-405-0', '3045074', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hydrovac, PN 1-47800-405-0', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2775, 'Cylinder, Brake Wheel 13/16\"', '3045075', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cylinder, Brake Wheel 13/16\"', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2776, 'Brake, DiaphragmChamber 6¼\"φ', '3046003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brake, DiaphragmChamber 6¼\"φ', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2777, 'Diode, Alternator Positive Big', '3049013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Diode, Alternator Positive Big', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2778, 'Diode, Alternator Negative Big', '3049018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Diode, Alternator Negative Big', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2779, 'Diode, Alternator Negative S', '3049019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Diode, Alternator Negative S', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2780, 'Dowel, #3F1953', '3052001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Dowel, #3F1953', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2781, 'Gear, Bendix Drive 11-teeth', '3053005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gear, Bendix Drive 11-teeth', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2782, 'Gear, Ring w/ Pinion Surplus', '3053016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gear, Ring w/ Pinion Surplus', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2783, 'Filter,Air Elem. 88x157x188', '3053045', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter,Air Elem. 88x157x188', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2784, 'Drum, Brake Rear', '3054022B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Drum, Brake Rear', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2785, 'Elbow, 90° #T30738', '3055010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elbow, 90° #T30738', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2786, 'Filter, Element Trans.PN 5S4', '3056001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Element Trans.PN 5S4', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2787, 'Filter, Air Elem.1-14215-028', '3056016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Air Elem.1-14215-028', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2788, 'Filter, Elem.Pri. PN-P181119', '3056034', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Elem.Pri. PN-P181119', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2789, 'Filter, Air Elmnt DA-320 EOM', '3056051', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Air Elmnt DA-320 EOM', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2790, 'Filter, Air Element', '3056063', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Air Element', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2791, 'Rod, Tie End', '3057007A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rod, Tie End', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2792, 'Rod, Tie End LH & RH', '3057045', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rod, Tie End LH & RH', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2793, 'Filter, Oil Element Primary #555', '3059003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil Element Primary #555', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2794, 'Filter, Fuel Cart. 551329', '3059005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Fuel Cart. 551329', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2795, 'Filter, Oil Element #609', '3059006B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil Element #609', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2796, 'Filter, Oil Element #556', '3059007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil Element #556', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2797, 'Filter, Oil Element EO-608', '3059012A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil Element EO-608', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2798, 'Filter, Oil Element #605', '3059014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil Element #605', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2799, 'Filter ElementPN-381-9402412', '3059015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter ElementPN-381-9402412', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2800, 'Filter, Oil Element #P550648', '3059040A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil Element #P550648', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2801, 'Filter, Fuel Cart. FC-321', '3059041', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Fuel Cart. FC-321', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2802, 'Filter, Oil Elem. Hyd. 9JO75', '3059045', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil Elem. Hyd. 9JO75', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2803, 'Filter, Fuel Element #507', '3059050', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Fuel Element #507', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2804, 'Filter, Fuel Cart. C-507', '3059050A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Fuel Cart. C-507', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2805, 'Filter, Oil Element #554', '3059058', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil Element #554', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2806, 'Filter, Oil Assy. PN-P555570', '3059067', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil Assy. PN-P555570', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25');
INSERT INTO `items` (`id`, `item_name`, `item_code`, `item_type`, `item_vendor_id`, `item_uom_id`, `item_reorder_point`, `item_category_id`, `item_quantity`, `item_sales_description`, `item_purchase_description`, `item_selling_price`, `item_cost_price`, `item_cogs_account_id`, `item_income_account_id`, `item_asset_account_id`, `created_at`) VALUES
(2807, 'Filter, Oil Cartridge PN-P555570', '3059067A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil Cartridge PN-P555570', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2808, 'Filter, Oil Element #557', '3059070', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil Element #557', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2809, 'Filter, Oil Element', '3059072A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil Element', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2810, 'Filter, Oil Cart. Type C-306', '3059077', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil Cart. Type C-306', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2811, 'Filter, Fuel Cart. PN-557440', '3059092', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Fuel Cart. PN-557440', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2812, 'Filter, Fuel Cart. FC-1203', '3059093', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Fuel Cart. FC-1203', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2813, 'Filter, Oil Element #DO-259', '3059111B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil Element #DO-259', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2814, 'Filter, Oil Element PN550484', '3059112', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil Element PN550484', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2815, 'Filter, Fuel Cart. PN-557440', '3059114C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Fuel Cart. PN-557440', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2816, 'Filter, Fuel Cart. PN-P559100', '3059118', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Fuel Cart. PN-P559100', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2817, 'Filter, Fuel Cartridge #FS 0211', '3059118A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Fuel Cartridge #FS 0211', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2818, 'Filter, Fuel Cartridge type', '3059140', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Fuel Cartridge type', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2819, 'Filter, Fuel Element#505', '3059145', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Fuel Element#505', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2820, 'Filter, Fuel Element 4505P', '3059145A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Fuel Element 4505P', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2821, 'Fiter, Fuel Elem. F-6240', '3059148', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fiter, Fuel Elem. F-6240', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2822, 'Filter,Oil PN32A40-00400', '3059160B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter,Oil PN32A40-00400', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2823, 'Filter,Oil Cartridge PN P556064', '3059162A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter,Oil Cartridge PN P556064', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2824, 'Filter, Oil Cart. PN-P554004', '3059192', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil Cart. PN-P554004', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2825, 'Filter, Oil Primary #C-509A', '3059195', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil Primary #C-509A', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2826, 'Filter, Oil Secondary GC-510', '3059197B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil Secondary GC-510', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2827, 'Filter, Fuel PN-3931063 FF50', '3059199', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Fuel PN-3931063 FF50', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2828, 'Filter, Oil Elem. PP559740', '3059201', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil Elem. PP559740', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2829, 'Filter, Oil P-553639', '3059204', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil P-553639', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2830, 'Filter, Oil #B 7451', '3059216', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil #B 7451', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2831, 'Filter, Oil Engine 356', '3059217', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil Engine 356', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2832, 'Filter, Oil Element #O-1805-5', '3059219', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil Element #O-1805-5', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2833, 'Filter, Oil Element #0-1805-1', '3059219A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil Element #0-1805-1', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2834, 'Filter, Oil PN MPR6769 1-13240187-0', '3059226A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil PN MPR6769 1-13240187-0', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2835, 'Filter, Oil Cartrdige PN JX01810/JX1008XN', '3059235', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Oil Cartrdige PN JX01810/JX1008XN', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2836, 'Filter, Fuel Cartridge FF-6322', '3059300', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Fuel Cartridge FF-6322', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2837, 'Fitting, Straight 3/16\" M/F', '3060009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fitting, Straight 3/16\" M/F', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2838, 'Fitting, Union 1/4\"', '3060012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fitting, Union 1/4\"', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2839, 'Fitting, Tee small', '3060022', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fitting, Tee small', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2840, 'Fitting, Union 1/4x3/16(smal', '3060025', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fitting, Union 1/4x3/16(smal', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2841, 'Fitting, Straight long nut 8', '3060030', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fitting, Straight long nut 8', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2842, 'Fitting, Wye Y 1/4 0  [sm]', '3060039', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fitting, Wye Y 1/4 0  [sm]', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2843, 'Fitting, Union 1/4 x 1/4 [sm', '3060042', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fitting, Union 1/4 x 1/4 [sm', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2844, 'Fitting, Union 12mmID (Female)', '3060049', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fitting, Union 12mmID (Female)', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2845, 'Fittings, Union 5/8\"ID', '3060061', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fittings, Union 5/8\"ID', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2846, 'Relay, Flasher Signal 6V', '3061003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Flasher Signal 6V', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2847, 'Flasher, Relay 24V', '3061007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Flasher, Relay 24V', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2848, 'Relay, Plug-in 110Vac 7A', '3061015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Plug-in 110Vac 7A', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2849, 'Relay,Non-latching 12Vdc 10A', '3061016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay,Non-latching 12Vdc 10A', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2850, 'Relay, Socket 250Vac', '3061017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Socket 250Vac', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2851, 'Fuse, 15amps glass type', '3065001A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, 15amps glass type', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2852, 'Fuse, 30 Amps. Glass Type', '3065009A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, 30 Amps. Glass Type', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2853, 'Gasket, Overhauling set', '3065013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Overhauling set', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2854, 'Fuse, 20 Amps. Glass Type', '3065015A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuse, 20 Amps. Glass Type', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2855, 'Gasket, PN-169-4200 (4M2969)', '3066016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, PN-169-4200 (4M2969)', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2856, 'Gasket, Cylinder Head', '3066019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Cylinder Head', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2857, 'Gasket, #2S-8960', '3066044', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, #2S-8960', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2858, 'Gasket, Cylinder Head', '3066051A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Cylinder Head', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2859, 'Gasket, PN-8S1605', '3066134', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, PN-8S1605', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2860, 'Gasket, PN-169-4199 (8S1965)', '3066149', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, PN-169-4199 (8S1965)', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2861, 'Gasket, Cylinder Head PN 6N-7263', '3066201', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Cylinder Head PN 6N-7263', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2862, 'Gasket, Front Housing PN2P1273', '3066202', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Front Housing PN2P1273', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2863, 'Gasket, Flywheel Housing PN9H5921', '3066203', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Flywheel Housing PN9H5921', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2864, 'Gauge, Amper Mechanical', '3067004B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gauge, Amper Mechanical', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2865, 'Gauge, Plastic 0.001\"-0.003\"', '3067016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gauge, Plastic 0.001\"-0.003\"', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2866, 'Gage, Plasti .002\"-.006\"', '3067033', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gage, Plasti .002\"-.006\"', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2867, 'Gage, Plasti .004\"-.009\"', '3067035', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gage, Plasti .004\"-.009\"', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2868, 'Gauge, Oil Pressure Mechanical Type', '3067038A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gauge, Oil Pressure Mechanical Type', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2869, 'Gauge, Temperature', '3067048A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gauge, Temperature', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2870, 'Gauge,WaterTemp.w/CaplryTube', '3067075', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gauge,WaterTemp.w/CaplryTube', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2871, 'Gear, First & Reverse 45teet', '3068087', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gear, First & Reverse 45teet', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2872, 'Gear, Pinion and Ring [sm]', '3068146', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gear, Pinion and Ring [sm]', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2873, 'Gear, Bendix Drive 9 Teeth', '3068157', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gear, Bendix Drive 9 Teeth', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2874, 'Plug, Glow 24V', '3071006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plug, Glow 24V', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2875, 'Compound, Grinding Silicone', '3073002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Compound, Grinding Silicone', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2876, 'Guide, Valve (12pcs./set)', '3074001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Guide, Valve (12pcs./set)', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2877, 'Head, Cylinder Assy.', '3077005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Head, Cylinder Assy.', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2878, 'Beam, Sealed 24V/75WTop2 Big', '3079004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Beam, Sealed 24V/75WTop2 Big', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2879, 'Beam,Sealed 12V Top#2 small', '3079018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Beam,Sealed 12V Top#2 small', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2880, 'Beam,Sealed 24V,top 1,small', '3079019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Beam,Sealed 24V,top 1,small', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2881, 'Beam, Sealed Top 2 w/ housing DC 24V, small', '3079020A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Beam, Sealed Top 2 w/ housing DC 24V, small', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2882, 'Beam,Sealed 24V/Top2 2AX,46L', '3079024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Beam,Sealed 24V/Top2 2AX,46L', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2883, 'Beam, Sealed Top2 w/housng & socket 24V, smll', '3079031A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Beam, Sealed Top2 w/housng & socket 24V, smll', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2884, 'Bearing, Holder PN-061121', '3080025', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bearing, Holder PN-061121', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2885, 'Horn, Electric 24V', '3082008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Horn, Electric 24V', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2886, 'Horn, Back 24V', '3082009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Horn, Back 24V', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2887, 'Horn, Air 24V', '3082016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Horn, Air 24V', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2888, 'Hose, Vacuum Pump Oil', '3083025', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Vacuum Pump Oil', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2889, 'Hose, Elbow, 1/2\"', '3083028', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Elbow, 1/2\"', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2890, 'Hose, Brake 700mmOAL BH-0566', '3083064A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Brake 700mmOAL BH-0566', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2891, 'Hose, #381-970427-1', '3083080A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, #381-970427-1', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2892, 'Hose,Rubber 1-1/2\"φx1mtr.', '3083136', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose,Rubber 1-1/2\"φx1mtr.', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2893, 'Hose, Assy. w/Tube & Fitting', '3083192', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Assy. w/Tube & Fitting', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2894, 'Hose,Alternator Pressure 270', '3083205', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose,Alternator Pressure 270', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2895, 'Hose, Brake 12mm x 10mm', '3083227', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Brake 12mm x 10mm', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2896, 'Hose, Brake', '3083237', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Brake', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2897, 'Hose, Brake Front 12x20 w/ fittings', '3083239', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Brake Front 12x20 w/ fittings', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2898, 'Hose, Hydraulic', '3083251', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Hydraulic', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2899, 'Hose, Hydraulic 42mmID,18\"', '3083253', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Hydraulic 42mmID,18\"', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2900, 'Hose, Hydraulic 1-3/4\"IDx12\"L', '3083254', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Hydraulic 1-3/4\"IDx12\"L', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2901, 'Hose, Rubber 3/4\"ID x 3.5ft.', '3083255', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Rubber 3/4\"ID x 3.5ft.', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2902, 'Hose, Hydraulic 25mmIDx1mtr.', '3083256', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Hydraulic 25mmIDx1mtr.', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2903, 'Hose, Hydraulic & fittings', '3083257', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Hydraulic & fittings', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2904, 'Hose, Hydraulic 59\"', '3083259', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Hydraulic 59\"', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2905, 'Hose, Hydraulic 1\"', '3083260', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Hydraulic 1\"', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2906, 'Joint, Linkage', '3088226', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Joint, Linkage', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2907, 'Kit, Repair Clutch Master 7/8\"', '3090004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Kit, Repair Clutch Master 7/8\"', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2908, 'Kit, Water Pump Rep. IsuzuDA', '3090013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Kit, Water Pump Rep. IsuzuDA', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2909, 'Kit, Brake Master Repair 1½\"φ', '3090031', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Kit, Brake Master Repair 1½\"φ', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2910, 'Kit, Water Pump Repair', '3090052A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Kit, Water Pump Repair', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2911, 'Kit, Water Pump Repair', '3090059', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Kit, Water Pump Repair', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2912, 'Kit, Repair Clutch Booster - 9', '3090102', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Kit, Repair Clutch Booster - 9', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2913, 'Kit, Repair Clutch Sleeve Lower  13/16\"', '3090296A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Kit, Repair Clutch Sleeve Lower  13/16\"', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2914, 'Kit, King Pin #KP-222 (50mm)', '3090298', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Kit, King Pin #KP-222 (50mm)', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2915, 'Kit, Caliper', '3090301', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Kit, Caliper', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2916, 'Assy, Knob Switch#421-06-182', '3091001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Assy, Knob Switch#421-06-182', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2917, 'Lever, Fork or Shaft D-500', '3094013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lever, Fork or Shaft D-500', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2918, 'Light, Signal Assy 24V', '3096020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Light, Signal Assy 24V', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2919, 'Light, Tail Assy. 24V LH/RH', '3096030', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Light, Tail Assy. 24V LH/RH', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2920, 'Light, Clearance Rectangular Type 24V', '3096065A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Light, Clearance Rectangular Type 24V', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2921, 'Light, Plate 24V', '3096073', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Light, Plate 24V', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2922, 'Light, Tail Signal & Stop 24V', '3096074A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Light, Tail Signal & Stop 24V', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2923, 'Lamp, Corner (LH/RH) - 24V', '3096075', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lamp, Corner (LH/RH) - 24V', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2924, 'Lining, Brake 152x193 4pcs/s', '3097006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lining, Brake 152x193 4pcs/s', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2925, 'Lining, Brk#47115-173 4pcs/set', '3097007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lining, Brk#47115-173 4pcs/set', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2926, 'Brake, Lining180x210x9,4pc/s', '3097009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brake, Lining180x210x9,4pc/s', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2927, 'Brake, Lining 8pcs./set RR', '3097014A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brake, Lining 8pcs./set RR', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2928, 'Lining, Brake w/ rivets Front', '3097015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lining, Brake w/ rivets Front', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2929, 'Lining, Brake (4pcs./set)', '3097035', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lining, Brake (4pcs./set)', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2930, 'Lining, Brake w/ Rivets Rear', '3097035B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lining, Brake w/ Rivets Rear', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2931, 'Lining, Brake Front(4pcs./set)', '3097035C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lining, Brake Front(4pcs./set)', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2932, 'Lining, Brake 8pcs./set', '3097040', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lining, Brake 8pcs./set', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2933, 'Brake Liningw/Rivets8pcs/set', '3097084', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brake Liningw/Rivets8pcs/set', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2934, 'Lining, Brake NT410-1780-N020', '3097089', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lining, Brake NT410-1780-N020', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2935, 'Brake, Lining w/ rivets Front', '3097091', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brake, Lining w/ rivets Front', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2936, 'Lining, Brake front w/ rivets #30-304-00', '3097092A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lining, Brake front w/ rivets #30-304-00', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2937, 'Link, Center RE243206', '3098010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Link, Center RE243206', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2938, 'Link, Lift Short RE243214', '3098011A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Link, Lift Short RE243214', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2939, 'Link Lift(RH) (RE243216)', '3098011B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Link Lift(RH) (RE243216)', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2940, 'Lock, Rim Isuzu', '3099076A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lock, Rim Isuzu', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2941, 'Lock, Rim', '3099076B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lock, Rim', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2942, 'Lock, Hub for Isuzu 4x4', '3099085', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lock, Hub for Isuzu 4x4', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2943, 'Motor, Wiper 24V w/arm blade', '3101008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Motor, Wiper 24V w/arm blade', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2944, 'Motor, Wiper 24V w/ arm blade', '3101008A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Motor, Wiper 24V w/ arm blade', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2945, 'Wiper, Motor Assy. 24V', '3101009A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wiper, Motor Assy. 24V', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2946, 'Starter, 24V reduction type', '3101014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Starter, 24V reduction type', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2947, 'Bar, Nikolite  50-50', '3103001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Nikolite  50-50', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2948, 'Nipple 380-888146-1', '3104013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nipple 380-888146-1', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2949, 'Nut, 3K 452 inner Portion', '3105022', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, 3K 452 inner Portion', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2950, 'Nut, Lock High Pressure Tube', '3105053', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Lock High Pressure Tube', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2951, 'Nut, Inverted 5/16\"', '3105063', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Inverted 5/16\"', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2952, 'Nut, Inverted 3/8\"', '3105065', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Inverted 3/8\"', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2953, 'Nut, 5R225 w/seal Assy. 6F10', '3105071', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, 5R225 w/seal Assy. 6F10', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2954, 'Nut, #14R for JD Tractor', '3105085', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, #14R for JD Tractor', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2955, 'Nut, RR #21157/AR-47374', '3105087', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, RR #21157/AR-47374', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2956, 'Nut, PN-213509', '3105088', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, PN-213509', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2957, 'Nut, Long Fitting 5/8\"', '3105091', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Long Fitting 5/8\"', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2958, 'Nut, Long Fitting 12mmφ', '3105108', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Long Fitting 12mmφ', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2959, 'Nut, Retainer Fuel Tank', '3105132', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nut, Retainer Fuel Tank', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2960, 'Pad, Brake FRT Motorcyle', '3106007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pad, Brake FRT Motorcyle', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2961, 'Rubber packing for Airmaster', '3107012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rubber packing for Airmaster', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2962, 'Packing, Rod PN-707-51-75030', '3107036', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Packing, Rod PN-707-51-75030', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2963, 'Pinion, Starter Motor', '3110003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pinion, Starter Motor', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2964, 'Tube, Steel 6mmIDx8mmODx10m', '3111016A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tube, Steel 6mmIDx8mmODx10m', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2965, 'Pipe, Tail Exhaust 65mm x 10', '3111023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pipe, Tail Exhaust 65mm x 10', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2966, 'Piston, Clutch Pack #220805', '3113001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Piston, Clutch Pack #220805', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2967, 'Piston, Clutches PN 7T3133', '3113052', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Piston, Clutches PN 7T3133', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2968, 'Piston, Clutches PN 7T3132', '3113053', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Piston, Clutches PN 7T3132', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2969, 'Piston, Clutches PN 9S6951', '3113055', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Piston, Clutches PN 9S6951', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2970, 'Plate, #4M8914 (Trans.group)', '3114012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plate, #4M8914 (Trans.group)', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2971, 'Plate, Clutch Pressure 12\"OD', '3114053', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plate, Clutch Pressure 12\"OD', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2972, 'Plug, Spark WS7F', '3116003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plug, Spark WS7F', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2973, 'Plug, #4H-0407', '3116011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plug, #4H-0407', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2974, 'Plug, Spark B8ES', '3116012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plug, Spark B8ES', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2975, 'Plug, Dust w/chain #AR 52623', '3116037', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plug, Dust w/chain #AR 52623', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2976, 'Pump, Water Assy.', '3120007A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pump, Water Assy.', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2977, 'Pump, Water Assy. CT 2W1223', '3120011B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pump, Water Assy. CT 2W1223', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2978, 'Pump, Fuel Transfer Assy.', '3120054', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pump, Fuel Transfer Assy.', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2979, 'Cylinder, Pump (2000.01.1106', '3120092', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cylinder, Pump (2000.01.1106', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2980, 'Pump, Fuel Primer', '3120096', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pump, Fuel Primer', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2981, 'Pump, Fuel Transfer', '3120105', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pump, Fuel Transfer', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2982, 'Regulator, Voltage', '3121031', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Regulator, Voltage', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2983, 'Regulator, Voltage 24V 5T', '3121046', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Regulator, Voltage 24V 5T', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2984, 'Relay, Starter 24V SW-5', '3122005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Starter 24V SW-5', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2985, 'Relay, Starter 24V', '3122005A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Starter 24V', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2986, 'Relay, 12V/30Amp. w/ socket', '3122007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, 12V/30Amp. w/ socket', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2987, 'Relay, Battery 24V Positive(+)', '3122009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Battery 24V Positive(+)', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2988, 'Relay, Battery 12V Possitive', '3122010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Battery 12V Possitive', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2989, 'Sender, Oil 12V', '3122012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sender, Oil 12V', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2990, 'Relay, Starter 24V', '3122021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Starter 24V', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2991, 'Battery, Relay (negative)', '3122029', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Battery, Relay (negative)', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2992, 'Relay, Headlight w/ socket 24V', '3122031', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay, Headlight w/ socket 24V', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2993, 'Relay,Flasher 12.8V', '3122032', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Relay,Flasher 12.8V', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2994, 'Rim, Spake 9.00x20', '3124012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rim, Spake 9.00x20', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2995, 'Rim, Tire 15x6 holes', '3124026', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rim, Tire 15x6 holes', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2996, 'Ring, Piston (std)', '3126001B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Piston (std)', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2997, 'Ring, Air Compressor Piston', '3126003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Air Compressor Piston', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2998, 'Ring-O, Cyl. Liner AR#4015', '3126032', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, Cyl. Liner AR#4015', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(2999, 'Ring-O, #07000-12135', '3126037', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, #07000-12135', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3000, 'Ring, Wear PN-6J2797', '3126042', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Wear PN-6J2797', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3001, 'Ring Back-Up 07146-02136', '3126087', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring Back-Up 07146-02136', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3002, 'Ring, Seal #380-174076-1', '3126150', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Seal #380-174076-1', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3003, 'Ring-O, Kit Seals', '3126155B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, Kit Seals', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3004, 'Ring, Back-Up PN-2K6830', '3126171', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Back-Up PN-2K6830', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3005, 'Ring, Back-up PN 07001-02060', '3126174A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Back-up PN 07001-02060', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3006, 'Ring, O- #4M-7022', '3126176', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, O- #4M-7022', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3007, 'Ring, Piston PN 707-44-14080', '3126179A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Piston PN 707-44-14080', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3008, 'Ring, Wear PN 7156-01417', '3126184A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Wear PN 7156-01417', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3009, 'Ring, Snap #380-336589-1', '3126186', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Snap #380-336589-1', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3010, 'Ring, Wear PN-4T5613', '3126209', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Wear PN-4T5613', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3011, 'Ring-O, PN 07000-02060', '3126241A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, PN 07000-02060', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3012, 'Ring, O PN 424-16-11130', '3126258', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, O PN 424-16-11130', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3013, 'Seal,O-ring PN-1H8128', '3126259', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal,O-ring PN-1H8128', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3014, 'Ring-O, Seal PN-8H-7521', '3126277', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, Seal PN-8H-7521', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3015, 'Ring-O, Seal PN-6F-4718', '3126280', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, Seal PN-6F-4718', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3016, 'Ring-O, Seal PN-1H-6227', '3126281', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, Seal PN-1H-6227', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3017, 'Ring, Seal PN-1T-1231', '3126282', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Seal PN-1T-1231', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3018, 'Ring, Seal PN-1T1230', '3126283', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Seal PN-1T1230', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3019, 'Ring, Piston (Std.)', '3126308', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Piston (Std.)', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3020, 'Ring, Oil 9S-7788', '3126369', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Oil 9S-7788', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3021, 'Ring,Intermediate   5S-6750', '3126370', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring,Intermediate   5S-6750', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3022, 'Ring, Top PN 9S-3029', '3126371', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Top PN 9S-3029', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3023, 'Ring, Back-up PN 705-17-03440', '3126372A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Back-up PN 705-17-03440', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3024, 'Ring-O, PN705-17-03380', '3126373A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, PN705-17-03380', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3025, 'Ring, Back-up PN 705-17-02440', '3126374', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Back-up PN 705-17-02440', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3026, 'Ring, Seal PN 705-17-02472', '3126375', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Seal PN 705-17-02472', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3027, 'Ring, O PN 705-17-02381', '3126376', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, O PN 705-17-02381', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3028, 'Ring, O PN 07000-03038', '3126378', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, O PN 07000-03038', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3029, 'Ring-O, Kit ISUZU DA640', '3126379', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, Kit ISUZU DA640', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3030, 'Ring-O, #117', '3126380', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, #117', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3031, 'Ring-O, PN8M4389', '3126381', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, PN8M4389', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3032, 'Ring-O, PN8M4445', '3126382', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, PN8M4445', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3033, 'Ring-O, 8M4448', '3126383', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, 8M4448', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3034, 'Ring-O,Square Brake Caliper Repair Kit', '3126384', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O,Square Brake Caliper Repair Kit', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3035, 'Ring-O, 8M5248', '3126385', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, 8M5248', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3036, 'Ring-O, Seal PN 2S4663', '3126386', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, Seal PN 2S4663', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3037, 'Ring-O, Seal PN8M5254', '3126387', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, Seal PN8M5254', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3038, 'Ring-0, Seal Rubber PN 456-5581', '3126388', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-0, Seal Rubber PN 456-5581', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3039, 'Ring, Planetary PN 9S6918', '3126389', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Planetary PN 9S6918', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3040, 'Screw, Bleeder', '3129004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, Bleeder', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3041, 'Screw, Pump drive', '3129018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, Pump drive', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3042, 'Screw, Cap 3/4\"x2-3/4\"19H313', '3129021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, Cap 3/4\"x2-3/4\"19H313', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3043, 'Screw, Bleeder  [sm]', '3129025', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, Bleeder  [sm]', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3044, 'Screw, Bleeder', '3129030', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, Bleeder', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3045, 'Screw, Bleeder', '3129031', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Screw, Bleeder', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3046, 'Seal, PN-1S6543', '3130002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, PN-1S6543', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3047, 'Seal, Oil Group #5k-5288', '3130004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Group #5k-5288', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3048, 'Seal,Oil 60mmx80mmx13mm', '3130008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal,Oil 60mmx80mmx13mm', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3049, 'Seal,Oil P/N-6K-357', '3130010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal,Oil P/N-6K-357', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3050, 'Seal,Oil Hub 174mmx118mmx29m', '3130013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal,Oil Hub 174mmx118mmx29m', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3051, 'Seal, PN-9M4849', '3130020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, PN-9M4849', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3052, 'Axle Oil Seal Rear Toyota FJ', '3130021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Axle Oil Seal Rear Toyota FJ', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3053, 'Seal, Oil Timming Cover', '3130026', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Timming Cover', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3054, 'Seal, Oil #22422 [sm]', '3130036', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil #22422 [sm]', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3055, 'Seal, Oil #50308', '3130039', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil #50308', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3056, 'Seal, Oil 4-1/2\"x3-1/2\"x3/8\"', '3130042', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 4-1/2\"x3-1/2\"x3/8\"', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3057, 'Seal, PN 7C-4297', '3130049A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, PN 7C-4297', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3058, 'Seal, Oil #32-45-7', '3130052', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil #32-45-7', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3059, 'Seal, Oil Steering Box 35x50x8T', '3130054A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Steering Box 35x50x8T', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3060, 'Seal, Oil Packing UN 63', '3130068', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Packing UN 63', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3061, 'Seal, Oil #40140', '3130070', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil #40140', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3062, 'Seal, Oil #380-223042-1', '3130078', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil #380-223042-1', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3063, 'Seal, Oil 25mmx45mmx11mm', '3130079A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 25mmx45mmx11mm', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3064, 'Seal, Oil Hub Rear #9699', '3130096A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Hub Rear #9699', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3065, 'Seal, Oil PN 381-973159-1', '3130102', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil PN 381-973159-1', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3066, 'Seal, Oil 30mmx50mmx11mmT', '3130112', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 30mmx50mmx11mmT', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3067, 'Seal, Oil 22mmIDx35mmODx8mmT', '3130113', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 22mmIDx35mmODx8mmT', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3068, 'Seal, Oil Assy. #5S-6296', '3130115', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Assy. #5S-6296', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3069, 'Seal,Oil Rear Cranshaft(4DR5', '3130117', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal,Oil Rear Cranshaft(4DR5', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3070, 'Seal, Hub Oil 117x174x15/27mmT', '3130123A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Hub Oil 117x174x15/27mmT', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3071, 'Seal, Oil 25-38-7/7.5', '3130131B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 25-38-7/7.5', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3072, 'Seal, Oil 33mmIDx50mmODx11mmT', '3130140', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 33mmIDx50mmODx11mmT', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3073, 'Seal, Oil 984mmx120mmx10mm', '3130142', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 984mmx120mmx10mm', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3074, 'Seal, Oil #AH-8544F', '3130162', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil #AH-8544F', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3075, 'Seal, Oil', '3130178', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3076, 'Seal, Oil Sector Shaft', '3130188', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Sector Shaft', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3077, 'Seal, Dust  PN 07016-00758', '3130191A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Dust  PN 07016-00758', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3078, 'Seal, Oil Pinion Drive', '3130201', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Pinion Drive', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3079, 'Seal, Oil 68mmx82mmx7mm', '3130207', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 68mmx82mmx7mm', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3080, 'Seal, Oil Torque Converter c', '3130213', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Torque Converter c', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3081, 'Seal, Ring #9F7378', '3130214', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Ring #9F7378', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3082, 'Seal, Oil #1805 R356 D-500', '3130228', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil #1805 R356 D-500', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3083, 'Seal, Ring PN-289-2935', '3130236A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Ring PN-289-2935', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3084, 'Oil,Seal Transfer Case', '3130238', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil,Seal Transfer Case', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3085, 'Seal, Assy.PN-5J5402', '3130256', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Assy.PN-5J5402', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3086, 'Seal, OilAE1708 30mmx50mmx11', '3130262', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, OilAE1708 30mmx50mmx11', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3087, 'Seal, Oil Final Drive #1888', '3130272', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Final Drive #1888', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3088, 'Ring, Seal Outer 3EA-15-1129', '3130290', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring, Seal Outer 3EA-15-1129', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3089, 'Seal, Oil #381-405368-1', '3130305', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil #381-405368-1', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3090, 'Seal, #5M9738 D6B SN-38H582', '3130309', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, #5M9738 D6B SN-38H582', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3091, 'Oil Seal 50x72x12', '3130313', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil Seal 50x72x12', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3092, 'Oil Seal #3059 (Rear Wheel)', '3130321', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil Seal #3059 (Rear Wheel)', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3093, 'Seal, Oil #385-1023861', '3130332', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil #385-1023861', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25');
INSERT INTO `items` (`id`, `item_name`, `item_code`, `item_type`, `item_vendor_id`, `item_uom_id`, `item_reorder_point`, `item_category_id`, `item_quantity`, `item_sales_description`, `item_purchase_description`, `item_selling_price`, `item_cost_price`, `item_cogs_account_id`, `item_income_account_id`, `item_asset_account_id`, `created_at`) VALUES
(3094, 'Seal, Oil Hub 115x156x15.5x31', '3130337A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Hub 115x156x15.5x31', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3095, 'Seal,Oil 77mmODx60mmx12mm', '3130340', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal,Oil 77mmODx60mmx12mm', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3096, 'Seal,OilHubFrntInr120x140x10.5', '3130348', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal,OilHubFrntInr120x140x10.5', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3097, 'Seal, Oil 11.5Tx120IDx140mmO', '3130349A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 11.5Tx120IDx140mmO', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3098, 'Seal, Oil#380-27063 front&re', '3130367', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil#380-27063 front&re', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3099, 'Oil, Seal Transfer Case#1010', '3130373', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil, Seal Transfer Case#1010', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3100, 'Seal, 8H7319 Oil', '3130382', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, 8H7319 Oil', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3101, 'Seal, Oil Hub Inner-RR #4309', '3130394', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Hub Inner-RR #4309', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3102, 'Seal, Hub Oil #100-125-13', '3130399', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Hub Oil #100-125-13', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3103, 'Seal, Oil AR27446', '3130419', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil AR27446', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3104, 'Seal, Timing Cover Oil', '3130425', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Timing Cover Oil', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3105, 'Seal, Ring grp frnt idlerSH2', '3130436', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Ring grp frnt idlerSH2', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3106, 'Seal, PN-3J-1907', '3130437', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, PN-3J-1907', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3107, 'Seal, Oil Acclerator Shaft 11mmIDx17mmODx4mmT', '3130454', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Acclerator Shaft 11mmIDx17mmODx4mmT', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3108, 'Seal, Oil 11mmIDx17mmODx4mmT', '3130454A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 11mmIDx17mmODx4mmT', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3109, 'Seal, Timing Cover Oil', '3130461', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Timing Cover Oil', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3110, 'Seal #4F7389 CAT.922', '3130465', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal #4F7389 CAT.922', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3111, 'Seal, Hydraulic Cylinder Oil', '3130473', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Hydraulic Cylinder Oil', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3112, 'Seal, Oil #380-888931-1 (P.L', '3130477', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil #380-888931-1 (P.L', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3113, 'Seal, Oil Hub Outer 74x142x14', '3130478A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Hub Outer 74x142x14', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3114, 'Seal, Lip type PN-308-1845', '3130482A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Lip type PN-308-1845', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3115, 'Seal, Hub Oil Inner V-10', '3130494', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Hub Oil Inner V-10', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3116, 'Seal, P/N 5M2997', '3130498', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, P/N 5M2997', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3117, 'Seal, #6L-7812', '3130508', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, #6L-7812', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3118, 'Seal,Oil 118mmIDx174mmODx29m', '3130659', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal,Oil 118mmIDx174mmODx29m', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3119, 'Seal, U-cup PN 421-4023', '3130672B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, U-cup PN 421-4023', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3120, 'Seal, Ring PN-1003', '3130678', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Ring PN-1003', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3121, 'Seal, Kit PN-1K-33EB-14-0506', '3130686', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Kit PN-1K-33EB-14-0506', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3122, 'Seal, Oil Double lips w/spring 16mmx28mmx7mm', '3130738A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Double lips w/spring 16mmx28mmx7mm', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3123, 'Seal, Oil Hub 64mmx133mmx13mm', '3130746A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Hub 64mmx133mmx13mm', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3124, 'Seal, Ring PN 9S6911', '3130747', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Ring PN 9S6911', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3125, 'Seal, Ring PN 8S3370', '3130748', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Ring PN 8S3370', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3126, 'Seal, Ring PN-9S6929', '3130751', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Ring PN-9S6929', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3127, 'Seal, Ring PN-9S6913', '3130752', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Ring PN-9S6913', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3128, 'Seal, Ring PN-9S6914', '3130753', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Ring PN-9S6914', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3129, 'Seal, Oil 62mmIDx90mmODx12mmth', '3130790A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 62mmIDx90mmODx12mmth', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3130, 'Seal, Oil PN 5S6622', '3130801', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil PN 5S6622', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3131, 'Seal, Oil 38mmIDx62mmODx9mmT', '3130804', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil 38mmIDx62mmODx9mmT', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3132, 'Seal, Oil Hub 75x112x10/18', '3130810', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Hub 75x112x10/18', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3133, 'Seal, Oil Rubber 105mmIDx135mmODx18mmT', '3130826', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Oil Rubber 105mmIDx135mmODx18mmT', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3134, 'Oil, Seal 32mmIDx54mmODx10mmT', '3130831', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil, Seal 32mmIDx54mmODx10mmT', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3135, 'Oil, Seal PN 705-17-02810', '3130833', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil, Seal PN 705-17-02810', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3136, 'Seal. seal PN705-17-03470', '3130834A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal. seal PN705-17-03470', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3137, 'Seal, Guide L35542', '3130835A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Guide L35542', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3138, 'Seal, Axle Rear SJ15582', '3130835B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Axle Rear SJ15582', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3139, 'Sender, Water Temp. Gauge 24V', '3132005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sender, Water Temp. Gauge 24V', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3140, 'Seal, Ring PN2P3597', '3132068', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Ring PN2P3597', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3141, 'Shaft, Main Transmission Hsn', '3136113', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Shaft, Main Transmission Hsn', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3142, 'Shoe, Brake w/lining(4pcs/se', '3137034', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Shoe, Brake w/lining(4pcs/se', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3143, 'Mirror, Side 6\"Wx10\"H', '3138002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Mirror, Side 6\"Wx10\"H', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3144, 'Mirror, Side 8\"x10\"', '3138006A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Mirror, Side 8\"x10\"', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3145, 'Mirror, Side 10\"x12\"', '3138017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Mirror, Side 10\"x12\"', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3146, 'Mirror, Side LH & RH', '3138018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Mirror, Side LH & RH', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3147, 'Male Socket 6Terminal', '3140004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Male Socket 6Terminal', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3148, 'Socket, Headlight', '3140016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Socket, Headlight', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3149, 'Race, #8B-5964 (US) - Cat.', '3142007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Race, #8B-5964 (US) - Cat.', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3150, 'Spacer, Hub Bearing Frnt.Whe', '3143010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spacer, Hub Bearing Frnt.Whe', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3151, 'Spacer, Side Jeep', '3143030', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spacer, Side Jeep', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3152, 'Spacer, 9F6186 of Transmissi', '3143032', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spacer, 9F6186 of Transmissi', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3153, 'Spacer, Side Gear GMC', '3143043', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spacer, Side Gear GMC', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3154, 'Spring, PN-3P1885', '3145003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, PN-3P1885', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3155, 'Spring, Air RR/RH TRL-230L', '3145006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Air RR/RH TRL-230L', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3156, 'Spring, Air TRL-230L', '3145006B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Air TRL-230L', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3157, 'Spring, Air Bag F-25 Front', '3145008A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Air Bag F-25 Front', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3158, 'Spring, Leaf#01 1410Lx80Wx10T', '3145057', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Leaf#01 1410Lx80Wx10T', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3159, 'Spring, Leaf #02 1,143x70x9x', '3145059', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Leaf #02 1,143x70x9x', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3160, 'Spring, Leaf 5th. RR Aux.', '3145071', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Leaf 5th. RR Aux.', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3161, 'Spring, Leaf 2nd. RR Aux.', '3145076', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Leaf 2nd. RR Aux.', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3162, 'Spring, Leaf 3rd. RR Aux.', '3145077', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Leaf 3rd. RR Aux.', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3163, 'Spring, Leaf 3rd RR', '3145080', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Leaf 3rd RR', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3164, 'Spring, Leaf Assy. P. Mover', '3145092', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Leaf Assy. P. Mover', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3165, 'Spring, Main Leaf 1st. RR', '3145107', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Main Leaf 1st. RR', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3166, 'Spring, Leaf Auxillary RR', '3145114', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Leaf Auxillary RR', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3167, 'Spring, Leaf 2nd', '3145141', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Leaf 2nd', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3168, 'Spring, Leaf 1st', '3145141A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Leaf 1st', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3169, 'Spring, Leaf 2nd Front 1557x80', '3145189', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Leaf 2nd Front 1557x80', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3170, 'Spring, Leaf 2nd. RR', '3145203', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Leaf 2nd. RR', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3171, 'Spring, Leaf 5th. RR', '3145204', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Leaf 5th. RR', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3172, 'Spring, Auxilliary Assy. Isu', '3145206', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Auxilliary Assy. Isu', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3173, 'Spring, Leaf Assy. FRT', '3145232', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Leaf Assy. FRT', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3174, 'Spring, Leaf 1st. FRT F-600', '3145276', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Leaf 1st. FRT F-600', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3175, 'Spring, Leaf Torque Rear', '3145303', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Leaf Torque Rear', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3176, 'Spring, Leaf 1st front 11mmTx', '3145337B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Leaf 1st front 11mmTx', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3177, 'Leaf,Spring 1st 1,305x70x11mmT', '3145343', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Leaf,Spring 1st 1,305x70x11mmT', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3178, 'Spring, leaf RR 2nd PN-51310-', '3145345', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, leaf RR 2nd PN-51310-', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3179, 'Spring, Leaf 1st 60Wx8Tx9mmφ', '3145348', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Leaf 1st 60Wx8Tx9mmφ', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3180, 'Spring, Leaf 2nd 60Wx8Tx9mmφ', '3145349', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Leaf 2nd 60Wx8Tx9mmφ', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3181, 'Arm, Idler Assy. LH', '3150002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Arm, Idler Assy. LH', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3182, 'Pump, Steering Isuzu 6HE1', '3150016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pump, Steering Isuzu 6HE1', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3183, 'Switch, Ignition 24V/6T', '3153023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Ignition 24V/6T', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3184, 'Switch, Starter Solenoid 24V', '3153030', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Starter Solenoid 24V', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3185, 'Switch, Solenoid 24V', '3153034', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Solenoid 24V', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3186, 'Switch, Brake Light 24V', '3153039', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Brake Light 24V', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3187, 'Switch, Single Pull', '3153053', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Single Pull', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3188, 'Switch, Push & Pull HD', '3153084', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Push & Pull HD', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3189, 'Switch, Push Button', '3153133', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Push Button', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3190, 'Switch, Sender Reverse NC', '3153151', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Sender Reverse NC', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3191, 'Sealing strip #059.01.1004', '3155001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sealing strip #059.01.1004', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3192, 'Thrust Piece (200.01.1136)', '3159001A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Thrust Piece (200.01.1136)', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3193, 'Tire, Exterior 13.00x24 16PR', '3159003C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Exterior 13.00x24 16PR', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3194, 'Tire, Exterior 2.50x17', '3159012A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Exterior 2.50x17', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3195, 'Tire, Exterior 8.25x16 14PR Miller', '3159013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Exterior 8.25x16 14PR Miller', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3196, 'Tire, Exterior 8.25x20 14PR Lug', '3159017A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Exterior 8.25x20 14PR Lug', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3197, 'Tire, Exterior 9.00x20 16ply', '3159019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Exterior 9.00x20 16ply', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3198, 'Tire, Ext. 9.00x20 16PR Lug', '3159019A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Ext. 9.00x20 16PR Lug', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3199, 'Tire, Ext. 9.00x20 18PR w/ tub', '3159019B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Ext. 9.00x20 18PR w/ tub', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3200, 'Tire, Exterior 14.00x24 16PR', '3159027', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Exterior 14.00x24 16PR', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3201, 'Tire, Exterior 17.5x25x20 ply', '3159028D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Exterior 17.5x25x20 ply', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3202, 'Tire, Exterior 28x9-15 14PR', '3159032', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Exterior 28x9-15 14PR', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3203, 'Tire, Exterior 10.00x20 16PR Miler', '3159035', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Exterior 10.00x20 16PR Miler', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3204, 'Tire, Ext. 8.25x20 16PR', '3159035B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Ext. 8.25x20 16PR', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3205, 'Tire, Ext. 10.00x20 16PR Lug', '3159035C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Ext. 10.00x20 16PR Lug', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3206, 'Tire, Ext. 10.00-20 x 18PR (Lug Type)', '3159035D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Ext. 10.00-20 x 18PR (Lug Type)', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3207, 'Tire, Exterior 7.00x15 12PR Lug Type', '3159036A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Exterior 7.00x15 12PR Lug Type', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3208, 'Tire, Exterior 10.00x20 18PR Miller', '3159038D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Exterior 10.00x20 18PR Miller', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3209, 'Tire, Ext. Tubeless 185 R14', '3159047B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Ext. Tubeless 185 R14', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3210, 'Tire, Exterior 2.75 x 21', '3159052', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Exterior 2.75 x 21', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3211, 'Tire, Solid 6.50 x 10', '3159054', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Solid 6.50 x 10', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3212, 'Tire, Exterior 12.4x24 8ply', '3159056A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Exterior 12.4x24 8ply', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3213, 'Tire, Ext. Rear 2.75x17', '3159062A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Ext. Rear 2.75x17', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3214, 'Tire, Exterior Front 8 x 18 8Ply', '3159074B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Exterior Front 8 x 18 8Ply', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3215, 'Tire, Exterior Rear 18.4 x 30 14ply', '3159076B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Exterior Rear 18.4 x 30 14ply', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3216, 'Tire, Exterior 20.5/70-16 14PR', '3159097B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Exterior 20.5/70-16 14PR', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3217, 'Tire, Ext. 11R-22.5 18PR (Tubeless)', '3159100A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Ext. 11R-22.5 18PR (Tubeless)', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3218, 'Tire, Ext. 12R22.5 18PR Tubeless', '3159101', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Ext. 12R22.5 18PR Tubeless', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3219, 'Tire, Exterior 8.3-20', '3159102', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Exterior 8.3-20', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3220, 'Tire, Exterior 8.3x20 6PR', '3159102A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Exterior 8.3x20 6PR', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3221, 'Tire, Ext. 110x90-17 Rear', '3159103', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Ext. 110x90-17 Rear', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3222, 'Tire, Exterior 6.40/6.50-13', '3159104', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tire, Exterior 6.40/6.50-13', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3223, 'Transmission, Isuzu 6BD1', '3161007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Transmission, Isuzu 6BD1', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3224, 'Tube, Inner 8.25x20', '3162007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tube, Inner 8.25x20', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3225, 'Tube, Inner 2.75 x 21', '3162017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tube, Inner 2.75 x 21', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3226, 'Tube, Inner 12.4/11-24 TR218', '3162018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tube, Inner 12.4/11-24 TR218', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3227, 'Tube, Inner 18.4/15-30 TR218', '3162020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tube, Inner 18.4/15-30 TR218', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3228, 'Tube, Inner 16.9-24', '3162021B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tube, Inner 16.9-24', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3229, 'Tube, Injector Fuel', '3162024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tube, Injector Fuel', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3230, 'Tube, Inner 17.5x25', '3162025', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tube, Inner 17.5x25', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3231, 'Tube, Inner 9.00-16', '3162038', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tube, Inner 9.00-16', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3232, 'Pipe, HighPress.3/16\"x6x8mtr', '3162087', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pipe, HighPress.3/16\"x6x8mtr', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3233, 'Tube, Inner 13.00x24', '3162093A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tube, Inner 13.00x24', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3234, 'Tube, Inner 14.9/13-28', '3162094', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tube, Inner 14.9/13-28', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3235, 'Valve, AirInlet Check Assy 4', '3163027A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, AirInlet Check Assy 4', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3236, 'Valve, Retainer 12 pcs./set4', '3163061', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Retainer 12 pcs./set4', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3237, 'Valve PN-2S5926', '3163064A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve PN-2S5926', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3238, 'Valve,Solenoid PN-419-15-169', '3163066', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve,Solenoid PN-419-15-169', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3239, 'Valve, Insert Exhaust/Intake', '3163079', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Insert Exhaust/Intake', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3240, 'Valve Cone', '3163111', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve Cone', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3241, 'Valve,Air Relief PN-44530-1290', '3163116', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve,Air Relief PN-44530-1290', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3242, 'Valve, Shut-off 12V', '3163134', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Shut-off 12V', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3243, 'Washer 2-S-1418', '3164004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer 2-S-1418', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3244, 'Washer, Trush PN-3EB-15-2135', '3164009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer, Trush PN-3EB-15-2135', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3245, 'Wire, Hi-tension 12R', '3168002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Hi-tension 12R', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3246, 'Wire, Automotive #12', '3168006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Automotive #12', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3247, 'Wire, Automotive #16 Std.', '3168012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Automotive #16 Std.', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3248, 'Wire, Automotive #14', '3168018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Automotive #14', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3249, 'Whell Impeller 380-888871-1', '3172002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Whell Impeller 380-888871-1', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3250, 'Radiator, Assy.', '3173005A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Radiator, Assy.', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3251, 'Race,(Inner)PN 2P-2811', '3173010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Race,(Inner)PN 2P-2811', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3252, 'Washer', '3176011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3253, 'Washer, Bronze', '3176011A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Washer, Bronze', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3254, 'Wire,  Terminal 5/16\"ID Util', '3177002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire,  Terminal 5/16\"ID Util', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3255, 'Separator, Fuel Water ES1280', '3178003A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Separator, Fuel Water ES1280', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3256, 'Rivets, #17H91', '3188004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rivets, #17H91', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3257, 'Rivets', '3188019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rivets', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3258, 'Rivets, Blind #10:14', '3188028', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rivets, Blind #10:14', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3259, 'Box, Fuse w/ fuse 10 legs', '3190004A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Box, Fuse w/ fuse 10 legs', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3260, 'Box, Fuse 6 Terminal w/ plug-in fuse', '3190004B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Box, Fuse 6 Terminal w/ plug-in fuse', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3261, 'Gum, Tube (Tapac)', '3191001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gum, Tube (Tapac)', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3262, 'Liner, Sleeve', '3195006A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Liner, Sleeve', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3263, 'Ring-O, Sleeve Liner', '3195009A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ring-O, Sleeve Liner', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3264, 'Liner, Sleeve PN 371-5941', '3195013A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Liner, Sleeve PN 371-5941', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3265, 'Rubber, Stopper Frt. Lower', '3204000', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rubber, Stopper Frt. Lower', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3266, 'Insert Hub Clutch,  (Isuzu)', '3208007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Insert Hub Clutch,  (Isuzu)', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3267, 'Assy., Diff\'l.Reo Frt2.5Tonner', '3217013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Assy., Diff\'l.Reo Frt2.5Tonner', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3268, 'Roller, for  D7E (Caterpilla', '3230001A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Roller, for  D7E (Caterpilla', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3269, 'Dryer, Air Assy.', '3238001B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Dryer, Air Assy.', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3270, 'Prussian Blue', '3247001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Prussian Blue', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3271, 'Packing, Cup  #1-47819-080-0', '3249009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Packing, Cup  #1-47819-080-0', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3272, 'Magnet No.8M7160 for 9M8669', '3260003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Magnet No.8M7160 for 9M8669', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3273, 'Roller, Assy. Doulbe PN-8S29', '3263004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Roller, Assy. Doulbe PN-8S29', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3274, 'Connector, Injector Rubber', '3271013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Connector, Injector Rubber', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3275, 'Disc. Assy. 8M6357', '3273004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Disc. Assy. 8M6357', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3276, 'Turbo,Charger 466721-0002', '3274004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Turbo,Charger 466721-0002', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3277, 'Band, PN-9L5854', '3285005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Band, PN-9L5854', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3278, 'Segment, PN-197-9679', '3286002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Segment, PN-197-9679', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3279, 'Wiper Retainer #381-980003-1', '3298007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wiper Retainer #381-980003-1', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3280, 'Winch, Mechanical 5 tons, 20,0', '3298036', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Winch, Mechanical 5 tons, 20,0', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3281, 'Trunnion, Assy w/spring seat', '3298038', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Trunnion, Assy w/spring seat', NULL, NULL, NULL, 4698, 4603, 3193, '2024-09-19 07:27:25'),
(3282, 'Fluid, Automatic Transmission', '4002001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fluid, Automatic Transmission', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3283, 'Fluid, Brake & Clutch (ltr)', '4005001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fluid, Brake & Clutch (ltr)', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3284, 'Fluid, Petromate 250ml/bot.', '4005002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fluid, Petromate 250ml/bot.', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3285, 'Fluid, Brake Prestone 270ml.', '4005003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fluid, Brake Prestone 270ml.', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3286, 'Fluid,Brake & Clutch 900ml/bot', '4005004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fluid,Brake & Clutch 900ml/bot', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3287, 'Fluid, Brake & Clutch 500ml/bot', '4005004A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fluid, Brake & Clutch 500ml/bot', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3288, 'Fluid, Petromate 900ml/bot.', '4005004B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fluid, Petromate 900ml/bot.', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3289, 'Cleaner', '4005006B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cleaner', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3290, 'Fuel, Diesel', '4008001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuel, Diesel', NULL, NULL, NULL, 4918, 4603, 3203, '2024-09-19 07:27:25'),
(3291, 'Grease, Gadus S1 @ 180kg/drum', '4012002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Grease, Gadus S1 @ 180kg/drum', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3292, 'Oil, Hydrotur T-32 200ltrs/drum', '4013001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil, Hydrotur T-32 200ltrs/drum', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3293, 'Oil, Hydrotur AW68 200ltrs/drum', '4013002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil, Hydrotur AW68 200ltrs/drum', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3294, 'Oil, Hydrotur AW100 200ltrs/drum', '4013003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil, Hydrotur AW100 200ltrs/drum', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3295, 'Oil, Hydrotur T-46 200ltrs/drm', '4013006A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil, Hydrotur T-46 200ltrs/drm', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3296, 'Oil, Hypex EP-320 200ltrs/drum', '4014002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil, Hypex EP-320 200ltrs/drum', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3297, 'Oil, Hypex EP-460 @ 200ltrs/drum', '4014004A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil, Hypex EP-460 @ 200ltrs/drum', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3298, 'Oil, Hypex EP-680 200ltrs/drum', '4014005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil, Hypex EP-680 200ltrs/drum', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3299, 'Fuel, Gasoline Regular', '4015001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fuel, Gasoline Regular', NULL, NULL, NULL, 4874, 4603, 3202, '2024-09-19 07:27:25'),
(3300, 'Oil, Milrol 5K 200ltrs/drum', '4019001A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil, Milrol 5K 200ltrs/drum', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3301, 'Oil, EP HD-680 \"Gulf\"', '4019004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil, EP HD-680 \"Gulf\"', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3302, 'Oil, EP HD-460 @ 200ltrs./drum', '4019006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil, EP HD-460 @ 200ltrs./drum', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3303, 'Oil,LAAPSA Sugar Press BR 46K @ 190Kgs./drum.', '4019007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil,LAAPSA Sugar Press BR 46K @ 190Kgs./drum.', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3304, 'Grease, Petro Premium #2', '4022001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Grease, Petro Premium #2', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3305, 'Grease, Petro HT2', '4022002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Grease, Petro HT2', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3306, 'Grease, Fuch Renolit LXEP-2', '4022003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Grease, Fuch Renolit LXEP-2', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3307, 'Funchs Renulite', '4022007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Funchs Renulite', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3308, 'Grease, Keltec Synlube KOPGRI-5', '4022008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Grease, Keltec Synlube KOPGRI-5', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3309, 'Grease, Gadus S2 180kgs./drum', '4022009A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Grease, Gadus S2 180kgs./drum', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3310, 'Grease, Crown HT @ 16kgs./pail', '4022015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Grease, Crown HT @ 16kgs./pail', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3311, 'Grease, Crown EP 16kgs./pail', '4022016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Grease, Crown EP 16kgs./pail', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3312, 'Oil, Engine XHD Plus 30 \"Gulf\"', '4026002D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil, Engine XHD Plus 30 \"Gulf\"', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3313, 'Oil, 2T 1Ltr./bot. \"Shell\"', '4026003A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil, 2T 1Ltr./bot. \"Shell\"', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3314, 'Oil, XHD 10W @ 200 ltrs./drum', '4026009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil, XHD 10W @ 200 ltrs./drum', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3315, 'Oil, Gear GEP-140 200ltrs/drum', '4027002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil, Gear GEP-140 200ltrs/drum', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3316, 'Oil, Gear Spirax S2 A140 \"Shell\" @ 20L/pail', '4027002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil, Gear Spirax S2 A140 \"Shell\" @ 20L/pail', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3317, 'Oil, Transmission #68', '4027006A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil, Transmission #68', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3318, 'Oil, RevX HD SAE 30 @ 200ltrs./drum', '4027007A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil, RevX HD SAE 30 @ 200ltrs./drum', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3319, 'Oil, RevX HD SAE 40', '4027008A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil, RevX HD SAE 40', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3320, 'Oil, RevX HD SAE 10', '4027009A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil, RevX HD SAE 10', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3321, 'Voltran 60 (Transformer Oil)', '4043001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Voltran 60 (Transformer Oil)', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3322, 'Oil, Diala S4 ZX-1', '4043001A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil, Diala S4 ZX-1', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3323, 'Oil, Spinol 15 200ltrs/drum', '4049001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil, Spinol 15 200ltrs/drum', NULL, NULL, NULL, 4831, 4603, 3201, '2024-09-19 07:27:25'),
(3324, 'Phosphoric Acid 35kg./cby.', '5001002C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Phosphoric Acid 35kg./cby.', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3325, 'Muriatic Acid', '5001003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Muriatic Acid', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3326, 'Muriatic Acid 500ml', '5001003A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Muriatic Acid 500ml', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3327, 'Alcohol, Isopropyl', '5003002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Alcohol, Isopropyl', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3328, 'Alcohol,Denatured 450cc', '5003004B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Alcohol,Denatured 450cc', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3329, 'Match', '5003005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Match', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3330, 'Ammonium Molybdate AR 500g/bot', '5005003A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ammonium Molybdate AR 500g/bot', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3331, 'Buffer, Solution pH7 1ltr/bo', '5009002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Buffer, Solution pH7 1ltr/bo', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3332, 'Buffer, Solution pH4 1ltr/bot', '5009003A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Buffer, Solution pH4 1ltr/bot', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3333, 'Ethanol, AR (2.5li.bot)', '5013001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ethanol, AR (2.5li.bot)', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3334, 'Filter, Paper 61x61cm', '5021001B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Paper 61x61cm', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3335, 'Filter, Paper 0.45um,47mm', '5021002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Paper 0.45um,47mm', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3336, 'Filter, Membrane 0.45um,47mm', '5021002B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filter, Membrane 0.45um,47mm', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3337, 'Bag, Filter 10 Microns Single Length', '5021014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bag, Filter 10 Microns Single Length', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3338, 'Bag, Filter 10 microns DL', '5021014A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bag, Filter 10 microns DL', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3339, 'Bag, Filter 10 Microns SL 15\"', '5021014B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bag, Filter 10 Microns SL 15\"', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3340, 'Bag, Filter 10 microns single-length 18\"L', '5021014C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bag, Filter 10 microns single-length 18\"L', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3341, 'Bag, Filter 50 Microns Double Length', '5021015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bag, Filter 50 Microns Double Length', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3342, 'Bag, Filter 50 microns, double-length 36\"L', '5021015B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bag, Filter 50 microns, double-length 36\"L', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3343, 'Bag, Filter 50 Microns Single Length 36\"L', '5021015C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bag, Filter 50 Microns Single Length 36\"L', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3344, 'Paper, Indicator pH 6.4-8.0', '5021023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paper, Indicator pH 6.4-8.0', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3345, 'Hydrochloric Acid', '5026004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hydrochloric Acid', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3346, 'Hydrochloric Acid AR 2.5L/bot.', '5026005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hydrochloric Acid AR 2.5L/bot.', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3347, 'Lime, Hydrated', '5032001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lime, Hydrated', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3348, 'Lysol Disinfectant Spray 510', '5033001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lysol Disinfectant Spray 510', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3349, 'Oil, Penetrating WD-40 12.9 oz', '5041002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oil, Penetrating WD-40 12.9 oz', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3350, 'Agar,Plate Count/Stndrd Method', '5047005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Agar,Plate Count/Stndrd Method', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3351, 'Glucose, Trypton Medium', '5047007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Glucose, Trypton Medium', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3352, 'Salt, Non-Iodized Industrial 25kgs./bag', '5048002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Salt, Non-Iodized Industrial 25kgs./bag', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3353, 'Caustic Soda Flakes 25kg/bag', '5049003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Caustic Soda Flakes 25kg/bag', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3354, 'Sulfite, Sodium AR 500g/bot', '5050001B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sulfite, Sodium AR 500g/bot', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3355, 'Sodium Chloride AR 500gms/bot', '5050002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sodium Chloride AR 500gms/bot', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3356, 'Sodium Hydroxide (1kg/Bot)', '5050003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sodium Hydroxide (1kg/Bot)', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3357, 'Sodium Metabisulphite 500g/bot', '5050003A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sodium Metabisulphite 500g/bot', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3358, 'Sodium Phosphate Monobasic 500g/bot.', '5050004A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sodium Phosphate Monobasic 500g/bot.', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3359, 'Potassium SodiumTartrate 500', '5050005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Potassium SodiumTartrate 500', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3360, 'Potassium Dihydrogen AR 500g/b', '5050017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Potassium Dihydrogen AR 500g/b', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3361, 'Sulphonic Acid 1-Amino-2', '5050020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sulphonic Acid 1-Amino-2', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3362, 'Flocculant, Polymer AP273P', '5053003A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Flocculant, Polymer AP273P', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3363, 'Zuclar, G 25kgs/bag', '5053007A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Zuclar, G 25kgs/bag', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3364, 'Polyflote @ 25kgs/bag', '5053007B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Polyflote @ 25kgs/bag', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3365, 'Flocculant, Flotation Millflote 361', '5053007C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Flocculant, Flotation Millflote 361', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3366, 'Liquid, Fabscalex 19kgs./cby.', '5053009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Liquid, Fabscalex 19kgs./cby.', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3367, 'Liquid, Fabscalex 20kgs./cby.', '5053009A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Liquid, Fabscalex 20kgs./cby.', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3368, 'Flocculant, Praestol LT27 AG AP', '5053010A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Flocculant, Praestol LT27 AG AP', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3369, 'Sulfuric Acid, Technical Gra', '5054001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sulfuric Acid, Technical Gra', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3370, 'Sulfuric Acid AR 2.5 ltrs./bot', '5054004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sulfuric Acid AR 2.5 ltrs./bot', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3371, 'Color Precipitant Poly-210ltrs', '5056110', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Color Precipitant Poly-210ltrs', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3372, 'Cleaner, Contact 400ml/can', '5062002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cleaner, Contact 400ml/can', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3373, 'Calcium Hypochlorite 70%', '5064004C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Calcium Hypochlorite 70%', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3374, 'Viscosity Aid (19kgs/cby)', '5070002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Viscosity Aid (19kgs/cby)', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3375, 'Adhesive, Threaded Locker #242', '5075003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Adhesive, Threaded Locker #242', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3376, 'Locker, Thread Loctite #243', '5075004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Locker, Thread Loctite #243', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25');
INSERT INTO `items` (`id`, `item_name`, `item_code`, `item_type`, `item_vendor_id`, `item_uom_id`, `item_reorder_point`, `item_category_id`, `item_quantity`, `item_sales_description`, `item_purchase_description`, `item_selling_price`, `item_cost_price`, `item_cogs_account_id`, `item_income_account_id`, `item_asset_account_id`, `created_at`) VALUES
(3377, 'Adhesive, Instant Loctite #401', '5075010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Adhesive, Instant Loctite #401', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3378, 'Loctite, Threaded Locker #62', '5075012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Loctite, Threaded Locker #62', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3379, 'Radiator Coolant', '5078002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Radiator Coolant', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3380, 'Coolant, Engine', '5078004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coolant, Engine', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3381, 'Diuron, 805C Liquid', '5088007B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Diuron, 805C Liquid', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3382, 'Mower', '5088009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Mower', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3383, 'Inhibitor, Scale RM 110 HDS', '5092001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Inhibitor, Scale RM 110 HDS', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3384, '2-4-D Amine', '5097001A', 'Inventory', NULL, NULL, NULL, NULL, 0, '2-4-D Amine', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3385, 'Ammonium Sulfate (21-00-00)', '5097003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ammonium Sulfate (21-00-00)', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3386, 'Cadre 300ml.', '5097004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cadre 300ml.', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3387, 'Herbadox 1000ml.', '5097005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Herbadox 1000ml.', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3388, 'Fertilizer, Organic J777', '5098006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fertilizer, Organic J777', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3389, 'Dolomite', '5099006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Dolomite', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3390, 'Fabchem N46 @ 50kgs./bag', '5109005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fabchem N46 @ 50kgs./bag', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3391, 'Fabearth Micro 110 inoculant', '5109006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fabearth Micro 110 inoculant', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3392, 'Conductivity, Standard Sol. 1413', '5109007A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Conductivity, Standard Sol. 1413', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3393, 'Octapol 1.5 kgs./bot.', '5109014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Octapol 1.5 kgs./bot.', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3394, 'Celite 577 Filter Aid', '5109016A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Celite 577 Filter Aid', NULL, NULL, NULL, 4986, 4603, 3194, '2024-09-19 07:27:25'),
(3395, 'Bar, Round MS 1\"φx20\'', '6002016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Round MS 1\"φx20\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:25'),
(3396, 'Bar, Round 16mmx20\'L', '6002017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Round 16mmx20\'L', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:25'),
(3397, 'Bar, Deformed 9mmφ x 20\'L', '6002019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Deformed 9mmφ x 20\'L', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3398, 'Bar, Deformed 12mmφ x 20\'L', '6002020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Deformed 12mmφ x 20\'L', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3399, 'Bar, Defformed 16mmφ', '6002022', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Defformed 16mmφ', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3400, 'Bar, Deformed 10mmφ x 20\'L', '6002023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Deformed 10mmφ x 20\'L', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3401, 'Bar, MS Flat 9mmTx4\"x20\'', '6002030', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, MS Flat 9mmTx4\"x20\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3402, 'Bar, Flat 3/16\"x2\"x20L', '6002036B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Flat 3/16\"x2\"x20L', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3403, 'Bar, Flat MS 4mm x 1\"W x 20ft. L', '6002036C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Flat MS 4mm x 1\"W x 20ft. L', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3404, 'Bar, Round 2\"x20\'', '6002039', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Round 2\"x20\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3405, 'Bar, Flat 15.20mmTx3\"Wx6mL', '6002045', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Flat 15.20mmTx3\"Wx6mL', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3406, 'Bar, Flat Aluminum 1/8\"x1\"x2', '6002060', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Flat Aluminum 1/8\"x1\"x2', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3407, 'Bar, Flat MS 1/8\"Tx2\"Wx20\'L', '6002064A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Flat MS 1/8\"Tx2\"Wx20\'L', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3408, 'Bar, Angle  1/4\"Tx2\"x2\"x20\'L', '6003006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Angle  1/4\"Tx2\"x2\"x20\'L', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3409, 'Bar, Angle 1/4\"x1½\"x1½\"x20\'', '6003009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Angle 1/4\"x1½\"x1½\"x20\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3410, 'Bar, Angle 5/16\"x2½\"x2½\"x20\'', '6003021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Angle 5/16\"x2½\"x2½\"x20\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3411, 'Bar, Angle 6mmTx3\"x3\"x20\'L', '6003023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Angle 6mmTx3\"x3\"x20\'L', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3412, 'Bar, MS Flat 1\"x4\"x20\'', '6003033', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, MS Flat 1\"x4\"x20\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3413, 'Bar, Angle 1\"x1\"x5.0mmTx20\'', '6003034B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Angle 1\"x1\"x5.0mmTx20\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3414, 'Bar, Copper Bus 1/4\"x2\"x10\'', '6003049', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Copper Bus 1/4\"x2\"x10\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3415, 'Bar, Angle 5mmTx2-1/2\"x2-1/2\"x20\'L', '6003060A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Angle 5mmTx2-1/2\"x2-1/2\"x20\'L', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3416, 'Bar, Rectangular hollow 2\"x4\"x', '6003061', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Rectangular hollow 2\"x4\"x', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3417, 'Bar, Angle 6mmx2-1/2\"x2-1/2x20\'', '6003062', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Angle 6mmx2-1/2\"x2-1/2x20\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3418, 'Bolt, Track w/nt 3/4\"φx3\"L UNC', '6006003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bolt, Track w/nt 3/4\"φx3\"L UNC', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3419, 'Glue, Wood @ 1ltr./can', '6013003A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Glue, Wood @ 1ltr./can', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3420, 'Tile,Grout', '6014001A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tile,Grout', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3421, 'Grout, ABC', '6014001B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Grout, ABC', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3422, 'Cement, Portland', '6014002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cement, Portland', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3423, 'Cement, Apo', '6014002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cement, Apo', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3424, 'Cement, Sahara', '6014003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cement, Sahara', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3425, 'Seal, Vulca - Gallons', '6014004A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Vulca - Gallons', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3426, 'Seal, Elasto', '6014005A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Seal, Elasto', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3427, 'Cement, Rubber 320ml./bot.', '6014006A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cement, Rubber 320ml./bot.', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3428, 'Cement, Rubber (Rugby)', '6014006B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cement, Rubber (Rugby)', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3429, 'Solvent, PVC Cement', '6014008A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Solvent, PVC Cement', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3430, 'Solvent, PVC 100cc', '6014008B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Solvent, PVC 100cc', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3431, 'Bidet', '6014025', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bidet', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3432, 'Water, Reducing Concrete Admixture', '6014028', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Water, Reducing Concrete Admixture', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3433, 'Bar, Channel 1/4\"x3\"x8\"x20\'', '6015014B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Channel 1/4\"x3\"x8\"x20\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3434, 'Bar, Channel 1/4\"x1½\"x3\"x20\'', '6015021A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Channel 1/4\"x1½\"x3\"x20\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3435, 'Bar, Channel 152mmx48mmx5.1mmx8.7mm', '6015072', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bar, Channel 152mmx48mmx5.1mmx8.7mm', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3436, 'Paint, TT Color Black (OB)', '6017001A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, TT Color Black (OB)', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3437, 'Paint,TT Color Lamp Black WB', '6017001B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint,TT Color Lamp Black WB', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3438, 'Paint,TT colorWB Hansan Yell', '6017010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint,TT colorWB Hansan Yell', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3439, 'Paint, TT Color Thalo Green WB', '6017011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, TT Color Thalo Green WB', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3440, 'Paint, TT Color Thalo Green WB', '6017011A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, TT Color Thalo Green WB', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3441, 'Paint, TT Color OB Hansa Yellow', '6017012A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, TT Color OB Hansa Yellow', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3442, 'Tinting,ColorVentianRed Ltex', '6017014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tinting,ColorVentianRed Ltex', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3443, 'Paint,  TT Color OB Green', '6017015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint,  TT Color OB Green', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3444, 'Paint, TT Color Thalo Blue WB', '6017017A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, TT Color Thalo Blue WB', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3445, 'Paint, TT Color OB Blue', '6017018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, TT Color OB Blue', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3446, 'Coupling, GI 1/4\"φ Sch. 40', '6018001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupling, GI 1/4\"φ Sch. 40', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3447, 'Coupling, GI 1½\"', '6018005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupling, GI 1½\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3448, 'Coupling 2½\" S-40', '6018007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupling 2½\" S-40', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3449, 'Coupling, GI 4\"', '6018009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupling, GI 4\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3450, 'Coupling, PVC 2\"', '6018012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupling, PVC 2\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3451, 'Coupling, GI 1½\"x2½\"', '6018016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupling, GI 1½\"x2½\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3452, 'Coupling, PVC 1\"', '6018020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupling, PVC 1\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3453, 'Coupling, PVC 2\"φ (Blue)', '6018021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupling, PVC 2\"φ (Blue)', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3454, 'Coupling, GI 6\"', '6018027', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupling, GI 6\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3455, 'Coupling, PVC 63mmφ', '6018028', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupling, PVC 63mmφ', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3456, 'Reducer, Coupling 3/8\"x1/2\"', '6018031A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Reducer, Coupling 3/8\"x1/2\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3457, 'Coupling, GI Reducer 2\"x3/4\"', '6018035', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupling, GI Reducer 2\"x3/4\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3458, 'Coupling, GI 3/8\"', '6018037', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupling, GI 3/8\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3459, 'Coupling, PVC 3\"', '6018047', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupling, PVC 3\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3460, 'Coupling-U, PVC 2\"φ S7 (Blue', '6018048', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupling-U, PVC 2\"φ S7 (Blue', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3461, 'Coupling, CS 1\"φxSch.80', '6018052', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupling, CS 1\"φxSch.80', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3462, 'Coupling, PE 1\"φ', '6018054', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupling, PE 1\"φ', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3463, 'Cup, PVC End 1/2\"φ (Blue)', '6019001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cup, PVC End 1/2\"φ (Blue)', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3464, 'Varnish, Plastic Natural', '6020009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Varnish, Plastic Natural', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3465, 'Varnish', '6020009A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Varnish', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3466, 'Elbow, G.I- 1/4\"φ x sch.40', '6021002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elbow, G.I- 1/4\"φ x sch.40', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3467, 'Elbow,G.I. 1-1/2\"x90Deg.', '6021004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elbow,G.I. 1-1/2\"x90Deg.', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3468, 'Elbow, GI 6\"', '6021006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elbow, GI 6\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3469, 'Elbow, GI 1/2\"φx90°', '6021007B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elbow, GI 1/2\"φx90°', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3470, 'Elbow,GI Strght 1\"x90°xS20', '6021008B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elbow,GI Strght 1\"x90°xS20', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3471, 'Elbow, CS 3\"φx90°xS-80', '6021011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elbow, CS 3\"φx90°xS-80', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3472, 'Elbow, GI Pipe 2½\"φx45° S40', '6021014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elbow, GI Pipe 2½\"φx45° S40', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3473, 'Elbow, PVC 2\"φ x 90°', '6021025A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elbow, PVC 2\"φ x 90°', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3474, 'Elbow, CS Pipe 4\"φx90° Sch-40', '6021030A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elbow, CS Pipe 4\"φx90° Sch-40', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3475, 'Elbow, PVC 2\"φx45°', '6021034', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elbow, PVC 2\"φx45°', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3476, 'Elbow, CS 10\"φx90° S-30', '6021038A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elbow, CS 10\"φx90° S-30', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3477, 'Elbow, SS Short Radius 2½\"', '6021042', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elbow, SS Short Radius 2½\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3478, 'Elbow, SS Pipe 6\"ASTM A105', '6021044', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elbow, SS Pipe 6\"ASTM A105', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3479, 'Elbow,GI Strght 3/4\"x90°xS20', '6021050', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elbow,GI Strght 3/4\"x90°xS20', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3480, 'Elbow, CS 8\"φx90°LxS40', '6021070A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elbow, CS 8\"φx90°LxS40', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3481, 'Pipe, CS Elbow 350mmx90° S-4', '6021083', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pipe, CS Elbow 350mmx90° S-4', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3482, 'Elbow, PVC 4\"φx90°', '6021094', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elbow, PVC 4\"φx90°', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3483, 'Elbow, CS 20\"φx90°', '6021116', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elbow, CS 20\"φx90°', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3484, 'Elbow, G.I Straight 1/2\"φ', '6021117', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elbow, G.I Straight 1/2\"φ', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3485, 'Elbow, SS 1-1/2\"φ x 90° x sch.80', '6021167', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Elbow, SS 1-1/2\"φ x 90° x sch.80', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3486, 'Faucet, Bronze 1/2\"φ', '6023002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Faucet, Bronze 1/2\"φ', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3487, 'Faucet, Brass 1/2\"dia.', '6023002C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Faucet, Brass 1/2\"dia.', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3488, 'Faucet, Lavatory 1/2\"φ', '6023003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Faucet, Lavatory 1/2\"φ', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3489, 'Faucet,Lavatory SingleContrl ½', '6023006A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Faucet,Lavatory SingleContrl ½', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3490, 'Faucet, Kitchen Sink Dble cont', '6023015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Faucet, Kitchen Sink Dble cont', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3491, 'Faucet, Threee-way w/ Hose & Shower Head', '6023019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Faucet, Threee-way w/ Hose & Shower Head', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3492, 'Glass,Clear 1/8\"T A=(1.70x1.70\')@40.46 sq.ft', '6024095', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Glass,Clear 1/8\"T A=(1.70x1.70\')@40.46 sq.ft', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3493, 'Glass,Clear 1/8\"T D=(1.80\'φ) @ 2.54 sq.ft', '6024095A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Glass,Clear 1/8\"T D=(1.80\'φ) @ 2.54 sq.ft', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3494, 'Glue, Wood', '6025001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Glue, Wood', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3495, 'Glue, Wood', '6025001B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Glue, Wood', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3496, 'Glue, Wood', '6025003B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Glue, Wood', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3497, 'Glue, Vinyl Tiles', '6025004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Glue, Vinyl Tiles', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3498, 'Lacquer,Clear Gloss 350cc/bot.', '6027008A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lacquer,Clear Gloss 350cc/bot.', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3499, 'Paint, Red Oxide Metal Primer', '6029001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, Red Oxide Metal Primer', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3500, 'Nails, CW #1½\" (25 kgs/box)', '6031001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nails, CW #1½\" (25 kgs/box)', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3501, 'Nails, CW #2\" (25 kgs/box)', '6031002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nails, CW #2\" (25 kgs/box)', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3502, 'Nails, CW #2½\" (25 kgs./box)', '6031003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nails, CW #2½\" (25 kgs./box)', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3503, 'Nails, CW #3\" (25 kgs/box)', '6031004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nails, CW #3\" (25 kgs/box)', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3504, 'Nails, CW #4\" (25 kgs./box)', '6031005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nails, CW #4\" (25 kgs./box)', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3505, 'Nails, CW #4\" (25 kgs./box)', '6031005A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nails, CW #4\" (25 kgs./box)', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3506, 'Nails, CW #5\" (25 kgs/box)', '6031006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nails, CW #5\" (25 kgs/box)', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3507, 'Nails, Finishing #1\"', '6031007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nails, Finishing #1\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3508, 'Nails, Finishing #1½\"', '6031008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nails, Finishing #1½\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3509, 'Nails, Finishing #2', '6031009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nails, Finishing #2', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3510, 'Nails, CW #1\" (25kgs/box)', '6031012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nails, CW #1\" (25kgs/box)', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3511, 'Nails, Umbrella', '6031019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nails, Umbrella', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3512, 'Nipple, GI 6\"', '6032001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nipple, GI 6\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3513, 'Nipple, Hex 3/4\"', '6032024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nipple, Hex 3/4\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3514, 'Paint, QDE Nile Green', '6033002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, QDE Nile Green', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3515, 'Paint, Permanent Red LTC', '6033005A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, Permanent Red LTC', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3516, 'Paint, QDE Silver Gray', '6033012C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, QDE Silver Gray', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3517, 'Paint, Silver Aluminum Finish', '6033012D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, Silver Aluminum Finish', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3518, 'Paint, Aluminum Hi-Heat', '6033015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, Aluminum Hi-Heat', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3519, 'Paint, QDE Aluminum', '6033015D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, QDE Aluminum', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3520, 'Paint, QDE Apple Green', '6033017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, QDE Apple Green', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3521, 'Paint, Thalo Green LTC', '6033020A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, Thalo Green LTC', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3522, 'Paint, Latex Gloss White', '6033025', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, Latex Gloss White', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3523, 'Rust, Converter \"Eagle\"', '6033028A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rust, Converter \"Eagle\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3524, 'Converter, Rust', '6033028B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Converter, Rust', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3525, 'Paint, QDE Black', '6033037', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, QDE Black', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3526, 'Paint, QDE White', '6033047', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, QDE White', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3527, 'Paint, Flatwall Enamel White', '6033047A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, Flatwall Enamel White', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3528, 'Paint, Flatwall Enamel White', '6033047C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, Flatwall Enamel White', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3529, 'Primer, Epoxy w/Catalyst Gray', '6033050', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Primer, Epoxy w/Catalyst Gray', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3530, 'Paint, QDE Choco Brown', '6033053', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, QDE Choco Brown', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3531, 'Paint, Latex Choco Brown', '6033054A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, Latex Choco Brown', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3532, 'Paint, Spray Red', '6033064A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, Spray Red', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3533, 'Paint, Spray Flourescent Red', '6033064B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, Spray Flourescent Red', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3534, 'Filler, Body w/hardener', '6033065A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filler, Body w/hardener', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3535, 'Filler, Body w/ hardener', '6033065B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Filler, Body w/ hardener', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3536, 'Paint,QDE International Red', '6033067', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint,QDE International Red', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3537, 'Solignum', '6033073', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Solignum', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3538, 'Paint, Black LTC', '6033082B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, Black LTC', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3539, 'Paint, QDE Caterpillar Yellow', '6033086', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, QDE Caterpillar Yellow', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3540, 'Paint,Hansa Yellow', '6033088B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint,Hansa Yellow', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3541, 'Paint, Latex Flat White', '6033101', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, Latex Flat White', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3542, 'Paint, QDE White Gloss', '6033108', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, QDE White Gloss', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3543, 'Paint, Spray', '6033125', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, Spray', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3544, 'Paint, QDE Blue Delft', '6033126', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, QDE Blue Delft', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3545, 'Paint, Thalo Blue LTC', '6033126A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paint, Thalo Blue LTC', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3546, 'Epoxy, Concrete High Viscosity', '6034004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Epoxy, Concrete High Viscosity', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3547, 'Pipe, CS 3\"φ x 20\' S-40', '6035003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pipe, CS 3\"φ x 20\' S-40', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3548, 'Pipe, CS 3\"φx20\'L S-80', '6035003B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pipe, CS 3\"φx20\'L S-80', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3549, 'Pipe, CS 10\"φx20\'xS-30', '6035004A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pipe, CS 10\"φx20\'xS-30', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3550, 'Pipe, GI ½\"φ x 20\'L S-40', '6035019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pipe, GI ½\"φ x 20\'L S-40', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3551, 'Pipe, CS 5\" x 20ft.L x sch.8', '6035026', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pipe, CS 5\" x 20ft.L x sch.8', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3552, 'Pipe, G.I 3/4\"φ x 20\'', '6035032', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pipe, G.I 3/4\"φ x 20\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3553, 'Pipe, PVC 2\"φ (Orange)', '6035038', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pipe, PVC 2\"φ (Orange)', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3554, 'Pipe, GI 2\"φ x 20\'L S-40', '6035048', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pipe, GI 2\"φ x 20\'L S-40', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3555, 'Pipe, SS 1¼\"x7½\'L', '6035060', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pipe, SS 1¼\"x7½\'L', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3556, 'Pipe, SS 1¼\"φx15½\'', '6035060A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pipe, SS 1¼\"φx15½\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3557, 'Pipe, SS 1¼\"φx21½\'', '6035060B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pipe, SS 1¼\"φx21½\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3558, 'Pipe, CS 8\"φx6MxSch-40', '6035074B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pipe, CS 8\"φx6MxSch-40', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3559, 'Pipe, CS (Seamless)- 3\"x6m L, Sch. 80', '6035075B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pipe, CS (Seamless)- 3\"x6m L, Sch. 80', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3560, 'Pipe, CS 4\"φx6mmx6mxS-40', '6035087C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pipe, CS 4\"φx6mmx6mxS-40', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3561, 'Pipe, RSC 3/4\"x10\'', '6035107', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pipe, RSC 3/4\"x10\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3562, 'Pipe, CS 6\"dia.x20\'L S-80', '6035131', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pipe, CS 6\"dia.x20\'L S-80', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3563, 'Pipe, BI 2\"dia.x 10\'', '6035136', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pipe, BI 2\"dia.x 10\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3564, 'Pipe, CS (150 NB x sch.40)', '6035151A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pipe, CS (150 NB x sch.40)', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3565, 'Pipe, CS (100 NB x sch.40)', '6035152', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pipe, CS (100 NB x sch.40)', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3566, 'Plate,MS 6mm X 4\'X 8\'', '6036002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plate,MS 6mm X 4\'X 8\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3567, 'Plate, MS 3/8\"x4\'x8\'', '6036004A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plate, MS 3/8\"x4\'x8\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3568, 'Plate, MS 12mmTx4\'Wx8\'L', '6036007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plate, MS 12mmTx4\'Wx8\'L', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3569, 'Plate, Steel 32mm\"Tx4\'Wx8\'L', '6036010A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plate, Steel 32mm\"Tx4\'Wx8\'L', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3570, 'Plate, MS 1/8\"x4\'Wx8\'L', '6036016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plate, MS 1/8\"x4\'Wx8\'L', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3571, 'Plate, MS 3mmTx4\'Wx8\'L', '6036016B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plate, MS 3mmTx4\'Wx8\'L', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3572, 'Plate, AR 1/4\"Tx4ft.x8ft.', '6036033A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plate, AR 1/4\"Tx4ft.x8ft.', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3573, 'Plate, MS 5/16\"thk x 4\'W x 8L\'', '6036036', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plate, MS 5/16\"thk x 4\'W x 8L\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3574, 'Plate,SS Perfrtd 53\"Lx28\"Wx2mmTx8mmx10mmDstnc', '6036045', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plate,SS Perfrtd 53\"Lx28\"Wx2mmTx8mmx10mmDstnc', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3575, 'Plate, MS 7.5mmx4\'x8\'', '6036046', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plate, MS 7.5mmx4\'x8\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3576, 'Plate, SS 2.5mmxTx4x8', '6036047', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plate, SS 2.5mmxTx4x8', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3577, 'Plate, SS Prforatd 53\"Lx28\"Wx2Tx8mmφx10mm dis', '6036048', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plate, SS Prforatd 53\"Lx28\"Wx2Tx8mmφx10mm dis', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3578, 'Plug, Drain GI 1 1/2\" S-40', '6038003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plug, Drain GI 1 1/2\" S-40', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3579, 'Sand, Washed', '6041103', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sand, Washed', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3580, 'Gravel 3/4\"', '6041105', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gravel 3/4\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3581, 'Sand & Gravel Mixed', '6041111', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sand & Gravel Mixed', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3582, 'Concrete, Ready Mix', '6041113', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Concrete, Ready Mix', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3583, 'Tee, Cross Soil 1-1/2x1\"', '6044001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tee, Cross Soil 1-1/2x1\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3584, 'Tee, CS Equal 1\"x400 lbs.', '6044003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tee, CS Equal 1\"x400 lbs.', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3585, 'Tee, GI 2½\"', '6044008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tee, GI 2½\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3586, 'Tee, GI 3\" S40', '6044009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tee, GI 3\" S40', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3587, 'Tee, GI Reducer 1½\"x3\"', '6044015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tee, GI Reducer 1½\"x3\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3588, 'Tee, Reducer CS 1\"x1/2\"', '6044018', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tee, Reducer CS 1\"x1/2\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3589, 'Tee, GI 1/2\"φ', '6044021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tee, GI 1/2\"φ', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3590, 'Tee,PVC 2-1/2\"', '6044024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tee,PVC 2-1/2\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3591, 'Tee, CS Reducer 2\"X1\' S40', '6044031', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tee, CS Reducer 2\"X1\' S40', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3592, 'Tee, Reducer 1\"x2\"', '6044033', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tee, Reducer 1\"x2\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3593, 'Tee, SS Cross 6\"', '6044037', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tee, SS Cross 6\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3594, 'Tee, BI Pipe 10\" S40', '6044044', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tee, BI Pipe 10\" S40', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3595, 'Tee, CS Reducer 1½\"x3/4\"', '6044046', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tee, CS Reducer 1½\"x3/4\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3596, 'Tee, GI Reducer 1\"φx1/2\"φ S-40', '6044049', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tee, GI Reducer 1\"φx1/2\"φ S-40', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3597, 'Tee, Steel Pipe 8\"φ S40', '6044050', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tee, Steel Pipe 8\"φ S40', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3598, 'Tee, CS Reducer 100mmx125mm', '6044051', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tee, CS Reducer 100mmx125mm', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3599, 'Tee, CS Reducer 8\"x4\" S40', '6044052', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tee, CS Reducer 8\"x4\" S40', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3600, 'Tee, GI red 6\"x4\"', '6044053', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tee, GI red 6\"x4\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3601, 'Tee, GI Reducer 4\"x2\"', '6044054', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tee, GI Reducer 4\"x2\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3602, 'Tee, Pipe G.I. 4\"x2½\"', '6044055', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tee, Pipe G.I. 4\"x2½\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3603, 'Tee, G.I. Weld 6\"', '6044057', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tee, G.I. Weld 6\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3604, 'Tee, G.I. Reducer 2½\"x1/2\"', '6044061', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tee, G.I. Reducer 2½\"x1/2\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3605, 'Tee, CS 3\"x3\" S40', '6044062', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tee, CS 3\"x3\" S40', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3606, 'Tee, Reducer pipe 8\"x8\"x6\"', '6044070', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tee, Reducer pipe 8\"x8\"x6\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3607, 'Tee, Reducer CS Pipe 10\"x4\"', '6044071', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tee, Reducer CS Pipe 10\"x4\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3608, 'Tee, CS Reducer 12\"x4\"', '6044075', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tee, CS Reducer 12\"x4\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3609, 'Tee, GI 1/4\"φ', '6044083', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tee, GI 1/4\"φ', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3610, 'Reducer, GI Tee 2½\" x 1\"φ S-', '6044084', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Reducer, GI Tee 2½\" x 1\"φ S-', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3611, 'Thinner, Paint', '6045000', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Thinner, Paint', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3612, 'Thinner, Paint \"GI\"', '6045001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Thinner, Paint \"GI\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3613, 'Thinner, Lacquer', '6045003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Thinner, Lacquer', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3614, 'Tile, Trim for 12\"x12\" White', '6046009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tile, Trim for 12\"x12\" White', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3615, 'Tiles 40cmx40cm', '6046032', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tiles 40cmx40cm', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3616, 'Tiles, Non-glazed 30cmx30cm', '6046033', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tiles, Non-glazed 30cmx30cm', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3617, 'Union 2½\" S-40', '6047004A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Union 2½\" S-40', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3618, 'Union, GI 1/4\"φ', '6047008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Union, GI 1/4\"φ', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3619, 'Union, SS 1/4\"φ x sch.40', '6047017A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Union, SS 1/4\"φ x sch.40', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3620, 'Union, GI 6\"', '6047019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Union, GI 6\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3621, 'Union, GI 3/4\"φ S-40', '6047024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Union, GI 3/4\"φ S-40', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3622, 'Union, PVC 2\"dia.', '6047030', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Union, PVC 2\"dia.', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3623, 'Wire, GI Tie #16', '6049001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, GI Tie #16', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3624, 'Wire, GI Tie #18', '6049002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, GI Tie #18', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3625, 'Wire, Mesh 5.5mmx2\"x2\" Steel Matting', '6049004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Mesh 5.5mmx2\"x2\" Steel Matting', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3626, 'Sheet, GI Plain #18 4\'x8\'', '6051001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sheet, GI Plain #18 4\'x8\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3627, 'Sheet, Plain (GI) .40mmTx3\'x8\'', '6051002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sheet, Plain (GI) .40mmTx3\'x8\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3628, 'Sheet, GI Corr. 12\'L (0.4mmT)', '6051056', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sheet, GI Corr. 12\'L (0.4mmT)', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3629, 'Plywood, Marine 1/4\"Tx4\'Wx8\'L', '6054004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plywood, Marine 1/4\"Tx4\'Wx8\'L', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3630, 'Plywood, Marine 3/4\"Tx4\'Wx8\'L', '6054007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Plywood, Marine 3/4\"Tx4\'Wx8\'L', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3631, 'Formica 4\'x8\' (White)', '6054016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Formica 4\'x8\' (White)', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3632, 'Board, Fiber Concrete ½\"Tx10\"Wx8\'L', '6054024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Board, Fiber Concrete ½\"Tx10\"Wx8\'L', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3633, 'Chisel, Star-Pointed 400mm', '6055011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Chisel, Star-Pointed 400mm', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3634, 'CHB 4\" Concrete Hollow Block', '6056001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'CHB 4\" Concrete Hollow Block', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3635, 'Reducer, CS Pipe 4\"x8\" S40', '6057003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Reducer, CS Pipe 4\"x8\" S40', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3636, 'Reducer, UPVC Blue 63mmx25mm', '6057009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Reducer, UPVC Blue 63mmx25mm', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3637, 'Reducer, PVC 2\"x3/4\" Concent', '6057029', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Reducer, PVC 2\"x3/4\" Concent', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3638, 'Reducer,Cncentrc pipe 12φx14', '6057045', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Reducer,Cncentrc pipe 12φx14', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3639, 'Tube, Boiler Seamless', '6059017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tube, Boiler Seamless', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3640, 'Tube, Steel 42.4mmx33.4mmx20\'', '6059022', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tube, Steel 42.4mmx33.4mmx20\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3641, 'Tube, Steel 50mmx34mmx20\'', '6059023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tube, Steel 50mmx34mmx20\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3642, 'Tube, Steel 89mmx80mmx20\'', '6059024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tube, Steel 89mmx80mmx20\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3643, 'Hose, Flexible 3/8\"x1/2\" USA', '6070003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Flexible 3/8\"x1/2\" USA', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3644, 'Hose, Flex. 3/8\"x1/2\"x12\"L', '6070003A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Flex. 3/8\"x1/2\"x12\"L', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3645, 'Hose, Flexible 3/8x1/2x18', '6070003B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Flexible 3/8x1/2x18', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3646, 'Hose, Flexible 3/8\"φx1/2\"φx18\"L', '6070003C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Flexible 3/8\"φx1/2\"φx18\"L', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3647, 'Hose, Flexible 1/2\"φx1/2\"φ16\"', '6070005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Flexible 1/2\"φx1/2\"φ16\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3648, 'Hose, Flexible 1/2\"x1/2\"x12\"', '6070005C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Flexible 1/2\"x1/2\"x12\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3649, 'Drain, Floor 4\"x4\"', '6070006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Drain, Floor 4\"x4\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3650, 'Hose, Flexible 3/8\"x7/8\" USA', '6070012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Flexible 3/8\"x7/8\" USA', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3651, 'Insulation, Roof 10mmThk.', '6072005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Insulation, Roof 10mmThk.', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3652, 'Blanket, Insulation Rckwool 4m', '6072019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Blanket, Insulation Rckwool 4m', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3653, 'Blanket, Insolation Rockwool 4', '6072020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Blanket, Insolation Rockwool 4', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3654, 'Moulding,Casing 1\"x1\"x8\'L', '6076003D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Moulding,Casing 1\"x1\"x8\'L', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3655, 'Moulding 1\"x1\"x8\'', '6076006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Moulding 1\"x1\"x8\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3656, 'Lumber, Lawaan 2\"x2\"x10\'L', '6076012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lumber, Lawaan 2\"x2\"x10\'L', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3657, 'Lumber,Lawaan 2\"x2\"x10\'L', '6076012A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lumber,Lawaan 2\"x2\"x10\'L', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3658, 'Lumber, Coco 2\"x2\"x12\'', '6076015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lumber, Coco 2\"x2\"x12\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3659, 'Lumber, Good 2\"x4\"x5\'', '6076021A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lumber, Good 2\"x4\"x5\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3660, 'Lumber, Good 3\"x4\"x4\'', '6076022A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lumber, Good 3\"x4\"x4\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3661, 'Lumber, Lawaan 1\"x4\"x10\'', '6076026B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lumber, Lawaan 1\"x4\"x10\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3662, 'Lumber, Lawaan 2x8x10\'L', '6076034A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lumber, Lawaan 2x8x10\'L', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3663, 'Lumber, Lawaan 3\"x4\"x10\'L', '6076039A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lumber, Lawaan 3\"x4\"x10\'L', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3664, 'Lumber, Coco 2\"x3\"x10\'', '6076041A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lumber, Coco 2\"x3\"x10\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26');
INSERT INTO `items` (`id`, `item_name`, `item_code`, `item_type`, `item_vendor_id`, `item_uom_id`, `item_reorder_point`, `item_category_id`, `item_quantity`, `item_sales_description`, `item_purchase_description`, `item_selling_price`, `item_cost_price`, `item_cogs_account_id`, `item_income_account_id`, `item_asset_account_id`, `created_at`) VALUES
(3665, 'Lumber, Lawaan 1\"x2x\"8\'L', '6076052', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lumber, Lawaan 1\"x2x\"8\'L', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3666, 'Lumber, Good 2\"x2\"x8\'', '6076073', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lumber, Good 2\"x2\"x8\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3667, 'Lumber, Good 1/4\"X1\"x8\'', '6076074', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lumber, Good 1/4\"X1\"x8\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3668, 'Moulding,  1/2\"x2\"x8\'', '6076075', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Moulding,  1/2\"x2\"x8\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3669, 'Moulding, Cornice 1\"x3\"x8\'', '6076076', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Moulding, Cornice 1\"x3\"x8\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3670, 'Lumber, Lawaan 2\"x2\"x6\'', '6076077', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Lumber, Lawaan 2\"x2\"x6\'', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3671, 'Valve, Angle 3/8\"', '6077010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Angle 3/8\"', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3672, 'Valve, Angle 1/2\"φx1/2\"φ', '6077010D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Angle 1/2\"φx1/2\"φ', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3673, 'Valve, Angle - 3 way 1/2', '6077015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Valve, Angle - 3 way 1/2', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3674, 'Ball, Flapper', '6079001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ball, Flapper', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3675, 'Indoor Panel WPC FLTD Radiant 4C', '6080001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Indoor Panel WPC FLTD Radiant 4C', NULL, NULL, NULL, 4615, 4603, 3195, '2024-09-19 07:27:26'),
(3676, 'Rubber Bond', '7013001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rubber Bond', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3677, 'Count Tags (Continous Form)', '7016002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Count Tags (Continous Form)', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3678, 'Book, Record 300 pages', '7019001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Book, Record 300 pages', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3679, 'Book, Record 500 pages', '7019002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Book, Record 500 pages', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3680, 'Notebook, Columnar  3 cols.', '7019003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Notebook, Columnar  3 cols.', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3681, 'Notebook, Columnar  4 cols.', '7019004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Notebook, Columnar  4 cols.', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3682, 'Notebook, Columnar 16 cols.', '7019006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Notebook, Columnar 16 cols.', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3683, 'Notebook, Columnar 24 cols.', '7019007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Notebook, Columnar 24 cols.', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3684, 'Chalk', '7035001A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Chalk', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3685, 'Clip, Paper 33mm/50gms', '7036001A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Clip, Paper 33mm/50gms', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3686, 'Clip, Paper #50', '7036002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Clip, Paper #50', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3687, 'Clip, Binder Long 51mm', '7036006B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Clip, Binder Long 51mm', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3688, 'Daily 6MW STG Operational Log', '7043002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Daily 6MW STG Operational Log', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3689, 'Daily Man Diesel Operation L', '7043009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Daily Man Diesel Operation L', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3690, 'Daily 5MWTBA Operation Log S', '7043010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Daily 5MWTBA Operation Log S', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3691, 'Envelope, Brown - Long', '7056001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Envelope, Brown - Long', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3692, 'Envelope, Brown - Short', '7056002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Envelope, Brown - Short', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3693, 'Fluid, Correction 15ml.', '7058002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fluid, Correction 15ml.', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3694, 'Eraser, Rubber \"Steadtler\"', '7059002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Eraser, Rubber \"Steadtler\"', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3695, 'Fastener, Paper', '7064001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fastener, Paper', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3696, 'Folder, File - Long', '7070001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Folder, File - Long', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3697, 'Folder, File - Short', '7070002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Folder, File - Short', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3698, 'Binder, Two-Holes Ring Short', '7070005A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Binder, Two-Holes Ring Short', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3699, 'Ink, Pilot - Black/Blue', '7078001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ink, Pilot - Black/Blue', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3700, 'Ink, JAM Matthews DOD 1001', '7078007A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ink, JAM Matthews DOD 1001', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3701, 'Ink, HP Deskjet #704 Black', '7078010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ink, HP Deskjet #704 Black', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3702, 'Ink, HP Deskjet #704 Tri-Color', '7078011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ink, HP Deskjet #704 Tri-Color', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3703, 'Kit, Toner Kyocera TK-4109', '7078015C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Kit, Toner Kyocera TK-4109', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3704, 'Toner kit TK 4140 Kyocera', '7078015D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Toner kit TK 4140 Kyocera', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3705, 'Ink, 003 Black EPSON', '7078046', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ink, 003 Black EPSON', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3706, 'Ink, 003 Cyan EPSON', '7078047', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ink, 003 Cyan EPSON', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3707, 'Ink, 003 Magenta EPSON', '7078048', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ink, 003 Magenta EPSON', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3708, 'Ink, 003 Yellow EPSON', '7078049', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ink, 003 Yellow EPSON', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3709, 'Ink, HP #678 Black', '7078086', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ink, HP #678 Black', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3710, 'Ink, HP #678 Tri-color', '7078087', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ink, HP #678 Tri-color', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3711, 'Ink, Epson T6641 Black', '7078088', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ink, Epson T6641 Black', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3712, 'Ink, Epson T6642 Cyan', '7078089', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ink, Epson T6642 Cyan', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3713, 'Ink, Epson T6643 Magenta', '7078090', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ink, Epson T6643 Magenta', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3714, 'Ink, Epson T6644 Yellow', '7078091', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ink, Epson T6644 Yellow', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3715, 'Ink, HP #901 Black', '7078092', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ink, HP #901 Black', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3716, 'Ink, HP Deskjet #680 Black', '7078094', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ink, HP Deskjet #680 Black', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3717, 'Ink, HP Deskjet #680 Tri-color', '7078095', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ink, HP Deskjet #680 Tri-color', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3718, 'Envelope, Letter - long', '7080001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Envelope, Letter - long', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3719, 'Envelope, Letter - short', '7080003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Envelope, Letter - short', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3720, 'Form, Materials Requisition Slip (MRS)', '7089001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Form, Materials Requisition Slip (MRS)', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3721, 'Notebook, Columnar 10 cols.', '7093002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Notebook, Columnar 10 cols.', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3722, 'Notebook, Columnar 6 columns', '7093007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Notebook, Columnar 6 columns', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3723, 'Pad, Yellow Ruled', '7102004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pad, Yellow Ruled', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3724, 'Paper, Computer 11x9½ 1ply', '7104019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paper, Computer 11x9½ 1ply', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3725, 'Paper, Computer 11x14-7/8 1ply', '7104020', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paper, Computer 11x14-7/8 1ply', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3726, 'Paper, Computer 11x14-7/8 3p', '7104021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paper, Computer 11x14-7/8 3p', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3727, 'Paper, Computer 11x14-7/8 2ply', '7104021A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paper, Computer 11x14-7/8 2ply', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3728, 'Paper, Computer 11x9½ 2ply', '7104022', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paper, Computer 11x9½ 2ply', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3729, 'Paper, Computer 11x9-1/2 2ply STD-DB', '7104022A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paper, Computer 11x9-1/2 2ply STD-DB', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3730, 'Paper, Computer 11x9½ 3ply', '7104023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paper, Computer 11x9½ 3ply', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3731, 'Paper, Bond Long F4 70g/m2', '7104026B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paper, Bond Long F4 70g/m2', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3732, 'Paper, Bond Short GSM70', '7104027A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paper, Bond Short GSM70', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3733, 'Paper, Bond Short F4 70g/m2', '7104027C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paper, Bond Short F4 70g/m2', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3734, 'Paper, Bond Short', '7104027D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paper, Bond Short', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3735, 'Voucher, Check 7-1/3x9½ 3ply', '7104028', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Voucher, Check 7-1/3x9½ 3ply', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3736, 'Paper, Chart \"Yokogawa\"', '7104029', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paper, Chart \"Yokogawa\"', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3737, 'Tag, Material Booklet', '7104032', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tag, Material Booklet', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3738, 'Paper, Computer 13\"x9½\" 2ply', '7104039', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paper, Computer 13\"x9½\" 2ply', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3739, 'Pencil, Mongol #2 12pcs/box', '7107001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pencil, Mongol #2 12pcs/box', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3740, 'Refill, Pen 0.5 \"My-Gel\"', '7107007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Refill, Pen 0.5 \"My-Gel\"', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3741, 'Pen, Ball', '7108001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pen, Ball', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3742, 'Pen, Pentel (Blue & Black)', '7108002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pen, Pentel (Blue & Black)', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3743, 'Pen, Stabilo Boss Hi-lighter', '7108004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pen, Stabilo Boss Hi-lighter', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3744, 'Marker, WyteBoard', '7108010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Marker, WyteBoard', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3745, 'Form, Purchase Requisition (PR)', '7116002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Form, Purchase Requisition (PR)', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3746, 'Ribbon, Typewriter \"BLACK\"', '7121002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ribbon, Typewriter \"BLACK\"', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3747, 'Ribbon, EPSON Cart. #8750', '7121010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ribbon, EPSON Cart. #8750', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3748, 'Ribbon, EPSON Cartridge Genuine#SO15086/15087', '7121014A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ribbon, EPSON Cartridge Genuine#SO15086/15087', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3749, 'Ribbon, EPSON Cart. #SO15327', '7121017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ribbon, EPSON Cart. #SO15327', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3750, 'Ribbon, Cart. SO15632', '7121030', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ribbon, Cart. SO15632', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3751, 'Cleaner, Printer DOD 1001', '7121046', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cleaner, Printer DOD 1001', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3752, 'Head, Printer EPSON FX 2175', '7121047', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Head, Printer EPSON FX 2175', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3753, 'Tape, Scotch 1\"', '7140002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tape, Scotch 1\"', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3754, 'Tape, Masking 1\"', '7140003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tape, Masking 1\"', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3755, 'Tape, Magic - 3/4\" width', '7140004A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tape, Magic - 3/4\" width', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3756, 'Tape, Packaging 2\"', '7140006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tape, Packaging 2\"', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3757, 'Tape, Scotch 1/2\"', '7140007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tape, Scotch 1/2\"', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3758, 'Form, Routing Slip', '7148001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Form, Routing Slip', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3759, 'Form, Routing Slip 100shts/pad', '7148002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Form, Routing Slip 100shts/pad', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3760, 'Wire, Staple #35-5M', '7156001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wire, Staple #35-5M', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3761, 'Form, Cane Delivery Receipts', '7182001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Form, Cane Delivery Receipts', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3762, 'Form, Planter Cane Delivery Data', '7182002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Form, Planter Cane Delivery Data', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3763, 'Form, Miscellaneous Gate Pass', '7310001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Form, Miscellaneous Gate Pass', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3764, 'Form, Molasses GatePass/Waybil', '7310002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Form, Molasses GatePass/Waybil', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3765, 'Form, Sugar GatePass/Waybil', '7310003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Form, Sugar GatePass/Waybil', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3766, 'Form, Refined Sugar Pass', '7310003A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Form, Refined Sugar Pass', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3767, 'Form, C.T.E.', '7320002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Form, C.T.E.', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3768, 'Form, Borrowers Slip (BS)', '7321002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Form, Borrowers Slip (BS)', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3769, 'Mouse, Optical USB type', '7340050', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Mouse, Optical USB type', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3770, 'Drive, Flash 32GB', '7340052', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Drive, Flash 32GB', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3771, 'Adapter, USB Network', '7340054A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Adapter, USB Network', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3772, 'USB, DH 485 Interface Converter', '7340188', 'Inventory', NULL, NULL, NULL, NULL, 0, 'USB, DH 485 Interface Converter', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3773, 'Drive, Hard 2.5\" SATA3 SSD 450-480GB', '7340189', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Drive, Hard 2.5\" SATA3 SSD 450-480GB', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3774, 'Cable, Female DB9', '7350012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cable, Female DB9', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3775, 'Cable, UTP Cat-5', '7360024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cable, UTP Cat-5', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3776, 'Cable, UTP CAT5E', '7360024B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cable, UTP CAT5E', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3777, 'Cable, UTP Cat-6', '7360024C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cable, UTP Cat-6', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3778, 'Form, Authority to Withdraw Slip (AWS)', '7409003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Form, Authority to Withdraw Slip (AWS)', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3779, 'Form, AuthoritytoWithdraw Slip', '7409003A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Form, AuthoritytoWithdraw Slip', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3780, 'Form, Fuel Issue Slip (FIS)', '7409004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Form, Fuel Issue Slip (FIS)', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3781, 'Form, Oil&Lubricant Issue Slip', '7409005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Form, Oil&Lubricant Issue Slip', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3782, 'Form, Receiving Report-LOCAL', '7409010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Form, Receiving Report-LOCAL', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3783, 'Form, Materials Issuance Slip (MIS)', '7409011A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Form, Materials Issuance Slip (MIS)', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3784, 'Form,Receiving Report-Makati', '7409013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Form,Receiving Report-Makati', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3785, 'Form, Return Items Report-RIR', '7409014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Form, Return Items Report-RIR', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3786, 'Voucher, Journal 11x9½, 3ply', '7409021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Voucher, Journal 11x9½, 3ply', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3787, 'Voucher, AccountPayable 6½x9½', '7409022', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Voucher, AccountPayable 6½x9½', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3788, 'Form, Acknowledgement Receip', '7417001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Form, Acknowledgement Receip', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3789, 'Calendar, HSMC 17\"Wx22\"L', '7464002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Calendar, HSMC 17\"Wx22\"L', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3790, 'Calendar, HSMC 22\"Wx34\"L', '7464002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Calendar, HSMC 22\"Wx34\"L', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3791, 'Calendar, Planner 12.5\"x18\"', '7464003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Calendar, Planner 12.5\"x18\"', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3792, 'Anti-Virus, Kaspersky', '7532013A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Anti-Virus, Kaspersky', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3793, 'Adapter, Cable RJ45', '7532027', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Adapter, Cable RJ45', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3794, 'Adapter,Card Port25 DB25', '7532028', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Adapter,Card Port25 DB25', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3795, 'Adapter,Card 2 Ports DB9 RS232', '7532029', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Adapter,Card 2 Ports DB9 RS232', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3796, 'Switch, Network LS1008 8 Ports', '7532101', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Switch, Network LS1008 8 Ports', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3797, 'Paper, Carbon @100pcs./pack', '7550003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Paper, Carbon @100pcs./pack', NULL, NULL, NULL, 5828, 4603, 3196, '2024-09-19 07:27:26'),
(3798, 'Acetylene, Refilled', '9001001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Acetylene, Refilled', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3799, 'Gas,Argon Refilled', '9001002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gas,Argon Refilled', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3800, 'Battery, Big \"D\" 1.5V (Black)', '9002001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Battery, Big \"D\" 1.5V (Black)', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3801, 'Battery, Small \"AA\" 1.5V', '9002002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Battery, Small \"AA\" 1.5V', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3802, 'Rechargeable Battery 6V', '9002010A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rechargeable Battery 6V', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3803, 'Battery Pack BP-264', '9002019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Battery Pack BP-264', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3804, 'Battery, Lead Acid 12V/7-7.2Ah', '9002028B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Battery, Lead Acid 12V/7-7.2Ah', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3805, 'Charger, Radio Icom BC-192', '9002040A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Charger, Radio Icom BC-192', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3806, 'Antenna, Transceiver', '9003001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Antenna, Transceiver', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3807, 'Flashlight, Rechargeable 230V', '9003005A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Flashlight, Rechargeable 230V', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3808, 'Jar, Plastic w/ cover 2kg.cap', '9004002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Jar, Plastic w/ cover 2kg.cap', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3809, 'Jar, Plastic w/cover 2pcs,/set', '9004003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Jar, Plastic w/cover 2pcs,/set', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3810, 'Umbrella', '9004004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Umbrella', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3811, 'Bottle,Plastic Sampling 2L cap', '9004024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bottle,Plastic Sampling 2L cap', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3812, 'Beaker, Glass Pyrex 1000ml.', '9006003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Beaker, Glass Pyrex 1000ml.', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3813, 'Beaker, Glass PYREX 150ml.cap', '9006009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Beaker, Glass PYREX 150ml.cap', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3814, 'Beaker, Plastic Pyrex 150ml cap.', '9006009A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Beaker, Plastic Pyrex 150ml cap.', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3815, 'Beaker, Glass PYREX 50ml.cap', '9006010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Beaker, Glass PYREX 50ml.cap', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3816, 'Beaker, Plastic 50ml./cap.', '9006011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Beaker, Plastic 50ml./cap.', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3817, 'Beaker, Plastic 250ml./cap.', '9006012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Beaker, Plastic 250ml./cap.', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3818, 'Beaker, Glass Pyrex 600ml cap.', '9006014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Beaker, Glass Pyrex 600ml cap.', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3819, 'Beaker, Glass Pyrex 2000ml', '9006015', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Beaker, Glass Pyrex 2000ml', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3820, 'Broom, Fiber', '9007001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Broom, Fiber', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3821, 'Broom, Midrib', '9007002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Broom, Midrib', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3822, 'Brush,Floor w/handle 2\"x11\"L', '9007021B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush,Floor w/handle 2\"x11\"L', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3823, 'Brush, Bottle', '9007023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush, Bottle', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3824, 'Roller, Paint 9\" w/pan', '9008002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Roller, Paint 9\" w/pan', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3825, 'Brush, Paint 1½\"', '9008003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush, Paint 1½\"', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3826, 'Brush, Paint 3\"', '9008005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush, Paint 3\"', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3827, 'Brush, Paint 4\"', '9008006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush, Paint 4\"', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3828, 'Roller, Paint 7\"', '9008007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Roller, Paint 7\"', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3829, 'Brush, Paint 2\"', '9008009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush, Paint 2\"', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3830, 'Brush, Paint 1\"', '9008010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush, Paint 1\"', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3831, 'Roller, Paint 9\"', '9008016A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Roller, Paint 9\"', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3832, 'Roller, Paint 4\"', '9008020A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Roller, Paint 4\"', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3833, 'Brush, Nylon 2\"Wx9\"L w/handle', '9008021A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush, Nylon 2\"Wx9\"L w/handle', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3834, 'Brush, Lettering 6 in 1', '9008034', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Brush, Lettering 6 in 1', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3835, 'Roller, Guide 16\"', '9009001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Roller, Guide 16\"', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3836, 'Sandpaper #120', '9011001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sandpaper #120', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3837, 'Sandpaper #240', '9011002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sandpaper #240', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3838, 'Sandpaper #220', '9011002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sandpaper #220', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3839, 'Sandpaper #280', '9011003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sandpaper #280', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3840, 'Sandpaper #150', '9011004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sandpaper #150', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3841, 'Sandpaper #180', '9011005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sandpaper #180', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3842, 'Sandpaper #100', '9011008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sandpaper #100', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3843, 'Gasket Cement', '9012001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket Cement', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3844, 'Cleanser, 500g/tube', '9013001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cleanser, 500g/tube', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3845, 'Cleanser, 350g', '9013001A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cleanser, 350g', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3846, 'Cleanser 350g/pack', '9013001C', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cleanser 350g/pack', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3847, 'Rug, Waste Cotton', '9015001A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rug, Waste Cotton', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3848, 'Coupling, Male & Female Hose & Faucet Adapter', '9018055', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Coupling, Male & Female Hose & Faucet Adapter', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3849, 'Door, Knob Kwikset', '9019002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Door, Knob Kwikset', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3850, 'Door Knob', '9019002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Door Knob', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3851, 'Closer, Door HD', '9019017', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Closer, Door HD', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3852, 'Spring, Door HD', '9019019', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spring, Door HD', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3853, 'Earplug', '9021005A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Earplug', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3854, 'Cloth, Crocus', '9023003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cloth, Crocus', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3855, 'Extinguisher, Fire 1 kg. cap.', '9024001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Extinguisher, Fire 1 kg. cap.', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3856, 'Refill, Fire Ext. 4.5kgs./cap.', '9024003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Refill, Fire Ext. 4.5kgs./cap.', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3857, 'Refill, Fire Ext. 5.0kgs./cap.', '9024004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Refill, Fire Ext. 5.0kgs./cap.', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3858, 'Refill, Fire Ext. 3.0kg/cap.', '9024008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Refill, Fire Ext. 3.0kg/cap.', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3859, 'Refill, Fire Ext. 6.8kg./cap', '9024009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Refill, Fire Ext. 6.8kg./cap', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3860, 'Hose, Fire Extinguisher', '9024011', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Fire Extinguisher', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3861, 'Refill, Fire Ext. 2.25kgs./cap', '9024012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Refill, Fire Ext. 2.25kgs./cap', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3862, 'Refill, Fire Ext. 1.3kg/cap.', '9024013', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Refill, Fire Ext. 1.3kg/cap.', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3863, 'Refill, Fire Ext. 3.30kgs./cap', '9024014', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Refill, Fire Ext. 3.30kgs./cap', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3864, 'Extiguisher, Fire 27.0kgs.ca', '9024021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Extiguisher, Fire 27.0kgs.ca', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3865, 'Refill, Fire Ext. 6.5kgs./cap.', '9024023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Refill, Fire Ext. 6.5kgs./cap.', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3866, 'Handle, Extinguisher', '9024027', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Handle, Extinguisher', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3867, 'Gauge, Extinguisher', '9024028', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gauge, Extinguisher', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3868, 'Gloves, Welding Leather Long', '9028002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gloves, Welding Leather Long', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3869, 'Gloves, Working (Rubberized)', '9028005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gloves, Working (Rubberized)', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3870, 'Gloves, Sterilizd/Latex Surgical 7\" @50prs/bx', '9028016A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gloves, Sterilizd/Latex Surgical 7\" @50prs/bx', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3871, 'Goggles, Eye', '9029007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Goggles, Eye', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3872, 'Goggles, Chemical Polycarbonate', '9029008', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Goggles, Chemical Polycarbonate', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3873, 'Handle, Cabinet w/screw', '9030001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Handle, Cabinet w/screw', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3874, 'Hinges, Cabinet Overlay 1/2\"', '9031005A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hinges, Cabinet Overlay 1/2\"', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3875, 'Hinges, Cabinet Overlay', '9031006B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hinges, Cabinet Overlay', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3876, 'Hinge, 3/4\"x2-1/2\" w/screw', '9031019B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hinge, 3/4\"x2-1/2\" w/screw', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3877, 'Hinges, 1\"x 2\"w/screw', '9031021', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hinges, 1\"x 2\"w/screw', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3878, 'Hinges 3\"x3\"', '9031023A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hinges 3\"x3\"', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3879, 'Hose, Leveling 1/4\"', '9032009A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Leveling 1/4\"', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3880, 'Hose, PE 1\"φ', '9032023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, PE 1\"φ', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3881, 'Hose, Garden 1/2\"φ x 25mtr.', '9032024', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Garden 1/2\"φ x 25mtr.', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3882, 'Hose, Garden 1/2\"φx30mtr.', '9032025', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Garden 1/2\"φx30mtr.', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3883, 'Hose, Pressure 8.5mm Power Washer', '9032026', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hose, Pressure 8.5mm Power Washer', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3884, 'Gasket Maker  #1', '9036001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket Maker  #1', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3885, 'Gasket, Red RTV Silicone 85gms', '9036003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Red RTV Silicone 85gms', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3886, 'Gasket, Red RTV Silicone 85g w/free superglue', '9036003B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gasket, Red RTV Silicone 85g w/free superglue', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3887, 'Sealant, Silicon type', '9036006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sealant, Silicon type', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3888, 'Sealant, Pipe', '9036016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Sealant, Pipe', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3889, 'Oxygen, Refilled', '9040001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Oxygen, Refilled', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3890, 'Padlock, 40mm', '9041005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Padlock, 40mm', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3891, 'Tissue,Bathroom 2 ply', '9042002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Tissue,Bathroom 2 ply', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3892, 'Wipes, Kim', '9042005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wipes, Kim', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3893, 'Pipette, Disposable 3ml cap.', '9043001A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Pipette, Disposable 3ml cap.', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3894, 'Mask, Face Surgical', '9044002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Mask, Face Surgical', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3895, 'Mask, Face Ear Loop', '9044010', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Mask, Face Ear Loop', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3896, 'Mask, Cloth', '9044010A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Mask, Cloth', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3897, 'Mirror,1/4\"thk A=(3.83\'x4.99\') at 19.11 sq.ft', '9045007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Mirror,1/4\"thk A=(3.83\'x4.99\') at 19.11 sq.ft', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3898, 'Rope, Nylon 24mmφ', '9047007', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rope, Nylon 24mmφ', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3899, 'Rope, Nylon ½\"φ', '9047012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Rope, Nylon ½\"φ', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3900, 'Ratchet, Strap Tie w/ Hook HD', '9048005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Ratchet, Strap Tie w/ Hook HD', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3901, 'Hasp, Lock-2\"', '9048006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Hasp, Lock-2\"', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3902, 'Trapal Size: 12\'x15mtr.', '9049012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Trapal Size: 12\'x15mtr.', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3903, 'Liquid, Dishwashing (250ml)', '9050001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Liquid, Dishwashing (250ml)', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3904, 'Liquid, Dishwashing 500ml', '9050002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Liquid, Dishwashing 500ml', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3905, 'Welding Apron', '9053001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Welding Apron', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3906, 'Shoes, Safety', '9056001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Shoes, Safety', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3907, 'Flask, Filtering Glass 1000ml', '9056012', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Flask, Filtering Glass 1000ml', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3908, 'Bags, Empty Sugar (Raw)', '9060001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bags, Empty Sugar (Raw)', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3909, 'Bags, Empty Sugar (Standard)', '9060003', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bags, Empty Sugar (Standard)', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3910, 'Bags, Empty Sugar (Premium)', '9060004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bags, Empty Sugar (Premium)', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3911, 'Wax, Floor 450g/can', '9062001B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wax, Floor 450g/can', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3912, 'Thread, Sewing', '9065005', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Thread, Sewing', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3913, 'Needle DR x 2# 25', '9066002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Needle DR x 2# 25', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3914, 'Needle, DR x 2 #26', '9066004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Needle, DR x 2 #26', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3915, 'Head, Floor Mop', '9069001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Head, Floor Mop', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3916, 'Handle, Mop Head', '9069002', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Handle, Mop Head', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3917, 'Marker, Metal (White/Yellow)', '9070001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Marker, Metal (White/Yellow)', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3918, 'Marker, Metal Paint', '9070001A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Marker, Metal Paint', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3919, 'Gas, Nitrogen Refilled', '9073001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gas, Nitrogen Refilled', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3920, 'Suction, Rubber Bulb One way', '9075001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Suction, Rubber Bulb One way', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3921, 'Gun, Sealant', '9080002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Gun, Sealant', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3922, 'Spray, Baygon 600ml', '9080010B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Spray, Baygon 600ml', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3923, 'Freon 22 (13.6kgs./cyl.)', '9085004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Freon 22 (13.6kgs./cyl.)', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3924, 'Freon, #134A', '9085006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Freon, #134A', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3925, 'Freon, R410 11-3kg 410A', '9085009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Freon, R410 11-3kg 410A', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3926, 'Frame, Certificate Plastic', '9085106', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Frame, Certificate Plastic', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3927, 'Bamboo (Lipak)', '9086001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bamboo (Lipak)', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3928, 'Cocoshell Uncrushed', '9088001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Cocoshell Uncrushed', NULL, NULL, NULL, 4971, 4603, 3205, '2024-09-19 07:27:26'),
(3929, 'Catches, Cabinet', '9090004', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Catches, Cabinet', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3930, 'Net, Hair Surgical Disposable', '9095015A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Net, Hair Surgical Disposable', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3931, 'Nozzle, Sprayer Power Stick', '9095016', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Nozzle, Sprayer Power Stick', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3932, 'T-shirt', '9096006', 'Inventory', NULL, NULL, NULL, NULL, 0, 'T-shirt', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3933, 'Aluminum, Foil 35sq.ft/roll', '9116001A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Aluminum, Foil 35sq.ft/roll', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3934, 'Machete', '9118001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Machete', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3935, 'Flashlight, Rechargeable', '9120001', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Flashlight, Rechargeable', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3936, 'Wrap, Tie Electrical 8mmx450mm', '9129002A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wrap, Tie Electrical 8mmx450mm', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3937, 'Fiber Bag', '9129009', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Fiber Bag', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3938, 'Wrap, Tie Electrical 200mmx5mm', '9129009A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Wrap, Tie Electrical 200mmx5mm', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3939, 'Bag, Plastic 8\"x14\"', '9129022B', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bag, Plastic 8\"x14\"', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3940, 'Bag, Plastic 7\"x14\"', '9129022D', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bag, Plastic 7\"x14\"', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3941, 'Bag, Plastic 6\"x12\"', '9129023', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bag, Plastic 6\"x12\"', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3942, 'Bags, Glad Plstic Storage 17', '9129026', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bags, Glad Plstic Storage 17', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3943, 'Bags, Glad Plstic Storage 16', '9129027', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bags, Glad Plstic Storage 16', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3944, 'Bags, Glad Plstic Storage 26', '9129028', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Bags, Glad Plstic Storage 26', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3945, 'Styropor', '9129040', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Styropor', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3946, 'Device, Early Warning AWD', '9136005A', 'Inventory', NULL, NULL, NULL, NULL, 0, 'Device, Early Warning AWD', NULL, NULL, NULL, 4785, 4603, 3197, '2024-09-19 07:27:26'),
(3947, 'Molasses', 'ML001', 'Finished Goods', NULL, 23, 0.00, 44, 0, 'Molasses', 'Molasses', 0.00, 0.00, 6041, 4599, 3189, '2024-09-19 07:46:39'),
(3948, 'Raw Sugar', 'RW001', 'Finished Goods', NULL, 23, 0.00, 44, 0, 'Raw Sugar', 'Raw Sugar', 0.00, 0.00, 4611, 4597, 3188, '2024-09-19 07:49:24'),
(3949, 'Molasses Hauling', 'MH002', 'Service', NULL, 19, 0.00, 44, 0, 'Molasses Hauling', '', 1.00, 1.00, 0, 4608, 0, '2024-09-23 05:02:27'),
(3950, 'Molasses Storage Fee', 'MSF01', 'Service', NULL, 19, 0.00, 44, 0, 'Molasses Storage Fee', '', 0.00, 0.00, 0, 4605, 0, '2024-09-23 05:05:20'),
(3951, 'Scrap', 'SC001', 'Service', NULL, 19, 0.00, 44, 0, 'Scrap', '', 0.00, 0.00, 0, 4607, 0, '2024-09-23 05:09:25'),
(3952, 'EDAP Allocation', 'ED001', 'Service', NULL, 19, 0.00, 44, 0, 'EDAP Allocation', '', 0.00, 0.00, 0, 4323, 0, '2024-09-23 05:11:25'),
(3953, 'Monster Energy Drink', '120500C', 'Inventory', NULL, 13, 0.00, 46, 0, 'Monster Energy Drink', '', 100.00, 98.00, 1, 11, 10, '2024-10-17 16:03:31');

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`id`, `name`) VALUES
(4, 'Makati'),
(5, 'Millsite');

-- --------------------------------------------------------

--
-- Table structure for table `material_issuance`
--

CREATE TABLE `material_issuance` (
  `id` int(11) NOT NULL,
  `mis_no` varchar(50) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0,
  `print_status` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `material_issuance_details`
--

CREATE TABLE `material_issuance_details` (
  `id` int(11) NOT NULL,
  `mis_id` int(11) DEFAULT 0,
  `item_id` int(11) DEFAULT 0,
  `quantity` int(11) DEFAULT 0,
  `cost` double(10,2) DEFAULT 0.00,
  `amount` double(10,2) DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `or_payments`
--

CREATE TABLE `or_payments` (
  `id` int(11) NOT NULL,
  `or_number` varchar(255) DEFAULT NULL,
  `ci_no` varchar(255) DEFAULT NULL,
  `or_date` date NOT NULL,
  `or_account_id` int(11) NOT NULL,
  `customer_po` varchar(50) DEFAULT NULL,
  `so_no` varchar(50) DEFAULT NULL,
  `rep` varchar(50) DEFAULT NULL,
  `check_no` varchar(50) DEFAULT NULL,
  `customer_id` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `location` varchar(50) NOT NULL,
  `memo` text NOT NULL,
  `gross_amount` decimal(15,2) NOT NULL,
  `discount_amount` decimal(15,2) NOT NULL,
  `net_amount_due` decimal(15,2) NOT NULL,
  `vat_amount` decimal(15,2) NOT NULL,
  `vatable_amount` decimal(15,2) NOT NULL,
  `zero_rated_amount` decimal(15,2) NOT NULL,
  `vat_exempt_amount` decimal(15,2) NOT NULL,
  `tax_withheld_percentage` int(11) NOT NULL,
  `tax_withheld_amount` decimal(15,2) NOT NULL,
  `total_amount_due` decimal(15,2) NOT NULL,
  `or_status` int(11) NOT NULL DEFAULT 1,
  `status` int(11) NOT NULL DEFAULT 0,
  `print_status` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `or_payment_details`
--

CREATE TABLE `or_payment_details` (
  `id` int(11) NOT NULL,
  `or_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `cost` decimal(15,2) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `discount_percentage` decimal(15,2) DEFAULT NULL,
  `discount_amount` decimal(15,2) NOT NULL,
  `net_amount_before_sales_tax` decimal(15,2) NOT NULL,
  `net_amount` decimal(15,2) NOT NULL,
  `sales_tax_percentage` decimal(15,2) NOT NULL,
  `sales_tax_amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `output_vat_id` int(11) DEFAULT NULL,
  `discount_account_id` int(11) DEFAULT NULL,
  `cogs_account_id` int(11) DEFAULT NULL,
  `income_account_id` int(11) DEFAULT NULL,
  `asset_account_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `other_name`
--

CREATE TABLE `other_name` (
  `id` int(11) NOT NULL,
  `other_name` varchar(255) NOT NULL,
  `other_name_code` varchar(255) NOT NULL,
  `account_number` varchar(255) NOT NULL,
  `other_name_address` varchar(255) NOT NULL,
  `contact_number` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `terms` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `ref_no` varchar(255) DEFAULT NULL,
  `cr_no` varchar(255) DEFAULT NULL,
  `memo` text DEFAULT NULL,
  `summary_amount_due` decimal(15,2) DEFAULT 0.00,
  `summary_applied_amount` decimal(15,2) DEFAULT 0.00,
  `applied_credits_discount` decimal(15,2) DEFAULT 0.00,
  `status` int(11) NOT NULL DEFAULT 0,
  `print_status` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_credit_details`
--

CREATE TABLE `payment_credit_details` (
  `id` int(11) NOT NULL,
  `payment_detail_id` int(11) NOT NULL,
  `credit_amount` decimal(15,2) DEFAULT NULL,
  `credit_no` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_details`
--

CREATE TABLE `payment_details` (
  `id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `amount_applied` decimal(15,2) NOT NULL,
  `discount_amount` decimal(15,2) DEFAULT 0.00,
  `credit_amount` decimal(15,2) DEFAULT 0.00,
  `credit_no` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `discount_account_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_method`
--

CREATE TABLE `payment_method` (
  `id` int(11) NOT NULL,
  `payment_method_name` varchar(50) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_method`
--

INSERT INTO `payment_method` (`id`, `payment_method_name`, `description`) VALUES
(1, 'Cash', 'Cash'),
(2, 'Credit', 'Credit'),
(3, 'Check', 'Check'),
(4, 'Credit Card', 'Credit Card'),
(5, 'Direct Deposit', 'Direct Deposit');

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `transaction_id` int(11) NOT NULL,
  `ref_no` varchar(50) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `item_id` int(11) NOT NULL,
  `cost` decimal(15,2) NOT NULL,
  `total_cost` decimal(15,2) NOT NULL,
  `discount_rate` decimal(5,2) DEFAULT 0.00,
  `purchase_discount_per_item` decimal(10,2) DEFAULT 0.00,
  `purchase_discount_amount` decimal(10,2) DEFAULT 0.00,
  `net_amount` decimal(15,2) NOT NULL,
  `tax_type` varchar(50) DEFAULT NULL,
  `input_vat` decimal(10,2) DEFAULT 0.00,
  `taxable_purchased_amount` decimal(15,2) DEFAULT 0.00,
  `cost_per_unit` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order`
--

CREATE TABLE `purchase_order` (
  `id` int(11) NOT NULL,
  `po_no` varchar(255) DEFAULT NULL,
  `po_account_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `terms` varchar(50) NOT NULL,
  `gross_amount` decimal(15,2) DEFAULT 0.00,
  `discount_amount` decimal(15,2) DEFAULT 0.00,
  `net_amount` decimal(15,2) DEFAULT 0.00,
  `input_vat` decimal(15,2) DEFAULT 0.00,
  `vatable` decimal(15,2) DEFAULT 0.00,
  `zero_rated` decimal(15,2) DEFAULT 0.00,
  `vat_exempt` decimal(15,2) DEFAULT 0.00,
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `memo` text NOT NULL,
  `location` int(11) NOT NULL,
  `po_status` tinyint(1) DEFAULT 0 COMMENT '0 - waiting for delivery\r\n1 = received',
  `status` tinyint(4) DEFAULT 1 COMMENT '0 = draft \r\n1 = posted',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `print_status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_details`
--

CREATE TABLE `purchase_order_details` (
  `id` int(11) NOT NULL,
  `po_id` int(11) DEFAULT NULL,
  `pr_no` varchar(50) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `cost_center_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `cost` decimal(13,4) DEFAULT NULL,
  `amount` decimal(13,4) DEFAULT NULL,
  `discount_percentage` int(11) DEFAULT NULL,
  `discount_type_id` int(11) DEFAULT NULL,
  `discount` decimal(13,4) DEFAULT NULL,
  `net_amount` decimal(13,4) DEFAULT NULL,
  `input_vat_percentage` int(11) DEFAULT NULL,
  `taxable_amount` decimal(13,4) DEFAULT NULL,
  `tax_type_id` int(11) DEFAULT NULL,
  `input_vat` decimal(13,4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `received_qty` int(11) DEFAULT 0,
  `balance_qty` int(11) DEFAULT 0,
  `last_ordered_qty` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_request`
--

CREATE TABLE `purchase_request` (
  `id` int(11) NOT NULL,
  `pr_no` varchar(50) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `required_date` date DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0,
  `print_status` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_request_details`
--

CREATE TABLE `purchase_request_details` (
  `id` int(11) NOT NULL,
  `pr_id` int(11) DEFAULT 0,
  `item_id` int(11) DEFAULT 0,
  `cost_center_id` int(11) DEFAULT 0,
  `quantity` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `ordered_quantity` int(11) DEFAULT 0,
  `balance_quantity` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `receive_items`
--

CREATE TABLE `receive_items` (
  `id` int(11) NOT NULL,
  `receive_account_id` int(11) DEFAULT NULL,
  `receive_no` varchar(50) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `terms` varchar(50) DEFAULT NULL,
  `receive_date` date DEFAULT NULL,
  `receive_due_date` date DEFAULT NULL,
  `memo` text DEFAULT NULL,
  `gross_amount` double(15,2) DEFAULT 0.00,
  `discount_amount` double(15,2) DEFAULT 0.00,
  `net_amount` double(15,2) DEFAULT 0.00,
  `input_vat` double(15,2) DEFAULT 0.00,
  `vatable` double(15,2) DEFAULT 0.00,
  `zero_rated` double(15,2) DEFAULT 0.00,
  `vat_exempt` double(15,2) DEFAULT 0.00,
  `total_amount` double(15,2) DEFAULT 0.00,
  `receive_status` tinyint(1) DEFAULT 0,
  `print_status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `receive_item_details`
--

CREATE TABLE `receive_item_details` (
  `id` int(11) NOT NULL,
  `receive_id` int(11) DEFAULT NULL,
  `po_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `cost_center_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `cost` double(15,2) NOT NULL DEFAULT 0.00,
  `amount` double(15,2) NOT NULL DEFAULT 0.00,
  `discount_percentage` double(5,2) NOT NULL DEFAULT 0.00,
  `discount` double(15,2) NOT NULL DEFAULT 0.00,
  `net_amount_before_input_vat` double(15,2) NOT NULL DEFAULT 0.00,
  `net_amount` double(15,2) NOT NULL DEFAULT 0.00,
  `input_vat_percentage` double(5,2) NOT NULL DEFAULT 0.00,
  `input_vat_amount` double(15,2) NOT NULL DEFAULT 0.00,
  `cost_per_unit` double(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_received_qty` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_invoice`
--

CREATE TABLE `sales_invoice` (
  `id` int(11) NOT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `invoice_account_id` int(11) DEFAULT NULL,
  `invoice_due_date` date DEFAULT NULL,
  `customer_po` varchar(100) DEFAULT NULL,
  `so_no` varchar(100) DEFAULT NULL,
  `rep` varchar(255) DEFAULT NULL,
  `customer_id` int(11) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `location` varchar(50) DEFAULT NULL,
  `terms` varchar(50) DEFAULT NULL,
  `memo` text DEFAULT NULL,
  `gross_amount` decimal(10,2) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `net_amount_due` decimal(10,2) DEFAULT NULL,
  `vat_amount` decimal(10,2) DEFAULT NULL,
  `vatable_amount` decimal(10,2) DEFAULT NULL,
  `zero_rated_amount` decimal(10,2) DEFAULT NULL,
  `vat_exempt_amount` decimal(10,2) DEFAULT NULL,
  `tax_withheld_percentage` int(11) DEFAULT NULL,
  `tax_withheld_amount` decimal(10,2) DEFAULT NULL,
  `total_amount_due` decimal(10,2) DEFAULT NULL,
  `invoice_status` int(11) NOT NULL DEFAULT 0 COMMENT '0 = unpaid\r\n1 = paid',
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '0 = draft\r\n1 = posted\r\n2 = modified\r\n3 = deleted',
  `balance_due` double(15,2) NOT NULL DEFAULT 0.00,
  `total_paid` double(15,2) NOT NULL DEFAULT 0.00,
  `print_status` int(11) NOT NULL DEFAULT 0 COMMENT '0 = original copy\r\n1 = reprinted',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_invoice`
--

INSERT INTO `sales_invoice` (`id`, `invoice_number`, `invoice_date`, `invoice_account_id`, `invoice_due_date`, `customer_po`, `so_no`, `rep`, `customer_id`, `payment_method`, `location`, `terms`, `memo`, `gross_amount`, `discount_amount`, `net_amount_due`, `vat_amount`, `vatable_amount`, `zero_rated_amount`, `vat_exempt_amount`, `tax_withheld_percentage`, `tax_withheld_amount`, `total_amount_due`, `invoice_status`, `status`, `balance_due`, `total_paid`, `print_status`, `created_at`, `updated_at`) VALUES
(1, 'SI000000001', '2024-09-25', 10, '2024-10-02', '', '124', '124', 6, 'Cash', '4', 'NET 7', '124', 15376.00, 153.76, 15222.24, 1630.95, 13591.29, 0.00, 0.00, 2, 135.91, 15086.33, 0, 1, 15086.33, 0.00, 2, '2024-09-24 20:36:12', '2024-09-24 22:44:04'),
(2, 'SI000000002', '2024-09-25', 10, '2024-10-02', '124', '124', '124', 5, 'Cash', '4', 'NET 7', '124', 15376.00, 153.76, 15222.24, 1630.95, 13591.29, 0.00, 0.00, 2, 135.91, 15086.33, 0, 1, 15086.33, 0.00, 1, '2024-09-24 20:37:01', '2024-09-24 20:37:11'),
(3, 'SI000000003', '2024-09-25', 10, '2024-10-10', '', '124', '124', 5, 'Credit', '4', 'NET 15', '124', 50000.00, 0.00, 50000.00, 5357.14, 44642.86, 0.00, 0.00, 2, 446.43, 49553.57, 0, 1, 49553.57, 0.00, 0, '2024-09-24 20:58:58', '2024-09-24 20:58:58'),
(4, 'SI000000004', '2024-09-28', 10, '2024-10-05', '124124', '124124', '124124', 6, 'Cash', '4', 'NET 7', '124', 125000.00, 0.00, 125000.00, 13392.86, 111607.14, 0.00, 0.00, 4, 0.00, 125000.00, 0, 1, 125000.00, 0.00, 2, '2024-09-27 19:18:24', '2024-09-29 20:01:42'),
(5, NULL, '2024-09-30', 10, '2024-10-07', 'POfdhfiud', 'SOdjfnkjsd', 'REPkjdsh', 6, 'Credit', '5', 'NET 7', 'MEMO', 100000.00, 1000.00, 99000.00, 0.00, 0.00, 99000.00, 0.00, 4, 0.00, 99000.00, 4, 1, 0.00, 0.00, 0, '2024-09-29 20:01:17', '2024-09-29 20:01:17'),
(6, 'SI000000005', '2024-10-17', 3, '2024-10-24', '124', '124', '124', 6, 'Credit', '4', 'NET 7', '124', 15376.00, 307.52, 15068.48, 8341.48, 6727.00, 0.00, 0.00, 9, 8341.48, 6727.00, 0, 1, 6727.00, 0.00, 1, '2024-10-17 15:22:54', '2024-10-17 15:22:56'),
(7, 'SI000000006', '2024-10-17', 3, '2024-10-24', '', '124', '124', 6, 'Cash', '4', 'NET 7', '124', 250000.00, 5000.00, 245000.00, 135625.00, 109375.00, 0.00, 0.00, 9, 135625.00, 109375.00, 0, 1, 109375.00, 0.00, 0, '2024-10-17 15:23:48', '2024-10-17 15:23:48'),
(8, 'SI000000007', '2024-10-18', 3, '2024-10-25', '', '124', '124', 5, 'Cash', '4', 'NET 7', '124', 4250000.00, 0.00, 4250000.00, 2352678.57, 1897321.43, 0.00, 0.00, 9, 2352678.57, 1897321.43, 0, 1, 1897321.43, 0.00, 0, '2024-10-17 16:04:03', '2024-10-17 16:04:03');

-- --------------------------------------------------------

--
-- Table structure for table `sales_invoice_details`
--

CREATE TABLE `sales_invoice_details` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `discount_percentage` decimal(5,2) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `net_amount_before_sales_tax` decimal(10,2) DEFAULT NULL,
  `net_amount` decimal(10,2) DEFAULT NULL,
  `sales_tax_percentage` decimal(5,2) DEFAULT NULL,
  `sales_tax_amount` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `output_vat_id` int(11) DEFAULT NULL,
  `discount_account_id` int(11) DEFAULT NULL,
  `cogs_account_id` int(11) DEFAULT NULL,
  `income_account_id` int(11) DEFAULT NULL,
  `asset_account_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_invoice_details`
--

INSERT INTO `sales_invoice_details` (`id`, `invoice_id`, `item_id`, `quantity`, `cost`, `amount`, `discount_percentage`, `discount_amount`, `net_amount_before_sales_tax`, `net_amount`, `sales_tax_percentage`, `sales_tax_amount`, `created_at`, `updated_at`, `output_vat_id`, `discount_account_id`, `cogs_account_id`, `income_account_id`, `asset_account_id`) VALUES
(1, 8, 3953, 500, 8500.00, 4250000.00, 0.00, 0.00, 4250000.00, 1897321.43, 124.00, 2352678.57, '2024-10-17 16:04:03', '2024-10-17 16:04:03', 3, 6039, 1, 11, 10);

-- --------------------------------------------------------

--
-- Table structure for table `sales_return`
--

CREATE TABLE `sales_return` (
  `id` int(11) NOT NULL,
  `sales_return_number` varchar(50) DEFAULT NULL,
  `sales_return_date` date DEFAULT NULL,
  `sales_return_account_id` int(11) DEFAULT NULL,
  `sales_return_due_date` date DEFAULT NULL,
  `customer_po` varchar(255) DEFAULT NULL,
  `so_no` varchar(255) DEFAULT NULL,
  `rep` varchar(255) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `location` varchar(50) DEFAULT NULL,
  `terms` varchar(50) DEFAULT NULL,
  `memo` text DEFAULT NULL,
  `gross_amount` double(10,2) DEFAULT NULL,
  `discount_amount` double(10,2) DEFAULT NULL,
  `net_amount_due` double(10,2) DEFAULT NULL,
  `vat_amount` double(10,2) DEFAULT NULL,
  `vatable_amount` double(10,2) DEFAULT NULL,
  `zero_rated_amount` double(10,2) DEFAULT NULL,
  `vat_exempt_amount` double(10,2) DEFAULT NULL,
  `tax_withheld_percentage` double(10,2) DEFAULT NULL,
  `tax_withheld_amount` double(10,2) DEFAULT NULL,
  `total_amount_due` double(10,2) DEFAULT NULL,
  `sales_return_status` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `print_status` tinyint(1) NOT NULL DEFAULT 0,
  `balance_due` double(10,2) DEFAULT NULL,
  `total_paid` double(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_return_details`
--

CREATE TABLE `sales_return_details` (
  `id` int(11) NOT NULL,
  `sales_return_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `cost` double(10,2) DEFAULT NULL,
  `amount` double(10,2) DEFAULT NULL,
  `discount_percentage` double(5,2) DEFAULT NULL,
  `discount_amount` double(10,2) DEFAULT NULL,
  `net_amount_before_sales_tax` double(10,2) DEFAULT NULL,
  `net_amount` double(10,2) DEFAULT NULL,
  `sales_tax_percentage` double(5,2) DEFAULT NULL,
  `sales_tax_amount` double(10,2) DEFAULT NULL,
  `discount_account_id` int(11) DEFAULT NULL,
  `output_vat_id` int(11) DEFAULT NULL,
  `cogs_account_id` int(11) DEFAULT NULL,
  `income_account_id` int(11) DEFAULT NULL,
  `asset_account_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_tax`
--

CREATE TABLE `sales_tax` (
  `id` int(11) NOT NULL,
  `sales_tax_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sales_tax_rate` float NOT NULL,
  `sales_tax_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sales_tax_account_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales_tax`
--

INSERT INTO `sales_tax` (`id`, `sales_tax_name`, `sales_tax_rate`, `sales_tax_description`, `sales_tax_account_id`, `created_at`) VALUES
(1, '12% Output VAT', 12, '12% Output VAT', 4034, '2024-09-23 17:36:33'),
(2, 'Z', 0, 'Zero-rated Tax', 4034, '2024-09-29 19:59:55'),
(3, '124', 124, '124', 3, '2024-10-17 15:22:34');

-- --------------------------------------------------------

--
-- Table structure for table `terms`
--

CREATE TABLE `terms` (
  `id` int(11) NOT NULL,
  `term_name` varchar(50) NOT NULL,
  `term_days_due` int(11) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `terms`
--

INSERT INTO `terms` (`id`, `term_name`, `term_days_due`, `description`) VALUES
(5, 'NET 7', 7, 'NET 7'),
(6, 'NET 15', 15, 'NET 15'),
(7, 'NET 30', 30, 'NET 30'),
(9, 'Due on Receipt', 0, 'Due on Receipt');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_entries`
--

CREATE TABLE `transaction_entries` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `transaction_type` varchar(50) NOT NULL,
  `transaction_date` date DEFAULT NULL,
  `ref_no` varchar(255) NOT NULL,
  `location` int(11) DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `item` varchar(255) DEFAULT NULL,
  `qty_sold` int(11) DEFAULT NULL,
  `account_id` int(11) NOT NULL,
  `debit` decimal(15,2) DEFAULT 0.00,
  `credit` decimal(15,2) DEFAULT 0.00,
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_entries`
--

INSERT INTO `transaction_entries` (`id`, `transaction_id`, `transaction_type`, `transaction_date`, `ref_no`, `location`, `name`, `item`, `qty_sold`, `account_id`, `debit`, `credit`, `balance`, `created_at`) VALUES
(1, 1, 'General Journal', '2024-10-18', 'GJ000000001', 0, '3M MARBLE ENTERPRISES', NULL, NULL, 1, 100.00, 0.00, 100.00, '2024-10-18 00:02:03'),
(2, 1, 'General Journal', '2024-10-18', 'GJ000000001', 0, '3M MARBLE ENTERPRISES', NULL, NULL, 3, 0.00, 100.00, -100.00, '2024-10-18 00:02:03'),
(3, 8, 'Invoice', '2024-10-18', 'SI000000007', 0, 'CHULIANTE MARKETING CORPORATION', NULL, NULL, 3, 4250000.00, 0.00, 4250000.00, '2024-10-18 00:04:03'),
(4, 8, 'Invoice', '2024-10-18', 'SI000000007', 0, 'CHULIANTE MARKETING CORPORATION', NULL, NULL, 1, 2352678.57, 0.00, 2352678.57, '2024-10-18 00:04:03'),
(5, 8, 'Invoice', '2024-10-18', 'SI000000007', 0, 'CHULIANTE MARKETING CORPORATION', NULL, NULL, 3, 0.00, 2352678.57, -2352678.57, '2024-10-18 00:04:03'),
(6, 8, 'Invoice', '2024-10-18', 'SI000000007', 0, 'CHULIANTE MARKETING CORPORATION', 'Monster Energy Drink', 500, 6039, 0.00, 0.00, 0.00, '2024-10-18 00:04:03'),
(7, 8, 'Invoice', '2024-10-18', 'SI000000007', 0, 'CHULIANTE MARKETING CORPORATION', 'Monster Energy Drink', 500, 11, 0.00, 1897321.43, -1897321.43, '2024-10-18 00:04:03'),
(8, 8, 'Invoice', '2024-10-18', 'SI000000007', 0, 'CHULIANTE MARKETING CORPORATION', 'Monster Energy Drink', 500, 1, 4250000.00, 0.00, 4250000.00, '2024-10-18 00:04:03'),
(9, 8, 'Invoice', '2024-10-18', 'SI000000007', 0, 'CHULIANTE MARKETING CORPORATION', 'Monster Energy Drink', 500, 10, 0.00, 4250000.00, -4250000.00, '2024-10-18 00:04:03'),
(10, 8, 'Invoice', '2024-10-18', 'SI000000007', 0, 'CHULIANTE MARKETING CORPORATION', NULL, NULL, 3, 0.00, 0.00, 0.00, '2024-10-18 00:04:03'),
(11, 8, 'Invoice', '2024-10-18', 'SI000000007', 0, 'CHULIANTE MARKETING CORPORATION', NULL, NULL, 3, 0.00, 2352678.57, -2352678.57, '2024-10-18 00:04:03');

-- --------------------------------------------------------

--
-- Table structure for table `uom`
--

CREATE TABLE `uom` (
  `id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uom`
--

INSERT INTO `uom` (`id`, `name`) VALUES
(7, 'kg/s'),
(11, 'pack'),
(12, 'cup/s'),
(13, 'pc/s'),
(14, 'sack/s'),
(18, 'gram/s'),
(19, 'lot/s'),
(23, 'mt/s');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `username` varchar(60) NOT NULL,
  `role_id` int(11) NOT NULL,
  `password` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `role_id`, `password`) VALUES
(1, 'Digimax', 'superadmin', 1, 'IloveDigimax3407'),
(36, 'Admin', 'admin', 0, 'D!gimax321@'),
(41, 'Kimberly Arellano', 'Kim', 1, 'IloveDigimax3407'),
(42, 'Theresa Cabigting', 'Theresa', 1, '3407'),
(43, 'Janine Rallos', 'Janine', 1, '3407'),
(44, 'Ma. Annil Villanueva', 'Annil', 17, '123'),
(46, 'Rafael Villanueva', 'Raffy', 20, '123'),
(47, 'Sally Jasme', 'Sally', 17, '321'),
(49, 'Marjorie Tan', 'Marj', 17, '456'),
(50, 'Paul Cutob', 'Paul', 21, '456'),
(51, 'test123', 'test123', 22, 'test123'),
(53, 'Payroll', 'test_payroll1', 24, 'test_payroll1');

-- --------------------------------------------------------

--
-- Table structure for table `user_module_access`
--

CREATE TABLE `user_module_access` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `module` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_module_access`
--

INSERT INTO `user_module_access` (`id`, `user_id`, `module`) VALUES
(71, 31, 'dashboard'),
(72, 31, 'chart_of_accounts_list'),
(73, 31, 'general_journal'),
(74, 31, 'transaction_entries'),
(75, 31, 'trial_balance'),
(76, 31, 'audit_trail'),
(77, 32, 'dashboard'),
(78, 32, 'invoice'),
(79, 32, 'reports'),
(80, 33, 'dashboard'),
(81, 33, 'chart_of_accounts_list'),
(82, 33, 'general_journal'),
(83, 33, 'transaction_entries'),
(84, 33, 'trial_balance'),
(85, 33, 'audit_trail'),
(86, 34, 'dashboard'),
(87, 34, 'purchase_order'),
(88, 34, 'bank_transfer'),
(89, 34, 'vendor_list'),
(90, 34, 'category'),
(91, 34, 'payment_method'),
(92, 35, 'dashboard'),
(93, 35, 'purchasing_purchase_request'),
(94, 35, 'purchasing_purchase_order'),
(95, 36, 'dashboard'),
(96, 36, 'reports'),
(97, 36, 'invoice'),
(98, 36, 'receive_payment'),
(99, 36, 'credit_memo'),
(100, 36, 'purchase_request'),
(101, 36, 'purchase_order'),
(102, 36, 'receive_items'),
(103, 36, 'accounts_payable_voucher'),
(104, 36, 'purchase_return'),
(105, 36, 'pay_bills'),
(106, 36, 'write_check'),
(107, 36, 'make_deposit'),
(108, 36, 'bank_transfer'),
(109, 36, 'chart_of_accounts_list'),
(110, 36, 'general_journal'),
(111, 36, 'transaction_entries'),
(112, 36, 'trial_balance'),
(113, 36, 'audit_trail'),
(114, 36, 'chart_of_accounts'),
(115, 36, 'item_list'),
(116, 36, 'customer'),
(117, 36, 'vendor_list'),
(118, 36, 'employee_list'),
(119, 36, 'other_name'),
(120, 36, 'location'),
(121, 36, 'uom'),
(122, 36, 'cost_center'),
(123, 36, 'category'),
(124, 36, 'terms'),
(125, 36, 'payment_method'),
(126, 36, 'discount'),
(127, 36, 'input_vat'),
(128, 36, 'sales_tax'),
(129, 36, 'wtax'),
(130, 36, 'purchasing_purchase_request'),
(131, 36, 'purchasing_purchase_order'),
(132, 36, 'warehouse_receive_items'),
(133, 36, 'warehouse_purchase_request'),
(134, 36, 'material_issuance'),
(135, 37, 'dashboard'),
(136, 37, 'accounts_payable_voucher');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `role_name`) VALUES
(1, 'SUPERADMIN'),
(17, 'ADMIN'),
(18, 'RECEIVABLES'),
(19, 'PURCHASING'),
(20, 'PURCHASING MANAGER'),
(21, 'PAYABLES'),
(22, 'SAMPLE'),
(24, 'PAYROLL MASTER');

-- --------------------------------------------------------

--
-- Table structure for table `user_role_module_access`
--

CREATE TABLE `user_role_module_access` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `module` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_role_module_access`
--

INSERT INTO `user_role_module_access` (`id`, `role_id`, `module`) VALUES
(21, 17, 'dashboard'),
(22, 17, 'reports'),
(23, 17, 'invoice'),
(24, 17, 'receive_payment'),
(25, 17, 'credit_memo'),
(26, 17, 'purchase_request'),
(27, 17, 'purchase_order'),
(28, 17, 'receive_items'),
(29, 17, 'accounts_payable_voucher'),
(30, 17, 'purchase_return'),
(31, 17, 'pay_bills'),
(32, 17, 'write_check'),
(33, 17, 'make_deposit'),
(34, 17, 'bank_transfer'),
(35, 17, 'chart_of_accounts_list'),
(36, 17, 'general_journal'),
(37, 17, 'transaction_entries'),
(38, 17, 'trial_balance'),
(39, 17, 'audit_trail'),
(40, 17, 'chart_of_accounts'),
(41, 17, 'item_list'),
(42, 17, 'customer'),
(43, 17, 'vendor_list'),
(44, 17, 'employee_list'),
(45, 17, 'other_name'),
(46, 17, 'location'),
(47, 17, 'uom'),
(48, 17, 'cost_center'),
(49, 17, 'category'),
(50, 17, 'terms'),
(51, 17, 'payment_method'),
(52, 17, 'discount'),
(53, 17, 'input_vat'),
(54, 17, 'sales_tax'),
(55, 17, 'wtax'),
(56, 17, 'purchasing_purchase_request'),
(57, 17, 'purchasing_purchase_order'),
(58, 17, 'warehouse_receive_items'),
(59, 17, 'warehouse_purchase_request'),
(60, 17, 'material_issuance'),
(61, 18, 'dashboard'),
(62, 18, 'invoice'),
(63, 18, 'receive_payment'),
(64, 18, 'credit_memo'),
(65, 18, 'chart_of_accounts'),
(66, 18, 'item_list'),
(67, 18, 'customer'),
(68, 18, 'employee_list'),
(69, 18, 'other_name'),
(70, 18, 'location'),
(71, 18, 'uom'),
(72, 18, 'cost_center'),
(73, 18, 'category'),
(74, 18, 'terms'),
(75, 18, 'payment_method'),
(76, 18, 'discount'),
(77, 18, 'input_vat'),
(78, 18, 'sales_tax'),
(79, 18, 'wtax'),
(80, 19, 'dashboard'),
(81, 19, 'item_list'),
(82, 19, 'vendor_list'),
(83, 19, 'other_name'),
(84, 19, 'location'),
(85, 19, 'uom'),
(86, 19, 'cost_center'),
(87, 19, 'category'),
(88, 19, 'terms'),
(89, 19, 'payment_method'),
(90, 19, 'discount'),
(91, 19, 'input_vat'),
(92, 19, 'sales_tax'),
(93, 19, 'wtax'),
(94, 19, 'purchasing_purchase_request'),
(95, 19, 'purchasing_purchase_order'),
(96, 19, 'inventory_list'),
(97, 19, 'warehouse_receive_items'),
(98, 19, 'warehouse_purchase_request'),
(99, 19, 'material_issuance'),
(100, 19, 'inventory_valuation_detail'),
(101, 19, 'reports'),
(102, 20, 'dashboard'),
(103, 20, 'purchase_request'),
(104, 20, 'purchase_order'),
(105, 20, 'receive_items'),
(106, 20, 'accounts_payable_voucher'),
(107, 20, 'purchase_return'),
(108, 20, 'pay_bills'),
(109, 20, 'chart_of_accounts_list'),
(110, 20, 'general_journal'),
(111, 20, 'transaction_entries'),
(112, 20, 'trial_balance'),
(113, 20, 'audit_trail'),
(114, 20, 'chart_of_accounts'),
(115, 20, 'item_list'),
(116, 20, 'customer'),
(117, 20, 'vendor_list'),
(118, 20, 'employee_list'),
(119, 20, 'other_name'),
(120, 20, 'fs_classification'),
(121, 20, 'fs_notes_classification'),
(122, 20, 'location'),
(123, 20, 'uom'),
(124, 20, 'cost_center'),
(125, 20, 'category'),
(126, 20, 'terms'),
(127, 20, 'payment_method'),
(128, 20, 'discount'),
(129, 20, 'input_vat'),
(130, 20, 'sales_tax'),
(131, 20, 'wtax'),
(132, 20, 'inventory_list'),
(133, 20, 'warehouse_receive_items'),
(134, 20, 'warehouse_purchase_request'),
(135, 20, 'material_issuance'),
(136, 20, 'warehouse_receive_items'),
(137, 20, 'inventory_valuation_detail'),
(138, 20, 'material_issuance'),
(139, 20, 'reports'),
(140, 21, 'dashboard'),
(141, 21, 'purchase_request'),
(142, 21, 'purchase_order'),
(143, 21, 'receive_items'),
(144, 21, 'accounts_payable_voucher'),
(145, 21, 'purchase_return'),
(146, 21, 'pay_bills'),
(147, 21, 'write_check'),
(148, 21, 'make_deposit'),
(149, 21, 'bank_transfer'),
(150, 21, 'chart_of_accounts_list'),
(151, 21, 'general_journal'),
(152, 21, 'transaction_entries'),
(153, 21, 'trial_balance'),
(154, 21, 'audit_trail'),
(155, 21, 'chart_of_accounts'),
(156, 21, 'item_list'),
(157, 21, 'customer'),
(158, 21, 'vendor_list'),
(159, 21, 'employee_list'),
(160, 21, 'other_name'),
(161, 21, 'fs_classification'),
(162, 21, 'fs_notes_classification'),
(163, 21, 'location'),
(164, 21, 'uom'),
(165, 21, 'cost_center'),
(166, 21, 'category'),
(167, 21, 'terms'),
(168, 21, 'payment_method'),
(169, 21, 'discount'),
(170, 21, 'input_vat'),
(171, 21, 'sales_tax'),
(172, 21, 'wtax'),
(173, 21, 'reports'),
(174, 22, 'dashboard'),
(175, 22, 'invoice'),
(176, 22, 'receive_payment'),
(177, 22, 'credit_memo'),
(178, 22, 'sales_return'),
(191, 24, 'dashboard'),
(192, 24, 'employee_list'),
(193, 24, 'department'),
(194, 24, 'position'),
(195, 24, 'shift_schedule'),
(196, 24, 'deduction'),
(197, 24, 'attendace_list'),
(198, 24, 'daily_time_record'),
(199, 24, 'leave_list'),
(200, 24, 'overtime_list'),
(201, 24, 'loan_list'),
(202, 24, 'payroll'),
(203, 24, 'generate_payroll');

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` int(11) NOT NULL,
  `vendor_name` varchar(255) DEFAULT NULL,
  `vendor_code` varchar(50) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `vendor_address` varchar(255) DEFAULT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `terms` varchar(50) DEFAULT NULL,
  `tin` varchar(50) DEFAULT NULL,
  `tax_type` varchar(11) DEFAULT NULL,
  `tel_no` varchar(50) DEFAULT NULL,
  `fax_no` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `item_type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `vendor_name`, `vendor_code`, `account_number`, `vendor_address`, `contact_number`, `email`, `terms`, `tin`, `tax_type`, `tel_no`, `fax_no`, `notes`, `item_type`) VALUES
(1, '3M MARBLE ENTERPRISES', '400001', NULL, 'REAL STREET ORMOC CITY', NULL, NULL, NULL, '138-417-416-000', 'N/A', NULL, NULL, NULL, NULL),
(2, 'ABOMAR CORPORATION', '400002', NULL, 'CEBU CITY', NULL, NULL, NULL, '000-309-800-000', 'N/A', NULL, NULL, NULL, NULL),
(3, 'ADVANCE POWER PRODUCTS, INC.', '400003', NULL, 'MANDAUE CITY', NULL, NULL, NULL, '000-104-129-000', 'N/A', NULL, NULL, NULL, NULL),
(4, 'ALAJAS MACHINE SHOP', '400004', NULL, 'KANGLEON ST., ORMOC CITY', NULL, NULL, NULL, '101-719-876-000', '12%', NULL, 'T-560-6316', NULL, NULL),
(5, 'ALLIED THREAD CO., INC.', '400005', NULL, '106-A KANGLEON STREET ORMOC CITY', NULL, NULL, NULL, '000-281-465-000', 'N/A', NULL, NULL, NULL, NULL),
(6, 'ALLOY INDUSTRIAL SUPPLY CORPORATION', '400006', NULL, NULL, NULL, NULL, NULL, '000-050-438-000', 'N/A', NULL, NULL, NULL, NULL),
(7, 'AMERICAN PACKING INDUSTRIES CORPORATION', '400007', NULL, 'CEBU CITY', NULL, NULL, NULL, '000-409-984-000', 'N/A', NULL, NULL, NULL, NULL),
(8, 'B. BENEDICTO & SONS CO., INC.', '400008', NULL, 'PLARIDEL STREET CEBU CITY', NULL, NULL, NULL, '000-310-804-0000', 'N/A', NULL, NULL, NULL, NULL),
(9, 'BES PACIFIC HARDWARE & INDUSTRIAL SUPPLY, INC.', '400009', NULL, 'D. JAKOSALEM COR. J. LUNA STS. CEBU CITY', NULL, NULL, NULL, '201-599-731-0000', 'N/A', NULL, NULL, NULL, NULL),
(10, 'BROADWAY INDUSTRIAL SUPPLY, INC.', '400010', NULL, 'LUDEL BLDG., 2461 URDANETA ST. GUADALUPE NUEVO, MAKATI CITY', NULL, NULL, NULL, '000-154-067-0000', '12%', NULL, NULL, NULL, NULL),
(11, 'BROD PRINTING PRESS & ENTERPRISES', '400011', NULL, 'RIZAL STREET ORMOC CITY', NULL, NULL, NULL, '180-491-870-0000', 'N/A', NULL, NULL, NULL, NULL),
(12, 'CATHAY HARDWARE, INC.', '400012', NULL, '84 PLARIDEL STREET CEBU CITY', NULL, NULL, NULL, '000-310-343-0000', 'N/A', NULL, NULL, NULL, NULL),
(13, 'CEBU FAR EASTERN DRUG, INC.', '400013', NULL, 'CEBU CITY', NULL, NULL, NULL, '000-310-302-0000', 'N/A', NULL, '(032) 416-8582', 'Marilou', NULL),
(14, 'CEBU FORTUNE MOTOR PARTS CENTER', '400014', NULL, 'SUBANGDAKU MANDAUE CITY', NULL, NULL, NULL, '000-256-684-0000', 'N/A', NULL, NULL, NULL, NULL),
(15, 'COOL SLOT SPECIALIST', '400015', NULL, '588 WIRELESS, SUBANGDAKU MANDAUE CITY', NULL, NULL, NULL, '119-487-039', 'N/A', NULL, NULL, NULL, NULL),
(16, 'CRO-MAGNON CORPORATION', '400016', NULL, '120 SANCIANGKO STREET CEBU CITY', NULL, NULL, NULL, '004-823-230', 'N/A', NULL, NULL, NULL, NULL),
(17, 'CRO-MAGNON CORPORATION', '400017', NULL, 'Guibilondo cor., Juan Luna Sts., Mabolo Cebu City', NULL, NULL, NULL, '004-823-230-0003', '12%', NULL, NULL, NULL, NULL),
(18, 'CRV RESOURCES TRADING &/OR VICTOR DIEZ', '400018', NULL, 'KANANGA LEYTE', NULL, NULL, NULL, '113-617-533', 'N/A', NULL, NULL, NULL, NULL),
(19, 'GILLESANIA PRINTING PRESS', '400019', NULL, 'LOPEZ JAENA STREET ORMOC CITY', NULL, NULL, NULL, '113-494-331', 'N/A', NULL, NULL, NULL, NULL),
(20, 'DEGALEN CORPORATION', '400020', NULL, 'SUBANGDAKU MANDAUE CITY', NULL, NULL, NULL, '000-067-683', '12%', NULL, NULL, NULL, NULL),
(21, 'DYNAMIC CASTINGS', '400021', NULL, 'GERARDO OUANO STREET MANDAUE CITY', NULL, NULL, NULL, '100-071-364', '12%', NULL, NULL, NULL, NULL),
(22, 'EBR MARKETING CORPORATION', '400022', NULL, 'REAL STREET ORMOC CITY', NULL, NULL, NULL, '004-305-869-0000', '12%', '255-4243;561-8346', NULL, NULL, NULL),
(23, 'ERIK\'S RUBBERTECH AND INDUSTRIAL SERVICES, INC.', '400023', NULL, '1-69 RAMONA VILLAGE DUMLOG TALISAY CEBU CITY', NULL, NULL, NULL, '424-908-487-000', '12%', '232-2174', '231-9651', 'ACCT#654-7-654-03209-9 (MBTC/CEBU BUSINESS PARK BRANCH)', NULL),
(24, 'ESE AUTO PARTS ENTERPRISES', '400024', NULL, '474 RIZAL STREET ORMOC CITY', NULL, NULL, NULL, '113-492-978-000', '12%', '255-4573;561-9754', NULL, 'ATTY. EVERGISTO S. ESCALON-PROP.', NULL),
(25, 'EUROMERICA TRADE PHILIPPINES, INC.', '400025', NULL, 'CEBU CITY', NULL, NULL, NULL, '000-417-136', 'N/A', NULL, NULL, NULL, NULL),
(26, 'FASTPAK INTERNATIONAL CORPORATION', '400026', NULL, 'RIZAL STREET ORMOC CITY', NULL, NULL, NULL, '001-947-880', '12%', NULL, NULL, NULL, NULL),
(27, 'FERVID INTERNATIONAL PRODUCTS, INC.', '400027', NULL, 'IMUS, CAVITE', NULL, NULL, NULL, '001-860-277', 'N/A', NULL, NULL, NULL, NULL),
(28, 'FESTO, INC.', '400028', NULL, 'CEBU CITY', NULL, NULL, NULL, '000-118-933', '12%', NULL, NULL, NULL, NULL),
(29, 'FRASER VENTURES CORPORATION', '400029', NULL, 'REAL STREET ORMOC CITY', NULL, NULL, NULL, '003-293-984', 'N/A', NULL, NULL, NULL, NULL),
(30, 'G. LETING SONS, INC.', '400030', NULL, 'TACLOBAN CITY', NULL, NULL, NULL, '000-272-319', 'N/A', NULL, NULL, NULL, NULL),
(31, 'GALLEON TRADING (PTE.)', '400031', NULL, 'CEBU CITY', NULL, NULL, NULL, '103-786-582', 'N/A', NULL, NULL, NULL, NULL),
(32, 'GIANTECH INDUSTRIES, INC.', '400032', NULL, 'MANDAUE CITY', NULL, NULL, NULL, '004-473-042', 'N/A', NULL, NULL, NULL, NULL),
(33, 'GIBROSEN GINETE BROS. ENTERPRISES', '400033', NULL, 'BACOLOD CITY', NULL, NULL, NULL, '104-073-187', 'N/A', NULL, NULL, NULL, NULL),
(34, 'GOLDWATER MART, INC.', '400034', NULL, 'REAL STREET ORMOC CITY', NULL, NULL, NULL, '002-198-492', 'N/A', NULL, NULL, NULL, NULL),
(35, 'GOODWILL AUTO SUPPLY & HARDWARE, INC.', '400035', NULL, 'AVILES COR. RIZAL STS. ORMOC CITY', NULL, NULL, NULL, '000-226-486', 'N/A', NULL, NULL, NULL, NULL),
(36, 'HI-SPEED INDUSTRIAL CORPORATION', '400036', NULL, 'SUBANGDAKU MANDAUE CITY', NULL, NULL, NULL, '000-068-643', '12%', '346-0543', NULL, NULL, NULL),
(37, 'HO TONG HARDWARE, INC.', '400037', NULL, 'F. GONZALES COR. M.C. BRIONES STS. CEBU CITY', NULL, NULL, NULL, '000-309-818', 'N/A', NULL, NULL, NULL, NULL),
(38, 'IPIL PORT SERVICES, INC.', '400038', NULL, 'IPIL ORMOC CITY', NULL, NULL, NULL, '000-198-276', 'N/A', NULL, NULL, NULL, NULL),
(39, 'JARDINE NELL', '400039', NULL, 'MANDAUE CITY', NULL, NULL, NULL, '000-126-853', 'N/A', NULL, NULL, NULL, NULL),
(40, 'JETRYLL CORPORATION', '400040', NULL, 'SUBANGDAKU MANDAUE CITY', NULL, NULL, NULL, '004-281-834', 'N/A', NULL, NULL, NULL, NULL),
(41, 'JRS BUSINESS CORPORATION', '400041', NULL, 'F. GONZALES COR. M.C. BRIONES ORMOC CITY', NULL, NULL, NULL, '000-056-694', '12%', NULL, NULL, NULL, NULL),
(42, 'KAPPER PHILIPPINES, INC.', '400042', NULL, 'J. BORROMEO BLDG., F. RAMOS ST CEBU CITY', NULL, NULL, NULL, '000-070-268', 'N/A', NULL, NULL, NULL, NULL),
(43, 'LEYTE V ELECTRIC COOPERATIVE, INC.', '400043', NULL, 'SAN PABLO ORMOC CITY', NULL, NULL, NULL, '001-383-331-000', 'N/A', NULL, NULL, NULL, NULL),
(44, 'LIBSALES INDUSTRIAL PARTS & GEN. MDSE.', '400044', NULL, '123 RIZAL ST. TRIBUNAL MANDAUE CITY', NULL, NULL, NULL, '100-072-768', 'N/A', NULL, NULL, NULL, NULL),
(45, 'LUZ PHARMACY', '400045', NULL, 'AVILES ST. ORMOC CITY', NULL, NULL, NULL, '005-355-070-0009', '12%', NULL, NULL, NULL, NULL),
(46, 'MAGLASANG TRADING & PRINTING PRESS', '400046', NULL, 'AVILES ST. ORMOC CITY', NULL, NULL, NULL, '101-720-586', 'N/A', NULL, NULL, NULL, NULL),
(47, 'MANGCAO BOOKSTORE', '400047', NULL, 'BONIFACIO ST. ORMOC CITY', NULL, NULL, NULL, '110-948-238', 'N/A', NULL, NULL, NULL, NULL),
(48, 'MARKET DEVELOPERS, INC.', '400048', NULL, 'CEBU CITY', NULL, NULL, NULL, '000-128-337', 'N/A', NULL, NULL, NULL, NULL),
(49, 'MAXIMA EQUIPMENT CORPORATION', '400049', NULL, 'SUBANGDAKU MANDAUE CITY', NULL, NULL, NULL, '000-398-547', 'N/A', NULL, NULL, NULL, NULL),
(50, 'MAYFLOWER EMPORIUM', '400050', NULL, 'ORMOC CITY', NULL, NULL, NULL, '108-934-752', 'N/A', NULL, NULL, NULL, NULL),
(51, 'MBL FACTORS & TRADERS, INC.', '400051', NULL, 'BACOLOD CITY', NULL, NULL, NULL, '000-258-138', 'N/A', NULL, NULL, NULL, NULL),
(52, 'MEGACAST ASIA', '400052', NULL, 'MANDAUE CITY', NULL, NULL, NULL, '104-096-220', 'N/A', NULL, NULL, NULL, NULL),
(53, 'MEGALINK TRADING CORPORATION', '400053', NULL, 'CEBU CITY', NULL, NULL, NULL, '004-265-516', 'N/A', NULL, NULL, NULL, NULL),
(54, 'MICROWAY COMPUTER SERVICES', '400054', NULL, 'ORMOC CITY', NULL, NULL, NULL, '131-507-769', 'N/A', NULL, NULL, NULL, NULL),
(55, 'MM PRINTING PRESS', '400055', NULL, 'ORMOC CITY', NULL, NULL, NULL, '101-721-072', 'N/A', NULL, NULL, NULL, NULL),
(56, 'MMC ENGINEERING WORKS DEALER', '400056', NULL, 'BACOLOD CITY', NULL, NULL, NULL, '165-212-326-001', '12%', NULL, NULL, NULL, NULL),
(57, 'MONARK EQUIPMENT CORPORATION', '400057', NULL, 'CEBU CITY', NULL, NULL, NULL, '000-385-447', 'N/A', NULL, NULL, NULL, NULL),
(58, 'NALCO CHEMICALS COMPANY (PHILS.), INC.', '400058', NULL, 'MAKATI CITY', NULL, NULL, NULL, '000-133-262', 'N/A', NULL, NULL, NULL, NULL),
(59, 'NEW BIAN YEK COMMERCIAL, INC.', '400059', NULL, 'CALINDAGAN DUMAGUETE CITY', NULL, NULL, NULL, '000-270-931-0000', '12%', '225-0302/422-2098', NULL, NULL, NULL),
(60, 'NEW FJ ENGINE REBUILDER', '400060', NULL, 'ORMOC CITY', NULL, NULL, NULL, '101-720-465', 'NV', NULL, NULL, NULL, NULL),
(61, 'NEW ORMOC COMMERCIAL AUTO PARTS CENTER', '400061', NULL, 'ORMOC CITY', NULL, NULL, NULL, '000-226-524', 'N/A', NULL, NULL, NULL, NULL),
(62, 'NEWLONG MACHINE PHILS., INC.', '400062', NULL, 'MANDAUE CITY', NULL, NULL, NULL, '000-162-587', 'N/A', NULL, NULL, NULL, NULL),
(63, 'ORMOC BUENAS SHOPPERS', '400063', NULL, 'ORMOC CITY', NULL, NULL, NULL, '101-721-303', '12%', NULL, NULL, NULL, NULL),
(64, 'ORMOC CITY TELEPHONE CO., INC.', '400064', NULL, 'L. JAENA COR. J. NAVARRO STS. ORMOC CITY', NULL, NULL, NULL, '000-798-914', '12%', NULL, NULL, NULL, NULL),
(65, 'ORMOC FUJI HARDWARE & AUTO SUPPLY', '400065', NULL, 'ORMOC CITY', NULL, NULL, NULL, '161-000-009', 'N/A', NULL, NULL, NULL, NULL),
(66, 'ORMOC HEAVY PARTS & TIRE SUPPLY', '400066', NULL, 'ORMOC CITY', NULL, NULL, NULL, '168-330-395', 'N/A', NULL, NULL, NULL, NULL),
(67, 'ORMOC RIZAL ENTERPRISES COMPANY', '400067', NULL, 'RIZAL STREET ORMOC CITY', NULL, NULL, NULL, '003-971-491', 'N/A', NULL, NULL, NULL, NULL),
(68, 'PETRON CORPORATION', '400068', NULL, 'LINAO ORMOC CITY', NULL, NULL, NULL, '000-168-801', '12%', NULL, NULL, NULL, NULL),
(69, 'PHILIPPINE DUPLICATORS, INC.', '400069', NULL, 'CEBU CITY', NULL, NULL, NULL, '000-941-342', 'N/A', NULL, NULL, NULL, NULL),
(70, 'PRYCE GASES, INC.', '400070', NULL, 'BRGY. PUNTA ORMOC CITY', NULL, NULL, NULL, '000-292-468-0031', '12%', '255-4668', '561-1063', NULL, NULL),
(71, 'PTR CEBU ENTERPRISES', '400071', NULL, 'CEBU CITY', NULL, NULL, NULL, '149-732-837', 'N/A', NULL, NULL, NULL, NULL),
(72, 'R. BRODETH MARKETING', '400072', NULL, 'ORMOC CITY', NULL, NULL, NULL, '155-499-813', 'N/A', NULL, NULL, NULL, NULL),
(73, 'RAM PARTS SUPPLY', '400073', NULL, 'CEBU CITY', NULL, NULL, NULL, '160-009-165', 'N/A', NULL, NULL, NULL, NULL),
(74, 'RJ & P PHILIPPINES FOUNDRY', '400074', NULL, 'CEBU CITY', NULL, NULL, NULL, '102-000-230', 'N/A', NULL, NULL, NULL, NULL),
(75, 'ROLIM RUBBER INDUSTRIAL SUPPLY, INC.', '400075', NULL, '2ND FLOOR, BEJAR BUILDING M.J. CUENCO AVE., CEBU CITY', NULL, NULL, NULL, '000-311-099', 'N/A', NULL, NULL, NULL, NULL),
(76, 'SAN JOSE MERCHANDISING, INC.', '400076', NULL, 'CEBU CITY', NULL, NULL, NULL, '103-785-599', 'N/A', NULL, NULL, NULL, NULL),
(77, 'SERV-WELL DRUGSTORE', '400077', NULL, 'ORMOC CITY', NULL, NULL, NULL, '101-719-391', 'N/A', NULL, NULL, NULL, NULL),
(78, 'SHUREBRIGHT ELECTRICAL SUPPLY', '400078', NULL, 'CEBU CITY', NULL, NULL, NULL, '110-781-504', 'N/A', NULL, NULL, NULL, NULL),
(79, 'SIGNAL TRADING CORPORATION', '400079', NULL, 'MANDAUE CITY', NULL, NULL, NULL, '000-313-346', 'N/A', NULL, NULL, NULL, NULL),
(80, 'SILAHIS MARKETING CORPORATION', '400080', NULL, 'RM104/105 KRC BLDG.,SUBANGDAKU MANDAUE CITY', NULL, NULL, NULL, '000-143-861-0001', '12%', NULL, NULL, NULL, NULL),
(81, 'SIMON ENTERPRISES, INC.', '400081', NULL, 'CEBU CITY', NULL, NULL, NULL, '000-070-744', 'N/A', NULL, NULL, NULL, NULL),
(82, 'SKEFTECH, INC.', '400082', NULL, 'CEBU CITY', NULL, NULL, NULL, '000-174-453', 'N/A', NULL, NULL, NULL, NULL),
(83, 'SMC PNEUMATICS PHILS., INC.', '400083', NULL, 'CEBU CITY', NULL, NULL, NULL, '000-420-220', 'N/A', NULL, NULL, NULL, NULL),
(84, 'SOUTHERN INDUSTRIAL GASES PHILS., INC.', '400084', NULL, 'ORMOC CITY', NULL, NULL, NULL, '000-395-990', 'N/A', NULL, NULL, NULL, NULL),
(85, 'STOCKPOLE INDUSTRIAL SUPPLIES', '400085', NULL, '12 TRES DE ABRIL ST. LABANGON, CEBU CITY', NULL, NULL, NULL, '100-072-156', 'N/A', NULL, NULL, NULL, NULL),
(86, 'SUPERLIFT EQUIPMENT, INC.', '400086', NULL, 'CEBU CITY', NULL, NULL, NULL, '000-390-817', 'N/A', NULL, NULL, NULL, NULL),
(87, 'THREADSETTERS PHILS., INC.', '400087', NULL, 'MANDAUE CITY', NULL, NULL, NULL, '000-071-222', 'N/A', NULL, NULL, NULL, NULL),
(88, 'TREADBONDERS PHILS., INC.', '400088', NULL, 'MANDAUE CITY', NULL, NULL, NULL, '000-224-032', 'N/A', NULL, NULL, NULL, NULL),
(89, 'THE REPORTER INC.', '400089', NULL, 'TRI BLDG., F. ABLEN STREET COGON, ORMOC CITY', NULL, NULL, NULL, '000-616-605', 'N/A', NULL, NULL, NULL, NULL),
(90, 'UNI-PRODUCERS CO., INC.', '400090', NULL, 'CEBU CITY', NULL, NULL, NULL, '000-151-056', 'N/A', NULL, NULL, NULL, NULL),
(91, 'UNI-REAL TRADING CORPORATION', '400091', NULL, 'REAL ST. ORMOC CITY', NULL, NULL, NULL, '004-305-254-000', '12%', '255-4521;561-5826', NULL, NULL, NULL),
(92, 'UP-TOWN INDUSTRIAL SALES, INC.', '400092', NULL, '56-58 MADISON STREET 1554 MANDALUYONG CITY', NULL, NULL, NULL, '000-062-769', 'N/A', NULL, NULL, NULL, NULL),
(93, 'VASQUEZ PRESS & MARKETING', '400093', NULL, 'ORMOC CITY', NULL, NULL, NULL, '160-000-175', 'N/A', NULL, NULL, NULL, NULL),
(94, 'WASHINGTON TRADING, INC.', '400094', NULL, 'RIZAL AVE. TACLOBAN CITY', NULL, NULL, NULL, '000-272-192', 'N/A', NULL, NULL, NULL, NULL),
(95, 'WESTERNHOME ELECTRICAL CORPORATION', '400095', NULL, 'T. PADILLA EXT. CEBU CITY', NULL, NULL, NULL, '000-310-207', 'N/A', NULL, NULL, NULL, NULL),
(96, 'YAKAL CONSTRUCTION', '400096', NULL, 'COGON, ORMOC CITY', NULL, NULL, NULL, '101-719-647', 'N/A', NULL, NULL, NULL, NULL),
(97, 'YUTEK HARDWARE', '400097', NULL, 'CEBU CITY', NULL, NULL, NULL, '103-785-268-0000', 'N/A', NULL, NULL, NULL, NULL),
(98, 'PHESCHEM INDUSTRIAL CORPORATION', '400098', NULL, 'PALOMPON LEYTE', NULL, NULL, NULL, '000-199-084-000', '12%', NULL, NULL, NULL, NULL),
(99, 'AMERICAN PLASTIC, INCORPORATED', '400099', NULL, '14 EMERALD AVE., EMERALD BLDG. ORTIGAS COMM\'L. COMPLEX, PASIG', NULL, NULL, NULL, '000-287-741-000', 'N/A', NULL, NULL, NULL, NULL),
(100, 'CODILLA\'S ENTERPRISES', '400100', NULL, 'OSMEÑA STREET ORMOC CITY', NULL, NULL, NULL, '101-719-698-0000', 'N/A', NULL, NULL, NULL, NULL),
(101, 'RUD ENTERPRISES & ENGINEERING WORKS', '400101', NULL, '892 M. J. CUENCO AVENUE CEBU CITY', NULL, NULL, NULL, '146-621-516', 'N/A', NULL, NULL, NULL, NULL),
(102, 'INTRASTEEL, INC.', '400102', NULL, 'SUITE 206 F. UY BLDG. TIPOLO, MANDAUE CITY', NULL, NULL, NULL, '000-126-223', 'N/A', NULL, NULL, NULL, NULL),
(103, 'MECHANICAL HANDLING EQUIPMENT CO., INC.', '400103', NULL, 'G/F DIAMOND PLAZA BLDG. MC BRIONES ST., HIGHWAY MAGUIKAY MANDAUE CITY, CEBU', NULL, NULL, NULL, '000-164-929-0002', 'N/A', '346-7752/422-9250', NULL, NULL, NULL),
(104, 'DESCO INCORPORATED', '400104', NULL, 'KM. 21 VILLONCO RD., WEST SERVICE RD., MUNTINLUPA CITY', NULL, NULL, NULL, '000-159-878', 'N/A', NULL, NULL, NULL, NULL),
(105, 'ORMOC RIZAL GLASS & ALUMINUM SUPPLY', '400105', NULL, 'RIZAL STREET ORMOC CITY', NULL, NULL, NULL, '116-611-406', 'N/A', NULL, NULL, NULL, NULL),
(106, 'HMIS &/OR GONZALITO V. HERMOSO', '400106', NULL, 'DAYHAGAN ORMOC CITY', NULL, NULL, NULL, '180-492-985', 'N/A', NULL, NULL, NULL, NULL),
(107, 'PRIME PARTS FABRICATORS & DEV\'T. CORP.', '400107', NULL, 'CANSOJONG BEACH ROAD, TALISAY CITY, CEBU 6045', NULL, NULL, NULL, '000-070-334-000', '12%', NULL, NULL, NULL, NULL),
(108, 'LEELENG COMMERCIAL, INC.', '400108', NULL, '387-393 DASMARIÑAS STREET BINONDO, MANILA', NULL, NULL, NULL, '000-337-640', 'N/A', NULL, NULL, NULL, NULL),
(109, 'ORMOC CALTEX SERVICE STATION', '400109', NULL, 'AVILES STREET ORMOC CITY', NULL, NULL, NULL, '180-497-164', 'N/A', NULL, NULL, NULL, NULL),
(110, 'SUPERLIFT MATERIAL HANDLING SYSTEMS,INC.', '400110', NULL, '225 E. DELOS SANTOS AVENUE MANDALUYONG CITY', NULL, NULL, NULL, '005-056-117', 'N/A', NULL, NULL, NULL, NULL),
(111, 'NEGROS METAL CORPORATION', '400111', NULL, 'ALIJIS BACOLOD CITY', NULL, NULL, NULL, '000-425-467-0000', '12%', NULL, NULL, NULL, NULL),
(112, 'MACHINE-AID TECH PHILIPPINES, INC.', '400112', NULL, 'LOCSIN BLDG.,GUERRERO ST. COR. MAKATI AVE., MAKATI CITY', NULL, NULL, NULL, '000-130-106', 'N/A', NULL, NULL, NULL, NULL),
(113, 'EL BURGOS REAL AUTO SUPPLY', '400113', NULL, 'REAL STREET ORMOC CITY', NULL, NULL, NULL, '103-975-545', 'N/A', NULL, NULL, NULL, NULL),
(114, 'CODILLA\'S PETRON SUPER SERVICE STATION', '400114', NULL, 'OSMEÑA STREET ORMOC CITY', NULL, NULL, NULL, '104-743-443-0000', '12%', NULL, NULL, NULL, NULL),
(115, 'ORMOC LANTERN SUPPLY & HARDWARE', '400115', NULL, '21 RIZAL STREET ORMOC CITY', NULL, NULL, NULL, '101-719-405', 'N/A', NULL, NULL, NULL, NULL),
(116, 'CEBU FILE SYSTEMS, INC.', '400116', NULL, 'G. OUANO STREET MANDAUE CITY 6014, CEBU', NULL, NULL, NULL, '004-754-152-0000', 'N/A', NULL, NULL, NULL, NULL),
(117, 'HAWK TRADING', '400117', NULL, 'BACK OF KTI MOTORS WIRELESS, MANDAUE CITY', NULL, NULL, NULL, '102-980-039-000', '12%', NULL, NULL, NULL, NULL),
(118, 'UNITED REBUILDERS, INC.', '400118', NULL, 'MCARTHUR BOULEVARD CEBU CITY 6000', NULL, NULL, NULL, '000-312-282', 'N/A', NULL, NULL, NULL, NULL),
(119, 'FIRST ASIANMETALS CORPORATION', '400119', NULL, 'NATIONAL HIGHWAY, CAGAYAN DE ORO CITY', NULL, NULL, NULL, '001-219-113-0000', '12%', NULL, NULL, NULL, NULL),
(120, 'RCA SYSTEMS', '400120', NULL, '291 MAGALLANES ST. CEBU CITY', NULL, NULL, NULL, '103-784-807', 'N/A', NULL, NULL, NULL, NULL),
(121, 'INGERSOLL-RAND PHILIPPINES, INC.', '400121', NULL, '2300 DON CHINO ROCES AVE. EXT. 1231 MAKATI CITY, PHILIPPINES', NULL, NULL, NULL, '000-126-055', 'N/A', NULL, NULL, NULL, NULL),
(122, 'SIMPSON\'S PHILS. INC.', '400122', NULL, 'CONTINENTAL PLAZA COND.,ANNA- POLIS,GREENHILLS,S.JUAN, M.M.', NULL, NULL, NULL, '003-743-192', 'N/A', NULL, NULL, NULL, NULL),
(123, 'ORMOC LIBERTY MARKETING INC.', '400123', NULL, 'LOPEZ JAENA STREET ORMOC CITY', NULL, NULL, NULL, '004-305-246-000', '12%', '255-1388,561-9632', NULL, NULL, NULL),
(124, 'HI-COOL SALES & SERVICES', '400124', NULL, 'DON F. LARRAZABAL HIGHWAY ORMOC CITY', NULL, NULL, NULL, '145-787-332', 'N/A', NULL, NULL, NULL, NULL),
(125, 'CHEMICAL RESOURCES, INC.', '400125', NULL, 'UNIT 1105 PRSTIGE TOWER EMERALD AVE., ORTIGAS CENTER', NULL, NULL, NULL, '000-281-713-0000', 'N/A', NULL, NULL, NULL, NULL),
(126, 'JETRONICS MARKETING & ALLIED SERVICES', '400126', NULL, '82 REAL STREET TACLOBAN CITY', NULL, NULL, NULL, '134-577-648', 'N/A', NULL, NULL, NULL, NULL),
(127, 'CEBU MEDICAL SUPPLY, INC.', '400127', NULL, '313 A. FORTUNA STREET MANDAUE CITY, CEBU', NULL, NULL, NULL, '000-067-378-0000', 'N/A', NULL, NULL, NULL, NULL),
(128, 'SAURO GEAR WORKS, INC.', '400128', NULL, '114-116 A. BONIFACIO STREET CEBU CITY 6000', NULL, NULL, NULL, '004-493-543', 'N/A', NULL, NULL, NULL, NULL),
(129, 'EGS ENTERPRISES', '400129', NULL, 'VALENCIA, ORMOC CITY', NULL, NULL, NULL, '113-504-989', 'N/A', NULL, NULL, NULL, NULL),
(130, 'EGS ENTERPRISES', '400130', NULL, 'VALENCIA ORMOC CITY, LEYTE', NULL, NULL, NULL, '113-504-989', 'N/A', NULL, NULL, NULL, NULL),
(131, 'SEALAND INDUSTRIAL SUPPLY', '400131', NULL, 'RM.204 FLOREM BLDG. A.C. CORTES AVE. IBABAO, MANDAUE CITY', NULL, NULL, NULL, '103-287-744-003', '12%', NULL, NULL, NULL, NULL),
(132, 'VAN DER HORST TECHNOLOGIES PHILS., INC.', '400132', NULL, 'LOT 8,BLOCK 4,1ST CAVITE IND\'L ESTATE, LANGKAAN, DASMARIÑAS C', NULL, NULL, NULL, '004-446-371', 'N/A', NULL, NULL, NULL, NULL),
(133, 'BUILD IT! BUILDING MATERIALS SALES, INC.', '400133', NULL, 'RM.27-28 CENTURY PLAZA COMM\'L. COMPLEX, JUANA OSMEÑA, CEBU C', NULL, NULL, NULL, '200-331-879-0000', 'N/A', NULL, NULL, NULL, NULL),
(134, 'SUNFIELD ENTERPRISES', '400134', NULL, 'MABINI ST. ORMOC CITY', NULL, NULL, NULL, '187-369-006', 'N/A', NULL, NULL, NULL, NULL),
(135, 'PAN-PHIL. ALLOYS MFG., INC.', '400135', NULL, '6TH FLR. EXEC. BLDG. CENTER BUENDIA COR.MAKATI AVE.,MAKATI', NULL, NULL, NULL, '000-167-866', 'N/A', NULL, NULL, NULL, NULL),
(136, 'CALTEX (PHILIPPINES), INC.', '400136', NULL, 'ORMOC BULK PLANT SAN ANTONIO, ORMOC CITY', NULL, NULL, NULL, '000-349-759-0000', 'N/A', NULL, NULL, NULL, NULL),
(137, 'NEWMAN CHEMICALS CORPORATION', '400137', NULL, '150 F.B. CABAHUG STREET MANDAUE CITY, CEBU', NULL, NULL, NULL, '000-070-288', 'N/A', NULL, NULL, '(BIR cert.) newman.chemicals@yahoo.com - Attn: MATIT', NULL),
(138, 'ALTA-WELD, INC.', '400138', NULL, 'CEBU CITY', NULL, NULL, NULL, '000-410-653-000', 'N/A', NULL, NULL, NULL, NULL),
(139, 'VEMAVAL, INC.', '400139', NULL, '1 LANGKA ROAD, FTI COMPOUND TAGUIG, METRO MANILA', NULL, NULL, NULL, '000-147-757', 'N/A', NULL, NULL, NULL, NULL),
(140, 'SEAFIX MARINE SERVICES', '400140', NULL, 'ST. AGUSTIN SUBD., SIMBORIO TAYUD, LILOAN, CEBU', NULL, NULL, NULL, '138-985-321', 'N/A', NULL, NULL, NULL, NULL),
(141, 'ASSISTCO ENERGY & INDUSTRIAL CORPORATION', '400141', NULL, '1ST AVE. COR. ANTONIO ST. BAGUMBAYAN, TAGUIG, METRO MLA.', NULL, NULL, NULL, '000-107-092-0000', 'N/A', NULL, NULL, NULL, NULL),
(142, 'R. ALLOSA TRUCKING', '400142', NULL, '68 OSMEÑA STREET ORMOC CITY', NULL, NULL, NULL, '101-719-915', 'N/A', NULL, NULL, NULL, NULL),
(143, 'FIVE L PHARMACEUTICALS, INC.', '400143', NULL, 'RM. 212 JOSMAR BLDG., M.H. DEL PILAR ST., TACLOBAN CITY', NULL, NULL, NULL, '005-672-964', 'N/A', NULL, NULL, NULL, NULL),
(144, 'VALPAC MARKETING', '400144', NULL, '14 - 1 A - J. LABRA ST. GUADALUPE, CEBU CITY', NULL, NULL, NULL, '123-775-885', 'N/A', NULL, NULL, NULL, NULL),
(145, 'GLOBAL ENTERPRISES', '400145', NULL, '25-A OSMEÑA STREET ORMOC CITY', NULL, NULL, NULL, '101-719-907', 'N/A', NULL, NULL, NULL, NULL),
(146, 'F.S. SERAFICA ENTERPRISES, INC.', '400146', NULL, 'REAL STREET ORMOC CITY', NULL, NULL, NULL, '000-197-799', 'N/A', NULL, NULL, NULL, NULL),
(147, 'YALE HARDWARE', '400147', NULL, 'PUNTA DEL NORTE BLDG., M.J. CUENCO COR.M.C.BRIONES ST,CEBU', NULL, NULL, NULL, '113-033-093', 'N/A', NULL, NULL, NULL, NULL),
(148, 'HILONGOS GENERAL CONTRACTORS, INC.', '400148', NULL, '29 BONIFACIO STREET ORMOC CITY', NULL, NULL, NULL, '000-226-792', 'N/A', NULL, NULL, NULL, NULL),
(149, 'PJ ENTERPRISES', '400149', NULL, 'REAL STREET ORMOC CITY', NULL, NULL, NULL, '104-745-750-000', 'N/A', NULL, NULL, NULL, NULL),
(150, 'NICKLAUS SALES CORPORATION', '400150', NULL, '1795 MALABON COR.A.MENDOZA STS (ANDALUCIA) STA. CRUZ, MANILA', NULL, NULL, NULL, '001-026-581', 'N/A', NULL, NULL, NULL, NULL),
(151, 'PROJECT ENGINEERING CONSTRUCTION', '400151', NULL, '7 JC AVILES BUILDING SAN PEDRO ST., ORMOC CITY', NULL, NULL, NULL, '101-719-689', 'N/A', NULL, NULL, NULL, NULL),
(152, 'MARKETPHIL PHARMA DISTRIBUTOR', '400152', NULL, 'BLK.6,LOT32,PH.2,VILLADOLINA TOWNSHIP,MARASBARAS,TACLOBAN', NULL, NULL, NULL, '902-650-433', 'N/A', NULL, NULL, NULL, NULL),
(153, 'APJ ENTERPRISES', '400153', NULL, 'REAL COR. SAN NICOLAS STS. ORMOC CITY', NULL, NULL, NULL, '919-704-011-000', 'N/A', NULL, NULL, NULL, NULL),
(154, 'SEVERO SY LING, INC. OR PERRY SYLING', '400154', NULL, 'DOOR 25 VEL-OUANO BLDG., MC BRIONES HIGHWAY, BAKILID MANDAUE CITY', NULL, NULL, NULL, '000-392-173-0002', 'N/A', '345-0503/0506', NULL, NULL, NULL),
(155, 'FASTPAK GLOBAL EXPRESS CORPORATION', '400155', NULL, 'RIZAL COR. SAN VIDAL STS. ORMOC CITY', NULL, NULL, NULL, '001-947-880-026', '12%', NULL, NULL, NULL, NULL),
(156, 'W AND L TRADING CORPORATION', '400156', NULL, 'DOOR #17 VELOSO OUANO BLDG. HIGHWAY, MANDAUE CITY 6014', NULL, NULL, NULL, '000-388-901-002', '12%', NULL, NULL, NULL, NULL),
(157, 'A.R.A. AUTO GLASS & ALUMINUM SUPPLY', '400157', NULL, 'LILIA AVENUE, COGON ORMOC CITY', NULL, NULL, NULL, '110-574-856-000', 'N/A', NULL, NULL, NULL, NULL),
(158, 'LUX MARKETING, INC.', '400158', NULL, '2231 PASONG TAMO STREET MAKATI CITY 1231', NULL, NULL, NULL, '000-116-270', 'N/A', NULL, NULL, NULL, NULL),
(159, 'CAPALONGA AGRI-BUSINESS CO., INC.', '400159', NULL, 'TACLOBAN CITY', NULL, NULL, NULL, '128-674-879-0000', 'N/A', NULL, NULL, NULL, NULL),
(160, 'ASIAMARK CORPORATION', '400160', NULL, 'SUITE 213, BSP BLDG., BUENDIA COR. MEDINA ST., MAKATI CITY', NULL, NULL, NULL, '004-658-441-0000', 'N/A', NULL, NULL, NULL, NULL),
(161, 'CODILLA\'S AUTO PARTS CENTER', '400161', NULL, '19 OSMEÑA STREET ORMOC CITY', NULL, NULL, NULL, '901-116-379-0000', '12%', NULL, 'T-561-5107', NULL, NULL),
(162, 'CAP INTERNATIONAL, INC.', '400162', NULL, '410 F. BLUMENTRITT SAN JUAN, METRO MANILA', NULL, NULL, NULL, '004-781-058-0000', 'N/A', NULL, NULL, NULL, NULL),
(163, 'FOSROC PHILIPPINES', '400163', NULL, 'U-1109 CITYLAND 10 TOWER II DELA COSTA, SALCEDO VILL.,M.M.', NULL, NULL, NULL, '162-491-002', 'N/A', NULL, NULL, NULL, NULL),
(164, 'M.S. MACHINERY & METAL FABRICATOR CORP.', '400164', NULL, '105 F. ROXAS ST., GRACE PARK CALOOCAN CITY, METRO MANILA', NULL, NULL, NULL, '000-295-260', 'N/A', NULL, NULL, NULL, NULL),
(165, 'VINATEX TIRE & INDUSTRIAL CORPORATION', '400165', NULL, 'BASAK, MANDAUE CITY CEBU, PHILIPPINES', NULL, NULL, NULL, '000-070-576', 'N/A', NULL, NULL, NULL, NULL),
(166, 'BUGSY ENTERPRISES', '400166', NULL, 'LILIA AVE., COGON ORMOC CITY', NULL, NULL, NULL, '089-871-086-0000', 'N/A', NULL, NULL, NULL, NULL),
(167, 'FUCHS LUBRICANTS PHILIPPINES, INC.', '400167', NULL, '8280 DR.A.SANTOS AVE., SUCAT PARAÑAQUE, METRO MANILA', NULL, NULL, NULL, '200-263-467', 'N/A', NULL, NULL, NULL, NULL),
(168, 'ORMOC GLOBAL ENTERPRISES', '400168', NULL, '25-A OSMEÑA STREET ORMOC CITY', NULL, NULL, NULL, '101-719-907', 'N/A', NULL, NULL, NULL, NULL),
(169, 'MINDWORKS COMPUTER CENTER', '400169', NULL, 'JC AVILES COMMERCIAL BUILDING AVILES ST., ORMOC CITY', NULL, NULL, NULL, '168-330-128', 'N/A', NULL, NULL, NULL, NULL),
(170, 'GLOBAL COMMERCIAL TRADING CORPORATION', '400170', NULL, '157 EDSA COR. BERKELEY ST. MANDALUYONG CITY', NULL, NULL, NULL, '000-119-887', 'N/A', NULL, NULL, NULL, NULL),
(171, 'HAWKEYE RESOURCES PHILIPPINES, INC.', '400171', NULL, 'MEJIA SUBDIVISION ORMOC CITY', NULL, NULL, NULL, '005-374-369', 'N/A', NULL, NULL, NULL, NULL),
(172, 'JUN-JUN TRADING', '400172', NULL, 'CAMP DOWNES ORMOC CITY', NULL, NULL, NULL, '100-451-612', 'N/A', NULL, NULL, NULL, NULL),
(173, 'HI-TEK ELECTRO-MECHANICAL SALES', '400173', NULL, 'GUSA HIGHWAY CAGAYAN DE ORO CITY', NULL, NULL, NULL, '004-352-366-000', '12%', '855-1188 ; 855-8960', NULL, NULL, NULL),
(174, 'BE INDUSTRIAL SALES & WORKS', '400174', NULL, 'BONIFACIO ST., ORMOC CITY TEL.#(053)255-8197', NULL, NULL, NULL, '121-248-395-0000', 'N/A', NULL, NULL, NULL, NULL),
(175, 'SELLCHEM CORPORATION', '400175', NULL, 'JAYME ST., PAKNAAN MANDAUE CITY', NULL, NULL, NULL, '59-8-000002', 'N/A', NULL, NULL, NULL, NULL),
(176, 'ORMOC CALIBRATION & MACHINING CENTER', '400176', NULL, 'REAL ST., ORMOC CITY TEL.# <053> 255-4758', NULL, NULL, NULL, '142-906-765', 'N/A', NULL, NULL, NULL, NULL),
(177, 'ORMOC CALIBRATION & MACHINING CENTER', '400177', NULL, 'REAL ST. ORMOC CITY, LEYTE', NULL, NULL, NULL, '142-906-765', 'N/A', NULL, NULL, NULL, NULL),
(178, 'FH COMMERCIAL, INC.', '400178', NULL, 'CHINA BANK BLDG.,MACARTHUR HI-WAY, POTRERO, MALABON, M.M.', NULL, NULL, NULL, '000-247-463', 'N/A', NULL, NULL, NULL, NULL),
(179, 'ONDEO NALCO (PHILIPPINES), INC.', '400179', NULL, '12F,ASIAN STAR BLDG.,ASEAN DRV COR.SINGAPURA LANE,ALABANG,M.M', NULL, NULL, NULL, '000-133-262', 'N/A', NULL, NULL, NULL, NULL),
(180, 'MARNIEL MARKETING', '400180', NULL, 'LOT 1, BLOCK 2, VILLA ESMAELA SUBD. COGON, PARDO CEBU CITY', NULL, NULL, NULL, '157-280-062-000', '12%', NULL, NULL, NULL, NULL),
(181, 'TOZEN PHILIPPINES, INC.', '400181', NULL, 'ZENAIDA I BUILDING CONGRESSIONAL AVE.,QUEZON CITY', NULL, NULL, NULL, '200-342-547', 'N/A', NULL, NULL, NULL, NULL),
(182, 'MACHTECH METAL FABRICATION', '400182', NULL, 'VISTA VERDE NORTH EXEC VILLAGE LAWANG BATO, VALENZUELA, M.M.', NULL, NULL, NULL, '116-172-103', 'N/A', NULL, NULL, NULL, NULL),
(183, 'L-TRADING', '400183', NULL, '#2-5 2ND FLR., JDI BLDG. GALO ST., BACOLOD CITY', NULL, NULL, NULL, '919-469-429', 'N/A', NULL, NULL, NULL, NULL),
(184, 'ELECMAN &/OR BLAS C. ARADO', '400184', NULL, 'D-21 REAL STREET ORMOC CITY', NULL, NULL, NULL, '107-567-029-000', '12%', NULL, NULL, 'ELECMAN AUTO INDUSTRIAL ELECTRICAL SUPPLY AND SERVICES', NULL),
(185, 'KAMBAL INDUSTRIAL SUPPLY', '400185', NULL, '2565 E/F CABANILLAS ST. BRGY. LA PAZ, MAKATI CITY', NULL, NULL, NULL, '102-689-001', 'N/A', NULL, NULL, NULL, NULL),
(186, 'MPL ENTERPRISES', '400186', NULL, 'BANTIGUE, ORMOC CITY TEL. # 255-2489', NULL, NULL, NULL, '157-317-727', 'N/A', NULL, NULL, NULL, NULL),
(187, 'RED-J PUMP SERVICES', '400187', NULL, '229-I A V. RAMA AVENUE GUADALUPE, CEBU CITY', NULL, NULL, NULL, '161-516-750', 'N/A', NULL, NULL, NULL, NULL),
(188, 'RCP MANUFACTURING COMPANY, INC.', '400188', NULL, '935 SALINAS DRIVE LAHUG, CEBU CITY', NULL, NULL, NULL, '000-310-256', 'N/A', NULL, NULL, NULL, NULL),
(189, 'SOUTHEAST TIRE TRADERS', '400189', NULL, 'AVILES STREET ORMOC CITY', NULL, NULL, NULL, '102-723-024', 'N/A', NULL, NULL, NULL, NULL),
(190, 'RS COMPONENTS LIMITED', '400190', NULL, 'FLR.21,UNITB1,MULTINAT\'L BANK- CORP CTR,6805 AYALA AVE,MAKATI', NULL, NULL, NULL, '201-813-524', 'N/A', NULL, NULL, NULL, NULL),
(191, 'ATOM CHEMICAL COMPANY, INC.', '400191', NULL, 'CITYLAND CONDO 10TOWER1,HVDELA COSTA COR AYALA,SALCEDO VILL.', NULL, NULL, NULL, '000-154-904-0000', 'N/A', NULL, NULL, NULL, NULL),
(192, 'DAYTON COMMERCIAL INC.', '400192', NULL, 'UNIT A-1 OSLO ST., VISTA VERDE EXEC.VILL.KAYBIGA,CALOOCANCITY', NULL, NULL, NULL, '208-471-808', 'N/A', NULL, NULL, NULL, NULL),
(193, 'STEELMAN INDUSTRIAL SALES CORPORATION', '400193', NULL, '2960 JOSE ABAD SANTOS STREET TONDO, MANILA', NULL, NULL, NULL, '004-563-600', 'N/A', NULL, NULL, NULL, NULL),
(194, 'ELASCO INTERNATIONAL CORPORATION', '400194', NULL, '4514 CASINO ST., PALANAN MAKATI CITY', NULL, NULL, NULL, '000-345-080', 'N/A', NULL, NULL, NULL, NULL),
(195, 'DEBISCO ENTERPRISES, INC.', '400195', NULL, '1316 C.M.RECTO AVENUE STA. CRUZ, MANILA', NULL, NULL, NULL, '000-330-548', 'N/A', NULL, NULL, NULL, NULL),
(196, 'JUNIC TRADING & INDT\'L VENTURES', '400196', NULL, 'CALAJO-AN, MINGLANILLA CEBU', NULL, NULL, NULL, '207-705-305', 'N/A', NULL, NULL, NULL, NULL),
(197, 'REGAN INDUSTRIAL SALES, INC.', '400197', NULL, '#5 HARMONY ST., GRACE VILLAGE BALINGASA 1, QUEZON CITY', NULL, NULL, NULL, '000-365-856', 'N/A', NULL, NULL, NULL, NULL),
(198, 'GOLDEN HARVEST CONSTRUCTION SUPPLY, INC.', '400198', NULL, '280 G.ARANETA AVENUE MASAMBONG, QUEZON CITY', NULL, NULL, NULL, '000-366-882', 'N/A', NULL, NULL, NULL, NULL),
(199, 'BARICA HEAVY EQUIPMENT PARTS CENTER', '400199', NULL, '221 RIZAL AVENUE EXTENSION CALOOCAN CITY', NULL, NULL, NULL, '100-454-961-0000', 'N/A', NULL, NULL, NULL, NULL),
(200, 'INCOPHIL MARKETING CORPORATION', '400200', NULL, '4032 CUL DE SAC ROAD, BRGY.SUN VALLEY, PARAÑAQUE CITY', NULL, NULL, NULL, '000-316-462', 'N/A', NULL, NULL, NULL, NULL),
(201, 'BEARING CENTER & MACHINERY, INC.', '400201', NULL, '641-645 EVANGELISTA ST., QUIAPO, MANILA', NULL, NULL, NULL, '000-081-273-0000', 'N/A', NULL, NULL, NULL, NULL),
(202, 'MAGNA ENGINEERING SUPPLY', '400202', NULL, '240 SOCORRO ST., RAMOS CPD. BANLAT, TANDANG SORA, Q.C.', NULL, NULL, NULL, '104-005-425', 'N/A', NULL, NULL, NULL, NULL),
(203, 'CELWILCOR MARKETING', '400203', NULL, '500-A CANTERAS STREET MANDALUYONG CITY', NULL, NULL, NULL, '165-603-141-0000', 'N/A', NULL, NULL, NULL, NULL),
(204, 'UNITED BEARING INDUSTRIAL CORP.', '400204', NULL, 'VSP BLDG., 220 V.GULLAS ST. (MANALILI) CEBU CITY', NULL, NULL, NULL, '330-865-001', 'N/A', NULL, NULL, NULL, NULL),
(205, 'PAYWELL MARKETING & INDUSTRIAL SERVICES', '400205', NULL, 'TANCHEN BLDG.APT.B BAHAYANG PAGASA SUBD,MAYSAN RD,VLNZUELA', NULL, NULL, NULL, '107-292-985', 'N/A', NULL, NULL, NULL, NULL),
(206, 'CEBU BOLT AND SCREW SALES', '400206', NULL, 'B22 GOCHAN BLDG.,LEON KILAT ST PAHINA CENTRAL, CEBU CITY', NULL, NULL, NULL, '171-847-217-0000', 'N/A', NULL, NULL, NULL, NULL),
(207, 'JARDINE DAVIES INC.', '400207', NULL, 'CEBU CITY', NULL, NULL, NULL, '000-126-853', 'N/A', NULL, NULL, NULL, NULL),
(208, 'ALTUS TRADING CORPORATION', '400208', NULL, '1254 CARDONA ST., RIZAL VILLAGE, MAKATI CITY', NULL, NULL, NULL, '892-972-000-000', 'N/A', NULL, NULL, NULL, NULL),
(209, 'PILIPINAS SHELL PETROLEUM CORPORATION', '400209', NULL, '156 VALERO ST.,SALCEDO VILLAGE MAKATI CITY 1227', NULL, NULL, NULL, '000-164-757-000', '12%', NULL, NULL, NULL, NULL),
(210, 'MEGA SIX PAINT CENTER & GEN. MERCHANDISE', '400210', NULL, 'REAL STREET ORMOC CITY', NULL, NULL, NULL, '005-759-260', 'N/A', NULL, NULL, NULL, NULL),
(211, 'ORMOC NET', '400211', NULL, 'DIST. 12, SAN PEDRO STREET ORMOC CITY', NULL, NULL, NULL, '197-622-306-000', '12%', NULL, NULL, NULL, NULL),
(212, 'RIVERA\'S SUPERBOOKSTORE', '400212', NULL, 'AVILES STREET ORMOC CITY', NULL, NULL, NULL, '116-607-529', 'N/A', NULL, NULL, NULL, NULL),
(213, 'BERCHEM MFG. & MKTG. ENTERPRISES', '400213', NULL, 'WEST POBLACION NAGA, CEBU', NULL, NULL, NULL, '169-374-935-0000', 'N/A', NULL, NULL, NULL, NULL),
(214, 'ORMOC ALEMARS MARKETING', '400214', NULL, 'BONIFACIO COR. OSMEÑA STS. ORMOC CITY', NULL, NULL, NULL, '101-719-592', 'N/A', NULL, NULL, NULL, NULL),
(215, 'AOG CONSTRUCTION SUPPLY', '400215', NULL, 'OSMEÑA STREET ORMOC CITY, LEYTE', NULL, NULL, NULL, '104-743-410-000', 'N/A', NULL, NULL, NULL, NULL),
(216, 'VISMIN OR VICENTE ALLAN PATRIMONIO', '400216', NULL, '697 M.L. QUEZON ST., CASUNTINGAN, MANDAUE CITY', NULL, NULL, NULL, '119-865-517-0000', '12%', NULL, NULL, NULL, NULL),
(217, 'DI-CATALYST INTERNATIONAL CORP.', '400217', NULL, '2ND FLOOR, CONSTANCIA BUILDING 71-D TIMOG AVE., QUEZON CITY', NULL, NULL, NULL, '000-390-202', 'N/A', NULL, NULL, NULL, NULL),
(218, 'PHILIPPINE SINTER CORPORATION', '400218', NULL, 'MINDANAO SINTER PLANT VILLANUEVA, MISAMIS ORIENTAL', NULL, NULL, NULL, '000-138-161-000', 'N/A', NULL, NULL, NULL, NULL),
(219, 'GT INDUS. & ENGINEERING/ROSALIE PANUNCIA', '400219', NULL, 'SITIO CALUBIAN, YATI LILOAN CEBU', NULL, NULL, NULL, '902-750-027', 'N/A', NULL, NULL, NULL, NULL),
(220, 'RS ADVERTISING', '400220', NULL, '115 RIZAL ST. ORMOC CITY', NULL, NULL, NULL, '168-331-619', 'N/A', NULL, NULL, NULL, 'PREVIOUSLY NAMED AS MARNIEL MARKETING'),
(221, 'MOM\'S COGON HARDWARE & PAINT CENTER', '400221', NULL, 'COGON, ORMOC CITY', NULL, NULL, NULL, '101-721-128', 'N/A', NULL, NULL, NULL, NULL),
(222, 'D.P. CATAAG ENTERPRISES', '400222', NULL, 'J. NAVARRO STREET ORMOC CITY', NULL, NULL, NULL, '104-862-820-000', 'NV', NULL, NULL, NULL, NULL),
(223, 'UNICHEM INDUSTRIAL SALES, INC.', '400223', NULL, 'N & N ARCADE, A.C. CORTEZ AVE. HIGHWAY, IBABAO, MANDAUE CITY', NULL, NULL, NULL, '207-231-298-0000', '12%', NULL, NULL, NULL, NULL),
(224, 'PRIME ENGINEERING SUPPLIES', '400224', NULL, '50-B PRODUCTION ST.,GSIS VILL. PROJ.8, QUEZON CITY', NULL, NULL, NULL, '157-936-435-000', 'N/A', NULL, NULL, NULL, NULL),
(225, 'ORMOC GLASS TRADING', '400225', NULL, '330 LILIA AVE., COGON ORMOC CITY', NULL, NULL, NULL, '113-492-339-0000', '12%', '255-4519,255-4831,561-0274', '561-8665', 'PROP.: ANTHONY C. DOMINIC', NULL),
(226, 'TACLOBAN GLEEN MARKETING, INC.', '400226', NULL, '115 J. ROMUALDEZ ST., TACLOBAN CITY', NULL, NULL, NULL, '000-272-335', 'N/A', NULL, NULL, NULL, NULL),
(227, 'MHE-DEMATIC (P), INC.', '400227', NULL, 'D7 GENMAR BLDG., GEN. ECHAREZ EXT., CEBU CITY 6000', NULL, NULL, NULL, '000-163-723', 'N/A', NULL, NULL, NULL, NULL),
(228, 'MERCURY DRUG CORPORATION', '400228', NULL, 'ORMOC BRANCH, REAL ST. ORMOC CITY, LEYTE', NULL, NULL, NULL, '388-474-004', 'N/A', NULL, NULL, NULL, NULL),
(229, 'L.D.C. VILLAR COMPUWARES', '400229', NULL, 'REAL STREET ORMOC CITY', NULL, NULL, NULL, '104-743-936', '12%', '255-3130', '255-3130', 'ALEXIS CRISOSTOMO VILLAR-PROP.', NULL),
(230, 'ORMOC MACMERCURY HARDWARE', '400230', NULL, '23 AVILES STREET ORMOC CITY, LEYTE', NULL, NULL, NULL, '005-760-260-000', '12%', NULL, NULL, NULL, NULL),
(231, 'CEBU HYDRAULICS & INDUS. SERVICES INC.', '400231', NULL, 'H.RAMIREZ BLDG.M.H.DEL PILAR ST.,GUIZO,MANDAUE CITY', NULL, NULL, NULL, '204-210-279-0000', 'N/A', NULL, NULL, NULL, NULL),
(232, 'EXCLUSIVE TRADERS, INC.', '400232', NULL, 'PADILLA DELOS REYES BLDG. 232 JUAN LUNA ST.,BINONDO,MLA.', NULL, NULL, NULL, '000-330-890', 'N/A', NULL, NULL, NULL, NULL),
(233, 'ARBA CALIBRATION SERVICES & PARTS CENTER', '400233', NULL, 'SAN ISIDRO ORMOC CITY', NULL, NULL, NULL, '183-874-918-000', 'NV', NULL, NULL, 'PROP.:JOSE ELGY O. ARNADO', NULL),
(234, 'CEBU TURBO SALES & SERVICES INC.', '400234', NULL, 'PELAEZ BLDG., A.S.FORTUNA ST. BAKILID, MANDAUE CITY', NULL, NULL, NULL, '217-434-054-0000', 'N/A', NULL, NULL, NULL, NULL),
(235, 'YOKOGAWA PHILIPPINES, INC.', '400235', NULL, 'TOPY INDUSTRIES BLDG.,#3 CALLE ECONOMIA ST.,BAGUMBAYAN,Q.C.', NULL, NULL, NULL, '004-621-326', 'N/A', NULL, NULL, NULL, NULL),
(236, 'TECHNO-TRADE RESOURCES (DAVAO), INC.', '400236', NULL, '164 R. CASTILLO ST. AGDAO, DAVAO CITY', NULL, NULL, NULL, '000-065-416', 'N/A', NULL, NULL, NULL, NULL),
(237, 'HIGHLAND TRACTOR PARTS, INC.', '400237', NULL, '180 4TH AVE. BRGY. 48 GRACE PARK CALOOCAN CITY 1400', NULL, NULL, NULL, '000-295-211-0000', '12%', NULL, NULL, NULL, NULL),
(238, 'LE PRICE INTERNATIONAL CORP.', '400238', NULL, '718 LEELENG BLDG. SHAW BLVD., MANDALUYONG CITY', NULL, NULL, NULL, '003-828-612', 'N/A', NULL, NULL, NULL, NULL),
(239, 'P.T. CERNA CORPORATION', '400239', NULL, '2166 PRIMO RIVERA ST. BRGY. LAPAZ, MAKATI CITY', NULL, NULL, NULL, '000-136-129-0002', 'N/A', NULL, NULL, NULL, NULL),
(240, 'MCCI CORPORATION', '400240', NULL, 'SALAMIN BLDG., 197 SALCEDO ST. LEGASPI VILLAGE, MAKATI CITY', NULL, NULL, NULL, '000-131-768', 'N/A', NULL, NULL, NULL, NULL),
(241, 'CEBU CAME INDUSTRIAL CORPORATION', '400241', NULL, 'VEL-OUANO BLDG., NAT\'L. HI-WAY A.S. FORTUNA ST., MANDAUE CITY', NULL, NULL, NULL, '004-760-108-0000', 'N/A', NULL, NULL, NULL, NULL),
(242, 'KENT INTERNATIONAL TRADING CO., INC.', '400242', NULL, '14 BRIXTON STREET PASIG CITY', NULL, NULL, NULL, '000-280-958', 'N/A', NULL, NULL, NULL, NULL),
(243, 'LLP INTRATRADE STEEL, CO.', '400243', NULL, 'RM. 206 F. UY BUILDING TIPOLO, MANDAUE CITY', NULL, NULL, NULL, '212-814-937', 'N/A', NULL, NULL, NULL, NULL),
(244, 'WDC ENTERPRISES', '400244', NULL, 'BLOCK 22,LOT 9,VEL PAL ESTATE PH.1,PAKIGNE,MINGLANILLA,CEBU', NULL, NULL, NULL, '101-597-047', 'N/A', NULL, NULL, NULL, NULL),
(245, 'MEGTRON INDUSTRIAL TECHNOLOGIES, INC.', '400245', NULL, '3747 J. LABRA ST. GUADALUPE, CEBU CITY', NULL, NULL, NULL, '226-614-527', 'N/A', NULL, NULL, NULL, NULL),
(246, 'GRAM INDUSTRIAL, INC.', '400246', NULL, 'SUITE 219 TIA MARIA SARAO BLDG C.V.STARR AVE.,LAS PIÑAS CITY', NULL, NULL, NULL, '000-415-544', 'N/A', NULL, NULL, NULL, NULL),
(247, 'SKF PHILIPPINES INC.', '400247', NULL, 'UNIT 302, ALEGRIA BLDG. 2229 CHINO ROCES AVE.,MAKATI', NULL, NULL, NULL, '000-141-297', 'N/A', NULL, NULL, NULL, NULL),
(248, 'TITING\'S UPHOLSTERY', '400248', NULL, 'F. ABLEN STREET COGON, ORMOC CITY', NULL, NULL, NULL, '155-582-303-000', 'NV', NULL, NULL, NULL, NULL),
(249, 'UNIFIED CHEMICAL INDUSTRIES, INC.', '400249', NULL, 'RM. 104 FC QUAD BLDG., CABAHUG ST., IBABAO, MANDAUE', NULL, NULL, NULL, '000-299-812', 'N/A', NULL, NULL, NULL, NULL),
(250, 'MHE-DEMAG(P)INC.', '400250', NULL, 'MAIN AVE, SEVERINA DIAMOND INDUSTRIAL ESTATE WEST SERVICE ROAD KM 16 SOUTH EXPRESSWAY PARANAQUE 1700', NULL, NULL, NULL, '000-163-723-0000', '12%', '786-7500', '786-7555', NULL, NULL),
(251, 'ABRIC\'S CONSTRUCTION & TRADING CORP.', '400251', NULL, 'BRGY. IPIL, ORMOC CITY LEYTE', NULL, NULL, NULL, '005-761-037-000', 'N/A', NULL, NULL, NULL, NULL),
(252, 'TOP COOL AIRCON SPECIALIST/TOP COOL AUTO AIRCONDITIONER', '400252', NULL, 'BRGY. PUNTA ORMOC CITY', NULL, NULL, NULL, '119-879-872-000', 'NV', '255-3305', NULL, 'PRO.: BONIFACIO L. DEGORIO JR.', NULL),
(253, 'GOODALL RUBBER CORPORATION', '400253', NULL, 'BASAK, MANDAUE CITY, CEBU', NULL, NULL, NULL, '000-068-218-000', 'N/A', NULL, NULL, NULL, NULL),
(254, 'FASTCARGO LOGISTICS CORPORATION', '400254', NULL, 'FATIMA VILLAGE, MARASBARAS TACLOBAN CITY', NULL, NULL, NULL, '000-312-748-008', '12%', NULL, NULL, NULL, NULL),
(255, 'PHILCOPY CORPORATION', '400255', NULL, '#140-B SANTIAGO APT REAL ST. DIST. 21, ORMOC CITY, LEYTE', NULL, NULL, NULL, '000-169-318-005', '12%', NULL, NULL, NULL, NULL),
(256, 'DOC\'S CARGO SERVICES', '400256', NULL, 'TABOK, MANDAUE CITY', NULL, NULL, NULL, '123-767-381-000(old) - 205-855-910-000 (new)', '12%', NULL, NULL, NULL, NULL),
(257, 'NEGROS INTEGRATED INDUSTRIES CORPORATION', '400257', NULL, 'OFF:73 MANDALAGAN, BACOLOD CIT', NULL, NULL, NULL, '077-000-425-400', 'N/A', NULL, NULL, NULL, NULL),
(258, 'THERMACOM, INC.', '400258', NULL, 'UNIT#1 201 SUMULONG HIGHWAY BGY. MAYAMOT, ANTIPOLO CITY', NULL, NULL, NULL, '228-799-896-000', 'N/A', NULL, NULL, NULL, NULL),
(259, 'SIME DARBY INDUSTRIES, INC.', '400259', NULL, 'MALUGAY ST. COR. AYALA AVE., MAKATI CITY P.O. BOX 1595 MCC', NULL, NULL, NULL, '000-530-760-0000', '12%', NULL, NULL, NULL, NULL),
(260, 'FLUID ENGINEERED EQUIPMENT TRADING', '400260', NULL, '53 P. ALMENDRAS ST., MABOLO, CEBU CITY', NULL, NULL, NULL, '102-716-443-000', 'N/A', NULL, NULL, NULL, NULL),
(261, 'MB MARKETING', '400261', NULL, 'BLK 2 LOT 3 UNO-R VILLE MANSILINGAN, BACOLOD CITY 6100', NULL, NULL, NULL, '077-137-473-589', 'N/A', NULL, NULL, NULL, NULL),
(262, 'XX PC TOOLS & COMPUTER SERVICES', '400262', NULL, 'FR. ISMAEL CATAAG ST. ORMOC CITY - BRANCH', NULL, NULL, NULL, '921-715-614', 'N/A', NULL, NULL, NULL, NULL),
(263, 'R.J. DEL PAN (CEBU), INC.', '400263', NULL, 'RM. 209 B.F. GOODRICH BLDG. LEGASPI ST., CEBU CITY', NULL, NULL, NULL, '000-305-830-000', 'N/A', NULL, NULL, NULL, NULL),
(264, 'MARISTELA ENGINEERING WORKS', '400264', NULL, 'BRGY. COOB, ORMOC CITY, LEYTE', NULL, NULL, NULL, '109-852-229-000', 'N/A', NULL, NULL, NULL, NULL),
(265, 'SHOKETSU SMC CORPORATION', '400265', NULL, 'UNIT 3 SENIDO BLDG A.C. CORTES AVE CAMBARO,MANDAUE CITY,PHILS', NULL, NULL, NULL, '225-318-059-002', 'N/A', NULL, NULL, NULL, NULL),
(266, 'ALB AUTO CARE', '400266', NULL, '552 HIGHWAY, F. MEJIA ORMOC CITY, LEYTE', NULL, NULL, NULL, '126-784-639-000', 'NV', NULL, NULL, NULL, NULL),
(267, 'TOTAL SERVE INDUSTRIAL SALES & SERVICES', '400267', NULL, '# 225 S.B. CABAHUG STREET IBABAO, MANDAUE CITY', NULL, NULL, NULL, '237-253-102-000', 'NV', NULL, NULL, NULL, NULL),
(268, 'INDUSTRIAL INSPECTION INTERNATIONAL INC.', '400268', NULL, '1175 NJL BLDG., CHINO ROCES AVE., MAKATI CITY', NULL, NULL, NULL, '000-123-412-000', 'N/A', NULL, NULL, NULL, NULL),
(269, 'KARL-GELSON INDUSTRIAL SALES CORPORATION', '400269', NULL, 'LUX MARKETING BLDG., V. RAMA AVENUE CALAMBA, CEBU CITY', NULL, NULL, NULL, '229-985-008-002', 'N/A', NULL, NULL, NULL, NULL),
(270, 'ORMOC HYDRAULIC HOSE CENTER', '400270', NULL, '889 LILIA AVE., COGON HIGHWAY, ORMOC CITY', NULL, NULL, NULL, '901-112-698', 'NV', NULL, NULL, NULL, NULL),
(271, 'MAPECON PHILIPPINES, INC.', '400271', NULL, 'MAPECON BLDG. 1091 B. HERNAN CORTES EXT SUBANGDAKU MANDAUE', NULL, NULL, NULL, '000-348-276-001', 'N/A', NULL, NULL, NULL, NULL),
(272, 'CHROMIX INDUSTRIAL TECHNOLOGIES, INC.', '400272', NULL, 'ZONE TANGKONG, PLARIDEL ST., PAKNAAN, MANDAUE CITY, CEBU', NULL, NULL, NULL, '230-510-953-0000', 'N/A', NULL, NULL, NULL, NULL),
(273, 'MAXIMAX SYSTEMS', '400273', NULL, 'G/F UNIT 3 FRIENDSHIP II BLDG. HI-WAY, SUBANGDAKU, MANDAUE CT', NULL, NULL, NULL, '000-314-678-000', 'N/A', NULL, NULL, NULL, NULL),
(274, 'MAXIMA MACHINERIES, INC.', '400274', NULL, '869 QUEZON AVENUE, QUEZON CITY PHILIPPINES', NULL, NULL, NULL, '006-618-023-001', 'N/A', NULL, NULL, NULL, NULL),
(275, 'HYPER AUTOMOTIVE PROFESSIONALS, INC.', '400275', NULL, 'BRGY. SAN PABLO, ORMOC CITY', NULL, NULL, NULL, '005-761-366-000', 'N/A', NULL, NULL, NULL, NULL),
(276, 'PRODUCT EQUIPT. RESOURCES & TRDG., INC.', '400276', NULL, 'DON SERGIO SUICO ST., BRGY. TINGUB, MANDAUE CITY, CEBU', NULL, NULL, NULL, '000-665-772-000', 'N/A', NULL, NULL, NULL, NULL),
(277, 'ATS SAND & GRAVEL SUPPLY', '400277', NULL, 'PANILAHAN, ORMOC CITY', NULL, NULL, NULL, '168-328-923-0000', 'NV', NULL, NULL, NULL, NULL),
(278, 'TOLYONG GENERAL MERCHANDISE', '400278', NULL, 'BALUGO, ALBUERA, LEYTE', NULL, NULL, NULL, '927-747-385-000', 'NV', NULL, NULL, NULL, NULL),
(279, 'LS PRINTING & OFFICE SUPPLIES', '400279', NULL, 'COR. BONIFACIO & OSMENA STS., ORMOC CITY', NULL, NULL, NULL, '187-361-822-0000', 'NV', NULL, NULL, NULL, NULL),
(280, 'TWIN-A ENTERPRISES', '400280', NULL, '4-B D. EMILIA BLDG. COR OSMENA BLVD & LAPU-LAPU ST., CEBU CIT', NULL, NULL, NULL, '137-029-186-000', 'N/A', NULL, NULL, NULL, NULL),
(281, 'WELD INDUSTRIAL SALES', '400281', NULL, 'Z2-409 TANGKE TALISAY CITY', NULL, NULL, NULL, '224-221-750-000', 'N/A', NULL, NULL, NULL, NULL),
(282, 'CL ENTERPRISES & ELECTRICAL SUPPLY', '400282', NULL, 'BRGY. LIBERTAD, ORMOC CITY', NULL, NULL, NULL, '164-817-106-0000', 'N/A', NULL, NULL, NULL, NULL),
(283, 'COMPRESSAIR CENTER, INC.', '400283', NULL, 'GRAND ARCADE BLDG. A.C. CORTES AVE., IBABAO, MANDAUE CITY', NULL, NULL, NULL, '220-842-777-0000', '12%', NULL, NULL, NULL, NULL),
(284, 'JELSON ENG\'G & IND\'L EXPONENT', '400284', NULL, 'GROUND FLOOR GEOCADIN BLDG. #41 MABINI ST., B.C.', NULL, NULL, NULL, '077-100-185-251', 'N/A', NULL, NULL, NULL, NULL),
(285, 'PERTIAN INDUSTRIES CORPORATION', '400285', NULL, 'DON SERGIO SUICO ST., UPPER BRGY., TINGUB, MANDAUE CITY', NULL, NULL, NULL, '210-927-899-000', '12%', NULL, NULL, NULL, NULL),
(286, 'THE HIVE ALL VISUAL & LIGHTS SYS. CORP.', '400286', NULL, 'UNIT 11 UPPER GROUD FLOOR BANILAD TOWN CENTER, CEBU CITY', NULL, NULL, NULL, '242-225-753-001', 'N/A', NULL, NULL, NULL, NULL),
(287, 'SIMPLEX INDUSTRIAL CORPORATION', '400287', NULL, '154 LOPEZ JAENA ST. SUBANGDAKU, MANDAUE CITY', NULL, NULL, NULL, '200-871-429-003', 'N/A', NULL, NULL, NULL, NULL),
(288, 'BNC INDUSTRIAL PRODUCTS ENTERPRISES', '400288', NULL, 'LUISVILLE DRIVE, MACABUG, ORMOC CITY', NULL, NULL, NULL, '186-764-393-0000', 'N/A', NULL, NULL, NULL, NULL),
(289, 'TOOLEC, INCORPORATED', '400289', NULL, 'BRIDGEVIEW BLDG. 171 E. ROD JR AVE, LIBIS, Q.C.', NULL, NULL, NULL, '000-061-517-000', 'N/A', NULL, NULL, NULL, NULL),
(290, 'PANTOJA HEAVY EQUIPT. PARTS', '400290', NULL, 'PITOGO, CONSOLACION, CEBU CITY', NULL, NULL, NULL, '103-788-124', 'NV', NULL, NULL, NULL, NULL),
(291, 'SERVICE CENTER NEWLONG, CORP.', '400291', NULL, 'S.B. CABAHUG ST., BO. IBABAO, MANDAUE CITY, CEBU', NULL, NULL, NULL, '230-390-163-002', 'N/A', NULL, NULL, NULL, NULL),
(292, 'ITOCHU ENTERPRISES', '400292', NULL, '778 LOPEZ JAENA ST., ORMOC CITY', NULL, NULL, NULL, '156-708-886-000', 'N/A', NULL, NULL, NULL, NULL),
(293, 'MOM\'S MARKETING', '400293', NULL, 'COR. REAL & AVILES STREETS, ORMOC CITY', NULL, NULL, NULL, '101-721-128-000', '12%', NULL, NULL, NULL, NULL),
(294, 'BENGAR INDUSTRIAL CORPORATION', '400294', NULL, '207 BIAK-NA-BATO ST. COR DAGOT ST. BGY. MANRESA, QUEZON CITY', NULL, NULL, NULL, '000-404-591-0000', '12%', '(632) 365-1557 TO 59', '(632) 361-8224 ; 361-7216', NULL, NULL),
(295, 'HUB CONCEPTS', '400295', NULL, 'PDCI BKDG. 48 J. RIZAL ST., ORMOC CITY, LEYTE', NULL, NULL, NULL, '181-483-754-000', 'NV', NULL, NULL, NULL, NULL),
(296, 'PETSS, INC.', '400296', NULL, 'SUITE 1505 CITYLAND TOWER 2 SALCEDO VILLAGE, MAKATI CITY', NULL, NULL, NULL, '005-025-933-000', 'N/A', NULL, NULL, NULL, NULL),
(297, 'JHONFIL TRADING', '400297', NULL, 'PUROK 2, NANGKA, CONSOLACION CEBU', NULL, NULL, NULL, '901-654-930-000', '12%', NULL, NULL, 'PROP.: JUVY N. TIZON', NULL),
(298, 'RBCJ PHARMACY', '400298', NULL, 'RF BUILDING, MARASBARAS TACLOBAN CITY, LEYTE', NULL, NULL, NULL, '206-416-136-001', '12%', NULL, NULL, NULL, NULL),
(299, 'JAVEZ INDUSTRIAL TRADING', '400299', NULL, 'GALO ST., SOLAR BLOCK AVE., BRGY. 23, BACOLOD CITY', NULL, NULL, NULL, '124-724-108-0000', 'N/A', NULL, '(034) 435-2070', 'PROP.: JACINTO B. ARAÑEZ JR.', NULL),
(300, 'ASSAB PACIFIC PTE. LTD.', '400300', NULL, 'LIIP, MAMPLASAN, BIÑAN, LAGUNA', NULL, NULL, NULL, '249-085-436-0000', 'N/A', NULL, NULL, NULL, NULL),
(301, 'GIBROSEN GENERAL MERCHANDISE', '400301', NULL, '414 CARLOS TAN STREET ORMOC CITY', NULL, NULL, NULL, '905-776-328-000', '12%', NULL, NULL, NULL, NULL),
(302, 'LIBCAP MARKETING CORPORATION', '400302', NULL, 'ALTA TIERRA VILLAGE, JARO, ILOILO CITY', NULL, NULL, NULL, '000-371-635-000', 'N/A', NULL, NULL, NULL, NULL),
(303, 'ALLAN\'S AUTO PARTS', '400303', NULL, 'SAN VIDAL STREET ORMOC CITY', NULL, NULL, NULL, '910-578-155-001', 'NV', NULL, NULL, NULL, NULL),
(304, 'KRYPTON INDUSTRIAL RESOURCES, CO.', '400304', NULL, 'KRYPTON BLDG.., M.C. BRIONES ST., HI-WAY TIPOLO, MANDAUE CITY, CEBU', NULL, NULL, NULL, '222-367-370-000', '12%', '(032) 345-2383', '(032) 345-3374', NULL, NULL),
(305, 'VILLAR ELECTRO-MECHANICAL & ELECTRONICS REPAIR SERVICES', '400305', NULL, '37 LOPEZ JAENA STREET ORMOC CITY', NULL, NULL, NULL, '134-621-097-000', 'NV', NULL, NULL, NULL, NULL),
(306, 'E-DIGITAL TECHNOLOGIES', '400306', NULL, 'G/F ALICE MAR BLDG. BONIFACIO ST. ORMOC CITY', NULL, NULL, NULL, '919-702-653-000', 'NV', NULL, NULL, NULL, NULL),
(307, 'ENGINEERED SOLUTIONS INTERNATIONAL CORP.', '400307', NULL, '5 GUADA SANCHEZ BF RESORTS VILLAGE LAS PIÑAS CITY', NULL, NULL, NULL, '007-007-815-000', 'N/A', NULL, NULL, NULL, NULL),
(308, 'GSC MEDICAL DISTRIBUTOR', '400308', NULL, '163 REAL ST. TACLOBAN CITY', NULL, NULL, NULL, '121-235-453-000', 'N/A', NULL, '(053) 321-6310', 'PINKY', NULL),
(309, 'CASTALLOY TECHNOLOGY CORPORATION', '400309', NULL, 'CEBU CITY', NULL, NULL, NULL, '000-067-031-0000', 'N/A', NULL, NULL, NULL, NULL),
(310, 'ERICKSON CYCLE CENTER', '400310', NULL, 'COR. OSMEÑA & RIZAL STS., ORMOC CITY', NULL, NULL, NULL, '166-548-835-000', '12%', NULL, NULL, 'PROP.: EBENEZER R. ONG', NULL),
(311, 'ITC MOTORPARTS', '400311', NULL, NULL, NULL, NULL, NULL, '157-726-291-000', 'N/A', NULL, NULL, NULL, NULL),
(312, 'MANILA SPRING FABRICATORS & MANUFACTURING', '400312', NULL, 'B-6 L-1 Santol St. Admiral Village, Talon 3, Las Pinas City - 1740', NULL, NULL, NULL, '136-872-883-000', '12%', NULL, NULL, NULL, NULL),
(313, 'MAP AUTO SHOP', '400313', NULL, NULL, NULL, NULL, NULL, '930-216-049', 'N/A', NULL, NULL, NULL, NULL),
(314, 'NEW ORIENTAL DISTRIBUTOR', '400314', NULL, 'REAL ST. ORMOC CITY', NULL, NULL, NULL, '101-720-959-000', '12%', NULL, NULL, 'PROP-RICARDO E. DEIPARINE', NULL);
INSERT INTO `vendors` (`id`, `vendor_name`, `vendor_code`, `account_number`, `vendor_address`, `contact_number`, `email`, `terms`, `tin`, `tax_type`, `tel_no`, `fax_no`, `notes`, `item_type`) VALUES
(315, 'CUETAL AUTO PARTS & SERVICE CENTER', '400315', NULL, 'DOMINGO VELOSO ST., COGON ORMOC CITY', NULL, NULL, NULL, '108-518-617-0000', '12%', NULL, 'T-561-6006', NULL, NULL),
(316, 'R.L. MOLINA\'S MFTG & IRON WORKS DISP CTR', '400316', NULL, 'REAL ST. ORMOC CITY', NULL, NULL, NULL, '183-874-691-000', 'N/A', NULL, NULL, NULL, NULL),
(317, 'D&G RAINBOW PRINTING PRESS', '400317', NULL, 'B7 L60 RAFI TAMBULILID, ORMOC CITY', NULL, NULL, NULL, '941-974-773-000', '12%', '561-5723;561-5349', NULL, 'GRACE F. WALKER - PROP.', NULL),
(318, 'MEGA SIX CORPORATION', '400318', NULL, 'EL BURGOS BLDG. REAL ST. ORMOC CITY', NULL, NULL, NULL, '005-759-260-0000', '12%', NULL, 'TELNO.561-8985', NULL, NULL),
(319, 'PANAY MINERAL PRODUCT RESOURCES CORP.', '400319', NULL, 'N SUSANA EXECUTIVE VILLAGE COMMONWEALTH AVE, Q.C.', NULL, NULL, NULL, '006-231-251-000', 'N/A', NULL, NULL, NULL, NULL),
(320, 'PLATINUM INT\'L SUPPLY & SERVICES, INC.', '400320', NULL, '7648 DELA ROSA ST., BRGY. PIO DEL PILAR MAKATI CITY, MANILA', NULL, NULL, NULL, '000-137-110-000', '12%', NULL, NULL, NULL, NULL),
(321, 'H.T. MINING PRODUCTS RESOURCES CORP.', '400321', NULL, 'BRGY. TINANDONG ATIMONAN QUEZON PROVINCE, PHILS', NULL, NULL, NULL, '007-129-084-000', 'N/A', NULL, NULL, NULL, NULL),
(322, 'FLOR STORE', '400322', NULL, 'SABANG BEACH ORMOC CITY', NULL, NULL, NULL, '164-418-816-000', 'NV', NULL, NULL, NULL, NULL),
(323, 'RL APPLIANCE, INC.', '400323', NULL, 'REAL ST. ORMOC CITY', NULL, NULL, NULL, '005-459-648-000', '12%', NULL, NULL, NULL, NULL),
(324, 'LL LIME CORPORATION', '400324', NULL, '73 LACSON ST., MANDALAGAN BACOLOD CITY', NULL, NULL, NULL, '000-425-379-0000', 'N/A', NULL, NULL, NULL, NULL),
(325, 'GRAN TORINO AUTO SUPPLY', '400325', NULL, 'F. ABLEN ST., ORMOC CITY', NULL, NULL, NULL, '305-411-588-000', '12%', NULL, NULL, 'KEATH MARYBETH C. CABAHUG-PROP.', NULL),
(326, 'BUSCO SUGAR MILLING CO., INC.', '400326', NULL, 'QUEZON BUKIDNON', NULL, NULL, NULL, '000-273-544-0000', 'N/A', NULL, NULL, NULL, NULL),
(327, 'SEAWISE MARITIME & SERVICES, INC.', '400327', NULL, 'RM 201 GMT BLDG. P. DEL ROSARIO COR. JUNQUERA STS. CEBU CITY', NULL, NULL, NULL, '005-691-385-000', '12%', NULL, NULL, NULL, NULL),
(328, 'GLENWOOD TECHNOLOGIES INTERNATIONAL, INC.', '400328', NULL, 'EISENHOWER ST., GREENHILLS SAN JUAN CITY, MANILA', NULL, NULL, NULL, '004-818-764-000', 'N/A', NULL, NULL, NULL, NULL),
(329, 'ALLAN NEIS', '400329', NULL, 'PUBLIC MARKET KANANGA, LEYTE', NULL, NULL, NULL, '104-748-062-000', 'NV', NULL, NULL, NULL, NULL),
(330, 'RAM TYRES INC.', '400330', NULL, 'CASUNTINGAN, MAGUIKAY MANDAUE CITY', NULL, NULL, NULL, '001-692-356-000', 'N/A', NULL, NULL, NULL, NULL),
(331, 'NEW IMAGE MULTI-LINE IND\'L RESOURCES', '400331', NULL, '1080 CEDLEX COMPLEX CHINO ROCES MAKATI CITY, MANILA', NULL, NULL, NULL, '227-223-700-000', 'N/A', NULL, NULL, NULL, NULL),
(332, 'SONIA PORCARE                      ECVencilao\'s TIN# =', '400332', NULL, 'ORMOC CITY', NULL, NULL, NULL, '104-749-215-000', 'N/A', NULL, NULL, NULL, NULL),
(333, 'TRACKSTAR HYDRAULIC SUPPLY, INC.', '400333', NULL, '287 RIZAL AVE COR 4TH AVE GRACE PARK, CALOOCAN CITY', NULL, NULL, NULL, '219-562-631-000', 'N/A', NULL, NULL, NULL, NULL),
(334, 'RBER INDUSTRIAL & TRADING CORPORATION', '400334', NULL, 'M. CENIZA ST., CASUNTINGAN, MANDAUE CITY, CEBU', NULL, NULL, NULL, '218-122-089-000', '12%', '(032) 343-7275', '(032) 328-0030', NULL, NULL),
(335, 'KIMIKA INDUSTRIAL CORP.', '400335', NULL, '1857 MARIA OROSA ST. 698 MALATE, MANILA', NULL, NULL, NULL, '000-344-617-000', 'N/A', NULL, NULL, NULL, NULL),
(336, 'DAKAY ESTATES DEVELOPMENT INC.', '400336', NULL, 'CONSOLACION DALAGUETE, CEBU', NULL, NULL, NULL, '205-428-019-000', 'N/A', NULL, NULL, NULL, NULL),
(337, 'MICRO-BIOLOGICAL LABORATORY, INC.', '400337', NULL, '2636 TRAMO LINE COR ALVAREZ ST PASAY CITY, MANILA', NULL, NULL, NULL, '000-302-958-000', 'N/A', NULL, NULL, NULL, NULL),
(338, 'NDMC ENTERPRISES', '400338', NULL, '329 LILIA AVE., COGON, ORMOC CITY', NULL, NULL, NULL, '243-258-375-000', 'NV', NULL, NULL, NULL, NULL),
(339, 'DM CODILLA ENTERPRISES, INC.', '400339', NULL, NULL, NULL, NULL, NULL, '005-761-728-000', 'N/A', NULL, NULL, NULL, NULL),
(340, 'PROLINE AUTO PARTS ENTERPRISES', '400340', NULL, NULL, NULL, NULL, NULL, '919-700-685-000', 'NV', NULL, NULL, NULL, NULL),
(341, 'SAMMYR SHOES', '400341', NULL, NULL, NULL, NULL, NULL, '168-300-201-000', 'N/A', NULL, NULL, NULL, NULL),
(342, 'EXCEL TRENDS MARKETING & SERVICES CORP.', '400342', NULL, NULL, NULL, NULL, NULL, '232-889-262-0000', '12%', NULL, NULL, NULL, NULL),
(343, 'SAN-VIC TRADERS, INC.', '400343', NULL, 'YATI, LILO-AN, CEBU, PHILIPPINES', NULL, NULL, NULL, '002-457-300-0000', '12%', NULL, NULL, NULL, NULL),
(344, 'RJR SAFETY TREND TRADING CORPORATION', '400344', NULL, NULL, NULL, NULL, NULL, '287-479-893-000', 'N/A', NULL, NULL, NULL, NULL),
(345, 'AGA GLASS & ALUMINUM', '400345', NULL, NULL, NULL, NULL, NULL, '167-211-323-000', 'NV', NULL, NULL, NULL, NULL),
(346, 'AFRODENCIO PACE', '400346', NULL, NULL, NULL, NULL, NULL, '151-585-664-000', 'N/A', NULL, NULL, NULL, NULL),
(347, 'BUENAVENTURA CAPUTOL', '400347', NULL, NULL, NULL, NULL, NULL, '114-061-023-0000', 'N/A', NULL, NULL, NULL, NULL),
(348, 'HYDRAUKING INDUSTRIAL CORPORATION', '400348', NULL, NULL, NULL, NULL, NULL, '206-412-107-000', 'N/A', NULL, NULL, NULL, NULL),
(349, 'LINDE PHILIPPINES (SOUTH), INC.', '400349', NULL, 'NATIONAL HIGHWAY, BRGY. DAYHAGAN ORMOC CITY', NULL, NULL, NULL, '002-396-529-012', '12%', NULL, '561-1243', NULL, NULL),
(350, 'LEGAZPI TIREWORLD CORP.', '400350', NULL, NULL, NULL, NULL, NULL, '005-768-507-000', '12%', NULL, NULL, NULL, NULL),
(351, 'ABC HYDRAULIC ENTERPRISES', '400351', NULL, 'LOOC, LINAO ORMOC CITY', NULL, NULL, NULL, '148-618-729-000', '12%', NULL, NULL, NULL, NULL),
(352, 'ORMOC PC SPECIALIST', '400352', NULL, 'SUPERDOME ORMOC CITY', NULL, NULL, NULL, '934-853-913-000', '12%', NULL, NULL, NULL, NULL),
(353, 'KJS INDUSTRIAL SOLUTIONS, INC.', '400353', NULL, '342 V. ALBAÑO ST., BAKILID II MANDAUE CITY', NULL, NULL, NULL, '234-964-006-000', '12%', '(032) 520-3205', '(032) 238-9934', NULL, NULL),
(354, 'FAST AUTOWORLD PHILIPPINES CORPORATION', '400354', NULL, 'BRGY. BANTIGUE, ORMOC CITY', NULL, NULL, NULL, '000-067-151-0010', '12%', NULL, NULL, NULL, NULL),
(355, 'ANDRES CRUZ', '400355', NULL, NULL, NULL, NULL, NULL, '147-999-334-000', 'NV', NULL, NULL, NULL, NULL),
(356, 'TIRE AVENUE', '400356', NULL, 'ORMOC CITY', NULL, NULL, NULL, '005-759-639-001', '12%', NULL, NULL, 'OPERATED BY: ORMOC BAY RESOURCES CORP.', NULL),
(357, 'MOBILECT POWER CORPORATION', '400357', NULL, NULL, NULL, NULL, NULL, '226-790-468-000', 'N/A', NULL, NULL, NULL, NULL),
(358, 'CESAR FORMENTERA', '400358', NULL, NULL, NULL, NULL, NULL, '164-418-816-0000', 'NV', NULL, NULL, NULL, NULL),
(359, 'VICTORIAS MILLING CORPORATION', '400359', NULL, NULL, NULL, NULL, NULL, '000-270-220-000', 'N/A', NULL, NULL, NULL, NULL),
(360, 'TDB ENTERPRISES', '400360', NULL, NULL, NULL, NULL, NULL, '138-816-297-001', 'N/A', NULL, NULL, NULL, NULL),
(361, 'NEXUS INDUSTRIAL PRIME SOLUTIONS CORPORATION', '400361', NULL, NULL, NULL, NULL, NULL, '008-037-342-0000', '12%', NULL, NULL, NULL, NULL),
(362, 'NEW BRIDGE ELECTRICAL ENTERPRISES', '400362', NULL, 'MANDAUE CITY', NULL, NULL, NULL, '006-265-903-0001', '12%', '(032) 420-3938', NULL, NULL, NULL),
(363, 'DM TRADING & INDUSTRIAL SERVICES, INC.', '400363', NULL, '3290 DUNGON ST., UNITED PARANAQUE CITY, PHILS.', NULL, NULL, NULL, '000-413-079-001', '12%', NULL, '823-0652', NULL, NULL),
(364, 'TOP\'S TEXTILES & TAILORING', '400364', NULL, NULL, NULL, NULL, NULL, '101-719-500-000', '12%', NULL, NULL, NULL, NULL),
(365, 'SHOPKO MARKETING CORPORATION', '400365', NULL, 'LOPEZ JAENA STREET ORMOC CITY', NULL, NULL, NULL, '270-810-941-0000', '12%', NULL, 'TEL#255-4066', NULL, NULL),
(366, 'BRODETH MARKETING', '400366', NULL, 'COR. J. LUNA & BURGOS STS., SABANG ORMOC CITY', NULL, NULL, NULL, '005-355-070-005', '12%', NULL, NULL, NULL, NULL),
(367, 'CEBU TRISTAR CORPORATION', '400367', NULL, '168 OUANO AVE., NORTH RECLAMATION AREA, SUBANGDAKU, MANDAUE CITY, CEBU', NULL, NULL, NULL, '000-256-902-0000', '12%', '346-0560', '346-4907', NULL, NULL),
(368, 'ECE MARKETING', '400368', NULL, 'CALANIPAWAN DIVERSION ROAD BRGY. 96 TACLOBAN CITY', NULL, NULL, NULL, '155-451-157-0009', '12%', '(053) 321-9934', NULL, 'PROP. ALELI C. BISNAR', NULL),
(369, 'EDSUERT ENTERPRISES', '400369', NULL, '728-D SAN ROQUE ST., MAMBALING CEBU CITY', NULL, NULL, NULL, '118-928-079-0000', 'NV', NULL, NULL, NULL, NULL),
(370, 'PANY HYDROTECH AUTOMATION', '400370', NULL, 'BASAK LAPU-LAPU CITY', NULL, NULL, NULL, '169-924-121-0000', '12%', '0932-427-5357', NULL, 'PROP. PANCRESIA Y. CANDAZA', NULL),
(371, 'CIRCUIT 2 ENTERPRISES', '400371', NULL, 'BLK 4, LOT 9, PHASE 5 STA. MARIA SUB. BANABA, SAN MATEO, RIZAL', NULL, NULL, NULL, '134-519-142-0000', '12%', '942-3806', NULL, NULL, NULL),
(372, 'ADVANCE AUTO AND TRUCK PARTS', '400372', NULL, 'BRGY. ALEGREA ORMOC CITY', NULL, NULL, NULL, '477-751-176-0001', '12%', NULL, NULL, 'PROP.: DANTE S. CATINGUB', NULL),
(373, 'MEGA INDUSTRIAL SALES AND INSTALLATION SERVICES', '400373', NULL, 'UNIT 3 KRYSTAL MALL, SAN ISIDRO, CITY OF TALISAY CEBU', NULL, NULL, NULL, '425-286-599-0000', '12%', NULL, NULL, 'PROP. SHEILA L. PARDILLO', NULL),
(374, 'SL RUIZ FURNITURE', '400374', NULL, 'SAN VIDAL ST. ORMOC CITY', NULL, NULL, NULL, '414-858-922-0003', '12%', '561-2112', NULL, NULL, NULL),
(375, 'TURBO CHEM ENTERPRISES', '400375', NULL, '187 COMBADO BRGY. COGON, ORMOC CITY', NULL, NULL, NULL, '186-753-039-0000', '12%', '561-9141', NULL, 'PROP.: EGLESCIANO S. BERESO', NULL),
(376, 'WHITE DRAGON CONSTRUCTION ENTERPRISES', '400376', NULL, 'BRGY. CAMBALADING ALBUERA LEYTE', NULL, NULL, NULL, '937-638-530-0000', 'NV', '562-9475 / 562-9424', NULL, 'PROP.: EBCAS, MARIA JUDITH SUPLICO', NULL),
(377, 'AAC SAND & GRAVEL & CONSTRUCTION SUPPLY', '400377', NULL, 'BRGY. SALVACION ALBUERA LEYTE', NULL, NULL, NULL, '305-411-588-0003', '12%', NULL, NULL, 'PROP.: KEATH MARYBETH C. CABAHUG', NULL),
(378, 'ORMOC SUPER SHELL SERVICE STATION', '400378', NULL, 'LILIA AVE.,  COGON, ORMOC CITY', NULL, NULL, NULL, '005-759-639-0000', '12%', NULL, NULL, 'OPERATED BY: ORMOC BAY RESOURCES CORP.', NULL),
(379, 'CORRTECH, INC.', '400379', NULL, 'UNIT TF-03 CHATSWOOD AVE., A.S. FORTUNA ST., BANILAD, MANDAUE CITY', NULL, NULL, NULL, '207-803-365-0000', '12%', NULL, NULL, 'CONCRETE RESTORATION AND REPAIR TECHNOLOGY, INC.', NULL),
(380, 'A.L. GANTUANGCO ENTERPRISES', '400380', NULL, 'BRGY. VALENCIA, ORMOC CITY', NULL, NULL, NULL, '187-364-251-0002', '12%', NULL, NULL, 'PROP. ANNABELLE L. GANTUANGCO', NULL),
(381, 'HIGH PRECISION MARKETING', '400381', NULL, 'REAL ST., ORMOC CITY', NULL, NULL, NULL, '925-726-728-0000', '12%', NULL, NULL, 'PROP.: LESLIE ANNE U. CHU', NULL),
(382, 'NEDO\'S GLASS AND ALUMINUM FABRICATOR', '400382', NULL, 'AVILES STREET ORMOC CITY', NULL, NULL, NULL, '928-642-940-0000', 'NV', '0915-352-6656', NULL, 'PROP.:NEDO E. LABARTINE', NULL),
(383, 'VENCILAO, EDGARDO', '400383', NULL, 'KANANGA, LEYTE', NULL, NULL, NULL, '104-749-215-0000', 'NV', NULL, NULL, NULL, NULL),
(384, 'CEBU METROLOGYX TECHNICAL SERVICES', '400384', NULL, 'MANDAUE CITY', NULL, NULL, NULL, '175-767-452-0000', 'NV', '420-3848', NULL, 'PROP.: JOPRIZ A. ZAMORA', NULL),
(385, 'BP SIMBAJON', '400385', NULL, 'LOPEZ JAENA STREET ORMOC CITY', NULL, NULL, NULL, '920-846-482-0000', 'NV', '255-5499/561-3323', '0999-591-9883', 'PROP.: BEMBE P. SIMBAJON', NULL),
(386, 'COSMOTEC ENTERPRISES', '400386', NULL, '1 ZUZUAREGUI ST. COMMONWEALTH AVE, MATANDANG BALARA 3 QUEZON CITY', NULL, NULL, NULL, '299-322-081-0000', '12%', NULL, NULL, 'PROP. PETER ALDEN T. AZANZA', NULL),
(387, 'RNUABLE ENERGY CORPORATION', '400387', NULL, 'RNU COMPOUND, DOLORES HIGHWAY ORMOC CITY', NULL, NULL, NULL, '007-301-056-0000', '12%', '(632) 985-0451', '(6353) 561-0569', NULL, NULL),
(388, 'YANA CHEMODITIES, INC.', '400388', NULL, 'MANDAUE RECLAMATION AREA, 2ND ST., AFTER OUANO AVE. MANDAUE CITY, CEBU', NULL, NULL, NULL, '003-584-182-000', '12%', '346-1836;346-8141;346-8031', '346-0879', NULL, NULL),
(389, 'ICI SYSTEM, INC.', '400389', NULL, '14/F BELVEDERE TOWER, 15 SAN MIGUEL AVE. ORTIGAS CENTER, BRGY. SAN ANTONIO, PASIG CITY', NULL, NULL, NULL, '000-125-584-0000', '12%', '637-8577 TO 80, 633-7838', '633-5127', NULL, NULL),
(390, 'JUANIZA INDUSTRIAL SUPPLY, INC.', '400390', NULL, 'A-5 SANTIAGO ARCADE, M.V. PATALINGHUG AVENUE, BRGY. PAJO, LAPU-LAPU CITY', NULL, NULL, NULL, '422-150-561-0000', '12%', NULL, NULL, NULL, NULL),
(391, 'SAMUEL C. QUINDAO', '400391', NULL, 'ORMOC CITY', NULL, NULL, NULL, '104-748-569-0000', 'NV', NULL, NULL, NULL, NULL),
(392, 'HI-TECH MOTOR PARTS CORPORATION', '400392', NULL, 'CMC CENTER, REAL STREET, DISTRICT 21 ORMOC CITY', NULL, NULL, NULL, '458-519-911-0000', '12%', '(053) 561-7964', NULL, NULL, NULL),
(393, 'BOSJAN MAIN MARKETING CORPORATION', '400393', NULL, 'OSMEÑA STREET ORMOC CITY', NULL, NULL, NULL, '436-949-468-0001', '12%', NULL, NULL, NULL, NULL),
(394, 'MORSE TECHPRO SPECIALIST CORPORATION', '400394', NULL, '171 SB CABAHUG ST., IBABAO MANDAUE CITY, CEBU', NULL, NULL, NULL, '283-445-806-002', '12%', NULL, NULL, NULL, NULL),
(395, 'DCV INDUSTRIAL CONTROLS ENTERPRISES', '400395', NULL, 'BRGY COGON ORMOC CITY', NULL, NULL, NULL, '103-424-717-0001', '12%', NULL, NULL, 'BIR FORM 2303 ATTACHED TO APV#0620038-6/23/20', NULL),
(396, 'RDS REWINDING AND BATTERY SHOP (ALLAN R. DECIO)', '400396', NULL, 'LILIA AVE., BRGY. COGON ORMOC CITY', NULL, NULL, NULL, '290-480-700-000', 'NV', NULL, NULL, 'PROP.: URBANO V. DECIO', NULL),
(397, 'GENERIKA OR TEROGUEV CORPORATION', '400397', NULL, 'ROOM 203 K&J BLDG. J. LLORENTE ST. CAPITOL SITE CEBU CITY 6000', NULL, NULL, NULL, '427-786-594-0004', '12%', '254-0462/0932-890-5573', NULL, 'MEDICINES - GENERIKA DRUGSTORE', NULL),
(398, 'DS CATINGUB BROS. CORPORATION', '400398', NULL, 'PUROK 4, BRGY. BANTIGUE ORMOC CITY', NULL, NULL, NULL, '477-751-176-0000', '12%', NULL, NULL, NULL, NULL),
(399, 'EBR 4-WHEEL CENTER', '400399', NULL, 'LILIA AVE., COGON ORMOC CITY', NULL, NULL, NULL, '004-305-869-0004', '12%', '561-6441/255-9109', NULL, 'OWNED & OPERATED BY: EBR MARKETING CORPORATION', NULL),
(400, 'G AND I ENTERPRISES', '400400', NULL, 'LILIA AVENUE, BRGY. COGON ORMOC CITY', NULL, NULL, NULL, '941-979-288-0000', 'NV', NULL, NULL, 'PROP.: GODILLO N. LAPINID', NULL),
(401, 'FILHOLLAND CORPORATION', '400401', NULL, 'C/O LCG MARKETING PHILS. OSMEÑA ST., 28 CAGAYAN DE ORO CITY', NULL, NULL, NULL, '276-480-105-0000', '12%', NULL, NULL, NULL, NULL),
(402, 'FORNIX INDUSTRIAL SYSTEMS CORPORATION', '400402', NULL, 'BACK OF PETRON M. PATALINGHUG AVE., SANGI ROAD, PAJO LAPU-LAPU CITY', NULL, NULL, NULL, '480-655-188-0000', '12%', NULL, NULL, NULL, NULL),
(403, 'LIFEQUEST SAFETYPRO VENTURES', '400403', NULL, 'GP BUILDING ML QUEZON ST., CABANCALAN MANDAUE CITY', NULL, NULL, NULL, '915-334-962-0000', '12%', '421-1337', NULL, NULL, NULL),
(404, 'RGSK TRADING', '400404', NULL, '20D-2 VILLAGONZALO II, TEJERO, CEBU CITY', NULL, NULL, NULL, '910-945-307-0000', '12%', '0943-462-5030 / 0927-753-6174', NULL, 'PROP.: RAYMOND A. CHAVEZ', NULL),
(405, 'LDS ALEMARS MARKETING, INC.', '400405', NULL, '30A BONIFACIO ST., ORMOC CITY', NULL, NULL, NULL, '480-876-868-0000', '12%', '255-2285/255-8452', '561-7453', NULL, NULL),
(406, 'MACPREMIUM SUPER GASES, INC.', '400406', NULL, 'PUROK KATAMBISAN, BRGY. SAN ISIDRO ORMOC CITY', NULL, NULL, NULL, '009-408-482-0000', '12%', NULL, NULL, NULL, NULL),
(407, 'DC AGGREGATES AND CONSTRUCTION', '400407', NULL, 'HI-WAY BRGY. BANTIGUE ORMOC CITY', NULL, NULL, NULL, '477-751-176-0006', '12%', NULL, NULL, 'PROP.: DS CATINGUB BROS. CORPORATION', NULL),
(408, 'KRYPTON INTERNATIONAL RESOURCES & SALES SERVICES, INC.', '400408', NULL, 'KRYPTON BLDG.., M.C. BRIONES ST., TIPOLO, MANDAUE CITY', NULL, NULL, NULL, '469-968-495-0000', '12%', NULL, NULL, NULL, NULL),
(409, 'CONTINENTAL PRINTERS, INC.', '400409', NULL, '23-A TRES BORCES PADRES STREET, MABOLO, CEBU CITY', NULL, NULL, NULL, '000-314-637-0000', '12%', NULL, NULL, NULL, NULL),
(410, 'POWERFLEX HYDRAULIC HOSE RETAILER', '400410', NULL, 'LILIA AVENUE BRGY. COGON ORMOC CITY', NULL, NULL, NULL, '216-289-970-0003', 'NV', NULL, NULL, 'PROP.: REYNALDO R. AGUIRRE JR.', NULL),
(411, 'ORMOC ALUMINUM CONCEPTS CORPORATION', '400411', NULL, 'COGON ORMOC CITY', NULL, NULL, NULL, '472-970-380-0000', '12%', NULL, NULL, NULL, NULL),
(412, 'PC TOOLS COMPUTER SERVICES MULTI-PURPOSE COOPERATIVE', '400412', NULL, 'G/F ALICE MAR BLDG. BONIFACIO ST. ORMOC CITY', NULL, NULL, NULL, '259-087-346-0000', '12%', '561-7236 / 255-7282', NULL, 'PAYEE: PC TOOLS MPC (as per LSO 03.20.2017)', NULL),
(413, 'INNOVATIVE CONTROLS, INC.', '400413', NULL, 'BACOOR CITY CAVITE', NULL, NULL, NULL, '210-264-194-0000', '12%', NULL, NULL, NULL, NULL),
(414, 'ULTRA TECH ELECTRO-MECHANICAL EQUIPMENT SALES CORPORATION', '400414', NULL, 'LOT 9 BLK 4, STA. MARIA SUBD. PH 5 ST. JOHN ST. BRGY. BANABA SAN MATEO, RIZAL', NULL, NULL, NULL, '009-323-278-0000', '12%', NULL, NULL, NULL, NULL),
(415, 'KY RUBBER & INDUSTRIAL SALES CO.', '400415', NULL, 'CEBU CITY', NULL, NULL, NULL, '454-156-110-0000', '12%', NULL, NULL, NULL, NULL),
(416, 'THE PERTMACHINERY COMPANY, INC.', '400416', NULL, 'CEBU CITY', NULL, NULL, NULL, '420-997-922-0000', '12%', NULL, NULL, NULL, NULL),
(417, 'PRESIDIUM CONTROLS & INDUSTRIAL TECHNOLOGIES CORP.', '400417', NULL, '3RD FLOOR DISPO BLDG., A.C. CORTES MANDAUE CITY', NULL, NULL, NULL, '224-434-089-0000', '12%', NULL, NULL, NULL, NULL),
(418, 'PROCESS INNOVATIONS INCORPORATED', '400418', NULL, 'RM. M-01 NORTH ROAD PLAZA BLDG., LABOGON MANDAUE CITY', NULL, NULL, NULL, '004-844-912-0001', '12%', NULL, NULL, NULL, NULL),
(419, 'STEAM TURBINE REPAIR & SERVICES, INC.', '400419', NULL, '19 MAXIMO FLORES ST., CANIOGAN, PASIG CITY, PHILIPPINES 1606', NULL, NULL, NULL, '000-286-735-0000', '12%', '641-4507', '02-641-7875', NULL, NULL),
(420, 'VCY SALES CORPORATION', '400420', NULL, 'CEBU CITY', NULL, NULL, NULL, '005-430-314-0001', '12%', NULL, NULL, NULL, NULL),
(421, 'FLUID INDUSTRIAL TRADING', '400421', NULL, 'CEBU CITY', NULL, NULL, NULL, '274-086-429-0000', '12%', '0917-566-8302', '232-1129', NULL, NULL),
(422, 'PHILIPPINE NEWLONG CORPORATION', '400422', NULL, 'MANDAUE CITY', NULL, NULL, NULL, '230-390-163-0002', '12%', NULL, NULL, NULL, NULL),
(423, 'GTB INDUSTRIAL NETWORK (TACLOBAN), INC.', '400423', NULL, 'TACLOBAN CITY', NULL, NULL, NULL, '007-576-997-0000', '12%', NULL, NULL, NULL, NULL),
(424, 'COOL TWINS AIRCON SERVICES', '400424', NULL, '565 ORMOC HEIGHTS SUBD. BRGY. SAN ISIDRO, ORMOC CITY', NULL, NULL, NULL, '166-522-287-0000', '12%', NULL, NULL, 'PROP. NANNETE M. SUBINGSUBING', NULL),
(425, 'MARY PEARLY ENTERPRISES', '400425', NULL, 'CAMAGONG ST., PUNTA ORMOC CITY', NULL, NULL, NULL, '945-572-159-0000', 'NV', NULL, NULL, 'PROP.: MARIA LUZ A. ORDINARIO', NULL),
(426, 'DI INDUSTRIAL DEPOT', '400426', NULL, 'ORMOC CITY', NULL, NULL, NULL, '324-594-818-0001', 'NV', NULL, NULL, NULL, NULL),
(427, 'EMBLEM PROCESS INSTRUMENTATION AND CONTROLS CORP.', '400427', NULL, '2166 PRIMO RIVERA ST. LA PAZ MAKATI CITY', NULL, NULL, NULL, '007-179-733-0000', 'N/A', NULL, NULL, NULL, NULL),
(428, 'RS COMPONENTS CORPORATION', '400428', NULL, 'AYALA AVE. MAKATI CITY', NULL, NULL, NULL, '008-449-129-0000', '12%', NULL, NULL, NULL, NULL),
(429, 'ALDO SERVICE CENTER, INC.', '400429', NULL, 'BASAK, MANDAUE CITY NOTE: SUPPLIES VATABLE - JO - VAT EXEMPT', NULL, NULL, NULL, '221-718-919-000', '12%', NULL, NULL, NULL, NULL),
(430, 'EVERDYNAMIC DISTRIBUTION NETWORK (EDDN), INC.', '400430', NULL, 'HERMAN CORTES ST. TIPOLO MANDAUE CITY', NULL, NULL, NULL, '253-441-035-000', '12%', NULL, NULL, NULL, NULL),
(431, 'SLIMTEX INDUSTRIES, INC.', '400431', NULL, '27 N.S. AMORANTO ST. BRGY PAANG BUNDOK QUEZON CITY', NULL, NULL, NULL, '001-399-179-000', '12%', NULL, NULL, NULL, NULL),
(432, 'PHILMASTIC INCORPORATED', '400432', NULL, '15 ATOK ST. BRGY. STO. DOMINGO QUEZON CITY', NULL, NULL, NULL, '008-669-447-001', '12%', NULL, NULL, NULL, NULL),
(433, 'FILZACK INDUSTRIES', '400433', NULL, 'NO.1 DAANG BATO ST. WHSE 3&4 BRGY. LAWANG BAT DIST. 1 VALENZUELA CITY', NULL, NULL, NULL, '006-788-664-000', '12%', NULL, NULL, NULL, NULL),
(434, 'CIFRA MARKETING CORPORATION', '400434', NULL, '4229 GENERAL MOJICA ST., BANGKAL MAKATI CITY', NULL, NULL, NULL, '001-159-593-000', '12%', NULL, NULL, NULL, NULL),
(435, 'SEAN ENGINEERING & TECHNICAL SERVICES', '400435', NULL, 'BRGY. MAYSAN DIST. 2 VALENZUELA CITY', NULL, NULL, NULL, '404-943-637-000', '12%', NULL, NULL, NULL, NULL),
(436, 'TC CHEMICAL ENGINEERING SERVICES', '400436', NULL, 'BRGY DON FELIPE LARRAZABAL ORMOC CITY', NULL, NULL, NULL, '186-753-039-002', '12%', NULL, NULL, NULL, NULL),
(437, 'CIACS INDUSTRIAL MACHINE INSTALLATION SERVICE', '400437', NULL, '697 M.L. QUEZON ST., CASUNTINGAN, MANDAUE CITY, CEBU', NULL, NULL, NULL, '758-033-746-000', '12%', NULL, NULL, NULL, NULL),
(438, 'KENTAN INDUSTRIAL SUPPLY AND SERVICES', '400438', NULL, 'BRGY. STO. NINO, TUGBOK DIST. DAVAO CITY', NULL, NULL, NULL, '222-319-489-000', '12%', NULL, NULL, NULL, NULL),
(439, 'ST. JUDE CVE-AGRICULTURAL MARKETING CORPORATION', '400439', NULL, '2ND FLOOR ST. JUDE BLDG., REAL ST. ORMOC CITY', NULL, NULL, NULL, '009-105-201-000', '12%', NULL, NULL, NULL, NULL),
(440, 'EPIC AUTOMATION EMBLEM PROCESS INSTRUMENTATION AND CONTROLS CORPORATION', '400440', NULL, 'RIVERA ST. LA PAZ MAKATI CITY', NULL, NULL, NULL, '007-179-733-000', '12%', NULL, NULL, NULL, NULL),
(441, 'GANEN INDUSTRIAL SUPPLY & SERVICES', '400441', NULL, 'H.PEPITO ST. POB. OREINTAL,  CONSOLACION, CEBU CITY', NULL, NULL, NULL, '164-149-794-000', '12%', NULL, NULL, NULL, NULL),
(442, 'LNC TRADING', '400442', NULL, 'REVILLA ST. POB. KANANGA LEYTE', NULL, NULL, NULL, '740-365-505-000', 'NV', NULL, NULL, NULL, NULL),
(443, 'TRANS PILIPINAS POWER & AUTOMATION INC.', '400443', NULL, 'Garcia heights, Bacaca Road Brgy. 19 B,  Davao City', NULL, NULL, NULL, '282-196-578-0000', '12%', NULL, NULL, NULL, NULL),
(444, 'JOHANN ENTERPRISES', '400444', NULL, 'BRGY. COGON, ORMOC CITY', NULL, NULL, NULL, '261-321-203-0000', '12%', NULL, NULL, NULL, NULL),
(445, 'GDG AGGREGATES SUPPLY', '400445', NULL, 'BRGY. MASARAYAO, KANANGA LEYTE', NULL, NULL, NULL, '115-334-253-0000', '12%', NULL, NULL, NULL, NULL),
(446, 'ALLEYAH MARIE ENT. & CONST. SERVICES', '400446', NULL, 'SAGKAHAN, BRGY. POBLACION, KANANGA LEYTE', NULL, NULL, NULL, '925-730-160-000', '12%', NULL, NULL, NULL, NULL),
(447, 'JMS FUEL RESOURCES', '400447', NULL, '415 GORORDO AVENUE, LAHUG CEBU CITY', NULL, NULL, NULL, '302-999-549-0000', '12%', NULL, NULL, NULL, NULL),
(448, 'PLATINUM SERVICES & TECHNOLOGIES, INC.', '400448', NULL, 'L. HERNANDEZ AVE., BRGY. ALMANZA UNO, LAS PINAS, CITY', NULL, NULL, NULL, '010-398-561-000', '12%', NULL, NULL, NULL, NULL),
(449, 'ROANCY MARKETING', '400449', NULL, 'LILIA AVENUE BRGY COGON, ORMOC CITY', NULL, NULL, NULL, '940-745-813-000', '12%', NULL, NULL, NULL, NULL),
(450, 'HARNWELL CHEMICALS CORPORATION', '400450', NULL, 'Sikatuna st. corner Bonifacio Day As, Cebu City', NULL, NULL, NULL, '000-163-731-001', '12%', NULL, NULL, NULL, NULL),
(451, 'EXPONENT CONTROL AND ELECTRICAL CORPORATION', '400451', NULL, 'Brgy. San Isisdro, Cainta, Rizla - 1900', NULL, NULL, NULL, '207-620-738-000', '12%', NULL, NULL, NULL, NULL),
(452, 'JMS PRIME SOLUTIONS INC.', '400452', NULL, '415 GORORDO AVENUE, LAHUG CEBU CITY', NULL, NULL, NULL, '600-590-663-000', '12%', NULL, NULL, NULL, NULL),
(453, 'VHM ENGINEERING & ALLIED SERVICES', '400453', NULL, 'Blk.27, Lot 8, Countryhomes Cabantian Buhangin, Davao City', NULL, NULL, NULL, '182-348-668-000', '12%', NULL, NULL, NULL, NULL),
(454, 'KYZ RUBBERTECH RUBBER PRODUCTS TRADING', '400454', NULL, 'Tabulasa St. San Isidro, Talizay City, Cebu', NULL, NULL, NULL, '310-956-592-0000', '12%', NULL, NULL, NULL, NULL),
(455, 'MAYOL ELECTRICAL MECHANICAL SERVICES & SUPPLIES', '400455', NULL, 'D. Jakosalem st., Kamagayan, Cebu City - 6000', NULL, NULL, NULL, '200-258-213-0000', '12%', NULL, NULL, NULL, NULL),
(456, 'EESI MATERIAL AND CONTROLS CORP.', '400456', NULL, 'Room 214 MGA Arcade, A.C. Cortes Avenue, Mandaue City, Cebu', NULL, NULL, NULL, '008-271-432-002', '12%', NULL, NULL, NULL, NULL),
(457, 'NEW INTERLOCK SALES & SERVICES', '400457', NULL, 'Door No.3 NGS Bldg. M.J. Cuenco Ave., Mabolo, Cebu City Philippines, 6000', NULL, NULL, NULL, '179-910-148-000', '12%', NULL, NULL, NULL, NULL),
(458, 'ORMOC EVERGREEN TRADING', '400458', NULL, 'Brgy Cogon, Ormoc City', NULL, NULL, NULL, '919-713-222-0000', '12%', NULL, NULL, NULL, NULL),
(459, 'LABTRADERS, INC.', '400459', NULL, '3f Dayson Bldg., 246 Wireless, Mandaue City, Cebu', NULL, NULL, NULL, '008-306-697-001', '12%', NULL, NULL, NULL, NULL),
(460, 'MAJARI SALES & TECHNICAL SERVICES, INC.', '400460', NULL, 'Carlos Tan St., Brgy. 23 (Pob) Ormoc Ctiy, 6541', NULL, NULL, NULL, '606-072-178-0000', '12%', NULL, NULL, NULL, NULL),
(461, 'CEBU BELMONT, INC.', '400461', NULL, 'GRD FLR, Anjo Bldg. M.C. Briones Highway Maguikay, Mandaue City, 6014', NULL, NULL, NULL, '000-067-311-000', '12%', NULL, NULL, NULL, NULL),
(462, 'INSUPHIL INDUSTRIAL CORPORATION', '400462', NULL, NULL, NULL, NULL, NULL, '000-068-547-000', 'N/A', NULL, NULL, NULL, NULL),
(463, 'MANDAUE FOAM INDUSTRIES INC.', '400463', NULL, 'ORMOC CITY', NULL, NULL, NULL, '000-069-873-019', '12%', NULL, NULL, NULL, NULL),
(464, 'DECO MACHINE SHOP, INC.', '400464', NULL, 'J.P. CABAGUIO AVENUE 8000 DAVAO CITY, PHILIPPINES', NULL, NULL, NULL, '000-075-025-000', '12%', NULL, NULL, NULL, NULL),
(465, 'TRADETON CORPORATION', '400465', NULL, 'METRO MANILA', NULL, NULL, NULL, '000-086-052-0000', '12%', NULL, NULL, NULL, NULL),
(466, 'STAR APPLIANCE CENTER, INC.', '400466', NULL, 'SM Megamall Bldg. Edsa cor. Doña Julia Vargas avenue Wack-Wack Greenhills, City of Mandaluyong NCR, Second District Philippines, 1550', NULL, NULL, NULL, '000-086-204-0000', '12%', NULL, NULL, NULL, NULL),
(467, 'SM APPLIANCES CENTER, INC.', '400467', NULL, 'REAL ST. ORMOC CITY', NULL, NULL, NULL, '000-086-204-0099', '12%', NULL, NULL, NULL, NULL),
(468, 'SGS PHILIPPINES, INC.', '400468', NULL, '2nd FLOOR ALEGRIA BLDG. 2229 CHINO ROCES AVENUE MAKATI CITY PHILIPPINES 1231', NULL, NULL, NULL, '000-135-377-000', '12%', '784-9400', '818-2971', NULL, NULL),
(469, 'ASIAN APPRAISAL COMPANY, INC.', '400469', NULL, 'MAKATI CITY', NULL, NULL, NULL, '000-154-791-0000', '12%', NULL, NULL, 'MAKATI EXPANDED', NULL),
(470, 'MABUHAY VINYL CORPORATION', '400470', NULL, 'CEBU CITY', NULL, NULL, NULL, '000-164-009-0001', '12%', NULL, NULL, NULL, NULL),
(471, 'NEW EAGLE ARRASTRE SERVICES.,INC.', '400471', NULL, NULL, NULL, NULL, NULL, '000-199-042-000', 'N/A', NULL, NULL, NULL, NULL),
(472, 'IÑAKI A. LARRAZABAL', '400472', NULL, 'ORMOC CITY', NULL, NULL, NULL, '000-226-557-0000', 'N/A', NULL, NULL, 'SUGAR PURCHASE', NULL),
(473, 'ORMOC VILLA HOTEL', '400473', NULL, NULL, NULL, NULL, NULL, '000-226-557-002', 'N/A', NULL, NULL, NULL, NULL),
(474, 'OPROCOMA', '400474', NULL, 'ORMOC CITY', NULL, NULL, NULL, '000-226-783-0000', 'NV', NULL, NULL, 'SUGAR PURCHASE', NULL),
(475, 'IMPERIAL APPLIANCE PLAZA', '400475', NULL, 'ORMOC CITY', NULL, NULL, NULL, '000-249-888-026', 'N/A', NULL, NULL, NULL, NULL),
(476, 'DU EK SAM, INC.', '400476', NULL, 'LILIA AVENUE ORMOC CITY', NULL, NULL, NULL, '000-254-247-0071', '12%', NULL, '561-0488 ; 561-7048', NULL, NULL),
(477, 'FOUR ACES TECHNOLOGY CONST., INC.', '400477', NULL, NULL, NULL, NULL, NULL, '000-262-107', 'N/A', NULL, NULL, NULL, NULL),
(478, 'GLEEN MARKETING, INC.', '400478', NULL, NULL, NULL, NULL, NULL, '000-272-335-0002', '12%', NULL, NULL, NULL, NULL),
(479, 'LBC EXPRESS - SMM, INC.', '400479', NULL, NULL, NULL, NULL, NULL, '000-279-156-000', 'N/A', NULL, NULL, NULL, NULL),
(480, 'LBC EXPRESS - NWMM, INC.', '400480', NULL, NULL, NULL, NULL, NULL, '000-279-199-000', 'N/A', NULL, NULL, NULL, NULL),
(481, 'FABCON PHILIPPINES, INC.', '400481', NULL, NULL, NULL, NULL, NULL, '000-280-811-000', '12%', NULL, NULL, NULL, NULL),
(482, 'G. U. ENGINEERING, INC.', '400482', NULL, NULL, NULL, NULL, NULL, '000-296-414-0000', 'N/A', NULL, NULL, NULL, NULL),
(483, 'KUYSEN ENTERPRISES, INC.', '400483', NULL, '236 E.RODRIGUEZ SR. AVE. GALAS QUEZON CITY', NULL, NULL, NULL, '000-300-149-000', '12%', NULL, NULL, NULL, NULL),
(484, 'CEBU DOCTORS\' UNIVERSITY HOSPITAL, INC.', '400484', NULL, 'OSMEÑA Boulevard, Cebu city 6000', NULL, NULL, NULL, '000-309-308-0000', 'NV', NULL, NULL, NULL, NULL),
(485, 'ROSE PHARMACY INC.', '400485', NULL, 'A Mall Bldg. Rizal St., Ormoc City', NULL, NULL, NULL, '000-310-457-185', '12%', NULL, NULL, NULL, NULL),
(486, 'LIM TONG PRESS, INC.', '400486', NULL, 'SERAFIN, BORCES ST. MABOLO, CEBU CITY', NULL, NULL, NULL, '000-311-395-0000', '12%', NULL, NULL, NULL, NULL),
(487, 'ATS CONSOLIDATED (ATSC), INC.', '400487', NULL, '8TH, 11TH FLOOR, TIMES PLAZA BUILDING, U.N. AVE. COR TAFT AVE. MANILA', NULL, NULL, NULL, '000-313-401-000', '12%', NULL, NULL, NULL, NULL),
(488, '2GO GROUP, INC.', '400488', NULL, 'ORMOC CITY', NULL, NULL, NULL, '000-313-401-006', '12%', NULL, NULL, NULL, NULL),
(489, 'CHAM SAMCO & SONS, INC.', '400489', NULL, '500-508 STO. CRISTO ST., MANILA CITY', NULL, NULL, NULL, '000-321-514-0000', '12%', '(02) 243-1561', NULL, 'HAULER / TIN OF RABADON, ALEJANDRO M. JR.', NULL),
(490, 'HI-TOP MERCHANDISING, INC.', '400490', NULL, 'BRGY. 328 STA. CRUZ MANILA CITY', NULL, NULL, NULL, '000-326-480-000', '12%', NULL, NULL, 'BIR FORM 2303 ATTACHED TO CV#0920227-9/25/20', NULL),
(491, 'MACHINEBANKS CORPORATION', '400491', NULL, 'MANILA CITY', NULL, NULL, NULL, '000-326-712-0000', '12%', NULL, NULL, 'BIR FORM 2303 @ MATERIAL FILE FOLDER', NULL),
(492, 'CLK SUPERTOOLS DEPOT INC.', '400492', NULL, '315 DASMARINAS ST BRGY. 290 ZONE 27  BINONDO MANILA 1006', NULL, NULL, NULL, '000-327-109-000', '12%', NULL, NULL, 'BIR FORM 2303 ATTACHED TO CV#1220030-12/4/20', NULL),
(493, 'TIMBERLAND MOTORPARTS CO.', '400493', NULL, '1019 BENAVIDEZ ST. BRGY. 294 ZONE 028 BONONDO, MANILA - 1006', NULL, NULL, NULL, '000-328-770-000', '12%', NULL, NULL, NULL, NULL),
(494, 'EASTMAN INDUSTRIAL SUPPLY, INC.', '400494', NULL, 'Brgy. 297 Zone 29 Dist. III Sta. Cruz Manila', NULL, NULL, NULL, '000-330-188-000', '12%', NULL, NULL, NULL, NULL),
(495, 'EASTMAN INDUSTRIAL SUPPLY, INC.', '400495', NULL, '2182-C2 Daystar Industrial Park, P. Sta. Cruz Sta. Rosa, Laguna, 4026', NULL, NULL, NULL, '000-330-188-006', '12%', NULL, NULL, NULL, NULL),
(496, 'TENSILE GROUP AND COMPANY, INC.', '400496', NULL, '1039-E Benavidez st. Brgy. 294 Zone 28, Binondo NCR, City of Manila, 1006', NULL, NULL, NULL, '000-340-947-000', '12%', NULL, NULL, NULL, NULL),
(497, 'POINTER ENTERPRISES INC.', '400497', NULL, '650-652 CABILDO ST. BRGY 658 ZONE 070 INTRAMUROS MANILA CITY 1002', NULL, NULL, NULL, '000-344-014-000', '12%', NULL, NULL, NULL, NULL),
(498, 'INNOVE COMMUNICATIONS, INC.', '400498', NULL, NULL, NULL, NULL, NULL, '000-360-916-026', '12%', NULL, NULL, NULL, NULL),
(499, 'GUZENT INC.', '400499', NULL, '1237 EDSA A. SAMSON  QUEZON CITY', NULL, NULL, NULL, '000-368-727-000', '12%', NULL, NULL, NULL, NULL),
(500, 'GOLDEN BAT (FAR EAST) INC.', '400500', NULL, '52 SCT Alcaraz, Maharlika 1, Quezon City, 1114', NULL, NULL, NULL, '000-373-371-000', '12%', NULL, NULL, NULL, NULL),
(501, 'RCLL TRADING CORPORATION', '400501', NULL, 'METRO MANILA', NULL, NULL, NULL, '000-399-000-0000', '12%', NULL, NULL, NULL, NULL),
(502, 'AVESCO MARKETING CORP.', '400502', NULL, NULL, NULL, NULL, NULL, '000-400-152-0000', 'N/A', NULL, NULL, NULL, NULL),
(503, 'ATLAS COPCO (PHILS.), INC.', '400503', NULL, 'MANDAUE CITY CEBU', NULL, NULL, NULL, '000-412-606-0000', '12%', NULL, NULL, NULL, NULL),
(504, 'FEDERAL PHOENIX ASSURANCE CO.', '400504', NULL, 'MAKATI CITY', NULL, NULL, NULL, '000-455-062-0000', 'N/A', NULL, NULL, 'MAKATI EXPANDED', NULL),
(505, 'BPI MS INSURANCE', '400505', NULL, 'MAKATI CITY', NULL, NULL, NULL, '000-474-030-0000', 'N/A', NULL, NULL, 'MAKATI EXPANDED', NULL),
(506, 'CHARTER PING AN INSURANCE', '400506', NULL, 'MAKATI CITY', NULL, NULL, NULL, '000-487-306-0000', 'N/A', NULL, NULL, 'MAKATI EXPANDED', NULL),
(507, 'PHILIPPINE PHOSPHATE FERTILIZER CORP.', '400507', NULL, NULL, NULL, NULL, NULL, '000-488-010-000', 'N/A', NULL, NULL, NULL, NULL),
(508, 'PNB GEN. INSURER CO.', '400508', NULL, 'PASAY CITY', NULL, NULL, NULL, '000-547-605-0000', 'N/A', NULL, NULL, 'MAKATI EXPANDED', NULL),
(509, 'KEN TOOL HARDWARE CORPORATION', '400509', NULL, 'BRGY. 238 ZONE 22, TONDO MANILA - 1012', NULL, NULL, NULL, '000-627-689-000', '12%', NULL, NULL, NULL, NULL),
(510, 'GLOBE TELECOM, INC.', '400510', NULL, 'BONIFACIO GLOBAL  TAGUIG CITY', NULL, NULL, NULL, '000-768-480-000', '12%', NULL, NULL, NULL, NULL),
(511, 'LBC EXPRESS, INC.', '400511', NULL, NULL, NULL, NULL, NULL, '000-782-140-000', 'N/A', NULL, NULL, NULL, NULL),
(512, 'P & A LARRAZABAL', '400512', NULL, 'ORMOC CITY', NULL, NULL, NULL, '000-799-340-0000', 'N/A', NULL, NULL, 'SUGAR PURCHASE', NULL),
(513, 'MAA GENERAL INSURANCE', '400513', NULL, 'MAKATI CITY', NULL, NULL, NULL, '000-801-332-0000', 'N/A', NULL, NULL, 'MAKATI EXPANDED', NULL),
(514, '2GO EXPRESS', '400514', NULL, 'ORMOC CITY', NULL, NULL, NULL, '000-855-492-0012', '12%', NULL, NULL, NULL, NULL),
(515, 'TAN, JORGE JR.', '400515', NULL, NULL, NULL, NULL, NULL, '000-905-026', 'N/A', NULL, NULL, NULL, NULL),
(516, 'CLINICA GATCHALIAN AND HOSPITAL', '400516', NULL, 'ORMOC CITY', NULL, NULL, NULL, '000-905-083-000', '12%', NULL, NULL, NULL, NULL),
(517, 'OSPA FARMERS\' MEDICAL CENTER', '400517', NULL, 'CAN-ADIENG ORMOC CITY', NULL, NULL, NULL, '001-387-778-001', 'E', NULL, NULL, NULL, NULL),
(518, 'BAP-MPC', '400518', NULL, NULL, NULL, NULL, NULL, '001-775-607-0000', 'N/A', NULL, NULL, NULL, NULL),
(519, 'PHILIPPINE SUGAR TECHNOLOGIST ASSN., INC.', '400519', NULL, 'PHILSUTECH bldg. beside Luxur place Masaysay Ave., Brgy. Singcang, Bacolod City', NULL, NULL, NULL, '002-391-857', '12%', NULL, NULL, NULL, NULL),
(520, 'UNION GALVASTEEL CORPORATION', '400520', NULL, 'COR. MALASAG ROAD, CUGMAN CAGAYAN DE ORO CITY', NULL, NULL, NULL, '002-645-341-0010', '12%', '(088) 855-1684 ; (088) 855-3750', NULL, NULL, NULL),
(521, 'MESCO INC.', '400521', NULL, 'RELIANCE CORNER BRIXTON ST. 1603 PASIG CITY, MANILA', NULL, NULL, NULL, '002-856-635-000', '12%', NULL, NULL, NULL, NULL),
(522, 'NFF INDUSTRIAL CORPORATION', '400522', NULL, 'Unit 721, 7F GF Globe Telecom Plaza II Pioneer Highlands Condo, Pioneer St. Barangka Ilaya, Mandaluyong City 1550', NULL, NULL, NULL, '002-857-177-000', '12%', NULL, NULL, NULL, NULL),
(523, 'NTPI INTERNATIONAL, INC.', '400523', NULL, 'MANDALUYONG CITY', NULL, NULL, NULL, '003-741-366-0000', '12%', '687-5123', NULL, NULL, NULL),
(524, 'SOFTOUCH CORPORATION', '400524', NULL, NULL, NULL, NULL, NULL, '003-876-017-000', 'N/A', NULL, NULL, NULL, NULL),
(525, 'JM BRENTON INDUSTRIES CORP.', '400525', NULL, 'NCR, Fourth District City, Makati - 1200', NULL, NULL, NULL, '003-958-670-000', '12%', NULL, NULL, NULL, NULL),
(526, 'TAIAN (SUBIC) ELECTRIC, INC.', '400526', NULL, 'SBIP Phase I Argonaut Hi-way corner Braveheart St., Subic Bay Freeport Zone - 2200', NULL, NULL, NULL, '004-076-587-000', '12%', NULL, NULL, NULL, NULL),
(527, 'CHRIS T. SPORTS CEBU, INC.', '400527', NULL, NULL, NULL, NULL, NULL, '004-265-687-0000', 'N/A', NULL, NULL, NULL, NULL),
(528, 'NITOS AUTO SUPPLY, INC.', '400528', NULL, 'PIER 4 NORTH RECLAMATION AREA, MABOLO CEBU CITY', NULL, NULL, NULL, '004-270-903-0002', '12%', '232-3411', NULL, NULL, NULL),
(529, 'ELECTRONICS CITY AND SERVICE CENTER, INC.', '400529', NULL, 'Conejos St. Brgy. Cogon, Ormoc City, 6541', NULL, NULL, NULL, '004-490-123-001', '12%', NULL, NULL, NULL, NULL),
(530, 'DE LEON IMPORT & EXPORT CORPORATION', '400530', NULL, 'ODELCO BUILDING, 128 KALAYAAN AVENUE, CENTRAL QUEZON CITY', NULL, NULL, NULL, '004-500-915-0000', '12%', '(02) 924-2685 ; (02) 924-2672', NULL, NULL, NULL),
(531, 'ASALUS CORPORATION', '400531', NULL, '7TH FLOOR FELIZA BLDG. MAKATI CITY', NULL, NULL, NULL, '004-666-055-0000', 'N/A', NULL, NULL, 'ADDRESS FROM SIR EMP 05.09.2017', NULL),
(532, 'PRIME ELECTRIX, INCORPORATED', '400532', NULL, 'MANILA CITY', NULL, NULL, NULL, '004-682-272-0000', '12%', NULL, NULL, NULL, NULL),
(533, 'CIRCUIT SOLUTIONS, INC.', '400533', NULL, NULL, NULL, NULL, NULL, '004-713-217-0000', 'N/A', NULL, NULL, NULL, NULL),
(534, 'SAN\'S INDUSTRIAL & DEVELOPMENT CO.', '400534', NULL, 'BRGY. 296 ZONE 028 BINONDO MANILA, 1006', NULL, NULL, NULL, '004-735-615-0000', '12%', NULL, NULL, NULL, NULL),
(535, 'MOTOR ACE PHIL., INC.', '400535', NULL, 'ORMOC CITY', NULL, NULL, NULL, '004-756-411-063', '12%', NULL, NULL, NULL, NULL),
(536, 'SUN PRINCE TBA CORPORATION', '400536', NULL, 'BRGY 58 ZONE 5 GRACE PARK CALOOCAN CITY', NULL, NULL, NULL, '004-851-083-000', '12%', NULL, NULL, NULL, NULL),
(537, 'BARONESSA METAL CORPORATION', '400537', NULL, 'ALIJIS ROAD, BACOLOD CITY NEGROS OCCIDENTAL, PHILIPPINES', NULL, NULL, NULL, '005-077-327-0000', '12%', NULL, NULL, NULL, NULL),
(538, 'ISUZU CEBU, INC.', '400538', NULL, 'or ISUZU MANDAUE', NULL, NULL, NULL, '005-104-972-0000', '12%', NULL, NULL, NULL, NULL),
(539, 'SCHNEIDER ELECTRIC', '400539', NULL, '24TH FLOOR FORT LEGEN TOWER, 3RD AVE., COR. 31ST ST., FORT BONIFACIO, GLOBAL TAGUIG CITY', NULL, NULL, NULL, '005-120-869-0000', '12%', NULL, NULL, NULL, NULL),
(540, 'AIR - RICH INDUSTRIAL SYSTEMS, INC.', '400540', NULL, NULL, NULL, NULL, NULL, '005-152-663-000', 'N/A', NULL, NULL, NULL, NULL),
(541, 'AIR - RICH INDUSTRIAL SYSTEMS, INC.', '400541', NULL, NULL, NULL, NULL, NULL, '005-152-663-000', 'N/A', NULL, NULL, NULL, NULL),
(542, 'FAST LABORATORIES', '400542', NULL, 'M.C BRIONES ST., HIGHWAY GUIZO MANDAUE CITY', NULL, NULL, NULL, '005-216-414-001', 'E', NULL, NULL, 'EXEMPT FROM VAT AND OTHER TAXES', NULL),
(543, 'AP CARGO LOGISTIC NETWORK CORP.', '400543', NULL, 'BONIFACIO ST. ORMOC CITY', NULL, NULL, NULL, '005-247-530-00057', '12%', NULL, NULL, NULL, NULL),
(544, 'DEUTSCHE MOTORGERATE INC.', '400544', NULL, 'KANTOBO, ORMOC CITY', NULL, NULL, NULL, '005-250-424-0002', '12%', NULL, NULL, NULL, NULL),
(545, 'AISON SURPLUS & AUTO PARTS CORPORATION', '400545', NULL, NULL, NULL, NULL, NULL, '005-269-053-001', 'N/A', NULL, NULL, NULL, NULL),
(546, 'OCEANIC CONTAINER LINES, INC.', '400546', NULL, '511 H LOPEZ BLVD., BALUT, TONDO, MANILA', NULL, NULL, NULL, '005-425-488-000', '12%', '255-8492', NULL, 'OFFICE: GGC COMPOUND - PUNTA, OC', NULL),
(547, 'MORSE HYDRAULIC SYSTEM CORPORATION', '400547', NULL, NULL, NULL, NULL, NULL, '005-429-976-0006', '12%', NULL, NULL, NULL, NULL),
(548, 'NEWTON ELECTRICAL EQUIPMENT CO., INC.', '400548', NULL, 'VALENZUELA CITY', NULL, NULL, NULL, '005-450-175-0000', '12%', '(02) 432-0105', NULL, NULL, NULL),
(549, 'CRL ENVIRONMENTAL CORPORATION', '400549', NULL, 'CLARKFIELD PAMPANGA', NULL, NULL, NULL, '005-650-419-0000', 'NV', NULL, NULL, NULL, NULL),
(550, 'SUMITOMO DRIVE TECHNOLOGIES', '400550', NULL, 'B2B BUILDING GRANVILLE INDUSTRIAL COMPLEX GOVERNOR\'S DRIVE, BO. BANCAL CARMONA CAVITE 4116 PHILIPPINES', NULL, NULL, NULL, '005-693-635-000', '12%', NULL, NULL, NULL, NULL),
(551, 'CHULIANTE MARKETING CORPORATION', '400551', NULL, 'ORMOC CITY', NULL, NULL, NULL, '005-758-783-000', 'E', NULL, NULL, NULL, NULL),
(552, 'CHU, LLOYD', '400552', NULL, NULL, NULL, NULL, NULL, '005-758-783-0000', 'N/A', NULL, NULL, NULL, NULL),
(553, 'ORMOC REAL SHELL SERVICE STATION', '400553', NULL, 'REAL STREET, DISTRICT 14 ORMOC CITY', NULL, NULL, NULL, '005-759-639-0002', '12%', NULL, NULL, 'OPERATED BY: ORMOC BAY RESOURCES CORP.', NULL),
(554, 'ORMOC SUPER SHELL SERVICE STATION', '400554', NULL, 'D. VELOSO ST. PUNTA, ORMOC CITY', NULL, NULL, NULL, '005-759-639-0003', '12%', NULL, NULL, 'OPERATED BY: ORMOC BAY RESOURCES CORP.', NULL),
(555, 'NIKANDREA TRADING CORPORATION', '400555', NULL, 'COR. RIZAL & BURGOS ST. ORMOC CITY', NULL, NULL, NULL, '005-760-203-000', '12%', NULL, NULL, NULL, NULL),
(556, 'DES MARKETING, INC.', '400556', NULL, 'ORMOC CITY', NULL, NULL, NULL, '005-888-306-0046', '12%', NULL, NULL, NULL, NULL),
(557, 'CITI HARDWARE', '400557', NULL, 'PANALI-AN, BRGY. IPIL ORMOC CITY', NULL, NULL, NULL, '005-919-438-0000', 'N/A', NULL, NULL, NULL, NULL),
(558, 'CITI HARDWARE BACOLOD, INC.', '400558', NULL, NULL, NULL, NULL, NULL, '005-919-438-0003', '12%', NULL, NULL, NULL, NULL),
(559, 'COOLING TOWER & HVAC SYSTEMS, INC.', '400559', NULL, 'SAN MATEO, RIZAL', NULL, NULL, NULL, '006-695-536-0000', '12%', '941-0382', '632-942-2263', 'PAYEE: CTHVAC COOLING TOWER', NULL),
(560, 'JAGUAR CARGO HANDLERS & MULTI SERVICES CO.', '400560', NULL, 'MANILA CITY', NULL, NULL, NULL, '006-718-155-000', '12%', NULL, NULL, NULL, NULL),
(561, 'JJ-LAPP CABLE (P) INC.', '400561', NULL, 'TAGUIG CITY', NULL, NULL, NULL, '006-921-991-0000', '12%', NULL, NULL, NULL, NULL),
(562, 'TYVAL INDUSTRIAL SUPPLY CORP.', '400562', NULL, '982 Severio Reyes St. Brgy 314 Zone 031 Sta. Cruz Manila, 1229', NULL, NULL, NULL, '007-131-738-000', '12%', NULL, NULL, NULL, NULL),
(563, 'G. U. ENGINEERING SALES, INC.', '400563', NULL, '580 7TH AVENUE CALOOCAN CITY', NULL, NULL, NULL, '007-192-151-0000', '12%', NULL, '364-8356', NULL, NULL),
(564, 'SPRAYING SYSTEMS CO. (PHILS.), INC.', '400564', NULL, 'MANILA CITY', NULL, NULL, NULL, '007-202-254-000', '12%', NULL, NULL, NULL, NULL),
(565, 'PRIME HYDRAULIC & PNEUMATIC SOLUTION, INC.', '400565', NULL, '111 5tH AVENUE, COR RIZAL AVE. EXT. BRGY. 51, CALOOCAN CITY 1400', NULL, NULL, NULL, '007-419-405-000', '12%', NULL, NULL, NULL, NULL),
(566, 'HI-SAFETY INDUSTRIAL SUPPLIES, INC.', '400566', NULL, 'M. CENIZA ST., CASUNTINGAN, MANDAUE CITY', NULL, NULL, NULL, '007-593-478-003', '12%', NULL, NULL, 'BIR FROM 2303 @ MATERIAL FILE FOLDER', NULL),
(567, 'ORMOC AUTO PARTS CENTER', '400567', NULL, NULL, NULL, NULL, NULL, '007-607-612-0001', '12%', NULL, NULL, NULL, NULL),
(568, 'GSPECS INDUSTRIAL CORPORATION', '400568', NULL, 'MANILA CITY', NULL, NULL, NULL, '007-677-784-000', '12%', NULL, NULL, NULL, NULL),
(569, 'BEHN MEYER CHEMICALS (PHILIPPINES) INC.', '400569', NULL, 'BRGY. SAN ANTONIO, pasig city #17 San Miguel ave. #1808 Hnaston Square Ortigas Center, Pasig City', NULL, NULL, NULL, '007-916-822-000', '12%', NULL, NULL, NULL, NULL),
(570, 'JK ENERGIES TECHNOLOGY INC.', '400570', NULL, 'QUEZON CITY', NULL, NULL, NULL, '007-951-825-0000', '12%', NULL, NULL, NULL, NULL),
(571, 'WIKA INSTRUMENTS PHILIPPINES, INC.', '400571', NULL, 'BRGY. KAPITOLYO PASIG CITY', NULL, NULL, NULL, '008-122-037-0000', '12%', NULL, NULL, NULL, NULL),
(572, 'RACAL VISMIN MOTORSALES CORPORATION', '400572', NULL, 'COGON ORMOC CITY', NULL, NULL, NULL, '008-125-586-0005', '12%', NULL, NULL, NULL, NULL),
(573, 'ENTHALPY UNITRADE SERVICES CO.', '400573', NULL, 'DON BOSCO, PARANAQUE CITY - 1700', NULL, NULL, NULL, '008-213-789-000', '12%', NULL, NULL, NULL, NULL),
(574, 'TOYOTA TACLOBAN LEYTE INC.', '400574', NULL, 'TACLOBAN CITY', NULL, NULL, NULL, '008-305-556-0000', '12%', NULL, NULL, NULL, NULL),
(575, 'SRV 888 INTERNATIONAL TRADING CORP.', '400575', NULL, 'COR. MINDANAO AVE. EXTENSION NLEX UGONG SULOK VALENZUELA CITY', NULL, NULL, NULL, '008-365-913-000', '12%', NULL, NULL, NULL, NULL),
(576, 'TUGATOG CONTRIVANCE & HYDRAULICS, INC.', '400576', NULL, '128 B Serrano Bet 7th & 8th Avenue, Brgy 109, Caloocan City', NULL, NULL, NULL, '008-548-617-000', '12%', NULL, NULL, NULL, NULL),
(577, 'TOP ONE LOGISTICS,  INC.', '400577', NULL, '2288 RODRIGUEZ ST., COR. GUIDOTE ST. BALUT TONDO MANILA CITY', NULL, NULL, NULL, '008-646-808-0000', '12%', '0922-855-2465', '522-3240', NULL, NULL),
(578, 'RICH-PAUL MARKETING COMPANY INC.', '400578', NULL, '86 BALER ST., BRGY PALTOK QUEZON CITY', NULL, NULL, NULL, '008-685-039-000', '12%', NULL, NULL, NULL, NULL),
(579, 'KSB PHILIPPINES, INC.', '400579', NULL, 'UNIT 3D, CLASSICA 1 TOWER 112, HV DELA COSTA STREET, SALCEDO VILLAGE MAKATI CITY', NULL, NULL, NULL, '008-730-126-0000', '12%', NULL, NULL, NULL, NULL),
(580, 'MICROHAND PHILS. CORP.', '400580', NULL, '26-C MACARIO RIVERA ST., SAN FRANCISCO DEL MONTE QUEZON CITY', NULL, NULL, NULL, '008-854-305-000', '12%', NULL, NULL, NULL, NULL),
(581, 'JNGLOBAL FINECHEM INC.', '400581', NULL, 'B2 L16 #20 H Rodis St. BF Resort Pamplona, Las Piñas, 1747', NULL, NULL, NULL, '008-855-154-0000', '12%', NULL, NULL, NULL, NULL),
(582, 'BRAAMD, INC.', '400582', NULL, 'Lot 1 & block 2 73 Phase 5 Rodeo Drive Laguna Bel-Air 2 Don Jose 4026 City of Santa Rosa Laguna Phils.', NULL, NULL, NULL, '008-863-659-000', '12%', NULL, NULL, NULL, NULL),
(583, 'ST. PAUL AUTOMATION AND CONTROLS, INC.', '400583', NULL, 'UNIT G-2 2ND FLOOR EMERALD GREEN BLDG. C.V. STARR AVE., PAMPLONA II LAS PIÑAS CITY', NULL, NULL, NULL, '008-901-008-0000', '12%', '(632) 546-9832', NULL, NULL, NULL),
(584, 'WILCON DEPOT, INC.', '400584', NULL, 'Sitio Haubon, Tambulilid 6541 Ormoc City, Leyte', NULL, NULL, NULL, '009-192-878-0070', '12%', NULL, NULL, NULL, NULL),
(585, 'BICM GENESES FREIGHT INTERNATIONAL INC.', '400585', NULL, 'B. NINOY AQUINO AVE., BRGY STO. NIÑO PARANAQUE CITY', NULL, NULL, NULL, '009-195-546-000', '12%', NULL, NULL, NULL, NULL),
(586, 'METROPHYSIKA INC.', '400586', NULL, 'MAKATI CITY', NULL, NULL, NULL, '009-211-623-0000', '12%', NULL, NULL, NULL, NULL),
(587, 'SUCRO TECH UNLIMITED SALES & SERVICES INC.', '400587', NULL, 'MANILA CITY', NULL, NULL, NULL, '009-261-818-0000', '12%', NULL, NULL, NULL, NULL),
(588, 'RKRUBBER ENTERPRISE CO.', '400588', NULL, '4 Katipunan Avenue California Vill. San Bartolome Novaliches, Quezon City', NULL, NULL, NULL, '009-537-068-000', '12%', NULL, NULL, NULL, NULL),
(589, 'PURE JJEM ENVIRONMENTAL CORP.', '400589', NULL, 'Pacific Grande 1 Purok Kalabasa, Gun ob 6015 Lapu-Lapu City (Opon) Cebu Philippines', NULL, NULL, NULL, '009-673-709-00001', 'N/A', NULL, NULL, NULL, NULL),
(590, 'ALLOWELD-CIRCUITS POWERS IND. INC.', '400590', NULL, 'CEBU CITY', NULL, NULL, NULL, '009-861-182-0000', '12%', NULL, NULL, NULL, NULL),
(591, 'OMLI-WEM & ALLIED SERVICES INC.', '400591', NULL, '124 Valero St. Salcedo Village, Makati City, 1209', NULL, NULL, NULL, '009-897-941-000', '12%', NULL, NULL, NULL, NULL),
(592, 'ALGOMETRICS PHILIPPINES INC.', '400592', NULL, '2/F B6 L27 DECA 5 SUDTONGAN BASAK LAPU-LAPU CITY, CEBU', NULL, NULL, NULL, '010-081-720-000', '12%', NULL, NULL, 'BIR FORM 2303 ATTACHED TO CV#1020190-10/28/20', NULL),
(593, 'INNOVCEBU ELECTRICAL CO., INC.', '400593', NULL, 'J.Lim Bldg P. Remedios St. Cabancalan 6014 Mandaue City Philippines', NULL, NULL, NULL, '010-554-348-00000', '12%', NULL, NULL, NULL, NULL),
(594, 'GOLDEN HARVESTER INTERNATIONAL', '400594', NULL, 'MANDAUE CITY', NULL, NULL, NULL, '100-071-767-000', '12%', '346-1667/345-1129', '346-0238', NULL, NULL),
(595, 'HIGHCHEM TRADING', '400595', NULL, '105 MT NATIB ST SAN JOSE CALOOCAN CITY', NULL, NULL, NULL, '100-557-198-000', '12%', NULL, NULL, 'BIR FORM 2303 ATTACHED TO CV#0121114-1/15/21', NULL),
(596, 'LARRAZABAL, RAMON SR.', '400596', NULL, NULL, NULL, NULL, NULL, '101-719-261', 'N/A', NULL, NULL, NULL, NULL),
(597, 'ALONZO, JAMES', '400597', NULL, NULL, NULL, NULL, NULL, '101-719-439-000', 'NV', NULL, NULL, NULL, NULL),
(598, 'SARSOZA, LEONITO', '400598', NULL, NULL, NULL, NULL, NULL, '101-719-592', 'N/A', NULL, NULL, NULL, NULL),
(599, 'MEMORY\'S DEVELOPING CENTER', '400599', NULL, NULL, NULL, NULL, NULL, '101-719-868-001', 'N/A', NULL, NULL, NULL, NULL),
(600, 'ORMOC FRIENDLY BAZAR', '400600', NULL, 'LOPEZ JAENA STREET ORMOC CITY', NULL, NULL, NULL, '101-720-006-0000', '12%', NULL, NULL, NULL, NULL),
(601, 'DOYON, JESUS SR.', '400601', NULL, NULL, NULL, NULL, NULL, '101-720-030', 'NV', NULL, NULL, NULL, NULL),
(602, 'ORMOC AUTO CARE', '400602', NULL, 'COGON ORMOC CITY', NULL, NULL, NULL, '101-720-272-000', 'NV', NULL, NULL, NULL, NULL),
(603, 'ALCON AUTOCARE SHOP', '400603', NULL, 'BRGY. COGON ORMOC CITY', NULL, NULL, NULL, '101-720-272-0000', 'NV', NULL, NULL, NULL, NULL),
(604, 'ROLITO VILLO', '400604', NULL, 'FATIMA, COGON ORMOC CITY', NULL, NULL, NULL, '102-663-580-000', 'N/A', NULL, NULL, NULL, NULL),
(605, 'LEYTE PAPERWORLD', '400605', NULL, 'Level 2, Robinson Place Ormoc, P. Chrysanthemum, Brgy. Cogon Ormoc City', NULL, NULL, NULL, '102-721-983-007', '12%', NULL, NULL, NULL, NULL),
(606, 'GSCOM MARKETING', '400606', NULL, '96 Old Samsom Road, Apolonio Samson, Quezon City', NULL, NULL, NULL, '103-756-642-0001', '12%', NULL, NULL, NULL, NULL),
(607, 'BURGOSCO AUTO AND TRUCK PARTS OUTLET', '400607', NULL, 'TACLOBAN CITY', NULL, NULL, NULL, '103-975-545-0008', '12%', NULL, NULL, NULL, NULL),
(608, 'CQ-DX REPAIR CENTER', '400608', NULL, 'EDIFICIO ENRIQUETA BLDG. No.2E 442 NS AMORATO ST. QUEZON CITY', NULL, NULL, NULL, '104-002-744-000', '12%', NULL, NULL, NULL, NULL),
(609, 'DANIEL MERCHANDISING', '400609', NULL, 'CEBU CITY', NULL, NULL, NULL, '104-106-868-005', 'N/A', NULL, NULL, NULL, NULL),
(610, 'ALGOM INTEGRATED SERVICES', '400610', NULL, NULL, NULL, NULL, NULL, '104-733-619-000', '12%', NULL, NULL, NULL, NULL),
(611, 'SALINAS, MARITO', '400611', NULL, NULL, NULL, NULL, NULL, '104-735-410-001', 'N/A', NULL, NULL, NULL, NULL),
(612, 'ARTAN DEVELOPMENT CORP.', '400612', NULL, NULL, NULL, NULL, NULL, '104-742-023-000', 'N/A', NULL, NULL, NULL, NULL),
(613, 'BINUEZA, DANILO (DR.)', '400613', NULL, NULL, NULL, NULL, NULL, '104-746-648-0000', 'N/A', NULL, NULL, NULL, NULL),
(614, 'PASTURAN, EDUARDO M.', '400614', NULL, NULL, NULL, NULL, NULL, '104-748-355', 'N/A', NULL, NULL, NULL, NULL),
(615, 'COQUILLA, JUANITO', '400615', NULL, NULL, NULL, NULL, NULL, '104-761-041', 'N/A', NULL, NULL, NULL, NULL),
(616, 'PEÑARANDA, EMMANUEL', '400616', NULL, NULL, NULL, NULL, NULL, '104-762-122', 'N/A', NULL, NULL, NULL, NULL),
(617, 'ALDERCHEM TRADING', '400617', NULL, NULL, NULL, NULL, NULL, '109-641-823-0000', '12%', NULL, NULL, NULL, NULL),
(618, 'ABOYME, NESTOR', '400618', NULL, 'HINDANG, LEYTE', NULL, NULL, NULL, '110-565-378-0000', 'NV', '0918-925-4152', NULL, NULL, NULL),
(619, 'KINGS BARGAIN CENTER', '400619', NULL, 'COR RIZAL & BURGOS STS. ORMOC CITY', NULL, NULL, NULL, '110-593-639-000', 'N/A', NULL, NULL, NULL, NULL),
(620, 'GUILLEMER, ERIBERTO JR.', '400620', NULL, NULL, NULL, NULL, NULL, '110-626-160', 'N/A', NULL, NULL, NULL, NULL),
(621, 'TORRES, MANUEL', '400621', NULL, NULL, NULL, NULL, NULL, '110-697-363', 'N/A', NULL, NULL, NULL, NULL);
INSERT INTO `vendors` (`id`, `vendor_name`, `vendor_code`, `account_number`, `vendor_address`, `contact_number`, `email`, `terms`, `tin`, `tax_type`, `tel_no`, `fax_no`, `notes`, `item_type`) VALUES
(622, 'ZENAIDA\'S CHATEAU', '400622', NULL, 'COR. LOPEZ JAENA & J. NAVARRO STS. ORMOC CITY', NULL, NULL, NULL, '110-702-123-001', '12%', NULL, NULL, NULL, NULL),
(623, 'CAMPOS, ROMEO', '400623', NULL, NULL, NULL, NULL, NULL, '113-489-221-0000', 'N/A', NULL, NULL, NULL, NULL),
(624, 'VSK MACHINE SHOP', '400624', NULL, 'MR Cmpd, Bantigue, Ormoc City', NULL, NULL, NULL, '113-495-791-003', '12%', NULL, NULL, NULL, NULL),
(625, 'PURA FARMS', '400625', NULL, NULL, NULL, NULL, NULL, '113-496-359', 'N/A', NULL, NULL, NULL, NULL),
(626, 'SACAY, JOSE/CORAZON', '400626', NULL, NULL, NULL, NULL, NULL, '113-504-998', 'N/A', NULL, NULL, NULL, NULL),
(627, 'TORITA, SANTIAGO', '400627', NULL, NULL, NULL, NULL, NULL, '113-507-205', 'N/A', NULL, NULL, NULL, NULL),
(628, 'TORREVILLAS, DIONISIO', '400628', NULL, NULL, NULL, NULL, NULL, '113-507-270', 'N/A', NULL, NULL, NULL, NULL),
(629, 'BVC TRUCKING', '400629', NULL, NULL, NULL, NULL, NULL, '114-061-023-0000', 'N/A', NULL, NULL, NULL, NULL),
(630, 'CAÑAZARES, ANASTACIA', '400630', NULL, NULL, NULL, NULL, NULL, '114-061-080-0000', 'N/A', NULL, NULL, NULL, NULL),
(631, 'GANTUANGCO, GIL', '400631', NULL, NULL, NULL, NULL, NULL, '115-334-253', 'N/A', NULL, NULL, 'HAULER 01.2018', NULL),
(632, 'SALAZAR, ARNULFO (ATTY.)', '400632', NULL, NULL, NULL, NULL, NULL, '115-505-108', 'N/A', NULL, NULL, NULL, NULL),
(633, 'VILLANUEVA, TYRONE', '400633', NULL, NULL, NULL, NULL, NULL, '116-607-127', 'N/A', NULL, NULL, NULL, NULL),
(634, 'BARTIQUIN, REYNALDO SR.', '400634', NULL, NULL, NULL, NULL, NULL, '116-607-127-0000', 'N/A', NULL, NULL, NULL, NULL),
(635, 'JE ARRADAZA CONSTRUCTION AND SUPPLY', '400635', NULL, 'DAMA DE NOCHE BRGY. PUNTA ORMOC CITY', NULL, NULL, NULL, '116-610-751-000', '12%', NULL, NULL, 'PROP: CARMELITA T. ARRADAZA', NULL),
(636, 'BATUCAN, ABELARDO', '400636', NULL, NULL, NULL, NULL, NULL, '116-621-089-0000', 'N/A', NULL, NULL, NULL, NULL),
(637, 'NORWOOD MACHINERY AND PARTS SUPPLY', '400637', NULL, '#7 CASIANA ST.  SANTOL, QUEZON CITY', NULL, NULL, NULL, '117-010-402-000', 'N/A', NULL, NULL, NULL, NULL),
(638, 'PINTURAMA PAINT DEPOT', '400638', NULL, 'Reat St. Ormoc City', NULL, NULL, NULL, '120-714-350-0001', '12%', NULL, NULL, NULL, NULL),
(639, 'LEE MICHAEL ENTERPRISES', '400639', NULL, NULL, NULL, NULL, NULL, '123-799-237', 'N/A', NULL, NULL, NULL, NULL),
(640, 'ALDEN, JERRY', '400640', NULL, 'PUERTOBELLO, MERIDA', NULL, NULL, NULL, '126-421-507-000', 'N/A', NULL, NULL, NULL, NULL),
(641, 'AVILES, JONATHAN', '400641', NULL, NULL, NULL, NULL, NULL, '126-623-207-0000', 'NV', NULL, NULL, NULL, NULL),
(642, 'CODILLA, EUFROCINO JR.', '400642', NULL, NULL, NULL, NULL, NULL, '126-673-663-0000', 'N/A', NULL, NULL, NULL, NULL),
(643, 'CALINAO, BENEDICTO', '400643', NULL, NULL, NULL, NULL, NULL, '126-802-636-0000', 'N/A', NULL, NULL, NULL, NULL),
(644, 'DEL SOCORRO, LIZA', '400644', NULL, NULL, NULL, NULL, NULL, '126-802-823', 'NV', NULL, NULL, NULL, NULL),
(645, 'GONZAGA, MARILYN', '400645', NULL, NULL, NULL, NULL, NULL, '127-584-426', 'N/A', NULL, NULL, NULL, NULL),
(646, 'HERMOSO, MARY ANN', '400646', NULL, NULL, NULL, NULL, NULL, '128-434-907-000', 'N/A', NULL, NULL, NULL, NULL),
(647, 'REGIS AGENCY', '400647', NULL, NULL, NULL, NULL, NULL, '128-508-986', 'N/A', NULL, NULL, NULL, NULL),
(648, 'DR. NOEL  A. MAICO (CO. PHYSICIAN)', '400648', NULL, 'FATIMA SUBDIVISION COGON, ORMOC CITY', NULL, NULL, NULL, '129-688-672-000', 'NV', NULL, NULL, 'STARTS:JULY 20,2020-WRITTEN TIN ATTACHED TO CV#0720289-7/30/20', NULL),
(649, 'TUBOD ENG\'G WORKS & IND\'L SALES', '400649', NULL, NULL, NULL, NULL, NULL, '130-472-335', 'N/A', NULL, NULL, NULL, NULL),
(650, 'CALIWAN, LIBERATA', '400650', NULL, NULL, NULL, NULL, NULL, '131-613-065-0000', 'N/A', NULL, NULL, NULL, NULL),
(651, 'CATALINO TRADING', '400651', NULL, '403 Carlos Tan St. Ormoc City', NULL, NULL, NULL, '131-613-459-0000', '12%', NULL, NULL, NULL, NULL),
(652, 'CATA-AG, DANILO', '400652', NULL, 'ORMOC CITY', NULL, NULL, NULL, '135-300-033-0000', 'NV', NULL, NULL, 'HAULER', NULL),
(653, 'DALDE, JAIME', '400653', NULL, 'KANANGA CITY', NULL, NULL, NULL, '135-715-371-0000', 'N/A', NULL, NULL, NULL, NULL),
(654, 'EDUARDO COCHING', '400654', NULL, NULL, NULL, NULL, NULL, '136-295-935-0000', 'NV', NULL, NULL, NULL, NULL),
(655, 'EQUILATERAL ENTERPRISES', '400655', NULL, NULL, NULL, NULL, NULL, '140-035-437-000', '12%', NULL, NULL, NULL, NULL),
(656, 'SALINAS, ROSEMARIE', '400656', NULL, NULL, NULL, NULL, NULL, '141-210-469', 'N/A', NULL, NULL, NULL, NULL),
(657, 'ALPHA OFFICE EQUIPMENTS CENTER', '400657', NULL, NULL, NULL, NULL, NULL, '141-271-496-000', 'N/A', NULL, NULL, NULL, NULL),
(658, 'PAMI, GERARDO', '400658', NULL, NULL, NULL, NULL, NULL, '142-310-462', 'N/A', NULL, NULL, NULL, NULL),
(659, 'OPON, FLOR AMOR (ATTY.)', '400659', NULL, NULL, NULL, NULL, NULL, '142-319-947', 'N/A', NULL, NULL, NULL, NULL),
(660, 'RETULLA, GEMELIANO (DR.)', '400660', NULL, NULL, NULL, NULL, NULL, '142-321-804', 'NV', NULL, NULL, NULL, NULL),
(661, 'LAO, LEONARDO', '400661', NULL, NULL, NULL, NULL, NULL, '142-745-821-000', 'NV', NULL, NULL, NULL, NULL),
(662, 'CARRILLO, VICENTE SR.', '400662', NULL, NULL, NULL, NULL, NULL, '144-061-023-0000', 'N/A', NULL, NULL, NULL, NULL),
(663, 'CAPAHI, RUSTICO JR.', '400663', NULL, 'ORMOC CITY', NULL, NULL, NULL, '144-061-056-0000', 'N/A', NULL, NULL, NULL, NULL),
(664, 'DASIGAN, CATALINO', '400664', NULL, NULL, NULL, NULL, NULL, '144-527-559-000', 'NV', NULL, NULL, NULL, NULL),
(665, 'ORMOC VICTORY TRADING', '400665', NULL, 'MABINI ST. ORMOC CITY', NULL, NULL, NULL, '145-242-096-000', 'NV', NULL, NULL, 'PROP.: RICARDO B. ZARATE', NULL),
(666, 'DOYON, ISAGANI', '400666', NULL, NULL, NULL, NULL, NULL, '145-247-570', 'NV', NULL, NULL, NULL, NULL),
(667, 'KIMUEL TRANSPORTATION', '400667', NULL, 'CAGBUHANGIN, ORMOC CITY', NULL, NULL, NULL, '145-247-570-0000', 'NV', NULL, NULL, NULL, NULL),
(668, 'TAN, FRANCISCO JR.', '400668', NULL, NULL, NULL, NULL, NULL, '145-507-337', 'N/A', NULL, NULL, NULL, NULL),
(669, 'DADIS, JOEL', '400669', NULL, NULL, NULL, NULL, NULL, '146-219-061', 'N/A', NULL, NULL, NULL, NULL),
(670, 'NEW ST. JUDE AGRICULTURAL SUPPLY', '400670', NULL, 'ORMOC CITY', NULL, NULL, NULL, '146-236-302-000', 'E', NULL, NULL, NULL, NULL),
(671, 'FIEL, ROY BERNARD', '400671', NULL, NULL, NULL, NULL, NULL, '148-017-026-0000', 'NV', NULL, NULL, NULL, NULL),
(672, 'A.C. ROSELLO BUILDERS', '400672', NULL, '6TH AVENUE VILLAGE, APAS  CEBU CITY', NULL, NULL, NULL, '148-184-480-0000', 'NV', NULL, NULL, 'PROP.:ANTONIO ANGELO CABATINGAN', NULL),
(673, 'GODOFREDO QUIAMCO', '400673', NULL, 'PALOMPON, LEYTE', NULL, NULL, NULL, '149-770-018-000', 'N/A', NULL, NULL, NULL, NULL),
(674, 'TAN, MA. ADELFA', '400674', NULL, 'ORMOC CITY', NULL, NULL, NULL, '149-770-041', 'N/A', NULL, NULL, NULL, NULL),
(675, 'SANTIAGO, DIONISIO SR.', '400675', NULL, NULL, NULL, NULL, NULL, '150-703-310', 'N/A', NULL, NULL, NULL, NULL),
(676, 'GBROSS ELECTRICAL SUPPLY AND SERVICES', '400676', NULL, 'MANILA CITY', NULL, NULL, NULL, '152-067-566-0000', '12%', NULL, NULL, 'PROP.: CARLOS T. PEÑARANDA', NULL),
(677, 'COOPER\'S HAWK SECURITY AGENCY, INC.', '400677', NULL, NULL, NULL, NULL, NULL, '154-344-161-0000', '12%', NULL, NULL, 'PREVIOUSLY NAMED AS JAYHAWK SECURITY AGENCY, INC.', NULL),
(678, 'CRISTOBAL\'S INN', '400678', NULL, 'Lopez Jane St. Ormo City', NULL, NULL, NULL, '155-355-867-002', '12%', NULL, NULL, NULL, NULL),
(679, 'EMBAYARTE, LETECIA', '400679', NULL, 'ORMOC CITY', NULL, NULL, NULL, '155-582-417-0000', 'NV', NULL, NULL, NULL, NULL),
(680, 'RAE, ANANITA F.', '400680', NULL, 'ORMOC CITY', NULL, NULL, NULL, '156-909-134-0000', 'NV', NULL, NULL, 'HAULER - SUGAR', NULL),
(681, 'LEMUEL CABILING', '400681', NULL, NULL, NULL, NULL, NULL, '157-550-643', 'NV', NULL, NULL, NULL, NULL),
(682, 'AKINGS INDUSTRIAL ENTERRISES', '400682', NULL, 'BLK 15 LOT 16 LAPIDSVILLE, TAMBUBONG SAN RAFAEL, BULACAN - 3008', NULL, NULL, NULL, '158-910-993-000', '12%', NULL, NULL, NULL, NULL),
(683, 'ANLINK INTEGRATED SERVICES', '400683', NULL, 'MONTEBELLO KANANGA, LEYTE', NULL, NULL, NULL, '159-399-977-000', '12%', NULL, NULL, NULL, NULL),
(684, 'GRACE ELIZALDE - BAYANG, CPA', '400684', NULL, 'Unit 817 West Insula Condo, No. 135 West Avenue, Bungad, Quezon City', NULL, NULL, NULL, '159-608-902-000', 'NV', NULL, NULL, NULL, NULL),
(685, 'ROSARIO, TEODORICO JR.', '400685', NULL, 'MANILA CITY', NULL, NULL, NULL, '160-277-341-0000', 'NV', NULL, NULL, NULL, NULL),
(686, 'CORNEL, TEOFILO JR.', '400686', NULL, NULL, NULL, NULL, NULL, '160-881-575', 'N/A', NULL, NULL, NULL, NULL),
(687, 'R.A. BENSIG CONSTRUCTION & GENERAL SERVICES', '400687', NULL, NULL, NULL, NULL, NULL, '161-628-153-000', 'N/A', NULL, NULL, NULL, NULL),
(688, 'NIKKON ELECTRICAL SERVICES PHILS.', '400688', NULL, '346 Prudencio St. Zone 044 Brgy.446 Sampaloc, Manila', NULL, NULL, NULL, '162-945-404-000', '12%', NULL, NULL, NULL, NULL),
(689, 'ABAYAN, NOEL', '400689', NULL, 'ORMOC CITY', NULL, NULL, NULL, '163-289-432-0000', 'NV', NULL, NULL, 'TS OPERATOR', NULL),
(690, 'ALAS, MACARIO', '400690', NULL, 'KAWAYAN, BILIRAN ISLAND', NULL, NULL, NULL, '164-900-896-0000', 'NV', NULL, NULL, 'COCOSHELL SUPPLIER', NULL),
(691, 'JEG INDUSTRIAL SUPPLY', '400691', NULL, '215 E MAYON N.S. AMORANTO 1 QUEZON CITY', NULL, NULL, NULL, '165-118-464-0000', '12%', NULL, NULL, 'PROP.: LUISITO O. CURA', NULL),
(692, 'FERGINO TRADING', '400692', NULL, 'DON PABLO TAN KANANGA LEYTE', NULL, NULL, NULL, '165-160-569-000', 'E', NULL, NULL, NULL, NULL),
(693, 'LAUDE, BERNABE', '400693', NULL, NULL, NULL, NULL, NULL, '165-302-690', 'N/A', NULL, NULL, NULL, NULL),
(694, 'LAURENTE, ELESEA', '400694', NULL, NULL, NULL, NULL, NULL, '166-183-662', 'N/A', NULL, NULL, NULL, NULL),
(695, 'PEDOY, JULITA', '400695', NULL, NULL, NULL, NULL, NULL, '167-885-890', 'N/A', NULL, NULL, NULL, NULL),
(696, 'DR. ERNESTO P. CODILLA (CO. DENTIST)', '400696', NULL, 'ORMOC CITY', NULL, NULL, NULL, '168-330-600-000', 'NV', NULL, NULL, NULL, NULL),
(697, 'VELASQUEZ, ERIC', '400697', NULL, NULL, NULL, NULL, NULL, '168-331-024', 'N/A', NULL, NULL, NULL, NULL),
(698, 'TAN, EDUARDO', '400698', NULL, NULL, NULL, NULL, NULL, '168-332-765', 'N/A', NULL, NULL, NULL, NULL),
(699, 'GATCHALIAN, JOEL', '400699', NULL, NULL, NULL, NULL, NULL, '168-332-960', 'N/A', NULL, NULL, NULL, NULL),
(700, 'RAFAEL BENJAMIN B. OMEGA III', '400700', NULL, 'COGON, ORMOC CITY', NULL, NULL, NULL, '168-333-162', 'NV', NULL, NULL, NULL, NULL),
(701, 'AGRO MILL-MATIC ENTERPRISES', '400701', NULL, '660 MAHARLIKA STREET PLAINVIEW MANDALUYONG CITY', NULL, NULL, NULL, '168-756-914-0000', '12%', NULL, NULL, 'PROP.: ADONIS S. MATIC', NULL),
(702, 'THE GENERICS PHARMACY', '400702', NULL, 'ORMOC CITY', NULL, NULL, NULL, '170-804-225-001', '12%', NULL, NULL, NULL, NULL),
(703, 'GARCIANO, RICO', '400703', NULL, NULL, NULL, NULL, NULL, '174-357-250', 'N/A', NULL, NULL, NULL, NULL),
(704, 'LARRAZABAL, CESAR ANTONIO', '400704', NULL, NULL, NULL, NULL, NULL, '180-291-122', 'N/A', NULL, NULL, NULL, NULL),
(705, 'NICOLAS TIRES & SERVICE CENTER', '400705', NULL, NULL, NULL, NULL, NULL, '180-486-478-000', 'N/A', NULL, NULL, NULL, NULL),
(706, 'PEPITO, REMEDIOS', '400706', NULL, 'ORMOC CITY', NULL, NULL, NULL, '180-490-272-0000', 'NV', NULL, NULL, 'HAULER', NULL),
(707, 'TAN, REYNALDO', '400707', NULL, NULL, NULL, NULL, NULL, '183-801-104', 'N/A', NULL, NULL, NULL, NULL),
(708, 'PAREDES, OLEGARIO JR.', '400708', NULL, NULL, NULL, NULL, NULL, '183-803-988', 'N/A', NULL, NULL, NULL, NULL),
(709, 'JABON, VICENTE', '400709', NULL, NULL, NULL, NULL, NULL, '185-729-247', 'N/A', NULL, NULL, NULL, NULL),
(710, 'LANDRAIN, RAMONETTE JOY', '400710', NULL, NULL, NULL, NULL, NULL, '185-955-789', 'N/A', NULL, NULL, NULL, NULL),
(711, 'ARELLANO, FREDDIE', '400711', NULL, 'KANANGA, LEYTE', NULL, NULL, NULL, '186-760-323-0000', 'NV', NULL, NULL, NULL, NULL),
(712, 'MENDOZA, EDWIN', '400712', NULL, 'BRGY. LILO-AN, ORMOC CITY', NULL, NULL, NULL, '186-771-335-0000', 'NV', NULL, NULL, NULL, NULL),
(713, 'MBR CONSTRUCTION OR MARK B. RACHO', '400713', NULL, 'ORMOC CITY', NULL, NULL, NULL, '186-776-380-0000', 'NV', NULL, NULL, 'PROP.: MARK B. RACHO', NULL),
(714, 'CRUZ, ROLANDO SR.', '400714', NULL, NULL, NULL, NULL, NULL, '187-369-272', 'N/A', NULL, NULL, NULL, NULL),
(715, 'DAVAO SEPTIC TANK CLEANING & PLUMBING SERVICES', '400715', NULL, 'DAVAO CITY', NULL, NULL, NULL, '193-297-635-0000', 'NV', NULL, NULL, NULL, NULL),
(716, 'EBPC PAINT CENTER', '400716', NULL, 'Mabini cor. Fr. Burgos st. Ormoc City - 6541', NULL, NULL, NULL, '194-069-786-005', '12%', NULL, NULL, NULL, NULL),
(717, 'FILINGPRO OFFICE EQUIPMENT', '400717', NULL, NULL, NULL, NULL, NULL, '199-158-972-000-NV', 'N/A', NULL, NULL, NULL, NULL),
(718, 'PHASEDIAG INDUSTRIAL SOLUTION', '400718', NULL, 'MINGLANILLA CEBU', NULL, NULL, NULL, '199-889-070-0001', 'NV', NULL, NULL, NULL, NULL),
(719, 'BAN YAC HARDWARE, INC.', '400719', NULL, NULL, NULL, NULL, NULL, '200-075-786-0000', '12%', NULL, NULL, NULL, NULL),
(720, 'PEÑARANDA, JOSE ROMMEL', '400720', NULL, 'ORMOC CITY', NULL, NULL, NULL, '200-085-328-0000', 'N/A', NULL, NULL, 'RETAINERS FEE', NULL),
(721, 'MAPITAGAN SCTY.& INVES.SVCS.,INC.', '400721', NULL, NULL, NULL, NULL, NULL, '200-252-103-000', 'N/A', NULL, NULL, NULL, NULL),
(722, 'ARQEE TRADING', '400722', NULL, 'PANDACAN MANILA', NULL, NULL, NULL, '200-871-348-0000', 'NV', NULL, NULL, NULL, NULL),
(723, 'MAN DIESEL AND TURBO PHILIPPINES, INC.', '400723', NULL, 'PARANAQUE CITY', NULL, NULL, NULL, '201-042-616-0000', '12%', NULL, NULL, NULL, NULL),
(724, 'SILGEN INDUSTRIAL CORPORATION', '400724', NULL, NULL, NULL, NULL, NULL, '201-793-093-0000', '12%', NULL, NULL, NULL, NULL),
(725, 'SOUTH SEAS CARGO FORWARDERS, INC.', '400725', NULL, NULL, NULL, NULL, NULL, '203-943-899-006', 'N/A', NULL, NULL, NULL, NULL),
(726, 'ACCESS FRONTIER TECHNOLOGIES, INC.', '400726', NULL, NULL, NULL, NULL, NULL, '204-248-281-000', 'N/A', NULL, NULL, NULL, NULL),
(727, 'JAYHAWK SECURITY AGENCY, INC.', '400727', NULL, NULL, NULL, NULL, NULL, '204-249-756-000', '12%', NULL, NULL, NULL, NULL),
(728, 'POWERTECH INDUSTRIAL SALES, INC.', '400728', NULL, '265 D. TUAZON ST., SAN JOSE QUEZON CITY', NULL, NULL, NULL, '205-120-354-0000', '12%', NULL, NULL, NULL, NULL),
(729, 'FOOTSAFE PHILIPPINES, INC.', '400729', NULL, 'Gomez, Bldg KM. 17 A Hi-way P. Esperito Bacoor, Cavite 4102', NULL, NULL, NULL, '205-269-305-0000', '12%', NULL, NULL, NULL, NULL),
(730, 'TOP-RIGID INDUSTRIAL SAFETY SUPPLY INC.', '400730', NULL, 'Plaridel st. Brgy. Umapad, Mandaue City, Cebu', NULL, NULL, NULL, '205-859-203-000', 'N/A', NULL, NULL, NULL, NULL),
(731, 'A AND B MARKETING', '400731', NULL, 'LILIA AVENUE BRGY COGON, ORMOC CITY', NULL, NULL, NULL, '206-690-880-000', 'NV', NULL, NULL, NULL, NULL),
(732, 'ROBINSONS APPLIANCES CORPORATION', '400732', NULL, 'ORMOC CITY', NULL, NULL, NULL, '207-540-656-107', '12%', NULL, NULL, NULL, NULL),
(733, 'FORMINGTECH MARKETING', '400733', NULL, 'MAKATI CITY', NULL, NULL, NULL, '210-382-191-0000', '12%', NULL, NULL, NULL, NULL),
(734, 'MEGA PRECISION CORPORATION', '400734', NULL, 'STA ROSA II MARILAO, BULACAN 3019', NULL, NULL, NULL, '211-980-931-000', '12%', NULL, NULL, NULL, NULL),
(735, 'PEPITO, ROEL', '400735', NULL, 'BRGY. CONCEPCION ORMOC CITY', NULL, NULL, NULL, '213-701-659', 'NV', NULL, NULL, NULL, NULL),
(736, 'J-MARRU MARKETING & CONSTRUCTION CORP.', '400736', NULL, NULL, NULL, NULL, NULL, '213-713-688-000', 'N/A', NULL, '02-376-2338', NULL, NULL),
(737, 'PEPCO GAS STATION', '400737', NULL, 'LIBONGAO KANANGA LEYTE', NULL, NULL, NULL, '213-811-431-000', '12%', NULL, NULL, NULL, NULL),
(738, 'ARVIN INTERNATIONAL MARKETING INC', '400738', NULL, 'PUROK 3 BRGY GAHONON DAET CAMARINES NORTE 4600', NULL, NULL, NULL, '215-261-911-000', '12%', NULL, NULL, NULL, NULL),
(739, 'DIGITEL MOBILE PHILIPPINES, INC.', '400739', NULL, '29F GALLERIA CORPORATE CENTER, EDSA CORNER ORTIGAS AVENUE, QUEZON CITY 1110', NULL, NULL, NULL, '215-398-626-0000', '12%', '(02) 395-8000', NULL, 'SUN CELLULAR (INTERCOM COMMUNICATION BILL)', NULL),
(740, 'INDUSTRIAL RESOURCES LINK INC.', '400740', NULL, 'SUBANGDAKO, MANDAUE CITY', NULL, NULL, NULL, '217-993-453-0000', '12%', NULL, NULL, NULL, NULL),
(741, 'FAIRBANKS SCALES INDUSTRIES CORPORATION', '400741', NULL, 'GROUND FLOOR, CNC INVESTMENT BLDG. 231 JUAN LUNA ST., BINONDO, MANILA', NULL, NULL, NULL, '218-656-408-0011', '12%', NULL, NULL, NULL, NULL),
(742, 'ZOOM IN PACKAGES, INC. ( merged w/ ATS)', '400742', NULL, 'IAL WAREHOUSE HERMOSILLA DRIVE COR. BONIFACIO ST., ORMOC CITY', NULL, NULL, NULL, '218-656-408-0011', 'N/A', NULL, NULL, NULL, NULL),
(743, 'J.Z. ELECTRICAL SUPPLY, INC.', '400743', NULL, '805 N Padilla St. cor Mithi St. Zone 067 Brgy 646, San Miguel Manila 1005', NULL, NULL, NULL, '221-087-094-000', '12%', NULL, NULL, NULL, NULL),
(744, 'JD INTERNATIONAL GLASS & ALUMINUM SUPPLY', '400744', NULL, 'TACLOBAN CITY', NULL, NULL, NULL, '221-635-527-001', '12%', NULL, NULL, NULL, NULL),
(745, 'JANDALE ENTERPRISES CORPORATION', '400745', NULL, 'G-19 SOUTH STAR PLAZA, SOUTH SUPERHIGHWAY 1200 MAKATI CITY', NULL, NULL, NULL, '221-715-489-0000', '12%', NULL, NULL, NULL, NULL),
(746, 'GOODWILL TECHNOLOGY & INDUSTRIAL CORPORATION', '400746', NULL, '38 GOV. PASCUAL AVE., POTRERO MALABON CITY', NULL, NULL, NULL, '224-252-041-0000', '12%', NULL, NULL, NULL, NULL),
(747, 'CEBU AGUA LAB, INC.', '400747', NULL, 'UNIT 2J FREESTAR ARCADE H. CORTES ST., SUBANGDAKU, MANDAUE CITY 6014', NULL, NULL, NULL, '225-533-454-0000', '12%', '422-7275 / 422-7276', NULL, NULL, NULL),
(748, 'DIAMOND BRAKE BONDING', '400748', NULL, 'LILIA AVE. COGON ORMOC CITY', NULL, NULL, NULL, '227-709-831-000', 'NV', NULL, NULL, NULL, NULL),
(749, 'THE FIRST FAMILY APPLIANCE CIRCLE CORP.', '400749', NULL, 'GAISANO RIVERSIDE ORMOC CITY', NULL, NULL, NULL, '234-858-579-0007', '12%', NULL, NULL, NULL, NULL),
(750, 'BERCNYL TIRES MARKETING', '400750', NULL, 'Unit 5 AU (Cebu) Ice Bldg M J Cuenco Avenue Cebu City, 6000', NULL, NULL, NULL, '235-678-435-000', '12%', NULL, NULL, NULL, NULL),
(751, 'TECHMESH MACHINERIES AND INDUSTRIAL DISTRIBUTOR', '400751', NULL, 'LAPU-LAPU CITY', NULL, NULL, NULL, '237-036-433-0000', '12%', NULL, NULL, NULL, NULL),
(752, 'INTEGRATED POWER & CONTROL PROVIDER INC.', '400752', NULL, NULL, NULL, NULL, NULL, '238-020-600-000', '12%', NULL, NULL, NULL, NULL),
(753, 'HOME OPTIONS INC.', '400753', NULL, 'HERMOSILLA DRIVE ORMOC CITY', NULL, NULL, NULL, '238-100-552-004', '12%', NULL, NULL, NULL, NULL),
(754, 'SAVERS HOME DEPOT', '400754', NULL, 'Hermosilla Drive Brgy. District 26, Ormoc City', NULL, NULL, NULL, '238-100-552-004', '12%', NULL, NULL, NULL, NULL),
(755, 'CDR - KING', '400755', NULL, NULL, NULL, NULL, NULL, '240-080-836-0180', '12%', NULL, NULL, NULL, NULL),
(756, 'MARKES AUTOFIX SHOP', '400756', NULL, 'BRGY. BANTIGUE ORMOC CITY', NULL, NULL, NULL, '246-900-538-000', 'NV', NULL, NULL, NULL, NULL),
(757, 'EDWIN S. SANTIAGO ENT. CORP.', '400757', NULL, 'Purok 3 Bagong Sikat, Cabiao Nueva Ecija', NULL, NULL, NULL, '246-937-938-000', '12%', NULL, NULL, NULL, NULL),
(758, 'PONGOS, AGAPITO JR.', '400758', NULL, 'ORMOC CITY', NULL, NULL, NULL, '247-161-311-000', '12%', NULL, NULL, 'TS OPERATOR', NULL),
(759, 'YES-JJE MARKETING', '400759', NULL, 'ALABANG ZAPOTE LAS PIÑAS CITY 1740', NULL, NULL, NULL, '249-632-188-000', '12%', NULL, NULL, 'BIR FORM 2303 ATTACHED TO CV#1120080-11/11/20', NULL),
(760, 'TEJARES, SEGUNDINO', '400760', NULL, NULL, NULL, NULL, NULL, '250-263-677-000', 'N/A', NULL, NULL, NULL, NULL),
(761, 'PASSWORD SECURITY AGENCY, INC.', '400761', NULL, 'Alvarico Compound, Quezon Blvd., Cebu City', NULL, NULL, NULL, '251-542-743-0000', '12%', NULL, NULL, NULL, NULL),
(762, 'YALE HARDWARE CORPORATION', '400762', NULL, '1205-1217 C.M. RECTO AVE. BRGY 260 ZONE 024 STA CRUZ MANILA CITY', NULL, NULL, NULL, '252-783-397-000', '12%', NULL, NULL, 'BIR FORM 2303 ATTACHED TO CV#1220031-12/4/20', NULL),
(763, 'PHILIPPINE POWERMC DISTRIBUTOR, INC.', '400763', NULL, 'ORMOC CITY', NULL, NULL, NULL, '254-517-057-004', 'N/A', NULL, NULL, NULL, NULL),
(764, 'TAMAYO, RODOLFO', '400764', NULL, 'ORMOC CITY', NULL, NULL, NULL, '261-017-688-0000', 'NV', NULL, NULL, NULL, NULL),
(765, 'YT INDUSTRIAL TRADING', '400765', NULL, 'San Vicente St. Poblacion Kananga Leyte', NULL, NULL, NULL, '261-983-950-000', 'NV', NULL, NULL, NULL, NULL),
(766, 'RIVERSIDE TAILORING', '400766', NULL, 'DIST. 1 ORMOC CITY', NULL, NULL, NULL, '266-903-691-000', 'NV', NULL, NULL, NULL, NULL),
(767, 'JEMAR INDUSTRIAL SUPPLY AND SERVICES', '400767', NULL, 'MANDAUE CITY, CEBU', NULL, NULL, NULL, '267-778-013-0000', '12%', NULL, NULL, NULL, NULL),
(768, 'PONGOS, ELLA', '400768', NULL, NULL, NULL, NULL, NULL, '268-065-316-000', 'NV', NULL, NULL, 'TS OPERATOR', NULL),
(769, 'MESC AUDIO TECH', '400769', NULL, 'D1, BOSS Bldg, Conejos St., Cogon Combado 6541 Ormoc City Leyte Philippines', NULL, NULL, NULL, '271-980-654-0000', 'NV', NULL, NULL, NULL, NULL),
(770, 'ACME SURPLUS PARTS SUPPLY', '400770', NULL, 'CEBU CITY', NULL, NULL, NULL, '272-478-953-0000', '12%', NULL, NULL, 'YVES NELSON U. UY', NULL),
(771, 'LUZMINDA JAICTIN', '400771', NULL, 'KANANGA, LEYTE', NULL, NULL, NULL, '275-181-994-000', 'N/A', NULL, NULL, NULL, NULL),
(772, 'DEN DEN\'S AUTO PARTS SUPPLY', '400772', NULL, 'JASMIN STREET, BRGY. COGON ORMOC CITY', NULL, NULL, NULL, '276-214-295-0000', 'NV', NULL, NULL, NULL, NULL),
(773, 'POWER UP GASOLINE STATION', '400773', NULL, 'PUROK SAMPAGUITA, BRGY. PUNTA ORMOC CITY', NULL, NULL, NULL, '276-297-153-0000', '12%', NULL, NULL, NULL, NULL),
(774, 'FILHOLLAND CORPORATION', '400774', NULL, 'Lacson Ext. cor. V.L. Yap st., Singcang-airport, Bacolod City - 6100', NULL, NULL, NULL, '276-480-105-001', 'NV', NULL, NULL, NULL, NULL),
(775, '4M RADIATOR REPAIR SHOP', '400775', NULL, 'COGON, ORMOC CITY', NULL, NULL, NULL, '276-837-637-000', 'NV', NULL, NULL, NULL, NULL),
(776, 'GINA SUDE', '400776', NULL, NULL, NULL, NULL, NULL, '277-406-695-000', 'N/A', NULL, NULL, NULL, NULL),
(777, 'SERDONCILLO, CARLOS JR.', '400777', NULL, 'VILLANUEVA, CALUBIAN LEYTE', NULL, NULL, NULL, '281-852-622', 'N/A', NULL, NULL, NULL, NULL),
(778, 'VAL C. CAYETANO', '400778', NULL, NULL, NULL, NULL, NULL, '282-283-665', 'NV', NULL, NULL, NULL, NULL),
(779, 'THE ONE, ONE STOP SHOP GENERAL MERCHANDISE', '400779', NULL, 'Brgy Linao, Ormoc City', NULL, NULL, NULL, '286-121-181-0003', '12%', NULL, NULL, NULL, NULL),
(780, 'IPCAR TECH., INC.', '400780', NULL, NULL, NULL, NULL, NULL, '287-591-588-000', '12%', NULL, NULL, NULL, NULL),
(781, 'A I M GLOBAL PRODUCTS, INC.', '400781', NULL, 'SAN ANTONIO PASIG CITY 1605', NULL, NULL, NULL, '289-315-348-000', '12%', NULL, NULL, 'BIR FORM 2303 ATTACHED TO CV#1120079-11/11/20', NULL),
(782, 'AIM GLOBAL PRODUCTS, INC.', '400782', NULL, 'Pearl Drive, Ortigas Center, Pasig City', NULL, NULL, NULL, '289-315-348-000', '12%', NULL, NULL, NULL, NULL),
(783, 'FIRST GLOBAL ALLIANCE TRANS. SERV., INC.', '400783', NULL, NULL, NULL, NULL, NULL, '291-722-104-000', 'N/A', NULL, '561-0165', NULL, NULL),
(784, 'ROADSTER AUTOSERVICE CENTER', '400784', NULL, 'COGON, ORMOC CITY', NULL, NULL, NULL, '292-414-881-000', '12%', NULL, NULL, NULL, NULL),
(785, 'IULEKS AGRI INPUTS MARKETING', '400785', NULL, 'D. VELOSO ST. BRGY. DOÑA FELIZA MEJIA ORMOC CITY', NULL, NULL, NULL, '294-410-326-002', '12%', NULL, NULL, 'NO BIR FORM 2303-TIN-ATTACHED TO CV#0720131-7/13/20', NULL),
(786, 'CABILING, PAULO', '400786', NULL, 'ORMOC CITY', NULL, NULL, NULL, '298-921-469-0000', 'NV', NULL, NULL, 'HAULER / TIN OF RABADON, ALEJANDRO M. JR.', NULL),
(787, 'MAK AUTO CARE CENTER', '400787', NULL, 'BRGY. BANTIGUE ORMOC CITY', NULL, NULL, NULL, '299-183-946-0000', 'NV', NULL, NULL, NULL, NULL),
(788, 'INDUSTRIAL WELDING CORPORATION', '400788', NULL, '10 R. Jacinto St. Brgy. Canunay West Dist.1, Valenzuela City 1443', NULL, NULL, NULL, '300-236-850-000', '12%', NULL, NULL, NULL, NULL),
(789, 'LUCKY SEVEN MINI LUMBER', '400789', NULL, 'Brgy. Talisayan, Albuera Leyte', NULL, NULL, NULL, '306-418-908-000', 'NV', NULL, NULL, NULL, NULL),
(790, 'RS CHERRY ELECTRONICS SHOP', '400790', NULL, 'Rizal St., District 6 Ormoc City Leyte', NULL, NULL, NULL, '306-816-513-000', 'NV', NULL, NULL, NULL, NULL),
(791, 'KNC PRINTZ', '400791', NULL, 'BRGY. DOÑA FELIZA MEJIA ORMOC CITY', NULL, NULL, NULL, '309-218-989-000', 'NV', NULL, NULL, NULL, NULL),
(792, 'JLRC TRADING', '400792', NULL, 'Blk 1 Lot 35 ph4 Mahogany St. Natividad Subd. Brgy. 168 Zone 15 D1, Caloocan City', NULL, NULL, NULL, '311-956-332-000', '12%', NULL, NULL, NULL, NULL),
(793, 'JYO VARIETY STORE', '400793', NULL, 'Bonifacio St. Ormoc City', NULL, NULL, NULL, '315-301-441-000', 'NV', NULL, NULL, NULL, NULL),
(794, 'WILMAR COCONUT CHARCOAL TRADING', '400794', NULL, 'Brgy. Visares, Capoocan Leyte', NULL, NULL, NULL, '386-234-104-0001', 'NV', NULL, NULL, NULL, NULL),
(795, 'RIEKI ENTERPRISES', '400795', NULL, 'REAL STREET ORMOC CITY', NULL, NULL, NULL, '405-150-217-0004', '12%', NULL, NULL, NULL, NULL),
(796, 'CLEANAWAY PHILIPPINES INC.', '400796', NULL, '4/F HERMA BLDG. 94 SCOUT RALLOS ST. BRGY SACRED HEART DIST 4, QUEZON CITY', NULL, NULL, NULL, '408-104-683-000', '12%', NULL, NULL, NULL, NULL),
(797, 'LAGAHIT, MA. CRISTINA DEVIE P.', '400797', NULL, 'ORMOC CITY', NULL, NULL, NULL, '410-500-525-0000', 'NV', NULL, NULL, 'HAULER ; C/O REMEDIOS PEPITO', NULL),
(798, 'ABOMAR EQUIPMENT SALES CORPORATION', '400798', NULL, 'BACOLOD CITY', NULL, NULL, NULL, '411-846-093-0000', '12%', '(034) 432-3673', NULL, 'BIR FROM 2303 @ MATERIAL FILE FOLDER', NULL),
(799, 'DEMATE, MELLY', '400799', NULL, 'KAWAYAN, BILIRAN ISLAND', NULL, NULL, NULL, '411-983-152-0000', 'NV', NULL, NULL, 'COCOSHELL SUPPLIER', NULL),
(800, 'GERMEL RUBBER PRODUCTS TRADING', '400800', NULL, 'ORMOC CITY', NULL, NULL, NULL, '412-967-837-000', 'NV', NULL, NULL, NULL, NULL),
(801, 'EDBS BOLT CENTER ENTERPRISES', '400801', NULL, 'Bonifacio St. Ormoc City', NULL, NULL, NULL, '414-029-255-000', 'NV', NULL, NULL, NULL, NULL),
(802, 'UN HARDWARE AND AUTO SUPPLY', '400802', NULL, 'ORMOC CITY', NULL, NULL, NULL, '416-357-121-0000', '12%', NULL, NULL, NULL, NULL),
(803, 'GIGATOOLS INDUSTRIAL CENTER', '400803', NULL, '44 Makaturing St. Brgy. Manresa, Quezon City', NULL, NULL, NULL, '418-271-866-000', '12%', NULL, NULL, NULL, NULL),
(804, 'JALC TRADING', '400804', NULL, 'Unit 1 2nd floor 152 F.Blumentritt st. Batis 1500 City of San Juan NCR', NULL, NULL, NULL, '430-052-280-000', '12%', NULL, NULL, NULL, NULL),
(805, 'THA WAN DEPARTMENT STORE', '400805', NULL, 'Purok 7. Linao Ormoc City, 6541', NULL, NULL, NULL, '431-051-648-0002', '12%', NULL, NULL, NULL, NULL),
(806, 'ORMOC DOCTORS\' HOSPITAL, INC.', '400806', NULL, 'COR AVILES & SAN PEDRO STS. ORMOC CITY', NULL, NULL, NULL, '432-556-595-000', 'E', NULL, NULL, NULL, NULL),
(807, 'PEPITO, EVANGELINE', '400807', NULL, 'BRGY. CAN-UNTOG ORMOC CITY', NULL, NULL, NULL, '435-191-274-0000', 'NV', NULL, NULL, 'HAULER', NULL),
(808, 'GMQ ENTERPRISES', '400808', NULL, 'ORMOC CITY', NULL, NULL, NULL, '442-451-669-0000', 'NV', NULL, NULL, NULL, NULL),
(809, 'EMERALD MINI DEPOT CORPORATION', '400809', NULL, 'cor. Osmena & Mabini St. District 17, Ormoc City', NULL, NULL, NULL, '449-720-955-000', '12%', NULL, NULL, NULL, NULL),
(810, 'CAPITAL F MARKETING & SERVICES', '400810', NULL, 'ROOM 305 SANCHEZ BLDG #68 SANCIANGCO ST. CEBU CITY', NULL, NULL, NULL, '452-435-682-0000', 'NV', NULL, NULL, 'PROP.:LANIE FE A. FAMILGAN', NULL),
(811, 'GO ENTERPRISES', '400811', NULL, 'MEJIA SUBDIVISION ORMOC CITY', NULL, NULL, NULL, '452-776-157-000', 'NV', '0917-843-4440', NULL, 'TIN ATTACHED CV#0820061-8/10/20', NULL),
(812, 'R.E. RADIATOR SHOP', '400812', NULL, 'ORMOC CITY', NULL, NULL, NULL, '453-401-326-0000', 'NV', NULL, NULL, NULL, NULL),
(813, 'SAMSON PERALES', '400813', NULL, 'CAMBALADING, ALBUERA LEYTE', NULL, NULL, NULL, '453-943-892', 'NV', NULL, NULL, NULL, NULL),
(814, 'OGUZ ENGINEERING SERVICES', '400814', NULL, 'VICTORIAS CITY NEGROS OCCIDENTAL', NULL, NULL, NULL, '454-430-339-000', '12%', NULL, NULL, NULL, NULL),
(815, 'RAYMUND JAY SOLANO', '400815', NULL, NULL, NULL, NULL, NULL, '459-105-871', 'N/A', NULL, NULL, NULL, NULL),
(816, 'MACROHON AUTOMATION AND INDUSTRIAL SERVICES CORPORATION', '400816', NULL, 'LAPU-LAPU CITY', NULL, NULL, NULL, '461-286-692-000', '12%', '032-384-8988', NULL, NULL, NULL),
(817, 'PEPITO, DOMINADOR', '400817', NULL, 'BRGY. CONCEPCION ORMOC CITY', NULL, NULL, NULL, '463-886-596', 'NV', NULL, NULL, NULL, NULL),
(818, 'LARRAZABAL, JULIO ALFONSO', '400818', NULL, 'ORMOC CITY', NULL, NULL, NULL, '465-044-061-0000', 'NV', NULL, NULL, 'HAULER', NULL),
(819, 'CATIGBE, VINA DINZO', '400819', NULL, 'ORMOC CITY', NULL, NULL, NULL, '468-744-968-0000', 'NV', NULL, NULL, 'COCOSHELL SUPPLIER', NULL),
(820, 'ABARICO AUTO REPAIR SHOP', '400820', NULL, 'BRGY. LINAO ORMOC CITY', NULL, NULL, NULL, '470-670-059-000', 'NV', NULL, NULL, NULL, NULL),
(821, 'DUETES, JERSON', '400821', NULL, 'ORMOC CITY', NULL, NULL, NULL, '471-373-253', 'NV', NULL, NULL, 'HAULER', NULL),
(822, 'GINA\'S RUBBER STAMP', '400822', NULL, 'ORMOC CITY', NULL, NULL, NULL, '485-743-699-000', 'NV', NULL, NULL, NULL, NULL),
(823, 'TUMULAK, VIVIAN', '400823', NULL, 'BOROC, ORMOC CITY', NULL, NULL, NULL, '497-177-788', 'N/A', NULL, NULL, NULL, NULL),
(824, 'DKSH MARKET EXPANSION SERVICES PHILIPPINES, INC.', '400824', NULL, '8F Cyber Sigma Lawton Ave. Fort Bonifacio Mckinley West 1634 Taguig City', NULL, NULL, NULL, '601-297-580-000', '12%', NULL, NULL, NULL, NULL),
(825, 'JAMES CONNIE ESPINOSA', '400825', NULL, 'Natugban, Kananga Leyte', NULL, NULL, NULL, '601-796-855-000', 'NV', NULL, NULL, NULL, NULL),
(826, 'YES-JJE MARKETING CORPORATION', '400826', NULL, '2nd floor #435 Alabang Zaporte Road, Talon Uno 1747 City of Las Piñas NCR, Fourth District Philippines', NULL, NULL, NULL, '606-730-811-000', 'N/A', NULL, NULL, NULL, NULL),
(827, 'INFOTEKPHL I.T. SOLUTIONS', '400827', NULL, 'Room 315 No.32 DM Bldg. Visayas corner Congressional Bahay Toro 1106 Quezon City - 00002 NCR, 2nd District Philippines', NULL, NULL, NULL, '619-074-982-0000', '12%', NULL, NULL, NULL, NULL),
(828, 'LEYTE AGRICULTURE COOPERATIVE', '400828', NULL, 'MONTEBELLO ORMOC CITY', NULL, NULL, NULL, '702-311-032-0000', 'NV', NULL, NULL, 'HAULER', NULL),
(829, 'ARJAY CAYETANO', '400829', NULL, 'SAN ANTONIO ORMOC CITY', NULL, NULL, NULL, '704-515-593', 'NV', NULL, NULL, NULL, NULL),
(830, 'JHING INDUSTRIAL SUPPLY', '400830', NULL, 'TANDANG SORA AVE COR COMMONWEALTH CULIAT 6 QUEZON CITY', NULL, NULL, NULL, '723-042-102-000', '12%', NULL, NULL, 'BIR FORM 2303 ATTACHED TO CV#0720067-7/4/20', NULL),
(831, 'NETWORKZ TECHNOLOGY CORPORATION', '400831', NULL, 'BONIFACIO EXT, ST. DIST. 19 ORMOC CITY', NULL, NULL, NULL, '760-723-457-001', '12%', NULL, NULL, NULL, NULL),
(832, 'SAMINSCO INDUSTRIAL SALES, INCORPORATED', '400832', NULL, '57 KING CENTER BUILDING SGT EMELIO RIVERA ST. BRGY. MANRESA, QUEZON CITY 1115', NULL, NULL, NULL, '771-252-390-000', '12%', NULL, NULL, NULL, NULL),
(833, 'LPC ELECTROTRADING OPC', '400833', NULL, '1717 A. Mendoza Ave. Brgy. 338 Zone 34 Sta. Cruz, Manila, 1014', NULL, NULL, NULL, '775-966-642-000', '12%', NULL, NULL, NULL, NULL),
(834, 'JMC POWER OPC', '400834', NULL, 'LILIA AVENUE BRGY COGON, ORMOC CITY', NULL, NULL, NULL, '777-445-005-000', '12%', NULL, NULL, NULL, NULL),
(835, 'DEMETERIO, RODRIGO', '400835', NULL, 'ORMOC CITY', NULL, NULL, NULL, '900-355-690-0000', 'NV', NULL, NULL, 'COCOSHELL SUPPLIER', NULL),
(836, 'NG CHUA TRADING', '400836', NULL, 'BRGY 512 SAMPALOC MANILA', NULL, NULL, NULL, '900-917-571-0000', '12%', NULL, NULL, NULL, NULL),
(837, 'MERLINDA C. NUEVO', '400837', NULL, NULL, NULL, NULL, NULL, '901-124-102', 'N/A', NULL, NULL, NULL, NULL),
(838, 'OLEIC ELECTRICAL SUPPLY', '400838', NULL, 'MAKATI CITY - 4027', NULL, NULL, NULL, '906-354-056-000', '12%', NULL, NULL, NULL, NULL),
(839, 'SHERDONE TRUCKING SERVICES', '400839', NULL, NULL, NULL, NULL, NULL, '906-496-155', 'N/A', NULL, NULL, NULL, NULL),
(840, 'NEVILLE MANPOWER SERVICES', '400840', NULL, NULL, NULL, NULL, NULL, '906-825-986', 'N/A', NULL, NULL, NULL, NULL),
(841, 'CERO, EDMUND', '400841', NULL, NULL, NULL, NULL, NULL, '911-544-590-0000', 'N/A', NULL, NULL, NULL, NULL),
(842, 'MONSAN CONSTRUCTION', '400842', NULL, '#17 ANGELA ST. MAYSILO  MALABON CITY', NULL, NULL, NULL, '911-775-385-000', '12%', NULL, NULL, NULL, NULL),
(843, 'CABILING, REYNALDO', '400843', NULL, NULL, NULL, NULL, NULL, '912-712-924-0000', 'N/A', NULL, NULL, NULL, NULL),
(844, 'MENDOZA, ROSENDO', '400844', NULL, NULL, NULL, NULL, NULL, '912-726-195', 'N/A', NULL, NULL, NULL, NULL),
(845, 'AIRTAC COMPRESOR PARTS AND SERVICES', '400845', NULL, '3/F Mafee Comml. Bldg. 387 Molino III, Bacoor Cavity, 4102', NULL, NULL, NULL, '914-830-552-000', '12%', NULL, NULL, NULL, NULL),
(846, 'MERAH SHOE SHOP', '400846', NULL, 'COGON ORMOC CITY', NULL, NULL, NULL, '914-949-741-000', 'NV', NULL, NULL, NULL, NULL),
(847, 'FORTUNA, MA. LILIBETH', '400847', NULL, 'ORMOC CITY', NULL, NULL, NULL, '915-327-840-0000', 'NV', NULL, NULL, 'HAULER', NULL),
(848, 'BERMUDO, WILSON', '400848', NULL, NULL, NULL, NULL, NULL, '917-681-851-0000', 'N/A', NULL, NULL, NULL, NULL),
(849, 'SHERYL TRACTOR MARKETING', '400849', NULL, 'Brgy. Bantigue, Ormoc City', NULL, NULL, NULL, '919-698-251-000', 'NV', NULL, NULL, NULL, NULL),
(850, 'CLARETE, JAMES', '400850', NULL, NULL, NULL, NULL, NULL, '920-844-383-0000', 'N/A', NULL, NULL, NULL, NULL),
(851, 'SANICO BRAKE BONDING', '400851', NULL, 'SAN NICOLAS ST., ORMOC CITY', NULL, NULL, NULL, '920-849-468-000', 'NV', NULL, NULL, 'PROP.: VICTOR L. SANICO', NULL),
(852, 'JEFFREY L. DEL SOCORRO', '400852', NULL, 'Maglasang Bldg, Real st. Ormoc City', NULL, NULL, NULL, '920-852-263-000', 'NV', NULL, NULL, NULL, NULL),
(853, 'SACAY, LEONILA', '400853', NULL, NULL, NULL, NULL, NULL, '920-852-958', 'N/A', NULL, NULL, NULL, NULL),
(854, 'EKHO RURAL DEVELOPMENT ENTERPRISE', '400854', NULL, 'AGAN-AN SIBULAN NEGROS ORIENTAL', NULL, NULL, NULL, '921-246-711-000', '12%', NULL, NULL, 'BIR FROM 2303 ATTACHED CV#0720033-7/1/20', NULL),
(855, 'ESALIG BUILDERS', '400855', NULL, NULL, NULL, NULL, NULL, '921-292-731', '12%', NULL, NULL, NULL, NULL),
(856, 'LIGNES, EDGAR', '400856', NULL, NULL, NULL, NULL, NULL, '921-292-731', 'N/A', NULL, NULL, NULL, NULL),
(857, 'CELLCOM WORLD COMMUNICATIONS TRADING', '400857', NULL, 'corner Aviles & Bonifacio St. Brgy 7 (Pob.) 6541, Ormoc City Leyte Philippines', NULL, NULL, NULL, '922-383-162-0075', '12%', NULL, NULL, NULL, NULL),
(858, 'AGCANG, GERSON', '400858', NULL, 'HERMOSILLA DRIVE, ORMOC CITY', NULL, NULL, NULL, '922-419-353', 'N/A', NULL, NULL, NULL, NULL),
(859, 'VELASQUEZ, BELEN', '400859', NULL, NULL, NULL, NULL, NULL, '922-436-108', 'N/A', NULL, NULL, NULL, NULL),
(860, 'W AND R MATTRESS KING', '400860', NULL, 'ORMOC CITY', NULL, NULL, NULL, '922-438-532-0000', '12%', NULL, NULL, 'PROP.: NICK WENDEL O. CHU', NULL),
(861, 'VALENCIA SLING CABLE WIRE', '400861', NULL, 'VALENCIA, ORMOC CITY', NULL, NULL, NULL, '925-726-819-0000', 'N/A', NULL, NULL, NULL, NULL),
(862, 'ALLEYAH MARIE ENT. & CONSTRUCTION SERVICES', '400862', NULL, 'KANANGA LEYTE', NULL, NULL, NULL, '925-730-160-000', 'NV', NULL, NULL, NULL, NULL),
(863, 'MICHAEL ALEGRE', '400863', NULL, NULL, NULL, NULL, NULL, '925-734-278-000', 'NV', NULL, NULL, NULL, NULL),
(864, 'DOLINO, ARSENIO JR.', '400864', NULL, NULL, NULL, NULL, NULL, '925-738-369', 'NV', NULL, NULL, NULL, NULL),
(865, 'ARMEA\'S ENTERPRISES', '400865', NULL, 'LILIA AVENUE, BRGY. COGON ORMOC CITY', NULL, NULL, NULL, '927-090-809-0000', 'NV', NULL, NULL, 'PROP.: JUANITA Y. ARMEA', NULL),
(866, 'CAPAHI, EUTIQUIA', '400866', NULL, NULL, NULL, NULL, NULL, '928-639-092-0000', 'N/A', NULL, NULL, NULL, NULL),
(867, 'MERCADO, IAN', '400867', NULL, 'BRGY. LUNA ORMOC CITY', NULL, NULL, NULL, '928-645-597-0000', 'NV', NULL, NULL, 'HAULER', NULL),
(868, 'ARQUILLANO, JAIME', '400868', NULL, 'ORMOC CITY', NULL, NULL, NULL, '928-649-566', 'NV', NULL, NULL, 'HAULER', NULL),
(869, 'EMERALD CERAMIC BARGAIN CENTER', '400869', NULL, NULL, NULL, NULL, NULL, '929-280-713-0000', '12%', NULL, NULL, NULL, NULL),
(870, 'R & C NATIVE PRODUCTS', '400870', NULL, 'MABINI ST. ORMOC CITY', NULL, NULL, NULL, '930-216-312-001', 'NV', NULL, NULL, NULL, NULL),
(871, 'TAN, MARY ANNE CONCEPTION', '400871', NULL, 'ORMOC CITY', NULL, NULL, NULL, '930-218-000-0000', 'N/A', NULL, NULL, NULL, NULL),
(872, 'ZABALA, CHERILL', '400872', NULL, NULL, NULL, NULL, NULL, '930-220-850-0000', 'NV', NULL, NULL, 'COCOSHELL SUPPLIER', NULL),
(873, 'GUILLEMER, ERIBERTO III', '400873', NULL, 'BRGY. ALEGREA ORMOC CITY', NULL, NULL, NULL, '930-222-255-0000', 'NV', NULL, NULL, 'HAULER', NULL),
(874, 'LA VICTORIA TRADING', '400874', NULL, 'Mabini st., District 4, Ormoc City - 6541', NULL, NULL, NULL, '930-222-423-000', 'NV', NULL, NULL, NULL, NULL),
(875, 'MENDOZA, ROSARIO', '400875', NULL, NULL, NULL, NULL, NULL, '930-569-770', 'N/A', NULL, NULL, NULL, NULL),
(876, 'MERCADO, MARILOU', '400876', NULL, 'ORMOC CITY', NULL, NULL, NULL, '932-299-421-0000', 'N/A', NULL, NULL, NULL, NULL),
(877, 'PABELAR, ERNESTO', '400877', NULL, NULL, NULL, NULL, NULL, '933-246-734', 'N/A', NULL, NULL, NULL, NULL),
(878, 'RED DRAGON GENERAL MERCHANDISE', '400878', NULL, 'F.A. Larrazabal st, corner San Vicente st., Kananga Leyte', NULL, NULL, NULL, '934-859-208-002', '12%', NULL, NULL, NULL, NULL),
(879, 'CATALINO MERCHANDISING', '400879', NULL, 'P. Chrysanthemum, Cogon Combado, Ormoc City', NULL, NULL, NULL, '934-974-312-000', '12%', NULL, NULL, NULL, NULL),
(880, 'LILAN CHAINSAW HOUSE', '400880', NULL, 'ORMOC CITY', NULL, NULL, NULL, '936-206-749-0000', 'NV', NULL, NULL, NULL, NULL),
(881, 'MANDAWE, IVAN', '400881', NULL, 'ORMOC CITY', NULL, NULL, NULL, '937-609-157-0000', 'NV', NULL, NULL, 'COCOSHELL SUPPLIER', NULL),
(882, 'E-STORE GENERAL MERCHANDISE', '400882', NULL, 'E. CONEJOS ST., COGON, ORMOC CITY', NULL, NULL, NULL, '937-623-453-0000', '12%', NULL, NULL, NULL, NULL),
(883, 'BLANCO, MA. MARTHA B.', '400883', NULL, NULL, NULL, NULL, NULL, '940-602-460-0000', 'N/A', NULL, NULL, NULL, NULL),
(884, 'SOLANO, ARNOLD', '400884', NULL, 'BOROC ORMOC CITY', NULL, NULL, NULL, '940-741-950-0000', 'NV', NULL, NULL, 'HAULER', NULL),
(885, 'GREENWARE CUSTOMIZED SYSTEMS & PC ACCESSORIES', '400885', NULL, 'J. Navarro St., District XII, Ormoc City', NULL, NULL, NULL, '940-746-025-008', '12%', NULL, NULL, NULL, NULL),
(886, 'JABON, NILDA', '400886', NULL, 'ALBUERA LEYTE', NULL, NULL, NULL, '943-246-929-0000', 'NV', NULL, NULL, 'HAULER', NULL),
(887, 'BERMUDO, ANTONIETA', '400887', NULL, NULL, NULL, NULL, NULL, '945-554-384-0000', 'N/A', NULL, NULL, NULL, NULL),
(888, 'PARATROOPERS TRUCKING SERVICES', '400888', NULL, 'MAHARLIKA HI-WAY CALIBAAN TACLOBAN CITY', NULL, NULL, NULL, '947-534-871-001', 'NV', NULL, NULL, NULL, NULL),
(889, 'DEEN, RAPHAEL JHED', '400889', NULL, 'ORMOC CITY', NULL, NULL, NULL, '947-754-525-0000', 'NV', NULL, NULL, 'HAULER', NULL),
(890, 'EARL CYCLE PARTS AND ACCESSORIES CENTER', '400890', NULL, 'OSMEÑA STREET ORMOC CITY', NULL, NULL, NULL, '947-756-480-002', '12%', NULL, NULL, 'TIN ATTACHED CV#0820128-8/14/20', NULL),
(891, 'HORBINO, ADRIAN', '400891', NULL, 'ORMOC CITY', NULL, NULL, NULL, '947-757-795', 'N/A', NULL, NULL, NULL, NULL),
(892, 'CODOG, CLARA', '400892', NULL, 'BOROC, ORMOC CITY', NULL, NULL, NULL, '947-767-439', 'N/A', NULL, NULL, NULL, NULL),
(893, 'MERLITA\'S GENERAL MERCHANDISE', '400893', NULL, 'Aguting, Kananga Leyte', NULL, NULL, NULL, '949-072-699-000', 'NV', NULL, NULL, NULL, NULL),
(894, 'AMERICAN BEARING MARKETING CORPORATION', '400894', NULL, '1265 CM Recto Ave. Brgy. 260 Zone 024 1013 Tondo City of Manila', NULL, NULL, NULL, '000-845-301-000', '12%', NULL, NULL, NULL, NULL),
(895, 'CONTROTEK SOLUTIONS INC.', '400895', NULL, 'Unit 4B-1 & 4B-2 Aro Bldg. Alabang-Zapote Rd. cor. Victor Buencamino St. Brgy Cupang Muntinlupa City', NULL, NULL, NULL, '008-333-141-000', '12%', NULL, NULL, NULL, NULL),
(896, 'EVC CONSULTANCY AND ENGINEERING SERVICES', '400896', NULL, 'Brgy. Dayhagan, Ormco City', NULL, NULL, NULL, '433-589-460-000', '12%', NULL, NULL, NULL, NULL),
(897, 'CRUZ, FRANK EUGENIO', '400897', NULL, NULL, NULL, NULL, NULL, '147-999-334-000', 'N/A', NULL, NULL, NULL, NULL),
(898, 'Hideco Sugar Milling Company Inc.', 'HSM001', '', 'ABC', '', '', '', '000123533000', '12%', '', '', '', 'Inventory'),
(899, 'M. B. CARGO VENTURES, INC.', '', '12345', 'RM. 108 G/F LEDESMA BLDG. GEN. LUNA ST. INTRAMUROS MANILA.', '8253-5910', '', '30', '226-215-955-000', '12%', '8253-5910', '8527-4069', '', 'Inventory');

-- --------------------------------------------------------

--
-- Table structure for table `wchecks`
--

CREATE TABLE `wchecks` (
  `id` int(11) NOT NULL,
  `cv_no` varchar(50) DEFAULT NULL,
  `check_no` varchar(50) DEFAULT NULL,
  `ref_no` varchar(50) NOT NULL,
  `check_date` date NOT NULL,
  `account_id` int(11) NOT NULL,
  `payee_type` varchar(50) NOT NULL,
  `payee_id` int(11) NOT NULL,
  `memo` text NOT NULL,
  `location` int(11) NOT NULL,
  `gross_amount` double(10,2) NOT NULL,
  `discount_amount` double(10,2) NOT NULL,
  `net_amount_due` double(10,2) NOT NULL,
  `vat_percentage_amount` double(10,2) NOT NULL,
  `net_of_vat` double(10,2) NOT NULL,
  `tax_withheld_amount` double(10,2) DEFAULT NULL,
  `tax_withheld_percentage` int(11) NOT NULL,
  `total_amount_due` double(10,2) NOT NULL,
  `discount_account_id` int(11) DEFAULT NULL,
  `input_vat_account_id` int(11) DEFAULT NULL,
  `tax_withheld_account_id` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) DEFAULT NULL,
  `print_status` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wchecks_details`
--

CREATE TABLE `wchecks_details` (
  `id` int(11) NOT NULL,
  `wcheck_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `cost_center_id` int(11) DEFAULT NULL,
  `memo` text NOT NULL,
  `amount` double(10,2) NOT NULL,
  `discount_percentage` double(10,2) DEFAULT 0.00,
  `discount_amount` double(10,2) DEFAULT NULL,
  `net_amount_before_vat` double(10,2) NOT NULL,
  `net_amount` double(10,2) DEFAULT NULL,
  `vat_percentage` double(10,0) DEFAULT 0,
  `input_vat` double(10,2) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wtax`
--

CREATE TABLE `wtax` (
  `id` int(11) NOT NULL,
  `wtax_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `wtax_rate` float NOT NULL,
  `wtax_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `wtax_account_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wtax`
--

INSERT INTO `wtax` (`id`, `wtax_name`, `wtax_rate`, `wtax_description`, `wtax_account_id`, `created_at`) VALUES
(1, 'C-N/A', 0, 'C-N/A', 3224, '2024-09-06 01:16:48'),
(2, 'C-Goods 1%', 1, 'C-Goods 1%', 3224, '2024-09-06 01:17:00'),
(3, 'C-Services 2%', 2, 'C-Services 2%', 3224, '2024-09-06 01:18:28'),
(4, 'V-N/A', 0, 'V-N/A', 4036, '2024-09-06 01:18:44'),
(5, 'V-Goods 1%', 1, 'V-Goods 1%', 4036, '2024-09-06 01:19:11'),
(6, 'V-Services 2%', 2, 'V-Services 2%', 4036, '2024-09-06 01:20:21'),
(7, 'V-Rent 5%', 5, 'V-Rent 5%', 4036, '2024-09-06 01:22:51'),
(8, 'V-10%', 10, 'V-10%', 4036, '2024-09-06 01:23:05'),
(9, '12412', 124, '124412', 1, '2024-10-17 15:22:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_types`
--
ALTER TABLE `account_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `apv`
--
ALTER TABLE `apv`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `apv_details`
--
ALTER TABLE `apv_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `audit_trail`
--
ALTER TABLE `audit_trail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `chart_of_account`
--
ALTER TABLE `chart_of_account`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `checks`
--
ALTER TABLE `checks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company_settings`
--
ALTER TABLE `company_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cost_center`
--
ALTER TABLE `cost_center`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `credit_memo`
--
ALTER TABLE `credit_memo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `credit_memo_details`
--
ALTER TABLE `credit_memo_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `discount`
--
ALTER TABLE `discount`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fs_classification`
--
ALTER TABLE `fs_classification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fs_notes_classification`
--
ALTER TABLE `fs_notes_classification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `general_journal`
--
ALTER TABLE `general_journal`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `general_journal_details`
--
ALTER TABLE `general_journal_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `input_vat`
--
ALTER TABLE `input_vat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_valuation`
--
ALTER TABLE `inventory_valuation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `material_issuance`
--
ALTER TABLE `material_issuance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `material_issuance_details`
--
ALTER TABLE `material_issuance_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `or_payments`
--
ALTER TABLE `or_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `or_payment_details`
--
ALTER TABLE `or_payment_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `other_name`
--
ALTER TABLE `other_name`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_credit_details`
--
ALTER TABLE `payment_credit_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_details`
--
ALTER TABLE `payment_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_method`
--
ALTER TABLE `payment_method`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_order`
--
ALTER TABLE `purchase_order`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_order_details`
--
ALTER TABLE `purchase_order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `po_id` (`po_id`);

--
-- Indexes for table `purchase_request`
--
ALTER TABLE `purchase_request`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_request_details`
--
ALTER TABLE `purchase_request_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `receive_items`
--
ALTER TABLE `receive_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `receive_item_details`
--
ALTER TABLE `receive_item_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales_invoice`
--
ALTER TABLE `sales_invoice`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales_invoice_details`
--
ALTER TABLE `sales_invoice_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales_return`
--
ALTER TABLE `sales_return`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales_return_details`
--
ALTER TABLE `sales_return_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales_tax`
--
ALTER TABLE `sales_tax`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `terms`
--
ALTER TABLE `terms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaction_entries`
--
ALTER TABLE `transaction_entries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `uom`
--
ALTER TABLE `uom`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_module_access`
--
ALTER TABLE `user_module_access`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_role_module_access`
--
ALTER TABLE `user_role_module_access`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wchecks`
--
ALTER TABLE `wchecks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wchecks_details`
--
ALTER TABLE `wchecks_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wtax`
--
ALTER TABLE `wtax`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account_types`
--
ALTER TABLE `account_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `apv`
--
ALTER TABLE `apv`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `apv_details`
--
ALTER TABLE `apv_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_trail`
--
ALTER TABLE `audit_trail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `chart_of_account`
--
ALTER TABLE `chart_of_account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `checks`
--
ALTER TABLE `checks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_settings`
--
ALTER TABLE `company_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cost_center`
--
ALTER TABLE `cost_center`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `credit_memo`
--
ALTER TABLE `credit_memo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `credit_memo_details`
--
ALTER TABLE `credit_memo_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `discount`
--
ALTER TABLE `discount`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `fs_classification`
--
ALTER TABLE `fs_classification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `fs_notes_classification`
--
ALTER TABLE `fs_notes_classification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `general_journal`
--
ALTER TABLE `general_journal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `general_journal_details`
--
ALTER TABLE `general_journal_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `input_vat`
--
ALTER TABLE `input_vat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_valuation`
--
ALTER TABLE `inventory_valuation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3954;

--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `material_issuance`
--
ALTER TABLE `material_issuance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `material_issuance_details`
--
ALTER TABLE `material_issuance_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `or_payments`
--
ALTER TABLE `or_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `or_payment_details`
--
ALTER TABLE `or_payment_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `other_name`
--
ALTER TABLE `other_name`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_credit_details`
--
ALTER TABLE `payment_credit_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_details`
--
ALTER TABLE `payment_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_method`
--
ALTER TABLE `payment_method`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order`
--
ALTER TABLE `purchase_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order_details`
--
ALTER TABLE `purchase_order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_request`
--
ALTER TABLE `purchase_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_request_details`
--
ALTER TABLE `purchase_request_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `receive_items`
--
ALTER TABLE `receive_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `receive_item_details`
--
ALTER TABLE `receive_item_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_invoice`
--
ALTER TABLE `sales_invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `sales_invoice_details`
--
ALTER TABLE `sales_invoice_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sales_return`
--
ALTER TABLE `sales_return`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_return_details`
--
ALTER TABLE `sales_return_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_tax`
--
ALTER TABLE `sales_tax`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `terms`
--
ALTER TABLE `terms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `transaction_entries`
--
ALTER TABLE `transaction_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `uom`
--
ALTER TABLE `uom`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `user_module_access`
--
ALTER TABLE `user_module_access`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `user_role_module_access`
--
ALTER TABLE `user_role_module_access`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=204;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=900;

--
-- AUTO_INCREMENT for table `wchecks`
--
ALTER TABLE `wchecks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wchecks_details`
--
ALTER TABLE `wchecks_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wtax`
--
ALTER TABLE `wtax`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
