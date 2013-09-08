<?
	checkLogin(0);
	
	if (isset($_POST['submitCharacter'])) {
		$gameID = intval($_POST['gameID']);
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		
		$system = $mysql->query('SELECT system FROM games WHERE gameID = '.$gameID);
		$system = $system->fetchColumn();
		
		$chargameID = $mysql->query('SELECT gameID FROM characters WHERE characterID = '.$characterID.' AND userID = '.$userID);
		$charGameID = $chargameID->fetchColumn();
		
		if (is_int($chargameID)) header('Location: '.SITEROOT.'/403');
		elseif ($charGameID == 0) {
			$mysql->query('UPDATE characters SET gameID = '.$gameID.' WHERE characterID = '.$characterID);
			$mysql->query("INSERT INTO characterHistory (characterID, enactedBy, enactedOn, gameID, action) VALUES ($characterID, $userID, NOW(), $gameID, 'appliedToGame')");
			$mysql->query("INSERT INTO gameHistory (gameID, enactedBy, enactedOn, enactedUpon, action) VALUES ($gameID, $userID, NOW(), $characterID, 'charApplied')");
			
			header('Location: '.SITEROOT.'/games/my/?submitted=1');
		} else header('Location: '.SITEROOT.'/games/'.$gameID);
	} else header('Location: '.SITEROOT.'/403');
?>