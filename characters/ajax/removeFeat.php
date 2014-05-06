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
					$featID = intval($_POST['featID']);
					$character->removeFeat($featID);
				}
			}
		}
	}
?>