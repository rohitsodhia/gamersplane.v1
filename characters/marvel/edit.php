<?
	require_once(FILEROOT.'/includes/systemInfo/marvel.php');
	
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$noChar = TRUE;
	
	$charInfo = $mysql->query("SELECT marvel.*, characters.gameID, characters.userID, gms.gameID IS NOT NULL isGM FROM marvel_characters marvel INNER JOIN characters ON marvel.characterID = characters.characterID LEFT JOIN (SELECT gameID, `primary` FROM gms WHERE userID = $userID) gms ON characters.gameID = gms.gameID WHERE marvel.characterID = $characterID");
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		$gameID = $charInfo['gameID'];
		if ($gameID) $fixedMenu = TRUE;
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) {
			$numVals = array('health_max', 'energy_max', 'int', 'str', 'agi', 'spd', 'dur');
			$textVals = array('normName', 'superName', 'notes');
			foreach ($_POST as $key => $value) {
				if (in_array($key, $textVals)) $updates[$key] = sanatizeString($value);
				elseif (in_array($key, $numVals)) {
					$updates[$key] = number_format(floatval($value), 1);
					if ($updates[$key] == intval($value)) $updates[$key] = intval($value);
				}
			}
			$noChar = FALSE;
			$fixedMenu = TRUE;
		}
	} else $noChar = TRUE;
?>
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
				<input type="text" name="normName" maxlength="50" value="<?=sanatizeString($charInfo['normName'])?>">
			</div>
			<div class="tr basicInfo charName">
				<label class="name">Super Name:</label>
				<input type="text" name="superName" maxlength="50" value="<?=sanatizeString($charInfo['superName'])?>">
			</div>
			<div class="tr basicInfo he">
				<label class="name">Health:</label>
				<div><input type="text" name="health_max" maxlength="2" value="<?=$charInfo['health_max']?>" class="shortNum alignCenter"></div>
			</div>
			<div class="tr basicInfo he">
				<label class="name">Energy:</label>
				<div><input type="text" name="energy_max" maxlength="2" value="<?=$charInfo['energy_max']?>" class="shortNum alignCenter"></div>
			</div>
			
			<div id="remainingStones" class="tr basicInfo">
				<label class="name">Remaining Stones:</label>
				<div><input type="text" name="white" maxlength="2" value="<?=whiteStones($charInfo['unusedStones'])?>" class="stones shortNum alignCenter"> <span>White</span> stones <input type="text" name="red" maxlength="2" value="<?=redStones($charInfo['unusedStones'])?>" class="stones shortNum alignCenter"> <span class="redStones">Red</span> stones</div>
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
				<h3 class="textLabel">Actions</h3>
				<div id="actionForm">
					<input type="text" value="Action" autocomplete="off" class="placeholderText">
					<button id="addAction" type="submit" name="addAction" class="btn_add"></button>
				</div>
				<p class="note">For costs of less then one white stone, use .3 for 1 red stone and .6 for 2 red stones</p>
<?
	$count = 0;
	$actions = $mysql->query('SELECT pa.actionID, al.name, pa.level, pa.offset, pa.stonesSpent, pa.details FROM marvel_actions pa INNER JOIN marvel_actionsList al USING (actionID) WHERE characterID = '.$characterID);
	foreach ($actions as $actionInfo) {
		$count++;
?>
				<div class="action<?=$count % 3 == 0?' third':''?>">
					<div class="tr labelTR">
						<label class="level">Level</label>
						<label class="cost">Cost</label>
					</div>
					<div class="clearfix">
						<span class="actionName"><?=$actionInfo['name']?></span>
						<input type="text" name="action[<?=$actionInfo['actionID']?>][cost]" value="<?=$actionInfo['stonesSpent']?>" class="cost">
						<input type="text" name="action[<?=$actionInfo['actionID']?>][level]" value="<?=$actionInfo['level']?>" class="level">
					</div>
					<textarea name="action[<?=$actionInfo['actionID']?>][details]"><?=$actionInfo['details']?></textarea>
				</div>
<? } ?>
			</div>
			
			<div id="modifiers" class="clearfix">
				<h3 class="textLabel">Modifiers</h3>
				<div id="modifierForm">
					<input type="text" value="Modifier" autocomplete="off" class="placeholderText">
					<button id="addModifier" type="submit" name="addModifier" class="btn_add"></button>
				</div>
				<p class="note">For costs of less then one white stone, use .3 for 1 red stone and .6 for 2 red stones</p>
<?
	$count = 0;
	$modifiers = $mysql->query('SELECT pm.modifierID, ml.name, pm.level, pm.offset, pm.stonesSpent, pm.details FROM marvel_modifiers pm INNER JOIN marvel_modifiersList ml USING (modifierID) WHERE characterID = '.$characterID);
	foreach ($modifiers as $modifierInfo) {
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
			
			<div id="challenges" class="clearfix">
				<h3 class="textLabel">Challenges</h3>
				<div id="challengeForm">
					<div class="labelTR clearfix">
						<div class="challengeName">Challenge</div>
						<div class="challengeStones">Stones</div>
					</div>
					<div class="tr">
						<input type="text" id="challengeName" autocomplete="off" class="challengeName">
						<input type="text" id="challengeStones" autocomplete="off" class="challengeStones">
						<button id="addChallenge" type="submit" name="addChallenge" class="btn_add"></button>
					</div>
				</div>
<?
	$count = 0;
	$challenges = $mysql->query('SELECT challengeID, challenge, stones FROM marvel_challenges WHERE characterID = '.$characterID);
	foreach ($challenges as $challengeInfo) {
?>
				<div class="challenge clearfix">
					<span class="challengeName"><?=$challengeInfo['challenge']?></span>
					<input type="text" name="challenge[<?=$challengeInfo['challengeID']?>]" value="<?=$challengeInfo['stones']?>" class="challengeStones">
				</div>
<? } ?>
			</div>
			
			<div id="notes">
				<h3 class="textLabel">Notes</h3>
				<textarea name="notes"><?=$challengeInfo['notes']?></textarea>
			</div>
			
			<div class="tr buttonPanel">
				<button id="saveBtn" type="submit" name="save" class="btn_save"></button>
			</div>
		</form>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>