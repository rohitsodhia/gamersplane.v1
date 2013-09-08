<?
	$loggedIn = checkLogin();
	
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query('SELECT c.label, c.gameID, c.systemID, s.shortName FROM characters c, systems s WHERE c.systemID = s.systemID AND c.userID = '.intval($_SESSION['userID']).' AND c.characterID = '.$characterID);
	if ($charInfo->rowCount() == 0 || $gameID != 0) { header('Location: '.SITEROOT.'/403'); }
	list($label, $gameID, $systemID, $shortName) = $charInfo->fetch();
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Delete Character</h1>
		
		<p class="alignCenter">Are you sure you wanna delete <?='<a href="'.SITEROOT.'/characters/'.$shortName.'/'.$characterID.'" target="_parent">'.$label.'</a>'?>?</p>
		<p class="alignCenter">This cannot be reversed!</p>
		
		<form method="post" action="<?=SITEROOT?>/characters/process/delete/" class="buttonPanel">
			<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">
			<button type="submit" name="delete" class="btn_delete"></button>
<!--			<button type="submit" name="cancel" class="btn_cancel"></button>-->
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>