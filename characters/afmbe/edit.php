<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT cd.*, c.userID, gms.primaryGM IS NOT NULL isGM FROM afmbe_characters cd INNER JOIN characters c ON cd.characterID = c.characterID LEFT JOIN (SELECT gameID, primaryGM FROM players WHERE isGM = 1 AND userID = $userID) gms ON c.gameID = gms.gameID WHERE cd.characterID = $characterID");
	$noChar = TRUE;
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		$gameID = $charInfo['gameID'];
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) $noChar = FALSE;
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Edit Character Sheet</h1>
		<div id="charSheetLogo"><img src="<?=SITEROOT?>/images/logos/afmbe.png"></div>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<form method="post" action="<?=SITEROOT?>/characters/process/afmbe/">
			<input type="hidden" name="characterID" value="<?=$characterID?>">
			
			<div id="nameDiv" class="tr">
				<label class="textLabel">Name:</label>
				<div><input type="text" name="name" maxlength="50"></div>
			</div>
			
			<div class="clearfix">
				<div id="primaryStatsCol">
					<h2 class="headerbar hbDark">Primary Attributes</h2>
					<div class="hbdMargined clearfix">
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
								<input type="text" name="con" maxlength="2">
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
				</div>
				
				<div id="secondaryStatsCol">
					<h2 class="headerbar hbDark">Secondary Attributes</h2>
					<div class="hbdMargined clearfix">
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
				</div>
			</div>
			
			<div class="clearfix">
				<div class="twoCol leftCol">
					<h2 class="headerbar hbDark">Qualities</h2>
					<textarea name="qualities" class="hbdMargined"></textarea>
				</div>
				
				<div class="twoCol">
					<h2 class="headerbar hbDark">Drawbacks</h2>
					<textarea name="drawbacks" class="hbdMargined"></textarea>
				</div>
			</div>
			
			<div class="clearfix">
				<div class="twoCol leftCol">
					<h2 class="headerbar hbDark">Skills</h2>
					<textarea name="skills" class="hbdMargined"></textarea>
				</div>
				
				<div class="twoCol">
					<h2 class="headerbar hbDark">Powers</h2>
					<textarea name="powers" class="hbdMargined"></textarea>
				</div>
			</div>
			
			<div class="clearfix">
				<div class="twoCol leftCol">
					<h2 class="headerbar hbDark">Weapons</h2>
					<textarea name="weapons" class="hbdMargined"></textarea>
				</div>
				
				<div class="twoCol">
					<h2 class="headerbar hbDark">Posessions</h2>
					<textarea name="items" class="hbdMargined"></textarea>
				</div>
			</div>
			
			<div id="charInfoDiv">
				<h2 class="headerbar hbDark">Character Info/Notes</h2>
				<textarea id="notes" name="notes" class="hbdMargined"></textarea>
			</div>
			
			<div id="submitDiv"><button type="submit" name="save" class="fancyButton">Save</button></div>
		</form>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>