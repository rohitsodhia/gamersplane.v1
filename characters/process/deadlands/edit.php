<?
	checkLogin();
	
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		if (allowCharEdit($characterID, $userID)) {
			$updates = array();
			$textVals = array('name', 'cogSkills', 'knoSkills', 'mieSkills', 'smaSkills', 'spiSkills', 'defSkills', 'nimSkills', 'strSkills', 'quiSkills', 'vigSkills', 'edge_hind', 'nightmare', 'weapons', 'arcane', 'equipment', 'notes');
			$numVals = array('cogNumDice', 'cogDieType', 'knoNumDice', 'knoDieType', 'mieNumDice', 'mieDieType', 'smaNumDice', 'smaDieType', 'spiNumDice', 'spiDieType', 'defNumDice', 'defDieType', 'nimNumDice', 'nimDieType', 'strNumDice', 'strDieType', 'quiNumDice', 'quiDieType', 'vigNumDice', 'vigDieType', 'wind');
			foreach (array_merge($numVals, $textVals) as $value) $updates[] = "`$value` = :$value";
			$updates[] = '`wounds` = :wounds';
			
			$updateChar = $mysql->prepare('UPDATE deadlands_characters SET '.implode($updates, ', ').' WHERE characterID = :characterID');
			foreach (array_merge($numVals, $textVals) as $value) $updateChar->bindValue(":$value", $_POST[$value]);
			$updateChar->bindvalue(':wounds', intval($_POST['wounds']['head']).','.intval($_POST['wounds']['leftHand']).','.intval($_POST['wounds']['rightHand']).','.intval($_POST['wounds']['guts']).','.intval($_POST['wounds']['leftLeg']).','.intval($_POST['wounds']['rightLeg']));
			$updateChar->bindValue(':characterID', $characterID);
			$updateChar->execute();
			addCharacterHistory($characterID, 'editedChar');
		}
		
		header('Location: '.SITEROOT.'/characters/deadlands/'.$characterID);
	} else header('Location: '.SITEROOT.'/403');
?>