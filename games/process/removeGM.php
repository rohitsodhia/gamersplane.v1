<?
	checkLogin(0);
	
	if (isset($_POST['remove'])) {
		$userID = intval($_SESSION['userID']);
		$gameID = intval($_POST['gameID']);
		$gmID = intval($_POST['gmID']);
		
		$pGMCheck = $mysql->query("SELECT `primary` FROM gms WHERE gameID = $gameID AND userID = $userID AND `primary` = 1");
		if ($pGMCheck->rowCount() == 0) {
			if (isset($_POST['modal'])) echo 0;
			else header('Location: '.SITEROOT.'/games/'.$gameID);
		} else {
			$gmCheck = $mysql->query("SELECT users.userID FROM users, gms WHERE users.userID = $gmID AND users.userID = gms.userID AND gms.gameID = $gameID");
			if ($gmCheck->rowCount() == 0) {
				if (isset($_POST['modal'])) echo 0;
				else header('Location: '.SITEROOT."/games/$gameID");
				exit;
			}
			
			$forumID = $mysql->query("SELECT forumID FROM games WHERE gameID = $gameID");
			$forumID = $forumID->fetchColumn();
			$mysql->query("DELETE FROM gms WHERE gameID = $gameID AND userID = $gmID");
			$mysql->query("DELETE FROM forumAdmins WHERE userID = $gmID AND forumID = $forumID");
			
			$mysql->query("INSERT INTO gameHistory (gameID, enactedBy, enactedOn, enactedUpon, action) VALUES ($gameID, $userID, NOW(), $gmID, 'gmRemoved')");
			
			if (isset($_POST['modal'])) echo 1;
			else header('Location: '.SITEROOT."/games/$gameID?gmRemoved=1");
		}
	} else {
		if (isset($_POST['modal'])) echo 0;
		else header('Location: '.SITEROOT.'/games/');
	}
?>