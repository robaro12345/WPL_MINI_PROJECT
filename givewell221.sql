-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 16, 2025 at 07:43 PM
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
-- Database: `givewell221`
--
CREATE DATABASE IF NOT EXISTS `givewell221` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `givewell221`;

-- --------------------------------------------------------

--
-- Table structure for table `approve`
--

DROP TABLE IF EXISTS `approve`;
CREATE TABLE `approve` (
  `ApproveID` int(11) NOT NULL,
  `AdminID` int(11) NOT NULL,
  `CampaignID` int(11) NOT NULL,
  `State` varchar(50) NOT NULL,
  `Approval_Date` timestamp NOT NULL DEFAULT current_timestamp(),
  `Comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `approve`
--

TRUNCATE TABLE `approve`;
-- --------------------------------------------------------

--
-- Table structure for table `campaign`
--

DROP TABLE IF EXISTS `campaign`;
CREATE TABLE `campaign` (
  `CID` int(11) NOT NULL,
  `CRID_USER` int(11) DEFAULT NULL,
  `CRID_ORG` int(11) DEFAULT NULL,
  `Current_Amount` decimal(10,2) DEFAULT 0.00,
  `Name` varchar(100) NOT NULL,
  `Start_Date` date NOT NULL,
  `End_Date` date NOT NULL,
  `Description` text NOT NULL,
  `Goal` decimal(10,2) NOT NULL,
  `Approval_Status` tinyint(1) DEFAULT 0,
  `Creation_Date` timestamp NOT NULL DEFAULT current_timestamp(),
  `Status` varchar(20) DEFAULT 'ACTIVE',
  `Category` varchar(256) DEFAULT NULL
) ;

--
-- Truncate table before insert `campaign`
--

TRUNCATE TABLE `campaign`;
--
-- Dumping data for table `campaign`
--

INSERT INTO `campaign` (`CID`, `CRID_USER`, `CRID_ORG`, `Current_Amount`, `Name`, `Start_Date`, `End_Date`, `Description`, `Goal`, `Approval_Status`, `Creation_Date`, `Status`, `Category`) VALUES
(1, 2, NULL, 0.00, 'Save the Oceans', '2025-01-05', '2025-03-01', 'A campaign to raise awareness and funds for ocean conservation.', 5000.00, 0, '2025-03-28 11:37:26', 'ACTIVE', NULL),
(2, 2, NULL, 0.00, 'Feed the Homeless', '2025-03-28', '2025-05-28', 'Providing meals for homeless people in urban areas', 8000.00, 0, '2025-03-28 11:39:11', 'ACTIVE', NULL),
(3, 2, NULL, 0.00, 'Tech for Schools', '2025-02-14', '2025-04-14', 'Donating laptops to underprivileged schools', 25000.00, 0, '2025-03-28 11:40:00', 'ACTIVE', NULL),
(4, 2, NULL, 0.00, 'Disaster Relief Fund', '2025-03-13', '2025-04-18', 'Emergency aid for disaster-affected areas', 50000.00, 0, '2025-03-28 11:43:59', 'ACTIVE', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `donation`
--

DROP TABLE IF EXISTS `donation`;
CREATE TABLE `donation` (
  `DonationID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `CampaignID` int(11) NOT NULL,
  `Wallet_Add` varchar(100) NOT NULL,
  `Trans_Hash` varchar(100) NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `Amount` decimal(10,2) NOT NULL,
  `Status` varchar(20) DEFAULT 'COMPLETED'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `donation`
--

TRUNCATE TABLE `donation`;
-- --------------------------------------------------------

--
-- Table structure for table `email`
--

DROP TABLE IF EXISTS `email`;
CREATE TABLE `email` (
  `EmailID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Primary_Email` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `email`
--

TRUNCATE TABLE `email`;
--
-- Dumping data for table `email`
--

INSERT INTO `email` (`EmailID`, `UserID`, `Email`, `Primary_Email`) VALUES
(1, 1, 'Go@fund.com', 1),
(2, 2, 'user@yahoo.com', 1);

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
  `MessageID` int(11) NOT NULL,
  `User_ID` int(11) DEFAULT NULL,
  `Campaign_ID` int(11) NOT NULL,
  `Message` text NOT NULL,
  `Created_At` timestamp NOT NULL DEFAULT current_timestamp(),
  `Updated_At` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Status` varchar(20) DEFAULT 'ACTIVE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `message`
--

TRUNCATE TABLE `message`;
-- --------------------------------------------------------

--
-- Table structure for table `organisation`
--

DROP TABLE IF EXISTS `organisation`;
CREATE TABLE `organisation` (
  `OrgID` int(11) NOT NULL,
  `Wallet_ID` varchar(100) NOT NULL,
  `NAME` varchar(100) NOT NULL,
  `Creation_Date` date NOT NULL,
  `TYPE` varchar(50) NOT NULL,
  `Status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `organisation`
--

TRUNCATE TABLE `organisation`;
-- --------------------------------------------------------

--
-- Table structure for table `preferences`
--

DROP TABLE IF EXISTS `preferences`;
CREATE TABLE `preferences` (
  `PreferenceID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Preferences` text DEFAULT NULL,
  `Last_Updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `preferences`
--

TRUNCATE TABLE `preferences`;
-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

DROP TABLE IF EXISTS `resources`;
CREATE TABLE `resources` (
  `ResourceID` int(11) NOT NULL,
  `CampID` int(11) NOT NULL,
  `TYPE` varchar(50) NOT NULL,
  `URL` varchar(255) NOT NULL,
  `Upload_Date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `resources`
--

TRUNCATE TABLE `resources`;
-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `Fname` varchar(50) NOT NULL,
  `Mname` varchar(50) DEFAULT NULL,
  `Lname` varchar(50) NOT NULL,
  `Wallet_Address` varchar(100) DEFAULT NULL,
  `Creation_Date` date NOT NULL,
  `Role` varchar(50) NOT NULL DEFAULT 'Donor',
  `Password` varchar(255) NOT NULL,
  `Status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `users`
--

TRUNCATE TABLE `users`;
--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `Fname`, `Mname`, `Lname`, `Wallet_Address`, `Creation_Date`, `Role`, `Password`, `Status`) VALUES
(1, 'ad', 'm', 'in', 'qwertyuiopasdfghjklzxcvbnm123456789', '2025-03-28', 'Admin', '$2y$10$Zr5Ot8ArD7EAv3vQAkqk6OeU735NsTXHJIR1s2.rY8n1xpqFVQ.My', 1),
(2, 'u', 's', 'er', 'qsxcdwertgbnjiu432123456789olkp\'/', '2025-03-28', 'Campaigner', '$2y$10$dJtfl1ec4t8XVlTRaF1.3et6tbdBrD88y7/8ocCyvkh2AFISMJw1a', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `approve`
--
ALTER TABLE `approve`
  ADD PRIMARY KEY (`ApproveID`),
  ADD KEY `AdminID` (`AdminID`),
  ADD KEY `CampaignID` (`CampaignID`);

--
-- Indexes for table `campaign`
--
ALTER TABLE `campaign`
  ADD PRIMARY KEY (`CID`),
  ADD KEY `CRID_USER` (`CRID_USER`),
  ADD KEY `CRID_ORG` (`CRID_ORG`),
  ADD KEY `idx_status` (`Status`),
  ADD KEY `idx_approval` (`Approval_Status`);

--
-- Indexes for table `donation`
--
ALTER TABLE `donation`
  ADD PRIMARY KEY (`DonationID`),
  ADD KEY `idx_campaign` (`CampaignID`),
  ADD KEY `idx_user` (`UserID`);

--
-- Indexes for table `email`
--
ALTER TABLE `email`
  ADD PRIMARY KEY (`EmailID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `idx_email` (`Email`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`MessageID`),
  ADD KEY `User_ID` (`User_ID`),
  ADD KEY `idx_campaign` (`Campaign_ID`);

--
-- Indexes for table `organisation`
--
ALTER TABLE `organisation`
  ADD PRIMARY KEY (`OrgID`),
  ADD UNIQUE KEY `Wallet_ID` (`Wallet_ID`),
  ADD KEY `idx_wallet` (`Wallet_ID`);

--
-- Indexes for table `preferences`
--
ALTER TABLE `preferences`
  ADD PRIMARY KEY (`PreferenceID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`ResourceID`),
  ADD KEY `CampID` (`CampID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Wallet_Address` (`Wallet_Address`),
  ADD KEY `idx_wallet` (`Wallet_Address`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `approve`
--
ALTER TABLE `approve`
  MODIFY `ApproveID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `campaign`
--
ALTER TABLE `campaign`
  MODIFY `CID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donation`
--
ALTER TABLE `donation`
  MODIFY `DonationID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email`
--
ALTER TABLE `email`
  MODIFY `EmailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `MessageID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `organisation`
--
ALTER TABLE `organisation`
  MODIFY `OrgID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `preferences`
--
ALTER TABLE `preferences`
  MODIFY `PreferenceID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `ResourceID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `approve`
--
ALTER TABLE `approve`
  ADD CONSTRAINT `approve_ibfk_1` FOREIGN KEY (`AdminID`) REFERENCES `users` (`UserID`),
  ADD CONSTRAINT `approve_ibfk_2` FOREIGN KEY (`CampaignID`) REFERENCES `campaign` (`CID`) ON DELETE CASCADE;

--
-- Constraints for table `campaign`
--
ALTER TABLE `campaign`
  ADD CONSTRAINT `campaign_ibfk_1` FOREIGN KEY (`CRID_USER`) REFERENCES `users` (`UserID`) ON DELETE SET NULL,
  ADD CONSTRAINT `campaign_ibfk_2` FOREIGN KEY (`CRID_ORG`) REFERENCES `organisation` (`OrgID`) ON DELETE SET NULL;

--
-- Constraints for table `donation`
--
ALTER TABLE `donation`
  ADD CONSTRAINT `donation_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE SET NULL,
  ADD CONSTRAINT `donation_ibfk_2` FOREIGN KEY (`CampaignID`) REFERENCES `campaign` (`CID`) ON DELETE CASCADE;

--
-- Constraints for table `email`
--
ALTER TABLE `email`
  ADD CONSTRAINT `email_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`UserID`) ON DELETE SET NULL,
  ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`Campaign_ID`) REFERENCES `campaign` (`CID`) ON DELETE CASCADE;

--
-- Constraints for table `preferences`
--
ALTER TABLE `preferences`
  ADD CONSTRAINT `preferences_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `resources`
--
ALTER TABLE `resources`
  ADD CONSTRAINT `resources_ibfk_1` FOREIGN KEY (`CampID`) REFERENCES `campaign` (`CID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
