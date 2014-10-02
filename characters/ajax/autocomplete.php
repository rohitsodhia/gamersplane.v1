<?
	if (checkLogin(0)) {
		$type = sanitizeString($_POST['type']);
		$search = sanitizeString($_POST['search'], 'search_format');
		$characterID = intval($_POST['characterID']);
		$system = sanitizeString($_POST['system']);
		$systemOnly = isset($_POST['systemOnly']) && $_POST['systemOnly'] == true?true:false;

		if ($systemID = $systems->getSystemID($system)) {
			$itemIDs = $mysql->prepare("SELECT il.name, sacm.itemID IS NOT NULL systemItem FROM charAutocomplete il LEFT JOIN system_charAutocomplete_map sacm ON sacm.systemID = {$systemID} AND sacm.itemID = il.itemID WHERE il.type = ?".($systemOnly?' AND sacm.systemID = '.$systemID:'')." AND il.name LIKE ? ORDER BY systemItem DESC, il.name LIMIT 5");
//			echo "SELECT il.name, sacm.itemID IS NOT NULL systemItem FROM charAutocomplete il LEFT JOIN system_charAutocomplete_map sacm ON sacm.systemID = {$systemID} AND sacm.itemID = il.itemID WHERE il.type = '$type'".($systemOnly?' AND sacm.systemID = '.$systemID:'')." AND il.name LIKE '%$search%' ORDER BY systemItem DESC, il.name LIMIT 5";
			$itemIDs->execute(array($type, "%$search%"));
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