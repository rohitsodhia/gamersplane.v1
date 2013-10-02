<?
/*
	Gamers Plane Functions
	Created by RhoVisions, designer Rohit Sodhia
*/

/* General Functions */
	function randomAlphaNum($length) {
		$validChars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$randomStr = "";
		for ($count = 0; $count < $length; $count++) $randomStr .= $validChars[rand(0, strlen($validChars) - 1)];
		
		return $randomStr;
	}
	
	function tabOrder($jump = 0) {
		global $tabNum;
		
		$jump += 1;
		
		if (isset($tabNum)) $tabNum += $jump;
		else $tabNum = 1;
		
		return $tabNum;
	}
	
	function camelcase($strings) {
		if (!is_array($strings)) $strings = explode(' ', $strings);
		$first = TRUE;
		$finalString = '';
		
		foreach ($strings as $indivString) {
			$indivString = strtolower($indivString);
			
			if ($first) $first = FALSE;
			else $indivString[0] = strtoupper($indivString[0]);
			
			$finalString .= $indivString;
		}
		
		return $finalString;
	}
	
	function sanitizeString($string) {
		$options = func_get_args();
		array_shift($options);
//		if (sizeof($options) == 0) $options = array('strip_tags');

		/*if (in_array('trim', $options)) */$string = trim($string);
		/*if (in_array('strip_tags', $options)) */$string = strip_tags($string);
		if (in_array('lower', $options)) $string = strtolower($string);
		if (in_array('like_clean', $options)) $string = str_replace(array('%', '_'), array('\%', '\_'), strip_tags($string));
		if (in_array('rem_dup_spaces', $options)) $string = preg_replace('/\s+/', ' ', $string);

		if (in_array('search_format', $options)) {
			$string = strtolower($string);
//			$string = str_replace('-', ' ', $string)
			$string = preg_replace('/[^A-za-z0-9]/', ' ', $string);
		}

		return $string;
	}

	function printReady($string, $options = array('stripslashes', 'nl2br')) {
		if (in_array('nl2br', $options)) {
			$string = str_replace('\r\n', "\n", $string);
			$string = nl2br($string);
		}
		if (in_array('stripslashes', $options)) $string = stripslashes($string);
		
		return $string;
	}
	
	function filterString($string) {
		global $mysql;
		$filters = $mysql->query('SELECT word FROM wordFilter');
		$filterWords = array();
		$replacements = array();
		$stars = '';
		while ($word = $filters->fetchColumn()) {
//			$filterWords[] = '/'.preg_replace('/(.*)(\[\^\\\w\\\d\]\*\\\s\?)/', '$1', preg_replace('/(.{1})/', '$1[^\w\d]*\s?', $word)).'/i';
			$filterWords[] = '/(\W|\s|^)'.preg_replace('/(.*)\\\s\?/', '$1', preg_replace('/(.*)(\[\^\\\w\\\d\]\*\\\s\?)/', '$1', preg_replace('/(.{1})/', '$1[^\w\d]*\s?', $word))).'(\W|\s|$)/i';
			$stars = '$1';
			for ($count = 0; $count < strlen($word); $count++) $stars .= '*';
			$stars .= '$2';
			$replacements[] = $stars;
		}
//		print_r($filterWords); echo '<br>';
		do { $string = preg_replace($filterWords, $replacements, $string); } while ($string != preg_replace($filterWords, $replacements, $string));
		return $string;
	}
	
	function switchTimezone($timezone = 'GMT', $dateTime = '0000-00-00 00:00:00') {
		if ($dateTime == '0000-00-00 00:00:00') $dateTime = date('Y-m-d H:i:s');
		$date = new DateTime($dateTime, new DateTimeZone('GMT'));
		$date->setTimezone(new DateTimeZone($timezone));
		return strtotime($date->format('Y-m-d H:i:s'));
	}
	
	function showSign($num) {
		return ($num >= 0?'+':'').$num;
	}
	
	function decToB26($num) {
		$str = '';
		while ($num > 0) {
			$charNum = ($num - 1) % 26;
			$str = chr($charNum + 97).$str;
			$num = floor(($num - $charNum)/26);
		}
		
		return $str;
	}
	
	function b26ToDec($str) {
		$num = 0;
		$str = strtolower($str);
		for ($count = 0; $count < strlen($str); $count++) $num += (ord($str[strlen($str) - 1 - $count]) - 96) * pow(26, $count);
		
		return $num;
	}

