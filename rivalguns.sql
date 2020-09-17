-- MariaDB dump 10.17  Distrib 10.4.13-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: rivalguns
-- ------------------------------------------------------
-- Server version	10.4.13-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `adminrights`
--

DROP TABLE IF EXISTS `adminrights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adminrights` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `description` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `adminrights`
--

LOCK TABLES `adminrights` WRITE;
/*!40000 ALTER TABLE `adminrights` DISABLE KEYS */;
INSERT INTO `adminrights` VALUES (1,'AddPosts','This person has the rights to create, edit and delete their own posts');
INSERT INTO `adminrights` VALUES (2,'OverrulePosts','This person is allowed to edit and/or delete the posts of the other admins');
INSERT INTO `adminrights` VALUES (4,'EditAdminRoles','This allows the user to create and edit the admin roles and to assign them to other users');
INSERT INTO `adminrights` VALUES (5,'HandleReportedConversations','This allows the user to handle the reported conversations.');
/*!40000 ALTER TABLE `adminrights` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `adminroles`
--

DROP TABLE IF EXISTS `adminroles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adminroles` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `colorCode` varchar(7) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `adminroles`
--

LOCK TABLES `adminroles` WRITE;
/*!40000 ALTER TABLE `adminroles` DISABLE KEYS */;
INSERT INTO `adminroles` VALUES (4,'Webmaster','#ff0000');
INSERT INTO `adminroles` VALUES (11,'xxx-test-overrulePosts','#000000');
INSERT INTO `adminroles` VALUES (12,'xxx-test-addPosts','#a36c6c');
/*!40000 ALTER TABLE `adminroles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `adminroles_adminrights`
--

DROP TABLE IF EXISTS `adminroles_adminrights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adminroles_adminrights` (
  `adminroleId` int(2) NOT NULL,
  `adminrightId` int(3) NOT NULL,
  KEY `adminrole_id` (`adminroleId`),
  KEY `adminright_id` (`adminrightId`),
  CONSTRAINT `adminroles_adminrights_ibfk_1` FOREIGN KEY (`adminroleId`) REFERENCES `adminroles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `adminroles_adminrights_ibfk_2` FOREIGN KEY (`adminrightId`) REFERENCES `adminrights` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `adminroles_adminrights`
--

LOCK TABLES `adminroles_adminrights` WRITE;
/*!40000 ALTER TABLE `adminroles_adminrights` DISABLE KEYS */;
INSERT INTO `adminroles_adminrights` VALUES (11,2);
INSERT INTO `adminroles_adminrights` VALUES (4,1);
INSERT INTO `adminroles_adminrights` VALUES (4,2);
INSERT INTO `adminroles_adminrights` VALUES (4,4);
INSERT INTO `adminroles_adminrights` VALUES (4,5);
INSERT INTO `adminroles_adminrights` VALUES (12,1);
/*!40000 ALTER TABLE `adminroles_adminrights` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `businesscategories`
--

DROP TABLE IF EXISTS `businesscategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `businesscategories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `profitPerDay` int(11) DEFAULT NULL,
  `launderingAmountPerDay` int(11) DEFAULT NULL,
  `isLegal` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `businesscategories`
--

LOCK TABLES `businesscategories` WRITE;
/*!40000 ALTER TABLE `businesscategories` DISABLE KEYS */;
INSERT INTO `businesscategories` VALUES (1,'House',NULL,NULL,1);
INSERT INTO `businesscategories` VALUES (2,'Nightstore',200,500,1);
/*!40000 ALTER TABLE `businesscategories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conversationreports`
--

DROP TABLE IF EXISTS `conversationreports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conversationreports` (
  `id` int(250) NOT NULL AUTO_INCREMENT,
  `conversationId` int(100) NOT NULL,
  `reportedById` int(250) NOT NULL,
  `sexismRacism` tinyint(1) NOT NULL,
  `spam` tinyint(1) NOT NULL,
  `insult` tinyint(1) NOT NULL,
  `other` tinyint(1) NOT NULL,
  `otherExplanation` text NOT NULL,
  `classified` tinyint(1) NOT NULL,
  `handledById` int(250) DEFAULT NULL,
  `createdAt` datetime NOT NULL DEFAULT current_timestamp(),
  `summary` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `link_to_conversation` (`conversationId`),
  CONSTRAINT `link_to_conversation` FOREIGN KEY (`conversationId`) REFERENCES `conversations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conversationreports`
--

LOCK TABLES `conversationreports` WRITE;
/*!40000 ALTER TABLE `conversationreports` DISABLE KEYS */;
/*!40000 ALTER TABLE `conversationreports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conversations`
--

DROP TABLE IF EXISTS `conversations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conversations` (
  `id` int(100) NOT NULL AUTO_INCREMENT,
  `userId` int(250) DEFAULT NULL,
  `subject` varchar(250) NOT NULL,
  `noReply` tinyint(1) NOT NULL,
  `noReplySender` varchar(30) DEFAULT NULL,
  `createdAt` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user1_id` (`userId`),
  CONSTRAINT `conversations_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conversations`
--

LOCK TABLES `conversations` WRITE;
/*!40000 ALTER TABLE `conversations` DISABLE KEYS */;
INSERT INTO `conversations` VALUES (26,2,'Hoi',0,NULL,'2020-05-13 14:27:58');
INSERT INTO `conversations` VALUES (27,2,'Hi there',0,NULL,'2020-05-16 23:36:22');
INSERT INTO `conversations` VALUES (44,NULL,'Work report',1,'Harry\'s Hoovers','2020-08-27 23:57:59');
INSERT INTO `conversations` VALUES (45,NULL,'Work report',1,'Harry\'s Hoovers','2020-08-27 23:59:46');
INSERT INTO `conversations` VALUES (46,NULL,'Work report',1,'Harry\'s Hoovers','2020-08-28 00:00:51');
INSERT INTO `conversations` VALUES (47,NULL,'Work report',1,'Harry\'s Hoovers','2020-08-28 00:01:44');
INSERT INTO `conversations` VALUES (48,NULL,'Work report',1,'Harry\'s Hoovers','2020-08-28 00:02:30');
INSERT INTO `conversations` VALUES (49,NULL,'Work report',1,'Harry\'s Hoovers','2020-08-28 00:05:02');
INSERT INTO `conversations` VALUES (50,NULL,'Work report',1,'Harry\'s Hoovers','2020-08-28 00:07:00');
INSERT INTO `conversations` VALUES (51,NULL,'Work report',1,'Harry\'s Hoovers','2020-08-28 00:10:09');
INSERT INTO `conversations` VALUES (52,NULL,'Work report',1,'Harry\'s Hoovers','2020-08-28 12:42:32');
INSERT INTO `conversations` VALUES (53,2,'test',0,NULL,'2020-09-14 14:49:25');
INSERT INTO `conversations` VALUES (54,2,'Question regarding r/socialanxiety',0,NULL,'2020-09-14 20:16:01');
/*!40000 ALTER TABLE `conversations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `crimecategories`
--

DROP TABLE IF EXISTS `crimecategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crimecategories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `mainCategory` enum('Mafia Jobs','Crimes','Car Stealing') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `crimecategories`
--

LOCK TABLES `crimecategories` WRITE;
/*!40000 ALTER TABLE `crimecategories` DISABLE KEYS */;
INSERT INTO `crimecategories` VALUES (1,'Go Begging','Looking for some easy money? You can get a little money by begging on the streets.','Crimes');
INSERT INTO `crimecategories` VALUES (2,'Look for new protectees.','There\'s some new businesses in town that might need some protection. Go tell them how much they need it.','Mafia Jobs');
/*!40000 ALTER TABLE `crimecategories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `crimes`
--

DROP TABLE IF EXISTS `crimes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crimes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `crimeCategoryId` int(11) NOT NULL,
  `crimeName` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `crimeCategoryId` (`crimeCategoryId`),
  CONSTRAINT `linkt_to_category` FOREIGN KEY (`crimeCategoryId`) REFERENCES `crimecategories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `crimes`
--

LOCK TABLES `crimes` WRITE;
/*!40000 ALTER TABLE `crimes` DISABLE KEYS */;
INSERT INTO `crimes` VALUES (1,1,'Go begging in front of the supermarket.');
INSERT INTO `crimes` VALUES (2,1,'Go begging in front of the jewelry.');
INSERT INTO `crimes` VALUES (3,2,'Go visit a nightstore.');
/*!40000 ALTER TABLE `crimes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `crimetypes`
--

DROP TABLE IF EXISTS `crimetypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crimetypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `expiredByTime` int(6) NOT NULL,
  `jailTime` int(6) NOT NULL,
  `addStars` decimal(3,2) NOT NULL,
  `addStarsUntil` decimal(3,2) NOT NULL,
  `alwaysConvict` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `crimetypes`
--

LOCK TABLES `crimetypes` WRITE;
/*!40000 ALTER TABLE `crimetypes` DISABLE KEYS */;
INSERT INTO `crimetypes` VALUES (1,'minor assault',180,30,0.25,2.00,0);
INSERT INTO `crimetypes` VALUES (2,'robbery',330,60,0.75,2.25,0);
INSERT INTO `crimetypes` VALUES (3,'property destruction',60,30,0.25,1.50,0);
INSERT INTO `crimetypes` VALUES (4,'participation in organized crime',300,300,0.75,4.25,0);
INSERT INTO `crimetypes` VALUES (5,'attempting to bust out a prison',120,180,0.75,4.00,0);
INSERT INTO `crimetypes` VALUES (6,'escaping prison (minimum)',60,60,0.25,3.00,1);
INSERT INTO `crimetypes` VALUES (7,'escaping prison (medium)',120,180,0.50,4.00,1);
INSERT INTO `crimetypes` VALUES (8,'escaping prison (maximum)',180,360,1.00,4.00,1);
INSERT INTO `crimetypes` VALUES (9,'escaping prison (supermax)',300,900,2.00,5.00,1);
INSERT INTO `crimetypes` VALUES (10,'escaping prison (extreme)',900,1800,2.00,5.00,1);
/*!40000 ALTER TABLE `crimetypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `criminalrecords`
--

DROP TABLE IF EXISTS `criminalrecords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `criminalrecords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `imprisonmentId` int(11) DEFAULT NULL,
  `userId` int(11) NOT NULL,
  `type` int(6) NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `type` (`type`),
  KEY `imprisonmentId` (`imprisonmentId`),
  CONSTRAINT `criminalrecords_ibfk_1` FOREIGN KEY (`type`) REFERENCES `crimetypes` (`id`),
  CONSTRAINT `criminalrecords_ibfk_2` FOREIGN KEY (`imprisonmentId`) REFERENCES `imprisonments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `link_to_userId` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `criminalrecords`
--

LOCK TABLES `criminalrecords` WRITE;
/*!40000 ALTER TABLE `criminalrecords` DISABLE KEYS */;
/*!40000 ALTER TABLE `criminalrecords` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hospitalizations`
--

DROP TABLE IF EXISTS `hospitalizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hospitalizations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT current_timestamp(),
  `duration` int(11) NOT NULL,
  `reason` enum('mugged','hospitalized','resting','bled out') NOT NULL,
  `causedById` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `causedById` (`causedById`),
  CONSTRAINT `hospitalizations_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `hospitalizations_ibfk_2` FOREIGN KEY (`causedById`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hospitalizations`
--

LOCK TABLES `hospitalizations` WRITE;
/*!40000 ALTER TABLE `hospitalizations` DISABLE KEYS */;
/*!40000 ALTER TABLE `hospitalizations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `imprisonments`
--

DROP TABLE IF EXISTS `imprisonments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `imprisonments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `department` enum('minimum','medium','maximum','solitary') NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  CONSTRAINT `imprisonment_to_id` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `imprisonments`
--

LOCK TABLES `imprisonments` WRITE;
/*!40000 ALTER TABLE `imprisonments` DISABLE KEYS */;
/*!40000 ALTER TABLE `imprisonments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `id` int(250) NOT NULL AUTO_INCREMENT,
  `conversationId` int(250) NOT NULL,
  `body` text NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT current_timestamp(),
  `userId` int(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `conversation_id` (`conversationId`),
  KEY `user_id` (`userId`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`conversationId`) REFERENCES `conversations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (45,26,'How you doing?','2020-05-13 14:27:58',2);
INSERT INTO `messages` VALUES (46,26,'I&#039;m doing fine :)\n\n*What about you?*','2020-05-13 14:37:20',1);
INSERT INTO `messages` VALUES (47,27,'Another test message','2020-05-16 23:36:22',2);
INSERT INTO `messages` VALUES (61,44,'Goddamnit Admin,\r\n                    \r\n                    Did you seriously fail to sell just a single vacuum cleaner.\r\n                    How hard can it be?\r\n                    \r\n                    Kind regards,\r\n                    Harry','2020-08-27 23:57:59',NULL);
INSERT INTO `messages` VALUES (62,45,'Dear Admin,\\\r\n                \\\r\n                Thank you for working with Harry\'s Hoovers!\\\r\n                By selling 1 vacuum cleaners, you made $40.\\\r\n                You also earned 1 charisma points. The money has been deposited in your bank account.\\\r\n                \\\r\n                Kind regards,\\\r\n                Harry','2020-08-27 23:59:46',NULL);
INSERT INTO `messages` VALUES (63,46,'Goddamnit Admin,<br/>\r\n                    <br/>\r\n                    Did you seriously fail to sell just a single vacuum cleaner.<br/>\r\n                    How hard can it be?<br/>\r\n                    <br/>\r\n                    Kind regards,<br/>\r\n                    Harry','2020-08-28 00:00:51',NULL);
INSERT INTO `messages` VALUES (64,47,'Dear Admin,\\n\r\n                \\n\r\n                Thank you for working with Harry\'s Hoovers!\\n\r\n                By selling 1 vacuum cleaners, you made $40.\\n\r\n                You also earned 1 charisma points. The money has been deposited in your bank account.\\n\r\n                <br/>\r\n                Kind regards,<br/>\r\n                Harry','2020-08-28 00:01:44',NULL);
INSERT INTO `messages` VALUES (65,48,'Goddamnit Admin,  \r\n                      \r\n                    Did you seriously fail to sell just a single vacuum cleaner.  \r\n                    How hard can it be?  \r\n                      \r\n                    Kind regards,  \r\n                    Harry','2020-08-28 00:02:30',NULL);
INSERT INTO `messages` VALUES (66,49,'Goddamnit Admin,__\r\n                    __\r\n                    Did you seriously fail to sell just a single vacuum cleaner.__\r\n                    How hard can it be?__\r\n                    __\r\n                    Kind regards,__\r\n                    Harry','2020-08-28 00:05:02',NULL);
INSERT INTO `messages` VALUES (67,50,'Dear Admin,\\r\\n\r\n                \\r\\n\r\n                Thank you for working with Harry\'s Hoovers!\\r\\n\r\n                By selling 1 vacuum cleaner(s), you made $40.\\r\\n\r\n                You also earned 1 charisma points. The money has been deposited in your bank account.\\r\\n\r\n                \\r\\n\r\n                Kind regards,\\r\\n\r\n                Harry','2020-08-28 00:07:00',NULL);
INSERT INTO `messages` VALUES (68,51,'Goddamnit Admin,\r\n\r\nYou sold no vacuum cleaners at all.\r\nHow hard can it be?\r\n\r\nHarry','2020-08-28 00:10:09',NULL);
INSERT INTO `messages` VALUES (69,52,'Dear Admin,\r\n\r\nThank you for working with Harry\\\'s Hoovers!\r\nBy selling 1 vacuum cleaner(s), you made $40.\r\nYou also earned 1 charisma points. The money has been deposited in your bank account.\r\n\r\nKind regards,\r\nHarry','2020-08-28 12:42:32',NULL);
INSERT INTO `messages` VALUES (70,53,'sdfsfsfdsfsqfdfs dfssf','2020-09-14 14:49:25',2);
INSERT INTO `messages` VALUES (71,54,'**dfsqdfsfddsf**\n\n1. **dddd**\n2. **dd**\n3. **dd**\n4. ddddd\n5. *ddddd*','2020-09-14 20:16:01',2);
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `body` text NOT NULL,
  `readAt` datetime DEFAULT NULL,
  `link` varchar(255) NOT NULL DEFAULT '#',
  `class` varchar(20) NOT NULL DEFAULT 'alert-primary',
  `createdAt` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (55,2,'Welcome to RivalGuns','Hoi allemaal,\n\nDit is een nieuwspost.\n\n**Veel plezier!**\n\nWillem','2020-05-13 13:59:11');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `properties`
--

DROP TABLE IF EXISTS `properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `propertyCategoryId` int(11) NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `propertyCategoryId` (`propertyCategoryId`),
  CONSTRAINT `properties_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `properties_ibfk_2` FOREIGN KEY (`propertyCategoryId`) REFERENCES `propertycategories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `properties`
--

LOCK TABLES `properties` WRITE;
/*!40000 ALTER TABLE `properties` DISABLE KEYS */;
INSERT INTO `properties` VALUES (1,2,1,'2020-09-10 23:04:12');
INSERT INTO `properties` VALUES (2,2,1,'2020-09-10 23:06:28');
INSERT INTO `properties` VALUES (3,2,1,'2020-09-14 14:49:58');
INSERT INTO `properties` VALUES (4,2,1,'2020-09-14 18:40:17');
INSERT INTO `properties` VALUES (5,2,1,'2020-09-14 18:42:39');
INSERT INTO `properties` VALUES (6,2,1,'2020-09-14 18:42:54');
INSERT INTO `properties` VALUES (7,2,1,'2020-09-14 18:46:25');
INSERT INTO `properties` VALUES (8,2,1,'2020-09-14 18:47:03');
INSERT INTO `properties` VALUES (9,2,1,'2020-09-14 19:57:32');
/*!40000 ALTER TABLE `properties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `propertycategories`
--

DROP TABLE IF EXISTS `propertycategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `propertycategories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `allowPaymentByCash` tinyint(1) NOT NULL DEFAULT 0,
  `price` int(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `propertycategories`
--

LOCK TABLES `propertycategories` WRITE;
/*!40000 ALTER TABLE `propertycategories` DISABLE KEYS */;
INSERT INTO `propertycategories` VALUES (1,'Shack',1,500);
INSERT INTO `propertycategories` VALUES (2,'House',0,20000);
INSERT INTO `propertycategories` VALUES (3,'Villa',0,1000000);
INSERT INTO `propertycategories` VALUES (4,'Downtown business estate',0,50000);
/*!40000 ALTER TABLE `propertycategories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `propertycategories_businesscategories`
--

DROP TABLE IF EXISTS `propertycategories_businesscategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `propertycategories_businesscategories` (
  `propertyCategoryId` int(11) NOT NULL,
  `businessCategoryId` int(11) NOT NULL,
  KEY `propertyCategoryId` (`propertyCategoryId`),
  KEY `businessCategoryId` (`businessCategoryId`),
  CONSTRAINT `propertycategories_businesscategories_ibfk_1` FOREIGN KEY (`businessCategoryId`) REFERENCES `businesscategories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `propertycategories_businesscategories_ibfk_2` FOREIGN KEY (`propertyCategoryId`) REFERENCES `propertycategories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `propertycategories_businesscategories`
--

LOCK TABLES `propertycategories_businesscategories` WRITE;
/*!40000 ALTER TABLE `propertycategories_businesscategories` DISABLE KEYS */;
INSERT INTO `propertycategories_businesscategories` VALUES (4,2);
INSERT INTO `propertycategories_businesscategories` VALUES (2,1);
INSERT INTO `propertycategories_businesscategories` VALUES (1,1);
INSERT INTO `propertycategories_businesscategories` VALUES (3,1);
/*!40000 ALTER TABLE `propertycategories_businesscategories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `punishments`
--

DROP TABLE IF EXISTS `punishments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `punishments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `conversationReportId` int(11) DEFAULT NULL,
  `createdAt` datetime NOT NULL DEFAULT current_timestamp(),
  `endsAt` datetime DEFAULT NULL,
  `explanation` text NOT NULL,
  `punishmentType` enum('warning','temporaryBan','permanentBan') NOT NULL,
  `punishedById` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `conversationReportId` (`conversationReportId`),
  KEY `punishedById` (`punishedById`),
  CONSTRAINT `punishments_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `punishments_ibfk_2` FOREIGN KEY (`conversationReportId`) REFERENCES `conversationreports` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `punishments_ibfk_3` FOREIGN KEY (`punishedById`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `punishments`
--

LOCK TABLES `punishments` WRITE;
/*!40000 ALTER TABLE `punishments` DISABLE KEYS */;
/*!40000 ALTER TABLE `punishments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `adminRole` int(2) DEFAULT NULL,
  `agilitySkills` int(9) NOT NULL DEFAULT 0,
  `bank` bigint(21) NOT NULL,
  `boxingSkills` int(9) NOT NULL DEFAULT 0,
  `burglarySkills` int(9) NOT NULL DEFAULT 0,
  `carTheftSkills` int(9) NOT NULL DEFAULT 0,
  `cash` bigint(20) NOT NULL DEFAULT 50,
  `charismaSkills` int(9) NOT NULL DEFAULT 0,
  `depositedToday` int(9) NOT NULL DEFAULT 0,
  `drivingSkills` int(9) NOT NULL DEFAULT 0,
  `email` varchar(255) NOT NULL,
  `enduranceSkills` int(9) NOT NULL DEFAULT 0,
  `health` decimal(5,2) unsigned NOT NULL DEFAULT 100.00,
  `inJailUntil` datetime NOT NULL,
  `lastCheckedAt` datetime NOT NULL DEFAULT current_timestamp(),
  `energy` decimal(5,2) NOT NULL DEFAULT 100.00,
  `password` varchar(255) NOT NULL,
  `pistolSkills` int(9) NOT NULL DEFAULT 0,
  `resetKey` varchar(100) NOT NULL,
  `rifleSkills` int(9) NOT NULL DEFAULT 0,
  `robbingSkills` int(9) NOT NULL DEFAULT 0,
  `stealingSkills` int(9) NOT NULL DEFAULT 0,
  `strengthSkills` int(9) NOT NULL DEFAULT 0,
  `workingUntil` datetime DEFAULT NULL,
  `createdAt` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `adminrole_id` (`adminRole`),
  CONSTRAINT `id_to_user_adminrole` FOREIGN KEY (`adminRole`) REFERENCES `adminroles` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'testaccount1',NULL,0,0,101,0,0,50,0,0,0,'test1@test.com',0,100.00,'0000-00-00 00:00:00','2020-05-15 21:02:02',100.00,'$2y$10$gx/3veekeiaiGWAE8CFE0.dB0GgpHUi/5sV31lc3YSZFTTADp/uUG',0,'',0,0,0,0,NULL,'2019-02-16 20:14:36');
INSERT INTO `users` VALUES (2,'admin',4,10,103,381,0,0,8194,52,500,0,'admin@test.com',0,100.00,'2020-08-28 18:27:10','2020-09-17 22:15:11',100.00,'$2y$10$eX0GEcmxokWdNbhsUpI25.GKpDqvySB1JbvUPS7Q.hS2Fdb/TlAd.',1,'',0,0,0,0,NULL,'2019-03-02 21:39:08');
INSERT INTO `users` VALUES (4,'testaccount2',NULL,0,0,0,0,0,69,5,0,0,'test2@test.com',0,100.00,'0000-00-00 00:00:00','2020-05-15 17:09:13',98.70,'$2y$10$kCorgsWvSQczBp16VjbIn.BK7S.nG2T/itHBEjjnVtOg9m94CREnW',0,'a906e303a164fd74e00dbd2a63815bba',0,0,0,0,NULL,'2020-03-24 18:56:36');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_conversations`
--

DROP TABLE IF EXISTS `users_conversations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_conversations` (
  `userid` int(11) NOT NULL,
  `conversationid` int(11) NOT NULL,
  KEY `conversationId` (`conversationid`),
  KEY `userId` (`userid`),
  CONSTRAINT `users_conversations_ibfk_1` FOREIGN KEY (`conversationid`) REFERENCES `conversations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `users_conversations_ibfk_2` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_conversations`
--

LOCK TABLES `users_conversations` WRITE;
/*!40000 ALTER TABLE `users_conversations` DISABLE KEYS */;
INSERT INTO `users_conversations` VALUES (1,26);
INSERT INTO `users_conversations` VALUES (2,26);
INSERT INTO `users_conversations` VALUES (4,27);
INSERT INTO `users_conversations` VALUES (2,27);
INSERT INTO `users_conversations` VALUES (2,44);
INSERT INTO `users_conversations` VALUES (2,45);
INSERT INTO `users_conversations` VALUES (2,46);
INSERT INTO `users_conversations` VALUES (2,47);
INSERT INTO `users_conversations` VALUES (2,48);
INSERT INTO `users_conversations` VALUES (2,49);
INSERT INTO `users_conversations` VALUES (2,50);
INSERT INTO `users_conversations` VALUES (2,51);
INSERT INTO `users_conversations` VALUES (2,52);
INSERT INTO `users_conversations` VALUES (1,53);
INSERT INTO `users_conversations` VALUES (2,53);
INSERT INTO `users_conversations` VALUES (1,54);
INSERT INTO `users_conversations` VALUES (2,54);
/*!40000 ALTER TABLE `users_conversations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_unread_messages`
--

DROP TABLE IF EXISTS `users_unread_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_unread_messages` (
  `userid` int(11) NOT NULL,
  `messageid` int(11) NOT NULL,
  `conversationid` int(11) NOT NULL,
  KEY `userId` (`userid`),
  KEY `messageId` (`messageid`),
  KEY `conversationid` (`conversationid`),
  CONSTRAINT `users_unread_messages_ibfk_1` FOREIGN KEY (`messageid`) REFERENCES `messages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `users_unread_messages_ibfk_2` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `users_unread_messages_ibfk_3` FOREIGN KEY (`conversationid`) REFERENCES `conversations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_unread_messages`
--

LOCK TABLES `users_unread_messages` WRITE;
/*!40000 ALTER TABLE `users_unread_messages` DISABLE KEYS */;
INSERT INTO `users_unread_messages` VALUES (2,46,26);
INSERT INTO `users_unread_messages` VALUES (4,47,27);
INSERT INTO `users_unread_messages` VALUES (1,70,53);
INSERT INTO `users_unread_messages` VALUES (1,71,54);
/*!40000 ALTER TABLE `users_unread_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wearablecategories`
--

DROP TABLE IF EXISTS `wearablecategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wearablecategories` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `agilitySkillsBonus` decimal(4,2) NOT NULL DEFAULT 1.00,
  `boxingSkillsBonus` decimal(4,2) NOT NULL DEFAULT 1.00,
  `burglarySkillsBonus` decimal(4,2) NOT NULL DEFAULT 1.00,
  `carTheftSkillsBonus` decimal(4,2) NOT NULL DEFAULT 1.00,
  `charismaSkillsBonus` decimal(4,2) NOT NULL DEFAULT 1.00,
  `drivingSkillsBonus` decimal(4,2) NOT NULL DEFAULT 1.00,
  `enduranceSkillsBonus` decimal(4,2) NOT NULL DEFAULT 1.00,
  `equippedAs` enum('melee weapon','pistol','rifle','wrist') NOT NULL,
  `illegal` tinyint(1) NOT NULL DEFAULT 0,
  `pistolSkillsBonus` decimal(4,2) NOT NULL DEFAULT 1.00,
  `rifleSkillsBonus` decimal(4,2) NOT NULL DEFAULT 1.00,
  `robbingSkillsBonus` decimal(4,2) NOT NULL DEFAULT 1.00,
  `stealingSkillsBonus` decimal(4,2) NOT NULL DEFAULT 1.00,
  `strengthSkillsBonus` decimal(4,2) NOT NULL DEFAULT 1.00,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wearablecategories`
--

LOCK TABLES `wearablecategories` WRITE;
/*!40000 ALTER TABLE `wearablecategories` DISABLE KEYS */;
INSERT INTO `wearablecategories` VALUES (1,'Baseball bat',1.00,1.00,1.00,1.00,1.00,1.00,1.00,'melee weapon',1,1.00,1.00,1.00,1.00,1.10);
INSERT INTO `wearablecategories` VALUES (2,'Knife',1.20,1.00,1.00,1.00,1.00,1.00,1.00,'melee weapon',1,1.00,1.00,1.00,1.00,1.00);
INSERT INTO `wearablecategories` VALUES (3,'Glock',1.00,1.00,1.00,1.00,1.00,1.00,1.00,'pistol',1,1.20,1.00,1.00,1.00,1.00);
/*!40000 ALTER TABLE `wearablecategories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wearables`
--

DROP TABLE IF EXISTS `wearables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wearables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `equipped` tinyint(1) NOT NULL DEFAULT 0,
  `userId` int(11) DEFAULT NULL,
  `wearableCategoryId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `wearableCategoryId` (`wearableCategoryId`),
  CONSTRAINT `userId` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `wearableCategoryId` FOREIGN KEY (`wearableCategoryId`) REFERENCES `wearablecategories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wearables`
--

LOCK TABLES `wearables` WRITE;
/*!40000 ALTER TABLE `wearables` DISABLE KEYS */;
INSERT INTO `wearables` VALUES (1,1,1,1);
INSERT INTO `wearables` VALUES (3,0,1,3);
/*!40000 ALTER TABLE `wearables` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-09-17 22:15:59
