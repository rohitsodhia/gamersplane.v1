<?
	checkLogin(1);
	
	$userID = intval($_SESSION['userID']);
	$gameID = intval($_POST['gameID']);
	$gmCheck = $mysql->query("SELECT `primary` FROM gms WHERE gameID = $gameID AND userID = $userID");
	$isGM = $gmCheck->rowCount()?TRUE:FALSE;
	if (isset($_POST['create']) && $isGM) {
		$deckLabel = sanatizeString($_POST['deckLabel']);
		$type = $_POST['deckType'];
		$deckInfo = $mysql->prepare('SELECT short, deckSize FROM decks WHERE short = :short');
		$deckInfo->execute(array($type));
		if ($deckInfo->rowCount() == 0) { header('Location: '.SITEROOT.'/games/'.$gameID.'/decks?new=1&invalidDeck=1'); exit; }
		$deckInfo = $deckInfo->fetch();
		$deck = array();
		for ($count = 1; $count <= $deckInfo['deckSize']; $count++) $deck[] = $count;
		shuffle($deck);
		$deck = sanatizeString(implode('~', $deck));
		
		$mysql->query('INSERT INTO decks (label, type, deck, position, gameID) VALUES ("'.$deckLabel.'", "'.$type.'", "'.$deck.'", 1, '.$gameID.')');
		$deckID = $mysql->lastInsertId();
		$deckPermissionsQ = $mysql->prepare('INSERT INTO deckPermissions SET deckID = :deckID, userID = :userID)');
		$deckPermissionsQ->bindValue(':deckID', $deckID);
		$deckPermissionsQ->bindParam(':userID', $dUserID);
		$dUserID = $userID;
		$deckPermissionsQ->execute();
		foreach ($_POST['addUser'] as $dUserID) $deckPermissionsQ->execute();
		
		$mysql->query("INSERT INTO gameHistory (gameID, enactedBy, enactedOn, action) VALUES ($gameID, $userID, NOW(), 'deckCreated')");
		
		header('Location: '.SITEROOT.'/games/'.$gameID.'/decks?success=create');
	} elseif (isset($_POST['shuffle']) && $isGM) {
		$deckID = intval($_POST['deckID']);
		$deckInfo = $mysql->query('SELECT type, deckSize FROM decks WHERE deckID = '.$deckID);
		$deckInfo = $deckInfo->fetch();
		$deck = array();
		for ($count = 1; $count <= $deckInfo['deckSize']; $count++) $deck[] = $count;
		shuffle($deck);
		$deck = sanatizeString(implode('~', $deck));
		$mysql->query('UPDATE decks SET position = 1, deck = "'.$deck.'", lastShuffle = "'.gmdate('Y-m-d H:i:s').'" WHERE deckID = '.$deckID);
		
		$mysql->query("INSERT INTO gameHistory (gameID, enactedBy, enactedOn, action) VALUES ($gameID, $userID, NOW(), 'deckShuffled')");
			
		header('Location: '.SITEROOT.'/games/'.$gameID.'/decks?success=shuffle');
	} elseif (isset($_POST['submit']) && $isGM) {
		$deckID = intval($_POST['deckID']);
		$deckInfo = $mysql->query('SELECT decks.label, decks.type, decks.deck, decks.position FROM decks INNER JOIN games ON decks.gameID = games.gameID INNER JOIN gms ON games.gameID = gms.gameID WHERE decks.deckID = '.$deckID.' AND gms.userID = '.$userID.' LIMIT 1');
		if ($deckInfo->rowCount()) {
			$deckInfo = $deckInfo->fetch();
			$updateStr = '';
			if ($deckInfo['label'] != sanatizeString($_POST['deckLabel'])) $updateStr .= 'label = "'.sanatizeString($_POST['deckLabel']).'" AND ';
			if ($deckInfo['type'] != sanatizeString($_POST['deckType'])) {
				$updateStr .= 'type = '.sanatizeString($_POST['deckType']).' AND ';
				$deck = array();
				$type = sanatizeString($_POST['deckType']);
				if ($type == 'pcwj') for ($count = 1; $count <= 54; $count++) $deck[] = $count;
				elseif ($type == 'pcwoj') for ($count = 1; $count <= 52; $count++) $deck[] = $count;
				shuffle($deck);
				$updateStr .= 'deck = "'.sanatizeString(implode('~', $deck)).'" AND ';
			}
			$mysql->query('UPDATE decks SET '.substr($updateStr, 0, -5).' WHERE deckID = '.$deckID);
			
			$mysql->query('DELETE FROM deckPermissions WHERE deckID = '.$deckID);
			$deckPermissionsQ = $mysql->prepare('INSERT INTO deckPermissions SET deckID = :deckID, userID = :userID)');
			$deckPermissionsQ->bindValue(':deckID', $deckID);
			$deckPermissionsQ->bindParam(':userID', $dUserID);
			$dUserID = $userID;
			$deckPermissionsQ->execute();
			foreach ($_POST['addUser'] as $dUserID) $deckPermissionsQ->execute();
			
			$mysql->query("INSERT INTO gameHistory (gameID, enactedBy, enactedOn, action) VALUES ($gameID, $userID, NOW(), 'deckEdited')");
		}
		header('Location: '.SITEROOT.'/games/'.$gameID.'/decks?success=edit');
	} elseif (isset($_POST['delete']) && $isGM) {
		$deckID = intval($_POST['deckID']);
		$gmCheck = $mysql->query('SELECT decks.label FROM decks INNER JOIN games ON decks.gameID = games.gameID INNER JOIN gms ON games.gameID = gms.gameID WHERE decks.deckID = '.$deckID.' AND gms.userID = '.$userID.' LIMIT 1');
		if ($gmCheck->rowCount()) $mysql->query("DELETE FROM decks WHERE deckID = $deckID");
		
		$mysql->query("INSERT INTO gameHistory (gameID, enactedBy, enactedOn, action) VALUES ($gameID, $userID, NOW(), 'deckDeleted')");
		
		header('Location: '.SITEROOT.'/games/'.$gameID.'/decks?success=delete');
	} else header('Location: '.SITEROOT.'/games/');
?>