<?
	if (checkLogin(0)) {
		$search = sanatizeString(preg_replace('/\s+/', ' ', strtolower($_POST['search'])));
		$characterID = intval($_POST['characterID']);
		$system = sanatizeString($_POST['system']);
		
		$feats = $mysql->query("SELECT featsList.featID, featsList.name FROM featsList LEFT JOIN (SELECT featID FROM {$system}_feats WHERE characterID = $characterID) {$system}_feats ON featsList.featID = {$system}_feats.featID WHERE name LIKE '%$search%' AND {$system}_feats.featID IS NULL LIMIT 5");
		foreach ($feats as $info) {
			echo "<a href=\"".SITEROOT."/characters/process/$system/addSkill/{$info['featID']}\">".mb_convert_case($info['name'], MB_CASE_TITLE)."</a>\n";
		}
	}
?>