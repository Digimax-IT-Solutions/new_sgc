SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `account_types`;
CREATE TABLE `account_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `type_order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `account_types` VALUES("1","Accounts Payable","LIABILITIES","6");
INSERT INTO `account_types` VALUES("2","Accounts Receivable","ASSETS","2");
INSERT INTO `account_types` VALUES("3","Other Current Assets","ASSETS","3");
INSERT INTO `account_types` VALUES("4","Other Current Liabilities","LIABILITIES","7");
INSERT INTO `account_types` VALUES("5","Other Expense","OTHER EXPENSE","15");
INSERT INTO `account_types` VALUES("6","Other Income","OTHER INCOME","14");
INSERT INTO `account_types` VALUES("7","Fixed Assets","ASSETS","4");
INSERT INTO `account_types` VALUES("8","Loans Payable","LIABILITIES","8");
INSERT INTO `account_types` VALUES("9","Cost of Goods Sold","COST OF GOODS SOLD","12");
INSERT INTO `account_types` VALUES("10","Equity","EQUITY","10");
INSERT INTO `account_types` VALUES("11","Expenses","EXPENSE","13");
INSERT INTO `account_types` VALUES("12","Income","INCOME","11");
INSERT INTO `account_types` VALUES("13","Non-current Liabilities","LIABILITIES","0");
INSERT INTO `account_types` VALUES("14","Cash and Cash Equivalents","ASSETS","1");
INSERT INTO `account_types` VALUES("15","Other Non-current Liabilities","LIABILITIES","9");
INSERT INTO `account_types` VALUES("16","Other Non-current Assets","ASSETS","5");

