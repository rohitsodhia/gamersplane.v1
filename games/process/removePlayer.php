<?
	checkLogin(0);
	
	if (isset($_POST['remove']) || isset($_POST['leave'])) {
		$userID = intval($_SESSION['userID']);
		$gameID = intval($_POST['gameID']);
		$playerID = intval($_POST['playerID']);
		
		$gmCheck = $mysql->query("SELECT primaryGM FROM players WHERE gameID = $gameID AND userID = $userID and isGM = 1");
		$playerCheck = $mysql->query("SELECT u.userID, u.username, g.title, g.forumID, p.isGM FROM users u, games g, players p WHERE g.gameID = $gameID AND p.gameID = g.gameID AND p.userID = $playerID AND u.userID = p.userID AND p.primaryGM IS NULL AND p.approved = 1");

		list($playerID, $playerName, $title, $forumID, $isGM) = $playerCheck->fetch(PDO::FETCH_NUM);

		if ($playerCheck->rowCount() == 0) {
			if (isset($_POST['modal'])) echo 'No player';
			else header('Location: '.SITEROOT.'/403');
		} elseif ($gmCheck->rowCount() != 0 || $playerID == $userID) {
			$forums = $mysql->query('SELECT forumID FROM forums WHERE heritage LIKE "'.str_pad(2, HERITAGE_PAD, 0, STR_PAD_LEFT).'-'.str_pad($forumID, HERITAGE_PAD, 0, STR_PAD_LEFT).'%"');
			$forumIDs = array();
			foreach ($forums as $info) $forumIDs[] = $info['forumID'];
			$mysql->query("DELETE FROM forumAdmins WHERE userID = $playerID AND forumID IN (".implode(', ', $forumIDs).")");
			$mysql->query("DELETE FROM forums_permissions_users WHERE userID = $playerID AND forumID IN (".implode(', ', $forumIDs).")");
			$mysql->query("DELETE FROM gm USING forums_groupMemberships gm INNER JOIN forums_permissions_groups p WHERE gm.userID = $playerID gm.groupID = p.groupID AND p.forumID IN (".implode(', ', $forumIDs).")");
			$mysql->query("INSERT INTO characterHistory (characterID, enactedBy, enactedOn, gameID, action) SELECT characterID, $userID, NOW(), $gameID, '".(isset($_POST['remove'])?'playerRemovedFromGame':'playerLeftGame')."' FROM characters WHERE gameID = $gameID AND userID = $playerID");
			$mysql->query("UPDATE characters SET gameID = NULL, approved = 0 WHERE gameID = $gameID AND userID = $playerID");
			$mysql->query("DELETE FROM players WHERE gameID = $gameID AND userID = $playerID");
			if (isset($_POST['remove'])) addGameHistory($gameID, 'removedPlayer', $userID, 'NOW()', $playerID);
			else addGameHistory($gameID, 'leftGame');
			
			if (isset($_POST['remove'])) {
				if (isset($_POST['modal'])) echo 'Removed';
				else header('Location: '.SITEROOT.'/games/'.$gameID.'/?removed=1');
			} elseif (isset($_POST['leave'])) {
				if (isset($_POST['modal'])) echo 'Left';
				else header('Location: '.SITEROOT.'/games/my');
			}
		} else {
			if (isset($_POST['modal'])) echo 'Invalid player';
			else header('Location: '.SITEROOT.'/games/'.$gameID.'/?notInGame=1');
		}
	} else {
		if (isset($_POST['modal'])) echo 'No submit';
		else header('Location: '.SITEROOT.'/games/');
	}
?>