/* Character Functions */
	function includeSystemInfo($system) {
		if (is_dir(FILEROOT.'/includes/characters/'.$system)) 
			foreach (glob(FILEROOT.'/includes/characters/'.$system.'/*') as $file) 
				include($file);
	}

	function getCharInfo($characterID, $system) {
		global $mysql;
		
		$characterID = intval($characterID);
		$userID = intval($_SESSION['userID']);
		$checkSystem = $mysql->prepare('SELECT systemID FROM systems WHERE shortName = :system');
		$checkSystem->execute(array(':system' => $system));
		if ($checkSystem->rowCount()) {
			$charInfo = $mysql->query("SELECT cd.*, c.userID, gms.primaryGM IS NOT NULL isGM FROM {$system}_characters cd INNER JOIN characters c ON cd.characterID = c.characterID LEFT JOIN (SELECT gameID, primaryGM FROM players WHERE isGM = 1 AND userID = $userID) gms ON c.gameID = gms.gameID WHERE cd.characterID = $characterID");
			if ($charInfo->rowCount()) return $charInfo->fetch();
			else return false;
		} else return false;
	}

	function addCharacterHistory($characterID, $action, $enactedBy = 0, $enactedOn = 'NOW()', $additionalInfo = '') {
		global $mysql;
		if ($enactedBy == 0 && checkLogin(0)) $enactedBy = intval($_SESSION['userID']);

		if (!isset($enactedBy) || !intval($characterID) || !strlen($action)) return false;
		if ($enactedOn == '') $enactedOn = 'NOW()';

		$addCharHistory = $mysql->prepare("INSERT INTO characterHistory (characterID, enactedBy, enactedOn, action, additionalInfo) VALUES ($characterID, $enactedBy, ".($enactedOn == 'NOW()'?'NOW()':':enactedOn').", :action, :additionalInfo)");
		if ($enactedOn != 'NOW()') $addCharHistory->bindvalue(':enactedOn', $enactedOn);
		$addCharHistory->bindvalue(':action', $action);
		$addCharHistory->bindvalue(':additionalInfo', $additionalInfo);
		$addCharHistory->execute();
	}
	
	function addGameHistory($gameID, $action, $enactedBy = 0, $enactedOn = 'NOW()', $affectedType = NULL, $affectedID = NULL) {
		global $mysql;
		if ($enactedBy == 0 && checkLogin(0)) $enactedBy = intval($_SESSION['userID']);

		if (!isset($enactedBy) || !intval($gameID) || !strlen($action)) return false;
		if ($enactedOn == '') $enactedOn = 'NOW()';

		$addGameHistory = $mysql->prepare("INSERT INTO gameHistory (gameID, enactedBy, enactedOn, action, affectedType, affectedID) VALUES ($gameID, $enactedBy, ".($enactedOn == 'NOW()'?'NOW()':':enactedOn').", :action, :affectedType, :affectedID)");
		if ($enactedOn != 'NOW()') $addGameHistory->bindvalue(':enactedOn', $enactedOn);
		$addGameHistory->bindvalue(':action', $action);
		$addGameHistory->bindvalue(':affectedType', $affectedType);
		$addGameHistory->bindvalue(':affectedID', $affectedID);
		$addGameHistory->execute();
	}
	
