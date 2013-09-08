<?
	checkLogin();
	
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$updates = array();
		$numVals = array('st', 'dx', 'iq', 'ht', 'hp', 'will', 'per', 'fp', 'hp_current', 'fp_current', 'dmg_thr', 'dmg_sw', 'speed', 'move');
		$textVals = array('name', 'languages', 'advantages', 'disadvantages', 'skills', 'items', 'notes');
		foreach ($_POST as $key => $value) {
			if (in_array($key, $textVals)) $updates['gurps_characters`.`'.$key] = sanatizeString($value);
			elseif (in_array($key, $numVals)) $updates['gurps_characters`.`'.$key] = intval($value);
		}
		
		$mysql->query('UPDATE gurps_characters, characters SET '.setupUpdates($updates).' WHERE gurps_characters.characterID = '.$characterID.' AND characters.characterID = gurps_characters.characterID AND characters.characterID = '.$characterID);
		$mysql->query("INSERT INTO characterHistory (characterID, enactedBy, enactedOn, action) VALUES ($characterID, $userID, NOW(), 'editedChar')");
		header('Location: '.SITEROOT.'/characters/gurps/sheet/'.$characterID);
	} else header('Location: '.SITEROOT.'/403');
?>