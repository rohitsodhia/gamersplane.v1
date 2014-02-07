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
		<form method="post" action="<?=SITEROOT?>/characters/process/marvel/edit/he/">
			<div class="tr alignCenter charName">
				<label class="name">Normal Name:</label> <input type="text" name="normName" maxlength="50" value="<?=sanitizeString($charInfo['normName'])?>">
			</div>
			<div class="tr alignCenter charName">
				<label class="name">Super Name:</label> <input type="text" name="superName" maxlength="50" value="<?=sanitizeString($charInfo['superName'])?>">
			</div>
			<div class="tr alignCenter he">
				<label class="name">Calculated Health/Current Max Health:</label> <?=$charInfo['dur'].' / <input type="text" name="maxHealth" maxlength="2" value="'.$charInfo['health_max']."\">
"?>
			</div>
			<div class="tr alignCenter he">
				<label class="name">Calculated Energy/Current Max Energy:</label> <?=($charInfo['dur'] * 3).' / <input type="text" name="maxEnergy" maxlength="2" value="'.$charInfo['energy_max']."\">
"?>
			</div>
			<input type="hidden" name="characterID" value="<?=$characterID?>">
			<div class="tr alignCenter">
				<button id="saveBtn" type="submit" name="save" class="btn_save"></button>
			</div>
		</form>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>