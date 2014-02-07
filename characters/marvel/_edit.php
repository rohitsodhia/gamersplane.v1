<?
	require_once(FILEROOT.'/includes/systemInfo/marvel.php');
	
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$noChar = TRUE;
	
	$mysql->query("SELECT marvel.*, characters.gameID, characters.userID, gms.gameID IS NOT NULL isGM FROM marvel_characters marvel INNER JOIN characters ON marvel.characterID = characters.characterID LEFT JOIN (SELECT gameID, `primary` FROM gms WHERE userID = $userID) gms ON characters.gameID = gms.gameID WHERE marvel.characterID = $characterID");
	if ($mysql->rowCount()) {
		$charInfo = $mysql->fetch();
		$gameID = $charInfo['gameID'];
		if ($gameID) $fixedMenu = TRUE;
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) {
/*			$numVals = array('size', 'str', 'dex', 'con', 'int', 'wis', 'cha', 'fort_base', 'fort_misc', 'ref_base', 'ref_misc', 'will_base', 'will_misc', 'vitality', 'wounds', 'speed', 'ac_armor', 'ac_dex', 'ac_size', 'ac_misc', 'initiative_misc', 'bab', 'melee_misc', 'ranged_misc', 'actionDie_total', 'inspiration_misc', 'education_misc');
			$textVals = array('superName', 'normName', 'class', 'department', 'actionDie_dieType', 'items', 'notes');
			foreach ($charInfo as $key => $value) {
				if (in_array($key, $textVals)) $charInfo[$key] = strlen($value)?printReady($value):'&nbsp';
				elseif (in_array($key, $numVals)) $charInfo[$key] = intval($value);
			}*/
			$noChar = FALSE;
			$fixedMenu = TRUE;
		}
	} else $noChar = TRUE;
?>
<!DOCTYPE html>
<html>
<head>
<? require_once(FILEROOT.'/meta.php'); ?>

<? require_once(FILEROOT.'/styles/styles.php'); ?>

<? require_once(FILEROOT.'/javascript/js.php'); ?>
</head>

<body>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Edit Character Sheet</h1>
		<h2><img src="<?=SITEROOT?>/images/logos/marvel.jpg"></h2>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<form method="post" action="<?=SITEROOT?>/characters/process/marvel/edit/">
			<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">
			<div class="tr basicInfo charName">
				<label class="name">Normal Name:</label>
				<input type="text" name="normName" maxlength="50" value="<?=sanitizeString($charInfo['normName'])?>">
			</div>
			<div class="tr basicInfo charName">
				<label class="name">Super Name:</label>
				<input type="text" name="superName" maxlength="50" value="<?=sanitizeString($charInfo['superName'])?>">
			</div>
			<div class="tr basicInfo he">
				<label class="name">Calculated Health:</label>
				<div><input type="text" name="health_max" maxlength="2" value="<?=$charInfo['health_max']?>" class="shortNum alignCenter"> (<?=$charInfo['dur']?> calculated)</div>
			</div>
			<div class="tr basicInfo he">
				<label class="name">Calculated Energy:</label>
				<div><input type="text" name="energy_max" maxlength="2" value="<?=$charInfo['energy_max']?>" class="shortNum alignCenter"> (<?=($charInfo['dur'] * 3)?> calculated)</div>
			</div>
			
			<div id="remainingStones" class="tr basicInfo">
				<label class="name">Remaining Stones:</label>
				<div><?=whiteStones($charInfo['unusedStones']).' <span>White</span> stone'.((whiteStones($charInfo['unusedStones']) == 1)?'':'s').', '.redStones($charInfo['unusedStones']).' <span class="redStones">Red</span> stone'.((redStones($charInfo['unusedStones']) == 1)?'':'s')?></div>
				<div id="modNumStones">
					<input type="radio" name="change" value="add" checked="checked">Add <input type="radio" name="change" value="sub">Subtract <input type="text" name="white" maxlength="2" value="0" class="stones shortNum alignCenter"> <span>White</span> stones <input type="text" name="red" maxlength="2" value="0" class="stones shortNum alignCenter"> <span class="redStones">Red</span> stones
					<button type="submit" name="addStones" class="btn_add"></button>
				</div>
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
			<div id="statCosts" class="tr clearfix">
				<label class="first textLabel">Stones Spent:</label>
				<input type="text" name="intCost" maxlength="2" value="<?=$charInfo['intCost']?>" class="shortNum alignCenter">
				<label class="textLabel">&nbsp;</label>
				<input type="text" name="strCost" maxlength="2" value="<?=$charInfo['strCost']?>" class="shortNum alignCenter">
				<label class="textLabel">&nbsp;</label>
				<input type="text" name="agiCost" maxlength="2" value="<?=$charInfo['agiCost']?>" class="shortNum alignCenter">
				<label class="textLabel">&nbsp;</label>
				<input type="text" name="spdCost" maxlength="2" value="<?=$charInfo['spdCost']?>" class="shortNum alignCenter">
				<label class="textLabel">&nbsp;</label>
				<input type="text" name="durCost" maxlength="2" value="<?=$charInfo['durCost']?>" class="last shortNum alignCenter">
			</div>
