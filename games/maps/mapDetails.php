<?
	$mapID = intval($pathOptions[2]);
	$modal = $pathOptions[3] == 'modal'?true:false;
	$gmCheck = $mysql->query("SELECT players.isGM FROM maps INNER JOIN players ON maps.gameID = players.gameID AND players.isGM = 1 WHERE gms.userID = {$currentUser->userID} AND maps.mapID = $mapID");
	if (!$gmCheck->rowCount()) {
		$isGM = false;
		$playerCheck = $mysql->query("SELECT characters.userID FROM characters INNER JOIN maps USING (gameID) WHERE characters.userID = {$currentUser->userID} AND maps.mapID = $mapID");
		if (!$playerCheck->rowCount()) { header('Location: /403'); exit; }
	} else 
		$isGM = true;
	$mapInfo = $mysql->query('SELECT maps.gameID, maps.name, maps.columns, maps.rows, maps.bgData, maps.details, games.title, games.system FROM maps, games WHERE maps.gameID = games.gameID AND maps.mapID = '.$mapID);
	$mapInfo = $mapInfo->fetch();
	$mapInfo['bgData'] = explode(';', $mapInfo['bgData']);
	
	$mapIcons = $mysql->query("SELECT * FROM maps_icons WHERE mapID = $mapID");
	$iconsInBox = array();
	$iconsOnMap = array();
	foreach ($mapIcons as $info) {
		if ($info['location'] == '') $iconsInBox[] = $info;
		else $iconsOnMap[$info['location']] = $info;
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<noscript><div class="alertBox_error">
			<p>This page requires javascript to run.</p>
			<p>Please make sure your browser is running javascript to use the GP mapping system.</p>
		</div></noscript>
		
		<h1>Map Details</h1>
		<h2><?=printReady($mapInfo['name'])?></h2>
		
		<form id="saveDetails" method="post" action="/tools/process/maps/saveDetails">
			<input type="hidden" name="mapID" value="<?=$mapID?>">
			<textarea name="details"><?=$mapInfo['details']?></textarea>
			<button type="submit" name="save" class="btn_save"></button>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>