<?
	require_once('includes/DeckTypes.class.php');
	$deckTypes = DeckTypes::getInstance()->getAll();

	$gameID = intval($_POST['gameID']);
	$addUsers = array();
	if (isset($_POST['addUser'])) 
		foreach ($_POST['addUser'] as $userID => $nothing) 
			if (intval($userID) > 0) 
				$addUsers[] = (int) $userID;
	$gmCheck = $mongo->games->findOne(array('gameID' => $gameID, 'players' => array('$elemMatch' => array('user.userID' => $currentUser->userID, 'isGM' => true))), array('decks' => true));
	$deckLabel = sanitizeString($_POST['deckLabel']);
	if (isset($_POST['create']) && $gmCheck) {
		$type = $_POST['deckType'];
		if (!array_key_exists($type, $deckTypes)) {
			if (isset($_POST['modal'])) 
				displayJSON(array('failed' => true, 'invalidDeck' => true), true);
			else 
				header("Location: /games/{$gameID}/decks/?new=1&invalidDeck=1");
		} else {
			$deck = array(
				'deckID' => mongo_getNextSequence('deckID'),
				'label' => $deckLabel,
				'type' => $type,
				'deck' => array(),
				'position' => 1,
				'lastShuffle' => new MongoDate(),
				'permissions' => sizeof($addUsers)?$addUsers:array()
			);
			for ($count = 1; $count <= $deckTypes[$type]['size']; $count++) 
				$deck['deck'][] = $count;
			shuffle($deck['deck']);

			$mongo->games->update(array('gameID' => $gameID), array('$push' => array('decks' => $deck)));

#			$hl_deckCreated = new HistoryLogger('deckCreated');
#			$hl_deckCreated->addDeck($deckID)->addUser($currentUser->userID)->addForUsers($addUsers)->save();

			if (isset($_POST['modal'])) 
				displayJSON(array(
					'success' => true,
					'action' => 'createDeck',
					'deck' => array(
						'deckID' => $deck['deckID'],
						'label' => $deck['label'],
						'type' => $deck['type'],
						'cardsRemaining' => sizeof($deck['deck'])
					)
				), true);
			else 
				header('Location: /games/'.$gameID.'/?success=createDeck');
		}
	} elseif (isset($_POST['edit']) && $gmCheck) {
		$deckID = intval($_POST['deckID']);
		$deck = array();
		foreach ($gmCheck['decks'] as $iDeck) {
			if ($iDeck['deckID'] == $deckID) {
				$deck = $iDeck;
				break;
			}
		}
		if (sizeof($deck)) {
			$deck['label'] = $deckLabel;
			$type = $_POST['deckType'];
			if ($deck['type'] != $type && array_key_exists($type, $deckTypes)) {
				$deck['deck'] = array();
				for ($count = 1; $count <= $deckTypes[$type]['size']; $count++) 
					$deck['deck'][] = $count;
				shuffle($deck['deck']);
				$deck['position'] = 1;
				$deck['type'] = $type;
				$deck['lastShuffle'] = new MongoDate();
			}
			$deck['permissions'] = sizeof($addUsers)?$addUsers:array();
			$mongo->games->update(array('gameID' => $gameID, 'decks.deckID' => $deckID), array('$set' => array('decks.$' => $deck)));

#			$hl_deckEdited = new HistoryLogger('deckEdited');
#			$hl_deckEdited->addDeck($deckID)->addUser($currentUser->userID)->addForUsers($addUsers)->save();
		}
		displayJSON(array(
			'success' => true,
			'action' => 'editDeck',
			'deck' => array(
				'deckID' => (int) $deckID,
				'label' => $deckLabel,
				'type' => $deck['type'],
				'cardsRemaining' => sizeof($deck['deck']) - $deck['position'] + 1)
			), true);
	} else {
		if (isset($_POST['modal'])) 
			displayJSON(array('failed' => true), true);
		else 
			header('Location: /games/');
	}
?>