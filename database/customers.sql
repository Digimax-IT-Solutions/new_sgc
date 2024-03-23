-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 23, 2024 at 04:25 AM
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
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customerID` int(11) NOT NULL,
  `customerCode` varchar(255) DEFAULT NULL,
  `customerName` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `customerPaymentMethod` varchar(255) DEFAULT NULL,
  `customerBillingAddress` text DEFAULT NULL,
  `customerShippingAddress` text DEFAULT NULL,
  `customerTin` varchar(255) DEFAULT NULL,
  `contactNumber` varchar(255) DEFAULT NULL,
  `customerDeliveryType` varchar(255) DEFAULT NULL,
  `customerTerms` varchar(255) DEFAULT NULL,
  `customerEmail` varchar(255) DEFAULT NULL,
  `customerBusinessStyle` text NOT NULL,
  `customerBalance` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customerID`, `customerCode`, `customerName`, `company`, `customerPaymentMethod`, `customerBillingAddress`, `customerShippingAddress`, `customerTin`, `contactNumber`, `customerDeliveryType`, `customerTerms`, `customerEmail`, `customerBusinessStyle`, `customerBalance`, `created_at`) VALUES
(49, '', 'A.G. Araja Cons\'t & Dev\'t Corp.', '', 'Check', 'AGA Bldg. Jaguar St., Mercado Village Sta Rosa Laguna', 'AGA Bldg. Jaguar St., Mercado', '006-730-755-000', '8584-4413', '', 'Net 60', '@newsgc', 'A.G. Araja Cons\'t & Dev\'t Corp.', 706932.11, '2024-02-15 02:48:31'),
(51, '', 'Acre Airconditioning Specialist, Inc.', 'Acre Airconditioning Specialist, Inc.', 'Check', '1st Avenue Arturo drive Bagumbayan, Taguig City', '1st Avenue Arturo drive Bagumbayan, Taguig City', '230-254-690-00000', '8838-3505', '', 'Net 60', '@new_sgc', 'Acre Airconditioning Specialist, Inc.', 548399.00, '2024-02-15 02:48:31'),
(53, '', 'Agacons Construction Inc.', 'Agacons Construction Inc.', 'Check', 'ACI Bldg. Mustang St..Mercado Village Pulong Sta Rosa Laguna', 'ACI Bldg. Mustang St. cor Pajero St.,', '006-944-428-000', '8519-4695', '', 'Net 30', '', 'Agacons Construction Inc.', 4092180.53, '2024-02-15 02:48:31'),
(56, '', 'Airtech System Construction Inc.', 'Airtech System Construction Inc.', 'Bank Transfer', 'Blk. Suite 2509 Cityland Pasong Tamo Tower 2210 Don Chino Roces Makati City', 'Suite 2509 Cityland Pasong Tamo Tower 2210 Don Chino Roces Makati City', '230-026-670-000', '', '', 'Net 30', '@new_sgc', 'Airtech System Construction Inc.', 667414.00, '2024-02-15 02:48:31'),
(61, '', 'Alliance Media Printing, Inc.', 'Alliance Media Printing, Inc.', 'Check', 'Printown Bldg Lot 2532 CIA Brgy Mamplasan Binan Laguna', 'Printown Bldg. Lot 2532 cia', '004-777-036-000', '', '', 'Net 30', '', 'Alliance Media Printing, Inc.', 3500.00, '2024-02-15 02:48:31'),
(62, '', 'Alpha Plus Property Holdings Corporation', '', 'Check', 'Block 5,Lot 9 City Center North,32 St.,cor 9th Ave Bonifacio Global City Taguig', 'Block 5,Lot 9 City Center North,32 St.,', '007-771-908-000', '8550-1258', '', 'Net 30', '@newsgc', 'Alpha Plus Property Holdings Corporation', 60293.00, '2024-02-15 02:48:31'),
(75, '', 'Aquatic Food Mfg Corp.', '', 'Check', 'Zamboanga National highway, Recodo Zamboanga City', '', '006-286-047-047', '', '', 'Net 30', '', 'Aquatic Food Mfg Corp.', 8425.00, '2024-02-15 02:48:31'),
(79, '', 'Aristorenas Enterprises', '', 'Cash', '589 Rizal Boulevard Brgy. Labas Sta Rosa City Laguna', '', '175-058-635-00000', '', '', 'COD', NULL, 'Aristorenas Enterprises', 154700.00, '2024-02-15 02:48:31'),
(80, '', 'Armadillo Holdings, Inc.', 'Armadillo Holdings, Inc.', 'Check', '2650 A Bonifacio st.,Bangkal,Makati City', '2650 A.Bonifacio St., Bangkal', '', '8889-3934', '', 'Net 30', NULL, 'Armadillo Holdings, Inc.', 0.00, '2024-02-15 02:48:31'),
(81, '', 'Arrowin Builders Inc.', 'Arrowin Builders Inc.', 'Check', 'ACI Bldg.Mustang St.,cor Pajero St. Mercado Village Brgy Pulong Sta Rosa Laguna', 'ACI Bldg.Mustang St.,cor Pajero St. Mercado Village Brgy Pulong Sta Rosa Laguna', '006-789-536-000', '8519-4695', '', 'Net 60', '@new_sgc', 'Arrowin Builders Inc.', 431418.66, '2024-02-15 02:48:31'),
(82, '', 'Asahi Asia Construction Inc.', 'Asahi Asia Construction Inc.', 'Check', 'Unit 606 6/f Prime Land Tower B5 L4 Market st Madrigal Bussiness Park Alabang Muntinlupa City', 'Unit 606 6Unit 606 6/f Prime Land Tower B5 L4 Market st Madrigal Bussiness Park Alabang Muntinlupa City/f Prime Land Tower', '008-640-907-000', '8808-0043', '', 'Net 60', '@new_sgc', 'Asahi Asia Construction Inc.', 471264.80, '2024-02-15 02:48:31'),
(83, '', 'Asia Brewery Incorporated', '', 'Check', 'Km.43  National Highway,Brgy.Sala,Cabuyao Laguna', '', '000-422-431-000', '8810-2701 loc 4520', '', 'Net 30', NULL, 'Asia Brewery,Inc.', 92512.01, '2024-02-15 02:48:31'),
(86, '', 'Balyena Tanker Corporation', '', 'Check', 'Blk 9 Lot 4-6 Fernando St Cor Francisco Serro St Manila Harbour Centre Barangay 128 Zone 10 1013 Tondo Manila', '', '009-234-939-00000', '', '', 'Net 30', NULL, 'Balyena Tanker Corporation', 12568.42, '2024-02-15 02:48:31'),
(88, '', 'Baseco Shipyard Corporation', 'Baseco Shipyard Corporation', 'Check', 'Yard 2 Engineering Island Baseco Compound Brgy 649 Zone 68 Port Area Manila 1018', '', '008-216-294-00000', '', '', 'Net 30', '', 'Baseco Shipyard Corporation', 0.00, '2024-02-15 02:48:31'),
(93, '', 'Billray Realty Holdings, Inc.', '', 'Check', '#12 Lus Circle Corinthian Garden Quezon City', '', '006-331-665-000', '', '', 'Net 30', '', 'Billray Realty Holdings, Inc.', 4600.00, '2024-02-15 02:48:31'),
(94, '', 'Blazemaster Philippines, Inc.', 'Blazemaster Philippines, Inc.', 'Check', 'Blk.132 Lot 4 Juan Luna cor Adela st.,Brgy Rizal, Makati City', 'Blazemaster Philippines, Inc.', '222-600-444-000', '8625-0652', '', 'Net 30', '', 'Blazemaster Philippines, Inc.', 560400.00, '2024-02-15 02:48:31'),
(103, '', 'Caaz Trading', '', 'Check', '', '', '', '', '', 'Net 30', NULL, 'Caaz Trading', 37058.50, '2024-02-15 02:48:31'),
(107, '', 'Catarman Oil Mills, Inc.', 'Catarman Oil Mills, Inc.', 'Check', 'Sitio Torotangbo,Brgy.Aguadahan San Jose Northern Samar', 'Sitio Torotangbo,Brgy.Aguadahan San Jose Northern Samar', '007-009-719-000', '', '', 'Net 30', '#new_sgc', 'Catarman Oil Mills, Inc.', 199113.20, '2024-02-15 02:48:31'),
(108, '', 'Celine Development Corporation', '', 'Check', '12 F Times Plaza Bldg. UN Ave.cor Taft Brgy 666 Zone 07 Ermita,Manila', '', '000-879-125-000', '', '', 'Net 30', '', 'Celine Development Corporation', 21229.00, '2024-02-15 02:48:31'),
(109, '', 'Centrex Corporation', 'Centrex Corporation', 'Check', '206 Shaw Blvd. corner Pilar st Mandaluyong City', '', '000-111-016-000', '8722-6010', '', 'Net 30', '', 'Centrex Corporation', 86924.00, '2024-02-15 02:48:31'),
(112, '', 'Charter International Inc.', '', 'Check', '9th Floor CMG Centre 12 J Cruz St Brgy Ugong Pasig City', '', '004-967-467-000', '', '', 'Net 30', '', 'Charter International Inc.', 43390.00, '2024-02-15 02:48:31'),
(115, '', 'CMG Retail Inc.', '', 'Check', '9th & 10th Floor CMG Center 12J Cruz st Brgy Ugong Pasig City', '', '000-337-941-00000', '', '', 'Net 30', '', 'CMG Retail Inc.', 321818.00, '2024-02-15 02:48:31'),
(117, '', 'Coco Davao, Inc.', 'Coco Davao, Inc.', 'Check', 'Sitio San Jose Zone IV Sta Cruz Davao del Sur', 'Sitio San Jose Zone IV Sta Cruz Davao del Sur', '005-630-623-000', '', '', 'Net 30', '@new_sgc', 'Coco Davao, Inc.', 516863.18, '2024-02-15 02:48:31'),
(119, '', 'D & L Polymer & Colours, Inc.', '', 'Check', '122 Progress Avenue, Carmelray Industrial Park 1 Canlubang Calamba Laguna', '122 Progress Avenue, Carmelray Industrial Park 1 Canlubang Calamba Laguna', '244-553-071-00000', '', '', 'Net 30', '@new_sgc', 'D & L Polymer & Colours, Inc.', 162298.00, '2024-02-15 02:48:31'),
(123, '', 'DCMS Construction, Inc.', 'DCMS Construction, Inc.', 'Check', '1408 South Center Tower,', '1408 South Center Tower,', '', '809-0823', '', 'Net 90', '', 'DCMS Construction, Inc.', 2075502.60, '2024-02-15 02:48:31'),
(124, '', 'De La Salle-College of Saint Benilde,Inc.', '', 'Bank Transfer', '2544 Taft Avenue Brgy 728 Malate NCR City of Manila', '', '001-399-066-000', '', '', 'Net 30', NULL, 'De La Salle-College of Saint Benilde,Inc.', 498823.02, '2024-02-15 02:48:31'),
(125, '', 'Delros Land Properties Inc.', '', 'Check', 'Unit 107 Cluster D Golfhill Terraces,manotoc Drive,Capitol Hills Balara QC', '', '006-909-152-000', '', '', 'Net 30', '', 'Delros Land Properties Inc.', 13066.00, '2024-02-15 02:48:31'),
(129, '', 'Development Academy of the Philippines', 'Development Academy of the Philippines', 'Check', 'DAP Bldg., San Miguel Avenue, Pasig City', '', '000-285-531-000', '8633-5571', '', 'Net 30', '', 'Development Academy of the Philippines', 311874.50, '2024-02-15 02:48:31'),
(130, '', 'Dhunwell Corporation', '', '', '#8 Dama De Noche St.,', '', '', '', '', '', '', 'Dhunwell Corporation', 30505.00, '2024-02-15 02:48:31'),
(131, '', 'Dipolog Coconut Oil Mill,Inc.', 'Dipolog Coconut Oil Mill,Inc.', 'Check', 'Brgy. Irasan Roxas, Zamboanga', '', '007-805-547-000', '', '', 'Net 30', '', 'Dipolog Coconut Oil Mill,Inc.', 378419.50, '2024-02-15 02:48:31'),
(135, '', 'DPP Builders & Development Corp.', '', 'Check', '.Blk 1 Lot 22 Ph 5 Brookside Lane Brgy San Francisco General trias Cavite', '', '', '', '', 'Net 30', '', 'DPP Builders & Development Corp.', 1490860.00, '2024-02-15 02:48:31'),
(136, '', 'Ecotechland, Inc.', '', 'Check', 'Mez Floor Ecoplaza Bldg.2205 Don Chino Ave Ext Magallanes Makati City', '', '006-660-417-000', '', '', 'Net 30', '', 'Ecotechland, Inc.', 45415.00, '2024-02-15 02:48:31'),
(137, '', 'Edali Properties', 'Edali Properties', 'Check', '#11 EO Bldg. Cor. United St. Bo Kapitolyo,Pasig City', '', '', '', '', 'Net 30', '', 'Edali Properties', 432260.00, '2024-02-15 02:48:31'),
(139, '', 'EK2 Marketing', 'EK2 Marketing', 'Check', '1151 Oliveros Compound F bautista St Brgy Ugong Valenzuela City', '', '104-006-082-000', '', '', 'Net 30', '', 'EK2 Marketing', 54206.60, '2024-02-15 02:48:31'),
(140, '', 'Elite Marine Construction Corporation', '', 'Check', 'Blk 9 Lot 4-6 Fernando St Cor Francisco Serro St Manila Harbour Centre Barangay128 Zone 10 1013 Tondo Manila', 'Blk 9 Lot 4-6 Fernando st cor Francisco Serro st Manila Harbour Centre Brgy 128 Tondo Manila', '009-258-979-000', '', '', 'Net 30', '@new_sgc', 'Elite Marine Construction Corporation', 729294.80, '2024-02-15 02:48:31'),
(141, '', 'Em Tech Global Construction Inc.', '', 'Check', 'Unit 12 2nd Flr. Casa Lolita Bldg National Highway Brgy Bucial Calamba Laguna', '', '010-183-027-000', '', '', 'Net 30', '', 'Em Tech Global Construction Inc.', 326036.50, '2024-02-15 02:48:31'),
(143, '', 'Ervil Innovations One Builders, Inc.', 'Ervil Innovations One Builders, Inc.', 'Check', '5 Robin St.,Don Mariano Subd. Cainta Rizal', '', '', '', '', 'Net 30', NULL, 'Ervil Innovations One Builders, Inc.', 50333.46, '2024-02-15 02:48:31'),
(144, '', 'Esguerra Shipping Corporation', '', 'Check', '#94 Scout Rallos St.,Kamuning Quezon City', '', '', '', '', 'Net 30', '', 'Esguerra Shipping Corporation', 323376.00, '2024-02-15 02:48:31'),
(146, '', 'ESJ Properties', 'ESJ Properties', 'Check', 'No.11 EO Bldg. Cor United St Bo Kapitolyo Pasig City', 'No.11 EO Bldg. Cor United Syt Bo Kapitolyo Pasig City', '112-211-748-000', '', '', 'Net 30', '', 'ESJ Properties', 87086.00, '2024-02-15 02:48:31'),
(148, '', 'Eurochem Manufacturing Corporation', '', 'Check', '#103 Progress Ave.,PH1,GIZ,Carmelray Industrial Park Canlubang Calamba Laguna', '', '241-085-627-000', '', '', 'Net 30', NULL, 'Eurochem Manufacturing Corporation', 0.00, '2024-02-15 02:48:31'),
(152, '', 'Felton Realty & Development Corporation', '', '', '#2300 Paong Tamo', '', '', '', '', '', '', 'Felton Realty & Development Corporation', 0.00, '2024-02-15 02:48:31'),
(153, '', 'FEP Printing Corporation', 'FEP Printing Corporation', 'Check', 'Printown Complex Lot 2532 C-128 Mamplasan Binan Laguna', '', '001-902-142-00000', '', '', 'Net 30', '', 'FEP Printing Corporation', 0.00, '2024-02-15 02:48:31'),
(156, '', 'Fisher Retail Inc.', '', 'Check', '#42 General Lim St. Brgy.Sta.Cruz,Quezon City', '', '008-410-028-000', '', '', 'Net 30', '', 'Fisher Retail Inc.', 27374.00, '2024-02-15 02:48:31'),
(158, '', 'Fishport Ice Plant, Inc.', 'Fishport Ice Plant, Inc.', 'Check', '', '', '', '', '', 'Net 30', '', 'Fishport Ice Plant, Inc.', 27607.90, '2024-02-15 02:48:31'),
(159, '', 'Flagship Petroleum Carriers, Inc.', 'Flagship Petroleum Carriers, Inc.', 'Check', '#94 Sct Rallos St.,Brgy Sacred Heart Kamuning Quezon City', '', '002-787-705-000', '8922-3421', '', 'Net 30', '', 'Flagship Petroleum Carriers, Inc.', 13778.50, '2024-02-15 02:48:31'),
(160, NULL, 'FODC-First Orient Dev\'t & Cons\'t Cor.', '', NULL, 'Unit 702 7/F Alabang Business', '', NULL, '', NULL, '', NULL, 'FODC-First Orient Dev\'t & Cons\'t Cor.', 0.00, '2024-02-15 02:48:31'),
(162, '', 'Fortune Packaging Corporation', 'Fortune Packaging Corporation', 'Check', '#20 Main Ave. Severina Industrial Subd Km 16 South Superhighway Paranaque City', '#20 Main Ave. Severina Industrial Subd Km 16 South Superhighway Paranaque City', '000-411-578-000', '', '', 'Net 30', '@new_sgc', 'Fortune Packaging Corporation', 113470.37, '2024-02-15 02:48:31'),
(168, '', 'GCH Internationanl Mercantile,Inc.', 'GCH Internationanl Mercantile,Inc.', 'Check', 'Suite 506 One Corporate Plaza Bldg 845 Arnaiz Avenue,Makati City', 'Suite 506 One Corporate Plaza Bldg 845 Arnaiz Avenue,Makati City', '000-412-835-000', '8818-5029 ', '', 'Net 30', '@new_sgc', 'GCH Internationanl Mercantile,Inc.', 174763.00, '2024-02-15 02:48:31'),
(171, '', 'GGP Pipetech Ent.', 'GGP Pipetech Enterprises', 'Check', '412 V.Jasmine st.,Greenland Executive Village Brgy.San Juan Cainta Rizal', '', '', '655-9885', '', 'COD', '', 'GGP Pipetech Ent.', 124162.00, '2024-02-15 02:48:31'),
(172, '', 'Gigatech Inc.', 'Gigatech Inc.', 'Check', 'Blk.2 Lot 1, Duhat St.,Mon-el Subd., San Antonio,Paranaque City', 'Gigatech Inc.', '202-424-686-000', '8820-2593', '', 'Net 30', '', 'Gigatech Inc.', 122382.00, '2024-02-15 02:48:31'),
(173, NULL, 'Gigatt Manufacturing Trading Inc.', '', NULL, '#55 Purok 2, Brgy.', '', NULL, '', NULL, 'Dated Cheque', NULL, 'Gigatt Manufacturing Trading Inc.', 361290.63, '2024-02-15 02:48:31'),
(174, '', 'Glacier FTI Refrigerated Services Corp.', '', 'Check', '36 DBP Avenue Taguig', '', '', '', '', 'Net 30', NULL, 'Glacier FTI Refrigerated Services Corp.', 8395.00, '2024-02-15 02:48:31'),
(175, '', 'Glacier Intergrated Logistic Incorporated', '', '', 'S-701 Royal Plaza Twin Tower', '', '', '', '', '', '', 'Glacier Intergrated Logistic Incorporated', 30000.00, '2024-02-15 02:48:31'),
(176, '', 'Glacier Liberty Refrig Services Corp.', '', '', '#548 Remedios Street,', '', '', '', '', '', '', 'Glacier Liberty Refrig Services Corp.', 0.00, '2024-02-15 02:48:31'),
(177, '', 'Glacier Panay Refrig Services Corp.', '', 'Check', '648 Remedios Street,', '', '', '', '', 'Net 30', '', 'Glacier Panay Refrig Services Corp.', 41649.00, '2024-02-15 02:48:31'),
(178, '', 'Glacier Paranaque Refrig Svcs. Corp.', '', 'Check', 'Amvel Business Park Brgy.San dionisio Paranaque City', '', '008-978-781-000', '', '', 'Net 30', '', 'Glacier Paranaque Refrig Svcs. Corp.', 45211.07, '2024-02-15 02:48:31'),
(179, '', 'Glacier Pulilan Refrig Services Corp.', '', 'Check', 'Nationa Road Dampol II-B Pulilan Bulacan', '', '010-058-353-000', '', '', 'Net 30', '', 'Glacier Pulilan Refrig Services Corp.', 230402.20, '2024-02-15 02:48:31'),
(180, '', 'Glacier Refrigerated Services Corp.', '', 'Check', '36 DBP Avenue, Taguig City', '', '237-401-768-000', '', '', 'Net 30', '', 'Glacier Refrigerated Services Corp.', 27000.00, '2024-02-15 02:48:31'),
(181, NULL, 'Glacier Samar Refrig Services Corp.', '', NULL, 'Unit 708 Royal Plaza Twin', '', NULL, '', NULL, '30 days', NULL, 'Glacier Samar Refrig Services Corp.', 6500.00, '2024-02-15 02:48:31'),
(182, '', 'Glacier South Refrigeration SVC Corp.', '', 'Check', 'Glacier Megafridge bldg. Amvel Business Park Brgy.San Dionisio Paranaque City', '', '008-978-781-000', '', '', 'Net 30', '', 'Glacier South Refrigeration SVC Corp.', 73202.00, '2024-02-15 02:48:31'),
(183, '', 'Globe Coco Products Manufacturing Corp.', 'Globe Coco Products Manufacturing Corp.', 'Check', 'Bo.Lidong, Sto Domingo, Albay', '681 Aurora Blvd..Quezon City', '004-198-674-000', '8412-2257', '', 'Net 30', '@new_sgc', 'Globe Coco Products Manufacturing Corp.', 486767.04, '2024-02-15 02:48:31'),
(185, '', 'Goldrich Industrial Packaging Corp.', '', 'Check', 'Whse 3 EZP Buss.Park Calamba Premier Int\'l.Park Calamba Laguna', '', '007-009-719-000', '8838-8982', '', 'Net 30', '', 'Goldrich Industrial Packaging Corp.', 681691.00, '2024-02-15 02:48:31'),
(186, NULL, 'Grandway Industrial Manufacturing Corp.', '', NULL, 'Grandway Industrial Manufacturing Corp.', '', NULL, '', NULL, '', NULL, 'Grandway Industrial Manufacturing Corp.', 1499.99, '2024-02-15 02:48:31'),
(187, '', 'Green Oracle Phils. Inc.', '', 'Check', 'Green Oracle Phils. Inc.', '', '', '', '', '', '', 'Green Oracle Phils. Inc.', 147083.75, '2024-02-15 02:48:31'),
(188, '', 'GTech Solutions Inc.', 'GTech Solutions Inc.', 'Check', 'Blk 2 Lot 8-A Road 2 Daiichi  Industrial Park Maguyam Silang Cavite', ' Lot 8-A Road 2 Dai-ichi Industrial Park Maguyam,Silang Cavite', '008-214-152-000', '', '', 'Net 30', '@new_sgc', 'GTech Solutions Inc.', 520281.00, '2024-02-15 02:48:31'),
(189, '', 'H.R.D. Singapore Pte Ltd.', '', 'Check', 'H.R.D. Singapore Pte Ltd.', '', '', '', '', 'Net 30', NULL, 'H.R.D. Singapore Pte Ltd.', 3264.00, '2024-02-15 02:48:31'),
(190, '', 'HBS Marketing', '', '', '41 Howmart Road, Edsa', '', '', '', '', '', '', 'HBS Marketing', 8000.00, '2024-02-15 02:48:31'),
(193, '', 'Herma Shipping & Transport Corporation', 'Herma Shipping & Transport Corporation', 'Check', '94 Scout Rallos St.,Kamuning Quezon City', 'Herma Shipping & Transport Corp.', '000-659-108-00000', '8922-3421', '', 'Net 30', '', 'Herma Shipping & Transport Corporation', 489697.20, '2024-02-15 02:48:31'),
(194, '', 'Herma Shipyard, Inc.', 'Herma Shipyard, Inc.', '', '94 Scout Rallos St.,Kamuning,Quezon City', '94 Scout Rallos St.,Kamuning,Quezon City', '005-858-633-000', '922-3421', '', 'Net 30', '@new_sgc', 'Herma Shipyard, Inc.', 333889.25, '2024-02-15 02:48:31'),
(196, '', 'House Technology Industries Pte Ltd.', '', 'Bank Transfer', 'Blk 1, Cavite Economic Zone II General Trias Cavite', '', '004-692-492-000', '', '', 'Net 30', NULL, 'House Technology Industries Pte Ltd.', 19465088.20, '2024-02-15 02:48:31'),
(197, '', 'HRD Singapore Pte Ltd.', '', 'Bank Transfer', 'Block 3,Cavite Economic Zone II General Trias Cavite', '', '', '', '', 'Net 20', NULL, 'HRD Singapore Pte Ltd.', 186073.00, '2024-02-15 02:48:31'),
(202, '', 'I ACADEMY', '', 'Check', '7434 Yakal St Brgy San Antonio Makati City', '7434 Yakal St Brgy San Antonio Makati City', '214-749-003-000', '', '', 'Net 30', '@new_sgc', 'I ACADEMY', 40906.00, '2024-02-15 02:48:31'),
(203, '', 'Imasen Philippine Manufacturing Corp.', 'Imasen Philippine Manufacturing Corp.', 'Check', '101 East Main Avenue,Technopark Binan Laguna', '101 East Main Avenue,', '004-781-033-000', '049-5411480', '', 'Net 30', '', 'Imasen Philippine Manufacturing Corp.', 100375.00, '2024-02-15 02:48:31'),
(204, '', 'Integral Machine Tool, Inc.', '', 'Check', '#12 Main Avenue,Km16 severina Industrial Estate South Superhighway Bicutan Paranaque City', '', '000-162-706-000', '', '', 'Net 30', '', 'Integral Machine Tool, Inc.', 17921.50, '2024-02-15 02:48:31'),
(205, NULL, 'Intertrans Cargo Vessel,Inc.', '', NULL, 'Sandoval Avenue Sitio,', '', NULL, '', NULL, '30 days', NULL, 'Intertrans Cargo Vessel,Inc.', 11960.00, '2024-02-15 02:48:31'),
(207, '', 'Irma Fishing & Trading, Inc.', 'Irma Fishing & Trading, Inc.', 'Check', 'Blk 3 Lot 1 Along C4 Road Phase IV Dagat Dagatan Development Project Malabon', 'Blk.3 Lot 1 Along C-4 Road Phase IV,', '', '8288-3403', '', 'Net 30', '', 'Irma Fishing & Trading, Inc.', 134859.75, '2024-02-15 02:48:31'),
(209, '', 'Ithiel Corporation - San Pedro', '', 'Check', 'National Hiway brgy. Nueva San Pedro Laguna', '', '004-730-571-001', '', '', 'Net 30', '', 'Ithiel Corporation - San Pedro', 0.00, '2024-02-15 02:48:31'),
(214, '', 'J. Beap Industries Inc.', '', 'Check', '', '', '', '', '', 'Net 30', NULL, 'J. Beap Industries Inc.', 147699.99, '2024-02-15 02:48:31'),
(217, NULL, 'JLO & Daughsons Inc. Gen. Engineering', '', NULL, 'Block 2 Lot 8-A DAIICHI', '', NULL, '', NULL, '', NULL, 'JLO & Daughsons Inc. Gen. Engineering', 9703.08, '2024-02-15 02:48:31'),
(219, '', 'JMTQUI Construction', '', 'Check', 'Purok 3, Brgy. San Isidro', '', '', '', '', 'Net 30', '', 'JMTQUI Construction', 143475.00, '2024-02-15 02:48:31'),
(220, '', 'Joaqscorp Cons\'t & Industries Inc.', '', 'Check', 'Lot 2 Blk. 3 Pointsettia St.Holiday Park bBrgy san Antonio San Pedro Laguna', 'Lot 2 Blk. 3 Pointsettia St.Holiday Park bBrgy san Antonio San Pedro Laguna', '', '', 'pick up', 'Net 30', '@new_sgc', 'Joaqscorp Cons\'t & Industries Inc.', 16821.50, '2024-02-15 02:48:31'),
(221, '', 'Jocar Piping Services', 'Jocar Piping Services', 'Check', '5 Dahlia St. SPS 9, Bagbag,Novaliches Quezon City', '5 Dahlia St. SPS 9, Bagbag,', '', '', '', 'Net 30', '', 'Jocar Piping Services', 0.00, '2024-02-15 02:48:31'),
(225, NULL, 'JRDI Wholesale & Retail Trading Corp.', '', NULL, 'B2 L16 Mondo Bambini', '', NULL, '', NULL, 'Bank Transfer', NULL, 'JRDI Wholesale & Retail Trading Corp.', 8940.00, '2024-02-15 02:48:31'),
(226, '', 'JRED-II Construction Inc.', '', 'Check', '116 San Pedro St. Plainview,Mandaluyong City', '', '768-994-460-000', '', '', 'Net 30', NULL, 'JRED-II Construction Inc.', 123000.00, '2024-02-15 02:48:31'),
(234, '', 'Kalinisan Workers Service Cooperative', '', 'Check', '#10 Manggahan St. Bagumbayan, quezon City', '', '', '', '', 'Net 30', '', 'Kalinisan Workers Service Cooperative', 67037.50, '2024-02-15 02:48:31'),
(237, '', 'Kinden Phils Corporation', 'Kinden Phils Corporation', 'Check', '5/f ODC International Plaza 219 Salcedo st Legaspi Village Makati City', '5/f ODC International Plaza', '004-711-490-000', '812-6440', '', 'Net 30', '', 'Kinden Phils Corporation', 28467.40, '2024-02-15 02:48:31'),
(247, '', 'Lalaland Funtertainment Inc.', 'Lalaland Funtertainment Inc.', 'Check', '42 Gen. Lim St Brgy. Sta Cruz Quezon City', '', '003-321-946-000', '', '', 'Net 30', NULL, 'Lalaland Funtertainment Inc.', 35780.00, '2024-02-15 02:48:31'),
(250, '', 'Lee Top Builders Industries Corp.', '', 'Bank Transfer', 'Block 11 Lot 12 Villa Olympia,San Vicente,San Pedro Laguna', '', '', '', '', 'Net 30', NULL, 'Lee Top Builders Industries Corp.', 20462348.09, '2024-02-15 02:48:31'),
(252, '', 'Legend Hotel International Corporation', '', 'Check', '2878 Zamora st.,Pasay City', '2878 Zamora Street,', '001-218-911-006', '', '', 'Net 30', '', 'Legend Hotel International Corp.', 268319.99, '2024-02-15 02:48:31'),
(253, NULL, 'Lexmedia Digital Corporation', 'Lexmedia Digital Corporation', NULL, 'Print Town Complex', '', NULL, '', NULL, 'Net 30', NULL, 'Lexmedia Digital Corporation', 0.00, '2024-02-15 02:48:31'),
(256, '', 'LMG Land Development Corp.', '', 'Check', '103 Progress Ave. Carmelray Industrial Park 1 Canlubang Calamba Laguna', '103 Progress Ave. Carmelray Industrial Park 1 Canlubang Calamba Laguna', '006-589-959-000', '', '', 'Net 30', '@new_sgc', 'LMG Land Development Corp.', 214502.00, '2024-02-15 02:48:31'),
(257, '', 'LOSCAM Phils. Inc.', 'Loscam  Phils. Inc.', 'Check', 'Suite 301-302 Common Goal Tower Finance cor Industry st Madrigal Business Park Ayala Alabang', 'Loscam  Phils. Inc.', '209-359-170-000', '8842-7878', '', 'Net 30', '', 'LOSCAM Phils. Inc.', 408996.00, '2024-02-15 02:48:31'),
(258, '', 'Macro Industrial Packaging Corporation', 'Macro Industrial Packaging Corporation', 'Check', 'Airstrip Road,Canlubang Industrial Estate 4028  Calamba Laguna', '', '244-179-687-000', '', '', 'Net 30', '', 'Macro Industrial Packaging Corporation', 26165.74, '2024-02-15 02:48:31'),
(260, '', 'Majestic Landscape Corporation', '', 'Bank Transfer', 'Blk 3 Cavite Economic Zone II General Trias Cavite', '', '207-286-660-000', '', '', 'Net 15', NULL, 'Majestic Landscape Corporation', 416922.52, '2024-02-15 02:48:31'),
(261, NULL, 'Majestics Energy Corporation', '', NULL, 'Cavite Economic Zone 11', '', NULL, '', NULL, '', NULL, 'Majestics Energy Corporation', 4250.00, '2024-02-15 02:48:31'),
(264, '', 'Mallers Investments Corporation - Malabon', '', 'Check', '5F-LA Fishermall Malabon Lot 2-10 Blk 8 Phase 3 E-2 Along C4 Road Longos Dist 2 Malabon', '', '008-018-421-000', '', '', 'Net 30', '', 'Mallers Investments Corporation - Malabon', 5670.00, '2024-02-15 02:48:31'),
(265, '', 'Mallers Management Corporation', 'Mallers Management Corporation', 'Check', '5/F La Fishermall Malabon Lot 2-10 Blk 3 Phase 3 E2 Along C4 Road Brgy Longos Malabon City', '', '003-197-139-000', '', '', 'Net 30', '', 'Mallers Management Corporation', 0.00, '2024-02-15 02:48:31'),
(266, NULL, 'Manvitts Corp.', '', NULL, '#648 Remedios Street,', '', NULL, '', NULL, '', NULL, 'Manvitts Corp.', 2950.00, '2024-02-15 02:48:31'),
(267, '', 'Marcons Builder Corporation', '', 'Cash', 'LGF City Hub #92 Upper General Luna Baguio City', '', '601-392-644-000', '', '', 'Cash on delivery', '', 'Marcons Builder Corporation', 0.00, '2024-02-15 02:48:31'),
(268, '', 'Margarrett Enterprises, Inc.', '', 'Check', 'Sandoval Avenue Sitio Iloguin,Cainta Rizal', '', '000-420-801-000', '', '', '', '', 'Margarrett Enterprises, Inc.', 33660.00, '2024-02-15 02:48:31'),
(271, '', 'Mase Builders Inc.', '', 'Check', '5F Treston Bldg 32nd st cor C5 Road BGC Taguig City', '', '010-489-093-000', '', '', 'Net 30', '', 'Mase Builders Inc.', 0.00, '2024-02-15 02:48:31'),
(277, '', 'Mega Packaging Corporation', 'Mega Packaging Corporation', 'Check', 'Airstrip Road Canlubang Ind\'l Estate Calamba Laguna', 'Mega PackagAirstrip Road Canlubang Ind\'l Estate Calamba Lagunaing Corporation', '003-057-431-000', '', '', 'Net 60', '@new_sgc', 'Mega Packaging Corporation', 353016.70, '2024-02-15 02:48:31'),
(279, '', 'Metech Industrial Corporation', 'Metech Industrial Corp.', 'Check', 'Lot 21-22 Dagat Dagatan Ave.,Kaunlaran village Caloocan City', '', '', '', '', 'Net 30', NULL, 'Metech Industrial Corp.', 148994.00, '2024-02-15 02:48:31'),
(280, '', 'Metro Diesel Company, Inc.', 'Metro Diesel Company, Inc.', 'Check', '59 C3 Road cor Dagat Dagatan Ave Kanluran Village Caloocan City', '225 Riverside St., San Rafael Village', '000-297-446-000', '8285-4084', '', 'Net 30', '', 'Metro Diesel Company, Inc.', 30345.00, '2024-02-15 02:48:31'),
(281, '', 'Metro Hue-Tech Chemical Co., Inc.', '', 'Check', '#103 Progress Ave. Ph 1 GIZ  Carmelray Ind\'l Parl 1 Canlubang Calamba City Laguna', '', '215-843-356-000', '', '', 'Net 30', '', 'Metro Hue-Tech Chemical Co., Inc.', 13766.00, '2024-02-15 02:48:31'),
(282, '', 'Metro Industries, Inc..', '', 'Check', '#103 Progress Ave. Ph1 GIZ Carmelray Ind\'lk. Park 1 Canlubang Calamba City', '', '000-232-024-000', '', '', 'Net 30', '', 'Metro Industries, Inc..', 8325.00, '2024-02-15 02:48:31'),
(283, '', 'MG8 Terminal Inc.', 'MG8 Terminal Inc.', 'Check', 'Blk 9 Lot 4-6 Fernando st cor Francisco Serro st Manila Harbour Centre Barangay 128 Zone 10 1013 Tondo Manila', 'Blk 9 Lot 4-6 Fernando st cor Francisco Serro st Manila Harbour Tondo', '008-008-832-00000', '85503649', '', 'Net 30', '@newsgc', 'MG8 Terminal Inc.', 179522.00, '2024-02-15 02:48:31'),
(288, '', 'Molave Tanker Corporation', '', 'Check', 'Blk 9 Lot 4-6 Fernando st cor Francisco Serro st Manila Harbour Centre Barangay 128 Zone10 1013Tondo Manila', '', '008-523-474-00000', '', '', 'Net 30', NULL, 'Molave Tanker Corporation', 92380.47, '2024-02-15 02:48:31'),
(289, NULL, 'Molson Statler Enterprises', '', NULL, '#58 Malac St., Cor.', '#58 Malac St., Cor.', NULL, '', NULL, '', NULL, 'Molson Statler Enterprises', 0.00, '2024-02-15 02:48:31'),
(291, '', 'Motech ( E-Car Supplies & Services Inc.)', '', 'Check', '140 West Ave. Brgy. Brgy Philam, quezon City', '', '770-413-973-000', '', '', 'Net 30', '', 'Motech ( E-Car Supplies & Services Inc.)', 0.00, '2024-02-15 02:48:31'),
(294, '', 'Narra Tanker Corporation', 'Narra Tanker Corporation', 'Check', 'Blk 9 Lot 4-6 Fernando St Cor Francisco Serro St Manila Harbour Centre  Barangay128 Zone 10 1013Tondo Manila', '', '008-523-458-00000', '', '', 'Net 30', '', 'Narra Tanker Corporation', 51780.00, '2024-02-15 02:48:31'),
(298, '', 'New Davao Oil Mill, Inc.', 'New Davao Oil Mill, Inc.', 'Check', 'Km 14 Brgy Ilang Panacan, Davao City', 'Km 14 Brgy Ilang Panacan, Davao City', '005-985-198-000', '', '', 'Net 30', '@new_sgc', 'New Davao Oil Mill, Inc.', 621738.00, '2024-02-15 02:48:31'),
(299, '', 'Newspaper Paraphernalia, Inc.', 'Newspaper Paraphernalia, Inc.', 'Check', '', '', '', '', '', 'Net 30', NULL, 'Newspaper Paraphernalia, Inc.', 2500.00, '2024-02-15 02:48:31'),
(301, '', 'Nobalam Realty Inc.', '', 'Check', 'Lot1 Blk.3 C4 Road Phase IV G Longos Malabon City', '', '003-927-430-000', '', '', 'Net 30', NULL, 'Nobalam Realty Inc.', 14144.00, '2024-02-15 02:48:31'),
(303, NULL, 'Nutribeu Corporation', '', NULL, '#43 Old Nationa Highway', '', NULL, '', NULL, '30 days', NULL, 'Nutribeu Corporation', 11800.01, '2024-02-15 02:48:31'),
(304, '', 'OBP Inc.', '', 'Check', '', '', '', '', '', 'Net 30', NULL, 'OBP Inc.', 48275.00, '2024-02-15 02:48:31'),
(305, '', 'Ocean Tankers Corporation', 'Ocean Tankers Corporation', 'Bank Transfer', '22/f Taipan Place Bldg.F Ortigas F.Ortigas  Jr Road Ortigas Center', '22nd/f Taipan Place Bldg.Emerald', '000-187-071-000', '8397-1010', '', 'Net 30', '', 'Ocean Tankers Corporation', 302850.00, '2024-02-15 02:48:31'),
(312, '', 'Perk Electrical & Plumbing Works', '', 'Check', '#35 Matimtiman Street Teachers Village Diliman Quezon City', '#35 Matimtiman Street Teachers Village Diliman Quezon City', '136-539-274-000', '', '', 'Net 30', '@new_sgc', 'Perk Electrical & Plumbing Works', 3456807.00, '2024-02-15 02:48:31'),
(317, '', 'Philippine Aluminum Wheels Inc.', '', 'Check', 'Severina Diamond Industrial', '', '', '', '', 'Net 30', '', 'Philippine Aluminum Wheels Inc.', 134908.00, '2024-02-15 02:48:31'),
(318, '', 'Philippine Heart Center', 'Philippine Heart Center', '', 'East Avenue, Diliman Quezon City', 'East Avenue, Diliman Quezon City', '001-009-312-000', '925-2401 loc 4050', '', 'Net 30', '@new_sgc', 'Philippine Heart Center', 4793256.52, '2024-02-15 02:48:31'),
(320, '', 'Philippine Normal University', 'Philippine Normal University', 'Check', 'Taft Avenue, Manila', 'Philippine Normal University', '', '527-0377', '', 'Net 30', '', 'Philippine Normal University', 0.00, '2024-02-15 02:48:31'),
(321, NULL, 'Philmola Marketing Corporation', '', NULL, '7th Floor CMG Centre 12 J.', '', NULL, '', NULL, '30 days', NULL, 'Philmola Marketing Corporation', 2444.99, '2024-02-15 02:48:31'),
(324, '', 'Placer 8 Logistics Express Inc.', '', 'Check', '#31 Katuray Street,Puok 4 Lower Bicutan Paranaque City', '', '736-995-881-000', '', '', 'Net 30', '', 'Placer 8 Logistics Express Inc.', 315533.01, '2024-02-15 02:48:31'),
(327, '', 'Prime-Choice Agri, Inc', '', '', 'Brgy, Uguiao, Jaro, Leyte', '', '', '', '', '', '', 'Prime-Choice Agri, Inc', 0.00, '2024-02-15 02:48:31'),
(329, '', 'Prime Xynergies Food Corporation', 'Prime Xynergies Food Corporation', 'Check', 'Sitio San Jose Zone IV, Sta Cruz Davao del Sur', '', '000-011-821-000', '8416-2257', '', 'Net 30', '', 'Prime Xynergies Food Corporation', 92957.75, '2024-02-15 02:48:31'),
(330, '', 'Primex Coco Products, Inc.', 'Primex Coco Products, Inc.', 'Check', 'Bo.Mangilag, Candelaria Quezon City', 'Primex Coco Products, Inc.', '000-253-607-000', '416-2257', '', 'Net 30', '', 'Primex Coco Products, Inc.', 699347.80, '2024-02-15 02:48:31'),
(331, '', 'Primex Isle De Coco Inc.', 'Primex Isle De Coco Inc.', 'Check', 'Purok 5 Brgy Lidong Sto Domingo Albay', '', '008-388-161-000', '', '', 'Net 30', '', 'Primex Isle De Coco Inc.', 270672.75, '2024-02-15 02:48:31'),
(332, NULL, 'Print Town, Inc.', 'Print Town, Inc.', NULL, 'Print Town Inc.', '', NULL, '', NULL, 'Net 30', NULL, 'Print Town, Inc.', 50757.50, '2024-02-15 02:48:31'),
(333, '', 'Printwell Inc.', 'Printwell Inc.', 'Check', '38 Dansalan St. Barangka Ilaya,Mandaluyong City', '38 Dansallan St.,', '000-064-335-000', '8533-2388', '', 'Net 30', '', 'Printwell Inc.', 1500.00, '2024-02-15 02:48:31'),
(338, '', 'PV Tech Pte.,Ltd.', '', 'Check', 'Cavite Economic Zone II Rosario Cavite', '', '400-426-329-000', '', '', 'Net 15', '', 'PV Tech Pte.,Ltd.', 0.00, '2024-02-15 02:48:31'),
(340, '', 'QUADX Inc.', '', 'Check', 'GF Allegro Center', '', '', '', '', 'Net 30', '', 'QUADX Inc.', 106870.00, '2024-02-15 02:48:31'),
(342, '', 'Ransom Retail Inc.', '', 'Check', '9th Floor CMG Centre 12 J.', '', '', '', '', 'Net 30', '', 'Ransom Retail Inc.', 1455.00, '2024-02-15 02:48:31'),
(343, '', 'Rapid Movers & Forwardes Co Inc.', '', 'Check', 'Bo Sala Cabuyao City Laguna', 'Bo Sala Cabuyao City Laguna', '000-635-932-000', '', '', 'Net 30', '@new_sgc', 'Rapid Movers & Forwardes Co Inc.', 803873.46, '2024-02-15 02:48:31'),
(344, '', 'RDB Kernstock Corporation', '', 'Check', 'Blk 8 Lot 4, Bethel Lower', '', '', '', '', 'Net 30', '', 'RDB Kernstock Corporation', 0.00, '2024-02-15 02:48:31'),
(346, '', 'Restored Energy Development Corp.', 'Restored Energy Development Corp.', 'Check', 'Metercore compound National Road Tunasan Muntinlupa City', '', '006-930-473-000', '', '', 'Net 30', NULL, 'Restored Energy Development Corp.', 321150.27, '2024-02-15 02:48:31'),
(350, '', 'RMR Electric Corporation', 'RMR Electric Corporation', 'Check', '20 Sumulong highway Brgy Mayamot Antipolo City', '', '000-515-350-000', '8681-7777', '', 'Net 90', '', 'RMR Electric Corporation', 1893759.00, '2024-02-15 02:48:31'),
(353, '', 'Rowell Industrial Corporation', '', 'Check', '#100 P. Rosales St, Kaliwa Sta Ana Pateros Metro Manila', '', '000-244-906-000', '', '', 'Net 30', '', 'Rowell Industrial Corporation', 65060.00, '2024-02-15 02:48:31'),
(354, '', 'Rowell Lithography and Metal Closure,Inc.', 'Rowell Lithography and Metal Closure,Inc.', 'Check', 'P.E Antonio Street, Bo. Ugong Pasig City', 'PE Antonio St., cor E.Rodriguez,', '000-281-838-000', '8671-9958', '', 'Net 30', '', 'Rowell Lithography and Metal Closure,Inc.', 159359.50, '2024-02-15 02:48:31'),
(363, '', 'Scad Services (s) Pte.Ltd.', '', 'Bank Transfer', 'Blk.23, Phase IV Expansion Cavite Economic Zone Rosario Cavite', '', '005-064-092-000', '', '', 'Net 15', '', 'Scad Services (s) Pte.Ltd.', 139003.00, '2024-02-15 02:48:31'),
(365, '', 'Seabass Carriers, Inc.', '', 'Check', '#20 Dona Juana Rodriguez Avenue Brgy Potrero Malabon City', '', '205-548-795-000', '', '', 'Net 30', '', 'Seabass Carriers, Inc.', 72400.00, '2024-02-15 02:48:31'),
(366, '', 'Seaoil Foundation, Inc.', '', 'Check', '22F The Taipan Place F.', '', '', '', '', 'Net 30', '', 'Seaoil Foundation, Inc.', 0.00, '2024-02-15 02:48:31'),
(368, '', 'Seaoil Philippines Inc.', '', 'Bank Transfer', '22F The Taipan Place F Ortigas Jr Road Ortigas Center Pasig City', '', '005-054-970-000', '', '', 'Net 30', '', 'Seaoil Philippines Inc.', 30036.00, '2024-02-15 02:48:31'),
(370, '', 'Shinryo Phils. Company, Inc.', 'Shinryo Phils. Company, Inc.', 'Check', 'Room 403-406 One Corporate', 'Rm.404 One Corporate Plaza', '', '8812-0083', '', 'Net 30', '', 'Shinryo Phils. Company, Inc.', 129397.60, '2024-02-15 02:48:31'),
(371, '', 'Shogun Ships Co.,Inc.', 'Shogun Ships Co.,Inc.', 'Check', '2601 Antel Global Corporate Center,Julia Vargas Ave.,Ortigas Center,Pasig City', 'Shogun Ships Co., Inc.', '222-363-537-00000', '8470-9419', '', 'Net 30', '123', 'Shogun Ships Co.,Inc.', 22675.14, '2024-02-15 02:48:31'),
(374, '', 'Sindangan Bay Mining Corporation', '', 'Check', '#59 C3 Road Cor. Dagat-Dagatan Ave.,Kalookan City', '', '', '', '', 'Net 30', '', 'Sindangan Bay Mining Corporation', 29433.00, '2024-02-15 02:48:31'),
(376, '', 'SL Harbor Bulk Terminal Corporation', 'SL Harbor Bulk Terminal Corp.', 'Check', 'Blk 4 Lot 10 & 11 Manila Harbour Centre North Harbor Brgy 128 Zone 10Tondo Manila 1012', 'SL Harbor Bulk Terminal Corp.', '008-375-221-000', '', '', 'Net 30', '', 'SL Harbor Bulk Terminal Corporation', 418426.71, '2024-02-15 02:48:31'),
(377, '', 'SL Mariveles Drydocking & Shipyard Corporation', 'SL MARIVELES DRYDOCKING AND SHIPYARD CORP', 'Check', 'Baseco Compound Luzon Avenue Mariveles Bataan 2105', 'SL MARIVELES DRYDOCKING AND SHIPYARD CORP', '008-957-616-00000', '', '', 'Net 30', '', 'SL Mariveles Drydockin & Shipyard Corp.', 0.00, '2024-02-15 02:48:31'),
(378, '', 'Slord Development Corporation', 'Slord Development Corporation', 'Check', 'FFDA Fishport Complex,Navotas City', '', '000-244-412-000', '', '', 'Net 30', '', 'Slord Development Corporation', 68673.00, '2024-02-15 02:48:31'),
(380, '', 'SMC Shipping and Lighterage Corporation', 'SMC Shipping and Lighterage Corp.', 'Check', 'Blk.9 Lot 4-6 Fernando St.,Cor Francisco Serro St Manila Harbour Centre Barangay128 Zone 10 1013 Tondo Manila', 'VIP Bldg.1440 Roxas Blvd. cor Nuestra', '000-190-742-000', '8550-3638', '', 'Net 30', NULL, 'SMC Shipping and Lighterage Corporationn', 8839792.34, '2024-02-15 02:48:31'),
(382, NULL, 'St .Rafael Dev\'t Corporation', '', NULL, '733 Wood St. Pasay City', '', NULL, '', NULL, '', NULL, 'St .Rafael Dev\'t Corporation', 17747.02, '2024-02-15 02:48:31'),
(384, '', 'Static Power Philippines,Inc.', '', 'Check', 'Unit 3D #5 Gen.Lim St.,San Antonio Village,Pasig City', '', '007-298-994-000', '', '', 'Net 60', '', 'Static Power Philippines,Inc.', 343829.00, '2024-02-15 02:48:31'),
(385, '', 'STK-Prime Real Property Development Corp.', '', 'Check', '681 Aurora Blvd. New Manila Quezon City', '', '', '', '', 'Net 30', '', 'STK-Prime Real Property Development Corp.', 13667.50, '2024-02-15 02:48:31'),
(388, '', 'Sun Moon Packaging Contractor Inc.', '', 'Check', '745 C.Raymundo Avenue, Maybunga Pasig City', '', '008-239-015-000', '', '', 'Net 30', '', 'Sun Moon Packaging Contractor Inc.', 10815.00, '2024-02-15 02:48:31'),
(389, '', 'Super Prime Holdings,Inc.', '', 'Check', 'Ecotower Bldg.,lot 5 Block 2,32nd Street cor 9th Avenue Bonifacio Global city,Taguig', '', '007-339-199-000', '8556-0653', '', 'Net 30', '', 'Super Prime Holdings,Inc.', 36113.00, '2024-02-15 02:48:31'),
(393, NULL, 'Sysnet United Technology Inc.', '', NULL, 'Unit 2J #3 Rd.1 One', '', NULL, '', NULL, '', NULL, 'Sysnet United Technology Inc.', 1721.00, '2024-02-15 02:48:31'),
(394, '', 'Tacloban Oil Mills, Inc.', 'Tacloban Oil Mills, Inc.', 'Check', 'Brgy. Opong, Tolosa Leyte', '', '006-167-917-000', '', '', 'Net 30', '', 'Tacloban Oil Mills, Inc.', 71544.00, '2024-02-15 02:48:31'),
(395, '', 'Tara Asset & Holdings Corporation', '', 'Check', '2300 PIFCO Bldg. Pasong Tamo Ext Makati City', '', '003-975-198-000', '', '', 'Net 30', '', 'Tara Asset & Holdings Corporation', 0.00, '2024-02-15 02:48:31'),
(396, '', 'The Infinity Condominium Corp.', '', 'Check', '25th Street Bonifacio', '', '', '', '', 'Net 30', '', 'The Infinity Condominium Corp.', 0.00, '2024-02-15 02:48:31'),
(409, '', 'Tradesphere Industrial Commodities,Inc.', '', 'Check', '', '', '', '', '', 'Net 30', NULL, 'Tradesphere Industrial Commodities,Inc.', 2985.00, '2024-02-15 02:48:31'),
(411, NULL, 'Triple E 2008 Builders Inc.', '', NULL, 'Unit 833 City & Land Mega', '', NULL, '', NULL, '', NULL, 'Triple E 2008 Builders Inc.', 949042.50, '2024-02-15 02:48:31'),
(413, '', 'UNICC Home Works', '', 'Check', 'Guiguinto Bulacan', '', '', '', '', 'Net 30', '', 'UNICC Home Works', 735572.50, '2024-02-15 02:48:31'),
(418, '', 'Valerie Products Mfg.,Inc.', 'Valerie Products Mfg.,Inc.', 'Check', 'PTC Complex Bo Maduya,Carmona Cavite', '', '000-281-432-000', '', '', 'Net 30', '', 'Valerie Products Mfg.,Inc.', 116885.00, '2024-02-15 02:48:31'),
(419, '', 'Varitech Design & Manufacturing Corp.', 'Varitech Design & Manufacturing Corp.', 'Check', '1 Industrial Road Km16 Severina Ind\'s Estate Bicutan Paranaque City', '', '004-479-409-000', '8823-3439', '', 'Net 60', '', 'Varitech Design & Manufacturing Corp.', 22536.00, '2024-02-15 02:48:31'),
(420, '', 'VCC-Vepac Construction Corporation', '', 'Check', 'Blk 1 Lot 22 Ph-5 Brokeside Lane Brgy San Francisco Gen Trias', '', '007-422-312-000', '', '', 'Net 30', '', 'VCC-Vepac Construction Corporation', 202861.00, '2024-02-15 02:48:31'),
(424, '', 'Wu Kong Singapore Pte.,Ltd.', '', 'Bank Transfer', 'Block 25 A & B Phase IV Expansion Cavite Economic Zone,Rosario Cavite', '', '004-114-798-000', '', '', 'Net 15', NULL, 'Wu Kong Singapore Pte.,Ltd.', 8612640.08, '2024-02-15 02:48:31'),
(425, '', 'Flaog Construction', NULL, 'Check', '#06 Romantic Village Ibayo Sta Ana, Taguig City', '#06 Romantic Village Ibayo Sta Ana, Taguig City', '183-124-371-000', '', '', 'Cash on delivery', '@new_sgc', 'Flaog Construction', 38820.00, '2024-02-19 01:49:32'),
(426, '', 'Genequipt Sales', NULL, 'Cash', 'WEZ Bldg 177 Congressional Ave cor Sinagtala Bahay Toro 1106 Quezon City', NULL, '197-463-173-000', '', '', 'Cash on delivery', NULL, 'Genequipt Sales', 4500.00, '2024-02-24 07:12:01'),
(432, '', 'Topkick Movers Corporation', NULL, 'Check', '23 E Rodriguez Avenue,Dona Josefa Quezon City', NULL, '001-627-930-000', '', '', 'Net 30', NULL, 'Topkick Movers Corporation', NULL, '2024-02-28 02:51:34'),
(434, '', 'Kabayan Hotel Pasay', NULL, 'Check', '2878 Zamora st.,Pasay City', NULL, '001-218-911-006', '', '', 'Net 30', NULL, 'Kabayan Hotel Pasay', 28865.00, '2024-03-05 08:10:29'),
(435, '', 'Exergy Phils.Corp.', NULL, 'Check', '648 Remedios St.,Malate Manila', NULL, '', '', '', 'Net 30', NULL, 'Exergy Phils.Corp.', 5800.00, '2024-03-12 05:32:22'),
(436, '', 'Global Compak,Inc.', NULL, 'Check', 'Sampalocan Road,Brgy.San Roque Sto Tomas, Batangas', NULL, '', '043-7844874', '', '', NULL, 'Global Compak,Inc.', 500.00, '2024-03-16 07:50:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customerID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=437;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
