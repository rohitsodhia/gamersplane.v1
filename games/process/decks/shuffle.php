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
	if (isset($_POST['shuffle']) && $gmCheck && sizeof($deck)) {
		require_once('includes/DeckTypes.class.php');
		$deckTypes = DeckTypes::getInstance()->getAll();
		$deckSize = $deckTypes[$deck['type']]['size'];
		$deck['deck'] = array();
		for ($count = 1; $count <= $deckSize; $count++) 
			$deck['deck'][] = $count;
		shuffle($deck['deck']);
		$deck['position'] = 1;
		$deck['lastShuffle'] = new MongoDate();
		$mongo->games->update(array('gameID' => $gameID, 'decks.deckID' => $deckID), array('$set' => array('decks.$' => $deck)));

#		$hl_deckShuffled = new HistoryLogger('deckShuffled');
#		$hl_deckShuffled->addDeck($deckID)->addUser($currentUser->userID)->save();
		
		displayJSON(array('success' => true, 'deckID' => (int) $deckID, 'deckSize' => (int) $deckSize));
	} else 
		displayJSON(array('failed' => true));
?>