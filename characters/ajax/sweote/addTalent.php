<?
	if (checkLogin(0)) {
		includeSystemInfo('sweote');
		
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = $userID");
		if ($charCheck->rowCount()) {
			$name = sanitizeString($_POST['name'], 'rem_dup_spaces');
			if (strlen($name)) {
				$talent = $mysql->prepare('SELECT talentID FROM sweote_talentsList WHERE searchName = :searchName');
				$talent->execute(array(':searchName' => sanitizeString($name, 'search_format')));
				if ($talent->rowCount()) $talentID = $talent->fetchColumn();
				else {
					$addNewTalent = $mysql->prepare('INSERT INTO sweote_talentsList (name, searchName, userDefined) VALUES (:name, :searchName, :userID)');
					$addNewTalent->execute(array(':name' => $name, ':searchName' => sanitizeString($name, 'search_format'), ':userID' => $userID));
					$talentID = $mysql->lastInsertId();
				}
				$talentInfo = array('talentID' => $talentID, 'name' => $name);
				$addTalent = $mysql->query("INSERT INTO sweote_talents (characterID, talentID) VALUES ($characterID, $talentID)");
				if ($addTalent->rowCount()) talentFormFormat($characterID, $talentInfo);
			}
		}
	}
?>