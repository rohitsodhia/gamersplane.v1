<?
	checkLogin(0);
	
	if (isset($_POST['submitCharacter'])) {
		$gameID = intval($_POST['gameID']);
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);

		$chargameID = $mysql->query('SELECT gameID FROM characters WHERE characterID = '.$characterID.' AND userID = '.$userID);
		$charGameID = $chargameID->fetchColumn();
		
		if (is_int($chargameID)) header('Location: /403');
		elseif ($charGameID == 0) {
			$mysql->query('UPDATE characters SET gameID = '.$gameID.' WHERE characterID = '.$characterID);
			addCharacterHistory($characterID, 'appliedToGame', $userID, 'NOW()', $gameID);
			$mysql->query("INSERT INTO gameHistory (gameID, enactedBy, enactedOn, enactedUpon, action) VALUES ($gameID, $userID, NOW(), $characterID, 'charApplied')");
			addGameHistory($gameID, 'charApplied', $userID, 'NOW()', 'character', $characterID);
			
			header('Location: /games/'.$gameID);
		} else header('Location: /games/'.$gameID);
	} else header('Location: /403');
?>