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

	function addLoginRecord($userID, $success) {
		global $mysql;

		$userID = intval($userID);
		$success = $success?1:0;

		$mysql->query("INSERT INTO loginRecords (userID, attemptStamp, ipAddress, successful) VALUES ({$userID}, NOW(), '{$_SERVER['REMOTE_ADDR']}', {$success})");
		
		return true;
	}

/*	Session Functions */
	function startSession() {
		session_start();
		
//		putenv('TZ=GMT');
		date_default_timezone_set('GMT');
	}
	
	function checkLogin($redirect = 1) {
		global $currentUser;
		if (!isset($currentUser)) $currentUser = new User();
		
		if (isset($_COOKIE['loginHash'])) {
			global $mysql;

			list($username, $loginHash) = explode('|', sanitizeString($_COOKIE['loginHash']));
			$userCheck = $mysql->prepare('SELECT userID FROM users WHERE username = :username AND suspendedUntil IS NULL AND banned = 0');
			$userCheck->execute(array(':username' => $username));

			if ($userCheck->rowCount()) {
				$userID = $userCheck->fetchColumn();
				$currentUser = new User($userID);
				if ($currentUser->getLoginHash() == $loginHash) {
					$currentUser->generateLoginCookie();

//					wp_set_current_user($userInfo['userID']);
//					wp_set_auth_cookie($userInfo['userID']);
//					do_action('wp_login', $userInfo['userID']);

					$mysql->query('UPDATE users SET lastActivity = NOW() WHERE userID = '.$currentUser->userID);

					return true;
				}
			}
		}
		
		logout();
		if ($redirect) { header('Location: /login/?redirect=1'); exit; }
		
		return false;
	}

	function logout($resetSession = false) {
		if ($resetSession) {
			session_unset();
//			unset($_COOKIE[session_name()]);
		
			session_regenerate_id(TRUE);
			session_destroy();
			setcookie(session_name(), '', time() - 30, '/');
			$_SESSION = array();
		}

		setcookie('loginHash', '', time() - 30, '/');
//		session_destroy();
	}

	function addUserHistory($userID, $action, $enactedBy = 0, $enactedOn = 'NOW()', $additionalInfo = '') {
		global $currentuser, $mysql;
		if ($enactedBy == 0 && $loggedIn) $enactedBy = $currentUser->userID;

		if (!intval($userID) || !strlen($action)) return false;
		if ($enactedOn == '') $enactedOn = 'NOW()';

		$addUserHistory = $mysql->prepare("INSERT INTO userHistory (userID, enactedBy, enactedOn, action, additionalInfo) VALUES ($userID, $enactedBy, ".($enactedOn == 'NOW()'?'NOW()':':enactedOn').", :action, :additionalInfo)");
		if ($enactedOn != 'NOW()') $addUserHistory->bindvalue(':enactedOn', $enactedOn);
		$addUserHistory->bindvalue(':action', $action);
		$addUserHistory->bindvalue(':additionalInfo', $additionalInfo);
		$addUserHistory->execute();
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
	
/* MySQL Functions */
	function sql_forumIDPad($forumID) {
		return str_pad($forumID, HERITAGE_PAD, 0, STR_PAD_LEFT);
	}
?>