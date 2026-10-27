-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Oct 27, 2026 at 02:27 PM
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
-- Database: `ubuntu_care`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `logID` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `details` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`logID`, `userID`, `action`, `details`, `created_at`) VALUES
(1, 15, 'Login', 'admin', '2026-10-26 00:47:08'),
(2, 16, 'Registered', 'Thato@sedulawesi123.co.za', '2026-10-26 00:57:14'),
(3, 16, 'Login', 'ngo_admin', '2026-10-26 00:57:44'),
(4, 16, 'NGO application submitted', 'Soweto Care Kitchen', '2026-10-26 00:59:53'),
(5, 15, 'Login', 'admin', '2026-10-26 01:00:55'),
(6, 15, 'NGO approved', 'Soweto Care Kitchen', '2026-10-26 01:01:26'),
(7, 16, 'Login', 'ngo_admin', '2026-10-26 01:02:43'),
(8, 15, 'Login', 'admin', '2026-10-26 01:07:23'),
(9, 16, 'Login', 'ngo_admin', '2026-10-26 01:12:58'),
(10, 17, 'Registered', 'phumzile31@zikode.co.za', '2026-10-26 01:17:44'),
(11, 16, 'Login', 'ngo_admin', '2026-10-26 02:29:06'),
(12, 14, 'Password reset requested', 'kiki1@gmail.com', '2026-10-26 02:30:50'),
(13, 14, 'Password reset completed', '', '2026-10-26 02:31:57'),
(14, 16, 'Login', 'ngo_admin', '2026-10-26 02:32:01'),
(15, 14, 'Login', 'donor', '2026-10-26 02:47:48'),
(16, 14, 'Login', 'donor', '2026-10-26 11:30:57'),
(17, 16, 'Login', 'ngo_admin', '2026-10-26 11:31:41'),
(18, 15, 'Login', 'admin', '2026-10-26 12:01:34'),
(19, 15, 'Database backup saved', 'ubuntu_care_backup_2026-10-26_11-02-43.sql', '2026-10-26 12:02:43'),
(20, 15, 'Report exported (CSV)', 'Help requests by category and area', '2026-10-26 12:25:26'),
(21, 15, 'Report exported (CSV)', 'Help requests by category and area', '2026-10-26 12:28:53'),
(22, 27, 'Registered', 'Role: beneficiary', '2026-10-12 13:05:50'),
(23, 27, 'Help request submitted', 'food, Soweto', '2026-10-12 13:05:50'),
(24, 37, 'Registered', 'Role: donor', '2026-10-13 13:05:50'),
(25, 37, 'Pledge made', 'R1500 to Soweto Food Hamper Project', '2026-10-14 13:05:50'),
(26, 18, 'NGO profile saved', 'Soweto Food Hamper Project', '2026-10-11 13:05:50'),
(27, 28, 'Help request submitted', 'shelter, Alexandra', '2026-10-13 13:05:50'),
(28, 39, 'Pledge made', 'R3000 to Alex Shelter Network', '2026-10-15 13:05:50'),
(29, 42, 'Pledge made', 'R5000 to Sandton Community Clinic', '2026-10-16 13:05:50'),
(30, 43, 'Volunteer signed up', 'Soweto Food Hamper Project', '2026-10-17 13:05:50'),
(31, 35, 'Help request submitted', 'food, Soweto', '2026-10-20 13:05:50'),
(32, 15, 'NGO approved', 'Katlehong Meals on Wheels', '2026-10-26 13:08:54'),
(33, 27, 'Login', 'beneficiary', '2026-10-26 13:13:32'),
(34, 18, 'Login', 'ngo_admin', '2026-10-26 13:23:56'),
(35, 18, 'Status update', 'match #18 to pending', '2026-10-26 13:25:35'),
(36, 37, 'Login', 'donor', '2026-10-26 13:32:14'),
(37, 37, 'Pledge made', 'R250.00 to Soweto Care Kitchen', '2026-10-26 13:33:34'),
(38, 43, 'Login', 'volunteer', '2026-10-26 13:36:30'),
(39, 18, 'Login', 'ngo_admin', '2026-10-26 13:41:17'),
(40, 25, 'Login', 'ngo_admin', '2026-10-26 13:42:31'),
(41, 15, 'Login', 'admin', '2026-10-26 17:23:56'),
(42, 27, 'Login', 'beneficiary', '2026-10-26 18:22:06'),
(43, 27, 'Help request submitted', 'shelter, Alexandra', '2026-10-26 18:23:05'),
(44, 27, 'Help request submitted', 'clothing, Tembisa', '2026-10-26 18:29:53'),
(45, 27, 'Help request submitted', 'medical, Midrand', '2026-10-26 18:34:27'),
(46, 27, 'Help request submitted', 'shelter, Katlehong', '2026-10-26 18:37:10'),
(47, 41, 'Login', 'donor', '2026-10-26 19:09:13'),
(48, 41, 'Login', 'donor', '2026-10-26 19:14:44'),
(49, 41, 'Account locked', 'Too many failed logins', '2026-10-26 19:15:09'),
(50, 41, 'Login', 'donor', '2026-10-26 20:30:55'),
(51, 51, 'Registered', 'lindiwe.dube@test.co.za', '2026-10-26 22:22:38'),
(52, 51, 'Login', 'beneficiary', '2026-10-26 22:25:29'),
(53, 52, 'Registered', 'khanyisa@test.co.za', '2026-10-26 22:40:50'),
(54, 53, 'Registered', 'michael.naidoo@test.co.za', '2026-10-26 22:43:17'),
(55, 53, 'Login', 'donor', '2026-10-26 22:43:37'),
(56, 54, 'Registered', 'ayanda.khumalo@test.co.za', '2026-10-26 22:45:57'),
(57, 54, 'Login', 'volunteer', '2026-10-26 22:46:13'),
(58, 54, 'Login', 'volunteer', '2026-10-26 23:06:59'),
(59, 52, 'Login', 'ngo_admin', '2026-10-26 23:07:24'),
(60, 52, 'NGO application submitted', 'khanyisa food relief', '2026-10-26 23:08:56'),
(61, 15, 'Login', 'admin', '2026-10-26 23:11:41'),
(62, 15, 'NGO rejected', 'Greenfield Youth Skills', '2026-10-26 23:15:03'),
(63, 15, 'NGO rejected', 'khanyisa food relief', '2026-10-26 23:15:11'),
(64, 15, 'NGO approved', 'khanyisa food relief', '2026-10-26 23:15:17'),
(65, 15, 'NGO rejected', 'Hope Counselling Centre', '2026-10-26 23:15:22'),
(66, 15, 'NGO rejected', 'Alex Shelter Network', '2026-10-26 23:15:25'),
(67, 15, 'NGO rejected', 'Soweto Food Hub', '2026-10-26 23:15:28'),
(68, 15, 'NGO approved', 'Greenfield Youth Skills', '2026-10-26 23:16:01'),
(69, 52, 'Login', 'ngo_admin', '2026-10-26 23:17:10'),
(70, 51, 'Login', 'beneficiary', '2026-10-26 23:19:35'),
(71, 51, 'Help request submitted', 'food, Soweto', '2026-10-26 23:21:31'),
(72, 52, 'Login', 'ngo_admin', '2026-10-26 23:24:00'),
(73, 52, 'Status update', 'match #45 to pending', '2026-10-26 23:25:02'),
(74, 52, 'Status update', 'match #45 to in_progress', '2026-10-26 23:25:30'),
(75, 52, 'Status update', 'match #45 to pending', '2026-10-26 23:26:11'),
(76, 52, 'Status update', 'match #45 to in_progress', '2026-10-26 23:26:17'),
(77, 51, 'Login', 'beneficiary', '2026-10-26 23:27:36'),
(78, 53, 'Login', 'donor', '2026-10-26 23:31:12'),
(79, 53, 'Pledge made', 'R600.00 to khanyisa food relief', '2026-10-26 23:32:02'),
(80, 53, 'Pledge made', 'R300.00 to khanyisa food relief', '2026-10-26 23:32:46'),
(81, 54, 'Login', 'volunteer', '2026-10-26 23:33:58'),
(82, 54, 'Volunteer signed up', 'Food parcel packing at khanyisa food relief', '2026-10-26 23:34:25'),
(83, 54, 'Login', 'volunteer', '2026-10-26 23:35:42'),
(84, 52, 'Login', 'ngo_admin', '2026-10-26 23:36:13'),
(85, 15, 'Login', 'admin', '2026-10-26 23:37:50'),
(86, 15, 'Automatic database backup', 'ubuntu_care_backup_2026-10-27_12-33-24_auto.sql', '2026-10-27 12:33:24'),
(87, 15, 'Login', 'admin', '2026-10-27 12:33:32'),
(88, 55, 'Registered', 'chantelmolewa1@gmail.com', '2026-10-27 14:47:52'),
(89, 55, 'Email verified', 'chantelmolewa1@gmail.com', '2026-10-27 14:48:55'),
(90, 55, 'Login', 'beneficiary', '2026-10-27 14:50:37'),
(91, 56, 'Registered', 'tshepang@gmail.com', '2026-10-27 14:56:13'),
(92, 55, 'Password reset requested', 'chantelmolewa1@gmail.com', '2026-10-27 15:20:43'),
(93, 55, 'Password reset', 'chantelmolewa1@gmail.com', '2026-10-27 15:21:58');

