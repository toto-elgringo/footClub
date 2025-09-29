-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 22, 2025 at 02:11 PM
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
-- Database: `foot_club`
--

-- --------------------------------------------------------

--
-- Table structure for table `match`
--

CREATE TABLE `match` (
  `id` int(11) NOT NULL,
  `team_score` int(11) DEFAULT NULL,
  `opponent_score` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `opposing_club_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `match`
--

INSERT INTO `match` (`id`, `team_score`, `opponent_score`, `date`, `team_id`, `city`, `opposing_club_id`) VALUES
(2, 1, 1, '2025-09-02 15:25:00', 1, '888', 3),
(3, 3, 1, '2025-09-03 15:28:00', 1, 'azfazf', 2);

-- --------------------------------------------------------

--
-- Table structure for table `opposing_club`
--

CREATE TABLE `opposing_club` (
  `id` int(11) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `opposing_club`
--

INSERT INTO `opposing_club` (`id`, `address`, `city`) VALUES
(2, 'la seine', 'paris'),
(3, 'aaaa', 'eeee');

-- --------------------------------------------------------

--
-- Table structure for table `player`
--

CREATE TABLE `player` (
  `id` int(11) NOT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `birthdate` datetime DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `player`
--

INSERT INTO `player` (`id`, `firstname`, `lastname`, `birthdate`, `picture`) VALUES
(2, 'antoine', 'andre', '2006-07-25 00:00:00', 'player_68cc3454d2f7b3.85358352.png'),
(3, 'Alex', 'Alex le mongol', '2023-10-05 00:00:00', 'player_68cc35f71f1af7.82692938.jpg'),
(4, 'toto', 'toto2', '2025-09-20 00:00:00', 'player_68ce949dd884d1.87388893.jpg'),
(5, 'gg', 'gg', '2000-08-07 00:00:00', 'player_68ce9a102041b8.05253739.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `player_has_team`
--

CREATE TABLE `player_has_team` (
  `player_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `role` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `player_has_team`
--

INSERT INTO `player_has_team` (`player_id`, `team_id`, `role`) VALUES
(2, 1, 'Gardien'),
(3, 1, 'Gardien'),
(3, 2, 'Défenseur');

-- --------------------------------------------------------

--
-- Table structure for table `staff_member`
--

CREATE TABLE `staff_member` (
  `id` int(11) NOT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff_member`
--

INSERT INTO `staff_member` (`id`, `firstname`, `lastname`, `picture`, `role`) VALUES
(6, 'toto', 'andre', 'uploads/staff/staff_68cebd006c0d77.68561694.jpg', 'Préparateur'),
(7, 'azfaz', 'ef', 'uploads/staff/staff_68cf9612392189.49502388.jpg', 'Entraineur');

-- --------------------------------------------------------

--
-- Table structure for table `team`
--

CREATE TABLE `team` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `team`
--

INSERT INTO `team` (`id`, `name`) VALUES
(1, 'toto team'),
(2, 'super team'),
(3, 'eeee');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `match`
--
ALTER TABLE `match`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_match_team` (`team_id`),
  ADD KEY `fk_match_opposing` (`opposing_club_id`);

--
-- Indexes for table `opposing_club`
--
ALTER TABLE `opposing_club`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `player`
--
ALTER TABLE `player`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `player_has_team`
--
ALTER TABLE `player_has_team`
  ADD PRIMARY KEY (`player_id`,`team_id`),
  ADD KEY `fk_pht_team` (`team_id`);

--
-- Indexes for table `staff_member`
--
ALTER TABLE `staff_member`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `team`
--
ALTER TABLE `team`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `match`
--
ALTER TABLE `match`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `opposing_club`
--
ALTER TABLE `opposing_club`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `player`
--
ALTER TABLE `player`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `staff_member`
--
ALTER TABLE `staff_member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `team`
--
ALTER TABLE `team`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `match`
--
ALTER TABLE `match`
  ADD CONSTRAINT `fk_match_opposing` FOREIGN KEY (`opposing_club_id`) REFERENCES `opposing_club` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_match_team` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `player_has_team`
--
ALTER TABLE `player_has_team`
  ADD CONSTRAINT `fk_pht_player` FOREIGN KEY (`player_id`) REFERENCES `player` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pht_team` FOREIGN KEY (`team_id`) REFERENCES `team` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
