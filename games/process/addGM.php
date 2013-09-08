<?
	checkLogin(0);
	
	if (isset($_POST['add'])) {
		$userID = intval($_SESSION['userID']);
		$gameID = intval($_POST['gameID']);
		$user = sanatizeString($_POST['user']);
		
		$gmCheck = $mysql->query("SELECT `primary` FROM gms WHERE gameID = $gameID AND userID = $userID");
		if ($gmCheck->rowCount() == 0) {
			if (isset($_POST['modal'])) echo -1;
			else header('Location: '.SITEROOT.'/games/'.$gameID);
		} else {
			$userCheck = $mysql->query("SELECT userID FROM users WHERE username = '$user'");
			if ($userCheck->rowCount()) $newGMID = $userCheck->fetchColumn();
			else { header('Location: '.SITEROOT."/games/$gameID/addGM?invalidUser=1"); exit; }
			
			$redundencyCheck = $mysql->query("SELECT userID FROM gms WHERE gameID = $gameID AND userID = $newGMID");
			if ($redundencyCheck->rowCount()) { header('Location: '.SITEROOT."/games/$gameID/addGM?alreadyGM=1"); exit; }
			
			$forumID = $mysql->query("SELECT forumID FROM games WHERE gameID = $gameID");
			$forumID = $forumID->fetchColumn();
			$mysql->query("INSERT INTO gms (gameID, userID) VALUES ($gameID, $newGMID)");
			$mysql->query("INSERT INTO forumAdmins (userID, forumID) VALUES ($newGMID, $forumID)");
			
			$mysql->query("INSERT INTO gameHistory (gameID, enactedBy, enactedOn, enactedUpon, action) VALUES ($gameID, $userID, NOW(), $newGMID, 'gmAdded')");
			
			if (isset($_POST['modal'])) echo 1;
			else header('Location: '.SITEROOT."/games/$gameID?gmAdded=1");
		}
	} else {
		if (isset($_POST['modal'])) echo 0;
		else header('Location: '.SITEROOT.'/games/');
	}
?>