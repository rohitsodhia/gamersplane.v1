<?
	checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$mapID = intval($_POST['mapID']);
	$iconID = intval($_POST['iconID']);
	$color = in_array($_POST['color'], array('blue', 'green', 'grey', 'orange', 'red', 'white'))?$_POST['color']:'white';
	$label = preg_match('/^[0-9a-zA-Z]{1,2}$/', $_POST['label'])?$_POST['label']:'';
	$name = sanitizeString($_POST['name']);

	if (isset($_POST['submit']) && strlen($label) && strlen($name)) {
		$newIcon = new Icon();
		$newIcon->updateAttr('mapID', $mapID);
		$newIcon->updateAttr('label', $label);
		$newIcon->updateAttr('name', $name);
		$newIcon->updateAttr('color', $color);
		$newIcon->saveIcon();
		echo json_encode(array('success' => TRUE, 'action' => 'new', 'iconHTML' => (string) $newIcon));
	} elseif (isset($_POST['save']) && $iconID && strlen($label) && strlen($name)) {
		$newIcon = new Icon($iconID);
		$newIcon->updateAttr('label', $label);
		$newIcon->updateAttr('name', $name);
		$newIcon->updateAttr('color', $color);
		$history = $newIcon->saveIcon();
		echo json_encode(array('success' => TRUE, 'action' => 'edit', 'history' => $history));
	} elseif (isset($_POST['delete']) && $iconID) {
		$mysql->query("DELETE FROM maps_icons WHERE iconID = $iconID");
		$mysql->query("INSERT INTO maps_iconHistory (iconID, mapID, enactedBy, enactedOn, action) VALUES ($iconID, $mapID, $userID, NOW()'deleted')");
	} else header('Location: '.SITEROOT.'/tools/maps');
?>