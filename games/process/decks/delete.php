<?
	$gameID = intval($_POST['gameID']);
	$deckID = intval($_POST['deckID']);
	$gmCheck = $mongo->games->findOne(array('gameID' => $gameID, 'players' => array('$elemMatch' => array('user.userID' => $currentUser->userID, 'isGM' => true))), array('decks' => true));
	$deck = array();
	foreach ($gmCheck['decks'] as $iDeck) {
		if ($iDeck['deckID'] == $deckID) {
			$deck = $iDeck;
			break;
		}
	}
	if (isset($_POST['delete']) && $gmCheck && sizeof($deck)) {
		$mongo->games->update(array('gameID' => $gameID, 'decks.deckID' => $deckID), array('$pull' => array('decks' => array('deckID' => $deckID))));

#		$hl_deckDeleted = new HistoryLogger('deckDeleted');
#		$hl_deckDeleted->addDeck($deckID)->addUser($currentUser->userID)->save();
		
		displayJSON(array('success' => true, 'deckID' => $deckID));
	} else 
		displayJSON(array('failed' => true));
?>