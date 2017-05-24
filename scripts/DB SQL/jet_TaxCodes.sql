-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 03, 2017 at 03:57 PM
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
-- Table structure for table `jet_TaxCodes`
--

CREATE TABLE IF NOT EXISTS `jet_TaxCodes` (
  `id` int(11) NOT NULL DEFAULT '0',
  `TaxCode` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `jet_TaxCodes`
--

INSERT INTO `jet_TaxCodes` (`id`, `TaxCode`) VALUES
(0, 'Toilet Paper'),
(1, 'Thermometers'),
(2, 'Sweatbands'),
(3, 'SPF Suncare Products'),
(4, 'Sparkling Water'),
(5, 'Smoking Cessation'),
(6, 'Shoe Insoles'''),
(7, 'Safety Clothing'),
(8, 'Pet Foods'),
(9, 'Paper Products'),
(10, 'OTC Pet Meds'),
(11, 'OTC Medication'),
(12, 'Oral Care Products'),
(13, 'Non-Motorized Boats'),
(14, 'Non Taxable Product'),
(15, 'Mobility Equipment'),
(16, 'Medicated Personal Care Items'),
(17, 'Infant Clothing'),
(18, 'Helmets'),
(19, 'Handkerchiefs'),
(20, 'Generic Taxable Product'),
(21, 'General Grocery Items'),
(22, 'General Clothing'),
(23, 'Fluoride Toothpaste'),
(24, 'Feminine Hygiene Products'),
(25, 'Durable Medical Equipment'),
(26, 'Drinks under 50 Percent Juice'),
(27, 'Disposable Wipes'),
(28, 'Disposable Infant Diapers'),
(29, 'Dietary Supplements'),
(30, 'Diabetic Supplies'),
(31, 'Costumes'),
(32, 'Contraceptives'),
(33, 'Contact Lens Solution'),
(34, 'Carbonated Soft Drinks'),
(35, 'Car Seats'),
(36, 'Candy with Flour'),
(37, 'Candy'),
(38, 'Breast Pumps'),
(39, 'Braces and Supports'),
(40, 'Bottled Water Plain'),
(41, 'Beverages with 51 to 99 Percent Juice'),
(42, 'Bathing Suits'),
(43, 'Bandages and First Aid Kits'),
(44, 'Baby Supplies'),
(45, 'Athletic Clothing'),
(46, 'Adult Diapers');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `jet_TaxCodes`
--
ALTER TABLE `jet_TaxCodes`
  ADD PRIMARY KEY (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
