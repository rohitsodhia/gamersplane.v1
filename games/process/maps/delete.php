<?
	checkLogin(1);
	
	$userID = intval($_SESSION['userID']);
	$gameID = intval($_POST['gameID']);
	$mapID = intval($_POST['mapID']);
	$gmCheck = $mysql->query("SELECT p.primaryGM FROM players p, maps m WHERE p.isGM = 1 AND p.gameID = $gameID AND m.gameID = p.gameID AND m.mapID = $mapID AND p.userID = $userID");
	if (isset($_POST['delete']) && $gmCheck->rowCount()) {
		$mysql->query("DELETE FROM maps WHERE mapID = $mapID");
		
		addGameHistory($gameID, 'mapDeleted', $userID, 'NOW()', 'map', $mapID);
		
		echo 1;
	} else echo 0;
?>