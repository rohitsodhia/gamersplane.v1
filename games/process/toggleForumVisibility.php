<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$gameID = intval($_POST['gameID']);
	
	$isGM = $mysql->query("SELECT isGM FROM players WHERE gameID = $gameID AND userID = $userID");
	if ($isGM->fetchColumn()) {
		$mysql->query("UPDATE games g, forums_permissions_general p SET p.read = p.read ^ 1 WHERE g.gameID = $gameID AND g.forumID = p.forumID");
	}
?>