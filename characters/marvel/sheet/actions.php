<?
	require_once(FILEROOT.'/includes/systemInfo/marvel.php');
	
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$noChar = TRUE;
	
	$mysql->query('SELECT label FROM characters WHERE userID = '.$userID.' AND characterID = '.$characterID);
	if ($mysql->rowCount()) {
		$mysql->query('SELECT * FROM marvel_characters WHERE characterID = '.$characterID);
		$noChar = FALSE;
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Character Editing</h1>
		<h2>Add/Edit/Delete Actions</h2>
		<h2><img src="<?=SITEROOT?>/images/logos/marvel.jpg"></h2>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else {
	if ($_GET['actionID']) {
		$actionID = intval($_GET['actionID']);
		
		$mysql->query('SELECT actions.actionID, actions.name, actions.cost, playerActions.level, playerActions.offset, playerActions.details FROM marvel_actions actions LEFT JOIN marvel_playerActions playerActions ON actions.actionID = playerActions.actionID AND playerActions.characterID = '.$characterID.' WHERE actions.actionID = '.$actionID);
		$actionInfo = $mysql->fetch();
?>
		<form id="editAction" method="post" action="<?=SITEROOT?>/characters/process/marvel/edit/actions/">
			<input type="hidden" name="characterID" value="<?=$characterID?>">
			<input type="hidden" name="actionID" value="<?=$actionID?>">
<?		if ($actionInfo['cost'] < 0) { ?>
			<div class="tr"><?=printReady($actionInfo['name'])?>: <?=abs(intval($actionInfo['cost']))?> <b>White</b> stones</div>
<?		} else { ?>
			<div class="tr"><?=$actionInfo['name']?>: <input id="actionLevel" type="text" name="level" maxlength="2" value="<?=$actionInfo['level']?$actionInfo['level']:''?>"> (Levels)</div>
			<div class="tr">Cost Level: <u><?=$actionInfo['cost']?></u> + <input id="actionOffset" type="text" maxlength="2" name="offset" value="<?=$actionInfo['offset']?$actionInfo['offset']:''?>"> (Options/Advantages/Disadvantages)</div>
<?		} ?>
			<label>Details (Options/Advantages/Disadvantages/Specialties):</label>
			<textarea name="details"><?=$actionInfo['details']?printReady($actionInfo['details'], array('stripslashes')):''?></textarea>

			<div class="tr alignCenter"><input type="checkbox" name="alterStones" checked="checked"> Add/Subtract from <b>Remaining Stones</b></div>
			<div class="tr alignCenter">
				<button id="addActionBtn" type="submit" name="save" class="btn_addAction"></button>
			</div>
		</form>
		<hr>

<?	} ?>
			
		<table id="actionsList">
<?
	$mysql->query('SELECT actions.actionID, actions.name, IF(playerActions.actionID IS NULL, 0, 1) hasAction FROM marvel_actions actions LEFT JOIN marvel_playerActions playerActions ON actions.actionID = playerActions.actionID AND playerActions.characterID = '.$characterID.' WHERE actions.name != "Phoenix Force, Full" ORDER BY actions.name');
	
	$odd = TRUE;
	while ($actionInfo = $mysql->fetch()) {
		if ($odd) { echo "\t\t\t<tr>\n"; }
		echo "\t\t\t\t".'<td class="actionName">'.printReady($actionInfo['name'])."</td>\n";
		echo "\t\t\t\t<td class=\"actionLinks\">\n";
		if ($actionInfo['hasAction'] == 0) echo "\t\t\t\t\t".'<a href="?actionID='.$actionInfo['actionID'].'">Add action</a>'."\n";
		else {
			echo "\t\t\t\t\t".'<a href="?actionID='.$actionInfo['actionID']."\">Edit action</a>\n";
			echo "\t\t\t\t\t".'<form method="post" action="'.SITEROOT.'/characters/marvel/edit/'.$characterID.'/delete/?type=action">'."\n";
			echo "\t\t\t\t\t\t".'<input type="hidden" name="actionID" value="'.$actionInfo['actionID'].'">'."\n";
			echo "\t\t\t\t\t\t".'<button type="submit" name="deleteAction" class="btn_text">Delete Action</button>'."\n";
			echo "\t\t\t\t\t</form>\n";
		}
		echo "\t\t\t\t</td>\n";
		if (!$odd) { echo "\t\t\t</tr>\n"; $odd = TRUE; }
		else { $odd = FALSE; }
	}
?>

<!--			<tr><td colspan="2"><a href="<?=$SITEROOT?>/dbAdd/addAction.php?from=chargen">Add new action</a></td></tr>-->
		</table>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>