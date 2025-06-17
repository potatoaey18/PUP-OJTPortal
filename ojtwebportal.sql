-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 07, 2025 at 11:52 AM
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
  `online_offlineStatus` varchar(50) NOT NULL DEFAULT 'Offline'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_account`
--

INSERT INTO `admin_account` (`id`, `uniqueID`, `first_name`, `middle_name`, `last_name`, `id_number`, `position`, `address`, `phone_number`, `admin_profile_picture`, `admin_email`, `admin_password`, `verification_code`, `verify_status`, `online_offlineStatus`) VALUES
(1, '662513a17a25e3248', 'Juan', 'A', 'Dela Cruz', 'ADM001', 'College of Engineering', 'Sta. Mesa, Manila', '09123456789', '../student_file_images/coe_admin.jpg', 'test1@gmail.com', '202cb962ac59075b964b07152d234b70', 710511, 'Verified', 'Offline'),
(2, '662513a17a26a3284', 'Maria', 'B', 'Santos', 'ADM002', 'College of Computer Studies', 'Quezon City', '09234567890', '../student_file_images/ccs_admin.jpg', 'test2@gmail.com', '202cb962ac59075b964b07152d234b70', 306649, 'Verified', 'Offline'),
(3, '662513a17a26b7452', 'Pedro', 'C', 'Reyes', 'ADM003', 'College of Business', 'Mandaluyong City', '09345678901', '../student_file_images/cob_admin.jpg', 'test3@gmail.com', '202cb962ac59075b964b07152d234b70', 830404, 'Verified', 'Offline'),
(4, '662513a17a26c4423', 'Ana', 'D', 'Gonzales', 'ADM004', 'College of Science', 'Makati City', '09456789012', '../student_file_images/cos_admin.jpg', 'test4@gmail.com', '202cb962ac59075b964b07152d234b70', 247453, 'Verified', 'Offline');

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
-- Table structure for table `company_skills_requirements`
--

CREATE TABLE `company_skills_requirements` (
  `id` int(11) NOT NULL,
  `company_name` varchar(150) NOT NULL,
  `skills_name` varchar(200) NOT NULL
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
(1, 1, 'You successfully logged in to your account.', 'April / 07 Monday / 2025', '4:37 PM', 'Unread');

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
  `online_offlineStatus` varchar(50) NOT NULL DEFAULT 'Offline'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coordinators_account`
--

INSERT INTO `coordinators_account` (`id`, `uniqueID`, `first_name`, `middle_name`, `last_name`, `faculty_id`, `coor_dept`, `course_handled`, `complete_address`, `phone_number`, `coordinators_email`, `coordinators_password`, `coordinators_profile_picture`, `verification_code`, `verify_status`, `online_offlineStatus`) VALUES
(1, '662517f7a16b25026', 'Prof. Carlos', 'A', 'Ramos', 'FAC001', 'College of Engineering', 'Bachelor of Science in Civil Engineering', 'Sta. Mesa, Manila', '09123456789', 'test5@gmail.com', '202cb962ac59075b964b07152d234b70', '../student_file_images/coor1.jpg', 985232, 'Verified', 'Online'),
(2, '662517f7a16bd3010', 'Prof. Lourdes', 'B', 'Tan', 'FAC002', 'College of Computer Studies', 'Bachelor of Science in Computer Science', 'Quezon City', '09234567890', 'test6@gmail.com', '202cb962ac59075b964b07152d234b70', '../student_file_images/coor2.jpg', 582458, 'Verified', 'Offline'),
(3, '662517f7a16be6633', 'Prof. Ricardo', 'C', 'Lim', 'FAC003', 'College of Business', 'Bachelor of Science in Accountancy', 'Mandaluyong City', '09345678901', 'test7@gmail.com', '202cb962ac59075b964b07152d234b70', '../student_file_images/coor3.jpg', 514148, 'Verified', 'Offline'),
(4, '662517f7a16bf9002', 'Prof. Sofia', 'D', 'Ong', 'FAC004', 'College of Science', 'Bachelor of Science in Biology', 'Makati City', '09456789012', 'test8@gmail.com', '202cb962ac59075b964b07152d234b70', '../student_file_images/coor4.jpg', 859603, 'Verified', 'Offline');

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
  `medical_at_campus` enum('YES','NO') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `endorsement_documents`
--

