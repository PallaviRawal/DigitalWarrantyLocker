-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 17, 2025 at 05:22 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `warranty_locker`
--

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `priority` enum('Low','Medium','High') DEFAULT 'Medium',
  `attachment_image` varchar(255) DEFAULT NULL,
  `attachment_audio` varchar(255) DEFAULT NULL,
  `status` enum('Pending','In Progress','Resolved','Not Resolved') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`id`, `user_id`, `product_id`, `title`, `description`, `priority`, `attachment_image`, `attachment_audio`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'kkkk', 'kkkkkkkkkkkkkkkkkk', 'Medium', NULL, NULL, 'Pending', '2025-09-16 06:42:50', '2025-09-16 06:49:50'),
(2, 2, 3, 'Falut', 'not working properly', 'Medium', NULL, NULL, 'Resolved', '2025-09-16 09:36:59', '2025-09-16 09:49:17'),
(3, 2, 10, 'Not working', 'Falut blank tv no color only voice', 'High', NULL, NULL, 'Pending', '2025-09-16 13:13:15', '2025-09-16 13:13:15'),
(4, 2, 11, 'fault', 'showing problwm in working', 'High', NULL, NULL, 'Pending', '2025-09-17 18:13:10', '2025-09-17 18:13:10'),
(5, 2, 11, 'k', 'kk', 'Medium', NULL, NULL, 'Pending', '2025-09-18 16:31:00', '2025-09-18 16:31:00'),
(6, 2, 15, 'kk', 'kk', 'Medium', NULL, NULL, 'In Progress', '2025-09-18 16:35:00', '2025-09-20 09:20:40'),
(7, 2, 15, 'kk', 'kk', 'Medium', NULL, NULL, 'In Progress', '2025-09-18 16:37:15', '2025-09-18 19:30:32'),
(8, 2, 11, 'k', 'kk', 'Medium', NULL, NULL, 'Not Resolved', '2025-09-18 16:44:04', '2025-09-18 19:30:36'),
(9, 2, 11, 'k', 'kk', 'Medium', NULL, NULL, 'Pending', '2025-09-18 16:44:07', '2025-09-18 16:44:07'),
(10, 2, 11, 'kk', 'kk', 'Medium', NULL, NULL, 'Pending', '2025-09-18 16:46:09', '2025-09-18 16:46:09'),
(11, 2, 15, 'iiiiiii', 'iiiiiiiii', 'Medium', NULL, NULL, 'Pending', '2025-09-18 16:50:52', '2025-09-18 16:50:52'),
(12, 2, 11, 'pallavi', 'pallavi', 'Medium', NULL, NULL, 'Resolved', '2025-09-18 18:00:22', '2025-09-18 19:16:39'),
(13, 2, 10, 'haaaaaaaa', 'haaaaaaaaa', 'Medium', NULL, NULL, 'Pending', '2025-09-18 18:20:27', '2025-09-18 18:20:27'),
(14, 2, 15, 'hault', 'getting stopped', 'Medium', NULL, NULL, 'Resolved', '2025-09-18 19:24:32', '2025-09-18 19:30:23'),
(15, 2, 15, 'not working', 'showing error', 'Medium', NULL, NULL, 'Pending', '2025-09-18 19:44:20', '2025-09-18 19:44:20'),
(16, 2, 13, 'not working', 'speaker not working proerply', 'Medium', NULL, NULL, 'Pending', '2025-09-19 08:55:30', '2025-09-19 08:55:30'),
(17, 2, 17, 'error', 'error', 'Medium', 'uploads/complaints/68ce57be3f9cf.jpg', NULL, 'Pending', '2025-09-20 07:29:02', '2025-09-20 07:29:02'),
(18, 8, 30, 'error', 'error', 'Medium', NULL, 'uploads/complaints/68ce63bb4d285.webm', 'Resolved', '2025-09-20 08:20:11', '2025-09-20 08:23:25'),
(19, 2, 11, 'kkkkkkkk', 'kkkkkkkkkkk', 'Medium', NULL, NULL, 'Pending', '2025-09-20 09:20:18', '2025-09-20 09:20:18'),
(20, 11, 44, 'not working properly', 'main problem asscociated with achine part', 'Medium', NULL, NULL, 'Pending', '2025-09-22 11:52:06', '2025-09-22 11:52:06'),
(21, 11, 47, 'Not working', 'Problem in voice', 'High', NULL, NULL, 'Pending', '2025-09-24 08:47:23', '2025-09-24 08:47:23'),
(22, 11, 45, 'not', 'kkk', 'Medium', NULL, NULL, 'Pending', '2025-09-24 08:47:47', '2025-09-24 08:47:47'),
(23, 11, 46, 'kkkk', 'issue', 'Medium', NULL, 'uploads/complaints/68d8bbf881011.webm', 'In Progress', '2025-09-28 04:39:20', '2025-10-07 17:55:40'),
(24, 11, 46, 'not working', 'problem in motor part', 'High', NULL, 'uploads/complaints/68ea685bc1373.webm', 'Pending', '2025-10-11 14:23:23', '2025-10-11 14:23:23'),
(25, 11, 46, 'new', 'pppppp', 'Medium', 'uploads/complaints/68ea7ce969e89.webp', NULL, 'Pending', '2025-10-11 15:51:05', '2025-10-11 15:51:05'),
(26, 11, 49, 'kk', 'kkk', 'Medium', 'uploads/complaints/68ea80138df5f.webp', NULL, 'Pending', '2025-10-11 16:04:35', '2025-10-11 16:04:35'),
(27, 11, 45, 'working not', 'aaaaa', 'Medium', 'uploads/complaints/68ebf51addaee.jpg', NULL, 'Pending', '2025-10-12 18:36:10', '2025-10-12 18:36:10'),
(28, 12, 50, 'Not Working properly', 'Problem in speaker part voice is not coming properly.Bought few days back only and facing this issue', 'High', NULL, 'uploads/complaints/68ec01a657782.webm', 'Resolved', '2025-10-12 19:29:42', '2025-10-12 19:47:09'),
(29, 12, 52, 'issue in product', 'recieved damaged product', 'Medium', 'uploads/complaints/68ec06e29e62d.png', 'uploads/complaints/68ec06e2a02c5.webm', 'Pending', '2025-10-12 19:52:02', '2025-10-12 19:52:02');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT '#',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `link`, `is_read`, `created_at`) VALUES
(1, 1, 'Welcome', 'Your dashboard is ready!', '#', 0, '2025-09-18 17:28:15'),
(2, 1, 'Reminder', 'Renew your Samsung TV warranty.', '#', 0, '2025-09-18 17:28:15');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `purchase_date` date NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `warranty_period` int(11) NOT NULL,
  `warranty_unit` enum('months','years') NOT NULL DEFAULT 'months',
  `warranty_expiry` date DEFAULT NULL,
  `bill_file` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `user_id`, `product_name`, `category`, `brand`, `serial_number`, `purchase_date`, `price`, `warranty_period`, `warranty_unit`, `warranty_expiry`, `bill_file`, `notes`, `created_at`, `updated_at`) VALUES