-- --------------------------------------------------------

--
-- Table structure for table `beneficiary_requests`
--

CREATE TABLE `beneficiary_requests` (
  `requestID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `area` varchar(100) DEFAULT NULL,
  `status` varchar(30) DEFAULT 'open',
  `date_created` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `beneficiary_requests`
--

INSERT INTO `beneficiary_requests` (`requestID`, `userID`, `description`, `category`, `area`, `status`, `date_created`) VALUES
(1, 9, 'I am hungry and my kids need food', 'food', 'Soweto', 'open', '2026-10-17 17:55:12'),
(2, 9, 'I need a lawyer for a court case', 'legal', 'Sandton', 'open', '2026-10-17 17:55:58'),
(3, 12, 'I am hungry and my kids need food.', 'food', 'Soweto', 'open', '2026-10-21 22:54:21'),
(4, 27, 'My family of five has had no food for three days. I need food parcels urgently.', 'food', 'Soweto', 'open', '2026-10-12 13:05:50'),
(5, 28, 'I was evicted last week and need a safe place to stay with my two children.', 'shelter', 'Alexandra', 'open', '2026-10-13 13:05:50'),
(6, 29, 'I need help to enrol for matric rewrite and cannot afford the study material.', 'education', 'Tembisa', 'open', '2026-10-14 13:05:50'),
(7, 30, 'My children need school uniforms and winter clothes before next term.', 'clothing', 'Mamelodi', 'open', '2026-10-15 13:05:50'),
(8, 31, 'I need a doctor and cannot pay for chronic medication this month.', 'medical', 'Sandton', 'open', '2026-10-16 13:05:50'),
(9, 32, 'I have been feeling very anxious since losing my job and need someone to talk to.', 'counselling', 'Diepsloot', 'open', '2026-10-17 13:05:50'),
(10, 33, 'I am struggling to feed my elderly mother and need regular meals.', 'food', 'Katlehong', 'open', '2026-10-18 13:05:50'),
(11, 34, 'My employer has not paid me and I need legal advice on my rights.', 'legal', 'Pretoria', 'open', '2026-10-19 13:05:50'),
(12, 35, 'I need food for my family until my grant is paid.', 'food', 'Soweto', 'open', '2026-10-20 13:05:50'),
(13, 36, 'I have nowhere to sleep and need emergency shelter tonight.', 'shelter', 'Alexandra', 'open', '2026-10-21 13:05:50'),
(14, 27, 'I need counselling after a family loss.', 'counselling', 'Soweto', 'open', '2026-10-22 13:05:50'),
(15, 28, 'My son needs a medical check-up and I have no medical aid.', 'medical', 'Alexandra', 'open', '2026-10-23 13:05:50'),
(16, 29, 'I need blankets and warm clothes for my family this winter.', 'clothing', 'Tembisa', 'open', '2026-10-24 13:05:50'),
(17, 30, 'I need help understanding my rental agreement before I sign it.', 'legal', 'Mamelodi', 'open', '2026-10-24 13:05:50'),
(18, 31, 'I want to learn computer skills so I can find work.', 'education', 'Sandton', 'open', '2026-10-25 13:05:50'),
(19, 27, 'We were thrown out of our flat and sleep in the car', 'shelter', 'Alexandra', 'open', '2026-10-26 18:23:04'),
(20, 27, 'My child does not have school uniform', 'clothing', 'Tembisa', 'open', '2026-10-26 18:29:53'),
(21, 27, 'My daughter has a fever, and we cannot afford the doctor', 'medical', 'Midrand', 'open', '2026-10-26 18:34:27'),
(22, 27, 'i dont  have  a place to stay', 'shelter', 'Katlehong', 'open', '2026-10-26 18:37:10'),
(23, 51, 'My children have not  eaten for the past days and i do not have money for groceries ..', 'food', 'Soweto', 'open', '2026-10-26 23:21:31');

-- --------------------------------------------------------

--
-- Table structure for table `donor_pledges`
--

CREATE TABLE `donor_pledges` (
  `pledgeID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `ngoID` int(11) NOT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `date_pledged` datetime DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'pledged'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donor_pledges`
--

INSERT INTO `donor_pledges` (`pledgeID`, `userID`, `ngoID`, `amount`, `date_pledged`, `status`) VALUES
(1, 10, 3, 500.00, '2026-10-17 18:06:29', 'pledged'),
(2, 37, 5, 1500.00, '2026-10-14 13:05:50', 'received'),
(3, 37, 7, 2500.00, '2026-10-17 13:05:50', 'received'),
(4, 38, 8, 1000.00, '2026-10-23 13:05:50', 'pledged'),
(5, 38, 9, 750.00, '2026-10-18 13:05:50', 'received'),
(6, 39, 6, 3000.00, '2026-10-15 13:05:50', 'received'),
(7, 39, 10, 500.00, '2026-10-24 13:05:50', 'pledged'),
(8, 40, 11, 1200.00, '2026-10-19 13:05:50', 'received'),
(9, 40, 5, 800.00, '2026-10-25 13:05:50', 'pledged'),
(10, 41, 6, 2000.00, '2026-10-22 13:05:50', 'pledged'),
(11, 42, 7, 5000.00, '2026-10-16 13:05:50', 'received'),
(12, 42, 9, 1800.00, '2026-10-21 13:05:50', 'pledged'),
(13, 37, 4, 250.00, '2026-10-26 13:33:34', 'pledged'),
(14, 53, 14, 600.00, '2026-10-26 23:32:02', 'pledged'),
(15, 53, 14, 300.00, '2026-10-26 23:32:46', 'pledged');

-- --------------------------------------------------------

--
-- Table structure for table `email_codes`
--

CREATE TABLE `email_codes` (
  `codeID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `purpose` varchar(20) NOT NULL,
  `code_hash` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `attempts` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `email_codes`
--

INSERT INTO `email_codes` (`codeID`, `userID`, `purpose`, `code_hash`, `expires_at`, `attempts`, `created_at`) VALUES
(2, 56, 'verify', 'aff860d7b470f48b149075a5974f3e20c000be263fbf50cb3b998e3fb1214d0c', '2026-10-27 15:11:13', 0, '2026-10-27 14:56:13');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `attemptID` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `attempted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`attemptID`, `email`, `ip`, `attempted_at`) VALUES
(8, 'kabelo.mashabo@demo.ubuntucare.co.za', '::1', '2026-10-26 19:08:02');

-- --------------------------------------------------------

--
-- Table structure for table `match_history`
--

CREATE TABLE `match_history` (
  `matchID` int(11) NOT NULL,
  `requestID` int(11) NOT NULL,
  `ngoID` int(11) NOT NULL,
  `match_score` decimal(5,2) DEFAULT NULL,
  `date_matched` datetime DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `match_history`
--

INSERT INTO `match_history` (`matchID`, `requestID`, `ngoID`, `match_score`, `date_matched`, `status`) VALUES
(1, 1, 3, 100.00, '2026-10-17 17:55:12', 'pending'),
(2, 1, 2, 16.69, '2026-10-17 17:55:12', 'pending'),
(3, 1, 1, 9.96, '2026-10-17 17:55:12', 'pending'),
(4, 2, 2, 100.00, '2026-10-17 17:55:58', 'pending'),
(5, 2, 1, 23.04, '2026-10-17 17:55:58', 'pending'),
(6, 2, 3, 16.69, '2026-10-17 17:55:58', 'pending'),
(7, 3, 3, 100.00, '2026-10-21 22:54:21', 'pending'),
(8, 3, 2, 16.69, '2026-10-21 22:54:21', 'pending'),
(9, 3, 1, 9.96, '2026-10-21 22:54:21', 'pending'),
(10, 4, 5, 96.00, '2026-10-12 13:05:50', 'completed'),
(11, 5, 6, 94.00, '2026-10-13 13:05:50', 'in_progress'),
(12, 6, 9, 92.00, '2026-10-14 13:05:50', 'accepted'),
(13, 7, 10, 95.00, '2026-10-15 13:05:50', 'completed'),
(14, 8, 7, 97.00, '2026-10-16 13:05:50', 'in_progress'),
(15, 9, 11, 93.00, '2026-10-17 13:05:50', 'accepted'),
(16, 10, 5, 71.00, '2026-10-18 13:05:50', 'pending'),
(17, 11, 8, 96.00, '2026-10-19 13:05:50', 'accepted'),
(18, 12, 5, 98.00, '2026-10-20 13:05:50', 'pending'),
(19, 13, 6, 95.00, '2026-10-21 13:05:50', 'in_progress'),
(20, 14, 11, 68.00, '2026-10-22 13:05:50', 'declined'),
(21, 15, 7, 84.00, '2026-10-23 13:05:50', 'pending'),
(22, 16, 10, 72.00, '2026-10-24 13:05:50', 'pending'),
(23, 17, 8, 81.00, '2026-10-24 13:05:50', 'accepted'),
(24, 18, 9, 64.00, '2026-10-25 13:05:50', 'pending'),
(25, 19, 6, 99.77, '2026-10-26 18:23:05', 'pending'),
(26, 19, 2, 27.92, '2026-10-26 18:23:05', 'pending'),
(27, 19, 7, 27.92, '2026-10-26 18:23:05', 'pending'),
(28, 19, 1, 24.04, '2026-10-26 18:23:05', 'pending'),
(29, 19, 4, 24.04, '2026-10-26 18:23:05', 'pending'),
(30, 20, 10, 82.40, '2026-10-26 18:29:53', 'pending'),
(31, 20, 9, 29.97, '2026-10-26 18:29:53', 'pending'),
(32, 20, 1, 24.98, '2026-10-26 18:29:53', 'pending'),
(33, 20, 4, 24.98, '2026-10-26 18:29:53', 'pending'),
(34, 20, 6, 21.08, '2026-10-26 18:29:53', 'pending'),
(35, 21, 7, 93.04, '2026-10-26 18:34:27', 'pending'),
(36, 21, 1, 30.00, '2026-10-26 18:34:27', 'pending'),
(37, 21, 4, 30.00, '2026-10-26 18:34:27', 'pending'),
(38, 21, 9, 24.98, '2026-10-26 18:34:27', 'pending'),
(39, 21, 6, 23.82, '2026-10-26 18:34:27', 'pending'),
(40, 22, 6, 87.18, '2026-10-26 18:37:10', 'pending'),
(41, 22, 12, 29.40, '2026-10-26 18:37:10', 'pending'),
(42, 22, 2, 16.62, '2026-10-26 18:37:10', 'pending'),
(43, 22, 7, 16.62, '2026-10-26 18:37:10', 'pending'),
(44, 22, 3, 15.02, '2026-10-26 18:37:10', 'pending'),
(45, 23, 14, 100.00, '2026-10-26 23:21:31', 'in_progress'),
(46, 23, 5, 98.90, '2026-10-26 23:21:31', 'pending'),
(47, 23, 12, 84.77, '2026-10-26 23:21:31', 'pending'),
(48, 23, 4, 79.96, '2026-10-26 23:21:31', 'pending'),
(49, 23, 2, 16.69, '2026-10-26 23:21:31', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `ngo_profiles`
--

CREATE TABLE `ngo_profiles` (
  `ngoID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `org_name` varchar(150) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `area` varchar(100) DEFAULT NULL,
  `latitude` decimal(9,6) DEFAULT NULL,
  `longitude` decimal(9,6) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ngo_profiles`
--

INSERT INTO `ngo_profiles` (`ngoID`, `userID`, `org_name`, `category`, `area`, `latitude`, `longitude`, `status`) VALUES
(1, 5, 'Aretshwaraneng', 'education', 'Midrand', -25.999200, 28.126300, 'approved'),
(2, 6, 'Sandon Legal Aid', 'legal', 'Sandton', -26.107600, 28.056700, 'approved'),
(3, 8, 'Soweto Food Hub', 'food', 'Soweto', -26.267800, 27.858500, 'rejected'),
(4, 16, 'Soweto Care Kitchen', 'food', 'Midrand', -25.999200, 28.126300, 'approved'),
(5, 18, 'Soweto Food Hamper Project', 'food', 'Soweto', -26.248500, 27.854000, 'approved'),
(6, 19, 'Alex Shelter Network', 'shelter', 'Alexandra', -26.107500, 28.098000, 'rejected'),
(7, 20, 'Sandton Community Clinic', 'medical', 'Sandton', -26.107600, 28.056700, 'approved'),
(8, 21, 'Ubuntu Legal Aid Desk', 'legal', 'Pretoria', -25.747900, 28.229300, 'approved'),
(9, 22, 'Tembisa Learning Hub', 'education', 'Tembisa', -25.997000, 28.226800, 'approved'),
(10, 23, 'Mamelodi Clothing Bank', 'clothing', 'Mamelodi', -25.706900, 28.369300, 'approved'),
(11, 24, 'Hope Counselling Centre', 'counselling', 'Diepsloot', -25.930000, 28.010000, 'rejected'),
(12, 25, 'Katlehong Meals on Wheels', 'food', 'Katlehong', -26.344000, 28.152000, 'approved'),
(13, 26, 'Greenfield Youth Skills', 'education', 'Pretoria', -25.750000, 28.200000, 'approved'),
(14, 52, 'khanyisa food relief', 'food', 'Soweto', -26.267800, 27.858500, 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notifID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `body` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notifID`, `userID`, `title`, `body`, `is_read`, `created_at`) VALUES
(1, 16, 'Your organisation was approved', 'You now appear in matching and can receive pledges and volunteers.', 1, '2026-10-26 01:01:26'),
(2, 27, 'Request completed', 'Soweto Food Hamper Project marked your request as completed.', 1, '2026-10-25 13:05:50'),
(3, 28, 'Match update', 'Alex Shelter Network is now working on your request.', 1, '2026-10-25 13:05:50'),
(4, 31, 'Match update', 'Sandton Community Clinic is now working on your request.', 1, '2026-10-25 13:05:50'),
(5, 18, 'New match', 'A new help request matches your organisation.', 1, '2026-10-25 13:05:50'),
(6, 19, 'New match', 'A new help request matches your organisation.', 0, '2026-10-25 13:05:50'),
(7, 25, 'Profile under review', 'Your organisation is waiting for admin approval.', 1, '2026-10-25 13:05:50'),
(8, 37, 'Pledge received', 'Soweto Food Hamper Project confirmed your R1500 pledge.', 1, '2026-10-25 13:05:50'),
(9, 25, 'Your organisation was approved', 'You now appear in matching and can receive pledges and volunteers.', 1, '2026-10-26 13:08:54'),
(10, 35, 'Soweto Food Hamper Project updated your status', 'New status: Awaiting response', 0, '2026-10-26 13:25:35'),
(11, 16, 'New pledge received', 'John Smith pledged R250.00 to Soweto Care Kitchen.', 0, '2026-10-26 13:33:34'),
(12, 19, 'New matched request', 'A new request in Alexandra matches your organisation (99.77% match).', 0, '2026-10-26 18:23:05'),
(13, 6, 'New matched request', 'A new request in Alexandra matches your organisation (27.92% match).', 0, '2026-10-26 18:23:05'),
(14, 20, 'New matched request', 'A new request in Alexandra matches your organisation (27.92% match).', 0, '2026-10-26 18:23:05'),
(15, 5, 'New matched request', 'A new request in Alexandra matches your organisation (24.04% match).', 0, '2026-10-26 18:23:05'),
(16, 16, 'New matched request', 'A new request in Alexandra matches your organisation (24.04% match).', 0, '2026-10-26 18:23:05'),
(17, 23, 'New matched request', 'A new request in Tembisa matches your organisation (82.4% match).', 0, '2026-10-26 18:29:53'),
(18, 22, 'New matched request', 'A new request in Tembisa matches your organisation (29.97% match).', 0, '2026-10-26 18:29:53'),
(19, 5, 'New matched request', 'A new request in Tembisa matches your organisation (24.98% match).', 0, '2026-10-26 18:29:53'),
(20, 16, 'New matched request', 'A new request in Tembisa matches your organisation (24.98% match).', 0, '2026-10-26 18:29:53'),
(21, 19, 'New matched request', 'A new request in Tembisa matches your organisation (21.08% match).', 0, '2026-10-26 18:29:53'),
(22, 20, 'New matched request', 'A new request in Midrand matches your organisation (93.04% match).', 0, '2026-10-26 18:34:27'),
(23, 5, 'New matched request', 'A new request in Midrand matches your organisation (30% match).', 0, '2026-10-26 18:34:27'),
(24, 16, 'New matched request', 'A new request in Midrand matches your organisation (30% match).', 0, '2026-10-26 18:34:27'),
(25, 22, 'New matched request', 'A new request in Midrand matches your organisation (24.98% match).', 0, '2026-10-26 18:34:27'),
(26, 19, 'New matched request', 'A new request in Midrand matches your organisation (23.82% match).', 0, '2026-10-26 18:34:27'),
(27, 19, 'New matched request', 'A new request in Katlehong matches your organisation (87.18% match).', 0, '2026-10-26 18:37:10'),
(28, 25, 'New matched request', 'A new request in Katlehong matches your organisation (29.4% match).', 0, '2026-10-26 18:37:10'),
(29, 6, 'New matched request', 'A new request in Katlehong matches your organisation (16.62% match).', 0, '2026-10-26 18:37:10'),
(30, 20, 'New matched request', 'A new request in Katlehong matches your organisation (16.62% match).', 0, '2026-10-26 18:37:10'),
(31, 8, 'New matched request', 'A new request in Katlehong matches your organisation (15.02% match).', 0, '2026-10-26 18:37:10'),
(32, 26, 'Your organisation was rejected', 'Please review your details or contact the platform admin.', 0, '2026-10-26 23:15:03'),
(33, 52, 'Your organisation was rejected', 'Please review your details or contact the platform admin.', 1, '2026-10-26 23:15:11'),
(34, 52, 'Your organisation was approved', 'You now appear in matching and can receive pledges and volunteers.', 1, '2026-10-26 23:15:17'),
(35, 24, 'Your organisation was rejected', 'Please review your details or contact the platform admin.', 0, '2026-10-26 23:15:22'),
(36, 19, 'Your organisation was rejected', 'Please review your details or contact the platform admin.', 0, '2026-10-26 23:15:25'),
(37, 8, 'Your organisation was rejected', 'Please review your details or contact the platform admin.', 0, '2026-10-26 23:15:28'),
(38, 26, 'Your organisation was approved', 'You now appear in matching and can receive pledges and volunteers.', 0, '2026-10-26 23:16:01'),
(39, 52, 'New matched request', 'A new request in Soweto matches your organisation (100% match).', 1, '2026-10-26 23:21:31'),
(40, 18, 'New matched request', 'A new request in Soweto matches your organisation (98.9% match).', 0, '2026-10-26 23:21:31'),
(41, 25, 'New matched request', 'A new request in Soweto matches your organisation (84.77% match).', 0, '2026-10-26 23:21:31'),
(42, 16, 'New matched request', 'A new request in Soweto matches your organisation (79.96% match).', 0, '2026-10-26 23:21:31'),
(43, 6, 'New matched request', 'A new request in Soweto matches your organisation (16.69% match).', 0, '2026-10-26 23:21:31'),
(44, 51, 'khanyisa food relief updated your status', 'New status: Awaiting response', 1, '2026-10-26 23:25:02'),
(45, 51, 'khanyisa food relief updated your status', 'New status: In progress', 1, '2026-10-26 23:25:30'),
(46, 51, 'khanyisa food relief updated your status', 'New status: Awaiting response', 1, '2026-10-26 23:26:11'),
(47, 51, 'khanyisa food relief updated your status', 'New status: In progress', 1, '2026-10-26 23:26:17'),
(48, 52, 'New pledge received', 'Michael Naidoo pledged R600.00 to khanyisa food relief.', 1, '2026-10-26 23:32:02'),
(49, 52, 'New pledge received', 'Michael Naidoo pledged R300.00 to khanyisa food relief.', 1, '2026-10-26 23:32:46'),
(50, 52, 'New volunteer', 'Ayanda Khumalo signed up to help with: Food parcel packing.', 1, '2026-10-26 23:34:25');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `resetID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `token_hash` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`resetID`, `userID`, `token_hash`, `expires_at`, `used`) VALUES
(1, 14, '1200bcc829cb8a331d376b7ccae8a66926e1cf98988c8284f451bc9bbea3c602', '2026-10-26 03:30:50', 1);

-- --------------------------------------------------------

--
-- Table structure for table `remember_tokens`
--

CREATE TABLE `remember_tokens` (
  `tokenID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `selector` varchar(32) NOT NULL,
  `token_hash` char(64) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `remember_tokens`
