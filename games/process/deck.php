<?
	$gameID = intval($_POST['gameID']);
	$gmCheck = $mysql->query("SELECT isGM FROM players WHERE gameID = $gameID AND userID = {$currentUser->userID} AND isGM = 1");
	$isGM = $gmCheck->rowCount()?true:false;
	$addUsers = array();
	if (isset($_POST['addUser'])) 
		foreach ($_POST['addUser'] as $userID) 
			if (intval($userID) > 0) 
				$addUsers[] = (int) $userID;
	if (isset($_POST['create']) && $isGM) {
		$deckLabel = sanitizeString($_POST['deckLabel']);
		$type = $_POST['deckType'];
		$deckInfo = $mysql->prepare('SELECT short, deckSize FROM decks WHERE short = :short');
		$deckInfo->execute(array($type));
		if ($deckInfo->rowCount() == 0) { header('Location: /games/'.$gameID.'/decks?new=1&invalidDeck=1'); exit; }
		$deckInfo = $deckInfo->fetch();
		$deck = array();
		for ($count = 1; $count <= $deckInfo['deckSize']; $count++) 
			$deck[] = $count;
		shuffle($deck);
		$deck = sanitizeString(implode('~', $deck));
		
		$addDeck = $mysql->prepare("INSERT INTO decks (label, type, deck, position, gameID) VALUES (:label, $type, :deck, 1, $gameID)");
		$addDeck->execute(array(':label' => $deckLabel, ':type' => $type, ':deck' => $deck));
		$deckID = $mysql->lastInsertId();
		$deckPermissionsQ = $mysql->prepare('INSERT INTO deckPermissions SET deckID = :deckID, userID = :userID)');
		$deckPermissionsQ->bindValue(':deckID', $deckID);
		$deckPermissionsQ->bindParam(':userID', $dUserID);
		$dUserID = $currentUser->userID;
		$deckPermissionsQ->execute();
		foreach ($addUsers as $dUserID) 
			$deckPermissionsQ->execute();

#		$hl_deckCreated = new HistoryLogger('deckCreated');
#		$hl_deckCreated->addDeck($deckID)->addUser($currentUser->userID)->addForUsers($addUsers)->save();
		
		header('Location: /games/'.$gameID.'/decks?success=create');
	} elseif (isset($_POST['shuffle']) && $isGM) {
		$deckID = intval($_POST['deckID']);
		$deckInfo = $mysql->query('SELECT type, deckSize FROM decks WHERE deckID = '.$deckID);
		$deckInfo = $deckInfo->fetch();
		$deck = array();
		for ($count = 1; $count <= $deckInfo['deckSize']; $count++) 
			$deck[] = $count;
		shuffle($deck);
		$deck = sanitizeString(implode('~', $deck));
		$updateDeck = $mysql->prepare("UPDATE decks SET position = 1, deck = :deck, lastShuffle = :lastShuffle WHERE deckID = $deckID");
		$updateDeck->execute(array(':deck' => $deck, ':lastShuffle' => gmdate('Y-m-d H:i:s')));
		
#		$hl_deckShuffled = new HistoryLogger('deckShuffled');
#		$hl_deckShuffled->addDeck($deckID)->addUser($currentUser->userID)->save();
			
		header('Location: /games/'.$gameID.'/decks?success=shuffle');
	} elseif (isset($_POST['submit']) && $isGM) {
		$deckID = intval($_POST['deckID']);
		$deckInfo = $mysql->query("SELECT label, type, deck, position FROM decks deckID = {$deckID} LIMIT 1");
		if ($deckInfo->rowCount()) {
			$deckInfo = $deckInfo->fetch();
			$updateStr = '';
			if ($deckInfo['label'] != sanitizeString($_POST['deckLabel'])) 
				$updateStr .= 'label = "'.sanitizeString($_POST['deckLabel']).'" AND ';
			if ($deckInfo['type'] != sanitizeString($_POST['deckType'])) {
				$updateStr .= 'type = '.sanitizeString($_POST['deckType']).' AND ';
				$deck = array();
				$type = sanitizeString($_POST['deckType']);
				if ($type == 'pcwj') for ($count = 1; $count <= 54; $count++) $deck[] = $count;
				elseif ($type == 'pcwoj') for ($count = 1; $count <= 52; $count++) $deck[] = $count;
				shuffle($deck);
				$updateStr .= 'deck = "'.sanitizeString(implode('~', $deck)).'" AND ';
			}
			$mysql->query('UPDATE decks SET '.substr($updateStr, 0, -5).' WHERE deckID = '.$deckID);
			
			$mysql->query('DELETE FROM deckPermissions WHERE deckID = '.$deckID);
			$deckPermissionsQ = $mysql->prepare('INSERT INTO deckPermissions SET deckID = :deckID, userID = :userID)');
			$deckPermissionsQ->bindValue(':deckID', $deckID);
			$deckPermissionsQ->bindParam(':userID', $dUserID);
			$dUserID = $currentUser->userID;
			$deckPermissionsQ->execute();
			foreach ($addUsers as $dUserID) 
				$deckPermissionsQ->execute();
			
#			$hl_deckEdited = new HistoryLogger('deckEdited');
#			$hl_deckEdited->addDeck($deckID)->addUser($currentUser->userID)->addForUsers($addUsers)->save();
		}
		header('Location: /games/'.$gameID.'/decks?success=edit');
	} elseif (isset($_POST['delete']) && $isGM) {
		$deckID = intval($_POST['deckID']);
		$mysql->query("DELETE FROM decks WHERE deckID = $deckID");
#		$hl_deckDeleted = new HistoryLogger('deckDeleted');
#		$hl_deckDeleted->addDeck($deckID)->addUser($currentUser->userID)->save();
		
		header('Location: /games/'.$gameID.'/decks?success=delete');
	} else 
	header('Location: /games/');
?>