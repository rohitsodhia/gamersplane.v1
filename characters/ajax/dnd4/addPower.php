<?
	if (checkLogin(0)) {
		includeSystemInfo('dnd4');

		$userID = $_SESSION['userID'];
		$characterID = intval($_POST['characterID']);
		if (allowCharEdit($characterID, $userID)) {
			$powerName = sanitizeString($_POST['name'], 'rem_dup_spaces');
			if (in_array($_POST['type'], array('a', 'e', 'd'))) $type = $_POST['type'];
			else exit;
			$powerCheck = $mysql->prepare('SELECT powerID FROM dnd4_powersList WHERE LOWER(searchName) = :searchName');
			$powerCheck->execute(array(':searchName' => sanitizeString($powerName, 'search_format')));
			if ($powerCheck->rowCount()) $powerID = $powerCheck->fetchColumn();
			else {
				$userID = intval($_SESSION['userID']);
				$addNewPower = $mysql->prepare("INSERT INTO dnd4_powersList (name, searchName, userDefined) VALUES (:name, :searchName, $userID)");
				$addNewPower->bindValue(':name', $powerName);
				$addNewPower->execute(array(':name' => $powerName, ':searchName' => sanitizeString($powerName, 'search_format')));
				$powerID = $mysql->lastInsertId();
			}

			$addPower = $mysql->query("INSERT INTO dnd4_powers (characterID, powerID, type) VALUES ($characterID, $powerID, '$type')");
			if ($addPower->rowCount()) {
				$powerInfo['powerID'] = $mysql->lastInsertId;
				$powerInfo['name'] = $powerName;
				powerFormFormat($powerInfo);
			}
		}
	}
?>