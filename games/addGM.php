<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$gameID = intval($pathOptions[0]);
	$gmCheck = $mysql->query("SELECT `primary` FROM gms WHERE gameID = $gameID AND userID = $userID");
	if ($gmCheck->rowCount() == 0) { header('Location: '.SITEROOT.'/games/'.$gameID); }
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Add GM</h1>
		
<? if ($_GET['invalidUser'] || $_GET['alreadyGM']) { ?>
		<div class="alertBox_error">
<?
		if ($_GET['invalidUser']) { echo "\t\t\tWe couldn't find this user. Please try a different user.\n"; }
		if ($_GET['alreadyGM']) { echo "\t\t\tThis user is already a GM in this game.\n"; }
?>
		</div>
		
<? } ?>
		<form method="post" action="<?=SITEROOT?>/games/process/addGM">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<p>What user would you like to add as a GM to this game? Remember this person will have full rights to the game, including changing game details, approving/rejecting players, adding other GMs, and being a admin for the game forum.</p>
			<input type="text" name="user">
			<div id="buttonDiv">
				<button type="submit" name="add" class="btn_add"></button>
<!--				<button type="submit" name="cancel" class="btn_cancel"></button>-->
			</div>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>