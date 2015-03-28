<?
	require_once(FILEROOT.'/includes/packages/dnd4Character.package.php');

	$characterID = intval($_POST['characterID']);
	if ($character = new dnd4Character($characterID)) {
		$character->load();
		$charPermissions = $character->checkPermissions($currentUser->userID);
		if ($charPermissions == 'edit') 
			$character->removePower($_POST['powerID']);
	}
?>