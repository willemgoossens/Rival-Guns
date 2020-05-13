-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2020 at 03:20 PM
-- Server version: 10.1.37-MariaDB
-- PHP Version: 7.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rivalguns`
--

-- --------------------------------------------------------

--
-- Table structure for table `adminrights`
--

CREATE TABLE `adminrights` (
  `id` int(3) NOT NULL,
  `name` varchar(32) NOT NULL,
  `description` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `adminrights`
--

INSERT INTO `adminrights` (`id`, `name`, `description`) VALUES
(1, 'AddPosts', 'This person has the rights to create, edit and delete their own posts'),
(2, 'OverrulePosts', 'This person is allowed to edit and/or delete the posts of the other admins'),
(4, 'EditAdminRoles', 'This allows the user to create and edit the admin roles and to assign them to other users'),
(5, 'HandleReportedConversations', 'This allows the user to handle the reported conversations.');

-- --------------------------------------------------------

--
-- Table structure for table `adminroles`
--

CREATE TABLE `adminroles` (
  `id` int(2) NOT NULL,
  `name` varchar(50) NOT NULL,
  `colorCode` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `adminroles`
--

INSERT INTO `adminroles` (`id`, `name`, `colorCode`) VALUES
(4, 'Webmaster', '#ff0000'),
(11, 'xxx-test-overrulePosts', '#000000'),
(12, 'xxx-test-addPosts', '#c0c0c0');

-- --------------------------------------------------------

--
-- Table structure for table `adminroles_adminrights`
--

CREATE TABLE `adminroles_adminrights` (
  `adminroleId` int(2) NOT NULL,
  `adminrightId` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `adminroles_adminrights`
--

INSERT INTO `adminroles_adminrights` (`adminroleId`, `adminrightId`) VALUES
(12, 1),
(11, 2),
(4, 1),
(4, 2),
(4, 4),
(4, 5);

-- --------------------------------------------------------

--
-- Table structure for table `conversationreports`
--

CREATE TABLE `conversationreports` (
  `id` int(250) NOT NULL,
  `conversationId` int(100) NOT NULL,
  `reportedById` int(250) NOT NULL,
  `sexismRacism` tinyint(1) NOT NULL,
  `spam` tinyint(1) NOT NULL,
  `insult` tinyint(1) NOT NULL,
  `other` tinyint(1) NOT NULL,
  `otherExplanation` text NOT NULL,
  `classified` tinyint(1) NOT NULL,
  `handledById` int(250) DEFAULT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `summary` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `id` int(100) NOT NULL,
  `userId` int(250) NOT NULL,
  `subject` varchar(250) NOT NULL,
  `noReply` tinyint(1) NOT NULL,
  `noReplySender` varchar(30) DEFAULT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `conversations`
--

INSERT INTO `conversations` (`id`, `userId`, `subject`, `noReply`, `noReplySender`, `createdAt`) VALUES
(26, 2, 'Hoi', 0, NULL, '2020-05-13 14:27:58');

-- --------------------------------------------------------

--
-- Table structure for table `crimecategories`
--

CREATE TABLE `crimecategories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `mainCategory` enum('Mafia Job','Crimes','Car Stealing') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `crimecategories`
--

INSERT INTO `crimecategories` (`id`, `name`, `description`, `mainCategory`) VALUES
(1, 'Go Begging', 'Looking for some easy money? You can get a little money by begging on the streets.', 'Crimes');

-- --------------------------------------------------------

--
-- Table structure for table `crimes`
--

