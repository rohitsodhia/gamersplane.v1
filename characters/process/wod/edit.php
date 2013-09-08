<?
	checkLogin();
	
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$updates = array();
		$numVals = array('int', 'str', 'pre', 'wit', 'dex', 'man', 'res', 'sta', 'com', 'academics', 'computer', 'crafts', 'investigation', 'medicine', 'occult', 'politics', 'science', 'athletics', 'brawl', 'drive', 'firearms', 'larceny', 'stealth', 'survival', 'weaponry', 'animalKen', 'empathy', 'expression', 'intimidation', 'persuasion', 'socialize', 'streetwise', 'subterfuge', 'health', 'willpower', 'morality', 'size', 'speed', 'initiativeMod', 'defense', 'armor');
		$textVals = array('name', 'merits', 'flaws', 'weapons', 'equipment', 'notes');
		foreach ($_POST as $key => $value) {
			if (in_array($key, $textVals)) $updates['wod_characters`.`'.$key] = sanatizeString($value);
			elseif (in_array($key, $numVals)) $updates['wod_characters`.`'.$key] = intval($value);
		}
		
		$mysql->query('UPDATE wod_characters, characters SET '.setupUpdates($updates).' WHERE wod_characters.characterID = '.$characterID.' AND characters.characterID = wod_characters.characterID AND characters.characterID = '.$characterID);
		$mysql->query("INSERT INTO characterHistory (characterID, enactedBy, enactedOn, action) VALUES ($characterID, $userID, NOW(), 'editedChar')");
		header('Location: '.SITEROOT.'/characters/wod/sheet/'.$characterID);
	} else header('Location: '.SITEROOT.'/403');
?>