-- MySQL dump 10.13  Distrib 8.0.39, for Linux (x86_64)
--
-- Host: localhost    Database: laperla
-- ------------------------------------------------------
-- Server version	8.0.39-0ubuntu0.22.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `account_types`
--

DROP TABLE IF EXISTS `account_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `account_types` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `type_order` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `account_types`
--

LOCK TABLES `account_types` WRITE;
/*!40000 ALTER TABLE `account_types` DISABLE KEYS */;
INSERT INTO `account_types` VALUES (1,'Accounts Payable','LIABILITIES',6),(2,'Accounts Receivable','ASSETS',2),(3,'Other Current Assets','ASSETS',3),(4,'Other Current Liabilities','LIABILITIES',7),(5,'Other Expense','OTHER EXPENSE',15),(6,'Other Income','OTHER INCOME',14),(7,'Fixed Assets','ASSETS',4),(8,'Loans Payable','LIABILITIES',8),(9,'Cost of Goods Sold','COST OF GOODS SOLD',12),(10,'Equity','EQUITY',10),(11,'Expenses','EXPENSE',13),(12,'Income','INCOME',11),(13,'Non-current Liabilities','LIABILITIES',0),(14,'Cash and Cash Equivalents','ASSETS',1),(15,'Other Non-current Liabilities','LIABILITIES',9),(16,'Other Non-current Assets','ASSETS',5);
/*!40000 ALTER TABLE `account_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `apv`
--

DROP TABLE IF EXISTS `apv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `apv` (
  `id` int NOT NULL AUTO_INCREMENT,
  `account_id` int DEFAULT NULL,
  `apv_no` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ref_no` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `vendor_id` int DEFAULT NULL,
  `apv_date` date DEFAULT NULL,
  `apv_due_date` date DEFAULT NULL,
  `terms_id` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `memo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `locations` int NOT NULL,
  `gross_amount` double(10,2) DEFAULT NULL,
  `discount_amount` double(10,2) DEFAULT NULL,
  `net_amount_due` double(10,2) DEFAULT NULL,
  `vat_percentage_amount` double(10,2) DEFAULT NULL,
  `net_of_vat` double(10,2) DEFAULT NULL,
  `tax_withheld_amount` double(10,2) DEFAULT NULL,
  `tax_withheld_percentage` int NOT NULL,
  `wtax_account_id` int DEFAULT NULL,
  `total_amount_due` double(10,2) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `print_status` int NOT NULL DEFAULT '0',
  `po_no` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rr_no` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `vendor_tin` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `apv`
--

