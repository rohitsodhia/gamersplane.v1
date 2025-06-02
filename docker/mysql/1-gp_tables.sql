-- MySQL dump 10.13  Distrib 8.4.2, for Linux (x86_64)
--
-- Host: localhost    Database: gamersplane
-- ------------------------------------------------------
-- Server version	8.4.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `acpPermissions`
--

DROP TABLE IF EXISTS `acpPermissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `acpPermissions` (
  `userID` int NOT NULL,
  `permission` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`userID`,`permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `charAutocomplete`
--

DROP TABLE IF EXISTS `charAutocomplete`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `charAutocomplete` (
  `itemID` int NOT NULL AUTO_INCREMENT,
  `type` varchar(30) NOT NULL,
  `name` varchar(100) NOT NULL,
  `searchName` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `userDefined` int DEFAULT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `approvedBy` int DEFAULT NULL,
  `approvedOn` datetime DEFAULT NULL,
  PRIMARY KEY (`itemID`),
  UNIQUE KEY `type` (`type`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `charAutocomplete_systems`
--

DROP TABLE IF EXISTS `charAutocomplete_systems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `charAutocomplete_systems` (
  `itemID` int NOT NULL,
  `system` varchar(200) NOT NULL,
  PRIMARY KEY (`itemID`,`system`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `characterHistory`
--

DROP TABLE IF EXISTS `characterHistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `characterHistory` (
  `actionID` int NOT NULL AUTO_INCREMENT,
  `characterID` int NOT NULL,
  `enactedBy` int NOT NULL,
  `enactedOn` datetime NOT NULL,
  `gameID` int DEFAULT NULL,
  `action` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `additionalInfo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`actionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `characterLibrary_favorites`
--

DROP TABLE IF EXISTS `characterLibrary_favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `characterLibrary_favorites` (
  `userID` int NOT NULL,
  `characterID` int NOT NULL,
  PRIMARY KEY (`userID`,`characterID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `characters`
--

DROP TABLE IF EXISTS `characters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `characters` (
  `characterID` int NOT NULL AUTO_INCREMENT,
  `userID` int NOT NULL,
  `label` varchar(100) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `charType` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `system` varchar(20) NOT NULL,
  `data` json NOT NULL,
  `gameID` int DEFAULT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `inLibrary` tinyint(1) NOT NULL DEFAULT '0',
  `libraryViews` int NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `retired` date DEFAULT NULL,
  PRIMARY KEY (`characterID`),
  KEY `userID` (`userID`),
  KEY `gameID` (`gameID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact` (
  `contactID` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `date` datetime NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `subject` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `comment` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`contactID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deckDraws`
--

DROP TABLE IF EXISTS `deckDraws`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `deckDraws` (
  `drawID` int NOT NULL AUTO_INCREMENT,
  `postID` int NOT NULL,
  `deckID` int NOT NULL,
  `type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `cardsDrawn` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `reveals` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `reason` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`drawID`),
  KEY `postID` (`postID`),
  KEY `deckID` (`deckID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deckPermissions`
--

DROP TABLE IF EXISTS `deckPermissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `deckPermissions` (
  `deckID` int NOT NULL,
  `userID` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`deckID`,`userID`),
  KEY `userID` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deckTypes`
--

DROP TABLE IF EXISTS `deckTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `deckTypes` (
  `short` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `deckSize` tinyint NOT NULL,
  `class` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `image` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `extension` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`short`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `decks`
--

DROP TABLE IF EXISTS `decks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `decks` (
  `deckID` int NOT NULL AUTO_INCREMENT,
  `gameID` int NOT NULL,
  `label` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `deck` json NOT NULL,
  `position` tinyint NOT NULL,
  `lastShuffle` datetime NOT NULL,
  PRIMARY KEY (`deckID`),
  KEY `gameID` (`gameID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dispatch`
--

DROP TABLE IF EXISTS `dispatch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dispatch` (
  `url` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `pageID` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ngController` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `file` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `loginReq` tinyint(1) NOT NULL DEFAULT '1',
  `fixedGameMenu` tinyint NOT NULL,
  `bodyClass` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `modalWidth` smallint DEFAULT NULL,
  PRIMARY KEY (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `faqs`
--

DROP TABLE IF EXISTS `faqs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `faqs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category` varchar(40) NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `order` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `featsList`
--

DROP TABLE IF EXISTS `featsList`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `featsList` (
  `featID` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `searchName` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `userDefined` int DEFAULT NULL,
  PRIMARY KEY (`featID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forumAdmins`
--

DROP TABLE IF EXISTS `forumAdmins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forumAdmins` (
  `userID` int NOT NULL,
  `forumID` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`userID`,`forumID`),
  KEY `forumID` (`forumID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forumNotifications`
--

DROP TABLE IF EXISTS `forumNotifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forumNotifications` (
  `userID` int NOT NULL,
  `threadID` int NOT NULL,
  `postID` int NOT NULL,
  `notificationType` int NOT NULL,
  PRIMARY KEY (`userID`,`threadID`,`postID`,`notificationType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forumSubs`
--

DROP TABLE IF EXISTS `forumSubs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forumSubs` (
  `userID` int NOT NULL,
  `type` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `ID` int NOT NULL,
  PRIMARY KEY (`userID`,`type`,`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forums`
--

DROP TABLE IF EXISTS `forums`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forums` (
  `forumID` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `forumType` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'f',
  `parentID` int DEFAULT NULL,
  `order` int NOT NULL,
  `gameID` int DEFAULT NULL,
  `threadCount` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`forumID`),
  KEY `parentID` (`parentID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forums_groupMemberships`
--

DROP TABLE IF EXISTS `forums_groupMemberships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forums_groupMemberships` (
  `groupID` int NOT NULL,
  `userID` int NOT NULL,
  PRIMARY KEY (`groupID`,`userID`),
  KEY `userID` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forums_groups`
--

DROP TABLE IF EXISTS `forums_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forums_groups` (
  `groupID` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `ownerID` int NOT NULL,
  `gameID` int DEFAULT NULL,
  PRIMARY KEY (`groupID`),
  KEY `ownerID` (`ownerID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `forums_permissions`
--

DROP TABLE IF EXISTS `forums_permissions`;
/*!50001 DROP VIEW IF EXISTS `forums_permissions`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `forums_permissions` AS SELECT
 1 AS `type`,
 1 AS `typeID`,
 1 AS `forumID`,
 1 AS `read`,
 1 AS `write`,
 1 AS `editPost`,
 1 AS `deletePost`,
 1 AS `createThread`,
 1 AS `deleteThread`,
 1 AS `addPoll`,
 1 AS `addRolls`,
 1 AS `addDraws`,
 1 AS `moderate`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `forums_permissions_general`
--

DROP TABLE IF EXISTS `forums_permissions_general`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forums_permissions_general` (
  `forumID` int NOT NULL,
  `read` tinyint(1) NOT NULL DEFAULT '-1',
  `write` tinyint(1) NOT NULL DEFAULT '-1',
  `editPost` tinyint(1) NOT NULL DEFAULT '-1',
  `deletePost` tinyint(1) NOT NULL DEFAULT '-1',
  `createThread` tinyint(1) NOT NULL DEFAULT '-1',
  `deleteThread` tinyint(1) NOT NULL DEFAULT '-1',
  `addPoll` tinyint(1) NOT NULL DEFAULT '-1',
  `addRolls` tinyint(1) NOT NULL DEFAULT '-1',
  `addDraws` tinyint(1) NOT NULL DEFAULT '-1',
  `moderate` tinyint(1) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`forumID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forums_permissions_groups`
--

DROP TABLE IF EXISTS `forums_permissions_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forums_permissions_groups` (
  `groupID` int NOT NULL,
  `forumID` int NOT NULL,
  `read` tinyint(1) NOT NULL DEFAULT '-2',
  `write` tinyint(1) NOT NULL DEFAULT '-2',
  `editPost` tinyint(1) NOT NULL DEFAULT '-2',
  `deletePost` tinyint(1) NOT NULL DEFAULT '-2',
  `createThread` tinyint(1) NOT NULL DEFAULT '-2',
  `deleteThread` tinyint(1) NOT NULL DEFAULT '-2',
  `addPoll` tinyint(1) NOT NULL DEFAULT '-2',
  `addRolls` tinyint(1) NOT NULL DEFAULT '-2',
  `addDraws` tinyint(1) NOT NULL DEFAULT '-2',
  `moderate` tinyint(1) NOT NULL DEFAULT '-2',
  PRIMARY KEY (`groupID`,`forumID`),
  KEY `forumID` (`forumID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `forums_permissions_groups_c`
--

DROP TABLE IF EXISTS `forums_permissions_groups_c`;
/*!50001 DROP VIEW IF EXISTS `forums_permissions_groups_c`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `forums_permissions_groups_c` AS SELECT
 1 AS `userID`,
 1 AS `forumID`,
 1 AS `read`,
 1 AS `write`,
 1 AS `editPost`,
 1 AS `deletePost`,
 1 AS `createThread`,
 1 AS `deleteThread`,
 1 AS `addPoll`,
 1 AS `addRolls`,
 1 AS `addDraws`,
 1 AS `moderate`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `forums_permissions_users`
--

DROP TABLE IF EXISTS `forums_permissions_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forums_permissions_users` (
  `userID` int NOT NULL,
  `forumID` int NOT NULL,
  `read` tinyint(1) NOT NULL DEFAULT '0',
  `write` tinyint(1) NOT NULL DEFAULT '0',
  `editPost` tinyint(1) NOT NULL DEFAULT '0',
  `deletePost` tinyint(1) NOT NULL DEFAULT '0',
  `createThread` tinyint(1) NOT NULL DEFAULT '0',
  `deleteThread` tinyint(1) NOT NULL DEFAULT '0',
  `addPoll` tinyint(1) NOT NULL DEFAULT '0',
  `addRolls` tinyint(1) NOT NULL DEFAULT '0',
  `addDraws` tinyint(1) NOT NULL DEFAULT '0',
  `moderate` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userID`,`forumID`),
  KEY `forumID` (`forumID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forums_pollOptions`
--

DROP TABLE IF EXISTS `forums_pollOptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forums_pollOptions` (
  `pollOptionID` int NOT NULL AUTO_INCREMENT,
  `threadID` int NOT NULL,
  `option` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`pollOptionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forums_pollVotes`
--

DROP TABLE IF EXISTS `forums_pollVotes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forums_pollVotes` (
  `userID` int NOT NULL,
  `pollOptionID` int NOT NULL,
  `votedOn` datetime NOT NULL,
  PRIMARY KEY (`userID`,`pollOptionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forums_polls`
--

DROP TABLE IF EXISTS `forums_polls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forums_polls` (
  `threadID` int NOT NULL,
  `poll` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `optionsPerUser` tinyint NOT NULL DEFAULT '1',
  `pollLength` tinyint DEFAULT NULL,
  `allowRevoting` tinyint(1) NOT NULL,
  PRIMARY KEY (`threadID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forums_postFFGFlips`
--

DROP TABLE IF EXISTS `forums_postFFGFlips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forums_postFFGFlips` (
  `flipID` int NOT NULL AUTO_INCREMENT,
  `postID` int NOT NULL,
  `userID` int NOT NULL,
  `toDark` int NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`flipID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forums_postPollVotes`
--

DROP TABLE IF EXISTS `forums_postPollVotes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forums_postPollVotes` (
  `postID` int NOT NULL,
  `userID` int NOT NULL,
  `vote` int NOT NULL,
  PRIMARY KEY (`postID`,`userID`,`vote`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forums_readData`
--

DROP TABLE IF EXISTS `forums_readData`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forums_readData` (
  `userID` int NOT NULL,
  `forumData` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `threadData` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forums_readData_forums`
--

DROP TABLE IF EXISTS `forums_readData_forums`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forums_readData_forums` (
  `userID` int NOT NULL,
  `forumID` int NOT NULL,
  `markedRead` int NOT NULL,
  PRIMARY KEY (`userID`,`forumID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forums_readData_threads`
--

DROP TABLE IF EXISTS `forums_readData_threads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forums_readData_threads` (
  `userID` int NOT NULL,
  `threadID` int NOT NULL,
  `lastRead` int NOT NULL,
  PRIMARY KEY (`userID`,`threadID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gameHistory`
--

DROP TABLE IF EXISTS `gameHistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gameHistory` (
  `actionID` int NOT NULL AUTO_INCREMENT,
  `gameID` int NOT NULL,
  `enactedBy` int NOT NULL,
  `enactedOn` datetime NOT NULL,
  `action` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `affectedType` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `affectedID` int DEFAULT NULL,
  PRIMARY KEY (`actionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gameInvites`
--

DROP TABLE IF EXISTS `gameInvites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gameInvites` (
  `gameID` int NOT NULL,
  `userID` int NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`gameID`,`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `games`
--

DROP TABLE IF EXISTS `games`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `games` (
  `gameID` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `system` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `customSystem` varchar(100) DEFAULT NULL,
  `gmID` int NOT NULL,
  `created` datetime NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime DEFAULT NULL,
  `postFrequency` json NOT NULL,
  `numPlayers` tinyint NOT NULL,
  `charsPerPlayer` tinyint NOT NULL DEFAULT '1',
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `charGenInfo` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `forumID` int DEFAULT NULL,
  `groupID` int NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `public` tinyint(1) NOT NULL,
  `retired` datetime DEFAULT NULL,
  `allowedCharSheets` json NOT NULL,
  `gameOptions` json DEFAULT NULL,
  `recruitmentThreadId` int DEFAULT NULL,
  PRIMARY KEY (`gameID`),
  KEY `gmID` (`gmID`),
  KEY `forumID` (`forumID`),
  KEY `groupID` (`groupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `games_favorites`
--

DROP TABLE IF EXISTS `games_favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `games_favorites` (
  `userID` int NOT NULL,
  `gameID` int NOT NULL,
  PRIMARY KEY (`userID`,`gameID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lfg`
--

DROP TABLE IF EXISTS `lfg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lfg` (
  `userID` int NOT NULL,
  `system` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`userID`,`system`),
  UNIQUE KEY `userID` (`userID`,`system`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `loginRecords`
--

DROP TABLE IF EXISTS `loginRecords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `loginRecords` (
  `userID` int NOT NULL,
  `attemptStamp` datetime NOT NULL,
  `ipAddress` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `successful` tinyint(1) NOT NULL,
  PRIMARY KEY (`userID`,`attemptStamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mapData`
--

DROP TABLE IF EXISTS `mapData`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mapData` (
  `mapID` int NOT NULL,
  `col` tinyint NOT NULL,
  `row` tinyint NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`mapID`,`col`,`row`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `maps`
--

DROP TABLE IF EXISTS `maps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `maps` (
  `mapID` int NOT NULL AUTO_INCREMENT,
  `gameID` int NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `rows` tinyint NOT NULL,
  `cols` tinyint NOT NULL,
  `info` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `visible` tinyint(1) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`mapID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `maps_iconHistory`
--

DROP TABLE IF EXISTS `maps_iconHistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `maps_iconHistory` (
  `actionID` int NOT NULL AUTO_INCREMENT,
  `iconID` int NOT NULL,
  `mapID` int NOT NULL,
  `enactedBy` int NOT NULL,
  `enactedOn` datetime NOT NULL,
  `action` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `origin` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `destination` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`actionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `maps_icons`
--

DROP TABLE IF EXISTS `maps_icons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `maps_icons` (
  `iconID` int NOT NULL AUTO_INCREMENT,
  `mapID` int NOT NULL,
  `label` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `description` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `color` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `location` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`iconID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `marvel_actionsList`
--

DROP TABLE IF EXISTS `marvel_actionsList`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marvel_actionsList` (
  `actionID` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `searchName` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `cost` tinyint NOT NULL,
  `magic` tinyint(1) NOT NULL,
  `source` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `userDefined` int DEFAULT NULL,
  PRIMARY KEY (`actionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `marvel_modifiersList`
--

DROP TABLE IF EXISTS `marvel_modifiersList`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marvel_modifiersList` (
  `modifierID` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `searchName` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `cost` float NOT NULL,
  `costTo` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `multipleAllowed` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `source` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `userDefined` int DEFAULT NULL,
  PRIMARY KEY (`modifierID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `music`
--

DROP TABLE IF EXISTS `music`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `music` (
  `id` int NOT NULL AUTO_INCREMENT,
  `url` text NOT NULL,
  `title` varchar(200) NOT NULL,
  `lyrics` tinyint(1) NOT NULL DEFAULT '0',
  `genres` json DEFAULT NULL,
  `notes` text,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `user` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `notificationID` int NOT NULL AUTO_INCREMENT,
  `category` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `enactedBy` int NOT NULL,
  `enactedOn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actedUpon` int NOT NULL,
  PRIMARY KEY (`notificationID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `players`
--

DROP TABLE IF EXISTS `players`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `players` (
  `gameID` int NOT NULL,
  `userID` int NOT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `isGM` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`gameID`,`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pms`
--

DROP TABLE IF EXISTS `pms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pms` (
  `pmID` int NOT NULL AUTO_INCREMENT,
  `recipientID` int NOT NULL,
  `senderID` int NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` mediumtext NOT NULL,
  `datestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `recipientRead` tinyint(1) NOT NULL DEFAULT '0',
  `senderRead` tinyint(1) NOT NULL DEFAULT '0',
  `replyTo` int DEFAULT NULL,
  `recipientDeleted` tinyint(1) NOT NULL DEFAULT '0',
  `senderDeleted` tinyint(1) NOT NULL DEFAULT '0',
  `history` json DEFAULT NULL,
  PRIMARY KEY (`pmID`),
  KEY `recipientID` (`recipientID`),
  KEY `senderID` (`senderID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `posts` (
  `postID` int NOT NULL AUTO_INCREMENT,
  `threadID` int NOT NULL,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `authorID` int NOT NULL,
  `message` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `datePosted` datetime NOT NULL,
  `lastEdit` datetime DEFAULT NULL,
  `timesEdited` tinyint NOT NULL DEFAULT '0',
  `postAs` int DEFAULT NULL,
  `messageFullText` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`postID`),
  KEY `threadID` (`threadID`),
  KEY `authorID` (`authorID`),
  FULLTEXT KEY `messageFullText` (`messageFullText`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `privilages`
--

DROP TABLE IF EXISTS `privilages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `privilages` (
  `userID` int NOT NULL,
  `privilage` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`userID`,`privilage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `referralLinks`
--

DROP TABLE IF EXISTS `referralLinks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `referralLinks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `order` int DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rolls`
--

DROP TABLE IF EXISTS `rolls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rolls` (
  `rollID` int NOT NULL AUTO_INCREMENT,
  `postID` int NOT NULL,
  `type` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `reason` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `roll` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `indivRolls` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `results` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `visibility` tinyint(1) NOT NULL,
  `extras` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`rollID`),
  KEY `postID` (`postID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `skillsList`
--

DROP TABLE IF EXISTS `skillsList`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `skillsList` (
  `skillID` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `searchName` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `userDefined` int DEFAULT NULL,
  PRIMARY KEY (`skillID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `spycraft2_focusesList`
--

DROP TABLE IF EXISTS `spycraft2_focusesList`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `spycraft2_focusesList` (
  `focusID` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `searchName` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `userDefined` int DEFAULT NULL,
  PRIMARY KEY (`focusID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `starwarsffg_talentsList`
--

DROP TABLE IF EXISTS `starwarsffg_talentsList`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `starwarsffg_talentsList` (
  `talentID` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `searchName` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `userDefined` int DEFAULT NULL,
  PRIMARY KEY (`talentID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_charAutocomplete_map`
--

DROP TABLE IF EXISTS `system_charAutocomplete_map`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_charAutocomplete_map` (
  `systemID` int NOT NULL,
  `system` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `itemID` int NOT NULL,
  PRIMARY KEY (`systemID`,`itemID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `systems`
--

DROP TABLE IF EXISTS `systems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `systems` (
  `id` varchar(20) NOT NULL,
  `name` varchar(40) NOT NULL,
  `sortName` varchar(40) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `angular` tinyint(1) NOT NULL DEFAULT '1',
  `genres` json DEFAULT NULL,
  `publisher` json DEFAULT NULL,
  `basics` json DEFAULT NULL,
  `hasCharSheet` tinyint(1) NOT NULL DEFAULT '1',
  `lfg` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `threads`
--

DROP TABLE IF EXISTS `threads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `threads` (
  `threadID` int NOT NULL AUTO_INCREMENT,
  `forumID` int NOT NULL,
  `sticky` tinyint(1) NOT NULL,
  `locked` tinyint(1) NOT NULL,
  `allowRolls` tinyint(1) NOT NULL,
  `allowDraws` tinyint(1) NOT NULL,
  `firstPostID` int NOT NULL,
  `lastPostID` int NOT NULL,
  `postCount` int NOT NULL,
  `publicPosting` tinyint(1) NOT NULL,
  `discordWebhook` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`threadID`),
  KEY `forumID` (`forumID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `threads_relPosts`
--

DROP TABLE IF EXISTS `threads_relPosts`;
/*!50001 DROP VIEW IF EXISTS `threads_relPosts`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `threads_relPosts` AS SELECT
 1 AS `threadID`,
 1 AS `firstPostID`,
 1 AS `lastPostID`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `userAddedItems`
--

DROP TABLE IF EXISTS `userAddedItems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `userAddedItems` (
  `uItemID` int NOT NULL AUTO_INCREMENT,
  `itemType` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `itemID` int DEFAULT NULL,
  `addedBy` int NOT NULL,
  `addedOn` datetime NOT NULL,
  `systemID` int DEFAULT NULL,
  `system` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `action` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `actedBy` int DEFAULT NULL,
  `actedOn` datetime DEFAULT NULL,
  PRIMARY KEY (`uItemID`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `itemID` (`itemID`,`systemID`),
  UNIQUE KEY `name_2` (`name`),
  UNIQUE KEY `itemID_2` (`itemID`,`systemID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userHistory`
--

DROP TABLE IF EXISTS `userHistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `userHistory` (
  `actionID` int NOT NULL AUTO_INCREMENT,
  `userID` int NOT NULL,
  `enactedBy` int NOT NULL,
  `enactedOn` datetime NOT NULL,
  `action` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `additionalInfo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`actionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `userPosts`
--

DROP TABLE IF EXISTS `userPosts`;
/*!50001 DROP VIEW IF EXISTS `userPosts`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `userPosts` AS SELECT
 1 AS `userID`,
 1 AS `numPosts`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `usermeta`
--

DROP TABLE IF EXISTS `usermeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usermeta` (
  `metaID` int NOT NULL AUTO_INCREMENT,
  `userID` int NOT NULL,
  `metaKey` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `metaValue` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `autoload` tinyint(1) NOT NULL,
  PRIMARY KEY (`metaID`),
  UNIQUE KEY `userID` (`userID`,`metaKey`),
  KEY `autoload` (`autoload`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `userID` int NOT NULL AUTO_INCREMENT,
  `username` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `salt` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `joinDate` datetime NOT NULL,
  `activatedOn` datetime DEFAULT NULL,
  `lastActivity` datetime DEFAULT NULL,
  `enableFilter` tinyint(1) NOT NULL DEFAULT '1',
  `timezone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'Europe/London',
  `suspendedUntil` datetime DEFAULT NULL,
  `banned` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userID`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wordFilter`
--

DROP TABLE IF EXISTS `wordFilter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wordFilter` (
  `wordID` int NOT NULL AUTO_INCREMENT,
  `word` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `spam` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`wordID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Final view structure for view `forums_permissions`
--

/*!50001 DROP VIEW IF EXISTS `forums_permissions`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb3 */;
/*!50001 SET character_set_results     = utf8mb3 */;
/*!50001 SET collation_connection      = utf8mb3_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`gamersplane`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `forums_permissions` AS select 'general' AS `type`,0 AS `typeID`,`forums_permissions_general`.`forumID` AS `forumID`,`forums_permissions_general`.`read` AS `read`,`forums_permissions_general`.`write` AS `write`,`forums_permissions_general`.`editPost` AS `editPost`,`forums_permissions_general`.`deletePost` AS `deletePost`,`forums_permissions_general`.`createThread` AS `createThread`,`forums_permissions_general`.`deleteThread` AS `deleteThread`,`forums_permissions_general`.`addPoll` AS `addPoll`,`forums_permissions_general`.`addRolls` AS `addRolls`,`forums_permissions_general`.`addDraws` AS `addDraws`,`forums_permissions_general`.`moderate` AS `moderate` from `forums_permissions_general` union select 'group' AS `type`,`forums_permissions_groups`.`groupID` AS `typeID`,`forums_permissions_groups`.`forumID` AS `forumID`,`forums_permissions_groups`.`read` AS `read`,`forums_permissions_groups`.`write` AS `write`,`forums_permissions_groups`.`editPost` AS `editPost`,`forums_permissions_groups`.`deletePost` AS `deletePost`,`forums_permissions_groups`.`createThread` AS `createThread`,`forums_permissions_groups`.`deleteThread` AS `deleteThread`,`forums_permissions_groups`.`addPoll` AS `addPoll`,`forums_permissions_groups`.`addRolls` AS `addRolls`,`forums_permissions_groups`.`addDraws` AS `addDraws`,`forums_permissions_groups`.`moderate` AS `moderate` from `forums_permissions_groups` union select 'user' AS `type`,`forums_permissions_users`.`userID` AS `typeID`,`forums_permissions_users`.`forumID` AS `forumID`,`forums_permissions_users`.`read` AS `read`,`forums_permissions_users`.`write` AS `write`,`forums_permissions_users`.`editPost` AS `editPost`,`forums_permissions_users`.`deletePost` AS `deletePost`,`forums_permissions_users`.`createThread` AS `createThread`,`forums_permissions_users`.`deleteThread` AS `deleteThread`,`forums_permissions_users`.`addPoll` AS `addPoll`,`forums_permissions_users`.`addRolls` AS `addRolls`,`forums_permissions_users`.`addDraws` AS `addDraws`,`forums_permissions_users`.`moderate` AS `moderate` from `forums_permissions_users` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `forums_permissions_groups_c`
--

/*!50001 DROP VIEW IF EXISTS `forums_permissions_groups_c`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb3 */;
/*!50001 SET character_set_results     = utf8mb3 */;
/*!50001 SET collation_connection      = utf8mb3_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`gamersplane`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `forums_permissions_groups_c` AS select `gm`.`userID` AS `userID`,`gp`.`forumID` AS `forumID`,if((max(`gp`.`read`) = 2),2,min(`gp`.`read`)) AS `read`,if((max(`gp`.`write`) = 2),2,min(`gp`.`write`)) AS `write`,if((max(`gp`.`editPost`) = 2),2,min(`gp`.`editPost`)) AS `editPost`,if((max(`gp`.`deletePost`) = 2),2,min(`gp`.`deletePost`)) AS `deletePost`,if((max(`gp`.`createThread`) = 2),2,min(`gp`.`createThread`)) AS `createThread`,if((max(`gp`.`deleteThread`) = 2),2,min(`gp`.`deleteThread`)) AS `deleteThread`,if((max(`gp`.`addPoll`) = 2),2,min(`gp`.`addPoll`)) AS `addPoll`,if((max(`gp`.`addRolls`) = 2),2,min(`gp`.`addRolls`)) AS `addRolls`,if((max(`gp`.`addDraws`) = 2),2,min(`gp`.`addDraws`)) AS `addDraws`,if((max(`gp`.`moderate`) = 2),2,min(`gp`.`moderate`)) AS `moderate` from (`forums_groupMemberships` `gm` join `forums_permissions_groups` `gp` on((`gm`.`groupID` = `gp`.`groupID`))) group by `gp`.`forumID`,`gm`.`userID` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `threads_relPosts`
--

/*!50001 DROP VIEW IF EXISTS `threads_relPosts`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb3 */;
/*!50001 SET character_set_results     = utf8mb3 */;
/*!50001 SET collation_connection      = utf8mb3_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`gamersplane`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `threads_relPosts` AS select `posts`.`threadID` AS `threadID`,min(`posts`.`postID`) AS `firstPostID`,max(`posts`.`postID`) AS `lastPostID` from `posts` group by `posts`.`threadID` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `userPosts`
--

/*!50001 DROP VIEW IF EXISTS `userPosts`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb3 */;
/*!50001 SET character_set_results     = utf8mb3 */;
/*!50001 SET collation_connection      = utf8mb3_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`gamersplane`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `userPosts` AS select `posts`.`authorID` AS `userID`,count(0) AS `numPosts` from `posts` group by `posts`.`authorID` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-23 23:12:21
