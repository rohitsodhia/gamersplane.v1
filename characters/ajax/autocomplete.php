<?
	if (checkLogin(0)) {
		$type = sanitizeString($_POST['type']);
		if (!in_array($type, array('skill', 'feat'))) return false;
		$search = sanitizeString($_POST['search'], 'search_format');
		$characterID = intval($_POST['characterID']);
		$system = sanitizeString($_POST['system']);

		if ($systemID = $systems->getSystemID($system)) {
			$itemIDs = $mysql->prepare("SELECT il.name, sacm.itemID IS NOT NULL systemItem FROM charAutocomplete il LEFT JOIN system_charAutocomplete_map sacm ON sacm.systemID = {$systemID} AND sacm.itemID = il.itemID WHERE il.type = '{$type}' AND il.name LIKE ? ORDER BY systemItem DESC, il.name LIMIT 5");
			$itemIDs->execute(array("%$search%"));
			$lastType = NULL;
			foreach ($itemIDs as $info) {
				$classes = array();
				if (!$info['systemItem']) $classes[] = 'nonSystemItem';
				if ($info['systemItem'] != $lastType && $lastType != NULL) $classes[] = 'lineAbove';
				$lastType = $info['systemItem'];
				echo "<a href=\"\"".(sizeof($classes)?' class="'.implode(' ', $classes).'"':'').">{$info['name']}</a>\n";
			}
		}
	}
?>