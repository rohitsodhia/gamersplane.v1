<?
	if (checkLogin(0)) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		define('SYSTEM', $_POST['system']);
		if ($systems->getSystemID(SYSTEM)) {
			require_once(FILEROOT.'/includes/packages/'.SYSTEM.'Character.package.php');
			$charClass = SYSTEM.'Character';
			if ($character = new $charClass($characterID)) {
				$character->load();
				$charPermissions = $character->checkPermissions($userID);
				if ($charPermissions == 'edit') {
					$name = sanitizeString($_POST['name'], 'rem_dup_spaces');
					if (strlen($name)) {
						$featID = getFeat($name);
						$character->addFeat($featID, $name);
					}
				}
			}
		}
	}
?>