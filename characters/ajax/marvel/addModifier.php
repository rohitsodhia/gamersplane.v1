<?
	if (checkLogin(0)) {
		includeSystemInfo('marvel');

		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = $userID");
		if ($charCheck->rowCount()) {
			$name = sanitizeString($_POST['modifierName'], 'rem_dup_spaces');
			$modifierID = $mysql->prepare('SELECT modifierID FROM marvel_modifiersList WHERE name = :name');
			$modifierID->execute(array(':name' => strtolower($name)));
			if ($modifierID->rowCount()) $modifierID = $modifierID->fetchColumn();
			else {
				$addNewModifier = $mysql->prepare('INSERT INTO marvel_modifiersList (name, userDefined) VALUES (:name, :userID)');
				$addNewModifier = execute(array(':name' => $name, ':userID' => $userID));
				$modifierID = $mysql->lastInsertId();
			}
			$addModifier = $mysql->query("INSERT INTO marvel_modifiers (characterID, modifierID) VALUES ($characterID, $modifierID)");
			$numModifiers = intval($_POST['numModifiers']) + 1;
			$modifierInfo = array('modifierID' => $modifierID, 'name' => $name);
			if ($addModifier->rowCount()) modifierFormFormat($modifierInfo, $numModifiers);
		}
	}
?>