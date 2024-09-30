<?php
	$gameID = intval($_POST['gameID']);
	$deckID = intval($_POST['deckID']);
	$getDeck = $mysql->query("SELECT decks.type FROM games INNER JOIN players ON games.gameID = players.gameID INNER JOIN decks ON games.gameID = decks.gameID WHERE games.gameID = {$gameID} AND players.userID = {$currentUser->userID} AND players.isGM = 1 AND decks.deckID = {$deckID} LIMIT 1");
	if (isset($_POST['shuffle']) && $getDeck->rowCount()) {
		$deckType = $getDeck->fetchColumn();
		require_once('includes/DeckTypes.class.php');
		$deckTypes = DeckTypes::getInstance()->getAll();
		$deckSize = $deckTypes[$deckType]['size'];
		$deck = [];
		for ($count = 1; $count <= $deckSize; $count++) {
			$deck[] = $count;
		}
		shuffle($deck);
		$updateDeck = $mysql->prepare("UPDATE decks SET position = 1, lastShuffle = NOW(), deck = ? WHERE deckID = {$deckID} LIMIT 1");
		$updateDeck->execute([json_encode($deck)]);

		displayJSON(['success' => true, 'deckID' => (int) $deckID, 'deckSize' => (int) $deckSize]);
	} else {
		displayJSON(['failed' => true]);
	}
?>
