<?php
	$gameID = intval($pathOptions[0]);
	$deckID = intval($pathOptions[2]);
	$gmCheck = $mongo->games->findOne(
		[
			'gameID' => $gameID,
			'players' => ['$elemMatch' => [
				'user.userID' => $currentUser->userID,
				'isGM' => true
			]]
		],
		['projection' => ['decks' => true]]
	);
	if (!$gmCheck) { header('Location: /tools/maps'); exit; }
?>
<?php	require_once(FILEROOT . '/header.php'); ?>
		<h1 class="headerbar">Shuffle Deck</h1>

<?php
	$deck = [];
	foreach ($gmCheck['decks'] as $iDeck) {
		if ($iDeck['deckID'] == $deckID) {
			$deck = $iDeck;
			break;
		}
	}
	$numCardsLeft = sizeof($deck['deck']) - $deck['position'] + 1;
	$lastShuffleAgo = time() - $deck['lastShuffle']->sec;
?>
		<form method="post" action="/games/process/decks/shuffle/" class="hbMargined">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<input type="hidden" name="deckID" value="<?=$deckID?>">
			<p class="alignCenter">There are <strong><?=$numCardsLeft?></strong> cards still left in this deck<?php if ($deck['lastShuffle']->sec != 0) { echo ' and it was last shuffled on <strong>' . date('F j, Y g:i:s a', getMongoSeconds($deck['lastShuffle'])) . '</strong>' . (($lastShuffleAgo < 3600) ? ', ' . intval(date('i', $lastShuffleAgo)) . ' minutes ago' : ''); } ?>.</p>
			<p class="alignCenter">Are you sure you want to shuffle <strong><?=$deck['label']?></strong>?</p>
			<div id="submitDiv" class="alignCenter"><button type="submit" name="shuffle" class="fancyButton">Shuffle Deck</button></div>
		</form>
<?php	require_once(FILEROOT . '/footer.php'); ?>
