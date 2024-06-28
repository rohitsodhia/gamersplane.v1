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
	$gmCheck = $mysql->query("SELECT games.gameID FROM games INNER JOIN players ON games.gameID = players.gameID WHERE games.gameID = {$gameID} AND players.userID = {$currentUser->userID} AND players.isGM = 1 LIMIT 1");

	$deckLabel = sanitizeString($_POST['deckLabel']);
	if (isset($_POST['create']) && $gmCheck->rowCount()) {
		$type = $_POST['deckType'];
		if (!array_key_exists($type, $deckTypes)) {
			if (isset($_POST['modal'])) {
				displayJSON(['failed' => true, 'invalidDeck' => true], true);
			} else {
				header("Location: /games/{$gameID}/decks/?new=1&invalidDeck=1");
			}
		} else {
			$insertDeck = $mysql->prepare("INSERT INTO decks SET label = :label, type = :type, deck = :deck, position = 1, lastShuffle = NOW()");
			$deck = []
			for ($count = 1; $count <= $deckTypes[$type]['size']; $count++) {
				$deck[] = $count;
			}
			shuffle($deck);
			$insertDeck->execute([
				'label' => $deckLabel,
				'type' => $type,
				'deck' => json_encode($deck),
			];
			$deckID = $mysql->lastInsertId();
			$addPermissions = $mysql->prepare("INSERT INTO deckPermissions SET deckID = {$deckID}, userID = ?");
			foreach ($addUsers as $userID) {
				$addPermissions->execute([$userID]);
			}

			if (isset($_POST['modal'])) {
				displayJSON([
					'success' => true,
					'action' => 'createDeck',
					'deck' => [
						'deckID' => $deckID,
						'label' => $deckLabel,
						'type' => $type,
						'cardsRemaining' => sizeof($deck)
					]
				], true);
			} else {
				header('Location: /games/' . $gameID . '/?success=createDeck');
			}
		}
	} elseif (isset($_POST['edit']) && $gmCheck->rowCount()) {
		$deckID = intval($_POST['deckID']);
		$deck = [];
		$getDeck = $mysql->query("SELECT * FROM decks WHERE deckID = {$deckID} LIMIT 1");
		$deck = $getDeck->fetch();

		$updateLastShuffle = false;
		$newValues = [];
		if ($deck['label'] != $deckLabel) {
			$newValues['label'] = $deckLabel;
		}
		$type = $_POST['deckType'];
		if ($deck['type'] != $type && array_key_exists($type, $deckTypes)) {
			$newValues['type'] = $type;
			$newDeck = [];
			for ($count = 1; $count <= $deckTypes[$type]['size']; $count++) {
				$newDeck[] = $count;
			}
			shuffle($newDeck);
			$newValues['deck'] = json_encode($newDeck);
			$newValues['position'] = 1;
			$updateLastShuffle = true;
		}
		$updatesPHs = [];
		foreach ($newValues as $key => $nothing) {
			$updatesPHs[] = "`{$key}` = :{$key}";
		}
		$updateDeck = $mysql->prepare("UPDATE decks SET " . implode(', ', $updatesPHs) . ($updateLastShuffle ? ", lastShuffle = NOW" : '') . " WHERE deckID = {$deckID} LIMIT 1");
		$updateDeck->execute($newValues);

		$mysql->query("DELETE FROM deckPermissions WHERE deckID = {$deckID}");
		$addPermissions = $mysql->prepare("INSERT INTO deckPermissions SET deckID = {$deckID}, userID = ?");
		foreach ($addUsers as $userID) {
			$addPermissions->execute([$userID]);
		}

		if ($newValues['deck']) {
			$cardsRemaining = sizeof($newValues['deck']);
		} else {
			sizeof($deck['deck']) - $deck['position'] + 1;
		}
		displayJSON([
			'success' => true,
			'action' => 'editDeck',
			'deck' => [
				'deckID' => (int) $deckID,
				'label' => $deckLabel,
				'type' => $newValues['type'] ?? $deck['type'],
				'cardsRemaining' => $cardsRemaining
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
