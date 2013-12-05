-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 05, 2013 at 07:17 PM
-- Server version: 5.5.24-log
-- PHP Version: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `gamersplane`
--

-- --------------------------------------------------------

--
-- Table structure for table `afmbe_characters`
--

CREATE TABLE IF NOT EXISTS `afmbe_characters` (
  `characterID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `str` tinyint(4) NOT NULL,
  `dex` tinyint(4) NOT NULL,
  `con` tinyint(4) NOT NULL,
  `int` tinyint(4) NOT NULL,
  `per` tinyint(4) NOT NULL,
  `wil` tinyint(4) NOT NULL,
  `lp` tinyint(4) NOT NULL,
  `end` tinyint(4) NOT NULL,
  `spd` tinyint(4) NOT NULL,
  `ess` tinyint(4) NOT NULL,
  `qualities` text NOT NULL,
  `drawbacks` text NOT NULL,
  `skills` text NOT NULL,
  `powers` text NOT NULL,
  `weapons` text NOT NULL,
  `items` text NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`characterID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `characterHistory`
--

CREATE TABLE IF NOT EXISTS `characterHistory` (
  `actionID` int(11) NOT NULL AUTO_INCREMENT,
  `characterID` int(11) NOT NULL,
  `enactedBy` int(11) NOT NULL,
  `enactedOn` datetime NOT NULL,
  `gameID` int(11) DEFAULT NULL,
  `action` varchar(30) NOT NULL,
  `additionalInfo` varchar(50) NOT NULL,
  PRIMARY KEY (`actionID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `characters`
--

CREATE TABLE IF NOT EXISTS `characters` (
  `characterID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `label` varchar(50) NOT NULL,
  `mob` tinyint(1) NOT NULL,
  `systemID` int(11) NOT NULL,
  `gameID` int(11) DEFAULT NULL,
  `approved` tinyint(1) NOT NULL,
  `retired` tinyint(1) NOT NULL,
  PRIMARY KEY (`characterID`),
  KEY `userID` (`userID`),
  KEY `gameID` (`gameID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE IF NOT EXISTS `chat_messages` (
  `chatID` int(11) NOT NULL AUTO_INCREMENT,
  `gameID` int(11) NOT NULL,
  `posterID` int(11) NOT NULL,
  `postedOn` datetime NOT NULL,
  `message` text NOT NULL,
  `logged` tinyint(1) NOT NULL,
  PRIMARY KEY (`chatID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `chat_sessions`
--

CREATE TABLE IF NOT EXISTS `chat_sessions` (
  `gameID` int(11) NOT NULL,
  `locked` tinyint(1) NOT NULL,
  PRIMARY KEY (`gameID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `chat_users`
--

CREATE TABLE IF NOT EXISTS `chat_users` (
  `userID` int(11) NOT NULL,
  `gameID` int(11) NOT NULL,
  `lastActive` datetime NOT NULL,
  PRIMARY KEY (`userID`,`gameID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE IF NOT EXISTS `contact` (
  `contactID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `date` datetime NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `subject` text NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`contactID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cthulhu_characters`
--

CREATE TABLE IF NOT EXISTS `cthulhu_characters` (
  `characterID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `profession` varchar(100) NOT NULL,
  `level` int(11) NOT NULL,
  `str` tinyint(4) NOT NULL DEFAULT '10',
  `dex` tinyint(4) NOT NULL DEFAULT '10',
  `con` tinyint(4) NOT NULL DEFAULT '10',
  `int` tinyint(4) NOT NULL DEFAULT '10',
  `wis` tinyint(4) NOT NULL DEFAULT '10',
  `cha` tinyint(4) NOT NULL DEFAULT '10',
  `hp` tinyint(4) NOT NULL,
  `hp_current` tinyint(4) NOT NULL,
  `sanity_max` tinyint(4) NOT NULL,
  `sanity_current` tinyint(4) NOT NULL,
  `ac_armor` tinyint(4) NOT NULL,
  `ac_dex` tinyint(4) NOT NULL,
  `ac_misc` tinyint(4) NOT NULL,
  `initiative_misc` tinyint(4) NOT NULL,
  `fort_base` tinyint(4) NOT NULL,
  `fort_magic` tinyint(4) NOT NULL,
  `fort_misc` tinyint(4) NOT NULL,
  `ref_base` tinyint(4) NOT NULL,
  `ref_magic` tinyint(4) NOT NULL,
  `ref_misc` tinyint(4) NOT NULL,
  `will_base` tinyint(4) NOT NULL,
  `will_magic` tinyint(4) NOT NULL,
  `will_misc` tinyint(4) NOT NULL,
  `bab` tinyint(4) NOT NULL,
  `melee_misc` tinyint(4) NOT NULL,
  `ranged_misc` tinyint(4) NOT NULL,
  `skills` text NOT NULL,
  `feats` text NOT NULL,
  `weapons` text NOT NULL,
  `armor` text NOT NULL,
  `items` text NOT NULL,
  `spells` text NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`characterID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `custom_characters`
--

CREATE TABLE IF NOT EXISTS `custom_characters` (
  `characterID` int(11) NOT NULL,
  `charSheet` text NOT NULL,
  PRIMARY KEY (`characterID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `deadlands_characters`
--

CREATE TABLE IF NOT EXISTS `deadlands_characters` (
  `characterID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `cogNumDice` tinyint(4) NOT NULL,
  `cogDieType` tinyint(4) NOT NULL,
  `cogSkills` text NOT NULL,
  `knoNumDice` tinyint(4) NOT NULL,
  `knoDieType` tinyint(4) NOT NULL,
  `knoSkills` text NOT NULL,
  `mieNumDice` tinyint(4) NOT NULL,
  `mieDieType` tinyint(4) NOT NULL,
  `mieSkills` text NOT NULL,
  `smaNumDice` tinyint(4) NOT NULL,
  `smaDieType` tinyint(4) NOT NULL,
  `smaSkills` text NOT NULL,
  `spiNumDice` tinyint(4) NOT NULL,
  `spiDieType` tinyint(4) NOT NULL,
  `spiSkills` text NOT NULL,
  `defNumDice` tinyint(4) NOT NULL,
  `defDieType` tinyint(4) NOT NULL,
  `defSkills` text NOT NULL,
  `nimNumDice` tinyint(4) NOT NULL,
  `nimDieType` tinyint(4) NOT NULL,
  `nimSkills` text NOT NULL,
  `strNumDice` tinyint(4) NOT NULL,
  `strDieType` tinyint(4) NOT NULL,
  `strSkills` text NOT NULL,
  `quiNumDice` tinyint(4) NOT NULL,
  `quiDieType` tinyint(4) NOT NULL,
  `quiSkills` text NOT NULL,
  `vigNumDice` tinyint(4) NOT NULL,
  `vigDieType` tinyint(4) NOT NULL,
  `vigSkills` text NOT NULL,
  `edge_hind` text NOT NULL,
  `nightmare` text NOT NULL,
  `wounds` varchar(17) NOT NULL,
  `wind` tinyint(4) NOT NULL,
  `weapons` text NOT NULL,
  `arcane` text NOT NULL,
  `equipment` text NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`characterID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `deckDraws`
--

CREATE TABLE IF NOT EXISTS `deckDraws` (
  `drawID` int(11) NOT NULL AUTO_INCREMENT,
  `postID` int(11) NOT NULL,
  `deckID` int(11) NOT NULL,
  `type` varchar(10) NOT NULL,
  `cardsDrawn` varchar(200) NOT NULL,
  `reveals` varchar(20) NOT NULL,
  `reason` varchar(50) NOT NULL,
  PRIMARY KEY (`drawID`),
  KEY `postID` (`postID`),
  KEY `deckID` (`deckID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `deckPermissions`
--

CREATE TABLE IF NOT EXISTS `deckPermissions` (
  `deckID` int(11) NOT NULL,
  `userID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`deckID`,`userID`),
  KEY `userID` (`userID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `decks`
--

CREATE TABLE IF NOT EXISTS `decks` (
  `deckID` int(11) NOT NULL AUTO_INCREMENT,
  `gameID` int(11) NOT NULL,
  `label` varchar(50) NOT NULL,
  `type` varchar(10) NOT NULL,
  `deck` varchar(200) NOT NULL,
  `position` tinyint(4) NOT NULL,
  `lastShuffle` datetime NOT NULL,
  PRIMARY KEY (`deckID`),
  KEY `gameID` (`gameID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `deckTypes`
--

CREATE TABLE IF NOT EXISTS `deckTypes` (
  `short` varchar(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `deckSize` tinyint(4) NOT NULL,
  `class` varchar(20) NOT NULL,
  `image` varchar(20) NOT NULL,
  `extension` varchar(4) NOT NULL,
  PRIMARY KEY (`short`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dispatch`
--

CREATE TABLE IF NOT EXISTS `dispatch` (
  `url` varchar(100) NOT NULL,
  `pageID` varchar(50) DEFAULT NULL,
  `file` varchar(100) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `fixedGameMenu` tinyint(4) NOT NULL,
  `bodyClass` varchar(50) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `modalWidth` smallint(4) DEFAULT NULL,
  PRIMARY KEY (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dnd3_armors`
--

CREATE TABLE IF NOT EXISTS `dnd3_armors` (
  `characterID` int(11) NOT NULL,
  `armorID` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `ac` varchar(20) NOT NULL,
  `maxDex` varchar(20) NOT NULL,
  `type` varchar(10) NOT NULL,
  `check` tinyint(4) NOT NULL,
  `spellFailure` varchar(4) NOT NULL,
  `speed` varchar(4) NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`characterID`,`armorID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dnd3_characters`
--

CREATE TABLE IF NOT EXISTS `dnd3_characters` (
  `characterID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `race` varchar(50) NOT NULL,
  `size` tinyint(4) NOT NULL,
  `class` varchar(100) NOT NULL,
  `alignment` varchar(2) NOT NULL,
  `str` tinyint(4) NOT NULL DEFAULT '10',
  `dex` tinyint(4) NOT NULL DEFAULT '10',
  `con` tinyint(4) NOT NULL DEFAULT '10',
  `int` tinyint(4) NOT NULL DEFAULT '10',
  `wis` tinyint(4) NOT NULL DEFAULT '10',
  `cha` tinyint(4) NOT NULL DEFAULT '10',
  `hp` tinyint(4) NOT NULL,
  `hp_current` tinyint(4) NOT NULL,
  `ac_armor` tinyint(4) NOT NULL,
  `ac_shield` tinyint(4) NOT NULL,
  `ac_dex` tinyint(4) NOT NULL,
  `ac_class` tinyint(4) NOT NULL,
  `ac_natural` tinyint(4) NOT NULL,
  `ac_deflection` tinyint(4) NOT NULL,
  `ac_misc` tinyint(4) NOT NULL,
  `dr` varchar(50) NOT NULL,
  `initiative_misc` tinyint(4) NOT NULL,
  `fort_base` tinyint(4) NOT NULL,
  `fort_magic` tinyint(4) NOT NULL,
  `fort_race` tinyint(4) NOT NULL,
  `fort_misc` tinyint(4) NOT NULL,
  `ref_base` tinyint(4) NOT NULL,
  `ref_magic` tinyint(4) NOT NULL,
  `ref_race` tinyint(4) NOT NULL,
  `ref_misc` tinyint(4) NOT NULL,
  `will_base` tinyint(4) NOT NULL,
  `will_magic` tinyint(4) NOT NULL,
  `will_race` tinyint(4) NOT NULL,
  `will_misc` tinyint(4) NOT NULL,
  `bab` tinyint(4) NOT NULL,
  `melee_misc` tinyint(4) NOT NULL,
  `ranged_misc` tinyint(4) NOT NULL,
  `sr` tinyint(4) NOT NULL,
  `items` text NOT NULL,
  `spells` text NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`characterID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dnd3_feats`
--

CREATE TABLE IF NOT EXISTS `dnd3_feats` (
  `featID` int(11) NOT NULL,
  `characterID` int(11) NOT NULL,
  `notes` text,
  PRIMARY KEY (`featID`,`characterID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dnd3_skills`
--

CREATE TABLE IF NOT EXISTS `dnd3_skills` (
  `characterID` int(11) NOT NULL,
  `skillID` int(11) NOT NULL,
  `stat` varchar(3) NOT NULL,
  `ranks` tinyint(4) NOT NULL,
  `misc` tinyint(4) NOT NULL,
  PRIMARY KEY (`characterID`,`skillID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dnd3_weapons`
--

CREATE TABLE IF NOT EXISTS `dnd3_weapons` (
  `characterID` int(11) NOT NULL,
  `weaponID` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `ab` varchar(20) NOT NULL,
  `damage` varchar(20) NOT NULL,
  `critical` varchar(10) NOT NULL,
  `range` varchar(5) NOT NULL,
  `type` varchar(3) NOT NULL,
  `size` varchar(1) NOT NULL,
  `notes` text NOT NULL,
  `test` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`characterID`,`weaponID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dnd4_attacks`
--

CREATE TABLE IF NOT EXISTS `dnd4_attacks` (
  `characterID` int(11) NOT NULL,
  `attackID` int(11) NOT NULL,
  `ability` varchar(50) NOT NULL,
  `stat` tinyint(4) NOT NULL,
  `class` tinyint(4) NOT NULL,
  `prof` tinyint(4) NOT NULL,
  `feat` tinyint(4) NOT NULL,
  `enh` tinyint(4) NOT NULL,
  `misc` tinyint(4) NOT NULL,
  PRIMARY KEY (`characterID`,`attackID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dnd4_characters`
--

CREATE TABLE IF NOT EXISTS `dnd4_characters` (
  `characterID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `race` varchar(50) NOT NULL,
  `alignment` varchar(2) NOT NULL,
  `class` varchar(100) NOT NULL,
  `paragon` varchar(50) NOT NULL,
  `epic` varchar(50) NOT NULL,
  `str` tinyint(4) NOT NULL DEFAULT '10',
  `con` tinyint(4) NOT NULL DEFAULT '10',
  `dex` tinyint(4) NOT NULL DEFAULT '10',
  `int` tinyint(4) NOT NULL DEFAULT '10',
  `wis` tinyint(4) NOT NULL DEFAULT '10',
  `cha` tinyint(4) NOT NULL DEFAULT '10',
  `ac_armor` tinyint(4) NOT NULL,
  `ac_class` tinyint(4) NOT NULL,
  `ac_feats` tinyint(4) NOT NULL,
  `ac_enh` tinyint(4) NOT NULL,
  `ac_misc` tinyint(4) NOT NULL,
  `fort_class` tinyint(4) NOT NULL,
  `fort_feats` tinyint(4) NOT NULL,
  `fort_enh` tinyint(4) NOT NULL,
  `fort_misc` tinyint(4) NOT NULL,
  `ref_class` tinyint(4) NOT NULL,
  `ref_feats` tinyint(4) NOT NULL,
  `ref_enh` tinyint(4) NOT NULL,
  `ref_misc` tinyint(4) NOT NULL,
  `will_class` tinyint(4) NOT NULL,
  `will_feats` tinyint(4) NOT NULL,
  `will_enh` tinyint(4) NOT NULL,
  `will_misc` tinyint(4) NOT NULL,
  `init_misc` tinyint(4) NOT NULL,
  `hp` tinyint(4) NOT NULL,
  `surges` tinyint(4) NOT NULL,
  `speed_base` tinyint(4) NOT NULL,
  `speed_armor` tinyint(4) NOT NULL,
  `speed_item` tinyint(4) NOT NULL,
  `speed_misc` tinyint(4) NOT NULL,
  `ap` tinyint(4) NOT NULL,
  `piSkill` tinyint(4) NOT NULL,
  `ppSkill` tinyint(4) NOT NULL,
  `items` text NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`characterID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dnd4_feats`
--

CREATE TABLE IF NOT EXISTS `dnd4_feats` (
  `featID` int(11) NOT NULL,
  `characterID` int(11) NOT NULL,
  `notes` text,
  PRIMARY KEY (`featID`,`characterID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `dnd4_powers`
--

CREATE TABLE IF NOT EXISTS `dnd4_powers` (
  `powerID` int(11) NOT NULL AUTO_INCREMENT,
  `characterID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `type` varchar(1) NOT NULL,
  PRIMARY KEY (`powerID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `dnd4_powersList`
--

CREATE TABLE IF NOT EXISTS `dnd4_powersList` (
  `powerID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `searchName` varchar(50) DEFAULT NULL,
  `userDefined` int(11) DEFAULT NULL,
  PRIMARY KEY (`powerID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `dnd4_skills`
--

CREATE TABLE IF NOT EXISTS `dnd4_skills` (
  `characterID` int(11) NOT NULL,
  `skillID` int(11) NOT NULL,
  `stat` varchar(3) NOT NULL,
  `ranks` tinyint(4) NOT NULL,
  `misc` tinyint(4) NOT NULL,
  PRIMARY KEY (`characterID`,`skillID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `featsList`
--

CREATE TABLE IF NOT EXISTS `featsList` (
  `featID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `searchName` varchar(50) DEFAULT NULL,
  `userDefined` int(11) DEFAULT NULL,
  PRIMARY KEY (`featID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `forumAdmins`
--

CREATE TABLE IF NOT EXISTS `forumAdmins` (
  `userID` int(11) NOT NULL,
  `forumID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userID`,`forumID`),
  KEY `forumID` (`forumID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `forums`
--

CREATE TABLE IF NOT EXISTS `forums` (
  `forumID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `forumType` varchar(1) NOT NULL DEFAULT 'f',
  `parentID` int(11) NOT NULL,
  `heritage` varchar(25) NOT NULL,
  `order` tinyint(4) NOT NULL,
  PRIMARY KEY (`forumID`),
  KEY `parentID` (`parentID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `forums_groupMemberships`
--

CREATE TABLE IF NOT EXISTS `forums_groupMemberships` (
  `groupID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  PRIMARY KEY (`groupID`,`userID`),
  KEY `userID` (`userID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `forums_groups`
--

CREATE TABLE IF NOT EXISTS `forums_groups` (
  `groupID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `ownerID` int(11) NOT NULL,
  `gameGroup` tinyint(1) NOT NULL,
  PRIMARY KEY (`groupID`),
  KEY `ownerID` (`ownerID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `forums_permissions_c`
--
CREATE TABLE IF NOT EXISTS `forums_permissions_c` (
`forumID` int(11)
,`userID` bigint(11)
,`read` bigint(11)
,`write` bigint(11)
,`editPost` bigint(11)
,`deletePost` bigint(11)
,`createThread` bigint(11)
,`deleteThread` bigint(11)
,`addPoll` bigint(11)
,`addRolls` bigint(11)
,`addDraws` bigint(11)
,`moderate` bigint(11)
);
-- --------------------------------------------------------

--
-- Table structure for table `forums_permissions_general`
--

CREATE TABLE IF NOT EXISTS `forums_permissions_general` (
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `forums_permissions_groups`
--

CREATE TABLE IF NOT EXISTS `forums_permissions_groups` (
  `groupID` int(11) NOT NULL,
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
  PRIMARY KEY (`groupID`,`forumID`),
  KEY `forumID` (`forumID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Stand-in structure for view `forums_permissions_groups_c`
--
CREATE TABLE IF NOT EXISTS `forums_permissions_groups_c` (
`userID` int(11)
,`forumID` int(11)
,`read` int(4)
,`write` int(4)
,`editPost` int(4)
,`deletePost` int(4)
,`createThread` int(4)
,`deleteThread` int(4)
,`addPoll` int(4)
,`addRolls` int(4)
,`addDraws` int(4)
,`moderate` int(4)
);
-- --------------------------------------------------------

--
-- Table structure for table `forums_permissions_users`
--

CREATE TABLE IF NOT EXISTS `forums_permissions_users` (
  `userID` int(11) NOT NULL,
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
  PRIMARY KEY (`userID`,`forumID`),
  KEY `forumID` (`forumID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `forums_pollOptions`
--

CREATE TABLE IF NOT EXISTS `forums_pollOptions` (
  `pollOptionID` int(11) NOT NULL AUTO_INCREMENT,
  `threadID` int(11) NOT NULL,
  `option` varchar(200) NOT NULL,
  PRIMARY KEY (`pollOptionID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `forums_polls`
--

CREATE TABLE IF NOT EXISTS `forums_polls` (
  `threadID` int(11) NOT NULL,
  `poll` varchar(200) NOT NULL,
  `optionsPerUser` tinyint(4) NOT NULL DEFAULT '1',
  `pollLength` tinyint(4) NOT NULL,
  `allowRevoting` tinyint(1) NOT NULL,
  PRIMARY KEY (`threadID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `forums_pollVotes`
--

CREATE TABLE IF NOT EXISTS `forums_pollVotes` (
  `userID` int(11) NOT NULL,
  `pollOptionID` int(11) NOT NULL,
  `votedOn` datetime NOT NULL,
  PRIMARY KEY (`userID`,`pollOptionID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `forums_readData`
--

CREATE TABLE IF NOT EXISTS `forums_readData` (
  `userID` int(11) NOT NULL,
  `forumData` text NOT NULL,
  `threadData` text NOT NULL,
  PRIMARY KEY (`userID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `forums_readData_forums`
--

CREATE TABLE IF NOT EXISTS `forums_readData_forums` (
  `userID` int(11) NOT NULL,
  `forumID` int(11) NOT NULL,
  `lastRead` int(11) NOT NULL,
  PRIMARY KEY (`userID`,`forumID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Stand-in structure for view `forums_readData_forums_c`
--
CREATE TABLE IF NOT EXISTS `forums_readData_forums_c` (
`forumID` int(11)
,`userID` int(11)
,`cLastRead` int(11)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `forums_readdata_newposts`
--
CREATE TABLE IF NOT EXISTS `forums_readData_newPosts` (
`forumID` int(11)
,`userID` int(11)
,`threadID` int(11)
,`lastPostID` int(11)
,`lastRead` int(11)
,`cLastRead` int(11)
,`newPosts` int(1)
);
-- --------------------------------------------------------

--
-- Table structure for table `forums_readData_threads`
--

CREATE TABLE IF NOT EXISTS `forums_readData_threads` (
  `userID` int(11) NOT NULL,
  `threadID` int(11) NOT NULL,
  `lastRead` int(11) NOT NULL,
  PRIMARY KEY (`userID`,`threadID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gameHistory`
--

CREATE TABLE IF NOT EXISTS `gameHistory` (
  `actionID` int(11) NOT NULL AUTO_INCREMENT,
  `gameID` int(11) NOT NULL,
  `enactedBy` int(11) NOT NULL,
  `enactedOn` datetime NOT NULL,
  `action` varchar(20) NOT NULL,
  `affectedType` varchar(20) DEFAULT NULL,
  `affectedID` int(11) DEFAULT NULL,
  PRIMARY KEY (`actionID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE IF NOT EXISTS `games` (
  `gameID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `systemID` int(11) NOT NULL,
  `gmID` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  `postFrequency` varchar(4) NOT NULL,
  `numPlayers` tinyint(4) NOT NULL,
  `charsPerPlayer` tinyint(4) NOT NULL DEFAULT '1',
  `description` text NOT NULL,
  `charGenInfo` text NOT NULL,
  `forumID` int(11) NOT NULL,
  `groupID` int(11) NOT NULL,
  `logForumID` int(11) DEFAULT NULL,
  `open` tinyint(1) NOT NULL DEFAULT '1',
  `retired` tinyint(1) NOT NULL,
  PRIMARY KEY (`gameID`),
  KEY `gmID` (`gmID`),
  KEY `forumID` (`forumID`),
  KEY `groupID` (`groupID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `gms`
--

CREATE TABLE IF NOT EXISTS `gms` (
  `gameID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `primary` tinyint(1) NOT NULL,
  PRIMARY KEY (`gameID`,`userID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gurps_characters`
--

CREATE TABLE IF NOT EXISTS `gurps_characters` (
  `characterID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `st` tinyint(4) NOT NULL DEFAULT '10',
  `dx` tinyint(4) NOT NULL DEFAULT '10',
  `iq` tinyint(4) NOT NULL DEFAULT '10',
  `ht` tinyint(4) NOT NULL DEFAULT '10',
  `hp` tinyint(4) NOT NULL DEFAULT '10',
  `hp_current` tinyint(4) NOT NULL DEFAULT '10',
  `will` tinyint(4) NOT NULL DEFAULT '10',
  `per` tinyint(4) NOT NULL DEFAULT '10',
  `fp` tinyint(4) NOT NULL DEFAULT '10',
  `fp_current` tinyint(4) NOT NULL DEFAULT '10',
  `dmg_thr` tinyint(4) NOT NULL,
  `dmg_sw` tinyint(4) NOT NULL,
  `speed` tinyint(4) NOT NULL DEFAULT '5',
  `move` tinyint(4) NOT NULL DEFAULT '5',
  `languages` text NOT NULL,
  `advantages` text NOT NULL,
  `disadvantages` text NOT NULL,
  `skills` text NOT NULL,
  `weapons` text NOT NULL,
  `items` text NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`characterID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `lfg`
--

CREATE TABLE IF NOT EXISTS `lfg` (
  `userID` int(11) NOT NULL,
  `systemID` int(11) NOT NULL,
  PRIMARY KEY (`userID`,`systemID`),
  UNIQUE KEY `userID` (`userID`,`systemID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `loginRecords`
--

CREATE TABLE IF NOT EXISTS `loginRecords` (
  `userID` int(11) NOT NULL,
  `attemptStamp` datetime NOT NULL,
  `ipAddress` varchar(15) NOT NULL,
  `successful` tinyint(1) NOT NULL,
  PRIMARY KEY (`userID`,`attemptStamp`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mapData`
--

CREATE TABLE IF NOT EXISTS `mapData` (
  `mapID` int(11) NOT NULL,
  `col` tinyint(4) NOT NULL,
  `row` tinyint(4) NOT NULL,
  `data` text,
  PRIMARY KEY (`mapID`,`col`,`row`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `maps`
--

CREATE TABLE IF NOT EXISTS `maps` (
  `mapID` int(11) NOT NULL AUTO_INCREMENT,
  `gameID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `rows` tinyint(4) NOT NULL,
  `cols` tinyint(4) NOT NULL,
  `info` varchar(300) NOT NULL,
  `visible` tinyint(1) NOT NULL,
  PRIMARY KEY (`mapID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `maps_iconHistory`
--

CREATE TABLE IF NOT EXISTS `maps_iconHistory` (
  `actionID` int(11) NOT NULL AUTO_INCREMENT,
  `iconID` int(11) NOT NULL,
  `mapID` int(11) NOT NULL,
  `enactedBy` int(11) NOT NULL,
  `enactedOn` datetime NOT NULL,
  `action` varchar(20) NOT NULL,
  `origin` varchar(10) DEFAULT NULL,
  `destination` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`actionID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `maps_icons`
--

CREATE TABLE IF NOT EXISTS `maps_icons` (
  `iconID` int(11) NOT NULL AUTO_INCREMENT,
  `mapID` int(11) NOT NULL,
  `label` varchar(2) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(200) NOT NULL,
  `color` varchar(6) NOT NULL,
  `location` varchar(7) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`iconID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `marvel_actions`
--

CREATE TABLE IF NOT EXISTS `marvel_actions` (
  `characterID` int(11) NOT NULL,
  `actionID` int(11) NOT NULL,
  `level` tinyint(4) NOT NULL,
  `offset` tinyint(4) NOT NULL,
  `details` text NOT NULL,
  `cost` tinyint(4) NOT NULL,
  PRIMARY KEY (`characterID`,`actionID`),
  KEY `characterID` (`characterID`),
  KEY `actionID` (`actionID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `marvel_actionsList`
--

CREATE TABLE IF NOT EXISTS `marvel_actionsList` (
  `actionID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `searchName` varchar(50) DEFAULT NULL,
  `cost` tinyint(4) NOT NULL,
  `magic` tinyint(1) NOT NULL,
  `source` varchar(50) NOT NULL,
  `userDefined` int(11) DEFAULT NULL,
  PRIMARY KEY (`actionID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `marvel_actionSpecialties`
--

CREATE TABLE IF NOT EXISTS `marvel_actionSpecialties` (
  `actionSpecialtyID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`actionSpecialtyID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `marvel_challenges`
--

CREATE TABLE IF NOT EXISTS `marvel_challenges` (
  `challengeID` int(11) NOT NULL AUTO_INCREMENT,
  `characterID` int(11) NOT NULL,
  `challenge` varchar(200) NOT NULL,
  `stones` tinyint(4) NOT NULL,
  PRIMARY KEY (`challengeID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `marvel_characters`
--

CREATE TABLE IF NOT EXISTS `marvel_characters` (
  `characterID` int(11) NOT NULL,
  `normName` varchar(50) NOT NULL,
  `superName` varchar(50) NOT NULL,
  `int` tinyint(4) NOT NULL,
  `str` tinyint(4) NOT NULL,
  `agi` tinyint(4) NOT NULL,
  `spd` tinyint(4) NOT NULL,
  `dur` tinyint(4) NOT NULL,
  `unusedStones` decimal(3,1) NOT NULL,
  `totalStones` decimal(3,1) NOT NULL,
  `rules` varchar(10) DEFAULT NULL,
  `health_max` tinyint(4) NOT NULL,
  `health_current` tinyint(4) NOT NULL,
  `energy_max` tinyint(4) NOT NULL,
  `energy_current` tinyint(4) NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`characterID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `marvel_modifiers`
--

CREATE TABLE IF NOT EXISTS `marvel_modifiers` (
  `characterID` int(11) NOT NULL,
  `modifierID` int(11) NOT NULL,
  `level` tinyint(4) NOT NULL,
  `offset` tinyint(4) NOT NULL,
  `extraStones` tinyint(4) NOT NULL,
  `timesTaken` tinyint(4) NOT NULL DEFAULT '1',
  `details` text NOT NULL,
  `cost` tinyint(4) NOT NULL,
  PRIMARY KEY (`characterID`,`modifierID`),
  KEY `characterID` (`characterID`),
  KEY `modifierID` (`modifierID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `marvel_modifiersList`
--

CREATE TABLE IF NOT EXISTS `marvel_modifiersList` (
  `modifierID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `searchName` varchar(50) DEFAULT NULL,
  `cost` float NOT NULL,
  `costTo` varchar(5) NOT NULL,
  `multipleAllowed` varchar(40) NOT NULL,
  `source` varchar(50) NOT NULL,
  PRIMARY KEY (`modifierID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `marvel_playerChallenges`
--

CREATE TABLE IF NOT EXISTS `marvel_playerChallenges` (
  `characterID` int(11) NOT NULL,
  `challengeID` int(11) NOT NULL,
  `stones` float NOT NULL,
  PRIMARY KEY (`characterID`,`challengeID`),
  KEY `characterID` (`characterID`),
  KEY `challengeID` (`challengeID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
  `notificationID` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(20) NOT NULL,
  `enactedBy` int(11) NOT NULL,
  `enactedOn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actedUpon` int(11) NOT NULL,
  PRIMARY KEY (`notificationID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pathfinder_armors`
--

CREATE TABLE IF NOT EXISTS `pathfinder_armors` (
  `armorID` int(11) NOT NULL AUTO_INCREMENT,
  `characterID` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `ac` varchar(20) NOT NULL,
  `maxDex` varchar(20) NOT NULL,
  `type` varchar(10) NOT NULL,
  `check` int(11) NOT NULL,
  `spellFailure` varchar(4) NOT NULL,
  `speed` varchar(4) NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`armorID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pathfinder_characters`
--

CREATE TABLE IF NOT EXISTS `pathfinder_characters` (
  `characterID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `race` varchar(50) NOT NULL,
  `class` varchar(100) NOT NULL,
  `alignment` varchar(2) NOT NULL,
  `str` tinyint(4) NOT NULL DEFAULT '10',
  `dex` tinyint(4) NOT NULL DEFAULT '10',
  `con` tinyint(4) NOT NULL DEFAULT '10',
  `int` tinyint(4) NOT NULL DEFAULT '10',
  `wis` tinyint(4) NOT NULL DEFAULT '10',
  `cha` tinyint(4) NOT NULL DEFAULT '10',
  `hp` tinyint(4) NOT NULL,
  `hp_current` tinyint(4) NOT NULL,
  `size` tinyint(4) NOT NULL,
  `ac_armor` tinyint(4) NOT NULL,
  `ac_shield` tinyint(4) NOT NULL,
  `ac_dex` tinyint(4) NOT NULL,
  `ac_class` tinyint(4) NOT NULL,
  `ac_natural` tinyint(4) NOT NULL,
  `ac_deflection` tinyint(4) NOT NULL,
  `ac_misc` tinyint(4) NOT NULL,
  `dr` varchar(50) NOT NULL,
  `initiative` tinyint(4) NOT NULL,
  `initiative_misc` tinyint(4) NOT NULL,
  `fort` tinyint(4) NOT NULL,
  `fort_base` tinyint(4) NOT NULL,
  `fort_magic` tinyint(4) NOT NULL,
  `fort_race` tinyint(4) NOT NULL,
  `fort_misc` tinyint(4) NOT NULL,
  `ref` tinyint(4) NOT NULL,
  `ref_base` tinyint(4) NOT NULL,
  `ref_magic` tinyint(4) NOT NULL,
  `ref_race` tinyint(4) NOT NULL,
  `ref_misc` tinyint(4) NOT NULL,
  `will` tinyint(4) NOT NULL,
  `will_base` tinyint(4) NOT NULL,
  `will_magic` tinyint(4) NOT NULL,
  `will_race` tinyint(4) NOT NULL,
  `will_misc` tinyint(4) NOT NULL,
  `bab` tinyint(4) NOT NULL,
  `melee_misc` tinyint(4) NOT NULL,
  `ranged_misc` tinyint(4) NOT NULL,
  `sr` tinyint(4) NOT NULL,
  `skills` text NOT NULL,
  `feats` text NOT NULL,
  `weapons` text NOT NULL,
  `armor` text NOT NULL,
  `items` text NOT NULL,
  `spells` text NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`characterID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pathfinder_feats`
--

CREATE TABLE IF NOT EXISTS `pathfinder_feats` (
  `featID` int(11) NOT NULL,
  `characterID` int(11) NOT NULL,
  `notes` text,
  PRIMARY KEY (`featID`,`characterID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pathfinder_skills`
--

CREATE TABLE IF NOT EXISTS `pathfinder_skills` (
  `characterID` int(11) NOT NULL,
  `skillID` int(11) NOT NULL,
  `stat` varchar(3) NOT NULL,
  `ranks` int(11) NOT NULL,
  `misc` int(11) NOT NULL,
  PRIMARY KEY (`characterID`,`skillID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pathfinder_weapons`
--

CREATE TABLE IF NOT EXISTS `pathfinder_weapons` (
  `weaponID` int(11) NOT NULL AUTO_INCREMENT,
  `characterID` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `ab` varchar(20) NOT NULL,
  `damage` varchar(20) NOT NULL,
  `critical` varchar(10) NOT NULL,
  `range` varchar(5) NOT NULL,
  `type` varchar(3) NOT NULL,
  `size` varchar(1) NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`weaponID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

CREATE TABLE IF NOT EXISTS `players` (
  `gameID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `approved` tinyint(1) NOT NULL,
  `isGM` tinyint(1) NOT NULL,
  `primaryGM` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`gameID`,`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pms`
--

CREATE TABLE IF NOT EXISTS `pms` (
  `pmID` int(11) NOT NULL AUTO_INCREMENT,
  `recipientID` int(11) NOT NULL,
  `senderID` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `datestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `viewed` tinyint(1) NOT NULL,
  `replyTo` int(11) NOT NULL,
  PRIMARY KEY (`pmID`),
  KEY `recipientID` (`recipientID`),
  KEY `senderID` (`senderID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `postID` int(11) NOT NULL AUTO_INCREMENT,
  `threadID` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `authorID` int(11) NOT NULL,
  `message` text NOT NULL,
  `datePosted` datetime NOT NULL,
  `lastEdit` datetime NOT NULL,
  `timesEdited` tinyint(4) NOT NULL,
  PRIMARY KEY (`postID`),
  KEY `threadID` (`threadID`),
  KEY `authorID` (`authorID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `rolls`
--

CREATE TABLE IF NOT EXISTS `rolls` (
  `rollID` int(11) NOT NULL AUTO_INCREMENT,
  `postID` int(11) NOT NULL,
  `roll` varchar(20) NOT NULL,
  `indivRolls` varchar(50) NOT NULL,
  `ra` tinyint(1) NOT NULL,
  `reason` varchar(50) NOT NULL,
  `total` tinyint(4) NOT NULL,
  `visibility` tinyint(1) NOT NULL,
  PRIMARY KEY (`rollID`),
  KEY `postID` (`postID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `shadowrun4_characters`
--

CREATE TABLE IF NOT EXISTS `shadowrun4_characters` (
  `characterID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `metatype` varchar(20) NOT NULL,
  `body` tinyint(4) NOT NULL,
  `agility` tinyint(4) NOT NULL,
  `reaction` tinyint(4) NOT NULL,
  `strength` tinyint(4) NOT NULL,
  `charisma` tinyint(4) NOT NULL,
  `intuition` tinyint(4) NOT NULL,
  `logic` tinyint(4) NOT NULL,
  `willpower` tinyint(4) NOT NULL,
  `edge_total` tinyint(4) NOT NULL,
  `edge_current` tinyint(4) NOT NULL,
  `essence` tinyint(4) NOT NULL,
  `mag_res` tinyint(4) NOT NULL,
  `initiative` tinyint(4) NOT NULL,
  `initiative_passes` tinyint(4) NOT NULL,
  `matrix_initiative` tinyint(4) NOT NULL,
  `astral_initiative` tinyint(4) NOT NULL,
  `physicalDamage` tinyint(4) NOT NULL,
  `stunDamage` tinyint(4) NOT NULL,
  `skills` text NOT NULL,
  `qualities` text NOT NULL,
  `weapons` text NOT NULL,
  `armor` text NOT NULL,
  `augments` text NOT NULL,
  `contacts` text NOT NULL,
  `spells` text NOT NULL,
  `items` text NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`characterID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `skillsList`
--

CREATE TABLE IF NOT EXISTS `skillsList` (
  `skillID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `searchName` varchar(50) DEFAULT NULL,
  `userDefined` int(11) DEFAULT NULL,
  PRIMARY KEY (`skillID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `spycraft2_armors`
--

CREATE TABLE IF NOT EXISTS `spycraft2_armors` (
  `armorID` int(11) NOT NULL AUTO_INCREMENT,
  `characterID` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `reduction` varchar(20) NOT NULL,
  `resist` varchar(20) NOT NULL,
  `penalty` varchar(10) NOT NULL,
  `check` int(11) NOT NULL,
  `speed` varchar(4) NOT NULL,
  `dc` varchar(4) NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`armorID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `spycraft2_characters`
--

CREATE TABLE IF NOT EXISTS `spycraft2_characters` (
  `characterID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `codename` varchar(50) NOT NULL,
  `class` varchar(100) NOT NULL,
  `talent` varchar(30) NOT NULL,
  `specialty` varchar(30) NOT NULL,
  `str` tinyint(4) NOT NULL DEFAULT '10',
  `dex` tinyint(4) NOT NULL DEFAULT '10',
  `con` tinyint(4) NOT NULL DEFAULT '10',
  `int` tinyint(4) NOT NULL DEFAULT '10',
  `wis` tinyint(4) NOT NULL DEFAULT '10',
  `cha` tinyint(4) NOT NULL DEFAULT '10',
  `vitality` tinyint(4) NOT NULL,
  `wounds` tinyint(4) NOT NULL,
  `subdual` tinyint(4) NOT NULL,
  `stress` tinyint(4) NOT NULL,
  `ac_class` tinyint(4) NOT NULL,
  `ac_armor` tinyint(4) NOT NULL,
  `ac_dex` tinyint(4) NOT NULL,
  `ac_misc` tinyint(4) NOT NULL,
  `initiative_class` tinyint(4) NOT NULL,
  `initiative_misc` tinyint(4) NOT NULL,
  `fort_base` tinyint(4) NOT NULL,
  `fort_misc` tinyint(4) NOT NULL,
  `ref_base` tinyint(4) NOT NULL,
  `ref_misc` tinyint(4) NOT NULL,
  `will_base` tinyint(4) NOT NULL,
  `will_misc` tinyint(4) NOT NULL,
  `bab` tinyint(4) NOT NULL,
  `unarmed_misc` tinyint(4) NOT NULL,
  `melee_misc` tinyint(4) NOT NULL,
  `ranged_misc` tinyint(4) NOT NULL,
  `actionDie_total` tinyint(4) NOT NULL,
  `actionDie_dieType` varchar(4) NOT NULL,
  `knowledge_misc` tinyint(4) NOT NULL,
  `request_misc` tinyint(4) NOT NULL,
  `gear_misc` tinyint(4) NOT NULL,
  `items` text NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`characterID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `spycraft2_feats`
--

CREATE TABLE IF NOT EXISTS `spycraft2_feats` (
  `featID` int(11) NOT NULL,
  `characterID` int(11) NOT NULL,
  `notes` text,
  PRIMARY KEY (`featID`,`characterID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `spycraft2_focuses`
--

CREATE TABLE IF NOT EXISTS `spycraft2_focuses` (
  `characterID` int(11) NOT NULL,
  `focusID` int(11) NOT NULL,
  `forte` tinyint(1) NOT NULL,
  PRIMARY KEY (`characterID`,`focusID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `spycraft2_focusesList`
--

CREATE TABLE IF NOT EXISTS `spycraft2_focusesList` (
  `focusID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `searchName` varchar(50) DEFAULT NULL,
  `userDefined` int(11) DEFAULT NULL,
  PRIMARY KEY (`focusID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `spycraft2_skills`
--

CREATE TABLE IF NOT EXISTS `spycraft2_skills` (
  `characterID` int(11) NOT NULL,
  `skillID` int(11) NOT NULL,
  `stat_1` varchar(3) NOT NULL,
  `stat_2` varchar(3) DEFAULT NULL,
  `ranks` int(11) NOT NULL,
  `misc` int(11) NOT NULL,
  `error` varchar(10) NOT NULL,
  `threat` varchar(10) NOT NULL,
  PRIMARY KEY (`characterID`,`skillID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `spycraft2_weapons`
--

CREATE TABLE IF NOT EXISTS `spycraft2_weapons` (
  `weaponID` int(11) NOT NULL AUTO_INCREMENT,
  `characterID` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `ab` varchar(20) NOT NULL,
  `damage` varchar(20) NOT NULL,
  `recoil` varchar(10) NOT NULL,
  `et` varchar(10) NOT NULL,
  `range` varchar(5) NOT NULL,
  `type` varchar(10) NOT NULL,
  `size` varchar(10) NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`weaponID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `spycraft_armors`
--

CREATE TABLE IF NOT EXISTS `spycraft_armors` (
  `armorID` int(11) NOT NULL AUTO_INCREMENT,
  `characterID` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `def` varchar(20) NOT NULL,
  `resist` varchar(20) NOT NULL,
  `check` tinyint(4) NOT NULL,
  `type` varchar(10) NOT NULL,
  `maxDex` varchar(20) NOT NULL,
  `speed` varchar(4) NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`armorID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `spycraft_characters`
--

CREATE TABLE IF NOT EXISTS `spycraft_characters` (
  `characterID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `codename` varchar(50) NOT NULL,
  `class` varchar(100) NOT NULL,
  `department` varchar(50) NOT NULL,
  `str` tinyint(4) NOT NULL DEFAULT '10',
  `dex` tinyint(4) NOT NULL DEFAULT '10',
  `con` tinyint(4) NOT NULL DEFAULT '10',
  `int` tinyint(4) NOT NULL DEFAULT '10',
  `wis` tinyint(4) NOT NULL DEFAULT '10',
  `cha` tinyint(4) NOT NULL DEFAULT '10',
  `vitality` tinyint(4) NOT NULL,
  `wounds` tinyint(4) NOT NULL,
  `speed` tinyint(4) NOT NULL,
  `ac_armor` tinyint(4) NOT NULL,
  `ac_dex` tinyint(4) NOT NULL,
  `ac_size` tinyint(4) NOT NULL,
  `ac_misc` tinyint(4) NOT NULL,
  `initiative_misc` tinyint(4) NOT NULL,
  `fort_base` tinyint(4) NOT NULL,
  `fort_misc` tinyint(4) NOT NULL,
  `ref_base` tinyint(4) NOT NULL,
  `ref_misc` tinyint(4) NOT NULL,
  `will_base` tinyint(4) NOT NULL,
  `will_misc` tinyint(4) NOT NULL,
  `bab` tinyint(4) NOT NULL,
  `melee_misc` tinyint(4) NOT NULL,
  `ranged_misc` tinyint(4) NOT NULL,
  `actionDie_total` tinyint(4) NOT NULL,
  `actionDie_dieType` varchar(4) NOT NULL,
  `inspiration_misc` tinyint(4) NOT NULL,
  `education_misc` tinyint(4) NOT NULL,
  `items` text NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`characterID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `spycraft_feats`
--

CREATE TABLE IF NOT EXISTS `spycraft_feats` (
  `featID` int(11) NOT NULL,
  `characterID` int(11) NOT NULL,
  `notes` text,
  PRIMARY KEY (`featID`,`characterID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `spycraft_skills`
--

CREATE TABLE IF NOT EXISTS `spycraft_skills` (
  `characterID` int(11) NOT NULL,
  `skillID` int(11) NOT NULL,
  `stat` varchar(3) NOT NULL,
  `ranks` tinyint(4) NOT NULL,
  `misc` tinyint(4) NOT NULL,
  `error` varchar(10) NOT NULL,
  `threat` varchar(10) NOT NULL,
  PRIMARY KEY (`characterID`,`skillID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `spycraft_weapons`
--

CREATE TABLE IF NOT EXISTS `spycraft_weapons` (
  `weaponID` int(11) NOT NULL AUTO_INCREMENT,
  `characterID` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `ab` varchar(20) NOT NULL,
  `damage` varchar(20) NOT NULL,
  `error` varchar(10) NOT NULL,
  `threat` varchar(10) NOT NULL,
  `range` varchar(5) NOT NULL,
  `type` varchar(3) NOT NULL,
  `size` varchar(1) NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`weaponID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `systems`
--

CREATE TABLE IF NOT EXISTS `systems` (
  `systemID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(20) NOT NULL,
  `fullName` varchar(40) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  PRIMARY KEY (`systemID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `threads`
--

CREATE TABLE IF NOT EXISTS `threads` (
  `threadID` int(11) NOT NULL AUTO_INCREMENT,
  `forumID` int(11) NOT NULL,
  `sticky` tinyint(1) NOT NULL,
  `locked` tinyint(1) NOT NULL,
  `allowRolls` tinyint(1) NOT NULL,
  `allowDraws` tinyint(1) NOT NULL,
  PRIMARY KEY (`threadID`),
  KEY `forumID` (`forumID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `threads_relposts`
--
CREATE TABLE IF NOT EXISTS `threads_relPosts` (
`threadID` int(11)
,`firstPostID` int(11)
,`lastPostID` int(11)
);
-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(24) NOT NULL,
  `password` varchar(64) NOT NULL,
  `email` varchar(50) NOT NULL,
  `joinDate` datetime NOT NULL,
  `lastActivity` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  `referrence` text NOT NULL,
  `enableFilter` tinyint(1) NOT NULL DEFAULT '1',
  `showAvatars` tinyint(1) NOT NULL DEFAULT '1',
  `avatarExt` varchar(3) NOT NULL,
  `timezone` varchar(20) NOT NULL DEFAULT 'Europe/London',
  `showTZ` tinyint(1) NOT NULL,
  `realName` varchar(50) DEFAULT NULL,
  `gender` varchar(1) NOT NULL,
  `birthday` date NOT NULL,
  `showAge` tinyint(1) NOT NULL,
  `location` varchar(100) NOT NULL,
  `aim` varchar(50) NOT NULL,
  `gmail` varchar(50) DEFAULT NULL,
  `twitter` varchar(50) DEFAULT NULL,
  `games` varchar(200) DEFAULT NULL,
  `newGameMail` tinyint(1) NOT NULL DEFAULT '1',
  `postSide` varchar(1) NOT NULL DEFAULT 'r',
  PRIMARY KEY (`userID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Table structure for table `wod_characters`
--

CREATE TABLE IF NOT EXISTS `wod_characters` (
  `characterID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `int` tinyint(4) NOT NULL DEFAULT '1',
  `str` tinyint(4) NOT NULL DEFAULT '1',
  `pre` tinyint(4) NOT NULL DEFAULT '1',
  `wit` tinyint(4) NOT NULL DEFAULT '1',
  `dex` tinyint(4) NOT NULL DEFAULT '1',
  `man` tinyint(4) NOT NULL DEFAULT '1',
  `res` tinyint(4) NOT NULL DEFAULT '1',
  `sta` tinyint(4) NOT NULL DEFAULT '1',
  `com` tinyint(4) NOT NULL DEFAULT '1',
  `academics` tinyint(4) NOT NULL,
  `computer` tinyint(4) NOT NULL,
  `crafts` tinyint(4) NOT NULL,
  `investigation` tinyint(4) NOT NULL,
  `medicine` tinyint(4) NOT NULL,
  `occult` tinyint(4) NOT NULL,
  `politics` tinyint(4) NOT NULL,
  `science` tinyint(4) NOT NULL,
  `athletics` tinyint(4) NOT NULL,
  `brawl` tinyint(4) NOT NULL,
  `drive` tinyint(4) NOT NULL,
  `firearms` tinyint(4) NOT NULL,
  `larceny` tinyint(4) NOT NULL,
  `stealth` tinyint(4) NOT NULL,
  `survival` tinyint(4) NOT NULL,
  `weaponry` tinyint(4) NOT NULL,
  `animalKen` tinyint(4) NOT NULL,
  `empathy` tinyint(4) NOT NULL,
  `expression` tinyint(4) NOT NULL,
  `intimidation` tinyint(4) NOT NULL,
  `persuasion` tinyint(4) NOT NULL,
  `socialize` tinyint(4) NOT NULL,
  `streetwise` tinyint(4) NOT NULL,
  `subterfuge` tinyint(4) NOT NULL,
  `merits` text NOT NULL,
  `flaws` text NOT NULL,
  `health` tinyint(4) NOT NULL DEFAULT '0',
  `willpower` tinyint(4) NOT NULL DEFAULT '0',
  `morality` tinyint(4) NOT NULL DEFAULT '0',
  `size` varchar(5) NOT NULL,
  `speed` varchar(5) NOT NULL,
  `initiativeMod` varchar(5) NOT NULL,
  `defense` varchar(5) NOT NULL,
  `armor` varchar(5) NOT NULL,
  `weapons` text NOT NULL,
  `equipment` text NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`characterID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `wordFilter`
--

CREATE TABLE IF NOT EXISTS `wordFilter` (
  `wordID` int(11) NOT NULL AUTO_INCREMENT,
  `word` varchar(50) NOT NULL,
  `spam` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`wordID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;

-- --------------------------------------------------------

--
-- Structure for view `forums_permissions_c`
--
DROP TABLE IF EXISTS `forums_permissions_c`;

CREATE VIEW `forums_permissions_c` AS select `bp`.`forumID` AS `forumID`,ifnull(`gp`.`userID`,`up`.`userID`) AS `userID`,if((ifnull(`up`.`read`,0) <> 0),`up`.`read`,if((ifnull(`gp`.`read`,0) <> 0),`gp`.`read`,`bp`.`read`)) AS `read`,if((ifnull(`up`.`write`,0) <> 0),`up`.`write`,if((ifnull(`gp`.`write`,0) <> 0),`gp`.`write`,`bp`.`write`)) AS `write`,if((ifnull(`up`.`editPost`,0) <> 0),`up`.`editPost`,if((ifnull(`gp`.`editPost`,0) <> 0),`gp`.`editPost`,`bp`.`editPost`)) AS `editPost`,if((ifnull(`up`.`deletePost`,0) <> 0),`up`.`deletePost`,if((ifnull(`gp`.`deletePost`,0) <> 0),`gp`.`deletePost`,`bp`.`deletePost`)) AS `deletePost`,if((ifnull(`up`.`createThread`,0) <> 0),`up`.`createThread`,if((ifnull(`gp`.`createThread`,0) <> 0),`gp`.`createThread`,`bp`.`createThread`)) AS `createThread`,if((ifnull(`up`.`deleteThread`,0) <> 0),`up`.`deleteThread`,if((ifnull(`gp`.`deleteThread`,0) <> 0),`gp`.`deleteThread`,`bp`.`deleteThread`)) AS `deleteThread`,if((ifnull(`up`.`addPoll`,0) <> 0),`up`.`addPoll`,if((ifnull(`gp`.`addPoll`,0) <> 0),`gp`.`addPoll`,`bp`.`addPoll`)) AS `addPoll`,if((ifnull(`up`.`addRolls`,0) <> 0),`up`.`addRolls`,if((ifnull(`gp`.`addRolls`,0) <> 0),`gp`.`addRolls`,`bp`.`addRolls`)) AS `addRolls`,if((ifnull(`up`.`addDraws`,0) <> 0),`up`.`addDraws`,if((ifnull(`gp`.`addDraws`,0) <> 0),`gp`.`addDraws`,`bp`.`addDraws`)) AS `addDraws`,if((ifnull(`up`.`moderate`,0) <> 0),`up`.`moderate`,if((ifnull(`gp`.`moderate`,0) <> 0),`gp`.`moderate`,`bp`.`moderate`)) AS `moderate` from ((`forums_permissions_general` `bp` left join `forums_permissions_groups_c` `gp` on((`bp`.`forumID` = `gp`.`forumID`))) left join `forums_permissions_users` `up` on((`bp`.`forumID` = `up`.`forumID`))) where (isnull(`gp`.`userID`) or isnull(`up`.`userID`) or (`gp`.`userID` = `up`.`userID`));

-- --------------------------------------------------------

--
-- Structure for view `forums_permissions_groups_c`
--
DROP TABLE IF EXISTS `forums_permissions_groups_c`;

CREATE VIEW `forums_permissions_groups_c` AS select `gm`.`userID` AS `userID`,`gp`.`forumID` AS `forumID`,if((max(`gp`.`read`) = 2),2,min(`gp`.`read`)) AS `read`,if((max(`gp`.`write`) = 2),2,min(`gp`.`write`)) AS `write`,if((max(`gp`.`editPost`) = 2),2,min(`gp`.`editPost`)) AS `editPost`,if((max(`gp`.`deletePost`) = 2),2,min(`gp`.`deletePost`)) AS `deletePost`,if((max(`gp`.`createThread`) = 2),2,min(`gp`.`createThread`)) AS `createThread`,if((max(`gp`.`deleteThread`) = 2),2,min(`gp`.`deleteThread`)) AS `deleteThread`,if((max(`gp`.`addPoll`) = 2),2,min(`gp`.`addPoll`)) AS `addPoll`,if((max(`gp`.`addRolls`) = 2),2,min(`gp`.`addRolls`)) AS `addRolls`,if((max(`gp`.`addDraws`) = 2),2,min(`gp`.`addDraws`)) AS `addDraws`,if((max(`gp`.`moderate`) = 2),2,min(`gp`.`moderate`)) AS `moderate` from (`forums_groupMemberships` `gm` join `forums_permissions_groups` `gp` on((`gm`.`groupID` = `gp`.`groupID`))) group by `gp`.`forumID`,`gm`.`userID`;

-- --------------------------------------------------------

--
-- Structure for view `forums_readData_forums_c`
--
DROP TABLE IF EXISTS `forums_readData_forums_c`;

CREATE VIEW `forums_readData_forums_c` AS select `f`.`forumID` AS `forumID`,`rdf`.`userID` AS `userID`,max(`rdf`.`lastRead`) AS `cLastRead` from ((`forums` `f` left join `forums` `p` on((`f`.`heritage` like concat(`p`.`heritage`,'%')))) left join `forums_readData_forums` `rdf` on((`p`.`forumID` = `rdf`.`forumID`))) where (`rdf`.`userID` is not null) group by `f`.`forumID`,`rdf`.`userID`;

-- --------------------------------------------------------

--
-- Structure for view `forums_readdata_newposts`
--
DROP TABLE IF EXISTS `forums_readData_newPosts`;

CREATE VIEW `forums_readData_newPosts` AS select `t`.`forumID` AS `forumID`,`rdf`.`userID` AS `userID`,`t`.`threadID` AS `threadID`,`r`.`lastPostID` AS `lastPostID`,`rdt`.`lastRead` AS `lastRead`,`rdf`.`cLastRead` AS `cLastRead`,if(((`r`.`lastPostID` > ifnull(`rdt`.`lastRead`,0)) and (`r`.`lastPostID` > `rdf`.`cLastRead`)),1,0) AS `newPosts` from (((`threads` `t` join `threads_relPosts` `r` on((`t`.`threadID` = `r`.`threadID`))) left join `forums_readData_forums_c` `rdf` on((`t`.`forumID` = `rdf`.`forumID`))) left join `forums_readData_threads` `rdt` on(((`t`.`threadID` = `rdt`.`threadID`) and (`rdf`.`userID` = `rdt`.`userID`)))) having (`newPosts` = 1);

-- --------------------------------------------------------

--
-- Structure for view `threads_relposts`
--
DROP TABLE IF EXISTS `threads_relPosts`;

CREATE VIEW `threads_relPosts` AS select `posts`.`threadID` AS `threadID`,min(`posts`.`postID`) AS `firstPostID`,max(`posts`.`postID`) AS `lastPostID` from `posts` group by `posts`.`threadID`;
