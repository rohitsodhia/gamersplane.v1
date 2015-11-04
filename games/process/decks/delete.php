<?
	$gameID = intval($_POST['gameID']);
	$deckID = intval($_POST['deckID']);
	$gmCheck = $mysql->query("SELECT p.primaryGM FROM players p INNER JOIN decks d ON p.gameID = d.gameID WHERE p.isGM = 1 AND p.gameID = {$gameID} AND d.deckID = {$deckID} AND p.userID = {$currentUser->userID}");
	if (isset($_POST['delete']) && $gmCheck->rowCount()) {
		$mysql->query("DELETE FROM deckPermissions WHERE deckID = {$deckID}");
		$mysql->query("DELETE FROM decks WHERE deckID = {$deckID}");
		
#		$hl_deckDeleted = new HistoryLogger('deckDeleted');
#		$hl_deckDeleted->addDeck($deckID)->addUser($currentUser->userID)->save();
		
		displayJSON(array('success' => true, 'deckID' => $deckID));
	} else 
		displayJSON(array('failed' => true));
?>