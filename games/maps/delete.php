<?php
	$gameID = intval($pathOptions[0]);
	$mapID = intval($pathOptions[2]);
	$gmCheck = $mongo->games->findOne(
		[
			'gameID' => $gameID,
			'players' => ['$elemMatch' => [
				'user.userID' => $currentUser->userID,
				'isGM ' => true
			]]
		],
		['projection' => ['players.$' => true]]
	);
	if (!$gmCheck) { header('Location: /tools/maps'); exit; }
?>
<?php	require_once(FILEROOT . '/header.php'); ?>
		<h1 class="headerbar">Delete Map</h1>

<?php
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
<?php	require_once(FILEROOT . '/footer.php'); ?>
