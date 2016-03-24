-- phpMyAdmin SQL Dump
-- version 4.4.13.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 24, 2016 at 06:55 PM
-- Server version: 5.6.28-0ubuntu0.15.10.1
-- PHP Version: 5.6.11-1ubuntu3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pos`
--

-- --------------------------------------------------------

--
-- Table structure for table `customerGroups`
--

CREATE TABLE IF NOT EXISTS `customerGroups` (
  `id` int(11) NOT NULL,
  `name` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE IF NOT EXISTS `customers` (
  `id` int(11) NOT NULL,
  `name` text,
  `customerGroup` int(11) DEFAULT NULL,
  `address` text,
  `address2` text,
  `city` text,
  `state` text,
  `zip` text,
  `phone` text,
  `email` text,
  `discounts` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE IF NOT EXISTS `discounts` (
  `id` int(11) NOT NULL,
  `group1` int(11) DEFAULT NULL,
  `group2` int(11) DEFAULT NULL,
  `discount` decimal(17,6) NOT NULL,
  `percentage` tinyint(1) NOT NULL DEFAULT '1',
  `min` int(11) NOT NULL DEFAULT '0',
  `max` int(11) NOT NULL DEFAULT '0',
  `stackable` tinyint(1) NOT NULL DEFAULT '0',
  `automatic` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `discountTypes`
--

CREATE TABLE IF NOT EXISTS `discountTypes` (
  `id` int(11) NOT NULL,
  `type` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `paymentTypes`
--

CREATE TABLE IF NOT EXISTS `paymentTypes` (
  `id` int(11) NOT NULL,
  `paymentType` text NOT NULL,
  `modifier` int(11) DEFAULT NULL,
  `percentage` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `endpoint` tinytext NOT NULL,
  `target` tinytext NOT NULL,
  `method` tinytext NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `productGroups`
--

CREATE TABLE IF NOT EXISTS `productGroups` (
  `id` int(11) NOT NULL,
  `name` varchar(1000) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `productRecords`
--

CREATE TABLE IF NOT EXISTS `productRecords` (
  `sku` varchar(50) NOT NULL,
  `reorderQuantity` int(11) DEFAULT NULL,
  `daysInStock` int(11) DEFAULT NULL,
  `lastUpdated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `sku` varchar(50) NOT NULL,
  `upc` varchar(50) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `manufacturer` varchar(100) DEFAULT NULL,
  `category` text,
  `wholesale` decimal(9,2) NOT NULL DEFAULT '0.00',
  `retail` decimal(9,2) NOT NULL DEFAULT '0.00',
  `taxable` tinyint(1) NOT NULL DEFAULT '1',
  `qoh` int(11) DEFAULT NULL,
  `reorder` int(11) NOT NULL DEFAULT '0',
  `updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `productsToGroups`
--

CREATE TABLE IF NOT EXISTS `productsToGroups` (
  `productId` int(11) NOT NULL,
  `productGroupId` int(11) NOT NULL,
  `exclusive` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `productsToTransactions`
--

CREATE TABLE IF NOT EXISTS `productsToTransactions` (
  `sku` varchar(50) NOT NULL,
  `transactionId` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `originalPrice` decimal(9,2) NOT NULL DEFAULT '0.00',
  `actualPrice` decimal(9,2) NOT NULL DEFAULT '0.00',
  `discounts` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sources`
--

CREATE TABLE IF NOT EXISTS `sources` (
  `id` int(11) NOT NULL,
  `name` text,
  `phone` text,
  `email` text,
  `notes` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE IF NOT EXISTS `transactions` (
  `transactionId` int(11) NOT NULL,
  `userId` int(11) DEFAULT NULL,
  `customerId` int(11) DEFAULT NULL,
  `total` decimal(9,2) DEFAULT NULL,
  `time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `discounts` varchar(255) DEFAULT NULL,
  `paymentType` int(11) DEFAULT NULL,
  `transactionType` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transactionTypes`
--

CREATE TABLE IF NOT EXISTS `transactionTypes` (
  `id` int(11) NOT NULL,
  `name` text,
  `effect` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `userGroups`
--

CREATE TABLE IF NOT EXISTS `userGroups` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `name` text,
  `phone` text,
  `password` text,
  `salt` text,
  `clerkGroup` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customerGroups`
--
ALTER TABLE `customerGroups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `discountTypes`
--
ALTER TABLE `discountTypes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `paymentTypes`
--
ALTER TABLE `paymentTypes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `productGroups`
--
ALTER TABLE `productGroups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `productRecords`
--
ALTER TABLE `productRecords`
  ADD PRIMARY KEY (`sku`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`sku`),
  ADD KEY `upc` (`upc`),
  ADD KEY `name` (`name`),
  ADD KEY `manufacturer` (`manufacturer`);

--
-- Indexes for table `productsToGroups`
--
ALTER TABLE `productsToGroups`
  ADD UNIQUE KEY `productId` (`productId`,`productGroupId`);

--
-- Indexes for table `sources`
--
ALTER TABLE `sources`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transactionId`);

--
-- Indexes for table `transactionTypes`
--
ALTER TABLE `transactionTypes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `userGroups`
--
ALTER TABLE `userGroups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customerGroups`
--
ALTER TABLE `customerGroups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `discounts`
--
ALTER TABLE `discounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `paymentTypes`
--
ALTER TABLE `paymentTypes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `productGroups`
--
ALTER TABLE `productGroups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `transactionTypes`
--
ALTER TABLE `transactionTypes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `userGroups`
--
ALTER TABLE `userGroups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
