<?
	if (checkLogin(0)) {
		$search = sanatizeString(preg_replace('/\s+/', ' ', strtolower($_POST['search'])));
		$characterID = intval($_POST['characterID']);
		$system = sanatizeString($_POST['system']);
		
		$skills = $mysql->query("SELECT skillsList.skillID, skillsList.name FROM skillsList LEFT JOIN (SELECT skillID FROM {$system}_skills WHERE characterID = $characterID) {$system}_skills ON skillsList.skillID = {$system}_skills.skillID WHERE name LIKE '%$search%' AND {$system}_skills.skillID IS NULL LIMIT 5");
		foreach ($skills as $info) {
			echo "<a href=\"".SITEROOT."/characters/process/$system/addSkill/{$info['skillID']}\">".mb_convert_case($info['name'], MB_CASE_TITLE)."</a>\n";
		}
	}
?>