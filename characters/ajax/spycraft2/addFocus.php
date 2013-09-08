<?
	if (checkLogin(0)) {
		includeSystemInfo('spycraft2');
		
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = $userID");
		if ($charCheck->rowCount()) {
			$name = sanitizeString($_POST['name'], 'rem_dup_spaces');
			if (strlen($name)) {
				$focus = $mysql->prepare('SELECT focusID FROM spycraft2_focusesList WHERE searchName = :searchName');
				$focus->execute(array(':searchName' => sanitizeString($name, 'search_format')));
				if ($focus->rowCount()) $focusID = $focus->fetchColumn();
				else {
					$addNewFocus = $mysql->prepare('INSERT INTO spycraft2_focusesList (name, searchName, userDefined) VALUES (:name, :searchName, :userID)');
					$addNewFocus->execute(array(':name' => $name, ':searchName' => sanitizeString($name, 'search_format'), ':userID' => $userID));
					$focusID = $mysql->lastInsertId();
				}
				$focusInfo = array('focusID' => $focusID, 'name' => $name);
				$addFocus = $mysql->query("INSERT INTO spycraft2_focuses (characterID, focusID) VALUES ($characterID, $focusID)");
				if ($addFocus->rowCount()) focusFormFormat($characterID, $focusInfo);
			}
		}
	}
?>