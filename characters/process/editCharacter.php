<?
	checkLogin();
	
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		define('SYSTEM', $_POST['system']);
		if ($systems->getSystemID(SYSTEM)) {
			includeSystemInfo(SYSTEM);
			$charClass = SYSTEM.'Character';
			if ($character = new $charClass($characterID)) {
				$character->load();
				$charPermissions = $character->checkPermissions();
				if ($charPermissions == 'edit') {
					$character->save();
					header('Location: '.SITEROOT.'/characters/'.SYSTEM.'/'.$characterID); exit;
				}
			}
		}
	}
	header('Location: '.SITEROOT.'/403');
?>