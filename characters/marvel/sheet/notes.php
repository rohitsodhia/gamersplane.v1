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
		<h2>Update Character Notes</h2>
		<h2><img src="<?=SITEROOT?>/images/logos/marvel.jpg"></h2>
		
<? if ($noChar) { ?>
		<h2 id="noCharFound">No Character Found</h2>
<? } else { ?>
		<form method="post" action="<?=SITEROOT?>/characters/process/marvel/edit/notes/">
			<input type="hidden" name="characterID" value="<?=$characterID?>">
			<textarea name="notes"><?=printReady($charInfo['notes'], array('stripslashes'))?></textarea>
			<div id="saveDiv" class="tr alignCenter">
				<button id="saveBtn" type="submit" name="save" class="btn_save"></button>
			</div>
		</form>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>