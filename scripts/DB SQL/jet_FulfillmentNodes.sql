-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 03, 2017 at 04:18 PM
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
-- Table structure for table `jet_FulfillmentNodes`
--

CREATE TABLE IF NOT EXISTS `jet_FulfillmentNodes` (
  `ShopifyStore` varchar(255) NOT NULL,
  `FulfillmentNodeID` varchar(255) NOT NULL,
  `FulfillmentName` varchar(255) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `jet_FulfillmentNodes`
--

INSERT INTO `jet_FulfillmentNodes` (`ShopifyStore`, `FulfillmentNodeID`, `FulfillmentName`, `updated_at`, `created_at`) VALUES
('craigsirk.myshopify.com', '1234', 'test', 0, 0),
('craigsirk.myshopify.com', 'a6facc2dd89046c7b5b5ce27e8ce9353', 'test', 0, 0),
('craigtesting.myshopify.com', 'a2d98e202c3d491b87e4a08283610461', 'Craigs House ', 1491073100, 1491061263),
('craigtesting.myshopify.com', 'a6facc2dd89046c7b5b5ce27e8ce9353', 'Test Dropshipper', 1491056186, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `jet_FulfillmentNodes`
--
ALTER TABLE `jet_FulfillmentNodes`
  ADD PRIMARY KEY (`ShopifyStore`,`FulfillmentNodeID`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `jet_FulfillmentNodes`
--
ALTER TABLE `jet_FulfillmentNodes`
  ADD CONSTRAINT `jet_FulfillmentNodes_ibfk_1` FOREIGN KEY (`ShopifyStore`) REFERENCES `shopify_store` (`shop`),
  ADD CONSTRAINT `jet_FulfillmentNodes_ibfk_2` FOREIGN KEY (`ShopifyStore`) REFERENCES `shopify_store` (`shop`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
