-- MySQL dump 10.13  Distrib 5.5.62, for linux-glibc2.12 (x86_64)
--
-- Host: localhost    Database: gamersplane
-- ------------------------------------------------------
-- Server version	5.5.62

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
-- Table structure for table `acpPermissions`
--

DROP TABLE IF EXISTS `acpPermissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `acpPermissions` (
  `userID` int(11) NOT NULL,
  `permission` varchar(20) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`userID`,`permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `charAutocomplete`
--

DROP TABLE IF EXISTS `charAutocomplete`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `charAutocomplete` (
  `itemID` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(10) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `searchName` varchar(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `userDefined` int(11) DEFAULT NULL,
  PRIMARY KEY (`itemID`),
  UNIQUE KEY `type` (`type`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=467 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `characterHistory`
--

DROP TABLE IF EXISTS `characterHistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `characterHistory` (
  `actionID` int(11) NOT NULL AUTO_INCREMENT,
  `characterID` int(11) NOT NULL,
  `enactedBy` int(11) NOT NULL,
  `enactedOn` datetime NOT NULL,
  `gameID` int(11) DEFAULT NULL,
  `action` varchar(30) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `additionalInfo` varchar(50) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`actionID`)
) ENGINE=InnoDB AUTO_INCREMENT=9577 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `characterLibrary`
--

DROP TABLE IF EXISTS `characterLibrary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `characterLibrary` (
  `characterID` int(11) NOT NULL,
  `inLibrary` tinyint(1) NOT NULL DEFAULT '1',
  `viewed` int(11) NOT NULL,
  PRIMARY KEY (`characterID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `characterLibrary_favorites`
--

DROP TABLE IF EXISTS `characterLibrary_favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `characterLibrary_favorites` (
  `userID` int(11) NOT NULL,
  `characterID` int(11) NOT NULL,
  `updateDate` datetime NOT NULL,
  PRIMARY KEY (`userID`,`characterID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `characters`
--

DROP TABLE IF EXISTS `characters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `characters` (
  `characterID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `label` varchar(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `charType` varchar(3) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `systemID` int(11) NOT NULL,
  `system` varchar(30) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `gameID` int(11) DEFAULT NULL,
  `approved` tinyint(1) NOT NULL,
  `retired` date DEFAULT NULL,
  PRIMARY KEY (`characterID`),
  KEY `userID` (`userID`),
  KEY `gameID` (`gameID`)
) ENGINE=InnoDB AUTO_INCREMENT=1450 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact` (
  `contactID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `date` datetime NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `subject` mediumtext COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `comment` mediumtext COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`contactID`)
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deckDraws`
--

DROP TABLE IF EXISTS `deckDraws`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `deckDraws` (
  `drawID` int(11) NOT NULL AUTO_INCREMENT,
  `postID` int(11) NOT NULL,
  `deckID` int(11) NOT NULL,
  `type` varchar(10) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `cardsDrawn` varchar(200) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `reveals` varchar(20) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `reason` varchar(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`drawID`),
  KEY `postID` (`postID`),
  KEY `deckID` (`deckID`)
) ENGINE=InnoDB AUTO_INCREMENT=1344 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deckPermissions`
--

DROP TABLE IF EXISTS `deckPermissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `deckPermissions` (
  `deckID` int(11) NOT NULL,
  `userID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`deckID`,`userID`),
  KEY `userID` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deckTypes`
--

DROP TABLE IF EXISTS `deckTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `deckTypes` (
  `short` varchar(10) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `deckSize` tinyint(4) NOT NULL,
  `class` varchar(20) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `image` varchar(20) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `extension` varchar(4) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`short`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `decks`
--

DROP TABLE IF EXISTS `decks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `decks` (
  `deckID` int(11) NOT NULL AUTO_INCREMENT,
  `gameID` int(11) NOT NULL,
  `label` varchar(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `type` varchar(10) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `deck` varchar(200) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `position` tinyint(4) NOT NULL,
  `lastShuffle` datetime NOT NULL,
  PRIMARY KEY (`deckID`),
  KEY `gameID` (`gameID`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dispatch`
--

DROP TABLE IF EXISTS `dispatch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `dispatch` (
  `url` varchar(100) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `pageID` varchar(50) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ngController` varchar(50) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `file` varchar(100) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `title` varchar(50) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_0900_ai_ci,
  `loginReq` tinyint(1) NOT NULL DEFAULT '1',
  `fixedGameMenu` tinyint(4) NOT NULL,
  `bodyClass` varchar(50) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `modalWidth` smallint(4) DEFAULT NULL,
  PRIMARY KEY (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `featsList`
--

DROP TABLE IF EXISTS `featsList`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `featsList` (
  `featID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `searchName` varchar(50) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `userDefined` int(11) DEFAULT NULL,
  PRIMARY KEY (`featID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=322 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forumAdmins`
--

DROP TABLE IF EXISTS `forumAdmins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `forumAdmins` (
  `userID` int(11) NOT NULL,
  `forumID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userID`,`forumID`),
  KEY `forumID` (`forumID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forumSubs`
--

DROP TABLE IF EXISTS `forumSubs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `forumSubs` (
  `userID` int(11) NOT NULL,
  `type` varchar(1) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `ID` int(11) NOT NULL,
  PRIMARY KEY (`userID`,`type`,`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forums`
--

DROP TABLE IF EXISTS `forums`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `forums` (
  `forumID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `description` text COLLATE utf8mb4_0900_ai_ci,
  `forumType` varchar(1) COLLATE utf8mb4_0900_ai_ci DEFAULT 'f',
  `parentID` int(11) DEFAULT NULL,
  `heritage` varchar(25) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `order` int(5) NOT NULL,
  `gameID` int(11) DEFAULT NULL,
  `threadCount` int(11) NOT NULL,
  PRIMARY KEY (`forumID`),
  UNIQUE KEY `heritage` (`heritage`),
  KEY `parentID` (`parentID`)
) ENGINE=InnoDB AUTO_INCREMENT=11369 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forums_groupMemberships`
--

DROP TABLE IF EXISTS `forums_groupMemberships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `forums_groupMemberships` (
  `groupID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  PRIMARY KEY (`groupID`,`userID`),
  KEY `userID` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forums_groups`
--

DROP TABLE IF EXISTS `forums_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `forums_groups` (
  `groupID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `status` tinyint(4) NOT NULL,
  `ownerID` int(11) NOT NULL,
  `gameID` int(11) DEFAULT NULL,
  PRIMARY KEY (`groupID`),
  KEY `ownerID` (`ownerID`)
) ENGINE=InnoDB AUTO_INCREMENT=4914 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forums_heritage`
--

DROP TABLE IF EXISTS `forums_heritage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `forums_heritage` (
  `parentID` int(11) NOT NULL,
  `childID` int(11) NOT NULL,
  PRIMARY KEY (`parentID`,`childID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `forums_permissions`
--

DROP TABLE IF EXISTS `forums_permissions`;
/*!50001 DROP VIEW IF EXISTS `forums_permissions`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `forums_permissions` (
  `type` tinyint NOT NULL,
  `typeID` tinyint NOT NULL,
  `forumID` tinyint NOT NULL,
  `read` tinyint NOT NULL,
  `write` tinyint NOT NULL,
  `editPost` tinyint NOT NULL,
  `deletePost` tinyint NOT NULL,
  `createThread` tinyint NOT NULL,
  `deleteThread` tinyint NOT NULL,
  `addPoll` tinyint NOT NULL,
  `addRolls` tinyint NOT NULL,
  `addDraws` tinyint NOT NULL,
  `moderate` tinyint NOT NULL
) ENGINE=InnoDB */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `forums_permissions_general`
--

DROP TABLE IF EXISTS `forums_permissions_general`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `forums_permissions_general` (
  `forumID` int(11) NOT NULL,
  `read` tinyint(1) NOT NULL,
  `write` tinyint(1) NOT NULL,
  `editPost` tinyint(1) NOT NULL,
  `deletePost` tinyint(1) NOT NULL,
  `createThread` tinyint(1) NOT NULL,
  `deleteThread` tinyint(1) NOT NULL,
  `addPoll` tinyint(1) NOT NULL,
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
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `forums_permissions_groups` (
  `groupID` int(11) NOT NULL,
  `forumID` int(11) NOT NULL,
  `read` tinyint(1) NOT NULL,
  `write` tinyint(1) NOT NULL,
  `editPost` tinyint(1) NOT NULL,
  `deletePost` tinyint(1) NOT NULL,
  `createThread` tinyint(1) NOT NULL,
  `deleteThread` tinyint(1) NOT NULL,
  `addPoll` tinyint(1) NOT NULL,
  `addRolls` tinyint(1) NOT NULL,
  `addDraws` tinyint(1) NOT NULL,
  `moderate` tinyint(1) NOT NULL,
  PRIMARY KEY (`groupID`,`forumID`),
  KEY `forumID` (`forumID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `forums_permissions_groups_c`
--

DROP TABLE IF EXISTS `forums_permissions_groups_c`;
/*!50001 DROP VIEW IF EXISTS `forums_permissions_groups_c`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `forums_permissions_groups_c` (
  `userID` tinyint NOT NULL,
  `forumID` tinyint NOT NULL,
  `read` tinyint NOT NULL,
  `write` tinyint NOT NULL,
  `editPost` tinyint NOT NULL,
  `deletePost` tinyint NOT NULL,
  `createThread` tinyint NOT NULL,
  `deleteThread` tinyint NOT NULL,
  `addPoll` tinyint NOT NULL,
  `addRolls` tinyint NOT NULL,
  `addDraws` tinyint NOT NULL,
  `moderate` tinyint NOT NULL
) ENGINE=InnoDB */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `forums_permissions_users`
--

DROP TABLE IF EXISTS `forums_permissions_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `forums_permissions_users` (
  `userID` int(11) NOT NULL,
  `forumID` int(11) NOT NULL,
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
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `forums_pollOptions` (
  `pollOptionID` int(11) NOT NULL AUTO_INCREMENT,
  `threadID` int(11) NOT NULL,
  `option` varchar(200) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`pollOptionID`)
) ENGINE=InnoDB AUTO_INCREMENT=359 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forums_pollVotes`
--

DROP TABLE IF EXISTS `forums_pollVotes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `forums_pollVotes` (
  `userID` int(11) NOT NULL,
  `pollOptionID` int(11) NOT NULL,
  `votedOn` datetime NOT NULL,
  PRIMARY KEY (`userID`,`pollOptionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forums_polls`
--

DROP TABLE IF EXISTS `forums_polls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `forums_polls` (
  `threadID` int(11) NOT NULL,
  `poll` varchar(200) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `optionsPerUser` tinyint(4) NOT NULL DEFAULT '1',
  `pollLength` tinyint(4) NOT NULL,
  `allowRevoting` tinyint(1) NOT NULL,
  PRIMARY KEY (`threadID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forums_readData`
--

DROP TABLE IF EXISTS `forums_readData`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `forums_readData` (
  `userID` int(11) NOT NULL,
  `forumData` mediumtext COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `threadData` mediumtext COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forums_readData_forums`
--

DROP TABLE IF EXISTS `forums_readData_forums`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `forums_readData_forums` (
  `userID` int(11) NOT NULL,
  `forumID` int(11) NOT NULL,
  `markedRead` int(11) NOT NULL,
  PRIMARY KEY (`userID`,`forumID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forums_readData_threads`
--

DROP TABLE IF EXISTS `forums_readData_threads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `forums_readData_threads` (
  `userID` int(11) NOT NULL,
  `threadID` int(11) NOT NULL,
  `lastRead` int(11) NOT NULL,
  PRIMARY KEY (`userID`,`threadID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gameHistory`
--

DROP TABLE IF EXISTS `gameHistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gameHistory` (
  `actionID` int(11) NOT NULL AUTO_INCREMENT,
  `gameID` int(11) NOT NULL,
  `enactedBy` int(11) NOT NULL,
  `enactedOn` datetime NOT NULL,
  `action` varchar(20) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `affectedType` varchar(20) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `affectedID` int(11) DEFAULT NULL,
  PRIMARY KEY (`actionID`)
) ENGINE=InnoDB AUTO_INCREMENT=2761 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gameInvites`
--

DROP TABLE IF EXISTS `gameInvites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gameInvites` (
  `gameID` int(11) NOT NULL,
  `invitedID` int(11) NOT NULL,
  `invitedOn` datetime NOT NULL,
  PRIMARY KEY (`gameID`,`invitedID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `games`
--

DROP TABLE IF EXISTS `games`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `games` (
  `gameID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `systemID` int(11) NOT NULL,
  `system` varchar(30) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `gmID` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  `postFrequency` varchar(4) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `numPlayers` tinyint(4) NOT NULL,
  `charsPerPlayer` tinyint(4) NOT NULL DEFAULT '1',
  `description` mediumtext COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `charGenInfo` mediumtext COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `forumID` int(11) DEFAULT NULL,
  `groupID` int(11) NOT NULL,
  `logForumID` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `public` tinyint(1) NOT NULL,
  `retired` datetime DEFAULT NULL,
  PRIMARY KEY (`gameID`),
  KEY `gmID` (`gmID`),
  KEY `forumID` (`forumID`),
  KEY `groupID` (`groupID`)
) ENGINE=InnoDB AUTO_INCREMENT=175 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lfg`
--

DROP TABLE IF EXISTS `lfg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `lfg` (
  `userID` int(11) NOT NULL,
  `systemID` int(11) NOT NULL,
  `system` varchar(30) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`userID`,`system`),
  UNIQUE KEY `userID` (`userID`,`system`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `loginRecords`
--

DROP TABLE IF EXISTS `loginRecords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `loginRecords` (
  `userID` int(11) NOT NULL,
  `attemptStamp` datetime NOT NULL,
  `ipAddress` varchar(15) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `successful` tinyint(1) NOT NULL,
  PRIMARY KEY (`userID`,`attemptStamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mapData`
--

DROP TABLE IF EXISTS `mapData`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mapData` (
  `mapID` int(11) NOT NULL,
  `col` tinyint(4) NOT NULL,
  `row` tinyint(4) NOT NULL,
  `data` mediumtext COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`mapID`,`col`,`row`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `maps`
--

DROP TABLE IF EXISTS `maps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `maps` (
  `mapID` int(11) NOT NULL AUTO_INCREMENT,
  `gameID` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `rows` tinyint(4) NOT NULL,
  `cols` tinyint(4) NOT NULL,
  `info` varchar(300) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `visible` tinyint(1) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`mapID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `maps_iconHistory`
--

DROP TABLE IF EXISTS `maps_iconHistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `maps_iconHistory` (
  `actionID` int(11) NOT NULL AUTO_INCREMENT,
  `iconID` int(11) NOT NULL,
  `mapID` int(11) NOT NULL,
  `enactedBy` int(11) NOT NULL,
  `enactedOn` datetime NOT NULL,
  `action` varchar(20) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `origin` varchar(10) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `destination` varchar(10) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`actionID`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `maps_icons`
--

DROP TABLE IF EXISTS `maps_icons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `maps_icons` (
  `iconID` int(11) NOT NULL AUTO_INCREMENT,
  `mapID` int(11) NOT NULL,
  `label` varchar(2) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `description` varchar(200) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `color` varchar(6) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `location` varchar(7) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`iconID`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `marvel_actionsList`
--

DROP TABLE IF EXISTS `marvel_actionsList`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `marvel_actionsList` (
  `actionID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `searchName` varchar(50) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `cost` tinyint(4) NOT NULL,
  `magic` tinyint(1) NOT NULL,
  `source` varchar(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `userDefined` int(11) DEFAULT NULL,
  PRIMARY KEY (`actionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `marvel_modifiersList`
--

DROP TABLE IF EXISTS `marvel_modifiersList`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `marvel_modifiersList` (
  `modifierID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `searchName` varchar(50) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `cost` float NOT NULL,
  `costTo` varchar(5) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `multipleAllowed` varchar(40) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `source` varchar(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `userDefined` int(11) DEFAULT NULL,
  PRIMARY KEY (`modifierID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `notificationID` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(20) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `enactedBy` int(11) NOT NULL,
  `enactedOn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actedUpon` int(11) NOT NULL,
  PRIMARY KEY (`notificationID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `players`
--

DROP TABLE IF EXISTS `players`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `players` (
  `gameID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `approved` tinyint(1) NOT NULL,
  `isGM` tinyint(1) NOT NULL,
  `primaryGM` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`gameID`,`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pms`
--

DROP TABLE IF EXISTS `pms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pms` (
  `pmID` int(11) NOT NULL AUTO_INCREMENT,
  `recipientID` int(11) NOT NULL,
  `senderID` int(11) NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `message` mediumtext COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `datestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `viewed` tinyint(1) NOT NULL,
  `replyTo` int(11) NOT NULL,
  PRIMARY KEY (`pmID`),
  KEY `recipientID` (`recipientID`),
  KEY `senderID` (`senderID`)
) ENGINE=InnoDB AUTO_INCREMENT=671 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `posts` (
  `postID` int(11) NOT NULL AUTO_INCREMENT,
  `threadID` int(11) NOT NULL,
  `title` varchar(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `authorID` int(11) NOT NULL,
  `message` mediumtext COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `datePosted` datetime NOT NULL,
  `lastEdit` datetime NOT NULL,
  `timesEdited` tinyint(4) NOT NULL,
  `postAs` int(11) DEFAULT NULL,
  `messageFullText` mediumtext COLLATE utf8mb4_0900_ai_ci,
  PRIMARY KEY (`postID`),
  KEY `threadID` (`threadID`),
  KEY `authorID` (`authorID`),
  FULLTEXT KEY `messageFullText` (`messageFullText`)
) ENGINE=InnoDB AUTO_INCREMENT=1664352 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `privilages`
--

DROP TABLE IF EXISTS `privilages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `privilages` (
  `userID` int(11) NOT NULL,
  `privilage` varchar(20) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`userID`,`privilage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `referralLinks`
--

DROP TABLE IF EXISTS `referralLinks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `referralLinks` (
  `key` varchar(255) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `link` varchar(255) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rolls`
--

DROP TABLE IF EXISTS `rolls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `rolls` (
  `rollID` int(11) NOT NULL AUTO_INCREMENT,
  `postID` int(11) NOT NULL,
  `type` varchar(12) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `reason` varchar(100) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `roll` varchar(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `indivRolls` varchar(500) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `results` varchar(250) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `visibility` tinyint(1) NOT NULL,
  `extras` varchar(20) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`rollID`),
  KEY `postID` (`postID`)
) ENGINE=InnoDB AUTO_INCREMENT=469514 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `skillsList`
--

DROP TABLE IF EXISTS `skillsList`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `skillsList` (
  `skillID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `searchName` varchar(50) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `userDefined` int(11) DEFAULT NULL,
  PRIMARY KEY (`skillID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=150 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `spycraft2_focusesList`
--

DROP TABLE IF EXISTS `spycraft2_focusesList`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `spycraft2_focusesList` (
  `focusID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `searchName` varchar(50) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `userDefined` int(11) DEFAULT NULL,
  PRIMARY KEY (`focusID`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `starwarsffg_talentsList`
--

DROP TABLE IF EXISTS `starwarsffg_talentsList`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `starwarsffg_talentsList` (
  `talentID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `searchName` varchar(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `userDefined` int(11) DEFAULT NULL,
  PRIMARY KEY (`talentID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=141 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `system_charAutocomplete_map`
--

DROP TABLE IF EXISTS `system_charAutocomplete_map`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_charAutocomplete_map` (
  `systemID` int(11) NOT NULL,
  `system` varchar(30) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `itemID` int(11) NOT NULL,
  PRIMARY KEY (`systemID`,`itemID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `systems`
--

DROP TABLE IF EXISTS `systems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `systems` (
  `systemID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(20) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `fullName` varchar(40) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `angular` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`systemID`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `threads`
--

DROP TABLE IF EXISTS `threads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `threads` (
  `threadID` int(11) NOT NULL AUTO_INCREMENT,
  `forumID` int(11) NOT NULL,
  `sticky` tinyint(1) NOT NULL,
  `locked` tinyint(1) NOT NULL,
  `allowRolls` tinyint(1) NOT NULL,
  `allowDraws` tinyint(1) NOT NULL,
  `firstPostID` int(11) NOT NULL,
  `lastPostID` int(11) NOT NULL,
  `postCount` int(11) NOT NULL,
  `publicPosting` tinyint(1) NOT NULL,
  `discordWebhook` varchar(256) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`threadID`),
  KEY `forumID` (`forumID`)
) ENGINE=InnoDB AUTO_INCREMENT=33651 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `threads_relPosts`
--

DROP TABLE IF EXISTS `threads_relPosts`;
/*!50001 DROP VIEW IF EXISTS `threads_relPosts`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `threads_relPosts` (
  `threadID` tinyint NOT NULL,
  `firstPostID` tinyint NOT NULL,
  `lastPostID` tinyint NOT NULL
) ENGINE=InnoDB */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `userAddedItems`
--

DROP TABLE IF EXISTS `userAddedItems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `userAddedItems` (
  `uItemID` int(11) NOT NULL AUTO_INCREMENT,
  `itemType` varchar(10) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `itemID` int(11) DEFAULT NULL,
  `addedBy` int(11) NOT NULL,
  `addedOn` datetime NOT NULL,
  `systemID` int(11) DEFAULT NULL,
  `system` varchar(30) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `action` varchar(10) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `actedBy` int(11) DEFAULT NULL,
  `actedOn` datetime DEFAULT NULL,
  PRIMARY KEY (`uItemID`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `itemID` (`itemID`,`systemID`),
  UNIQUE KEY `name_2` (`name`),
  UNIQUE KEY `itemID_2` (`itemID`,`systemID`)
) ENGINE=InnoDB AUTO_INCREMENT=834 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userHistory`
--

DROP TABLE IF EXISTS `userHistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `userHistory` (
  `actionID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `enactedBy` int(11) NOT NULL,
  `enactedOn` datetime NOT NULL,
  `action` varchar(30) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `additionalInfo` varchar(50) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`actionID`)
) ENGINE=InnoDB AUTO_INCREMENT=929 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `userPosts`
--

DROP TABLE IF EXISTS `userPosts`;
/*!50001 DROP VIEW IF EXISTS `userPosts`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `userPosts` (
  `userID` tinyint NOT NULL,
  `numPosts` tinyint NOT NULL
) ENGINE=InnoDB */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `usermeta`
--

DROP TABLE IF EXISTS `usermeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `usermeta` (
  `metaID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `metaKey` varchar(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `metaValue` longtext COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `autoload` tinyint(1) NOT NULL,
  PRIMARY KEY (`metaID`),
  UNIQUE KEY `userID` (`userID`,`metaKey`),
  KEY `autoload` (`autoload`)
) ENGINE=InnoDB AUTO_INCREMENT=150004 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(24) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(64) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `salt` varchar(20) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `joinDate` datetime NOT NULL,
  `activatedOn` datetime DEFAULT NULL,
  `lastActivity` datetime DEFAULT NULL,
  `reference` mediumtext COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `enableFilter` tinyint(1) NOT NULL DEFAULT '1',
  `showAvatars` tinyint(1) NOT NULL DEFAULT '1',
  `avatarExt` varchar(3) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `timezone` varchar(20) COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'Europe/London',
  `showTZ` tinyint(1) NOT NULL,
  `realName` varchar(50) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `gender` varchar(1) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `birthday` date DEFAULT NULL,
  `showAge` tinyint(1) NOT NULL,
  `location` varchar(100) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `aim` varchar(50) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `gmail` varchar(50) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `twitter` varchar(50) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `stream` varchar(50) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `games` varchar(200) COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `newGameMail` tinyint(1) NOT NULL DEFAULT '1',
  `postSide` varchar(1) COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'r',
  `suspendedUntil` datetime DEFAULT NULL,
  `banned` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`userID`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=19767 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wordFilter`
--

DROP TABLE IF EXISTS `wordFilter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wordFilter` (
  `wordID` int(11) NOT NULL AUTO_INCREMENT,
  `word` varchar(50) COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `spam` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`wordID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Final view structure for view `forums_permissions`
--

/*!50001 DROP TABLE IF EXISTS `forums_permissions`*/;
/*!50001 DROP VIEW IF EXISTS `forums_permissions`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`gamersplane`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `forums_permissions` AS select 'general' AS `type`,0 AS `typeID`,`forums_permissions_general`.`forumID` AS `forumID`,`forums_permissions_general`.`read` AS `read`,`forums_permissions_general`.`write` AS `write`,`forums_permissions_general`.`editPost` AS `editPost`,`forums_permissions_general`.`deletePost` AS `deletePost`,`forums_permissions_general`.`createThread` AS `createThread`,`forums_permissions_general`.`deleteThread` AS `deleteThread`,`forums_permissions_general`.`addPoll` AS `addPoll`,`forums_permissions_general`.`addRolls` AS `addRolls`,`forums_permissions_general`.`addDraws` AS `addDraws`,`forums_permissions_general`.`moderate` AS `moderate` from `forums_permissions_general` union select 'group' AS `type`,`forums_permissions_groups`.`groupID` AS `typeID`,`forums_permissions_groups`.`forumID` AS `forumID`,`forums_permissions_groups`.`read` AS `read`,`forums_permissions_groups`.`write` AS `write`,`forums_permissions_groups`.`editPost` AS `editPost`,`forums_permissions_groups`.`deletePost` AS `deletePost`,`forums_permissions_groups`.`createThread` AS `createThread`,`forums_permissions_groups`.`deleteThread` AS `deleteThread`,`forums_permissions_groups`.`addPoll` AS `addPoll`,`forums_permissions_groups`.`addRolls` AS `addRolls`,`forums_permissions_groups`.`addDraws` AS `addDraws`,`forums_permissions_groups`.`moderate` AS `moderate` from `forums_permissions_groups` union select 'user' AS `type`,`forums_permissions_users`.`userID` AS `typeID`,`forums_permissions_users`.`forumID` AS `forumID`,`forums_permissions_users`.`read` AS `read`,`forums_permissions_users`.`write` AS `write`,`forums_permissions_users`.`editPost` AS `editPost`,`forums_permissions_users`.`deletePost` AS `deletePost`,`forums_permissions_users`.`createThread` AS `createThread`,`forums_permissions_users`.`deleteThread` AS `deleteThread`,`forums_permissions_users`.`addPoll` AS `addPoll`,`forums_permissions_users`.`addRolls` AS `addRolls`,`forums_permissions_users`.`addDraws` AS `addDraws`,`forums_permissions_users`.`moderate` AS `moderate` from `forums_permissions_users` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `forums_permissions_groups_c`
--

/*!50001 DROP TABLE IF EXISTS `forums_permissions_groups_c`*/;
/*!50001 DROP VIEW IF EXISTS `forums_permissions_groups_c`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`gamersplane`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `forums_permissions_groups_c` AS select `gm`.`userID` AS `userID`,`gp`.`forumID` AS `forumID`,if((max(`gp`.`read`) = 2),2,min(`gp`.`read`)) AS `read`,if((max(`gp`.`write`) = 2),2,min(`gp`.`write`)) AS `write`,if((max(`gp`.`editPost`) = 2),2,min(`gp`.`editPost`)) AS `editPost`,if((max(`gp`.`deletePost`) = 2),2,min(`gp`.`deletePost`)) AS `deletePost`,if((max(`gp`.`createThread`) = 2),2,min(`gp`.`createThread`)) AS `createThread`,if((max(`gp`.`deleteThread`) = 2),2,min(`gp`.`deleteThread`)) AS `deleteThread`,if((max(`gp`.`addPoll`) = 2),2,min(`gp`.`addPoll`)) AS `addPoll`,if((max(`gp`.`addRolls`) = 2),2,min(`gp`.`addRolls`)) AS `addRolls`,if((max(`gp`.`addDraws`) = 2),2,min(`gp`.`addDraws`)) AS `addDraws`,if((max(`gp`.`moderate`) = 2),2,min(`gp`.`moderate`)) AS `moderate` from (`forums_groupMemberships` `gm` join `forums_permissions_groups` `gp` on((`gm`.`groupID` = `gp`.`groupID`))) group by `gp`.`forumID`,`gm`.`userID` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `threads_relPosts`
--

/*!50001 DROP TABLE IF EXISTS `threads_relPosts`*/;
/*!50001 DROP VIEW IF EXISTS `threads_relPosts`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`gamersplane`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `threads_relPosts` AS select `posts`.`threadID` AS `threadID`,min(`posts`.`postID`) AS `firstPostID`,max(`posts`.`postID`) AS `lastPostID` from `posts` group by `posts`.`threadID` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `userPosts`
--

/*!50001 DROP TABLE IF EXISTS `userPosts`*/;
/*!50001 DROP VIEW IF EXISTS `userPosts`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
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

-- Dump completed on 2024-06-29  2:50:25
