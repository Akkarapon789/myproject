-- phpMyAdmin SQL Dump
-- version 5.0.4deb2+deb11u2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 11, 2025 at 12:00 AM
-- Server version: 10.5.29-MariaDB-0+deb11u1
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `myproject`
--
CREATE DATABASE IF NOT EXISTS `myproject` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `myproject`;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `title`, `description`, `image_url`, `created_at`) VALUES
(1, 'หนังสือ', 'หมวดหมู่หนังสือทั่วไป', 'uploads/category_books.jpg', '2025-10-10 16:58:04'),
(2, 'เสื้อผ้า', 'หมวดหมู่แฟชั่นและเสื้อผ้า', 'uploads/category_clothes.jpg', '2025-10-10 16:58:04'),
(3, 'อุปกรณ์อิเล็กทรอนิกส์', 'หมวดหมู่สินค้าด้านเทคโนโลยี', 'uploads/category_electronics.jpg', '2025-10-10 16:58:04');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `payment` enum('cod','bank','credit') NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `fullname`, `email`, `phone`, `address`, `payment`, `total`, `created_at`) VALUES
(1, 20, 'as', 'kha@gmail.com', '0258741369', 'aa', 'bank', '220.50', '2025-10-10 00:15:06'),
(2, 5, 'as', 'kha@gmail.com', '0258741369', 'ฟฟ', 'cod', '1345.14', '2025-10-10 00:40:03'),
(3, 13, 'ฮาร์ทนะจ๊ะ', 'kha@gmail.com', '0946782293', 'อยู่นั่นล่ะ', 'cod', '565.25', '2025-10-10 13:16:29');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `title`, `price`, `stock`, `image_url`, `created_at`) VALUES
(1, 1, 'หนังสือเรียนภาษาอังกฤษ', '150.00', 20, 'uploads/book1.jpg', '2025-10-10 16:58:04'),
(2, 2, 'เสื้อยืดลายกราฟิก', '299.00', 15, 'uploads/shirt1.jpg', '2025-10-10 16:58:04'),
(3, 3, 'หูฟังบลูทูธ', '899.00', 10, 'uploads/headphone1.jpg', '2025-10-10 16:58:04');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `firstname`, `lastname`, `email`, `password`, `address`, `phone`, `role`) VALUES
(1, 'Admin', 'One', 'admin@admin.gmail.com', '$2y$10$k3uU0I0oB0LwQyP5x9nP5u2m6/6yAqP1x9nP5u2m6/6yAqP1x9nP5', NULL, NULL, 'admin'),
(5, 'ขจรศักดิ์', 'จันทรเสนา', 'kha@gmail.com', '$2y$10$XdapPIBVKd2Cl1wwpcvmXejVLP4s8Z/Hk53dtilZ/kfBKAhuraEiG', 'kk', '0946782293', 'user'),
(12, 'ขจรศักดิ์', 'จันทรเสนา', 'kc@gmail.com', '$2y$10$hsIe9bR.Ng.r9UKGHeRXGeXBx4zxZ.wpJiCiejF.PcQ4Jpp2tbDtq', 'la', '0946782293', 'admin'),
(13, '77', '88', '789@admin.gmail.com', '$2y$10$1Ao.5Yb9/8Hova32QFB1hOBeFbTZipqD//ZZm8X0qbFKbodlcAJE.', '55', '0123456799', 'admin'),
(14, '77', '88', 'khaaa@gmail.com', '$2y$10$q0./T/W0l.BsZtomVC6eE.Acr9IRO9FjZ9xL.Fi7LhApm/dT9CSRq', '77', '0123456779', 'user'),
(16, 'Akkarapon', '77', 'kha77@gmail.com', '$2y$10$/g0Kxa73cGu2ptZMIF/vmOrx0Yq1RuOaIPX6eAThfm8sqB9XC22hq', '77', '0123456777', 'user'),
(17, 'Akkarapon', '6', 'kha9@gmail.com', '$2y$10$jIxNrJ36VpGKozJTL9LymOPzjZD5OGHWElIytEdEBeXTmvthtqYb2', '9', '9', 'user'),
(18, 'hjos', 'sh', 'ss@gmail.com', '$2y$10$ah/oMySVamu7t96I1sEIYuwZRk7vL8tlAO1fVWvNGBdQe21RlMpxW', 'aa', '0935658741', 'user'),
(19, 'น้าขิง', 'น้าขิง', 'nakhing@gmail.com', '$2y$10$6yAsg9WavL3dVxX/qdTByuyZBXY.ukcWClqMARz8F7Tn9cFPLpm.G', '123', '123', 'user'),
(20, 'ขิง', 'ขิง', 'khing@gmail.com', '$2y$10$w4Fw1gyjFsMQ3590D8pqbOblYZ0ahsFucUGOAzQkeoBE2MV5vbSrO', '123', '123', 'user'),
(21, 'new', 'new', 'new@gmail.com', '$2y$10$h.7wSWZtkXIUU0PJbtoNZ.JifTujCwKCaCBigJ/NzTgNEMsT6xc8G', '22', '0123456789', 'user'),
(22, 'ภาวิตา', 'นนทะชาติ', 'pavitanon238@gmail.com', '$2y$10$F.ySC.WrPZUUmxMHksvKaO2aW1l6YF7O6IIZg0c0KG/Mo8xcN5FPW', '99', '0929300112', 'user'),
(23, 'Akkarapon', '88', 'khaa@gmail.com', '$2y$10$F0k6vhEwKPB8pac/k1cJW.O1KEfyErvfSCcrqpYZJ/G.fZCb6WIX.', NULL, '0123456779', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
