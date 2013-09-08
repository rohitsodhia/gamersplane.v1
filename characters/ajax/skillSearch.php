<?
	if (checkLogin(0)) {
		$search = sanitizeString($_POST['search'], 'like_clean', 'search_format');
		$characterID = intval($_POST['characterID']);
		$system = sanitizeString($_POST['system']);
		
		$validateSystem = $mysql->prepare('SELECT systemID FROM systems WHERE shortName = :shortName');
		$validateSystem->execute(array(':shortName' => $system));
		if ($validateSystem->rowCount()) {
			$skills = $mysql->prepare("SELECT skillsList.skillID, skillsList.name FROM skillsList LEFT JOIN (SELECT skillID FROM {$system}_skills WHERE characterID = $characterID) {$system}_skills ON skillsList.skillID = {$system}_skills.skillID WHERE name LIKE ? AND {$system}_skills.skillID IS NULL LIMIT 5");
			$skills->execute(array("%$search%"));
			foreach ($skills as $info) {
				echo "<a href=\"\">{$info['name']}</a>\n";
			}
		}
	}
?>