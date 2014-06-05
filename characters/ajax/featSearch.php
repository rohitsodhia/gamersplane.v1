<?
	if (checkLogin(0)) {
		$search = sanitizeString($_POST['search'], 'search_format');
		$characterID = intval($_POST['characterID']);
		$system = sanitizeString($_POST['system']);

		if ($systems->getSystemID($system)) {
			$feats = $mysql->prepare("SELECT featsList.featID, featsList.name FROM featsList LEFT JOIN (SELECT featID FROM {$system}_feats WHERE characterID = $characterID) {$system}_feats ON featsList.featID = {$system}_feats.featID WHERE searchName LIKE ? AND {$system}_feats.featID IS NULL LIMIT 5");
			$feats->execute(array("%$search%"));
			foreach ($feats as $info) {
				echo "<a href=\"\">{$info['name']}</a>\n";
			}
		}
	}
?>