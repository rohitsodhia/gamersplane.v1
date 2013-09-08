<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT deadlands.*, characters.userID, gms.gameID IS NOT NULL isGM FROM deadlands_characters deadlands INNER JOIN characters ON deadlands.characterID = characters.characterID LEFT JOIN (SELECT gameID, `primary` FROM gms WHERE userID = $userID) gms ON characters.gameID = gms.gameID WHERE deadlands.characterID = $characterID");
	$noChar = TRUE;
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		$gameID = $charInfo['gameID'];
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) {
			foreach ($charInfo as $key => $value) if ($value == '') $charInfo[$key] = '&nbsp;';
			$charInfo['wounds'] = explode(',', $charInfo['wounds']);
			$noChar = FALSE;
		}
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Character Sheet</h1>
		<h2><img src="<?=SITEROOT?>/images/logos/deadlands.jpg"></h2>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<a href="<?=SITEROOT?>/characters/deadlands/<?=$characterID?>/edit">Edit Character</a>
		<div id="nameDiv" class="tr">
			<label>Name:</label>
			<div><?=$charInfo['name']?></div>
		</div>
		
		<div class="triCol">
			<h2>Mental</h2>
<?
	$first = TRUE;
	foreach (array('cog' => 'Cognition', 'kno' => 'Knowledge', 'mie' => 'Mien', 'sma' => 'Smarts', 'spi' => 'Spirit') as $abbrev => $label) {
?>
			<div class="statDiv<?=$first?' firstStatDiv':''?>">
				<?=$charInfo[$abbrev.'NumDice'].' d '.$charInfo[$abbrev.'DieType']." <h3>$label</h3>"?>
			</div>
			<h3 class="skillTitle"><?=$label?> Skills</h3>
			<?=printReady($charInfo[$abbrev.'Skills'])?>
<?
		if ($first) $first = FALSE;
	}
?>
		</div>
		<div class="triCol">
			<h2>Corporeal</h2>
<?
	$first = TRUE;
	foreach (array('def' => 'Deftness', 'nim' => 'Nimbleness', 'str' => 'Strength', 'qui' => 'Quickness', 'vig' => 'Vigor') as $abbrev => $label) {
?>
			<div class="statDiv<?=$first?' firstStatDiv':''?>">
				<?=$charInfo[$abbrev.'NumDice'].' d '.$charInfo[$abbrev.'DieType']." <h3>$label</h3>"?>
			</div>
			<h3 class="skillTitle"><?=$label?> Skills</h3>
			<?=printReady($charInfo[$abbrev.'Skills'])?>
<?
		if ($first) $first = FALSE;
	}
?>
		</div>
		<div class="triCol lastTriCol">
			<h2>Edges &amp; Hindrances</h2>
			<?=printReady($charInfo['edge_hind'])?>
			
			<h2>Worst Nightmare</h2>
			<?=printReady($charInfo['nightmare'])?>
			
			<h2>Wounds</h2>
			<div id="woundsDiv">
				<div class="indivWoundDiv">
					<h3>Head</h3>
					<?=$charInfo['wounds'][0]?>
				</div>
				<div class="indivWoundDiv subTwoCol">
					<h3>Left Hand</h3>
					<?=$charInfo['wounds'][1]?>
				</div>
				<div class="indivWoundDiv subTwoCol">
					<h3>Right Hand</h3>
					<?=$charInfo['wounds'][2]?>
				</div>
				<div class="indivWoundDiv">
					<h3>Guts</h3>
					<?=$charInfo['wounds'][3]?>
				</div>
				<div class="indivWoundDiv subTwoCol">
					<h3>Left Leg</h3>
					<?=$charInfo['wounds'][4]?>
				</div>
				<div class="indivWoundDiv subTwoCol">
					<h3>Right Leg</h3>
					<?=$charInfo['wounds'][5]?>
				</div>
			</div>
			
			<div id="windDiv">
				<h3>Wind</h3><?=printReady($charInfo['wind'])?>
			</div>
		</div>
		
		<br class="clear">
		<div class="twoCol">
			<h2 class="leftTitle">Shootin Irons & Such</h2>
			<?=printReady($charInfo['weapons'])?>
			
			<h2 class="leftTitle">Arcane Abilities</h2>
			<?=printReady($charInfo['arcane'])?>
		</div>
		<div class="twoCol lastTwoCol">
			<h2 class="leftTitle">Equipment</h2>
			<?=printReady($charInfo['equipment'])?>
		</div>
		
		<br class="clear">
		<h2 class="leftTitle">Background/Notes</h2>
		<?=printReady($charInfo['notes'])?>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>