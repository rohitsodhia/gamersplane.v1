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
/*			$numVals = array('size', 'str', 'dex', 'con', 'int', 'wis', 'cha', 'fort_base', 'fort_misc', 'ref_base', 'ref_misc', 'will_base', 'will_misc', 'vitality', 'wounds', 'speed', 'ac_armor', 'ac_dex', 'ac_size', 'ac_misc', 'initiative_misc', 'bab', 'melee_misc', 'ranged_misc', 'actionDie_total', 'inspiration_misc', 'education_misc');
			$textVals = array('superName', 'normName', 'class', 'department', 'actionDie_dieType', 'items', 'notes');
			foreach ($charInfo as $key => $value) {
				if ($key == 'rules') $charInfo['rules'] = explode(';', $charInfo['rules']);
				elseif (in_array($key, $textVals)) $charInfo[$key] = strlen($value)?printReady($value):'&nbsp';
				elseif (in_array($key, $numVals)) $charInfo[$key] = intval($value);
			}*/
			$noChar = FALSE;
		}
	} else $noChar = TRUE;
	
	$charSheetURL = SITEROOT.'/characters/marvel/edit/'.$characterID;
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Character Sheet</h1>
		<h2><img src="<?=SITEROOT?>/images/logos/marvel.jpg"></h2>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<div id="editCharLink"><a href="<?=SITEROOT?>/characters/marvel/<?=$characterID?>/edit">Edit Character</a></div>
		<div class="tr basicInfo">
			<label class="name">Secret Identity:</label>
			<div><?=$charInfo['normName'] != ''?printReady($charInfo['normName']):'&nbsp;'?></div>
		</div>
		<div class="tr basicInfo">
			<label class="name">Super Name:</label>
			<div><?=$charInfo['superName'] != ''?printReady($charInfo['superName']):'&nbsp;'?></div>
		</div>
		<div class="tr basicInfo">
			<label class="name">Health:</label>
			<div><?=$charInfo['health_max']?></div>
		</div>
		<div class="tr basicInfo">
			<label class="name">Energy:</label>
			<div><?=$charInfo['energy_max']?></div>
		</div>
		<div class="tr basicInfo">
			<label class="name">Remaining Stones:</label>
			<div><?=whiteStones($charInfo['unusedStones']).' <b>White</b> stone'.((whiteStones($charInfo['unusedStones']) == 1)?'':'s').', '.redStones($charInfo['unusedStones']).' <b class="redStones">Red</b> stone'.((redStones($charInfo['unusedStones']) == 1)?'':'s')?></div>
		</div>
		<div id="stats" class="tr basicInfo clearfix">
			<label class="first">Intelligence:</label>
			<div><?=$charInfo['int']?></div>
			<label>Strength:</label>
			<div><?=$charInfo['str']?></div>
			<label>Agility:</label>
			<div><?=$charInfo['agi']?></div>
			<label>Speed:</label>
			<div><?=$charInfo['spd']?></div>
			<label>Durability:</label>
			<div><?=$charInfo['dur']?></div>
		</div>
		
		<div id="actions" class="clearfix">
			<h3>Actions</h3>
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
					<span class="level"><?=$actionInfo['level']?></span>
					<span class="cost"><?=$actionInfo['stonesSpent']?></span>
				</div>
				<div class="details"><?=$actionInfo['details']?></div>
			</div>
<? } ?>
		</div>
		
		<div id="modifiers" class="clearfix">
			<h3>Modifiers</h3>
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
					<span class="level"><?=$modifierInfo['level']?></span>
					<span class="cost"><?=$modifierInfo['stonesSpent']?></span>
				</div>
				<div class="details"><?=$modifierInfo['details']?></div>
			</div>
<? } ?>
		</div>
		
		<div id="challenges" class="clearfix">
			<h3>Challenges</h3>
<?
	$count = 0;
	$challenges = $mysql->query('SELECT challengeID, challenge, stones FROM marvel_challenges WHERE characterID = '.$characterID);
	foreach ($challenges as $challengeInfo) {
?>
			<div class="challenge clearfix">
				<span class="challengeName"><?=$challengeInfo['challenge']?></span>
				<span class="challengeStones"><?=$challengeInfo['stones']?></span>
			</div>
<? } ?>
		</div>
		
		<div id="notes">
			<h3>Character Notes</h3>
<? echo printReady($charInfo['notes']); ?>
		</div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>