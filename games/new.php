<?
	$loggedIn = checkLogin();
	
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
		<div class="sidebar">
			<div class="widget">
				<h3>LFGs</h3>
				<div class="widgetBody">
					<p>Players are currently looking to play...</p>
<?
	$lfgSummaries = $mysql->query('SELECT s.systemID, s.fullName, l.numPlayers FROM systems s LEFT JOIN (SELECT systemID, COUNT(systemID) numPlayers FROM lfg GROUP BY systemID) l USING (systemID) WHERE l.numPlayers != 0 ORDER BY l.numPlayers DESC, s.fullName');
	$totalsInfo = array();
	foreach ($lfgSummaries as $info) echo "\t\t\t\t\t<p class=\"indent\">{$info['fullName']} - ".($info['numPlayers']?$info['numPlayers']:'0')."</p>\n";
?>
				</div>
			</div>
		</div>
		<div class="mainColumn">
			<h1>New Game</h1>
			
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
			
			<form method="post" action="<?=SITEROOT?>/games/process/new/">
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
	foreach ($systems as $info) echo "\t\t\t\t\t\t".'<option value="'.$info['systemID'].'">'.printReady($info['fullName'])."</option>\n";
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
				
				<div class="alignCenter"><button type="submit" name="create" class="btn_create"></button></div>
			</form>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>