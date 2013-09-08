<?
	checkLogin(0);
	
	if (isset($_POST['remove']) || isset($_POST['leave'])) {
		$userID = intval($_SESSION['userID']);
		$gameID = intval($_POST['gameID']);
		$playerID = intval($_POST['playerID']);
		
		$playerInfo = $mysql->query("SELECT g.forumID, g.groupID, gms.isGM IS NOT NULL isGM, gms.primaryGM FROM players p INNER JOIN games g ON p.gameID = g.gameID LEFT JOIN players gms ON g.gameID = gms.gameID AND gms.isGM = 1 AND gms.userID = $userID WHERE p.gameID = $gameID AND p.userID = $playerID");
		list($forumID, $groupID, $isGM, $primaryGM) = $playerInfo->fetch();
		
		if ($playerInfo->rowCount() == 0) {
			if (isset($_POST['modal'])) echo 0;
			else header('Location: '.SITEROOT.'/403');
		} elseif (($playerID == $userID || $isGM) && !$primaryGM) {
			$forums = $mysql->query('SELECT forumID FROM forums WHERE heritage LIKE "'.str_pad(2, HERITAGE_PAD, 0, STR_PAD_LEFT).'-'.str_pad($forumID, HERITAGE_PAD, 0, STR_PAD_LEFT).'%"');
			$forumIDs = array();
			foreach ($forums as $info) $forumIDs[] = $info['forumID'];
			$mysql->query("DELETE FROM forums_permissions_users WHERE userID = $playerID AND forumID IN (".implode(', ', $forumIDs).")");
			$mysql->query("DELETE FROM gm USING forums_groupMemberships gm INNER JOIN forums_permissions_groups p WHERE gm.userID = $playerID gm.groupID = p.groupID AND p.forumID IN (".implode(', ', $forumIDs).")");
			$mysql->query("INSERT INTO characterHistory (characterID, enactedBy, enactedOn, gameID, action) SELECT characterID, $userID, NOW(), $gameID, '".(isset($_POST['remove'])?'playerRemovedFromGame':'playerLeftGame')."' FROM characters WHERE gameID = $gameID AND userID = $playerID");
			$mysql->query("UPDATE characters SET gameID = NULL, approved = 0 WHERE gameID = $gameID AND userID = $playerID");
			if (isset($_POST['remove'])) $mysql->query("INSERT INTO gameHistory (gameID, enactedBy, enactedOn, action, targetUser) VALUES ($gameID, $userID, NOW(), 'removedChar, $playerID')");
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