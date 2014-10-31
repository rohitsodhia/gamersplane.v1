<?
	if (isset($_POST['submitCharacter'])) {
		$gameID = intval($_POST['gameID']);
		$characterID = intval($_POST['characterID']);

		$chargameID = $mysql->query('SELECT gameID FROM characters WHERE characterID = '.$characterID.' AND userID = '.$currentUser->userID);
		$charGameID = $chargameID->fetchColumn();
		
		if (is_int($chargameID)) header('Location: /403');
		elseif ($charGameID == 0) {
			$mysql->query('UPDATE characters SET gameID = '.$gameID.' WHERE characterID = '.$characterID);
			addCharacterHistory($characterID, 'charApplied', $currentUser->userID, 'NOW()', $gameID);
			addGameHistory($gameID, 'charApplied', $currentUser->userID, 'NOW()', 'character', $characterID);
			
			header('Location: /games/'.$gameID);
		} else header('Location: /games/'.$gameID);
	} else header('Location: /403');
?>