LOCK TABLES `apv` WRITE;
/*!40000 ALTER TABLE `apv` DISABLE KEYS */;
/*!40000 ALTER TABLE `apv` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `apv_details`
--

DROP TABLE IF EXISTS `apv_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `apv_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `apv_id` int DEFAULT NULL,
  `account_id` int DEFAULT NULL,
  `cost_center_id` int DEFAULT NULL,
  `memo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `amount` double(10,2) DEFAULT NULL,
  `discount_percentage` double(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` double(10,2) DEFAULT NULL,
  `net_amount_before_vat` double(10,2) DEFAULT NULL,
  `net_amount` double(10,2) DEFAULT NULL,
  `vat_percentage` double(5,2) NOT NULL DEFAULT '0.00',
  `input_vat` double(10,2) DEFAULT NULL,
  `discount_account_id` int NOT NULL,
  `input_vat_account_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `apv_details`
--

LOCK TABLES `apv_details` WRITE;
/*!40000 ALTER TABLE `apv_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `apv_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_trail`
--

DROP TABLE IF EXISTS `audit_trail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_trail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `transaction_id` int DEFAULT NULL,
  `transaction_type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `transaction_date` date DEFAULT NULL,
  `ref_no` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `location` int DEFAULT '0',
  `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `item` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `qty_sold` int DEFAULT NULL,
  `qty_purch` decimal(15,2) NOT NULL DEFAULT '0.00',
  `ave_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `sell_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `cogs_sold` decimal(15,2) NOT NULL DEFAULT '0.00',
  `amt_sold` decimal(15,2) NOT NULL DEFAULT '0.00',
  `account_id` int NOT NULL,
  `debit` double(15,2) DEFAULT '0.00',
  `credit` double(15,2) DEFAULT '0.00',
  `created_by` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `state` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_trail`
--

LOCK TABLES `audit_trail` WRITE;
/*!40000 ALTER TABLE `audit_trail` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_trail` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `after_audit_trail_insert` AFTER INSERT ON `audit_trail` FOR EACH ROW BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Goods');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chart_of_account`
--

DROP TABLE IF EXISTS `chart_of_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chart_of_account` (
  `id` int NOT NULL AUTO_INCREMENT,
  `account_code` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `account_type_id` int NOT NULL,
  `account_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `account_description` varchar(250) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `balance` double(15,2) DEFAULT '0.00',
  `sub_account_id` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chart_of_account`
--

LOCK TABLES `chart_of_account` WRITE;
/*!40000 ALTER TABLE `chart_of_account` DISABLE KEYS */;
INSERT INTO `chart_of_account` VALUES (4,'10000',14,'Petty Cash Fund','Petty Cash Fund',0.00,0,'2024-10-18 07:37:15'),(5,'10100',14,'Cash in Bank','Cash in Bank',0.00,0,'2024-10-18 07:38:03'),(7,'11000',2,'Accounts Receivable - Trade','Accounts Receivable - Trade',0.00,0,'2024-10-18 07:41:16'),(8,'20000',1,'Accounts Payable - Trade','Accounts Payable - Trade',0.00,0,'2024-10-18 07:41:44'),(9,'30000',10,'Shareholders\' Equity','Shareholders\' Equity',0.00,0,'2024-10-18 07:42:11'),(10,'40000',12,'Sales Income','Sales Income',0.00,0,'2024-10-18 07:54:11'),(11,'60000',11,'Salaries and Wages','Salaries and Wages',0.00,0,'2024-10-18 07:54:53'),(13,'13000',3,'Inventory','Inventory',0.00,0,'2024-10-18 08:00:40'),(14,'70100',6,'Bank Interest Income','Bank Interest Income',0.00,0,'2024-10-18 08:02:06'),(15,'50100',9,'Input VAT (COGS)','Input VAT (COGS)',0.00,0,'2024-10-18 08:14:44'),(16,'60100',11,'Input VAT (Expense)','Input VAT (Expense)',0.00,0,'2024-10-18 08:16:37'),(17,'20100',4,'VAT Payable','VAT Payable',0.00,0,'2024-10-18 08:17:51'),(18,'80100',5,'Bank Charge','Bank Charge',0.00,0,'2024-10-18 08:25:08'),(19,'30100',10,'Net Income','Net Income',0.00,0,'2024-10-18 08:33:24'),(20,'30200',10,'Retained Earnings','Retained Earnings',0.00,0,'2024-10-18 08:35:31'),(21,'40100',12,'Output VAT','Output VAT',0.00,0,'2024-10-18 08:44:24'),(22,'40010',12,'Sales Discount','Sales Discount',0.00,0,'2024-10-18 08:50:55'),(23,'50000',9,'Cost of Sales','Cost of Sales',0.00,0,'2024-10-18 08:51:26'),(24,'50010',9,'Purchase Discount','Purchase Discount',0.00,0,'2024-10-18 08:52:08'),(25,'20110',4,'Expanded Withholding Tax Payable','Expanded Withholding Tax Payable',0.00,0,'2024-10-18 09:01:42'),(26,'12000',3,'Undeposited Fund','Undeposited Fund',0.00,0,'2024-10-18 09:03:47'),(27,'20120',4,'Withholding Tax Payable on Compensation','Withholding Tax Payable on Compensation',0.00,0,'2024-10-18 09:05:30'),(28,'10101',14,'BDO','Cash in bank - BDO',0.00,5,'2024-10-18 09:29:18'),(29,'60010',11,'SSS Expense','SSS Expense',0.00,0,'2024-10-21 09:47:59'),(30,'60020',11,'PHIC Expense','PHIC Expense',0.00,0,'2024-10-21 09:48:41'),(31,'60030',11,'Pagibig Expense','Pagibig Expense',0.00,0,'2024-10-21 09:49:14'),(32,'20210',4,'SSS Payable','SSS Payable',0.00,0,'2024-10-21 09:50:34'),(33,'20220',4,'SSS Loans Payable','SSS Loans Payable',0.00,0,'2024-10-21 09:51:05'),(34,'20230',4,'PHIC Payable','PHIC Payable',0.00,0,'2024-10-21 09:51:40'),(35,'20240',4,'Pagibig Payable','Pagibig Payable',0.00,0,'2024-10-21 09:52:13'),(36,'20250',4,'Pagibig Loans Payable','Pagibig Loans Payable',0.00,0,'2024-10-21 09:52:49'),(37,'',14,'MBTC','Cash in Bank -MBTC',0.00,5,'2024-10-21 09:53:31'),(38,'10102',14,'MBTC','Cash in Bank - MBTC',0.00,5,'2024-10-21 09:54:16');
/*!40000 ALTER TABLE `chart_of_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `checks`
--

DROP TABLE IF EXISTS `checks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `checks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cv_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ref_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payee_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payee_id` int NOT NULL,
  `check_date` datetime NOT NULL,
  `account_id` int NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `gross_amount` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `net_amount` decimal(10,2) NOT NULL,
  `taxable_amount` decimal(10,2) NOT NULL,
  `input_vat_amount` decimal(10,2) NOT NULL,
  `tax_withheld_amount` decimal(10,2) NOT NULL,
  `total_amount_due` decimal(10,2) NOT NULL,
  `create_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `checks`
--

LOCK TABLES `checks` WRITE;
/*!40000 ALTER TABLE `checks` DISABLE KEYS */;
/*!40000 ALTER TABLE `checks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_settings`
--

DROP TABLE IF EXISTS `company_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `company_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `zip_code` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `contact` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `tin` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_settings`
--

LOCK TABLES `company_settings` WRITE;
/*!40000 ALTER TABLE `company_settings` DISABLE KEYS */;
INSERT INTO `company_settings` VALUES (1,'Eurospec',NULL,'5125','125','125','125','2024-10-17 15:37:58','2024-10-17 16:29:14');
/*!40000 ALTER TABLE `company_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cost_center`
--

DROP TABLE IF EXISTS `cost_center`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cost_center` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `particular` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cost_center`
--

LOCK TABLES `cost_center` WRITE;
/*!40000 ALTER TABLE `cost_center` DISABLE KEYS */;
INSERT INTO `cost_center` VALUES (1,'10','Admin'),(2,'20','Operations');
/*!40000 ALTER TABLE `cost_center` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `credit_memo`
--

DROP TABLE IF EXISTS `credit_memo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `credit_memo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `credit_no` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `credit_date` date NOT NULL,
  `customer_id` int NOT NULL,
  `credit_account` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `memo` text COLLATE utf8mb4_general_ci,
  `location` int NOT NULL,
  `gross_amount` decimal(10,2) NOT NULL,
  `net_amount_due` decimal(10,2) NOT NULL,
  `vat_percentage_amount` decimal(10,2) DEFAULT NULL,
  `net_of_vat` decimal(10,2) DEFAULT NULL,
  `tax_withheld_amount` decimal(10,2) DEFAULT NULL,
  `tax_withheld_percentage` int DEFAULT NULL,
  `total_amount_due` decimal(10,2) NOT NULL,
  `status` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `print_status` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `credit_memo`
--

LOCK TABLES `credit_memo` WRITE;
/*!40000 ALTER TABLE `credit_memo` DISABLE KEYS */;
/*!40000 ALTER TABLE `credit_memo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `credit_memo_details`
--

DROP TABLE IF EXISTS `credit_memo_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `credit_memo_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `credit_memo_id` int NOT NULL,
  `account_id` int NOT NULL,
  `cost_center_id` int DEFAULT NULL,
  `memo` text COLLATE utf8mb4_general_ci,
  `amount` decimal(10,2) NOT NULL,
  `net_amount` decimal(10,2) DEFAULT NULL,
  `taxable_amount` decimal(10,2) DEFAULT NULL,
  `vat_percentage` varchar(55) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sales_tax` decimal(10,2) DEFAULT NULL,
  `sales_tax_account_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `credit_memo_details`
--

LOCK TABLES `credit_memo_details` WRITE;
/*!40000 ALTER TABLE `credit_memo_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `credit_memo_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `customer_code` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `customer_contact` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `shipping_address` text COLLATE utf8mb4_general_ci,
  `billing_address` text COLLATE utf8mb4_general_ci,
  `business_style` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `customer_terms` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `customer_tin` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `customer_email` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `credit_balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_invoiced` double(15,2) DEFAULT NULL,
  `total_paid` double(15,2) DEFAULT NULL,
  `total_credit_memo` double(15,2) DEFAULT NULL,
  `balance_due` double(15,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (1,'Hideco Sugar Milling Co. Inc','C0001','098765432','Kanaga, Leyte','','Hideco Sugar Milling Co. Inc','','1234567','',1079276.79,NULL,NULL,NULL,NULL),(2,'242','','','','','','','','',0.00,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `discount`
--

DROP TABLE IF EXISTS `discount`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `discount` (
  `id` int NOT NULL AUTO_INCREMENT,
  `discount_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_rate` float NOT NULL,
  `discount_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_account_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discount`
--

LOCK TABLES `discount` WRITE;
/*!40000 ALTER TABLE `discount` DISABLE KEYS */;
INSERT INTO `discount` VALUES (1,'S1',1,'1% sales discount',22,'2024-10-21 10:22:17'),(2,'S2',2,'2% sales discount',22,'2024-10-21 10:23:03'),(3,'P1',1,'1% purchase discount',24,'2024-10-21 10:23:54'),(4,'P2',2,'2% purchase discount',24,'2024-10-21 10:24:40');
/*!40000 ALTER TABLE `discount` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee`
--

DROP TABLE IF EXISTS `employee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee` (
  `id` int NOT NULL AUTO_INCREMENT,
  `employee_code` int NOT NULL,
  `employment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ext_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `co_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tin` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `terms` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `house_lot_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `street` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barangay` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `town` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zip` int NOT NULL,
  `sss` int NOT NULL,
  `philhealth` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pagibig` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee`
--

LOCK TABLES `employee` WRITE;
/*!40000 ALTER TABLE `employee` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fs_classification`
--

DROP TABLE IF EXISTS `fs_classification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fs_classification` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fs_classification`
--

LOCK TABLES `fs_classification` WRITE;
/*!40000 ALTER TABLE `fs_classification` DISABLE KEYS */;
/*!40000 ALTER TABLE `fs_classification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fs_notes_classification`
--

DROP TABLE IF EXISTS `fs_notes_classification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fs_notes_classification` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fs_notes_classification`
--

LOCK TABLES `fs_notes_classification` WRITE;
/*!40000 ALTER TABLE `fs_notes_classification` DISABLE KEYS */;
/*!40000 ALTER TABLE `fs_notes_classification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `general_journal`
--

DROP TABLE IF EXISTS `general_journal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `general_journal` (
  `id` int NOT NULL AUTO_INCREMENT,
  `entry_no` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `journal_date` date NOT NULL,
  `total_debit` decimal(15,2) NOT NULL,
  `total_credit` decimal(15,2) NOT NULL,
  `memo` text COLLATE utf8mb4_general_ci,
  `location` int NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `print_status` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `general_journal`
--

LOCK TABLES `general_journal` WRITE;
/*!40000 ALTER TABLE `general_journal` DISABLE KEYS */;
/*!40000 ALTER TABLE `general_journal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `general_journal_details`
--

DROP TABLE IF EXISTS `general_journal_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `general_journal_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `general_journal_id` int NOT NULL,
  `cost_center_id` int DEFAULT NULL,
  `account_id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `debit` decimal(15,2) DEFAULT '0.00',
  `credit` decimal(15,2) DEFAULT '0.00',
  `memo` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `general_journal_details`
--

LOCK TABLES `general_journal_details` WRITE;
/*!40000 ALTER TABLE `general_journal_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `general_journal_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `input_vat`
--

DROP TABLE IF EXISTS `input_vat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `input_vat` (
  `id` int NOT NULL AUTO_INCREMENT,
  `input_vat_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `input_vat_rate` int NOT NULL,
  `input_vat_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `input_vat_account_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `input_vat`
--

LOCK TABLES `input_vat` WRITE;
/*!40000 ALTER TABLE `input_vat` DISABLE KEYS */;
INSERT INTO `input_vat` VALUES (1,'12% (COGS)',12,'12% Input VAT',15,'2024-10-21 03:15:41'),(2,'12% (Expense)',12,'12% Input VAT',16,'2024-10-21 03:16:11'),(3,'NV (COGS)',0,'Non-VAT Purchases',15,'2024-10-21 03:17:11'),(4,'NV (Expense)',0,'Non-VAT Purchases',16,'2024-10-21 03:17:45'),(5,'E (COGS)',0,'VAT-Exempt',15,'2024-10-21 03:29:39'),(6,'E (Expense)',0,'VAT-Exempt',16,'2024-10-21 03:30:35'),(7,'Z (COGS)',0,'Zero-rated VAT',15,'2024-10-21 03:40:58'),(8,'Z (Expense)',0,'Zero-rated VAT',16,'2024-10-21 03:41:27');
/*!40000 ALTER TABLE `input_vat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `transaction_id` int NOT NULL,
  `ref_no` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `date` date NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `item_id` int NOT NULL,
  `qty_purchased` int DEFAULT '0',
  `qty_sold` int DEFAULT '0',
  `qty_on_hand` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory`
--

LOCK TABLES `inventory` WRITE;
/*!40000 ALTER TABLE `inventory` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_valuation`
--

DROP TABLE IF EXISTS `inventory_valuation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_valuation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `transaction_id` int DEFAULT NULL,
  `ref_no` varchar(50) COLLATE utf8mb4_general_ci DEFAULT '',
  `date` date DEFAULT NULL,
  `name` int DEFAULT NULL,
  `item_id` int DEFAULT NULL,
  `qty_purchased` decimal(10,2) DEFAULT '0.00',
  `qty_sold` decimal(10,2) DEFAULT '0.22',
  `qty_on_hand` decimal(10,2) DEFAULT '0.00',
  `cost` decimal(15,2) DEFAULT '0.00',
  `total_cost` decimal(15,2) DEFAULT '0.00',
  `purchase_discount_rate` decimal(15,2) DEFAULT '0.00',
  `purchase_discount_per_item` decimal(15,2) DEFAULT '0.00',
  `purchase_discount_amount` decimal(15,2) DEFAULT '0.00',
  `net_amount` decimal(15,2) DEFAULT '0.00',
  `input_vat_rate` decimal(15,2) DEFAULT '0.00',
  `input_vat` decimal(15,2) DEFAULT '0.00',
  `taxable_purchased_amount` decimal(15,2) DEFAULT '0.00',
  `cost_per_unit` decimal(15,2) DEFAULT '0.00',
  `selling_price` decimal(15,2) DEFAULT '0.00',
  `gross_sales` decimal(15,2) DEFAULT '0.00',
  `sales_discount_rate` decimal(15,2) DEFAULT '0.00',
  `sales_discount_amount` decimal(15,2) DEFAULT '0.00',
  `net_sales` decimal(15,2) DEFAULT '0.00',
  `sales_tax` decimal(15,2) DEFAULT '0.00',
  `output_vat` decimal(15,2) DEFAULT '0.00',
  `taxable_sales_amount` decimal(15,2) DEFAULT '0.00',
  `selling_price_per_unit` decimal(15,2) DEFAULT '0.00',
  `weighted_average_cost` decimal(15,2) DEFAULT '0.00',
  `asset_value_wa` decimal(15,2) DEFAULT '0.00',
  `fifo_cost` decimal(15,2) DEFAULT '0.00',
  `cost_of_goods_sold` decimal(15,2) DEFAULT '0.00',
  `asset_value_fifo` decimal(15,2) DEFAULT '0.00',
  `gross_margin` decimal(15,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_valuation`
--

LOCK TABLES `inventory_valuation` WRITE;
/*!40000 ALTER TABLE `inventory_valuation` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_valuation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `item_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `item_code` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `item_type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `item_vendor_id` int DEFAULT '0',
  `item_uom_id` int DEFAULT '0',
  `item_reorder_point` double(10,2) DEFAULT '0.00',
  `item_category_id` int DEFAULT '0',
  `item_quantity` int DEFAULT '0',
  `item_sales_description` text COLLATE utf8mb4_general_ci,
  `item_purchase_description` text COLLATE utf8mb4_general_ci,
  `item_selling_price` decimal(10,2) DEFAULT '0.00',
  `item_cost_price` decimal(10,2) DEFAULT '0.00',
  `item_cogs_account_id` int DEFAULT NULL,
  `item_income_account_id` int DEFAULT NULL,
  `item_asset_account_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` VALUES (1,'Raw Sugar','RW001','Inventory',NULL,2,0.00,1,0,'Raw Sugar','Raw Sugar',0.00,0.00,23,10,13,'2024-10-21 10:37:06'),(2,'Refined Sugar','RF001','Inventory',NULL,2,0.00,1,0,'Refined Sugar','Refined Sugar',0.00,0.00,23,10,13,'2024-10-21 10:38:28'),(3,'Molasses','ML001','Inventory',NULL,2,0.00,1,0,'Molasses','Molasses',0.00,0.00,23,10,13,'2024-10-21 10:40:09');
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS `location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `location` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `location`
--

LOCK TABLES `location` WRITE;
/*!40000 ALTER TABLE `location` DISABLE KEYS */;
INSERT INTO `location` VALUES (1,'Warehouse');
/*!40000 ALTER TABLE `location` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `material_issuance`
--

DROP TABLE IF EXISTS `material_issuance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `material_issuance` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mis_no` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `location` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `purpose` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `print_status` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `material_issuance`
--

LOCK TABLES `material_issuance` WRITE;
/*!40000 ALTER TABLE `material_issuance` DISABLE KEYS */;
/*!40000 ALTER TABLE `material_issuance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `material_issuance_details`
--

DROP TABLE IF EXISTS `material_issuance_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `material_issuance_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mis_id` int DEFAULT '0',
  `item_id` int DEFAULT '0',
  `quantity` int DEFAULT '0',
  `cost` double(10,2) DEFAULT '0.00',
  `amount` double(10,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `material_issuance_details`
--

LOCK TABLES `material_issuance_details` WRITE;
/*!40000 ALTER TABLE `material_issuance_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `material_issuance_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `or_payment_details`
--

DROP TABLE IF EXISTS `or_payment_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `or_payment_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `or_id` int NOT NULL,
  `item_id` int NOT NULL,
  `quantity` int NOT NULL,
  `cost` decimal(15,2) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `discount_percentage` decimal(15,2) DEFAULT NULL,
  `discount_amount` decimal(15,2) NOT NULL,
  `net_amount_before_sales_tax` decimal(15,2) NOT NULL,
  `net_amount` decimal(15,2) NOT NULL,
  `sales_tax_percentage` decimal(15,2) NOT NULL,
  `sales_tax_amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `output_vat_id` int DEFAULT NULL,
  `discount_account_id` int DEFAULT NULL,
  `cogs_account_id` int DEFAULT NULL,
  `income_account_id` int DEFAULT NULL,
  `asset_account_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `or_payment_details`
--

LOCK TABLES `or_payment_details` WRITE;
/*!40000 ALTER TABLE `or_payment_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `or_payment_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `or_payments`
--

DROP TABLE IF EXISTS `or_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `or_payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `or_number` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ci_no` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `or_date` date NOT NULL,
  `or_account_id` int NOT NULL,
  `customer_po` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `so_no` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rep` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `check_no` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `customer_id` int NOT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `location` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `memo` text COLLATE utf8mb4_general_ci NOT NULL,
  `gross_amount` decimal(15,2) NOT NULL,
  `discount_amount` decimal(15,2) NOT NULL,
  `net_amount_due` decimal(15,2) NOT NULL,
  `vat_amount` decimal(15,2) NOT NULL,
  `vatable_amount` decimal(15,2) NOT NULL,
  `zero_rated_amount` decimal(15,2) NOT NULL,
  `vat_exempt_amount` decimal(15,2) NOT NULL,
  `tax_withheld_percentage` int NOT NULL,
  `tax_withheld_amount` decimal(15,2) NOT NULL,
  `total_amount_due` decimal(15,2) NOT NULL,
  `or_status` int NOT NULL DEFAULT '1',
  `status` int NOT NULL DEFAULT '0',
  `print_status` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `or_payments`
--

LOCK TABLES `or_payments` WRITE;
/*!40000 ALTER TABLE `or_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `or_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `other_name`
--

DROP TABLE IF EXISTS `other_name`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `other_name` (
  `id` int NOT NULL AUTO_INCREMENT,
  `other_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `other_name_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `other_name_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `terms` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `other_name`
--

LOCK TABLES `other_name` WRITE;
/*!40000 ALTER TABLE `other_name` DISABLE KEYS */;
/*!40000 ALTER TABLE `other_name` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_credit_details`
--

DROP TABLE IF EXISTS `payment_credit_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_credit_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `payment_detail_id` int NOT NULL,
  `credit_amount` decimal(15,2) DEFAULT NULL,
  `credit_no` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_credit_details`
--

LOCK TABLES `payment_credit_details` WRITE;
/*!40000 ALTER TABLE `payment_credit_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_credit_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_details`
--

DROP TABLE IF EXISTS `payment_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `payment_id` int NOT NULL,
  `invoice_id` int NOT NULL,
  `amount_applied` decimal(15,2) NOT NULL,
  `discount_amount` decimal(15,2) DEFAULT '0.00',
  `credit_amount` decimal(15,2) DEFAULT '0.00',
  `credit_no` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `discount_account_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_details`
--

LOCK TABLES `payment_details` WRITE;
/*!40000 ALTER TABLE `payment_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_method`
--

DROP TABLE IF EXISTS `payment_method`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_method` (
  `id` int NOT NULL AUTO_INCREMENT,
  `payment_method_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_method`
--

LOCK TABLES `payment_method` WRITE;
/*!40000 ALTER TABLE `payment_method` DISABLE KEYS */;
INSERT INTO `payment_method` VALUES (1,'CASH','CASH'),(2,'CHECK','CHECK'),(3,'BANK TRANSFER','BANK TRANSFER');
/*!40000 ALTER TABLE `payment_method` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method_id` int NOT NULL,
  `account_id` int NOT NULL,
  `ref_no` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cr_no` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `memo` text COLLATE utf8mb4_general_ci,
  `summary_amount_due` decimal(15,2) DEFAULT '0.00',
  `summary_applied_amount` decimal(15,2) DEFAULT '0.00',
  `applied_credits_discount` decimal(15,2) DEFAULT '0.00',
  `status` int NOT NULL DEFAULT '0',
  `print_status` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_order`
--

DROP TABLE IF EXISTS `purchase_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_order` (
  `id` int NOT NULL AUTO_INCREMENT,
  `po_no` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `po_account_id` int DEFAULT NULL,
  `date` date DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `vendor_id` int DEFAULT NULL,
  `terms` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gross_amount` decimal(15,2) DEFAULT '0.00',
  `discount_amount` decimal(15,2) DEFAULT '0.00',
  `net_amount` decimal(15,2) DEFAULT '0.00',
  `input_vat` decimal(15,2) DEFAULT '0.00',
  `vatable` decimal(15,2) DEFAULT '0.00',
  `zero_rated` decimal(15,2) DEFAULT '0.00',
  `vat_exempt` decimal(15,2) DEFAULT '0.00',
  `total_amount` decimal(15,2) DEFAULT '0.00',
  `memo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` int NOT NULL,
  `po_status` tinyint(1) DEFAULT '0' COMMENT '0 - waiting for delivery\r\n1 = received',
  `status` tinyint DEFAULT '1' COMMENT '0 = draft \r\n1 = posted',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `print_status` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_order`
--

LOCK TABLES `purchase_order` WRITE;
/*!40000 ALTER TABLE `purchase_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_order_details`
--

DROP TABLE IF EXISTS `purchase_order_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_order_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `po_id` int DEFAULT NULL,
  `pr_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_id` int DEFAULT NULL,
  `cost_center_id` int DEFAULT NULL,
  `qty` int DEFAULT NULL,
  `cost` decimal(13,4) DEFAULT NULL,
  `amount` decimal(13,4) DEFAULT NULL,
  `discount_percentage` int DEFAULT NULL,
  `discount_type_id` int DEFAULT NULL,
  `discount` decimal(13,4) DEFAULT NULL,
  `net_amount` decimal(13,4) DEFAULT NULL,
  `input_vat_percentage` int DEFAULT NULL,
  `taxable_amount` decimal(13,4) DEFAULT NULL,
  `tax_type_id` int DEFAULT NULL,
  `input_vat` decimal(13,4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `received_qty` int DEFAULT '0',
  `balance_qty` int DEFAULT '0',
  `last_ordered_qty` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `po_id` (`po_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_order_details`
--

LOCK TABLES `purchase_order_details` WRITE;
/*!40000 ALTER TABLE `purchase_order_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_order_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_request`
--

DROP TABLE IF EXISTS `purchase_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_request` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pr_no` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `location` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `required_date` date DEFAULT NULL,
  `memo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `print_status` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_request`
--

LOCK TABLES `purchase_request` WRITE;
/*!40000 ALTER TABLE `purchase_request` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_request_details`
--

DROP TABLE IF EXISTS `purchase_request_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_request_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pr_id` int DEFAULT '0',
  `item_id` int DEFAULT '0',
  `cost_center_id` int DEFAULT '0',
  `quantity` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ordered_quantity` int DEFAULT '0',
  `balance_quantity` int DEFAULT '0',
  `status` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_request_details`
--

LOCK TABLES `purchase_request_details` WRITE;
/*!40000 ALTER TABLE `purchase_request_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_request_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchases`
--

DROP TABLE IF EXISTS `purchases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchases` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `transaction_id` int NOT NULL,
  `ref_no` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `item_id` int NOT NULL,
  `cost` decimal(15,2) NOT NULL,
  `total_cost` decimal(15,2) NOT NULL,
  `discount_rate` decimal(5,2) DEFAULT '0.00',
  `purchase_discount_per_item` decimal(10,2) DEFAULT '0.00',
  `purchase_discount_amount` decimal(10,2) DEFAULT '0.00',
  `net_amount` decimal(15,2) NOT NULL,
  `tax_type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `input_vat` decimal(10,2) DEFAULT '0.00',
  `taxable_purchased_amount` decimal(15,2) DEFAULT '0.00',
  `cost_per_unit` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchases`
--

LOCK TABLES `purchases` WRITE;
/*!40000 ALTER TABLE `purchases` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `receive_item_details`
--

DROP TABLE IF EXISTS `receive_item_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `receive_item_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `receive_id` int DEFAULT NULL,
  `po_id` int DEFAULT NULL,
  `item_id` int DEFAULT NULL,
  `cost_center_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `cost` double(15,2) NOT NULL DEFAULT '0.00',
  `amount` double(15,2) NOT NULL DEFAULT '0.00',
  `discount_percentage` double(5,2) NOT NULL DEFAULT '0.00',
  `discount` double(15,2) NOT NULL DEFAULT '0.00',
  `net_amount_before_input_vat` double(15,2) NOT NULL DEFAULT '0.00',
  `net_amount` double(15,2) NOT NULL DEFAULT '0.00',
  `input_vat_percentage` double(5,2) NOT NULL DEFAULT '0.00',
  `input_vat_amount` double(15,2) NOT NULL DEFAULT '0.00',
  `cost_per_unit` double(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_received_qty` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `receive_item_details`
--

LOCK TABLES `receive_item_details` WRITE;
/*!40000 ALTER TABLE `receive_item_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `receive_item_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `receive_items`
--

DROP TABLE IF EXISTS `receive_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `receive_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `receive_account_id` int DEFAULT NULL,
  `receive_no` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `vendor_id` int DEFAULT NULL,
  `location` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `terms` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `receive_date` date DEFAULT NULL,
  `receive_due_date` date DEFAULT NULL,
  `memo` text COLLATE utf8mb4_general_ci,
  `gross_amount` double(15,2) DEFAULT '0.00',
  `discount_amount` double(15,2) DEFAULT '0.00',
  `net_amount` double(15,2) DEFAULT '0.00',
  `input_vat` double(15,2) DEFAULT '0.00',
  `vatable` double(15,2) DEFAULT '0.00',
  `zero_rated` double(15,2) DEFAULT '0.00',
  `vat_exempt` double(15,2) DEFAULT '0.00',
  `total_amount` double(15,2) DEFAULT '0.00',
  `receive_status` tinyint(1) DEFAULT '0',
  `print_status` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `receive_items`
--

LOCK TABLES `receive_items` WRITE;
/*!40000 ALTER TABLE `receive_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `receive_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_invoice`
--

DROP TABLE IF EXISTS `sales_invoice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_invoice` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `invoice_account_id` int DEFAULT NULL,
  `invoice_due_date` date DEFAULT NULL,
  `customer_po` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `so_no` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rep` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `customer_id` int NOT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `location` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `terms` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `memo` text COLLATE utf8mb4_general_ci,
  `gross_amount` decimal(10,2) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `net_amount_due` decimal(10,2) DEFAULT NULL,
  `vat_amount` decimal(10,2) DEFAULT NULL,
  `vatable_amount` decimal(10,2) DEFAULT NULL,
  `zero_rated_amount` decimal(10,2) DEFAULT NULL,
  `vat_exempt_amount` decimal(10,2) DEFAULT NULL,
  `tax_withheld_percentage` int DEFAULT NULL,
  `tax_withheld_amount` decimal(10,2) DEFAULT NULL,
  `total_amount_due` decimal(10,2) DEFAULT NULL,
  `invoice_status` int NOT NULL DEFAULT '0' COMMENT '0 = unpaid\r\n1 = paid',
  `status` int NOT NULL DEFAULT '1' COMMENT '0 = draft\r\n1 = posted\r\n2 = modified\r\n3 = deleted',
  `balance_due` double(15,2) NOT NULL DEFAULT '0.00',
  `total_paid` double(15,2) NOT NULL DEFAULT '0.00',
  `print_status` int NOT NULL DEFAULT '0' COMMENT '0 = original copy\r\n1 = reprinted',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_invoice`
--

LOCK TABLES `sales_invoice` WRITE;
/*!40000 ALTER TABLE `sales_invoice` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_invoice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_invoice_details`
--

DROP TABLE IF EXISTS `sales_invoice_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_invoice_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_id` int NOT NULL,
  `item_id` int NOT NULL,
  `quantity` int DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `discount_percentage` decimal(5,2) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `net_amount_before_sales_tax` decimal(10,2) DEFAULT NULL,
  `net_amount` decimal(10,2) DEFAULT NULL,
  `sales_tax_percentage` decimal(5,2) DEFAULT NULL,
  `sales_tax_amount` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `output_vat_id` int DEFAULT NULL,
  `discount_account_id` int DEFAULT NULL,
  `cogs_account_id` int DEFAULT NULL,
  `income_account_id` int DEFAULT NULL,
  `asset_account_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_invoice_details`
--

LOCK TABLES `sales_invoice_details` WRITE;
/*!40000 ALTER TABLE `sales_invoice_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_invoice_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_return`
--

DROP TABLE IF EXISTS `sales_return`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_return` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sales_return_number` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sales_return_date` date DEFAULT NULL,
  `sales_return_account_id` int DEFAULT NULL,
  `sales_return_due_date` date DEFAULT NULL,
  `customer_po` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `so_no` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rep` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `location` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `terms` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `memo` text COLLATE utf8mb4_general_ci,
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
  `sales_return_status` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `print_status` tinyint(1) NOT NULL DEFAULT '0',
  `balance_due` double(10,2) DEFAULT NULL,
  `total_paid` double(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_return`
--

LOCK TABLES `sales_return` WRITE;
/*!40000 ALTER TABLE `sales_return` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_return` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_return_details`
--

DROP TABLE IF EXISTS `sales_return_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_return_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sales_return_id` int DEFAULT NULL,
  `item_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `cost` double(10,2) DEFAULT NULL,
  `amount` double(10,2) DEFAULT NULL,
  `discount_percentage` double(5,2) DEFAULT NULL,
  `discount_amount` double(10,2) DEFAULT NULL,
  `net_amount_before_sales_tax` double(10,2) DEFAULT NULL,
  `net_amount` double(10,2) DEFAULT NULL,
  `sales_tax_percentage` double(5,2) DEFAULT NULL,
  `sales_tax_amount` double(10,2) DEFAULT NULL,
  `discount_account_id` int DEFAULT NULL,
  `output_vat_id` int DEFAULT NULL,
  `cogs_account_id` int DEFAULT NULL,
  `income_account_id` int DEFAULT NULL,
  `asset_account_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_return_details`
--

LOCK TABLES `sales_return_details` WRITE;
/*!40000 ALTER TABLE `sales_return_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_return_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_tax`
--

DROP TABLE IF EXISTS `sales_tax`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_tax` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sales_tax_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sales_tax_rate` float NOT NULL,
  `sales_tax_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `sales_tax_account_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_tax`
--

LOCK TABLES `sales_tax` WRITE;
/*!40000 ALTER TABLE `sales_tax` DISABLE KEYS */;
INSERT INTO `sales_tax` VALUES (1,'12%',12,'12% VAT',21,'2024-10-21 03:13:32'),(2,'E',0,'VAT Exempt',21,'2024-10-21 03:14:00'),(3,'NV',0,'Non-VAT',21,'2024-10-21 03:14:30'),(4,'Z',0,'Zero-rated Tax',21,'2024-10-21 03:14:53');
/*!40000 ALTER TABLE `sales_tax` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `terms`
--

DROP TABLE IF EXISTS `terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `terms` (
  `id` int NOT NULL AUTO_INCREMENT,
  `term_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `term_days_due` int NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `terms`
--

LOCK TABLES `terms` WRITE;
/*!40000 ALTER TABLE `terms` DISABLE KEYS */;
INSERT INTO `terms` VALUES (1,'COD',0,'Cash on Delivery'),(2,'7 days',7,'7 days'),(3,'15 days',15,'15 days');
/*!40000 ALTER TABLE `terms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaction_entries`
--

DROP TABLE IF EXISTS `transaction_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transaction_entries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `transaction_id` int DEFAULT NULL,
  `transaction_type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `transaction_date` date DEFAULT NULL,
  `ref_no` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `location` int DEFAULT '0',
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `item` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `qty_sold` int DEFAULT NULL,
  `account_id` int NOT NULL,
  `debit` decimal(15,2) DEFAULT '0.00',
  `credit` decimal(15,2) DEFAULT '0.00',
  `balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaction_entries`
--

LOCK TABLES `transaction_entries` WRITE;
/*!40000 ALTER TABLE `transaction_entries` DISABLE KEYS */;
/*!40000 ALTER TABLE `transaction_entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `uom`
--

DROP TABLE IF EXISTS `uom`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `uom` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `uom`
--

LOCK TABLES `uom` WRITE;
/*!40000 ALTER TABLE `uom` DISABLE KEYS */;
INSERT INTO `uom` VALUES (1,'kg/s'),(2,'mt/s');
/*!40000 ALTER TABLE `uom` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_module_access`
--

DROP TABLE IF EXISTS `user_module_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_module_access` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `module` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=137 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_module_access`
--

LOCK TABLES `user_module_access` WRITE;
/*!40000 ALTER TABLE `user_module_access` DISABLE KEYS */;
INSERT INTO `user_module_access` VALUES (71,31,'dashboard'),(72,31,'chart_of_accounts_list'),(73,31,'general_journal'),(74,31,'transaction_entries'),(75,31,'trial_balance'),(76,31,'audit_trail'),(77,32,'dashboard'),(78,32,'invoice'),(79,32,'reports'),(80,33,'dashboard'),(81,33,'chart_of_accounts_list'),(82,33,'general_journal'),(83,33,'transaction_entries'),(84,33,'trial_balance'),(85,33,'audit_trail'),(86,34,'dashboard'),(87,34,'purchase_order'),(88,34,'bank_transfer'),(89,34,'vendor_list'),(90,34,'category'),(91,34,'payment_method'),(92,35,'dashboard'),(93,35,'purchasing_purchase_request'),(94,35,'purchasing_purchase_order'),(95,36,'dashboard'),(96,36,'reports'),(97,36,'invoice'),(98,36,'receive_payment'),(99,36,'credit_memo'),(100,36,'purchase_request'),(101,36,'purchase_order'),(102,36,'receive_items'),(103,36,'accounts_payable_voucher'),(104,36,'purchase_return'),(105,36,'pay_bills'),(106,36,'write_check'),(107,36,'make_deposit'),(108,36,'bank_transfer'),(109,36,'chart_of_accounts_list'),(110,36,'general_journal'),(111,36,'transaction_entries'),(112,36,'trial_balance'),(113,36,'audit_trail'),(114,36,'chart_of_accounts'),(115,36,'item_list'),(116,36,'customer'),(117,36,'vendor_list'),(118,36,'employee_list'),(119,36,'other_name'),(120,36,'location'),(121,36,'uom'),(122,36,'cost_center'),(123,36,'category'),(124,36,'terms'),(125,36,'payment_method'),(126,36,'discount'),(127,36,'input_vat'),(128,36,'sales_tax'),(129,36,'wtax'),(130,36,'purchasing_purchase_request'),(131,36,'purchasing_purchase_order'),(132,36,'warehouse_receive_items'),(133,36,'warehouse_purchase_request'),(134,36,'material_issuance'),(135,37,'dashboard'),(136,37,'accounts_payable_voucher');
/*!40000 ALTER TABLE `user_module_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_role_module_access`
--

DROP TABLE IF EXISTS `user_role_module_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_role_module_access` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role_id` int NOT NULL,
  `module` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=243 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_role_module_access`
--

LOCK TABLES `user_role_module_access` WRITE;
/*!40000 ALTER TABLE `user_role_module_access` DISABLE KEYS */;
INSERT INTO `user_role_module_access` VALUES (21,17,'dashboard'),(22,17,'reports'),(23,17,'invoice'),(24,17,'receive_payment'),(25,17,'credit_memo'),(26,17,'purchase_request'),(27,17,'purchase_order'),(28,17,'receive_items'),(29,17,'accounts_payable_voucher'),(30,17,'purchase_return'),(31,17,'pay_bills'),(32,17,'write_check'),(33,17,'make_deposit'),(34,17,'bank_transfer'),(35,17,'chart_of_accounts_list'),(36,17,'general_journal'),(37,17,'transaction_entries'),(38,17,'trial_balance'),(39,17,'audit_trail'),(40,17,'chart_of_accounts'),(41,17,'item_list'),(42,17,'customer'),(43,17,'vendor_list'),(44,17,'employee_list'),(45,17,'other_name'),(46,17,'location'),(47,17,'uom'),(48,17,'cost_center'),(49,17,'category'),(50,17,'terms'),(51,17,'payment_method'),(52,17,'discount'),(53,17,'input_vat'),(54,17,'sales_tax'),(55,17,'wtax'),(56,17,'purchasing_purchase_request'),(57,17,'purchasing_purchase_order'),(58,17,'warehouse_receive_items'),(59,17,'warehouse_purchase_request'),(60,17,'material_issuance'),(61,18,'dashboard'),(62,18,'invoice'),(63,18,'receive_payment'),(64,18,'credit_memo'),(65,18,'chart_of_accounts'),(66,18,'item_list'),(67,18,'customer'),(68,18,'employee_list'),(69,18,'other_name'),(70,18,'location'),(71,18,'uom'),(72,18,'cost_center'),(73,18,'category'),(74,18,'terms'),(75,18,'payment_method'),(76,18,'discount'),(77,18,'input_vat'),(78,18,'sales_tax'),(79,18,'wtax'),(102,20,'dashboard'),(103,20,'purchase_request'),(104,20,'purchase_order'),(105,20,'receive_items'),(106,20,'accounts_payable_voucher'),(107,20,'purchase_return'),(108,20,'pay_bills'),(109,20,'chart_of_accounts_list'),(110,20,'general_journal'),(111,20,'transaction_entries'),(112,20,'trial_balance'),(113,20,'audit_trail'),(114,20,'chart_of_accounts'),(115,20,'item_list'),(116,20,'customer'),(117,20,'vendor_list'),(118,20,'employee_list'),(119,20,'other_name'),(120,20,'fs_classification'),(121,20,'fs_notes_classification'),(122,20,'location'),(123,20,'uom'),(124,20,'cost_center'),(125,20,'category'),(126,20,'terms'),(127,20,'payment_method'),(128,20,'discount'),(129,20,'input_vat'),(130,20,'sales_tax'),(131,20,'wtax'),(132,20,'inventory_list'),(133,20,'warehouse_receive_items'),(134,20,'warehouse_purchase_request'),(135,20,'material_issuance'),(136,20,'warehouse_receive_items'),(137,20,'inventory_valuation_detail'),(138,20,'material_issuance'),(139,20,'reports'),(140,21,'dashboard'),(141,21,'purchase_request'),(142,21,'purchase_order'),(143,21,'receive_items'),(144,21,'accounts_payable_voucher'),(145,21,'purchase_return'),(146,21,'pay_bills'),(147,21,'write_check'),(148,21,'make_deposit'),(149,21,'bank_transfer'),(150,21,'chart_of_accounts_list'),(151,21,'general_journal'),(152,21,'transaction_entries'),(153,21,'trial_balance'),(154,21,'audit_trail'),(155,21,'chart_of_accounts'),(156,21,'item_list'),(157,21,'customer'),(158,21,'vendor_list'),(159,21,'employee_list'),(160,21,'other_name'),(161,21,'fs_classification'),(162,21,'fs_notes_classification'),(163,21,'location'),(164,21,'uom'),(165,21,'cost_center'),(166,21,'category'),(167,21,'terms'),(168,21,'payment_method'),(169,21,'discount'),(170,21,'input_vat'),(171,21,'sales_tax'),(172,21,'wtax'),(173,21,'reports'),(174,22,'dashboard'),(175,22,'invoice'),(176,22,'receive_payment'),(177,22,'credit_memo'),(178,22,'sales_return'),(191,24,'dashboard'),(192,24,'employee_list'),(193,24,'department'),(194,24,'position'),(195,24,'shift_schedule'),(196,24,'deduction'),(197,24,'attendace_list'),(198,24,'daily_time_record'),(199,24,'leave_list'),(200,24,'overtime_list'),(201,24,'loan_list'),(202,24,'payroll'),(203,24,'generate_payroll'),(209,26,'dashboard'),(210,26,'invoice'),(211,26,'receive_payment'),(212,26,'credit_memo'),(213,26,'sales_return'),(214,26,'purchase_request'),(215,26,'purchase_order'),(216,26,'receive_items'),(217,26,'accounts_payable_voucher'),(218,26,'purchase_return'),(219,26,'write_check'),(220,26,'chart_of_accounts_list'),(221,26,'general_journal'),(222,26,'transaction_entries'),(223,26,'trial_balance'),(224,26,'audit_trail'),(225,26,'chart_of_accounts'),(226,26,'item_list'),(227,26,'customer'),(228,26,'vendor_list'),(229,26,'employee_list'),(230,26,'other_name'),(231,26,'location'),(232,26,'uom'),(233,26,'cost_center'),(234,26,'category'),(235,26,'terms'),(236,26,'payment_method'),(237,26,'discount'),(238,26,'input_vat'),(239,26,'sales_tax'),(240,26,'wtax'),(241,26,'inventory_valuation_detail'),(242,26,'reports');
/*!40000 ALTER TABLE `user_role_module_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_roles`
--

LOCK TABLES `user_roles` WRITE;
/*!40000 ALTER TABLE `user_roles` DISABLE KEYS */;
INSERT INTO `user_roles` VALUES (1,'SUPERADMIN'),(17,'ADMIN'),(18,'RECEIVABLES'),(20,'PURCHASING MANAGER'),(21,'PAYABLES'),(22,'SAMPLE'),(24,'PAYROLL MASTER'),(26,'LAPERLA ADMIN');
/*!40000 ALTER TABLE `user_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  `role_id` int NOT NULL,
  `password` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Digimax','superadmin',1,'IloveDigimax3407'),(36,'Admin','admin',0,'D!gimax321@'),(41,'Kimberly Arellano','Kim',1,'IloveDigimax3407'),(42,'Theresa Cabigting','Theresa',1,'3407'),(43,'Janine Rallos','Janine',1,'3407'),(46,'Rafael Villanueva','Raffy',20,'123'),(53,'Payroll','test_payroll1',24,'test_payroll1'),(55,'Mayla Caro','Mayla',26,'LaPerla321@'),(56,'Theresa Admin','Theresa Admin',26,'3407');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vendors`
--

DROP TABLE IF EXISTS `vendors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vendors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vendor_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `vendor_code` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `account_number` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `vendor_address` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `contact_number` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `terms` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tin` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tax_type` varchar(11) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tel_no` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fax_no` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `item_type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vendors`
--

LOCK TABLES `vendors` WRITE;
/*!40000 ALTER TABLE `vendors` DISABLE KEYS */;
INSERT INTO `vendors` VALUES (2,'Hideco Sugar Milling Co. Inc.','HS001','','Kananga, Leyte','','','','','12% (COGS)','','','','');
/*!40000 ALTER TABLE `vendors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wchecks`
--

DROP TABLE IF EXISTS `wchecks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wchecks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cv_no` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `check_no` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ref_no` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `check_date` date NOT NULL,
  `account_id` int NOT NULL,
  `payee_type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `payee_id` int NOT NULL,
  `memo` text COLLATE utf8mb4_general_ci NOT NULL,
  `location` int NOT NULL,
  `gross_amount` double(10,2) NOT NULL,
  `discount_amount` double(10,2) NOT NULL,
  `net_amount_due` double(10,2) NOT NULL,
  `vat_percentage_amount` double(10,2) NOT NULL,
  `net_of_vat` double(10,2) NOT NULL,
  `tax_withheld_amount` double(10,2) DEFAULT NULL,
  `tax_withheld_percentage` int NOT NULL,
  `total_amount_due` double(10,2) NOT NULL,
  `discount_account_id` int DEFAULT NULL,
  `input_vat_account_id` int DEFAULT NULL,
  `tax_withheld_account_id` int DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `print_status` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wchecks`
--

LOCK TABLES `wchecks` WRITE;
/*!40000 ALTER TABLE `wchecks` DISABLE KEYS */;
/*!40000 ALTER TABLE `wchecks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wchecks_details`
--

DROP TABLE IF EXISTS `wchecks_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wchecks_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `wcheck_id` int NOT NULL,
  `account_id` int NOT NULL,
  `cost_center_id` int DEFAULT NULL,
  `memo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` double(10,2) NOT NULL,
  `discount_percentage` double(10,2) DEFAULT '0.00',
  `discount_amount` double(10,2) DEFAULT NULL,
  `net_amount_before_vat` double(10,2) NOT NULL,
  `net_amount` double(10,2) DEFAULT NULL,
  `vat_percentage` double(10,0) DEFAULT '0',
  `input_vat` double(10,2) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wchecks_details`
--

LOCK TABLES `wchecks_details` WRITE;
/*!40000 ALTER TABLE `wchecks_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `wchecks_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wtax`
--

DROP TABLE IF EXISTS `wtax`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wtax` (
  `id` int NOT NULL AUTO_INCREMENT,
  `wtax_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `wtax_rate` float NOT NULL,
  `wtax_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `wtax_account_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wtax`
--

LOCK TABLES `wtax` WRITE;
/*!40000 ALTER TABLE `wtax` DISABLE KEYS */;
INSERT INTO `wtax` VALUES (1,'C-N/A',0,'C-N/A',3224,'2024-09-06 01:16:48'),(2,'C-Goods 1%',1,'C-Goods 1%',3224,'2024-09-06 01:17:00'),(3,'C-Services 2%',2,'C-Services 2%',3224,'2024-09-06 01:18:28'),(4,'V-N/A',0,'V-N/A',4036,'2024-09-06 01:18:44'),(5,'V-Goods 1%',1,'V-Goods 1%',4036,'2024-09-06 01:19:11'),(6,'V-Services 2%',2,'V-Services 2%',4036,'2024-09-06 01:20:21'),(7,'V-Rent 5%',5,'V-Rent 5%',4036,'2024-09-06 01:22:51'),(8,'V-10%',10,'V-10%',4036,'2024-09-06 01:23:05'),(9,'12412',124,'124412',1,'2024-10-17 15:22:25'),(10,'1%-goods',1,'1% withholding tax on goods',25,'2024-10-21 04:14:33'),(11,'2% - services',2,'2% withholding tax on services',25,'2024-10-21 04:15:09'),(12,'5% rent',5,'5% withholding tax on rent',25,'2024-10-21 04:15:36');
/*!40000 ALTER TABLE `wtax` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-10-22 15:24:57
