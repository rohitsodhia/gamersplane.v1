<?
/*
	Gamers Plane Functions
	Created by RhoVisions, designer Rohit Sodhia
*/

/* General Functions */
	function addPackage($package) {
		include_once(FILEROOT."/includes/packages/{$package}.package.php");
	}

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
		$first = true;
		$finalString = '';
		
		foreach ($strings as $indivString) {
			$indivString = strtolower($indivString);
			
			if ($first) $first = false;
			else $indivString[0] = strtoupper($indivString[0]);
			
			$finalString .= $indivString;
		}
		
		return $finalString;
	}
	
	function sanitizeString($string) {
		$options = func_get_args();
		array_shift($options);

		if (in_array('search_format', $options)) {
			$string = preg_replace('/[^A-za-z0-9]/', ' ', $string);
			$options = array('lower', 'rem_dup_spaces');
		}

		$string = trim($string);
		$string = strip_tags($string);
		if (in_array('lower', $options)) $string = strtolower($string);
		if (in_array('like_clean', $options)) $string = str_replace(array('%', '_'), array('\%', '\_'), strip_tags($string));
		if (in_array('rem_dup_spaces', $options)) $string = preg_replace('/\s+/', ' ', $string);

		return $string;
	}

	function printReady($string, $options = array('stripslashes', 'nl2br')) {
		if (in_array('nl2br', $options)) {
			$string = str_replace("\r\n", "\n", $string);
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

	function addBodyClass($class) {
		global $bodyClasses;

		if (strlen($class)) $bodyClasses[] = $class;
	}

/* Character Functions */
	function getCharacterClass($characterID) {
		global $mysql;

		$system = $mysql->query('SELECT s.shortName FROM systems s INNER JOIN characters c USING (systemID) WHERE c.characterID = '.$characterID);

		if ($system->rowCount()) return $system->fetchColumn();
		else return false;
	}

	function addCharacterHistory($characterID, $action, $enactedBy = 0, $enactedOn = 'NOW()', $additionalInfo = '') {
		global $currentuser, $mysql;
		if ($enactedBy == 0 && $loggedIn) $enactedBy = $currentUser->userID;

		if (!isset($enactedBy) || !intval($characterID) || !strlen($action)) return false;
		if ($enactedOn == '') $enactedOn = 'NOW()';

		$addCharHistory = $mysql->prepare("INSERT INTO characterHistory (characterID, enactedBy, enactedOn, action, additionalInfo) VALUES ($characterID, $enactedBy, ".($enactedOn == 'NOW()'?'NOW()':':enactedOn').", :action, :additionalInfo)");
		if ($enactedOn != 'NOW()') $addCharHistory->bindvalue(':enactedOn', $enactedOn);
		$addCharHistory->bindvalue(':action', $action);
		$addCharHistory->bindvalue(':additionalInfo', $additionalInfo);
		$addCharHistory->execute();
	}
	
	function addGameHistory($gameID, $action, $enactedBy = 0, $enactedOn = 'NOW()', $affectedType = NULL, $affectedID = NULL) {
		global $currentUser, $mysql;
		if ($enactedBy == 0 && $loggedIn) $enactedBy = $currentUser->userID;

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
	function rollDice($roll, $rerollAces = 0) {
		list($numDice, $diceType) = explode('d', str_replace(' ', '', trim($roll)));
		$numDice = intval($numDice);
		if (strpos($diceType, '-')) { list($diceType, $modifier) = explode('-', $diceType); $modifier = intval('-'.$modifier); }
		elseif (strpos($diceType, '+')) list($diceType, $modifier) = explode('+', $diceType);
		else $modifier = 0;
		$diceType = intval($diceType);
		if ($numDice > 0 && $diceType > 1 && $numDice <= 1000 && $diceType <= 1000) {
			$totalRoll = $modifier;
			$indivRolls = array('dice' => array(), 'mod' => intval($modifier));
			for ($rollCount = 0; $rollCount < $numDice; $rollCount++) {
				$curRoll = mt_rand(1, $diceType);
				$totalRoll += $curRoll;

				if (isset($indivRolls['dice'][$rollCount]) && is_array($indivRolls['dice'][$rollCount])) $indivRolls['dice'][$rollCount][] = $curRoll;
				elseif ($curRoll == $diceType && $rerollAces) $indivRolls['dice'][$rollCount] = array($curRoll);
				else $indivRolls['dice'][$rollCount] = $curRoll;

				if ($curRoll == $diceType && $rerollAces) $rollCount -= 1;
			}
			
			return array('result' => $totalRoll, 'indivRolls' => $indivRolls, 'numDice' => $numDice, 'diceType' => $diceType, 'modifier' => $modifier);
		} else return false;
	}

	function displayIndivDice($dice) {
		$diceString = '( ';

		foreach ($dice as &$die) {
			if (is_array($die)) $die = '[ '.implode(', ', $die).' ]';
		}
		$diceString .= implode(', ', $dice).' )';
		
		return $diceString;
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
		} else return false;
	}
	
	function clearGlobalDeck($deckType) {
		unset($_SESSION['deckShort'], $_SESSION['deckName'], $_SESSION['deck']);
		
		return true;
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

		$validSizes = array('', 'mid', 'mini');
		if (!in_array($size, $validSizes)) $size = '';
		if ($size != '') $size = ' '.$size;

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

		return '<div class="cardWindow deck_'.$deckInfo['class'].$size.'"><img src="/images/tools/cards/'.$deckInfo['image'].'.png" title="'.cardText($cardNum, $deckInfo['class']).'" alt="'.cardText($cardNum, $deckInfo['class']).'" class="'.$classes.'"></div>';
	}
	
/* Forum Functions */
	function buildForumStructure($rawForums) {
		$forums = array(array('info' => $rawForums[0], 'children' => array()));
		foreach ($rawForums as $key => $forum) { if ($key != 0) {
			$heritage = array_map('intval', explode('-', $forum['heritage']));
			$currentParent = &$forums[0];
			for ($count = 0; $count + 1 < sizeof($heritage); $count++) {
				$currentParent = &$currentParent['children'][$heritage[$count]];
			}
			$currentParent['children'][$forum['forumID']] = array('info' => $forum, 'children' => array());
		} }
		return $forums;
	}

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
	
	function retrievePermissions($userID, $forumIDs, $types, $returnSDA = 0) {
		global $mysql;
		$userID = intval($userID);
		if (!is_array($forumIDs)) $forumIDs = array($forumIDs);
		$queryColumn = array('permissions' => '', 'general' => '', 'group' => '');
		$allTypes = array('read', 'write', 'editPost', 'deletePost', 'createThread', 'deleteThread', 'addPoll', 'addRolls', 'addDraws', 'moderate');
		if ($types == '') $types = $allTypes;
		elseif (is_string($types)) $types = preg_split('/\s*,\s*/', $types);

		foreach ($types as $type) {
			$queryColumn['permissions'] .= "`$type`, ";
			$queryColumn['permissionSums'] .= "SUM(`$type`) `$type`, ";
			$bTemplate[$type] = 0;
			$aTemplate[$type] = 1;
		}
		$queryColumn['permissions'] = substr($queryColumn['permissions'], 0, -2);
		$queryColumn['permissionSums'] = substr($queryColumn['permissionSums'], 0, -2);
		
		$allForumIDs = $forumIDs;
		$forumInfos = $mysql->query('SELECT forumID, heritage FROM forums WHERE forumID IN ('.implode(', ', $allForumIDs).')');
		while (list($indivForumID, $heritage) = $forumInfos->fetch(PDO::FETCH_NUM)) {
			$heritages[$indivForumID] = explode('-', $heritage);
			$intValHolder = array();
			foreach ($heritages[$indivForumID] as $key => $hForumID) {
				$heritages[$indivForumID][$key] = intval($hForumID);
				$allForumIDs[] = intval($hForumID);
			}
		}
		$allForumIDs = array_unique($allForumIDs);
		sort($allForumIDs);
		
		if ($userID) {
			$adminForums = array();
			$adminIn = $mysql->query("SELECT forumID FROM forumAdmins WHERE userID = $userID AND forumID IN (0, ".implode(', ', $allForumIDs).')');
			foreach ($adminIn as $indivAdmin) $adminForums[] = $indivAdmin['forumID'];
			$getPermissionsFor = array();
			$superFAdmin = array_search(0, $adminForums) !== false?true:false;
			foreach ($forumIDs as $forumID) {
				if (sizeof(array_intersect($heritages[$forumID], $adminForums)) || $superFAdmin) $permissions[$forumID] = $aTemplate;
				else $getPermissionsFor[] = $forumID;
			}
			foreach ($getPermissionsFor as $forumID) {
				$getPermissionsFor = array_merge($getPermissionsFor, $heritages[$forumID]);
			}
			$getPermissionsFor = array_unique($getPermissionsFor);
			sort($getPermissionsFor);
		} else $getPermissionsFor = $allForumIDs;

		if (sizeof($getPermissionsFor)) {
			if (sizeof($getPermissionsFor) == 1) $forumString = '= '.$getPermissionsFor[0];
			else {
				$forumString = implode(', ', $getPermissionsFor);
				$forumString = 'IN ('.$forumString.')';
			}
			$permissionsInfos = $mysql->query("SELECT forumID, {$queryColumn['permissionSums']} FROM (SELECT forumID, {$queryColumn['permissions']} FROM forums_permissions_general WHERE forumID {$forumString} UNION SELECT forumID, {$queryColumn['permissions']} FROM forums_permissions_groups_c WHERE userID = {$userID} AND forumID {$forumString} UNION SELECT forumID, {$queryColumn['permissions']} FROM forums_permissions_users WHERE userID = {$userID} AND forumID {$forumString}) permissions GROUP BY forumID");
			$rawPermissions = $permissionsInfos->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
			$rawPermissions = array_map('reset', $rawPermissions);

			foreach ($forumIDs as $forumID) {
				if (!isset($permissions[$forumID])) {
					if (isset($rawPermissions[$forumID])) $permissions[$forumID] = $rawPermissions[$forumID];
					else $permissions[$forumID] = $bTemplate;
					foreach (array_reverse($heritages[$forumID]) as $heritage) {
						if ($heritage == $forumID) continue;
						if (isset($rawPermissions[$heritage])) { foreach (array_keys($permissions[$forumID], 0) as $type) {
							if ($rawPermissions[$heritage][$type] != 0) $permissions[$forumID][$type] = $rawPermissions[$heritage][$type];
						} }
					}
				}
			}
			global $loggedIn;
			foreach ($forumIDs as $forumID) {
				foreach ($types as $type) {
					if ($permissions[$forumID][$type] < 1 || (!$loggedIn && $type != 'read')) $permissions[$forumID][$type] = 0;
					else $permissions[$forumID][$type] = 1;
				}
			}
		}
		
		if (sizeof($forumIDs) == 1 && $returnSDA) return $permissions[$forumIDs[0]];
		else return $permissions;
	}
	
/* MySQL Functions */
	function sql_forumIDPad($forumID) {
		return str_pad($forumID, HERITAGE_PAD, 0, STR_PAD_LEFT);
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