<?
	if (checkLogin(0)) {
		$search = sanatizeString(preg_replace('/\s+/', ' ', strtolower($_POST['search'])));
		$characterID = intval($_POST['characterID']);
		
		$focuses = $mysql->query("SELECT fl.focusID, fl.name FROM spycraft2_focusesList fl LEFT JOIN (SELECT focusID FROM spycraft2_focuses WHERE characterID = $characterID) cf ON fl.focusID = cf.focusID WHERE name LIKE '%$search%' AND cf.focusID IS NULL LIMIT 5");
		foreach ($focuses as $info) {
			echo "<a href=\"".SITEROOT."/characters/process/$system/addSkill/{$info['focusID']}\">".mb_convert_case($info['name'], MB_CASE_TITLE)."</a>\n";
		}
	}
?>