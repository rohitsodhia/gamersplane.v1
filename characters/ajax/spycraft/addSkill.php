<?
	if (checkLogin(0)) {
		includeSystemInfo('spycraft');

		$userID = $_SESSION['userID'];
		$characterID = intval($_POST['characterID']);
		if (allowCharEdit($characterID, $userID)) {
			$name = sanitizeString($_POST['name'], 'rem_dup_spaces');
			if (strlen($name)) {
				$skillID = getSkill($name, 'spycraft');
				if (array_key_exists($_POST['stat'], $stats)) $stat = sanitizeString($_POST['stat']);
				else exit;
				$skillInfo = array('skillID' => $skillID, 'name' => $name, 'stat' => $stat, 'ranks' => 0, 'misc' => 0, 'error' => '', 'threat' => '');
				$statBonus = intval($_POST['statBonus']);
				$addSkill = $mysql->query("INSERT INTO spycraft_skills (characterID, skillID, stat) VALUES ($characterID, $skillID, '$stat')");
				if ($addSkill->rowCount()) skillFormFormat($skillInfo, $statBonus);
			}
		}
	}
?>