/* Tools Functions */
	function parseRolls($rawRolls) {
		$rolls = array();
		preg_match_all('/\d+d\d+([+-]\d+)?/', $rawRolls, $rolls);
		if (sizeof($rolls[0])) return $rolls[0];
		else return FALSE;
	}
	
	function rollDice($roll, $rerollAces = 0) {
		list($numDice, $diceType) = explode('d', str_replace(' ', '', trim($roll)));
		$numDice = intval($numDice);
		if (strpos($diceType, '-')) { list($diceType, $modifier) = explode('-', $diceType); $modifier = intval('-'.$modifier); }
		elseif (strpos($diceType, '+')) list($diceType, $modifier) = explode('+', $diceType);
		else $modifier = 0;
		$diceType = intval($diceType);
		if ($numDice > 0 && $diceType > 1 && $numDice <= 1000 && $diceType <= 1000) {
			
			$totalRoll = $modifier;
//			$indivRolls = array();
			$first = TRUE;
			$firstAce = TRUE;
			$aced;
			$indivRolls = '( ';
			for ($rollCount = 1; $rollCount <= $numDice; $rollCount++) {
				$aced = FALSE;
				
				$curRoll = mt_rand(1, $diceType);
//				$indivRolls[] = $curRoll;
				$totalRoll += $curRoll;
				
				if ($rerollAces && $curRoll == $diceType) { $aced = TRUE; $rollCount -= 1; }
				$indivRolls .= (!$first?', ':'').(($firstAce && $aced)?'[ ':'').$curRoll;
				if ($firstAce && $aced) $firstAce = FALSE;
				elseif (!$firstAce && !$aced) { $indivRolls .= ' ]'; $firstAce = TRUE; }
				
				if ($first) $first = FALSE;
			}
			$indivRolls .= ' )'.($modifier >= 0?' + ':' - ').intval(abs($modifier));//.' = '.$totalRoll;
			
			return array('total' => $totalRoll, 'indivRolls' => $indivRolls, 'numDice' => $numDice, 'diceType' => $diceType, 'modifier' => $modifier);
		} else return FALSE;
	}
	
	function newGlobalDeck($deckType) {
		global $mysql;
		$deckCheck = $mysql->prepare("SELECT short, name, deckSize FROM deckTypes WHERE short = :short");
		$deckCheck->execute(array(':short' => $deckType));
		if ($deckCheck->rowCount()) {
			$deckInfo = $deckCheck->fetch();
			$_SESSION['deckShort'] = $deckType;
			$_SESSION['deckName'] = $deckInfo['name'];
			$_SESSION['deck'] = array_fill(1, $deckInfo['deckSize'], 1);
			
			return array($deckShort, $deckInfo['name'], $deckInfo['deckSize']);
		} else return FALSE;
	}
	
	function clearGlobalDeck($deckType) {
		unset($_SESSION['deckShort'], $_SESSION['deckName'], $_SESSION['deck']);
		
		return TRUE;
	}
	
	function cardText($card, $deck) {
		if ($deck == 'pc') {
			if ($card <= 52) {
				$suit = array('Hearts', 'Spades', 'Diamonds', 'Clubs');
				$cardNum = $card - (floor(($card - 1)/13) * 13);
				
				if ($cardNum == 1) $cardNum = 'Ace';
				elseif ($cardNum == 11) $cardNum = 'Jack';
				elseif ($cardNum == 12) $cardNum = 'Queen';
				elseif ($cardNum == 13) $cardNum = 'King';
				
				return $cardNum.' of '.$suit[floor(($card - 1)/13)];
			} elseif ($card == 53) return 'Black Joker';
			elseif ($card == 54) return 'Red Joker';
		}
	}
	
	function getCardImg($cardNum, $deckType, $size = '') {
		global $mysql;

		$deckInfo = $mysql->prepare("SELECT class, image FROM deckTypes WHERE short = :short");
		$deckInfo->execute(array(':short' => $deckType));
		$deckInfo = $deckInfo->fetch();

		$classes = '';

		if ($deckInfo['class'] == 'pc') {
			if ($cardNum <= 52) {
				$suit = array('hearts', 'spades', 'diamonds', 'clubs');
				$classes = $cardNum - (floor(($cardNum - 1)/13) * 13);
				
				if ($classes == 1) $classes = 'A';
				elseif ($classes == 11) $classes = 'J';
				elseif ($classes == 12) $classes = 'Q';
				elseif ($classes == 13) $classes = 'K';

				$classes = 'num_'.$classes;
				
				$classes .= ' '.$suit[floor(($cardNum - 1)/13)];
			} elseif ($classes == 53) return 'blackJoker';
			elseif ($classes == 54) return 'redJoker';
		}

		return '<div class="cardWindow deck_'.$deckInfo['class'].'"><img src="'.SITEROOT.'/images/tools/cards/'.$deckInfo['image'].'.png" title="'.cardText($cardNum, $deckInfo['class']).'" alt="'.cardText($cardNum, $deckInfo['class']).'" class="'.$classes.'"></div>';
//		return '<div class="cardWindow deck_'.$deckInfo['class'].($size != ''?' mini':'').'"><img src="'.SITEROOT.'/images/tools/cards/'.$deckInfo['image'].($size != ''?'_mini':'').'.png" title="'.cardText($cardNum, $deckInfo['class']).'" alt="'.cardText($cardNum, $deckInfo['class']).'" class="'.$classes.'"></div>';
	}

	
