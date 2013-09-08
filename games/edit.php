<?
	$loggedIn = checkLogin();
	
	$gameID = intval($pathOptions[0]);
	$userID = intval($_SESSION['userID']);
	$gameDetails = $mysql->query('SELECT g.gameID, g.title, g.systemID, g.gmID, g.postFrequency, g.numPlayers, g.description, g.charGenInfo FROM games g INNER JOIN gms ON g.gameID = gms.gameID WHERE g.gameID = '.$gameID.' AND gms.userID = '.$userID);
	if ($gameDetails->rowCount() == 0) { header('Location: '.SITEROOT.'/403'); exit; }
	else {
		$gameInfo = $gameInfo->fetch();
		foreach ($gameInfo as $key => $value) $$key = $value;
	}
	
	print_r($_SESSION['errors']);
	if ($_SESSION['errors']) {
		if (preg_match('/games(\/process)?\/.*$/', $_SESSION['lastURL'])) {
			$errors = $_SESSION['errors'];
			foreach ($_SESSION['errorVals'] as $key => $value) $$key = $value;
		}
		if (!preg_match('/games(\/process)?\/.*$/', $_SESSION['lastURL']) || time() > $_SESSION['errorTime']) {
			unset($_SESSION['errors']);
			unset($_SESSION['errorVals']);
		}
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>New Game</h1>
		
<? if (sizeof($_GET) && $errors) { ?>
		<div class="alertBox_error">
			Seems like there were some problems:
			<ul>
<?
	if ($errors['invalidTitle']) { echo "\t\t\t\t<li>Seems like there's something wrong with your game's title.</li>\n"; }
	if ($errors['repeatTitle']) { echo "\t\t\t\t<li>Someone else already has a game by this name.</li>\n"; }
	if ($errors['invalidSystem']) { echo "\t\t\t\t<li>You didn't select a system!</li>\n"; }
	if ($errors['invalidNumPlayers']) { echo "\t\t\t\t<li>You need at least 2 players in a game.</li>\n"; }
?>
			</ul>
		</div>
<? } ?>
		
		<form method="post" action="<?=SITEROOT?>/games/process/edit/">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<div class="tr">
				<label>Title</label>
				<input type="text" name="title" value="<?=$title?>" maxlength="50">
			</div>
			<div class="tr">
				<label>System</label>
				<select name="system">
					<option value="">Select One</option>
<?
	$systems = $mysql->query('SELECT systemID, shortName, fullName FROM systems WHERE enabled = 1 AND systemID != 1 ORDER BY fullName');
	foreach ($systems as $info) echo "\t\t\t\t\t".'<option value="'.$info['systemID'].'">'.printReady($info['fullName'])."</option>\n";
?>
					<option value="1">Custom</option>
				</select>
			</div>
			<div class="tr">
				<label>Post Frequency</label>
				<input id="timesPer" type="text" name="timesPer" value="<?=isset($postFrequency)?substr($postFrequency, 0, strpos($postFrequency, '/')):1?>" maxlength="2"> time(s) per 
				<select name="perPeriod">
					<option value="d"<?=(substr($postFrequency, -1) == 'd')?' selected="selected"':''?>>Day</option>
					<option value="w"<?=(substr($postFrequency, -1) == 'w')?' selected="selected"':''?>>Week</option>
				</select>
			</div>
			<div class="tr">
				<label>Number of Players</label>
				<input id="numPlayers" type="text" name="numPlayers" value="<?=isset($numPlayers)?$numPlayers:2?>" maxlength="2">
			</div>
			<div class="tr">
				<label>Description</label>
				<textarea name="description"><?=printReady($description, array('stripslashes'))?></textarea>
			</div>
			<div class="tr">
				<label>Character Generation Info</label>
				<textarea name="charGenInfo"><?=printReady($charGenInfo, array('stripslashes'))?></textarea>
			</div>
			
			<div class="alignCenter"><button type="submit" name="save" class="btn_save"></button></div>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>