<?
	if (checkLogin(0)) {
		require_once(FILEROOT.'/includes/packages/spycraft2Character.package.php');
		
		$userID = $_SESSION['userID'];
		$characterID = intval($_POST['characterID']);
		if ($character = new spycraft2Character($characterID)) {
			$character->load();
			$charPermissions = $character->checkPermissions($userID);
			if ($charPermissions == 'edit') {
				$name = sanitizeString($name, 'rem_dup_spaces');
				if (strlen($name)) $character->addFocus($_POST['name']);
			}
		}
	}
?>