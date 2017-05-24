-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 03, 2017 at 04:24 PM
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
-- Table structure for table `join_JetTaxonomy_ShopifyStores`
--

CREATE TABLE IF NOT EXISTS `join_JetTaxonomy_ShopifyStores` (
  `shopifyStore` varchar(255) NOT NULL,
  `JetTaxonomyId` int(11) NOT NULL,
  `ShopifyType` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `join_JetTaxonomy_ShopifyStores`
--

INSERT INTO `join_JetTaxonomy_ShopifyStores` (`shopifyStore`, `JetTaxonomyId`, `ShopifyType`) VALUES
('craigtesting.myshopify.com', 1000000, 'Book');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `join_JetTaxonomy_ShopifyStores`
--
ALTER TABLE `join_JetTaxonomy_ShopifyStores`
  ADD PRIMARY KEY (`shopifyStore`,`JetTaxonomyId`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
