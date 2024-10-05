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
-- Dumping data for table `acpPermissions`
--

LOCK TABLES `acpPermissions` WRITE;
/*!40000 ALTER TABLE `acpPermissions` DISABLE KEYS */;
INSERT INTO `acpPermissions` VALUES (1,'all'),(6,'faqs');
/*!40000 ALTER TABLE `acpPermissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `deckTypes`
--

LOCK TABLES `deckTypes` WRITE;
/*!40000 ALTER TABLE `deckTypes` DISABLE KEYS */;
INSERT INTO `deckTypes` VALUES ('pcwj','Playing Cards w/ Jokers',54,'pc','pc',''),('pcwoj','Playing Cards w/o Jokers',52,'pc','pc','');
/*!40000 ALTER TABLE `deckTypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `dispatch`
--

LOCK TABLES `dispatch` WRITE;
/*!40000 ALTER TABLE `dispatch` DISABLE KEYS */;
INSERT INTO `dispatch` VALUES ('/','home',NULL,'index.php',NULL,NULL,0,0,NULL,1,NULL),('403/','403',NULL,'errors/403.php','Forbidden',NULL,0,0,NULL,1,NULL),('404/','404',NULL,'errors/404.php','Not Found',NULL,0,0,NULL,1,NULL),('characters/','myCharacters','myCharacters','characters/my.php','My Characters',NULL,1,1,NULL,1,NULL),('characters/ajax/marvel/removeAction/',NULL,NULL,'characters/ajax/marvel/removeAction.php',NULL,NULL,1,0,NULL,1,NULL),('characters/ajax/marvel/removeModifier/',NULL,NULL,'characters/ajax/marvel/removeModifier.php',NULL,NULL,1,0,NULL,1,NULL),('characters/ajax/dnd4/addAttack/',NULL,NULL,'characters/ajax/dnd4/addAttack.php',NULL,NULL,1,0,NULL,1,NULL),('characters/ajax/dnd4/addPower/',NULL,NULL,'characters/ajax/dnd4/addPower.php',NULL,NULL,1,0,NULL,1,NULL),('characters/ajax/dnd4/powerSearch/',NULL,NULL,'characters/ajax/dnd4/powerSearch.php',NULL,NULL,1,0,NULL,1,NULL),('characters/ajax/dnd4/removePower/',NULL,NULL,'characters/ajax/dnd4/removePower.php',NULL,NULL,1,0,NULL,1,NULL),('characters/ajax/featSearch/',NULL,NULL,'characters/ajax/featSearch.php',NULL,NULL,1,0,NULL,1,NULL),('characters/ajax/marvel/actionSearch/',NULL,NULL,'characters/ajax/marvel/actionSearch.php',NULL,NULL,1,0,NULL,1,NULL),('characters/ajax/marvel/addAction/',NULL,NULL,'characters/ajax/marvel/addAction.php',NULL,NULL,1,0,NULL,1,NULL),('characters/ajax/marvel/addChallenge/',NULL,NULL,'characters/ajax/marvel/addChallenge.php',NULL,NULL,1,0,NULL,1,NULL),('characters/ajax/marvel/addModifier/',NULL,NULL,'characters/ajax/marvel/addModifier.php',NULL,NULL,1,0,NULL,1,NULL),('characters/ajax/marvel/modifierSearch/',NULL,NULL,'characters/ajax/marvel/modifierSearch.php',NULL,NULL,1,0,NULL,1,NULL),('characters/ajax/skillSearch/',NULL,NULL,'characters/ajax/skillSearch.php',NULL,NULL,1,0,NULL,1,NULL),('characters/ajax/spycraft2/addFocus/',NULL,NULL,'characters/ajax/spycraft2/addFocus.php',NULL,NULL,1,0,NULL,1,NULL),('characters/ajax/spycraft2/focusSearch/',NULL,NULL,'characters/ajax/spycraft2/focusSearch.php',NULL,NULL,1,0,NULL,1,NULL),('characters/ajax/spycraft2/removeFocus/',NULL,NULL,'characters/ajax/spycraft2/removeFocus.php',NULL,NULL,1,0,NULL,1,NULL),('forums/subscriptions/','subscriptions','subscriptions','forums/subscriptions.php','Manage Subscriptions',NULL,1,0,NULL,1,NULL),('contact/','contact','contact','contact/contact.php','Contact Us',NULL,0,0,NULL,1,NULL),('tools/flgs/','flgs','flgs','tools/flgs.php','FLGS Finder',NULL,0,0,NULL,1,NULL),('forums/','forum',NULL,'forums/forum.php','Forums',NULL,0,1,NULL,1,NULL),('forums/acp/','forumACP','forums_acp','forums/acp/acp.php','ACP',NULL,1,0,NULL,1,NULL),('forums/acp/(###)/deleteForum/','forumACP_deleteForum',NULL,'forums/acp/deleteForum.php','ACP - Delete',NULL,1,0,NULL,1,450),('forums/acp/(###)/deletePermission/','forumACP_deletePermission',NULL,'forums/acp/deletePermission.php','Delete Permission',NULL,1,0,NULL,1,400),('forums/acp/(###)/newPermission/','forumACP_newPermission',NULL,'forums/acp/newPermission.php','New Permission',NULL,1,0,NULL,1,500),('forums/acp/ajax/optSearch',NULL,NULL,'forums/acp/ajax/optSearch.php',NULL,NULL,1,0,NULL,1,NULL),('forums/ajax/addRoll/',NULL,NULL,'forums/ajax/addRoll.php',NULL,NULL,1,0,NULL,1,NULL),('forums/delete/','forum_delete',NULL,'forums/delete.php','Delete',NULL,1,0,NULL,1,400),('forums/editPost/','forum_post',NULL,'forums/post.php','Edit Post',NULL,1,1,NULL,1,NULL),('forums/moveThread/','forum_moveThread',NULL,'forums/moveThread.php','Move Thread',NULL,1,0,NULL,1,NULL),('forums/newThread/','forum_post',NULL,'forums/post.php','New Thread',NULL,1,1,NULL,1,NULL),('forums/post/','forum_post',NULL,'forums/post.php','Post',NULL,1,1,NULL,1,NULL),('forums/process/acp/deleteForum/',NULL,NULL,'forums/acp/process/deleteForum.php',NULL,NULL,1,0,NULL,1,NULL),('forums/process/acp/deletePermission/',NULL,NULL,'forums/acp/process/deletePermission.php',NULL,NULL,1,0,NULL,1,NULL),('forums/process/acp/edit/',NULL,NULL,'forums/acp/process/edit.php',NULL,NULL,1,0,NULL,1,NULL),('forums/process/acp/new/',NULL,NULL,'forums/acp/process/new.php',NULL,NULL,1,0,NULL,1,NULL),('forums/process/acp/permissions/',NULL,NULL,'forums/acp/process/permissions.php',NULL,NULL,1,0,NULL,1,NULL),('forums/process/acp/subforums/',NULL,NULL,'forums/acp/process/subforums.php',NULL,NULL,1,0,NULL,1,NULL),('forums/process/cardVis/',NULL,NULL,'forums/process/cardVis.php',NULL,NULL,1,0,NULL,1,NULL),('forums/process/delete/',NULL,NULL,'forums/process/delete.php',NULL,NULL,1,0,NULL,1,NULL),('forums/process/modThread/',NULL,NULL,'forums/process/modThread.php',NULL,NULL,1,0,NULL,1,NULL),('forums/process/moveThread/',NULL,NULL,'forums/process/moveThread.php',NULL,NULL,1,0,NULL,1,NULL),('forums/process/post/',NULL,NULL,'forums/process/post.php',NULL,NULL,1,0,NULL,1,NULL),('forums/process/read/',NULL,NULL,'forums/process/read.php',NULL,NULL,1,0,NULL,1,NULL),('forums/process/vote/',NULL,NULL,'forums/process/vote.php',NULL,NULL,1,0,NULL,1,NULL),('forums/rules/','forum_rules',NULL,'forums/rules.php','Forum Rules',NULL,0,0,NULL,1,NULL),('forums/search/','forum_search',NULL,'forums/search.php','Forums Search',NULL,1,0,NULL,1,NULL),('forums/thread/','forum_thread',NULL,'forums/thread.php','Thread',NULL,0,1,NULL,1,NULL),('gamersList/','gamersList','gamersList','ucp/list.php','Gamers List',NULL,0,0,NULL,1,NULL),('games/','myGames','myGames','games/my.php','My Games',NULL,1,0,NULL,1,NULL),('games/(###)/','gameDetails','games_details','games/details.php',NULL,NULL,0,1,NULL,1,NULL),('games/(###)/approveChar/(###)/','pendingChar',NULL,'games/pendingChar.php',NULL,NULL,1,0,NULL,1,500),('games/(###)/approvePlayer/(###)/','game_pendingPlayer',NULL,'games/pendingPlayer.php','Approve Player',NULL,1,1,NULL,1,500),('games/(###)/decks/','gameDecks',NULL,'games/decks.php','Game Decks',NULL,1,1,NULL,1,NULL),('games/(###)/decks/(###)/delete/','deleteDeck',NULL,'games/decks/delete.php','Delete Deck',NULL,1,0,NULL,1,450),('games/(###)/decks/(###)/edit/','deckDetails',NULL,'games/decks/details.php','Edit Deck',NULL,1,0,'editDeck',1,400),('games/(###)/decks/(###)/shuffle/','shuffleDeck',NULL,'games/decks/shuffle.php','Shuffle Deck',NULL,1,0,NULL,1,450),('games/(###)/decks/new/','deckDetails',NULL,'games/decks/details.php','New Deck',NULL,1,0,'newDeck',1,400),('games/(###)/edit/','editGame','games_cu','games/edit.php','Edit Game',NULL,1,1,NULL,1,NULL),('games/(###)/leaveGame/(###)/','leaveGame',NULL,'games/leaveGame.php','Leave Game',NULL,1,1,NULL,1,550),('games/(###)/maps/(###)/','map',NULL,'games/maps/map.php',NULL,NULL,1,1,NULL,1,NULL),('games/(###)/maps/(###)/delete/','deleteMap',NULL,'games/maps/delete.php','Delete Map',NULL,1,0,NULL,1,450),('games/(###)/maps/(###)/details/','mapDetails',NULL,'games/maps/mapDetails.php','Map Details',NULL,1,1,NULL,1,NULL),('games/(###)/maps/(###)/edit/','mapDetails',NULL,'games/maps/details.php','Edit Map',NULL,1,1,NULL,1,400),('games/(###)/maps/(###)/editInfo/','editMapInfo',NULL,'games/maps/editInfo.php','Edit Map Info',NULL,1,0,NULL,1,550),('games/(###)/maps/(###)/iconHistory/','iconHistory',NULL,'games/maps/iconHistory.php','Icon History',NULL,1,1,NULL,1,NULL),('games/(###)/maps/new/','mapDetails',NULL,'games/maps/details.php','New Map',NULL,1,1,NULL,1,400),('games/(###)/rejectPlayer/(###)/','game_pendingPlayer',NULL,'games/pendingPlayer.php','Reject Player',NULL,1,1,NULL,1,500),('games/(###)/removeChar/(###)/','pendingChar',NULL,'games/pendingChar.php',NULL,NULL,1,0,NULL,1,500),('games/(###)/removePlayer/(###)/','game_removePlayer',NULL,'games/removePlayer.php','Remove Player',NULL,1,1,NULL,1,550),('games/(###)/toggleGM/(###)/','toggleGM',NULL,'games/toggleGM.php','',NULL,1,1,NULL,1,550),('games/ajax/gamesSearch/',NULL,NULL,'games/ajax/gamesSearch.php',NULL,NULL,1,0,NULL,1,NULL),('games/ajax/maps/iconData/',NULL,NULL,'games/ajax/maps/iconData.php',NULL,NULL,1,0,NULL,1,NULL),('games/ajax/maps/save/',NULL,NULL,'games/ajax/maps/save.php',NULL,NULL,1,0,NULL,1,NULL),('games/ajax/maps/updateLoc/',NULL,NULL,'games/ajax/maps/updateLoc.php',NULL,NULL,1,0,NULL,1,NULL),('games/changeStatus/','changeGameStatus',NULL,'games/changeStatus.php','Change Game Status',NULL,1,0,NULL,1,450),('games/list/','listGames','listGames','games/list.php','All Games',NULL,0,0,NULL,1,NULL),('games/new/','newGame','games_cu','games/edit.php','New Game',NULL,1,0,NULL,1,NULL),('games/process/addCharacter/',NULL,NULL,'games/process/addCharacter.php',NULL,NULL,1,0,NULL,1,NULL),('games/process/changeStatus/',NULL,NULL,'games/process/changeStatus.php',NULL,NULL,1,0,NULL,1,NULL),('games/process/decks/delete/',NULL,NULL,'games/process/decks/delete.php',NULL,NULL,1,0,NULL,1,NULL),('games/process/decks/details/',NULL,NULL,'games/process/decks/details.php',NULL,NULL,1,0,NULL,1,NULL),('games/process/decks/shuffle/',NULL,NULL,'games/process/decks/shuffle.php',NULL,NULL,1,0,NULL,1,NULL),('games/process/edit/',NULL,NULL,'games/process/gameDetails.php',NULL,NULL,1,0,NULL,1,NULL),('games/process/join/',NULL,NULL,'games/process/join.php',NULL,NULL,1,0,NULL,1,NULL),('games/process/leaveGame/',NULL,NULL,'games/process/removePlayer.php',NULL,NULL,1,0,NULL,1,NULL),('games/process/maps/addCR/',NULL,NULL,'games/process/maps/addCR.php',NULL,NULL,1,0,NULL,1,NULL),('games/process/maps/delete/',NULL,NULL,'games/process/maps/delete.php',NULL,NULL,1,0,NULL,1,NULL),('games/process/maps/details/',NULL,NULL,'games/process/maps/details.php',NULL,NULL,1,0,NULL,1,NULL),('games/process/maps/editInfo/',NULL,NULL,'games/process/maps/editInfo.php',NULL,NULL,1,0,NULL,1,NULL),('games/process/maps/icons/',NULL,NULL,'games/process/maps/icons.php',NULL,NULL,1,0,NULL,1,NULL),('games/process/maps/new/',NULL,NULL,'games/process/maps/new.php',NULL,NULL,1,0,NULL,1,NULL),('games/process/maps/removeCR/',NULL,NULL,'games/process/maps/removeCR.php',NULL,NULL,1,0,NULL,1,NULL),('games/process/maps/saveDetails/',NULL,NULL,'tools/process/maps/saveDetails.php',NULL,NULL,1,0,NULL,1,NULL),('games/process/new/',NULL,NULL,'games/process/gameDetails.php',NULL,NULL,1,0,NULL,1,NULL),('games/process/pendingChar/',NULL,NULL,'games/process/pendingChar.php',NULL,NULL,1,0,NULL,1,NULL),('games/process/pendingPlayer/',NULL,NULL,'games/process/pendingPlayer.php',NULL,NULL,1,0,NULL,1,NULL),('games/process/removePlayer/',NULL,NULL,'games/process/removePlayer.php',NULL,NULL,1,0,NULL,1,NULL),('games/process/toggleGM/',NULL,NULL,'games/process/toggleGM.php',NULL,NULL,1,0,NULL,1,NULL),('login/','login',NULL,'login/login.php','Login',NULL,0,0,NULL,1,450),('login/process/login/',NULL,NULL,'login/process/login.php',NULL,NULL,0,0,NULL,1,NULL),('login/process/requestReset/',NULL,NULL,'login/process/requestReset.php',NULL,NULL,0,0,NULL,1,NULL),('login/process/resetPass/',NULL,NULL,'login/process/resetPass.php',NULL,NULL,0,0,NULL,1,NULL),('login/requestReset/','requestReset',NULL,'login/requestReset.php','Request Password Reset',NULL,0,0,NULL,1,550),('login/resetPass/','resetPass',NULL,'login/resetPass.php','Reset Password',NULL,0,0,NULL,1,NULL),('logout/',NULL,NULL,'login/process/logout.php',NULL,NULL,1,0,NULL,1,NULL),('pms/','pms','pmList','pms/list.php','Private Messages',NULL,1,0,NULL,1,NULL),('pms/ajax/userSearch/',NULL,NULL,'pms/ajax/userSearch.php',NULL,NULL,1,0,NULL,1,NULL),('pms/delete/(###)/','pm_delete','pmDelete','pms/delete.php','Delete Private Message',NULL,1,0,NULL,1,420),('pms/process/delete/',NULL,NULL,'pms/process/delete.php',NULL,NULL,1,0,NULL,1,NULL),('pms/process/reply/',NULL,NULL,'pms/process/send.php',NULL,NULL,1,0,NULL,1,NULL),('pms/process/send/',NULL,NULL,'pms/process/send.php',NULL,NULL,1,0,NULL,1,NULL),('pms/reply/','pm_send','pmSend','pms/send.php','Reply',NULL,1,0,NULL,1,NULL),('pms/send/','pm_send','pmSend','pms/send.php','Send Private Message',NULL,1,0,NULL,1,NULL),('pms/success/sent/','pms_success_sent',NULL,'pms/success/send.php','Success',NULL,1,0,NULL,1,NULL),('pms/view/','pm_view','pmView','pms/view.php','Private Message',NULL,1,0,NULL,1,NULL),('register/','register',NULL,'register/register.php','Register',NULL,0,0,NULL,1,NULL),('register/activate/','regActivate',NULL,'register/activate.php','Activate Registration',NULL,0,0,NULL,1,NULL),('register/ajax/loginSearch/',NULL,NULL,'register/ajax/loginSearch.php',NULL,NULL,0,0,NULL,1,NULL),('register/process/register/',NULL,NULL,'register/process/register.php',NULL,NULL,0,0,NULL,1,NULL),('register/success/','regSuccess',NULL,'register/success.php','Registration Success',NULL,0,0,NULL,1,NULL),('tools/','tools',NULL,'tools/tools.php','Tools',NULL,0,0,NULL,1,NULL),('acp/music/','acp_music','acp_music','acp/music.php','Site ACP - Manage Music',NULL,1,0,NULL,1,NULL),('characters/process/avatar/',NULL,NULL,'characters/process/avatar.php',NULL,NULL,1,0,NULL,1,NULL),('tools/ajax/decks/',NULL,NULL,'tools/ajax/decks.php',NULL,NULL,0,0,NULL,1,NULL),('tools/ajax/dice/',NULL,NULL,'tools/ajax/dice.php',NULL,NULL,0,0,NULL,1,NULL),('tools/ajax/newDeck/',NULL,NULL,'tools/ajax/newDeck.php',NULL,NULL,0,0,NULL,1,NULL),('tools/cards/','tools_cards',NULL,'tools/cards.php','Cards',NULL,0,0,NULL,1,NULL),('tools/chat/','chat',NULL,'tools/chat/chat.php','Chat',NULL,0,1,NULL,1,NULL),('characters/avatar/','charAvatar',NULL,'characters/avatar.php','Character Avatar',NULL,1,0,NULL,1,280),('tools/dice/','tools_dice',NULL,'tools/dice.php','Dice',NULL,0,0,NULL,1,NULL),('tools/process/cards/',NULL,NULL,'tools/process/cards.php',NULL,NULL,0,0,NULL,1,NULL),('tools/process/dice/',NULL,NULL,'tools/process/dice.php',NULL,NULL,0,0,NULL,1,NULL),('ucp/','ucp','ucp','ucp/ucp.php','User Control Panel',NULL,1,0,NULL,1,NULL),('ucp/process/changeDetails/',NULL,NULL,'ucp/process/changeDetails.php',NULL,NULL,1,0,NULL,1,NULL),('ucp/process/changeForumOptions/',NULL,NULL,'ucp/process/changeForumOptions.php',NULL,NULL,1,0,NULL,1,NULL),('ucp/process/changeInfo/',NULL,NULL,'ucp/process/changeInfo.php',NULL,NULL,1,0,NULL,1,NULL),('unauthorized/','401',NULL,'errors/401.php','Unauthorized',NULL,0,0,NULL,1,NULL),('user/','user','user','ucp/user.php','User',NULL,1,0,NULL,1,NULL),('characters/ajax/starwarsffg/talentSearch/',NULL,NULL,'characters/ajax/starwarsffg/talentSearch.php',NULL,NULL,1,0,NULL,1,NULL),('characters/ajax/starwarsffg/addTalent/',NULL,NULL,'characters/ajax/starwarsffg/addTalent.php',NULL,NULL,1,0,NULL,1,NULL),('characters/ajax/starwarsffg/removeTalent/',NULL,NULL,'characters/ajax/starwarsffg/removeTalent.php',NULL,NULL,1,0,NULL,1,NULL),('characters/ajax/starwarsffg/removeSkill/',NULL,NULL,'characters/ajax/starwarsffg/removeSkill.php',NULL,NULL,1,0,NULL,1,NULL),('tools/music/','music','music','tools/music.php','Game Music and Clips',NULL,0,0,NULL,1,NULL),('characters/library/unfavorite/',NULL,NULL,'characters/unfavorite.php','Unfavorite Character',NULL,1,0,NULL,1,400),('characters/process/favorite/',NULL,NULL,'characters/process/favorite.php',NULL,NULL,1,0,NULL,1,NULL),('characters/library/','charLibrary','charLibrary','characters/library.php','Character Library',NULL,1,0,NULL,1,NULL),('characters/process/libraryToggle/',NULL,NULL,'characters/process/libraryToggle.php',NULL,NULL,1,0,NULL,1,NULL),('characters/(system)/(###)/','characterSheet','characterSheet','characters/character.php','Character Sheet',NULL,1,1,NULL,1,NULL),('characters/(system)/(###)/edit','characterSheet_edit',NULL,'characters/editCharacter.php','Edit Character Sheet',NULL,1,1,NULL,1,NULL),('characters/ajax/addSkill/',NULL,NULL,'characters/ajax/addSkill.php',NULL,NULL,1,0,NULL,1,NULL),('characters/ajax/addFeat/',NULL,NULL,'characters/ajax/addFeat.php',NULL,NULL,1,0,NULL,1,NULL),('characters/ajax/removeSkill/',NULL,NULL,'characters/ajax/removeSkill.php',NULL,NULL,1,0,NULL,1,NULL),('characters/ajax/removeFeat/',NULL,NULL,'characters/ajax/removeFeat.php',NULL,NULL,1,0,NULL,1,NULL),('characters/(system)/(###)/editFeatNotes/(###)/','editFeatNotes',NULL,'characters/editFeatNotes.php',NULL,NULL,1,0,NULL,1,500),('characters/process/editFeatNotes/',NULL,NULL,'characters/process/editFeatNotes.php',NULL,NULL,1,0,NULL,1,NULL),('characters/process/editCharacter/',NULL,NULL,'characters/process/editCharacter.php',NULL,NULL,1,0,NULL,1,NULL),('characters/(system)/(###)/featNotes/(###)/','featNotes',NULL,'characters/featNotes.php',NULL,NULL,1,0,NULL,1,500),('characters/ajax/addWeapon/',NULL,NULL,'characters/ajax/addWeapon.php',NULL,NULL,1,0,NULL,1,NULL),('characters/ajax/addArmor/',NULL,NULL,'characters/ajax/addArmor.php',NULL,NULL,1,0,NULL,1,NULL),('characters/ajax/autocomplete/',NULL,NULL,'characters/ajax/autocomplete.php',NULL,NULL,1,0,NULL,1,NULL),('acp/','acp',NULL,'acp/index.php','Site ACP',NULL,1,0,NULL,1,NULL),('acp/autocomplete/','acp_autocomplete','acp_autocomplete','acp/autocomplete.php','Site ACP - Manage Autocomplete',NULL,1,0,NULL,1,NULL),('acp/process/newItem/',NULL,NULL,'acp/process/newItem.php',NULL,NULL,1,0,NULL,1,NULL),('acp/process/addToSystem/',NULL,NULL,'acp/process/addToSystem.php',NULL,NULL,1,0,NULL,1,NULL),('characters/ajax/addItemized/',NULL,NULL,'characters/ajax/addItemized.php',NULL,NULL,1,0,NULL,1,NULL),('games/process/toggleForumVisibility/',NULL,NULL,'games/process/toggleForumVisibility.php',NULL,NULL,1,0,NULL,1,NULL),('faqs/','faqs','faqs','faqs/list.php','FAQs',NULL,0,0,NULL,1,NULL),('acp/faqs/','acp_faqs','acp_faqs','acp/faqs.php','Site ACP - Manage FAQs',NULL,1,0,NULL,1,NULL),('about/','about','about','about.php','About Gamers\' Plane',NULL,0,0,NULL,1,NULL),('notifications/','notifications','notifications','notifications.php','Notifications',NULL,1,0,NULL,1,NULL),('acp/users/','acp_users','acp_users','acp/users.php','Site ACP - Users',NULL,1,0,NULL,1,NULL),('acp/ajax/listUsers/',NULL,NULL,'acp/ajax/listUsers.php',NULL,NULL,1,0,NULL,1,NULL),('acp/process/users/',NULL,NULL,'acp/process/users.php',NULL,NULL,1,0,NULL,1,NULL),('acp/links/','acp_links','acp_links','acp/links.php','Site ACP - Links',NULL,1,0,NULL,1,NULL),('acp/process/manageLink/',NULL,NULL,'acp/process/manageLink.php',NULL,NULL,1,0,NULL,1,NULL),('forums/process/togglePubGames/',NULL,NULL,'forums/process/togglePubGames.php',NULL,NULL,1,0,NULL,1,NULL),('forums/process/subscribe/',NULL,NULL,'forums/process/subscribe.php',NULL,NULL,1,0,NULL,1,NULL),('acp/systems/','acp_systems','acp_systems','acp/systems.php','Site ACP - Systems',NULL,1,0,NULL,1,NULL),('systems/','systems','systems','systems.php','Systems',NULL,0,0,NULL,1,NULL),('links/','links','links','links.php','Links',NULL,0,0,NULL,1,NULL),('register/resendActivation/','resendActivation',NULL,'register/resendActivation.php','Resend Activation Email',NULL,0,0,NULL,1,NULL),('register/process/resendActivation/',NULL,NULL,'register/process/resendActivation.php',NULL,NULL,0,0,NULL,1,NULL),('privacy/','privacy_policy',NULL,'privacy.php','Privacy Policy',NULL,0,0,NULL,1,NULL);
/*!40000 ALTER TABLE `dispatch` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `forumAdmins` WRITE;
/*!40000 ALTER TABLE `forumAdmins` DISABLE KEYS */;
INSERT INTO `forumAdmins` VALUES (1,0);
/*!40000 ALTER TABLE `forumAdmins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `forums`
--

LOCK TABLES `forums` WRITE;
/*!40000 ALTER TABLE `forums` DISABLE KEYS */;
INSERT INTO `forums` VALUES (0,'Index','',NULL,NULL,'',0,NULL,0),(1,'General','','c',0,'0001',1,NULL,0),(2,'Game Forums','','c',0,'0002',5,NULL,0),(3,'Announcements','Read and discuss all site announcements.','f',1,'0001-0003',1,NULL,109),(4,'General Chat','Anything not fitting into another forum goes here.','f',1,'0001-0004',2,NULL,487),(5,'Site Discussions','','f',1,'0001-0005',3,NULL,546),(6,'Game Discussions','','c',0,'0006',2,NULL,0),(7,'Pen and Paper Games','','f',6,'0006-0007',1,NULL,408),(8,'Video Games','','f',6,'0006-0008',2,NULL,84),(9,'Board/Card Games','','f',6,'0006-0009',3,NULL,27),(10,'Games Tavern','Discuss games you\'re interested in running, post about a game you\'d like to see, or recruit people for your game here.','f',2,'0002-0010',1,NULL,2585),(11,'Bugs','','f',5,'0001-0005-0011',1,NULL,422),(12,'Media','Got thoughts on books, movies, comics, etc? Share it!','f',4,'0001-0004-0012',2,NULL,8),(13,'Magic the Gathering','','f',9,'0006-0009-0013',1,NULL,2),(14,'Introductions','Introduce yourself to your fellow gamers!','f',4,'0001-0004-0014',1,NULL,1118),(15,'Wargames/Miniatures Games','','f',6,'0006-0015',4,NULL,16),(16,'Game Development','For creation of new RPGs or concept work, and homebrew creations as well...','f',6,'0006-0016',5,NULL,66),(17,'Concept Work','If you have raw ideas, or need to work soms kinks out of existing ideas.','f',16,'0006-0016-0017',1,NULL,27),(18,'Mechanics','How does it work the way it does, what is the system behind it, and how does it tick','f',16,'0006-0016-0018',2,NULL,6),(19,'Playtesting & Feedback','','f',16,'0006-0016-0019',3,NULL,15),(20,'Guides','','c',0,'0020',3,NULL,1),(21,'General Guides','','f',20,'0020-0021',1,NULL,49),(28,'AMAs','AMA - Ask Me Anything. Threads in this forum will give you the chance to ask specific other members questions on a range of topics!','f',1,'0001-0028',4,NULL,55),(26,'Advertising','','f',1,'0001-0026',5,NULL,12),(27,'Site Development','','f',5,'0001-0005-0027',2,NULL,16),(8672,'Questions and Help','','f',20,'0001-0020-8672',2,NULL,62),(9552,'Advent Calendar 22','','f',4,'0001-0004-9552',3,NULL,24);
/*!40000 ALTER TABLE `forums` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `forums_heritage`
--

LOCK TABLES `forums_heritage` WRITE;
/*!40000 ALTER TABLE `forums_heritage` DISABLE KEYS */;
INSERT INTO `forums_heritage` VALUES (1,3),(1,4),(1,5),(1,11),(1,12),(1,14),(2,10),(4,12),(4,14),(5,11),(6,7),(6,8),(6,9),(6,13),(6,15),(6,16),(6,17),(6,18),(6,19),(9,13),(16,17),(16,18),(16,19),(20,21),(22,23),(22,24),(22,25);
/*!40000 ALTER TABLE `forums_heritage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `forums_groupMemberships`
--

LOCK TABLES `forums_groupMemberships` WRITE;
/*!40000 ALTER TABLE `forums_groupMemberships` DISABLE KEYS */;
INSERT INTO `forums_groupMemberships` VALUES (1,1),(415,1);
/*!40000 ALTER TABLE `forums_groupMemberships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `forums_groups`
--

LOCK TABLES `forums_groups` WRITE;
/*!40000 ALTER TABLE `forums_groups` DISABLE KEYS */;
INSERT INTO `forums_groups` VALUES (1,'Registered Users',0,1,NULL),(415,'Admin',0,6,375),(416,'Players',0,6,375);
/*!40000 ALTER TABLE `forums_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `forums_permissions_general`
--

LOCK TABLES `forums_permissions_general` WRITE;
/*!40000 ALTER TABLE `forums_permissions_general` DISABLE KEYS */;
INSERT INTO `forums_permissions_general` VALUES (1,1,1,1,1,1,-1,0,-1,-1,-1),(2,-1,-1,-1,-1,-1,-1,0,-1,-1,-1),(3,0,0,0,-1,-1,-1,0,-1,-1,-1),(6,1,1,1,1,1,-1,0,-1,-1,-1),(10,1,0,0,0,0,0,0,-1,-1,-1);
/*!40000 ALTER TABLE `forums_permissions_general` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `forums_permissions_groups`
--

LOCK TABLES `forums_permissions_groups` WRITE;
/*!40000 ALTER TABLE `forums_permissions_groups` DISABLE KEYS */;
INSERT INTO `forums_permissions_groups` VALUES (1,10,2,2,2,2,2,2,2,2,2,-2),(415,859,2,2,2,2,2,2,0,2,2,2),(416,859,2,2,2,-2,0,-2,0,2,-2,-2);/*!40000 ALTER TABLE `forums_permissions_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `privilages`
--

LOCK TABLES `privilages` WRITE;
/*!40000 ALTER TABLE `privilages` DISABLE KEYS */;
INSERT INTO `privilages` VALUES (1,'manageMusic');
/*!40000 ALTER TABLE `privilages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `referralLinks`
--

LOCK TABLES `referralLinks` WRITE;
/*!40000 ALTER TABLE `referralLinks` DISABLE KEYS */;
INSERT INTO `referralLinks` VALUES ('amazon','Amazon','http://smile.amazon.com/?_encoding=UTF8&camp=1789&creative=9325&linkCode=ur2&tag=gampla0e6-20&linkId=7RQR4I66XH6Z2U4B',1),('dtrpg','DriveThruRPG','http://rpg.drivethrustuff.com/browse.php?affiliate_id=739399',2),('elderwood','Elderwood Academy','https://www.elderwoodacademy.com/?utm_source=Gamers-Plane',4),('erd','Easy Roller Dice Co.','http://www.shareasale.com/r.cfm?B=751134&U=1218073&M=60247&urllink=',3);
/*!40000 ALTER TABLE `referralLinks` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `systems` WRITE;
/*!40000 ALTER TABLE `systems` DISABLE KEYS */;
INSERT INTO `systems` VALUES ('13thage','13th Age','13th age',1,0,'[\"Fantasy\"]','{\"name\": \"Pelgrane Press Ltd\", \"site\": \"http://www.pelgranepress.com\"}','[{\"site\": \"http://www.amazon.com/gp/product/190898340X/ref=as_li_tl?ie=UTF8&camp=1789&creative=390957&creativeASIN=190898340X&linkCode=as2&tag=gampla0e6-20&linkId=B7FFODCXGZT3L52A\", \"text\": \"Core Book (Amazon)\"}, {\"site\": \"http://www.drivethrurpg.com/product/118994/13th-Age-Core-Book?affiliate_id=739399\", \"text\": \"Core Book (DTRPG, PDF)\"}]',1,0),('7thsea_2e','7th Sea (2e)','7th sea (2e)',1,1,'[\"Fantasy\"]','{\"name\": \"John Wick Presents\", \"site\": \"http://johnwickpresents.com/\"}','[{\"site\": \"https://www.drivethrurpg.com/product/185462/7th-Sea-Core-Rulebook-Second-Edition?affiliate_id=739399\", \"text\": \"Core Rulebook\"}]',1,0),('afmbe','All Flesh Must Be Eaten','all flesh must be eaten',1,0,'[\"Horror\"]','{\"name\": \"Eden Studios Inc.\", \"site\": \"http://edenstudios.net/\"}','[]',1,0),('cthulhu_brs7e','Call of Cthulhu (Chaosium, 7e)','call of cthulhu (chaosium, 7e)',1,1,'[\"Horror\"]','{\"name\": \"Chaosium Inc\", \"site\": \"http://chaosium.com/\"}','[{\"site\": \"http://www.drivethrurpg.com/product/150997/Call-of-Cthulhu-7th-Edition--Keepers-Rulebook?affiliate_id=739399\", \"text\": \"Call of Cthulhu 7e - Keeper\'s Handbook\"}]',1,0),('custom','Custom','custom',1,1,'[]','{\"name\": \"You!\", \"site\": null}','[]',1,0),('d20cthulhu','d20 Call of Cthulhu','d20 call of cthulhu',1,0,'[\"Horror\"]','{\"name\": \"Wizards of the Coast\", \"site\": \"http://www.wizards.com\"}','[]',1,0),('deadlands','Deadlands','deadlands',1,0,'[\"Steampunk\"]','{\"name\": \"Pinnacle Entertainment Group\", \"site\": \"https://www.peginc.com/\"}','[]',1,0),('dnd3','Dungeons &amp; Dragons 3/3.5','dungeons &amp; dragons 3/3.5',1,0,'[]','{\"name\": \"Wizards of the Coast\", \"site\": \"http://www.wizards.com\"}','[]',1,0),('dnd4','Dungeons &amp; Dragons 4th','dungeons &amp; dragons 4th',1,0,'[]','{\"name\": \"Wizards of the Coast\", \"site\": \"http://www.wizards.com\"}','[]',1,0),('dnd5','Dungeons &amp; Dragons 5th','dungeons &amp; dragons 5th',1,0,'[]','{\"name\": \"Wizards of the Coast\", \"site\": \"http://www.wizards.com\"}','[{\"site\": \"http://www.amazon.com/gp/product/0786965606/ref=as_li_tl?ie=UTF8&camp=1789&creative=390957&creativeASIN=0786965606&linkCode=as2&tag=gampla0e6-20&linkId=32O7J52S2MQAP2YL\", \"text\": \"Players Handbook\"}]',1,0),('dresden','Dresden Files RPG','dresden files rpg',1,0,'[]','{\"name\": \"Evil Hat Productions\", \"site\": \"http://www.evilhat.com\"}','[]',1,0),('dungeonworld','Dungeon World','dungeon world',1,1,'[\"Fantasy\"]','{\"name\": \"Sage Kobold\", \"site\": \"http://www.dungeon-world.com/\"}','[{\"site\": \"http://www.drivethrurpg.com/product/108028/Dungeon-World?affiliate_id=739399\", \"text\": \"Dungeon World PDF\"}]',1,0),('fae','Fate Accelerated','fate accelerated',1,1,'[]','{\"name\": \"Evil Hat Productions\", \"site\": \"http://www.evilhat.com\"}','[]',1,0),('fate','Fate Core','fate core',1,0,'[]','{\"name\": \"Evil Hat Productions\", \"site\": \"http://www.evilhat.com\"}','[]',1,0),('gurps','GURPS','gurps',1,0,'[]','{\"name\": \"Steve Jackson Games\", \"site\": \"http://www.sjgames.com\"}','[]',1,0),('hellfrost','Hellfrost','hellfrost',1,0,'[]','{\"name\": \"Triple Ace Games\", \"site\": \"http://www.tripleacegames.com\"}','[]',1,0),('identeco','Identeco','identeco',1,1,'[\"Cyberpunk\"]','{\"name\": \"Humanoid Games\", \"site\": \"http://www.playidenteco.com/\"}','[]',1,0),('marvel','Marvel','marvel',1,0,'[]','{\"name\": \"Marvel Comics\", \"site\": null}','[]',1,0),('numenera','Numenera','numenera',1,1,'[]','{\"name\": \"Monte Cook Games\", \"site\": \"http://www.montecookgames.com/\"}','[{\"site\": \"http://www.amazon.com/gp/product/1939979005/ref=as_li_tl?ie=UTF8&camp=1789&creative=390957&creativeASIN=1939979005&linkCode=as2&tag=gampla0e6-20&linkId=23TDF4NGF7UKFSOK\", \"text\": \"Core Book (Amazon)\"}]',1,0),('pathfinder','Pathfinder','pathfinder',1,0,'[]','{\"name\": \"Paizo Inc\", \"site\": \"http://www.paizo.com\"}','[]',1,0),('pathfinder2e','Pathfinder2e','pathfinder2e',1,0,'[]','{\"name\": \"Paizo Inc\", \"site\": \"http://www.paizo.com\"}','[]',1,0),('pbta','Powered by the Apocalypse','powered by the apocalypse',1,0,'[]','{\"name\": \"Meguey Baker and Vincent Baker\", \"site\": \"http://apocalypse-world.com/pbta/\"}','[]',1,0),('primeval','Primeval','primeval',1,0,'[\"Fantasy\"]','{\"name\": \"Cubicle 7\", \"site\": \"http://www.cubicle7.co.uk/our-games/primeval/\"}','[]',1,0),('savageworlds','Savage Worlds','savage worlds',1,0,'[]','{\"name\": \"Pinnacle Entertainment Group\", \"site\": \"https://www.peginc.com/\"}','[]',1,0),('shadowrun4','Shadowrun (4e)','shadowrun (4th)',1,0,'[\"Cyberpunk\"]','{\"name\": \"Catalyst Game Labs\", \"site\": \"http://www.catalystgamelabs.com/\"}','[]',1,0),('shadowrun5','Shadowrun (5e)','shadowrun (5e)',1,1,'[]','{\"name\": \"Catalyst Game Labs\", \"site\": \"http://www.catalystgamelabs.com\"}','[]',1,0),('spycraft','Spycraft (1st Edition)','spycraft (1st edition)',1,0,'[\"Spy\"]','{\"name\": \"Crafty Games\", \"site\": \"http://www.crafty-games.com\"}','[]',1,0),('spycraft2','Spycraft 2.0','spycraft 2.0',1,0,'[\"Spy\"]','{\"name\": \"Crafty Games\", \"site\": \"http://www.crafty-games.com\"}','[]',1,0),('starwarsffg','Star Wars FFG','star wars ffg',1,0,'[]','{\"name\": \"Fantasy Flight Games\", \"site\": \"https://www.fantasyflightgames.com/\"}','[]',1,0),('tftf','Things from the Flood','things from the flood',1,0,'[\"Sci-Fi\"]','{\"name\": \"Free League Publishing\", \"site\": \"https://freeleaguepublishing.com/\"}','[]',1,0),('tftl','Tales from the Loop','tales from the loop',1,0,'[\"Sci-Fi\"]','{\"name\": \"Free League Publishing\", \"site\": \"https://freeleaguepublishing.com/\"}','[]',1,0),('thestrange','The Strange','the strange',1,0,'[\"Fantasy\"]','{\"name\": \"Monte Cook Games\", \"site\": \"http://www.montecookgames.com/\"}','[]',1,0),('tor','The One Ring','tor',1,1,'[\"Fantasy\"]','{\"name\": \"Cubicle 7\", \"site\": \"http://cubicle7.co.uk\"}','[]',1,0),('wod','World of Darkness','world of darkness',1,0,'[]','{\"name\": \"White Wolf Publishing\", \"site\": \"http://www.white-wolf.com/\"}','[]',1,0);
/*!40000 ALTER TABLE `systems` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Dumping data for table `usermeta`
--

LOCK TABLES `usermeta` WRITE;
/*!40000 ALTER TABLE `usermeta` DISABLE KEYS */;
INSERT INTO `usermeta` VALUES (1,1,'enableFilter','1',1),(2200,1,'showAvatars','0',0),(828,1,'twitter','GamersPlane',0),(4,1,'showTZ','0',0),(5,1,'gender','m',0),(6,1,'birthday','1986-08-08',0),(8,1,'location','Edison, NJ',0),(9,1,'stream','http://twitch.tv/gamersplane',0),(10,1,'newGameMail','1',0),(11,1,'postSide','r',1),(12,2,'reference','From me!',0),(13,2,'enableFilter','1',1),(14,2,'showAvatars','1',1),(15,2,'showTZ','0',0),(16,2,'showAge','0',0),(17,2,'newGameMail','1',0),(18,2,'postSide','r',1),(19,3,'reference','no',0),(20,3,'enableFilter','1',1),(21,3,'showAvatars','1',1),(22,3,'showTZ','0',0),(23,3,'showAge','0',0),(24,3,'newGameMail','1',0),(25,3,'postSide','r',1),(931,1,'acpPermissions','a:1:{i:0;s:3:\"all\";}',1),(1762,1,'showPubGames','0',1),(2201,1,'showAge','0',0),(2202,1,'avatarExt','png',1),(2791,1,'pmMail','1',0),(2792,1,'gmMail','1',0),(2793,2,'pmMail','1',0),(2794,2,'gmMail','1',0),(2795,3,'pmMail','1',0),(2796,3,'gmMail','1',0),(3498,2,'showPubGames','1',1),(9775,3,'showPubGames','1',1),(9941,1,'isGM','1',0),(14994,3,'isGM','1',0);
/*!40000 ALTER TABLE `usermeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Keleth','edee9d4a3f5bfed8c99871d446f312fb4bfdb8e8dea3b8f389e90fd558df8d2b','yLJDqQknboELqp7DBmYk','rohit@rhovisions.com','2013-12-01 16:02:27','2013-12-01 16:02:27','2024-10-04 20:04:18',1,'America/New_York',NULL,0),(2,'Irdalth','40211cae0c41704f3ee9f32c214ade5fe5311860e5706fc4f5d9873639aafc73','07kHQ3yIJh6fLyOeoDHk','rohit@rhovisions.com','2013-12-01 16:02:27','2013-12-01 16:02:27','2016-01-24 00:40:49',1,'Europe/London',NULL,0),(3,'GPTest','0f1442dd3f6bedf7e9ec6681995510516389232b05494a9ee0ec5a17029480cb','H9X38HWsKkdR9K9Jrbz1','contact@gamersplane.com','2013-12-01 16:02:27','2013-12-01 16:02:27','2018-03-29 19:47:56',1,'Europe/London',NULL,0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `wordFilter`
--

LOCK TABLES `wordFilter` WRITE;
/*!40000 ALTER TABLE `wordFilter` DISABLE KEYS */;
INSERT INTO `wordFilter` VALUES (1,'ass',1),(2,'fuck',1),(3,'bitch',1),(4,'cunt',1),(5,'penis',1),(6,'butt',1),(7,'sex',1);
/*!40000 ALTER TABLE `wordFilter` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-10-04 20:26:11
