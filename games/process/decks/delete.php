<?
	$gameID = intval($_POST['gameID']);
	$deckID = intval($_POST['deckID']);
	$gmCheck = $mongo->games->findOne(array('gameID' => $gameID, 'players' => array('$elemMatch' => array('user.userID' => $currentUser->userID, 'isGM ' => true))), array('players.$' => true));
	if (isset($_POST['delete']) && $gmCheck) {
		$mysql->query("DELETE FROM deckPermissions WHERE deckID = {$deckID}");
		$mysql->query("DELETE FROM decks WHERE deckID = {$deckID}");
		
#		$hl_deckDeleted = new HistoryLogger('deckDeleted');
#		$hl_deckDeleted->addDeck($deckID)->addUser($currentUser->userID)->save();
		
		displayJSON(array('success' => true, 'deckID' => $deckID));
	} else 
		displayJSON(array('failed' => true));
?>