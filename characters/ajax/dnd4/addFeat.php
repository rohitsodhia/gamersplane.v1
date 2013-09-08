<?
	if (checkLogin(0)) {
		includeSystemInfo('dnd4');
		
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = {$_SESSION['userID']}");
		if ($charCheck->rowCount()) {
			$name = sanitizeString($_POST['name'], 'rem_dup_spaces');
			if (strlen($name)) {
				$featID = getFeat($name);
				$featInfo = array('featID' => $featID, 'name' => $name);
				$addFeat = $mysql->query("INSERT INTO dnd4_feats (characterID, featID) VALUES ($characterID, $featID)");
				if ($addFeat->rowCount()) featFormFormat($characterID, $featInfo);
			}
		}
	}
?>