<?
	if (checkLogin(0)) {
		includeSystemInfo('spycraft');

		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = $userID");
		if ($charCheck->rowCount()) {
			$name = sanitizeString($_POST['name'], 'rem_dup_spaces');
			if (strlen($name)) {
				$skillID = getSkill($name, 'spycraft');
				$stat = sanitizeString($_POST['stat']);
				$skillInfo = array('skillID' => $skillID, 'name' => $name, 'stat' => $stat, 'ranks' => 0, 'misc' => 0, 'error' => '', 'threat' => '');
				$statBonus = intval($_POST['statBonus']);
				$addSkill = $mysql->query("INSERT INTO spycraft_skills (characterID, skillID, stat) VALUES ($characterID, $skillID, '$stat')");
				if ($addSkill->rowCount()) skillFormFormat($skillInfo, $statBonus);
			}
		}
	}
?>