<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT afmbe.*, characters.userID, gms.gameID IS NOT NULL isGM FROM afmbe_characters afmbe INNER JOIN characters ON afmbe.characterID = characters.characterID LEFT JOIN (SELECT gameID, `primary` FROM gms WHERE userID = $userID) gms ON characters.gameID = gms.gameID WHERE afmbe.characterID = $characterID");
	$noChar = TRUE;
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		$gameID = $charInfo['gameID'];
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) {
			foreach ($charInfo as $key => $value) if ($value == '') $charInfo[$key] = '&nbsp;';
			$noChar = FALSE;
		}
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Character Sheet</h1>
		<h2><img src="<?=SITEROOT?>/images/logos/afmbe.jpg"></h2>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<a href="<?=SITEROOT?>/characters/afmbe/<?=$characterID?>/edit">Edit Character</a>
		<div id="nameDiv" class="tr">
			<label>Name:</label>
			<div><?=$charInfo['name']?></div>
		</div>
		
		<div id="primaryStatsCol">
			<h2>Primary Attributes</h2>
			<div class="twoCol leftCol">
				<div class="tr">
					<label>Strength</label>
					<?=$charInfo['str']?>
				</div>
				<div class="tr">
					<label>Dexterity</label>
					<?=$charInfo['dex']?>
				</div>
				<div class="tr">
					<label>Constitution</label>
					<?=$charInfo['con']?>
				</div>
			</div>
			<div class="twoCol">
				<div class="tr">
					<label>Intelligence</label>
					<?=$charInfo['int']?>
				</div>
				<div class="tr">
					<label>Perception</label>
					<?=$charInfo['per']?>
				</div>
				<div class="tr">
					<label>Willpower</label>
					<?=$charInfo['wil']?>
				</div>
			</div>
		</div>
		
		<div id="secondaryStatsCol">
			<h2>Secondary Attributes</h2>
			<div class="tr">
				<label>Life Points</label>
					<?=$charInfo['lp']?>
			</div>
			<div class="tr">
				<label>Endurance Points</label>
					<?=$charInfo['end']?>
			</div>
			<div class="tr">
				<label>Speed</label>
					<?=$charInfo['spd']?>
			</div>
			<div class="tr">
				<label>Essence Pool</label>
					<?=$charInfo['ess']?>
			</div>
		</div>
		
		<br class="clear">
		
		<div class="twoCol leftCol">
			<h2>Qualities</h2>
			<?=printReady($charInfo['qualities'])?>
		</div>
		
		<div class="twoCol">
			<h2>Drawbacks</h2>
			<?=printReady($charInfo['drawbacks'])?>
		</div>
		
		<br class="clear">
		
		<div class="twoCol leftCol">
			<h2>Skills</h2>
			<?=printReady($charInfo['skills'])?>
		</div>
		
		<div class="twoCol">
			<h2>Powers</h2>
			<?=printReady($charInfo['powers'])?>
		</div>
		
		<br class="clear">
		
		<div class="twoCol leftCol">
			<h2>Weapons</h2>
			<?=printReady($charInfo['weapons'])?>
		</div>
		
		<div class="twoCol">
			<h2>Posessions</h2>
			<?=printReady($charInfo['items'])?>
		</div>
		
		<br class="clear">
		
		<div id="charInfoDiv">
			<h2>Character Info/Notes</h2>
			<?=printReady($charInfo['notes'])?>
		</div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>