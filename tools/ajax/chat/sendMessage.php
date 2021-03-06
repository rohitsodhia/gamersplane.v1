<?
	header('Content-Type: text/xml');
	echo "<?xml version=\"1.0\" ?>\n\n";
	
	$message = sanitizeString($_POST['message']);
	$gameID = intval($_POST['gameID']);
	if ($loggedIn && gameID && strlen($message) > 0) {
		$postedOn = date('Y-m-d H:i:s');
		
		if (substr($message, 0, 6) == '/roll ') {
			list($roll) = parseRolls(substr($message, 6));
			if ($roll != '') {
				$rollResults = rollDice($roll);
				$message = "$roll > ".displayIndivDice($rollResults['indivRolls'])." = {$rollResults['total']}";
				$mysql->query('INSERT INTO chat_messages '.$mysql->setupInserts(array('gameID' => $gameID, 'posterID' => $currentUser->userID, 'postedOn' => $postedOn, 'message' => $message)));
			} else echo '<error>1</error>';
		} else {
			$mysql->query('INSERT INTO chat_messages '.$mysql->setupInserts(array('gameID' => $gameID, 'posterID' => $currentUser->userID, 'postedOn' => $postedOn, 'message' => $message)));
			
			if ($mysql->getResult()) {
				echo '<success>';
				echo '<date>'.date('H:i:s', strtotime($postedOn)).'</date>';
				echo '<username>'.$username.'</username>';
				echo '</success>';
			} else echo '<error>1</error>';
		}
	} else echo '<error>1</error>';
?>