<!--			<div id="specialRules" class="basicInfo">
				<label>Special Rules:</label>
				<div class="tr">
					<input type="checkbox" name="rule1"<?=$charInfo['rule1']?' checked="checked"':''?>>
					<div><u>Special Intelligence-Energy Rule:</u> In return for a double cost of Intelligence, your Energy pool is equal to twice your Intelligence and you regain red stones equal to your Intelligence rather then your Durability.</div>
				</div>
				<div class="tr">
					<input type="checkbox" name="rule2"<?=$charInfo['rule2']?' checked="checked"':''?>>
					<div><u>Stat Equality Rule:</u> In exchange for the cost of Durability being double instead of Triple, your Energy pool will be calculated by twice your Durability plus your next highest stat.</div>
				</div>
				<div class="tr">
					<input type="checkbox" name="rule3"<?=$charInfo['rule3']?' checked="checked"':''?>>
					<div><u>Stat Overpower Limit:</u> Stats above 3 (except Int) will be at +1 levels, <i>ex.</i> a stat at 4 will be charged as if 5 (3 stones instead of 2).</div>
				</div>
				<div id="specialNote"><b>Special Note:</b> If you apply the Special Intelligence-Energy Rule and Stat Equality Rule at the same time, your Durability and Intelligence will both cost double, and your Energy pool will be calculated by twice your Intelligence plus your next highest stat.</div>
			</div>-->
			
			<div id="actions" class="clearfix">
				<h3 class="textLabel">Actions</h3>
				<div id="actionForm">
					<input type="text" name="action" value="Action" autocomplete="off" class="placeholderText">
					<button id="addAction" type="submit" name="addAction" class="btn_add"></button>
				</div>
				<p class="note">For costs of less then one white stone, use .3 for 1 red stone and .6 for 2 red stones</p>
<?
	$count = 0;
	$mysql->query('SELECT pa.actionID, al.name, pa.level, pa.offset, pa.stonesSpent, pa.details FROM marvel_actions pa INNER JOIN marvel_actionsList al USING (actionID) WHERE characterID = '.$characterID);
	while ($actionInfo = $mysql->fetch()) {
		$count++;
?>
				<div class="action<?=$count % 3 == 0?' third':''?>">
					<div class="tr labelTR">
						<label class="level">Level</label>
						<label class="offset">Level Offset</label>
						<label class="cost">Cost</label>
					</div>
					<div class="clearfix">
						<span class="actionName"><?=$actionInfo['name']?></span>
						<input type="text" name="action[<?=$actionInfo['actionID']?>][cost]" value="<?=$actionInfo['stonesSpent']?>" class="cost">
						<input type="text" name="action[<?=$actionInfo['actionID']?>][offset]" value="<?=$actionInfo['offset']?>" class="offset">
						<input type="text" name="action[<?=$actionInfo['actionID']?>][level]" value="<?=$actionInfo['level']?>" class="level">
					</div>
					<textarea name="action[<?=$actionInfo['actionID']?>][details]"><?=$actionInfo['details']?></textarea>
				</div>
<? } ?>
			</div>
			
			<div id="modifiers" class="clearfix">
				<h3 class="textLabel">Modifiers</h3>
				<div id="modifierForm">
					<input type="text" name="modifier" value="Modifier" autocomplete="off" class="placeholderText">
					<button id="addModifier" type="submit" name="addModifier" class="btn_add"></button>
				</div>
				<p class="note">For costs of less then one white stone, use .3 for 1 red stone and .6 for 2 red stones</p>
<?
	$count = 0;
	$mysql->query('SELECT pm.modifierID, ml.name, pm.level, pm.offset, pm.stonesSpent, pm.details FROM marvel_modifiers pm INNER JOIN marvel_modifiersList ml USING (modifierID) WHERE characterID = '.$characterID);
	while ($modifierInfo = $mysql->fetch()) {
		$count++;
?>
				<div class="modifier<?=$count % 3 == 0?' third':''?>">
					<div class="tr labelTR">
						<label class="level">Level</label>
						<label class="cost">Cost</label>
					</div>
					<div class="clearfix">
						<span class="modifierName"><?=$modifierInfo['name']?></span>
						<input type="text" name="modifier[<?=$modifierInfo['modifierID']?>][cost]" value="<?=$modifierInfo['stonesSpent']?>" class="cost">
						<input type="text" name="modifier[<?=$modifierInfo['modifierID']?>][level]" value="<?=$modifierInfo['level']?>" class="level">
					</div>
					<textarea name="modifier[<?=$modifierInfo['modifierID']?>][details]"><?=$modifierInfo['details']?></textarea>
				</div>
<? } ?>
			</div>
			
			<div class="tr alignCenter">
				<button id="saveBtn" type="submit" name="save" class="btn_save"></button>
			</div>
		</form>
<? } ?>
			
			<div id="challenges" class="clearfix">
				<h3 class="textLabel">Modifiers</h3>
				<div id="challengeForm">
					<input type="text" name="challenge" value="Modifier" autocomplete="off" class="placeholderText">
					<button id="addModifier" type="submit" name="addModifier" class="btn_add"></button>
				</div>
				<p class="note">For costs of less then one white stone, use .3 for 1 red stone and .6 for 2 red stones</p>
<?
	$count = 0;
	$mysql->query('SELECT pc.challengeID, ml.name, pc.stones FROM marvel_challenges pc INNER JOIN marvel_challengesList ml USING (challengeID) WHERE characterID = '.$characterID);
	while ($challengeInfo = $mysql->fetch()) {
		$count++;
?>
				<div class="challenge">
					<div class="tr labelTR">
						<label class="cost">Cost</label>
					</div>
					<div class="clearfix">
						<span class="challengeName"><?=$challengeInfo['name']?></span>
						<input type="text" name="challenge[<?=$challengeInfo['challengeID']?>][level]" value="<?=$challengeInfo['level']?>" class="level">
					</div>
				</div>
<? } ?>
			</div>
			
			<div class="tr alignCenter">
				<button id="saveBtn" type="submit" name="save" class="btn_save"></button>
			</div>
		</form>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>