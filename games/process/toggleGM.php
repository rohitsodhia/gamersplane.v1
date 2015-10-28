<?
	if (isset($_POST['toggle'])) {
		$gameID = intval($_POST['gameID']);
		$playerID = intval($_POST['playerID']);
		
		$gmCheck = $mysql->query("SELECT primaryGM FROM players WHERE gameID = {$gameID} AND userID = {$currentUser->userID} and primaryGM = 1");
		$playerCheck = $mysql->query("SELECT isGM FROM players WHERE gameID = {$gameID} AND userID = {$playerID} AND approved = 1");
		if ($gmCheck->rowCount() == 0 || $playerCheck->rowCount() == 0) {
			if (isset($_POST['modal'])) 
				displayJSON(array('failed' => true, 'errors' => array('invalidPost')));
			else 
				header("Location: /games/{$gameID}/");
		} else {
			$isGM = $playerCheck->fetchColumn();
			$mysql->query("UPDATE players SET isGM = isGM ^ 1 WHERE gameID = {$gameID} AND userID = {$playerID}");
			$forumID = $mysql->query("SELECT forumID FROM games WHERE gameID = {$gameID}");
			$forumID = $forumID->fetchColumn();

			if ($isGM) 
				$mysql->query("DELETE FROM forumAdmins WHERE userID = {$playerID} AND forumID = {$forumID}");
			else 
				$mysql->query("INSERT INTO forumAdmins (userID, forumID) VALUES ({$playerID}, {$forumID})");

			$hl_toggleGM = new HistoryLogger($isGM?'gmRemoved':'gmAdded');
			$hl_toggleGM->addUser($playerID)->addGame($gameID)->addUser($currentUser->userID, 'gm')->save();
			
			if (isset($_POST['modal'])) 
				displayJSON(array('success' => true, 'userID' => $playerID));
			else 
				header("Location: /games/{$gameID}/?gmAdded=1");
		}
	} else {
		if (isset($_POST['modal'])) 
			displayJSON(array('failed' => true));
		else 
			header('Location: /games/');
	}
?>