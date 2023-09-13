-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 13, 2023 at 06:55 AM
-- Server version: 8.0.33
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `schoolstuff`
--

-- --------------------------------------------------------

--
-- Table structure for table `resto`
--

CREATE TABLE `resto` (
  `id` int NOT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `harga` int DEFAULT NULL,
  `type` varchar(7) DEFAULT NULL,
  `status` varchar(30) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `resto`
--

INSERT INTO `resto` (`id`, `nama`, `harga`, `type`, `status`, `img`) VALUES
(1, 'spaghet', 200000, 'Makanan', 'Tidak tersedia', 'uploads/PALPATINE.png'),
(2, 'Rice', 1000, 'Makanan', 'Tersedia', 'uploads/mat.jpg'),
(3, 'memoriesbrokenthetruthgoesunspoken', 50000, 'Makanan', 'Tersedia', 'uploads/sam.jpg'),
(4, 'memesthednaofthesoul', 66666, 'Makanan', 'Tersedia', 'uploads/monsoon.jpg'),
(9, 'Something to eat', 123, 'Makanan', 'Tidak tersedia', 'uploads/steeltex.jpg'),
(11, 'aslkdjlaskd', 123123, 'Makanan', 'Tidak tersedia', 'uploads/UAC_light_purp.jpg'),
(12, 'f>d>f+d>2', 120, 'Makanan', 'Tersedia', 'uploads/kazuyawhite.jpg'),
(18, 'Pizz(a)', 292929, 'Makanan', 'Tersedia', 'uploads/redditsave.com_tktzc240l4da1.gif'),
(19, 'Rizz(a)', 69696, 'Minuman', 'Tersedia', 'uploads/postermaybefinished.jpg'),
(20, 'fjgfjfj', 4353, 'Makanan', 'Tersedia', 'uploads/leathertex.jpg'),
(21, 'waltuh', 12938, 'Makanan', 'Tersedia', 'uploads/walter-white-ballin.jpg'),
(26, 'Boomerang', 12000, 'Makanan', 'Tersedia', 'uploads/Icon.png'),
(27, 'Rand Flynn', 1000, 'Makanan', 'Tersedia', 'uploads/RandFlynn.png');

-- --------------------------------------------------------

--
-- Table structure for table `restoacc`
--

CREATE TABLE `restoacc` (
  `id` int NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `pfp` varchar(255) DEFAULT NULL,
  `privilege` varchar(255) DEFAULT NULL,
  `orders` varchar(255) DEFAULT '',
  `balance` int DEFAULT '0',
  `transactions` varchar(550) DEFAULT '',
  `realname` varchar(100) DEFAULT '',
  `address` varchar(255) DEFAULT '',
  `phone` varchar(9) DEFAULT '',
  `email` varchar(35) DEFAULT '',
  `gender` varchar(6) DEFAULT '',
  `position` varchar(25) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `restoacc`
--

INSERT INTO `restoacc` (`id`, `username`, `password`, `pfp`, `privilege`, `orders`, `balance`, `transactions`, `realname`, `address`, `phone`, `email`, `gender`, `position`) VALUES
(2, 'Grey', 'admin', 'kazuyawhite.jpg', 'admin', '', 0, '', 'Seth', 'Earth', '69696969', 'sethman@gmail.com', 'male', 'manager'),
(6, 'ben', 'ten', 'mike.jpg', 'user', '#3q1#4q1#26q1', 0, '', 'tennyson', '', '', '', 'undef', 'customer'),
(7, 'bruhman', 'man', 'monsoon.jpg', 'user', '', 0, '', '', '', '', '', 'undef', 'customer'),
(8, 'guest', 'guest', NULL, 'user', '', 0, '', '', '', '', '', 'undef', 'customer'),
(9, 'thisisme', 'thisisapw', 'yodium.png', 'user', '', 0, '', '', '', '', '', 'undef', 'customer'),
(10, 'Grey', 'user', NULL, 'user', '', 0, '', '', '', '', '', 'undef', 'customer'),
(12, 'Newaccount', 'new', NULL, 'user', '', 0, '', '', '', '', '', 'undef', 'customer'),
(13, '123', '123', NULL, 'user', '', 0, '', '', '', '', '', 'undef', 'customer');

-- --------------------------------------------------------

--
-- Table structure for table `restoorders`
--

CREATE TABLE `restoorders` (
  `id` int NOT NULL,
  `ordernum` varchar(255) DEFAULT NULL,
  `status` varchar(25) DEFAULT 'pending',
  `paymethod` varchar(25) DEFAULT NULL,
  `paystatus` varchar(25) DEFAULT 'unpaid',
  `employeeId` int DEFAULT NULL,
  `tablenum` int DEFAULT NULL,
  `ordertime` datetime DEFAULT NULL,
  `orders` varchar(550) DEFAULT '',
  `customerId` int DEFAULT NULL,
  `payamount` int DEFAULT NULL,
  `completion` varchar(25) DEFAULT 'In Process'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `restoorders`
--

INSERT INTO `restoorders` (`id`, `ordernum`, `status`, `paymethod`, `paystatus`, `employeeId`, `tablenum`, `ordertime`, `orders`, `customerId`, `payamount`, `completion`) VALUES
(28, 'tn_28', 'pending', 'card', 'paid', NULL, 1, '2023-09-13 08:42:52', '######3q1qReady#4q1qReady', 2, 116666, 'Ready'),
(29, 'tn_29', 'accepted', 'cash', 'unpaid', 2, 3, '2023-09-13 08:44:32', '################1q1qRejected#2q3qReady', 2, 203000, 'Ready');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `resto`
--
ALTER TABLE `resto`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `restoacc`
--
ALTER TABLE `restoacc`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `restoorders`
--
ALTER TABLE `restoorders`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `resto`
--
ALTER TABLE `resto`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `restoacc`
--
ALTER TABLE `restoacc`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `restoorders`
--
ALTER TABLE `restoorders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
