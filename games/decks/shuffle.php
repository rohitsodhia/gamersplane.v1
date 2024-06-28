<?php
	$gameID = intval($pathOptions[0]);
	$deckID = intval($pathOptions[2]);
	$getDeck = $mysql->query("SELECT decks.label, decks.deck, decks.position, decks.lastShuffle FROM games INNER JOIN players ON games.gameID = players.gameID INNER JOIN decks ON games.gameID = decks.gameID WHERE games.gameID = {$gameID} AND players.userID = {$currentUser->userID} AND players.isGM = 1 AND decks.deckID = {$deckID} LIMIT 1");
	if (!$getDeck->rowCount()) { header('Location: /tools/maps'); exit; }
	$deck = $getDeck->fetch();
	$deck['deck'] = json_decode($deck['deck']);
?>
<?php	require_once(FILEROOT . '/header.php'); ?>
		<h1 class="headerbar">Shuffle Deck</h1>

<?php
	$numCardsLeft = sizeof($deck['deck']) - $deck['position'] + 1;
	$lastShuffleAgo = time() - strtotime($deck['lastShuffle']);
?>
		<form method="post" action="/games/process/decks/shuffle/" class="hbMargined">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<input type="hidden" name="deckID" value="<?=$deckID?>">
			<p class="alignCenter">There are <strong><?=$numCardsLeft?></strong> cards still left in this deck<?php if ($deck['lastShuffle']) { echo ' and it was last shuffled on <strong>' . date('F j, Y g:i:s a', strtotime($deck['lastShuffle'])) . '</strong>' . (($lastShuffleAgo < 3600) ? ', ' . intval(date('i', $lastShuffleAgo)) . ' minutes ago' : ''); } ?>.</p>
			<p class="alignCenter">Are you sure you want to shuffle <strong><?=$deck['label']?></strong>?</p>
			<div id="submitDiv" class="alignCenter"><button type="submit" name="shuffle" class="fancyButton">Shuffle Deck</button></div>
		</form>
<?php	require_once(FILEROOT . '/footer.php'); ?>
