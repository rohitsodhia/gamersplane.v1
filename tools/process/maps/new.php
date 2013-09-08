<?
	checkLogin();
	
	if (isset($_POST['submit'])) {
		$userID = intval($_SESSION['userID']);
		$gameID = intval($_POST['gameID']);
		$gmCheck = $mysql->query("SELECT `primary` FROM gms WHERE gameID = $gameID AND userID = $userID");
		if (!$gmCheck->rowCount()) { header('Location: '.SITEROOT.'/tools/maps'); exit; }
		
		$mapName = sanatizeString($_POST['mapName']);
		$rows = intval($_POST['rows']);
		$columns = intval($_POST['columns']);
		if ($rows < 0 || $columns < 0 || strlen($mapName)) { header('Location: '.$_SESSION['lastURL']); exit; }
		$bgData = '';
		for ($count = 0; $count < $rows * $columns; $count++) $bgData .= ';';
		
		$mysql->query("INSERT INTO maps (gameID, name, rows, columns, bgData) VALUES ($gameID, '$mapName', $rows, $columns, '$bgData')");
		
		header('Location: '.SITEROOT.'/tools/maps/edit/'.$mysql->lastInsertId());
	} else header('Location: '.SITEROOT.'/tools/maps');
?>