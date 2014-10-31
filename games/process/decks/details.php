<?
	$gameID = intval($_POST['gameID']);
	$gmCheck = $mysql->query("SELECT primaryGM FROM players WHERE isGM = 1 AND gameID = $gameID AND userID = {$currentUser->userID}");
	$isGM = $gmCheck->rowCount()?TRUE:FALSE;
	if (isset($_POST['create']) && $isGM) {
		$deckLabel = sanitizeString($_POST['deckLabel']);
		$type = $_POST['deckType'];
		$deckInfo = $mysql->prepare('SELECT short, deckSize FROM deckTypes WHERE short = :short');
		$deckInfo->execute(array(':short' => $type));
		if ($deckInfo->rowCount() == 0) {
			if (isset($_POST['modal'])) echo -1;
			else header('Location: /games/'.$gameID.'/decks?new=1&invalidDeck=1');
		} else {
			$deckInfo = $deckInfo->fetch();
			$deck = array();
			for ($count = 1; $count <= $deckInfo['deckSize']; $count++) $deck[] = $count;
			shuffle($deck);
			$deck = sanitizeString(implode('~', $deck));
			
			$addDeck = $mysql->prepare("INSERT INTO decks (label, type, deck, position, gameID) VALUES (:deckLabel, '$type', '$deck', 1, $gameID)");
			$addDeck->execute(array(':deckLabel' => $deckLabel));
			$deckID = $mysql->lastInsertId();

			addGameHistory($gameID, 'deckCreated', $currentUser->userID, 'NOW()', 'deck', $deckID);

			if (isset($_POST['addUser']) && sizeof($_POST['addUser'])) {
				$addDeckPermissions = $mysql->prepare("INSERT INTO deckPermissions SET deckID = $deckID, userID = :userID");
				$dUserID = NULL;
				$addDeckPermissions->bindParam(':userID', $dUserID);
				foreach (array_keys($_POST['addUser']) as $dUserID) {
					$addDeckPermissions->execute();
				}
			}
			
			if (isset($_POST['modal'])) echo 1;
			else header('Location: /games/'.$gameID.'/?success=createDeck');
		}
	} elseif (isset($_POST['edit']) && $isGM) {
		$deckID = intval($_POST['deckID']);
		$deckInfo = $mysql->query("SELECT d.label, d.type, d.deck, d.position FROM decks d INNER JOIN games g ON d.gameID = g.gameID INNER JOIN players p ON g.gameID = p.gameID AND p.isGM = 1 WHERE d.deckID = $deckID AND p.userID = {$currentUser->userID} LIMIT 1");
		if ($deckInfo->rowCount()) {
			$deckInfo = $deckInfo->fetch();
			$type = $_POST['deckType'];
			if ($deckInfo['type'] != $type) {
				$deckInfo = $mysql->prepare('SELECT short, deckSize FROM deckTypes WHERE short = :short');
				$deckInfo->execute(array(':short' => $type));
				if ($deckInfo = $deckInfo->fetch()) {
					$deck = array();
					for ($count = 1; $count <= $deckInfo['deckSize']; $count++) $deck[] = $count;
					shuffle($deck);
					$deck = sanitizeString(implode('~', $deck));
					$position = 1;
				}
			}
			if ($deck == '') {
				$deck = $deckInfo['deck'];
				$position = $deckInfo['position'];
			}
			
			$updateDeck = $mysql->prepare("UPDATE decks SET label = :deckLabel, type = '$type', deck = '$deck', position = $position WHERE deckID = $deckID");
			$updateDeck->execute(array(':deckLabel' => sanitizeString($_POST['deckLabel'])));

			addGameHistory($gameID, 'deckUpdated', $currentUser->userID, 'NOW()', 'deck', $deckID);
			
			$mysql->query('DELETE FROM deckPermissions WHERE deckID = '.$deckID);
			if (isset($_POST['addUser']) && sizeof($_POST['addUser'])) {
				$addDeckPermissions = $mysql->prepare("INSERT INTO deckPermissions SET deckID = $deckID, userID = :userID");
				$dUserID = NULL;
				$addDeckPermissions->bindParam(':userID', $dUserID);
				foreach (array_keys($_POST['addUser']) as $dUserID) {
					$addDeckPermissions->execute();
				}
			}
		}
		echo 1;
	} else {
		if (isset($_POST['modal'])) echo 0;
		else header('Location: /games/');
	}
?>