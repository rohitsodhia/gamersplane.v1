<?
	if (checkLogin(0)) {
		$userID = $_SESSION['userID'];
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = $userID");
		if ($charCheck->rowCount()) {
			$name = sanitizeString($_POST['name'], 'rem_dup_spaces');
			$type = in_array($_POST['type'], array('a', 'e', 'd'));
			$checkDup = $mysql->prepare('SELECT powerID FROM dnd4_powers WHERE characterID = '.$characterID.' AND LOWER(name) = :name');
			$checkDup->execute(array(':name' => strtolower($name)));
			if ($checkDup->rowCount()) {
				echo 'Exists'
			} else {
				$addPower = $mysql->prepare("INSERT INTO dnd4_powers (characterID, name, type) VALUES ($characterID, :name, :type)");
				$addPower->execute(array(':name' => $name, ':type' => $type));
				if ($addPower->getResult()) {
					$powerInfo['powerID'] = $mysql->lastInsertId;
					$powerInfo['name'] = $name;
					powerFormFormat($powerInfo);
				}
			}
		}
	}
?>