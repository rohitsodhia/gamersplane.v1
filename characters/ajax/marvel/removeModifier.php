<?
	if (checkLogin(0)) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);

		require_once(FILEROOT.'/includes/packages/marvelCharacter.package.php');
		if ($character = new marvelCharacter($characterID)) {
			$character->load();
			$charPermissions = $character->checkPermissions($userID);
			if ($charPermissions == 'edit') {
				$modifierID = intval($_POST['modifierID']);
				$character->removeModifier($modifierID);
			}
		}
	}
?>