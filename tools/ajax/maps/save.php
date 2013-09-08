<?
	checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$mapID = intval($_POST['mapID']);
	$bgData = $_POST['bgData'];
	
	$gmCheck = $mysql->query('SELECT gms.userID FROM maps, gms WHERE maps.gameID = gms.gameID AND gms.userID = '.$userID.' AND maps.mapID = '.$mapID);
	if (!$gmCheck->rowCount()) echo 0;
	else {
		$mysql->query("DELETE FROM mapData WHERE mapID = $mapID");
		foreach ($bgData as $pos => $data) {
			if (preg_match('/[^\w\d#]/', $data)) continue;
			list($col, $row) = explode('_', $pos);
			$col = intval($col);
			$row = intval($row);
			$mysql->query("INSERT INTO mapData SET mapID = $mapID, `column` = $col, `row` = $row, data = '$data'");
		}
		
		if (sizeof($bgData)) echo 1;
		else echo 0;
	}
?>