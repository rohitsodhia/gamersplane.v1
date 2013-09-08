<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT cd.*, c.userID, gms.primaryGM IS NOT NULL isGM FROM afmbe_characters cd INNER JOIN characters c ON cd.characterID = c.characterID LEFT JOIN (SELECT gameID, primaryGM FROM players WHERE isGM = 1 AND userID = $userID) gms ON c.gameID = gms.gameID WHERE cd.characterID = $characterID");
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
		<h1 class="headerbar">Character Sheet</h1>
		<div id="charSheetLogo"><img src="<?=SITEROOT?>/images/logos/afmbe.png"></div>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<div class="actions"><a id="editCharacter" href="<?=SITEROOT?>/characters/afmbe/<?=$characterID?>/edit" class="button">Edit Character</a></div>
		<div id="nameDiv" class="tr">
			<label>Name:</label>
			<div><?=$charInfo['name']?></div>
		</div>
		
		<div class="clearfix">
			<div id="primaryStatsCol">
				<h2 class="headerbar hbDark">Primary Attributes</h2>
				<div class="hbMargined clearfix">
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
			</div>
			
			<div id="secondaryStatsCol">
				<h2 class="headerbar hbDark">Secondary Attributes</h2>
				<div class="hbMargined clearfix">
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
			</div>
		</div>
		
		<div class="clearfix">
			<div class="twoCol leftCol">
				<h2 class="headerbar hbDark">Qualities</h2>
				<div class="hbMargined"><?=printReady($charInfo['qualities'])?></div>
			</div>
			
			<div class="twoCol">
				<h2 class="headerbar hbDark">Drawbacks</h2>
				<div class="hbMargined"><?=printReady($charInfo['drawbacks'])?></div>
			</div>
		</div>
		
		<div class="clearfix">
			<div class="twoCol leftCol">
				<h2 class="headerbar hbDark">Skills</h2>
				<div class="hbMargined"><?=printReady($charInfo['skills'])?></div>
			</div>
			
			<div class="twoCol">
				<h2 class="headerbar hbDark">Powers</h2>
				<div class="hbMargined"><?=printReady($charInfo['powers'])?></div>
			</div>
		</div>
		
		<div class="clearfix">
			<div class="twoCol leftCol">
				<h2 class="headerbar hbDark">Weapons</h2>
				<div class="hbMargined"><?=printReady($charInfo['weapons'])?></div>
			</div>
			
			<div class="twoCol">
				<h2 class="headerbar hbDark">Posessions</h2>
				<div class="hbMargined"><?=printReady($charInfo['items'])?></div>
			</div>
		</div>
		
		<div id="charInfoDiv" class="clearfix">
			<h2 class="headerbar hbDark">Character Info/Notes</h2>
			<div class="hbMargined"><?=printReady($charInfo['notes'])?></div>
		</div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>