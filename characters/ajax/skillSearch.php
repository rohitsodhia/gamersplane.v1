<?
	if ($loggedIn) {
		$search = sanitizeString($_POST['search'], 'search_format');
		$characterID = intval($_POST['characterID']);
		$system = $_POST['system'];
		
		if ($systems->verifySystem($system)) {
			$skills = $mysql->prepare("SELECT sl.itemID skillID, sl.name, sacm.itemID IS NOT NULL systemSkill FROM charAutocomplete sl LEFT JOIN system_charAutocomplete_map sacm ON sacm.system = '{$system}' AND sacm.itemID = sl.itemID WHERE sl.type = 'skill' AND sl.name LIKE ? ORDER BY systemSkill DESC, sl.name LIMIT 5");
			$skills->execute(array("%$search%"));
			$lastType = NULL;
			foreach ($skills as $info) {
				$classes = array();
				if (!$info['systemSkill']) $classes[] = 'nonSystemSkill';
				if ($info['systemSkill'] != $lastType && $lastType != NULL) $classes[] = 'lineAbove';
				$lastType = $info['systemSkill'];
				echo "<a href=\"\"".(sizeof($classes)?' class="'.implode(' ', $classes).'"':'').">{$info['name']}</a>\n";
			}
		}
	}
?>