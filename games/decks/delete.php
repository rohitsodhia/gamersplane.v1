<?php
	$gameID = intval($pathOptions[0]);
	$deckID = intval($pathOptions[2]);
	$getDeck = $mysql->query("SELECT decks.label FROM games INNER JOIN players ON games.gameID = players.gameID INNER JOIN decks ON games.gameID = decks.gameID WHERE games.gameID = {$gameID} AND players.userID = {$currentUser->userID} AND players.isGM = 1 AND decks.deckID = {$deckID} LIMIT 1");
	if (!$getDeck->rowCount()) { header('Location: /tools/decks/'); exit; }
	$label = $getDeck->fetchColumn();
?>
<?php	require_once(FILEROOT . '/header.php'); ?>
		<h1 class="headerbar">Delete Deck</h1>

		<form method="post" action="/games/process/decks/delete/" class="hbMargined">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<input type="hidden" name="deckID" value="<?=$deckID?>">

			<p class="alignCenter">Are you sure you want to delete the deck <strong><?=$label?></strong>?</p>
			<div class="alignCenter"><button type="submit" name="delete" class="fancyButton">Delete</button></div>
		</form>
<?php	require_once(FILEROOT . '/footer.php'); ?>