/* Character Functions */
	
/* Forum Functions */
	function retrieveHeritage($forumID, $parent = 0) {
		global $mysql;
		$level = 0;
		$family = array();
		
		if ($parent == 1) {
			$children = $mysql->query('SELECT forumID FROM forums WHERE parentID = '.$forumID);
			while ($hForumID = $children->fetchColumn()) $family[$hForumID] = 0;
			$level = 1;
		}
		
		$heritage = $mysql->query('SELECT heritage FROM forums WHERE forumID = '.$forumID);
		$heritage = $heritage->fetchColumn();
		$heritage = array_reverse(explode('-', $heritage));
		foreach ($heritage as $hForumID) {
			$family[$hForumID] = $level;
			$level++;
		}
		
		return $family;
	}
	
	function retrievePermissions($userID, $forumIDs = NULL, $types, $returnSDA = 0) {
		global $mysql;
		$userID = intval($userID);
		if (!is_array($forumIDs)) $forumIDs = array($forumIDs);
		$queryColumn = array('permissions' => '', 'general' => '', 'group' => '');
		if ($types == '') $types = array('read', 'write', 'editPost', 'deletePost', 'createThread', 'deleteThread', 'addPoll', 'addRolls', 'addDraws', 'moderate');
		elseif (is_string($types)) $types = preg_split('/\s*,\s*/', $types);
		
		foreach ($types as $value) {
			$queryColumn['permissions'] .= "`$value`, ";
			$queryColumn['group'] .= "groupsP.`$value`, ";
			$bTemplate[$value] = 0;
			$bTemplate[$value.'_priority'] = 0;
			$aTemplate[$value] = 1;
		}
		$queryColumn['permissions'] = substr($queryColumn['permissions'], 0, -2);
		$queryColumn['group'] = substr($queryColumn['group'], 0, -2);
		
		$forumInfos = $mysql->query('SELECT forumID, heritage FROM forums WHERE forumID IN ('.implode(', ', $forumIDs).')');
		$allForumIDs = $forumIDs;
		while (list($indivForumID, $heritage) = $forumInfos->fetch()) {
			$heritages[$indivForumID] = explode('-', $heritage);
			$intValHolder = array();
			foreach ($heritages[$indivForumID] as $hForumID) {
				$intValHolder[] = intval($hForumID);
				$allForumIDs[] = intval($hForumID);
			}
			$heritages[$indivForumID] = $intValHolder;
		}
		$allForumIDs = array_unique($allForumIDs);
		sort($allForumIDs);
		
		$adminForums = array();
		$adminIn = $mysql->query("SELECT forumID FROM forumAdmins WHERE userID = $userID AND forumID IN (0, ".implode(', ', $allForumIDs).')');
		while (list($adminForumID) = $adminIn->fetch()) $adminForums[] = $adminForumID;
//		$forumString = 'IN (0, '.implode(', ', array_diff($allForumIDs, $adminForums)).')';
		
		if (in_array(0, $adminForums)) foreach ($forumIDs as $forumID) $permissions[$forumID] = $aTemplate;
		else {
			$forumString = implode(', ', $allForumIDs);
			if (sizeof($allForumIDs) == 1) $forumString = '= '.$forumString;
			else $forumString = 'IN ('.$forumString.')';
			$permissionsInfos = $mysql->query('SELECT type, forumID, '.$queryColumn['permissions'].' FROM (SELECT type, forumID, '.$queryColumn['permissions'].' FROM forums_permissions WHERE type = "general" OR (type = "user" AND typeID = "'.$userID.'") UNION SELECT groupsP.type, groupsP.forumID, '.$queryColumn['group'].' FROM forums_permissions AS groupsP, forums_groupMemberships AS membership WHERE membership.groupID = groupsP.typeID AND membership.userID = '.$userID.' AND groupsP.type = "group") AS permissions WHERE forumID '.$forumString.' ORDER BY forumID');
			$rawPermissions = array();
			foreach ($permissionsInfos as $permissionInfo) {
				$permissionForumID = $permissionInfo['forumID'];
				if ($permissionInfo['type'] == 'user') $priority = 3;
				elseif ($permissionInfo['type'] == 'group') $priority = 2;
				elseif ($permissionInfo['type'] == 'general') $priority = 1;
				
				if (!isset($rawPermissions[$permissionForumID])) $rawPermissions[$permissionForumID] = $bTemplate;
				
				foreach ($types as $type) { if ($priority >= $rawPermissions[$permissionForumID][$type.'_priority'] && $permissionInfo[$type] != 0) {
					$rawPermissions[$permissionForumID][$type] = $permissionInfo[$type];
					$rawPermissions[$permissionForumID][$type.'_priority'] = $priority;
				} }
			}
			
			foreach ($forumIDs as $forumID) {
				if (isset($heritages[$forumID]) && sizeof(array_intersect($heritages[$forumID], $adminForums))) $rawPermissions[$forumID] = $aTemplate;
				else {
					if (!isset($rawPermissions[$forumID])) $rawPermissions[$forumID] = $bTemplate;
					$currentParent = 1;
					$rHeritage = array_reverse($heritages[$forumID]);
					while (in_array(0, $rawPermissions[$forumID]) && $currentParent < sizeof($rHeritage)) {
						if (isset($rawPermissions[$rHeritage[$currentParent]])) { foreach (array_keys($rawPermissions[$forumID], 0) as $type) {
							if ($rawPermissions[$rHeritage[$currentParent]][$type] != 0) $rawPermissions[$forumID][$type] = $rawPermissions[$rHeritage[$currentParent]][$type];
						} }
						$currentParent++;
					}
				}
			}
			
			global $loggedIn;
			foreach ($forumIDs as $forumID) {
				foreach ($rawPermissions[$forumID] as $key => $value) if (strpos($key, '_priority')) unset($rawPermissions[$forumID][$key]);
				$permissions[$forumID] = $rawPermissions[$forumID];
				foreach ($types as $type) if ($permissions[$forumID][$type] != 1 || (!$loggedIn && $type != 'read')) $permissions[$forumID][$type] = 0;
			}
		}
		
		if (sizeof($forumIDs) == 1 && $returnSDA) return $permissions[$forumIDs[0]];
		else return $permissions;
	}
	