--

INSERT INTO `remember_tokens` (`tokenID`, `userID`, `selector`, `token_hash`, `expires_at`) VALUES
(1, 15, '45eada2076d74fbd', '5d6b1d41d69007908fc80f81f859298a1f090ac547d5c11baf5f7dfa4f716cf8', '2026-11-24 23:47:08'),
(2, 16, '17324e84ca74660c', '8823dd547a165b368e974ad4f86d7024f050f414a11f0ccda08c8e4a61427d92', '2026-11-24 23:57:44'),
(3, 15, '9252e7065db84e54', 'cc6fc780cd214c33d00bf91e641c968f6c8d568485105e6e42b230c5a7788573', '2026-11-25 00:00:55'),
(4, 16, '36cdb3f384e86757', '952fa30fc398b6b8356a03c5b14c4a79c1bb0318f150354b3a62d01e864c9df1', '2026-11-25 00:02:43'),
(13, 27, '3e14a76e1f48c481', '617a30ffd90186a1a55b6a6491dc532580cd4e50a1fd8c255d40a29623be7ca2', '2026-11-25 12:13:32'),
(26, 54, '9db081d717605a80', '3b7301014d14e853f00291215807b1017442d613dfea02a3ff64a9a8f7a527ce', '2026-11-25 21:46:13');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `roleID` int(11) NOT NULL,
  `role_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`roleID`, `role_name`) VALUES
(5, 'admin'),
(1, 'beneficiary'),
(3, 'donor'),
(2, 'ngo_admin'),
(4, 'volunteer');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `roleID` int(11) NOT NULL,
  `location` varchar(150) DEFAULT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `name`, `email`, `password_hash`, `roleID`, `location`, `email_verified`) VALUES
(1, 'chantal', 'tiro1@gmail.com', '$2y$10$WE3.G7Vc7qrnmwNmmir9TeUSTMYFAHDzhyt.WN8daaomhmuzJLPdC', 4, 'Midrand', 1),
(2, 'tshepang molewa', 'tshepang12@gmail.com', '$2y$10$RtSf4IzbSZRyHVTTJsk/2udBsTpAhvxEGqPFAiu0XLcRgNqE/8X4i', 1, 'MIDRAND', 1),
(3, 'Tshiamo', 'tshiamothuso24@gmail.com', '$2y$10$1IG2a7UwssP3JlsKF7j2ueAw7KrTaqbqNifvCqVqQAGmqSVNi3vsi', 1, 'MIDRAND', 1),
(4, 'test2', 'test2@gmail.com', '$2y$10$UOq3R80d9JHzvSc7NAtkFePRA3thqnHPtMIXujpTaTRvlLRbw3XUG', 2, 'soweto', 1),
(5, 'test3', 'test3@gmail.com', '$2y$10$MV/cH85ZwdX4VsY70Fettu75RJcEe7k6xXWJYKWjRnObXDvxrGfd.', 2, 'Ivory Park', 1),
(6, 'Santon Legal Aid', 'ngo2@test.com', '$2y$10$zhKHPNutGmBdyY8sg/w0P.4aF5M.Bazln3BzdJni13wxDG7bKCzLC', 2, 'Sandton', 1),
(7, 'Tembisa Clinic Care', 'ngo3@test.com', '$2y$10$VTH4r7yny2xxgwBxowF.K.oPaGymqvUfyP56E.Qv.nO6PHp.c6MLm', 3, 'tembisa', 1),
(8, 'Soweto Food Hub', 'ngo4@test.com', '$2y$10$oxtp2D5tREJ1Sb77LI.DaOXkNnENoPtvutQgz17.kzynihI5vhTuS', 2, 'Soweto', 1),
(9, 'Ben', 'ben1@test.com', '$2y$10$gbZD2lXfEjB7pXXcm8MvwO8QxH0ke9Dlzuj3MVgt3K.d17PBBfPBq', 1, 'soweto', 1),
(10, 'donor1', 'donor1@test.com', '$2y$10$bgHMvgr/KgZRyzQmIhHLdee7uOKa70/dsKUAtkQGfQ1qGOSYOZgCC', 3, 'soweto', 1),
(11, 'volunteer', 'vol1@test.com', '$2y$10$b.qePqqOJ86eqZ1KgDNtWu/WURYUOv42lTFIPVnS3iXfamvIL5ose', 4, 'MIDRAND', 1),
(12, 'Tshiamo', 'Tshia1@test.com', '$2y$10$SXxwlgZqLCyAKLAfkpt2/OGAHlPdsCoprnlsFYkPV0lT7PIcPpsBm', 1, 'Soweto', 1),
(13, 'kenisha', 'kenisha@123.com', '$2y$10$/aBftvlAzZjUs8u.uL..TOlVG18k9EM50xklvvBXEw15bkZUdygQu', 2, 'Ivory Park, Midrand', 1),
(14, 'kiki', 'kiki1@gmail.com', '$2y$10$hdRHE6ZuQvJPAdpuYJAK8.t3Glb2hwAtNBwtEnLiNQrmHC7Ca7nTq', 3, 'Sandton', 1),
(15, 'Platform Admin', 'admin@ubuntucare.co.za', '$2y$10$39R4FvA8WuczibT3dFmbP.WIre3TDI1XiMDzuAjuQ51EZSEXUS3dW', 5, 'Johannesburg', 1),
(16, 'Thato Sedulawesi', 'Thato@sedulawesi123.co.za', '$2y$10$qg/MxSSVrIQSRmmz1a/.JeI/NAK6XZeQAhVLwBfIVJjRRM43/rCya', 2, 'midrand', 1),
(17, 'Phumzile Zikode', 'phumzile31@zikode.co.za', '$2y$10$WF.HrLYlr6nn5Z2rKIY6e.IW6wXti7FOaTM5Y6W3YvjxAbl9po2xO', 1, 'Sandton', 1),
(18, 'Thandi Mokoena', 'thandi.mokoena@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 2, 'Soweto', 1),
(19, 'Sipho Dlamini', 'sipho.dlamini@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 2, 'Alexandra', 1),
(20, 'Dr Naledi Khumalo', 'dr.naledi.khumalo@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 2, 'Sandton', 1),
(21, 'Adv. Pieter van der Merwe', 'adv.pieter.vdm@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 2, 'Pretoria', 1),
(22, 'Lerato Nkosi', 'lerato.nkosi@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 2, 'Tembisa', 1),
(23, 'Zanele Mahlangu', 'zanele.mahlangu@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 2, 'Mamelodi', 1),
(24, 'Mpho Sithole', 'ps.mpho.sithole@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 2, 'Diepsloot', 1),
(25, 'Bongani Zulu', 'bongani.zulu@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 2, 'Katlehong', 1),
(26, 'Ayesha Patel', 'ayesha.patel@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 2, 'Pretoria', 1),
(27, 'Nomsa Cele', 'nomsa.cele@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 1, 'Soweto', 1),
(28, 'Tebogo Maseko', 'tebogo.maseko@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 1, 'Alexandra', 1),
(29, 'Kagiso Molefe', 'kagiso.molefe@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 1, 'Tembisa', 1),
(30, 'Palesa Ndlovu', 'palesa.ndlovu@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 1, 'Mamelodi', 1),
(31, 'Lwazi Khoza', 'lwazi.khoza@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 1, 'Sandton', 1),
(32, 'Refilwe Tau', 'refilwe.tau@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 1, 'Diepsloot', 1),
(33, 'Thabo Radebe', 'thabo.radebe@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 1, 'Katlehong', 1),
(34, 'Amahle Zwane', 'amahle.zwane@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 1, 'Pretoria', 1),
(35, 'Sibusiso Mthembu', 'sibusiso.mthembu@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 1, 'Soweto', 1),
(36, 'Karabo Letsoalo', 'karabo.letsoalo@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 1, 'Alexandra', 1),
(37, 'John Smith', 'john.smith@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 3, 'Sandton', 1),
(38, 'Fatima Moosa', 'fatima.moosa@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 3, 'Pretoria', 1),
(39, 'Andile Ngcobo', 'andile.ngcobo@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 3, 'Johannesburg', 1),
(40, 'Megan Botha', 'megan.botha@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 3, 'Randburg', 1),
(41, 'Kabelo Mashaba', 'kabelo.mashaba@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 3, 'Soweto', 1),
(42, 'Priya Naidoo', 'priya.naidoo@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 3, 'Sandton', 1),
(43, 'Naledi Pillay', 'naledi.pillay@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 4, 'Soweto', 1),
(44, 'Themba Shabalala', 'themba.shabalala@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 4, 'Alexandra', 1),
(45, 'Chloe Jacobs', 'chloe.jacobs@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 4, 'Sandton', 1),
(46, 'Musa Hadebe', 'musa.hadebe@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 4, 'Tembisa', 1),
(47, 'Olwethu Sibiya', 'olwethu.sibiya@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 4, 'Pretoria', 1),
(48, 'Rudo Chikwanha', 'rudo.chikwanha@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 4, 'Mamelodi', 1),
(49, 'Zinhle Dube', 'zinhle.dube@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 4, 'Diepsloot', 1),
(50, 'Liam O\'Connor', 'liam.oconnor@demo.ubuntucare.co.za', '$2y$10$IY6lCFVtc0GMPjDzzrgnn.QqJ1BdLqkvgqjPluh1Dwund9yVY3joS', 4, 'Randburg', 1),
(51, 'Lindiwe Dube', 'lindiwe.dube@test.co.za', '$2y$10$heANSInpCGJWCpMYs5tUdub7SXqbaESGwY0f2cF.dCHb.Y2i7xjlW', 1, 'Soweto', 1),
(52, 'Khanyisa', 'khanyisa@test.co.za', '$2y$10$L10IJvAQSmSZAf78UI0FiuEOaV7z2afIYA7CqEykslUTTbs0h77Wu', 2, 'Soweto', 1),
(53, 'Michael Naidoo', 'michael.naidoo@test.co.za', '$2y$10$5/ZrgVH88jM/XPvdRTH4KuM/BxJhaDooExPE09q5wXdWB3PLT9RxO', 3, 'Sandton', 1),
(54, 'Ayanda Khumalo', 'ayanda.khumalo@test.co.za', '$2y$10$aRmZ7lCOtgo4Dlmq1UBQUOH2J/BoTwUJmMe.jz7YiuUa9kbTAkQOi', 4, 'Soweto', 1),
(55, 'Chantel Molewa', 'chantelmolewa1@gmail.com', '$2y$10$AJ.0JeAVu3sr1rZSctZSZeqWi45N4NqMWoZjhs3YcCcyDgf225EWi', 1, 'Tembisa', 1),
(56, 'Tshepang', 'tshepang@gmail.com', '$2y$10$qDunENv.RXRR8wgnthoTJO4evX9X.RB./ySJUpMrUEC6IgMGUzehK', 4, 'Sandton', 0);

-- --------------------------------------------------------

--
-- Table structure for table `volunteer_signups`
--

CREATE TABLE `volunteer_signups` (
  `signupID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `ngoID` int(11) NOT NULL,
  `task` varchar(150) DEFAULT NULL,
  `status` varchar(30) DEFAULT 'signed_up'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `volunteer_signups`
