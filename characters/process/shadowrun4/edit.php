<?
	checkLogin();
	
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$updates = array();
		$numVals = array('body', 'agility', 'reaction', 'strength', 'charisma', 'intuition', 'logic', 'willpower', 'edge_total', 'edge_current', 'essence', 'mag_res', 'initiative', 'initiative_passes', 'matrix_initiative', 'astral_initiative', 'physicalDamage', 'stunDamage');
		$textVals = array('name', 'metatype', 'qualities', 'skills', 'spells', 'weapons', 'armor', 'augments', 'contacts', 'items', 'notes');
		foreach ($_POST as $key => $value) {
			if (in_array($key, $textVals)) $updates['shadowrun4_characters`.`'.$key] = sanatizeString($value);
			elseif (in_array($key, $numVals)) $updates['shadowrun4_characters`.`'.$key] = intval($value);
		}

		$mysql->query('UPDATE shadowrun4_characters, characters SET '.setupUpdates($updates).' WHERE shadowrun4_characters.characterID = '.$characterID.' AND characters.characterID = shadowrun4_characters.characterID AND characters.characterID = '.$characterID);
		$mysql->query("INSERT INTO characterHistory (characterID, enactedBy, enactedOn, action) VALUES ($characterID, $userID, NOW(), 'editedChar')");
		header('Location: '.SITEROOT.'/characters/shadowrun4/sheet/'.$characterID);
	} else header('Location: '.SITEROOT.'/403');
?>