<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT afmbe.*, characters.userID, gms.gameID IS NOT NULL isGM FROM afmbe_characters afmbe INNER JOIN characters ON afmbe.characterID = characters.characterID LEFT JOIN (SELECT gameID, `primary` FROM gms WHERE userID = $userID) gms ON characters.gameID = gms.gameID WHERE afmbe.characterID = $characterID");
	$noChar = TRUE;
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		$gameID = $charInfo['gameID'];
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) $noChar = FALSE;
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Character Sheet</h1>
		<h2><img src="<?=SITEROOT?>/images/logos/afmbe.jpg"></h2>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<form method="post" action="<?=SITEROOT?>/characters/process/afmbe/">
			<input type="hidden" name="characterID" value="<?=$characterID?>">
			
			<div id="nameDiv" class="tr">
				<label class="textLabel">Name:</label>
				<div><input type="text" name="name" maxlength="50"></div>
			</div>
			
			<div id="primaryStatsCol">
				<h2>Primary Attributes</h2>
				<div class="twoCol leftCol">
					<div class="tr">
						<label class="textLabel">Strength</label>
						<input type="text" name="str" maxlength="2">
					</div>
					<div class="tr">
						<label class="textLabel">Dexterity</label>
						<input type="text" name="dex" maxlength="2">
					</div>
					<div class="tr">
						<label class="textLabel">Constitution</label>
						<input type="text" name="Con" maxlength="2">
					</div>
				</div>
				<div class="twoCol">
					<div class="tr">
						<label class="textLabel">Intelligence</label>
						<input type="text" name="int" maxlength="2">
					</div>
					<div class="tr">
						<label class="textLabel">Perception</label>
						<input type="text" name="per" maxlength="2">
					</div>
					<div class="tr">
						<label class="textLabel">Willpower</label>
						<input type="text" name="wil" maxlength="2">
					</div>
				</div>
			</div>
			
			<div id="secondaryStatsCol">
				<h2>Secondary Attributes</h2>
				<div class="tr">
					<label class="textLabel">Life Points</label>
					<input type="text" name="lp" maxlength="2">
				</div>
				<div class="tr">
					<label class="textLabel">Endurance Points</label>
					<input type="text" name="end" maxlength="2">
				</div>
				<div class="tr">
					<label class="textLabel">Speed</label>
					<input type="text" name="spd" maxlength="2">
				</div>
				<div class="tr">
					<label class="textLabel">Essence Pool</label>
					<input type="text" name="ess" maxlength="2">
				</div>
			</div>
			
			<br class="clear">
			
			<div class="twoCol leftCol">
				<h2>Qualities</h2>
				<textarea name="qualities"></textarea>
			</div>
			
			<div class="twoCol">
				<h2>Drawbacks</h2>
				<textarea name="drawbacks"></textarea>
			</div>
			
			<br class="clear">
			
			<div class="twoCol leftCol">
				<h2>Skills</h2>
				<textarea name="skills"></textarea>
			</div>
			
			<div class="twoCol">
				<h2>Powers</h2>
				<textarea name="powers"></textarea>
			</div>
			
			<br class="clear">
			
			<div class="twoCol leftCol">
				<h2>Weapons</h2>
				<textarea name="weapons"></textarea>
			</div>
			
			<div class="twoCol">
				<h2>Posessions</h2>
				<textarea name="items"></textarea>
			</div>
			
			<br class="clear">
			
			<div id="charInfoDiv">
				<h2>Character Info/Notes</h2>
				<textarea id="notes" name="notes"></textarea>
			</div>
			
			<div id="submitDiv">
				<button type="submit" name="save" class="btn_save"></button>
			</div>
		</form>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>