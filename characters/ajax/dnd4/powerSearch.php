<?
	if ($loggedIn) {
		$search = sanitizeString($_POST['search'], 'search_format');
		$characterID = intval($_POST['characterID']);
		
		$powers = $mysql->prepare("SELECT pl.powerID, pl.name FROM dnd4_powersList pl LEFT JOIN dnd4_powers cp ON pl.powerID = cp.powerID AND cp.characterID = $characterID WHERE pl.searchName LIKE ? AND cp.characterID IS NULL ORDER BY pl.name LIMIT 5");
		$powers->execute(array("%$search%"));
		foreach ($powers as $info) {
			echo "<a href=\"\">{$info['name']}</a>\n";
		}
	}
?>