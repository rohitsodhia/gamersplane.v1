<?
	checkLogin();
	
	if (isset($_POST['addCR'])) {
		$userID = intval($_SESSION['userID']);
		$mapID = intval($_POST['mapID']);
		$mapCheck = $mysql->query("SELECT maps.rows, maps.columns, maps.bgData FROM maps INNER JOIN gms ON maps.gameID = gms.gameID WHERE gms.userID = $userID AND maps.mapID = $mapID");
		if (!$mapCheck->rowCount()) { header('Location: '.SITEROOT.'/tools/maps'); exit; }
		$mapInfo = $mysql->fetch();
		list($rows, $columns, $bgData) = $mapInfo;
		
		$addType = $_POST['addType'] == 'c'?'c':'r';
		$addLoc = $_POST['addLoc'] == 'a'?'a':'b';
		$addPos = sanatizeString($_POST['addPos']);
		if ($addType == 'c') $addPos = b26ToDec($addPos);
		else $addPos = intval($addPos);
		
		if (($addType == 'r' && $rows + 1 > 20) || ($addType == 'c' && $columns + 1 > 20)) { header('Location: '.SITEROOT.'/tools/maps/edit/'.$mapID.'?exceededSize=1'); exit; }
		
		if ($addType == 'c') {
			$bgData = explode(';', $bgData);
			for ($count = $rows - 1; $count >= 0; $count--) array_splice($bgData, $count * $columns + ($addPos - ($addLoc == 'b'?1:0)), 0, '');
			$bgData = implode(';', $bgData);
		} else {
			$bgData = explode(';', $bgData);
			$insArray = array();
			for ($count = 0; $count < $columns; $count++) $insArray[] = '';
			array_splice($bgData, ($addPos - ($addLoc == 'b'?1:0)) * $columns, 0, $insArray);
			$bgData = implode(';', $bgData);
		}
		
		$mysql->query('UPDATE maps SET '.(($addType == 'c')?"columns = columns":"rows = rows")." + 1, bgData = '$bgData' where mapID = $mapID");
		$_SESSION['lastSet'] = $addType;
		
		header('Location: '.SITEROOT.'/tools/maps/edit/'.$mapID);
	} else header('Location: '.SITEROOT.'/tools/maps');
?>