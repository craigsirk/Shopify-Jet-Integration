-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 03, 2017 at 03:37 PM
-- Server version: 5.6.35-81.0
-- PHP Version: 5.5.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shopify_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `jet_cpsiaCautionaryStatements`
--

CREATE TABLE IF NOT EXISTS `jet_cpsiaCautionaryStatements` (
  `id` int(11) NOT NULL DEFAULT '0',
  `cpsia_cautionary_statements` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `jet_cpsiaCautionaryStatements`
--

INSERT INTO `jet_cpsiaCautionaryStatements` (`id`, `cpsia_cautionary_statements`) VALUES
(1, 'no warning applicable'),
(2, 'choking hazard small parts'),
(3, 'choking hazard is a small ball'),
(4, 'choking hazard is a marble'),
(5, 'choking hazard contains a small ball'),
(6, 'choking hazard contains a marble'),
(7, 'choking hazard balloon');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `jet_cpsiaCautionaryStatements`
--
ALTER TABLE `jet_cpsiaCautionaryStatements`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
