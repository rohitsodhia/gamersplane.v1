<?
	checkLogin();
	
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = $userID");
		if ($charCheck->rowCount()) {
			$updates = array();
			$numVals = array('str', 'dex', 'con', 'int', 'per', 'wil', 'lp', 'end', 'spd', 'ess');
			$textVals = array('name', 'qualities', 'drawbacks', 'skills', 'powers', 'weapons', 'items', 'notes');
			foreach (array_merge($numVals, $textVals) as $value) $updates[] = "`$value` = :$value";
			
			$updateChar = $mysql->prepare('UPDATE afmbe_characters SET '.implode($updates, ', ').' WHERE characterID = :characterID');
			foreach (array_merge($numVals, $textVals) as $value) $updateChar->bindValue(":$value", $_POST[$value]);
			$updateChar->bindValue(':characterID', $characterID);
			$updateChar->execute();
			addCharacterHistory($characterID, 'editedChar');
		}
		
		header('Location: '.SITEROOT.'/characters/afmbe/'.$characterID);
	} else header('Location: '.SITEROOT.'/403');
?>