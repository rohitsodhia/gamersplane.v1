<?
	if (isset($_POST['pendingAction'])) {
		$gameID = intval($_POST['gameID']);
		$playerID = intval($_POST['playerID']);
		$pendingAction = $_POST['pendingAction'] == 'approve'?'approve':'reject';
		
		$sanityCheck = $mysql->query("SELECT g.groupID FROM players p INNER JOIN players gm ON p.gameID = gm.gameID AND gm.isGM = 1 INNER JOIN games g ON p.gameID = g.gameID WHERE p.userID = {$playerID} AND p.gameID = $gameID AND gm.userID = {$currentUser->userID}");
		
		if ($sanityCheck->rowCount() == 0) {
			if (isset($_POST['modal'])) 
				displayJSON(array('failed' => true, 'errors' => ['noPlayer']));
			else 
				header("Location: /games/{$gameID}/?approveError=1");
		} else {
			$groupID = $sanityCheck->fetchColumn();
			if ($pendingAction == 'approve') {
				$mysql->query("UPDATE players SET approved = 1 WHERE userID = {$playerID} AND gameID = {$gameID}");
				$mysql->query("INSERT INTO forums_groupMemberships SET groupID = {$groupID}, userID = {$playerID}");
			} else {
				$mysql->query("DELETE FROM players WHERE userID = {$playerID} AND gameID = {$gameID}");
			}
#			$hl_playerApplied = new HistoryLogger($pendingAction == 'approve'?'playerApproved':'playerRejected');
#			$hl_playerApplied->addUser($playerID)->addUser($currentUser->userID, 'gm')->addGame($gameID)->save();
			
			if (isset($_POST['modal'])) 
				displayJSON(array('success' => true, 'action' => $pendingAction, 'userID' => $playerID));
			else 
				header("Location: /games/{$gameID}/");
		}
	} else {
		if (isset($_POST['modal'])) 
				displayJSON(array('failed' => true));
		else 
			header('Location: /games/'.($gameID?$gameID.'/':''));
	}
?>