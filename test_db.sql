-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2023 at 07:02 AM
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
-- Database: `test_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `message` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `date`, `message`) VALUES
(1, '2023-11-08 03:16:22', 'test'),
(0, '2023-11-08 10:28:10', 'hey'),
(0, '2023-11-08 11:22:59', 'what\'s up'),
(0, '2023-11-08 11:27:30', 'hey'),
(0, '2023-11-08 12:09:57', 'hey'),
(0, '2023-11-08 12:25:37', 'hey'),
(0, '2023-11-08 12:28:50', 'hey'),
(0, '2023-11-08 12:29:04', 'hello'),
(0, '2023-11-08 12:29:41', 'hey'),
(0, '2023-11-08 12:31:10', 'hey'),
(0, '2023-11-08 12:31:47', 'test'),
(0, '2023-11-08 12:36:13', 'whats up'),
(0, '2023-11-08 15:37:55', '****'),
(0, '2023-11-27 12:50:08', 'test'),
(0, '2023-11-27 13:30:41', 'test');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_name`, `password`, `name`) VALUES
(1, 'elias', '123', 'Elias'),
(2, 'john', 'abc', 'John'),
(3, 'admin', '12345', 'miles'),
(4, 'test', '$2y$10$9d0Kgt9koMptqs4i6jYn8OMl4tLWmz9NJOqFYsEULufftehVwrTba', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
