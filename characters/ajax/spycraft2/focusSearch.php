<?
	if ($loggedIn) {
		$search = sanitizeString($_POST['search'], 'search_format');
		$characterID = intval($_POST['characterID']);
		
		$focuses = $mysql->prepare("SELECT fl.focusID, fl.name FROM spycraft2_focusesList fl LEFT JOIN spycraft2_focuses cf ON fl.focusID = cf.focusID AND cf.characterID = $characterID WHERE name LIKE ? AND cf.focusID IS NULL LIMIT 5");
		$focuses->execute(array("%$search%"));
		foreach ($focuses as $info) {
			echo "<a href=\"\">".mb_convert_case($info['name'], MB_CASE_TITLE)."</a>\n";
		}
	}
?>