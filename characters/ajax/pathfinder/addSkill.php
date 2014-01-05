<?
	if (checkLogin(0)) {
		includeSystemInfo('pathfinder');

		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = $userID");
		if ($charCheck->rowCount()) {
			$name = sanitizeString($_POST['name'], 'rem_dup_spaces');
			if (strlen($name)) {
				$skillID = getSkill($name, 'pathfinder');
				$stat = sanitizeString($_POST['stat']);
				$skillInfo = array('skillID' => $skillID, 'name' => $name, 'stat' => $stat, 'ranks' => 0, 'misc' => 0);
				$statBonus = intval($_POST['statBonus']);
				$addSkill = $mysql->query("INSERT INTO pathfinder_skills (characterID, skillID, stat) VALUES ($characterID, $skillID, '$stat')");
				if ($addSkill->rowCount()) skillFormFormat($skillInfo, $statBonus);
			}
		}
	}
?>