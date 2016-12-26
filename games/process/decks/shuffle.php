<?php
	$gameID = intval($_POST['gameID']);
	$deckID = intval($_POST['deckID']);
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
	$deck = [];
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
		$deck['deck'] = [];
		for ($count = 1; $count <= $deckSize; $count++) {
			$deck['deck'][] = $count;
		}
		shuffle($deck['deck']);
		$deck['position'] = 1;
		$deck['lastShuffle'] = genMongoDate();
		$mongo->games->updateOne(
			['gameID' => $gameID, 'decks.deckID' => $deckID],
			['$set' => ['decks.$' => $deck]]
		);

#		$hl_deckShuffled = new HistoryLogger('deckShuffled');
#		$hl_deckShuffled->addDeck($deckID)->addUser($currentUser->userID)->save();

		displayJSON(['success' => true, 'deckID' => (int) $deckID, 'deckSize' => (int) $deckSize]);
	} else {
		displayJSON(['failed' => true]);
	}
?>
