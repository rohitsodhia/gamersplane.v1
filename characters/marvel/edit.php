<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$noChar = TRUE;
	$charInfo = getCharInfo($characterID, 'marvel');
	if ($charInfo) {
		$gameID = $charInfo['gameID'];
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) $noChar = FALSE;
		includeSystemInfo('marvel');
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Edit Character Sheet</h1>
		<div id="charSheetLogo"><img src="<?=SITEROOT?>/images/logos/marvel.png"></div>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<form method="post" action="<?=SITEROOT?>/characters/process/marvel/edit/">
			<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">
			<div class="tr basicInfo charName">
				<label class="name">Normal Name:</label>
				<input type="text" name="normName" maxlength="50" value="<?=$charInfo['normName']?>">
			</div>
			<div class="tr basicInfo charName">
				<label class="name">Super Name:</label>
				<input type="text" name="superName" maxlength="50" value="<?=$charInfo['superName']?>">
			</div>
			<div class="tr basicInfo he">
				<label class="name">Health:</label>
				<input type="text" name="health_max" maxlength="2" value="<?=$charInfo['health_max']?>" class="shortNum alignCenter">
			</div>
			<div class="tr basicInfo he">
				<label class="name">Energy:</label>
				<input type="text" name="energy_max" maxlength="2" value="<?=$charInfo['energy_max']?>" class="shortNum alignCenter">
			</div>
			
			<div id="remainingStones" class="tr basicInfo">
				<label class="name">Remaining Stones:</label>
				<input type="text" name="white" maxlength="2" value="<?=whiteStones($charInfo['unusedStones'])?>" class="stones shortNum alignCenter"> <span>White</span> stones <input type="text" id="remainingRedStones" name="red" maxlength="2" value="<?=redStones($charInfo['unusedStones'])?>" class="stones shortNum alignCenter"> <span class="redStones">Red</span> stones
			</div>
			
			<div id="stats" class="tr clearfix">
				<label class="first textLabel">Intelligence:</label>
				<input type="text" name="int" maxlength="2" value="<?=$charInfo['int']?>" class="shortNum alignCenter">
				<label class="textLabel">Strength:</label>
				<input type="text" name="str" maxlength="2" value="<?=$charInfo['str']?>" class="shortNum alignCenter">
				<label class="textLabel">Agility:</label>
				<input type="text" name="agi" maxlength="2" value="<?=$charInfo['agi']?>" class="shortNum alignCenter">
				<label class="textLabel">Speed:</label>
				<input type="text" name="spd" maxlength="2" value="<?=$charInfo['spd']?>" class="shortNum alignCenter">
				<label class="textLabel">Durability:</label>
				<input type="text" name="dur" maxlength="2" value="<?=$charInfo['dur']?>" class="last shortNum alignCenter">
			</div>
			
			<div id="actions" class="clearfix">
				<h2 class="headerbar hbDark">Actions</h2>
				<div class="hbdMargined">
					<div id="actionForm">
						<input id="actionSearch" type="text" autocomplete="off" class="placeholder" data-placeholder="Action">
						<button id="addAction" type="submit" name="addAction" class="fancyButton">Add</button>
					</div>
					<p class="note">For costs of less then one white stone, use .3 for 1 red stone and .6 for 2 red stones</p>
<?
	$count = 0;
	$actions = $mysql->query('SELECT pa.actionID, al.name, pa.level, pa.offset, pa.cost, pa.details FROM marvel_actions pa INNER JOIN marvel_actionsList al USING (actionID) WHERE characterID = '.$characterID);
	foreach ($actions as $actionInfo) actionFormFormat($actionInfo, ++$count);
?>
				</div>
			</div>
			
			<div id="modifiers" class="clearfix">
				<h2 class="headerbar hbDark">Modifiers</h2>
				<div class="hbdMargined">
					<div id="modifierForm">
						<input id="modifierSearch" type="text" autocomplete="off" class="placeholder" data-placeholder="Modifier">
						<button id="addModifier" type="submit" name="addModifier" class="fancyButton">Add</button>
					</div>
					<p class="note">For costs of less then one white stone, use .3 for 1 red stone and .6 for 2 red stones</p>
<?
	$count = 0;
	$modifiers = $mysql->query('SELECT pm.modifierID, ml.name, pm.level, pm.offset, pm.cost, pm.details FROM marvel_modifiers pm INNER JOIN marvel_modifiersList ml USING (modifierID) WHERE characterID = '.$characterID);
	foreach ($modifiers as $modifierInfo) modifierFormFormat($modifierInfo, ++$count);
?>
				</div>
			</div>
			
			<div id="challenges" class="clearfix">
				<h2 class="headerbar hbDark">Challenges</h2>
				<div class="hbdMargined">
					<div id="challengeForm">
						<div class="labelTR clearfix">
							<div class="challengeName">Challenge</div>
							<div class="challengeStones">Stones</div>
						</div>
						<div class="tr">
							<input type="text" id="challengeName" autocomplete="off" class="challengeName">
							<input type="text" id="challengeStones" autocomplete="off" class="challengeStones">
							<button id="addChallenge" type="submit" name="addChallenge" class="fancyButton">Add</button>
						</div>
					</div>
<?
	$count = 0;
	$challenges = $mysql->query('SELECT challengeID, challenge, stones FROM marvel_challenges WHERE characterID = '.$characterID);
	foreach ($challenges as $challengeInfo) challengeFormFormat($challengeInfo);
?>
				</div>
			</div>
			
			<div id="notes">
				<h2 class="headerbar hbDark">Notes</h2>
				<textarea name="notes" class="hbdMargined"><?=$challengeInfo['notes']?></textarea>
			</div>
			
			<div id="submitDiv" class="tr alignCenter">
				<button id="saveBtn" type="submit" name="save" class="fancyButton">Save</button>
			</div>
		</form>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>