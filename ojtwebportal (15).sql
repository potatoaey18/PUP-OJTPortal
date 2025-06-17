-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2025 at 07:19 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ojtwebportal`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_account`
--

CREATE TABLE `admin_account` (
  `id` int(11) NOT NULL,
  `uniqueID` varchar(150) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `id_number` varchar(50) NOT NULL,
  `position` varchar(100) NOT NULL,
  `address` varchar(250) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `admin_profile_picture` varchar(200) NOT NULL,
  `admin_email` varchar(100) NOT NULL,
  `admin_password` varchar(100) NOT NULL,
  `verification_code` int(8) NOT NULL,
  `verify_status` varchar(50) NOT NULL DEFAULT 'Not Verified',
  `online_offlineStatus` varchar(50) NOT NULL DEFAULT 'Offline',
  `access_level` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_account`
--

INSERT INTO `admin_account` (`id`, `uniqueID`, `first_name`, `middle_name`, `last_name`, `id_number`, `position`, `address`, `phone_number`, `admin_profile_picture`, `admin_email`, `admin_password`, `verification_code`, `verify_status`, `online_offlineStatus`, `access_level`) VALUES
(1, '662513a17a25e3248', 'Juan', 'A', 'Dela Cruz', 'ADM001', 'College of Engineering', 'Sta. Mesa, Manila', '09123456789', '../student_file_images/coe_admin.jpg', 'test1@gmail.com', '202cb962ac59075b964b07152d234b70', 710511, 'Verified', 'Online', 1),
(2, '662513a17a26a3284', 'Maria', 'B', 'Santos', 'ADM002', 'College of Computer Studies', 'Quezon City', '09234567890', '../student_file_images/ccs_admin.jpg', 'test2@gmail.com', '202cb962ac59075b964b07152d234b70', 306649, 'Verified', 'Offline', 1),
(3, '662513a17a26b7452', 'Pedro', 'C', 'Reyes', 'ADM003', 'College of Business', 'Mandaluyong City', '09345678901', '../student_file_images/cob_admin.jpg', 'test3@gmail.com', '202cb962ac59075b964b07152d234b70', 830404, 'Verified', 'Offline', 1),
(4, '662513a17a26c4423', 'Ana', 'D', 'Gonzales', 'ADM004', 'College of Science', 'Makati City', '09456789012', '../student_file_images/cos_admin.jpg', 'test4@gmail.com', '202cb962ac59075b964b07152d234b70', 247453, 'Verified', 'Offline', 1),
(5, 'manual_admin_001', 'Dashinnn', 'Camba', 'Maratas', 'ADM005', 'Administrator', 'Unknown', '00000000000', '../student_file_images/default_admin.jpg', 'admin@gmail.com', '32250170a0dca92d53ec9624f336ca24', 12345678, 'Verified', 'Online', 1);

-- --------------------------------------------------------

--
-- Table structure for table `admin_system_notification`
--

CREATE TABLE `admin_system_notification` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `logs` varchar(150) NOT NULL,
  `logs_date` varchar(50) NOT NULL,
  `logs_time` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_system_notification`
--

INSERT INTO `admin_system_notification` (`id`, `admin_id`, `logs`, `logs_date`, `logs_time`, `status`) VALUES
(1, 1, 'You successfully logged in to your account.', 'June / 12 / 2025', '4:42 PM', 'Unread'),
(2, 5, 'You successfully logged in to your account.', 'June / 14 / 2025', '5:51 PM', 'Unread'),
(3, 5, 'You successfully logged in to your account.', 'June / 14 / 2025', '8:36 PM', 'Unread'),
(4, 1, 'You successfully logged in to your account.', 'June / 14 / 2025', '8:45 PM', 'Unread'),
(5, 1, 'You successfully logged in to your account.', 'June / 14 / 2025', '8:56 PM', 'Unread'),
(6, 1, 'You successfully logged in to your account.', 'June / 14 / 2025', '9:13 PM', 'Unread'),
(7, 3, 'You successfully logged in to your account.', 'June / 15 / 2025', '10:45 PM', 'Unread'),
(8, 1, 'You successfully logged in to your account.', 'June / 15 / 2025', '11:13 PM', 'Unread'),
(9, 1, 'You successfully logged in to your account.', 'June / 15 / 2025', '11:25 PM', 'Unread'),
(10, 1, 'You successfully logged in to your account.', 'June / 15 / 2025', '11:26 PM', 'Unread'),
(11, 1, 'You successfully logged in to your account.', 'June / 15 / 2025', '11:42 PM', 'Unread');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `portal` enum('Student','Adviser','HTE','All') NOT NULL DEFAULT 'All',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_size` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_system`
--

CREATE TABLE `chat_system` (
  `id` int(11) NOT NULL,
  `sender_id` varchar(50) NOT NULL,
  `receiver_id` varchar(50) NOT NULL,
  `messages` text DEFAULT NULL,
  `images` varchar(500) DEFAULT NULL,
  `date_only` varchar(100) NOT NULL,
  `time_only` varchar(100) NOT NULL,
  `status` enum('Sent','Delivered','Read') DEFAULT 'Sent',
  `documents` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_moa`
--

CREATE TABLE `company_moa` (
  `id` int(11) NOT NULL,
  `moa_file` varchar(255) DEFAULT NULL,
  `company_name` varchar(50) DEFAULT NULL,
  `supervisor_email` varchar(50) DEFAULT NULL,
  `date_uploaded` datetime DEFAULT current_timestamp(),
  `renewal_status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_skills_requirements`
--

CREATE TABLE `company_skills_requirements` (
  `id` int(11) NOT NULL,
  `company_name` varchar(150) NOT NULL,
  `skills_name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conversations_coordinator_hte`
--

CREATE TABLE `conversations_coordinator_hte` (
  `id` int(11) NOT NULL,
  `coordinator_id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_message_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Stores conversation mappings between coordinators and HTE supervisors';

-- --------------------------------------------------------

--
-- Table structure for table `conversations_coordinator_student`
--

CREATE TABLE `conversations_coordinator_student` (
  `id` int(11) NOT NULL,
  `coordinator_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conversation_participants`
--

CREATE TABLE `conversation_participants` (
  `id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `last_read_message_id` int(11) DEFAULT NULL,
  `is_muted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coordinatorsystemnotification`
--

CREATE TABLE `coordinatorsystemnotification` (
  `id` int(11) NOT NULL,
  `coordinator_id` int(12) NOT NULL,
  `logs` varchar(200) NOT NULL,
  `logs_date` varchar(50) NOT NULL,
  `logs_time` varchar(50) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT 'Unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coordinatorsystemnotification`
--

INSERT INTO `coordinatorsystemnotification` (`id`, `coordinator_id`, `logs`, `logs_date`, `logs_time`, `status`) VALUES
(1, 6, 'You successfully logged in to your account.', 'April / 03 Thursday / 2025', '9:20 AM', 'Unread'),
(2, 6, 'You successfully logged in to your account.', 'April / 03 Thursday / 2025', '9:50 AM', 'Unread'),
(3, 6, 'You successfully logged in to your account.', 'April / 03 Thursday / 2025', '9:56 AM', 'Unread'),
(4, 6, 'You successfully logged out to your account.', 'April / 03 Thursday / 2025', '10:09 AM', 'Unread'),
(5, 6, 'You successfully logged in to your account.', 'April / 03 Thursday / 2025', '10:09 AM', 'Unread'),
(6, 6, 'You successfully logged in to your account.', 'April / 03 Thursday / 2025', '10:44 AM', 'Unread'),
(7, 6, 'You successfully logged in to your account.', 'April / 03 Thursday / 2025', '10:49 AM', 'Unread'),
(8, 6, 'You successfully logged in to your account.', 'April / 03 Thursday / 2025', '10:58 AM', 'Unread'),
(9, 6, 'You successfully logged in to your account.', 'April / 03 Thursday / 2025', '9:17 PM', 'Unread'),
(10, 6, 'You successfully logged in to your account.', 'April / 03 Thursday / 2025', '10:15 PM', 'Unread'),
(11, 6, 'You successfully logged out to your account.', 'April / 03 Thursday / 2025', '10:52 PM', 'Unread'),
(12, 6, 'You successfully logged in to your account.', 'April / 03 Thursday / 2025', '10:56 PM', 'Unread'),
(13, 6, 'You successfully logged out to your account.', 'April / 03 Thursday / 2025', '11:40 PM', 'Unread'),
(14, 6, 'You successfully logged in to your account.', 'April / 06 Sunday / 2025', '8:53 AM', 'Unread'),
(15, 6, 'You successfully logged in to your account.', 'April / 07 Monday / 2025', '8:36 AM', 'Unread'),
(16, 6, 'You successfully logged in to your account.', 'April / 07 Monday / 2025', '9:41 AM', 'Unread'),
(17, 6, 'You successfully logged in to your account.', 'April / 07 Monday / 2025', '10:13 PM', 'Unread'),
(18, 6, 'You successfully logged out to your account.', 'April / 07 Monday / 2025', '10:45 PM', 'Unread'),
(19, 6, 'You successfully logged in to your account.', 'April / 07 Monday / 2025', '10:45 PM', 'Unread'),
(20, 7, 'You successfully logged in to your account.', 'April / 07 Monday / 2025', '11:45 PM', 'Unread'),
(21, 7, 'You successfully logged in to your account.', 'April / 07 Monday / 2025', '11:48 PM', 'Unread'),
(22, 7, 'You successfully logged out to your account.', 'April / 07 Monday / 2025', '11:48 PM', 'Unread'),
(23, 7, 'You successfully logged in to your account.', 'April / 09 Wednesday / 2025', '9:25 AM', 'Unread'),
(24, 7, 'You successfully logged in to your account.', 'April / 09 Wednesday / 2025', '9:55 AM', 'Unread'),
(25, 7, 'You successfully logged in to your account.', 'April / 09 Wednesday / 2025', '10:35 AM', 'Unread'),
(26, 7, 'You successfully logged in to your account.', 'April / 09 Wednesday / 2025', '10:47 AM', 'Unread'),
(27, 7, 'You successfully logged in to your account.', 'April / 09 Wednesday / 2025', '11:07 AM', 'Unread'),
(28, 7, 'You successfully logged in to your account.', 'April / 09 Wednesday / 2025', '11:14 AM', 'Unread'),
(29, 7, 'You successfully logged in to your account.', 'April / 09 Wednesday / 2025', '11:56 AM', 'Unread'),
(30, 7, 'You successfully logged in to your account.', 'April / 09 Wednesday / 2025', '12:09 PM', 'Unread'),
(31, 7, 'You successfully logged in to your account.', 'April / 09 Wednesday / 2025', '2:13 PM', 'Unread'),
(32, 7, 'You successfully logged in to your account.', 'April / 09 Wednesday / 2025', '2:42 PM', 'Unread'),
(33, 7, 'You successfully logged in to your account.', 'April / 09 Wednesday / 2025', '2:57 PM', 'Unread'),
(34, 7, 'You successfully logged in to your account.', 'April / 09 Wednesday / 2025', '3:02 PM', 'Unread'),
(35, 7, 'You successfully logged in to your account.', 'April / 09 Wednesday / 2025', '3:09 PM', 'Unread'),
(36, 7, 'You successfully logged in to your account.', 'April / 10 Thursday / 2025', '9:25 AM', 'Unread'),
(37, 7, 'You successfully logged in to your account.', 'April / 10 Thursday / 2025', '9:59 AM', 'Unread'),
(38, 7, 'You successfully logged in to your account.', 'April / 10 Thursday / 2025', '10:07 AM', 'Unread'),
(39, 7, 'You successfully logged in to your account.', 'April / 10 Thursday / 2025', '10:13 AM', 'Unread'),
(40, 7, 'You successfully logged in to your account.', 'April / 10 Thursday / 2025', '10:18 AM', 'Unread'),
(41, 7, 'You successfully logged in to your account.', 'April / 10 Thursday / 2025', '10:23 AM', 'Unread'),
(42, 7, 'You successfully logged in to your account.', 'April / 10 Thursday / 2025', '10:27 AM', 'Unread'),
(43, 7, 'You successfully logged in to your account.', 'April / 10 Thursday / 2025', '10:34 AM', 'Unread'),
(44, 7, 'You successfully logged in to your account.', 'April / 10 Thursday / 2025', '10:40 AM', 'Unread'),
(45, 7, 'You successfully logged in to your account.', 'April / 10 Thursday / 2025', '10:47 AM', 'Unread'),
(46, 7, 'You successfully logged out to your account.', 'April / 10 Thursday / 2025', '10:48 AM', 'Unread'),
(47, 7, 'You successfully logged in to your account.', 'April / 11 Friday / 2025', '9:52 AM', 'Unread'),
(48, 7, 'You successfully logged in to your account.', 'April / 11 Friday / 2025', '9:54 AM', 'Unread'),
(49, 7, 'You successfully logged in to your account.', 'April / 11 Friday / 2025', '10:04 AM', 'Unread'),
(50, 7, 'You successfully logged in to your account.', 'April / 11 Friday / 2025', '3:18 PM', 'Unread'),
(51, 7, 'You successfully logged in to your account.', 'April / 11 Friday / 2025', '3:25 PM', 'Unread'),
(52, 7, 'You successfully logged out to your account.', 'April / 11 Friday / 2025', '4:18 PM', 'Unread'),
(53, 7, 'You successfully logged in to your account.', 'April / 11 Friday / 2025', '11:44 PM', 'Unread'),
(54, 7, 'You successfully logged out to your account.', 'April / 12 Saturday / 2025', '12:06 AM', 'Unread'),
(55, 7, 'You successfully logged in to your account.', 'April / 12 Saturday / 2025', '12:06 AM', 'Unread'),
(56, 7, 'You successfully logged in to your account.', 'April / 12 Saturday / 2025', '12:29 AM', 'Unread'),
(57, 7, 'You successfully logged in to your account.', 'April / 12 Saturday / 2025', '12:35 AM', 'Unread'),
(58, 7, 'You successfully logged in to your account.', 'April / 12 Saturday / 2025', '12:42 AM', 'Unread'),
(59, 7, 'You successfully logged in to your account.', 'April / 12 Saturday / 2025', '12:50 AM', 'Unread'),
(60, 7, 'You successfully logged in to your account.', 'April / 12 Saturday / 2025', '3:47 PM', 'Unread'),
(61, 7, 'You successfully logged in to your account.', 'April / 12 Saturday / 2025', '4:13 PM', 'Unread'),
(62, 7, 'You successfully logged in to your account.', 'April / 12 Saturday / 2025', '4:14 PM', 'Unread'),
(63, 7, 'You successfully logged in to your account.', 'April / 12 Saturday / 2025', '4:30 PM', 'Unread'),
(64, 7, 'You successfully logged in to your account.', 'April / 22 Tuesday / 2025', '10:00 AM', 'Unread'),
(65, 7, 'You successfully logged in to your account.', 'April / 22 Tuesday / 2025', '10:09 AM', 'Unread'),
(66, 7, 'You successfully logged in to your account.', 'April / 22 Tuesday / 2025', '11:11 AM', 'Unread'),
(67, 7, 'You successfully logged in to your account.', 'April / 22 Tuesday / 2025', '1:39 PM', 'Unread'),
(68, 7, 'You successfully logged in to your account.', 'April / 22 Tuesday / 2025', '1:50 PM', 'Unread'),
(69, 7, 'You successfully logged in to your account.', 'April / 22 Tuesday / 2025', '1:54 PM', 'Unread'),
(70, 7, 'You successfully logged in to your account.', 'April / 22 Tuesday / 2025', '2:03 PM', 'Unread'),
(71, 7, 'You successfully logged in to your account.', 'April / 22 Tuesday / 2025', '2:05 PM', 'Unread'),
(72, 7, 'You successfully logged in to your account.', 'April / 22 Tuesday / 2025', '2:10 PM', 'Unread'),
(73, 7, 'You successfully logged in to your account.', 'April / 22 Tuesday / 2025', '2:13 PM', 'Unread'),
(74, 7, 'You successfully logged in to your account.', 'April / 22 Tuesday / 2025', '2:15 PM', 'Unread'),
(75, 7, 'You successfully logged out to your account.', 'April / 22 Tuesday / 2025', '2:24 PM', 'Unread'),
(76, 7, 'You successfully logged in to your account.', 'April / 23 Wednesday / 2025', '8:42 AM', 'Unread'),
(77, 7, 'You successfully logged in to your account.', 'April / 23 Wednesday / 2025', '9:00 AM', 'Unread'),
(78, 7, 'You successfully logged in to your account.', 'April / 23 Wednesday / 2025', '9:50 AM', 'Unread'),
(79, 7, 'You successfully logged in to your account.', 'April / 23 Wednesday / 2025', '9:57 AM', 'Unread'),
(80, 7, 'You successfully logged in to your account.', 'April / 23 Wednesday / 2025', '10:02 AM', 'Unread'),
(81, 7, 'You successfully logged in to your account.', 'April / 23 Wednesday / 2025', '10:06 AM', 'Unread'),
(82, 7, 'You successfully logged in to your account.', 'April / 23 Wednesday / 2025', '10:16 AM', 'Unread'),
(83, 7, 'You successfully logged out to your account.', 'April / 23 Wednesday / 2025', '3:30 PM', 'Unread'),
(84, 7, 'You successfully logged in to your account.', 'April / 23 Wednesday / 2025', '3:30 PM', 'Unread'),
(85, 7, 'You successfully logged in to your account.', 'April / 24 Thursday / 2025', '9:32 AM', 'Unread'),
(86, 7, 'You successfully logged in to your account.', 'April / 24 Thursday / 2025', '9:35 AM', 'Unread'),
(87, 7, 'You successfully logged in to your account.', 'April / 24 Thursday / 2025', '9:51 AM', 'Unread'),
(88, 7, 'You successfully logged in to your account.', 'April / 24 Thursday / 2025', '10:28 AM', 'Unread'),
(89, 7, 'You successfully logged in to your account.', 'April / 24 Thursday / 2025', '10:45 AM', 'Unread'),
(90, 7, 'You successfully logged in to your account.', 'April / 24 Thursday / 2025', '10:46 AM', 'Unread'),
(91, 7, 'You successfully logged in to your account.', 'April / 24 Thursday / 2025', '10:48 AM', 'Unread'),
(92, 7, 'You successfully logged in to your account.', 'April / 24 Thursday / 2025', '10:54 AM', 'Unread'),
(93, 7, 'You successfully logged in to your account.', 'April / 24 Thursday / 2025', '11:01 AM', 'Unread'),
(94, 7, 'You successfully logged in to your account.', 'April / 24 Thursday / 2025', '9:06 PM', 'Unread'),
(95, 7, 'You successfully logged in to your account.', 'April / 29 Tuesday / 2025', '8:21 AM', 'Unread'),
(96, 7, 'You successfully logged in to your account.', 'April / 29 Tuesday / 2025', '9:25 AM', 'Unread'),
(97, 7, 'You successfully logged in to your account.', 'May / 05 Monday / 2025', '2:57 PM', 'Unread'),
(98, 7, 'You successfully logged out to your account.', 'May / 05 Monday / 2025', '2:57 PM', 'Unread'),
(99, 7, 'You successfully logged in to your account.', 'May / 05 Monday / 2025', '3:05 PM', 'Unread'),
(100, 7, 'You successfully logged in to your account.', 'May / 05 Monday / 2025', '3:21 PM', 'Unread'),
(101, 7, 'You successfully logged in to your account.', 'May / 05 Monday / 2025', '3:30 PM', 'Unread'),
(102, 7, 'You successfully logged in to your account.', 'May / 05 Monday / 2025', '3:39 PM', 'Unread'),
(103, 7, 'You successfully logged in to your account.', 'May / 05 Monday / 2025', '3:40 PM', 'Unread'),
(104, 7, 'You successfully logged in to your account.', 'May / 05 Monday / 2025', '3:47 PM', 'Unread'),
(105, 7, 'You successfully logged out to your account.', 'May / 05 Monday / 2025', '3:48 PM', 'Unread'),
(106, 7, 'You successfully logged in to your account.', 'May / 06 Tuesday / 2025', '10:28 AM', 'Unread'),
(107, 7, 'You successfully logged in to your account.', 'May / 06 Tuesday / 2025', '10:53 AM', 'Unread'),
(108, 7, 'You successfully logged in to your account.', 'May / 06 Tuesday / 2025', '11:02 AM', 'Unread'),
(109, 7, 'You successfully logged in to your account.', 'May / 06 Tuesday / 2025', '11:15 AM', 'Unread'),
(110, 7, 'You successfully logged in to your account.', 'May / 09 Friday / 2025', '3:11 PM', 'Unread'),
(111, 7, 'You successfully logged in to your account.', 'May / 09 Friday / 2025', '6:10 PM', 'Unread'),
(112, 7, 'You successfully logged in to your account.', 'May / 09 Friday / 2025', '6:15 PM', 'Unread'),
(113, 7, 'You successfully logged in to your account.', 'May / 09 Friday / 2025', '6:21 PM', 'Unread'),
(114, 7, 'You successfully logged in to your account.', 'May / 12 Monday / 2025', '3:31 PM', 'Unread'),
(115, 7, 'You successfully logged in to your account.', 'May / 12 Monday / 2025', '4:56 PM', 'Unread'),
(116, 7, 'You successfully logged in to your account.', 'May / 12 Monday / 2025', '4:56 PM', 'Unread'),
(117, 7, 'You successfully logged in to your account.', 'May / 15 Thursday / 2025', '2:02 PM', 'Unread'),
(118, 7, 'You successfully logged in to your account.', 'May / 15 Thursday / 2025', '8:39 PM', 'Unread'),
(119, 7, 'You successfully logged in to your account.', 'May / 17 Saturday / 2025', '10:37 AM', 'Unread'),
(120, 7, 'You successfully logged in to your account.', 'May / 17 Saturday / 2025', '11:54 AM', 'Unread'),
(121, 7, 'You successfully logged in to your account.', 'May / 17 Saturday / 2025', '2:21 PM', 'Unread'),
(122, 7, 'You successfully logged in to your account.', 'May / 23 Friday / 2025', '11:34 PM', 'Unread'),
(123, 7, 'You successfully logged in to your account.', 'May / 23 Friday / 2025', '11:43 PM', 'Unread'),
(124, 7, 'You successfully logged in to your account.', 'May / 24 Saturday / 2025', '1:04 AM', 'Unread'),
(125, 7, 'You successfully logged in to your account.', 'May / 24 Saturday / 2025', '9:17 PM', 'Unread'),
(126, 7, 'You successfully logged in to your account.', 'May / 24 Saturday / 2025', '9:46 PM', 'Unread'),
(127, 7, 'You successfully logged in to your account.', 'May / 24 Saturday / 2025', '9:59 PM', 'Unread'),
(128, 7, 'You successfully logged out to your account.', 'May / 24 Saturday / 2025', '10:04 PM', 'Unread'),
(129, 7, 'You successfully logged in to your account.', 'May / 29 Thursday / 2025', '8:58 AM', 'Unread'),
(130, 7, 'You successfully logged in to your account.', 'May / 29 Thursday / 2025', '9:05 AM', 'Unread'),
(131, 7, 'You successfully logged in to your account.', 'May / 29 Thursday / 2025', '9:58 AM', 'Unread'),
(132, 7, 'You successfully logged in to your account.', 'May / 29 Thursday / 2025', '9:58 AM', 'Unread'),
(133, 7, 'You successfully logged in to your account.', 'May / 29 Thursday / 2025', '9:59 AM', 'Unread'),
(134, 7, 'You successfully logged in to your account.', 'May / 29 Thursday / 2025', '9:30 PM', 'Unread'),
(135, 7, 'You successfully logged in to your account.', 'May / 29 Thursday / 2025', '9:34 PM', 'Unread'),
(136, 7, 'You successfully logged in to your account.', 'May / 29 Thursday / 2025', '9:35 PM', 'Unread'),
(137, 7, 'You successfully logged in to your account.', 'May / 29 Thursday / 2025', '9:38 PM', 'Unread'),
(138, 7, 'You successfully logged in to your account.', 'May / 29 Thursday / 2025', '9:40 PM', 'Unread'),
(139, 7, 'You successfully logged in to your account.', 'May / 29 Thursday / 2025', '9:42 PM', 'Unread'),
(140, 7, 'You successfully logged in to your account.', 'May / 29 Thursday / 2025', '9:53 PM', 'Unread'),
(141, 7, 'You successfully logged in to your account.', 'May / 29 Thursday / 2025', '10:00 PM', 'Unread'),
(142, 7, 'You successfully logged in to your account.', 'May / 30 Friday / 2025', '12:11 AM', 'Unread'),
(143, 7, 'You successfully logged in to your account.', 'May / 30 Friday / 2025', '12:17 AM', 'Unread'),
(144, 7, 'You successfully logged in to your account.', 'May / 30 Friday / 2025', '12:52 AM', 'Unread'),
(145, 7, 'You successfully logged in to your account.', 'May / 30 Friday / 2025', '12:59 AM', 'Unread'),
(146, 7, 'You successfully logged in to your account.', 'May / 30 Friday / 2025', '1:08 AM', 'Unread'),
(147, 7, 'You successfully logged in to your account.', 'May / 30 Friday / 2025', '1:12 AM', 'Unread'),
(148, 7, 'You successfully logged in to your account.', 'May / 30 Friday / 2025', '2:46 AM', 'Unread'),
(149, 7, 'You successfully logged in to your account.', 'May / 30 Friday / 2025', '2:52 AM', 'Unread'),
(150, 7, 'You successfully logged in to your account.', 'May / 31 Saturday / 2025', '5:08 PM', 'Unread'),
(151, 7, 'You successfully logged in to your account.', 'May / 31 Saturday / 2025', '5:55 PM', 'Unread'),
(152, 7, 'You successfully logged in to your account.', 'May / 31 Saturday / 2025', '5:56 PM', 'Unread'),
(153, 7, 'You successfully logged in to your account.', 'May / 31 Saturday / 2025', '9:46 PM', 'Unread'),
(154, 7, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '11:19 AM', 'Unread'),
(155, 7, 'You successfully logged out to your account.', 'June / 01 Sunday / 2025', '1:20 PM', 'Unread'),
(156, 7, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '1:22 PM', 'Unread'),
(157, 7, 'You successfully logged out to your account.', 'June / 01 Sunday / 2025', '1:45 PM', 'Unread'),
(158, 7, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '1:45 PM', 'Unread'),
(159, 7, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '2:13 PM', 'Unread'),
(160, 7, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '3:53 PM', 'Unread'),
(161, 7, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '4:07 PM', 'Unread'),
(162, 7, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '4:07 PM', 'Unread'),
(163, 7, 'You successfully logged out to your account.', 'June / 01 Sunday / 2025', '5:34 PM', 'Unread'),
(164, 8, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '5:41 PM', 'Unread'),
(165, 8, 'You successfully logged out to your account.', 'June / 01 Sunday / 2025', '5:42 PM', 'Unread'),
(166, 7, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '5:42 PM', 'Unread'),
(167, 7, 'You successfully logged out to your account.', 'June / 01 Sunday / 2025', '5:42 PM', 'Unread'),
(168, 8, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '5:43 PM', 'Unread'),
(169, 8, 'You successfully logged out to your account.', 'June / 01 Sunday / 2025', '6:15 PM', 'Unread'),
(170, 8, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '6:22 PM', 'Unread'),
(171, 8, 'You successfully logged out to your account.', 'June / 01 Sunday / 2025', '6:29 PM', 'Unread'),
(172, 8, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '6:29 PM', 'Unread'),
(173, 8, 'You successfully logged out to your account.', 'June / 01 Sunday / 2025', '6:31 PM', 'Unread'),
(174, 8, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '6:32 PM', 'Unread'),
(175, 8, 'You successfully logged out to your account.', 'June / 01 Sunday / 2025', '6:39 PM', 'Unread'),
(176, 8, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '6:40 PM', 'Unread'),
(177, 8, 'You successfully logged out to your account.', 'June / 01 Sunday / 2025', '6:41 PM', 'Unread'),
(178, 8, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '6:42 PM', 'Unread'),
(179, 7, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '6:49 PM', 'Unread'),
(180, 7, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '6:50 PM', 'Unread'),
(181, 7, 'You successfully logged out to your account.', 'June / 01 Sunday / 2025', '6:51 PM', 'Unread'),
(182, 8, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '6:51 PM', 'Unread'),
(183, 8, 'You successfully logged out to your account.', 'June / 01 Sunday / 2025', '6:51 PM', 'Unread'),
(184, 7, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '6:51 PM', 'Unread'),
(185, 7, 'You successfully logged out to your account.', 'June / 01 Sunday / 2025', '6:52 PM', 'Unread'),
(186, 8, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '6:52 PM', 'Unread'),
(187, 8, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '6:54 PM', 'Unread'),
(188, 8, 'You successfully logged out to your account.', 'June / 01 Sunday / 2025', '6:55 PM', 'Unread'),
(189, 8, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '6:55 PM', 'Unread'),
(190, 8, 'You successfully logged out to your account.', 'June / 01 Sunday / 2025', '6:56 PM', 'Unread'),
(191, 8, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '6:56 PM', 'Unread'),
(192, 8, 'You successfully logged out to your account.', 'June / 01 Sunday / 2025', '7:00 PM', 'Unread'),
(193, 8, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '7:01 PM', 'Unread'),
(194, 8, 'You successfully logged out to your account.', 'June / 01 Sunday / 2025', '7:04 PM', 'Unread'),
(195, 8, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '7:05 PM', 'Unread'),
(196, 8, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '7:14 PM', 'Unread'),
(197, 8, 'You successfully logged out to your account.', 'June / 01 Sunday / 2025', '7:15 PM', 'Unread'),
(198, 8, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '7:16 PM', 'Unread'),
(199, 8, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '7:18 PM', 'Unread'),
(200, 8, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '7:19 PM', 'Unread'),
(201, 8, 'You successfully logged out to your account.', 'June / 01 Sunday / 2025', '7:31 PM', 'Unread'),
(202, 7, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '12:19 PM', 'Unread'),
(203, 7, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '1:05 PM', 'Unread'),
(204, 7, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '1:22 PM', 'Unread'),
(205, 7, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '1:22 PM', 'Unread'),
(206, 7, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '1:33 PM', 'Unread'),
(207, 7, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '1:40 PM', 'Unread'),
(208, 8, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '1:57 PM', 'Unread'),
(209, 7, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '2:41 PM', 'Unread'),
(210, 8, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '2:52 PM', 'Unread'),
(211, 7, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '2:56 PM', 'Unread'),
(212, 8, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '3:14 PM', 'Unread'),
(213, 8, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '3:16 PM', 'Unread'),
(214, 8, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '4:41 PM', 'Unread'),
(215, 8, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '4:43 PM', 'Unread'),
(216, 8, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '4:47 PM', 'Unread'),
(217, 8, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '5:03 PM', 'Unread'),
(218, 8, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '5:20 PM', 'Unread'),
(219, 7, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '6:15 PM', 'Unread'),
(220, 8, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '8:22 PM', 'Unread'),
(221, 7, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '8:33 PM', 'Unread'),
(222, 7, 'You successfully logged out to your account.', 'June / 02 Monday / 2025', '8:34 PM', 'Unread'),
(223, 7, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '8:34 PM', 'Unread'),
(224, 8, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '8:35 PM', 'Unread'),
(225, 8, 'You successfully logged out to your account.', 'June / 02 Monday / 2025', '8:36 PM', 'Unread'),
(226, 8, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '8:36 PM', 'Unread'),
(227, 8, 'You successfully logged out to your account.', 'June / 02 Monday / 2025', '8:37 PM', 'Unread'),
(228, 8, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '8:37 PM', 'Unread'),
(229, 8, 'You successfully logged out to your account.', 'June / 02 Monday / 2025', '8:37 PM', 'Unread'),
(230, 8, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '8:38 PM', 'Unread'),
(231, 8, 'You successfully logged out to your account.', 'June / 02 Monday / 2025', '8:46 PM', 'Unread'),
(232, 8, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '8:53 PM', 'Unread'),
(233, 8, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '9:30 PM', 'Unread'),
(234, 8, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '9:38 PM', 'Unread'),
(235, 7, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '9:42 PM', 'Unread'),
(236, 7, 'You successfully logged out to your account.', 'June / 02 Monday / 2025', '9:43 PM', 'Unread'),
(237, 8, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '9:44 PM', 'Unread'),
(238, 8, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '10:16 PM', 'Unread'),
(239, 7, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '10:23 PM', 'Unread'),
(240, 7, 'You successfully logged out to your account.', 'June / 02 Monday / 2025', '11:18 PM', 'Unread'),
(241, 8, 'You successfully logged in to your account.', 'June / 03 Tuesday / 2025', '1:54 PM', 'Unread'),
(242, 8, 'You successfully logged in to your account.', 'June / 03 Tuesday / 2025', '10:08 PM', 'Unread'),
(243, 7, 'You successfully logged in to your account.', 'June / 05 Thursday / 2025', '10:14 AM', 'Unread'),
(244, 7, 'You successfully logged out to your account.', 'June / 05 Thursday / 2025', '11:18 AM', 'Unread'),
(245, 8, 'You successfully logged in to your account.', 'June / 05 Thursday / 2025', '11:18 AM', 'Unread'),
(246, 7, 'You successfully logged in to your account.', 'June / 08 Sunday / 2025', '9:41 PM', 'Unread'),
(247, 7, 'You successfully logged out to your account.', 'June / 08 Sunday / 2025', '9:46 PM', 'Unread'),
(248, 7, 'You successfully logged in to your account.', 'June / 08 Sunday / 2025', '9:56 PM', 'Unread'),
(249, 7, 'You successfully logged out to your account.', 'June / 08 Sunday / 2025', '9:56 PM', 'Unread'),
(250, 8, 'You successfully logged in to your account.', 'June / 08 Sunday / 2025', '9:56 PM', 'Unread'),
(251, 8, 'You successfully logged out to your account.', 'June / 08 Sunday / 2025', '10:03 PM', 'Unread'),
(252, 7, 'You successfully logged in to your account.', 'June / 09 Monday / 2025', '4:53 PM', 'Unread'),
(253, 8, 'You successfully logged in to your account.', 'June / 09 Monday / 2025', '4:59 PM', 'Unread'),
(254, 8, 'You successfully logged in to your account.', 'June / 09 Monday / 2025', '5:26 PM', 'Unread'),
(255, 8, 'You successfully logged in to your account.', 'June / 09 Monday / 2025', '5:32 PM', 'Unread'),
(256, 8, 'You successfully logged in to your account.', 'June / 09 Monday / 2025', '6:07 PM', 'Unread'),
(257, 8, 'You successfully logged in to your account.', 'June / 09 Monday / 2025', '6:31 PM', 'Unread'),
(258, 8, 'You successfully logged in to your account.', 'June / 09 Monday / 2025', '10:30 PM', 'Unread'),
(259, 8, 'You successfully logged out to your account.', 'June / 09 Monday / 2025', '10:30 PM', 'Unread'),
(260, 8, 'You successfully logged in to your account.', 'June / 09 Monday / 2025', '11:14 PM', 'Unread'),
(261, 8, 'You successfully logged in to your account.', 'June / 10 Tuesday / 2025', '1:28 PM', 'Unread'),
(262, 8, 'You successfully logged in to your account.', 'June / 10 Tuesday / 2025', '5:03 PM', 'Unread'),
(263, 8, 'You successfully logged in to your account.', 'June / 10 Tuesday / 2025', '10:57 PM', 'Unread'),
(264, 8, 'You successfully logged in to your account.', 'June / 11 Wednesday / 2025', '10:50 AM', 'Unread'),
(265, 8, 'You successfully logged in to your account.', 'June / 11 Wednesday / 2025', '1:40 PM', 'Unread'),
(266, 7, 'You successfully logged in to your account.', 'June / 11 Wednesday / 2025', '1:48 PM', 'Unread'),
(267, 7, 'You successfully logged out to your account.', 'June / 11 Wednesday / 2025', '1:48 PM', 'Unread'),
(268, 8, 'You successfully logged in to your account.', 'June / 11 Wednesday / 2025', '6:45 PM', 'Unread'),
(269, 8, 'You successfully logged in to your account.', 'June / 11 Wednesday / 2025', '7:33 PM', 'Unread'),
(270, 8, 'You successfully logged in to your account.', 'June / 11 Wednesday / 2025', '10:12 PM', 'Unread'),
(271, 8, 'You successfully logged out to your account.', 'June / 11 Wednesday / 2025', '10:16 PM', 'Unread'),
(272, 8, 'You successfully logged in to your account.', 'June / 11 Wednesday / 2025', '10:17 PM', 'Unread'),
(273, 8, 'You successfully logged in to your account.', 'June / 11 Wednesday / 2025', '10:25 PM', 'Unread'),
(274, 8, 'You successfully logged out to your account.', 'June / 11 Wednesday / 2025', '10:26 PM', 'Unread'),
(275, 8, 'You successfully logged in to your account.', 'June / 11 Wednesday / 2025', '10:26 PM', 'Unread'),
(276, 8, 'You successfully logged out to your account.', 'June / 11 Wednesday / 2025', '10:30 PM', 'Unread'),
(277, 8, 'You successfully logged in to your account.', 'June / 11 Wednesday / 2025', '10:30 PM', 'Unread'),
(278, 8, 'You successfully logged in to your account.', 'June / 12 Thursday / 2025', '1:26 PM', 'Unread'),
(279, 7, 'You successfully logged in to your account.', 'June / 12 / 2025', '4:06 PM', 'Unread'),
(280, 7, 'You successfully logged in to your account.', 'June / 12 / 2025', '4:06 PM', 'Unread'),
(281, 7, 'You successfully logged in to your account.', 'June / 12 / 2025', '4:57 PM', 'Unread'),
(282, 8, 'You successfully logged in to your account.', 'June / 12 / 2025', '4:58 PM', 'Unread'),
(283, 8, 'You successfully logged in to your account.', 'June / 12 / 2025', '5:00 PM', 'Unread'),
(284, 7, 'You successfully logged in to your account.', 'June / 12 / 2025', '5:04 PM', 'Unread'),
(285, 7, 'You successfully logged in to your account.', 'June / 12 / 2025', '5:06 PM', 'Unread'),
(286, 8, 'You successfully logged in to your account.', 'June / 12 / 2025', '5:06 PM', 'Unread'),
(287, 8, 'You successfully logged in to your account.', 'June / 12 / 2025', '5:30 PM', 'Unread'),
(288, 8, 'You successfully logged out to your account.', 'June / 12 Thursday / 2025', '8:33 PM', 'Unread'),
(289, 7, 'You successfully logged in to your account.', 'June / 12 / 2025', '8:34 PM', 'Unread'),
(290, 7, 'You successfully logged out to your account.', 'June / 12 Thursday / 2025', '8:34 PM', 'Unread'),
(291, 8, 'You successfully logged in to your account.', 'June / 12 / 2025', '8:35 PM', 'Unread'),
(292, 8, 'You successfully logged out to your account.', 'June / 12 Thursday / 2025', '9:47 PM', 'Unread'),
(293, 8, 'You successfully logged in to your account.', 'June / 12 / 2025', '9:48 PM', 'Unread'),
(294, 8, 'You successfully logged in to your account.', 'June / 12 / 2025', '9:54 PM', 'Unread'),
(295, 8, 'You successfully logged in to your account.', 'June / 12 / 2025', '10:07 PM', 'Unread'),
(296, 8, 'You successfully logged in to your account.', 'June / 12 / 2025', '10:10 PM', 'Unread'),
(297, 8, 'You successfully logged in to your account.', 'June / 12 / 2025', '10:55 PM', 'Unread'),
(298, 8, 'You successfully logged out to your account.', 'June / 12 Thursday / 2025', '11:03 PM', 'Unread'),
(299, 8, 'You successfully logged in to your account.', 'June / 12 / 2025', '11:04 PM', 'Unread'),
(300, 8, 'You successfully logged out to your account.', 'June / 13 Friday / 2025', '12:04 AM', 'Unread'),
(301, 7, 'You successfully logged in to your account.', 'June / 14 / 2025', '10:18 AM', 'Unread'),
(302, 7, 'You successfully logged out to your account.', 'June / 14 Saturday / 2025', '10:19 AM', 'Unread'),
(303, 7, 'You successfully logged in to your account.', 'June / 14 / 2025', '10:45 AM', 'Unread'),
(304, 7, 'You successfully logged out to your account.', 'June / 14 Saturday / 2025', '10:45 AM', 'Unread'),
(305, 7, 'You successfully logged in to your account.', 'June / 14 / 2025', '12:03 PM', 'Unread'),
(306, 7, 'You successfully logged out to your account.', 'June / 14 Saturday / 2025', '12:03 PM', 'Unread'),
(307, 8, 'You successfully logged in to your account.', 'June / 14 / 2025', '12:03 PM', 'Unread'),
(308, 8, 'You successfully logged in to your account.', 'June / 14 / 2025', '12:20 PM', 'Unread'),
(309, 8, 'You successfully logged in to your account.', 'June / 14 / 2025', '12:22 PM', 'Unread'),
(310, 8, 'You successfully logged in to your account.', 'June / 14 / 2025', '12:37 PM', 'Unread'),
(311, 8, 'You successfully logged in to your account.', 'June / 14 / 2025', '12:37 PM', 'Unread'),
(312, 8, 'You successfully logged in to your account.', 'June / 14 / 2025', '12:41 PM', 'Unread'),
(313, 8, 'You successfully logged in to your account.', 'June / 14 / 2025', '4:23 PM', 'Unread'),
(314, 8, 'You successfully logged in to your account.', 'June / 14 / 2025', '8:30 PM', 'Unread'),
(315, 8, 'You successfully logged out to your account.', 'June / 14 Saturday / 2025', '8:35 PM', 'Unread'),
(316, 2, 'You successfully logged in to your account.', 'June / 14 / 2025', '9:02 PM', 'Unread'),
(317, 2, 'You successfully logged in to your account.', 'June / 14 / 2025', '9:12 PM', 'Unread'),
(318, 9, 'You successfully logged in to your account.', 'June / 14 / 2025', '9:14 PM', 'Unread'),
(319, 9, 'You successfully logged out to your account.', 'June / 14 Saturday / 2025', '9:14 PM', 'Unread'),
(320, 4, 'You successfully logged in to your account.', 'June / 15 / 2025', '10:46 PM', 'Unread'),
(321, 6, 'You successfully logged in to your account.', 'June / 15 / 2025', '11:24 PM', 'Unread'),
(322, 10, 'You successfully logged in to your account.', 'June / 15 / 2025', '11:25 PM', 'Unread'),
(323, 10, 'You successfully logged in to your account.', 'June / 15 / 2025', '11:42 PM', 'Unread'),
(324, 10, 'You successfully logged out to your account.', 'June / 15 Sunday / 2025', '11:56 PM', 'Unread');

-- --------------------------------------------------------

--
-- Table structure for table `coordinators_account`
--

CREATE TABLE `coordinators_account` (
  `id` int(11) NOT NULL,
  `uniqueID` varchar(200) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `faculty_id` varchar(20) NOT NULL,
  `coor_dept` varchar(200) NOT NULL,
  `course_handled` varchar(200) NOT NULL,
  `complete_address` varchar(300) NOT NULL,
  `phone_number` varchar(13) NOT NULL,
  `coordinators_email` varchar(200) NOT NULL,
  `coordinators_password` varchar(200) NOT NULL,
  `coordinators_profile_picture` varchar(500) NOT NULL,
  `verification_code` int(8) NOT NULL,
  `verify_status` varchar(80) NOT NULL DEFAULT 'Not Verified',
  `online_offlineStatus` varchar(50) NOT NULL DEFAULT 'Offline',
  `assigned_section` varchar(255) NOT NULL,
  `second_assigned_section` varchar(255) NOT NULL,
  `active_student` int(11) NOT NULL,
  `access_level` int(11) NOT NULL DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coordinators_account`
--

INSERT INTO `coordinators_account` (`id`, `uniqueID`, `first_name`, `middle_name`, `last_name`, `faculty_id`, `coor_dept`, `course_handled`, `complete_address`, `phone_number`, `coordinators_email`, `coordinators_password`, `coordinators_profile_picture`, `verification_code`, `verify_status`, `online_offlineStatus`, `assigned_section`, `second_assigned_section`, `active_student`, `access_level`) VALUES
(1, '662517f7a16b25026', 'Prof. Carlos', 'A', 'Ramos', 'FAC001', 'College of Engineering', 'Bachelor of Science in Civil Engineering', 'Sta. Mesa, Manila', '09123456789', 'test5@gmail.com', '202cb962ac59075b964b07152d234b70', '../student_file_images/coor1.jpg', 985232, 'Verified', 'Offline', '', '', 0, 2),
(2, '662517f7a16bd3010', 'Prof. Lourdes', 'B', 'Tan', 'FAC002', 'College of Computer Studies', 'Bachelor of Science in Computer Science', 'Quezon City', '09234567890', 'test6@gmail.com', '202cb962ac59075b964b07152d234b70', '../student_file_images/coor2.jpg', 582458, 'Verified', 'Offline', '', '', 0, 2),
(3, '662517f7a16be6633', 'Prof. Ricardo', 'C', 'Lim', 'FAC003', 'College of Business', 'Bachelor of Science in Accountancy', 'Mandaluyong City', '09345678901', 'test7@gmail.com', '202cb962ac59075b964b07152d234b70', '../student_file_images/coor3.jpg', 514148, 'Verified', 'Offline', '', '', 0, 2),
(4, '662517f7a16bf9002', 'Prof. Sofia', 'D', 'Ong', 'FAC004', 'College of Science', 'Bachelor of Science in Biology', 'Makati City', '09456789012', 'test8@gmail.com', '202cb962ac59075b964b07152d234b70', '../student_file_images/coor4.jpg', 859603, 'Verified', 'Offline', '', '', 0, 2),
(7, '67f3f307eaa3b1846', 'Yagoda', 'Camba', 'Maratas', '111111', 'College of Technology', 'Diploma in Computer Engineering Technology (DCET)', 'MANILA', '09123123123', 'yagodamaratas28@gmail.com', '32250170a0dca92d53ec9624f336ca24', '../student_file_images/67f3f307eaa40-noel.jpg', 285902, 'Verified', 'Offline', '', '', 0, 2),
(8, '683c1ffb256dd2016', 'Ian', 'De marcus', 'Paul', '2022-0001-MN-0', 'College of Technology', 'Diploma in Information Technology (DIT)', 'Villa Cuana, Pasig City, Manila', '094987677628', 'rajamaratas100@gmail.com', '32250170a0dca92d53ec9624f336ca24', '../student_file_images/683c1ffb256e3-270335944_3171425873141608_6264688726435691639_n.jpg', 463746, 'Verified', 'Offline', '', '', 0, 2),
(10, '5e1f00c4e0a14f94684ee577945d5', 'Aey', 'Em', 'Esguerra', '1234', '', '', '06 Ipapo st. Pandayan', '09123456789', 'aeyemako@gmail.com', 'a2ca2ed599c28a253392dffd4deb78d3', '', 941298, 'Verified', 'Offline', 'DIT 3-2', '', 0, 2);

-- --------------------------------------------------------

--
-- Table structure for table `courses_sections`
--

CREATE TABLE `courses_sections` (
  `id` int(11) NOT NULL,
  `course` varchar(100) NOT NULL,
  `section` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses_sections`
--

INSERT INTO `courses_sections` (`id`, `course`, `section`) VALUES
(1, 'DIT', '3-2');

-- --------------------------------------------------------

--
-- Table structure for table `daily_time_records`
--

CREATE TABLE `daily_time_records` (
  `id` int(11) NOT NULL,
  `assigned_dep` varchar(100) DEFAULT NULL,
  `sis_no` varchar(50) DEFAULT NULL,
  `section` varchar(50) DEFAULT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deployed_students`
--

CREATE TABLE `deployed_students` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `company_name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `endorsements`
--

CREATE TABLE `endorsements` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `first_name` varchar(70) NOT NULL,
  `middle_name` varchar(70) DEFAULT NULL,
  `last_name` varchar(70) NOT NULL,
  `section` varchar(150) NOT NULL,
  `date_submitted` date NOT NULL,
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `endorsement_documents`
--

CREATE TABLE `endorsement_documents` (
  `id` int(11) NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `document_type` varchar(50) NOT NULL,
  `uploaded_path` varchar(255) DEFAULT NULL,
  `student_id` int(11) NOT NULL,
  `upload_date` datetime DEFAULT NULL,
  `status` enum('pending','accepted','denied') NOT NULL DEFAULT 'pending',
  `medical_at_campus` enum('YES','NO') NOT NULL,
  `remarks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `endorsement_documents`
--

INSERT INTO `endorsement_documents` (`id`, `document_name`, `document_type`, `uploaded_path`, `student_id`, `upload_date`, `status`, `medical_at_campus`, `remarks`) VALUES
(46, 'testFileMOA.pdf', 'moa', '/PUP/student/uploads/moa/1_testFileMOA.pdf', 1, '2025-03-30 15:43:03', 'pending', 'YES', ''),
(47, 'Esguerra-CV.pdf', 'moa', '/PUP/student/uploads/moa/2_Esguerra-CV.pdf', 2, '2025-03-30 15:43:46', 'pending', 'YES', ''),
(65, 'CORNERSTEEL SYSTEMS CORPORATION.pdf', 'portfolio', '/PUP/student/uploads/portfolio/13_CORNERSTEEL SYSTEMS CORPORATION.pdf', 13, '2025-05-09 14:32:40', 'denied', 'YES', 'asdf'),
(70, 'RegistrationCertificate (4).pdf', 'coc', '/PUP/student/uploads/coc/13_RegistrationCertificate (4).pdf', 13, '2025-05-30 02:43:10', 'denied', 'YES', 'asdf'),
(79, 'Trainees_Section_DIT 3-2 (7).pdf', 'company_profile', '/PUP/student/uploads/company_profile/13_Trainees_Section_DIT 3-2 (7).pdf', 13, '2025-06-02 13:29:29', 'accepted', 'YES', ''),
(80, 'Trainees_Section_DIT 3-1 (1).pdf', 'contract', '/PUP/student/uploads/contract/13_Trainees_Section_DIT 3-1 (1).pdf', 13, '2025-06-02 13:29:42', 'accepted', 'YES', ''),
(81, 'Trainees_Section_DIT 3-2 (7).pdf', 'supervisor_details', '/PUP/student/uploads/supervisor_details/13_Trainees_Section_DIT 3-2 (7).pdf', 13, '2025-06-02 13:29:55', 'accepted', 'YES', ''),
(86, 'Trainees_Section_DIT 3-2 (7).pdf', 'medical', '/PUP/student/uploads/medical/13_Trainees_Section_DIT 3-2 (7).pdf', 13, '2025-06-02 13:31:52', 'accepted', 'NO', ''),
(87, 'Trainees_Section_DIT 3-2 (1).pdf', 'coe', '/PUP/student/uploads/coe/13_Trainees_Section_DIT 3-2 (1).pdf', 13, '2025-06-02 13:32:22', 'denied', 'YES', 'asdf'),
(88, 'Trainees_Section_DIT 3-2 (7).pdf', 'insurance', '/PUP/student/uploads/insurance/13_Trainees_Section_DIT 3-2 (7).pdf', 13, '2025-06-02 13:32:30', 'accepted', 'YES', ''),
(90, 'Trainees_Section_DIT 3-1.pdf', 'company_id', '/PUP/student/uploads/company_id/13_Trainees_Section_DIT 3-1.pdf', 13, '2025-06-02 16:43:07', 'denied', 'YES', 'asdf'),
(91, 'Trainees_Section_DIT 3-2 (10).pdf', 'eval_hte', '/PUP/student/uploads/eval_hte/13_Trainees_Section_DIT 3-2 (10).pdf', 13, '2025-06-02 17:19:26', 'accepted', 'YES', ''),
(92, 'PUP-Medical-Health-Information-Form-for-Students-2022-1.pdf', 'moa', '/PUP/student/uploads/moa/13_PUP-Medical-Health-Information-Form-for-Students-2022-1.pdf', 13, '2025-06-02 20:32:21', 'accepted', 'YES', ''),
(93, 'Trainees_Section_DIT 3-2 (1).pdf', 'internship_agreement', '/PUP/student/uploads/internship_agreement/13_Trainees_Section_DIT 3-2 (1).pdf', 13, '2025-06-02 20:32:41', 'denied', 'YES', 'asdf'),
(94, 'Trainees_Section_DIT 3-2 (3).pdf', 'consent_form', '/PUP/student/uploads/consent_form/13_Trainees_Section_DIT 3-2 (3).pdf', 13, '2025-06-02 20:33:26', 'accepted', 'YES', ''),
(95, 'PUP-Medical-Health-Information-Form-for-Students-2022 (1).pdf', 'eval_supervisor', '/PUP/student/uploads/eval_supervisor/13_PUP-Medical-Health-Information-Form-for-Students-2022 (1).pdf', 13, '2025-06-02 20:34:52', 'denied', 'YES', 'asdf'),
(96, 'Trainees_Section_DIT 3-1 (6).pdf', 'resume', '/PUP/student/uploads/resume/13_Trainees_Section_DIT 3-1 (6).pdf', 13, '2025-06-09 17:24:32', 'accepted', 'YES', ''),
(97, 'Trainees_Section_DIT 3-1 (6).pdf', 'Intent_Letter', '/PUP/student/uploads/intent_letter/13_Trainees_Section_DIT 3-1 (6).pdf', 13, '2025-06-10 17:02:56', 'denied', 'YES', 'asdfasdf'),
(98, 'Trainees_Section_DIT 3-2 (9).pdf', 'endorsement', '/PUP/student/uploads/endorsement/13_Trainees_Section_DIT 3-2 (9).pdf', 13, '2025-06-10 17:03:16', 'accepted', 'YES', ''),
(99, 'Trainees_Section_DIT 3-2 (9).pdf', 'nda', '/PUP/student/uploads/nda/13_Trainees_Section_DIT 3-2 (9).pdf', 13, '2025-06-10 17:03:34', 'denied', 'YES', 'asdf');

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `portal` enum('Student','Adviser','HTE','All') NOT NULL DEFAULT 'All',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `interns`
--

CREATE TABLE `interns` (
  `id` int(11) NOT NULL,
  `sis_no` varchar(50) NOT NULL,
  `section` varchar(50) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `status` enum('Pending','Ongoing','Completed','Deployed') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `internship_experience`
--

CREATE TABLE `internship_experience` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `uploaded_path` varchar(255) NOT NULL,
  `upload_date` datetime NOT NULL,
  `status` enum('pending','accepted','denied') DEFAULT 'pending',
  `remarks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `internship_experience`
--

INSERT INTO `internship_experience` (`id`, `student_id`, `document_name`, `uploaded_path`, `upload_date`, `status`, `remarks`) VALUES
(1, 2, 'OJT Web Portal Student DTR.pdf', '/PUP/student/uploads/internship_experience/2_OJT Web Portal Student DTR.pdf', '2025-04-19 23:26:04', 'pending', ''),
(2, 13, 'New_MOA_Template test.pdf', '/PUP/student/uploads/internship_experience/13_New_MOA_Template test.pdf', '2025-04-24 10:53:50', 'accepted', '');

-- --------------------------------------------------------

--
-- Table structure for table `intern_deployments`
--

CREATE TABLE `intern_deployments` (
  `id` int(11) NOT NULL,
  `intern_id` varchar(50) NOT NULL,
  `department` varchar(100) NOT NULL,
  `assigned_supervisor` varchar(100) NOT NULL,
  `deployed_by` int(11) NOT NULL,
  `deployment_date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `required_hours` decimal(6,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meetings`
--

CREATE TABLE `meetings` (
  `id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `meeting_type` varchar(50) NOT NULL DEFAULT 'Zoom Meeting',
  `link` varchar(255) NOT NULL,
  `passcode` varchar(50) DEFAULT NULL,
  `meeting_date` date NOT NULL,
  `meeting_time` varchar(20) NOT NULL,
  `agenda` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `portal` enum('student','faculty','hte','all') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meetings`
--

INSERT INTO `meetings` (`id`, `created_by`, `meeting_type`, `link`, `passcode`, `meeting_date`, `meeting_time`, `agenda`, `created_at`, `portal`) VALUES
(2, 1, 'Zoom Meeting', 'https://www.linkedin.com/in/agatha-may-esguerra-9a5b/', 'asdfsdf', '2025-06-19', '1pm', 'asdf', '2025-06-14 13:13:38', 'faculty');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `conversation_id` int(11) DEFAULT NULL COMMENT 'Can reference either conversations or conversations_coordinator_hte',
  `conversation_type` enum('student_supervisor','coordinator_supervisor') NOT NULL,
  `sender_id` varchar(50) NOT NULL,
  `sender_type` enum('student','supervisor','coordinator','admin') NOT NULL,
  `content` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages_coordinator_student`
--

CREATE TABLE `messages_coordinator_student` (
  `id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `sender_type` enum('coordinator','student') NOT NULL,
  `content` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages_coordinator_supervisor`
--

CREATE TABLE `messages_coordinator_supervisor` (
  `id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `sender_type` enum('coordinator','supervisor') NOT NULL,
  `content` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `moa_form`
--

CREATE TABLE `moa_form` (
  `id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `company_address` text NOT NULL,
  `nature_of_business` varchar(255) DEFAULT NULL,
  `contact_person_name` varchar(255) DEFAULT NULL,
  `company_position` varchar(100) DEFAULT NULL,
  `email_address` varchar(150) DEFAULT NULL,
  `start_date_validity` date DEFAULT NULL,
  `end_date_validity` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `supervisor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `narrative_reports`
--

CREATE TABLE `narrative_reports` (
  `id` int(11) NOT NULL,
  `student_id` int(12) NOT NULL,
  `dateOFSubmit` varchar(50) NOT NULL,
  `objectives` varchar(2000) NOT NULL,
  `accomplishments` varchar(2000) NOT NULL,
  `reflections` varchar(2000) NOT NULL,
  `realizations` varchar(2000) NOT NULL,
  `knowledge` varchar(2000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `new_moa_processing`
--

CREATE TABLE `new_moa_processing` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `nature_of_business` varchar(255) DEFAULT NULL,
  `company_address` text NOT NULL,
  `moa_document_path` varchar(255) NOT NULL,
  `status` enum('checking_info','ulco_review','returned_to_coordinator','dean_vpaa_signature','signed_moa_retrieved','rejected') NOT NULL,
  `request_date` datetime NOT NULL,
  `checking_info_date` datetime DEFAULT NULL,
  `ulco_review_date` datetime DEFAULT NULL,
  `returned_to_coordinator_date` datetime DEFAULT NULL,
  `dean_vpaa_signature_date` datetime DEFAULT NULL,
  `signed_moa_retrieved_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ojt_hours`
--

CREATE TABLE `ojt_hours` (
  `id` int(11) NOT NULL,
  `total_hours` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ojt_hours`
--

INSERT INTO `ojt_hours` (`id`, `total_hours`) VALUES
(1, 250.00);

-- --------------------------------------------------------

--
-- Table structure for table `ojt_requirements`
--

CREATE TABLE `ojt_requirements` (
  `id` int(11) NOT NULL,
  `student_id` int(12) NOT NULL,
  `document_name` varchar(500) NOT NULL,
  `document_fileName` varchar(500) NOT NULL,
  `document_location` varchar(1000) NOT NULL,
  `status` varchar(80) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `participants`
--

CREATE TABLE `participants` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `application_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pending_users`
--

CREATE TABLE `pending_users` (
  `id` int(11) NOT NULL,
  `uniqueID` varchar(100) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `id_number` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `gender` varchar(50) NOT NULL,
  `age` int(11) NOT NULL,
  `phone_number` varchar(12) NOT NULL,
  `stud_email` varchar(100) NOT NULL,
  `role` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `online_offlineStatus` varchar(20) NOT NULL DEFAULT 'Offline',
  `sis_document` varchar(255) DEFAULT NULL,
  `access_level` int(11) NOT NULL DEFAULT 0,
  `verification_code` varchar(6) NOT NULL,
  `verify_status` varchar(20) NOT NULL DEFAULT 'Not Verified',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `photo_documentation`
--

CREATE TABLE `photo_documentation` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `document_type` enum('photo_documentation','other') NOT NULL,
  `uploaded_path` varchar(255) NOT NULL,
  `upload_date` datetime NOT NULL,
  `status` enum('pending','accepted','denied') DEFAULT 'pending',
  `feedback` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `remarks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `photo_documentation`
--

INSERT INTO `photo_documentation` (`id`, `student_id`, `document_name`, `document_type`, `uploaded_path`, `upload_date`, `status`, `feedback`, `created_at`, `updated_at`, `remarks`) VALUES
(1, '2', 'ESGUERRA-REPORT.pdf', 'photo_documentation', '/PUP/student/uploads/photo_documentation/2_ESGUERRA-REPORT.pdf', '2025-04-21 15:53:50', 'pending', NULL, '2025-04-21 07:53:50', '2025-04-21 07:53:50', ''),
(2, '13', 'CORNERSTEEL SYSTEMS CORPORATION.pdf', 'photo_documentation', '/PUP/student/uploads/photo_documentation/13_CORNERSTEEL SYSTEMS CORPORATION.pdf', '2025-05-06 10:53:14', 'denied', NULL, '2025-05-06 02:53:14', '2025-06-12 14:31:11', 'asdf');

-- --------------------------------------------------------

--
-- Table structure for table `portfolios`
--

CREATE TABLE `portfolios` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `section` varchar(50) NOT NULL,
  `date_submitted` date NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students_data`
--

CREATE TABLE `students_data` (
  `id` int(11) NOT NULL,
  `uniqueID` varchar(50) NOT NULL,
  `first_name` varchar(70) NOT NULL,
  `middle_name` varchar(70) NOT NULL,
  `last_name` varchar(70) NOT NULL,
  `student_ID` varchar(50) NOT NULL,
  `stud_dept` varchar(150) NOT NULL,
  `stud_course` varchar(150) NOT NULL,
  `stud_section` varchar(150) NOT NULL,
  `complete_address` varchar(200) NOT NULL,
  `stud_gender` varchar(100) NOT NULL,
  `phone_number` varchar(12) NOT NULL,
  `stud_email` varchar(200) NOT NULL,
  `stud_password` varchar(200) NOT NULL,
  `guardians_name` varchar(200) NOT NULL,
  `guardians_cpNumber` varchar(20) NOT NULL,
  `profile_picture` varchar(200) NOT NULL,
  `verification_code` int(8) NOT NULL,
  `verify_status` varchar(80) NOT NULL DEFAULT 'Not Verified',
  `online_offlineStatus` varchar(80) NOT NULL DEFAULT 'Offline',
  `ojt_status` varchar(80) DEFAULT NULL,
  `year_lvl` varchar(10) NOT NULL,
  `stud_hte` varchar(255) NOT NULL,
  `total_rendered_hours` int(11) NOT NULL,
  `medical_condition` varchar(255) NOT NULL,
  `is_working_student` enum('yes','no') NOT NULL DEFAULT 'no',
  `verification_status` enum('pending','accept','reject') NOT NULL DEFAULT 'pending',
  `required_hours` int(11) NOT NULL DEFAULT 0,
  `access_level` int(11) NOT NULL DEFAULT 4,
  `year_level` varchar(50) DEFAULT NULL,
  `sis_document` varchar(255) NOT NULL DEFAULT '',
  `age` int(255) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students_data`
--

INSERT INTO `students_data` (`id`, `uniqueID`, `first_name`, `middle_name`, `last_name`, `student_ID`, `stud_dept`, `stud_course`, `stud_section`, `complete_address`, `stud_gender`, `phone_number`, `stud_email`, `stud_password`, `guardians_name`, `guardians_cpNumber`, `profile_picture`, `verification_code`, `verify_status`, `online_offlineStatus`, `ojt_status`, `year_lvl`, `stud_hte`, `total_rendered_hours`, `medical_condition`, `is_working_student`, `verification_status`, `required_hours`, `access_level`, `year_level`, `sis_document`, `age`) VALUES
(1, '6625205cd60bf4782', 'Miguel', 'A', 'Torres', '2020-00123', 'College of Engineering', 'Bachelor of Science in Civil Engineering', 'BSCE 3-1', '123 R. Magsaysay Blvd, Sta. Mesa, Manila', 'Male', '09123456789', 'test9@gmail.com', '202cb962ac59075b964b07152d234b70', 'Roberto Torres', '09187654321', '../student_file_images/67e95fb976bfd-enhanced_image (1) (1).png', 187622, 'Verified', 'Online', NULL, '', '', 0, '', 'no', 'pending', 0, 4, NULL, '', 0),
(2, '6625205cd60cc1159', 'Antonio', 'B', 'Santos', '2020-00124', 'College of Engineering', 'Bachelor of Science in Civil Engineering', 'BSCE 3-1', '456 Aurora Blvd, Quezon City', 'Male', '09234567890', 'test10@gmail.com', '202cb962ac59075b964b07152d234b70', 'Enrique Santos', '09276543210', '../student_file_images/student2.jpg', 264399, 'Verified', 'Online', NULL, '', '', 0, '', 'no', 'pending', 0, 4, NULL, '', 0),
(3, '6625205cd60cd7278', 'Carlos', 'C', 'Reyes', '2020-00125', 'College of Engineering', 'Bachelor of Science in Civil Engineering', 'BSCE 3-1', '789 Shaw Blvd, Mandaluyong City', 'Male', '09345678901', 'test11@gmail.com', '202cb962ac59075b964b07152d234b70', 'Alberto Reyes', '09365432109', '../student_file_images/student3.jpg', 970213, 'Verified', 'Offline', NULL, '', '', 0, '', 'no', 'pending', 0, 4, NULL, '', 0),
(4, '6625205cd60ce3907', 'Andrea', 'D', 'Lim', '2020-00234', 'College of Computer Studies', 'Bachelor of Science in Computer Science', 'BSCS 3-2', '101 Ayala Ave, Makati City', 'Female', '09456789012', 'test12@gmail.com', '202cb962ac59075b964b07152d234b70', 'Antonio Lim', '09454321098', '../student_file_images/student4.jpg', 130605, 'Verified', 'Offline', NULL, '', '', 0, '', 'no', 'pending', 0, 4, NULL, '', 0),
(5, '6625205cd60cf4759', 'Maria', 'E', 'Gonzales', '2020-00235', 'College of Computer Studies', 'Bachelor of Science in Computer Science', 'BSCS 3-2', '202 Buendia Ave, Makati City', 'Female', '09567890123', 'test13@gmail.com', '202cb962ac59075b964b07152d234b70', 'Juan Gonzales', '09543210987', '../student_file_images/student5.jpg', 795766, 'Verified', 'Offline', NULL, '', '', 0, '', 'no', 'pending', 0, 4, NULL, '', 0),
(6, '6625205cd60d01403', 'Sofia', 'F', 'Ong', '2020-00236', 'College of Computer Studies', 'Bachelor of Science in Computer Science', 'BSCS 3-2', '303 Ortigas Ave, Pasig City', 'Female', '09678901234', 'test14@gmail.com', '202cb962ac59075b964b07152d234b70', 'Ricardo Ong', '09632109876', '../student_file_images/student6.jpg', 617952, 'Verified', 'Offline', NULL, '', '', 0, '', 'no', 'pending', 0, 4, NULL, '', 0),
(7, '6625205cd60d16348', 'Luis', 'G', 'Tan', '2020-00345', 'College of Business', 'Bachelor of Science in Accountancy', 'BSA 3-1', '404 EDSA, Mandaluyong City', 'Male', '09789012345', 'test15@gmail.com', '202cb962ac59075b964b07152d234b70', 'Alfredo Tan', '09721098765', '../student_file_images/student7.jpg', 648993, 'Verified', 'Offline', NULL, '', '', 0, '', 'no', 'pending', 0, 4, NULL, '', 0),
(8, '6625205cd60d27110', 'Jose', 'H', 'Chua', '2020-00346', 'College of Business', 'Bachelor of Science in Accountancy', 'BSA 3-1', '505 Taft Ave, Manila', 'Male', '09890123456', 'test16@gmail.com', '202cb962ac59075b964b07152d234b70', 'Fernando Chua', '09810987654', '../student_file_images/student8.jpg', 808163, 'Verified', 'Offline', NULL, '', '', 0, '', 'no', 'pending', 0, 4, NULL, '', 0),
(9, '6625205cd60d34910', 'Manuel', 'I', 'Sy', '2020-00347', 'College of Business', 'Bachelor of Science in Accountancy', 'BSA 3-1', '606 Roxas Blvd, Pasay City', 'Male', '09901234567', 'test17@gmail.com', '202cb962ac59075b964b07152d234b70', 'Eduardo Sy', '09909876543', '../student_file_images/student9.jpg', 613007, 'Verified', 'Offline', NULL, '', '', 0, '', 'no', 'pending', 0, 4, NULL, '', 0),
(10, '6625205cd60d48477', 'Elena', 'J', 'Dizon', '2020-00456', 'College of Science', 'Bachelor of Science in Biology', 'BSBIO 3-1', '707 Commonwealth Ave, Quezon City', 'Female', '09112345678', 'test18@gmail.com', '202cb962ac59075b964b07152d234b70', 'Ramon Dizon', '09198765432', '../student_file_images/student10.jpg', 577511, 'Verified', 'Offline', NULL, '', '', 0, '', 'no', 'pending', 0, 4, NULL, '', 0),
(11, '6625205cd60d55377', 'Carmen', 'K', 'Navarro', '2020-00457', 'College of Science', 'Bachelor of Science in Biology', 'BSBIO 3-1', '808 Katipunan Ave, Quezon City', 'Female', '09223456789', 'test19@gmail.com', '202cb962ac59075b964b07152d234b70', 'Roberto Navarro', '09287654321', '../student_file_images/student11.jpg', 328206, 'Verified', 'Offline', NULL, '', '', 0, '', 'no', 'pending', 0, 4, NULL, '', 0),
(12, '6625205cd60d62431', 'Isabel', 'L', 'Romero', '2020-00458', 'College of Science', 'Bachelor of Science in Biology', 'BSBIO 3-1', '909 España Blvd, Manila', 'Female', '09334567890', 'test20@gmail.com', '202cb962ac59075b964b07152d234b70', 'Felipe Romero', '09376543210', '../student_file_images/student12.jpg', 598221, 'Verified', 'Offline', NULL, '', '', 0, '', 'no', 'pending', 0, 4, NULL, '', 0),
(13, '67edf21e9043c5038', 'Andrei ', 'Paul', 'Bailon', '2022-4313-MN-0', 'College of Technology', 'Diploma in Information Technology (DIT)', 'DIT 3-1', 'MANILA', 'MALE', '05206532', 'zionandrewlopez@gmail.com', '32250170a0dca92d53ec9624f336ca24', 'Phernan', '45124512451', '../student_file_images/67edf21e9043f-270335944_3171425873141608_6264688726435691639_n.jpg', 254204, 'Verified', 'Offline', 'dropped', '3rd', 'Corner Steel Corp.', 300, 'Type 1 Diabetes', 'no', 'accept', 300, 4, NULL, '', 0),
(15, '9c229d4936342986684edd3fb606f', 'Agatha May', 'Dela Pedra', 'Esguerra', '2022-04263-MN-0', 'ITECH', 'DIT', '2', '06 Ipapo st. Pandayan', 'Female', '09150586942', 'agathamayesguerra@gmail.com', 'a2ca2ed599c28a253392dffd4deb78d3', 'Lean Esguerra', '09150586942', '', 403480, 'Verified', 'Offline', NULL, '', '', 0, '', 'no', 'pending', 0, 0, '3', 'C:\\xampp\\htdocs\\PUP/pending/student_file_documents/9c229d4936342986684edd3fb606f_RegistrationCertificateEsguerra.pdf', 20);

-- --------------------------------------------------------

--
-- Table structure for table `student_evaluations`
--

CREATE TABLE `student_evaluations` (
  `student_id` int(11) NOT NULL,
  `evaluation_file` varchar(255) DEFAULT NULL,
  `evaluation_status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_evaluations`
--

INSERT INTO `student_evaluations` (`student_id`, `evaluation_file`, `evaluation_status`) VALUES
(212121212, 'supervisor/evaluation/evaluation_212121212_1748802362.pdf', 'Complete'),
(2020476141, 'supervisor/evaluation/evaluation_2020476141_1748805163.pdf', 'Complete'),
(2020733545, 'supervisor/evaluation/evaluation_2020733545_1748803558.pdf', 'Complete'),
(2020812969, 'supervisor/evaluation/evaluation_2020812969_1748968401.pdf', 'Complete');

-- --------------------------------------------------------

--
-- Table structure for table `stud_daily_time_records`
--

CREATE TABLE `stud_daily_time_records` (
  `id` int(11) NOT NULL,
  `stud_id` int(13) NOT NULL,
  `recordDate` date NOT NULL,
  `AM_time_IN` time NOT NULL,
  `AM_time_OUT` time NOT NULL,
  `PM_time_IN` time NOT NULL,
  `PM_time_OUT` time NOT NULL,
  `total_working_hours` decimal(10,2) NOT NULL,
  `recordStatus` varchar(50) NOT NULL DEFAULT 'Pending',
  `AM_time_IN_pic` varchar(255) DEFAULT NULL,
  `AM_time_OUT_pic` varchar(255) DEFAULT NULL,
  `PM_time_IN_pic` varchar(255) DEFAULT NULL,
  `PM_time_OUT_pic` varchar(255) DEFAULT NULL,
  `remarks` varchar(255) NOT NULL,
  `status` enum('Pending','Denied','Accepted') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stud_daily_time_records`
--

INSERT INTO `stud_daily_time_records` (`id`, `stud_id`, `recordDate`, `AM_time_IN`, `AM_time_OUT`, `PM_time_IN`, `PM_time_OUT`, `total_working_hours`, `recordStatus`, `AM_time_IN_pic`, `AM_time_OUT_pic`, `PM_time_IN_pic`, `PM_time_OUT_pic`, `remarks`, `status`) VALUES
(9, 13, '2025-04-24', '10:44:00', '11:46:00', '01:47:00', '03:47:00', 2.82, 'Pending', 'uploads/dtr_13_AM_time_IN_1745462710.jpg', 'uploads/dtr_13_AM_time_OUT_1745462775.jpg', 'uploads/dtr_13_PM_time_IN_1745462874.jpg', 'uploads/dtr_13_PM_time_OUT_1745462896.jpg', '', 'Accepted');

-- --------------------------------------------------------

--
-- Table structure for table `stud_evaluation`
--

CREATE TABLE `stud_evaluation` (
  `id` int(15) NOT NULL,
  `stud_id` int(15) NOT NULL,
  `week` varchar(30) NOT NULL,
  `job_knowledge` int(15) NOT NULL,
  `dependability` int(15) NOT NULL,
  `communication_skills` int(15) NOT NULL,
  `conduct` int(15) NOT NULL,
  `initiative_and_creativity` int(15) NOT NULL,
  `cooperatives_and_relationship` int(15) NOT NULL,
  `attendance_and_punctuality` int(15) NOT NULL,
  `total_points` int(12) NOT NULL,
  `comments_suggestions` varchar(2000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stud_skills`
--

CREATE TABLE `stud_skills` (
  `id` int(11) NOT NULL,
  `stud_id` int(13) NOT NULL,
  `skills_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stud_task_list`
--

CREATE TABLE `stud_task_list` (
  `id` int(11) NOT NULL,
  `stud_id` int(13) NOT NULL,
  `task_date_of_deployed` date NOT NULL,
  `task_name` varchar(100) NOT NULL,
  `TASK_description` text NOT NULL,
  `task_date` date NOT NULL,
  `task_priority` varchar(100) NOT NULL,
  `task_status` varchar(100) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supervisor`
--

CREATE TABLE `supervisor` (
  `id` int(11) NOT NULL,
  `uniqueID` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `company_name` varchar(150) NOT NULL,
  `business_nature` varchar(150) NOT NULL,
  `position` varchar(200) NOT NULL,
  `company_address` varchar(150) NOT NULL,
  `date_notarized` varchar(150) NOT NULL,
  `moa_validity` varchar(150) NOT NULL,
  `link_to_moa` varchar(150) NOT NULL,
  `supervisor_email` varchar(150) NOT NULL,
  `supervisor_password` varchar(150) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `supervisor_profile_picture` varchar(150) NOT NULL,
  `verification_code` int(8) NOT NULL,
  `verify_status` varchar(150) NOT NULL DEFAULT 'Not Verified',
  `online_offlineStatus` varchar(150) NOT NULL DEFAULT 'Offline',
  `access_level` int(11) NOT NULL DEFAULT 3
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supervisor`
--

INSERT INTO `supervisor` (`id`, `uniqueID`, `first_name`, `middle_name`, `last_name`, `company_name`, `business_nature`, `position`, `company_address`, `date_notarized`, `moa_validity`, `link_to_moa`, `supervisor_email`, `supervisor_password`, `phone_number`, `supervisor_profile_picture`, `verification_code`, `verify_status`, `online_offlineStatus`, `access_level`) VALUES
(1, '66252d8f8c95a7624', 'Robert', 'A', 'Johnson', 'Accenture Philippines', 'IT Services', 'Senior Manager', '16/F Robinsons Cybergate Tower, EDSA, Mandaluyong City', '2025-01-15', '2026-01-15', '#', 'test13@gmail.com', '202cb962ac59075b964b07152d234b70', '09123456789', '../student_file_images/supervisor1.jpg', 788441, 'Verified', 'Online', 3),
(2, '66252d8f8c9649173', 'Jennifer', 'B', 'Smith', 'IBM Philippines', 'Technology Solutions', 'HR Manager', '12/F IBM Plaza, Eastwood City, Quezon City', '2025-02-20', '2026-02-20', '#', 'test14@gmail.com', '202cb962ac59075b964b07152d234b70', '09234567890', '../student_file_images/supervisor2.jpg', 957348, 'Verified', 'Offline', 3),
(3, '66252d8f8c9652162', 'Michael', 'C', 'Williams', 'Deloitte Philippines', 'Professional Services', 'Consulting Manager', '21/F Deloitte Center, Bonifacio Global City', '2025-03-10', '2026-03-10', '#', 'test15@gmail.com', '202cb962ac59075b964b07152d234b70', '09345678901', '../student_file_images/supervisor3.jpg', 402868, 'Verified', 'Offline', 3),
(4, '66252d8f8c9669130', 'Sarah', 'D', 'Brown', 'PwC Philippines', 'Audit and Assurance', 'Senior Associate', '30/F PwC Tower, Makati City', '2025-01-30', '2026-01-30', '#', 'test16@gmail.com', '202cb962ac59075b964b07152d234b70', '09456789012', '../student_file_images/supervisor4.jpg', 566765, 'Verified', 'Offline', 3);

-- --------------------------------------------------------

--
-- Table structure for table `supervisor_skills`
--

CREATE TABLE `supervisor_skills` (
  `id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `skill_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supervisor_system_notification`
--

CREATE TABLE `supervisor_system_notification` (
  `id` int(11) NOT NULL,
  `supervisor_id` int(13) NOT NULL,
  `logs` varchar(200) NOT NULL,
  `logs_date` varchar(50) NOT NULL,
  `logs_time` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supervisor_system_notification`
--

INSERT INTO `supervisor_system_notification` (`id`, `supervisor_id`, `logs`, `logs_date`, `logs_time`, `status`) VALUES
(1, 1, 'You successfully logged in to your account.', 'April / 18 Friday / 2025', '11:32 PM', 'Unread'),
(2, 5, 'You successfully logged in to your account.', 'June / 15 / 2025', '10:47 PM', 'Unread');

-- --------------------------------------------------------

--
-- Table structure for table `system_notification`
--

CREATE TABLE `system_notification` (
  `id` int(11) NOT NULL,
  `student_id` int(12) NOT NULL,
  `logs` varchar(200) NOT NULL,
  `logs_date` varchar(50) NOT NULL,
  `logs_time` varchar(50) NOT NULL,
  `status` varchar(100) NOT NULL DEFAULT 'Unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_notification`
--

INSERT INTO `system_notification` (`id`, `student_id`, `logs`, `logs_date`, `logs_time`, `status`) VALUES
(1, 2, 'You successfully logged out to your account.', 'March / 30 Sunday / 2025', '3:33 PM', 'Unread'),
(2, 1, 'You successfully logged in to your account.', 'March / 30 Sunday / 2025', '3:34 PM', 'Unread'),
(3, 1, 'You successfully logged out to your account.', 'March / 30 Sunday / 2025', '3:43 PM', 'Unread'),
(4, 2, 'You successfully logged in to your account.', 'March / 30 Sunday / 2025', '3:43 PM', 'Unread'),
(5, 2, 'You successfully logged in to your account.', 'March / 30 Sunday / 2025', '3:44 PM', 'Unread'),
(6, 2, 'You successfully logged out to your account.', 'March / 30 Sunday / 2025', '3:51 PM', 'Unread'),
(7, 1, 'You successfully logged in to your account.', 'March / 30 Sunday / 2025', '10:18 PM', 'Unread'),
(8, 1, 'You successfully logged out to your account.', 'March / 30 Sunday / 2025', '10:24 PM', 'Unread'),
(9, 1, 'You successfully logged in to your account.', 'March / 30 Sunday / 2025', '10:26 PM', 'Unread'),
(10, 2, 'You successfully logged in to your account.', 'March / 30 Sunday / 2025', '10:32 PM', 'Unread'),
(11, 2, 'You successfully logged in to your account.', 'March / 30 Sunday / 2025', '10:55 PM', 'Unread'),
(12, 2, 'You successfully logged out to your account.', 'March / 30 Sunday / 2025', '11:02 PM', 'Unread'),
(13, 1, 'You successfully logged in to your account.', 'March / 30 Sunday / 2025', '11:06 PM', 'Unread'),
(14, 1, 'Profile picture updated successfully.', 'March / 30 Sunday / 2025', '11:14 PM', 'Unread'),
(15, 14, 'You successfully logged in to your account.', 'April / 03 Thursday / 2025', '10:30 AM', 'Unread'),
(16, 14, 'You successfully logged out to your account.', 'April / 03 Thursday / 2025', '10:39 AM', 'Unread'),
(17, 14, 'You successfully logged in to your account.', 'April / 03 Thursday / 2025', '10:39 AM', 'Unread'),
(18, 14, 'You successfully logged out to your account.', 'April / 03 Thursday / 2025', '10:48 AM', 'Unread'),
(19, 14, 'You successfully logged in to your account.', 'April / 03 Thursday / 2025', '10:02 PM', 'Unread'),
(20, 14, 'You successfully logged out to your account.', 'April / 03 Thursday / 2025', '10:08 PM', 'Unread'),
(21, 14, 'You successfully logged in to your account.', 'April / 07 Monday / 2025', '9:21 AM', 'Unread'),
(22, 14, 'You successfully logged out to your account.', 'April / 07 Monday / 2025', '9:32 AM', 'Unread'),
(23, 13, 'You successfully logged in to your account.', 'April / 07 Monday / 2025', '11:47 PM', 'Unread'),
(24, 13, 'You successfully logged in to your account.', 'April / 09 Wednesday / 2025', '9:03 AM', 'Unread'),
(25, 13, 'You successfully logged out to your account.', 'April / 09 Wednesday / 2025', '9:21 AM', 'Unread'),
(26, 13, 'You successfully logged in to your account.', 'April / 09 Wednesday / 2025', '11:05 AM', 'Unread'),
(27, 13, 'You successfully logged out to your account.', 'April / 09 Wednesday / 2025', '11:13 AM', 'Unread'),
(28, 13, 'You successfully logged in to your account.', 'April / 09 Wednesday / 2025', '11:55 AM', 'Unread'),
(29, 13, 'You successfully logged out to your account.', 'April / 09 Wednesday / 2025', '12:03 PM', 'Unread'),
(30, 13, 'You successfully logged in to your account.', 'April / 09 Wednesday / 2025', '2:41 PM', 'Unread'),
(31, 13, 'You successfully logged out to your account.', 'April / 09 Wednesday / 2025', '2:56 PM', 'Unread'),
(32, 13, 'You successfully logged in to your account.', 'April / 09 Wednesday / 2025', '3:02 PM', 'Unread'),
(33, 13, 'You successfully logged out to your account.', 'April / 09 Wednesday / 2025', '3:08 PM', 'Unread'),
(34, 13, 'You successfully logged in to your account.', 'April / 10 Thursday / 2025', '9:54 AM', 'Unread'),
(35, 13, 'You successfully logged out to your account.', 'April / 10 Thursday / 2025', '10:05 AM', 'Unread'),
(36, 13, 'You successfully logged in to your account.', 'April / 10 Thursday / 2025', '10:11 AM', 'Unread'),
(37, 13, 'You successfully logged out to your account.', 'April / 10 Thursday / 2025', '10:18 AM', 'Unread'),
(38, 13, 'You successfully logged in to your account.', 'April / 10 Thursday / 2025', '10:21 AM', 'Unread'),
(39, 13, 'You successfully logged in to your account.', 'April / 10 Thursday / 2025', '10:26 AM', 'Unread'),
(40, 13, 'You successfully logged in to your account.', 'April / 10 Thursday / 2025', '10:32 AM', 'Unread'),
(41, 13, 'You successfully logged in to your account.', 'April / 10 Thursday / 2025', '10:37 AM', 'Unread'),
(42, 13, 'You successfully logged in to your account.', 'April / 10 Thursday / 2025', '10:46 AM', 'Unread'),
(43, 13, 'You successfully logged in to your account.', 'April / 11 Friday / 2025', '9:53 AM', 'Unread'),
(44, 13, 'You successfully logged out to your account.', 'April / 11 Friday / 2025', '9:59 AM', 'Unread'),
(45, 13, 'You successfully logged in to your account.', 'April / 11 Friday / 2025', '3:16 PM', 'Unread'),
(46, 13, 'You successfully logged out to your account.', 'April / 11 Friday / 2025', '3:24 PM', 'Unread'),
(47, 13, 'You successfully logged in to your account.', 'April / 12 Saturday / 2025', '12:27 AM', 'Unread'),
(48, 13, 'You successfully logged out to your account.', 'April / 12 Saturday / 2025', '12:35 AM', 'Unread'),
(49, 13, 'You successfully logged in to your account.', 'April / 12 Saturday / 2025', '12:41 AM', 'Unread'),
(50, 13, 'You successfully logged out to your account.', 'April / 12 Saturday / 2025', '12:47 AM', 'Unread'),
(51, 13, 'You successfully logged in to your account.', 'April / 12 Saturday / 2025', '3:09 PM', 'Unread'),
(52, 13, 'You successfully logged out to your account.', 'April / 12 Saturday / 2025', '3:16 PM', 'Unread'),
(53, 13, 'You successfully logged in to your account.', 'April / 12 Saturday / 2025', '3:44 PM', 'Unread'),
(54, 13, 'You successfully logged out to your account.', 'April / 12 Saturday / 2025', '3:47 PM', 'Unread'),
(55, 13, 'You successfully logged in to your account.', 'April / 12 Saturday / 2025', '4:12 PM', 'Unread'),
(56, 13, 'You successfully logged out to your account.', 'April / 12 Saturday / 2025', '4:19 PM', 'Unread'),
(57, 2, 'You successfully logged out to your account.', 'April / 16 Wednesday / 2025', '1:35 PM', 'Unread'),
(58, 2, 'You successfully logged in to your account.', 'April / 16 Wednesday / 2025', '1:59 PM', 'Unread'),
(59, 2, 'You successfully logged out to your account.', 'April / 16 Wednesday / 2025', '2:07 PM', 'Unread'),
(60, 2, 'You successfully logged in to your account.', 'April / 16 Wednesday / 2025', '2:11 PM', 'Unread'),
(61, 2, 'You successfully logged in to your account.', 'April / 16 Wednesday / 2025', '2:18 PM', 'Unread'),
(62, 2, 'You successfully logged in to your account.', 'April / 16 Wednesday / 2025', '2:32 PM', 'Unread'),
(63, 2, 'You successfully logged in to your account.', 'April / 16 Wednesday / 2025', '2:36 PM', 'Unread'),
(64, 2, 'You successfully logged out to your account.', 'April / 16 Wednesday / 2025', '2:44 PM', 'Unread'),
(65, 2, 'You successfully logged in to your account.', 'April / 16 Wednesday / 2025', '2:44 PM', 'Unread'),
(66, 2, 'You successfully logged out to your account.', 'April / 16 Wednesday / 2025', '3:00 PM', 'Unread'),
(67, 2, 'You successfully logged in to your account.', 'April / 16 Wednesday / 2025', '3:04 PM', 'Unread'),
(68, 2, 'You successfully logged out to your account.', 'April / 16 Wednesday / 2025', '3:22 PM', 'Unread'),
(69, 2, 'You successfully logged in to your account.', 'April / 16 Wednesday / 2025', '3:24 PM', 'Unread'),
(70, 2, 'You successfully logged in to your account.', 'April / 16 Wednesday / 2025', '3:25 PM', 'Unread'),
(71, 2, 'You successfully logged in to your account.', 'April / 16 Wednesday / 2025', '3:46 PM', 'Unread'),
(72, 2, 'You successfully logged in to your account.', 'April / 16 Wednesday / 2025', '5:47 PM', 'Unread'),
(73, 2, 'You successfully logged out to your account.', 'April / 16 Wednesday / 2025', '5:53 PM', 'Unread'),
(74, 2, 'You successfully logged in to your account.', 'April / 17 Thursday / 2025', '12:31 AM', 'Unread'),
(75, 2, 'You successfully logged in to your account.', 'April / 17 Thursday / 2025', '9:32 PM', 'Unread'),
(76, 2, 'You successfully logged in to your account.', 'April / 17 Thursday / 2025', '9:45 PM', 'Unread'),
(77, 2, 'You successfully logged out to your account.', 'April / 17 Thursday / 2025', '10:00 PM', 'Unread'),
(78, 2, 'You successfully logged in to your account.', 'April / 17 Thursday / 2025', '10:06 PM', 'Unread'),
(79, 2, 'You successfully logged out to your account.', 'April / 17 Thursday / 2025', '10:15 PM', 'Unread'),
(80, 2, 'You successfully logged in to your account.', 'April / 17 Thursday / 2025', '10:18 PM', 'Unread'),
(81, 2, 'You successfully logged in to your account.', 'April / 17 Thursday / 2025', '10:21 PM', 'Unread'),
(82, 2, 'You successfully logged in to your account.', 'April / 17 Thursday / 2025', '10:24 PM', 'Unread'),
(83, 2, 'You successfully logged out to your account.', 'April / 17 Thursday / 2025', '10:39 PM', 'Unread'),
(84, 2, 'You successfully logged in to your account.', 'April / 17 Thursday / 2025', '10:39 PM', 'Unread'),
(85, 2, 'You successfully logged in to your account.', 'April / 17 Thursday / 2025', '11:25 PM', 'Unread'),
(86, 1, 'You successfully logged in to your account.', 'April / 18 Friday / 2025', '9:31 PM', 'Unread'),
(87, 1, 'You successfully logged in to your account.', 'April / 18 Friday / 2025', '11:32 PM', 'Unread'),
(88, 1, 'You successfully logged out to your account.', 'April / 18 Friday / 2025', '11:32 PM', 'Unread'),
(89, 2, 'You successfully logged in to your account.', 'April / 18 Friday / 2025', '11:58 PM', 'Unread'),
(90, 2, 'You successfully logged out to your account.', 'April / 18 Friday / 2025', '11:58 PM', 'Unread'),
(91, 1, 'You successfully logged in to your account.', 'April / 18 Friday / 2025', '11:58 PM', 'Unread'),
(92, 10, 'You successfully logged out to your account.', 'April / 19 Saturday / 2025', '9:21 PM', 'Unread'),
(93, 1, 'You successfully logged in to your account.', 'April / 19 Saturday / 2025', '9:25 PM', 'Unread'),
(94, 1, 'You successfully logged out to your account.', 'April / 19 Saturday / 2025', '9:36 PM', 'Unread'),
(95, 1, 'You successfully logged in to your account.', 'April / 19 Saturday / 2025', '9:51 PM', 'Unread'),
(96, 1, 'You successfully logged out to your account.', 'April / 19 Saturday / 2025', '9:59 PM', 'Unread'),
(97, 1, 'You successfully logged in to your account.', 'April / 19 Saturday / 2025', '10:05 PM', 'Unread'),
(98, 1, 'You successfully logged out to your account.', 'April / 19 Saturday / 2025', '10:12 PM', 'Unread'),
(99, 1, 'You successfully logged in to your account.', 'April / 19 Saturday / 2025', '10:12 PM', 'Unread'),
(100, 1, 'You successfully logged out to your account.', 'April / 19 Saturday / 2025', '10:18 PM', 'Unread'),
(101, 2, 'You successfully logged in to your account.', 'April / 19 Saturday / 2025', '10:21 PM', 'Unread'),
(102, 2, 'You successfully logged out to your account.', 'April / 19 Saturday / 2025', '10:36 PM', 'Unread'),
(103, 1, 'You successfully logged in to your account.', 'April / 19 Saturday / 2025', '10:38 PM', 'Unread'),
(104, 1, 'You successfully logged out to your account.', 'April / 19 Saturday / 2025', '10:47 PM', 'Unread'),
(105, 1, 'You successfully logged in to your account.', 'April / 19 Saturday / 2025', '10:50 PM', 'Unread'),
(106, 1, 'You successfully logged out to your account.', 'April / 19 Saturday / 2025', '11:10 PM', 'Unread'),
(107, 2, 'You successfully logged in to your account.', 'April / 19 Saturday / 2025', '11:25 PM', 'Unread'),
(108, 2, 'You successfully logged out to your account.', 'April / 19 Saturday / 2025', '11:33 PM', 'Unread'),
(109, 1, 'You successfully logged in to your account.', 'April / 19 Saturday / 2025', '11:36 PM', 'Unread'),
(110, 1, 'You successfully logged in to your account.', 'April / 19 Saturday / 2025', '11:41 PM', 'Unread'),
(111, 1, 'You successfully logged in to your account.', 'April / 19 Saturday / 2025', '11:42 PM', 'Unread'),
(112, 1, 'You successfully logged in to your account.', 'April / 19 Saturday / 2025', '11:42 PM', 'Unread'),
(113, 1, 'You successfully logged in to your account.', 'April / 19 Saturday / 2025', '11:42 PM', 'Unread'),
(114, 1, 'You successfully logged in to your account.', 'April / 19 Saturday / 2025', '11:49 PM', 'Unread'),
(115, 1, 'You successfully logged in to your account.', 'April / 19 Saturday / 2025', '11:50 PM', 'Unread'),
(116, 1, 'You successfully logged in to your account.', 'April / 19 Saturday / 2025', '11:55 PM', 'Unread'),
(117, 1, 'You successfully logged in to your account.', 'April / 19 Saturday / 2025', '11:55 PM', 'Unread'),
(118, 1, 'You successfully logged in to your account.', 'April / 20 Sunday / 2025', '12:01 AM', 'Unread'),
(119, 1, 'You successfully logged in to your account.', 'April / 20 Sunday / 2025', '12:02 AM', 'Unread'),
(120, 1, 'You successfully logged in to your account.', 'April / 20 Sunday / 2025', '12:15 AM', 'Unread'),
(121, 1, 'You successfully logged in to your account.', 'April / 20 Sunday / 2025', '12:23 AM', 'Unread'),
(122, 1, 'You successfully logged out to your account.', 'April / 20 Sunday / 2025', '12:30 AM', 'Unread'),
(123, 1, 'You successfully logged in to your account.', 'April / 20 Sunday / 2025', '12:35 AM', 'Unread'),
(124, 1, 'You successfully logged out to your account.', 'April / 20 Sunday / 2025', '12:58 AM', 'Unread'),
(125, 1, 'You successfully logged in to your account.', 'April / 20 Sunday / 2025', '12:58 AM', 'Unread'),
(126, 2, 'You successfully logged in to your account.', 'April / 20 Sunday / 2025', '1:04 AM', 'Unread'),
(127, 1, 'You successfully logged in to your account.', 'April / 20 Sunday / 2025', '1:12 AM', 'Unread'),
(128, 2, 'You successfully logged in to your account.', 'April / 20 Sunday / 2025', '1:28 PM', 'Unread'),
(129, 2, 'You successfully logged out to your account.', 'April / 20 Sunday / 2025', '1:39 PM', 'Unread'),
(130, 2, 'You successfully logged in to your account.', 'April / 21 Monday / 2025', '3:40 PM', 'Unread'),
(131, 2, 'You successfully logged in to your account.', 'April / 21 Monday / 2025', '3:40 PM', 'Unread'),
(132, 2, 'You successfully logged out to your account.', 'April / 21 Monday / 2025', '3:52 PM', 'Unread'),
(133, 2, 'You successfully logged in to your account.', 'April / 21 Monday / 2025', '3:53 PM', 'Unread'),
(134, 2, 'You successfully logged out to your account.', 'April / 21 Monday / 2025', '4:00 PM', 'Unread'),
(135, 2, 'You successfully logged in to your account.', 'April / 21 Monday / 2025', '4:01 PM', 'Unread'),
(136, 13, 'You successfully logged in to your account.', 'April / 22 Tuesday / 2025', '10:00 AM', 'Unread'),
(137, 13, 'You successfully logged out to your account.', 'April / 22 Tuesday / 2025', '10:12 AM', 'Unread'),
(138, 13, 'You successfully logged in to your account.', 'April / 22 Tuesday / 2025', '1:37 PM', 'Unread'),
(139, 13, 'You successfully logged in to your account.', 'April / 22 Tuesday / 2025', '1:48 PM', 'Unread'),
(140, 13, 'You successfully logged in to your account.', 'April / 22 Tuesday / 2025', '1:53 PM', 'Unread'),
(141, 13, 'You successfully logged out to your account.', 'April / 22 Tuesday / 2025', '2:00 PM', 'Unread'),
(142, 13, 'You successfully logged in to your account.', 'April / 22 Tuesday / 2025', '2:04 PM', 'Unread'),
(143, 13, 'You successfully logged in to your account.', 'April / 22 Tuesday / 2025', '2:05 PM', 'Unread'),
(144, 13, 'You successfully logged out to your account.', 'April / 22 Tuesday / 2025', '2:12 PM', 'Unread'),
(145, 13, 'You successfully logged in to your account.', 'April / 22 Tuesday / 2025', '2:13 PM', 'Unread'),
(146, 13, 'You successfully logged in to your account.', 'April / 23 Wednesday / 2025', '9:55 AM', 'Unread'),
(147, 13, 'You successfully logged in to your account.', 'April / 23 Wednesday / 2025', '10:00 AM', 'Unread'),
(148, 13, 'You successfully logged in to your account.', 'April / 23 Wednesday / 2025', '10:04 AM', 'Unread'),
(149, 13, 'You successfully logged out to your account.', 'April / 23 Wednesday / 2025', '10:12 AM', 'Unread'),
(150, 13, 'You successfully logged in to your account.', 'April / 24 Thursday / 2025', '9:34 AM', 'Unread'),
(151, 13, 'You successfully logged out to your account.', 'April / 24 Thursday / 2025', '9:41 AM', 'Unread'),
(152, 13, 'You successfully logged in to your account.', 'April / 24 Thursday / 2025', '10:44 AM', 'Unread'),
(153, 13, 'You successfully logged in to your account.', 'April / 24 Thursday / 2025', '10:46 AM', 'Unread'),
(154, 13, 'You successfully logged in to your account.', 'April / 24 Thursday / 2025', '10:47 AM', 'Unread'),
(155, 13, 'You successfully logged in to your account.', 'April / 24 Thursday / 2025', '10:53 AM', 'Unread'),
(156, 13, 'You successfully logged out to your account.', 'April / 24 Thursday / 2025', '11:01 AM', 'Unread'),
(157, 13, 'You successfully logged in to your account.', 'April / 24 Thursday / 2025', '1:45 PM', 'Unread'),
(158, 13, 'You successfully logged out to your account.', 'April / 24 Thursday / 2025', '1:52 PM', 'Unread'),
(159, 13, 'You successfully logged in to your account.', 'April / 29 Tuesday / 2025', '9:11 AM', 'Unread'),
(160, 13, 'You successfully logged out to your account.', 'April / 29 Tuesday / 2025', '9:20 AM', 'Unread'),
(161, 13, 'You successfully logged in to your account.', 'April / 29 Tuesday / 2025', '9:29 AM', 'Unread'),
(162, 13, 'You successfully logged out to your account.', 'April / 29 Tuesday / 2025', '9:39 AM', 'Unread'),
(163, 13, 'You successfully logged in to your account.', 'May / 05 Monday / 2025', '2:58 PM', 'Unread'),
(164, 13, 'You successfully logged out to your account.', 'May / 05 Monday / 2025', '2:59 PM', 'Unread'),
(165, 13, 'You successfully logged in to your account.', 'May / 05 Monday / 2025', '3:12 PM', 'Unread'),
(166, 13, 'You successfully logged out to your account.', 'May / 05 Monday / 2025', '3:21 PM', 'Unread'),
(167, 13, 'You successfully logged in to your account.', 'May / 05 Monday / 2025', '3:28 PM', 'Unread'),
(168, 13, 'You successfully logged out to your account.', 'May / 05 Monday / 2025', '3:39 PM', 'Unread'),
(169, 13, 'You successfully logged in to your account.', 'May / 05 Monday / 2025', '3:39 PM', 'Unread'),
(170, 13, 'You successfully logged out to your account.', 'May / 05 Monday / 2025', '3:47 PM', 'Unread'),
(171, 13, 'You successfully logged in to your account.', 'May / 06 Tuesday / 2025', '10:52 AM', 'Unread'),
(172, 13, 'You successfully logged out to your account.', 'May / 06 Tuesday / 2025', '10:59 AM', 'Unread'),
(173, 13, 'You successfully logged in to your account.', 'May / 06 Tuesday / 2025', '11:10 AM', 'Unread'),
(174, 13, 'You successfully logged out to your account.', 'May / 06 Tuesday / 2025', '11:21 AM', 'Unread'),
(175, 13, 'You successfully logged in to your account.', 'May / 09 Friday / 2025', '2:30 PM', 'Unread'),
(176, 13, 'You successfully logged out to your account.', 'May / 09 Friday / 2025', '2:31 PM', 'Unread'),
(177, 13, 'You successfully logged in to your account.', 'May / 09 Friday / 2025', '2:32 PM', 'Unread'),
(178, 13, 'You successfully logged out to your account.', 'May / 09 Friday / 2025', '2:39 PM', 'Unread'),
(179, 13, 'You successfully logged in to your account.', 'May / 09 Friday / 2025', '6:14 PM', 'Unread'),
(180, 13, 'You successfully logged out to your account.', 'May / 09 Friday / 2025', '6:20 PM', 'Unread'),
(181, 13, 'You successfully logged in to your account.', 'May / 12 Monday / 2025', '2:52 PM', 'Unread'),
(182, 13, 'You successfully logged out to your account.', 'May / 12 Monday / 2025', '2:53 PM', 'Unread'),
(183, 13, 'You successfully logged in to your account.', 'May / 12 Monday / 2025', '2:54 PM', 'Unread'),
(184, 13, 'You successfully logged in to your account.', 'May / 12 Monday / 2025', '2:59 PM', 'Unread'),
(185, 13, 'You successfully logged out to your account.', 'May / 12 Monday / 2025', '2:59 PM', 'Unread'),
(186, 13, 'You successfully logged in to your account.', 'May / 12 Monday / 2025', '3:01 PM', 'Unread'),
(187, 13, 'You successfully logged out to your account.', 'May / 12 Monday / 2025', '3:11 PM', 'Unread'),
(188, 13, 'You successfully logged in to your account.', 'May / 17 Saturday / 2025', '11:54 AM', 'Unread'),
(189, 13, 'You successfully logged out to your account.', 'May / 17 Saturday / 2025', '11:54 AM', 'Unread'),
(190, 13, 'You successfully logged in to your account.', 'May / 17 Saturday / 2025', '2:12 PM', 'Unread'),
(191, 13, 'You successfully logged out to your account.', 'May / 17 Saturday / 2025', '2:17 PM', 'Unread'),
(192, 13, 'You successfully logged in to your account.', 'May / 17 Saturday / 2025', '2:18 PM', 'Unread'),
(193, 13, 'You successfully logged out to your account.', 'May / 17 Saturday / 2025', '2:20 PM', 'Unread'),
(194, 13, 'You successfully logged in to your account.', 'May / 17 Saturday / 2025', '2:21 PM', 'Unread'),
(195, 13, 'You successfully logged in to your account.', 'May / 24 Saturday / 2025', '1:01 AM', 'Unread'),
(196, 13, 'You successfully logged in to your account.', 'May / 24 Saturday / 2025', '9:59 PM', 'Unread'),
(197, 13, 'You successfully logged in to your account.', 'May / 29 Thursday / 2025', '9:52 PM', 'Unread'),
(198, 13, 'You successfully logged out to your account.', 'May / 29 Thursday / 2025', '9:59 PM', 'Unread'),
(199, 13, 'You successfully logged in to your account.', 'May / 30 Friday / 2025', '12:00 AM', 'Unread'),
(200, 13, 'You successfully logged out to your account.', 'May / 30 Friday / 2025', '12:17 AM', 'Unread'),
(201, 13, 'You successfully logged in to your account.', 'May / 30 Friday / 2025', '12:52 AM', 'Unread'),
(202, 13, 'You successfully logged out to your account.', 'May / 30 Friday / 2025', '12:58 AM', 'Unread'),
(203, 13, 'You successfully logged in to your account.', 'May / 30 Friday / 2025', '1:04 AM', 'Unread'),
(204, 13, 'You successfully logged out to your account.', 'May / 30 Friday / 2025', '1:11 AM', 'Unread'),
(205, 13, 'You successfully logged in to your account.', 'May / 30 Friday / 2025', '2:42 AM', 'Unread'),
(206, 13, 'You successfully logged out to your account.', 'May / 30 Friday / 2025', '2:51 AM', 'Unread'),
(207, 13, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '3:43 PM', 'Unread'),
(208, 13, 'You successfully logged out to your account.', 'June / 01 Sunday / 2025', '3:52 PM', 'Unread'),
(209, 13, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '3:52 PM', 'Unread'),
(210, 13, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '3:59 PM', 'Unread'),
(211, 13, 'You successfully logged out to your account.', 'June / 01 Sunday / 2025', '4:07 PM', 'Unread'),
(212, 13, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '6:48 PM', 'Unread'),
(213, 13, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '6:49 PM', 'Unread'),
(214, 13, 'You successfully logged in to your account.', 'June / 01 Sunday / 2025', '7:12 PM', 'Unread'),
(215, 13, 'You successfully logged out to your account.', 'June / 01 Sunday / 2025', '7:18 PM', 'Unread'),
(216, 13, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '1:01 PM', 'Unread'),
(217, 13, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '1:08 PM', 'Unread'),
(218, 13, 'You successfully logged out to your account.', 'June / 02 Monday / 2025', '1:22 PM', 'Unread'),
(219, 13, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '1:28 PM', 'Unread'),
(220, 13, 'You successfully logged out to your account.', 'June / 02 Monday / 2025', '1:38 PM', 'Unread'),
(221, 13, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '1:43 PM', 'Unread'),
(222, 13, 'You successfully logged out to your account.', 'June / 02 Monday / 2025', '1:50 PM', 'Unread'),
(223, 13, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '2:38 PM', 'Unread'),
(224, 13, 'You successfully logged out to your account.', 'June / 02 Monday / 2025', '2:45 PM', 'Unread'),
(225, 13, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '2:54 PM', 'Unread'),
(226, 13, 'You successfully logged out to your account.', 'June / 02 Monday / 2025', '3:00 PM', 'Unread'),
(227, 13, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '3:16 PM', 'Unread'),
(228, 13, 'You successfully logged out to your account.', 'June / 02 Monday / 2025', '3:22 PM', 'Unread'),
(229, 13, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '4:42 PM', 'Unread'),
(230, 13, 'You successfully logged out to your account.', 'June / 02 Monday / 2025', '4:49 PM', 'Unread'),
(231, 13, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '5:19 PM', 'Unread'),
(232, 13, 'You successfully logged out to your account.', 'June / 02 Monday / 2025', '5:25 PM', 'Unread'),
(233, 13, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '5:27 PM', 'Unread'),
(234, 13, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '8:31 PM', 'Unread'),
(235, 13, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '8:34 PM', 'Unread'),
(236, 13, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '8:51 PM', 'Unread'),
(237, 13, 'You successfully logged out to your account.', 'June / 02 Monday / 2025', '8:59 PM', 'Unread'),
(238, 13, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '9:37 PM', 'Unread'),
(239, 13, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '9:41 PM', 'Unread'),
(240, 13, 'You successfully logged in to your account.', 'June / 02 Monday / 2025', '10:14 PM', 'Unread'),
(241, 13, 'You successfully logged out to your account.', 'June / 02 Monday / 2025', '10:22 PM', 'Unread'),
(242, 13, 'You successfully logged in to your account.', 'June / 03 Tuesday / 2025', '2:07 PM', 'Unread'),
(243, 13, 'You successfully logged out to your account.', 'June / 03 Tuesday / 2025', '2:09 PM', 'Unread'),
(244, 13, 'You successfully logged in to your account.', 'June / 09 Monday / 2025', '5:24 PM', 'Unread'),
(245, 13, 'You successfully logged out to your account.', 'June / 09 Monday / 2025', '5:32 PM', 'Unread'),
(246, 13, 'You successfully logged in to your account.', 'June / 09 Monday / 2025', '6:04 PM', 'Unread'),
(247, 13, 'You successfully logged out to your account.', 'June / 09 Monday / 2025', '6:27 PM', 'Unread'),
(248, 13, 'You successfully logged in to your account.', 'June / 10 Tuesday / 2025', '5:02 PM', 'Unread'),
(249, 13, 'You successfully logged in to your account.', 'June / 11 Wednesday / 2025', '1:38 PM', 'Unread'),
(250, 13, 'You successfully logged in to your account.', 'June / 11 Wednesday / 2025', '1:45 PM', 'Unread'),
(251, 13, 'You successfully logged out to your account.', 'June / 11 Wednesday / 2025', '1:54 PM', 'Unread'),
(252, 13, 'You successfully logged in to your account.', 'June / 11 Wednesday / 2025', '6:23 PM', 'Unread'),
(253, 13, 'You successfully logged out to your account.', 'June / 11 Wednesday / 2025', '6:29 PM', 'Unread'),
(254, 13, 'You successfully logged in to your account.', 'June / 14 / 2025', '12:02 PM', 'Unread'),
(255, 13, 'Switched to working student status.', 'June / 14 Saturday / 2025', '12:02 PM', 'Unread'),
(256, 13, 'You successfully logged out to your account.', 'June / 14 Saturday / 2025', '12:09 PM', 'Unread'),
(257, 13, 'You successfully logged in to your account.', 'June / 14 / 2025', '12:14 PM', 'Unread'),
(258, 13, 'Switched to working student status.', 'June / 14 Saturday / 2025', '12:15 PM', 'Unread'),
(259, 13, 'Switched to working student status.', 'June / 14 Saturday / 2025', '12:18 PM', 'Unread'),
(260, 13, 'Switched to working student status.', 'June / 14 Saturday / 2025', '12:18 PM', 'Unread'),
(261, 13, 'You successfully logged in to your account.', 'June / 14 / 2025', '12:19 PM', 'Unread'),
(262, 13, 'You successfully logged in to your account.', 'June / 14 / 2025', '12:20 PM', 'Unread'),
(263, 13, 'Switched to working student status.', 'June / 14 Saturday / 2025', '12:21 PM', 'Unread'),
(264, 13, 'You successfully logged in to your account.', 'June / 14 / 2025', '12:22 PM', 'Unread'),
(265, 13, 'You successfully logged out to your account.', 'June / 14 Saturday / 2025', '12:29 PM', 'Unread'),
(266, 13, 'You successfully logged in to your account.', 'June / 14 / 2025', '12:30 PM', 'Unread'),
(267, 13, 'Switched to working student status.', 'June / 14 Saturday / 2025', '12:31 PM', 'Unread'),
(268, 13, 'Switched to working student status.', 'June / 14 Saturday / 2025', '12:31 PM', 'Unread'),
(269, 13, 'You successfully logged in to your account.', 'June / 14 / 2025', '12:31 PM', 'Unread'),
(270, 13, 'You successfully logged in to your account.', 'June / 14 / 2025', '12:35 PM', 'Unread'),
(271, 13, 'Switched to working student status.', 'June / 14 Saturday / 2025', '12:35 PM', 'Unread'),
(272, 13, 'You successfully logged in to your account.', 'June / 14 / 2025', '12:37 PM', 'Unread'),
(273, 13, 'You successfully logged in to your account.', 'June / 14 / 2025', '12:38 PM', 'Unread'),
(274, 13, 'You successfully logged in to your account.', 'June / 14 / 2025', '8:27 PM', 'Unread'),
(275, 13, 'Switched to working student status.', 'June / 14 Saturday / 2025', '8:28 PM', 'Unread'),
(276, 13, 'Switched to working student status.', 'June / 14 Saturday / 2025', '8:29 PM', 'Unread'),
(277, 13, 'Switched to working student status.', 'June / 14 Saturday / 2025', '8:30 PM', 'Unread'),
(278, 13, 'You successfully logged out to your account.', 'June / 14 Saturday / 2025', '8:30 PM', 'Unread'),
(279, 13, 'You successfully logged in to your account.', 'June / 14 / 2025', '8:46 PM', 'Unread'),
(280, 13, 'You successfully logged out to your account.', 'June / 14 Saturday / 2025', '8:47 PM', 'Unread'),
(281, 15, 'You successfully logged in to your account.', 'June / 15 / 2025', '11:04 PM', 'Unread'),
(282, 15, 'You successfully logged out to your account.', 'June / 15 Sunday / 2025', '11:13 PM', 'Unread');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('student','supervisor','admin') NOT NULL,
  `avatar` varchar(255) DEFAULT 'default.png',
  `last_seen` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `weekly_accomplishment`
--

CREATE TABLE `weekly_accomplishment` (
  `id` int(11) NOT NULL,
  `stud_id` varchar(50) NOT NULL,
  `uniqueID` varchar(50) NOT NULL,
  `week_number` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `accomplishment` text NOT NULL,
  `coworkers` varchar(255) NOT NULL,
  `working_hours` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `weekly_accomplishment`
--

INSERT INTO `weekly_accomplishment` (`id`, `stud_id`, `uniqueID`, `week_number`, `date`, `time`, `accomplishment`, `coworkers`, `working_hours`, `created_at`) VALUES
(2, '2020-00123', '6625205cd60bf4782', 1, '2023-10-01', '09:00:00', 'Completed task', 'Team A', 8, '2025-04-19 16:51:03');

-- --------------------------------------------------------

--
-- Table structure for table `weekly_accomplishments`
--

CREATE TABLE `weekly_accomplishments` (
  `id` int(11) NOT NULL,
  `company_name` varchar(150) DEFAULT NULL,
  `sis_no` varchar(50) DEFAULT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_account`
--
ALTER TABLE `admin_account`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_system_notification`
--
ALTER TABLE `admin_system_notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_id` (`message_id`);

--
-- Indexes for table `chat_system`
--
ALTER TABLE `chat_system`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `company_moa`
--
ALTER TABLE `company_moa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company_skills_requirements`
--
ALTER TABLE `company_skills_requirements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_conversation` (`student_id`,`supervisor_id`),
  ADD KEY `supervisor_id` (`supervisor_id`);

--
-- Indexes for table `conversations_coordinator_hte`
--
ALTER TABLE `conversations_coordinator_hte`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_conversation` (`coordinator_id`,`supervisor_id`),
  ADD KEY `supervisor_id` (`supervisor_id`),
  ADD KEY `coordinator_id` (`coordinator_id`);

--
-- Indexes for table `conversations_coordinator_student`
--
ALTER TABLE `conversations_coordinator_student`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_conversation` (`coordinator_id`,`student_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `conversation_participants`
--
ALTER TABLE `conversation_participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_participant` (`conversation_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `coordinatorsystemnotification`
--
ALTER TABLE `coordinatorsystemnotification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coordinators_account`
--
ALTER TABLE `coordinators_account`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses_sections`
--
ALTER TABLE `courses_sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_course_section` (`course`,`section`);

--
-- Indexes for table `daily_time_records`
--
ALTER TABLE `daily_time_records`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deployed_students`
--
ALTER TABLE `deployed_students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `endorsements`
--
ALTER TABLE `endorsements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `endorsement_documents`
--
ALTER TABLE `endorsement_documents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_document` (`student_id`,`document_type`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `interns`
--
ALTER TABLE `interns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sis_no` (`sis_no`);

--
-- Indexes for table `internship_experience`
--
ALTER TABLE `internship_experience`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `intern_deployments`
--
ALTER TABLE `intern_deployments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_intern` (`intern_id`);

--
-- Indexes for table `meetings`
--
ALTER TABLE `meetings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_meetings_created_by` (`created_by`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `idx_sender_type` (`sender_type`),
  ADD KEY `fk_messages_conversation` (`conversation_id`);

--
-- Indexes for table `messages_coordinator_student`
--
ALTER TABLE `messages_coordinator_student`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conversation_id` (`conversation_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `created_at` (`created_at`),
  ADD KEY `is_read` (`is_read`);

--
-- Indexes for table `messages_coordinator_supervisor`
--
ALTER TABLE `messages_coordinator_supervisor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conversation_id` (`conversation_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `moa_form`
--
ALTER TABLE `moa_form`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_supervisor` (`supervisor_id`);

--
-- Indexes for table `narrative_reports`
--
ALTER TABLE `narrative_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `new_moa_processing`
--
ALTER TABLE `new_moa_processing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `ojt_hours`
--
ALTER TABLE `ojt_hours`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ojt_requirements`
--
ALTER TABLE `ojt_requirements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `participants`
--
ALTER TABLE `participants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pending_users`
--
ALTER TABLE `pending_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniqueID` (`uniqueID`),
  ADD UNIQUE KEY `id_number` (`id_number`),
  ADD UNIQUE KEY `phone_number` (`phone_number`),
  ADD UNIQUE KEY `stud_email` (`stud_email`);

--
-- Indexes for table `photo_documentation`
--
ALTER TABLE `photo_documentation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_document_type` (`document_type`);

--
-- Indexes for table `portfolios`
--
ALTER TABLE `portfolios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `students_data`
--
ALTER TABLE `students_data`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniqueID` (`uniqueID`),
  ADD UNIQUE KEY `idx_student_ID` (`student_ID`);

--
-- Indexes for table `student_evaluations`
--
ALTER TABLE `student_evaluations`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `stud_daily_time_records`
--
ALTER TABLE `stud_daily_time_records`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stud_evaluation`
--
ALTER TABLE `stud_evaluation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stud_skills`
--
ALTER TABLE `stud_skills`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stud_task_list`
--
ALTER TABLE `stud_task_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supervisor`
--
ALTER TABLE `supervisor`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supervisor_skills`
--
ALTER TABLE `supervisor_skills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supervisor_id` (`supervisor_id`);

--
-- Indexes for table `supervisor_system_notification`
--
ALTER TABLE `supervisor_system_notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_notification`
--
ALTER TABLE `system_notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `weekly_accomplishment`
--
ALTER TABLE `weekly_accomplishment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stud_id` (`stud_id`),
  ADD KEY `uniqueID` (`uniqueID`);

--
-- Indexes for table `weekly_accomplishments`
--
ALTER TABLE `weekly_accomplishments`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_account`
--
ALTER TABLE `admin_account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `admin_system_notification`
--
ALTER TABLE `admin_system_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_system`
--
ALTER TABLE `chat_system`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `company_moa`
--
ALTER TABLE `company_moa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_skills_requirements`
--
ALTER TABLE `company_skills_requirements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `conversations_coordinator_hte`
--
ALTER TABLE `conversations_coordinator_hte`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `conversations_coordinator_student`
--
ALTER TABLE `conversations_coordinator_student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `conversation_participants`
--
ALTER TABLE `conversation_participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coordinatorsystemnotification`
--
ALTER TABLE `coordinatorsystemnotification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=325;

--
-- AUTO_INCREMENT for table `coordinators_account`
--
ALTER TABLE `coordinators_account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `courses_sections`
--
ALTER TABLE `courses_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `daily_time_records`
--
ALTER TABLE `daily_time_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deployed_students`
--
ALTER TABLE `deployed_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `endorsements`
--
ALTER TABLE `endorsements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `endorsement_documents`
--
ALTER TABLE `endorsement_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `interns`
--
ALTER TABLE `interns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `internship_experience`
--
ALTER TABLE `internship_experience`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `intern_deployments`
--
ALTER TABLE `intern_deployments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meetings`
--
ALTER TABLE `meetings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages_coordinator_student`
--
ALTER TABLE `messages_coordinator_student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages_coordinator_supervisor`
--
ALTER TABLE `messages_coordinator_supervisor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `moa_form`
--
ALTER TABLE `moa_form`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `narrative_reports`
--
ALTER TABLE `narrative_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `new_moa_processing`
--
ALTER TABLE `new_moa_processing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ojt_hours`
--
ALTER TABLE `ojt_hours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ojt_requirements`
--
ALTER TABLE `ojt_requirements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `participants`
--
ALTER TABLE `participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pending_users`
--
ALTER TABLE `pending_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `photo_documentation`
--
ALTER TABLE `photo_documentation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `portfolios`
--
ALTER TABLE `portfolios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students_data`
--
ALTER TABLE `students_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `stud_daily_time_records`
--
ALTER TABLE `stud_daily_time_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `stud_evaluation`
--
ALTER TABLE `stud_evaluation`
  MODIFY `id` int(15) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stud_skills`
--
ALTER TABLE `stud_skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stud_task_list`
--
ALTER TABLE `stud_task_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supervisor`
--
ALTER TABLE `supervisor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `supervisor_skills`
--
ALTER TABLE `supervisor_skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supervisor_system_notification`
--
ALTER TABLE `supervisor_system_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `system_notification`
--
ALTER TABLE `system_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=283;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `weekly_accomplishment`
--
ALTER TABLE `weekly_accomplishment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `weekly_accomplishments`
--
ALTER TABLE `weekly_accomplishments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `admin_account` (`id`);

--
-- Constraints for table `attachments`
--
ALTER TABLE `attachments`
  ADD CONSTRAINT `attachments_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_system`
--
ALTER TABLE `chat_system`
  ADD CONSTRAINT `chat_system_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `students_data` (`uniqueID`),
  ADD CONSTRAINT `chat_system_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `students_data` (`uniqueID`);

--
-- Constraints for table `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `conversations_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students_data` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversations_ibfk_2` FOREIGN KEY (`supervisor_id`) REFERENCES `supervisor` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `conversations_coordinator_hte`
--
ALTER TABLE `conversations_coordinator_hte`
  ADD CONSTRAINT `conversations_coordinator_hte_ibfk_1` FOREIGN KEY (`coordinator_id`) REFERENCES `coordinators_account` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversations_coordinator_hte_ibfk_2` FOREIGN KEY (`supervisor_id`) REFERENCES `supervisor` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `conversations_coordinator_student`
--
ALTER TABLE `conversations_coordinator_student`
  ADD CONSTRAINT `fk_ccs_coordinator` FOREIGN KEY (`coordinator_id`) REFERENCES `coordinators_account` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ccs_student` FOREIGN KEY (`student_id`) REFERENCES `students_data` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `conversation_participants`
--
ALTER TABLE `conversation_participants`
  ADD CONSTRAINT `conversation_participants_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversation_participants_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `endorsements`
--
ALTER TABLE `endorsements`
  ADD CONSTRAINT `endorsements_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students_data` (`id`);

--
-- Constraints for table `endorsement_documents`
--
ALTER TABLE `endorsement_documents`
  ADD CONSTRAINT `fk_student_id` FOREIGN KEY (`student_id`) REFERENCES `students_data` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `faqs`
--
ALTER TABLE `faqs`
  ADD CONSTRAINT `faqs_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `admin_account` (`id`);

--
-- Constraints for table `internship_experience`
--
ALTER TABLE `internship_experience`
  ADD CONSTRAINT `internship_experience_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students_data` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `intern_deployments`
--
ALTER TABLE `intern_deployments`
  ADD CONSTRAINT `fk_intern_id` FOREIGN KEY (`intern_id`) REFERENCES `students_data` (`student_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `meetings`
--
ALTER TABLE `meetings`
  ADD CONSTRAINT `fk_meetings_created_by` FOREIGN KEY (`created_by`) REFERENCES `coordinators_account` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `messages_coordinator_student`
--
ALTER TABLE `messages_coordinator_student`
  ADD CONSTRAINT `fk_mcs_conv_student` FOREIGN KEY (`conversation_id`) REFERENCES `conversations_coordinator_student` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages_coordinator_supervisor`
--
ALTER TABLE `messages_coordinator_supervisor`
  ADD CONSTRAINT `fk_mcs_conversation` FOREIGN KEY (`conversation_id`) REFERENCES `conversations_coordinator_hte` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `moa_form`
--
ALTER TABLE `moa_form`
  ADD CONSTRAINT `fk_supervisor` FOREIGN KEY (`supervisor_id`) REFERENCES `supervisor` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `new_moa_processing`
--
ALTER TABLE `new_moa_processing`
  ADD CONSTRAINT `new_moa_processing_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students_data` (`id`);

--
-- Constraints for table `portfolios`
--
ALTER TABLE `portfolios`
  ADD CONSTRAINT `portfolios_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students_data` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `supervisor_skills`
--
ALTER TABLE `supervisor_skills`
  ADD CONSTRAINT `supervisor_skills_ibfk_1` FOREIGN KEY (`supervisor_id`) REFERENCES `supervisor` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `weekly_accomplishment`
--
ALTER TABLE `weekly_accomplishment`
  ADD CONSTRAINT `weekly_accomplishment_ibfk_1` FOREIGN KEY (`stud_id`) REFERENCES `students_data` (`student_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `weekly_accomplishment_ibfk_2` FOREIGN KEY (`uniqueID`) REFERENCES `students_data` (`uniqueID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
