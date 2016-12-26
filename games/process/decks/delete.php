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
	if (isset($_POST['delete']) && $gmCheck && sizeof($deck)) {
		$mongo->games->updateOne(
			['gameID' => $gameID, 'decks.deckID' => $deckID],
			['$pull' => ['decks' => ['deckID' => $deckID]]]
		);

#		$hl_deckDeleted = new HistoryLogger('deckDeleted');
#		$hl_deckDeleted->addDeck($deckID)->addUser($currentUser->userID)->save();

		displayJSON(['success' => true, 'deckID' => $deckID]);
	} else {
		displayJSON(['failed' => true]);
	}
?>
