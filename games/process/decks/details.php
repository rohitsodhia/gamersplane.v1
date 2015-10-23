<?
	$gameID = intval($_POST['gameID']);
	$addUsers = array();
	if (isset($_POST['addUser'])) 
		foreach ($_POST['addUser'] as $userID) 
			if (intval($userID) > 0) 
				$addUsers[] = (int) $userID;
	$gmCheck = $mysql->query("SELECT primaryGM FROM players WHERE isGM = 1 AND gameID = {$gameID} AND userID = {$currentUser->userID}");
	if (isset($_POST['create']) && $gmCheck->rowCount()) {
		$deckLabel = sanitizeString($_POST['deckLabel']);
		$type = $_POST['deckType'];
		$deckInfo = $mysql->prepare('SELECT short, name, deckSize FROM deckTypes WHERE short = :short');
		$deckInfo->execute(array(':short' => $type));
		if ($deckInfo->rowCount() == 0) {
			if (isset($_POST['modal'])) 
				displayJSON(array('failed' => true, 'invalidDeck' => true), true);
			else 
				header("Location: /games/{$gameID}/decks/?new=1&invalidDeck=1");
		} else {
			$deckInfo = $deckInfo->fetch();
			$deck = array();
			for ($count = 1; $count <= $deckInfo['deckSize']; $count++) 
				$deck[] = $count;
			shuffle($deck);
			$deck = implode('~', $deck);

			$addDeck = $mysql->prepare("INSERT INTO decks (label, type, deck, position, gameID) VALUES (:deckLabel, '{$type}', '{$deck}', 1, {$gameID})");
			$addDeck->execute(array(':deckLabel' => $deckLabel));
			$deckID = $mysql->lastInsertId();

			if (isset($addUsers) && sizeof($addUsers)) {
				$addDeckPermissions = $mysql->prepare("INSERT INTO deckPermissions SET deckID = $deckID, userID = :userID");
				$dUserID = null;
				$addDeckPermissions->bindParam(':userID', $dUserID);
				foreach (array_keys($addUsers) as $dUserID) 
					$addDeckPermissions->execute();
			}

			$hl_deckCreated = new HistoryLogger('deckCreated');
			$hl_deckCreated->addDeck($deckID)->addUser($currentUser->userID)->addForUsers($addUsers)->save();
			
			if (isset($_POST['modal'])) 
				displayJSON(array(
					'success' => true,
					'action' => 'createDeck',
					'deck' => array(
						'deckID' => (int) $deckID,
						'label' => $deckLabel,
						'type' => array(
							'short' => $deckInfo['short'],
							'name' => $deckInfo['name']
						),
						'cardsRemaining' => (int) $deckInfo['deckSize'])
					), true);
			else 
				header('Location: /games/'.$gameID.'/?success=createDeck');
		}
	} elseif (isset($_POST['edit']) && $gmCheck->rowCount()) {
		$deckID = intval($_POST['deckID']);
		$deckInfo = $mysql->query("SELECT d.label, d.type, dt.name, d.deck, d.position FROM decks d INNER JOIN deckTypes dt ON d.type = dt.short INNER JOIN games g ON d.gameID = g.gameID INNER JOIN players p ON g.gameID = p.gameID AND p.isGM = 1 WHERE d.deckID = $deckID AND p.userID = {$currentUser->userID} LIMIT 1");
		if ($deckInfo->rowCount()) {
			$deckInfo = $deckInfo->fetch();
			$type = $_POST['deckType'];
			if ($deckInfo['type'] != $type) {
				$nDeckInfo = $mysql->prepare('SELECT short, name, deckSize FROM deckTypes WHERE short = :short');
				$nDeckInfo->execute(array(':short' => $type));
				if ($nDeckInfo = $nDeckInfo->fetch()) {
					$deck = array();
					for ($count = 1; $count <= $nDeckInfo['deckSize']; $count++) $deck[] = $count;
					shuffle($deck);
					$deck = sanitizeString(implode('~', $deck));
					$position = 1;
					$deckInfo['type'] = $nDeckInfo['type'];
					$deckInfo['name'] = $nDeckInfo['name'];
				}
			}
			if ($deck == '') {
				$deck = $deckInfo['deck'];
				$position = $deckInfo['position'];
			}
			
			$updateDeck = $mysql->prepare("UPDATE decks SET label = :deckLabel, type = '{$type}', deck = '{$deck}', position = {$position} WHERE deckID = {$deckID}");
			$deckLabel = sanitizeString($_POST['deckLabel']);
			$updateDeck->execute(array(':deckLabel' => $deckLabel));
			
			$mysql->query("DELETE FROM deckPermissions WHERE deckID = {$deckID}");
			if (isset($addUsers) && sizeof($addUsers)) {
				$addDeckPermissions = $mysql->prepare("INSERT INTO deckPermissions SET deckID = {$deckID}, userID = :userID");
				$dUserID = null;
				$addDeckPermissions->bindParam(':userID', $dUserID);
				foreach (array_keys($addUsers) as $dUserID) {
					$addDeckPermissions->execute();
				}
			}

			$hl_deckEdited = new HistoryLogger('deckEdited');
			$hl_deckEdited->addDeck($deckID)->addUser($currentUser->userID)->addForUsers($addUsers)->save();
		}
		displayJSON(array(
			'success' => true,
			'action' => 'editDeck',
			'deck' => array(
				'deckID' => (int) $deckID,
				'label' => $deckLabel,
				'type' => array(
					'short' => $deckInfo['type'],
					'name' => $deckInfo['name']
				),
				'cardsRemaining' => (int) sizeof(explode('~', $deck)) - $position + 1)
			), true);
	} else {
		if (isset($_POST['modal'])) 
			displayJSON(array('failed' => true), true);
		else 
			header('Location: /games/');
	}
?>