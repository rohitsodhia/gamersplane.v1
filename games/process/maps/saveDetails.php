<?
	checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$mapID = intval($_POST['mapID']);
	$details = sanatizeString($_POST['details']);
	
	if (isset($_POST['save'])) {
		$mapCheck = $mysql->query('SELECT gms.userID FROM maps, gms WHERE maps.gameID = gms.gameID AND gms.userID = '.$userID.' AND maps.mapID = '.$mapID);
		if ($mapCheck->rowCount()) $mysql->query("UPDATE maps SET details = '$details' where mapID = $mapID");
	}
	
	header('Location: '.SITEROOT.'/tools/maps/'.$mapID);
?>