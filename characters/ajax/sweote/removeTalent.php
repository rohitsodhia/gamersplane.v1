<?
	if ($loggedIn) {
		require_once(FILEROOT.'/includes/packages/swoeteCharacter.package.php');
		
		$characterID = intval($_POST['characterID']);
		if ($character = new swoeteCharacter($characterID)) {
			$character->load();
			$charPermissions = $character->checkPermissions($currentUser->userID);
			if ($charPermissions == 'edit') {
				$character->removeTalent($_POST['talentID']);
			}
		}
	}
?>