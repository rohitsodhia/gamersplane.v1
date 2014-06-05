<?
	checkLogin();
	
	if (isset($_POST['addCR'])) {
		$userID = intval($_SESSION['userID']);
		$mapID = intval($_POST['mapID']);
		$mapCheck = $mysql->query("SELECT m.gameID, m.rows, m.cols FROM maps m, players p WHERE m.gameID = p.gameID AND p.userID = $userID AND m.mapID = $mapID AND p.isGM = 1");
		if (!$mapCheck->rowCount()) { header('Location: /tools/maps'); exit; }
		list($gameID, $rows, $columns) = $mapCheck->fetch(PDO::FETCH_NUM);
		
		$addType = $_POST['addType'] == 'c'?'col':'row';
		$addLoc = $_POST['addLoc'] == 'a'?'a':'b';
		if ($addType == 'column') $addPos = preg_match('/[a-z]+/',$_POST['addPos'])?$_POST['addPos']:'a';
		else $addPos = preg_match('/[0-9]+/',$_POST['addPos'])?$_POST['addPos']:'1';
		if ($addType == 'column') $addPos = b26ToDec($addPos);
		else $addPos = intval($addPos);
		
		if (($addType == 'row' && $rows + 1 > 20) || ($addType == 'col' && $columns + 1 > 20)) { header('Location: /tools/maps/edit/'.$mapID.'?exceededSize=1'); exit; }
		
		$mysql->query("UPDATE maps SET {$addType}s = {$addType}s + 1 WHERE mapID = $mapID");
		$mysql->query("UPDATE mapData SET $addType = $addType + 1 WHERE mapID = $mapID AND $addType >".($addLoc == 'b'?'=':'')." $addPos");
		
		header("Location: /games/$gameID/maps/{$mapID}/");
	} else header('Location: /games/');
?>