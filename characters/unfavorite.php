<?php
	$characterID = intval($pathOptions[1]);
	try {
		$charInfo = $mysql->query("SELECT characters.label, characters.system FROM characters INNER JOIN characterLibrary_favorites favorites ON characters.characterID = favorites.characterID WHERE characters.characterID = {$characterID} AND favorites.userID = {$currentUser->userID} LIMIT 1")->fetch();
	} catch (Exception $e) {
		header('Location: /403'); exit;
	}
?>
<?php	require_once(FILEROOT . '/header.php'); ?>
		<h1 class="headerbar">Unfavorite Character</h1>

		<p class="alignCenter">Are you sure you want to unfavortite <a href="/characters/<?=$charInfo['system']?>/<?=$characterID?>/" target="_parent"><?=$charInfo['label']?></a>?</p>

		<form method="post" action="/characters/process/favorite/" class="alignCenter">
			<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">
			<button type="submit" name="unfavorite" class="fancyButton">Unfavorite</button>
		</form>
<?php	require_once(FILEROOT . '/footer.php'); ?>
