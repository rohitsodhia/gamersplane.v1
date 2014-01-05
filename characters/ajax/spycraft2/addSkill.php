<?
	if (checkLogin(0)) {
		includeSystemInfo('spycraft2');

		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = $userID");
		if ($charCheck->rowCount()) {
			$name = sanitizeString($_POST['name'], 'rem_dup_spaces');
			if (strlen($name)) {
				$skillID = getSkill($name, 'spycraft2');
				$stat_1 = sanitizeString($_POST['stat_1']);
				$stat_2 = sanitizeString($_POST['stat_2']);
				$statBonus_1 = intval($_POST['statBonus_1']);
				$statBonus_2 = intval($_POST['statBonus_2']);
				$skillInfo = array('skillID' => $skillID, 'name' => $name, 'stat_1' => $stat_1, 'stat_2' => $stat_2, 'ranks' => 0, 'misc' => 0, 'error' => '', 'threat' => '');
				$addSkill = $mysql->query("INSERT INTO spycraft2_skills (characterID, skillID, stat_1, stat_2) VALUES ($characterID, $skillID, '$stat_1', '$stat_2')");
				if ($addSkill->rowCount()) skillFormFormat($skillInfo, $statBonus_1, $statBonus_2);
			}
		}
	}
?>