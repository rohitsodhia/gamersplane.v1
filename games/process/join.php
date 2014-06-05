<?
	checkLogin(0);
	
	if (isset($_POST['apply'])) {
		$gameID = intval($_POST['gameID']);
		$userID = intval($_SESSION['userID']);
		
		$numPlayers = $mysql->query("SELECT numPlayers FROM games WHERE gameID = $gameID");
		$numPlayers = $numPlayers->fetchColumn();
		$numApprovedPlayers = $mysql->query("SELECT u.userID FROM users u, players p WHERE p.gameID = $gameID AND u.userID = p.userID AND p.approved = 1 ORDER BY u.username ASC");
		$numApprovedPlayers = $numApprovedPlayers->rowCount();
		
		if ($numApprovedPlayers < $numPlayers) {
			$mysql->query("INSERT INTO players (gameID, userID) VALUES ($gameID, $userID)");
			addGameHistory($gameID, 'appliedToGame');
		}
		header('Location: /games/'.$gameID);
	} else header('Location: /403');
?>