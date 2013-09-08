<?
	if (checkLogin(0)) {
		$search = sanatizeString(preg_replace('/\s+/', ' ', strtolower($_POST['search'])));
		$characterID = intval($_POST['characterID']);
		
		$actions = $mysql->query("SELECT al.actionID, al.name FROM marvel_actionsList al LEFT JOIN marvel_actions pa ON al.actionID = pa.actionID AND pa.characterID = $characterID WHERE al.name LIKE '%$search%' AND al.name != '$search' AND pa.actionID IS NULL LIMIT 5");
		foreach ($actions as $info) {
			echo "<a id=\"newAction_{$info['actionID']}\" href=\"\">".mb_convert_case($info['name'], MB_CASE_TITLE)."</a>\n";
		}
	}
?>