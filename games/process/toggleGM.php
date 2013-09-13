<?
	checkLogin(0);
	
	if (isset($_POST['toggle'])) {
		$userID = intval($_SESSION['userID']);
		$gameID = intval($_POST['gameID']);
		$playerID = intval($_POST['playerID']);
		
		$gmCheck = $mysql->query("SELECT primaryGM FROM players WHERE gameID = $gameID AND userID = $userID and isGM = 1");
		$playerCheck = $mysql->query("SELECT isGM FROM players WHERE gameID = $gameID AND userID = $playerID AND approved = 1");
		if ($gmCheck->rowCount() == 0 || $playerCheck->rowCount() == 0) {
			if (isset($_POST['modal'])) echo -1;
			else header('Location: '.SITEROOT.'/games/'.$gameID);
		} else {
			$isGM = $playerCheck->fetchColumn();
			$mysql->query("UPDATE players SET isGM = isGM ^ 1 WHERE gameID = $gameID AND userID = $playerID");
			$forumID = $mysql->query("SELECT forumID FROM games WHERE gameID = $gameID");
			$forumID = $forumID->fetchColumn();

			if ($isGM) $mysql->query("DELETE FROM forumAdmins WHERE userID = $playerID AND forumID = $forumID");
			else $mysql->query("INSERT INTO forumAdmins (userID, forumID) VALUES ($playerID, $forumID)");

			addGameHistory($gameID, ($isGM?'gmRemoved':'gmAdded'), $userID, 'NOW()', $playerID);
			
			if (isset($_POST['modal'])) echo 1;
			else header('Location: '.SITEROOT."/games/$gameID?gmAdded=1");
		}
	} else {
		if (isset($_POST['modal'])) echo 0;
		else header('Location: '.SITEROOT.'/games/');
	}
?>