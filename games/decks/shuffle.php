<?
	$gameID = intval($pathOptions[0]);
	$deckID = intval($pathOptions[2]);
	$gmCheck = $mongo->games->findOne(array('gameID' => $gameID, 'players' => array('$elemMatch' => array('user.userID' => $currentUser->userID, 'isGM' => true))), array('decks' => true));
	if (!$gmCheck) { header('Location: /tools/maps'); exit; }
?>
<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Shuffle Deck</h1>
		
<?
	$deck = array();
	foreach ($gmCheck['decks'] as $iDeck) {
		if ($iDeck['deckID'] == $deckID) {
			$deck = $iDeck;
			break;
		}
	}
	$numCardsLeft = sizeof($deck['deck']) - $deck['position'] + 1;
	$lastShuffleAgo = time() - $deck['lastShuffle']->sec;
?>
		<form method="post" action="/games/process/decks/shuffle" class="hbMargined">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<input type="hidden" name="deckID" value="<?=$deckID?>">
			<p class="alignCenter">There are <strong><?=$numCardsLeft?></strong> cards still left in this deck<? if ($deck['lastShuffle']->sec != 0) echo ' and it was last shuffled on <strong>'.date('F j, Y g:i:s a', $deck['lastShuffle']->sec).'</strong>'.(($lastShuffleAgo < 3600 )?', '.intval(date('i', $lastShuffleAgo)).' minutes ago':''); ?>.</p>
			<p class="alignCenter">Are you sure you want to shuffle <strong><?=$deck['label']?></strong>?</p>
			<div id="submitDiv" class="alignCenter"><button type="submit" name="shuffle" class="fancyButton">Shuffle Deck</button></div>
		</form>
<?	require_once(FILEROOT.'/footer.php'); ?>