<?
	if (checkLogin(0)) {
		includeSystemInfo('dnd4');

		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		if (allowCharEdit($characterID, $userID)) {
			$name = sanitizeString($_POST['name'], 'rem_dup_spaces');
			if (strlen($name)) {
				$skillID = getSkill($name, 'dnd4');
				if (array_key_exists($_POST['stat'], $stats)) $stat = sanitizeString($_POST['stat']);
				else exit;
				$skillInfo = array('skillID' => $skillID, 'name' => $name, 'stat' => $stat, 'ranks' => 0, 'misc' => 0);
				$statBonus = intval($_POST['statBonus']);
				$addSkill = $mysql->query("INSERT INTO dnd4_skills (characterID, skillID, stat) VALUES ($characterID, $skillID, '$stat')");
				if ($addSkill->rowCount()) skillFormFormat($skillInfo, $statBonus);
			}
		}
	}
?>