<?
	$gameID = intval($pathOptions[0]);
	$mapID = intval($pathOptions[2]);
	$gmCheck = $mongo->games->findOne(array('gameID' => $gameID, 'players' => array('$elemMatch' => array('user.userID' => $currentUser->userID, 'isGM ' => true))), array('players.$' => true));
	if (!$gmCheck) { header('Location: /tools/maps'); exit; }
?>
<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Delete Map</h1>
		
<?
	$mapDetails = $mysql->query("SELECT name FROM maps where mapID = $mapID");
	$mapDetails = $mapDetails->fetch();
	extract($mapDetails);
?>
		<form method="post" action="/games/process/maps/delete" class="hbMargined">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<input type="hidden" name="mapID" value="<?=$mapID?>">
			
			<p class="alignCenter">Are you sure you want to delete the map <strong><?=$name?></strong>?</p>
			<div class="alignCenter"><button type="submit" name="delete" class="fancyButton">Delete</button></div>
		</form>
<?	require_once(FILEROOT.'/footer.php'); ?>