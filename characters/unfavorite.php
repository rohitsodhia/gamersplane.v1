<?php
	$characterID = intval($pathOptions[1]);
	$favorited = $mongo->characterLibraryFavorites->findOne(
		[
			'characterID' => $characterID,
			'userID' => $currentUser->userID
		],
		['projection' => ['_id' => true]]
	);
	if (!$favorited) { header('Location: /403'); exit; }
	$charInfo = $mongo->characters->findOne(
		['characterID' => $characterID],
		['projection' => ['system' => true, 'label' => true]]
	);
	if (!$charInfo) { header('Location: /403'); exit; }
?>
<?php	require_once(FILEROOT . '/header.php'); ?>
		<h1 class="headerbar">Unfavorite Character</h1>

		<p class="alignCenter">Are you sure you want to unfavortite <a href="/characters/<?=$charInfo['system']?>/<?=$characterID?>/" target="_parent"><?=$charInfo['label']?></a>?</p>

		<form method="post" action="/characters/process/favorite/" class="alignCenter">
			<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">
			<button type="submit" name="unfavorite" class="fancyButton">Unfavorite</button>
		</form>
<?php	require_once(FILEROOT . '/footer.php'); ?>
