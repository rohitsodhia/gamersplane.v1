<?
	if (checkLogin(0)) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$system = $_POST['system'];
		if ($systems->getSystemID($system)) {
			includeSystemInfo($system);
			$charClass = $system.'Character';
			if ($character = new $charClass($characterID)) {
				$character->load();
				$charPermissions = $character->checkPermissions();
				if ($charPermissions == 'edit') {
					$name = sanitizeString($_POST['name'], 'rem_dup_spaces');
					if (strlen($name)) {
						$skillID = getSkill($name, $system);
						$character->addSkill($skillID, $name, $_POST);
					}
				}
			}
		}
	}
?>