INSERT INTO `endorsement_documents` (`id`, `document_name`, `document_type`, `uploaded_path`, `student_id`, `upload_date`, `status`, `medical_at_campus`) VALUES
(82, 'RegistrationCertificate (4).pdf', 'moa', '/PUP/student/uploads/moa/2_RegistrationCertificate (4).pdf', 2, '2025-04-06 11:31:43', 'pending', NULL);

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
  `status` enum('checking_info','ulco_review','returned_to_coordinator','dean_vpaa_signature','signed_moa_retrieved','rejected') NOT NULL DEFAULT 'checking_info',
  `request_date` datetime DEFAULT NULL,
  `checking_info_date` datetime DEFAULT NULL,
  `ulco_review_date` datetime DEFAULT NULL,
  `returned_to_coordinator_date` datetime DEFAULT NULL,
  `dean_vpaa_signature_date` datetime DEFAULT NULL,
  `signed_moa_retrieved_date` datetime DEFAULT NULL,
  `moa_document_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `new_moa_processing`
--

INSERT INTO `new_moa_processing` (`id`, `student_id`, `status`, `request_date`, `checking_info_date`, `ulco_review_date`, `returned_to_coordinator_date`, `dean_vpaa_signature_date`, `signed_moa_retrieved_date`, `moa_document_path`) VALUES
(7, 1, 'signed_moa_retrieved', '2025-04-01 14:41:44', '2025-04-01 14:41:44', '2025-04-15 14:43:09', '2025-04-22 14:43:09', '2025-04-29 14:43:09', '2025-05-06 15:00:46', '/PUP/student/uploads/moa/1_RegistrationCertificate (3).pdf'),
(8, 2, 'checking_info', '2025-04-02 11:04:03', '2025-04-02 11:04:03', NULL, NULL, NULL, NULL, '/PUP/student/uploads/moa/2_testFileMOA.pdf');

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
  `medical_condition` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students_data`
--

