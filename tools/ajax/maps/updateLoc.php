<?
	checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$iconID = intval($_POST['iconID']);
	$location = preg_match('/^[0-9]{1,2}_[0-9]{1,2}$/', $_POST['location'])?$_POST['location']:'';
	
//	if (strlen($location)) {
		$iconInfo = $mysql->query("SELECT mapID, location FROM maps_icons WHERE iconID = $iconID");
		$iconInfo = $iconInfo->fetch();
		list($mapID, $origin) = $iconInfo;
		$mysql->query("UPDATE maps_icons SET location = '$location' WHERE iconID = $iconID");
		$mysql->query("INSERT INTO maps_iconHistory (iconID, mapID, enactedBy, enactedOn, action, origin, destination) VALUES ($iconID, $mapID, $userID, NOW(), 'moved', '$origin', '$location')");
//	}
?>