<?
	checkLogin();
	
	if (isset($_POST['pendingAction'])) {
		$userID = intval($_SESSION['userID']);
		$gameID = intval($_POST['gameID']);
		$characterID = intval($_POST['characterID']);
		$pendingAction = $_POST['pendingAction'] == 'approve'?'approve':'removed';
		
		$gmCheck = $mysql->query('SELECT isGM FROM players WHERE gameID = '.$gameID.' AND userID = '.$userID);
		$charCheck = $mysql->query('SELECT c.label, c.userID, u.username, g.title, s.shortName FROM characters c, users u, games g, systems s WHERE c.userID = u.userID AND g.systemID = s.systemID AND c.characterID = '.$characterID.' AND g.gameID = '.$gameID);
		
		if ($charCheck->rowCount() == 0 && $gmCheck->rowCount() == 0) {
			if (isset($_POST['modal'])) echo -1;
			else header('Location: '.SITEROOT.'/games/'.$gameID.'/?charError=1');
		} else {
			if ($pendingAction == 'approve') $mysql->query('UPDATE characters SET approved = 1 WHERE characterID = '.$characterID);
			else $mysql->query('UPDATE characters SET approved = 0, gameID = NULL WHERE characterID = '.$characterID);
			addCharacterHistory($characterID, 'character'.ucwords($pendingAction).'d', $userID, 'NOW()', $userID);
			addGameHistory($gameID, 'character'.ucwords($pendingAction).'d', $userID, 'NOW()', 'character', $characterID);
			
			if (isset($_POST['modal'])) echo 1;
			else header('Location: '.SITEROOT.'/games/'.$gameID);
		}
	} else {
		if (isset($_POST['modal'])) echo 0;
		else header('Location: '.SITEROOT.'/games/'.($gameID?$gameID:''));
	}
?>