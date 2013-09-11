<?
	checkLogin();
	
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = $userID");
		if ($charCheck->rowCount()) {
			$updates = array();
			$numVals = array('health_max', 'energy_max', 'int', 'str', 'agi', 'spd', 'dur');
			$textVals = array('normName', 'superName', 'notes');
			foreach (array_merge($numVals, $textVals) as $value) $updates[] = "`$value` = :$value";
			$unusedStones = number_format(intval($_POST['white']) + intval($_POST['red']) / 3, 1);
			$updates[] = '`unusedStones` = '.$unusedStones;
			
			$mysql->query('DELETE FROM marvel_actions WHERE characterID = '.$characterID);
			$addAction = $mysql->prepare("INSERT INTO marvel_actions SET characterID = :characterID, actionID = :actionID, level = :level, details = :details, cost = :cost");
			foreach ($_POST['action'] as $key => $value) { if (intval($key) != 0) {
				$addAction->bindValue(':characterID', $characterID);
				$addAction->bindValue(':actionID', $key);
				$addAction->bindValue(':level', $value['level']);
				$addAction->bindValue(':cost', number_format(floatval($value['cost']), 1));
				$addAction->bindValue(':details', sanitizeString($value['details']));
				$addAction->execute();
			} }
			
			$mysql->query('DELETE FROM marvel_modifiers WHERE characterID = '.$characterID);
			$addModifier = $mysql->prepare("INSERT INTO marvel_modifiers SET characterID = :characterID, modifierID = :modifierID, level = :level, details = :details, cost = :cost");
			foreach ($_POST['modifier'] as $key => $value) { if (intval($key) != 0) {
				$addModifier->bindValue(':characterID', $characterID);
				$addModifier->bindValue(':modifierID', $key);
				$addModifier->bindValue(':level', $value['level']);
				$addModifier->bindValue(':cost', number_format(floatval($value['cost']), 1));
				$addModifier->bindValue(':details', sanitizeString($value['details']));
				$addModifier->execute();
			} }
			
			$deleteChallenge = $mysql->prepare('DELETE FROM marvel_challenges WHERE challengeID = :challengeID');
			$updateChallenge = $mysql->prepare('UPDATE marvel_challenges SET stones = :stones WHERE challengeID = :challengeID');
			foreach ($_POST['challenge'] as $key => $value) {
				$challengeID = intval($key);
				$stones = intval($value['cost']);
				if ($stones == 0) $deleteChallenge->execute(array(':challengeID' => $challengeID));
				else $updateChallenge->execute(array(':challengeID' => $challengeID, ':stones' => $stones));
			}
			
			$updateChar = $mysql->prepare('UPDATE marvel_characters SET '.implode($updates, ', ').' WHERE characterID = :characterID');
			foreach (array_merge($numVals, $textVals) as $value) $updateChar->bindValue(":$value", $_POST[$value]);
 			$updateChar->bindValue(':characterID', $characterID);
			$updateChar->execute();
			addCharacterHistory($characterID, 'editedChar');
			header('Location: '.SITEROOT.'/characters/marvel/'.$characterID);
		} else header('Location: '.SITEROOT.'/403');
	} else header('Location: '.SITEROOT.'/403');
?>