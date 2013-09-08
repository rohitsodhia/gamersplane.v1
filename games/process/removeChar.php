<?
	checkLogin(0);
	
	$gameID = intval($_POST['gameID']);
	if (isset($_POST['remove']) || isset($_POST['leave'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		
		$charInfo = $mysql->query("SELECT characters.userID, games.gameID, games.forumID, games.groupID, gms.primary IS NOT NULL isGM FROM characters INNER JOIN games ON characters.gameID = games.gameID LEFT JOIN (SELECT gameID, `primary` FROM gms WHERE userID = $userID) gms ON games.gameID = gms.gameID WHERE characters.characterID = $characterID");
		list($playerID, $gameID, $forumID, $groupID, $isGM) = $charInfo->fetch();
		
		if ($charInfo->rowCount() == 0) {
			if (isset($_POST['modal'])) echo 0;
			else header('Location: '.SITEROOT.'/403');
		} elseif ($playerID == $userID || $isGM) {
			$mysql->query('UPDATE characters SET gameID = NULL, approved = 0, activeSince = "0000-00-00 00:00:00" WHERE characterID = '.$characterID);
			
			$forums = $mysql->query('SELECT forumID FROM forums WHERE heritage LIKE "'.str_pad(2, HERITAGE_PAD, 0, STR_PAD_LEFT).'-'.str_pad($forumID, HERITAGE_PAD, 0, STR_PAD_LEFT).'%"');
			$forumIDs = array();
			foreach ($forums as $info) $forumIDs[] = $info['forumID'];
			$mysql->query("DELETE FROM forums_permissions_users WHERE userID = $playerID AND forumID IN (".implode(', ', $forumIDs).")");
			$mysql->query("DELETE FROM gm USING forums_groupMemberships gm INNER JOIN forums_permissions_groups p WHERE gm.userID = $playerID gm.groupID = p.groupID AND p.forumID IN (".implode(', ', $forumIDs).")");
			$mysql->query("INSERT INTO characterHistory (characterID, enactedBy, enactedOn, action) VALUES ($characterID, $userID, NOW(), '".(isset($_POST['remove'])?'removedFromGame':'leftGame')."')");
			if (isset($_POST['remove'])) $mysql->query("INSERT INTO gameHistory (gameID, enactedBy, enactedOn, enactedUpon, action) VALUES ($gameID, $userID, NOW(), $playerID, 'removedChar')");
			else $mysql->query("INSERT INTO gameHistory (gameID, enactedBy, enactedOn, action) VALUES ($gameID, $userID, NOW(), 'leftGame')");
			
			if (isset($_POST['remove'])) {
				if (isset($_POST['modal'])) echo 1;
				else header('Location: '.SITEROOT.'/games/'.$gameID.'/?removed=1');
			} elseif (isset($_POST['leave'])) {
				if (isset($_POST['modal'])) echo 1;
				else header('Location: '.SITEROOT.'/games/my');
			}
		} else {
			if (isset($_POST['modal'])) echo 0;
			else header('Location: '.SITEROOT.'/games/'.$gameID.'/?notInGame=1');
		}
	} else {
		if (isset($_POST['modal'])) echo 0;
		else header('Location: '.SITEROOT.'/games/');
	}
?>