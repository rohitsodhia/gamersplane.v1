<?
	if (checkLogin(0)) {
		includeSystemInfo('sweote');

		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = $userID");
		if ($charCheck->rowCount()) {
			$name = sanitizeString($_POST['name'], 'rem_dup_spaces');
			if (strlen($name)) {
				$skillID = getSkill($name, 'sweote');
				$stat = sanitizeString($_POST['stat']);
				$skillInfo = array('skillID' => $skillID, 'name' => $name, 'stat' => $stat, 'rank' => 0);
				$addSkill = $mysql->query("INSERT INTO sweote_skills (characterID, skillID, stat) VALUES ($characterID, $skillID, '$stat')");
				if ($addSkill->rowCount()) skillFormFormat($skillInfo);
			}
		}
	}
?>