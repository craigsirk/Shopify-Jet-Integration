-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 03, 2017 at 04:21 PM
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
-- Table structure for table `shopify_stores`
--

CREATE TABLE IF NOT EXISTS `shopify_stores` (
  `shop` varchar(255) NOT NULL,
  `SetupComplete` tinyint(1) NOT NULL DEFAULT '0',
  `oauth_token` varchar(255) NOT NULL,
  `jet_api_key` varchar(255) NOT NULL,
  `jet_pass` varchar(255) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `shopify_stores`
--

INSERT INTO `shopify_stores` (`shop`, `SetupComplete`, `oauth_token`, `jet_api_key`, `jet_pass`, `created_at`, `updated_at`) VALUES
('craigsirk.myshopify.com', 0, '58dbfacad49671284c4df2ec8a183e71', '', '', 1489974075, 1489974075),
('craigtesting.myshopify.com', 1, '95cc756b722c123082eff8afdb273746', '1276228E4DD93CEEDA8B1D9EAC293794C71C7CFD', 'KXKvTVNQxQwY38WM/6PiXfTArxne/TfHFJovk9DgKue7', 1489975514, 1492707614);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `shopify_stores`
--
ALTER TABLE `shopify_stores`
  ADD PRIMARY KEY (`shop`),
  ADD UNIQUE KEY `shop` (`shop`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
