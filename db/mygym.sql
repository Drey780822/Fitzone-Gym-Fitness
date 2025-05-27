-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: APRIL 08, 2025 at 23:12 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30
--
-- Database: `mygym`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(10) NOT NULL AUTO_INCREMENT,
  `admin_email` varchar(30) NOT NULL,
  `admin_pass` varchar(255) NOT NULL,  -- Adjusted length for hashed password
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--
-- --------------------------------------------------------
--
-- Table structure for table `days`
--

CREATE TABLE `days` (
  `day_id` int(10) NOT NULL,
  `day_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `days`
--

INSERT INTO `days` (`day_id`, `day_name`) VALUES
(1, 'Sunday'),
(2, 'Monday'),
(3, 'Tuesday'),
(4, 'Wednesday'),
(5, 'Thursday'),
(6, 'Friday'),
(7, 'Saturday');

-- --------------------------------------------------------

--
-- Table structure for table `exercises`
--

CREATE TABLE `exercises` (
  `exer_id` int(10) NOT NULL,
  `exer_name` varchar(20) NOT NULL,
  `sets` varchar(10) NOT NULL,
  `day_id` int(10) NOT NULL,
  `exer_img` text NOT NULL,
  `user_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `exercises`
--

INSERT INTO `exercises` (`exer_id`, `exer_name`, `sets`, `day_id`, `exer_img`, `user_id`) VALUES
(20, 'Barbell hip thrust', '10', 1, 'Barbell hip thrust.jpg', 3),
(21, 'Bench Press', '15', 2, 'Bench Press.jpg', 3),
(22, 'Deadlift', '20', 3, 'Deadlift.jpg', 3),
(23, 'Dumbbell romanian de', '25', 4, 'Dumbbell romanian deadlift.jpg', 3),
(24, 'Farmer Walk', '20', 5, 'Farmer Walk.jpg', 3),
(25, 'Hamstring curl', '15', 6, 'Hamstring curl.jpg', 3),
(26, 'Pullup', '10', 6, 'Pullup.jpg', 3),
(27, 'Suspended inverted r', '14', 7, 'Suspended inverted row.jpg', 3);

-- --------------------------------------------------------

--
-- Table structure for table `trainer`
--

CREATE TABLE `trainer` (
  `tran_id` int(10) NOT NULL,
  `tran_name` varchar(20) NOT NULL,
  `tran_class` varchar(30) NOT NULL,
  `tran_contact` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `trainer`
--

INSERT INTO `trainer` (`tran_id`, `tran_name`, `tran_class`, `tran_contact`) VALUES
(1, 'Samsons', 'Barbell hip thrust', '00000123'),
(2, 'BIG', 'Bench Press', '11111'),
(3, 'Awa', 'Deadlift', '22222'),
(4, 'Bounce', 'Dumbbell romanian de', '3333300'),
(5, 'Ahsan', 'Farmer Walk', '44444'),
(6, 'Punisher', 'Hamstring curl', '55555'),
(7, 'Ghost', 'Pullup', '6660'),
(8, 'Talha', 'Suspended inverted r', '66666'),
(9, 'train1', 'Deadlift', '6660'),
(10, 'New', 'Suspended inverted r', '0720000000');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) NOT NULL,
  `user_name` varchar(20) NOT NULL,
  `user_email` varchar(30) NOT NULL,
  `user_pass` varchar(20) NOT NULL,
  `user_weight` int(10) NOT NULL,
  `user_age` int(10) NOT NULL,
  `user_contact` varchar(20) NOT NULL,
  `package` varchar(100) NOT NULL,
  `user_surname` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

