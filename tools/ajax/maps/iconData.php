<?
	checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$iconID = intval($_POST['iconID']);
	
	$iconInfo = $mysql->query("SELECT label, name, color FROM maps_icons WHERE iconID = $iconID");
	if ($iconInfo->rowCount()) {
		$iconInfo = $iconInfo->fetch();
		echo "{$iconInfo['color']}~~~{$iconInfo['label']}~~~{$iconInfo['name']}";
	}
?>