/*	function retrievePermissions_new($userID, $types, $forumIDs, $returnSDA = FALSE) {
		$mysql = new mysqlConnection();
		$userID = intval($userID);
		$mysql->query(sql_forumPermissions($userID, $types, $forumIDs));
		$permissions = array();
		while ($permission = $mysql->fetch()) {
			$permissions[$permission['forumID']] = $permission;
			unset($permissions[$permission['forumID']]['forumID']);
		}*/
//		if (!is_array($forumIDs)) $forumIDs = array($forumIDs);
//		if ($types == '') $types = array('read', 'write', 'editPost', 'deletePost', 'createThread', 'deleteThread', 'addPoll', 'addRolls', 'addDraws', 'moderate');
//		elseif (is_string($types)) $types = preg_split('/\s*,\s*/', $types);
		
/*		foreach ($types as $value) {
			$queryColumn['permissions'] .= "`$value`, ";
			$bTemplate[$value] = 0;
			$bTemplate[$value.'_priority'] = 0;
			$aTemplate[$value] = 1;
		}
		$queryColumn['permissions'] = substr($queryColumn['permissions'], 0, -2);
		
		$mysql->query('SELECT forumID, heritage FROM forums WHERE forumID IN ('.implode(', ', $forumIDs).')');
		$allForumIDs = $forumIDs;
		$heritages = array();
		while (list($indivForumID, $heritage) = $mysql->getList()) {
			$heritages[$indivForumID] = explode('-', $heritage);
			$intValHolder = array();
			foreach ($heritages[$indivForumID] as $hForumID) {
				$intValHolder[] = intval($hForumID);
				$allForumIDs[] = intval($hForumID);
			}
			$heritages[$indivForumID] = $intValHolder;
		}
		$allForumIDs = array_unique($allForumIDs);
		sort($allForumIDs);
		
		$adminForums = array();
		$mysql->query("SELECT forumID FROM forumAdmins WHERE userID = $userID AND forumID IN (0, ".implode(', ', $allForumIDs).')');
		while (list($adminForumID) = $mysql->fetch()) $adminForums[] = $adminForumID;
		
		if (in_array(0, $adminForums)) foreach ($forumIDs as $forumID) $permissions[$forumID] = $aTemplate;
		else {
			$forumIDsString = implode(', ', $allForumIDs);
			if (sizeof($allForumIDs) == 1) $forumIDsString = '= '.$forumIDsString;
			else $forumIDsString = 'IN ('.$forumIDsString.')';
			$mysql->query('SELECT forumID, '.$queryColumn['permissions'].' FROM forums_permissions_c WHERE forumID '.$forumIDsString.' ORDER BY forumID');
			$iPermissions = array();
			while ($permissionInfo = $mysql->fetch()) $iPermissions[$permissionInfo['forumID']] = $permissionInfo;
			
			foreach ($forumIDs as $forumID) {
				if (isset($heritages[$forumID]) && sizeof(array_intersect($heritages[$forumID], $adminForums))) $iPermissions[$forumID] = $aTemplate;
				else {
					if (!isset($iPermissions[$forumID])) $iPermissions[$forumID] = $bTemplate;
					$currentParent = 1;
					$rHeritage = array_reverse($heritages[$forumID]);
					while (in_array(0, $iPermissions[$forumID]) && $currentParent < sizeof($rHeritage)) {
						if (isset($iPermissions[$rHeritage[$currentParent]])) { foreach (array_keys($iPermissions[$forumID], 0) as $type) {
							if ($iPermissions[$rHeritage[$currentParent]][$type] != 0) $iPermissions[$forumID][$type] = $iPermissions[$rHeritage[$currentParent]][$type];
						} }
						$currentParent++;
					}
				}
			}
			
			global $loggedIn;
			foreach ($forumIDs as $forumID) {
				$permissions[$forumID] = $iPermissions[$forumID];
				foreach ($types as $type) if ($permissions[$forumID][$type] != 1 || (!$loggedIn && $type != 'read')) $permissions[$forumID][$type] = 0;
			}
		}
		
//		if (is_numeric($forumIDs) == 1 && $returnSDA) return $permissions[$forumIDs];
//		else return $permissions;
	}*/
	
