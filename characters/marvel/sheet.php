<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$noChar = TRUE;
	$system = 'marvel';
	$charInfo = getCharInfo($characterID, $system);
	if ($charInfo) {
		if ($viewerStatus = allowCharView($characterID, $userID)) {
			$noChar = FALSE;
			includeSystemInfo($system);

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
				<a id="editCharacter" href="/characters/<?=$system?>/<?=$characterID?>/edit" class="sprite pencil"></a>
<?		} else { ?>
				<a href="/" class="favoriteChar sprite tassel off" title="Favorite" alt="Favorite"></a>
<?		} ?>
			</div>
			<div class="wing ulWing"></div>
			<div class="wing urWing"></div>
		</div></div>
<? } ?>
		<div id="charSheetLogo"><img src="/images/logos/<?=$system?>.png"></div>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">

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
			<div class="hbdMargined clearfix">
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
			<div class="hbdMargined clearfix">
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
			<div class="hbdMargined">
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
			<div class="hbdMargined">
<? echo printReady($charInfo['notes']); ?>
			</div>
		</div>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>