DROP TABLE IF EXISTS `apv`;
CREATE TABLE `apv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `vendor_tin` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `apv_details`;
CREATE TABLE `apv_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `audit_trail`;
CREATE TABLE `audit_trail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `modified_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `audit_trail` VALUES("1","1","Invoice","2024-10-18","SI000000001","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","10000","0","superadmin","1","2024-10-18 12:32:32","2024-10-18 12:32:32");
INSERT INTO `audit_trail` VALUES("2","1","Invoice","2024-10-18","SI000000001","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","11","89.29","0","superadmin","1","2024-10-18 12:32:32","2024-10-18 12:32:32");
INSERT INTO `audit_trail` VALUES("3","1","Invoice","2024-10-18","SI000000001","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","16","0","1071.43","superadmin","1","2024-10-18 12:32:32","2024-10-18 12:32:32");
INSERT INTO `audit_trail` VALUES("4","1","Invoice","2024-10-18","SI000000001","4","Lloyd Golez","Monster Energy Drink","100","0.00","0.00","0.00","0.00","0.00","0.00","17","0","0","superadmin","1","2024-10-18 12:32:32","2024-10-18 12:32:32");
INSERT INTO `audit_trail` VALUES("5","1","Invoice","2024-10-18","SI000000001","4","Lloyd Golez","Monster Energy Drink","100","0.00","0.00","0.00","0.00","0.00","0.00","15","0","8928.57","superadmin","1","2024-10-18 12:32:32","2024-10-18 12:32:32");
INSERT INTO `audit_trail` VALUES("6","1","Invoice","2024-10-18","SI000000001","4","Lloyd Golez","Monster Energy Drink","100","0.00","0.00","0.00","0.00","0.00","0.00","14","10000","0","superadmin","1","2024-10-18 12:32:32","2024-10-18 12:32:32");
INSERT INTO `audit_trail` VALUES("7","1","Invoice","2024-10-18","SI000000001","4","Lloyd Golez","Monster Energy Drink","100","0.00","0.00","0.00","0.00","0.00","0.00","4","0","10000","superadmin","1","2024-10-18 12:32:32","2024-10-18 12:32:32");
INSERT INTO `audit_trail` VALUES("8","1","Invoice","2024-10-18","SI000000001","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","0","0","superadmin","1","2024-10-18 12:32:32","2024-10-18 12:32:32");
INSERT INTO `audit_trail` VALUES("9","1","Invoice","2024-10-18","SI000000001","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","0","89.29","superadmin","1","2024-10-18 12:32:32","2024-10-18 12:32:32");
INSERT INTO `audit_trail` VALUES("10","2","Invoice","2024-10-28","SI000000002","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","20","124","0","superadmin","1","2024-10-28 09:43:41","2024-10-28 09:43:41");
INSERT INTO `audit_trail` VALUES("11","2","Invoice","2024-10-28","SI000000002","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","0","0","0","superadmin","1","2024-10-28 09:43:41","2024-10-28 09:43:41");
INSERT INTO `audit_trail` VALUES("12","2","Invoice","2024-10-28","SI000000002","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","0","0","0","superadmin","1","2024-10-28 09:43:41","2024-10-28 09:43:41");
INSERT INTO `audit_trail` VALUES("13","2","Invoice","2024-10-28","SI000000002","4","Lloyd Golez","5","1","0.00","0.00","0.00","0.00","0.00","0.00","17","2.48","0","superadmin","1","2024-10-28 09:43:41","2024-10-28 09:43:41");
INSERT INTO `audit_trail` VALUES("14","2","Invoice","2024-10-28","SI000000002","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","20","0","2.48","superadmin","1","2024-10-28 09:43:41","2024-10-28 09:43:41");
INSERT INTO `audit_trail` VALUES("15","2","Invoice","2024-10-28","SI000000002","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","20","0","0","superadmin","1","2024-10-28 09:43:41","2024-10-28 09:43:41");
INSERT INTO `audit_trail` VALUES("16","3","Invoice","2024-10-28","SI000000003","5","124",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","15376","0","superadmin","1","2024-10-28 09:44:58","2024-10-28 09:44:58");
INSERT INTO `audit_trail` VALUES("17","3","Invoice","2024-10-28","SI000000003","5","124",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","11","130.42","0","superadmin","1","2024-10-28 09:44:58","2024-10-28 09:44:58");
INSERT INTO `audit_trail` VALUES("18","3","Invoice","2024-10-28","SI000000003","5","124",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","16","0","1565.06","superadmin","1","2024-10-28 09:44:58","2024-10-28 09:44:58");
INSERT INTO `audit_trail` VALUES("19","3","Invoice","2024-10-28","SI000000003","5","124","Monster Energy Drink","124","0.00","0.00","0.00","0.00","0.00","0.00","17","768.8","0","superadmin","1","2024-10-28 09:44:58","2024-10-28 09:44:58");
INSERT INTO `audit_trail` VALUES("20","3","Invoice","2024-10-28","SI000000003","5","124","Monster Energy Drink","124","0.00","0.00","0.00","0.00","0.00","0.00","15","0","13810.94","superadmin","1","2024-10-28 09:44:58","2024-10-28 09:44:58");
INSERT INTO `audit_trail` VALUES("21","3","Invoice","2024-10-28","SI000000003","5","124","Monster Energy Drink","124","0.00","0.00","0.00","0.00","0.00","0.00","14","15376","0","superadmin","1","2024-10-28 09:44:58","2024-10-28 09:44:58");
INSERT INTO `audit_trail` VALUES("22","3","Invoice","2024-10-28","SI000000003","5","124","Monster Energy Drink","124","0.00","0.00","0.00","0.00","0.00","0.00","4","0","15376","superadmin","1","2024-10-28 09:44:58","2024-10-28 09:44:58");
INSERT INTO `audit_trail` VALUES("23","3","Invoice","2024-10-28","SI000000003","5","124",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","0","768.8","superadmin","1","2024-10-28 09:44:58","2024-10-28 09:44:58");
INSERT INTO `audit_trail` VALUES("24","3","Invoice","2024-10-28","SI000000003","5","124",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","0","130.42","superadmin","1","2024-10-28 09:44:58","2024-10-28 09:44:58");
INSERT INTO `audit_trail` VALUES("25","4","Invoice","2024-10-28","SI000000004","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","5456","0","superadmin","1","2024-10-28 09:46:15","2024-10-28 09:46:15");
INSERT INTO `audit_trail` VALUES("26","4","Invoice","2024-10-28","SI000000004","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","11","53.47","0","superadmin","1","2024-10-28 09:46:15","2024-10-28 09:46:15");
INSERT INTO `audit_trail` VALUES("27","4","Invoice","2024-10-28","SI000000004","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","16","0","0","superadmin","1","2024-10-28 09:46:15","2024-10-28 09:46:15");
INSERT INTO `audit_trail` VALUES("28","4","Invoice","2024-10-28","SI000000004","4","Lloyd Golez","5","44","0.00","0.00","0.00","0.00","0.00","0.00","17","109.12","0","superadmin","1","2024-10-28 09:46:15","2024-10-28 09:46:15");
INSERT INTO `audit_trail` VALUES("29","4","Invoice","2024-10-28","SI000000004","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","0","109.12","superadmin","1","2024-10-28 09:46:15","2024-10-28 09:46:15");
INSERT INTO `audit_trail` VALUES("30","4","Invoice","2024-10-28","SI000000004","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","0","53.47","superadmin","1","2024-10-28 09:46:15","2024-10-28 09:46:15");
INSERT INTO `audit_trail` VALUES("31","5","Invoice","2024-10-28","SI000000005","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","700","0","superadmin","1","2024-10-28 10:48:00","2024-10-28 10:48:00");
INSERT INTO `audit_trail` VALUES("32","5","Invoice","2024-10-28","SI000000005","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","11","6.25","0","superadmin","1","2024-10-28 10:48:00","2024-10-28 10:48:00");
INSERT INTO `audit_trail` VALUES("33","5","Invoice","2024-10-28","SI000000005","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","16","0","75","superadmin","1","2024-10-28 10:48:00","2024-10-28 10:48:00");
INSERT INTO `audit_trail` VALUES("34","5","Invoice","2024-10-28","SI000000005","4","Lloyd Golez","Monster Energy Drink","50","0.00","0.00","0.00","0.00","0.00","0.00","17","0","0","superadmin","1","2024-10-28 10:48:00","2024-10-28 10:48:00");
INSERT INTO `audit_trail` VALUES("35","5","Invoice","2024-10-28","SI000000005","4","Lloyd Golez","Monster Energy Drink","50","0.00","0.00","0.00","0.00","0.00","0.00","15","0","625","superadmin","1","2024-10-28 10:48:00","2024-10-28 10:48:00");
INSERT INTO `audit_trail` VALUES("36","5","Invoice","2024-10-28","SI000000005","4","Lloyd Golez","Monster Energy Drink","50","0.00","0.00","0.00","0.00","0.00","0.00","14","700","0","superadmin","1","2024-10-28 10:48:00","2024-10-28 10:48:00");
INSERT INTO `audit_trail` VALUES("37","5","Invoice","2024-10-28","SI000000005","4","Lloyd Golez","Monster Energy Drink","50","0.00","0.00","0.00","0.00","0.00","0.00","4","0","700","superadmin","1","2024-10-28 10:48:00","2024-10-28 10:48:00");
INSERT INTO `audit_trail` VALUES("38","5","Invoice","2024-10-28","SI000000005","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","0","0","superadmin","1","2024-10-28 10:48:00","2024-10-28 10:48:00");
INSERT INTO `audit_trail` VALUES("39","5","Invoice","2024-10-28","SI000000005","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","0","6.25","superadmin","1","2024-10-28 10:48:00","2024-10-28 10:48:00");
INSERT INTO `audit_trail` VALUES("40","6","Invoice","2024-10-28","SI000000006","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","50000","0","superadmin","1","2024-10-28 10:52:28","2024-10-28 10:52:28");
INSERT INTO `audit_trail` VALUES("41","6","Invoice","2024-10-28","SI000000006","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","11","437.5","0","superadmin","1","2024-10-28 10:52:28","2024-10-28 10:52:28");
INSERT INTO `audit_trail` VALUES("42","6","Invoice","2024-10-28","SI000000006","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","16","0","5250","superadmin","1","2024-10-28 10:52:28","2024-10-28 10:52:28");
INSERT INTO `audit_trail` VALUES("43","6","Invoice","2024-10-28","SI000000006","4","Lloyd Golez","Monster Energy Drink","200","0.00","0.00","0.00","0.00","0.00","0.00","17","1000","0","superadmin","1","2024-10-28 10:52:28","2024-10-28 10:52:28");
INSERT INTO `audit_trail` VALUES("44","6","Invoice","2024-10-28","SI000000006","4","Lloyd Golez","Monster Energy Drink","200","0.00","0.00","0.00","0.00","0.00","0.00","15","0","44750","superadmin","1","2024-10-28 10:52:28","2024-10-28 10:52:28");
INSERT INTO `audit_trail` VALUES("45","6","Invoice","2024-10-28","SI000000006","4","Lloyd Golez","Monster Energy Drink","200","0.00","0.00","0.00","0.00","0.00","0.00","14","50000","0","superadmin","1","2024-10-28 10:52:28","2024-10-28 10:52:28");
INSERT INTO `audit_trail` VALUES("46","6","Invoice","2024-10-28","SI000000006","4","Lloyd Golez","Monster Energy Drink","200","0.00","0.00","0.00","0.00","0.00","0.00","4","0","50000","superadmin","1","2024-10-28 10:52:28","2024-10-28 10:52:28");
INSERT INTO `audit_trail` VALUES("47","6","Invoice","2024-10-28","SI000000006","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","0","1000","superadmin","1","2024-10-28 10:52:28","2024-10-28 10:52:28");
INSERT INTO `audit_trail` VALUES("48","6","Invoice","2024-10-28","SI000000006","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","0","437.5","superadmin","1","2024-10-28 10:52:28","2024-10-28 10:52:28");
INSERT INTO `audit_trail` VALUES("49","7","Invoice","2024-10-28","SI000000007","5","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","1500000","0","superadmin","1","2024-10-28 10:53:28","2024-10-28 10:53:28");
INSERT INTO `audit_trail` VALUES("50","7","Invoice","2024-10-28","SI000000007","5","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","11","12723.21","0","superadmin","1","2024-10-28 10:53:28","2024-10-28 10:53:28");
INSERT INTO `audit_trail` VALUES("51","7","Invoice","2024-10-28","SI000000007","5","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","16","0","152678.57","superadmin","1","2024-10-28 10:53:28","2024-10-28 10:53:28");
INSERT INTO `audit_trail` VALUES("52","7","Invoice","2024-10-28","SI000000007","5","Lloyd Golez","Monster Energy Drink","500","0.00","0.00","0.00","0.00","0.00","0.00","17","75000","0","superadmin","1","2024-10-28 10:53:28","2024-10-28 10:53:28");
INSERT INTO `audit_trail` VALUES("53","7","Invoice","2024-10-28","SI000000007","5","Lloyd Golez","Monster Energy Drink","500","0.00","0.00","0.00","0.00","0.00","0.00","15","0","1347321.43","superadmin","1","2024-10-28 10:53:28","2024-10-28 10:53:28");
INSERT INTO `audit_trail` VALUES("54","7","Invoice","2024-10-28","SI000000007","5","Lloyd Golez","Monster Energy Drink","500","0.00","0.00","0.00","0.00","0.00","0.00","14","1500000","0","superadmin","1","2024-10-28 10:53:28","2024-10-28 10:53:28");
INSERT INTO `audit_trail` VALUES("55","7","Invoice","2024-10-28","SI000000007","5","Lloyd Golez","Monster Energy Drink","500","0.00","0.00","0.00","0.00","0.00","0.00","4","0","1500000","superadmin","1","2024-10-28 10:53:28","2024-10-28 10:53:28");
INSERT INTO `audit_trail` VALUES("56","7","Invoice","2024-10-28","SI000000007","5","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","0","75000","superadmin","1","2024-10-28 10:53:28","2024-10-28 10:53:28");
INSERT INTO `audit_trail` VALUES("57","7","Invoice","2024-10-28","SI000000007","5","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","0","12723.21","superadmin","1","2024-10-28 10:53:28","2024-10-28 10:53:28");
INSERT INTO `audit_trail` VALUES("58","8","Invoice","2024-10-28","555125215","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","6875","0","superadmin","1","2024-10-28 13:52:41","2024-10-28 13:52:41");
INSERT INTO `audit_trail` VALUES("59","8","Invoice","2024-10-28","555125215","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","11","60.16","0","superadmin","1","2024-10-28 13:52:42","2024-10-28 13:52:42");
INSERT INTO `audit_trail` VALUES("60","8","Invoice","2024-10-28","555125215","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","16","0","721.87","superadmin","1","2024-10-28 13:52:42","2024-10-28 13:52:42");
INSERT INTO `audit_trail` VALUES("61","8","Invoice","2024-10-28","555125215","4","Lloyd Golez","5","55","0.00","0.00","0.00","0.00","0.00","0.00","17","137.5","0","superadmin","1","2024-10-28 13:52:42","2024-10-28 13:52:42");
INSERT INTO `audit_trail` VALUES("62","8","Invoice","2024-10-28","555125215","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","0","137.5","superadmin","1","2024-10-28 13:52:42","2024-10-28 13:52:42");
INSERT INTO `audit_trail` VALUES("63","8","Invoice","2024-10-28","555125215","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","0","60.16","superadmin","1","2024-10-28 13:52:42","2024-10-28 13:52:42");
INSERT INTO `audit_trail` VALUES("64","10","Invoice","2024-10-28","124","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","250000","0","superadmin","1","2024-10-28 14:11:17","2024-10-28 14:11:17");
INSERT INTO `audit_trail` VALUES("65","10","Invoice","2024-10-28","124","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","11","2232.14","0","superadmin","1","2024-10-28 14:11:17","2024-10-28 14:11:17");
INSERT INTO `audit_trail` VALUES("66","10","Invoice","2024-10-28","124","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","16","0","26785.71","superadmin","1","2024-10-28 14:11:17","2024-10-28 14:11:17");
INSERT INTO `audit_trail` VALUES("67","10","Invoice","2024-10-28","124","4","Lloyd Golez","Monster Energy Drink","500","0.00","0.00","0.00","0.00","0.00","0.00","17","0","0","superadmin","1","2024-10-28 14:11:17","2024-10-28 14:11:17");
INSERT INTO `audit_trail` VALUES("68","10","Invoice","2024-10-28","124","4","Lloyd Golez","Monster Energy Drink","500","0.00","0.00","0.00","0.00","0.00","0.00","15","0","223214.29","superadmin","1","2024-10-28 14:11:17","2024-10-28 14:11:17");
INSERT INTO `audit_trail` VALUES("69","10","Invoice","2024-10-28","124","4","Lloyd Golez","Monster Energy Drink","500","0.00","0.00","0.00","0.00","0.00","0.00","14","250000","0","superadmin","1","2024-10-28 14:11:17","2024-10-28 14:11:17");
INSERT INTO `audit_trail` VALUES("70","10","Invoice","2024-10-28","124","4","Lloyd Golez","Monster Energy Drink","500","0.00","0.00","0.00","0.00","0.00","0.00","4","0","250000","superadmin","1","2024-10-28 14:11:17","2024-10-28 14:11:17");
INSERT INTO `audit_trail` VALUES("71","10","Invoice","2024-10-28","124","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","0","0","superadmin","1","2024-10-28 14:11:17","2024-10-28 14:11:17");
INSERT INTO `audit_trail` VALUES("72","10","Invoice","2024-10-28","124","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","0","2232.14","superadmin","1","2024-10-28 14:11:17","2024-10-28 14:11:17");
INSERT INTO `audit_trail` VALUES("73","11","Invoice","2024-10-28","12455555","4","gegege",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","15376","0","superadmin","1","2024-10-28 14:34:53","2024-10-28 14:34:53");
INSERT INTO `audit_trail` VALUES("74","11","Invoice","2024-10-28","12455555","4","gegege",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","11","134.54","0","superadmin","1","2024-10-28 14:34:53","2024-10-28 14:34:53");
INSERT INTO `audit_trail` VALUES("75","11","Invoice","2024-10-28","12455555","4","gegege",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","16","0","1614.48","superadmin","1","2024-10-28 14:34:53","2024-10-28 14:34:53");
INSERT INTO `audit_trail` VALUES("76","11","Invoice","2024-10-28","12455555","4","gegege","5","124","0.00","0.00","0.00","0.00","0.00","0.00","17","307.52","0","superadmin","1","2024-10-28 14:34:53","2024-10-28 14:34:53");
INSERT INTO `audit_trail` VALUES("77","11","Invoice","2024-10-28","12455555","4","gegege",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","0","307.52","superadmin","1","2024-10-28 14:34:53","2024-10-28 14:34:53");
INSERT INTO `audit_trail` VALUES("78","11","Invoice","2024-10-28","12455555","4","gegege",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","0","134.54","superadmin","1","2024-10-28 14:34:53","2024-10-28 14:34:53");
INSERT INTO `audit_trail` VALUES("79","12","Invoice","2024-10-28","12422222","4","trtrt",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","15376","0","superadmin","1","2024-10-28 14:43:52","2024-10-28 14:43:52");
INSERT INTO `audit_trail` VALUES("80","12","Invoice","2024-10-28","12422222","4","trtrt",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","11","153.76","0","superadmin","1","2024-10-28 14:43:52","2024-10-28 14:43:52");
INSERT INTO `audit_trail` VALUES("81","12","Invoice","2024-10-28","12422222","4","trtrt",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","16","0","0","superadmin","1","2024-10-28 14:43:52","2024-10-28 14:43:52");
INSERT INTO `audit_trail` VALUES("82","12","Invoice","2024-10-28","12422222","4","trtrt","Monster Energy Drink","124","0.00","0.00","0.00","0.00","0.00","0.00","17","0","0","superadmin","1","2024-10-28 14:43:52","2024-10-28 14:43:52");
INSERT INTO `audit_trail` VALUES("83","12","Invoice","2024-10-28","12422222","4","trtrt","Monster Energy Drink","124","0.00","0.00","0.00","0.00","0.00","0.00","15","0","15376","superadmin","1","2024-10-28 14:43:52","2024-10-28 14:43:52");
INSERT INTO `audit_trail` VALUES("84","12","Invoice","2024-10-28","12422222","4","trtrt","Monster Energy Drink","124","0.00","0.00","0.00","0.00","0.00","0.00","14","15376","0","superadmin","1","2024-10-28 14:43:52","2024-10-28 14:43:52");
INSERT INTO `audit_trail` VALUES("85","12","Invoice","2024-10-28","12422222","4","trtrt","Monster Energy Drink","124","0.00","0.00","0.00","0.00","0.00","0.00","4","0","15376","superadmin","1","2024-10-28 14:43:52","2024-10-28 14:43:52");
INSERT INTO `audit_trail` VALUES("86","12","Invoice","2024-10-28","12422222","4","trtrt",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","0","0","superadmin","1","2024-10-28 14:43:52","2024-10-28 14:43:52");
INSERT INTO `audit_trail` VALUES("87","12","Invoice","2024-10-28","12422222","4","trtrt",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","0","153.76","superadmin","1","2024-10-28 14:43:52","2024-10-28 14:43:52");
INSERT INTO `audit_trail` VALUES("88","13","Invoice","2024-10-28","124QQ$@$!2","4","qweqweqwe",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","1488","0","superadmin","1","2024-10-28 14:44:58","2024-10-28 14:44:58");
INSERT INTO `audit_trail` VALUES("89","13","Invoice","2024-10-28","124QQ$@$!2","4","qweqweqwe",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","11","14.88","0","superadmin","1","2024-10-28 14:44:58","2024-10-28 14:44:58");
INSERT INTO `audit_trail` VALUES("90","13","Invoice","2024-10-28","124QQ$@$!2","4","qweqweqwe",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","16","0","0","superadmin","1","2024-10-28 14:44:58","2024-10-28 14:44:58");
INSERT INTO `audit_trail` VALUES("91","13","Invoice","2024-10-28","124QQ$@$!2","4","qweqweqwe","Monster Energy Drink","124","0.00","0.00","0.00","0.00","0.00","0.00","17","0","0","superadmin","1","2024-10-28 14:44:58","2024-10-28 14:44:58");
INSERT INTO `audit_trail` VALUES("92","13","Invoice","2024-10-28","124QQ$@$!2","4","qweqweqwe","Monster Energy Drink","124","0.00","0.00","0.00","0.00","0.00","0.00","15","0","1488","superadmin","1","2024-10-28 14:44:58","2024-10-28 14:44:58");
INSERT INTO `audit_trail` VALUES("93","13","Invoice","2024-10-28","124QQ$@$!2","4","qweqweqwe","Monster Energy Drink","124","0.00","0.00","0.00","0.00","0.00","0.00","14","1488","0","superadmin","1","2024-10-28 14:44:58","2024-10-28 14:44:58");
INSERT INTO `audit_trail` VALUES("94","13","Invoice","2024-10-28","124QQ$@$!2","4","qweqweqwe","Monster Energy Drink","124","0.00","0.00","0.00","0.00","0.00","0.00","4","0","1488","superadmin","1","2024-10-28 14:44:58","2024-10-28 14:44:58");
INSERT INTO `audit_trail` VALUES("95","13","Invoice","2024-10-28","124QQ$@$!2","4","qweqweqwe",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","0","0","superadmin","1","2024-10-28 14:44:58","2024-10-28 14:44:58");
INSERT INTO `audit_trail` VALUES("96","13","Invoice","2024-10-28","124QQ$@$!2","4","qweqweqwe",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","0","14.88","superadmin","1","2024-10-28 14:44:58","2024-10-28 14:44:58");
INSERT INTO `audit_trail` VALUES("97","14","Invoice","2024-10-29","125","4","214555",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","15625","0","superadmin","1","2024-10-29 12:20:16","2024-10-29 12:20:16");
INSERT INTO `audit_trail` VALUES("98","14","Invoice","2024-10-29","125","4","214555",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","11","156.25","0","superadmin","1","2024-10-29 12:20:16","2024-10-29 12:20:16");
INSERT INTO `audit_trail` VALUES("99","14","Invoice","2024-10-29","125","4","214555",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","16","0","0","superadmin","1","2024-10-29 12:20:16","2024-10-29 12:20:16");
INSERT INTO `audit_trail` VALUES("100","14","Invoice","2024-10-29","125","4","214555","5","125","0.00","0.00","0.00","0.00","0.00","0.00","17","0","0","superadmin","1","2024-10-29 12:20:16","2024-10-29 12:20:16");
INSERT INTO `audit_trail` VALUES("101","14","Invoice","2024-10-29","125","4","214555",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","0","0","superadmin","1","2024-10-29 12:20:16","2024-10-29 12:20:16");
INSERT INTO `audit_trail` VALUES("102","14","Invoice","2024-10-29","125","4","214555",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","0","156.25","superadmin","1","2024-10-29 12:20:16","2024-10-29 12:20:16");
INSERT INTO `audit_trail` VALUES("103","1","Check Expense","2024-10-29","CV000000001","5","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","2","110.98","0","superadmin","1","2024-10-29 14:59:13","2024-10-29 14:59:13");
INSERT INTO `audit_trail` VALUES("104","1","Check Expense","2024-10-29","CV000000001","5","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","17","0","2.48","superadmin","1","2024-10-29 14:59:13","2024-10-29 14:59:13");
INSERT INTO `audit_trail` VALUES("105","1","Check Expense","2024-10-29","CV000000001","5","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","18","13.02","0","superadmin","1","2024-10-29 14:59:13","2024-10-29 14:59:13");
INSERT INTO `audit_trail` VALUES("106","1","Check Expense","2024-10-29","CV000000001","5","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","11","0","1.09","superadmin","1","2024-10-29 14:59:13","2024-10-29 14:59:13");
INSERT INTO `audit_trail` VALUES("107","1","Check Expense","2024-10-29","CV000000001","5","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","1","0","120.44","superadmin","1","2024-10-29 14:59:13","2024-10-29 14:59:13");
INSERT INTO `audit_trail` VALUES("108","15","Invoice","2024-11-04","SI000000008","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","100","0","superadmin","1","2024-11-04 04:58:41","2024-11-04 04:58:41");
INSERT INTO `audit_trail` VALUES("109","15","Invoice","2024-11-04","SI000000008","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","11","0.89","0","superadmin","1","2024-11-04 04:58:41","2024-11-04 04:58:41");
INSERT INTO `audit_trail` VALUES("110","15","Invoice","2024-11-04","SI000000008","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","16","0","10.71","superadmin","1","2024-11-04 04:58:41","2024-11-04 04:58:41");
INSERT INTO `audit_trail` VALUES("111","15","Invoice","2024-11-04","SI000000008","4","Lloyd Golez","5","1","0.00","0.00","0.00","0.00","0.00","0.00","17","0","0","superadmin","1","2024-11-04 04:58:41","2024-11-04 04:58:41");
INSERT INTO `audit_trail` VALUES("112","15","Invoice","2024-11-04","SI000000008","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","0","0","superadmin","1","2024-11-04 04:58:41","2024-11-04 04:58:41");
INSERT INTO `audit_trail` VALUES("113","15","Invoice","2024-11-04","SI000000008","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","0","0.89","superadmin","1","2024-11-04 04:58:41","2024-11-04 04:58:41");
INSERT INTO `audit_trail` VALUES("114","1","Payment","2024-11-04","CR000000001","0","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","20","9910.71","0","superadmin","1","2024-11-04 05:03:01","2024-11-04 05:03:01");
INSERT INTO `audit_trail` VALUES("115","1","Payment","2024-11-04","CR000000001","0","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","3","0","9910.71","superadmin","1","2024-11-04 05:03:01","2024-11-04 05:03:01");
INSERT INTO `audit_trail` VALUES("116","2","Check Expense","2024-11-04","CV000000002","5","124",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","1","89.29","0","superadmin","1","2024-11-04 05:25:42","2024-11-04 05:25:42");
INSERT INTO `audit_trail` VALUES("117","2","Check Expense","2024-11-04","CV000000002","5","124",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","17","0","0","superadmin","1","2024-11-04 05:25:42","2024-11-04 05:25:42");
INSERT INTO `audit_trail` VALUES("118","2","Check Expense","2024-11-04","CV000000002","5","124",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","18","10.71","0","superadmin","1","2024-11-04 05:25:42","2024-11-04 05:25:42");
INSERT INTO `audit_trail` VALUES("119","2","Check Expense","2024-11-04","CV000000002","5","124",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","11","0","0.89","superadmin","1","2024-11-04 05:25:42","2024-11-04 05:25:42");
INSERT INTO `audit_trail` VALUES("120","2","Check Expense","2024-11-04","CV000000002","5","124",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","2","0","99.11","superadmin","1","2024-11-04 05:25:42","2024-11-04 05:25:42");
INSERT INTO `audit_trail` VALUES("121","3","Check Expense","2024-11-04","CV000000003","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","1","446.43","0","superadmin","1","2024-11-04 05:27:07","2024-11-04 05:27:07");
INSERT INTO `audit_trail` VALUES("122","3","Check Expense","2024-11-04","CV000000003","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","17","0","0","superadmin","1","2024-11-04 05:27:07","2024-11-04 05:27:07");
INSERT INTO `audit_trail` VALUES("123","3","Check Expense","2024-11-04","CV000000003","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","18","53.57","0","superadmin","1","2024-11-04 05:27:07","2024-11-04 05:27:07");
INSERT INTO `audit_trail` VALUES("124","3","Check Expense","2024-11-04","CV000000003","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","11","0","4.46","superadmin","1","2024-11-04 05:27:07","2024-11-04 05:27:07");
INSERT INTO `audit_trail` VALUES("125","3","Check Expense","2024-11-04","CV000000003","4","Lloyd Golez",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","2","0","495.54","superadmin","1","2024-11-04 05:27:07","2024-11-04 05:27:07");
INSERT INTO `audit_trail` VALUES("126","4","Check Expense","2024-11-04","CV000000004","5","2",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","1","110.71","0","superadmin","1","2024-11-04 05:41:03","2024-11-04 05:41:03");
INSERT INTO `audit_trail` VALUES("127","4","Check Expense","2024-11-04","CV000000004","5","2",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","17","0","0","superadmin","1","2024-11-04 05:41:03","2024-11-04 05:41:03");
INSERT INTO `audit_trail` VALUES("128","4","Check Expense","2024-11-04","CV000000004","5","2",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","18","13.29","0","superadmin","1","2024-11-04 05:41:03","2024-11-04 05:41:03");
INSERT INTO `audit_trail` VALUES("129","4","Check Expense","2024-11-04","CV000000004","5","2",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","10","0","2.21","superadmin","1","2024-11-04 05:41:03","2024-11-04 05:41:03");
INSERT INTO `audit_trail` VALUES("130","4","Check Expense","2024-11-04","CV000000004","5","2",NULL,NULL,"0.00","0.00","0.00","0.00","0.00","0.00","2","0","121.79","superadmin","1","2024-11-04 05:41:03","2024-11-04 05:41:03");

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `categories` VALUES("44","Category 1");
INSERT INTO `categories` VALUES("46","Maintenance Supplies");
INSERT INTO `categories` VALUES("22","Sample");

DROP TABLE IF EXISTS `chart_of_account`;
CREATE TABLE `chart_of_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_code` varchar(50) NOT NULL,
  `account_type_id` int(11) NOT NULL,
  `account_name` varchar(50) NOT NULL,
  `account_description` varchar(250) DEFAULT NULL,
  `balance` double(15,2) DEFAULT 0.00,
  `sub_account_id` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `chart_of_account` VALUES("1","10000","14","Petty Cash Fund","Petty Cash Fund","0","0","2024-10-18 12:23:42");
INSERT INTO `chart_of_account` VALUES("2","10010","14","Cash in Bank","Cash in Bank","0","0","2024-10-18 12:23:42");
INSERT INTO `chart_of_account` VALUES("3","11000","2","Accounts Receivable","Accounts Receivable","0","0","2024-10-18 12:23:42");
INSERT INTO `chart_of_account` VALUES("4","12000","3","Inventory","Inventory","0","0","2024-10-18 12:23:42");
INSERT INTO `chart_of_account` VALUES("5","13000","3","Prepayments","Prepayments","0","0","2024-10-18 12:23:42");
INSERT INTO `chart_of_account` VALUES("6","14000","7","Leasehold Improvements","Leasehold Improvements","0","0","2024-10-18 12:23:42");
INSERT INTO `chart_of_account` VALUES("7","15000","7","Accumulated Depreciation","Accumulated Depreciation","0","0","2024-10-18 12:23:42");
INSERT INTO `chart_of_account` VALUES("8","20000","1","Accounts Payable","Accounts Payable","0","0","2024-10-18 12:23:42");
INSERT INTO `chart_of_account` VALUES("9","20100","1","VAT Payable","VAT Payable","0","0","2024-10-18 12:23:42");
INSERT INTO `chart_of_account` VALUES("10","20110","1","Withholding Tax Payable","Withholding Tax Payable","0","0","2024-10-18 12:23:42");
INSERT INTO `chart_of_account` VALUES("11","12110","3","Creditable Tax Withheld","Creditable Tax Withheld","0","0","2024-10-18 12:23:42");
INSERT INTO `chart_of_account` VALUES("12","30000","10","Capital Stock","Capital Stock","0","0","2024-10-18 12:23:42");
INSERT INTO `chart_of_account` VALUES("13","30100","10","Retained Earnings","Retained Earnings","0","0","2024-10-18 12:23:42");
INSERT INTO `chart_of_account` VALUES("14","50000","9","Cost of Goods Sold","Cost of Goods Sold","0","0","2024-10-18 12:23:42");
INSERT INTO `chart_of_account` VALUES("15","40000","12","Sales Income","Sales Income","0","0","2024-10-18 12:23:42");
INSERT INTO `chart_of_account` VALUES("16","40100","12","Output VAT","Output VAT","0","0","2024-10-18 12:23:42");
INSERT INTO `chart_of_account` VALUES("17","40200","12","Sales Discount","Sales Discount","0","0","2024-10-18 12:23:42");
INSERT INTO `chart_of_account` VALUES("18","50100","9","Input VAT","Input VAT","0","0","2024-10-18 12:23:42");
INSERT INTO `chart_of_account` VALUES("19","123","1","123","123","0","9","2024-10-28 09:11:45");
INSERT INTO `chart_of_account` VALUES("20","13200","3","Undeposited Funds","Undeposited Funds","0","0","2024-11-04 05:02:45");

DROP TABLE IF EXISTS `checks`;
CREATE TABLE `checks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `create_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `company_settings`;
CREATE TABLE `company_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `zip_code` varchar(20) NOT NULL,
  `contact` varchar(50) NOT NULL,
  `tin` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `company_settings` VALUES("1","Eurospec",NULL,"5125","125","125","125","2024-10-17 23:37:58","2024-10-18 00:29:14");

DROP TABLE IF EXISTS `cost_center`;
CREATE TABLE `cost_center` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `particular` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cost_center` VALUES("2","100","Office of the President");
INSERT INTO `cost_center` VALUES("3","200","Office of the Asst.to the  President");
INSERT INTO `cost_center` VALUES("4","201","Office of the Finance Controller");
INSERT INTO `cost_center` VALUES("5","202","Office of the Purchasing Mgr.");
INSERT INTO `cost_center` VALUES("6","300","Office of the Resident Mgr.");
INSERT INTO `cost_center` VALUES("7","303","Office of the Asst. Resident Mgr.");
INSERT INTO `cost_center` VALUES("8","301","Office of the Purchasing Mgr.");
INSERT INTO `cost_center` VALUES("9","302","Purchasing Section");
INSERT INTO `cost_center` VALUES("10","400","Office of the Plant Accountant");
INSERT INTO `cost_center` VALUES("11","401","General Accounting Section");
INSERT INTO `cost_center` VALUES("12","402","Production Acctg. Section");
INSERT INTO `cost_center` VALUES("13","403","Cashiering Section");
INSERT INTO `cost_center` VALUES("14","500","Office of the Admin. Mgr.");
INSERT INTO `cost_center` VALUES("16","502","Medical / Dental Section");
INSERT INTO `cost_center` VALUES("17","503","Service Vehicle Section");
INSERT INTO `cost_center` VALUES("18","504","Civil Works Section");
INSERT INTO `cost_center` VALUES("19","505","CDT Section");
INSERT INTO `cost_center` VALUES("20","506","Office of the Trans. Supt.");
INSERT INTO `cost_center` VALUES("21","507","Heavy Equipment Section");
INSERT INTO `cost_center` VALUES("22","508","Mudpress/Bagasse Section");
INSERT INTO `cost_center` VALUES("23","509","Trans. Maintenance Section");
INSERT INTO `cost_center` VALUES("24","600","Office of  the Gen. Services Mgr.");
INSERT INTO `cost_center` VALUES("25","601","Audit & Budget Section");
INSERT INTO `cost_center` VALUES("26","602","Material Warehouse Section");
INSERT INTO `cost_center` VALUES("27","603","Sugar/Molasses Section");
INSERT INTO `cost_center` VALUES("28","700","Office of the Operation Div. Mgr.");
INSERT INTO `cost_center` VALUES("29","701","Office of the Factory Maint. Supt.");
INSERT INTO `cost_center` VALUES("30","702","Machine/Factory Maint. Section");
INSERT INTO `cost_center` VALUES("31","703","Office of the Mill Caneyard Supt.");
INSERT INTO `cost_center` VALUES("32","704","Mill Section");
INSERT INTO `cost_center` VALUES("33","705","Cane Yard Section");
INSERT INTO `cost_center` VALUES("34","706","Office of the Boiler Supt.");
INSERT INTO `cost_center` VALUES("35","707","Boiler Section");
INSERT INTO `cost_center` VALUES("36","708","Office of the PHESD Supt.");
INSERT INTO `cost_center` VALUES("37","709","PowerHouseESD Section");
INSERT INTO `cost_center` VALUES("38","710","Cooling Tower");
INSERT INTO `cost_center` VALUES("39","800","Office of the Production Div. Mgr.");
INSERT INTO `cost_center` VALUES("40","801","Refinery Department");
INSERT INTO `cost_center` VALUES("41","802","Office of the Boiling House Supt.");
INSERT INTO `cost_center` VALUES("42","803","Boiling House Department");
INSERT INTO `cost_center` VALUES("43","804","Office of the QA Supt.");
INSERT INTO `cost_center` VALUES("44","805","Quality Assurance Department");
INSERT INTO `cost_center` VALUES("47","900","Makati");

DROP TABLE IF EXISTS `credit_memo`;
CREATE TABLE `credit_memo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `print_status` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `credit_memo_details`;
CREATE TABLE `credit_memo_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `balance_due` double(15,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `customers` VALUES("1","Lloyd Golez","100011AXE","09945765116","Blk 41 Lot 8 Calamba City Laguna","Blk 41 Lot 8 Calamba City Laguna","4221-24535-0002","NET 7","9985125","golez.sf@gmail.com","1736961.03",NULL,NULL,NULL,NULL);
INSERT INTO `customers` VALUES("2","124","","","","","","","","","14476.78",NULL,NULL,NULL,NULL);
INSERT INTO `customers` VALUES("3","5555","","","","","","","","","0.00",NULL,NULL,NULL,NULL);
INSERT INTO `customers` VALUES("4","gegege",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"0.00",NULL,NULL,NULL,NULL);
INSERT INTO `customers` VALUES("5","gegege",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"14933.94",NULL,NULL,NULL,NULL);
INSERT INTO `customers` VALUES("6","trtrt",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"0.00",NULL,NULL,NULL,NULL);
INSERT INTO `customers` VALUES("7","trtrt",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"15222.24",NULL,NULL,NULL,NULL);
INSERT INTO `customers` VALUES("8","qweqweqwe",NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,"1473.12",NULL,NULL,NULL,NULL);

DROP TABLE IF EXISTS `discount`;
CREATE TABLE `discount` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `discount_name` varchar(50) NOT NULL,
  `discount_rate` float NOT NULL,
  `discount_description` text NOT NULL,
  `discount_account_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `discount` VALUES("26","N/A","0","N/A","17","2024-07-15 04:34:29");
INSERT INTO `discount` VALUES("27","S01","1","1% Sales Discount","17","2024-07-15 04:34:41");
INSERT INTO `discount` VALUES("28","S50","50","50% Sales Discount","17","2024-07-15 04:34:50");
INSERT INTO `discount` VALUES("30","S10","10","10% Sales Discount","17","2024-07-16 13:58:51");
INSERT INTO `discount` VALUES("31","S05","5","5% Sales Discount","17","2024-07-16 13:59:32");
INSERT INTO `discount` VALUES("32","S02","2","S02","17","2024-09-11 11:56:21");

DROP TABLE IF EXISTS `employee`;
CREATE TABLE `employee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `pagibig` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `employee` VALUES("4","0","KP","Lloyd","Banggay","Golez","N/A","Digiamx","123-1235","","BJ4","ST.","Mahogany","Calamba","Calamba","2047","12312312","","1231231");

DROP TABLE IF EXISTS `fs_classification`;
CREATE TABLE `fs_classification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `fs_classification` VALUES("1","Cash");
INSERT INTO `fs_classification` VALUES("2","Receivables");
INSERT INTO `fs_classification` VALUES("3","Other Current Assets");
INSERT INTO `fs_classification` VALUES("4","Inventories");
INSERT INTO `fs_classification` VALUES("5","Other Noncurrent Assets");
INSERT INTO `fs_classification` VALUES("6","Other Current Assests");
INSERT INTO `fs_classification` VALUES("7","Investment and Advances to a Subsidiary");
INSERT INTO `fs_classification` VALUES("8","PPE");
INSERT INTO `fs_classification` VALUES("9","Accumulated Depreciation");
INSERT INTO `fs_classification` VALUES("10","Accounts and Other Payables");
INSERT INTO `fs_classification` VALUES("11","Deferred Tax Liability");
INSERT INTO `fs_classification` VALUES("12","Loans Payable");
INSERT INTO `fs_classification` VALUES("13","Authorized Capital Stock");
INSERT INTO `fs_classification` VALUES("14","Unissued Capital Stock");
INSERT INTO `fs_classification` VALUES("15","Capital Stock");
INSERT INTO `fs_classification` VALUES("16","Retained Earnings (Deficit)");
INSERT INTO `fs_classification` VALUES("17","Prior Period Adjustment");
INSERT INTO `fs_classification` VALUES("18","Deposit on Future Stock Subscription");
INSERT INTO `fs_classification` VALUES("19","Revenue & Expense Summary");
INSERT INTO `fs_classification` VALUES("20","Revenue from contract with customers");
INSERT INTO `fs_classification` VALUES("21","Interest Income");
INSERT INTO `fs_classification` VALUES("22","EQUITY in Net Loss of Affiliated Co.");
INSERT INTO `fs_classification` VALUES("23","Cost of Raw Sugar Sold");
INSERT INTO `fs_classification` VALUES("24","Repairs and maintenance");
INSERT INTO `fs_classification` VALUES("25","Fuel, Oil and Lubricant");
INSERT INTO `fs_classification` VALUES("26","Supplies");
INSERT INTO `fs_classification` VALUES("27","Salaries and Wages");
INSERT INTO `fs_classification` VALUES("28","Employee Benefits");
INSERT INTO `fs_classification` VALUES("29","Trucking hauling and trash incentives");
INSERT INTO `fs_classification` VALUES("30","Contract Services");
INSERT INTO `fs_classification` VALUES("31","Sugar & Molasses Handling");
INSERT INTO `fs_classification` VALUES("32","Sugar Lien Expenses");
INSERT INTO `fs_classification` VALUES("33","Finance Costs");
INSERT INTO `fs_classification` VALUES("34","Provision for Impairment on Investment");
INSERT INTO `fs_classification` VALUES("35","Provision For Income Tax");
INSERT INTO `fs_classification` VALUES("36","Insurance Expenses");
INSERT INTO `fs_classification` VALUES("37","Taxes & Licenses");
INSERT INTO `fs_classification` VALUES("38","Security Services (Outside Services)");
INSERT INTO `fs_classification` VALUES("39","Professional, Legal & Audit Fees");
INSERT INTO `fs_classification` VALUES("40","Light & Power Expenses");
INSERT INTO `fs_classification` VALUES("41","Freight & Handling");
INSERT INTO `fs_classification` VALUES("42","Transportation & Travelling");
INSERT INTO `fs_classification` VALUES("43","Miscellaneous Expenses");
INSERT INTO `fs_classification` VALUES("44","Recruitment, Trainings & Seminars");
INSERT INTO `fs_classification` VALUES("45","Medical & Dental Supplies");
INSERT INTO `fs_classification` VALUES("46","Recreations & Other Social Activities");
INSERT INTO `fs_classification` VALUES("47","Membership/Condominium dues");
INSERT INTO `fs_classification` VALUES("48","Ads, Donations & Promotions");
INSERT INTO `fs_classification` VALUES("49","Provision for impairment losses on CWT");
INSERT INTO `fs_classification` VALUES("50","Depreciation Expenses");
INSERT INTO `fs_classification` VALUES("51","Sales Discount");
INSERT INTO `fs_classification` VALUES("52","Purchase Discount");
INSERT INTO `fs_classification` VALUES("53","Cost of Refined Sugar Sold");

DROP TABLE IF EXISTS `fs_notes_classification`;
CREATE TABLE `fs_notes_classification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `fs_notes_classification` VALUES("1","Cash on hand");
INSERT INTO `fs_notes_classification` VALUES("2","Cash in bank");
INSERT INTO `fs_notes_classification` VALUES("3","Trade");
INSERT INTO `fs_notes_classification` VALUES("4","Others");
INSERT INTO `fs_notes_classification` VALUES("5","Planters");
INSERT INTO `fs_notes_classification` VALUES("6","Advances to Officers and Employees");
INSERT INTO `fs_notes_classification` VALUES("7","Other Current Assets");
INSERT INTO `fs_notes_classification` VALUES("8","Prepayments");
INSERT INTO `fs_notes_classification` VALUES("9","ALLOWANCE FOR DOUBTFUL ACCOUNT");
INSERT INTO `fs_notes_classification` VALUES("10","Sugar Inventory");
INSERT INTO `fs_notes_classification` VALUES("11","Molasses Inventory");
INSERT INTO `fs_notes_classification` VALUES("12","Materials, supplies and spare parts");
INSERT INTO `fs_notes_classification` VALUES("13","Fuel, Oil and Lubricant");
INSERT INTO `fs_notes_classification` VALUES("14","Material in Transit");
INSERT INTO `fs_notes_classification` VALUES("15","Allowance For Inventory Obsolescence");
INSERT INTO `fs_notes_classification` VALUES("16","Allowance For Impairment Loss on Input Vat");
INSERT INTO `fs_notes_classification` VALUES("17","Input Vat");
INSERT INTO `fs_notes_classification` VALUES("18","Allowance for Inv. Obsolescene");
INSERT INTO `fs_notes_classification` VALUES("19","Advance VAT");
INSERT INTO `fs_notes_classification` VALUES("20","Creditable Withholding Tax (CWT)");
INSERT INTO `fs_notes_classification` VALUES("21","Allowance For Impairment Loss on CWT");
INSERT INTO `fs_notes_classification` VALUES("22","Allowance For Impairment Loss on Taxes");
INSERT INTO `fs_notes_classification` VALUES("23","Advances");
INSERT INTO `fs_notes_classification` VALUES("24","Investment at Cost");
INSERT INTO `fs_notes_classification` VALUES("25","Allowance for Impairment on Investment");
INSERT INTO `fs_notes_classification` VALUES("26","Allowance for Impairment on Advances to Subsidiary");
INSERT INTO `fs_notes_classification` VALUES("27","Land");
INSERT INTO `fs_notes_classification` VALUES("28","Land Improvements");
INSERT INTO `fs_notes_classification` VALUES("29","Building, Office Condominium and Structures");
INSERT INTO `fs_notes_classification` VALUES("30","Mill, Refinery and Machinery Equipment");
INSERT INTO `fs_notes_classification` VALUES("31","Transportation Equipment");
INSERT INTO `fs_notes_classification` VALUES("32","Office Furniture and Equipment");
INSERT INTO `fs_notes_classification` VALUES("33","Laboratory and Communication Equipment");
INSERT INTO `fs_notes_classification` VALUES("34","Construction in Progress");
INSERT INTO `fs_notes_classification` VALUES("35","Deposits");
INSERT INTO `fs_notes_classification` VALUES("36","Accounts Payable");
INSERT INTO `fs_notes_classification` VALUES("37","Payable to Government Agencies");
INSERT INTO `fs_notes_classification` VALUES("38","Advances from Customers");
INSERT INTO `fs_notes_classification` VALUES("39","Deferred Tax liability");
INSERT INTO `fs_notes_classification` VALUES("40","Accrued Expenses");
INSERT INTO `fs_notes_classification` VALUES("41","Loans Payable");
INSERT INTO `fs_notes_classification` VALUES("42","Authorized Capital Stock");
INSERT INTO `fs_notes_classification` VALUES("43","Unissued Capital Stock");
INSERT INTO `fs_notes_classification` VALUES("44","Capital Stock");
INSERT INTO `fs_notes_classification` VALUES("45","Retained Earnings (Deficit)");
INSERT INTO `fs_notes_classification` VALUES("46","Provision for impairment losses on CWT");
INSERT INTO `fs_notes_classification` VALUES("47","Deposit on Future Stock Subscription");
INSERT INTO `fs_notes_classification` VALUES("48","Revenue & Expense Summary");
INSERT INTO `fs_notes_classification` VALUES("49","Sale of Raw Sugar");
INSERT INTO `fs_notes_classification` VALUES("50","Sale of Refined Sugar");
INSERT INTO `fs_notes_classification` VALUES("51","Sale of Molasses");
INSERT INTO `fs_notes_classification` VALUES("52","Tolling Services");
INSERT INTO `fs_notes_classification` VALUES("53","Milling Services");
INSERT INTO `fs_notes_classification` VALUES("54","Interest Income");
INSERT INTO `fs_notes_classification` VALUES("55","Storage, Handling, Hauling Fees and others");
INSERT INTO `fs_notes_classification` VALUES("56","EQUITY in Net Loss of Affiliated Co.");
INSERT INTO `fs_notes_classification` VALUES("57","Rental Income");
INSERT INTO `fs_notes_classification` VALUES("58","Cost of Raw Sugar Sold");
INSERT INTO `fs_notes_classification` VALUES("59","Repairs and maintenance");
INSERT INTO `fs_notes_classification` VALUES("60","Supplies");
INSERT INTO `fs_notes_classification` VALUES("61","Salaries and Wages");
INSERT INTO `fs_notes_classification` VALUES("62","Employee Benefits");
INSERT INTO `fs_notes_classification` VALUES("63","Trucking hauling and trash incentives");
INSERT INTO `fs_notes_classification` VALUES("64","Contract Services");
INSERT INTO `fs_notes_classification` VALUES("65","Sugar & Molasses Handling");
INSERT INTO `fs_notes_classification` VALUES("66","Sugar Lien Expenses");
INSERT INTO `fs_notes_classification` VALUES("67","Interest & Bank Charges");
INSERT INTO `fs_notes_classification` VALUES("68","Provision for Impairment on Investment");
INSERT INTO `fs_notes_classification` VALUES("69","Current");
INSERT INTO `fs_notes_classification` VALUES("70","Miscellaneous Expenses");
INSERT INTO `fs_notes_classification` VALUES("71","Taxes & Licenses");
INSERT INTO `fs_notes_classification` VALUES("72","Security Services (Outside Services)");
INSERT INTO `fs_notes_classification` VALUES("73","Professional, Legal & Audit Fees");
INSERT INTO `fs_notes_classification` VALUES("74","Light & Power Expenses");
INSERT INTO `fs_notes_classification` VALUES("75","Freight & Handling");
INSERT INTO `fs_notes_classification` VALUES("76","Transportation & Travelling");
INSERT INTO `fs_notes_classification` VALUES("77","Recruitment, Trainings & Seminars");
INSERT INTO `fs_notes_classification` VALUES("78","Medical & Dental Supplies");
INSERT INTO `fs_notes_classification` VALUES("79","Recreations & Other Social Activities");
INSERT INTO `fs_notes_classification` VALUES("80","Membership/Condominium dues");
INSERT INTO `fs_notes_classification` VALUES("81","Ads, Donations & Promotions");
INSERT INTO `fs_notes_classification` VALUES("82","Depreciation Expenses");
INSERT INTO `fs_notes_classification` VALUES("83","Undeposited Funds");
INSERT INTO `fs_notes_classification` VALUES("84","Sales Discount");
INSERT INTO `fs_notes_classification` VALUES("85","Purchase Discount");

DROP TABLE IF EXISTS `general_journal`;
CREATE TABLE `general_journal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entry_no` varchar(50) DEFAULT NULL,
  `journal_date` date NOT NULL,
  `total_debit` decimal(15,2) NOT NULL,
  `total_credit` decimal(15,2) NOT NULL,
  `memo` text DEFAULT NULL,
  `location` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `print_status` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `general_journal_details`;
CREATE TABLE `general_journal_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `general_journal_id` int(11) NOT NULL,
  `cost_center_id` int(11) DEFAULT NULL,
  `account_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `debit` decimal(15,2) DEFAULT 0.00,
  `credit` decimal(15,2) DEFAULT 0.00,
  `memo` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `input_vat`;
CREATE TABLE `input_vat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `input_vat_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `input_vat_rate` int(11) NOT NULL,
  `input_vat_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `input_vat_account_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `input_vat` VALUES("1","N/A","0","N/A","18","2024-10-18 12:26:32");
INSERT INTO `input_vat` VALUES("2","V12","12","V12","18","2024-10-18 12:26:42");

DROP TABLE IF EXISTS `inventory`;
CREATE TABLE `inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `ref_no` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `name` varchar(100) NOT NULL,
  `item_id` int(11) NOT NULL,
  `qty_purchased` int(11) DEFAULT 0,
  `qty_sold` int(11) DEFAULT 0,
  `qty_on_hand` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `inventory_valuation`;
CREATE TABLE `inventory_valuation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `gross_margin` decimal(15,2) DEFAULT 0.00,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `inventory_valuation` VALUES("1","Invoice","1","SI000000001","2024-10-18","1","1","0.00","100.00","-100.00","0.00","0.00","0.00","0.00","0.00","0.00","0.00","0.00","0.00","0.00","100.00","10000.00","0.00","0.00","10000.00","12.00","1071.43","8928.57","89.29","0.00","0.00","0.00","0.00","0.00","0.00");

DROP TABLE IF EXISTS `items`;
CREATE TABLE `items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `items` VALUES("1","Monster Energy Drink","ITEM02214","Inventory",NULL,"13","0","46","250","Monster Energy Drink","Monster Energy Drink","100.00","95.00","14","15","4","2024-10-18 12:31:27");
INSERT INTO `items` VALUES("2","5","","",NULL,"0","0","44","0","","","0.00","0.00","0","0","0","2024-10-28 09:27:02");
INSERT INTO `items` VALUES("3","2","","",NULL,"18","0","44","0","","","0.00","0.00","0","0","0","2024-10-28 09:37:17");

DROP TABLE IF EXISTS `location`;
CREATE TABLE `location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `location` VALUES("4","Makati");
INSERT INTO `location` VALUES("5","Millsite");

DROP TABLE IF EXISTS `material_issuance`;
CREATE TABLE `material_issuance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mis_no` varchar(50) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0,
  `print_status` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `material_issuance_details`;
CREATE TABLE `material_issuance_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mis_id` int(11) DEFAULT 0,
  `item_id` int(11) DEFAULT 0,
  `quantity` int(11) DEFAULT 0,
  `cost` double(10,2) DEFAULT 0.00,
  `amount` double(10,2) DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `or_payment_details`;
CREATE TABLE `or_payment_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `asset_account_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `or_payments`;
CREATE TABLE `or_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `other_name`;
CREATE TABLE `other_name` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `other_name` varchar(255) NOT NULL,
  `other_name_code` varchar(255) NOT NULL,
  `account_number` varchar(255) NOT NULL,
  `other_name_address` varchar(255) NOT NULL,
  `contact_number` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `terms` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `payment_credit_details`;
CREATE TABLE `payment_credit_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_detail_id` int(11) NOT NULL,
  `credit_amount` decimal(15,2) DEFAULT NULL,
  `credit_no` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `payment_details`;
CREATE TABLE `payment_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `amount_applied` decimal(15,2) NOT NULL,
  `discount_amount` decimal(15,2) DEFAULT 0.00,
  `credit_amount` decimal(15,2) DEFAULT 0.00,
  `credit_no` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `discount_account_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `payment_details` VALUES("1","1","1","9910.71","0.00","0.00",NULL,"2024-11-04 05:03:01","2024-11-04 05:03:01",NULL);

DROP TABLE IF EXISTS `payment_method`;
CREATE TABLE `payment_method` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_method_name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `payment_method` VALUES("1","Cash","Cash");
INSERT INTO `payment_method` VALUES("2","Credit","Credit");
INSERT INTO `payment_method` VALUES("3","Check","Check");
INSERT INTO `payment_method` VALUES("4","Credit Card","Credit Card");
INSERT INTO `payment_method` VALUES("5","Direct Deposit","Direct Deposit");

DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `payments` VALUES("1","1","2024-11-04","1","20","14","CR000000001","124","9910.71","9910.71","0.00","0","1","2024-11-04 05:03:01","2024-11-04 05:03:02");

DROP TABLE IF EXISTS `purchase_order`;
CREATE TABLE `purchase_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `print_status` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `purchase_order` VALUES("1","PO000000001",NULL,"2024-10-18","2024-10-18","1","NET 7","1312500.00","0.00","1312500.00","140625.00","0.00","0.00","0.00","140625.00","124","4","1","1","2024-10-18 12:35:05","1");

DROP TABLE IF EXISTS `purchase_order_details`;
CREATE TABLE `purchase_order_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `last_ordered_qty` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `po_id` (`po_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `purchase_order_details` VALUES("1","1","PR000000001","1","2","250","5250.0000","1312500.0000","0","17","0.0000","1312500.0000","12","1171875.0000","18","140625.0000","2024-10-18 12:35:05","250","0","0");

DROP TABLE IF EXISTS `purchase_request`;
CREATE TABLE `purchase_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pr_no` varchar(50) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `required_date` date DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0,
  `print_status` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `purchase_request` VALUES("1","PR000000001","4","2024-10-18","2024-10-18","test","2","1","2024-10-18 12:33:49");
INSERT INTO `purchase_request` VALUES("2","PR000000002","4","2024-10-28","2024-10-28","124","0","1","2024-10-28 10:53:46");

DROP TABLE IF EXISTS `purchase_request_details`;
CREATE TABLE `purchase_request_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pr_id` int(11) DEFAULT 0,
  `item_id` int(11) DEFAULT 0,
  `cost_center_id` int(11) DEFAULT 0,
  `quantity` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `ordered_quantity` int(11) DEFAULT 0,
  `balance_quantity` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `purchase_request_details` VALUES("1","1","1","2","500","2024-10-18 12:33:49","250","250","2");
INSERT INTO `purchase_request_details` VALUES("2","2","1","2","800","2024-10-28 10:53:46","0","800","0");

DROP TABLE IF EXISTS `purchases`;
CREATE TABLE `purchases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `receive_item_details`;
CREATE TABLE `receive_item_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `last_received_qty` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `receive_item_details` VALUES("1","1","1","1","2","250","5250","1312500","0","0","1312500","1171875","12","140625","4687.5","2024-10-28 10:58:45","2024-10-28 10:58:45","0");

DROP TABLE IF EXISTS `receive_items`;
CREATE TABLE `receive_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `receive_items` VALUES("1","8","RR000000001","1","","","0000-00-00","0000-00-00","2024-10-28","2024","124124","1312500","0","1312500","140625","1171875","0","1","1","2024-10-28 10:58:44","2024-10-28 10:58:47");

DROP TABLE IF EXISTS `sales_invoice`;
CREATE TABLE `sales_invoice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `sales_invoice` VALUES("1","SI000000001","2024-10-18","3","2024-10-25","","","","1","Cash","4","NET 7","","10000.00","0.00","10000.00","1071.43","8928.57","0.00","0.00","1","89.29","9910.71","1","1","0","0","2","2024-10-18 12:32:32","2024-11-04 05:03:01");
INSERT INTO `sales_invoice` VALUES("2","SI000000002","2024-10-28","20","2024-11-04","","124","124","1","Cash","4","NET 7","124","124.00","2.48","121.52","0.00","121.52","0.00","0.00","0","0.00","121.52","0","1","121.52","0","2","2024-10-28 09:43:41","2024-10-28 09:43:58");
INSERT INTO `sales_invoice` VALUES("3","SI000000003","2024-10-28","3","2024-11-04","124","124","124","2","Credit","5","NET 7","","15376.00","768.80","14607.20","1565.06","13042.14","0.00","0.00","1","130.42","14476.78","0","1","14476.78","0","1","2024-10-28 09:44:58","2024-10-28 09:45:00");
INSERT INTO `sales_invoice` VALUES("4","SI000000004","2024-10-28","3","2024-11-04","","124","124","1","Cash","4","NET 7","124","5456.00","109.12","5346.88","0.00","5346.88","0.00","0.00","1","53.47","5293.41","0","1","5293.41","0","1","2024-10-28 09:46:15","2024-10-28 09:46:17");
INSERT INTO `sales_invoice` VALUES("5","SI000000005","2024-10-28","3","2024-11-04","","124","124","1","Cash","4","NET 7","124","700.00","0.00","700.00","75.00","625.00","0.00","0.00","1","6.25","693.75","0","1","693.75","0","1","2024-10-28 10:48:00","2024-10-28 10:48:02");
INSERT INTO `sales_invoice` VALUES("6","SI000000006","2024-10-28","3","2024-11-04","","124","124","1","Credit","4","NET 7","124","50000.00","1000.00","49000.00","5250.00","43750.00","0.00","0.00","1","437.50","48562.50","0","1","48562.5","0","1","2024-10-28 10:52:28","2024-10-28 10:52:30");
INSERT INTO `sales_invoice` VALUES("7","SI000000007","2024-10-28","3","2024-11-04","","124","124","1","Cash","5","NET 7","124","1500000.00","75000.00","1425000.00","152678.57","1272321.43","0.00","0.00","1","12723.21","1412276.79","0","1","1412276.79","0","1","2024-10-28 10:53:28","2024-10-28 10:53:30");
INSERT INTO `sales_invoice` VALUES("8","555125215","2024-10-28","3","2024-11-04","","125","125","1","Cash","4","NET 7","125","6875.00","137.50","6737.50","721.87","6015.63","0.00","0.00","1","60.16","6677.34","0","1","6677.34","0","1","2024-10-28 13:52:41","2024-10-28 13:52:43");
INSERT INTO `sales_invoice` VALUES("9",NULL,"2024-10-28","3","2024-11-04","124","124","124","1","Cash","4","NET 7","124","15376.00","0.00","15376.00","0.00","15376.00","0.00","0.00","1","153.76","15222.24","4","1","0","0","0","2024-10-28 13:53:27","2024-10-28 13:53:27");
INSERT INTO `sales_invoice` VALUES("10","124","2024-10-28","3","2024-11-04","124","124","124","1","Cash","4","NET 7","124","250000.00","0.00","250000.00","26785.71","223214.29","0.00","0.00","1","2232.14","247767.86","0","1","247767.86","0","1","2024-10-28 14:11:17","2024-10-28 14:11:19");
INSERT INTO `sales_invoice` VALUES("11","12455555","2024-10-28","3","2024-11-04","","124","124","5","Cash","4","NET 7","","15376.00","307.52","15068.48","1614.48","13454.00","0.00","0.00","1","134.54","14933.94","0","1","14933.94","0","1","2024-10-28 14:34:53","2024-10-28 14:34:55");
INSERT INTO `sales_invoice` VALUES("12","12422222","2024-10-28","3","2024-11-04","","124","124","7","Cash","4","NET 7","124","15376.00","0.00","15376.00","0.00","15376.00","0.00","0.00","1","153.76","15222.24","0","1","15222.24","0","1","2024-10-28 14:43:52","2024-10-28 14:43:54");
INSERT INTO `sales_invoice` VALUES("13","124QQ$@$!2","2024-10-28","3","2024-11-04","","124","124","8","Cash","4","NET 7","124","1488.00","0.00","1488.00","0.00","1488.00","0.00","0.00","1","14.88","1473.12","0","1","1473.12","0","1","2024-10-28 14:44:58","2024-10-28 14:45:00");
INSERT INTO `sales_invoice` VALUES("14","125","2024-10-29","3","2024-11-13","","124","124","1","Cash","4","NET 15","125","15625.00","0.00","15625.00","0.00","15625.00","0.00","0.00","1","156.25","15468.75","0","1","15468.75","0","1","2024-10-29 12:20:16","2024-10-29 12:20:18");
INSERT INTO `sales_invoice` VALUES("15","SI000000008","2024-11-04","3","2024-11-11","","124","124","1","Cash","4","NET 7","124","100.00","0.00","100.00","10.71","89.29","0.00","0.00","1","0.89","99.11","0","1","99.11","0","1","2024-11-04 04:58:41","2024-11-04 04:58:43");

DROP TABLE IF EXISTS `sales_invoice_details`;
CREATE TABLE `sales_invoice_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `asset_account_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `sales_invoice_details` VALUES("1","1","1","100","100.00","10000.00","0.00","0.00","10000.00","8928.57","12.00","1071.43","2024-10-18 12:32:32","2024-10-18 12:32:32","16","17","14","15","4");
INSERT INTO `sales_invoice_details` VALUES("2","2","2","1","124.00","124.00","2.00","2.48","121.52","121.52",NULL,"0.00","2024-10-28 09:43:41","2024-10-28 09:43:41",NULL,"17",NULL,NULL,NULL);
INSERT INTO `sales_invoice_details` VALUES("3","3","1","124","124.00","15376.00","5.00","768.80","14607.20","13042.14","12.00","1565.06","2024-10-28 09:44:58","2024-10-28 09:44:58","16","17","14","15","4");
INSERT INTO `sales_invoice_details` VALUES("4","4","2","44","124.00","5456.00","2.00","109.12","5346.88","5346.88","0.00","0.00","2024-10-28 09:46:15","2024-10-28 09:46:15","16","17",NULL,NULL,NULL);
INSERT INTO `sales_invoice_details` VALUES("5","5","1","50","14.00","700.00","0.00","0.00","700.00","625.00","12.00","75.00","2024-10-28 10:48:00","2024-10-28 10:48:00","16","17","14","15","4");
INSERT INTO `sales_invoice_details` VALUES("6","6","1","200","250.00","50000.00","2.00","1000.00","49000.00","43750.00","12.00","5250.00","2024-10-28 10:52:28","2024-10-28 10:52:28","16","17","14","15","4");
INSERT INTO `sales_invoice_details` VALUES("7","7","1","500","3000.00","1500000.00","5.00","75000.00","1425000.00","1272321.43","12.00","152678.57","2024-10-28 10:53:28","2024-10-28 10:53:28","16","17","14","15","4");
INSERT INTO `sales_invoice_details` VALUES("8","8","2","55","125.00","6875.00","2.00","137.50","6737.50","6015.63","12.00","721.87","2024-10-28 13:52:42","2024-10-28 13:52:42","16","17",NULL,NULL,NULL);
INSERT INTO `sales_invoice_details` VALUES("9","9","1","124","124.00","15376.00","0.00","0.00","15376.00","15376.00","0.00","0.00","2024-10-28 13:53:27","2024-10-28 13:53:27","16","17","14","15","4");
INSERT INTO `sales_invoice_details` VALUES("10","10","1","500","500.00","250000.00","0.00","0.00","250000.00","223214.29","12.00","26785.71","2024-10-28 14:11:17","2024-10-28 14:11:17","16","17","14","15","4");
INSERT INTO `sales_invoice_details` VALUES("11","11","2","124","124.00","15376.00","2.00","307.52","15068.48","13454.00","12.00","1614.48","2024-10-28 14:34:53","2024-10-28 14:34:53","16","17",NULL,NULL,NULL);
INSERT INTO `sales_invoice_details` VALUES("12","12","1","124","124.00","15376.00","0.00","0.00","15376.00","15376.00","0.00","0.00","2024-10-28 14:43:52","2024-10-28 14:43:52","16","17","14","15","4");
INSERT INTO `sales_invoice_details` VALUES("13","13","1","124","12.00","1488.00","0.00","0.00","1488.00","1488.00","0.00","0.00","2024-10-28 14:44:58","2024-10-28 14:44:58","16","17","14","15","4");
INSERT INTO `sales_invoice_details` VALUES("14","14","2","125","125.00","15625.00","0.00","0.00","15625.00","15625.00","0.00","0.00","2024-10-29 12:20:16","2024-10-29 12:20:16","16","17",NULL,NULL,NULL);
INSERT INTO `sales_invoice_details` VALUES("15","15","2","1","100.00","100.00","0.00","0.00","100.00","89.29","12.00","10.71","2024-11-04 04:58:41","2024-11-04 04:58:41","16","17",NULL,NULL,NULL);

DROP TABLE IF EXISTS `sales_return`;
CREATE TABLE `sales_return` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `sales_return_details`;
CREATE TABLE `sales_return_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `sales_tax`;
CREATE TABLE `sales_tax` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sales_tax_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `sales_tax_rate` float NOT NULL,
  `sales_tax_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sales_tax_account_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sales_tax` VALUES("1","N/A","0","N/A","16","2024-10-18 12:27:00");
INSERT INTO `sales_tax` VALUES("2","12%","12","12%","16","2024-10-18 12:27:15");
INSERT INTO `sales_tax` VALUES("3","E","0","E","16","2024-10-18 12:27:24");
INSERT INTO `sales_tax` VALUES("4","Z","0","Z","16","2024-10-18 12:27:37");
INSERT INTO `sales_tax` VALUES("5","NV","0","NV","16","2024-10-18 12:27:46");

DROP TABLE IF EXISTS `terms`;
CREATE TABLE `terms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `term_name` varchar(50) NOT NULL,
  `term_days_due` int(11) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `terms` VALUES("5","NET 7","7","NET 7");
INSERT INTO `terms` VALUES("6","NET 15","15","NET 15");
INSERT INTO `terms` VALUES("7","NET 30","30","NET 30");
INSERT INTO `terms` VALUES("9","Due on Receipt","0","Due on Receipt");

DROP TABLE IF EXISTS `transaction_entries`;
CREATE TABLE `transaction_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `transaction_entries` VALUES("1","1","Invoice","2024-10-18","SI000000001","0","Lloyd Golez",NULL,NULL,"3","10000.00","0.00","10000.00","2024-10-18 12:32:32");
INSERT INTO `transaction_entries` VALUES("2","1","Invoice","2024-10-18","SI000000001","0","Lloyd Golez",NULL,NULL,"11","89.29","0.00","89.29","2024-10-18 12:32:32");
INSERT INTO `transaction_entries` VALUES("3","1","Invoice","2024-10-18","SI000000001","0","Lloyd Golez",NULL,NULL,"16","0.00","1071.43","-1071.43","2024-10-18 12:32:32");
INSERT INTO `transaction_entries` VALUES("4","1","Invoice","2024-10-18","SI000000001","0","Lloyd Golez","Monster Energy Drink","100","17","0.00","0.00","0.00","2024-10-18 12:32:32");
INSERT INTO `transaction_entries` VALUES("5","1","Invoice","2024-10-18","SI000000001","0","Lloyd Golez","Monster Energy Drink","100","15","0.00","8928.57","-8928.57","2024-10-18 12:32:32");
INSERT INTO `transaction_entries` VALUES("6","1","Invoice","2024-10-18","SI000000001","0","Lloyd Golez","Monster Energy Drink","100","14","10000.00","0.00","10000.00","2024-10-18 12:32:32");
INSERT INTO `transaction_entries` VALUES("7","1","Invoice","2024-10-18","SI000000001","0","Lloyd Golez","Monster Energy Drink","100","4","0.00","10000.00","-10000.00","2024-10-18 12:32:32");
INSERT INTO `transaction_entries` VALUES("8","1","Invoice","2024-10-18","SI000000001","0","Lloyd Golez",NULL,NULL,"3","0.00","0.00","0.00","2024-10-18 12:32:32");
INSERT INTO `transaction_entries` VALUES("9","1","Invoice","2024-10-18","SI000000001","0","Lloyd Golez",NULL,NULL,"3","0.00","89.29","-89.29","2024-10-18 12:32:32");
INSERT INTO `transaction_entries` VALUES("10","2","Invoice","2024-10-28","SI000000002","0","Lloyd Golez",NULL,NULL,"20","124.00","0.00","124.00","2024-10-28 09:43:41");
INSERT INTO `transaction_entries` VALUES("11","2","Invoice","2024-10-28","SI000000002","0","Lloyd Golez",NULL,NULL,"0","0.00","0.00","0.00","2024-10-28 09:43:41");
INSERT INTO `transaction_entries` VALUES("12","2","Invoice","2024-10-28","SI000000002","0","Lloyd Golez",NULL,NULL,"0","0.00","0.00","0.00","2024-10-28 09:43:41");
INSERT INTO `transaction_entries` VALUES("13","2","Invoice","2024-10-28","SI000000002","0","Lloyd Golez","5","1","17","2.48","0.00","2.48","2024-10-28 09:43:41");
INSERT INTO `transaction_entries` VALUES("14","2","Invoice","2024-10-28","SI000000002","0","Lloyd Golez",NULL,NULL,"20","0.00","2.48","-2.48","2024-10-28 09:43:41");
INSERT INTO `transaction_entries` VALUES("15","2","Invoice","2024-10-28","SI000000002","0","Lloyd Golez",NULL,NULL,"20","0.00","0.00","0.00","2024-10-28 09:43:41");
INSERT INTO `transaction_entries` VALUES("16","3","Invoice","2024-10-28","SI000000003","0","124",NULL,NULL,"3","15376.00","0.00","15376.00","2024-10-28 09:44:58");
INSERT INTO `transaction_entries` VALUES("17","3","Invoice","2024-10-28","SI000000003","0","124",NULL,NULL,"11","130.42","0.00","130.42","2024-10-28 09:44:58");
INSERT INTO `transaction_entries` VALUES("18","3","Invoice","2024-10-28","SI000000003","0","124",NULL,NULL,"16","0.00","1565.06","-1565.06","2024-10-28 09:44:58");
INSERT INTO `transaction_entries` VALUES("19","3","Invoice","2024-10-28","SI000000003","0","124","Monster Energy Drink","124","17","768.80","0.00","768.80","2024-10-28 09:44:58");
INSERT INTO `transaction_entries` VALUES("20","3","Invoice","2024-10-28","SI000000003","0","124","Monster Energy Drink","124","15","0.00","13810.94","-13810.94","2024-10-28 09:44:58");
INSERT INTO `transaction_entries` VALUES("21","3","Invoice","2024-10-28","SI000000003","0","124","Monster Energy Drink","124","14","15376.00","0.00","15376.00","2024-10-28 09:44:58");
INSERT INTO `transaction_entries` VALUES("22","3","Invoice","2024-10-28","SI000000003","0","124","Monster Energy Drink","124","4","0.00","15376.00","-15376.00","2024-10-28 09:44:58");
INSERT INTO `transaction_entries` VALUES("23","3","Invoice","2024-10-28","SI000000003","0","124",NULL,NULL,"3","0.00","768.80","-768.80","2024-10-28 09:44:58");
INSERT INTO `transaction_entries` VALUES("24","3","Invoice","2024-10-28","SI000000003","0","124",NULL,NULL,"3","0.00","130.42","-130.42","2024-10-28 09:44:58");
INSERT INTO `transaction_entries` VALUES("25","4","Invoice","2024-10-28","SI000000004","0","Lloyd Golez",NULL,NULL,"3","5456.00","0.00","5456.00","2024-10-28 09:46:15");
INSERT INTO `transaction_entries` VALUES("26","4","Invoice","2024-10-28","SI000000004","0","Lloyd Golez",NULL,NULL,"11","53.47","0.00","53.47","2024-10-28 09:46:15");
INSERT INTO `transaction_entries` VALUES("27","4","Invoice","2024-10-28","SI000000004","0","Lloyd Golez",NULL,NULL,"16","0.00","0.00","0.00","2024-10-28 09:46:15");
INSERT INTO `transaction_entries` VALUES("28","4","Invoice","2024-10-28","SI000000004","0","Lloyd Golez","5","44","17","109.12","0.00","109.12","2024-10-28 09:46:15");
INSERT INTO `transaction_entries` VALUES("29","4","Invoice","2024-10-28","SI000000004","0","Lloyd Golez",NULL,NULL,"3","0.00","109.12","-109.12","2024-10-28 09:46:15");
INSERT INTO `transaction_entries` VALUES("30","4","Invoice","2024-10-28","SI000000004","0","Lloyd Golez",NULL,NULL,"3","0.00","53.47","-53.47","2024-10-28 09:46:15");
INSERT INTO `transaction_entries` VALUES("31","5","Invoice","2024-10-28","SI000000005","0","Lloyd Golez",NULL,NULL,"3","700.00","0.00","700.00","2024-10-28 10:48:00");
INSERT INTO `transaction_entries` VALUES("32","5","Invoice","2024-10-28","SI000000005","0","Lloyd Golez",NULL,NULL,"11","6.25","0.00","6.25","2024-10-28 10:48:00");
INSERT INTO `transaction_entries` VALUES("33","5","Invoice","2024-10-28","SI000000005","0","Lloyd Golez",NULL,NULL,"16","0.00","75.00","-75.00","2024-10-28 10:48:00");
INSERT INTO `transaction_entries` VALUES("34","5","Invoice","2024-10-28","SI000000005","0","Lloyd Golez","Monster Energy Drink","50","17","0.00","0.00","0.00","2024-10-28 10:48:00");
INSERT INTO `transaction_entries` VALUES("35","5","Invoice","2024-10-28","SI000000005","0","Lloyd Golez","Monster Energy Drink","50","15","0.00","625.00","-625.00","2024-10-28 10:48:00");
INSERT INTO `transaction_entries` VALUES("36","5","Invoice","2024-10-28","SI000000005","0","Lloyd Golez","Monster Energy Drink","50","14","700.00","0.00","700.00","2024-10-28 10:48:00");
INSERT INTO `transaction_entries` VALUES("37","5","Invoice","2024-10-28","SI000000005","0","Lloyd Golez","Monster Energy Drink","50","4","0.00","700.00","-700.00","2024-10-28 10:48:00");
INSERT INTO `transaction_entries` VALUES("38","5","Invoice","2024-10-28","SI000000005","0","Lloyd Golez",NULL,NULL,"3","0.00","0.00","0.00","2024-10-28 10:48:00");
INSERT INTO `transaction_entries` VALUES("39","5","Invoice","2024-10-28","SI000000005","0","Lloyd Golez",NULL,NULL,"3","0.00","6.25","-6.25","2024-10-28 10:48:00");
INSERT INTO `transaction_entries` VALUES("40","6","Invoice","2024-10-28","SI000000006","0","Lloyd Golez",NULL,NULL,"3","50000.00","0.00","50000.00","2024-10-28 10:52:28");
INSERT INTO `transaction_entries` VALUES("41","6","Invoice","2024-10-28","SI000000006","0","Lloyd Golez",NULL,NULL,"11","437.50","0.00","437.50","2024-10-28 10:52:28");
INSERT INTO `transaction_entries` VALUES("42","6","Invoice","2024-10-28","SI000000006","0","Lloyd Golez",NULL,NULL,"16","0.00","5250.00","-5250.00","2024-10-28 10:52:28");
INSERT INTO `transaction_entries` VALUES("43","6","Invoice","2024-10-28","SI000000006","0","Lloyd Golez","Monster Energy Drink","200","17","1000.00","0.00","1000.00","2024-10-28 10:52:28");
INSERT INTO `transaction_entries` VALUES("44","6","Invoice","2024-10-28","SI000000006","0","Lloyd Golez","Monster Energy Drink","200","15","0.00","44750.00","-44750.00","2024-10-28 10:52:28");
INSERT INTO `transaction_entries` VALUES("45","6","Invoice","2024-10-28","SI000000006","0","Lloyd Golez","Monster Energy Drink","200","14","50000.00","0.00","50000.00","2024-10-28 10:52:28");
INSERT INTO `transaction_entries` VALUES("46","6","Invoice","2024-10-28","SI000000006","0","Lloyd Golez","Monster Energy Drink","200","4","0.00","50000.00","-50000.00","2024-10-28 10:52:28");
INSERT INTO `transaction_entries` VALUES("47","6","Invoice","2024-10-28","SI000000006","0","Lloyd Golez",NULL,NULL,"3","0.00","1000.00","-1000.00","2024-10-28 10:52:28");
INSERT INTO `transaction_entries` VALUES("48","6","Invoice","2024-10-28","SI000000006","0","Lloyd Golez",NULL,NULL,"3","0.00","437.50","-437.50","2024-10-28 10:52:28");
INSERT INTO `transaction_entries` VALUES("49","7","Invoice","2024-10-28","SI000000007","0","Lloyd Golez",NULL,NULL,"3","1500000.00","0.00","1500000.00","2024-10-28 10:53:28");
INSERT INTO `transaction_entries` VALUES("50","7","Invoice","2024-10-28","SI000000007","0","Lloyd Golez",NULL,NULL,"11","12723.21","0.00","12723.21","2024-10-28 10:53:28");
INSERT INTO `transaction_entries` VALUES("51","7","Invoice","2024-10-28","SI000000007","0","Lloyd Golez",NULL,NULL,"16","0.00","152678.57","-152678.57","2024-10-28 10:53:28");
INSERT INTO `transaction_entries` VALUES("52","7","Invoice","2024-10-28","SI000000007","0","Lloyd Golez","Monster Energy Drink","500","17","75000.00","0.00","75000.00","2024-10-28 10:53:28");
INSERT INTO `transaction_entries` VALUES("53","7","Invoice","2024-10-28","SI000000007","0","Lloyd Golez","Monster Energy Drink","500","15","0.00","1347321.43","-1347321.43","2024-10-28 10:53:28");
INSERT INTO `transaction_entries` VALUES("54","7","Invoice","2024-10-28","SI000000007","0","Lloyd Golez","Monster Energy Drink","500","14","1500000.00","0.00","1500000.00","2024-10-28 10:53:28");
INSERT INTO `transaction_entries` VALUES("55","7","Invoice","2024-10-28","SI000000007","0","Lloyd Golez","Monster Energy Drink","500","4","0.00","1500000.00","-1500000.00","2024-10-28 10:53:28");
INSERT INTO `transaction_entries` VALUES("56","7","Invoice","2024-10-28","SI000000007","0","Lloyd Golez",NULL,NULL,"3","0.00","75000.00","-75000.00","2024-10-28 10:53:28");
INSERT INTO `transaction_entries` VALUES("57","7","Invoice","2024-10-28","SI000000007","0","Lloyd Golez",NULL,NULL,"3","0.00","12723.21","-12723.21","2024-10-28 10:53:28");
INSERT INTO `transaction_entries` VALUES("58","8","Invoice","2024-10-28","555125215","0","Lloyd Golez",NULL,NULL,"3","6875.00","0.00","6875.00","2024-10-28 13:52:41");
INSERT INTO `transaction_entries` VALUES("59","8","Invoice","2024-10-28","555125215","0","Lloyd Golez",NULL,NULL,"11","60.16","0.00","60.16","2024-10-28 13:52:42");
INSERT INTO `transaction_entries` VALUES("60","8","Invoice","2024-10-28","555125215","0","Lloyd Golez",NULL,NULL,"16","0.00","721.87","-721.87","2024-10-28 13:52:42");
INSERT INTO `transaction_entries` VALUES("61","8","Invoice","2024-10-28","555125215","0","Lloyd Golez","5","55","17","137.50","0.00","137.50","2024-10-28 13:52:42");
INSERT INTO `transaction_entries` VALUES("62","8","Invoice","2024-10-28","555125215","0","Lloyd Golez",NULL,NULL,"3","0.00","137.50","-137.50","2024-10-28 13:52:42");
INSERT INTO `transaction_entries` VALUES("63","8","Invoice","2024-10-28","555125215","0","Lloyd Golez",NULL,NULL,"3","0.00","60.16","-60.16","2024-10-28 13:52:42");
INSERT INTO `transaction_entries` VALUES("64","10","Invoice","2024-10-28","124","0","Lloyd Golez",NULL,NULL,"3","250000.00","0.00","250000.00","2024-10-28 14:11:17");
INSERT INTO `transaction_entries` VALUES("65","10","Invoice","2024-10-28","124","0","Lloyd Golez",NULL,NULL,"11","2232.14","0.00","2232.14","2024-10-28 14:11:17");
INSERT INTO `transaction_entries` VALUES("66","10","Invoice","2024-10-28","124","0","Lloyd Golez",NULL,NULL,"16","0.00","26785.71","-26785.71","2024-10-28 14:11:17");
INSERT INTO `transaction_entries` VALUES("67","10","Invoice","2024-10-28","124","0","Lloyd Golez","Monster Energy Drink","500","17","0.00","0.00","0.00","2024-10-28 14:11:17");
INSERT INTO `transaction_entries` VALUES("68","10","Invoice","2024-10-28","124","0","Lloyd Golez","Monster Energy Drink","500","15","0.00","223214.29","-223214.29","2024-10-28 14:11:17");
INSERT INTO `transaction_entries` VALUES("69","10","Invoice","2024-10-28","124","0","Lloyd Golez","Monster Energy Drink","500","14","250000.00","0.00","250000.00","2024-10-28 14:11:17");
INSERT INTO `transaction_entries` VALUES("70","10","Invoice","2024-10-28","124","0","Lloyd Golez","Monster Energy Drink","500","4","0.00","250000.00","-250000.00","2024-10-28 14:11:17");
INSERT INTO `transaction_entries` VALUES("71","10","Invoice","2024-10-28","124","0","Lloyd Golez",NULL,NULL,"3","0.00","0.00","0.00","2024-10-28 14:11:17");
INSERT INTO `transaction_entries` VALUES("72","10","Invoice","2024-10-28","124","0","Lloyd Golez",NULL,NULL,"3","0.00","2232.14","-2232.14","2024-10-28 14:11:17");
INSERT INTO `transaction_entries` VALUES("73","11","Invoice","2024-10-28","12455555","0","gegege",NULL,NULL,"3","15376.00","0.00","15376.00","2024-10-28 14:34:53");
INSERT INTO `transaction_entries` VALUES("74","11","Invoice","2024-10-28","12455555","0","gegege",NULL,NULL,"11","134.54","0.00","134.54","2024-10-28 14:34:53");
INSERT INTO `transaction_entries` VALUES("75","11","Invoice","2024-10-28","12455555","0","gegege",NULL,NULL,"16","0.00","1614.48","-1614.48","2024-10-28 14:34:53");
INSERT INTO `transaction_entries` VALUES("76","11","Invoice","2024-10-28","12455555","0","gegege","5","124","17","307.52","0.00","307.52","2024-10-28 14:34:53");
INSERT INTO `transaction_entries` VALUES("77","11","Invoice","2024-10-28","12455555","0","gegege",NULL,NULL,"3","0.00","307.52","-307.52","2024-10-28 14:34:53");
INSERT INTO `transaction_entries` VALUES("78","11","Invoice","2024-10-28","12455555","0","gegege",NULL,NULL,"3","0.00","134.54","-134.54","2024-10-28 14:34:53");
INSERT INTO `transaction_entries` VALUES("79","12","Invoice","2024-10-28","12422222","0","trtrt",NULL,NULL,"3","15376.00","0.00","15376.00","2024-10-28 14:43:52");
INSERT INTO `transaction_entries` VALUES("80","12","Invoice","2024-10-28","12422222","0","trtrt",NULL,NULL,"11","153.76","0.00","153.76","2024-10-28 14:43:52");
INSERT INTO `transaction_entries` VALUES("81","12","Invoice","2024-10-28","12422222","0","trtrt",NULL,NULL,"16","0.00","0.00","0.00","2024-10-28 14:43:52");
INSERT INTO `transaction_entries` VALUES("82","12","Invoice","2024-10-28","12422222","0","trtrt","Monster Energy Drink","124","17","0.00","0.00","0.00","2024-10-28 14:43:52");
INSERT INTO `transaction_entries` VALUES("83","12","Invoice","2024-10-28","12422222","0","trtrt","Monster Energy Drink","124","15","0.00","15376.00","-15376.00","2024-10-28 14:43:52");
INSERT INTO `transaction_entries` VALUES("84","12","Invoice","2024-10-28","12422222","0","trtrt","Monster Energy Drink","124","14","15376.00","0.00","15376.00","2024-10-28 14:43:52");
INSERT INTO `transaction_entries` VALUES("85","12","Invoice","2024-10-28","12422222","0","trtrt","Monster Energy Drink","124","4","0.00","15376.00","-15376.00","2024-10-28 14:43:52");
INSERT INTO `transaction_entries` VALUES("86","12","Invoice","2024-10-28","12422222","0","trtrt",NULL,NULL,"3","0.00","0.00","0.00","2024-10-28 14:43:52");
INSERT INTO `transaction_entries` VALUES("87","12","Invoice","2024-10-28","12422222","0","trtrt",NULL,NULL,"3","0.00","153.76","-153.76","2024-10-28 14:43:52");
INSERT INTO `transaction_entries` VALUES("88","13","Invoice","2024-10-28","124QQ$@$!2","0","qweqweqwe",NULL,NULL,"3","1488.00","0.00","1488.00","2024-10-28 14:44:58");
INSERT INTO `transaction_entries` VALUES("89","13","Invoice","2024-10-28","124QQ$@$!2","0","qweqweqwe",NULL,NULL,"11","14.88","0.00","14.88","2024-10-28 14:44:58");
INSERT INTO `transaction_entries` VALUES("90","13","Invoice","2024-10-28","124QQ$@$!2","0","qweqweqwe",NULL,NULL,"16","0.00","0.00","0.00","2024-10-28 14:44:58");
INSERT INTO `transaction_entries` VALUES("91","13","Invoice","2024-10-28","124QQ$@$!2","0","qweqweqwe","Monster Energy Drink","124","17","0.00","0.00","0.00","2024-10-28 14:44:58");
INSERT INTO `transaction_entries` VALUES("92","13","Invoice","2024-10-28","124QQ$@$!2","0","qweqweqwe","Monster Energy Drink","124","15","0.00","1488.00","-1488.00","2024-10-28 14:44:58");
INSERT INTO `transaction_entries` VALUES("93","13","Invoice","2024-10-28","124QQ$@$!2","0","qweqweqwe","Monster Energy Drink","124","14","1488.00","0.00","1488.00","2024-10-28 14:44:58");
INSERT INTO `transaction_entries` VALUES("94","13","Invoice","2024-10-28","124QQ$@$!2","0","qweqweqwe","Monster Energy Drink","124","4","0.00","1488.00","-1488.00","2024-10-28 14:44:58");
INSERT INTO `transaction_entries` VALUES("95","13","Invoice","2024-10-28","124QQ$@$!2","0","qweqweqwe",NULL,NULL,"3","0.00","0.00","0.00","2024-10-28 14:44:58");
INSERT INTO `transaction_entries` VALUES("96","13","Invoice","2024-10-28","124QQ$@$!2","0","qweqweqwe",NULL,NULL,"3","0.00","14.88","-14.88","2024-10-28 14:44:58");
INSERT INTO `transaction_entries` VALUES("97","14","Invoice","2024-10-29","125","0","214555",NULL,NULL,"3","15625.00","0.00","15625.00","2024-10-29 12:20:16");
INSERT INTO `transaction_entries` VALUES("98","14","Invoice","2024-10-29","125","0","214555",NULL,NULL,"11","156.25","0.00","156.25","2024-10-29 12:20:16");
INSERT INTO `transaction_entries` VALUES("99","14","Invoice","2024-10-29","125","0","214555",NULL,NULL,"16","0.00","0.00","0.00","2024-10-29 12:20:16");
INSERT INTO `transaction_entries` VALUES("100","14","Invoice","2024-10-29","125","0","214555","5","125","17","0.00","0.00","0.00","2024-10-29 12:20:16");
INSERT INTO `transaction_entries` VALUES("101","14","Invoice","2024-10-29","125","0","214555",NULL,NULL,"3","0.00","0.00","0.00","2024-10-29 12:20:16");
INSERT INTO `transaction_entries` VALUES("102","14","Invoice","2024-10-29","125","0","214555",NULL,NULL,"3","0.00","156.25","-156.25","2024-10-29 12:20:16");
INSERT INTO `transaction_entries` VALUES("103","1","Check Expense","2024-10-29","CV000000001","0","Lloyd Golez",NULL,NULL,"2","110.98","0.00","110.98","2024-10-29 14:59:13");
INSERT INTO `transaction_entries` VALUES("104","1","Check Expense","2024-10-29","CV000000001","0","Lloyd Golez",NULL,NULL,"17","0.00","2.48","-2.48","2024-10-29 14:59:13");
INSERT INTO `transaction_entries` VALUES("105","1","Check Expense","2024-10-29","CV000000001","0","Lloyd Golez",NULL,NULL,"18","13.02","0.00","13.02","2024-10-29 14:59:13");
INSERT INTO `transaction_entries` VALUES("106","1","Check Expense","2024-10-29","CV000000001","0","Lloyd Golez",NULL,NULL,"11","0.00","1.09","-1.09","2024-10-29 14:59:13");
INSERT INTO `transaction_entries` VALUES("107","1","Check Expense","2024-10-29","CV000000001","0","Lloyd Golez",NULL,NULL,"1","0.00","120.44","-120.44","2024-10-29 14:59:13");
INSERT INTO `transaction_entries` VALUES("108","15","Invoice","2024-11-04","SI000000008","0","Lloyd Golez",NULL,NULL,"3","100.00","0.00","100.00","2024-11-04 04:58:41");
INSERT INTO `transaction_entries` VALUES("109","15","Invoice","2024-11-04","SI000000008","0","Lloyd Golez",NULL,NULL,"11","0.89","0.00","0.89","2024-11-04 04:58:41");
INSERT INTO `transaction_entries` VALUES("110","15","Invoice","2024-11-04","SI000000008","0","Lloyd Golez",NULL,NULL,"16","0.00","10.71","-10.71","2024-11-04 04:58:41");
INSERT INTO `transaction_entries` VALUES("111","15","Invoice","2024-11-04","SI000000008","0","Lloyd Golez","5","1","17","0.00","0.00","0.00","2024-11-04 04:58:41");
INSERT INTO `transaction_entries` VALUES("112","15","Invoice","2024-11-04","SI000000008","0","Lloyd Golez",NULL,NULL,"3","0.00","0.00","0.00","2024-11-04 04:58:41");
INSERT INTO `transaction_entries` VALUES("113","15","Invoice","2024-11-04","SI000000008","0","Lloyd Golez",NULL,NULL,"3","0.00","0.89","-0.89","2024-11-04 04:58:41");
INSERT INTO `transaction_entries` VALUES("114","1","Payment","2024-11-04","CR000000001","0","Lloyd Golez",NULL,NULL,"20","9910.71","0.00","9910.71","2024-11-04 05:03:01");
INSERT INTO `transaction_entries` VALUES("115","1","Payment","2024-11-04","CR000000001","0","Lloyd Golez",NULL,NULL,"3","0.00","9910.71","-9910.71","2024-11-04 05:03:01");
INSERT INTO `transaction_entries` VALUES("116","2","Check Expense","2024-11-04","CV000000002","0","124",NULL,NULL,"1","89.29","0.00","89.29","2024-11-04 05:25:42");
INSERT INTO `transaction_entries` VALUES("117","2","Check Expense","2024-11-04","CV000000002","0","124",NULL,NULL,"17","0.00","0.00","0.00","2024-11-04 05:25:42");
INSERT INTO `transaction_entries` VALUES("118","2","Check Expense","2024-11-04","CV000000002","0","124",NULL,NULL,"18","10.71","0.00","10.71","2024-11-04 05:25:42");
INSERT INTO `transaction_entries` VALUES("119","2","Check Expense","2024-11-04","CV000000002","0","124",NULL,NULL,"11","0.00","0.89","-0.89","2024-11-04 05:25:42");
INSERT INTO `transaction_entries` VALUES("120","2","Check Expense","2024-11-04","CV000000002","0","124",NULL,NULL,"2","0.00","99.11","-99.11","2024-11-04 05:25:42");
INSERT INTO `transaction_entries` VALUES("121","3","Check Expense","2024-11-04","CV000000003","0","Lloyd Golez",NULL,NULL,"1","446.43","0.00","446.43","2024-11-04 05:27:07");
INSERT INTO `transaction_entries` VALUES("122","3","Check Expense","2024-11-04","CV000000003","0","Lloyd Golez",NULL,NULL,"17","0.00","0.00","0.00","2024-11-04 05:27:07");
INSERT INTO `transaction_entries` VALUES("123","3","Check Expense","2024-11-04","CV000000003","0","Lloyd Golez",NULL,NULL,"18","53.57","0.00","53.57","2024-11-04 05:27:07");
INSERT INTO `transaction_entries` VALUES("124","3","Check Expense","2024-11-04","CV000000003","0","Lloyd Golez",NULL,NULL,"11","0.00","4.46","-4.46","2024-11-04 05:27:07");
INSERT INTO `transaction_entries` VALUES("125","3","Check Expense","2024-11-04","CV000000003","0","Lloyd Golez",NULL,NULL,"2","0.00","495.54","-495.54","2024-11-04 05:27:07");
INSERT INTO `transaction_entries` VALUES("126","4","Check Expense","2024-11-04","CV000000004","0","2",NULL,NULL,"1","110.71","0.00","110.71","2024-11-04 05:41:03");
INSERT INTO `transaction_entries` VALUES("127","4","Check Expense","2024-11-04","CV000000004","0","2",NULL,NULL,"17","0.00","0.00","0.00","2024-11-04 05:41:03");
INSERT INTO `transaction_entries` VALUES("128","4","Check Expense","2024-11-04","CV000000004","0","2",NULL,NULL,"18","13.29","0.00","13.29","2024-11-04 05:41:03");
INSERT INTO `transaction_entries` VALUES("129","4","Check Expense","2024-11-04","CV000000004","0","2",NULL,NULL,"10","0.00","2.21","-2.21","2024-11-04 05:41:03");
INSERT INTO `transaction_entries` VALUES("130","4","Check Expense","2024-11-04","CV000000004","0","2",NULL,NULL,"2","0.00","121.79","-121.79","2024-11-04 05:41:03");

DROP TABLE IF EXISTS `uom`;
CREATE TABLE `uom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `uom` VALUES("7","kg/s");
INSERT INTO `uom` VALUES("11","pack");
INSERT INTO `uom` VALUES("12","cup/s");
INSERT INTO `uom` VALUES("13","pc/s");
INSERT INTO `uom` VALUES("14","sack/s");
INSERT INTO `uom` VALUES("18","gram/s");
INSERT INTO `uom` VALUES("19","lot/s");
INSERT INTO `uom` VALUES("23","mt/s");

DROP TABLE IF EXISTS `user_module_access`;
CREATE TABLE `user_module_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `module` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=137 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `user_module_access` VALUES("71","31","dashboard");
INSERT INTO `user_module_access` VALUES("72","31","chart_of_accounts_list");
INSERT INTO `user_module_access` VALUES("73","31","general_journal");
INSERT INTO `user_module_access` VALUES("74","31","transaction_entries");
INSERT INTO `user_module_access` VALUES("75","31","trial_balance");
INSERT INTO `user_module_access` VALUES("76","31","audit_trail");
INSERT INTO `user_module_access` VALUES("77","32","dashboard");
INSERT INTO `user_module_access` VALUES("78","32","invoice");
INSERT INTO `user_module_access` VALUES("79","32","reports");
INSERT INTO `user_module_access` VALUES("80","33","dashboard");
INSERT INTO `user_module_access` VALUES("81","33","chart_of_accounts_list");
INSERT INTO `user_module_access` VALUES("82","33","general_journal");
INSERT INTO `user_module_access` VALUES("83","33","transaction_entries");
INSERT INTO `user_module_access` VALUES("84","33","trial_balance");
INSERT INTO `user_module_access` VALUES("85","33","audit_trail");
INSERT INTO `user_module_access` VALUES("86","34","dashboard");
INSERT INTO `user_module_access` VALUES("87","34","purchase_order");
INSERT INTO `user_module_access` VALUES("88","34","bank_transfer");
INSERT INTO `user_module_access` VALUES("89","34","vendor_list");
INSERT INTO `user_module_access` VALUES("90","34","category");
INSERT INTO `user_module_access` VALUES("91","34","payment_method");
INSERT INTO `user_module_access` VALUES("92","35","dashboard");
INSERT INTO `user_module_access` VALUES("93","35","purchasing_purchase_request");
INSERT INTO `user_module_access` VALUES("94","35","purchasing_purchase_order");
INSERT INTO `user_module_access` VALUES("95","36","dashboard");
INSERT INTO `user_module_access` VALUES("96","36","reports");
INSERT INTO `user_module_access` VALUES("97","36","invoice");
INSERT INTO `user_module_access` VALUES("98","36","receive_payment");
INSERT INTO `user_module_access` VALUES("99","36","credit_memo");
INSERT INTO `user_module_access` VALUES("100","36","purchase_request");
INSERT INTO `user_module_access` VALUES("101","36","purchase_order");
INSERT INTO `user_module_access` VALUES("102","36","receive_items");
INSERT INTO `user_module_access` VALUES("103","36","accounts_payable_voucher");
INSERT INTO `user_module_access` VALUES("104","36","purchase_return");
INSERT INTO `user_module_access` VALUES("105","36","pay_bills");
INSERT INTO `user_module_access` VALUES("106","36","write_check");
INSERT INTO `user_module_access` VALUES("107","36","make_deposit");
INSERT INTO `user_module_access` VALUES("108","36","bank_transfer");
INSERT INTO `user_module_access` VALUES("109","36","chart_of_accounts_list");
INSERT INTO `user_module_access` VALUES("110","36","general_journal");
INSERT INTO `user_module_access` VALUES("111","36","transaction_entries");
INSERT INTO `user_module_access` VALUES("112","36","trial_balance");
INSERT INTO `user_module_access` VALUES("113","36","audit_trail");
INSERT INTO `user_module_access` VALUES("114","36","chart_of_accounts");
INSERT INTO `user_module_access` VALUES("115","36","item_list");
INSERT INTO `user_module_access` VALUES("116","36","customer");
INSERT INTO `user_module_access` VALUES("117","36","vendor_list");
INSERT INTO `user_module_access` VALUES("118","36","employee_list");
INSERT INTO `user_module_access` VALUES("119","36","other_name");
INSERT INTO `user_module_access` VALUES("120","36","location");
INSERT INTO `user_module_access` VALUES("121","36","uom");
INSERT INTO `user_module_access` VALUES("122","36","cost_center");
INSERT INTO `user_module_access` VALUES("123","36","category");
INSERT INTO `user_module_access` VALUES("124","36","terms");
INSERT INTO `user_module_access` VALUES("125","36","payment_method");
INSERT INTO `user_module_access` VALUES("126","36","discount");
INSERT INTO `user_module_access` VALUES("127","36","input_vat");
INSERT INTO `user_module_access` VALUES("128","36","sales_tax");
INSERT INTO `user_module_access` VALUES("129","36","wtax");
INSERT INTO `user_module_access` VALUES("130","36","purchasing_purchase_request");
INSERT INTO `user_module_access` VALUES("131","36","purchasing_purchase_order");
INSERT INTO `user_module_access` VALUES("132","36","warehouse_receive_items");
INSERT INTO `user_module_access` VALUES("133","36","warehouse_purchase_request");
INSERT INTO `user_module_access` VALUES("134","36","material_issuance");
INSERT INTO `user_module_access` VALUES("135","37","dashboard");
INSERT INTO `user_module_access` VALUES("136","37","accounts_payable_voucher");

DROP TABLE IF EXISTS `user_role_module_access`;
CREATE TABLE `user_role_module_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `module` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=204 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `user_role_module_access` VALUES("21","17","dashboard");
INSERT INTO `user_role_module_access` VALUES("22","17","reports");
INSERT INTO `user_role_module_access` VALUES("23","17","invoice");
INSERT INTO `user_role_module_access` VALUES("24","17","receive_payment");
INSERT INTO `user_role_module_access` VALUES("25","17","credit_memo");
INSERT INTO `user_role_module_access` VALUES("26","17","purchase_request");
INSERT INTO `user_role_module_access` VALUES("27","17","purchase_order");
INSERT INTO `user_role_module_access` VALUES("28","17","receive_items");
INSERT INTO `user_role_module_access` VALUES("29","17","accounts_payable_voucher");
INSERT INTO `user_role_module_access` VALUES("30","17","purchase_return");
INSERT INTO `user_role_module_access` VALUES("31","17","pay_bills");
INSERT INTO `user_role_module_access` VALUES("32","17","write_check");
INSERT INTO `user_role_module_access` VALUES("33","17","make_deposit");
INSERT INTO `user_role_module_access` VALUES("34","17","bank_transfer");
INSERT INTO `user_role_module_access` VALUES("35","17","chart_of_accounts_list");
INSERT INTO `user_role_module_access` VALUES("36","17","general_journal");
INSERT INTO `user_role_module_access` VALUES("37","17","transaction_entries");
INSERT INTO `user_role_module_access` VALUES("38","17","trial_balance");
INSERT INTO `user_role_module_access` VALUES("39","17","audit_trail");
INSERT INTO `user_role_module_access` VALUES("40","17","chart_of_accounts");
INSERT INTO `user_role_module_access` VALUES("41","17","item_list");
INSERT INTO `user_role_module_access` VALUES("42","17","customer");
INSERT INTO `user_role_module_access` VALUES("43","17","vendor_list");
INSERT INTO `user_role_module_access` VALUES("44","17","employee_list");
INSERT INTO `user_role_module_access` VALUES("45","17","other_name");
INSERT INTO `user_role_module_access` VALUES("46","17","location");
INSERT INTO `user_role_module_access` VALUES("47","17","uom");
INSERT INTO `user_role_module_access` VALUES("48","17","cost_center");
INSERT INTO `user_role_module_access` VALUES("49","17","category");
INSERT INTO `user_role_module_access` VALUES("50","17","terms");
INSERT INTO `user_role_module_access` VALUES("51","17","payment_method");
INSERT INTO `user_role_module_access` VALUES("52","17","discount");
INSERT INTO `user_role_module_access` VALUES("53","17","input_vat");
INSERT INTO `user_role_module_access` VALUES("54","17","sales_tax");
INSERT INTO `user_role_module_access` VALUES("55","17","wtax");
INSERT INTO `user_role_module_access` VALUES("56","17","purchasing_purchase_request");
INSERT INTO `user_role_module_access` VALUES("57","17","purchasing_purchase_order");
INSERT INTO `user_role_module_access` VALUES("58","17","warehouse_receive_items");
INSERT INTO `user_role_module_access` VALUES("59","17","warehouse_purchase_request");
INSERT INTO `user_role_module_access` VALUES("60","17","material_issuance");
INSERT INTO `user_role_module_access` VALUES("61","18","dashboard");
INSERT INTO `user_role_module_access` VALUES("62","18","invoice");
INSERT INTO `user_role_module_access` VALUES("63","18","receive_payment");
INSERT INTO `user_role_module_access` VALUES("64","18","credit_memo");
INSERT INTO `user_role_module_access` VALUES("65","18","chart_of_accounts");
INSERT INTO `user_role_module_access` VALUES("66","18","item_list");
INSERT INTO `user_role_module_access` VALUES("67","18","customer");
INSERT INTO `user_role_module_access` VALUES("68","18","employee_list");
INSERT INTO `user_role_module_access` VALUES("69","18","other_name");
INSERT INTO `user_role_module_access` VALUES("70","18","location");
INSERT INTO `user_role_module_access` VALUES("71","18","uom");
INSERT INTO `user_role_module_access` VALUES("72","18","cost_center");
INSERT INTO `user_role_module_access` VALUES("73","18","category");
INSERT INTO `user_role_module_access` VALUES("74","18","terms");
INSERT INTO `user_role_module_access` VALUES("75","18","payment_method");
INSERT INTO `user_role_module_access` VALUES("76","18","discount");
INSERT INTO `user_role_module_access` VALUES("77","18","input_vat");
INSERT INTO `user_role_module_access` VALUES("78","18","sales_tax");
INSERT INTO `user_role_module_access` VALUES("79","18","wtax");
INSERT INTO `user_role_module_access` VALUES("80","19","dashboard");
INSERT INTO `user_role_module_access` VALUES("81","19","item_list");
INSERT INTO `user_role_module_access` VALUES("82","19","vendor_list");
INSERT INTO `user_role_module_access` VALUES("83","19","other_name");
INSERT INTO `user_role_module_access` VALUES("84","19","location");
INSERT INTO `user_role_module_access` VALUES("85","19","uom");
INSERT INTO `user_role_module_access` VALUES("86","19","cost_center");
INSERT INTO `user_role_module_access` VALUES("87","19","category");
INSERT INTO `user_role_module_access` VALUES("88","19","terms");
INSERT INTO `user_role_module_access` VALUES("89","19","payment_method");
INSERT INTO `user_role_module_access` VALUES("90","19","discount");
INSERT INTO `user_role_module_access` VALUES("91","19","input_vat");
INSERT INTO `user_role_module_access` VALUES("92","19","sales_tax");
INSERT INTO `user_role_module_access` VALUES("93","19","wtax");
INSERT INTO `user_role_module_access` VALUES("94","19","purchasing_purchase_request");
INSERT INTO `user_role_module_access` VALUES("95","19","purchasing_purchase_order");
INSERT INTO `user_role_module_access` VALUES("96","19","inventory_list");
INSERT INTO `user_role_module_access` VALUES("97","19","warehouse_receive_items");
INSERT INTO `user_role_module_access` VALUES("98","19","warehouse_purchase_request");
INSERT INTO `user_role_module_access` VALUES("99","19","material_issuance");
INSERT INTO `user_role_module_access` VALUES("100","19","inventory_valuation_detail");
INSERT INTO `user_role_module_access` VALUES("101","19","reports");
INSERT INTO `user_role_module_access` VALUES("102","20","dashboard");
INSERT INTO `user_role_module_access` VALUES("103","20","purchase_request");
INSERT INTO `user_role_module_access` VALUES("104","20","purchase_order");
INSERT INTO `user_role_module_access` VALUES("105","20","receive_items");
INSERT INTO `user_role_module_access` VALUES("106","20","accounts_payable_voucher");
INSERT INTO `user_role_module_access` VALUES("107","20","purchase_return");
INSERT INTO `user_role_module_access` VALUES("108","20","pay_bills");
INSERT INTO `user_role_module_access` VALUES("109","20","chart_of_accounts_list");
INSERT INTO `user_role_module_access` VALUES("110","20","general_journal");
INSERT INTO `user_role_module_access` VALUES("111","20","transaction_entries");
INSERT INTO `user_role_module_access` VALUES("112","20","trial_balance");
INSERT INTO `user_role_module_access` VALUES("113","20","audit_trail");
INSERT INTO `user_role_module_access` VALUES("114","20","chart_of_accounts");
INSERT INTO `user_role_module_access` VALUES("115","20","item_list");
INSERT INTO `user_role_module_access` VALUES("116","20","customer");
INSERT INTO `user_role_module_access` VALUES("117","20","vendor_list");
INSERT INTO `user_role_module_access` VALUES("118","20","employee_list");
INSERT INTO `user_role_module_access` VALUES("119","20","other_name");
INSERT INTO `user_role_module_access` VALUES("120","20","fs_classification");
INSERT INTO `user_role_module_access` VALUES("121","20","fs_notes_classification");
INSERT INTO `user_role_module_access` VALUES("122","20","location");
INSERT INTO `user_role_module_access` VALUES("123","20","uom");
INSERT INTO `user_role_module_access` VALUES("124","20","cost_center");
INSERT INTO `user_role_module_access` VALUES("125","20","category");
INSERT INTO `user_role_module_access` VALUES("126","20","terms");
INSERT INTO `user_role_module_access` VALUES("127","20","payment_method");
INSERT INTO `user_role_module_access` VALUES("128","20","discount");
INSERT INTO `user_role_module_access` VALUES("129","20","input_vat");
INSERT INTO `user_role_module_access` VALUES("130","20","sales_tax");
INSERT INTO `user_role_module_access` VALUES("131","20","wtax");
INSERT INTO `user_role_module_access` VALUES("132","20","inventory_list");
INSERT INTO `user_role_module_access` VALUES("133","20","warehouse_receive_items");
INSERT INTO `user_role_module_access` VALUES("134","20","warehouse_purchase_request");
INSERT INTO `user_role_module_access` VALUES("135","20","material_issuance");
INSERT INTO `user_role_module_access` VALUES("136","20","warehouse_receive_items");
INSERT INTO `user_role_module_access` VALUES("137","20","inventory_valuation_detail");
INSERT INTO `user_role_module_access` VALUES("138","20","material_issuance");
INSERT INTO `user_role_module_access` VALUES("139","20","reports");
INSERT INTO `user_role_module_access` VALUES("140","21","dashboard");
INSERT INTO `user_role_module_access` VALUES("141","21","purchase_request");
INSERT INTO `user_role_module_access` VALUES("142","21","purchase_order");
INSERT INTO `user_role_module_access` VALUES("143","21","receive_items");
INSERT INTO `user_role_module_access` VALUES("144","21","accounts_payable_voucher");
INSERT INTO `user_role_module_access` VALUES("145","21","purchase_return");
INSERT INTO `user_role_module_access` VALUES("146","21","pay_bills");
INSERT INTO `user_role_module_access` VALUES("147","21","write_check");
INSERT INTO `user_role_module_access` VALUES("148","21","make_deposit");
INSERT INTO `user_role_module_access` VALUES("149","21","bank_transfer");
INSERT INTO `user_role_module_access` VALUES("150","21","chart_of_accounts_list");
INSERT INTO `user_role_module_access` VALUES("151","21","general_journal");
INSERT INTO `user_role_module_access` VALUES("152","21","transaction_entries");
INSERT INTO `user_role_module_access` VALUES("153","21","trial_balance");
INSERT INTO `user_role_module_access` VALUES("154","21","audit_trail");
INSERT INTO `user_role_module_access` VALUES("155","21","chart_of_accounts");
INSERT INTO `user_role_module_access` VALUES("156","21","item_list");
INSERT INTO `user_role_module_access` VALUES("157","21","customer");
INSERT INTO `user_role_module_access` VALUES("158","21","vendor_list");
INSERT INTO `user_role_module_access` VALUES("159","21","employee_list");
INSERT INTO `user_role_module_access` VALUES("160","21","other_name");
INSERT INTO `user_role_module_access` VALUES("161","21","fs_classification");
INSERT INTO `user_role_module_access` VALUES("162","21","fs_notes_classification");
INSERT INTO `user_role_module_access` VALUES("163","21","location");
INSERT INTO `user_role_module_access` VALUES("164","21","uom");
INSERT INTO `user_role_module_access` VALUES("165","21","cost_center");
INSERT INTO `user_role_module_access` VALUES("166","21","category");
INSERT INTO `user_role_module_access` VALUES("167","21","terms");
INSERT INTO `user_role_module_access` VALUES("168","21","payment_method");
INSERT INTO `user_role_module_access` VALUES("169","21","discount");
INSERT INTO `user_role_module_access` VALUES("170","21","input_vat");
INSERT INTO `user_role_module_access` VALUES("171","21","sales_tax");
INSERT INTO `user_role_module_access` VALUES("172","21","wtax");
INSERT INTO `user_role_module_access` VALUES("173","21","reports");
INSERT INTO `user_role_module_access` VALUES("174","22","dashboard");
INSERT INTO `user_role_module_access` VALUES("175","22","invoice");
INSERT INTO `user_role_module_access` VALUES("176","22","receive_payment");
INSERT INTO `user_role_module_access` VALUES("177","22","credit_memo");
INSERT INTO `user_role_module_access` VALUES("178","22","sales_return");
INSERT INTO `user_role_module_access` VALUES("191","24","dashboard");
INSERT INTO `user_role_module_access` VALUES("192","24","employee_list");
INSERT INTO `user_role_module_access` VALUES("193","24","department");
INSERT INTO `user_role_module_access` VALUES("194","24","position");
INSERT INTO `user_role_module_access` VALUES("195","24","shift_schedule");
INSERT INTO `user_role_module_access` VALUES("196","24","deduction");
INSERT INTO `user_role_module_access` VALUES("197","24","attendace_list");
INSERT INTO `user_role_module_access` VALUES("198","24","daily_time_record");
INSERT INTO `user_role_module_access` VALUES("199","24","leave_list");
INSERT INTO `user_role_module_access` VALUES("200","24","overtime_list");
INSERT INTO `user_role_module_access` VALUES("201","24","loan_list");
INSERT INTO `user_role_module_access` VALUES("202","24","payroll");
INSERT INTO `user_role_module_access` VALUES("203","24","generate_payroll");

DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `user_roles` VALUES("1","SUPERADMIN");
INSERT INTO `user_roles` VALUES("17","ADMIN");
INSERT INTO `user_roles` VALUES("18","RECEIVABLES");
INSERT INTO `user_roles` VALUES("19","PURCHASING");
INSERT INTO `user_roles` VALUES("20","PURCHASING MANAGER");
INSERT INTO `user_roles` VALUES("21","PAYABLES");
INSERT INTO `user_roles` VALUES("22","SAMPLE");
INSERT INTO `user_roles` VALUES("24","PAYROLL MASTER");

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL,
  `username` varchar(60) NOT NULL,
  `role_id` int(11) NOT NULL,
  `password` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` VALUES("1","Digimax","superadmin","1","IloveDigimax3407");
INSERT INTO `users` VALUES("36","Admin","admin","0","D!gimax321@");
INSERT INTO `users` VALUES("41","Kimberly Arellano","Kim","1","IloveDigimax3407");
INSERT INTO `users` VALUES("42","Theresa Cabigting","Theresa","1","3407");
INSERT INTO `users` VALUES("43","Janine Rallos","Janine","1","3407");
INSERT INTO `users` VALUES("44","Ma. Annil Villanueva","Annil","17","123");
INSERT INTO `users` VALUES("46","Rafael Villanueva","Raffy","20","123");
INSERT INTO `users` VALUES("47","Sally Jasme","Sally","17","321");
INSERT INTO `users` VALUES("49","Marjorie Tan","Marj","17","456");
INSERT INTO `users` VALUES("50","Paul Cutob","Paul","21","456");
INSERT INTO `users` VALUES("51","test123","test123","22","test123");
INSERT INTO `users` VALUES("53","Payroll","test_payroll1","24","test_payroll1");

DROP TABLE IF EXISTS `vendors`;
CREATE TABLE `vendors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `item_type` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `vendors` VALUES("1","Kenneth James Alfon","VENDOR234124","214444124","Blk 4 project 8","0944561224","dev.digimax@gmail.com","7 days","138-417-416-000","V12","242442","44124","4444","Inventory");
INSERT INTO `vendors` VALUES("2","2","","","","","","","","","","","","Inventory");

DROP TABLE IF EXISTS `wchecks`;
CREATE TABLE `wchecks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `print_status` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `wchecks` VALUES("1","CV000000001","","4124124","2024-10-29","1","customers","1","12412","5","124","2.48","121.52","13.02","108.5","1.09","0","120.44",NULL,NULL,NULL,"0","2024-10-29 14:59:13","superadmin","1");
INSERT INTO `wchecks` VALUES("2","CV000000002","124","124","2024-11-04","2","customers","2","124","5","100","0","100","10.71","89.29","0.89","0","99.11",NULL,NULL,NULL,"0","2024-11-04 05:25:42","superadmin","1");
INSERT INTO `wchecks` VALUES("3","CV000000003","124","124","2024-11-04","2","customers","1","12412","4","500","0","500","53.57","446.43","4.46","0","495.54",NULL,NULL,NULL,"0","2024-11-04 05:27:07","superadmin","1");
INSERT INTO `wchecks` VALUES("4","CV000000004","124","14","2024-11-04","2","vendors","2","124","5","124","0","124","13.29","110.71","2.21","0","121.79",NULL,NULL,NULL,"0","2024-11-04 05:41:03","superadmin","1");

DROP TABLE IF EXISTS `wchecks_details`;
CREATE TABLE `wchecks_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `wchecks_details` VALUES("1","1","2","2","124","124","2","2.48","121.52","108.5","12","13.02","2024-10-29 14:59:13");
INSERT INTO `wchecks_details` VALUES("2","2","1","2","124","100","0","0","100","89.29","12","10.71","2024-11-04 05:25:42");
INSERT INTO `wchecks_details` VALUES("3","3","1","2","qwe","500","0","0","500","446.43","12","53.57","2024-11-04 05:27:07");
INSERT INTO `wchecks_details` VALUES("4","4","1","2","124","124","0","0","124","110.71","12","13.29","2024-11-04 05:41:03");

DROP TABLE IF EXISTS `wtax`;
CREATE TABLE `wtax` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wtax_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `wtax_rate` float NOT NULL,
  `wtax_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `wtax_account_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `wtax` VALUES("1","1%-goods","1","1%-goods","11","2024-10-18 12:28:07");
INSERT INTO `wtax` VALUES("2","2%","2","2%","10","2024-10-18 12:28:19");


SET FOREIGN_KEY_CHECKS=1;

-- Triggers

DROP TRIGGER IF EXISTS `after_audit_trail_insert`;
DELIMITER //
CREATE TRIGGER `after_audit_trail_insert` AFTER INSERT ON `audit_trail`
FOR EACH ROW
BEGIN
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
//
DELIMITER ;

-- Stored Procedures and Functions

DROP PROCEDURE IF EXISTS `insert_inventory_valuation`;
DELIMITER //
CREATE DEFINER=`` PROCEDURE `insert_inventory_valuation`(
    IN p_type VARCHAR(50),
    IN p_transaction_id INT,
    IN p_ref_no VARCHAR(50),
    IN p_date DATE, 
    IN p_name INT,
    IN p_item_id INT,
    IN p_qty_purchased INT,
    IN p_qty_sold INT,
    IN p_cost DECIMAL(15, 2),
    IN p_total_cost DECIMAL(15, 2),
    IN p_purchase_discount_rate DECIMAL(10, 2),
    IN p_purchase_discount_per_item DECIMAL(10, 2),
    IN p_purchase_discount_amount DECIMAL(15, 2),
    IN p_net_amount DECIMAL(15, 2),
    IN p_input_vat_rate DECIMAL(15, 2),
    IN p_input_vat DECIMAL(15, 2),
    IN p_taxable_purchased_amount DECIMAL(15, 2),
    IN p_cost_per_unit DECIMAL(15, 2),
    IN p_selling_price DECIMAL(15, 2),
    IN p_gross_sales DECIMAL(15, 2),
    IN p_sales_discount_rate DECIMAL(15, 2),
    IN p_sales_discount_amount DECIMAL(15, 2),
    IN p_net_sales DECIMAL(15, 2),
    IN p_sales_tax DECIMAL(15, 2),
    IN p_output_vat DECIMAL(15, 2),
    IN p_taxable_sales_amount DECIMAL(15, 2),
    IN p_selling_price_per_unit DECIMAL(15, 2)
)
BEGIN
    DECLARE last_qty_on_hand DECIMAL(10, 2) DEFAULT 0;
    DECLARE last_asset_value_wa DECIMAL(15, 2) DEFAULT 0;
    DECLARE last_weighted_average_cost DECIMAL(15, 2) DEFAULT 0;
    DECLARE computed_weighted_average_cost DECIMAL(15, 2);
    DECLARE computed_asset_value_wa DECIMAL(15, 2);
    DECLARE computed_fifo_cost DECIMAL(15, 2);
    DECLARE computed_asset_value_fifo DECIMAL(15, 2);

    -- Fetch the last qty_on_hand, asset_value_wa, and weighted_average_cost for the given item_id
    SELECT qty_on_hand, asset_value_wa, weighted_average_cost 
    INTO last_qty_on_hand, last_asset_value_wa, last_weighted_average_cost
    FROM inventory_valuation
    WHERE item_id = p_item_id
    ORDER BY date DESC, id DESC
    LIMIT 1;

    -- Calculate the new qty_on_hand
    SET last_qty_on_hand = COALESCE(last_qty_on_hand, 0) + COALESCE(p_qty_purchased, 0) - COALESCE(p_qty_sold, 0);

    -- Compute the weighted average cost and asset value
    IF last_asset_value_wa IS NULL OR last_asset_value_wa = 0 THEN
        -- This is the first item or we're starting from zero
        IF p_qty_purchased > 0 THEN
            SET computed_weighted_average_cost = p_total_cost / p_qty_purchased;
            SET computed_asset_value_wa = p_total_cost;
        ELSE
            SET computed_weighted_average_cost = 0;
            SET computed_asset_value_wa = 0;
        END IF;
    ELSE
        IF p_qty_purchased > 0 THEN
            -- For purchases
            SET computed_weighted_average_cost = 
                (COALESCE(last_asset_value_wa, 0) + COALESCE(p_total_cost, 0)) 
                / last_qty_on_hand;
        ELSE
            -- For sales (when only qty_sold is provided)
            SET computed_weighted_average_cost = last_weighted_average_cost;
        END IF;
        
        -- Calculate asset value for both purchases and sales
        SET computed_asset_value_wa = last_qty_on_hand * computed_weighted_average_cost;
    END IF;

    -- Compute the FIFO cost
    SET computed_fifo_cost = IF(p_qty_purchased > 0, p_cost_per_unit, last_weighted_average_cost);

    -- Compute the asset value based on FIFO cost
    SET computed_asset_value_fifo = computed_fifo_cost * last_qty_on_hand;

     -- Insert data into inventory_valuation table
    INSERT INTO inventory_valuation (
        type,
        transaction_id,
        ref_no,
        date,
        name,
        item_id,
        qty_purchased,
        qty_sold,
        qty_on_hand,
        cost,
        total_cost,
        purchase_discount_rate,
        purchase_discount_per_item,
        purchase_discount_amount,
        net_amount,
        input_vat_rate,
        input_vat,
        taxable_purchased_amount,
        cost_per_unit,
        selling_price,
        gross_sales,
        sales_discount_rate,
        sales_discount_amount,
        net_sales,
        sales_tax,
        output_vat,
        taxable_sales_amount,
        selling_price_per_unit,
        weighted_average_cost,
        asset_value_wa,
        fifo_cost,
        asset_value_fifo
    ) VALUES (
        p_type,
        p_transaction_id,
        p_ref_no,
        p_date,
        p_name,
        p_item_id,
        p_qty_purchased,
        p_qty_sold,
        last_qty_on_hand,
        p_cost,
        p_total_cost,
        p_purchase_discount_rate,
        p_purchase_discount_per_item,
        p_purchase_discount_amount,
        p_net_amount,
        p_input_vat_rate,
        p_input_vat,
        p_taxable_purchased_amount,
        p_cost_per_unit,
        p_selling_price,
        p_gross_sales,
        p_sales_discount_rate,
        p_sales_discount_amount,
        p_net_sales,
        p_sales_tax,
        p_output_vat,
        p_taxable_sales_amount,
        p_selling_price_per_unit,
        computed_weighted_average_cost,
        computed_asset_value_wa,
        computed_fifo_cost,
        computed_asset_value_fifo
    );

END
//
DELIMITER ;

