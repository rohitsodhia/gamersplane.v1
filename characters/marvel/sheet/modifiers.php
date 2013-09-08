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
		<h2>Add/Edit/Delete Modifiers</h2>
		<h2><img src="<?=SITEROOT?>/images/logos/marvel.jpg"></h2>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<form id="editModifier" method="post" action="<?=SITEROOT?>/characters/process/marvel/edit/modifiers/">
<?
	if ($_GET['modifierID']) {
		$modifierID = intval($_GET['modifierID']);
		
		$mysql->query('SELECT modifiers.modifierID, modifiers.name, modifiers.cost, modifiers.costTo, playerModifiers.level, playerModifiers.offset, playerModifiers.extraStones, playerModifiers.timesTaken, playerModifiers.details FROM marvel_modifiers modifiers LEFT JOIN marvel_playerModifiers playerModifiers ON modifiers.modifierID = playerModifiers.modifierID AND playerModifiers.characterID = '.$characterID.' WHERE modifiers.modifierID = '.$modifierID);
		$modifierInfo = $mysql->fetch();
		
//		if (isset($_SESSION['character']->modifiers[$modifierInfo['name']]))echo "\t\t\t\t<b>NOTE:</b> If you have modified <u>${modifierInfo['costTo']}</u> since the last update to this modifiers, you must change <u>${modifierInfo['costTo']}</u> back to its original level, remove this modifiers, then continue\n";
		echo "\t\t\t".'<input type="hidden" name="characterID" value="'.$characterID."\">\n";
		echo "\t\t\t".'<input type="hidden" name="modifierID" value="'.$modifierID."\">\n";
		if ($modifierInfo['cost'] < 0 && $modifierID != 44) {
			if ($modifierInfo['cost'] > -1) {
				$cost = abs($modifierInfo['cost']);
				echo "\t\t\t<div class=\"tr\">".printReady($modifierInfo['name']).': '.redStones(abs($modifierInfo['cost'])).' <b class="redStones">Red</b> stone'.((redStones(abs($modifierInfo['cost'])) == 1)?'':'s').(($modifierInfo['multipleAllowed'] != '')?' per '.$modifierInfo['multipleAllowed']:'').(($modifierID == 23)?' per level per option':'')."</div>\n";
			} else { echo "\t\t\t<div class=\"tr\">".printReady($modifierInfo['name']).': '.abs($modifierInfo['cost']).' <b>White</b> stone'.((whiteStones(abs($modifierInfo['cost'])) == 1)?'':'s').(($modifierInfo['multipleAllowed'] != '')?' per '.$modifierInfo['multipleAllowed']:'')."</div>\n"; }
			
			if ($modifierInfo['multipleAllowed'] != '') { echo "\t\t\t".'<div class="tr">Number of '.$modifierInfo['multipleAllowed'].'s: <input type="text" name="timesTaken" maxlength="2" value="'.($modifierInfo['playerModifierID']?$modifierInfo['timesTaken']:'').'"></div>'."\n"; }
			if ($modifierID == 23) {
				echo "\t\t\t".'<div class="tr">Number of levels: <input type="text" name="level" maxlength="2" value="'.($modifierInfo['playerModifierID']?$modifierInfo['level']:'').'"></div>'."\n";
				echo "\t\t\t".'<div class="tr">Number of options: <input type="text" name="timesTaken" maxlength="2" value="'.($modifierInfo['playerModifierID']?$modifierInfo['timesTaken']:'').'"></div>'."\n";
			}
		} else {
			echo "\t\t\t<div class=\"tr\">".printReady($modifierInfo['name']);
			if ($modifierInfo['costTo'] == '' && $modifierID != 25) echo ': <input id="modifierLevel" type="text" maxlength="2" name="level" value="'.($modifierInfo['playerModifierID']?$modifierInfo['level']:'').'"> (Levels)';
			elseif ($modifierInfo['costTo'] == '' && $modifierID == 25) {
				$level = $modifierInfo['playerModifierID']?$modifierInfo['level']:'';
				echo ': <select name="level">';
				for ($count = 1; $count <= 3; $count++) { echo '<option value="'.$count.'"'.(($level == $count)?' selected="selected"':'').'>+'.$count.'</option>'; }
				echo '</select> (Rank)';
			}
			echo "</div>\n";
			
			if ($modifierInfo['costTo'] != '' && is_int($modifierInfo['costTo'])) { echo 1; }
			if ($modifierInfo['costTo'] != '' && is_string($modifierInfo['costTo'])) $costTo = ucwords($modifierInfo['costTo']);
			if (!in_array($modifierID, array(25, 44))) echo "\t\t\t".'<div class="tr">Cost Level Offset: <u>'.$modifierInfo['cost'].'</u> '.($costTo?'+ '.$costTo.' ':'').'+ <input id="modifierOffset" type="text" name="offset" maxlength="2" value="'.($modifierInfo['playerModifierID']?$modifierInfo['offset']:'')."\"> (Options/Advantages/Disadvantages)</div>\n";
		}
		if ($modifierID != 44) echo "\t\t\t".'<div class="tr">Extra Options Stone Cost: <input id="modifierOptionStones" type="text" name="modifierOptionStones" maxlength="2" value="'.($modifierInfo['playerModifierID']?$modifierInfo['extraStones']:'')."\"> Stones</div>\n";
		echo "\t\t\t<label>Details (Options/Advantages/Disadvantages):</label>\n";
		echo "\t\t\t".'<textarea id name="details">'.($modifierInfo['playerModifierID']?printReady($modifierInfo['details'], array('stripslashes')):'')."</textarea>\n";
		echo "\t\t\t".'<div id="modStones" class="tr alignCenter"><input type="checkbox" name="alterStones" checked="checked"> Add/Subtract from <b>Remaining Stones</b></div>'."\n";
	}
