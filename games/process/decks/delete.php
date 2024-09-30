<?php
	$gameID = intval($_POST['gameID']);
	$deckID = intval($_POST['deckID']);
	$gmCheck = $mysql->query("SELECT games.gameID FROM games INNER JOIN players ON games.gameID = players.gameID WHERE games.gameID = {$gameID} AND players.userID = {$currentUser->userID} AND players.isGM = 1 LIMIT 1");
	if (isset($_POST['delete']) && $gmCheck->rowCount()) {
		$mysql->query("DELETE FROM decks WHERE deckID = {$deckID} LIMIT 1");
		$mysql->query("DELETE FROM deckPermissions WHERE deckID = {$deckID} LIMIT 1");

		displayJSON(['success' => true, 'deckID' => $deckID]);
	} else {
		displayJSON(['failed' => true]);
	}
?>