INSERT INTO `students_data` (`id`, `uniqueID`, `first_name`, `middle_name`, `last_name`, `student_ID`, `stud_dept`, `stud_course`, `stud_section`, `complete_address`, `stud_gender`, `phone_number`, `stud_email`, `stud_password`, `guardians_name`, `guardians_cpNumber`, `profile_picture`, `verification_code`, `verify_status`, `online_offlineStatus`, `ojt_status`, `year_lvl`, `stud_hte`, `total_rendered_hours`, `medical_condition`) VALUES
(1, '6625205cd60bf4782', 'Miguel', 'A', 'Torres', '2020-00123', 'College of Engineering', 'Bachelor of Science in Civil Engineering', 'BSCE 3-1', '123 R. Magsaysay Blvd, Sta. Mesa, Manila', 'Male', '09123456789', 'test9@gmail.com', '202cb962ac59075b964b07152d234b70', 'Roberto Torres', '09187654321', '../student_file_images/67ecaa4c8b3a9-DSC09393.JPG', 187622, 'Verified', 'Online', NULL, '', '', 0, ''),
(2, '6625205cd60cc1159', 'Antonio', 'B', 'Santos', '2020-00124', 'College of Engineering', 'Bachelor of Science in Civil Engineering', 'BSCE 3-1', '456 Aurora Blvd, Quezon City', 'Male', '09234567890', 'test10@gmail.com', '202cb962ac59075b964b07152d234b70', 'Enrique Santos', '09276543210', '../student_file_images/67eca902190b5-Neurodecks_New-removebg-preview.png', 264399, 'Verified', 'Offline', NULL, '', '', 0, ''),
(3, '6625205cd60cd7278', 'Carlos', 'C', 'Reyes', '2020-00125', 'College of Engineering', 'Bachelor of Science in Civil Engineering', 'BSCE 3-1', '789 Shaw Blvd, Mandaluyong City', 'Male', '09345678901', 'test11@gmail.com', '202cb962ac59075b964b07152d234b70', 'Alberto Reyes', '09365432109', '../student_file_images/student3.jpg', 970213, 'Verified', 'Offline', NULL, '', '', 0, ''),
(4, '6625205cd60ce3907', 'Andrea', 'D', 'Lim', '2020-00234', 'College of Computer Studies', 'Bachelor of Science in Computer Science', 'BSCS 3-2', '101 Ayala Ave, Makati City', 'Female', '09456789012', 'test12@gmail.com', '202cb962ac59075b964b07152d234b70', 'Antonio Lim', '09454321098', '../student_file_images/student4.jpg', 130605, 'Verified', 'Offline', NULL, '', '', 0, ''),
(5, '6625205cd60cf4759', 'Maria', 'E', 'Gonzales', '2020-00235', 'College of Computer Studies', 'Bachelor of Science in Computer Science', 'BSCS 3-2', '202 Buendia Ave, Makati City', 'Female', '09567890123', 'test13@gmail.com', '202cb962ac59075b964b07152d234b70', 'Juan Gonzales', '09543210987', '../student_file_images/student5.jpg', 795766, 'Verified', 'Offline', NULL, '', '', 0, ''),
(6, '6625205cd60d01403', 'Sofia', 'F', 'Ong', '2020-00236', 'College of Computer Studies', 'Bachelor of Science in Computer Science', 'BSCS 3-2', '303 Ortigas Ave, Pasig City', 'Female', '09678901234', 'test14@gmail.com', '202cb962ac59075b964b07152d234b70', 'Ricardo Ong', '09632109876', '../student_file_images/67ee2c92a5585-99b11d57-8b96-44cd-9d6d-0775345436f2.jpg', 617952, 'Verified', 'Offline', NULL, '', '', 0, ''),
(7, '6625205cd60d16348', 'Luis', 'G', 'Tan', '2020-00345', 'College of Business', 'Bachelor of Science in Accountancy', 'BSA 3-1', '404 EDSA, Mandaluyong City', 'Male', '09789012345', 'test15@gmail.com', '202cb962ac59075b964b07152d234b70', 'Alfredo Tan', '09721098765', '../student_file_images/student7.jpg', 648993, 'Verified', 'Offline', NULL, '', '', 0, ''),
(8, '6625205cd60d27110', 'Jose', 'H', 'Chua', '2020-00346', 'College of Business', 'Bachelor of Science in Accountancy', 'BSA 3-1', '505 Taft Ave, Manila', 'Male', '09890123456', 'test16@gmail.com', '202cb962ac59075b964b07152d234b70', 'Fernando Chua', '09810987654', '../student_file_images/student8.jpg', 808163, 'Verified', 'Offline', NULL, '', '', 0, ''),
(9, '6625205cd60d34910', 'Manuel', 'I', 'Sy', '2020-00347', 'College of Business', 'Bachelor of Science in Accountancy', 'BSA 3-1', '606 Roxas Blvd, Pasay City', 'Male', '09901234567', 'test17@gmail.com', '202cb962ac59075b964b07152d234b70', 'Eduardo Sy', '09909876543', '../student_file_images/student9.jpg', 613007, 'Verified', 'Offline', NULL, '', '', 0, ''),
(10, '6625205cd60d48477', 'Elena', 'J', 'Dizon', '2020-00456', 'College of Science', 'Bachelor of Science in Biology', 'BSBIO 3-1', '707 Commonwealth Ave, Quezon City', 'Female', '09112345678', 'test18@gmail.com', '202cb962ac59075b964b07152d234b70', 'Ramon Dizon', '09198765432', '../student_file_images/student10.jpg', 577511, 'Verified', 'Offline', NULL, '', '', 0, ''),
(11, '6625205cd60d55377', 'Carmen', 'K', 'Navarro', '2020-00457', 'College of Science', 'Bachelor of Science in Biology', 'BSBIO 3-1', '808 Katipunan Ave, Quezon City', 'Female', '09223456789', 'test19@gmail.com', '202cb962ac59075b964b07152d234b70', 'Roberto Navarro', '09287654321', '../student_file_images/student11.jpg', 328206, 'Verified', 'Offline', NULL, '', '', 0, ''),
(12, '6625205cd60d62431', 'Isabel', 'L', 'Romero', '2020-00458', 'College of Science', 'Bachelor of Science in Biology', 'BSBIO 3-1', '909 España Blvd, Manila', 'Female', '09334567890', 'test20@gmail.com', '202cb962ac59075b964b07152d234b70', 'Felipe Romero', '09376543210', '../student_file_images/student12.jpg', 598221, 'Verified', 'Offline', NULL, '', '', 0, '');

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
  `PM_time_OUT_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `online_offlineStatus` varchar(150) NOT NULL DEFAULT 'Offline'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supervisor`
--

