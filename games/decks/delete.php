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
	if (!$gmCheck) { header('Location: /tools/decks/'); exit; }
?>
<?php	require_once(FILEROOT . '/header.php'); ?>
		<h1 class="headerbar">Delete Deck</h1>

<?php
	$deck = array();
	foreach ($gmCheck['decks'] as $iDeck) {
		if ($iDeck['deckID'] == $deckID) {
			$deck = $iDeck;
			break;
		}
	}
?>
		<form method="post" action="/games/process/decks/delete/" class="hbMargined">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<input type="hidden" name="deckID" value="<?=$deckID?>">

			<p class="alignCenter">Are you sure you want to delete the deck <strong><?=$deck['label']?></strong>?</p>
			<div class="alignCenter"><button type="submit" name="delete" class="fancyButton">Delete</button></div>
		</form>
<?php	require_once(FILEROOT . '/footer.php'); ?>