(25, 3, 'Test Product 3', '', '', NULL, '2025-09-19', NULL, 0, 'months', '2025-09-19', NULL, NULL, '2025-09-19 13:27:18', '2025-09-19 13:27:18'),
(50, 12, 'samsung', 'Appliances', 'samsung', '', '2025-09-10', 100000.00, 1, 'years', '2025-11-01', '1760297277_cn_index2.jpg', '', '2025-10-12 19:27:57', '2025-10-13 07:49:02'),
(52, 12, 'Vivo', 'Electronics', 'Vivo', '', '2024-11-01', NULL, 1, 'years', '2025-11-10', '1760298136_Screenshot_2025-08-20_114709.png', '', '2025-10-12 19:42:16', '2025-10-12 19:43:13'),
(53, 15, 'Vivo', 'Appliances', 'Vivo', '', '2024-10-20', NULL, 1, 'years', '2025-10-20', '1760341263_3rdpage.jpeg', '', '2025-10-13 07:41:03', '2025-10-13 07:41:03');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_admin` tinyint(1) DEFAULT 0,
  `role` enum('owner','member') DEFAULT 'owner'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `username`, `email`, `phone`, `password_hash`, `created_at`, `updated_at`, `is_admin`, `role`) VALUES
(3, 'Pallavi', 'Rawal', 'pallavi', 'pallavirawal679@gmail.com', '9833980767', '$2y$10$YjD.L48CjWo0x2qewD7rZOliJHgzFur87RvMpLVU4yMhns.zwjYYi', '2025-09-16 09:45:18', '2025-09-16 09:45:33', 1, 'owner'),
(6, '', '', '', 'bharatrawal266@gmal.com', 'temp1758268242', '$2y$10$qQ0/7xCCUPcgEaNohbE.Se56wrPsYLH6G3GEXdyT5EcmdePW05PbC', '2025-09-19 07:50:42', '2025-09-19 07:50:42', 0, 'member'),
(7, 'Pinky', 'Rawal', 'pinky', 'pinkyrawal018@gmail.com', '1234567891', '$2y$10$YxY3QjyBFLa2LZJjEBfrb.MX1D84sqX1qDjpNfJzHM6/XdBsoXtqC', '2025-09-19 12:59:29', '2025-09-19 12:59:29', 0, 'owner'),
(12, 'Hetal', 'Rawal', 'hetal', 'hetalrawal183@gmail.com', '9702892066', '$2y$10$FRLIiKRf.cCT4GMEMK3oyOgipmcnRpWrhKXnuiZUmcgu4pU1RFs.2', '2025-10-12 19:24:30', '2025-10-12 19:24:30', 0, 'owner'),
(14, '', '', 'member_1760298001', 'bharatrawal266@gmail.com', 'temp1760298001', '$2y$10$aGSyr2zvxce.BSM0HX7HDu1ehCwyGqsFGbaRPySfZxRhUWYoUAzlG', '2025-10-12 19:40:01', '2025-10-12 19:40:01', 0, 'member'),
(15, 'Mansi', 'Rawal', 'Mansi', 'mansirawal123@gmail.com', '2345678912', '$2y$10$.BQLf1WMdERGQ4Dh8z4BK.zwMrhlEkQpjgSM/ccwxGws73mGfeDnG', '2025-10-13 07:39:33', '2025-10-13 07:39:33', 0, 'owner');

-- --------------------------------------------------------

--
-- Table structure for table `warranty_shared_members`
--

CREATE TABLE `warranty_shared_members` (
  `id` int(11) NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `owner_id` int(10) UNSIGNED NOT NULL,
  `member_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `warranty_shared_members`
--

INSERT INTO `warranty_shared_members` (`id`, `product_id`, `owner_id`, `member_id`, `created_at`) VALUES
(4, 50, 12, 14, '2025-10-12 19:40:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Indexes for table `warranty_shared_members`
--
ALTER TABLE `warranty_shared_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `owner_id` (`owner_id`),
  ADD KEY `member_id` (`member_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `warranty_shared_members`
--
ALTER TABLE `warranty_shared_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `warranty_shared_members`
--
ALTER TABLE `warranty_shared_members`
  ADD CONSTRAINT `warranty_shared_members_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `warranty_shared_members_ibfk_2` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `warranty_shared_members_ibfk_3` FOREIGN KEY (`member_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
