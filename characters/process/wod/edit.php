<?
	checkLogin();
	
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = $userID");
		if ($charCheck->rowCount()) {
			$updates = array();
			$numVals = array('int', 'str', 'pre', 'wit', 'dex', 'man', 'res', 'sta', 'com', 'academics', 'computer', 'crafts', 'investigation', 'medicine', 'occult', 'politics', 'science', 'athletics', 'brawl', 'drive', 'firearms', 'larceny', 'stealth', 'survival', 'weaponry', 'animalKen', 'empathy', 'expression', 'intimidation', 'persuasion', 'socialize', 'streetwise', 'subterfuge', 'health', 'willpower', 'morality', 'size', 'speed', 'initiativeMod', 'defense', 'armor');
			$textVals = array('name', 'merits', 'flaws', 'weapons', 'equipment', 'notes');
			foreach (array_merge($numVals, $textVals) as $value) $updates[] = "`$value` = :$value";
		
			$updateChar = $mysql->prepare('UPDATE dnd3_characters SET '.implode($updates, ', ').' WHERE characterID = :characterID');
			foreach (array_merge($numVals, $textVals) as $value) $updateChar->bindValue(":$value", $_POST[$value]);
 			$updateChar->bindValue(':characterID', $characterID);
			$updateChar->execute();
			addCharacterHistory($characterID, 'editedChar');
		}
		
		header('Location: /characters/wod/sheet/'.$characterID);
	} else header('Location: /403');
?>