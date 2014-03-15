<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$noChar = TRUE;
	$system = 'afmbe';
	$charInfo = getCharInfo($characterID, $system);
	if ($charInfo) {
		if ($viewerStatus = allowCharView($characterID, $userID)) {
			$noChar = FALSE;

			if ($viewerStatus == 'library') $mysql->query("UPDATE characterLibrary SET viewed = viewed + 1 WHERE characterID = $characterID");
		}
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Character Sheet</h1>
<? if (!$noChar) { ?>
		<div class="clearfix"><div id="sheetActions" class="wingDiv hbMargined floatRight">
			<div>
<?		if ($viewerStatus == 'edit') { ?>
				<a id="editCharacter" href="<?=SITEROOT?>/characters/<?=$system?>/<?=$characterID?>/edit" class="sprite pencil"></a>
<?		} else { ?>
				<a href="/" class="favoriteChar sprite tassel off" title="Favorite" alt="Favorite"></a>
<?		} ?>
			</div>
			<div class="wing ulWing"></div>
			<div class="wing urWing"></div>
		</div></div>
<? } ?>
		<div id="charSheetLogo"><img src="<?=SITEROOT?>/images/logos/<?=$system?>.png"></div>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">

		<div id="nameDiv" class="tr">
			<label>Name:</label>
			<div><?=$charInfo['name']?></div>
		</div>
		
		<div class="clearfix">
			<div id="primaryStatsCol">
				<h2 class="headerbar hbDark">Primary Attributes</h2>
				<div class="hbdMargined clearfix">
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
				<div class="hbdMargined clearfix">
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
				<div class="hbdMargined"><?=printReady($charInfo['qualities'])?></div>
			</div>
			
			<div class="twoCol">
				<h2 class="headerbar hbDark">Drawbacks</h2>
				<div class="hbdMargined"><?=printReady($charInfo['drawbacks'])?></div>
			</div>
		</div>
		
		<div class="clearfix">
			<div class="twoCol leftCol">
				<h2 class="headerbar hbDark">Skills</h2>
				<div class="hbdMargined"><?=printReady($charInfo['skills'])?></div>
			</div>
			
			<div class="twoCol">
				<h2 class="headerbar hbDark">Powers</h2>
				<div class="hbdMargined"><?=printReady($charInfo['powers'])?></div>
			</div>
		</div>
		
		<div class="clearfix">
			<div class="twoCol leftCol">
				<h2 class="headerbar hbDark">Weapons</h2>
				<div class="hbdMargined"><?=printReady($charInfo['weapons'])?></div>
			</div>
			
			<div class="twoCol">
				<h2 class="headerbar hbDark">Posessions</h2>
				<div class="hbdMargined"><?=printReady($charInfo['items'])?></div>
			</div>
		</div>
		
		<div id="charInfoDiv" class="clearfix">
			<h2 class="headerbar hbDark">Character Info/Notes</h2>
			<div class="hbdMargined"><?=printReady($charInfo['notes'])?></div>
		</div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>