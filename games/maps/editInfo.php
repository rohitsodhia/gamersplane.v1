<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$gameID = intval($pathOptions[0]);
	$mapID = intval($pathOptions[2]);
	$gmCheck = $mysql->query("SELECT primaryGM FROM players WHERE isGM = 1 AND gameID = $gameID AND userID = $userID");
	if (!$gmCheck->rowCount()) { header('Location: /games'); exit; }

	$mapInfo = $mysql->query("SELECT info FROM maps WHERE gameID = $gameID AND mapID = $mapID");
	$mapInfo = $mapInfo->fetchColumn();
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Map Info</h1>
		
		<form id="saveDetails" method="post" action="/games/process/maps/editInfo" class="ajaxForm_refreshParent">
			<input type="hidden" name="mapID" value="<?=$mapID?>">
			<textarea name="info" class="hbMargined"><?=$mapInfo?></textarea>
			<div class="alignCenter"><button type="submit" name="save" class="fancyButton">Save</button></div>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>