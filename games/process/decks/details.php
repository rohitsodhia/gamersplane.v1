<?php
	require_once('includes/DeckTypes.class.php');
	$deckTypes = DeckTypes::getInstance()->getAll();

	$gameID = intval($_POST['gameID']);
	$addUsers = [];
	if (isset($_POST['addUser'])) {
		foreach ($_POST['addUser'] as $userID => $nothing) {
			if (intval($userID) > 0) {
				$addUsers[] = (int) $userID;
			}
		}
	}
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
	$deckLabel = sanitizeString($_POST['deckLabel']);
	if (isset($_POST['create']) && $gmCheck) {
		$type = $_POST['deckType'];
		if (!array_key_exists($type, $deckTypes)) {
			if (isset($_POST['modal'])) {
				displayJSON(['failed' => true, 'invalidDeck' => true], true);
			} else {
				header("Location: /games/{$gameID}/decks/?new=1&invalidDeck=1");
			}
		} else {
			$deck = [
				'deckID' => mongo_getNextSequence('deckID'),
				'label' => $deckLabel,
				'type' => $type,
				'deck' => [],
				'position' => 1,
				'lastShuffle' => new MongoDB\BSON\UTCDateTime(),
				'permissions' => sizeof($addUsers) ? $addUsers : []
			];
			for ($count = 1; $count <= $deckTypes[$type]['size']; $count++) {
				$deck['deck'][] = $count;
			}
			shuffle($deck['deck']);

			$mongo->games->updateOne(
				['gameID' => $gameID],
				['$push' => ['decks' => $deck]]
			);

#			$hl_deckCreated = new HistoryLogger('deckCreated');
#			$hl_deckCreated->addDeck($deckID)->addUser($currentUser->userID)->addForUsers($addUsers)->save();

			if (isset($_POST['modal'])) {
				displayJSON([
					'success' => true,
					'action' => 'createDeck',
					'deck' => [
						'deckID' => $deck['deckID'],
						'label' => $deck['label'],
						'type' => $deck['type'],
						'cardsRemaining' => sizeof($deck['deck'])
					]
				], true);
			} else {
				header('Location: /games/' . $gameID . '/?success=createDeck');
			}
		}
	} elseif (isset($_POST['edit']) && $gmCheck) {
		$deckID = intval($_POST['deckID']);
		$deck = [];
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
				$deck['deck'] = [];
				for ($count = 1; $count <= $deckTypes[$type]['size']; $count++) {
					$deck['deck'][] = $count;
				}
				shuffle($deck['deck']);
				$deck['position'] = 1;
				$deck['type'] = $type;
				$deck['lastShuffle'] = genMongoDate();
			}
			$deck['permissions'] = sizeof($addUsers) ? $addUsers : [];
			$mongo->games->updateOne(
				['gameID' => $gameID, 'decks.deckID' => $deckID],
				['$set' => ['decks.$' => $deck]]
			);

#			$hl_deckEdited = new HistoryLogger('deckEdited');
#			$hl_deckEdited->addDeck($deckID)->addUser($currentUser->userID)->addForUsers($addUsers)->save();
		}
		displayJSON([
			'success' => true,
			'action' => 'editDeck',
			'deck' => [
				'deckID' => (int) $deckID,
				'label' => $deckLabel,
				'type' => $deck['type'],
				'cardsRemaining' => sizeof($deck['deck']) - $deck['position'] + 1
			]
		], true);
	} else {
		if (isset($_POST['modal'])) {
			displayJSON(['failed' => true], true);
		} else {
			header('Location: /games/');
		}
	}
?>
