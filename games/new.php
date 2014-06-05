<?
	$loggedIn = checkLogin();
	
	if ($pathOptions[0] != 'new') $display = 'new';
	else $display = 'edit';

	if ($display == 'edit') {
		$gameID = intval($pathOptions[0]);
		$userID = intval($_SESSION['userID']);
		$gameDetails = $mysql->query('SELECT g.gameID, g.title, g.systemID, g.gmID, g.postFrequency, g.numPlayers, g.description, g.charGenInfo FROM games g INNER JOIN players gms ON g.gameID = gms.gameID AND gms.isGM = 1 WHERE g.gameID = '.$gameID.' AND gms.userID = '.$userID);
		if ($gameDetails->rowCount() == 0) { header('Location: /403'); exit; }
		else {
			$gameDetails = $gameDetails->fetch();
			foreach ($gameDetails as $key => $value) $$key = $value;
		}
	}

	if ($_SESSION['errors']) {
		if (preg_match('/games(\/process)?\/new\/?.*$/', $_SESSION['lastURL'])) {
			$errors = $_SESSION['errors'];
			foreach ($_SESSION['errorVals'] as $key => $value) $$key = $value;
		}
		if (!preg_match('/games(\/process)?\/new\/?.*$/', $_SESSION['lastURL']) || time() > $_SESSION['errorTime']) {
			unset($_SESSION['errors']);
			unset($_SESSION['errorVals']);
		}
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
<? if ($display == 'new') { ?>
		<div class="sideWidget">
			<h2>LFGs</h2>
			<div class="widgetBody">
				<p>Players are currently looking to play...</p>
				<ul>
<?
	$lfgSummaries = $mysql->query('SELECT s.systemID, s.fullName, l.numPlayers FROM systems s LEFT JOIN (SELECT systemID, COUNT(systemID) numPlayers FROM lfg GROUP BY systemID) l USING (systemID) WHERE l.numPlayers != 0 ORDER BY l.numPlayers DESC, s.fullName');
	$totalsInfo = array();
	foreach ($lfgSummaries as $info) echo "\t\t\t\t\t<li>{$info['fullName']} - ".($info['numPlayers']?$info['numPlayers']:'0')."</li>\n";
?>
				</ul>
			</div>
		</div>

		<div class="mainColumn">
<? } ?>
			<h1 class="headerbar"><?=$display == 'new'?'New':'Edit'?> Game</h1>
			
<? if (sizeof($_GET) && $errors) { ?>
			<div class="alertBox_error">
				Seems like there were some problems:
				<ul>
<?
	if ($errors['invalidTitle']) { echo "\t\t\t\t\t<li>Seems like there's something wrong with your game's title.</li>\n"; }
	if ($errors['repeatTitle']) { echo "\t\t\t\t\t<li>Someone else already has a game by this name.</li>\n"; }
	if ($errors['invalidSystem']) { echo "\t\t\t\t\t<li>You didn't select a system!</li>\n"; }
	if ($errors['invalidNumPlayers']) { echo "\t\t\t\t\t<li>You need at least 2 players in a game.</li>\n"; }
?>
				</ul>
			</div>
<? } ?>
			
			<form method="post" action="/games/process/new/">
				<div class="tr">
					<label>Title</label>
					<input type="text" name="title" value="<?=$title?>" maxlength="50">
				</div>
				<div class="tr">
					<label>System</label>
					<select name="system">
						<option value="">Select One</option>
<?
	$allSystems = $systems->getAllSystems(TRUE);
	foreach ($allSystems as $systemID => $systemInfo) echo "\t\t\t\t\t\t".'<option value="'.$systemID.'">'.printReady($systemInfo['fullName'])."</option>\n";
//	foreach ($systemNames as $value => $name) { echo "\t\t\t\t\t\t".'<option value="'.$value.'"'.(($system == $value)?' selected="selected"':'').'>'.$name.'</option>'."\n"; }
?>
						<option value="1">Custom</option>
					</select>
				</div>
				<div class="tr">
					<label>Post Frequency</label>
					<input id="timesPer" type="text" name="timesPer" value="<?=$errors?$timesPer:1?>" maxlength="2"> time(s) per 
					<select name="perPeriod">
						<option value="d"<?=($perPeriod == 'd')?' selected="selected"':''?>>Day</option>
						<option value="w"<?=($perPeriod == 'w')?' selected="selected"':''?>>Week</option>
					</select>
				</div>
				<div class="tr">
					<label>Number of Players</label>
					<input id="numPlayers" type="text" name="numPlayers" value="<?=$errors?$numPlayers:2?>" maxlength="2">
				</div>
				<div class="tr">
					<label>Description</label>
					<textarea name="description"><?=$description?></textarea>
				</div>
				<div class="tr">
					<label>Character Generation Info</label>
					<textarea name="charGenInfo"><?=$charGenInfo?></textarea>
				</div>
				
				<div id="submitDiv"><button type="submit" name="<?=$display == 'new'?'create':'save'?>" class="fancyButton"><?=$display == 'new'?'Create':'Save'?></button></div>
			</form>
<? if ($display == 'new') { ?>
		</div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>