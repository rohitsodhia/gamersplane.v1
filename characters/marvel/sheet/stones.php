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
		<form method="post" action="<?=SITEROOT?>/characters/process/marvel/edit/stones/">
			<div id="remainingStones" class="tr alignCenter">
				<label class="name">Remaining Stones:</label> <?=whiteStones($charInfo['unusedStones']).' <b>White</b> stone'.((whiteStones($charInfo['unusedStones']) == 1)?'':'s').', '.redStones($charInfo['unusedStones']).' <b class="redStones">Red</b> stone'.((redStones($charInfo['unusedStones']) == 1)?'':'s')?>
			</div>
			<div class="tr alignCenter">
				<input type="radio" name="change" value="add" checked="checked">Add <input type="radio" name="change" value="sub">Subtract <input type="text" name="white" maxlength="2" value="0" class="stones"> <b>White</b> stones <input type="text" name="red" maxlength="2" value="0" class="stones"> <b class="redStones">Red</b> stones
			</div>
			<input type="hidden" name="characterID" value="<?=$characterID?>">
			<div class="tr alignCenter">
				<button id="saveBtn" type="submit" name="save" class="btn_save"></button>
			</div>
		</form>
<? } ?>
<? require_once(FILEROOT.'/footer.php'); ?>