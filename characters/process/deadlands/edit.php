<?
	checkLogin();
	
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$updates = array();
		$textVals = array('name', 'cogSkills', 'knoSkills', 'mieSkills', 'smaSkills', 'spiSkills', 'defSkills', 'nimSkills', 'strSkills', 'quiSkills', 'vigSkills', 'edge_hind', 'nightmare', 'weapons', 'arcane', 'equipment', 'notes');
		$numVals = array('cogNumDice', 'cogDieType', 'knoNumDice', 'knoDieType', 'mieNumDice', 'mieDieType', 'smaNumDice', 'smaDieType', 'spiNumDice', 'spiDieType', 'defNumDice', 'defDieType', 'nimNumDice', 'nimDieType', 'strNumDice', 'strDieType', 'quiNumDice', 'quiDieType', 'vigNumDice', 'vigDieType', 'wind');
		foreach ($_POST as $key => $value) {
			if (in_array($key, $textVals)) $updates['deadlands_characters`.`'.$key] = sanatizeString($value);
			elseif (in_array($key, $numVals)) $updates['deadlands_characters`.`'.$key] = intval($value);
			elseif ($key == 'wounds') {
				$updates['deadlands_characters`.`wounds'] = array(intval($value['head']), intval($value['leftHand']), intval($value['rightHand']), intval($value['guts']), intval($value['leftLeg']), intval($value['rightLeg']));
				$updates['deadlands_characters`.`wounds'] = implode(',', $updates['deadlands_characters`.`wounds']);
			}
		}
		
		$mysql->query('UPDATE deadlands_characters, characters SET '.setupUpdates($updates).' WHERE deadlands_characters.characterID = '.$characterID.' AND characters.characterID = deadlands_characters.characterID AND characters.characterID = '.$characterID);
		$mysql->query("INSERT INTO characterHistory (characterID, enactedBy, enactedOn, action) VALUES ($characterID, $userID, NOW(), 'editedChar')");
		header('Location: '.SITEROOT.'/characters/deadlands/sheet/'.$characterID);
	} else header('Location: '.SITEROOT.'/403');
?>