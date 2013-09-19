<?
	checkLogin(0);
	
	if (isset($_POST['submitCharacter'])) {
		$gameID = intval($_POST['gameID']);
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);

		$chargameID = $mysql->query('SELECT gameID FROM characters WHERE characterID = '.$characterID.' AND userID = '.$userID);
		$charGameID = $chargameID->fetchColumn();
		
		if (is_int($chargameID)) header('Location: '.SITEROOT.'/403');
		elseif ($charGameID == 0) {
			$mysql->query('UPDATE characters SET gameID = '.$gameID.' WHERE characterID = '.$characterID);
			addCharacterHistory($characterID, 'appliedToGame', $userID, 'NOW()', $gameID);
			$mysql->query("INSERT INTO gameHistory (gameID, enactedBy, enactedOn, enactedUpon, action) VALUES ($gameID, $userID, NOW(), $characterID, 'charApplied')");
			addGameHistory($gameID, 'charApplied', $userID, 'NOW()', $characterID, 'character');
			
			header('Location: '.SITEROOT.'/games/my/?submitted=1');
		} else header('Location: '.SITEROOT.'/games/'.$gameID);
	} else header('Location: '.SITEROOT.'/403');
?>