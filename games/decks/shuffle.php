<?
	$gameID = intval($pathOptions[0]);
	$deckID = intval($pathOptions[2]);
	$gmCheck = $mysql->query("SELECT p.primaryGM FROM players p, decks d WHERE p.isGM = 1 AND p.gameID = $gameID AND d.gameID = p.gameID AND d.deckID = $deckID AND p.userID = {$currentUser->userID}");
	if (!$gmCheck->rowCount()) { header('Location: /tools/maps'); exit; }
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Shuffle Deck</h1>
		
<?
	$deckDetails = $mysql->query("SELECT label, deck, position, lastShuffle FROM decks where deckID = $deckID");
	$deckDetails = $deckDetails->fetch();
	$totalNumCards = sizeof(explode('~', $deckDetails['deck']));
	$numCardsLeft = $totalNumCards - $deckDetails['position'] + 1;
	$lastShuffleAgo = time() - strtotime($deckDetails['lastShuffle']);
?>
		<form method="post" action="/games/process/decks/shuffle" class="hbMargined">
			<input type="hidden" name="gameID" value="<?=$gameID?>">
			<input type="hidden" name="deckID" value="<?=$deckID?>">
			<p class="alignCenter">There are <strong><?=$numCardsLeft?></strong> cards still left in this deck<? if ($deckDetails['lastShuffle'] != '0000-00-00 00:00:00') echo ' and it was last shuffled on <strong>'.date('F j, Y g:i:s a', strtotime($deckDetails['lastShuffle'])).'</strong>'.(($lastShuffleAgo < 3600 )?', '.intval(date('i', $lastShuffleAgo)).' minutes ago':''); ?>.</p>
			<p class="alignCenter">Are you sure you want to shuffle <strong><?=$deckDetails['label']?></strong>?</p>
			<div id="submitDiv" class="alignCenter"><button type="submit" name="shuffle" class="fancyButton">Shuffle Deck</button></div>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>