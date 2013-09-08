<?
	checkLogin();
	
	if (isset($_POST['pendingAction'])) {
		$userID = intval($_SESSION['userID']);
		$gameID = intval($_POST['gameID']);
		$playerID = intval($_POST['playerID']);
		$pendingAction = $_POST'pendingAction'] == 'approve'?'approve':'reject';
		
		$sanityCheck = $mysql->query("SELECT g.groupID FROM players p, players gm, games g WHERE p.userID = $playerID AND p.gameID = $gameID AND g.gameID = $gameID AND gm.gameID = $gameID AND gm.userID = $userID AND gm.isGM = 1");
		
		if ($sanityCheck->rowCount() == 0) {
			if (isset($_POST['modal'])) echo -1;
			else header('Location: '.SITEROOT.'/games/'.$gameID.'/?approveError=1');
		} else {
			$groupID = $sanityCheck->fetchColumn();
			if ($pendingAction == 'approve') {
				$mysql->query("UPDATE players SET approved = 1 WHERE userID = $playerID AND gameID = $gameID");
				$mysql->query("INSERT INTO forums_groupMemberships SET groupID = $groupID, userID = $playerID");
				$mysql->query("INSERT INTO gameHistory (gameID, enactedBy, enactedOn, action, targetUser) VALUES ($gameID, $userID, NOW(), 'playerApproved', $playerID)");
			} else {
				$mysql->query("DELETE FROM players WHERE userID = $playerID AND gameID = $gameID");
				$mysql->query("INSERT INTO gameHistory (gameID, enactedBy, enactedOn, action, targetUser) VALUES ($gameID, $userID, NOW(), 'playerRejected', $playerID)");
			}
			
			if (isset($_POST['modal'])) echo 1;
			else header('Location: '.SITEROOT.'/games/'.$gameID);
		}
	} else {
		if (isset($_POST['modal'])) echo 0;
		else header('Location: '.SITEROOT.'/games/'.($gameID?$gameID:''));
	}
?>