<?
	$characterID = intval($pathOptions[1]);
	$charInfo = $mysql->query("SELECT label, gameID, system FROM characters WHERE userID = {$currentUser->userID} AND characterID = {$characterID}");
	if ($charInfo->rowCount() == 0 || $gameID != 0) { header('Location: /403'); exit; }
	$charInfo = $charInfo->fetch();
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Delete Character</h1>
		
		<p class="alignCenter">Are you sure you wanna delete <a href="/characters/<?=$charInfo['system']?>/<?=$characterID?>/" target="_parent"><?=$charInfo['label']?></a>?</p>
		<p class="alignCenter">This cannot be reversed!</p>
		
		<form method="post" action="/characters/process/delete/" class="alignCenter">
			<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">
			<button type="submit" name="delete" class="fancyButton">Delete</button>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>