?>

			<div class="alignCenter">
<? if ($_GET['modifierID']) { ?>
				<button id="addModifierBtn" type="submit" name="save" class="btn_addModifier"></button>
<? } ?>
<? //				<button id="startOverBtn" type="submit" name="startOver" class="btn_startOver"></button> ?>
			</div>
		</form>
		<hr>
		
		<h4>NOTICE:</h4>
		<p>Some modifiers will not show up on this list. Modifiers such as <u>Armor Penetration</u> are more like options, and are more appropriately treated as such. Modifiers like <u>Transform Self</u> are much too variable for our system at this time. At this time, the best we can offer for something like that is to include it in your player description while we work out a "forms" system.</p>
		<table id="modifierList">
<?
	$mysql->query('SELECT modifiers.modifierID, modifiers.name, IF(playerModifiers.modifierID IS NULL, 0, 1) hasModifier FROM marvel_modifiers modifiers LEFT JOIN marvel_playerModifiers playerModifiers ON modifiers.modifierID = playerModifiers.modifierID AND playerModifiers.characterID = '.$characterID.' ORDER BY modifiers.name');
	
	$odd = TRUE;
	while ($modifierInfo = $mysql->fetch()) {
		if ($odd) { echo "\t\t\t<tr>\n"; }
		echo "\t\t\t\t".'<td class="modifierName">'.printReady($modifierInfo['name'])."</td>\n";
		echo "\t\t\t\t<td class=\"modifierLinks\">\n";
		if ($modifierInfo['hasModifier'] == 0) { echo "\t\t\t\t\t".'<a href="?modifierID='.$modifierInfo['modifierID'].'">Add modifier</a>'."\n"; }
		else {
			echo "\t\t\t\t\t".'<a href="?modifierID='.$modifierInfo['modifierID']."\">Edit modifier</a>\n";
			echo "\t\t\t\t\t".'<form method="post" action="'.SITEROOT.'/characters/marvel/edit/'.$characterID.'/delete/?type=modifier">'."\n";
			echo "\t\t\t\t\t\t".'<input type="hidden" name="modifierID" value="'.$modifierInfo['modifierID'].'">'."\n";
			echo "\t\t\t\t\t\t".'<button type="submit" name="deleteModifier" class="btn_text">Delete Modifier</button>'."\n";
			echo "\t\t\t\t\t</form>\n";
		}
		echo "\t\t\t\t</td>\n";
		if (!$odd) { echo "\t\t\t</tr>\n"; $odd = TRUE; }
		else { $odd = FALSE; }
	}
?>
	
<!--		<div><a href="<?=$gamePath['Marvel Universe RPG']?>/dbAdd/addModifier.php?from=chargen">Add new modifier</a></div>-->
		</table>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>