CREATE TABLE `crimes` (
  `id` int(11) NOT NULL,
  `crimeCategoryId` int(11) NOT NULL,
  `crimeName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `crimes`
--

INSERT INTO `crimes` (`id`, `crimeCategoryId`, `crimeName`) VALUES
(1, 1, 'Go begging in front of the supermarket.'),
(2, 1, 'Go begging in front of the jewelry.');

-- --------------------------------------------------------

--
-- Table structure for table `crimetypes`
--

CREATE TABLE `crimetypes` (
  `id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `expiredByTime` int(6) NOT NULL,
  `jailTime` int(6) NOT NULL,
  `addStars` decimal(3,2) NOT NULL,
  `addStarsUntil` decimal(3,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `crimetypes`
--

INSERT INTO `crimetypes` (`id`, `name`, `expiredByTime`, `jailTime`, `addStars`, `addStarsUntil`) VALUES
(1, 'minor assault', 180, 30, '0.25', '2.00'),
(2, 'robbery', 330, 60, '0.75', '2.25');

-- --------------------------------------------------------

--
-- Table structure for table `criminalrecords`
--

CREATE TABLE `criminalrecords` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `type` int(6) NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(250) NOT NULL,
  `conversationId` int(250) NOT NULL,
  `body` text NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userId` int(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `conversationId`, `body`, `createdAt`, `userId`) VALUES
(45, 26, 'How you doing?', '2020-05-13 14:27:58', 2),
(46, 26, 'I&#039;m doing fine :)\n\n*What about you?*', '2020-05-13 14:37:20', 1);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `userId`, `title`, `body`, `createdAt`) VALUES
(55, 2, 'Welcome to RivalGuns', 'Hoi allemaal,\n\nDit is een nieuwspost.\n\n**Veel plezier!**\n\nWillem', '2020-05-13 13:59:11');

-- --------------------------------------------------------

--
-- Table structure for table `punishments`
--

CREATE TABLE `punishments` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `conversationReportId` int(11) DEFAULT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `endsAt` datetime DEFAULT NULL,
  `explanation` text NOT NULL,
  `punishmentType` enum('warning','temporaryBan','permanentBan') NOT NULL,
  `punishedById` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `adminRole` int(2) DEFAULT NULL,
  `boxing` int(9) NOT NULL DEFAULT '0',
  `cash` bigint(20) NOT NULL DEFAULT '50',
  `charisma` int(9) NOT NULL DEFAULT '0',
  `email` varchar(255) NOT NULL,
  `health` decimal(5,2) NOT NULL DEFAULT '100.00',
  `inJailUntil` datetime NOT NULL,
  `energy` decimal(5,2) NOT NULL DEFAULT '100.00',
  `password` varchar(255) NOT NULL,
  `resetKey` varchar(100) NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `adminRole`, `boxing`, `cash`, `charisma`, `email`, `health`, `inJailUntil`, `energy`, `password`, `resetKey`, `createdAt`) VALUES
(1, 'testaccount1', NULL, 101, 50, 0, 'test1@test.com', '100.00', '0000-00-00 00:00:00', '100.00', '$2y$10$gx/3veekeiaiGWAE8CFE0.dB0GgpHUi/5sV31lc3YSZFTTADp/uUG', '', '2019-02-16 20:14:36'),
(2, 'admin', 4, 558, 3657, 601, 'admin@test.com', '64.95', '2020-05-04 00:00:00', '46.50', '$2y$10$eX0GEcmxokWdNbhsUpI25.GKpDqvySB1JbvUPS7Q.hS2Fdb/TlAd.', '', '2019-03-02 21:39:08'),
(4, 'testaccount2', NULL, 0, 69, 5, 'test2@test.com', '100.00', '0000-00-00 00:00:00', '98.70', '$2y$10$kCorgsWvSQczBp16VjbIn.BK7S.nG2T/itHBEjjnVtOg9m94CREnW', 'a906e303a164fd74e00dbd2a63815bba', '2020-03-24 18:56:36');

-- --------------------------------------------------------

--
-- Table structure for table `users_conversations`
--

CREATE TABLE `users_conversations` (
  `userid` int(11) NOT NULL,
  `conversationid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_conversations`
--

INSERT INTO `users_conversations` (`userid`, `conversationid`) VALUES
(1, 26),
(2, 26);

-- --------------------------------------------------------

--
-- Table structure for table `users_unread_messages`
--

CREATE TABLE `users_unread_messages` (
  `userid` int(11) NOT NULL,
  `messageid` int(11) NOT NULL,
  `conversationid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_unread_messages`
--

INSERT INTO `users_unread_messages` (`userid`, `messageid`, `conversationid`) VALUES
(2, 46, 26);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adminrights`
--
ALTER TABLE `adminrights`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `adminroles`
--
ALTER TABLE `adminroles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `adminroles_adminrights`
--
ALTER TABLE `adminroles_adminrights`
  ADD KEY `adminrole_id` (`adminroleId`),
  ADD KEY `adminright_id` (`adminrightId`);

--
-- Indexes for table `conversationreports`
--
ALTER TABLE `conversationreports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `link_to_conversation` (`conversationId`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user1_id` (`userId`);

--
-- Indexes for table `crimecategories`
--
ALTER TABLE `crimecategories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crimes`
--
ALTER TABLE `crimes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crimeCategoryId` (`crimeCategoryId`);

--
-- Indexes for table `crimetypes`
--
ALTER TABLE `crimetypes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `criminalrecords`
--
ALTER TABLE `criminalrecords`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userId` (`userId`),
  ADD KEY `type` (`type`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conversation_id` (`conversationId`),
  ADD KEY `user_id` (`userId`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `punishments`
--
ALTER TABLE `punishments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userId` (`userId`),
  ADD KEY `conversationReportId` (`conversationReportId`),
  ADD KEY `punishedById` (`punishedById`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `adminrole_id` (`adminRole`);

--
-- Indexes for table `users_conversations`
--
ALTER TABLE `users_conversations`
  ADD KEY `conversationId` (`conversationid`),
  ADD KEY `userId` (`userid`);

--
-- Indexes for table `users_unread_messages`
--
ALTER TABLE `users_unread_messages`
  ADD KEY `userId` (`userid`),
  ADD KEY `messageId` (`messageid`),
  ADD KEY `conversationid` (`conversationid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adminrights`
--
ALTER TABLE `adminrights`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `adminroles`
--
ALTER TABLE `adminroles`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `conversationreports`
--
ALTER TABLE `conversationreports`
  MODIFY `id` int(250) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `crimecategories`
--
ALTER TABLE `crimecategories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `crimes`
--
ALTER TABLE `crimes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `crimetypes`
--
ALTER TABLE `crimetypes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `criminalrecords`
--
ALTER TABLE `criminalrecords`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(250) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `punishments`
--
ALTER TABLE `punishments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adminroles_adminrights`
--
ALTER TABLE `adminroles_adminrights`
  ADD CONSTRAINT `adminroles_adminrights_ibfk_1` FOREIGN KEY (`adminroleId`) REFERENCES `adminroles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `adminroles_adminrights_ibfk_2` FOREIGN KEY (`adminrightId`) REFERENCES `adminrights` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `conversationreports`
--
ALTER TABLE `conversationreports`
  ADD CONSTRAINT `link_to_conversation` FOREIGN KEY (`conversationId`) REFERENCES `conversations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `conversations_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `crimes`
--
ALTER TABLE `crimes`
  ADD CONSTRAINT `linkt_to_category` FOREIGN KEY (`crimeCategoryId`) REFERENCES `crimecategories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `criminalrecords`
--
ALTER TABLE `criminalrecords`
  ADD CONSTRAINT `criminalrecords_ibfk_1` FOREIGN KEY (`type`) REFERENCES `crimetypes` (`id`),
  ADD CONSTRAINT `link_to_userId` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`conversationId`) REFERENCES `conversations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `punishments`
--
ALTER TABLE `punishments`
  ADD CONSTRAINT `punishments_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `punishments_ibfk_2` FOREIGN KEY (`conversationReportId`) REFERENCES `conversationreports` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `punishments_ibfk_3` FOREIGN KEY (`punishedById`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `id_to_user_adminrole` FOREIGN KEY (`adminRole`) REFERENCES `adminroles` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `users_conversations`
--
ALTER TABLE `users_conversations`
  ADD CONSTRAINT `users_conversations_ibfk_1` FOREIGN KEY (`conversationId`) REFERENCES `conversations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_conversations_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users_unread_messages`
--
ALTER TABLE `users_unread_messages`
  ADD CONSTRAINT `users_unread_messages_ibfk_1` FOREIGN KEY (`messageid`) REFERENCES `messages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_unread_messages_ibfk_2` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_unread_messages_ibfk_3` FOREIGN KEY (`conversationid`) REFERENCES `conversations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
