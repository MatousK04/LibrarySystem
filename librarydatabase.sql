-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 29, 2024 at 12:00 AM
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
-- Database: `librarydatabase`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `ISBN` varchar(10) NOT NULL,
  `BookTitle` varchar(50) DEFAULT NULL,
  `Author` varchar(20) DEFAULT NULL,
  `Edition` int(11) DEFAULT NULL,
  `YearReleased` int(11) DEFAULT NULL,
  `CategoryID` varchar(3) DEFAULT NULL,
  `Reserved` varchar(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`ISBN`, `BookTitle`, `Author`, `Edition`, `YearReleased`, `CategoryID`, `Reserved`) VALUES
('9780123456', 'Czech Scientific Achievements', 'Václav Havlíček', 1, 2016, '003', 'No'),
('9781011122', 'Czech Avant-Garde Art', 'Václav Čermák', 1, 2002, '003', 'No'),
('9781122334', 'The Bohemian Kingdoms', 'Alena Sokolová', 1, 2008, '004', 'No'),
('9781234567', 'Czech Legends', 'Karel Novák', 1, 2005, '001', 'Yes'),
('9782233445', 'Czech Cooking Secrets', 'Jana Malá', 2, 2015, '002', 'No'),
('9782345678', 'History of Prague', 'Petr Svoboda', 2, 2010, '004', 'No'),
('9783344556', 'Echoes of Prague', 'Tomas Holeček', 1, 2019, '001', 'No'),
('9783456789', 'The Mystery of the Prague Castle', 'Eva Horáková', 1, 2015, '006', 'No'),
('9784455667', 'Tales from Moravia', 'Zdenka Novotná', 1, 2020, '007', 'No'),
('9784567890', 'The Czech Republic in the 20th Century', 'Jan Procházka', 3, 1988, '004', 'No'),
('9785566778', 'The Battle of White Mountain', 'Martin Vondra', 3, 2012, '004', 'No'),
('9785678901', 'Great Czech Writers', 'Milan Kundera', 1, 1999, '005', 'No'),
('9786677889', 'The Prague Underground', 'Jaroslav Černý', 1, 2017, '006', 'No'),
('9786789012', 'Romantic Tales of Bohemia', 'Zuzana Černá', 1, 2018, '007', 'No'),
('9787788990', 'Bohemian Fairy Tales', 'Lukáš Kříž', 2, 2005, '008', 'No'),
('9787890123', 'Czech Fantasy Adventures', 'Jiří Karel', 1, 2020, '008', 'No'),
('9788899001', 'Prague: A City of Mysteries', 'Veronika Mlynářová', 1, 2014, '006', 'No'),
('9788901234', 'Czech Folklore', 'Ludmila Králová', 1, 2012, '002', 'No'),
('9789012345', 'Czech Detective Stories', 'Marek Dvořák', 4, 2000, '006', 'No'),
('9789900112', 'Czech Women in History', 'Kristýna Králová', 4, 2011, '005', 'No');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `CategoryID` varchar(3) NOT NULL,
  `CategoryDescription` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`CategoryID`, `CategoryDescription`) VALUES
('001', 'Fiction'),
('002', 'Non-fiction'),
('003', 'Science'),
('004', 'History'),
('005', 'Biography'),
('006', 'Mystery'),
('007', 'Romance'),
('008', 'Fantasy');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `ISBN` varchar(10) NOT NULL,
  `Username` varchar(20) NOT NULL,
  `ReservedDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`ISBN`, `Username`, `ReservedDate`) VALUES
('9781234567', 'GlueKing98', '2024-11-28');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `username` varchar(20) NOT NULL,
  `password` varchar(6) DEFAULT NULL,
  `email` varchar(20) DEFAULT NULL,
  `phone` varchar(10) DEFAULT NULL,
  `FirstName` varchar(10) DEFAULT NULL,
  `LastName` varchar(10) DEFAULT NULL,
  `House` varchar(20) DEFAULT NULL,
  `Town` varchar(20) DEFAULT NULL,
  `County` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`username`, `password`, `email`, `phone`, `FirstName`, `LastName`, `House`, `Town`, `County`) VALUES
('GlueKing98', 'iamgay', 'kloucek.matous@outlo', '0830963962', 'Matous', 'Kloucek', '1 Churchview Park', 'Killiney', 'Co. Dublin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`ISBN`),
  ADD KEY `CategoryID` (`CategoryID`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`CategoryID`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`ISBN`,`Username`),
  ADD KEY `Username` (`Username`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`username`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`CategoryID`) REFERENCES `categories` (`CategoryID`);

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`ISBN`) REFERENCES `books` (`ISBN`),
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`Username`) REFERENCES `users` (`username`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
