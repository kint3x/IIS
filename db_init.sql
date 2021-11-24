-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 24, 2021 at 01:08 PM
-- Server version: 10.3.25-MariaDB-0ubuntu0.20.04.1
-- PHP Version: 7.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `iis`
--

-- --------------------------------------------------------

--
-- Table structure for table `Conference`
--

CREATE TABLE `Conference` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `name` varchar(150) COLLATE utf8_slovak_ci NOT NULL,
  `description` text COLLATE utf8_slovak_ci DEFAULT NULL,
  `street` varchar(150) COLLATE utf8_slovak_ci NOT NULL,
  `city` varchar(150) COLLATE utf8_slovak_ci NOT NULL,
  `zip` int(11) NOT NULL,
  `state` varchar(150) COLLATE utf8_slovak_ci NOT NULL,
  `time_from` int(11) NOT NULL,
  `time_to` int(11) NOT NULL,
  `price` float NOT NULL,
  `capacity` int(11) NOT NULL,
  `image_url` varchar(255) COLLATE utf8_slovak_ci NOT NULL DEFAULT '/img/placeholder.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cross_conf_tag`
--

CREATE TABLE `cross_conf_tag` (
  `conference_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `Lecture`
--

CREATE TABLE `Lecture` (
  `id` int(11) NOT NULL,
  `name` varchar(150) CHARACTER SET utf8 COLLATE utf8_slovak_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_slovak_ci NOT NULL,
  `time_from` int(11) DEFAULT NULL,
  `time_to` int(11) DEFAULT NULL,
  `img_url` varchar(300) CHARACTER SET utf8 COLLATE utf8_slovak_ci DEFAULT '/img/lecture_placholder.jpg',
  `conference_id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `id_user` int(100) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `Question`
--

CREATE TABLE `Question` (
  `id` int(11) NOT NULL,
  `question` varchar(350) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lecture_id` int(11) NOT NULL,
  `top` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `Reservation`
--

CREATE TABLE `Reservation` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8_slovak_ci NOT NULL,
  `surname` varchar(100) COLLATE utf8_slovak_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_slovak_ci NOT NULL,
  `conference_id` int(11) NOT NULL,
  `street` varchar(255) COLLATE utf8_slovak_ci NOT NULL,
  `city` varchar(255) COLLATE utf8_slovak_ci NOT NULL,
  `zip` varchar(255) COLLATE utf8_slovak_ci NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `country` varchar(255) COLLATE utf8_slovak_ci NOT NULL,
  `price` float NOT NULL,
  `num_tickets` int(11) NOT NULL,
  `state` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Room`
--

CREATE TABLE `Room` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8_slovak_ci NOT NULL,
  `conference_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Schedule`
--

CREATE TABLE `Schedule` (
  `id_user` int(11) NOT NULL,
  `id_lecture` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `Tag`
--

CREATE TABLE `Tag` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8_slovak_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Ticket`
--

CREATE TABLE `Ticket` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `hash` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `User`
--

CREATE TABLE `User` (
  `id` int(11) NOT NULL,
  `email` varchar(100) COLLATE utf8_slovak_ci NOT NULL,
  `password` varchar(100) COLLATE utf8_slovak_ci NOT NULL,
  `role` tinyint(4) NOT NULL,
  `name` varchar(100) COLLATE utf8_slovak_ci NOT NULL DEFAULT '',
  `surname` varchar(100) COLLATE utf8_slovak_ci NOT NULL DEFAULT '',
  `street` varchar(255) CHARACTER SET utf16 COLLATE utf16_slovak_ci DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf16 COLLATE utf16_slovak_ci DEFAULT NULL,
  `zip` int(11) DEFAULT NULL,
  `state` varchar(255) CHARACTER SET utf16 COLLATE utf16_slovak_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Conference`
--
ALTER TABLE `Conference`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_id_user` (`id_user`);

--
-- Indexes for table `cross_conf_tag`
--
ALTER TABLE `cross_conf_tag`
  ADD PRIMARY KEY (`conference_id`,`tag_id`),
  ADD KEY `FK_Genre` (`tag_id`);

--
-- Indexes for table `Lecture`
--
ALTER TABLE `Lecture`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_Room` (`room_id`) USING BTREE,
  ADD KEY `FK_User` (`id_user`),
  ADD KEY `FK_Lecture_Conference` (`conference_id`);

--
-- Indexes for table `Question`
--
ALTER TABLE `Question`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_User` (`user_id`) USING BTREE,
  ADD KEY `FK_question_lecture` (`lecture_id`);

--
-- Indexes for table `Reservation`
--
ALTER TABLE `Reservation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_Reservation` (`conference_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `Room`
--
ALTER TABLE `Room`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_Room` (`conference_id`);

--
-- Indexes for table `Schedule`
--
ALTER TABLE `Schedule`
  ADD PRIMARY KEY (`id_user`,`id_lecture`),
  ADD KEY `id_lecture` (`id_lecture`);

--
-- Indexes for table `Tag`
--
ALTER TABLE `Tag`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Ticket`
--
ALTER TABLE `Ticket`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `User`
--
ALTER TABLE `User`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Conference`
--
ALTER TABLE `Conference`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Lecture`
--
ALTER TABLE `Lecture`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Question`
--
ALTER TABLE `Question`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Reservation`
--
ALTER TABLE `Reservation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Room`
--
ALTER TABLE `Room`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Tag`
--
ALTER TABLE `Tag`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Ticket`
--
ALTER TABLE `Ticket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `User`
--
ALTER TABLE `User`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Conference`
--
ALTER TABLE `Conference`
  ADD CONSTRAINT `FK_id_user` FOREIGN KEY (`id_user`) REFERENCES `User` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cross_conf_tag`
--
ALTER TABLE `cross_conf_tag`
  ADD CONSTRAINT `FK_Conference` FOREIGN KEY (`conference_id`) REFERENCES `Conference` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_Genre` FOREIGN KEY (`tag_id`) REFERENCES `Tag` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `Lecture`
--
ALTER TABLE `Lecture`
  ADD CONSTRAINT `FK_Lecture_Conference` FOREIGN KEY (`conference_id`) REFERENCES `Conference` (`id`),
  ADD CONSTRAINT `FK_User` FOREIGN KEY (`id_user`) REFERENCES `User` (`id`);

--
-- Constraints for table `Question`
--
ALTER TABLE `Question`
  ADD CONSTRAINT `FK_question_lecture` FOREIGN KEY (`lecture_id`) REFERENCES `Lecture` (`id`),
  ADD CONSTRAINT `Question_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Reservation`
--
ALTER TABLE `Reservation`
  ADD CONSTRAINT `FK_Reservation` FOREIGN KEY (`conference_id`) REFERENCES `Conference` (`id`),
  ADD CONSTRAINT `Reservation_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `User` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Room`
--
ALTER TABLE `Room`
  ADD CONSTRAINT `FK_Room` FOREIGN KEY (`conference_id`) REFERENCES `Conference` (`id`);

--
-- Constraints for table `Schedule`
--
ALTER TABLE `Schedule`
  ADD CONSTRAINT `Schedule_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `User` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Schedule_ibfk_2` FOREIGN KEY (`id_lecture`) REFERENCES `Lecture` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Ticket`
--
ALTER TABLE `Ticket`
  ADD CONSTRAINT `Ticket_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `Reservation` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

INSERT INTO `Tag` (`name`) VALUES ('Enviromentalistika');
INSERT INTO `Tag` (`name`) VALUES ('Šport');
INSERT INTO `Tag` (`name`) VALUES ('Informačné technológie');
INSERT INTO `Tag` (`name`) VALUES ('Duchovné');
INSERT INTO `Tag` (`name`) VALUES ('Cestovanie');
INSERT INTO `Tag` (`name`) VALUES ('Technológie');
INSERT INTO `Tag` (`name`) VALUES ('Auto-moto');
INSERT INTO `Tag` (`name`) VALUES ('Hudba');
INSERT INTO `Tag` (`name`) VALUES ('Filmy');
INSERT INTO `Tag` (`name`) VALUES ('Veda');
INSERT INTO `Tag` (`name`) VALUES ('Zdravie');