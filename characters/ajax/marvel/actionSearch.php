<?
	if (checkLogin(0)) {
		$search = sanitizeString($_POST['search'], 'like_clean', 'search_format');
		$characterID = intval($_POST['characterID']);
		
		$actions = $mysql->prepare("SELECT al.actionID, al.name FROM marvel_actionsList al LEFT JOIN marvel_actions pa ON al.actionID = pa.actionID AND pa.characterID = $characterID WHERE al.name LIKE ? AND al.name != '$search' AND pa.actionID IS NULL LIMIT 5");
		$actions->execute(array("%$search%"));
		foreach ($actions as $info) {
			echo "<a id=\"newAction_{$info['actionID']}\" href=\"\">".mb_convert_case($info['name'], MB_CASE_TITLE)."</a>\n";
		}
	}
?>