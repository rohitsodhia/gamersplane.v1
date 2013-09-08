<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT cd.*, c.userID, gms.primaryGM IS NOT NULL isGM FROM marvel_characters cd INNER JOIN characters c ON cd.characterID = c.characterID LEFT JOIN (SELECT gameID, primaryGM FROM players WHERE isGM = 1 AND userID = $userID) gms ON c.gameID = gms.gameID WHERE cd.characterID = $characterID");
	$noChar = TRUE;
	if ($charInfo->rowCount()) {
		$charInfo = $charInfo->fetch();
		$gameID = $charInfo['gameID'];
		if ($charInfo['userID'] == $userID || $charInfo['isGM']) {
			foreach ($charInfo as $key => $value) if ($value == '') $charInfo[$key] = '&nbsp;';
			$noChar = FALSE;
			includeSystemInfo('marvel');
		}
	} else $noChar = TRUE;
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Character Sheet</h1>
		<div id="charSheetLogo"><img src="<?=SITEROOT?>/images/logos/marvel.png"></div>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<div class="actions"><a id="editCharacter" href="<?=SITEROOT?>/characters/marvel/<?=$characterID?>/edit" class="button">Edit Character</a></div>
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
			<h2 class="headerbar hbDark">Actions</h2>
			<div class="hbMargined clearfix">
<?
	$count = 0;
	$actions = $mysql->query('SELECT pa.actionID, al.name, pa.level, pa.offset, pa.cost, pa.details FROM marvel_actions pa INNER JOIN marvel_actionsList al USING (actionID) WHERE characterID = '.$characterID);
	foreach ($actions as $actionInfo) {
		$count++;
?>
				<div class="action<?=$count % 3 == 0?' third':''?>">
					<div class="tr labelTR clearfix">
						<label class="level">Level</label>
						<label class="cost">Cost</label>
					</div>
					<div class="clearfix">
						<span class="actionName"><?=$actionInfo['name']?></span>
						<span class="level"><?=$actionInfo['level']?></span>
						<span class="cost"><?=$actionInfo['cost']?></span>
					</div>
					<div class="details"><?=$actionInfo['details']?></div>
				</div>
<? } ?>
			</div>
		</div>
		
		<div id="modifiers" class="clearfix">
			<h2 class="headerbar hbDark">Modifiers</h2>
			<div class="hbMargined clearfix">
<?
	$count = 0;
	$modifiers = $mysql->query('SELECT pm.modifierID, ml.name, pm.level, pm.offset, pm.cost, pm.details FROM marvel_modifiers pm INNER JOIN marvel_modifiersList ml USING (modifierID) WHERE characterID = '.$characterID);
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
						<span class="cost"><?=$modifierInfo['cost']?></span>
					</div>
					<div class="details"><?=$modifierInfo['details']?></div>
				</div>
<? } ?>
			</div>
		</div>
		
		<div id="challenges" class="clearfix">
			<h2 class="headerbar hbDark">Challenges</h2>
			<div class="hbMargined">
<?
	$challenges = $mysql->query('SELECT challengeID, challenge, stones FROM marvel_challenges WHERE characterID = '.$characterID);
	foreach ($challenges as $challengeInfo) {
?>
				<div class="challenge clearfix">
					<span class="challengeName"><?=$challengeInfo['challenge']?></span>
					<span class="challengeStones"><?=$challengeInfo['stones']?></span>
				</div>
<? } ?>
			</div>
		</div>
		
		<div id="notes">
			<h2 class="headerbar hbDark">Character Notes</h2>
			<div class="hbMargined">
<? echo printReady($charInfo['notes']); ?>
			</div>
		</div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>