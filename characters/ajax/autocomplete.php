<?php
	if ($loggedIn) {
		$type = sanitizeString($_POST['type']);
		$searchName = sanitizeString($_POST['search'], 'search_format');
		$characterID = intval($_POST['characterID']);
		$system = $_POST['system'];
		$systemOnly = isset($_POST['systemOnly']) && $_POST['systemOnly'] ? true : false;

		if ($systems->verifySystem($system)) {
			if ($systemOnly) {
				$searchSkills = $mysql->query("SELECT charAutocomplete.name, 1 'systemSkill' FROM charAutocomplete INNER JOIN charAutocomplete_systems ON charAutocomplete.itemID = charAutocomplete_systems.itemID WHERE charAutocomplete.searchName LIKE ? AND charAutocomplete_systems = '{$system}' ORDER BY name LIMIT 5");
				$searchSkills->execute(["%{$searchName}%"]);
			} else {
				$searchSkills = $mysql->query("SELECT charAutocomplete.name, IF(charAutocomplete_systems.itemID, 1, 0) systemSkill FROM charAutocomplete LEFT JOIN charAutocomplete_systems ON charAutocomplete.itemID = charAutocomplete_systems.itemID WHERE charAutocomplete.searchName LIKE ? AND charAutocomplete_systems = '{$system}' ORDER BY systemSkill DESC, name LIMIT 5");
				$searchSkills->execute(["%{$searchName}%"]);
			}
			$lastType = null;
			foreach ($searchSkills->fetchAll() as $item) {
				$classes = [];
				if (!$item['systemItem']) {
					$classes[] = 'nonSystemItem';
				}
				if ($item['systemItem'] != $lastType && $lastType != null) {
					$classes[] = 'lineAbove';
				}
				$lastType = $item['systemItem'];
				echo "<a href=\"\"" . (sizeof($classes) ? ' class="' . implode(' ', $classes) . '"' : '') . ">{$item['name']}</a>\n";
			}
		}
	}
?>
