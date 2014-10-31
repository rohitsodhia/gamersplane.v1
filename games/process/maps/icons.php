<?
	$mapID = intval($_POST['mapID']);
	$iconID = intval($_POST['iconID']);
	$color = preg_match('/[0-9A-F]{6}/', $_POST['color'])?$_POST['color']:'FFFFFF';
	$label = preg_match('/^[0-9a-zA-Z]{1,2}$/', $_POST['label'])?$_POST['label']:'';
	$name = sanitizeString($_POST['name']);

	if (isset($_POST['submit']) && strlen($label) && strlen($name)) {
		$newIcon = new Icon();
		$newIcon->updateAttr('mapID', $mapID);
		$newIcon->updateAttr('label', $label);
		$newIcon->updateAttr('name', $name);
		$newIcon->updateAttr('color', $color);
		$history = $newIcon->saveIcon();
		echo json_encode(array('success' => TRUE, 'action' => 'new', 'iconHTML' => (string) $newIcon, 'history' => $history));
	} elseif (isset($_POST['save']) && $iconID && strlen($label) && strlen($name)) {
		$icon = new Icon($iconID);
		$icon->updateAttr('label', $label);
		$icon->updateAttr('name', $name);
		$icon->updateAttr('color', $color);
		$history = $icon->saveIcon();
		echo json_encode(array('success' => TRUE, 'action' => 'edit', 'history' => $history));
	} elseif (isset($_POST['delete']) && $iconID) {
		$icon = new Icon($iconID);
		$history = $icon->deleteIcon();
		echo json_encode(array('success' => TRUE, 'action' => 'delete', 'history' => $history));
	} else echo json_encode(array('failed' => TRUE));
?>