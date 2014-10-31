<?
	checkLogin();
	
	$mapID = intval($_POST['mapID']);
	$bgData = $_POST['bgData'];

	$gmCheck = $mysql->query("SELECT p.userID FROM maps m, players p WHERE m.gameID = p.gameID AND p.userID = {$currentUser->userID} AND m.mapID = $mapID and p.isGM = 1");
	if (!$gmCheck->rowCount()) echo 0;
	else {
		$mysql->query("DELETE FROM mapData WHERE mapID = $mapID");
		$addMapData = $mysql->prepare("INSERT INTO mapData SET mapID = $mapID, col = :col, row = :row, data = :data");
		foreach ($bgData as $pos => $data) {
			if (preg_match('/[^\w\d#]/', $data)) continue;
			list($col, $row) = explode('_', $pos);
			$addMapData->bindValue(':col', intval($col));
			$addMapData->bindValue(':row', intval($row));
			$addMapData->bindValue(':data', '#'.$data);
			$addMapData->execute();
		}
		
		if (sizeof($bgData)) echo 1;
		else echo 0;
	}
?>