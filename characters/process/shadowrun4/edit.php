<?
	checkLogin();
	
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		if (allowCharEdit($characterID, $userID)) {
			$updates = array();
			$numVals = array('body', 'agility', 'reaction', 'strength', 'charisma', 'intuition', 'logic', 'willpower', 'edge_total', 'edge_current', 'essence', 'mag_res', 'initiative', 'initiative_passes', 'matrix_initiative', 'astral_initiative', 'physicalDamage', 'stunDamage');
			$textVals = array('name', 'metatype', 'qualities', 'skills', 'spells', 'weapons', 'armor', 'augments', 'contacts', 'items', 'notes');
			foreach (array_merge($numVals, $textVals) as $value) $updates[] = "`$value` = :$value";

			$updateChar = $mysql->prepare('UPDATE shadowrun4_characters SET '.implode($updates, ', ').' WHERE characterID = :characterID');
			foreach (array_merge($numVals, $textVals) as $value) $updateChar->bindValue(":$value", $_POST[$value]);
 			$updateChar->bindValue(':characterID', $characterID);
			$updateChar->execute();
			addCharacterHistory($characterID, 'editedChar');
		}

		header('Location: '.SITEROOT.'/characters/shadowrun4/'.$characterID);
	} else header('Location: '.SITEROOT.'/403');
?>