/*	function checkNewPosts($forumID, $forumRD, $threadRD, $latestPost, $indivLatestPosts, $permissions, $hasChildren) {
		$mysql = new mysqlConnection();
		$markedRead = array($forumID => $forumRD[0]);
		$scanForums = array($forumID);
		
		$lastPull = 0;
		$indivLatestPulls = array($forumID => 0);
		
		$heritageInfos = $mysql->query('SELECT forumID, parentID, heritage FROM forums WHERE heritage LIKE "%'.str_pad($forumID, 3, '0', STR_PAD_LEFT).'%" ORDER BY heritage');
		foreach ($heritageInfos as $info) { if ($permissions[$info['forumID']]['read']) {
			if (strpos($info['heritage'], str_pad($forumID, 3, '0', STR_PAD_LEFT).'-') !== FALSE){
				$scanForums[] = $info['forumID'];
				$indivLatestPulls[$info['forumID']] = 0;
				$markedRead[$info['forumID']] = $forumRD[$info['forumID']] > $markedRead[$info['parentID']]?$forumRD[$info['forumID']]:$markedRead[$info['parentID']];
			} elseif (isset($forumRD[$info['forumID']]) && $forumRD[$info['forumID']] > $markedRead[$forumID]) $markedRead[$forumID] = $forumRD[$info['forumID']];
		} }
		if ($markedRead[$forumID] >= $latestPost) return FALSE;
		
		foreach ($threadRD as $threadID => $threadInfo) { if (in_array($threadInfo['forumID'], $scanForums)) {
			if ($threadInfo['lastRead'] < $threadInfo['lastPost'] && $threadInfo['lastPost'] > $markedRead[$threadInfo['forumID']]) return TRUE;
			if($indivLatestPulls[$threadInfo['forumID']] < $threadInfo['lastPost']) $indivLatestPulls[$threadInfo['forumID']] = $threadInfo['lastPost'];
		} }
		
		foreach ($scanForums as $sForumID) if ($indivLatestPulls[$sForumID] < $indivLatestPosts[$sForumID] && $markedRead[$sForumID] < $indivLatestPosts[$sForumID]) return TRUE;
		
		return FALSE;
	}
	
	function checkNewPosts_new($forumID, $readData, $permissions, $children) {
		$mysql = new mysqlConnection();
		
		if (($readData[$forumID]['unreadThreads'] || $readData[$forumID]['lastPostID'] > $readData[$forumID]['cLastRead'] && $readData[$forumID]['cLastRead'] > $readData[$forumID]['lastPostRead']) && $permissions[$forumID]['read'] == 1) return TRUE;
		else foreach ($children as $cForumID) if (($readData[$cForumID]['unreadThreads'] || $readData[$cForumID]['lastPostID'] > $readData[$cForumID]['cLastRead'] && $readData[$cForumID]['cLastRead'] > $readData[$cForumID]['lastPostRead']) && $permissions[$cForumID]['read'] == 1) return TRUE;
		
		return FALSE;
	}*/
	
