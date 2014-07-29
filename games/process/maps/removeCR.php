<?
	checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$mapID = intval($pathOptions[3]);
	$whichCR = $pathOptions[4];
	$mapInfo = $mysql->query("SELECT maps.rows, maps.columns, maps.bgData FROM maps INNER JOIN players ON maps.gameID = players.gameID AND players.isGM = 1 WHERE players.userID = $userID AND maps.mapID = $mapID");
	if (!$mapInfo->rowCount()) { header('Location: /tools/maps'); exit; }
	list($rows, $columns, $bgData) = $mapInfo->fetch(PDO::FETCH_NUM);
	
	if (preg_match('/\d{1,2}/', $whichCR)) $type = 'r';
	else {
		$whichCR = b26ToDec($whichCR);
		$type = 'c';
	}
	
	$bgData = explode(';', $bgData);
	$tData = array();
/*	for ($rCount = 1; $rCount <= $rows; $rCount++) { if ($type == 'c' || $whichCR != $rCount) {
		for ($cCount = 1; $cCount <= $columns; $cCount++) {
			if ($type == 'r' || b26ToDec($whichCR) != $cCount) {
//				$tData[$rCount][$cCount] = array_shift($sBGData);
				$bgData .= array_shift($sBGData).';';
			} else array_shift($sBGData);
		}
	} }*/
	if ($type == 'c') for ($count = $rows - 1; $count >= 0; $count--) unset($bgData[$count * $columns + $whichCR - 1]);
	else for ($count = ($whichCR - 1) * $columns; $count < $whichCR * $columns; $count++) unset($bgData[$count]);
	
	$mysql->query('UPDATE maps SET '.(($type == 'c')?"columns = columns":"rows = rows")." - 1, bgData = '".implode(';', $bgData)."' where mapID = $mapID");
	
	header('Location: /tools/maps/'.$mapID);
?>