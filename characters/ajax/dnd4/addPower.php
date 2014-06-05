<?
	if (checkLogin(0)) {
		require_once(FILEROOT.'/includes/packages/dnd4Character.package.php');

		$userID = $_SESSION['userID'];
		$characterID = intval($_POST['characterID']);
		if ($character = new dnd4Character($characterID)) {
			$character->load();
			$charPermissions = $character->checkPermissions($userID);
			if ($charPermissions == 'edit') {
				$character->addPower($_POST['name'], $_POST['type']);
			}
		}
	}
?>