--

INSERT INTO `volunteer_signups` (`signupID`, `userID`, `ngoID`, `task`, `status`) VALUES
(1, 11, 2, 'Clinic support', 'signed_up'),
(2, 43, 5, 'Pack and hand out food parcels on Saturdays', 'completed'),
(3, 44, 6, 'Help families settle in and assist with intake', 'confirmed'),
(4, 45, 7, 'Reception and patient queue support', 'confirmed'),
(5, 46, 9, 'Weekend maths tutoring for matric learners', 'signed_up'),
(6, 47, 8, 'Admin support and document filing', 'signed_up'),
(7, 48, 10, 'Sort and fold donated clothing', 'completed'),
(8, 49, 11, 'Welcome visitors and run the tea station', 'confirmed'),
(9, 50, 5, 'Drive the delivery bakkie on Fridays', 'signed_up'),
(10, 43, 6, 'Evening meal service', 'confirmed'),
(11, 44, 9, 'Library helper', 'signed_up'),
(12, 54, 14, 'Food parcel packing', 'signed_up');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`logID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `beneficiary_requests`
--
ALTER TABLE `beneficiary_requests`
  ADD PRIMARY KEY (`requestID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `donor_pledges`
--
ALTER TABLE `donor_pledges`
  ADD PRIMARY KEY (`pledgeID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `ngoID` (`ngoID`);

--
-- Indexes for table `email_codes`
--
ALTER TABLE `email_codes`
  ADD PRIMARY KEY (`codeID`),
  ADD KEY `userID` (`userID`,`purpose`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`attemptID`),
  ADD KEY `email` (`email`,`attempted_at`);

--
-- Indexes for table `match_history`
--
ALTER TABLE `match_history`
  ADD PRIMARY KEY (`matchID`),
  ADD KEY `requestID` (`requestID`),
  ADD KEY `ngoID` (`ngoID`);

--
-- Indexes for table `ngo_profiles`
--
ALTER TABLE `ngo_profiles`
  ADD PRIMARY KEY (`ngoID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notifID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`resetID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD PRIMARY KEY (`tokenID`),
  ADD UNIQUE KEY `selector` (`selector`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`roleID`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `roleID` (`roleID`);

--
-- Indexes for table `volunteer_signups`
--
ALTER TABLE `volunteer_signups`
  ADD PRIMARY KEY (`signupID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `ngoID` (`ngoID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `logID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `beneficiary_requests`
--
ALTER TABLE `beneficiary_requests`
  MODIFY `requestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `donor_pledges`
--
ALTER TABLE `donor_pledges`
  MODIFY `pledgeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `email_codes`
--
ALTER TABLE `email_codes`
  MODIFY `codeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `attemptID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `match_history`
--
ALTER TABLE `match_history`
  MODIFY `matchID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `ngo_profiles`
--
ALTER TABLE `ngo_profiles`
  MODIFY `ngoID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notifID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `resetID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `tokenID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `roleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `volunteer_signups`
--
ALTER TABLE `volunteer_signups`
  MODIFY `signupID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE SET NULL;

--
-- Constraints for table `beneficiary_requests`
--
ALTER TABLE `beneficiary_requests`
  ADD CONSTRAINT `beneficiary_requests_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`);

--
-- Constraints for table `donor_pledges`
--
ALTER TABLE `donor_pledges`
  ADD CONSTRAINT `donor_pledges_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`),
  ADD CONSTRAINT `donor_pledges_ibfk_2` FOREIGN KEY (`ngoID`) REFERENCES `ngo_profiles` (`ngoID`);

--
-- Constraints for table `match_history`
--
ALTER TABLE `match_history`
  ADD CONSTRAINT `match_history_ibfk_1` FOREIGN KEY (`requestID`) REFERENCES `beneficiary_requests` (`requestID`),
  ADD CONSTRAINT `match_history_ibfk_2` FOREIGN KEY (`ngoID`) REFERENCES `ngo_profiles` (`ngoID`);

--
-- Constraints for table `ngo_profiles`
--
ALTER TABLE `ngo_profiles`
  ADD CONSTRAINT `ngo_profiles_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD CONSTRAINT `remember_tokens_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`roleID`) REFERENCES `roles` (`roleID`);

--
-- Constraints for table `volunteer_signups`
--
ALTER TABLE `volunteer_signups`
  ADD CONSTRAINT `volunteer_signups_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`),
  ADD CONSTRAINT `volunteer_signups_ibfk_2` FOREIGN KEY (`ngoID`) REFERENCES `ngo_profiles` (`ngoID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
