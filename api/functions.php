<?
/*
	Gamers Plane Functions
	Created by RhoVisions, designer Rohit Sodhia
*/

/* General Functions */
	function utf8ize($input) {
		if (is_array($input)) {
			foreach ($input as $key => $value) 
				$input[$key] = utf8ize($value);
		} else if (is_string ($input)) 
			return utf8_encode($input);
		return $input;
	}

	function displayJSON($data, $exit = false) {
		header('Content-Type: application/json');
		echo json_encode($data);
		if ($exit) 
			exit;
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
		$string = mb_convert_encoding($string, 'UTF-8');
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

/* Session Functions */
	function startSession() {
		session_start();
		
//		putenv('TZ=GMT');
		date_default_timezone_set('GMT');
	}

/* DB Functions */
	function sql_forumIDPad($forumID) {
		return str_pad($forumID, HERITAGE_PAD, 0, STR_PAD_LEFT);
	}

	function mongo_getNextSequence($key) {
		global $mongo;

		$counter = $mongo->counters->findAndModify(
			array('_id' => $key),
			array('$inc' => array('seq' => 1)),
			null,
			array('new' => true)
		);

		return $counter['seq'];
	}

/* Character Functions */
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
?>