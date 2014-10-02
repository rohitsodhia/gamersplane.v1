<?
	if (checkLogin(0)) {
		$search = sanitizeString($_POST['search'], 'search_format');
		$characterID = intval($_POST['characterID']);
		$system = sanitizeString($_POST['system']);

		if ($systems->getSystemID($system)) {
			$feats = $mysql->prepare("SELECT featID, name FROM featsList WHERE searchName LIKE ? ORDER BY name LIMIT 5");
			$feats->execute(array("%$search%"));
			foreach ($feats as $info) {
				echo "<a href=\"\">{$info['name']}</a>\n";
			}
		}
	}
?>