<?
	if (checkLogin(0)) {
		includeSystemInfo('pathfinder');
		
		$userID = $_SESSION['userID'];
		$characterID = intval($_POST['characterID']);
		if (allowCharEdit($characterID, $userID)) {
			$name = sanitizeString($_POST['name'], 'rem_dup_spaces');
			if (strlen($name)) {
				$featID = getFeat($name);
				$featInfo = array('featID' => $featID, 'name' => $name);
				$addFeat = $mysql->query("INSERT INTO pathfinder_feats (characterID, featID) VALUES ($characterID, $featID)");
				if ($addFeat->rowCount()) featFormFormat($characterID, $featInfo);
			}
		}
	}
?>