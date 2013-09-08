<?
	checkLogin();
	
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$updates = array();
		$numVals = array('str', 'dex', 'con', 'int', 'per', 'wil', 'lp', 'end', 'spd', 'ess');
		$textVals = array('name', 'qualities', 'drawbacks', 'skills', 'powers', 'weapons', 'items', 'notes');
		foreach ($_POST as $key => $value) {
			if (in_array($key, $textVals)) $updates['afmbe_characters`.`'.$key] = sanatizeString($value);
			elseif (in_array($key, $numVals)) $updates['afmbe_characters`.`'.$key] = intval($value);
		}
		
		$mysql->query('UPDATE afmbe_characters, characters SET '.setupUpdates($updates).' WHERE afmbe_characters.characterID = '.$characterID.' AND characters.characterID = afmbe_characters.characterID AND characters.characterID = '.$characterID);
		$mysql->query("INSERT INTO characterHistory (characterID, enactedBy, enactedOn, action) VALUES ($characterID, $userID, NOW(), 'editedChar')");
		header('Location: '.SITEROOT.'/characters/afmbe/sheet/'.$characterID);
	} else header('Location: '.SITEROOT.'/403');
?>