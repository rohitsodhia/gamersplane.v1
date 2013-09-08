<?
	checkLogin();
	
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = $userID");
		if ($charCheck->rowCount()) {
			$updates = array();
			$numVals = array('st', 'dx', 'iq', 'ht', 'hp', 'will', 'per', 'fp', 'hp_current', 'fp_current', 'dmg_thr', 'dmg_sw', 'speed', 'move');
			$textVals = array('name', 'languages', 'advantages', 'disadvantages', 'skills', 'items', 'notes');
			foreach (array_merge($numVals, $textVals) as $value) $updates[] = "`$value` = :$value";
			
			$updateChar = $mysql->prepare('UPDATE gurps_characters SET '.implode($updates, ', ').' WHERE characterID = :characterID');
			foreach (array_merge($numVals, $textVals) as $value) $updateChar->bindValue(":$value", $_POST[$value]);
 			$updateChar->bindValue(':characterID', $characterID);
			$updateChar->execute();
			updateCharacterHistory($characterID, 'editedChar');
		}

		header('Location: '.SITEROOT.'/characters/gurps/'.$characterID);
	} else header('Location: '.SITEROOT.'/403');
?>