/* MySQL Functions */
	function setupInserts() {
		$columns = '';
		$values = '';
		
		$args = func_get_args();
		if (func_num_args() == 1 && !is_array(current($args[0]))) {
			$inserts = func_get_arg(0);
			foreach ($inserts as $key => $value) {
				$columns .= ', `'.$key.'`';
				if (is_numeric($value)) $values .= ", $value";
				else $values .= ', "'.$value.'"';
			}
			
			$columns = substr($columns, 2);
			$values = substr($values, 2);
			$insertStr = "({$columns}) VALUES ({$values})";
		} elseif (func_num_args() == 1 && is_array(current($args[0]))) {
			$args = $args[0];
			$insertStr = '(';
			$first = TRUE;
			foreach ($args as $inserts) {
				if ($first) {
					$values = '';
					foreach ($inserts as $key => $value) {
						$insertStr .= "`$key`, ";
						if (is_numeric($value)) $values .= "$value, ";
						else $values .= '"'.$value.'", ';
					}
					$insertStr = substr($insertStr, 0, -2).') VALUES ('.substr($values, 0, -2).'), ';
					$first = FALSE;
				} else {
					$values = '';
					foreach ($inserts as $value) {
						if (is_numeric($value)) $values .= "$value, ";
						else $values .= '"'.$value.'", ';
					}
					$insertStr .= '('.substr($values, 0, -2).'), ';
				}
			}
			
			$insertStr = substr($insertStr, 0, -2);
		} elseif (func_num_args() > 1) {
			$insertStr = '(';
			$first = TRUE;
			foreach ($args as $inserts) {
				if ($first) {
					foreach ($inserts as $value) $insertStr .= "`$value`, ";
					$insertStr = substr($insertStr, 0, -2).') VALUES ';
					$first = FALSE;
				} else {
					$values = '';
					foreach ($inserts as $value) {
						if (is_numeric($value)) $values .= "$value, ";
						else $values .= '"'.$value.'", ';
					}
					$insertStr .= '('.substr($values, 0, -2).'), ';
				}
			}
			
			$insertStr = substr($insertStr, 0, -2);
		}
		
		return $insertStr;
	}
	
	function setupUpdates($updates) {
		$updateString = '';
		foreach ($updates as $key => $value) {
			if (is_numeric($value)) $updateString .= ', `'.$key.'` = '.$value;
			else $updateString .= ', `'.$key.'` = "'.$value.'"';
		}
		
		if ($updateString[0] == ',') $updateString = substr($updateString, 2);
		
		return $updateString;
	}
	
	function sql_forumPermissions($userID, $types, $forumIDs = NULL) {
		if ($types == '') $types = array('read', 'write', 'editPost', 'deletePost', 'createThread', 'deleteThread', 'addPoll', 'addRolls', 'addDraws', 'moderate');
		elseif (is_string($types)) $types = preg_split('/\s*,\s*/', $types);
		
		$query = "SELECT f.forumID";
		foreach ($types as $type) $query .= ",\n\tIF(MAX(IF(a.forumID IS NOT NULL, 1, 0)) = 1, 1, IF(MAX(IF(IFNULL(up.$type, 0) <> 0, up.$type, IF(IFNULL(gp.$type, 0) <> 0, gp.$type, bp.$type)) * LENGTH(p.heritage)) + MIN(IF(IFNULL(up.$type, 0) <> 0, up.$type, IF(IFNULL(gp.$type, 0) <> 0, gp.$type, bp.$type)) * LENGTH(p.heritage)) > 0, 1, 0)) '$type'";
		$query .= "
FROM forums f
LEFT JOIN forums p ON f.heritage LIKE CONCAT(p.heritage, '%')
LEFT JOIN forumAdmins a ON p.forumID = a.forumID AND a.userID = $userID
LEFT JOIN forums_permissions_general bp ON p.forumID = bp.forumID
LEFT JOIN forums_permissions_groups_c gp ON bp.forumID = gp.forumID AND gp.userID = $userID
LEFT JOIN forums_permissions_users up ON bp.forumID = up.forumID AND up.userID = $userID
WHERE f.forumID != 0 AND (gp.userID IS NULL OR up.userID IS NULL OR gp.userID = up.userID)";
		if (is_numeric($forumIDs)) $query .= " AND f.forumID = $forumIDs";
		elseif (is_array($forumIDs)) $query .= " AND f.forumID IN (".implode(', ', $forumIDs).')';
		$query .= "\nGROUP BY f.forumID";
		
		return $query;
	}
?>