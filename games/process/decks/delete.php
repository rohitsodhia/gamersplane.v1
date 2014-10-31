<?
	$gameID = intval($_POST['gameID']);
	$deckID = intval($_POST['deckID']);
	$gmCheck = $mysql->query("SELECT p.primaryGM FROM players p, decks d WHERE p.isGM = 1 AND p.gameID = $gameID AND d.gameID = p.gameID AND d.deckID = $deckID AND p.userID = {$currentUser->userID}");
	if (isset($_POST['delete']) && $gmCheck->rowCount()) {
		$mysql->query("DELETE FROM deckPermissions WHERE deckID = $deckID");
		$mysql->query("DELETE FROM decks WHERE deckID = $deckID");
		
		addGameHistory($gameID, 'deckDeleted', $currentUser->userID, 'NOW()', 'deck', $deckID);
		
		echo 1;
	} else echo 0;
?>