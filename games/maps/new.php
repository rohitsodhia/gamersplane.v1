<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$gameID = intval($pathOptions[0]);
	$gmCheck = $mysql->query("SELECT primaryGM FROM players WHERE isGM = 1 AND gameID = $gameID AND userID = $userID");
	if (!$gmCheck->rowCount()) { header('Location: '.SITEROOT.'/tools/maps'); exit; }
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">New Map</h1>
		
		<form method="post" action="<?=SITEROOT?>/games/process/maps/new" class="hbMargined">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<div class="tr">
				<label class="textLabel">Map Name:</label>
				<input type="text" name="mapName">
			</div>
			<div id="mapSize" class="tr">
				<label class="textLabel">Map Size:</label>
				<input type="text" name="rows"> Rows x <input type="text" name="columns"> Columns
			</div>
			<div class="tr alignCenter"><button type="submit" name="submit" class="fancyButton">Submit</button></div>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>