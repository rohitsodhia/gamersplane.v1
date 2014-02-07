<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$noChar = TRUE;
	$charInfo = getCharInfo($characterID, 'deadlands');
	if ($charInfo) {
		$gameID = $charInfo['gameID'];
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) {
			$charInfo['wounds'] = explode(',', $charInfo['wounds']);
			$noChar = FALSE;
		}
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Edit Character Sheet</h1>
		<div id="charSheetLogo"><img src="<?=SITEROOT?>/images/logos/deadlands.jpg"></div>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<form method="post" action="<?=SITEROOT?>/characters/process/deadlands/">
			<input type="hidden" name="characterID" value="<?=$characterID?>">
			
			<div id="nameDiv" class="tr">
				<label class="textLabel">Name:</label>
				<div><input type="text" name="name" maxlength="50" value="<?=$charInfo['name']?>"></div>
			</div>
			
			<div class="clearfix">
				<div class="triCol">
					<h2 class="headerbar hbDark">Mental</h2>
<?
	$first = TRUE;
	$defaults = array('cog' => 'Search - 1', 'kno' => "Area Knowledge: Home County - 2\nLanguage: Native Tongue - 2");
	foreach (array('cog' => 'Cognition', 'kno' => 'Knowledge', 'mie' => 'Mien', 'sma' => 'Smarts', 'spi' => 'Spirit') as $abbrev => $label) {
?>
					<div class="hbdMargined statDiv<?=$first?' firstStatDiv':''?>">
						<div class="statDice">
							<input type="text" name="<?=$abbrev?>NumDice" maxlength="2" class="numDice" value="<?=$charInfo[$abbrev.'NumDice']?>"> d <input type="text" name="<?=$abbrev?>DieType" class="dieType" value="<?=$charInfo[$abbrev.'DieType']?>"> <?=$label?>
						</div>
						<div class="skillTitle"><?=$label?> Skills</div>
						<textarea name="<?=$abbrev?>Skills"><?=strlen($charInfo[$abbrev.'Skills'])?$charInfo[$abbrev.'Skills']:$defaults[$abbrev]?></textarea>
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
					<div class="hbdMargined statDiv<?=$first?' firstStatDiv':''?>">
						<div class="statDice">
							<input type="text" name="<?=$abbrev?>NumDice" maxlength="2" class="numDice" value="<?=$charInfo[$abbrev.'NumDice']?>"> d <input type="text" name="<?=$abbrev?>DieType" class="dieType" value="<?=$charInfo[$abbrev.'DieType']?>"> <?=$label?>
						</div>
						<div class="skillTitle"><?=$label?> Skills</div>
						<textarea name="<?=$abbrev?>Skills"><?=strlen($charInfo[$abbrev.'Skills'])?$charInfo[$abbrev.'Skills']:$defaults[$abbrev]?></textarea>
					</div>
<?
		if ($first) $first = FALSE;
	}
?>
				</div>
				<div class="triCol lastTriCol">
					<h2 class="headerbar hbDark">Edges &amp; Hindrances</h2>
					<textarea id="edge_hind" name="edge_hind" class="hbdMargined"><?=$charInfo['edge_hind']?></textarea>
					
					<h2 class="headerbar hbDark">Worst Nightmare</h2>
					<textarea id="nightmare" name="nightmare" class="hbdMargined"><?=$charInfo['nightmare']?></textarea>
					
					<h2 class="headerbar hbDark">Wounds</h2>
					<div id="woundsDiv" class="clearfix">
						<div class="indivWoundDiv">
							<div>Head</div>
							<input type="text" name="wounds[head]" maxlength="2" value="<?=$charInfo['wounds'][0]?>">
						</div>
						<div class="indivWoundDiv subTwoCol">
							<div>Left Hand</div>
							<input type="text" name="wounds[leftHand]" maxlength="2" value="<?=$charInfo['wounds'][1]?>">
						</div>
						<div class="indivWoundDiv subTwoCol">
							<div>Right Hand</div>
							<input type="text" name="wounds[rightHand]" maxlength="2" value="<?=$charInfo['wounds'][2]?>">
						</div>
						<div class="indivWoundDiv">
							<div>Guts</div>
							<input type="text" name="wounds[guts]" maxlength="2" value="<?=$charInfo['wounds'][3]?>">
						</div>
						<div class="indivWoundDiv subTwoCol">
							<div>Left Leg</div>
							<input type="text" name="wounds[leftLeg]" maxlength="2" value="<?=$charInfo['wounds'][4]?>">
						</div>
						<div class="indivWoundDiv subTwoCol">
							<div>Right Leg</div>
							<input type="text" name="wounds[rightLeg]" maxlength="2" value="<?=$charInfo['wounds'][5]?>">
						</div>
					</div>
					
					<div id="windDiv">
						<div>Wind</div>
						<input type="text" name="wind" maxlength="2" value="<?=$charInfo['wind']?>">
					</div>
				</div>
			</div>
			
			<div class="clearfix">
				<div class="twoCol">
					<h2 class="headerbar hbDark">Shootin Irons & Such</h2>
					<textarea id="weapons" name="weapons" class="hbdMargined"><?=$charInfo['weapons']?></textarea>
					
					<h2 class="headerbar hbDark">Arcane Abilities</h2>
					<textarea id="arcane" name="arcane" class="hbdMargined"><?=$charInfo['arcane']?></textarea>
				</div>
				<div class="twoCol lastTwoCol">
					<h2 class="headerbar hbDark">Equipment</h2>
					<textarea id="equipment" name="equipment" class="hbdMargined"><?=$charInfo['equipment']?></textarea>
				</div>
			</div>
			
			<h2 class="headerbar hbDark">Background/Notes</h2>
			<textarea id="notes" name="notes" class="hbdMargined"><?=$charInfo['notes']?></textarea>
			
			<div id="submitDiv"><button type="submit" name="save" class="fancyButton">Save</button></div>
		</form>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>