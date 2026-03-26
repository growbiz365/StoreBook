-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 26, 2026 at 04:53 AM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u173806351_rahmantradersd`
--

-- --------------------------------------------------------

--
-- Table structure for table `banks`
--

CREATE TABLE `banks` (
  `bank_id` int(11) NOT NULL,
  `bank_name` varchar(30) NOT NULL,
  `account_number` varchar(50) NOT NULL,
  `bank_type_id` tinyint(4) NOT NULL,
  `opening_balance` double NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `user_id` int(11) NOT NULL,
  `opening_date` date NOT NULL,
  `account_code` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `banks`
--

INSERT INTO `banks` (`bank_id`, `bank_name`, `account_number`, `bank_type_id`, `opening_balance`, `status`, `user_id`, `opening_date`, `account_code`) VALUES
(1, 'CASH COUNTER', '', 1, 0, 1, 49, '2025-10-11', 1101),
(2, 'SAQIB KHAN EASY PAISA', '03119994452', 1, 0, 1, 49, '2025-10-14', 1102),
(3, 'SANAULLAH EASY PAISA', '03159757417', 1, 0, 1, 49, '2025-10-14', 1103),
(4, 'SAQIB KHAN BANK ALHABIB ACCOUN', '20360095004522010', 1, 0, 1, 49, '2025-10-14', 1104),
(5, 'REHMAT TILES AND SANITARY STOR', '07120111658023', 1, 0, 1, 49, '2025-10-14', 1105),
(6, 'HABIB METRO POLITAN', '', 1, 0, 1, 49, '2025-10-15', 1106);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `banks`
--
ALTER TABLE `banks`
  ADD PRIMARY KEY (`bank_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `banks`
--
ALTER TABLE `banks`
  MODIFY `bank_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
