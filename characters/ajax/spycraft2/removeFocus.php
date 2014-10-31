<?
	if ($loggedIn) {
		require_once(FILEROOT.'/includes/packages/spycraft2Character.package.php');
		
		$characterID = intval($_POST['characterID']);
		if ($character = new spycraft2Character($characterID)) {
			$character->load();
			$charPermissions = $character->checkPermissions($currentUser->userID);
			if ($charPermissions == 'edit') {
				$character->removeFocus($_POST['focusID']);
			}
		}
	}
?>