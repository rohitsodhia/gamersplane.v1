<?
	require_once(FILEROOT.'/includes/systemInfo/marvel.php');
	
	$loggedIn = checkLogin();
	$noChar = FALSE;
	
	$characterID = intval($pathOptions[1]);
	$mysql->query('SELECT label FROM characters WHERE userID = '.intval($_SESSION['userID']).' AND characterID = '.$characterID);
	if ($mysql->rowCount() == 0) { $noChar = TRUE; }
	else {
		$mysql->query('SELECT * FROM marvel_characters WHERE characterID = '.$characterID);
		
		if ($mysql->rowCount()) { $charInfo = $mysql->fetch(); }
		else { $noChar = TRUE; }
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Character Editing</h1>
		<h2>Update Health/Energy</h2>
		<h2><img src="<?=SITEROOT?>/images/logos/marvel.jpg"></h2>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<form method="post" action="<?=SITEROOT?>/characters/process/marvel/edit/stats/">
			<div class="tr alignCenter"><input type="checkbox" name="alterStones" value="alter" checked="checked"> Add/Subtract from <b>Remaining Stones</b></div>
			<div id="stats" class="tr alignCenter">
				<label class="first">Intelligence:</label>
				<input type="text" name="int" maxlength="2" value="<?=$charInfo['int']?>">
				<label>Strength:</label>
				<input type="text" name="str" maxlength="2" value="<?=$charInfo['str']?>">
				<label>Agility:</label>
				<input type="text" name="agi" maxlength="2" value="<?=$charInfo['agi']?>">
				<label>Speed:</label>
				<input type="text" name="spd" maxlength="2" value="<?=$charInfo['spd']?>">
				<label>Durability:</label>
				<input type="text" name="dur" maxlength="2" value="<?=$charInfo['dur']?>" class="last">
			</div>
			<div id="specialRules">
				<h3 class="alignCenter">Special Rules:</h3>
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
			</div>
			<input type="hidden" name="characterID" value="<?=$characterID?>">
			<div id="saveDiv" class="tr alignCenter">
				<button id="saveBtn" type="submit" name="save" class="btn_save"></button>
			</div>
		</form>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>