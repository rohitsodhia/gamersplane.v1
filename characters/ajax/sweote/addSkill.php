<?
	if (checkLogin(0)) {
		includeSystemInfo('sweote');

		$userID = $_SESSION['userID'];
		$characterID = intval($_POST['characterID']);
		if (allowCharEdit($characterID, $userID)) {
			$name = sanitizeString($_POST['name'], 'rem_dup_spaces');
			if (strlen($name)) {
				$skillID = getSkill($name, 'sweote');
				if (array_key_exists($_POST['stat'], $stats)) $stat = sanitizeString($_POST['stat']);
				else exit;
				$skillInfo = array('skillID' => $skillID, 'name' => $name, 'stat' => $stat, 'rank' => 0);
				$addSkill = $mysql->query("INSERT INTO sweote_skills (characterID, skillID, stat) VALUES ($characterID, $skillID, '$stat')");
				if ($addSkill->rowCount()) skillFormFormat($skillInfo);
			}
		}
	}
?>