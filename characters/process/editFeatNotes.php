<?
	$success = FALSE;
	if (checkLogin(0)) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		define('SYSTEM', $_POST['system']);
		if ($systems->getSystemID(SYSTEM)) {
			require_once(FILEROOT.'/includes/packages/'.SYSTEM.'Character.package.php');
			$charClass = SYSTEM.'Character';
			$dispatchInfo['title'] = $systems->getFullName(SYSTEM).' Edit Feat Notes';
			if ($character = new $charClass($characterID)) {
				$charPermissions = $character->checkPermissions($userID);
				if ($charPermissions == 'edit') {
					$featID = intval($_POST['featID']);
					$notes = sanitizeString($_POST['notes']);
					$updateFeat = $mysql->prepare("UPDATE ".SYSTEM."_feats SET notes = :notes WHERE characterID = $characterID AND featID = $featID");
					$updateFeat->execute(array(':notes' => $notes));
					if ($updateFeat->rowCount()) echo 1;
					else echo -1;

					$success = TRUE;
				}
			}
		}
	}

	if (!$success) echo 0;
?>