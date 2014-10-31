<?
	if ($_POST['validate'] != 'M3FnvL763xjm8HJfZ6kn') { header('Location: /'); exit; }
	
	$mapID = intval($_POST['mapID']);
	$gmCheck = $mysql->query("SELECT players.isGM FROM maps INNER JOIN players ON players.gameID = maps.gameID AND players.isGM = 1 WHERE players.userID = {$currentUser->userID} AND maps.mapID = $mapID");
	$isGM = $gmCheck->rowCount()?TRUE:FALSE;
	$iconHistory = $mysql->query("SELECT ic.iconID, icons.label, icons.name, ic.mapID, ic.enactedBy, users.username, ic.enactedOn, ic.action, ic.origin, ic.destination FROM maps_iconHistory ic, maps_icons icons, users WHERE ic.iconID = icons.iconID AND ic.enactedBy = users.userID ".($isGM?'':"AND ic.action = 'moved' ")."AND ic.mapID = $mapID ORDER BY ic.actionID");
?>
<!DOCTYPE html>
<html>
<head>
</head>

<body>
<?
	if ($iconHistory->rowCount()) { foreach ($iconHistory as $actionInfo) {
		if ($actionInfo['action'] == 'moved') echo "\t\t\t\t<p>(".date('m/d/y H:i', strtotime($actionInfo['enactedOn'])).") <a href=\"/ucp/{$actionInfo['enactedBy']}\">{$actionInfo['username']}</a> moved <b>{$actionInfo['name']}</b> ({$actionInfo['label']}) from ".(strlen($actionInfo['origin'])?strtoupper($actionInfo['origin']):'Box')." to ".(strlen($actionInfo['destination'])?strtoupper($actionInfo['destination']):'Box')."</p>\n";
		elseif ($actionInfo['action'] == 'created') echo "\t\t\t\t<p>(".date('m/d/y H:i', strtotime($actionInfo['enactedOn'])).") <a href=\"/ucp/{$actionInfo['enactedBy']}\">{$actionInfo['username']}</a> created <b>{$actionInfo['name']}</b> ({$actionInfo['label']})</p>\n";
		elseif ($actionInfo['action'] == 'edited') echo "\t\t\t\t<p>(".date('m/d/y H:i', strtotime($actionInfo['enactedOn'])).") <a href=\"/ucp/{$actionInfo['enactedBy']}\">{$actionInfo['username']}</a> edited <b>{$actionInfo['name']}</b> ({$actionInfo['label']})</p>\n";
		elseif ($actionInfo['action'] == 'deleted') echo "\t\t\t\t<p>(".date('m/d/y H:i', strtotime($actionInfo['enactedOn'])).") <a href=\"/ucp/{$actionInfo['enactedBy']}\">{$actionInfo['username']}</a> deleted <b>{$actionInfo['name']}</b> ({$actionInfo['label']})</p>\n";
	} } else echo "<p>No history yet.</p>\n";
?>
</body>
</html>