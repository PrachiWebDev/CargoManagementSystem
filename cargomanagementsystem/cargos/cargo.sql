-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 27, 2025 at 02:56 PM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cargo`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` text,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `user_id`, `full_name`, `phone`, `address`, `city`, `state`, `pincode`) VALUES
(1, 2, 'diego', '9876543210', 'Gorakhpur', 'Jabalpur', 'Madhya Pradesh', '482001'),
(2, 4, 'user', '7896541235', 'jabalpur', 'jabalpur', 'madhya pradesh', '482001');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
CREATE TABLE IF NOT EXISTS `locations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `address` text,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `name`, `address`, `city`, `state`, `pincode`, `phone`) VALUES
(1, 'Gorakhpur HUB', 'Gorakhpur', 'JABALPUR', 'Madhya Pradesh', '482001', '9876543210'),
(2, 'Bhopal HUB', 'Bhopal', 'Bhopal', 'Madhya Pradesh', '462001', '7896352416'),
(3, 'Indore HUB', 'Indore', 'Indore', 'Madhya Pradesh', '452001', '6235987465'),
(4, 'Ujjain HUB', 'Ujjain', 'Ujjain', 'Madhya Pradesh', '456001', '9784659866');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `shipment_id` int DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `transaction_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `shipment_id` (`shipment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shipments`
--

DROP TABLE IF EXISTS `shipments`;
CREATE TABLE IF NOT EXISTS `shipments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tracking_number` varchar(20) NOT NULL,
  `customer_id` int DEFAULT NULL,
  `pickup_location_id` int DEFAULT NULL,
  `delivery_location_id` int DEFAULT NULL,
  `vehicle_id` int DEFAULT NULL,
  `status` enum('pending','in_transit','delivered','cancelled') DEFAULT 'pending',
  `weight` decimal(10,2) DEFAULT NULL,
  `dimensions` varchar(50) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tracking_number` (`tracking_number`),
  KEY `customer_id` (`customer_id`),
  KEY `pickup_location_id` (`pickup_location_id`),
  KEY `delivery_location_id` (`delivery_location_id`),
  KEY `vehicle_id` (`vehicle_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `shipments`
--

INSERT INTO `shipments` (`id`, `tracking_number`, `customer_id`, `pickup_location_id`, `delivery_location_id`, `vehicle_id`, `status`, `weight`, `dimensions`, `description`, `created_at`) VALUES
(1, 'TRK202504255285', 1, 1, 1, 1, 'in_transit', 10.00, '12*15*20', 'electronic items', '2025-04-25 18:44:32'),
(2, 'TRK202504307721', 4, 1, 1, 2, 'pending', 0.10, '12*15*20', 'test', '2025-04-30 07:43:14');

-- --------------------------------------------------------

--
-- Table structure for table `tracking`
--

DROP TABLE IF EXISTS `tracking`;
CREATE TABLE IF NOT EXISTS `tracking` (
  `id` int NOT NULL AUTO_INCREMENT,
  `shipment_id` int DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `remarks` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `shipment_id` (`shipment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tracking`
--

INSERT INTO `tracking` (`id`, `shipment_id`, `status`, `location`, `remarks`, `created_at`) VALUES
(1, 1, 'in_transit', 'Pickup location', 'Shipment created and assigned to vehicle', '2025-04-25 18:44:32'),
(2, 2, 'pending', 'Pickup location', 'Shipment created and assigned to vehicle', '2025-04-30 07:43:14');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','staff','customer') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `created_at`) VALUES
(1, 'admin', '$argon2i$v=19$m=65536,t=4,p=1$alRpTXpYbXdvSUU0eXdhcQ$Mk7Wf+ZY9qmcxCiO2Ad3Ipfi1+cEKSlQix7UEDhrlds', 'admin@cargo.com', 'admin', '2025-04-25 18:35:59'),
(3, 'diego', '$2y$10$PoAP2DlRkiftzCM.t0QU3ei2.K/978Fjemi1fITIzh4HwCPsdbk5m', 'diego@gmail.com', 'customer', '2025-04-25 19:51:52'),
(4, 'user', '$2y$10$s5RH26YbuwyRoakSPv9hdeiiO5ILPq/dGqDIl.B9ikkJXye7VcuMy', 'user@gmail.com', 'customer', '2025-05-27 09:14:25');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
CREATE TABLE IF NOT EXISTS `vehicles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vehicle_number` varchar(20) NOT NULL,
  `vehicle_type` varchar(50) NOT NULL,
  `capacity` decimal(10,2) DEFAULT NULL,
  `status` enum('available','in_use','maintenance') DEFAULT 'available',
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicle_number` (`vehicle_number`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `vehicle_number`, `vehicle_type`, `capacity`, `status`) VALUES
(1, 'MP22ML9691', 'Truck', 1000.00, 'in_use'),
(2, 'MP20LP3484', 'Truck', 1000.00, 'in_use'),
(3, 'MP20TO2345', 'Truck', 1000.00, 'available');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