INSERT INTO `supervisor` (`id`, `uniqueID`, `first_name`, `middle_name`, `last_name`, `company_name`, `business_nature`, `position`, `company_address`, `date_notarized`, `moa_validity`, `link_to_moa`, `supervisor_email`, `supervisor_password`, `phone_number`, `supervisor_profile_picture`, `verification_code`, `verify_status`, `online_offlineStatus`) VALUES
(1, '66252d8f8c95a7624', 'Robert', 'A', 'Johnson', 'Accenture Philippines', 'IT Services', 'Senior Manager', '16/F Robinsons Cybergate Tower, EDSA, Mandaluyong City', '2025-01-15', '2026-01-15', '#', 'test13@gmail.com', '202cb962ac59075b964b07152d234b70', '09123456789', '../student_file_images/supervisor1.jpg', 788441, 'Verified', 'Offline'),
(2, '66252d8f8c9649173', 'Jennifer', 'B', 'Smith', 'IBM Philippines', 'Technology Solutions', 'HR Manager', '12/F IBM Plaza, Eastwood City, Quezon City', '2025-02-20', '2026-02-20', '#', 'test14@gmail.com', '202cb962ac59075b964b07152d234b70', '09234567890', '../student_file_images/supervisor2.jpg', 957348, 'Verified', 'Offline'),
(3, '66252d8f8c9652162', 'Michael', 'C', 'Williams', 'Deloitte Philippines', 'Professional Services', 'Consulting Manager', '21/F Deloitte Center, Bonifacio Global City', '2025-03-10', '2026-03-10', '#', 'test15@gmail.com', '202cb962ac59075b964b07152d234b70', '09345678901', '../student_file_images/supervisor3.jpg', 402868, 'Verified', 'Offline'),
(4, '66252d8f8c9669130', 'Sarah', 'D', 'Brown', 'PwC Philippines', 'Audit and Assurance', 'Senior Associate', '30/F PwC Tower, Makati City', '2025-01-30', '2026-01-30', '#', 'test16@gmail.com', '202cb962ac59075b964b07152d234b70', '09456789012', '../student_file_images/supervisor4.jpg', 566765, 'Verified', 'Offline');

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
(15, 6, 'You successfully logged in to your account.', 'March / 31 Monday / 2025', '8:21 PM', 'Unread'),
(16, 2, 'You successfully logged in to your account.', 'March / 31 Monday / 2025', '9:36 PM', 'Unread'),
(17, 2, 'You successfully logged out to your account.', 'March / 31 Monday / 2025', '9:45 PM', 'Unread'),
(18, 1, 'You successfully logged in to your account.', 'March / 31 Monday / 2025', '10:08 PM', 'Unread'),
(19, 1, 'You successfully logged out to your account.', 'March / 31 Monday / 2025', '10:19 PM', 'Unread'),
(20, 2, 'You successfully logged in to your account.', 'March / 31 Monday / 2025', '10:20 PM', 'Unread'),
(21, 2, 'You successfully logged out to your account.', 'March / 31 Monday / 2025', '10:26 PM', 'Unread'),
(22, 2, 'You successfully logged in to your account.', 'March / 31 Monday / 2025', '10:52 PM', 'Unread'),
(23, 2, 'You successfully logged out to your account.', 'March / 31 Monday / 2025', '10:59 PM', 'Unread'),
(24, 1, 'You successfully logged in to your account.', 'March / 31 Monday / 2025', '11:07 PM', 'Unread'),
(25, 1, 'You successfully logged out to your account.', 'March / 31 Monday / 2025', '11:14 PM', 'Unread'),
(26, 2, 'You successfully logged in to your account.', 'March / 31 Monday / 2025', '11:30 PM', 'Unread'),
(27, 1, 'You successfully logged in to your account.', 'April / 01 Tuesday / 2025', '11:14 AM', 'Unread'),
(28, 1, 'You successfully logged out to your account.', 'April / 01 Tuesday / 2025', '11:27 AM', 'Unread'),
(29, 1, 'You successfully logged in to your account.', 'April / 01 Tuesday / 2025', '11:29 AM', 'Unread'),
(30, 1, 'You successfully logged out to your account.', 'April / 01 Tuesday / 2025', '11:46 AM', 'Unread'),
(31, 6, 'You successfully logged in to your account.', 'April / 01 Tuesday / 2025', '12:21 PM', 'Unread'),
(32, 6, 'You successfully logged out to your account.', 'April / 01 Tuesday / 2025', '12:38 PM', 'Unread'),
(33, 1, 'You successfully logged in to your account.', 'April / 01 Tuesday / 2025', '12:38 PM', 'Unread'),
(34, 1, 'You successfully logged out to your account.', 'April / 01 Tuesday / 2025', '12:51 PM', 'Unread'),
(35, 2, 'You successfully logged in to your account.', 'April / 01 Tuesday / 2025', '1:04 PM', 'Unread'),
(36, 2, 'You successfully logged out to your account.', 'April / 01 Tuesday / 2025', '1:17 PM', 'Unread'),
(37, 6, 'You successfully logged in to your account.', 'April / 01 Tuesday / 2025', '1:27 PM', 'Unread'),
(38, 2, 'You successfully logged in to your account.', 'April / 01 Tuesday / 2025', '1:30 PM', 'Unread'),
(39, 6, 'You successfully logged out to your account.', 'April / 01 Tuesday / 2025', '1:36 PM', 'Unread'),
(40, 2, 'You successfully logged in to your account.', 'April / 01 Tuesday / 2025', '1:36 PM', 'Unread'),
(41, 2, 'You successfully logged out to your account.', 'April / 01 Tuesday / 2025', '1:59 PM', 'Unread'),
(42, 6, 'You successfully logged in to your account.', 'April / 01 Tuesday / 2025', '2:14 PM', 'Unread'),
(43, 2, 'You successfully logged in to your account.', 'April / 01 Tuesday / 2025', '2:18 PM', 'Unread'),
(44, 1, 'You successfully logged in to your account.', 'April / 01 Tuesday / 2025', '2:29 PM', 'Unread'),
(45, 1, 'You successfully logged out to your account.', 'April / 01 Tuesday / 2025', '3:29 PM', 'Unread'),
(46, 1, 'You successfully logged in to your account.', 'April / 01 Tuesday / 2025', '3:32 PM', 'Unread'),
(47, 1, 'You successfully logged out to your account.', 'April / 01 Tuesday / 2025', '3:44 PM', 'Unread'),
(48, 6, 'You successfully logged in to your account.', 'April / 01 Tuesday / 2025', '3:50 PM', 'Unread'),
(49, 6, 'You successfully logged out to your account.', 'April / 01 Tuesday / 2025', '3:56 PM', 'Unread'),
(50, 1, 'You successfully logged in to your account.', 'April / 01 Tuesday / 2025', '4:18 PM', 'Unread'),
(51, 1, 'You successfully logged out to your account.', 'April / 01 Tuesday / 2025', '4:33 PM', 'Unread'),
(52, 2, 'You successfully logged in to your account.', 'April / 01 Tuesday / 2025', '5:48 PM', 'Unread'),
(53, 2, 'You successfully logged out to your account.', 'April / 01 Tuesday / 2025', '5:57 PM', 'Unread'),
(54, 2, 'You successfully logged in to your account.', 'April / 01 Tuesday / 2025', '6:01 PM', 'Unread'),
(55, 6, 'You successfully logged in to your account.', 'April / 01 Tuesday / 2025', '9:21 PM', 'Unread'),
(56, 6, 'You successfully logged out to your account.', 'April / 01 Tuesday / 2025', '9:28 PM', 'Unread'),
(57, 2, 'You successfully logged in to your account.', 'April / 01 Tuesday / 2025', '9:32 PM', 'Unread'),
(58, 2, 'You successfully logged out to your account.', 'April / 01 Tuesday / 2025', '9:38 PM', 'Unread'),
(59, 1, 'You successfully logged in to your account.', 'April / 01 Tuesday / 2025', '9:47 PM', 'Unread'),
(60, 1, 'You successfully logged out to your account.', 'April / 01 Tuesday / 2025', '9:53 PM', 'Unread'),
(61, 1, 'You successfully logged in to your account.', 'April / 01 Tuesday / 2025', '10:10 PM', 'Unread'),
(62, 2, 'You successfully logged in to your account.', 'April / 02 Wednesday / 2025', '11:03 AM', 'Unread'),
(63, 2, 'Profile picture updated successfully.', 'April / 02 Wednesday / 2025', '11:03 AM', 'Unread'),
(64, 1, 'You successfully logged in to your account.', 'April / 02 Wednesday / 2025', '11:06 AM', 'Unread'),
(65, 1, 'You successfully logged out to your account.', 'April / 02 Wednesday / 2025', '11:06 AM', 'Unread'),
(66, 2, 'You successfully logged in to your account.', 'April / 02 Wednesday / 2025', '11:06 AM', 'Unread'),
(67, 1, 'You successfully logged in to your account.', 'April / 02 Wednesday / 2025', '11:08 AM', 'Unread'),
(68, 1, 'Profile picture updated successfully.', 'April / 02 Wednesday / 2025', '11:09 AM', 'Unread'),
(69, 1, 'You successfully logged out to your account.', 'April / 02 Wednesday / 2025', '11:09 AM', 'Unread'),
(70, 6, 'You successfully logged in to your account.', 'April / 02 Wednesday / 2025', '10:45 PM', 'Unread'),
(71, 6, 'You successfully logged out to your account.', 'April / 02 Wednesday / 2025', '10:51 PM', 'Unread'),
(72, 2, 'You successfully logged in to your account.', 'April / 02 Wednesday / 2025', '10:52 PM', 'Unread'),
(73, 2, 'You successfully logged out to your account.', 'April / 02 Wednesday / 2025', '10:58 PM', 'Unread'),
(74, 2, 'You successfully logged in to your account.', 'April / 02 Wednesday / 2025', '11:02 PM', 'Unread'),
(75, 1, 'You successfully logged in to your account.', 'April / 03 Thursday / 2025', '9:26 AM', 'Unread'),
(76, 6, 'You successfully logged in to your account.', 'April / 03 Thursday / 2025', '9:34 AM', 'Unread'),
(77, 6, 'You successfully logged out to your account.', 'April / 03 Thursday / 2025', '9:41 AM', 'Unread'),
(78, 2, 'You successfully logged in to your account.', 'April / 03 Thursday / 2025', '12:32 PM', 'Unread'),
(79, 2, 'You successfully logged out to your account.', 'April / 03 Thursday / 2025', '12:41 PM', 'Unread'),
(80, 1, 'You successfully logged in to your account.', 'April / 03 Thursday / 2025', '12:41 PM', 'Unread'),
(81, 1, 'You successfully logged out to your account.', 'April / 03 Thursday / 2025', '12:59 PM', 'Unread'),
(82, 2, 'You successfully logged in to your account.', 'April / 03 Thursday / 2025', '1:22 PM', 'Unread'),
(83, 2, 'You successfully logged out to your account.', 'April / 03 Thursday / 2025', '1:42 PM', 'Unread'),
(84, 6, 'You successfully logged in to your account.', 'April / 03 Thursday / 2025', '1:43 PM', 'Unread'),
(85, 6, 'You successfully logged out to your account.', 'April / 03 Thursday / 2025', '2:13 PM', 'Unread'),
(86, 6, 'You successfully logged in to your account.', 'April / 03 Thursday / 2025', '2:17 PM', 'Unread'),
(87, 6, 'You successfully logged out to your account.', 'April / 03 Thursday / 2025', '2:28 PM', 'Unread'),
(88, 6, 'You successfully logged in to your account.', 'April / 03 Thursday / 2025', '2:36 PM', 'Unread'),
(89, 6, 'Profile picture updated successfully.', 'April / 03 Thursday / 2025', '2:37 PM', 'Unread'),
(90, 6, 'You successfully logged out to your account.', 'April / 03 Thursday / 2025', '2:43 PM', 'Unread'),
(91, 2, 'You successfully logged in to your account.', 'April / 03 Thursday / 2025', '2:55 PM', 'Unread'),
(92, 2, 'You successfully logged out to your account.', 'April / 03 Thursday / 2025', '3:01 PM', 'Unread'),
(93, 2, 'You successfully logged in to your account.', 'April / 03 Thursday / 2025', '3:05 PM', 'Unread'),
(94, 2, 'You successfully logged out to your account.', 'April / 03 Thursday / 2025', '3:13 PM', 'Unread'),
(95, 2, 'You successfully logged in to your account.', 'April / 03 Thursday / 2025', '3:24 PM', 'Unread'),
(96, 2, 'You successfully logged out to your account.', 'April / 03 Thursday / 2025', '3:31 PM', 'Unread'),
(97, 1, 'You successfully logged in to your account.', 'April / 03 Thursday / 2025', '3:35 PM', 'Unread'),
(98, 1, 'You successfully logged out to your account.', 'April / 03 Thursday / 2025', '3:44 PM', 'Unread'),
(99, 2, 'You successfully logged in to your account.', 'April / 03 Thursday / 2025', '5:45 PM', 'Unread'),
(100, 2, 'You successfully logged out to your account.', 'April / 03 Thursday / 2025', '5:57 PM', 'Unread'),
(101, 6, 'You successfully logged in to your account.', 'April / 03 Thursday / 2025', '10:25 PM', 'Unread'),
(102, 6, 'You successfully logged out to your account.', 'April / 03 Thursday / 2025', '10:48 PM', 'Unread'),
(103, 6, 'You successfully logged in to your account.', 'April / 03 Thursday / 2025', '10:54 PM', 'Unread'),
(104, 6, 'You successfully logged out to your account.', 'April / 03 Thursday / 2025', '11:01 PM', 'Unread'),
(105, 1, 'You successfully logged in to your account.', 'April / 03 Thursday / 2025', '11:18 PM', 'Unread'),
(106, 2, 'You successfully logged in to your account.', 'April / 04 Friday / 2025', '8:48 AM', 'Unread'),
(107, 2, 'You successfully logged out to your account.', 'April / 04 Friday / 2025', '8:55 AM', 'Unread'),
(108, 2, 'You successfully logged in to your account.', 'April / 04 Friday / 2025', '8:55 AM', 'Unread'),
(109, 2, 'You successfully logged in to your account.', 'April / 04 Friday / 2025', '9:09 AM', 'Unread'),
(110, 2, 'You successfully logged out to your account.', 'April / 04 Friday / 2025', '9:21 AM', 'Unread'),
(111, 2, 'You successfully logged in to your account.', 'April / 04 Friday / 2025', '9:23 AM', 'Unread'),
(112, 2, 'You successfully logged out to your account.', 'April / 04 Friday / 2025', '9:32 AM', 'Unread'),
(113, 2, 'You successfully logged in to your account.', 'April / 04 Friday / 2025', '9:34 AM', 'Unread'),
(114, 2, 'You successfully logged out to your account.', 'April / 04 Friday / 2025', '9:40 AM', 'Unread'),
(115, 2, 'You successfully logged in to your account.', 'April / 04 Friday / 2025', '10:11 AM', 'Unread'),
(116, 2, 'You successfully logged out to your account.', 'April / 04 Friday / 2025', '10:20 AM', 'Unread'),
(117, 2, 'You successfully logged in to your account.', 'April / 04 Friday / 2025', '10:34 AM', 'Unread'),
(118, 2, 'You successfully logged out to your account.', 'April / 04 Friday / 2025', '10:42 AM', 'Unread'),
(119, 2, 'You successfully logged in to your account.', 'April / 04 Friday / 2025', '2:44 PM', 'Unread'),
(120, 2, 'You successfully logged out to your account.', 'April / 04 Friday / 2025', '3:03 PM', 'Unread'),
(121, 1, 'You successfully logged in to your account.', 'April / 05 Saturday / 2025', '11:01 PM', 'Unread'),
(122, 1, 'You successfully logged out to your account.', 'April / 05 Saturday / 2025', '11:07 PM', 'Unread'),
(123, 1, 'You successfully logged in to your account.', 'April / 06 Sunday / 2025', '12:30 AM', 'Unread'),
(124, 2, 'You successfully logged in to your account.', 'April / 06 Sunday / 2025', '11:27 AM', 'Unread'),
(125, 2, 'You successfully logged out to your account.', 'April / 06 Sunday / 2025', '11:37 AM', 'Unread'),
(126, 6, 'You successfully logged in to your account.', 'April / 06 Sunday / 2025', '11:42 AM', 'Unread'),
(127, 6, 'You successfully logged out to your account.', 'April / 06 Sunday / 2025', '11:51 AM', 'Unread'),
(128, 2, 'You successfully logged in to your account.', 'April / 06 Sunday / 2025', '12:10 PM', 'Unread'),
(129, 2, 'You successfully logged out to your account.', 'April / 06 Sunday / 2025', '12:18 PM', 'Unread'),
(130, 6, 'You successfully logged in to your account.', 'April / 06 Sunday / 2025', '12:21 PM', 'Unread'),
(131, 6, 'You successfully logged out to your account.', 'April / 06 Sunday / 2025', '12:33 PM', 'Unread'),
(132, 2, 'You successfully logged in to your account.', 'April / 06 Sunday / 2025', '12:40 PM', 'Unread'),
(133, 2, 'You successfully logged out to your account.', 'April / 06 Sunday / 2025', '12:46 PM', 'Unread'),
(134, 1, 'You successfully logged in to your account.', 'April / 06 Sunday / 2025', '2:39 PM', 'Unread'),
(135, 1, 'You successfully logged out to your account.', 'April / 06 Sunday / 2025', '2:48 PM', 'Unread'),
(136, 1, 'You successfully logged in to your account.', 'April / 06 Sunday / 2025', '3:40 PM', 'Unread'),
(137, 1, 'You successfully logged out to your account.', 'April / 06 Sunday / 2025', '3:52 PM', 'Unread'),
(138, 6, 'You successfully logged in to your account.', 'April / 06 Sunday / 2025', '3:56 PM', 'Unread'),
(139, 6, 'You successfully logged out to your account.', 'April / 06 Sunday / 2025', '4:02 PM', 'Unread'),
(140, 6, 'You successfully logged in to your account.', 'April / 06 Sunday / 2025', '5:38 PM', 'Unread'),
(141, 6, 'You successfully logged out to your account.', 'April / 06 Sunday / 2025', '5:44 PM', 'Unread'),
(142, 2, 'You successfully logged in to your account.', 'April / 06 Sunday / 2025', '6:29 PM', 'Unread'),
(143, 2, 'You successfully logged out to your account.', 'April / 06 Sunday / 2025', '6:36 PM', 'Unread'),
(144, 1, 'You successfully logged in to your account.', 'April / 06 Sunday / 2025', '8:15 PM', 'Unread'),
(145, 1, 'You successfully logged out to your account.', 'April / 06 Sunday / 2025', '8:21 PM', 'Unread'),
(146, 1, 'You successfully logged in to your account.', 'April / 06 Sunday / 2025', '8:23 PM', 'Unread'),
(147, 1, 'You successfully logged out to your account.', 'April / 06 Sunday / 2025', '8:30 PM', 'Unread'),
(148, 1, 'You successfully logged in to your account.', 'April / 06 Sunday / 2025', '9:34 PM', 'Unread'),
(149, 1, 'You successfully logged out to your account.', 'April / 06 Sunday / 2025', '9:48 PM', 'Unread'),
(150, 1, 'You successfully logged in to your account.', 'April / 06 Sunday / 2025', '9:57 PM', 'Unread'),
(151, 1, 'You successfully logged out to your account.', 'April / 06 Sunday / 2025', '10:03 PM', 'Unread'),
(152, 1, 'You successfully logged in to your account.', 'April / 06 Sunday / 2025', '10:23 PM', 'Unread'),
(153, 1, 'You successfully logged out to your account.', 'April / 06 Sunday / 2025', '10:30 PM', 'Unread'),
(154, 6, 'You successfully logged in to your account.', 'April / 06 Sunday / 2025', '10:44 PM', 'Unread'),
(155, 6, 'You successfully logged out to your account.', 'April / 06 Sunday / 2025', '10:58 PM', 'Unread'),
(156, 1, 'You successfully logged in to your account.', 'April / 06 Sunday / 2025', '11:15 PM', 'Unread'),
(157, 6, 'You successfully logged in to your account.', 'April / 06 Sunday / 2025', '11:28 PM', 'Unread'),
(158, 6, 'You successfully logged out to your account.', 'April / 06 Sunday / 2025', '11:34 PM', 'Unread'),
(159, 1, 'You successfully logged in to your account.', 'April / 06 Sunday / 2025', '11:47 PM', 'Unread');

