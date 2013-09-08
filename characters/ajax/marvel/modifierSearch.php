<?
	if (checkLogin(0)) {
		$search = sanatizeString(preg_replace('/\s+/', ' ', strtolower($_POST['search'])));
		$characterID = intval($_POST['characterID']);
		
		$modifiers = $mysql->query("SELECT ml.modifierID, ml.name FROM marvel_modifiersList ml LEFT JOIN marvel_modifiers pa ON ml.modifierID = pa.modifierID AND pa.characterID = $characterID WHERE ml.name LIKE '%$search%' AND ml.name != '$search' AND pa.modifierID IS NULL LIMIT 5");
		foreach ($modifiers as $info) {
			echo "<a id=\"newModifier_{$info['modifierID']}\" href=\"\">".mb_convert_case($info['name'], MB_CASE_TITLE)."</a>\n";
		}
	}
?>