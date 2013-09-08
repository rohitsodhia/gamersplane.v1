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
		<h2>Add/Edit/Delete Challenges</h2>
		<h2><img src="<?=SITEROOT?>/images/logos/marvel.jpg"></h2>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<form id="challenges" method="post" action="<?=SITEROOT?>/characters/process/marvel/edit/challenges/">
			<input type="hidden" name="characterID" value="<?=$characterID?>">
<?
	$mysql->query('SELECT challenges.challengeID, challenges.desc, challenges.minStones, challenges.maxStones, playerChallenges.stones FROM marvel_challenges challenges LEFT JOIN marvel_playerChallenges playerChallenges ON challenges.challengeID = playerChallenges.challengeID AND playerChallenges.characterID = '.$characterID);
?>
			<table>
				<tr>
					<th class="selectChallenge"></th>
					<th class="challengeDescription">Description</th>
					<th class="stonesText">Stones</th>
					<th class="stonesInput"></th>
				</tr>
<?
	while ($challengeInfo = $mysql->fetch()) {
		echo "\t\t\t\t<tr>\n";
		echo "\t\t\t\t\t".'<td class="selectChallenge"><input id="challengeID_'.$challengeInfo['challengeID'].'" type="checkbox" name="challengeID_'.$challengeInfo['challengeID'].($challengeInfo['stones']?'_added':'').'"'.($challengeInfo['stones']?' checked="checked"':'').'></td>'."\n";
		echo "\t\t\t\t\t".'<td class="challengeDescription"><label for="'.$challengeInfo['challengeID'].'">'.printReady($challengeInfo['desc'])."</label></div>\n";
		echo "\t\t\t\t\t".'<td class="stonesText">'.(!$challengeInfo['maxStones']?$challengeInfo['minStones']:$challengeInfo['minStones'].'-'.$challengeInfo['maxStones'])."</td>\n";
		if (!$challengeInfo['maxStones']) echo "\t\t\t\t\t".'<td class="stonesInput"><input type="hidden" name="challengeStones_'.$challengeInfo['challengeID'].($challengeInfo['stones']?'_added':'').'" value="'.$challengeInfo['minStones']."\"></td>\n";
		else echo "\t\t\t\t\t".'<td  class="stonesInput"><input type="text" name="challengeStones_'.$challengeInfo['challengeID'].($challengeInfo['stones']?'_added" maxlength="2" value="'.$challengeInfo['stones'].'"':'"')."></td>\n";
		echo "\t\t\t\t</tr>\n";
	}
?>
			</table>
<!--		<div><a href="<?=$gamePath["Marvel Universe RPG"]?>/dbAdd/addChallenge.php?from=chargen">Add new challenge</a></div>-->

			<div class="alignCenter">
				<button id="setChallengesBtn" type="submit" name="save" class="btn_setChallenges"></button>
<? //				<button id="startOverBtn" type="submit" name="startOver" class="btn_startOver"></button> ?>
			</div>
		</form>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>