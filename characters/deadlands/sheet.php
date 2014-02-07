<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$noChar = TRUE;
	$charInfo = getCharInfo($characterID, 'deadlands');
	if ($charInfo) {
		$gameID = $charInfo['gameID'];
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) {
			foreach ($charInfo as $key => $value) if ($value == '') $charInfo[$key] = '&nbsp;';
			$charInfo['wounds'] = explode(',', $charInfo['wounds']);
			$noChar = FALSE;
		}
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Character Sheet</h1>
		<div id="charSheetLogo"><img src="<?=SITEROOT?>/images/logos/deadlands.jpg"></div>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<div class="actions"><a id="editCharacter" href="<?=SITEROOT?>/characters/deadlands/<?=$characterID?>/edit" class="fancyButton">Edit Character</a></div>
		<div id="nameDiv" class="tr">
			<label>Name:</label>
			<div><?=$charInfo['name']?></div>
		</div>
		
		<div class="clearfix">
			<div class="triCol">
				<h2 class="headerbar hbDark">Mental</h2>
<?
	$first = TRUE;
	foreach (array('cog' => 'Cognition', 'kno' => 'Knowledge', 'mie' => 'Mien', 'sma' => 'Smarts', 'spi' => 'Spirit') as $abbrev => $label) {
?>
				<div class="hbdMargined statDiv<?=$first?' firstStatDiv':''?>">
					<div class="statDice">
						<?=$charInfo[$abbrev.'NumDice'].' d '.$charInfo[$abbrev.'DieType']." $label"?>
					</div>
					<div class="skillTitle"><?=$label?> Skills</div>
					<?=printReady($charInfo[$abbrev.'Skills'])?>
				</div>
<?
		if ($first) $first = FALSE;
	}
?>
			</div>
			<div class="triCol">
				<h2 class="headerbar hbDark">Corporeal</h2>
<?
	$first = TRUE;
	foreach (array('def' => 'Deftness', 'nim' => 'Nimbleness', 'str' => 'Strength', 'qui' => 'Quickness', 'vig' => 'Vigor') as $abbrev => $label) {
?>
				<div class="hbdMargined">
					<div class="statDiv<?=$first?' firstStatDiv':''?>">
						<?=$charInfo[$abbrev.'NumDice'].' d '.$charInfo[$abbrev.'DieType']." $label"?>
					</div>
					<div class="skillTitle"><?=$label?> Skills</div>
					<?=printReady($charInfo[$abbrev.'Skills'])?>
				</div>
<?
		if ($first) $first = FALSE;
	}
?>
			</div>
			<div class="triCol lastTriCol">
				<h2 class="headerbar hbDark">Edges &amp; Hindrances</h2>
				<div class="hbdMargined"><?=printReady($charInfo['edge_hind'])?></div>
				
				<h2 class="headerbar hbDark">Worst Nightmare</h2>
				<div class="hbdMargined"><?=printReady($charInfo['nightmare'])?></div>
				
				<h2 class="headerbar hbDark">Wounds</h2>
				<div id="woundsDiv" class="clearfix">
					<div class="indivWoundDiv">
						<div>Head</div>
						<?=$charInfo['wounds'][0]?>
					</div>
					<div class="indivWoundDiv subTwoCol">
						<div>Left Hand</div>
						<?=$charInfo['wounds'][1]?>
					</div>
					<div class="indivWoundDiv subTwoCol">
						<div>Right Hand</div>
						<?=$charInfo['wounds'][2]?>
					</div>
					<div class="indivWoundDiv">
						<div>Guts</div>
						<?=$charInfo['wounds'][3]?>
					</div>
					<div class="indivWoundDiv subTwoCol">
						<div>Left Leg</div>
						<?=$charInfo['wounds'][4]?>
					</div>
					<div class="indivWoundDiv subTwoCol">
						<div>Right Leg</div>
						<?=$charInfo['wounds'][5]?>
					</div>
				</div>
				
				<div id="windDiv">
					<div>Wind</div><?=printReady($charInfo['wind'])?>
				</div>
			</div>
		</div>
		
		<div class="clearfix">
			<div class="twoCol">
				<h2 class="headerbar hbDark">Shootin Irons & Such</h2>
				<div class="hbdMargined"><?=printReady($charInfo['weapons'])?></div>
				
				<h2 class="headerbar hbDark">Arcane Abilities</h2>
				<div class="hbdMargined"><?=printReady($charInfo['arcane'])?></div>
			</div>
			<div class="twoCol lastTwoCol">
				<h2 class="headerbar hbDark">Equipment</h2>
				<div class="hbdMargined"><?=printReady($charInfo['equipment'])?></div>
			</div>
		</div>
		
		<h2 class="headerbar hbDark">Background/Notes</h2>
		<div class="hbdMargined"><?=printReady($charInfo['notes'])?></div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>