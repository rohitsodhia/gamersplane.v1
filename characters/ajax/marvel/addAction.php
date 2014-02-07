<?
	if (checkLogin(0)) {
		includeSystemInfo('marvel');
		
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		if (allowCharEdit($characterID, $userID)) {
			$name = sanitizeString($_POST['actionName'], 'rem_dup_spaces');
			$actionID = $mysql->prepare('SELECT actionID FROM marvel_actionsList WHERE LOWER(name) = :name');
			$actionID->execute(array(':name' => strtolower($name)));
			if ($actionID->rowCount()) $actionID = $actionID->fetchColumn();
			else {
				$addNewAction = $mysql->query('INSERT INTO marvel_actionsList (name, userDefined) VALUES (:name, :userID)');
				$addNewAction = execute(array(':name' => $name, ':userID' => $userID));
				$actionID = $mysql->lastInsertId();
			}
			$addAction = $mysql->query("INSERT INTO marvel_actions (characterID, actionID) VALUES ($characterID, $actionID)");
			$numActions = intval($_POST['numActions']) + 1;
			$actionInfo = array('actionID' => $actionID, 'name' => $name);
			if ($addAction->rowCount()) actionFormFormat($actionInfo, $numActions);
		}
	}
?>