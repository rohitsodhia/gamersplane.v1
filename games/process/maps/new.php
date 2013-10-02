<?
	checkLogin();
	
	if (isset($_POST['submit'])) {
		$userID = intval($_SESSION['userID']);
		$gameID = intval($_POST['gameID']);
		$gmCheck = $mysql->query("SELECT primaryGM FROM players WHERE isGM = 1 AND gameID = $gameID AND userID = $userID");
		if (!$gmCheck->rowCount()) {
			if (isset($_POST['modal'])) echo -1;
			else header('Location: '.SITEROOT.'/games/'.$gameID);
		} else {
			$mapName = sanitizeString($_POST['mapName']);
			$rows = intval($_POST['rows']);
			$columns = intval($_POST['columns']);
			if ($rows < 0 || $columns < 0 || strlen($mapName)) {
				if (isset($_POST['modal'])) echo -1;
				else header('Location: '.$_SESSION['lastURL']);
			} else {
				$bgData = serialize(array());
				
				$addMap = $mysql->prepare("INSERT INTO maps (gameID, name, rows, columns, bgData) VALUES ($gameID, :mapName, $rows, $columns, :bgData)");
				$addMap->execute(array(':mapName' => $mapName, ':bgData' => $bgData));
				$mapID = $mysql->lastInsertId();
				
				if (isset($_POST['modal'])) echo $mapID;
				else header('Location: '.SITEROOT.'/games/'.$gameID.'/maps/edit/'.$mapID);
			}
		}
	} else {
		if (isset($_POST['modal'])) echo 0;
		else header('Location: '.SITEROOT.'/games/');
	}
?>