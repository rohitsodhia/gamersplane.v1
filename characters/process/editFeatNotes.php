<?
	$success = false;
	if ($loggedIn) {
		$characterID = intval($_POST['characterID']);
		define('SYSTEM', $_POST['system']);
		if ($systems->verifySystem(SYSTEM)) {
			require_once(FILEROOT."/includes/packages/".SYSTEM."Character.package.php");
			$charClass = $systems->systemClassName(SYSTEM).'Character';
			if ($character = new $charClass($characterID)) {
				$charPermissions = $character->checkPermissions($currentUser->userID);
				if ($charPermissions == 'edit') {
					$featID = intval($_POST['featID']);
					$notes = sanitizeString($_POST['notes']);
					$updateFeat = $mysql->prepare("UPDATE ".SYSTEM."_feats SET notes = :notes WHERE characterID = $characterID AND featID = $featID");
					$updateFeat->execute(array(':notes' => $notes));
					if ($updateFeat->rowCount()) echo 1;
					else echo -1;

					$success = true;
				}
			}
		}
	}

	if (!$success) 
		echo 0;
?>