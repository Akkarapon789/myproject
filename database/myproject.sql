-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 01, 2025 at 02:15 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

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
CREATE DATABASE IF NOT EXISTS 'myproject' DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `myproject`;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `title`, `slug`, `parent_id`, `created_at`, `updated_at`) VALUES
(1, 'นิยาย', 'novel', NULL, '2025-09-28 10:37:15', '2025-09-28 10:37:15'),
(2, 'หนังสือเด็ก', 'kids', NULL, '2025-09-28 10:37:15', '2025-09-28 10:37:15'),
(3, 'การ์ตูน / มังงะ', 'comic-manga', NULL, '2025-09-28 10:37:15', '2025-09-28 10:37:15'),
(4, 'การเรียน / คู่มือสอบ', 'study', NULL, '2025-09-28 10:37:15', '2025-09-28 10:37:15'),
(5, 'ธุรกิจ / การเงิน', 'business', NULL, '2025-09-28 10:37:15', '2025-09-28 10:37:15'),
(6, 'ศิลปะ / งานอดิเรก', 'art-hobby', NULL, '2025-09-28 10:37:15', '2025-09-28 10:37:15');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text NOT NULL,
  `payment` varchar(50) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_detail`
--

CREATE TABLE `order_detail` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `title`, `slug`, `description`, `author`, `publisher`, `price`, `stock`, `image_url`, `created_at`, `updated_at`) VALUES
(9, 1, 'นิยายเรื่อง A', 'novel-a', NULL, 'Author A', 'Publisher A', 350.00, 10, '/images/novel-a.jpg', '2025-09-28 16:37:29', '2025-09-28 16:37:29'),
(10, 1, 'นิยายเรื่อง B', 'novel-b', NULL, 'Author B', 'Publisher B', 420.00, 8, '/images/novel-b.jpg', '2025-09-28 16:37:29', '2025-09-28 16:37:29'),
(11, 1, 'นิยายเรื่อง C', 'novel-c', NULL, 'Author C', 'Publisher C', 299.00, 12, '/images/novel-c.jpg', '2025-09-28 16:37:29', '2025-09-28 16:37:29'),
(12, 1, 'นิยายเรื่อง D', 'novel-d', NULL, 'Author D', 'Publisher D', 380.00, 5, '/images/novel-d.jpg', '2025-09-28 16:37:29', '2025-09-28 16:37:29'),
(13, 2, 'หนังสือเด็กเรื่อง A', 'kids-a', NULL, 'Author E', 'Publisher E', 180.00, 20, '/images/kids-a.jpg', '2025-09-28 16:37:29', '2025-09-28 16:37:29'),
(14, 2, 'หนังสือเด็กเรื่อง B', 'kids-b', NULL, 'Author B', 'Publisher B', 200.00, 15, '/images/kids-b.jpg', '2025-09-28 16:37:29', '2025-09-28 16:37:29'),
(15, 2, 'หนังสือเด็กเรื่อง C', 'kids-c', NULL, 'Author F', 'Publisher F', 220.00, 18, '/images/kids-c.jpg', '2025-09-28 16:37:29', '2025-09-28 16:37:29'),
(16, 2, 'หนังสือเด็กเรื่อง D', 'kids-d', NULL, 'Author G', 'Publisher G', 250.00, 10, '/images/kids-d.jpg', '2025-09-28 16:37:29', '2025-09-28 16:37:29'),
(17, 3, 'มังงะเรื่อง A', 'manga-a', NULL, 'Author H', 'Publisher H', 95.00, 30, '/images/manga-a.jpg', '2025-09-28 16:37:29', '2025-09-28 16:37:29'),
(18, 3, 'มังงะเรื่อง B', 'manga-b', NULL, 'Author I', 'Publisher I', 120.00, 25, '/images/manga-b.jpg', '2025-09-28 16:37:29', '2025-09-28 16:37:29'),
(19, 3, 'มังงะเรื่อง C', 'manga-c', NULL, 'Author J', 'Publisher J', 110.00, 22, '/images/manga-c.jpg', '2025-09-28 16:37:29', '2025-09-28 16:37:29'),
(20, 3, 'มังงะเรื่อง D', 'manga-d', NULL, 'Author K', 'Publisher K', 130.00, 15, '/images/manga-d.jpg', '2025-09-28 16:37:29', '2025-09-28 16:37:29'),
(21, 4, 'คู่มือสอบวิชาคณิตศาสตร์', 'study-math', NULL, 'Author L', 'Publisher L', 400.00, 12, '/images/study-math.jpg', '2025-09-28 16:37:29', '2025-09-28 16:37:29'),
(22, 4, 'คู่มือสอบวิชาภาษาอังกฤษ', 'study-english', NULL, 'Author M', 'Publisher M', 380.00, 10, '/images/study-english.jpg', '2025-09-28 16:37:29', '2025-09-28 16:37:29'),
(23, 4, 'คู่มือสอบวิชาฟิสิกส์', 'study-physics', NULL, 'Author N', 'Publisher N', 420.00, 8, '/images/study-physics.jpg', '2025-09-28 16:37:29', '2025-09-28 16:37:29'),
(24, 5, 'การเงินสำหรับมือใหม่', 'business-basic', NULL, 'Author O', 'Publisher O', 500.00, 7, '/images/business-basic.jpg', '2025-09-28 16:37:29', '2025-09-28 16:37:29'),
(25, 5, 'ลงทุนหุ้นง่ายๆ', 'business-stock', NULL, 'Author P', 'Publisher P', 550.00, 6, '/images/business-stock.jpg', '2025-09-28 16:37:29', '2025-09-28 16:37:29'),
(26, 5, 'การตลาดดิจิทัล', 'business-digital', NULL, 'Author Q', 'Publisher Q', 480.00, 9, '/images/business-digital.jpg', '2025-09-28 16:37:29', '2025-09-28 16:37:29'),
(27, 6, 'วาดรูปสีน้ำ', 'art-watercolor', NULL, 'Author R', 'Publisher R', 320.00, 14, '/images/art-watercolor.jpg', '2025-09-28 16:37:29', '2025-09-28 16:37:29'),
(28, 6, 'งานประดิษฐ์ DIY', 'art-diy', NULL, 'Author S', 'Publisher S', 250.00, 16, '/images/art-diy.jpg', '2025-09-28 16:37:29', '2025-09-28 16:37:29');

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `discount_type` enum('percentage','fixed') DEFAULT 'percentage',
  `discount_value` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(13, '77', '88', '789@admin.gmail.com', '$2y$10$1Ao.5Yb9/8Hova32QFB1hOBeFbTZipqD//ZZm8X0qbFKbodlcAJE.', '55', '0123456799', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
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
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_detail`
--
ALTER TABLE `order_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD CONSTRAINT `order_detail_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_detail_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `promotions`
--
ALTER TABLE `promotions`
  ADD CONSTRAINT `promotions_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `promotions_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