-- --------------------------------------------------------

--
-- Table structure for table `weekly_accomplishment`
--

CREATE TABLE `weekly_accomplishment` (
  `id` int(11) NOT NULL,
  `stud_id` int(11) NOT NULL,
  `uniqueID` varchar(50) NOT NULL,
  `week_number` int(2) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `accomplishment` text NOT NULL,
  `coworkers` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `working_hours` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `working_student_documents`
--

CREATE TABLE `working_student_documents` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `document_type` varchar(50) NOT NULL,
  `uploaded_path` varchar(255) DEFAULT NULL,
  `upload_date` datetime DEFAULT NULL,
  `status` enum('pending','accepted','denied') NOT NULL DEFAULT 'pending'
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
-- Indexes for table `chat_system`
--
ALTER TABLE `chat_system`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `company_skills_requirements`
--
ALTER TABLE `company_skills_requirements`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `deployed_students`
--
ALTER TABLE `deployed_students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `endorsement_documents`
--
ALTER TABLE `endorsement_documents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_document` (`student_id`,`document_type`);

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
-- Indexes for table `students_data`
--
ALTER TABLE `students_data`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniqueID` (`uniqueID`);

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
-- Indexes for table `weekly_accomplishment`
--
ALTER TABLE `weekly_accomplishment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stud_id` (`stud_id`),
  ADD KEY `uniqueID` (`uniqueID`);

--
-- Indexes for table `working_student_documents`
--
ALTER TABLE `working_student_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_account`
--
ALTER TABLE `admin_account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `admin_system_notification`
--
ALTER TABLE `admin_system_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_system`
--
ALTER TABLE `chat_system`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_skills_requirements`
--
ALTER TABLE `company_skills_requirements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coordinatorsystemnotification`
--
ALTER TABLE `coordinatorsystemnotification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `coordinators_account`
--
ALTER TABLE `coordinators_account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `deployed_students`
--
ALTER TABLE `deployed_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `endorsement_documents`
--
ALTER TABLE `endorsement_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `narrative_reports`
--
ALTER TABLE `narrative_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `new_moa_processing`
--
ALTER TABLE `new_moa_processing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
-- AUTO_INCREMENT for table `students_data`
--
ALTER TABLE `students_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `stud_daily_time_records`
--
ALTER TABLE `stud_daily_time_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `supervisor_system_notification`
--
ALTER TABLE `supervisor_system_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_notification`
--
ALTER TABLE `system_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=160;

--
-- AUTO_INCREMENT for table `weekly_accomplishment`
--
ALTER TABLE `weekly_accomplishment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `working_student_documents`
--
ALTER TABLE `working_student_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chat_system`
--
ALTER TABLE `chat_system`
  ADD CONSTRAINT `chat_system_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `students_data` (`uniqueID`),
  ADD CONSTRAINT `chat_system_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `students_data` (`uniqueID`);

--
-- Constraints for table `endorsement_documents`
--
ALTER TABLE `endorsement_documents`
  ADD CONSTRAINT `fk_student_id` FOREIGN KEY (`student_id`) REFERENCES `students_data` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `new_moa_processing`
--
ALTER TABLE `new_moa_processing`
  ADD CONSTRAINT `fk_new_moa_processing_student_id` FOREIGN KEY (`student_id`) REFERENCES `students_data` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `weekly_accomplishment`
--
ALTER TABLE `weekly_accomplishment`
  ADD CONSTRAINT `weekly_accomplishment_ibfk_1` FOREIGN KEY (`stud_id`) REFERENCES `students_data` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `weekly_accomplishment_ibfk_2` FOREIGN KEY (`uniqueID`) REFERENCES `students_data` (`uniqueID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `working_student_documents`
--
ALTER TABLE `working_student_documents`
  ADD CONSTRAINT `fk_working_student_documents_student_id` FOREIGN KEY (`student_id`) REFERENCES `students_data` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
