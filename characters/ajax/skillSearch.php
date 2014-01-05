<?
	if (checkLogin(0)) {
		$search = sanitizeString($_POST['search'], 'like_clean', 'search_format');
		$characterID = intval($_POST['characterID']);
		$system = sanitizeString($_POST['system']);
		
		if ($systemID = getSystemID($system)) {
			$skills = $mysql->prepare("SELECT sl.skillID, sl.name, ssm.skillID IS NOT NULL systemSkill FROM skillsList sl LEFT JOIN (SELECT skillID FROM {$system}_skills WHERE characterID = $characterID) ss ON sl.skillID = ss.skillID LEFT JOIN system_skill_map ssm ON ssm.systemID = $systemID AND ssm.skillID = sl.skillID WHERE sl.name LIKE ? AND ss.skillID IS NULL ORDER BY systemSkill DESC, sl.name LIMIT 5");
			$skills->execute(array("%$search%"));
			$lastType = NULL;
			foreach ($skills as $info) {
				$classes = array();
				if (!$info['systemSkill']) $classes[] = 'nonSystemSkill';
				if ($info['systemSkill'] != $lastType && $lastType != NULL) $classes[] = 'lineAbove';
				$lastType = $info['systemSkill'];
				echo "<a href=\"\"".(sizeof($classes)?' class="'.implode(' ', $classes).'"':'').">{$info['name']}</a>\n";
			}
		}
	}
?>