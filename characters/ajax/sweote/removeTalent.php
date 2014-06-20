<?
	if (checkLogin(0)) {
		require_once(FILEROOT.'/includes/packages/swoeteCharacter.package.php');
		
		$userID = $_SESSION['userID'];
		$characterID = intval($_POST['characterID']);
		if ($character = new swoeteCharacter($characterID)) {
			$character->load();
			$charPermissions = $character->checkPermissions($userID);
			if ($charPermissions == 'edit') {
				$character->removeTalent($_POST['talentID']);
			}
		}
	}
?>