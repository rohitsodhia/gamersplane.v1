<?
	if (checkLogin(0)) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		require_once(FILEROOT.'/includes/packages/sweoteCharacter.package.php');

		if ($character = new sweoteCharacter($characterID)) {
			$character->load();
			$charPermissions = $character->checkPermissions($userID);
			if ($charPermissions == 'edit') {
				$name = sanitizeString($_POST['name'], 'rem_dup_spaces');
				if (strlen($name)) $character->addTalent($name);
			}
		}
	}
?>