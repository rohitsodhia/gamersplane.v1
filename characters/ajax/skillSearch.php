<?php
	if ($loggedIn) {
		$searchName = sanitizeString($_POST['search'], 'search_format');
		$characterID = intval($_POST['characterID']);
		$system = $_POST['system'];

		if ($systems->verifySystem($system)) {
			$searchSkills = $mysql->query("SELECT charAutocomplete.name, IF(charAutocomplete_systems.itemID, 1, 0) systemSkill FROM charAutocomplete LEFT JOIN charAutocomplete_systems ON charAutocomplete.itemID = charAutocomplete_systems.itemID WHERE charAutocomplete.searchName LIKE ? AND charAutocomplete_systems = '{$system} ORDER BY systemSkill DESC, name LIMIT 5");
			$searchSkills->execute(["%{$searchName}%"]);
			$lastType = null;
			foreach ($searchSkills->fetchAll() as $info) {
				$classes = [];
				if (!$info['systemSkill']) {
					$classes[] = 'nonSystemSkill';
				}
				if ($info['systemSkill'] != $lastType && $lastType != null) {
					$classes[] = 'lineAbove';
				}
				$lastType = $info['systemSkill'];
				echo "<a href=\"\"" . (sizeof($classes) ? ' class="' . implode(' ', $classes) . '"' : '') . ">{$info['name']}</a>\n";
			}
		}
	}
?>
