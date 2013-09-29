<?
	checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$mapID = intval($_POST['mapID']);
	$iconID = intval($_POST['iconID']);
	$color = in_array($_POST['color'], array('blue', 'green', 'grey', 'orange', 'red', 'white'))?$_POST['color']:'white';
	$label = preg_match('/^[0-9a-zA-Z]{1,2}$/', $_POST['label'])?$_POST['label']:'';
	$name = sanatizeString($_POST['name']);
	
	if (isset($_POST['submit']) && strlen($label) && strlen($name)) {
		$mysql->query("INSERT INTO maps_icons (mapID, label, name, color) VALUES ($mapID, '$label', '$name', '$color')");
		$mysql->query("INSERT INTO maps_iconHistory (iconID, mapID, enactedBy, enactedOn, action) VALUES (".$mysql->lastInsdrtID().", $mapID, $userID, NOW()'created')");
		header('Location: '.SITEROOT.'/tools/maps/'.$mapID);
	} elseif (isset($_POST['save']) && $iconID && strlen($label) && strlen($name)) {
		$mysql->query("UPDATE maps_icons SET label = '$label', name ='$name', color = '$color' WHERE iconID = $iconID");
		$mysql->query("INSERT INTO maps_iconHistory (iconID, mapID, enactedBy, enactedOn, action) VALUES ($iconID, $mapID, $userID, NOW()'edited')");
		header('Location: '.SITEROOT.'/tools/maps/'.$mapID);
	} elseif (isset($_POST['delete']) && $iconID) {
		$mysql->query("DELETE FROM maps_icons WHERE iconID = $iconID");
		$mysql->query("INSERT INTO maps_iconHistory (iconID, mapID, enactedBy, enactedOn, action) VALUES ($iconID, $mapID, $userID, NOW()'deleted')");
		header('Location: '.SITEROOT.'/tools/maps/'.$mapID);
	} else header('Location: '.SITEROOT.'/tools/maps');
?>